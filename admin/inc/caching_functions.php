<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * page cache functions
 *
 * These functions are used to maintain a page cache
 * so that information on all pages is available without loading page files
 * 
 * @file		caching_functions.php
 * @package		GetSimple
 * @subpackage  Caching-Functions
 * @since		3.1
 */

$pagesArray = array();

// add_action('index-header','getPagesXmlValues',array(false));       // make $pagesArray available to the front 
// add_action('header', 'getPagesXmlValues',array(get_filename_id() == 'pages'));             // make $pagesArray available to the back
add_action('page-delete', 'create_pagesxml',array(true));          // Create pages.array if page deleted
add_action('page-restore', 'create_pagesxml',array(true));         // Create pages.array if page undo
add_action('page-clone', 'create_pagesxml',array(true));           // Create pages.array if page undo
add_action('draft-publish', 'create_pagesxml',array(true));        // Create pages.array if page is updated
add_action('changedata-aftersave', 'create_pagesxml',array(true)); // Create pages.array if page is updated
// add_action('pagecache-aftersave', 'initUpgradeMenus');             // regenerate menu cache


/**
 * LEGACY, replaced by getPages()
 * Get Cached Pages XML File Values
 *
 * Populates $pagesArray from page cache file
 * If the file does not exist it is created
 * @todo refresh does nothing
 * 
 * @since 3.1
 * @param bool $refresh check cache for pages changes and regen
 *  
 */
function getPagesXmlValues($refresh=false){
	GLOBAL $pagesArray;
	if(!$pagesArray) init_pageCache($refresh);
	return $pagesArray;
}

/**
 * Initialize pagecache
 * load pagesarray from pages.xml, rebuild from xml files if missing, or requested
 * Will do tentative refresh if differs, if required
 * @since  3.4
 * @param bool $refresh regenerate cache from pages files IF necessary according to check
 * @param bool $force   force regeneration
 */
function init_pageCache($refresh = false, $force = false) {
	GLOBAL $pagesArray, $pageCacheXml;
	
	debugLog("page cache: initializing");
	
	// load from pages.xml
	if(!$force){
		$pageCacheXml = load_pageCacheXml();
		$pagesArray   = pageCacheXMLtoArray($pageCacheXml);
		// force update if pagecache failed to load
		if(!$pagesArray) $force = true; 
	}

	// if not force, check pagecachediff, *pagecachediffers requires pagecache to be loaded first
	$refresh = $force || ($refresh && pageCacheDiffers());

	if(!$refresh) return; // pagecache loaded ok

	// regenerate from files if force, empty, or refresh request
	if($refresh){
		debugLog("page cache: refreshing");
		$pageCacheXml = generate_pageCacheXml();
		$status       = save_pageCacheXml($pageCacheXml);
		$pagesArray   = pageCacheXMLtoArray($pageCacheXml);
		// updatePagesMenu(); // update pages menu cache
	}
	// debugLog($pagesArray);
}


/**
 * LEGACY
 * Create the Cached Pages XML file
 *  
 * @since 3.1
 * @param bool $flag true saves pages.xml
 * @return null 
 */
function create_pagesxml($save=false){
	debugLog("page cache: LEGACY " . __FUNCTION__ . ' save - ' . $save);
	init_pageCache(true,true);
	return;

	global $pagesArray, $pageCacheXml;
  	$pageCacheXml = generate_pageCacheXml();
	
	if((bool)$save){ 
		save_pageCacheXml($pageCacheXml); 
	}
	$pagesArray = pageCacheXMLtoArray($pageCacheXml);
}


/* 
 #################
 # HELPERS
 #################
 */


/**
 * Return true if pagecache differs from pages
 * Uses very basic filecount checks
 * @todo  make more complex checking
 * 
 * @since 3.3.0 
 * @return bool
 */
function pageCacheCountDiffers(){
	GLOBAL $pagesArray;
	$path = GSDATAPAGESPATH;
	$filenames = getXmlFiles($path);
	// debugLog($filenames);
	return count($pagesArray)!=count($filenames);
}

/**
 * Return true if pagecache differs from pages
 * @todo  this will be a problem if pages do not store filename properly
 * it will always fail, can probably add some kind of time checking
 * @since 3.3.0 
 * @return bool
 */
function pageCacheDiffers(){
	GLOBAL $pagesArray;
	if(!$pagesArray) return true;

	$path          = GSDATAPAGESPATH;
	$filenames     = getXmlFiles($path);
	$filenames_old = array_column($pagesArray,'filename');

	if(count($pagesArray) != count($filenames)) return true;

	sort($filenames);
	sort($filenames_old);
	$new = md5(implode(',',$filenames));
	$old = md5(implode(',',$filenames_old));

	// debugLog($old . " " . $new);
	debugLog("page cache: cache differs -  " . ($new !== $old ? 'true' : 'false') );
	return $new !== $old;
}

/**
 * Loads in pagescache file xml 
 */
function load_pageCacheXml(){
	$file = GSDATAOTHERPATH.getDef('GSPAGECACHEFILE');	
	$pageCacheXml = getXml($file,false);
	return $pageCacheXml;
}

/**
 * Save pagecache xml file
 * @param  simpleXmlObj
 * @return sucess
 */
function save_pageCacheXml($xml){
	// debugLog(debug_backtrace());
	$file = GSDATAOTHERPATH.getDef('GSPAGECACHEFILE');		
  	// Plugin Authors should add custome fields etc.. here
  	$xml = exec_filter('pagecache',$xml); // @filter pagecache (obj) filter the page cache xml obj before save
	if(!empty($xml)) XMLsave($xml,$file);
  	exec_action('pagecache-aftersave');	// @hook pagecache-aftersave pagecache data file was saved
	debugLog("page cache: saved");
  	return;
}

/**
 * Generates pagecachexml obj from pages xml
 * @return simpleXmlobj pagecache xml
 */
function generate_pageCacheXml(){
	debugLog('page cache: re-generating from pages files');

	// read in each pages xml file
	$path      = GSDATAPAGESPATH;
	$filenames = getXmlFiles($path);
	$cacheXml  = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');

	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			
			// load page xml
			$pageXml  = getXml($path.$file);
			if(!$pageXml) continue;
			$id = $pageXml->url; // page id

			$cacheItems = $cacheXml->addChild('item');
			$children = $pageXml->children();

			$pageCacheExclude = getDef('GSPAGECACHEEXCLUDE',false,true);

			foreach ($children as $item => $itemdata) {
				
				// add all fields skip excludes
				if (isset($pageCacheExclude) && in_array($item, $pageCacheExclude)) continue;

				$note = $cacheItems->addChild($item);
				$note->addCData($itemdata);
			}
			
			// removed from xml , redundant
			# $note = $pages->addChild('slug');
			# $note->addCData($id);
			# $note = $pages->addChild('filename'); 
			# $note->addCData($file);

			pageCacheAddRoutes($id,$cacheItems);
		}
	}
	return $cacheXml;
}

/**
 * Add routing info to page cache dynamically
 * @todo  tentative
 * @param  [type] $id          [description]
 * @param  [type] &$cacheItems [description]
 * @return [type]              [description]
 */
function pageCacheAddRoutes($id,&$cacheItems){
	GLOBAL $pagesArray;
	if(!$pagesArray) return false;
	
	// @todo can lead to infinite loops if generate_permalink triggers a cache rebuild somehow
	$permaroute = generate_url($id);

	$cacheItems->addChild('route')->updateCData($permaroute);

	return;
	// @todo add routes test

	$routesNode = $cacheItems->addChild('routes');
	$routeNode = $routesNode->addChild('route');
	
	$pathNode = $routeNode->addChild('path');
	$pathNode->addCData($permaroute);

	$keyNode = $routeNode->addChild('key');
	$keyNode->addCData(md5($permaroute));
}


/**
 * creates pagecache array from $pagesarray xml
 * 
 * @since 3.3.0
 * @global $pagesArray
 * @param simpleXmlObj $xml xml object of page cache
 * @return  array new pagesarray
 */
function pageCacheXMLtoArray($xml){
	$pagesArray = array();
	$data = $xml;
	if(!$xml || !$xml->item) return $pagesArray; // @todo probably should catch this instead
	$pages = $data->item;
	foreach ($pages as $page) {
		$key=(string)$page->url;
		$pagesArray[$key]=array();

		$children = $page->children();
		foreach ($children as $opt=>$val) {
			$pagesArray[$key][(string)$opt]=(string)$val;
		}
		$pagesArray[$key]['slug']=$key; // legacy
		$pagesArray[$key]['filename']=$key.'.xml'; // legacy
	}

	return $pagesArray;
}

/**
 * Adds a single page to pagecache array from page xml node
 * 
 * @since 3.3.0
 * @global $pagesArray
 * @param simpleXmlObj $xml xml node of single page
 */
function pageXMLtoArray($xml){
	GLOBAL $pagesArray;
	$data = $xml;
	$key=(string)$data->url;		
	$pagesArray[$key]['url']=$key;  

	$children = $data->children();
	foreach ($children as $item => $itemdata) {
		if ($item!="content"){
			$pagesArray[$key][$item]=(string)$itemdata;
		}
	}
	$pagesArray[$key]['slug']=$key; // legacy
	$pagesArray[$key]['filename']=$key.'.xml'; // legacy

	return $pagesArray[$key];
}

/* ?> */

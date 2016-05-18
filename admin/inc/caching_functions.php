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
add_action('request-refreshcache', 'create_pagesxml',array(true)); // Create pages.array if page is updated
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

	debugLog("page cache: loaded");

	if(!$refresh) return; // pagecache loaded ok

	// regenerate from files if force, empty, or refresh request
	if($refresh){
		debugLog("page cache: refreshing");
		$pageCacheXml = generate_pageCacheXml();
		$status       = save_pageCacheXml($pageCacheXml);
		$pagesArray   = pageCacheXMLtoArray($pageCacheXml);
		// updatePagesMenu(); // update pages menu cache
		menuPageCacheSync();
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
 * @todo  this will be a problem if pages do not store `filename` properly
 * it will always fail, can probably add some kind of time checking
 * 
 * @since 3.3.0 
 * @return bool diff array
 */
function pageCacheDiffers(){
	GLOBAL $pagesArray;
	if(!$pagesArray) return true;

	$path          = GSDATAPAGESPATH;
	$filenames     = getXmlFiles($path);
	$filenames_old = array_column($pagesArray,'filename');

	// fast count compare
	if(count($pagesArray) != count($filenames)) return true;

	// filename diff compare
	$diff = array_diff_dual($filenames, $filenames_old);
	// debugLog($diff);
	
	// advanced compare, md5, timestamp etc. NOT IMPLEMENTED

	debugLog("page cache: cache differs check -  " . (count($diff) > 0 ? 'true' : 'false') . ' (' . count($diff).')');
	return $diff;
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
	set_time_limit(30);

	// read in each pages xml file
	$path      = GSDATAPAGESPATH;
	$filenames = getXmlFiles($path);
	$cacheXml  = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	$menudata  = array();

	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			$filename = $file;
			// load page xml
			$pageXml  = getXml($path.$file);
			if(!$pageXml) continue;

			$id = (string)$pageXml->url; // page id

			// AUTO CLEAN UP, fixes slugs to match filenames
			if($id !== _id(getFileName($file)) && getDef("GSAUTOFIXPAGESLUGS",true)){
				$id = fixupPageSlugs($path,$file,$id,$pageXml);
				$pageXml->url = $id;
			}
			// AUTO CLEAN UP, fixes filenames to match slugs
			if($id !== getFileName($file) && getDef("GSAUTOFIXPAGEFILES",true)) $filename = fixupPageFilenames($path,$file,$id,$pageXml);

			$cacheItems = $cacheXml->addChild('item');
			$children   = $pageXml->children();

			$pageCacheExclude = getDef('GSPAGECACHEEXCLUDE',false,true);

			foreach ($children as $item => $itemdata) {
				
				// add all fields skip excludes
				if (isset($pageCacheExclude) && in_array($item, $pageCacheExclude)) continue;

				$node = $cacheItems->addChild($item);
				$node->addCData($itemdata);
			}
			
			// removed from xml , redundant
			$node = $cacheItems->addChild('slug');
			$node->addCData($id);
			$node = $cacheItems->addChild('filename'); // add actual filename to page cache for _id mismatches, this might be used in the future
			$node->addCData($filename);

			pageCacheAddRoutes($id,$cacheItems); // @todo not working
		}
	}

	return $cacheXml;
}

function fixupPageSlugs($path,$file,$id,$pageXml){
	$id = _id(getFileName($file)); // set slug to filename
	$pageXml->url->setValue($id); // update id
	debugLog(__FUNCTION__ . ' ' .$id . '  ' . $file);
	XMLsave($pageXml, $path.$file); // save as new filename to match clean slug
	return $id;
}

function fixupPageFilenames($path,$file,$id,$pageXml){
	// collision protection not implemented
	// get slug from filename and increment if exists
	// if(file_exists(GSDATAPAGESPATH . $id .".xml")){
	// 	list($id,$count) = getNextFileName(GSDATAPAGESPATH,$id.'.xml');
	// 	$id = $id .'-'. $count;
	// }
	debugLog(__FUNCTION__ . ' ' .$id . ' -> ' . $file);
	backup_datafile(GSDATAPAGESPATH.$file);
	delete_file($path.$file);
	XMLsave($pageXml, $path.$id.'.xml'); // save as new filename to match clean slug
	return $path.$id.'.xml';
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
	
	if(menuItemGetData($id)) $cacheItems->addChild('parent')->updateCData(getParentByCoreMenu($id));

	return;
	// @todo TESTING theory , add routes sample test, store multiple routes as arrays in page xml
	// @REMOVE
	// NOTE this is not compatible with getPageField functions as they expect single node values
	// so we might not actually want to do this, despite how flexible it is

	$routesNode = $cacheItems->addChild('routes');
	$routeNode  = $routesNode->addChild('route');
	
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
function pageXMLtoArray($xml,$filename = null){
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
	$pagesArray[$key]['filename']= $filename ? $filename : $key.'.xml'; // legacy

	return $pagesArray[$key];
}

/* ?> */

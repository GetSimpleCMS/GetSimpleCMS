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


/**
 * Return Page Content
 *
 * Return the content of the requested page. 
 * Retreives from page file if content does not exist in cache
 * NOTE Performs content filter before returning by default.
 * LEGACY, use returnPageField for other fields, $field provided for legacy use only.
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $raw false - if true return raw xml, no strip, no filter
 * @param $nofilter false - if true skip content filter execution
 *
 */
function returnPageContent($page, $field='content', $raw = false, $nofilter = false){ 
	
	if($field !=='content'){
		debugLog('LEGACY NOTICE: '.__FUNCTION__.' is DEPRECATED for fields other than content use returnPageField instead');
		$data = returnPageField($page,$field,$raw);
	}
	else {
		$data = returnPageField($page,'content',true);
		$data = $nofilter || $raw ? $data : filterPageContent($page,$data);
	}

	return $data;
}

/**
 * Get Page Content
 *
 * Retrieve and display the content of the requested page. 
 * As the Content is not cached the file is read in.
 *
 * @since 2.0
 * @param $page - slug of the page to retrieve content
 *
 */
function getPageContent($page,$field='content'){   
	echo returnPageContent($page,$field);
}


/**
 * helper for filtering content
 * filter content handler for pageid, or content
 * @param  str $page    pageid
 * @param  str $content content data
 * @return str          result of content filtering
 */
function filterPageContent($page, $content){
	// if(!$content) $content = getPageField($page,'content',true); // could cause infinite loops, must be raw
	$content = exec_filter('content',$content); // @filter content (str) filter page content in returnPageContent
	return $content;
}


/**
 * Return Page Field
 *
 * Retrieve the requested field from the given page cache, fallback to page file
 * If the field is "content" and not raw it will run it through content filter
 *
 * @since 3.1
 * @param $page  slug of the page to retrieve content
 * @param $field the Field to display
 * @param $raw   if true, prevent any processing of data, use with caution as result can vary if falls back to page file
 * @param $cache if false, bypass cache and get directly from page file
 * 
 */
function returnPageField($page, $field, $raw = false, $cache = true){   
	$pagesArray = getPagesXmlValues();

	if ($cache && isset($pagesArray[(string)$page]) && isset($pagesArray[(string)$page][$field]) ){
		$ret = $raw ? $pagesArray[(string)$page][(string)$field] : strip_decode($pagesArray[(string)$page][(string)$field]);
	} else {
		$ret = returnPageFieldFromFile($page,$field,$raw);
	}

	// @todo this needs to come out of there, its dumb, special handling for special fields needs to be external
	if ($field=="content" && !$raw){
		$ret = filterPageContent($page,$ret);
	}

	return $ret;
}

/**
 * Get Page Field
 *
 * Retrieve and display the requested field from the given page. 
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $field - the Field to display
 * 
 */
function getPageField($page,$field){   
	echo returnPageField($page,$field);
}

/**
 * alias for getPageField()
 */
function echoPageField($page,$field){
	getPageField($page,$field);
}

/**
 * get page field directly from page file, bypasses page cache
 * @since  3.4
 * @param  str  $page  page id
 * @param  str  $field field id
 * @param  boolean $raw  return raw xml
 * @return returns field data from page xml
 */
function returnPageFieldFromFile($page, $field, $raw = false){   
	$data = getPageXML($page);
	if(!$data) return;

	$data = $data->$field;
	if($raw) return $data; // return without any processing
	$data = stripslashes(htmlspecialchars_decode($data, ENT_QUOTES));
	return $data;
}

/**
 * Get Page Children
 *
 * Return an Array of pages that are children of the requested page/slug
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * 
 * @returns - Array of slug names 
 * 
 */
function getChildren($page){
	$pagesArray = getPagesXmlValues();	
	$returnArray = array();
	foreach ($pagesArray as $key => $value) {
		if ($pagesArray[$key]['parent']==$page){
			$returnArray[]=$key;
		}
	}
	return $returnArray;
}

/**
 * Get Page Children - returns multi fields
 *
 * Return an Array of pages that are children of the requested page/slug with optional fields.
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param options - array of optional fields to return
 * 
 * @returns - Array of slug names and optional fields. 
 * 
 */

function getChildrenMulti($page,$options=array()){
	$pagesArray = getPagesXmlValues();	
	$count=0;
	$returnArray = array();
	foreach ($pagesArray as $key => $value) {
		if ($pagesArray[$key]['parent']==$page){
			$returnArray[$count]=array();
			$returnArray[$count]['url']=$key;
			foreach ($options as $option){
				$returnArray[$count][$option]=returnPageField($key,$option);
			}
			$count++;
		}
	}
	return $returnArray;
}

/**
 * LEGACY
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
 * 
 * @param bool $refresh regenerate cache from pages files if necessary according to check
 * @param bool force   force regen regardless
 */
function init_pageCache($refresh = false) {
	GLOBAL $pagesArray, $pageCacheXml;
	
	debugLog("page cache: initialized");

	if(!$refresh){
		$pageCacheXml = load_pageCacheXml();
		$pagesArray   = pageCacheXMLtoArray($pageCacheXml);
		if($pagesArray) return; // return if success, else continue to regen
	}

	// @todo check page time diff before doing this check
	// we can make always refresh by adding an OR here, and always check 
	$refresh  = !$pagesArray || ($refresh && pageCacheDiffers());

	// if refreshing or is still empty re-generate/save
	if($refresh){
		$pageCacheXml = generate_pageCacheXml();
		$status       = save_pageCacheXml($pageCacheXml);
		$pagesArray   = pageCacheXMLtoArray($pageCacheXml);
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
	debugLog("page cache: update needed? " . ($new !== $old ? 'true' : 'false') );
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
	debugLog('page cache: re-generated from disk');

	// read in each pages xml file
	$path = GSDATAPAGESPATH;
	$filenames = getXmlFiles($path);
	$cacheXml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			
			// load page xml
			$pageXml  = getXml($path.$file);
			if(!$pageXml) continue;
			$id = $pageXml->url; // page id

			$cacheItems = $cacheXml->addChild('item');
			// $pages->addChild('url', $id);
			$children = $pageXml->children();

			foreach ($children as $item => $itemdata) {
				// add all fields skip content
				if ($item!="content"){
					$note = $cacheItems->addChild($item);
					$note->addCData($itemdata);
				}
			}
			
			pageCacheAddRoutes($id,$cacheItems);

			// removed from xml , redundant
			# $note = $pages->addChild('slug');
			# $note->addCData($id);
			# $note = $pages->addChild('filename'); 
			# $note->addCData($file);
		}
	}
	return $cacheXml;
}

function pageCacheAddRoutes($id,&$cacheItems){
	GLOBAL $pagesArray;
	if(!$pagesArray) return false;

	// add route
	$routesNode = $cacheItems->addChild('routes');
	$routeNode = $routesNode->addChild('route');

	// can lead to infinite loops
	$permaroute = no_tsl(generate_permalink($id));

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
	if(!$xml || !$xml->item) return $pagesArray;
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
}

/* ?> */

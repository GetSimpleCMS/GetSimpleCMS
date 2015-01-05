<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:		caching_functions.php
* @Package:		GetSimple
* @since:		3.1
* @Action:		Plugin to create pages.xml and new functions  
*
*****************************************************/

$pagesArray = array();

add_action('index-header','getPagesXmlValues',array(false));       // make $pagesArray available to the front 
add_action('header', 'getPagesXmlValues',array(get_filename_id() == 'pages'));             // make $pagesArray available to the back
add_action('page-delete', 'create_pagesxml',array(true));          // Create pages.array if page deleted
add_action('page-restore', 'create_pagesxml',array(true));         // Create pages.array if page undo
add_action('page-clone', 'create_pagesxml',array(true));           // Create pages.array if page undo
add_action('draft-publish', 'create_pagesxml',array(true));        // Create pages.array if page is updated
add_action('changedata-aftersave', 'create_pagesxml',array(true)); // Create pages.array if page is updated


/**
 * Get Page Content
 *
 * Retrieve and display the content of the requested page. 
 * As the Content is not cahed the file is read in.
 *
 * @since 2.0
 * @param $page - slug of the page to retrieve content
 *
 */
function getPageContent($page,$field='content'){   
	echo returnPageContent($page,$field);
}

/**
 * Return Page Content
 *
 * Return the content of the requested page. 
 * As the Content is not cahed the file is read in.
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $raw false - if true return raw xml, no strip, no filter
 * @param $nofilter false - if true skip content filter execution
 *
 */
function returnPageContent($page, $field='content', $raw = false, $nofilter = false){   
	$data = getPageXML($page);
	if(!$data) return;
	$content = $data->$field;
	if($raw) return $content; // return without any processing

	$content = stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
	if ($field=='content' and !$nofilter){
		$content = exec_filter('content',$content); // @filter content (str) filter page content in returnPageContent
	}
	return $content;
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
 * Return Page Field
 *
 * Retrieve the requested field from the given page. 
 * If the field is "content" it will call returnPageContent()
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $field - the Field to display
 * 
 */
function returnPageField($page,$field){   
	$pagesArray = getPagesXmlValues();	

	if ($field=="content"){
		$ret=returnPageContent($page); 
	} else {
		if (isset($pagesArray[(string)$page]) && isset($pagesArray[(string)$page][$field]) ){
			$ret=strip_decode($pagesArray[(string)$page][(string)$field]);
		} else {
			$ret = returnPageContent($page,$field);
		}
	} 
	return $ret;
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
	
	if($refresh){
		if(!$pagesArray){
			// should always be empty, but in case someone calls this more than once
			$pageCacheXml = load_pageCacheXml();
			$pagesArray   = pageCacheXMLtoArray($pageCacheXml);		
		}
		// @todo check page time diff before doing this check
		$refresh  = pageCacheDiffers();
	}	
	// if refreshing or init generate/save
	if($refresh || !$pagesArray){
		$pageCacheXml = generate_pageCacheXml();
		$status       = save_pageCacheXml($pageCacheXml);
		$pagesArray = pageCacheXMLtoArray($pageCacheXml);
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
	debugLog($filenames);
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
  	return;
}

/**
 * Generates pagecachexml obj from pages xml
 * @return simpleXmlobj pagecache xml
 */
function generate_pageCacheXml(){
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

			// cyclical, depends on pagecache to generate permalink
			// @todo this is a test
			GLOBAL $pagesArray;
			if($pagesArray){
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

			// removed from xml , redundant
			# $note = $pages->addChild('slug');
			# $note->addCData($id);
			# $note = $pages->addChild('filename'); 
			# $note->addCData($file);
		}
	}

	return $cacheXml;
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

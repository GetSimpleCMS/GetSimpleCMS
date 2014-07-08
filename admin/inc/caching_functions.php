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
add_action('header', 'getPagesXmlValues',array(true));             // make $pagesArray available to the back
add_action('page-delete', 'create_pagesxml',array(true));          // Create pages.array if page deleted
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
	$thisfile = file_get_contents(GSDATAPAGESPATH.$page.'.xml');
	$data = simplexml_load_string($thisfile);
	$content = $data->$field;
	if($raw) return $content; // return without any processing

	$content = stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
	if ($field=='content' and !$nofilter){
		$content = exec_filter('content',$content);
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
		if (array_key_exists($field, $pagesArray[(string)$page])){
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
	return count($pagesArray)!=count($filenames);
}

/**
 * LEGACY
 * Get Cached Pages XML File Values
 *
 * Populates $pagesArray from page cache file
 * If the file does not exist it is created
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
	pageCacheXMLtoArray($pageCacheXml);
}


/**
 * Initialize pagecache
 * 
 * @param bool $refresh regenerate cache from pages files
 */
function init_pageCache($refresh = false) {
	GLOBAL $pageCacheXml;

	$file=GSDATAOTHERPATH."pages.xml";
	
	if (file_exists($file) and !$refresh){
		// if exists load it
		load_pageCache();
	} else {
		// else generate,save it,set global pagecache array
  		$pageCacheXml = generate_pageCacheXml();
		save_pageCacheXml($pageCacheXml);   		
		pageCacheXMLtoArray($pageCacheXml);
		return;
	}
}

/**
 * Loads in pagescache file xml to pagecache array
 */
function load_pageCache(){
	GLOBAL $pagesArray,$pageCacheXml;
	$file=GSDATAOTHERPATH."pages.xml";	
	$pagesArray=array(); // wipe array
	$pageCacheXml = getXml($file);
	pageCacheXMLtoArray($pageCacheXml); // create array from xml
}

/**
 * Save pagecache xml file
 * @param  simpleXmlObj
 * @return sucess
 */
function save_pageCacheXml($xml){
	$file=GSDATAOTHERPATH."pages.xml";		
  	// Plugin Authors should add custome fields etc.. here
  	$xml = exec_filter('pagecache',$xml);	
	if(!empty($xml)) $success = $xml->asXML($file);
  	exec_action('pagecache-aftersave');	
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
	$xml = @new SimpleXMLExtended('<channel></channel>');
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			$data = getXml($path.$file);
						
			$id=$data->url;
			$pages = $xml->addChild('item');
			// $pages->addChild('url', $id);
			$children = $data->children();
			foreach ($children as $item => $itemdata) {
				if ($item!="content"){
					$note = $pages->addChild($item);
					$note->addCData($itemdata);
				}
			}
			// removed from xml , redundant
			# $note = $pages->addChild('slug');
			# $note->addCData($id);
			# $note = $pages->addChild('filename'); 
			# $note->addCData($file);
		}
	}

	return $xml;
}

/**
 * creates pagecache array from pagescache xml
 * 
 * @since 3.3.0
 * @uses $pagesArray
 * @param simpleXmlObj $xml xml object of page cache 
 */
function pageCacheXMLtoArray($xml){
	GLOBAL $pagesArray;
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
}

/**
 * Adds a single page to pagecache array from page xml node
 * 
 * @since 3.3.0
 * @uses $pagesArray
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

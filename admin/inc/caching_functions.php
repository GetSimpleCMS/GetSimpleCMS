<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File:  caching_functions.php
* @Package: GetSimple
* @since 3.1
* @Action:  Plugin to create pages.xml and new functions  
*
*****************************************************/

$pagesArray = array();

add_action('index-header','getPagesXmlValues',array(false));      // make $pagesArray available to the front 
add_action('header', 'getPagesXmlValues',array(true));           // make $pagesArray available to the back
add_action('page-delete', 'create_pagesxml',array(true));         // Create pages.array if page deleted
add_action('changedata-aftersave', 'create_pagesxml',array(true));     // Create pages.array if page is updated


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
	$thisfile = file_get_contents(GSDATAPAGESPATH.$page.'.xml');
	$data = simplexml_load_string($thisfile);
	$content = stripslashes(htmlspecialchars_decode($data->$field, ENT_QUOTES));
	if ($field=='content'){
		$content = exec_filter('content',$content);
	}
	echo $content;
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
	global $pagesArray;
	if(!$pagesArray) getPagesXmlValues();	
	
	if ($field=="content"){
		getPageContent($page);  
	} else {
		if (array_key_exists($field, $pagesArray[(string)$page])){
			echo strip_decode($pagesArray[(string)$page][(string)$field]);
		} else {
			getPageContent($page,$field);
		}
	} 
}

/**
 * Echo Page Field
 *
 * Retrieve and display the requested field from the given page. 
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $field - the Field to display
 * 
 */
function echoPageField($page,$field){
	getPageField($page,$field);
}


/**
 * Return Page Content
 *
 * Return the content of the requested page. 
 * As the Content is not cahed the file is read in.
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $raw false - if true return raw xml
 * @param $nofilter false - if true skip content filter execution
 *
 */
function returnPageContent($page, $field='content', $raw = false, $nofilter = false){   
	$thisfile = file_get_contents(GSDATAPAGESPATH.$page.'.xml');
	$data = simplexml_load_string($thisfile);
	$content = $data->$field;
	if(!$raw) $content = stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
	if ($field=='content' and !$nofilter){
		$content = exec_filter('content',$content);
	}
	return $content;
}

/**
 * Get Page Field
 *
 * Retrieve and display the requested field from the given page. 
 * If the field is "content" it will call returnPageContent()
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $field - the Field to display
 * 
 */
function returnPageField($page,$field){   
	global $pagesArray;
	if(!$pagesArray) getPagesXmlValues();	

	if ($field=="content"){
		$ret=returnPageContent($page); 
	} else {
		if (array_key_exists($field, $pagesArray[(string)$page])){
			$ret=strip_decode(@$pagesArray[(string)$page][(string)$field]);
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
	global $pagesArray;
	if(!$pagesArray) getPagesXmlValues();		
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
	global $pagesArray;
	if(!$pagesArray) getPagesXmlValues();		
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
 * Get Cached Pages XML Values
 *
 * Loads the Cached XML data into the Array $pagesArray
 * If the file does not exist it is created the first time. 
 *
 * @since 3.1
 * @param bool $refresh check cache for changes and regen
 *  
 */

function getPagesXmlValues($refresh=true){
	debugLog('getPagesXmlValues '.$refresh);

	$file=GSDATAOTHERPATH."pages.xml";

	if (file_exists($file)){
		load_pageCache();
	} else {
		create_pagesxml(true);
		return;
	}

	// check for changes
	if ((bool)$refresh===true and pageCacheCountDiffers()){
		create_pagesxml(true);
	}
	
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
	global $pagesArray;
	debugLog('create_pagesxml '.$save);
  	$pageCacheXml = generate_pageCacheXml();
	
	if((bool)$save){ 
		save_pageCacheXml($pageCacheXml); 
	}

	pageCacheXMLtoArray($pageCacheXml);
}


/**
 * Initialize pagecache
 * 
 * @param bool $refresh regenerate cache
 */
function init_pageCache($refresh = false)
{
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
 * Loads in pagescache xml to pagecache array
 */
function load_pageCache(){
	GLOBAL $pagesArray;
	$file=GSDATAOTHERPATH."pages.xml";	
	$pagesArray=array(); // wipe array
	$data = getXml($file);
	pageCacheXMLtoArray($data); // create array from xml
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
	if(!empty($xml)) return $xml->asXML($file);
  	exec_action('pagecache-aftersave');	
}

/**
 * Generates pagecachexml from pages xml
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
			$pages->addChild('url', $id);
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
 * @param simpleXmlObj $xml xml node of single page
 */
function pageCacheXMLtoArray($xml){
	GLOBAL $pagesArray;
	debugLog('pageCacheXMLtoArray');
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
	// debugLog(var_export($pagesArray,true));
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
	// debugLog('pageXMLtoArray ' . $key);
	$pagesArray[$key]['url']=$key;  

	$children = $data->children();
	foreach ($children as $item => $itemdata) {
		if ($item!="content"){
			$pagesArray[$key][$item]=(string)$itemdata;
		}
	}
	$pagesArray[$key]['slug']=$key; // legacy
	$pagesArray[$key]['filename']=$key.'.xml'; // legacy
	// debugLog(var_export($pagesArray[$key],true));
	// _debugLog('pageXMLtoArray ' . $key,$pagesArray[$key]);
}

?>
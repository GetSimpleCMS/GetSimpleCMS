<?php 
/****************************************************
*
* @File:  caching_functions.php
* @Package: GetSimple
* @since 3.1
* @Action:  Plugin to create pages.xml and new functions  
*
*****************************************************/

$pagesArray = array();

add_action('index-pretemplate','getPagesXmlValues',array('false'));// make $pagesArray available to the theme 
add_action('header', 'getPagesXmlValues',array('false'));          // add hook to save  $tags values 
add_action('page-delete', 'create_pagesxml',array('true'));        // Create pages.array if file deleted


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
 *
 */
function returnPageContent($page,$field='content'){   
	$thisfile = file_get_contents(GSDATAPAGESPATH.$page.'.xml');
	$data = simplexml_load_string($thisfile);
	$content = stripslashes(htmlspecialchars_decode($data->$field, ENT_QUOTES));
	if ($field=='content'){
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
 * Get Cached Pages XML Values
 *
 * Loads the Cached XML data into the Array $pagesArray
 * If the file does not exist it is created the first time. 
 *
 * @since 3.1
 * @param bool $chkcount regenerate cache before raeding in
 *  
 */
function getPagesXmlValues($chkcount=true){
	global $pagesArray;
	debugLog('getPagesXmlValues');
	$pagesArray=array(); // wipe array

	$file=GSDATAOTHERPATH."pages.xml";

	// check for changes
	if ($chkcount==true and pageCacheCountDiffers()){
		create_pagesxml(true);
		return; 
	}

	// load file and create array
	if (file_exists($file)){
		$thisfile = file_get_contents($file);
		$data = simplexml_load_string($thisfile);
		pageCacheXMLtoArray($data); // create array from xml
	} else {
		create_pagesxml(true);
		# getPagesXmlValues(false);
	}
	
}


/**
 * Create the Cached Pages XML file
 *
 * Reads in all pages xml builds pagecache xml obj
 * data/pages/pages xml
 *  
 * Optionally saves pagecache xml into data/other/pages.xml
 * @todo why optonal, why would we not always save?
 *
 * @since 3.1
 * @uses $pagesArray
 * @param bool $flag true saves pages.xml
 * @return null 
 */
function create_pagesxml($flag=false){
	global $pagesArray;
	debugLog('create_pagesxml');

	// @todo what purpose did this and flag serve ?
	// @todo why flag to not save xml ?
	// if ((isset($_GET['upd']) && $_GET['upd']=="edit-success")) || $flag=='true'){
	$menu = '';
	$filem=GSDATAOTHERPATH."pages.xml";

	// read in each pages xml file
	$path = GSDATAPAGESPATH;
	$filenames = getXmlFiles($path);
	$count=0;
	$xml = @new SimpleXMLExtended('<channel></channel>');
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			$thisfile = file_get_contents($path.$file);
			$data = simplexml_load_string($thisfile);
			$count++;   
						
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
			$note = $pages->addChild('slug');
			$note->addCData($id);
			$note = $pages->addChild('filename'); 
			$note->addCData($file);
			
			// pageXMLtoArray($data,$file); // testing per pagexml

			// Plugin Authors should add custome fields etc.. here
			exec_action('caching-save');
		} // end foreach
	}  // endif      

	pageCacheXMLtoArray($xml);
	if ($flag==true){
		$xml->asXML($filem);
	}
	// }

}

/**
 * creates pagecache array from pagescache xml
 * 
 * @since 3.3.0
 * @uses $pagesArray
 * @param simpleXmlObj $xml xml node of single page
 * @return
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

	}	
	// debugLog(var_export($pagesArray,true));
	// debugLog(var_export($pagesArray,true));
}

/**
 * Adds a single page to pagecache array from page xml node
 * 
 * @since 3.3.0
 * @uses $pagesArray
 * @param simpleXmlObj $xml xml node of single page
 * @return
 */
function pageXMLtoArray($xml,$file=''){
	GLOBAL $pagesArray;
	$data = $xml;
	$id=(string)$data->url;		
	// debugLog('pageXMLtoArray ' . $id);
	$pagesArray[$id]['url']=$id;  

	$children = $data->children();
	foreach ($children as $item => $itemdata) {
		if ($item!="content"){
			$pagesArray[$id][$item]=(string)$itemdata;
		}
	}
	$pagesArray[$id]['slug']=(string)$data->slug;
	$pagesArray[$id]['filename']=$file;
	// debugLog(var_export($pagesArray[$id],true));
	// _debugLog('pageXMLtoArray ' . $id,$pagesArray[$id]);
}

?>
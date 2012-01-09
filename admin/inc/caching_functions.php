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

add_action('index-pretemplate','getPagesXmlValues',array('false'));           		// make $pagesArray available to the theme 
add_action('header', 'create_pagesxml',array('false'));            					// add hook to save  $tags values 
add_action('page-delete', 'create_pagesxml',array('true'));            				// Create pages.array if file deleted



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
function getPageContent($page){   
	$thisfile = file_get_contents(GSDATAPAGESPATH.$page.'.xml');
	$data = simplexml_load_string($thisfile);
	$content = stripslashes(htmlspecialchars_decode($data->content, ENT_QUOTES));
	$content = exec_filter('content',$content);
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
	  echo strip_decode($pagesArray[(string)$page][(string)$field]);
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
function returnPageContent($page){   
  $thisfile = file_get_contents(GSDATAPAGESPATH.$page.'.xml');
  $data = simplexml_load_string($thisfile);
  $content = stripslashes(htmlspecialchars_decode($data->content, ENT_QUOTES));
  $content = exec_filter('content',$content);
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
	  $ret=strip_decode(@$pagesArray[(string)$page][(string)$field]);
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
 * Get Cached Pages XML Values
 *
 * Loads the Cached XML data into the Array $pagesArray
 * If the file does not exist it is created the first time. 
 *
 * @since 3.1
 *  
 */
function getPagesXmlValues(){
  global $pagesArray;
  $file=GSDATAOTHERPATH."pages.xml";
  if (file_exists($file)){
  // load the xml file and setup the array. 
    $thisfile = file_get_contents($file);
    $data = simplexml_load_string($thisfile);
    $pages = $data->item;
      foreach ($pages as $page) {
        $key=$page->url;
        $pagesArray[(string)$key]=array();
        foreach ($page->children() as $opt=>$val) {
            $pagesArray[(string)$key][(string)$opt]=(string)$val;
        }
        
      }
	  $path = GSDATAPAGESPATH;
	  $dir_handle = @opendir($path) or die("Unable to open $path");
	  $filenames = array();
	  while ($filename = readdir($dir_handle)) {
	    $ext = substr($filename, strrpos($filename, '.') + 1);
	    if ($ext=="xml"){
	      $filenames[] = $filename;
	    }
	  }
	  if (count($pagesArray)!=count($filenames)) {
	  		create_pagesxml('true');
    		getPagesXmlValues();
	  }
  } else {
    create_pagesxml('true');
    getPagesXmlValues();
  }
  
}

/**
 * Create the Cached Pages XML file
 *
 * Reads in each page of the site and creates a single XML file called 
 * data/pages/pages.array 
 *
 * @since 3.1
 *  
 */
function create_pagesxml($flag){
global $pagesArray;

if ((isset($_GET['upd']) && $_GET['upd']=="edit-success") || $flag=='true'){
  $menu = '';
  $filem=GSDATAOTHERPATH."pages.xml";

  $path = GSDATAPAGESPATH;
  $dir_handle = @opendir($path) or die("Unable to open $path");
  $filenames = array();
  while ($filename = readdir($dir_handle)) {
    $ext = substr($filename, strrpos($filename, '.') + 1);
    if ($ext=="xml"){
      $filenames[] = $filename;
    }
  }
  
  $count=0;
  $xml = @new SimpleXMLExtended('<channel></channel>');
  if (count($filenames) != 0) {
    foreach ($filenames as $file) {
      if ($file == "." || $file == ".." || is_dir(GSDATAPAGESPATH.$file) || $file == ".htaccess"  ) {
        // not a page data file
      } else {
        $thisfile = file_get_contents($path.$file);
        $data = simplexml_load_string($thisfile);
        $count++;   
        $id=$data->url;
        
    	$pages = $xml->addChild('item');
    	$pages->addChild('url', $id);
    	$pagesArray[(string)$id]['url']=(string)$id;		
		
		foreach ($data->children() as $item => $itemdata) {
		   	if ($item!="content"){
				$note = $pages->addChild($item);
	        	$note->addCData($itemdata);
	        	$pagesArray[(string)$id][$item]=(string)$itemdata;
			}
		}
		
        $note = $pages->addChild('slug');
        $note->addCData($id);
        $pagesArray[(string)$id]['slug']=(string)$data->slug;
		
        $pagesArray[(string)$id]['filename']=$file;
        $note = $pages->addChild('filename'); 
        $note->addCData($file);
		
        // Plugin Authors should add custome fields etc.. here
  			exec_action('caching-save');
	  
      } // else
    } // end foreach
  }   // endif      
  if ($flag==true){
    $xml->asXML($filem);
  }
}
}



?>
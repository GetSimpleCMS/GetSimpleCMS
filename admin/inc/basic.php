<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	basic.php
* @Package:	GetSimple
* @Action:	Functions used to help create the cp pages	
*
*****************************************************/

/*******************************************************
 * @function clean_url
 * @param $text - text you want to turn encode into a URL
 * @returns valid encoded url
 *
*/
function clean_url($text)  { 
	if (function_exists('mb_strtolower')) {
		$text = strip_tags(mb_strtolower($text)); 
	} else {
		$text = strip_tags(strtolower($text)); 
	}
	$code_entities_match = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=','.'); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = rtrim($text, "-");
	return $text; 
} 
/******************************************************/

/*******************************************************
 * @function clean_img_name
 * @param $text - image name you want to turn encode into a URL
 * @returns valid encoded url
 * @ same as clean_url except it keeps the . in there
*/
function clean_img_name($text)  { 
	if (function_exists('mb_strtolower')) {
		$text = strip_tags(mb_strtolower($text)); 
	} else {
		$text = strip_tags(strtolower($text)); 
	}
	$code_entities_match = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','='); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = rtrim($text, "-");
	return $text; 
} 
/******************************************************/


/*******************************************************
 * @function to7bit
 * @param $text - text you want to turn encode from UTF8
 * @returns valid encoded string
 *
*/
function to7bit($text,$from_enc) {
		if (function_exists('mb_convert_encoding')) {
   		$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
   	}
    $text = preg_replace(
        array('/&szlig;/','/&(..)lig;/',
             '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
        array('ss',"$1","$1".'e',"$1"),
        $text);
    return $text;
}
/******************************************************/

/*******************************************************
 * @function sendmail
 * @param $to - email address of recipient
 * @param $subject - subject of email
 * @param $message - body of email
 *
*/
function sendmail($to,$subject,$message) {

	if (defined('GSFROMEMAIL')){
		$fromemail = GSFROMEMAIL; 
	} else {
		$fromemail = 'noreply@get-simple.info';
	}
	
	global $EMAIL;
	$headers  = "From: ".$fromemail."\r\n";
	$headers .= "Reply-To: ".$fromemail."\r\n";
	$headers .= "Return-Path: ".$fromemail."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
	
	if( mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=',"$message",$headers) ) {
		//mail sent
		return 'success';
	} else {
		//mail failed
		return 'error';
	}
}
/******************************************************/


/*******************************************************
 * @function subval_sort
 * @param $a - array to sort
 * @param $subkey - key to sort on
 *
*/
function subval_sort($a,$subkey) {
	if (count($a) != 0 || (!empty($a))) { 
		foreach($a as $k=>$v) {
			$b[$k] = function_exists('mb_strtolower') ? mb_strtolower($v[$subkey]) : strtolower($v[$subkey]);
		}
		asort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
	}
}
/******************************************************/

/*******************************************************
 * @function json_decode
 * @returns the API response in the form of an object
 * @about - This is to temporarily fix issues with PHP versions < 5.2.0
 *
*/
if(!function_exists('json_decode')) {
    function json_decode($api_data) {
        preg_match('/(?P<status>[^"]+)","((api_key":"(?P<api_key>[^"]+))|(latest":"(?P<latest>[^"]+)))/',$api_data,$api_data);
        return (object)$api_data;
    }
}
/******************************************************/

/*******************************************************
 * @class SimpleXMLExtended
 * @extends SimpleXMLElement
 * @adds cdata functionality to simplexml
 *
*/
class SimpleXMLExtended extends SimpleXMLElement{   
  public function addCData($cdata_text){   
   $node= dom_import_simplexml($this);   
   $no = $node->ownerDocument;   
   $node->appendChild($no->createCDATASection($cdata_text));   
  } 
} 
/******************************************************/ 


/*******************************************************
 * @function isFile
 * @param $file - file you are checking for
 * @param $type - type of file to look for. Default is xml
 *
*/
function isFile($file, $path, $type = 'xml') {
	if( is_file(tsl($path) . $file) && $file != "." && $file != ".." && (strstr($file, $type))  ) {
		return true;
	} else {
		return false;
	}
}
/******************************************************/


/*******************************************************
 * @function getFiles
 * @param $path - path to get array of files from
 * @return - array of files within that path
 *
*/
function getFiles($path) {
	$handle = @opendir($path) or die("Unable to open $path");
	$file_arr = array();
	while ($file = readdir($handle)) {
		$file_arr[] = $file;
	}
	closedir($handle);
	return $file_arr;
}
/******************************************************/


/*******************************************************
 * @function getXML
 * @param $file - file to pull xml data from
 * @return - xml data from file
 *
*/
function getXML($file) {
	$xml = @file_get_contents($file);
	$data = simplexml_load_string($xml, 'SimpleXMLExtended', LIBXML_NOCDATA);
	return $data;
}
/******************************************************/

/*******************************************************
 * @function XMLsave
 * @param $file - file to save
 * @param $xml - data to save
 *
*/
function XMLsave($xml, $file) {
	$xml->asXML($file);
	
	if (defined('GSCHMOD')) {
		chmod($file, GSCHMOD);
	} else {
		chmod($file, 0755);
	}
}
/******************************************************/



/*******************************************************
 * @function lngDate
 * @param $dt - date to convert, optional
 * @returns - date in standard SM long format
 *
*/
function lngDate($dt) {
	global $i18n;
	
	if (!$dt) {
		$data = date($i18n['DATE_AND_TIME_FORMAT']);
	} else {
		$data = date($i18n['DATE_AND_TIME_FORMAT'], strtotime($dt));
	}
	return $data;
}
/******************************************************/


/*******************************************************
 * @function shtDate
 * @param $dt - date to convert, optional
 * @returns - date in standard SM short format
 *
*/
function shtDate($dt) {
	global $i18n;
	
	if (!$dt) {
		$data = date($i18n['DATE_FORMAT']);
	} else {
		$data = date($i18n['DATE_FORMAT'], strtotime($dt));
	}
	return $data;
}
/******************************************************/



/*******************************************************
 * @function cl
 * @param $data - data to strip html crap from
 * @returns - cleaned data
 *
*/
function cl($data){
	$data = stripslashes(strip_tags(html_entity_decode($data, ENT_QUOTES, 'UTF-8')));
	return $data;
}
/******************************************************/



/*******************************************************
 * @function tsl
 * @param $path - path to add training slash to
 *
*/
function tsl($path) {
	if( substr($path, strlen($path) - 1) != '/' ) {
		$path .= '/';
	}
	return $path;
}
/******************************************************/



/*******************************************************
 * @function in_arrayi
 * @param $needle - look for
 * @param $haystack - look in
 * @about - case insensitive in_array replacement
 *
*/
function in_arrayi($needle, $haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}
/******************************************************/


/*******************************************************
 * @function ListDir
 * @param $dir_handle - directory handle
 * @param $path - starting path
 *
*/
function ListDir($dir_handle,$path) {
	global $zipfile;
	while (false !== ($file = readdir($dir_handle))) {
	  $dir = $path.'/'.$file;
	  $zippath = substr_replace($dir, 'getsimple' , 0, 2);
	  if(is_dir($dir) && $file != '.' && $file !='..' ) {
			$handle = @opendir($dir) or die("Unable to open file $file");
			$zipfile->add_dir($zippath);
			ListDir($handle, $dir);
	  } elseif($file != '.' && $file !='..') {
			$filedata = file_get_contents($dir);
			$zipfile->add_file($filedata, $zippath);
	  }
	}
	closedir($dir_handle);
}
/***************************************************/


/*******************************************************
 * @function find_url
 * @returns returns the url of a page
 *
*/
function find_url($slug, $parent, $type='full') {
	global $PRETTYURLS;
	global $SITEURL;
	global $PERMALINK;
				
	if ($type == 'full') {
		$full = $SITEURL;
	} elseif($type == 'relative') {
		$s = pathinfo(htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES));
		$full = $s['dirname'] .'/';
		$full = str_replace('//', '/', $full);
	} else {
		$full = '/';
	}
	
	if ($parent != '') {
		$parent = tsl($parent); 
	}	

  if ($PRETTYURLS == '1') {      
    if ($slug != 'index'){  
    	$url = $full . $parent . $slug . '/';
    } else {
    	$url = $full;
    }   
  } else {
		if ($slug != 'index'){ 
    	$url = $full .'index.php?id='.$slug;
    } else {
    	$url = $full;
    }
  }
  
if ($PERMALINK != '' && $slug != 'index'){
		$plink = str_replace('%parent%/', $parent, $PERMALINK);
		$plink = str_replace('%parent%', $parent, $plink);
		$plink = str_replace('%slug%', $slug, $plink);
		$url = $full . $plink;
	}

	return $url;
}
/******************************************************/


/*******************************************************
 * @function strippatch
 * @param $path - path supplied by user input
 * @returns returns same path without it going up in the folder structure
 *
*/
function strippath($path) {
	$segments = explode('/',implode('/',explode('\\',$path)));
	$path = '';
	foreach ($segments as $part) if ($part !== '..') $path .= trim($part).'/';
	$path = preg_replace('/\/+/','/',substr($path, 0, -1));
	if (strlen($path)<=0||$path=='/') return false;
	return $path;
}
/******************************************************/


/*******************************************************
 * @function strip_quotes
 * @param $text - text needing to have all quotes and html stripped out
 * @returns returns same text without quotes and HTML
 *
*/
function strip_quotes($text)  { 
	$text = strip_tags($text); 
	$code_entities_match = array('"','\'','&quot;'); 
	$text = str_replace($code_entities_match, '', $text); 
	return trim($text); 
} 
/******************************************************/
?>
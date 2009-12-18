<?php
/****************************************************
*
* @File: 	basic.php
* @Package:	GetSimple
* @Action:	Functions used to help create the cp pages	
*
*****************************************************/

if (basename($_SERVER['PHP_SELF']) == 'basic.php') { 
	die('You cannot load this page directly.'); 
} 

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
	$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','='); 
	$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
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
	
	global $EMAIL;
	$headers  = "From: noreply@get-simple.info\r\n";
	$headers .= "Reply-To: noreply@get-simple.info\r\n";
	$headers .= "Return-Path: noreply@get-simple.info\r\n";
	$headers .= "Content-type: text/html\r\n";
	
	if( mail($to,$subject,"$message",$headers) ) {
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
			//$b[$k] = strtolower($v[$subkey]);
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
	return $file_arr;
	closedir($handle);
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
	$data = simplexml_load_string($xml, NULL, LIBXML_NOCDATA);
	return $data;
}
/******************************************************/


/*******************************************************
 * @function lngDate
 * @param $dt - date to convert, optional
 * @returns - date in standard SM long format
 *
*/
function lngDate($dt) {
	if (!$dt) {
		$data = date("F jS, Y - g:i A");
	} else {
		$data = date("F jS, Y - g:i A", strtotime($dt));
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
function cl($data) {
	$data = stripslashes(strip_tags(html_entity_decode($data, ENT_QUOTES)));
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
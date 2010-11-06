<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Basic Functions 
 *
 * These functions are used throughout the installation of GetSimple.
 *
 * @package GetSimple
 * @subpackage Basic-Functions
 */

/**
 * Clean URL
 *
 * @since 1.0
 *
 * @param string $text
 * @return string
 */
function clean_url($text)  { 
	$text = strip_tags(lowercase($text)); 
	$code_entities_match = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=','.'); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = rtrim($text, "-");
	return $text; 
} 

/**
 * Clean Image Name
 *
 * Mirror image of Clean URL, but it allows periods so file extentions still work
 *
 * @since 2.0
 *
 * @param string $text
 * @return string
 */
function clean_img_name($text)  { 
	$text = strip_tags(lowercase($text)); 
	$code_entities_match = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','='); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = rtrim($text, "-");
	return $text; 
} 

/**
 * 7bit Text Converter
 *
 * Converts a string to a different encoding format
 *
 * @since 1.0
 *
 * @param string $text
 * @param string $from_enc
 * @return string 
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

/**
 * Send Email
 *
 * @since 1.0
 * @uses GSFROMEMAIL
 * @uses $EMAIL
 *
 * @param string $to
 * @param string $subject
 * @param string $message
 * @return string
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
		return 'success';
	} else {
		return 'error';
	}
}

/**
 * Sub-Array Sort
 *
 * Sorts the passed array by a subkey
 *
 * @since 1.0
 *
 * @param array $a
 * @param string $subkey Key within the array passed you want to sort by
 * @return array
 */
function subval_sort($a,$subkey) {
	if (count($a) != 0 || (!empty($a))) { 
		foreach($a as $k=>$v) {
			$b[$k] = lowercase($v[$subkey]);
		}
		asort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
	}
}

/**
 * JSON Decode
 *
 * Allows backward compatibility for servers that do not have the JSON decoder installed
 *
 * @since 2.0
 * @author Martijn van der Ven
 *
 * @param string $api_data
 * @return object
 */
if(!function_exists('json_decode')) {
  function json_decode($api_data) {
    preg_match('/(?P<status>[^"]+)","((api_key":"(?P<api_key>[^"]+))|(latest":"(?P<latest>[^"]+)))/',$api_data,$api_data);
    return (object)$api_data;
  }
}

/**
 * SimpleXMLExtended Class
 *
 * Extends the default PHP SimpleXMLElement class by 
 * allowing the addition of cdata
 *
 * @since 1.0
 *
 * @param string $cdata_text
 */
class SimpleXMLExtended extends SimpleXMLElement{   
  public function addCData($cdata_text){   
   $node= dom_import_simplexml($this);   
   $no = $node->ownerDocument;   
   $node->appendChild($no->createCDATASection($cdata_text));   
  } 
} 

/**
 * Is File
 *
 * @since 1.0
 * @uses tsl
 *
 * @param string $file
 * @param string $path
 * @param string $type Optiona, default is 'xml'
 * @return bool
 */
function isFile($file, $path, $type = 'xml') {
	if( is_file(tsl($path) . $file) && $file != "." && $file != ".." && (strstr($file, $type))  ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Get Files
 *
 * Returns an array of files from the passed path
 *
 * @since 1.0
 *
 * @param string $path
 * @return array
 */
function getFiles($path) {
	$handle = opendir($path) or die("Unable to open $path");
	$file_arr = array();
	while ($file = readdir($handle)) {
		$file_arr[] = $file;
	}
	closedir($handle);
	return $file_arr;
}

/**
 * Get XML Data
 *
 * Turns the XML file into an object 
 *
 * @since 1.0
 *
 * @param string $file
 * @return object
 */
function getXML($file) {
	$xml = file_get_contents($file);
	$data = simplexml_load_string($xml, 'SimpleXMLExtended', LIBXML_NOCDATA);
	return $data;
}

/**
 * XML Save
 *
 * @since 2.0
 * @todo create and chmod file before ->asXML call (if it doesnt exist already, if so, then just chmod it.)
 *
 * @param object $xml
 * @param string $file Filename that it will be saved as
 * @return bool
 */
function XMLsave($xml, $file) {
	$success = $xml->asXML($file) === TRUE;
	
	if (defined('GSCHMOD')) {
		return $success && chmod($file, GSCHMOD);
	} else {
		return $success && chmod($file, 0755);
	}
}

/**
 * Long Date Output
 *
 * @since 1.0
 * @uses $i18n
 * @uses i18n_r
 *
 * @param string $dt Date/Time format, default is $i18n['DATE_AND_TIME_FORMAT']
 * @return string
 */
function lngDate($dt) {
	global $i18n;
	
	if (!$dt) {
		$data = date(i18n_r('DATE_AND_TIME_FORMAT'));
	} else {
		$data = date(i18n_r('DATE_AND_TIME_FORMAT'), strtotime($dt));
	}
	return $data;
}

/**
 * Short Date Output
 *
 * @since 1.0
 * @uses $i18n
 * @uses i18n_r
 *
 * @param string $dt Date/Time format, default is $i18n['DATE_FORMAT']
 * @return string
 */
function shtDate($dt) {
	global $i18n;
	
	if (!$dt) {
		$data = date(i18n_r('DATE_FORMAT'));
	} else {
		$data = date(i18n_r('DATE_FORMAT'), strtotime($dt));
	}
	return $data;
}

/**
 * Clean Utility
 *
 * @since 1.0
 *
 * @param string $data
 * @return string
 */
function cl($data){
	$data = stripslashes(strip_tags(html_entity_decode($data, ENT_QUOTES, 'UTF-8')));
	return $data;
}

/**
 * Add Trailing Slash
 *
 * @since 1.0
 *
 * @param string $path
 * @return string
 */
function tsl($path) {
	if( substr($path, strlen($path) - 1) != '/' ) {
		$path .= '/';
	}
	return $path;
}

/**
 * Case-Insensitve In-Array
 *
 * Creates a function that PHP should already have, but doesnt
 *
 * @since 1.0
 *
 * @param string $path
 * @return string
 */
if(!function_exists('in_arrayi')) {
	function in_arrayi($needle, $haystack) {
	    return in_array(lowercase($needle), array_map('lowercase', $haystack));
	}
}

/**
 * Creates Standard URL for Pages
 *
 * Default function to create the correct url structure for each front-end page
 *
 * @since 2.0
 * @uses $PRETTYURLS
 * @uses $SITEURL
 * @uses $PERMALINK
 * @uses tsl
 *
 * @param string $slug
 * @param string $parent
 * @param string $type Default is 'full', alternative is 'relative'
 * @return string
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

/**
 * Strip Path
 *
 * @since 2.0
 * @author Martijn van der Ven
 *
 * @param string $path
 * @return string
 */
function strippath($path) {
	$segments = explode('/',implode('/',explode('\\',$path)));
	$path = '';
	foreach ($segments as $part) if ($part !== '..') $path .= trim($part).'/';
	$path = preg_replace('/\/+/','/',substr($path, 0, -1));
	if (strlen($path)<=0||$path=='/') return false;
	return $path;
}

/**
 * Strip Quotes
 *
 * @since 2.0
 *
 * @param string $text
 * @return string
 */
function strip_quotes($text)  { 
	$text = strip_tags($text); 
	$code_entities_match = array('"','\'','&quot;'); 
	$text = str_replace($code_entities_match, '', $text); 
	return trim($text); 
} 

/**
 * Redirect URL
 *
 * @since 2.04
 * @author schlex
 *
 * @param string $url
 */
function redirect($url) {
	global $i18n;

	if (!headers_sent($filename, $linenum)) {
    header('Location: '.$url);
  } else {
    echo "<html><head><title>".i18n_r('REDIRECT')."</title></head><body>";
    if ( !defined('GSDEBUG') || (GSDEBUG != TRUE) ) {
	    echo '<script type="text/javascript">';
	    echo 'window.location.href="'.$url.'";';
	    echo '</script>';
	    echo '<noscript>';
	    echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
	    echo '</noscript>';
	  }
    echo i18n_r('ERROR').": Headers already sent in ".$filename." on line ".$linenum."\n";
    printf(i18n_r('REDIRECT_MSG'), $url);
		echo "</body></html>";
	}
	
	exit;
}

/**
 * Display i18n
 *
 * Displays the default language's tranlation, but if it 
 * does not exist, it falls back to the en_US one.
 *
 * @since 2.04
 * @author ccagle8
 * @uses GSLANGPATH
 * @uses $i18n
 * @uses $LANG
 *
 * @param string $name
 * @param bool $echo Optional, default is true
 */
function i18n($name, $echo=true) {
	global $i18n;
	global $LANG;

	if (array_key_exists($name, $i18n)) {
		$myVar = $i18n[$name];
	} else {
		# this messes with the global $i18n
		//include_once(GSLANGPATH . 'en_US.php');
		if (array_key_exists($name, $i18n)) {
			$myVar = $i18n[$name];
		} else {
			$myVar = '{'.$name.'}';
		}
	}
	
	if (!$echo) {
		return $myVar;
	} else {
		echo $myVar;
	}
} 

/**
 * Return i18n
 *
 * Same as i18n, but returns instead of echos
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $name
 */
function i18n_r($name) {
	return i18n($name, false);
}

/**
 * Safe AddSlashes HTML
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function safe_slash_html($text) {
	if (get_magic_quotes_gpc()==0) {
		$text = addslashes(htmlentities($text, ENT_QUOTES, 'UTF-8'));
	} else {
		$text = htmlentities($text, ENT_QUOTES, 'UTF-8');
	}
	return $text;
}


/**
 * Safe StripSlashes HTML Decode
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function safe_strip_decode($text) {
	if (get_magic_quotes_gpc()==0) {
		$text = htmlspecialchars_decode($text, ENT_QUOTES);
	} else {
		$text = stripslashes(htmlspecialchars_decode($text, ENT_QUOTES));
	}
	return $text;
}

/**
 * StripSlashes HTML Decode
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function strip_decode($text) {
	$text = stripslashes(htmlspecialchars_decode($text, ENT_QUOTES));
	return $text;
}

/**
 * Safe Pathinfo Filename
 *
 * for backwards compatibility for before PHP 5.2
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $file
 * @return string
 */
function pathinfo_filename($file) { //file.name.ext, returns file.name
   if (defined('PATHINFO_FILENAME')) return pathinfo($file,PATHINFO_FILENAME);
   if (strstr($file, '.')) return substr($file,0,strrpos($file,'.'));
}

/**
 * Suggest Site Path
 *
 * Suggestion function for SITEURL variable
 *
 * @since 2.04
 * @uses $GSAMIN
 * @author ccagle8
 *
 * @param bool $parts 
 * @return string
 */
function suggest_site_path($parts=false) {
	global $GSADMIN;
	$path_parts = pathinfo(htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES));
	$path_parts = str_replace("/".$GSADMIN, "", $path_parts['dirname']);
	$fullpath = "http://". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $path_parts ."/";
	
	if ($parts) {
		return $path_parts;
	} else {
		return $fullpath;
	}
}

/**
 * Myself 
 *
 * Returns the page itself 
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param bool $echo
 * @return string
 */
function myself($echo=true) {
	if ($echo) {
		echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES);
	} else {
		return htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES);
	}
}

/**
 * Get Available Themes 
 *
 * @since 2.04
 * @uses GSTHEMESPATH
 * @author ccagle8
 *
 * @param string $temp
 * @return array
 */
function get_themes($temp) {
	$themes_path = GSTHEMESPATH . $temp .'/';
	$themes_handle = opendir($themes_path);
	while ($file = readdir($themes_handle))	{
		if( is_file($themes_path . $file) && $file != "." && $file != ".." ) {
			$templates[] = $file;
		}
	}
	sort($templates);	
	return $templates;
}

/**
 * HTML Decode 
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function htmldecode($text) {
	return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Safe to LowerCase 
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function lowercase($text) {
	if (function_exists('mb_strtolower')) {
		$text = mb_strtolower($text); 
	} else {
		$text = strtolower($text); 
	}
	
	return $text;
}
?>
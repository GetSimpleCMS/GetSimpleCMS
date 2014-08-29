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
	$code_entities_match   = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=','.'); 
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
	$code_entities_match   = array(' ?',' ','--','&quot;','!','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','='); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = str_replace('%40','@',$text); // ensure @ is not encoded
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
function to7bit($text,$from_enc="UTF-8") {
	if (function_exists('mb_convert_encoding')) {
			$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
		} else {
		$text = htmlspecialchars_decode(utf8_decode(htmlentities($text, ENT_COMPAT, 'utf-8', false)));
	}
		$text = preg_replace(
				array('/&szlig;/','/&(..)lig;/',
						 '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
				array('ss',"$1","$1".'e',"$1"),
				$text);
		return $text;
}


/**
 * Formats Email to HTML Style
 *
 * @since 3.1
 *
 * @global $site_link_back_url
 * @param string $message
 * @return string
 */
function email_template($message) {
	GLOBAL $site_link_back_url;
	$linkurl = $site_link_back_url;
	if(getDef('GSEMAILLINKBACK') && getDef('GSEMAILLINKBACK') !== $linkurl) $linkurl = getDef('GSEMAILLINKBACK');

	$data = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<style>
	 table td p {margin-bottom:15px;}
	 a img {border:none;}
	</style>
	</head>
	<body style="padding:0;margin:0;background: #f3f3f3;font-family:arial, \'helvetica neue\', helvetica, serif" >
	<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="padding: 0 0 35px 0; background: #f3f3f3;">
		<tr>
			<td align="center" style="margin: 0; padding: 0;">
				<center>
					<table border="0" cellpadding="0" cellspacing="0" width="580" style="border-radius:3px;">
						<tr>
							<th style="padding:15px 0 15px 20px;text-align:left;vertical-align:top;background:#171E25;border-radius:4px 4px 0 0;" >
								<a href="'.$linkurl.'"><img src="'.$linkurl.'logo.png" alt="GetSimple CMS"></a>
							</th>
						</tr>
						<tr>
							<td style="background:#fff;border-bottom:1px solid #e1e1e1;border-right:1px solid #e1e1e1;border-left:1px solid #e1e1e1;font-size:13px;font-family:arial, helvetica, sans-serif;padding:20px;line-height:22px;" >
								'.$message.'
							</td>
						</tr>
						<tr>
							<td style="padding-top:10px;font-size:10px;color:#aaa;line-height:14px;font-family:arial, \'helvetica neue\', helvetica, serif" >
								<p class="meta">This is a system-generated email, please do not reply to it. For help or questions about GetSimple, please visit our <a href="'.$site_link_back_url.'" style="color:#aaa;" >website</a>.<br />&copy; '.date('Y').' GetSimple CMS. All Rights Reserved.&nbsp;<a href="'.$linkurl.'start/privacy" style="color:#aaa;" >Privacy Policy</a>. </p>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>
	</body>
	</html>
	';
	return $data;
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
	
	$message = email_template($message);

	if (getDef('GSFROMEMAIL')){
		$fromemail = GSFROMEMAIL; 
	} else {
		if(!empty($_SERVER['SERVER_ADMIN']) && check_email_address($_SERVER['SERVER_ADMIN'])) $fromemail = $_SERVER['SERVER_ADMIN'];
		else $fromemail =  'noreply@'.$_SERVER['SERVER_NAME'];
	}
	
	global $EMAIL;
	$headers  ='"MIME-Version: 1.0' . PHP_EOL;
	$headers .= 'Content-Type: text/html; charset=UTF-8' . PHP_EOL;
	$headers .= 'From: '.$fromemail . PHP_EOL;
	$headers .= 'Reply-To: '.$fromemail . PHP_EOL;
	$headers .= 'Return-Path: '.$fromemail . PHP_EOL;
	
	if( @mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=',"$message",$headers) ) {
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
 * @param string $order - order 'asc' ascending or 'desc' descending
 * @param bool $natural - sort using a "natural order" algorithm
 * @return array
 */
function subval_sort($a,$subkey, $order='asc',$natural = true) {
	if (count($a) != 0 || (!empty($a))) { 
		foreach($a as $k=>$v) {
			if(isset($v[$subkey])) $b[$k] = lowercase($v[$subkey]);
		}

		if(!isset($b)) return $a;

		if($natural){
			natsort($b);
			if($order=='desc') $b = array_reverse($b,true);	
		} 
		else {
			($order=='asc')? asort($b) : arsort($b);
		}
		
		foreach($b as $key=>$val) {
			$c[$key] = $a[$key];
		}

		return $c;
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
	$node = dom_import_simplexml($this);   
	$no   = $node->ownerDocument;   
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
 * @param string $ext optional file extensions to filter
 * @return array
 */
function getFiles($path,$ext = null) {
	$handle   = opendir($path) or die("getFiles: Unable to open $path");
	$file_arr = array();

	while ($file = readdir($handle)) {
		if(isset($ext)){
			$fileext = lowercase(pathinfo($file, PATHINFO_EXTENSION));
			if ($fileext == $ext) $file_arr[] = $file;
		}
		else {
			if ($file != '.' && $file != '..') {
				$file_arr[] = $file;
			}
		}	
	}

	closedir($handle);
	return $file_arr;
}

/**
 * Get XML Files
 * Returns an array of xml files from the passed path
 * @since 3.3.0
 * @param string $path
 * @return array
 */
function getXmlFiles($path) {
	$handle   = opendir($path) or die("Unable to open $path");
	$file_arr = array();

	while ($file = readdir($handle)) {
		$ext = lowercase(pathinfo($file, PATHINFO_EXTENSION));
		if ($ext == 'xml') {
			$file_arr[] = $file;
		}
	}

	closedir($handle);
	return $file_arr;
}

/**
 * execution timer
 * 
 * @since 3.2
 * @uses $microtime_start
 * 
 * @param bool $reset resets global to timestamp
 * @return 
 */
function get_execution_time($reset=false)
{
	GLOBAL $microtime_start;
		if($reset) $microtime_start = null;
		
		if($microtime_start === null)
		{
				$microtime_start = microtime(true);
				return 0.0; 
		}    
		return round(microtime(true) - $microtime_start,5); 
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
	$xml = @file_get_contents($file);
	if($xml){
		$data = simplexml_load_string($xml, 'SimpleXMLExtended', LIBXML_NOCDATA); 
		return $data;
	}	
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
	# get_execution_time(true);
	if(!is_object($xml)) return false;
	$success = @$xml->asXML($file) === TRUE;
	# debugLog('XMLsave: ' . $file . ' ' . get_execution_time());	
	
	if (getDef('GSCHMOD')) {
		return $success && chmod($file, GSCHMOD);
	} else {
		return $success && chmod($file, 0755);
	}
}

/**
 * Formated Date Output, special handling for params on windows
 *
 * @since  3.4.0
 * @author  cnb
 *
 * @param  string $format    A strftime or date format
 * @param  time $timestamp   A timestamp
 * @return string            returns a formated date string
  */
function formatDate($format, $timestamp = null) {
	if(!$timestamp) $timestamp = time();

	if (strpos($format, '%') === false) {
		$date = date($format, $timestamp);
	}
	else {
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
		  # fixes for Windows
		  $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format); // strftime %e parameter not supported
		  $date   = utf8_encode(strftime($format, $timestamp)); // strftime returns ISO-8859-1 encoded string
		} else {
		  $date = strftime($format, $timestamp);
		}
 	}

	return $date;
}

/**
 * Time Output using locale
 *
 * @since 3.4.0
 * @param  str $dt Date/Time String
 * @return str
 */
function output_time($dt = null) {
	if(isset($dt)) $dt = strtotime($dt);
	if(getTimeFormat()) return formatDate(getTimeFormat(),$dt);
}

/**
 * Date/Time Output using locale
 *
 * @since 1.0
 * @param string $dt Date/Time string
 * @return string
 */
function output_datetime($dt = null) {
	if(isset($dt)) $dt = strtotime($dt);
	if(getDateTimeFormat()) return formatDate(getDateTimeFormat(),$dt);
}

/**
 * Date only Output using locale
 *
 * @since 1.0
 * @param string $dt Date/Time string
 * @return string
 */
function output_date($dt = null) {
	if(isset($dt)) $dt = strtotime($dt);
	if(getDateFormat()) return formatDate(getDateFormat(),$dt);
}

// legacy aliases
function shtTime($dt = null){
	return output_time($dt);
}
function shtDate($dt = null){
	return output_date($dt);
}
function lngDate($dt = null){
	return output_datetime($dt);
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
	//$data = preg_replace('/[[:cntrl:]]/', '', $data); //remove control characters that cause interface to choke
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
 * Remove Trailing Slash if missing
 *
 * @since 3.4
 *
 * @param string $path
 * @return string
 */
function no_tsl($path) {
	if( substr($path, -1) == '/' ) {
		$path =  substr($path,0,-1);
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
		$s = pathinfo(htmlentities(getScriptFile(), ENT_QUOTES));
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
	
	if (trim($PERMALINK) != '' && $slug != 'index'){
		$plink = str_replace('%parent%/', $parent, $PERMALINK);
		$plink = str_replace('%parent%', $parent, $plink);
		$plink = str_replace('%slug%', $slug, $plink);
		$url = $full . $plink;
	}

	return (string)$url;
}

/**
 * Strip Path
 *
 * Strips all path info from a filepath or basedir
 *
 * @since 2.0
 * @author Martijn van der Ven
 *
 * @param string $path
 * @return string
 */
function strippath($path) {
	$pathparts = pathinfo($path);
	if(isset($pathparts['extension'])) return $pathparts['filename'].'.'.$pathparts['extension'];
	return $pathparts['basename'];
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
 * Encode Quotes
 *
 * @since 3.0
 *
 * @param string $text
 * @return string
 */
function encode_quotes($text)  { 
	$text = strip_tags($text);

	if (version_compare(PHP_VERSION, "5.2.3")  >= 0) {	
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
	} else {	
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	return trim($text); 
} 

/**
 * Redirect URL
 *
 * @since 3.0
 * @author schlex
 *
 * @param string $url
 */
function redirect($url) {
	global $i18n;

	// handle expired sessions for ajax requests
	if(requestIsAjax() && !cookie_check()){
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: FormBased');
		// @note this is not a security function for ajax, just a handler
		die();
	}	

	if(function_exists('exec_action')) exec_action('redirect');

	if (!headers_sent($filename, $linenum)) {
		header('Location: '.$url);
	} else {
		// @todo not sure this ever gets used or headers_sent is reliable ( turn output buffering off to test )
		echo "<html><head><title>".i18n_r('REDIRECT')."</title></head><body>";
		if ( !isDebug() ) {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.$url.'";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
			echo '</noscript>';
		}

		echo i18n_r('ERROR').": Headers already sent in ".$filename." on line ".$linenum."<br/><br/>\n\n";
		printf(i18n_r('REDIRECT_MSG'), $url);

		if(!isAuthPage()) {
			if (isDebug()){
				global $GS_debug;
				echo '<h2>'.i18n_r('DEBUG_CONSOLE').'</h2><div id="gsdebug">';
				echo '<pre>';

				foreach ($GS_debug as $log){
					print($log.'<br/>');
				}

				echo '</pre>';	
				echo '</div>';
			}
		}
		
		echo "</body></html>";
	}
	
	exit;
}

/**
 * Display i18n
 *
 * Displays the default language's tranlation, but if it
 * does not exist, it falls back to $default if set, else GSMERGELANG else {token}.
 *
 * @since 3.0
 * @author ccagle8
 * @uses GSLANGPATH
 * @uses $i18n
 * @uses $LANG
 *
 * @param string $name
 * @param bool $echo Optional, default is true
 * @param mixed $default default return value if i18n or token not exist, default:true {token}, false:null, str:string
 */
function i18n($name, $echo=true, $default = true) {
	global $i18n;
	global $LANG;

	if(isset($i18n) && isset($i18n[$name])){
		$myVar = $i18n[$name];
	}
	else if($default === true){
		$myVar = '{'.$name.'}'; // if $i18n doesnt exist yet return something
	}
	else if(is_string($default)){
		$myVar = $default;
	}
	else return;

	return echoReturn($myVar,$echo);
}

/**
 * Return i18n
 *
 * Same as i18n, but returns instead of echos
 *
 * @since 3.0
 * @author ccagle8
 *
 * @param string $name
 */
function i18n_r($name,$default = true) {
	return i18n($name, false, $default);
}

/**
 * i18n Merge
 *
 * Merges a plugin's language file with the global $i18n language
 * This is the function that plugin developers will call to initiate the language merge
 *
 * @since 3.0
 * @author mvlcek
 * @uses i18n_merge_impl
 * @uses $i18n
 * @uses $LANG
 *
 * @param string $plugin
 * @param string $language, default=null
 * @return bool
 */
function i18n_merge($plugin, $language=null) {
	global $i18n, $LANG;
	return i18n_merge_impl($plugin, $language ? $language : $LANG, $i18n);
}

/**
 * i18n Merge Implementation
 *
 * Does the merging of a plugin's language file with the global $i18n language
 *
 * @since 3.0
 * @author mvlcek
 * @uses GSPLUGINPATH
 *
 * @param string $plugin null if merging in core langs
 * @param string $lang
 * @param string $globali18n
 * @return bool
 */
function i18n_merge_impl($plugin = '', $lang, &$globali18n) {

	$i18n = array(); // local from file
	if(!isset($globali18n)) $globali18n = array(); //global ref to $i18n

	$path     = (isset($plugin) && $plugin !=='' ? GSPLUGINPATH.$plugin.'/lang/' : GSLANGPATH);
	$filename = $path.$lang.'.php';
	$prefix   = $plugin ? $plugin.'/' : '';

	if (!filepath_is_safe($filename,$path) || !file_exists($filename)) {
		return false;
	}

	include($filename); 

	// if core lang and glboal is empty assign
	if(!$plugin && !$globali18n && count($i18n) > 0){
		$globali18n = $i18n;
	 	return true;
	}

	// replace on per key basis
	if (count($i18n) > 0){
		foreach ($i18n as $code => $text) {
			if (!array_key_exists($prefix.$code, $globali18n)) {
				$globali18n[$prefix.$code] = $text;
			}
		}
	} 
	return true;
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
		$text = addslashes(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
	} else {
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	return xmlFilterChars($text);
}

/**
 * xmlFilterChars
 *
 * @since  3.3.3
 * @param  str $str string to prepare for xml cdata
 * @return str      filtered string
 */
function xmlFilterChars($str){
	$chr = getRegexUnicode();
	// filter only xml allowed characters
	return preg_replace ('/[^'.$chr['ht'].$chr['lf'].$chr['cr'].$chr['lower'].$chr['upper'].']+/u', ' ', $str);
}

/**
 * getRegexUnicode
 * defines unicode char and char ranges for use in regex filters
 *
 * @since  3.3.3
 * @param  str $id key to return from char range array
 * @return mixed     array or str if id specified of regex char strings
 */
function getRegexUnicode($id = null){
	$chars = array(
		'null'       => '\x{0000}',            // 0 null
		'ht'         => '\x{0009}',            // 9 horizontal tab
		'lf'         => '\x{000a}',            // 10 line feed
		'vt'         => '\x{000b}',            // 11 vertical tab
		'FF'         => '\x{000c}',            // 12 form feed
		'cr'         => '\x{000d}',            // 13 carriage return
		'cntrl'      => '\x{0001}-\x{0019}',   // 1-31 control codes
		'cntrllow'   => '\x{0001}-\x{000c}',   // 1-12 low end control codes
		'cntrlhigh'  => '\x{000e}-\x{0019}',   // 14-31 high end control codes
		'bom'        => '\x{FEFF}',            // 65279 BOM byte order mark
		'lower'      => '\x{0020}-\x{D7FF}',   // 32 - 55295
		'surrogates' => '\x{D800}-\x{DFFF}',   // 55296 - 57343
		'upper'      => '\x{E000}-\x{FFFD}',   // 57344 - 65533
		'nonchars'   => '\x{FFFE}-\x{FFFF}',   // 65534 - 65535
		'privateb'   => '\x{10000}-\x{10FFFD}' // 65536 - 1114109
	);

	if(isset($id)) return $chars[$id];
	return $chars;
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
 * Safe Pathinfo Filename, pathinfo php 5.2 compatability wrapper
 *
 * for backwards compatibility for before PHP 5.2
 *
 * @since 3.0
 * @author madvic
 *
 * @todo remove shim support, min requirements is php 5.2
 * @param string $file
 * @return string
 */
function pathinfo_filename($file) {
	if (getDef('PATHINFO_FILENAME')) return pathinfo($file,PATHINFO_FILENAME);

	// php 5.2 support
	$path_parts = pathinfo($file);
	if(isset($path_parts['extension']) && ($file!='..')){
		return substr($path_parts['basename'],0 ,strlen($path_parts['basename'])-strlen($path_parts['extension'])-1);
	} else{
		return $path_parts['basename'];
	}
}

/**
 * Suggest Site Path
 *
 * Suggestion function for SITEURL variable
 *
 * @since 2.04
 * @uses $GSAMIN
 * @uses http_protocol
 * @author ccagle8
 *
 * @param bool $parts 
 * @return string
 */
function suggest_site_path($parts=false, $protocolRelative = false) {
	global $GSADMIN;
	$protocol   = $protocolRelative ? '' : http_protocol().':';
	$path_parts = pathinfo(htmlentities(getScriptFile(), ENT_QUOTES));
	$path_parts = str_replace("/".$GSADMIN, "", $path_parts['dirname']);
	$port       = ( $p=$_SERVER['SERVER_PORT'] ) != '80' && $p != '443' ? ':'.$p : '';
	
	if($path_parts == '/') {
		$fullpath = $protocol."//". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $port . "/";
	} else {
		$fullpath = $protocol."//". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $port . $path_parts ."/";
	}
		
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
		echo htmlentities(getScriptFile(), ENT_QUOTES);
	} else {
		return htmlentities(getScriptFile(), ENT_QUOTES);
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
	$themes_path   = GSTHEMESPATH . $temp .'/';
	$themes_handle = opendir($themes_path);
	while ($file   = readdir($themes_handle))	{
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
		$text = mb_strtolower($text, 'UTF-8'); 
	} else {
		$text = strtolower($text); 
	}
	
	return $text;
}

/**
 * Find AccessKey
 *
 * Provides a simple way to find the accesskey defined by translators as
 * accesskeys are language dependent.
 * 
 * @param string $string, text from the i18n array
 * @return string
 */
function find_accesskey($string) {
	$found   = array();
	$matched = preg_match('/<em>([a-zA-Z])<\/em>/', $string, $found);
	if ($matched != 1) {
		 return null;
	}
	return strtolower($found[1]);
}

/**
 * Clean ID
 *
 * Removes characters that don't work in URLs or IDs
 * 
 * @param string $text
 * @return string
 */
function _id($text) {
	$text = to7bit($text, "UTF-8");
	$text = clean_url($text);
	$text = preg_replace('/[[:cntrl:]]/', '', $text); //remove control characters that cause interface to choke
	return lowercase($text);
}

/**
 * Defined Array
 *
 * Checks an array of PHP constants and verifies they are defined
 * 
 * @param array $constants
 * @return bool
 */
function defined_array($constants) {
	$defined = true;
	foreach ($constants as $constant) {
		if (!defined($constant)) {
			$defined = false;
			break;
		}
	}
	return $defined;
}


/**
 * Is Folder Empty
 *
 * Check to see if a folder is empty or not
 * 
 * @param string $folder
 * @return bool
 */
function check_empty_folder($folder) {
	$files = array ();
	if ( $handle = opendir ( $folder ) ) {
		while ( false !== ( $file = readdir ( $handle ) ) ) {
			if ( $file != "." && $file != ".." ) {
				$files [] = $file;
			}
		}
		closedir ( $handle );
	}
	return ( count ( $files ) > 0 ) ? FALSE : TRUE;
}


/**
 * Folder Items
 *
 * Return the count of items within the given folder
 * 
 * @param string $folder
 * @return string
 */
function folder_items($folder) {
	$files = array ();
	if ( $handle = opendir ( $folder ) ) {
		while ( false !== ( $file = readdir ( $handle ) ) ) {
			if ( $file != "." && $file != ".." ) {
				$files [] = $file;
			}
		}
		closedir($handle);
	}
	return count($files);
}

/**
 * Validate a URL String
 * 
 * @param string $u
 * @return bool
 */
function validate_url($u) {
	return filter_var($u,FILTER_VALIDATE_URL);
}


/**
 * Format XML to Formatted String
 * 
 * @param string $xml
 * @return string
 */
function formatXmlString($xml) {  
	
	// add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
	$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
	
	// now indent the tags
	$token      = strtok($xml, "\n");
	$result     = '';      // holds formatted version as it is built
	$pad        = 0;       // initial indent
	$matches    = array(); // returns from preg_matches()
	
	// scan each line and adjust indent based on opening/closing tags
	while ($token !== false) : 
	
		// test for the various tag states
		
		// 1. open and closing tags on same line - no change
		if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
			$indent=0;
		// 2. closing tag - outdent now
		elseif (preg_match('/^<\/\w/', $token, $matches)) :
			$pad--;
		// 3. opening tag - don't pad this one, only subsequent tags
		elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
			$indent=1;
		// 4. no indentation needed
		else :
			$indent = 0; 
		endif;
		
		// pad the line with the required number of leading spaces
		$line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
		$result .= $line . "\n"; // add to the cumulative result, with linefeed
		$token   = strtok("\n"); // get the next token
		$pad    += $indent;      // update the pad size for subsequent lines    
	endwhile; 
	
	return $result;
}

/**
 * Check Server Protocol
 * 
 * Checks to see if the website should be served using HTTP or HTTPS
 *
 * @since 3.1
 * @return string
 */
function http_protocol() {
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
		return 'https';
	} else {
		return 'http';
	}
}

/**
 * Get File Mime-Type
 *
 * @since 3.1
 * @param $file, absolute path
 * @return string/bool
 */
function file_mime_type($file) {
	if (!file_exists($file)) {
		return false;
		exit;
	}
	if(function_exists('finfo_open')) {
		# http://www.php.net/manual/en/function.finfo-file.php
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_file($finfo, $file);
		finfo_close($finfo);
		
	} elseif(function_exists('mime_content_type')) {
		# Deprecated: http://php.net/manual/en/function.mime-content-type.php
		$mimetype = mime_content_type($file);
	} else {
		return false;
		exit;	
	}
	return $mimetype;
}

/**
 * Check Is FrontEnd
 * 
 * Checks to see if the you are on the frontend or not
 *
 * @since 3.1
 * @return bool
 */
function is_frontend() {
	return GSBASE;
}

/**
 * Get Installed GetSimple Version
 *
 * This will return the version of GetSimple that is installed
 *
 * @since 1.0
 * @uses GSVERSION
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_version($echo=true) {
	include(GSADMININCPATH.'configuration.php');
	if ($echo) {
		echo GSVERSION;
	} else {
		return GSVERSION;
	}
}


/**
 * Get GetSimple Language
 *
 * @since 3.1
 * @uses $LANG
 *
 * @param string
 */
function get_site_lang($short=false) {
	global $LANG;
	if ($short) {
		$LANG_header = preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG);
		return $LANG_header;
	} else {
		return $LANG;
	}
}

/**
 * Convert to Bytes
 *
 * @since 3.0
 *
 * @param $str string
 * @return string
 */
function toBytes($str){
	$val = trim($str);
	$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
	return $val;
}

/**
 * Remove Relative Paths
 *
 * @since 3.1
 *
 * @param $file string
 * @return string
 */
function removerelativepath($file) {
	while(strpos($file,'../')!==false) { 
		$file = str_replace('../','',$file);
	}
	return $file;
}

/**
 * Return a directory of files and folders
 *
 * @since 3.1
 *
 * @param $directory string directory to scan
 * @param $recursive boolean whether to do a recursive scan or not. 
 * @return array or files and folders
 */
function directoryToArray($directory, $recursive) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

/**
 * Return a directory of files and folders with heirarchy and additional data
 *
 * @since 3.1.3
 *
 * @param $directory string directory to scan
 * @param $recursive boolean whether to do a recursive scan or not. 
 * @param $exts array file extension include filter, array of extensions to include
 * @param $exclude bool true to treat exts as exclusion filter instead of include
 * @return multidimensional array or files and folders {type,path,name}
 */
function directoryToMultiArray($dir,$recursive = true,$exts = null,$exclude = false) {
	// $recurse is not implemented

	$result = array();
	$dir = rtrim($dir,DIRECTORY_SEPARATOR);

	$cdir = scandir($dir);
	foreach ($cdir as $key => $value)	{
		if (!in_array($value,array(".",".."))) {
			if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$path =  preg_replace("#\\\|//#", "/", $dir . '/' . $value . '/');
					$result[$value] = array();
					$result[$value]['type'] = "directory";
					$result[$value]['path'] = $path;
					$result[$value]['dir'] = $value;
					$result[$value]['value'] = call_user_func(__FUNCTION__,$path,$recursive,$exts,$exclude);
			}
			else {
				$path =  preg_replace("#\\\|//#", "/", $dir . '/');
				// filetype filter
				$ext = lowercase(pathinfo($value,PATHINFO_EXTENSION));	
				if(is_array($exts)){
					if(!in_array($ext,$exts) and !$exclude) continue;
					if($exclude and in_array($ext,$exts)) continue;
				}

				$result[$value] = array();
				$result[$value]['type'] = 'file';
				$result[$value]['path'] = $path;
				$result[$value]['value'] = $value;
			}
		}
	}

	return $result;
}

/**
 * Returns definition safely
 * 
 * @since 3.1.3
 * 
 * @param str $id 
 * @param bool $isbool treat definition as boolean and cast it
 * @return * returns definition or null if not defined
 */
function getDef($id,$isbool = false){
	if( defined($id) ) {
		if($isbool) return (bool) constant($id);
		return constant($id);
	}
}

/**
 * Alias for checking for debug constant
 * @since 3.2.1
 * @return  bool true if debug enabled
 */
function isDebug(){
	return getDef('GSDEBUG',true);
}

/**
 * check gs version is Alpha
 *
 * @since  3.3.0
 * @return boolean true if Alpha release
 */
function isAlpha(){
	return strPos(get_site_version(false),"a");
}

/**
 * check gs version is Beta
 *
 * @since  3.3.0
 * @return boolean true if beta release
 */
function isBeta(){
	return strPos(get_site_version(false),"b");
}

/**
 * Check if request is an ajax request
 * @since  3.3.0
 * @return bool true if ajax
 */
function requestIsAjax(){
	return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax']);
}

/**
 * check if array is multidimensional
 * @since 3.3.2
 * @param  mixed $ary
 * @return bool true if $ary is a multidimensional array
 */
function arrayIsMultid($ary){
	return is_array($ary) && ( count($ary) != count($ary,COUNT_RECURSIVE) );
}

/**
 * normalizes toolbar setting, always returns js array string syntax
 * @since 3.3.2
 * 
 * @param mixed $var string or array var to convert to js array syntax
 */
function returnJsArray($var){
	
	if(!$var) return;

	if(!is_array($var)) {
		// if looks like an array string try to parse as array
		if(strrpos($var, '[') !==false){
			// normalize array strings
			$var = stripslashes($var);         // remove escaped quotes
			$var = trim(trim($var),',');       // remove trailing commas
			$var = str_replace('\'','"',$var); // replace single quotes with double (for json)
			
			$ary = json_decode($var);
			
			// add primary nest if missing
			if(!is_array($ary) || !arrayIsMultid($ary) ) $ary = json_decode('['.$var.']');
			
			// if proper array use it
			if(is_array($ary) ) $var = json_encode($ary);
			else $var = "'".trim($var,"\"'")."'"; 
		} 
		else{
			// else quote wrap string, trim to avoid double quoting
			$var = "'".trim($var,"\"'")."'";
		}	
	} 
	else {
		// convert php array to js array
		$var = json_encode($var);
	}

	return $var;
}


/**
 * Returns status of mode rewrite
 * @return bool true if on false if not, null if unknown
 */
function hasModRewrite(){
	if ( function_exists('apache_get_modules') ) {
		if(in_arrayi('mod_rewrite',apache_get_modules()) ) {	
			return true;
		}	
	}
	if(getenv('HTTP_MOD_REWRITE') == 'On') return true;
}

/**
 *  @return bool true if we not in an install file
 */
function notInInstall(){
	return ( get_filename_id() != 'install' && get_filename_id() != 'setup' && get_filename_id() != 'update' && get_filename_id() != 'style' );
}

/**
 * Returns a path relative to GSROOTPATH or optional root path
 * @todo  probably not fully windows drive safe
 * @since 3.4
 * @param  string $path full file path
 * @param  string $root optional root path, defaults to GSROOTPATH
 * @return string       relative file path
 */
function getRelPath($path,$root = GSROOTPATH ){
	$relpath = str_replace($root,'',$path);
	return $relpath;
}

/**
 * returns a global, easier inline usage of readonly globals
 * @since  3.4.0
 * @param  str $var variable name
 * @return global
 */
function getGlobal($var) {
	global $$var;
	return $$var;
}

/** 
 * returns a page global 
 * currently an alias for getGlobal
 * @since 3.4.0
 */
function getPageGlobal($var){
	return getGlobal($var);
}

/**
 * echo or return toggle
 * @since  3.4.0
 * @param str $str 
 * @param bool $echo default true, echoes or returns $str
 */
function echoReturn($str,$echo = true){
	if (!$echo) return $str;
	echo $str;
}

/**
 * clamps an integer reference to specified value
 * @since 3.4
 * @param int &$var reference to clamp
 * @param int $min minimum to enforce clamp
 * @param int $max maximum to enforce clamp
 * @param type $default default to set if not set
 */
function clamp(&$var,$min=null,$max=null,$default=null){
	if(is_numeric($var)){
		if(is_numeric($min) && $var < $min) $var = $min;
		if(is_numeric($max) && $var > $max) $var = $max;
	}
	if(isset($default)) setDefault($var,$default);
}

/**
 * set reference to default value if $var not set
 * does no type checking or conversions on default
 * @since 3.4
 * @param $value   reference
 * @param $default default value to set
 */
function setDefault(&$var = '',$default){
	if(!isset($var) || empty($var)) $var = $default;
}


function allowVerCheck(){
	return !isAuthPage() && !getDef('GSNOVERCHECK');
}

function getVerCheck(){
	# check to see if there is a core update needed
	$data = get_api_details();
	if ($data)	{
		return json_decode($data);
	}else {
		return null;
	}
}

/**
 * includeTheme
 *
 * @param  str $template      template name
 * @param  str $template_file template filename
 */
function includeTheme($template, $template_file = GSTEMPLATEFILE){
	# include the functions.php page if it exists within the theme
	if ( file_exists(GSTHEMESPATH .$template."/functions.php") ) {
		include(GSTHEMESPATH .$template."/functions.php");
	}

	# include the template and template file set within theme.php and each page
	if ( (!file_exists(GSTHEMESPATH .$template."/".$template_file)) || ($template_file == '') ) { $template_file = GSTEMPLATEFILE; }
	include(GSTHEMESPATH .$template."/".$template_file);
}

/**
 * get the current accessed script file
 * @since 3.4.0
 * @return str  path to script filename
 */
function getScriptFile(){
	return $_SERVER['SCRIPT_NAME'];
}

/**
 * get custom locale as defined in i18n
 * @since 3.4.0
 * @return str
 */
function getLocaleConfig(){
	return i18n_r("LOCALE",null);
}

/**
 * get date format as defined in i18n
 * @since 3.4.0
 * @return str date format string
 */
function getDateFormat(){
	return i18n_r("DATE_FORMAT",getDef('GSDATEFORMAT'));
}
/**
 * get date time format as defined in i18n
 * @since 3.4.0
 * @return str date time format string
 */
function getDateTimeFormat(){
	return i18n_r("DATE_AND_TIME_FORMAT",getDef('GSDATETIMEFORMAT'));
}
/**
 * get date time format as defined in i18n
 * @since 3.4.0
 * @return str date time format string
 */
function getTimeFormat(){
	return i18n_r("TIME_FORMAT",getDef('GSTIMEFORMAT'));
}
/**
 * get transliteration set as defined in i18n
 * @since 3.4.0
 * @return str
 */
function getTransliteration(){
	return i18n_r("TRANSLITERATION",null);
}

/**
 * set php locale via i18n
 * @since 3.4.0
 * @param str locale str
 */
function  setCustomLocale($locale){
	if ($locale) setlocale(LC_ALL, preg_split('/s*,s*/', $locale));
}

/**
 * Merge GSMERGELANG, a fallback language, into i18n
 * This is a default lang to load after the custom lang to
 * avoid empty lang tokens not found in the custom lang
 *
 * @since 3.4.0
 * @global $LANG
 */
function i18n_mergeDefault(){
	GLOBAL $LANG;
	// Merge in default lang to avoid empty lang tokens
	// if GSMERGELANG is undefined or false merge GSDEFAULTLANG else merge custom
	if(getDef('GSMERGELANG', true) !== false and !getDef('GSMERGELANG', true) ){
		if($LANG !=GSDEFAULTLANG)	i18n_merge(null,GSDEFAULTLANG);
	} else{
		// merge GSMERGELANG defined lang if not the same as $LANG
		if($LANG !=getDef('GSMERGELANG') ) i18n_merge(null,getDef('GSMERGELANG'));
	}
}

/**
 * get the gs editor height config
 * @since 3.4.0
 * @return str string with height units
 */
function getEditorHeight(){
	if (getDef('GSEDITORHEIGHT')) return getDef('GSEDITORHEIGHT') .'px';
}

/**
 * get the gs editor language
 * @since 3.4.0
 * @return str
 */
function getEditorLang(){
	if (getDef('GSEDITORLANG')) return getDef('GSEDITORLANG');
	else if (file_exists(GSADMINTPLPATH.'js/ckeditor/lang/'.i18n_r('CKEDITOR_LANG').'.js')){
		return i18n_r('CKEDITOR_LANG');
	}
}

/**
 * get the gs editor custom options
 * @since 3.4.0
 * @return str js config string
 */
function getEditorOptions(){
	if (getDef('GSEDITOROPTIONS') && trim(getDef('GSEDITOROPTIONS'))!="" ) return getDef('GSEDITOROPTIONS');
}

/**
 * get the gs editor custom toolbar
 * @since 3.4.0
 * @return str valid js nested array ([[ ]]) or escaped toolbar id ('toolbar_id')
 */
function getEditorToolbar(){
	if (getDef('GSEDITORTOOL')) $edtool = getDef('GSEDITORTOOL');
	if($edtool == "none") $edtool = null; // toolbar to use cke default
	// if($edtool === null) $edtool = 'null'; // not supported in cke 3.x

	// at this point $edtool should always be a valid js nested array ([[ ]]) or escaped toolbar id ('toolbar_id')
	return returnJsArray($edtool);
}

/**
 * get defined timezone from user->site->gsconfig
 * @since 3.4.0
 * @return str timezone identifier
 */
function getDefaultTimezone(){
	GLOBAL $USRTIMEZONE, $SITETIMEZONE;
	if(isset($USRTIMEZONE)) return $USRTIMEZONE;
	if(isset($SITETIMEZONE)) return $SITETIMEZONE;
	if(getDef('GSTIMEZONE')) return getDef('GSTIMEZONE');
}

/**
 * set defined timezone
 *
 * @since 3.4.0
 * @param str timezone identifier http://us3.php.net/manual/en/timezones.php
 */
function setTimezone($timezone){
	if(isset($timezone) && function_exists('date_default_timezone_set') && ($timezone != "" || stripos($timezone, '--')) ) {
		date_default_timezone_set($timezone);
	}
}


/**
 * get web root relative url
 * 
 * @since  3.4
 * @var str url to normalize
 */
function getRootRelPath($url){
  $urlparts = parse_url($url);
  $strip    = isset($urlparts['scheme']) ? $urlparts['scheme'] .':' : '';
  $strip   .=  '//';
  $strip   .= isset($urlparts['host']) ? $urlparts['host'] : '';
  debugLog(__FUNCTION__.' base = ' . $strip);
  if(strpos($url,$strip) === 0) return debugLog(str_replace($strip,'',$url));
  return debugLog($url);
}

/**
 * gets website data from GSWEBSITEFILE
 *
 * @todo use a custom schema array for extracting fields
 * @since 3.4.0
 * @param  boolean $returnGlobals return as obj or array of vars
 * @return mixed    depending on returnGlobals returns xml as object or a defined var array for global extraction
 */
function getWebsiteData($returnGlobals = false){
	$thisfilew = GSDATAOTHERPATH .GSWEBSITEFILE;
	if (file_exists($thisfilew)) {
		$dataw        = getXML($thisfilew);
		$SITENAME     = stripslashes( $dataw->SITENAME);
		$SITEURL      = trim((string) $dataw->SITEURL);
		$TEMPLATE     = trim((string) $dataw->TEMPLATE);
		$PRETTYURLS   = trim((string) $dataw->PRETTYURLS);
		$PERMALINK    = trim((string) $dataw->PERMALINK);
		$SITEEMAIL    = trim((string) $dataw->EMAIL);
		$SITETIMEZONE = trim((string) $dataw->TIMEZONE);
		$SITELANG     = trim((string) $dataw->LANG);
		$SITEUSR      = trim((string) $dataw->USR);
	} else {
		$SITENAME = '';
		$SITEURL  = '';
	}

	// asseturl is scheme-less ://url if GSASSETSCHEMES is not true
	$ASSETURL = getDef('GSASSETSCHEMES',true) !==true ? str_replace(parse_url($SITEURL, PHP_URL_SCHEME).':', '', $SITEURL) : $SITEURL;

	if(getDef('GSASSETURLREL')) $ASSETURL = getRootRelPath($ASSETURL);
	if(getDef('GSSITEURLREL'))  $SITEURL  = getRootRelPath($SITEURL);

	unset($thisfilew);
	if($returnGlobals) return get_defined_vars();
	return $dataw;
}

/**
 * gets user data from cookie_user.xml
 * 
 * @since 3.4.0
 * @todo use a custom schema array for extracting fields
 * @param  boolean $returnGlobals return as obj or array of vars
 * @return mixed    depending on returnGlobals returns xml as object or a defined var array for global extraction
 */
function getUserData($returnGlobals = false){

	if (isset($_COOKIE['GS_ADMIN_USERNAME'])) {
		$cookie_user_id = _id($_COOKIE['GS_ADMIN_USERNAME']);
		if (file_exists(GSUSERSPATH . $cookie_user_id.'.xml')) {
			$datau      = getXML(GSUSERSPATH  . $cookie_user_id.'.xml');
			$USR        = stripslashes($datau->USR);
			$HTMLEDITOR = (string) $datau->HTMLEDITOR;
			$USRTIMEZONE= (string) $datau->TIMEZONE;
			$USRLANG    = (string) $datau->LANG;
		} else {
			$USR = null;
		}
	} else {
		$USR = null;
	}

	unset($cookie_user_id);
	if($returnGlobals) return get_defined_vars();
	return $datau;
}


function getDefaultSalt(){
	$salt = null;
	if (defined('GSUSECUSTOMSALT')) {
		// use GSUSECUSTOMSALT
		$salt = sha1(getDef('GSUSECUSTOMSALT'));
	}
	else {
		// use from GSAUTHFILE
		if (file_exists(GSDATAOTHERPATH .GSAUTHFILE)) {
			$dataa = getXML(GSDATAOTHERPATH .GSAUTHFILE);
			$salt  = stripslashes($dataa->apikey);
		}
	}

	return $salt;
}

/**
 * get the default language user->site->gsconfig->GSDEFAULTLANG->filesystem fallback
 * @return str IETF langcode
 */
function getDefaultLang(){
	GLOBAL $USRLANG, $SITELANG;

	if(isset($USRLANG)) return $USRLANG;
	if(isset($SITELANG)) return $SITELANG;
	if(getDef('GSLANG')) return getDef('GSLANG');

	// get language files
	$filenames = glob(GSLANGPATH.'*.php');
	$cntlang   = count($filenames);
	if ($cntlang == 1) {
		// 1 file , assign lang to only existing file
		return basename($filenames[0], ".php");
	} elseif($cntlang > 1 && in_array(GSLANGPATH .GSDEFAULTLANG.'.php',$filenames)) {
		// prefer GSDEFAULTLANG as default if available
		return GSDEFAULTLANG;
	} elseif(isset($filenames[0])) {
		// else fallback to first lang found
		return basename($filenames[0], ".php");
	} else {
		return ''; // no languages available
	}
}

/**
 * perform transliteration conversion on string
 * @param  str $str string to convert
 * @return str      str after transliteration replacement array ran on it
 */
function doTransliteration($str){
	if (getTransliteration() && is_array($translit=getTransliteration()) && count($translit>0)) {
		$str = str_replace(array_keys($translit),array_values($translit),$str);
	}
	return $str;
}

/* ?> */

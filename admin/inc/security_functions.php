<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Security
 *
 * @package GetSimple
 * @subpackage Security-Functions
 */

/*
 * File and File MIME-TYPE Blacklist arrays
 */
$mime_type_blacklist = array(
	# HTML may contain cookie-stealing JavaScript and web bugs
	'text/html', 'text/javascript', 'text/x-javascript',  'application/x-shellscript',
	# PHP scripts may execute arbitrary code on the server
	'application/x-php', 'text/x-php',
	# Other types that may be interpreted by some servers
	'text/x-python', 'text/x-perl', 'text/x-bash', 'text/x-sh', 'text/x-csh',
	# Client-side hazards on Internet Explorer
	'text/scriptlet', 'application/x-msdownload',
	# Windows metafile, client-side vulnerability on some systems
	'application/x-msmetafile',
	# MS Office OpenXML and other Open Package Conventions files are zip files
	# and thus blacklisted just as other zip files
	'application/x-opc+zip'
);

$file_ext_blacklist = array(
	# HTML may contain cookie-stealing JavaScript and web bugs
	'html', 'htm', 'js', 'jsb', 'mhtml', 'mht',
	# PHP scripts may execute arbitrary code on the server
	'php', 'pht', 'phtm', 'phtml', 'php3', 'php4', 'php5', 'ph3', 'ph4', 'ph5', 'phps',
	# Other types that may be interpreted by some servers
	'shtml', 'jhtml', 'pl', 'py', 'cgi', 'sh', 'ksh', 'bsh', 'c', 'htaccess', 'htpasswd',
	# May contain harmful executables for Windows victims
	'exe', 'scr', 'dll', 'msi', 'vbs', 'bat', 'com', 'pif', 'cmd', 'vxd', 'cpl' 
);


/**
 * Anti-XSS
 *
 * Attempts to clean variables from XSS attacks
 * @since 2.03
 *
 * @author Martijn van der Ven
 *
 * @param string $str The string to be stripped of XSS attempts
 * @return string
 */
function antixss($str){
	$strdirty = $str;
	// attributes blacklist:
	$attr = array('style','on[a-z]+');
	// elements blacklist:
	$elem = array('script','iframe','embed','object');
	// extermination:
	$str = preg_replace('#<!--.*?-->?#', '', $str);
	$str = preg_replace('#<!--#', '', $str);
	$str = preg_replace('#(<[a-z]+(\s+[a-z][a-z\-]+\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*)\s+href\s*=\s*(\'javascript:[^\']*\'|"javascript:[^"]*"|javascript:[^\s>]*)((\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>)#is', '$1$5', $str);
	
	foreach($attr as $a) {
	    $regex = '(<[a-z]+(\s+[a-z][a-z\-]+\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*)\s+'.$a.'\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*)((\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>)';
	    $str   = preg_replace('#'.$regex.'#is', '$1$5', $str);
	}

	foreach($elem as $e) {
		$regex = '<'.$e.'(\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>.*?<\/'.$e.'\s*>';
    	$str   = preg_replace('#'.$regex.'#is', '', $str);
	}

	// if($strdirty !== $str) debugLog("string cleaned: removed ". (strlen($strdirty) - strlen($str)) .' chars');

	return $str;
}

function xss_clean($data){
	$datadirty = $data;
	// Fix &entity\n;
	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
	
	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
	
	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
	
	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
	
	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
	
	do
	{
		// Remove really unwanted tags
		$old_data = $data;
		$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	}
	while ($old_data !== $data);
	
	// we are done...
	// if($datadirty !== $data) debugLog("string cleaned: removed ". (strlen($datadirty) - strlen($data)) .' chars');
	return $data;
}


/**
 * check for csrfs
 * @param  string $action action to pass to check_nonce
 * @param  string $file   file to pass to check_nonce
 * @param  bool   $die    if false return instead of die
 * @return bool   returns true if csrf check fails
 */
function check_for_csrf($action, $file="", $die = true){
	// check for csrf
	if (!getDef('GSNOCSRF',true)) {
		$nonce = $_REQUEST['nonce'];
		if(!check_nonce($nonce, $action, $file)) {
			exec_action('csrf'); // @hook csrf a csrf was detected
			if(requestIsAjax()){
				$error = i18n_r("CSRF","CRSF Detected!");
				echo "<div>"; // jquery bug will not parse 1 html element so we wrap it
				include('template/error_checking.php');
				echo "</div>";
				die();
			}
			if($die) die(i18n_r("CSRF","CRSF Detected!"));
			return true;
		}
	}
}


/**
 * Get Nonce
 *
 * @since 2.03
 * @author tankmiche
 * @uses $USR
 * @uses $SALT
 *
 * @param string $action Id of current page
 * @param string $file Optional, default is empty string
 * @param bool $last 
 * @return string
 */
function get_nonce($action, $file = "", $last = false) {
	global $USR;
	global $SALT;

	// set nonce_timeout default and clamps
	include_once(GSADMININCPATH.'configuration.php');
	clamp($nonce_timeout, 60, 86400, 3600);// min, max, default in seconds

	// $nonce_timeout = 10;

	if($file == "")
		$file = getScriptFile();

	// using user agent since ip can change on proxys
	$uid = $_SERVER['HTTP_USER_AGENT'];

	// set nonce time domain to $nonce_timeout or $nonce_timeout x 2 when last is $true
	$time = $last ? time() - $nonce_timeout: time();
	$time = floor($time/$nonce_timeout);

	// Mix with a little salt
	$hash=sha1($action.$file.$uid.$USR.$SALT.$time);
	return $hash;
}


/**
 * Check Nonce
 *
 * @since 2.03
 * @author tankmiche
 * @uses get_nonce
 *
 * @param string $nonce
 * @param string $action
 * @param string $file Optional, default is empty string
 * @return bool
 */	
function check_nonce($nonce, $action, $file = ""){
	return ( $nonce === get_nonce($action, $file) || $nonce === get_nonce($action, $file, true) );
}


/**
 * Validate Safe File
 * NEVER USE MIME CHECKING FROM BROWSERS, eg. $_FILES['userfile']['type'] cannot be trusted
 * @since 3.1
 * @uses file_mime_type
 * @uses $mime_type_blacklist
 * @uses $file_ext_blacklist
 *
 * @param string $file, absolute path
 * @param string $name, filename
 * @param string $mime, optional
 * @return bool
 */	
function validate_safe_file($file, $name, $mime = null){
	global $mime_type_blacklist, $file_ext_blacklist, $mime_type_whitelist, $file_ext_whitelist;

	include(GSADMININCPATH.'configuration.php');

	$file_extension = lowercase(pathinfo($name,PATHINFO_EXTENSION));

	if ($mime && $mime_type_whitelist && in_arrayi($mime, $mime_type_whitelist)) {
		return true;
	}
	if ($file_ext_whitelist && in_arrayi($file_extension, $file_ext_whitelist)) {
		return true;
	}

	// skip blackist checks if whitelists exist
	if($mime_type_whitelist || $file_ext_whitelist) return false;

	if ($mime && in_arrayi($mime, $mime_type_blacklist)) {
		return false;	
	} elseif (in_arrayi($file_extension, $file_ext_blacklist)) {
		return false;	
	} else {
		return true;	
	}
}

/**
 * Checks that an existing filepath is safe to use by checking canonicalized absolute pathname.
 * If file does not exist and realpath fails, we realpath dirname() instead
 *
 * @since 3.1.3
 *
 * @param string $filepath Unknown Path to file to check for safety
 * @param string $pathmatch Known Path to parent folder to check against
 * @param bool $subdir allow path to be a deeper subfolder
 * @param bool $newfile if true fallback and realpath basename, caution, use with other filename sanitizers
 * @return bool Returns true if files path resolves to your known path
 */
function filepath_is_safe($filepath, $pathmatch, $subdir = true, $newfile = false){
	$realpath = realpath($filepath);
	if(!$realpath && $newfile) return path_is_safe(dirname($filepath),$pathmatch,$subdir);

	$realpathmatch = realpath($pathmatch);
	if($subdir) return strpos(dirname($realpath),$realpathmatch) === 0;
	return dirname($realpath) == $realpathmatch;
}

/**
 * Checks that an existing path is safe to use by checking canonicalized absolute path
 *
 * @since 3.1.3
 *
 * @param string $path Unknown Path to check for safety
 * @param string $pathmatch Known Path to check against
 * @param bool $subdir allow path to be a deeper subfolder
 * @return bool Returns true if $path is direct subfolder of $pathmatch
 *
 */
function path_is_safe($path,$pathmatch,$subdir = true){
	$realpath      = realpath($path);
	$realpathmatch = realpath($pathmatch);
	if($subdir) return strpos($realpath,$realpathmatch) === 0;
	return $realpath == $realpathmatch;
}

// alias to check a subdir easily
function subpath_is_safe($path,$dir){
	return path_is_safe($path.$dir,$path);
}

/**
 * Check if server is Apache
 * 
 * @returns bool
 */
function server_is_apache() {
    return( strpos(strtolower(get_Server_Software()),'apache') !== false );
}

/**
 * Try to get server_software
 * 
 * @returns string
 */
function get_Server_Software() {
    return $_SERVER['SERVER_SOFTWARE'];
}

/**
 * Performs filtering on variable, falls back to htmlentities
 *
 * @since 3.3.0
 * @param  string $var    var to filter
 * @param  string $filter filter type
 * @return string         return filtered string
 */
function var_out($var,$filter = "special"){

	// php 5.2 shim
	if(!defined('FILTER_SANITIZE_FULL_SPECIAL_CHARS')){
		define('FILTER_SANITIZE_FULL_SPECIAL_CHARS',522);
		if($filter == "full") return htmlspecialchars($var, ENT_QUOTES);
	}

	if(function_exists( "filter_var") ){
		$aryFilter = array(
			"string"  => FILTER_SANITIZE_STRING,
			"int"     => FILTER_SANITIZE_NUMBER_INT,
			"float"   => FILTER_SANITIZE_NUMBER_FLOAT,
			"url"     => FILTER_SANITIZE_URL,
			"email"   => FILTER_SANITIZE_EMAIL,
			"special" => FILTER_SANITIZE_SPECIAL_CHARS,
		);
		if(isset($aryFilter[$filter])) return filter_var( $var, $aryFilter[$filter]);
		return filter_var( $var, FILTER_SANITIZE_SPECIAL_CHARS);
	}
	else {
		return htmlentities($var);
	}
}

//alias var_out for inputs in case we ned to diverge in future
function var_in($var,$filter = 'special'){
	return var_out($var,$filter);
}

function validImageFilename($file){
	$image_exts = array('jpg','jpeg','gif','png');
	return in_array(getFileExtension($file),$image_exts);
}

/* ?> */

<?php
/**
 * Security
 *
 * @package GetSimple
 * @subpackage init
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
	'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
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
	    $str = preg_replace('#'.$regex.'#is', '$1$5', $str);
	}
	foreach($elem as $e) {
		$regex = '<'.$e.'(\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>.*?<\/'.$e.'\s*>';
	    $str = preg_replace('#'.$regex.'#is', '', $str);
	}
	return $str;
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
	
	if($file == "")
		$file = $_SERVER['PHP_SELF'];
	
	// using user agent since ip can change on proxys
	$uid = $_SERVER['HTTP_USER_AGENT'];
	
	// Limits Nonce to one hour
	$time = $last ? time() - 3600: time(); 
	
	// Mix with a little salt
	$hash=sha1($action.$file.$uid.$USR.$SALT.@date('YmdH',$time));
	
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

/*
 * Validate Safe File
 *
 * @since 3.1
 * @uses file_mime_type
 * @uses $mime_type_blacklist
 * @uses $file_ext_blacklist
 *
 * @param string $file, absolute path
 * @param string $name, default null
 * @param string $type, default 'upload'
 * @return bool
 */	
function validate_safe_file($file, $name, $mime){
	global $mime_type_blacklist, $file_ext_blacklist, $mime_type_whitelist, $file_ext_whitelist;

	include(GSADMININCPATH.'configuration.php');

	$file_extention = pathinfo($name,PATHINFO_EXTENSION);
	$file_mime_type = $mime;

	if ($mime_type_whitelist && in_arrayi($file_mime_type, $mime_type_whitelist)) {
		return true;	
	} elseif ($file_ext_whitelist && $in_arrayi($file_extention, $file_ext_whitelist)) {
		return true;	
	}

	// skip blackist checks if whitelists exist
	if($mime_type_whitelist || $file_ext_whitelist) return false;

	if (in_arrayi($file_mime_type, $mime_type_blacklist)) {
		return false;	
	} elseif (in_arrayi($file_extention, $file_ext_blacklist)) {
		return false;	
	} else {
		return true;	
	}
}

/**
 * Checks that an existing filepath is safe to use by checking canonicalized absolute pathname.
 *
 * @since 3.1.3
 *
 * @param string $path Unknown Path to file to check for safety
 * @param string $pathmatch Known Path to parent folder to check against
 * @param bool $subdir allow path to be a deeper subfolder
 * @return bool Returns true if files path resolves to your known path
 */
function filepath_is_safe($path,$pathmatch,$subdir = true){
	$realpath = realpath($path);
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
	$realpath = realpath($path);
	$realpathmatch = realpath($pathmatch);
	if($subdir) return strpos($realpath,$realpathmatch) === 0;
	return $realpath == $realpathmatch;
}

/**
 * Check if server is Apache
 * 
 * @returns bool
 */
function server_is_apache() {
    return( strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'apache') !== false );
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

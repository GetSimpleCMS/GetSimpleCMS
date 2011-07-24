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
	
	// Any problem with this?
	$ip = $_SERVER['REMOTE_ADDR'];
	
	// Limits Nonce to one hour
	$time = $last ? time() - 3600: time(); 
	
	// Mix with a little salt
	$hash=sha1($action.$file.$ip.$USR.$SALT.date('YmdH',$time));
	
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
	global $mime_type_blacklist;
	global $file_ext_blacklist;

	$file_extention = pathinfo($name,PATHINFO_EXTENSION);
	$file_mime_type = $mime;
	
	if (in_arrayi($file_mime_type, $mime_type_blacklist)) {
		return false;	
	} elseif (in_arrayi($file_extention, $file_ext_blacklist)) {
		return false;	
	} else {
		return true;	
	}
}

?>
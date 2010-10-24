<?php
/**
 * Nonce 
 *
 * @author tankmiche
 * @link http://www.tankmiche.com/
 *
 * @package GetSimple
 * @subpackage XSS
 */

/**
 * Get Nonce
 *
 * @since 2.03
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

?>
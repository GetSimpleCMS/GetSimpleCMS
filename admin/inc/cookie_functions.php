<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Cookie Functions
 *
 * @package GetSimple
 * @subpackage Login
 */

require_once(GSADMININCPATH.'configuration.php');

define('HMACALGO','sha256');
define('COOKIEDELIM',':');

/**
 * Create Cookie
 *
 * @since 1.0
 * @uses $USR
 * @uses $SALT
 * @uses $cookie_time
 * @uses $cookie_name
 *
 *
 */
function create_cookie() {
	global $USR,$SALT,$cookie_time,$cookie_name;

	$saltUSR    = sha1($USR);
	$saltCOOKIE = sha1($cookie_name);
	$expiration = time() + $cookie_time;
	$hash       = hash_hmac( HMACALGO, $saltUSR . $expiration, $SALT );
	$cookie     = $saltUSR . COOKIEDELIM . $expiration . COOKIEDELIM . $hash;

	setcookie($saltCOOKIE, $cookie, $expiration,'/');
	setcookie('GS_ADMIN_USERNAME', $USR, time() + $cookie_time,'/');
}


/**
 * Cookie Checker
 *
 * @since 1.0
 * @uses $SALT
 * @uses $USR
 * @uses $cookie_name
 * @uses GSCOOKIEISSITEWIDE
 *
 * @return bool
 */
function cookie_check() {
	global $USR,$SALT,$cookie_name,$cookie_time;
	$saltUSR      = sha1($USR);
	$saltCOOKIEID = sha1($cookie_name);

	if(!isset($_COOKIE[$saltCOOKIEID])) return false; // cookie doesn't exist
	else $cookie = $_COOKIE[$saltCOOKIEID];

	$cookie_values = explode( COOKIEDELIM, $cookie );
	if(count($cookie_values) < 3) return false; // not enough values

	list( $id, $expiration, $hmac ) = $cookie_values; // split values

	if ( $expiration < time() ) return false; // expired

	$hash = hash_hmac( HMACALGO, $saltUSR . $expiration, $SALT );

	return hash_equals($hash,$hmac);
}

/**
 * Kill Cookie
 *
 * @since 1.0
 * @uses $SALT
 *
 * @params string $identifier Name of the cookie to kill
 */
function kill_cookie($identifier) {
	global $SALT;
	$saltCOOKIE = sha1($identifier.$SALT);
		setcookie('GS_ADMIN_USERNAME', 'null', time() - 3600,'/');  
	if (isset($_COOKIE[$saltCOOKIE])) {
		$_COOKIE[$saltCOOKIE] = false;
		setcookie($saltCOOKIE, false, time() - 3600,'/');
	}
}

/**
 * Check Login Cookie
 *
 * @since 1.0
 * @uses $cookie_login
 * @uses cookie_check
 * @uses redirect
 */
function login_cookie_check() {
	global $cookie_login;
	if(cookie_check()) {
		create_cookie();
	} else {
		$qstring      = filter_queryString(array('id'));
		$redirect_url = $cookie_login.'?redirect='.myself(false).'?'.$qstring;
		redirect($redirect_url);
	}
}

/**
 * Get Cookie
 *
 * @since 1.0
 * @global $_COOKIE
 * @uses cookie_check
 *
 * @return bool
 */
function get_cookie($cookie_name) {
	if(cookie_check($cookie_name) === true) { 
		return $_COOKIE[$cookie_name];
	}
}

if (!function_exists('hash_equals')) {
    /**
     * Use HMAC with a nonce to compare two strings in a manner that is resistant to timing attacks
     *
     * Shim for older PHP versions providing support for the PHP >= 5.6.0 built-in function
     *
     * @param $a string first hash
     * @param $b string second hash
     * @return boolean true if the strings are the same, false otherwise
     */
    function hash_equals($a, $b) {
		$nonce = mcrypt_create_iv(32, MCRYPT_DEV_URANDOM);
        return hash_hmac('sha256', $a, $nonce, true) === hash_hmac('sha256', $b, $nonce, true);
	}
}
/* ?> */

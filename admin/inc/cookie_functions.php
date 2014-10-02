<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Cookie Functions
 *
 * @package GetSimple
 * @subpackage Login
 */

require_once(GSADMININCPATH.'configuration.php');

/**
 * Create Cookie
 *
 * @since 1.0
 * @uses $USR
 * @uses $SALT
 * @uses $cookie_time
 * @uses $cookie_name
 * @return setcookie rwsponse, true if headers not sent
 */
function create_cookie() {
	global $USR,$SALT,$cookie_time,$cookie_name;

	if(!isset($SALT) || empty($SALT)) return;
	if(!isset($USR) || empty($USR)) return;

	$userid     = $USR;
	$cookiename = $cookie_name;
	$expiration = time() + $cookie_time;

	$id         = $userid . GSCOOKIEDELIM . $expiration;
	$hash       = hash_hmac( GSCOOKIEALGO, $id, $SALT );
	$cookie     = $id . GSCOOKIEDELIM . $hash;

	if(getDef('GSOLDCOOKIE',true)) setcookie('GS_ADMIN_USERNAME', $USR, $expiration,'/');
	return setcookie($cookiename, $cookie, $expiration,'/');
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
	// $userid     = $USR;
	$cookiename = $cookie_name;

	if(!isset($SALT) || empty($SALT)) return false; // fail , SALT doesn't exist
	if(!isset($_COOKIE[$cookiename])) return false; // fail, cookie doesn't exist
	else $cookie = $_COOKIE[$cookiename];

	$cookie_values = explode( GSCOOKIEDELIM, $cookie );
	if(count($cookie_values) < 3) return false; // fail, not enough values

	list( $userid, $expiration, $hmac ) = $cookie_values; // split values

	if(empty($userid)) return false; // fail, no username
	if ( $expiration < time() ) return false; // fail, expired

	$hash   = hash_hmac( GSCOOKIEALGO, $userid . GSCOOKIEDELIM . $expiration, $SALT );
	$result = hash_equals($hash,$hmac);
	return $result;
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
	$cookiename = $identifier;
		setcookie('GS_ADMIN_USERNAME', 'null', time() - 3600,'/');  
	if (isset($_COOKIE[$cookiename])) {
		$_COOKIE[$cookiename] = false;
		setcookie($cookiename, false, time() - 3600,'/');
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
 * Gets a Cookie after confirming logged in session
 *
 * @since 1.0
 * @global $_COOKIE
 * @uses cookie_check
 *
 * @return bool
 */
function get_cookie($cookie_name) {
	if(cookie_check() === true) {
		if(isset($_COOKIE[$cookie_name])) return $_COOKIE[$cookie_name];
	}
}

/**
 * Get the logged in user from cookies
 * @return 	str userid from cookie
 */
function getCookieUser(){
	GLOBAL $cookie_name;
	if(cookie_check()){
		$fields = explode(GSCOOKIEDELIM, $_COOKIE[$cookie_name]);
		return $fields[0];
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
	function hash_equals($a, $b)
	{
		// We jump trough some hoops to match the internals errors as closely as possible
		$argc   = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("hash_equals() expects at least 2 parameters, {$argc} given", E_USER_ERROR);
            return null;
        }

        if (!is_string($a)) {
        	trigger_error("\nFatal error: Argument 1 passed to ".__FUNCTION__." must be an instance of string, " . gettype($a) . " given", E_USER_ERROR);
            return false;
        }

        if (!is_string($b)) {
        	trigger_error("\nFatal error: Argument 2 passed to ".__FUNCTION__." must be an instance of string, " . gettype($b) . " given", E_USER_ERROR);
            return false;
        }

		// preffered method of timing attack avoidance is to double hash with nonce to avoid all possible optimizations
		// MCRYPT_DEV_URANDOM not supported in windows pre 5.3, we could use MCRYPT_RAND, but it is slow and blocking
        if(function_exists('mcrypt_create_iv') && defined('MCRYPT_DEV_URANDOM')){
			$nonce = mcrypt_create_iv (32, MCRYPT_DEV_URANDOM);
        	return hash_hmac('sha256', $a, $nonce, true) === hash_hmac('sha256', $b, $nonce, true);
        }

        // falling back to binary safe string compare
        if (strlen($a) !== strlen($b)) {
        	return false;
        }

		$len = strlen($a);
		$result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= (ord($a[$i]) ^ ord($b[$i]));
        }

        // They are only identical strings if $result is exactly 0...
        return 0 === $result;
	}
}

/* ?> */

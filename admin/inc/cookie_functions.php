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
 * @uses GSCOOKIEISSITEWIDE
 */
function create_cookie() {
	global $USR;
  global $SALT;
  global $cookie_time;
  global $cookie_name;
  $saltUSR = $USR.$SALT;
  $saltCOOKIE = sha1($cookie_name.$SALT);
  if ( defined('GSCOOKIEISSITEWIDE') && (GSCOOKIEISSITEWIDE == TRUE) ) {
    setcookie($saltCOOKIE, sha1($saltUSR), time() + $cookie_time,'/');    
  } else {
    setcookie($saltCOOKIE, sha1($saltUSR), time() + $cookie_time);        
  }
}

/**
 * Kill Cookie
 *
 * @since 1.0
 * @uses $SALT
 * @uses GSCOOKIEISSITEWIDE
 *
 * @params string $identifier Name of the cookie to kill
 */
function kill_cookie($identifier) {
  global $SALT;
  $saltCOOKIE = sha1($identifier.$SALT);
  if (isset($_COOKIE[$saltCOOKIE])) {
	  if ( defined('GSCOOKIEISSITEWIDE') && (GSCOOKIEISSITEWIDE == TRUE) ) {
	     $_COOKIE[$saltCOOKIE] = FALSE;
	     setcookie($saltCOOKIE, FALSE, time() - 3600,'/');    
	  } else {
	     $_COOKIE[$saltCOOKIE] = FALSE;
	     setcookie($saltCOOKIE, FALSE, time() - 3600);
	  }
  }
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
 * @params bool $cookie_name Default value is false. Name of the cookie to check
 */
function cookie_check($cookie_name=FALSE) {
	if($cookie_name==FALSE) { // Assume login cookie.
		global $USR;
		global $SALT;
		global $cookie_name;
		$saltUSR = $USR.$SALT;
		$saltCOOKIE = sha1($cookie_name.$SALT);
		if(isset($_COOKIE[$saltCOOKIE])&&$_COOKIE[$saltCOOKIE]==sha1($saltUSR)) {
			return TRUE; // Cookie proves logged in status.
		} else { return FALSE; }
	}
	else if(isset($_COOKIE[$cookie_name])) {
		return TRUE; // Old versions returned the cookie value, use get_cookie() for that!
	}
	else {
		return FALSE;
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
		redirect($cookie_login.'?redirect='.myself(FALSE));
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
	if(cookie_check($cookie_name)==TRUE) { 
		return $_COOKIE[$cookie_name];
	}
}
	
?>
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
 */
function create_cookie() {
	global $USR,$SALT,$cookie_time,$cookie_name;
  $saltUSR = $USR.$SALT;
  $saltCOOKIE = sha1($cookie_name.$SALT);
  setcookie($saltCOOKIE, sha1($saltUSR), time() + $cookie_time,'/'); 
  setcookie('GS_ADMIN_USERNAME', $USR, time() + $cookie_time,'/');   
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
		$_COOKIE[$saltCOOKIE] = FALSE;
		setcookie($saltCOOKIE, FALSE, time() - 3600,'/');
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
 * @return bool
 */
function cookie_check() {
	global $USR,$SALT,$cookie_name;
	$saltUSR = $USR.$SALT;
	$saltCOOKIE = sha1($cookie_name.$SALT);
	if(isset($_COOKIE[$saltCOOKIE])&&$_COOKIE[$saltCOOKIE]==sha1($saltUSR)) {
		return TRUE; // Cookie proves logged in status.
	} else { 
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
		$qstring = filter_queryString(array('id'));
		$redirect_url = $cookie_login.'?redirect='.myself(FALSE).'?'.$qstring;
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
	if(cookie_check($cookie_name)==TRUE) { 
		return $_COOKIE[$cookie_name];
	}
}
	
?>
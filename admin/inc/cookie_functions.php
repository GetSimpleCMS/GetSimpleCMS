<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 		cookie_functions.php
* @Package:	GetSimple
* @Action:	Functions to create and read cookies.	
*
*****************************************************/
require_once(GSADMININCPATH.'configuration.php');

//****************************************************//
//** FUNCTION: create_cookie();  *********************//
//**                                                **//
//** Creates login cookie                           **//
//****************************************************//
	function create_cookie() {
		global $USR;
    global $SALT;
    global $cookie_time;
    global $cookie_name;
    $saltUSR = $USR.$SALT;
    $saltCOOKIE = $cookie_name.$SALT;
    if ( defined('GSCOOKIEISSITEWIDE') && (GSCOOKIEISSITEWIDE == TRUE) ) {
      setcookie($saltCOOKIE, sha1($saltUSR), time() + $cookie_time,'/');    
    } else {
      setcookie($saltCOOKIE, sha1($saltUSR), time() + $cookie_time);        
    }
	}



//****************************************************//
//** FUNCTION: kill_cookie();  ***********************//
//**                                                **//
//** Kills given cookie                             **//
//****************************************************//	
	function kill_cookie($identifier) {
    global $SALT;
    $saltCOOKIE = $identifier.$SALT;
    if ( defined('GSCOOKIEISSITEWIDE') && (GSCOOKIEISSITEWIDE == TRUE) ) {
       $_COOKIE[$saltCOOKIE] = FALSE;
       setcookie($saltCOOKIE, FALSE, time() - 3600,'/');    
    } else {
       $_COOKIE[$saltCOOKIE] = FALSE;
       setcookie($saltCOOKIE, FALSE, time() - 3600);
    }
  }



//****************************************************//
//** FUNCTION: cookie_check();  **********************//
//**                                                **//
//** Checks to see if a cookie is set, if it is, it **// 
//** returns it's value, otherwise it returns FALSE **//
//** When no cookie name is given it checks the     **//
//** login cookie and returns true when the user is **//
//** logged in.                                     **//
//****************************************************//
	function cookie_check($cookie_name=FALSE) {
		if($cookie_name==FALSE) { // Assume login cookie.
			global $USR;
			global $SALT;
			global $cookie_name;
			$saltUSR = $USR.$SALT;
			$saltCOOKIE = $cookie_name.$SALT;
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



//****************************************************//
//** FUNCTION: login_cookie_check();  ****************//
//**                                                **//
//** Checks to see if a user is logged in. if they  **// 
//** are, it returns their userid, otherwise it     **//
//** redirects them back to the login page.         **//
//****************************************************//	
	function login_cookie_check() {
		global $cookie_login;
		if(cookie_check()) {
			create_cookie();
		} else {
			header("Location: ". $cookie_login);
			exit;
		}
	}



//****************************************************//
//** FUNCTION: get_cookie();  ************************//
//**                                                **//
//** Returns a cookie's contents, if any            **//
//****************************************************//
	function get_cookie($cookie_name) {
		if(cookie_check($cookie_name)==TRUE) { 
			return $_COOKIE[$cookie_name];
		}
	}
	
?>
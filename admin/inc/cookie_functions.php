<?php 
/****************************************************
*
* @File: 		cookie_functions.php
* @Package:	GetSimple
* @Action:	Functions to create and read cookies.	
*
*****************************************************/

if (basename($_SERVER['PHP_SELF']) == 'cookie_functions.php') { 
	die('You cannot load this page directly.'); 
} 


require_once('inc/configuration.php'); 


//****************************************************//
//** FUNCTION: create_cookie();  *********************//
//**                                                **//
//** Creates given cookie                           **//
//****************************************************//
	function create_cookie($identifier, $value) {
		global $cookie_extended_time;
		setcookie($identifier, $value, time() + $cookie_extended_time);
	}	



//****************************************************//
//** FUNCTION: kill_cookie();  ***********************//
//**                                                **//
//** Kills given cookie                             **//
//****************************************************//	
	function kill_cookie($identifier) {
		setcookie($identifier, "", time() - 1);
	}
	
	
	
//****************************************************//
//** FUNCTION: login_cookie_check();  ****************//
//**                                                **//
//** Checks to see if a user is logged in. if they  **// 
//** are, it returns their userid, otherwise it     **//
//** redirects them back to the login page.         **//
//****************************************************//	
	function login_cookie_check() {
		global $USR;
		global $cookie_time;
		global $cookie_name;
		global $cookie_login;
		global $cookie_extended_time;
		if(isset($_COOKIE[$cookie_name])) { 	
			// If the cookie is set, pull username and reset the cookie
			setcookie($cookie_name, $USR, time() + $cookie_time);
			return $USR;
		} else {
			// If the cookie has expired, redirect back to login page
			header("Location: ". $cookie_login);
			exit;
		}
	}



//****************************************************//
//** FUNCTION: cookie_check();  **********************//
//**                                                **//
//** Checks to see if a cookie is set, if it is, it **// 
//** returns it's value, otherwise it returns FALSE **//
//****************************************************//
	function cookie_check($cookie_name) {
		global $cookie_time;
		if(isset($_COOKIE[$cookie_name])) { 	
			// If the cookie is set, reset it
			$cookie_value = $_COOKIE[$cookie_name]; 
			//setcookie($cookie_name, $cookie_value, time() + $cookie_time);
			return $cookie_value;
		} else {
			// Cookie has expired or is not set
			return 'FALSE';
		}
	}



//****************************************************//
//** FUNCTION: get_cookie();  ************************//
//**                                                **//
//** Echos a cookie's contents, if any              **//
//****************************************************//
	function get_cookie($cookie_name) {
		$result = cookie_check($cookie_name);
		if ($result != 'FALSE') { 
			return $result; 
		}
	}
	
?>
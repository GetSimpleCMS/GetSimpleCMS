<?php
/****************************************************
*
* @File: 	nonce.php
* @Package:	GetSimple
* @Action:	Protects against CSRF
*
*****************************************************/

//****************************************************//
//** FUNCTION: get_nonce();  *************************//
//**                                                **//
//** Returns the nonce for a certain action of an   **// 
//** admin action, mixing user and global salts.    **//
//** TODO: add an expiration component              **//
//****************************************************//	
	function get_nonce($action, $file = "") {
		global $USR;
		global $SALT;
		
		if($file == "")
			$file = $_SERVER['PHP_SELF'];

		// Any problem with this?
		$ip = $_SERVER['REMOTE_ADDR'];

		// Mix with a little salt
		$hash=sha1($action.$file.$ip.$USR.$SALT);

		return $hash;
	}

//****************************************************//
//** FUNCTION: check_nonce();  ***********************//
//**                                                **//
//** Checks the nonce returned via a POST o GET to  **// 
//** verify if the action is legitimate (not CSRF)  **//
//** TODO: add an expiration component              **//
//****************************************************//	
	function check_nonce($nonce, $action, $file = ""){
		return ( $nonce === get_nonce($action, $file) );
	}

?>
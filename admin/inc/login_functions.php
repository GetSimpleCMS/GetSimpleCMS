<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	  login_functions.php
* @Package:	GetSimple
* @Action:	Functions needed for cp login page. 	
*
*****************************************************/

$MSG = "";



// If the login cookie is already set, redirect user to secure panel
if(cookie_check()) {
	header("Location: ". $cookie_redirect);                                             
}

	if (file_exists(GSDATAOTHERPATH.'user.xml')) {
		$data = getXML(GSDATAOTHERPATH.'user.xml');
		$PASSWD = $data->PWD;
	}

// Was the login form button pressed? If so, continue...
if(isset($_POST['submitted'])) 
{ 
	// Initial variable setup
	$userid = $_POST['userid'];
	$password = $_POST['pwd'];
	$error = '';

	// Is either the Username or Password field empty?
	if ( !$userid || !$password ) 
	{
		$error = 'TRUE';
		$MSG .= '<b>'.$i18n['ERROR'].':</b> '.$i18n['FILL_IN_REQ_FIELD'].'.<br />';
	} 
	
	// If both Username & Password are populated, continue...
	if ( ! $error ) {
		$password = passhash($password);

		// Are the Username and Password both correct?
		if ( ($userid == $USR) && ($password == $PASSWD) ) {
			$authenticated = true;  // Successful Login
		} else {
			$authenticated = false;  // Unsuccessful Login
			
			$xmlfile = GSDATAOTHERPATH.'logs/failedlogins.log';
			
			if ( ! file_exists($xmlfile) ) 	{ 
				$xml = new SimpleXMLExtended('<channel></channel>');
			} else {
				$xmldata = file_get_contents($xmlfile);
				$xml = new SimpleXMLExtended($xmldata);
			}
			
			$thislog = $xml->addChild('entry');
			$thislog->addChild('date', date('r'));
			$cdata = $thislog->addChild('Username');
			$cdata->addCData(htmlentities($userid, ENT_QUOTES));
			$cdata = $thislog->addChild('IP_Address');
			$ip = getenv("REMOTE_ADDR"); 
			$cdata->addCData(htmlentities($ip, ENT_QUOTES));
			XMLsave($xml, $xmlfile);
			
		}
		
		// Was there a Successful Logon attempt?
		if( $authenticated ) {
			// Set the login cookie, then redirect user to secure panel		
			create_cookie();
			header("Location: ". $cookie_redirect); 
		} else {
			$MSG .= '<b>'.$i18n['ERROR'].':</b> '.$i18n['LOGIN_FAILED'].'.';
		}
	}
}
	
?>
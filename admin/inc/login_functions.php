<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	  login_functions.php
* @Package:	GetSimple
* @Action:	Functions needed for cp login page. 	
*
*****************************************************/

$MSG = "";

if(defined('GSLOGINSALT')) { $logsalt = GSLOGINSALT;} else { $logsalt = null; }

// If the login cookie is already set, redirect user to secure panel
if(cookie_check()) 
{
	header("Location: ". $cookie_redirect);                                             
}

	if (file_exists('../data/other/user.xml')) {
		$thisfile = file_get_contents('../data/other/user.xml');
		$data = simplexml_load_string($thisfile);
		$PASSWD = $data->PWD;
	}

// Was the login form button pressed? If so, continue...
if(isset($_POST['submitted'])) 
{ 
	// Initial variable setup
	$userid = $_POST['userid'];
	$password = sha1($_POST['pwd'] . $logsalt);
	$error = '';

	// Is either the Username or Password field empty?
	if ( !$userid || !$password ) 
	{
		$error = 'TRUE';
		$MSG .= '<b>'.$i18n['ERROR'].':</b> '.$i18n['FILL_IN_REQ_FIELD'].'.<br />';
	} 
	
	// If both Username & Password are populated, continue...
	if ( ! $error ) 
	{
		// Are the Username and Password both correct?
		if ( ($userid == $USR) && ($password == $PASSWD) ) {
			$authenticated = true;  // Successful Login
		} else {
			
			$xmlfile = "../data/other/logs/failedlogins.log";
			
			if ( ! file_exists($xmlfile) ) 	{ 
				$xml = new SimpleXMLExtended('<channel></channel>');
			} else {
				$xmldata = file_get_contents($xmlfile);
				$xml = new SimpleXMLExtended($xmldata);
			}
			
			$thislog = $xml->addChild('entry');
			$thislog->addChild('date', date('r'));
			$cdata = $thislog->addChild('Username');
			$cdata->addCData($userid);
			$cdata = $thislog->addChild('IP_Address');
			$ip = getenv ("REMOTE_ADDR"); 
			$cdata->addCData($ip);
			$xml->asXML($xmlfile);
			
		}
		
		// Was there a Successful Logon attempt?
		if( $authenticated ) 
		{
			// Set the login cookie, then redirect user to secure panel		
			create_cookie();
			header("Location: ". $cookie_redirect); 
		} 
		else 
		{
			$MSG .= '<b>'.$i18n['ERROR'].':</b> '.$i18n['LOGIN_FAILED'].'.';
		}
	}
}
	
?>
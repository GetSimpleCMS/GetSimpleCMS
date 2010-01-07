<?php  	
/****************************************************
*
* @File: 	  login_functions.php
* @Package:	GetSimple
* @Action:	Functions needed for cp login page. 	
*
*****************************************************/

	if (basename($_SERVER['PHP_SELF']) == 'login_functions.php') { 
		die('You cannot load this page directly.'); 
	} 

 	
	if (file_exists('../data/other/user.xml')) {
		$thisfile = file_get_contents('../data/other/user.xml');
		$data = simplexml_load_string($thisfile);
		$USR = $data->USR;
		$PASSWD = $data->PWD;
		$EMAIL = $data->EMAIL;
	}
	
	if (file_exists('../data/other/website.xml')) {
		$dataw = getXML('../data/other/website.xml');
		$LANG = $dataw->LANG;
	}
	
	//set internationalization
	if($LANG != '') {
		include('lang/'.$LANG.'.php');
	} else {
		include('lang/en_US.php');
	}
	
	$MSG = "";
	
	// If the login cookie is already set, redirect user to secure panel
	if(cookie_check()) {
  	header("Location: ". $cookie_redirect);                                             
	}
	
	// Was the login form button pressed? If so, continue...
	if(isset($_POST['submitted'])) { 
		
		// Initial variable setup
		$userid = $_POST['userid'];
		$password = sha1($_POST['pwd']);
		$error = '';

		// Is either the Username or Password field empty?
	  if ( !$userid || !$password ) {
	  	$error = 'TRUE';
	  	$MSG .= '<b>'.$i18n['ERROR'].':</b> '.$i18n['FILL_IN_REQ_FIELD'].'.<br />';
	  } 
	  
	  // If both Username & Password are populated, continue...
	  if ( ! $error ) {
			
			// Are the Username and Password both correct?
			if ( $userid == $USR and $password == $PASSWD ) {
				$authenticated = 'TRUE';  // Successful Login
			} else {
				$authenticated = 'FALSE'; // Failed Login
				
				$xmlfile = "../data/other/logs/failedlogins.log";
				if ( ! file_exists($xmlfile) ) { 
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
			if( $authenticated == 'TRUE' ) {
				
				// Set the login cookie, then redirect user to secure panel		
  			create_cookie();
				header("Location: ". $cookie_redirect); 
			} else {
				$MSG .= '<b>'.$i18n['ERROR'].':</b> '.$i18n['LOGIN_FAILED'].'.';
			}
		}
	}
	
?>
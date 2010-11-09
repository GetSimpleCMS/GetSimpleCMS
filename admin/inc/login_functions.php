<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Login Functions
 *
 * @package GetSimple
 * @subpackage Login
 */

$MSG = "";

/* check for legacy version of user.xml */
if (file_exists(GSDATAOTHERPATH .'user.xml')) {
	$datau = getXML(GSDATAOTHERPATH .'user.xml');
	$USR = stripslashes($datau->USR);
	$PASSWD = stripslashes($datau->PWD);
	$EMAIL = stripslashes($datau->EMAIL);
	
	$xml = new SimpleXMLElement('<item></item>');
	$xml->addChild('USR', $USR);
	$xml->addChild('PWD', $PASSWD);
	$xml->addChild('EMAIL', $EMAIL);
	$status = XMLsave($xml, GSDATAOTHERPATH . _id($USR) .'.xml');	
	if ($status) {
		rename(GSDATAOTHERPATH .'user.xml', GSDATAOTHERPATH .'_legacy_user_file.xml');
	}
}


// If the login cookie is already set, redirect user to secure panel
if(cookie_check()) {
	redirect($cookie_redirect);                                             
}



// Was the login form button pressed? If so, continue...
if(isset($_POST['submitted'])) { 
	// Initial variable setup
	$user_xml = GSDATAUSERSPATH . _id($_POST['userid']).'.xml';
	$userid = $_POST['userid'];
	$password = $_POST['pwd'];
	$error = null;
	
	// Is either the Username or Password field empty?
	if ( !$userid || !$password ) {
		$error = true;
		$MSG .= '<b>'.i18n_r('ERROR').':</b> '.i18n_r('FILL_IN_REQ_FIELD').'.<br />';
	} 
	
	// If both Username & Password are populated, continue...
	if ( !$error ) {
		$password = passhash($password);
		
		if (file_exists($user_xml)) {
			$data = getXML($user_xml);
			$PASSWD = $data->PWD;
			$USR = $data->USR;
		} 
		
		// Are the Username and Password both correct?
		if ( ($userid == $USR) && ($password == $PASSWD) ) {
			$authenticated = true;  // Successful Login
		} else {
			$authenticated = false;  // Unsuccessful Login
			
			$xmlfile = GSDATAOTHERPATH.'logs/failedlogins_'._id($USR).'.log';
			
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
			setcookie('GS_ADMIN_USERNAME', _id($USR));
			redirect($cookie_redirect); 
		} else {
			$MSG .= '<b>'.i18n_r('ERROR').':</b> '.i18n_r('LOGIN_FAILED').'.';
		}
	}
}
	
?>
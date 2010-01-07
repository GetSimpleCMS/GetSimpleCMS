<?php
/****************************************************
*
* @File: 	functions.php
* @Package:	GetSimple
* @Action:	Initialize needed functions for cp. 	
*
*****************************************************/

	//disable or enable error reporting
	if (file_exists('../data/other/debug.xml')) {
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 1);
	} else {
		error_reporting(0);
		@ini_set('display_errors', 0);
	}
	ini_set('log_errors', 1);
	ini_set('error_log', '../data/other/logs/errorlog.txt');
	
	
	//include other function files
	require_once('inc/cookie_functions.php');
	require_once('inc/template_functions.php');
	
	
	//get website data
	if (file_exists('../data/other/website.xml')) {
		$thisfilew = '../data/other/website.xml';
		$dataw = getXML($thisfilew);
		$SITENAME = stripslashes($dataw->SITENAME);
		$SITEURL = $dataw->SITEURL;
		$TEMPLATE = $dataw->TEMPLATE;
		$TIMEZONE = $dataw->TIMEZONE;
		$LANG = $dataw->LANG;
	} else {
		$TIMEZONE = 'America/New_York';
		$LANG = 'en_US';
	}
	if (file_exists('../data/other/cp_settings.xml')) {
		$thisfilec = '../data/other/cp_settings.xml';
		$datac = getXML($thisfilec);
		$HTMLEDITOR = $datac->HTMLEDITOR;
		$PRETTYURLS = $datac->PRETTYURLS;
		$FOUR04MONITOR = $datac->FOUR04MONITOR;
	}
	
	if (file_exists('../data/other/user.xml')) {
		$datau = getXML('../data/other/user.xml');
		$USR = stripslashes($datau->USR);
	} else {
		$USR = null;	
	}
	
	if (file_exists('../data/other/authorization.xml')) {
		$dataa = getXML('../data/other/authorization.xml');
		$SALT = stripslashes($dataa->apikey);
	} else {
		$SALT = sha1($USR);
	}
		
	// if there is no siteurl set, redirect user to install setup
	if (get_filename_id() != 'install' && get_filename_id() != 'setup') {
		if (@$SITEURL == '') { 
			header('Location: ../admin/install.php'); 
			exit; 
		}
		if (file_exists('../admin/install.php')) {
			unlink('../admin/install.php');
		}
		if (file_exists('../admin/setup.php')) {
			unlink('../admin/setup.php');
		}
	}
	
	//set timezone if module is available
	if( function_exists('date_default_timezone_set') && ($TIMEZONE != '' || stripos($TIMEZONE, '--')) ) { 
		date_default_timezone_set(@$TIMEZONE);
	}
	
	//set internationalization
	if($LANG != '') {
		include('lang/'.$LANG.'.php');
	} else {
		include('lang/en_US.php');
	}

?>
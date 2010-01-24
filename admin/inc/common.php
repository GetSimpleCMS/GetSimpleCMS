<?php
/****************************************************
*
* @File: 	common.php
* @Package:	GetSimple
* @Action:	Initialize needed functions for cp. 	
*
*****************************************************/

// Define GS
define('IN_GS', TRUE);


// Variable check to prevent debugging going off
$relative = (isset($relative)) ? $relative : '';
$admin_relative = (isset($admin_relative)) ? $admin_relative : '';
$lang_relative = (isset($lang_relative)) ? $lang_relative : '';
$load['login'] = (isset($load['login'])) ? $load['login'] : '';
$load['plugin'] = (isset($load['plugin'])) ? $load['plugin'] : '';

// Debugging
if (file_exists($relative . 'data/other/debug.xml'))
{
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} 
else 
{
	error_reporting(0);
	@ini_set('display_errors', 0);
}

ini_set('log_errors', 1);
ini_set('error_log', $relative . 'data/other/logs/errorlog.txt');

// Basic functionality
include('basic.php');

// Website Data
if (file_exists($relative . 'data/other/website.xml')) {
	$thisfilew = $relative . 'data/other/website.xml';
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

if(!isset($base))
{
	// Settings
	if (file_exists($relative . 'data/other/cp_settings.xml')) {
		$thisfilec = $relative . 'data/other/cp_settings.xml';
		$datac = getXML($thisfilec);
		$HTMLEDITOR = $datac->HTMLEDITOR;
		$PRETTYURLS = $datac->PRETTYURLS;
		$FOUR04MONITOR = $datac->FOUR04MONITOR;
	}

	// User Data
	if (file_exists($relative . 'data/other/user.xml')) {
		$datau = getXML($relative . 'data/other/user.xml');
		$USR = stripslashes($datau->USR);
	} else {
		$USR = null;	
	}

	// Authorization data
	if (file_exists($relative . 'data/other/authorization.xml'))
	{
		$dataa = getXML($relative . 'data/other/authorization.xml');
		$SALT = stripslashes($dataa->apikey);
	}
	else
	{
		$SALT = sha1($USR);
	}
}

// Set correct timestamp if available.
if( function_exists('date_default_timezone_set') && ($TIMEZONE != '' || stripos($TIMEZONE, '--')) )
{ 
	date_default_timezone_set(@$TIMEZONE);
}

// Language control
if($LANG != '')
{
	include($lang_relative . 'lang/' . $LANG . '.php');
} 
else 
{
	include($lang_relative . 'lang/en_US.php');
}

// Globalization
global $SITENAME, $SITEURL, $TEMPLATE, $TIMEZONE, $LANG, $SALT, $i18n, $USR;

// Check for main
if(!isset($base))
{
	// Admin base files
	include('cookie_functions.php');
}

// Template functions
include('template_functions.php');

// Check if site is installed?
if (get_filename_id() != 'install' && get_filename_id() != 'setup')
{
	if ($SITEURL == '')
	{ 
		header('Location: ' . $relative . 'admin/install.php'); 
		exit; 
	}
	
	if (file_exists($relative . 'admin/install.php'))
	{
		unlink($relative . 'admin/install.php');
	}
	
	if (file_exists($relative . 'admin/setup.php'))
	{
		unlink($relative . 'admin/setup.php');
	}
}
	
// Check for main
if(!isset($base))
{
	// for Uploadify security
	$SESSIONHASH = md5($SALT . $SITENAME);
}
else
{
	include('theme_functions.php');
}

// Include other files
if(isset($load['login']) && $load['login']){ 	include('login_functions.php'); }
if(isset($load['plugin']) && $load['plugin']){ 	include('plugin_functions.php'); }

// Define some constants
define('GSADMINPATH', get_admin_path());
define('GSROOTPATH', get_root_path());

// Uncomment & change below to be your specific language for PHP
# setlocale(LC_ALL, 'xx_XX');


?>
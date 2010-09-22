<?php
/****************************************************
*
* @File: 	common.php
* @Package:	GetSimple
* @Action:	Initialize needed functions for cp. 	
*
*****************************************************/

define('IN_GS', TRUE);

// Anti-CSRF, highly experimental
include_once('nonce.php');

// Anti-XSS, highly experimental
include_once('xss.php');
foreach ($_GET as &$xss) $xss = antixss($xss);

// Basic functionality
include('basic.php');
include('template_functions.php');

// Define some constants
define('GSROOTPATH', get_root_path());
define('GSADMINPATH', get_admin_path());
define('GSADMININCPATH', get_admin_path(). 'inc/');
define('GSPLUGINPATH', get_root_path(). 'plugins/');
define('GSLANGPATH', get_admin_path(). 'lang/');
define('GSDATAPATH', get_root_path(). 'data/');
define('GSDATAOTHERPATH', get_root_path(). 'data/other/');
define('GSDATAPAGESPATH', get_root_path(). 'data/pages/');
define('GSDATAUPLOADPATH', get_root_path(). 'data/uploads/');
define('GSTHUMBNAILPATH', get_root_path(). 'data/thumbs/');
define('GSBACKUPSPATH', get_root_path(). 'backups/');
define('GSTHEMESPATH', get_root_path(). 'theme/');

if (file_exists(GSROOTPATH . 'gsconfig.php')) {
	include(GSROOTPATH . 'gsconfig.php');
}

// Debugging
if ( defined('GSDEBUG') && (GSDEBUG == TRUE) ) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	@ini_set('display_errors', 0);
}

ini_set('log_errors', 1);
ini_set('error_log', GSDATAOTHERPATH .'logs/errorlog.txt');


// Variable check to prevent debugging going off
$relative = (isset($relative)) ? $relative : '';
$admin_relative = (isset($admin_relative)) ? $admin_relative : '';
$lang_relative = (isset($lang_relative)) ? $lang_relative : '';
$load['login'] = (isset($load['login'])) ? $load['login'] : '';
$load['plugin'] = (isset($load['plugin'])) ? $load['plugin'] : '';




// Website Data
$thisfilew = GSDATAOTHERPATH .'website.xml';
if (file_exists($thisfilew)) {
	$dataw = getXML($thisfilew);
	$SITENAME = stripslashes($dataw->SITENAME);
	$SITEURL = $dataw->SITEURL;
	$TEMPLATE = $dataw->TEMPLATE;
	$TIMEZONE = $dataw->TIMEZONE;
	$LANG = $dataw->LANG;
} else {
	$TIMEZONE = 'America/New_York';
}



if(!isset($base)) {
	// User Data
	if (file_exists(GSDATAOTHERPATH .'user.xml')) {
		$datau = getXML(GSDATAOTHERPATH .'user.xml');
		$USR = stripslashes($datau->USR);
	} else {
		$USR = null;	
	}
}

// Authorization data
if (file_exists(GSDATAOTHERPATH .'authorization.xml')) {
	$dataa = getXML(GSDATAOTHERPATH .'authorization.xml');
	$SALT = stripslashes($dataa->apikey);
}	else {
	$SALT = sha1($SITEURL);
}
// for form and file security
$SESSIONHASH = md5($SALT . @$SITENAME);

// Settings
if (file_exists(GSDATAOTHERPATH .'cp_settings.xml')) {
	$thisfilec = GSDATAOTHERPATH .'cp_settings.xml';
	$datac = getXML($thisfilec);
	$HTMLEDITOR = $datac->HTMLEDITOR;
	$PRETTYURLS = $datac->PRETTYURLS;
	$PERMALINK = $datac->PERMALINK;
}

// Set correct timestamp if available.
if( function_exists('date_default_timezone_set') && ($TIMEZONE != '' || stripos($TIMEZONE, '--')) )
{ 
	date_default_timezone_set(@$TIMEZONE);
}

// Language control
if(!isset($LANG) || $LANG == '') {
	$LANG = 'en_US';
}
include_once(GSLANGPATH . $LANG . '.php');



// Globalization
global $SITENAME, $SITEURL, $TEMPLATE, $TIMEZONE, $LANG, $SALT, $i18n, $USR, $PERMALINK;

// Check for main
if(!isset($base)) {
	// Admin base files
	include_once(GSADMININCPATH.'cookie_functions.php');
}

// Check if site is installed?
if (get_filename_id() != 'install' && get_filename_id() != 'setup')
{
	if ($SITEURL == '')
	{ 
		header('Location: ' . $relative . 'admin/install.php'); 
		exit; 
	}
	
	if (file_exists(GSADMINPATH.'install.php'))
	{
		unlink(GSADMINPATH.'install.php');
	}
	
	if (file_exists(GSADMINPATH.'setup.php'))
	{
		unlink(GSADMINPATH.'setup.php');
	}
}

// Check for main
if(isset($base)) {
	include_once(GSADMININCPATH.'theme_functions.php');
}

// Include other files
if(isset($load['login']) && $load['login']){ 	include_once(GSADMININCPATH.'login_functions.php'); }
if(isset($load['plugin']) && $load['plugin']){ 	include_once(GSADMININCPATH.'plugin_functions.php'); }

?>
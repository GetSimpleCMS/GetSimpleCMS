<?php
/**
 * Common Setup File
 * 
 * This file initializes up most variables for the site. It is also where most files
 * are included from. It also reads and stores certain variables.
 *
 * @package GetSimple
 * @subpackage init
 */

/**
 * Bad stuff protection
 */
define('IN_GS', TRUE);
include_once('nonce.php');
include_once('xss.php');
if (version_compare(PHP_VERSION, "5")  >= 0) {
	foreach ($_GET as &$xss) $xss = antixss($xss);
}

/**
 * Basic file inclusions
 */
include('basic.php');
include('template_functions.php');

define('GSROOTPATH', get_root_path());

if (file_exists(GSROOTPATH . 'gsconfig.php')) {
	include(GSROOTPATH . 'gsconfig.php');
}

if (defined('GSADMIN')) {
	$GSADMIN = GSADMIN;
} else {
	$GSADMIN = 'admin';
}

/**
 * Define some constants
 */

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


/**
 * Debugging
 */
if ( defined('GSDEBUG') && (GSDEBUG == TRUE) ) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	ini_set('display_errors', 0);
}
ini_set('log_errors', 1);
ini_set('error_log', GSDATAOTHERPATH .'logs/errorlog.txt');


/**
 * Variable check to prevent debugging going off
 */
$admin_relative = (isset($admin_relative)) ? $admin_relative : '';
$lang_relative = (isset($lang_relative)) ? $lang_relative : '';
$load['login'] = (isset($load['login'])) ? $load['login'] : '';
$load['plugin'] = (isset($load['plugin'])) ? $load['plugin'] : '';


/**
 * Grab website data
 */
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


/**
 * Sets up user data
 */
if(!isset($base)) {
	if (isset($_COOKIE['GS_ADMIN_USERNAME'])) {
		if (file_exists(GSDATAOTHERPATH . $_COOKIE['GS_ADMIN_USERNAME'].'.xml')) {
			$datau = getXML(GSDATAOTHERPATH  . $_COOKIE['GS_ADMIN_USERNAME'].'.xml');
			$USR = stripslashes($datau->USR);
		} else {
			$USR = null;	
		}
	} else {
		$USR = null;	
	}
}


/**
 * Authorization and security setup
 */
if (file_exists(GSDATAOTHERPATH .'authorization.xml')) {
	$dataa = getXML(GSDATAOTHERPATH .'authorization.xml');
	$SALT = stripslashes($dataa->apikey);
}	else {
	$SALT = sha1($SITEURL);
}
$SESSIONHASH = sha1($SALT . $SITENAME);


/**
 * Sitewide settings
 */
if (file_exists(GSDATAOTHERPATH .'cp_settings.xml')) {
	$thisfilec = GSDATAOTHERPATH .'cp_settings.xml';
	$datac = getXML($thisfilec);
	$HTMLEDITOR = $datac->HTMLEDITOR;
	$PRETTYURLS = $datac->PRETTYURLS;
	$PERMALINK = $datac->PERMALINK;
}


/**
 * Timezone setup
 */
if( function_exists('date_default_timezone_set') && ($TIMEZONE != '' || stripos($TIMEZONE, '--')) ) { 
	date_default_timezone_set($TIMEZONE);
}


/**
 * Language control
 */
if(!isset($LANG) || $LANG == '') {
	$LANG = 'en_US';
}
include_once(GSLANGPATH . $LANG . '.php');


/**
 * Variable Globalization
 */
global $SITENAME, $SITEURL, $TEMPLATE, $TIMEZONE, $LANG, $SALT, $i18n, $USR, $PERMALINK, $GSADMIN;


/**
 * $base is if the site is being viewed from the front-end
 */
if(!isset($base)) {
	include_once(GSADMININCPATH.'cookie_functions.php');
} else {
	include_once(GSADMININCPATH.'theme_functions.php');
}


/**
 * Check to make sure site is already installed
 */
if (get_filename_id() != 'install' && get_filename_id() != 'setup') {
	if ($SITEURL == '')	{
		$fullpath = suggest_site_path();	
		redirect($fullpath . $GSADMIN.'/install.php');
	}
	if (file_exists(GSADMINPATH.'install.php'))	{
		unlink(GSADMINPATH.'install.php');
	}
	if (file_exists(GSADMINPATH.'setup.php'))	{
		unlink(GSADMINPATH.'setup.php');
	}
}


/**
 * Include other files depending if they are needed or not
 */
if(isset($load['login']) && $load['login']){ 	include_once(GSADMININCPATH.'login_functions.php'); }
if(isset($load['plugin']) && $load['plugin']){ 	include_once(GSADMININCPATH.'plugin_functions.php'); }

?>
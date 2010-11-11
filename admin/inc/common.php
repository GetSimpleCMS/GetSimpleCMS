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
define('GSUSERSPATH', get_root_path(). 'data/users/');
define('GSBACKUSERSPATH', get_root_path(). 'backups/users/');

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
	$PRETTYURLS = $dataw->PRETTYURLS;
	$PERMALINK = $dataw->PERMALINK;
} 


/**
 * Sets up user data
 */
if(!isset($base)) {
	if (isset($_COOKIE['GS_ADMIN_USERNAME'])) {
		if (file_exists(GSUSERSPATH . $_COOKIE['GS_ADMIN_USERNAME'].'.xml')) {
			$datau = getXML(GSUSERSPATH  . $_COOKIE['GS_ADMIN_USERNAME'].'.xml');
			$USR = stripslashes($datau->USR);
			$HTMLEDITOR = $datau->HTMLEDITOR;
			$TIMEZONE = $datau->TIMEZONE;
			$LANG = $datau->LANG;
		} else {
			$USR = null;
			$TIMEZONE = 'America/New_York';	
		}
	} else {
		$USR = null;
		$TIMEZONE = 'America/New_York';
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
if (get_filename_id() != 'install' && get_filename_id() != 'setup' && get_filename_id() != 'update') {
	$fullpath = suggest_site_path();
	
	# if an update file was included in the install package, redirect there first	
	if (file_exists(GSDATAOTHERPATH .'user.xml')) {
		if (file_exists(GSADMINPATH.'update.php'))	{
			redirect($fullpath . $GSADMIN.'/update.php');
		}
	}
	
	# if there is no SITEURL set, then it's a fresh install. Start installation process
	if ($SITEURL == '')	{
		redirect($fullpath . $GSADMIN.'/install.php');
	} 
	
	# if you've made it this far, the site is already installed so remove the installation files
	if (file_exists(GSADMINPATH.'install.php'))	{
		unlink(GSADMINPATH.'install.php');
	}
	if (file_exists(GSADMINPATH.'setup.php'))	{
		unlink(GSADMINPATH.'setup.php');
	}
	if (file_exists(GSADMINPATH.'update.php'))	{
		unlink(GSADMINPATH.'update.php');
	}
}


/**
 * Include other files depending if they are needed or not
 */
if(isset($load['login']) && $load['login']){ 	include_once(GSADMININCPATH.'login_functions.php'); }
if(isset($load['plugin']) && $load['plugin']){ 	include_once(GSADMININCPATH.'plugin_functions.php'); }

?>
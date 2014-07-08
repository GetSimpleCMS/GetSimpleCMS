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

define('IN_GS', TRUE); // GS enviroment flag

// GS Debugger
GLOBAL $GS_debug; // GS debug trace array
if(!isset($GS_debug)) $GS_debug = array();

/**
 * Set PHP enviroment
 */
if(function_exists('mb_internal_encoding')) mb_internal_encoding("UTF-8"); // set multibyte encoding

/**
 *  GSCONFIG definitions
 */

$GS_constants = array(
	'GSTARTTIME'            => microtime(),
 	'GSBASE'                => false,          // front end flag
	'GSROOTPATH'            => dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR, // root path of getsimple
	'GSCONFIGFILE'          => 'gsconfig.php', // config filename
	'GSSTYLEWIDE'           => 'wide',         // wide stylesheet
	'GSSTYLE_SBFIXED'       => 'sbfixed',      // fixed sidebar
	'GSCSSMAINFILE'         => 'css.php',
	'GSCSSCUSTOMFILE'       => 'admin.css',
	'GSCONSTANTSLOADED'     => true          // loaded flag
);

$GS_definitions = array(
	// 'GSHEADERCLASS'     => 'gradient',     // custom class to add to header
	'GSHTTPPREFIX'         => '',             // http slug prefix GSHTTPPREFIX.GSSLUGxx
	'GSSLUGNOTFOUND'       => '404',          // http slug for not found
	'GSSLUGPRIVATE'        => '403',          // http slug for private pages
	'GSADMIN'              => 'admin',        // admin foldername
	'GSERRORLOGFILE'       => 'errorlog.txt', // error log filename
	'GSSTYLE'              => 'wide,sbfixed', // default style modifiers
	'GSWIDTH'              => '1024px',       // pagewidth on backend, widths implemented as max-width, defaults to 100%
	'GSWIDTHWIDE'          => '1366px',       // page width on backend pages defined in GSWIDEPAGES
	'GSWIDEPAGES'          => 'theme-edit,components', // pages to apply GSWIDTHWIED on
	'GSALLOWLOGIN'         => true,           // allow front end login
	'GSALLOWRESETPASS'     => true,           // allow front end login
	'GSTHEMEEDITEXTS'      => 'php,css,js,html,htm,txt,xml,', // file extensions to show and edit in theme editor
	'GSDEFINITIONSLOADED'  => true            // loaded flag
);

/* Define Constants */
GS_defineFromArray($GS_constants);

/**
 * Variable Globalization
 */
global
 $SITENAME,       // (str) sitename setting
 $SITEURL,        // (str) siteurl setting
 $TEMPLATE,       // (str) current theme
 $TIMEZONE,       // (str) current timezone either from config or user
 $LANG,           // (str) settings language
 $SALT,           // (str) salt holds gsconfig GSUSECUSTOMSALT or authentication.xml salt
 $i18n,           // (array) holds global i18n token array
 $USR,            // (str) holds the GS_ADMIN_USERNAME cookie value
 $PERMALINK,      // (str) permalink structure
 $GSADMIN,        // (str) admin foldername
 $GS_debug,       // (array) debug log entries are stored here
 $components,     // (array) components array, array of objs from components.xml
 $nocache,        // (bool) disable site wide cache true, not fully implemented
 $microtime_start,// (microtime) used for benchmark timers
 $pagesArray,     // (array) page cache array, used for all page fields aside from content
 $pageCacheXml,    // (obj) page cache raw xml simpleXMLobj
 $plugins_info,
 $live_plugins,
 $plugins,
 $filters,
 $GS_scripts,
 $GS_styles
;

if(isset($_GET['nocache'])){
	// @todo: disables caching, this should probably only be allowed for auth users, it is also not well inplemented
	$nocache = true;
}

/*
 * If backend Load config, else do front end stuff
 */
if(!GSBASE){
	if (file_exists(GSROOTPATH . GSCONFIGFILE)){
		require_once(GSROOTPATH . GSCONFIGFILE);
	}
}
else {
	$base = GSBASE; // LEGACY frontend flag DEPRECATED

	// set loaders, if you want to override these do it your main common wrapper or index.php
	if(!isset($load['plugin']))   $load['plugin'] = true;   // load plugin system
	if(!isset($load['template'])) $load['template'] = true; // load template system
}

/*
 * Apply default definitions
 */
GS_defineFromArray($GS_definitions);
$GSADMIN       = rtrim(GSADMIN,'/\\'); // global GS admin root folder name

/**
 * Define some constants
 */
define('GSADMINPATH'     , GSROOTPATH      . $GSADMIN.'/'); // admin/
define('GSADMININCPATH'  , GSADMINPATH     . 'inc/');       // admin/inc/
define('GSPLUGINPATH'    , GSROOTPATH      . 'plugins/');   // plugins/
define('GSLANGPATH'      , GSADMINPATH     . 'lang/');      // lang/

// data
define('GSDATAPATH'      , GSROOTPATH      . 'data/');      // data/
define('GSDATAOTHERPATH' , GSDATAPATH      . 'other/');     // data/other/
define('GSDATAPAGESPATH' , GSDATAPATH      . 'pages/');     // data/pages/

define('GSAUTOSAVEPATH'  , GSDATAPAGESPATH . 'autosave/');  // data/pages/autosave/
define('GSDATAUPLOADPATH', GSDATAPATH      . 'uploads/');   // data/uploads/
define('GSTHUMBNAILPATH' , GSDATAPATH      . 'thumbs/');    // data/thumbs/
define('GSUSERSPATH'     , GSDATAPATH      . 'users/');     // data/users/
define('GSCACHEPATH'     , GSDATAPATH      . 'cache/');     // data/cache/

define('GSBACKUPSPATH'   , GSROOTPATH      . 'backups/');   // backups/
define('GSBACKUSERSPATH' , GSBACKUPSPATH   . 'users/');     // backups/users
define('GSTHEMESPATH'    , GSROOTPATH      . 'theme/');     // theme/

$reservedSlugs = array($GSADMIN,'data','theme','plugins','backups');

/**
 * Init debug mode
 * Enable php error logging
 */
if(defined('GSDEBUG') and (bool)GSDEBUG == true) {
	error_reporting(-1);
	ini_set('display_errors', 1);
	$nocache = true;
} else if( defined('SUPRESSERRORS') and (bool)SUPPRESSERRORS == true ) {
	error_reporting(0);
	ini_set('display_errors', 0);
}

ini_set('log_errors', 1);
ini_set('error_log', GSDATAOTHERPATH .'logs/'. GSERRORLOGFILE);

/**
 * Basic file inclusions
 */
include('basic.php');
include('template_functions.php');
include('theme_functions.php');
include('logging.class.php');

require_once(GSADMININCPATH.'configuration.php');

/**
 * Bad stuff protection
 */
include_once('security_functions.php');

if (version_compare(PHP_VERSION, "5")  >= 0) {
	foreach ($_GET as &$xss) $xss = antixss($xss);
}


/**
 * Headers
 */

// charset utf-8
if(get_filename_id() != 'style' ) header('content-type: text/html; charset=utf-8');

// no-cache headers
if(!is_frontend()){
	$timestamp = gmdate("D, d M Y H:i:s") . " GMT";
	header("Expires: " . $timestamp);
	header("Last-Modified: " . $timestamp);
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
}


/**
 * Pull data from storage
 */

/** grab website data */

$thisfilew = GSDATAOTHERPATH .'website.xml';
if (file_exists($thisfilew)) {
	$dataw      = getXML($thisfilew);
	$SITENAME   = stripslashes($dataw->SITENAME);
	$SITEURL    = $dataw->SITEURL;
	$TEMPLATE   = $dataw->TEMPLATE;
	$PRETTYURLS = $dataw->PRETTYURLS;
	$PERMALINK  = $dataw->PERMALINK;
} else {
	$SITENAME = '';
	$SITEURL  = '';
}

/** grab user data */

if (isset($_COOKIE['GS_ADMIN_USERNAME'])) {
	$cookie_user_id = _id($_COOKIE['GS_ADMIN_USERNAME']);
	if (file_exists(GSUSERSPATH . $cookie_user_id.'.xml')) {
		$datau      = getXML(GSUSERSPATH  . $cookie_user_id.'.xml');
		$USR        = stripslashes($datau->USR);
		$HTMLEDITOR = $datau->HTMLEDITOR;
		$TIMEZONE   = $datau->TIMEZONE;
		$LANG       = $datau->LANG;
	} else {
		$USR = null;
	}
} else {
	$USR = null;
}


/** grab authorization and security data */

if (defined('GSUSECUSTOMSALT')) {
	// use GSUSECUSTOMSALT
	$SALT = sha1(GSUSECUSTOMSALT);
}
else {
	// use from authorization.xml
	if (file_exists(GSDATAOTHERPATH .'authorization.xml')) {
		$dataa = getXML(GSDATAOTHERPATH .'authorization.xml');
		$SALT  = stripslashes($dataa->apikey);
	} else {
		if($SITEURL !='' && notInInstall()) die(i18n_r('KILL_CANT_CONTINUE')."<br/>".i18n_r('MISSING_FILE').": "."authorization.xml");
	}
}

$SESSIONHASH = sha1($SALT . $SITENAME);

/**
 * Language control
 */
if(!isset($LANG) || $LANG == '') {
	$filenames = glob(GSLANGPATH.'*.php');
	$cntlang = count($filenames);
	if ($cntlang == 1) {
		// assign lang to only existing file
		$LANG = basename($filenames[0], ".php");
	} elseif($cntlang > 1 && in_array(GSLANGPATH .'en_US.php',$filenames)) {
		// fallback to en_US if it exists
		$LANG = 'en_US';
	} elseif(isset($filenames[0])) {
		// fallback to first lang found
		$LANG=basename($filenames[0], ".php");
	}
}

i18n_merge(null); // load $LANG file into $i18n

// Merge in default lang to avoid empty lang tokens
// if GSMERGELANG is undefined or false merge en_US else merge custom
if(getDef('GSMERGELANG', true) !== false and !getDef('GSMERGELANG', true) ){
	if($LANG !='en_US')	i18n_merge(null,"en_US");
} else{
	// merge GSMERGELANG defined lang if not the same as $LANG
	if($LANG !=getDef('GSMERGELANG') ) i18n_merge(null,getDef('GSMERGELANG'));
}

// Set Locale
if (array_key_exists('LOCALE', $i18n))
  setlocale(LC_ALL, preg_split('/s*,s*/', $i18n['LOCALE']));

/**
 * Init Editor globals
 * @uses $EDHEIGHT
 * @uses $EDLANG
 * @uses $EDTOOL js array string | php array | 'none' | ck toolbar_ name
 * @uses $EDOPTIONS js obj param strings, comma delimited
 */
if (defined('GSEDITORHEIGHT')) { $EDHEIGHT = GSEDITORHEIGHT .'px'; } else {	$EDHEIGHT = '500px'; }
if (defined('GSEDITORLANG'))   { $EDLANG = GSEDITORLANG; } else {	$EDLANG = i18n_r('CKEDITOR_LANG'); }
if (defined('GSEDITORTOOL') and !isset($EDTOOL)) { $EDTOOL = GSEDITORTOOL; }
if (defined('GSEDITOROPTIONS') and !isset($EDOPTIONS) && trim(GSEDITOROPTIONS)!="" ) $EDOPTIONS = GSEDITOROPTIONS;

if(!isset($EDTOOL)) $EDTOOL = 'basic'; // default gs toolbar

if($EDTOOL == "none") $EDTOOL = null; // toolbar to use cke default
$EDTOOL = returnJsArray($EDTOOL);
// if($EDTOOL === null) $EDTOOL = 'null'; // not supported in cke 3.x
// at this point $EDTOOL should always be a valid js nested array ([[ ]]) or escaped toolbar id ('toolbar_id')

/**
 * Timezone setup
 */

// set defined timezone from config if not set on user
if( (!isset($TIMEZONE) || trim($TIMEZONE) == '' ) && defined('GSTIMEZONE') ){
	$TIMEZONE = GSTIMEZONE;
}

if(isset($TIMEZONE) && function_exists('date_default_timezone_set') && ($TIMEZONE != "" || stripos($TIMEZONE, '--')) ) {
	date_default_timezone_set($TIMEZONE);
}

/**
 * Check to make sure site is already installed
 */
if (notInInstall()) {
	$fullpath = suggest_site_path();

	# if there is no SITEURL set, then it's a fresh install. Start installation process
	# siteurl check is not good for pre 3.0 since it will be empty, so skip and run update first.
	if ($SITEURL == '' &&  get_gs_version() >= 3.0)	{
		serviceUnavailable();
		redirect($fullpath . $GSADMIN.'/install.php');
	}
	else {
	# if an update file was included in the install package, redirect there first
		if (file_exists(GSADMINPATH.'update.php') && !isset($_GET['updated']) && !getDef('GSDEBUGINSTALL'))	{
			serviceUnavailable();
			redirect($fullpath . $GSADMIN.'/update.php');
		}
	}

	if(!getDef('GSDEBUGINSTALL',true)){
		# if you've made it this far, the site is already installed so remove the installation files
		$filedeletionstatus=true;
		if (file_exists(GSADMINPATH.'install.php'))	{
			$filedeletionstatus = unlink(GSADMINPATH.'install.php');
		}
		if (file_exists(GSADMINPATH.'setup.php'))	{
			$filedeletionstatus = unlink(GSADMINPATH.'setup.php');
		}
		if (file_exists(GSADMINPATH.'update.php'))	{
			$filedeletionstatus = unlink(GSADMINPATH.'update.php');
		}
		if (!$filedeletionstatus) {
			$error = sprintf(i18n_r('ERR_CANNOT_DELETE'), '<code>/'.$GSADMIN.'/install.php</code>, <code>/'.$GSADMIN.'/setup.php</code> or <code>/'.$GSADMIN.'/update.php</code>');
		}
	}

		}


/**
 * Include other files depending if they are needed or not
 */
include_once(GSADMININCPATH.'cookie_functions.php');

if(isset($load['plugin']) && $load['plugin']){
	# remove the pages.php plugin if it exists.
	if (file_exists(GSPLUGINPATH.'pages.php'))	{
		unlink(GSPLUGINPATH.'pages.php');
	}

	include_once(GSADMININCPATH.'plugin_functions.php');

	if(get_filename_id()=='settings' || get_filename_id()=='load') {
		/* this core plugin only needs to be visible when you are viewing the
		settings page since that is where its sidebar item is. */
		if (getDef('GSEXTAPI',true)) {
			include_once('api.plugin.php');
		}
	}

	# include core plugin for page caching
	include_once('caching_functions.php');

	# main hook for common.php
	exec_action('common');

}

if(isset($load['login']) && $load['login'] && getDef('GSALLOWLOGIN',true)){ include_once(GSADMININCPATH.'login_functions.php'); }

// do the template rendering
if(GSBASE) include_once(GSADMINPATH.'base.php');


// common methods are immediatly available

/**
 * Debug Console Log
 * @since 3.1
 * @param $txt string
 */
function debugLog($txt = '') {
	global $GS_debug;
	array_push($GS_debug,$txt);
}

/**
 * Define from an array
 * @param array assoc of keyed values [DEFINITIONNAME] => value
 */
function GS_defineFromArray($definitions){
	foreach($definitions as $definition => $value){
		if(!defined($definition)) define($definition,$value);
	}
}

/**
 * service is unavailable
 * performs a service unavailable if front end
 */
function serviceUnavailable(){
	if(is_frontend()){
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 7200'); // in seconds
		i18n('SERVICE_UNAVAILABLE');
		die();
	}
}

/* ?> */

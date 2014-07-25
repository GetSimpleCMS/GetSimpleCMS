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

// @todo remove for production
// debug catcher for this core wide change issues
if(htmlentities($_SERVER['SCRIPT_NAME'], ENT_QUOTES) !== htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)) die('PHP_SELF mismatch ' . $_SERVER['PHP_SELF']);

/**
 * Set PHP enviroments
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
	'GSWEBSITEFILE'         => 'website.xml',  // website data filename
	'GSAUTHFILE'            => 'authorization.xml',  // authorizaton salt data filename
	'GSCSSMAINFILE'         => 'css.php',
	'GSCSSCUSTOMFILE'       => 'admin.css',
	'GSTEMPLATEFILE'        => 'template.php',
	'GSSITEMAPFILE'         => 'sitemap.xml',
	'GSINSTALLTEMPLATE'     => 'Innovation',   // tempalte to set on install
	'GSINSTALLPLUGINS'      => 'InnovationPlugin.php', // comma delimited list of plugins to activate on install
	'GSSTYLEWIDE'           => 'wide',         // wide stylesheet
	'GSSTYLE_SBFIXED'       => 'sbfixed',      // fixed sidebar
	'GSFRONT'               => 1,
	'GSBACK'                => 2,
	'GSBOTH'                => 3,
	'GSDEFAULTLANG'         => 'en_US',      // default language for core
	'GSTITLEMAX'            => '70',         // max length allowed for titles
	'GSFILENAMEMAX'         => '255',        // max length allowed for file names/slugs
	'GSCONSTANTSLOADED'     => true          // $GS_constants IS LOADED FLAG
);

$GS_definitions = array(
	// 'GSHEADERCLASS'     => 'gradient',     // custom class to add to header
	'GSHTTPPREFIX'         => '',             // http slug prefix GSHTTPPREFIX.GSSLUGxx
	'GSSLUGNOTFOUND'       => '404',          // http slug for not found
	'GSSLUGPRIVATE'        => '403',          // http slug for private pages
	'GSADMIN'              => 'admin',        // admin foldername
	'GSERRORLOGFILE'       => 'errorlog.txt', // error log filename
	'GSERRORLOGENABLE'     => true,           // (bool) should GS log php errors to GSERRORLOGFILE
	'GSSTYLE'              => 'wide,sbfixed', // default style modifiers
	'GSWIDTH'              => '1024px',       // pagewidth on backend, widths implemented as max-width, defaults to 100%
	'GSWIDTHWIDE'          => '1366px',       // page width on backend pages defined in GSWIDEPAGES
	'GSWIDEPAGES'          => 'theme-edit,components', // pages to apply GSWIDTHWIED on
	'GSALLOWLOGIN'         => true,           // (bool) allow front end login
	'GSALLOWRESETPASS'     => true,           // (bool) allow front end password resets
	'GSTHEMEEDITEXTS'      => 'php,css,js,html,htm,txt,xml,', // file extensions to show and edit in theme editor
	'GSASSETSCHEMES'       => false,          // (bool) should $ASSETURL contian the url scheme http|https
	'GSALLOWDOWNLOADS'     => true,           // (bool) allow using downloads.php to download files from /uploads and backups/zip
	'GSEDITORHEIGHT'       => '500',          // (str) wysiwyg editor height in px
	'GSEDITORTOOL'         => 'basic',        // (str) wysiwyg editor toobar
	'GSEDITORCONFIGFILE'   => 'config.js',    // (str) wysiwyg editor toobar
	'GSEMAILLINKBACK'      => 'http://get-simple.info/', // url used in email template
 	'GSDEFINITIONSLOADED'  => true	          // $GS_definitions IS LOADED FLAG
);

/* Define Constants */
GS_defineFromArray($GS_constants);

/**
 * Variable Globalization
 */
global
 $TEMPLATE,       // (str) current theme
 $USR,            // (str) holds the GS_ADMIN_USERNAME cookie value
 $GSADMIN,        // (str) admin foldername
 $GS_debug,       // (array) global array for storing debug log entries
 $components,     // (array) global array for storing components, array of objs from components.xml
 $nocache,        // (bool) disable site wide cache true, not fully implemented
 $microtime_start,// (microtime) used for benchmark timers
 $pagesArray,     // (array) global array for storing pages cache, used for all page fields aside from content
 $pageCacheXml,   // (obj) page cache raw xml simpleXMLobj
 $plugins_info,   // (array) contains registered plugin info for active and inactive plugins
 $live_plugins,   // (array) contains plugin file ids and enable status
 $plugins,        // (array) global array for storing action hook callbacks
 $filters,        // (array) global array for storing action filter callbacks
 $GS_scripts,     // (array) global array for storing queued asset scripts
 $GS_styles       // (array) global array for storing queued asset styles
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
		include_once(GSROOTPATH . GSCONFIGFILE);
	}
}
else {
	$base = GSBASE; // LEGACY frontend flag DEPRECATED

	// set loaders, if you want to override these do it your main common wrapper or index.php
	if(!isset($load['plugin']))   $load['plugin']   = true;   // load plugin system
	if(!isset($load['template'])) $load['template'] = true; // load template system
}

/*
 * Apply default definitions
 */
GS_defineFromArray($GS_definitions);
$GSADMIN = rtrim(GSADMIN,'/\\'); // global GS admin root folder name

/**
 * Define Paths
 */
define('GSADMINPATH'     , GSROOTPATH      . $GSADMIN.'/'); // admin/
define('GSADMININCPATH'  , GSADMINPATH     . 'inc/');       // admin/inc/
define('GSADMINTPLPATH'  , GSADMINPATH     . 'template/');  // admin/template/
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
 */
if(defined('GSDEBUG') && (bool) GSDEBUG === true) {
	error_reporting(-1);
	ini_set('display_errors', 1);
	$nocache = true;
} else if( defined('GSSUPPRESSERRORS') && (bool)GSSUPPRESSERRORS === true ) {
	error_reporting(0);
	ini_set('display_errors', 0);
}

/*
 * Enable php error logging
 */
if(defined('GSERRORLOGENABLE') && (bool) GSERRORLOGENABLE === true){
	ini_set('log_errors', 1);
	ini_set('error_log', GSDATAOTHERPATH .'logs/'. GSERRORLOGFILE);
}

/**
 * Basic file inclusions
 */
require_once('basic.php');
require_once('template_functions.php');
require_once('theme_functions.php');
require_once('logging.class.php');

include_once(GSADMININCPATH.'configuration.php');

/**
 * Bad stuff protection
 */
require_once('security_functions.php');

if (version_compare(PHP_VERSION, "5")  >= 0) {
	foreach ($_GET as &$xss) $xss = antixss($xss);
}

/**
 * Headers
 */

// charset utf-8
header('content-type: text/html; charset=utf-8');

// no-cache headers
if(!is_frontend()){
	$timestamp = gmdate("D, d M Y H:i:s") . " GMT";
	header("Expires: " . $timestamp);
	header("Last-Modified: " . $timestamp);
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
}

/**
 * Global website data
 *
 * @global (obj) $dataw         user xml raw obj from GSDATAOTHERPATH.GSWEBSITEFILE
 * @global (str) $SITENAME      sitename
 * @global (str) $SITEURL       siteurl
 * @global (str) $TEMPLATE      site default theme
 * @global (str) $PRETTYURLS    toggle pretty urls enabled
 * @global (str) $PERMALINK     permalink structure, default %parents%/%slug%
 * @global (str) $SITEEMAIL     default site email for sending email primarily or contacting administrator
 * @global (str) $SITETIMEZONE  default timezone of server, safer to set than guess from server
 * @global (str) $SITELANG      default site ITEF langstring, used for login etc. see $LANG
 * @global (str) $SITEUSR       primary user id that installed GS
 * @global (str) $ASSETURL      url for asset loading in head default same as SITEURL but without the scheme '://url'
 */

GLOBAL
 $dataw,
 $SITENAME,
 $SITEURL,
 $TEMPLATE,
 $PRETTYURLS,
 $PERMALINK,
 $SITEEMAIL,
 $SITETIMEZONE,
 $SITELANG,
 $SITEUSR,
 $ASSETURL
;

// load website data from website.xml
extract(getWebsiteData(true));

/**
 * Global user data
 *
 * @global  (str) $datau      user xml raw obj from GSUSERSPATH/userid.xml
 * @global  (str) $USR        user id
 * @global  (str) $HTMLEDITOR htmleditor toggle for auth user
 * @global  (str) $TIMEZONE   timezone for auth user
 * @global  (str) $LANG       language for auth user
 */
// grab cookie user data from userid.xml
GLOBAL
 $datau,
 $USR,
 $HTMLEDITOR,
 $USRTIMEZONE,
 $USRLANG
;
extract(getUserData(true));

/**
 * Global Language Data
 *
 * @global  (array) $i18n i18n token keyed translation array
 * @global  (str) $LANG  IETF langcode (w/underscore delim) [tag]_[subtag]
 */

GLOBAL
 $i18n,
 $LANG
;

// load language
$LANG = getDefaultLang();   // set global language from config heirarchy
i18n_merge(null);           // load $LANG file into $i18n
i18n_mergeDefault();        // load GSDEFAULTLANG or GSMERGELANG lang into $i18n to override ugly missing {} tokens if set

//set php locale
setCustomLocale(getLocaleConfig());

/**
 * Globals for salt and authentication data
 *
 * @global (obj) $dataa,       authorization xml raw obj from GSDATAOTHERPATH.GSAUTHFILE
 * @global (str) $SALT,        salt from gsconfig else authorization file
 * @global (str) $SESSIONHASH  used for stateless session confirmation, or as non-expiring nonce for certain operations
 */

GLOBAL
 $dataa, // legacy for anyone using
 $SALT,
 $SESSIONHASH
;

// grab authorization and security data fatal fail if salt is not set
$SALT = getDefaultSalt();
if(!isset($SALT) && $SITEURL !='' && notInInstall()) die(i18n_r('KILL_CANT_CONTINUE')."<br/>".sprintf(i18n_r('NOT_SET'),'SALT') );
$SESSIONHASH = sha1($SALT . $SITENAME);


/**
 * Global editor vars (ckeditor)
 *
 * @global (str) 	$EDHEIGHT editor custom height
 * @global (str) 	$EDLANG editor custom user lang or lang file specified
 * @global (mixed) 	$EDTOOL editor custom toolbar, json array | php array | 'none' | ck toolbar_ name
 * @global (str) 	$EDOPTIONS editor custom options config, js obj string, comma delimited
 */

// Init Editor globals
GLOBAL
 $EDTOOL,
 $EDHEIGHT,
 $EDLANG,
 $EDOPTIONS
;

// init editor globals
$EDHEIGHT  = getEditorHeight();
$EDLANG    = getEditorLang();
$EDOPTIONS = getEditorOptions();
$EDTOOL    = getEditorToolbar();

$TIMEZONE  = getDefaultTimezone();
setTimezone($TIMEZONE);


$dump = array(
// 'dataw'        => $dataw,
// 'datau'        => $datau,
// 'dataa'        => $dataa,
'SITENAME'     => $SITENAME,
'SITEURL'      => $SITEURL,
'TEMPLATE'     => $TEMPLATE,
'PRETTYURLS'   => $PRETTYURLS,
'PERMALINK'    => $PERMALINK,
'SITEEMAIL'    => $SITEEMAIL,
'SITETIMEZONE' => $SITETIMEZONE,
'SITELANG'     => $SITELANG,
'SITEUSR'      => $SITEUSR,
'USR'          => $USR,
'HTMLEDITOR'   => $HTMLEDITOR,
'USRTIMEZONE'  => $USRTIMEZONE,
'USRLANG'      => $USRLANG,
'ASSETURL'     => $ASSETURL,
'i18n'         => count($i18n),
'SALT'         => $SALT,
'SESSIONHASH'  => $SESSIONHASH,
'EDTOOL'       => $EDTOOL,
'EDOPTIONS'    => $EDOPTIONS,
'EDLANG'       => $EDLANG,
'EDHEIGHT'     => $EDHEIGHT,
);
debugLog($dump);

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
		$filedeletionstatus = true;
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
require_once(GSADMININCPATH.'cookie_functions.php');
require_once(GSADMININCPATH.'assets.php');

if(isset($load['plugin']) && $load['plugin']){

	// load plugins functions
	$live_plugins = array();  // global array for storing active plugins
	include_once(GSADMININCPATH.'plugin_functions.php');

	// include core plugin for page caching, requires plugin functions for hooks
	include_once('caching_functions.php');

	// Include plugins files in global scope
	loadPluginData();
	foreach ($live_plugins as $file=>$en) {
		if ($en=='true' && file_exists(GSPLUGINPATH . $file)){
			include_once(GSPLUGINPATH . $file);
		}
	}
	exec_action('plugins-loaded');

	// load api
	if(get_filename_id()=='settings' || get_filename_id()=='load') {
		/* this core plugin only needs to be visible when you are viewing the
		settings page since that is where its sidebar item is. */
		if (getDef('GSEXTAPI',true)) {
			include_once('api.plugin.php');
		}
	}

	# main hook for common.php
	exec_action('common');

}

if(isset($load['login']) && $load['login'] && getDef('GSALLOWLOGIN',true)){ require_once(GSADMININCPATH.'login_functions.php'); }

// do the template rendering
if(GSBASE) require_once(GSADMINPATH.'base.php');


// common methods that are required before dpendancy includes

/**
 * Debug Console Log
 * @since 3.1
 * @param $txt string
 */
function debugLog($txt = '') {
	global $GS_debug;
	array_push($GS_debug,$txt);
	return $txt;
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

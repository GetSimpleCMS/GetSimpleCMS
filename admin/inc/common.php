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

// @todo REMOVE FOR PRODUCTION
// debug catcher for this core wide change issues
if(htmlentities($_SERVER['SCRIPT_NAME'], ENT_QUOTES) !== htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)) die('PHP_SELF mismatch ' . htmlentities($_SERVER['PHP_SELF']));

/**
 * Set PHP enviroments
 */
if(function_exists('mb_internal_encoding')) mb_internal_encoding("UTF-8"); // set multibyte encoding

/**
 *  GSCONFIG definitions
 */
$GS_constants = array(
	'GSROOTPATH'            => getGSRootPath(false),          // root path of getsimple
	"GSDATEFORMAT"          => "M j, Y",                      // (str) short date-only format fallback
	"GSDATEANDTIMEFORMAT"   => "F jS, Y - g:i A",             // (str) date/time format fallback
	"GSTIMEFORMAT"          => "g:i A",                       // (str) time only format fallback
	'GSSTARTTIME'           => microtime(),                   // (int) micro time stamp for gs loaded
 	'GSBASE'                => false,                         // (bool) front end flag
	'GSCONFIGFILE'          => 'gsconfig.php',                // (str) config filename
	'GSWEBSITEFILE'         => 'website.xml',                 // (str) website data filename
	'GSCOMPONENTSFILE'      => 'components.xml',              // (str) components data filename
	'GSSNIPPETSFILE'        => 'snippets.xml',                // (str) snippets data filename
	'GSAUTHFILE'            => 'authorization.xml',           // (str) authorizaton salt data filename
	'GSPLUGINTRIGGERFILE'   => 'plugin-update.trigger',       // (str) plugin update trigger filename
	'GSCSSMAINFILE'         => 'css.php',                     // (str) main css filename
	'GSPAGECACHEFILE'       => 'pages.xml',                   // (str) page cache xml file name
	'GSPLUGINSFILE'         => 'plugins.xml',                 // (str) plugins xml file name
	'GSADMINTHEMEFILE'      => 'admin.xml',                   // (str) custom admin xml theme file name
	'GSADMINTHEMEENABLE'    => true,                          // (bool) custom admin xml enabled
	'GSCSSCUSTOMFILE'       => 'admin.css',                   // (str) custom css file name
	'GSCSSCUSTOMENABLE'     => true,                          // (bool) custom css enabled
	'GSSTYLECACHEENABLE'    => true,                          // (bool) enable style.php cache
	'GSTEMPLATEFILE'        => 'template.php',                // (str) default template file name
	'GSINSTALLTEMPLATE'     => 'Innovation',                  // (str) template to set on install
	'GSINSTALLPLUGINS'      => 'InnovationPlugin.php',        // (str) csv comma delimited list of plugins to activate on install
	'GSSTYLEWIDE'           => 'wide',                        // (str) wide stylesheet constant
	'GSSTYLE_SBFIXED'       => 'sbfixed',                     // (str) fixed sidebar constant
	'GSFRONT'               => 1,                             // (int) front end enum constant
	'GSBACK'                => 2,                             // (int) back end enum constant
	'GSBOTH'                => 3,                             // (int) front and back enum constant
	'GSDEFAULTLANG'         => 'en_US',                       // (str) default language for core
	'GSTITLEMAX'            => 70,                            // (int) max length allowed for titles
	'GSFILENAMEMAX'         => 255,                           // (int) max length allowed for file names/slugs
	'GSPASSLENGTHMIN'       => 4,                             // (int) min length of passwords
	'GSBAKFILESUFFIX'       => '',                            // (str) backup file naming suffix after extension
	'GSBAKFILEPREFIX'       => '.bak',                        // (str) backup file naming prefix before extension
	'GSRESETFILESUFFIX'     => '.reset',                      // (str) password reset file naming suffix before extension
	'GSRESETFILEPREFIX'     => '',                            // (str) password reset file naming prefix after extension
	'GSDEFAULTPERMALINK'    => '%path%/%slug%/',              // (str) default permalink structure to use if prettyurls is enabled, and custom not exist 
	'GSTOKENDELIM'          => '%',                           // (str) delimiter for token boundaries
	'GSLOGINQSALLOWED'      => 'id,draft,nodraft,safemode,i,path',   // (str) csv query string keys to allow during login redirects
	'GSPAGECACHEEXCLUDE'    => 'content',                     // (str) csv list of page fields to exlclude from page cache
	# -----------------------------------------------------------------------------------------------------------------------------------------------	
	'GSCONSTANTSLOADED'     => true                           // $GS_constants IS LOADED FLAG
);

$GS_definitions = array(
	'GSSUPERUSER'          => '',                             // (str) userid for superuser, defaults to website data/global $SITEUSR if it exists
	'GSDEFAULTPAGE'        => 'pages.php',                    // (str) Default backend index page
	'GSHEADERCLASS'        => '',                             // (str) custom class to add to header eg. `gradient` to add 3.3 gradients back
	'GSHTTPPREFIX'         => '',                             // (str) http slug prefix for error page slugs, GSHTTPPREFIX.GSSLUGx
	'GSSLUGNOTFOUND'       => '404',                          // (str) http slug for not found
	'GSSLUGPRIVATE'        => '403',                          // (str) http slug for private pages
	'GSADMIN'              => 'admin',                        // (str) admin foldername
	'GSSITEMAPFILE'        => 'sitemap.xml',                  // (str) sitemap file name, must modify in .htaccess as needed
	'GSERRORLOGENABLE'     => true,                           // (bool) should GS log php errors to GSERRORLOGFILE
	'GSERRORLOGFILE'       => 'errorlog.txt',                 // (str) error log filename
	'GSASSETSCHEMES'       => false,                          // (bool) should $ASSETURL contain the url scheme http|https
	'GSASSETURLREL'        => false,                           // (bool) Use root relative urls for $ASSETURL, overrides GSASSETSCHEMES
	'GSSITEURLREL'         => false,                           // (bool) Use root relative urls for $SITEURL
	'GSEMAILLINKBACK'      => 'http://get-simple.info/',      // (str) url used in email template
	'GSINDEXSLUG'          => 'index',                        // (str) slug to use as index when no slug provided
	'GSPLUGINORDER'        => '',                             // (str-csv) csv list of live_plugins keys to load first and in order, kludge and not supported
	'GSNOFRAME'            => true,                           // (mixed) allow GS to be loaded in frames via x-frame policy
	'GSNOFRAMEDEFAULT'     => 'SAMEORIGIN',                   // (string) GSNOFRAME X-Frame-Options default value
	'GSCDNFALLBACK'        => true,                           // (bool) if true, CDN assets queued on GSFRONT will fallback to local version
	'GSLOGINUPGRADES'      => true,                           // (bool) if true, temporarily close front end during upgrades, must login to upgrade
	'GSSANITIZEECHO'       => true,                           // (bool) sanitize out put of echoreturn
	# STYLES/UI -------------------------------------------------------------------------------------------------------------------------------------------
	'GSTABS'               => 'pages,upload,theme,snippets,components,backups,plugins', // (str) csv list of page ids and order to show tabs
	'GSNOSIDEBAR'          => 'theme-edit',                   // (str) csv list of page ids to hide sidebar
	'GSSTYLE'              => 'wide,sbfixed',                 // (str-csv) default style modifiers
	'GSWIDTH'              => '1366px',                       // (str) pagewidth on backend,(max-width), null,'none',''  for 100% width
	'GSWIDTHWIDE'          => '1920px',                       // (str) page width on backend pages defined in GSWIDEPAGES, values as above
	'GSWIDEPAGES'          => 'theme-edit,components,snippets', // (str-csv) pages to apply GSWIDTHWIDE on
	'GSTABICONS'           => true,                           // (bool) show icons on nav tabs
	'GSNOCDN'              => true,                           // (bool) disable cdn for assets that have cdn urls in assets.php
	'GSSUPPORTLINKHC'      => false,                          // (bool) show healthcheck small nav instead of support
	# CHMOD --------------------------------------------------------------------------------------------------------------------------------------------
	'GSCHMOD'              => 0644,                           // (octal) chmod mode legacy
	'GSCHMODFILE'          => 0644,                           // (octal) chmod mode for files
	'GSCHMODDIR'           => 0755,                           // (octal) chmod mode for dirs
	'GSDOCHMOD'            => true,                           // (bool) perform chmod when creating files or directories
	'GSCHMODCHECK'         => true,                           // (bool) warn on gschmod mismatches in health check
	# ALLOW --------------------------------------------------------------------------------------------------------------------------------------------
	'GSALLOWLOGIN'         => true,                           // (bool) allow front end login
	'GSALLOWRESETPASS'     => true,                           // (bool) allow front end password resets
	'GSALLOWDOWNLOADS'     => true,                           // (bool) allow using downloads.php to download files from /uploads and backups/zip
	'GSPROFILEALLOWADD'    => false,                          // (bool) allow superuser to add new users
	'GSPROFILEALLOWEDIT'   => false,                          // (bool) allow superuser to edit other users
	'GSEXECANON'           => true,                           // (bool) allow callbacks to be anonymous closures, security implications
	# ALLOW/UPLOADS ------------------------------------------------------------------------------------------------------------------------------------
	'GSALLOWUPLOADS'       => true,                           // (bool) allow upload files
	'GSALLOWUPLOADCREATE'  => true,                           // (bool) allow upload folder creation
	'GSALLOWUPLOADDELETE'  => true,                           // (bool) allow upload file/folder delete
	'GSALLOWBROWSEUPLOAD'  => true,                           // (bool) allow uploading when browsing files
	'GSUSEGSUPLOADER'      => true,                           // (bool) use ajax upload library gsupload (dropzone) for uploads, else standard form 
	'GSUPLOADSLC'          => true,                           // (bool) if true force upload filenames to lowercase
	'GSUPLOADSEXTLC'       => true,                           // (bool) if true force upload extensions to lowercase
	'GSAUTOUPLOADPATH'     => "autoupload",                   // (str) subpath to put uploads in when performing autouploads
	# EDITORS ------------------------------------------------------------------------------------------------------------------------------------------
	'GSEDITORCONFIGFILE'   => 'config.js',                    // (str) custom user cke config filename override in themes/
	'GSEDITORSTYLESFILE'   => 'styles.js',                    // (str) custom user cke config filename override in themes/
	'GSEDITORCSSFILE'      => 'contents.css',                 // (str) custom user cke config filename override in themes/
	'GSEDITORSTYLESID'     => 'userstyles',                   // (str) custom user cke config filename section override in themes/
	'GSSNIPPETSATTRIB'     => 'getHtmlEditorAttr',            // (str) callback funcname for htmleditors used to init htmleditor
	'GSCOMPONENTSATTRIB'   => 'getCodeEditorAttr',            // (str) callback funcname for codeeditors used to init codeeditor
	'GSPAGESATTRIB'        => 'getDefaultHtmlEditorAttr',     // (str) callback funcname for page html editor
	// 'GSTHEMEEDITATTRIB'    => 'getCodeEditorAttr',            // (str) callback funcname for page html editor NI
	'GSSHOWCODEHINTS'      => true,                           // (bool) show code hints on components page and snippets etc.
	'GSAJAXSAVE'           => true,                           // (bool) use ajax for saving themes, components, and pages
	'GSTHEMEEDITROOT'      => false,                          // (bool) allow editing theme root files
	'GSTHEMEEDITEXTS'      => 'php,css,js,html,htm,txt,xml,', // (str-csv) file extensions to show and edit in theme editor
	'GSEDITORHEIGHT'       => '500',                          // (str) wysiwyg editor height in px
	'GSEDITORTOOL'         => 'basic',                        // (str) wysiwyg editor toobar
	'GSHTMLEDITINLINE'     => false,                          // (bool) show html cke editors inline EXPERIMENTAL
	'GSHTMLEDITCOMPACT'    => true,                           // (bool) show html cke editors compacted, hides ui when not focused
	'GSHTMLEDITAUTOHEIGHT' => true,                           // (bool) after init, auto set the ckeditors height	
	// 'GSCODEEDITORTHEMES'   => '3024-day,3024-night,abcdef,ambiance-mobile,ambiance,base16-dark,base16-light,bespin,blackboard,cobalt,colorforth,default,dracula,duotone-dark,duotone-light,eclipse,elegant,erlang-dark,hopscotch,icecoder,isotope,lesser-dark,liquibyte,material,mbo,mdn-like,midnight,monokai,neat,neo,night,panda-syntax,paraiso-dark,paraiso-light,pastel-on-dark,railscasts,rubyblue,seti,solarized light,solarized dark,the-matrix,tomorrow-night-bright,tomorrow-night-eighties,ttcn,twilight,vibrant-ink,xq-dark,xq-light,yeti,zenburn', # themes for codemirror
	'GSCODEEDITORTHEMES'   => '
		monokai,
		dracula,
		material,
		mbo,
		neat,
		neo,
		ambiance,
		base16-dark,
		base16-light,
		bespin,
		blackboard,
		eclipse,
		panda-syntax,
		paraiso-dark,
		paraiso-light,
		railscasts,
		solarized light,
		solarized dark,
		textarea
	',
	'GSPAGETABS'           => true,                           // (bool) use tabbed interface for page edit
	'GSCOMPSORTSAVE'       => true,                           // (bool) sort collections by title on save
	'GSEDITNAVCLONE'       => false,                          // (bool) show clone edit nav on page edit
	'GSEDITNAVVIEW'        => true,                           // (bool) show view edit nav on page edit
	'GSNOHIGHLIGHT'        => false,                          // (bool) disabled code mirror
	# DRAFTS -------------------------------------------------------------------------------------------------------------------------------------------
	'GSUSEDRAFTS'          => false,                          // (bool) use page drafts
	'GSUSEPAGESTACK'       => true,                           // (bool) use page stacks for drafts, else manually pass `nodraft` or `draft` qs
	'GSDRAFTSTACKDEFAULT'  => false,                          // (bool) default page stack editing to drafts if true
	'GSSDRAFTSPUBLISHEDTAG'=> true,                           // (bool) show published label on non draft pages if true
	'GSAUTOSAVE'           => true,                           // (bool) auto save enabled, disabled if false, only used for drafts currently
	'GSAUTOSAVEINTERVAL'   => 6,                              // (int)  auto save interval in seconds,  only used for drafts currently
	# IMAGES -------------------------------------------------------------------------------------------------------------------------------------------
	'GSIMAGEWIDTH'         => 200,                            // (int) thumbnail size
	'GSTHUMBSMWIDTH'       => 80,                             // (int) thumbsm max height
	'GSTHUMBSMHEIGHT'      => 160,                            // (int) thumbsm max width
	'GSTHUMBSSHOW'         => true,                           // (bool) always show thumbnails
	# DEBUGGING ----------------------------------------------------------------------------------------------------------------------------------------
	'GSDEBUGINSTALL'       => false,                          // (bool) debug installs, prevent removal of installation files (install,setup,update)
	'GSDEBUGINSTALLWIPE'   => false,                          // (bool) debug installs, wipes website.xml on logouts
	'GSDEBUG'              => true,                           // (bool) output debug mode console
	'GSDEBUGAPI'           => false,                          // (bool) debug api calls to debuglog
	'GSDEBUGREDIRECTS'     => false,                          // (bool) if debug mode enabled, prevent redirects for debugging
	'GSDEBUGFILEIO'        => false,                          // (bool) debug filio operations
	'GSDEBUGHOOKS'         => false,                          // (bool) debug hooks, adds callee (file,line,core) to $plugins, always true if DEBUG MODE
	'GSDEBUGLOGTIME'       => false,                          // (bool) timestamp debuglog str entries
	'GSDEBUGLOGDUR'        => false,                          // (bool) duration timestamp debuglog str entries
	'GSDEBUGHEADERS'       => false,                          // (bool) enable header output debugging ( helpful for finding whitespace or headers already sent errors )
	'GSDEBUGARRAYS'        => false,                          // (bool) will dump all arrays on init for debugging
	'GSDEBUGPATHS'         => false,                          // (bool) will dump all arrays on init for debugging
	# INIT ----------------------------------------------------------------------------------------------------------------------------------------------
	'GSSAFEMODE'           => false,                          // (bool) enable safe mode, safe mode disables plugins and components
	'GSFORMATXML'          => false,                          // (bool) format xml files before saving them, making them more legible
	'GSSAFEMODE'           => false,                          // (bool) enable safe mode, safe mode disables plugins and components
	'GSINITPAGECACHE'      => true,                           // (bool) initPageCache in common, solves issues with lazyload via getPages, or when still using Global $pagesArray
	'GSUSELEGACYPLUGINS'   => false,                          // (bool) use legacy plugins array for plugin hooks, some features will not work, namely priorities!
	'GSNOPLUGINCHECK'      => false,                          // (bool) perform no plugin api checks if false, else api checks will be limited to 5 per run(page load)
	# VARIABLES -----------------------------------------------------------------------------------------------------------------------------------------
	'GSMETADLEN'           => 160,                            // (int) optional meta description max length enforced
	'GSBACKUPROOT'         => false,                          // (bool) include all root files in archive backups
	'GSAUTOMETAD'          => false,                          // (bool) auto generate meta desc from content
	// 'GSBACKUPEXTRAS'       => '',                          // (str) testing add stuff to backup, only accepts files for now
	# SANITY --------------------------------------------------------------------------------------------------------------------------------------------
	# ---------------------------------------------------------------------------------------------------------------------------------------------------
 	'GSDEFINITIONSLOADED'  => true	                          // (bool) $GS_definitions IS LOADED FLAG
);


// check php env for GSROOTHPATH to allow for symlink GSADMIN etc.
if(getenv('GSROOTPATH') && !defined('GSROOTPATH')) define('GSROOTPATH',getenv('GSROOTPATH'));

/* Define Constants */
GS_defineFromArray($GS_constants);

/**
 * Variable Globalization
 */
global
 $TEMPLATE,       // (str) current theme
 $GSADMIN,        // (str) admin foldername
 $GS_debug,       // (array) global array for storing debug log entries
 $components,     // (array) global array for storing components, array of objs from components.xml
 $snippets,       // (array) global array for storing snippets, array of objs from snippets.xml
 $nocache,        // (bool) disable site wide cache true, not fully implemented
 $microtime_start,// (microtime) used for benchmark timers
 $microtime_last, // (microtime) used for benchmark timers
 $pagesArray,     // (array) global array for storing pages cache, used for all page fields aside from content
 $pageCacheXml,   // (obj) page cache raw xml simpleXMLobj //@todo REMOVE memory waste, not needed when not debugging
 $plugin_info,    // (array) contains registered plugin info for active and inactive plugins
 $live_plugins,   // (array) contains plugin file ids and enable status
 $plugins,        // (array) global array for storing action hook callbacks, not used for exec_
 $pluginHooks,    // (array) global array for storing action hook callbacks hash table
 $filters,        // (array) global array for storing filter callbacks
 $pluginFilters,  // (array) global array for storing filter callbacks hash table
 $secfilters,     // (array) global array for storing security filters
 $securityFilters,// (array) global array for storing security filters hash table
 $GS_scripts,     // (array) global array for storing queued asset scripts
 $GS_styles,      // (array) global array for storing queued asset styles
 $plugincallstats // (array) global array for storing plugin call stats
;

$microtime_start = microtime(true);
$microtime_last = $microtime_start;

if(isset($_GET['nocache'])){
	// @todo: disables caching, this should probably only be allowed for auth users, it is also not well implemented
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
	$base = GSBASE; // @global $base LEGACY frontend flag DEPRECATED
	// set loaders, if you want to override these do it your main common wrapper or index.php
	if(!isset($load['plugin']))   $load['plugin']   = true;   // load plugin system
	if(!isset($load['template'])) $load['template'] = true; // load template system
	if(!isset($load['login']))    $load['login']    = false; // load login system
}

/*
 * Apply default definitions
 */
GS_defineFromArray($GS_definitions);
$GSADMIN = rtrim(GSADMIN,'/\\'); // global GS admin root folder name

/**
 * Define Paths
 */
define('GSPATH'          , getGSRootPath()                );// /
define('GSADMINPATH'     , GSPATH          . $GSADMIN.'/'); // admin/
define('GSADMININCPATH'  , GSADMINPATH     . 'inc/');       // admin/inc/
define('GSADMINTPLPATH'  , GSADMINPATH     . 'template/');  // admin/template/
define('GSPLUGINPATH'    , GSROOTPATH      . 'plugins/');   // plugins/
define('GSLANGPATH'      , GSADMINPATH     . 'lang/');      // lang/

// data
define('GSDATAPATH'      , GSROOTPATH      . 'data/');      // data/
define('GSDATAOTHERPATH' , GSDATAPATH      . 'other/');     // data/other/
define('GSDATAPAGESPATH' , GSDATAPATH      . 'pages/');     // data/pages/

define('GSAUTOSAVEPATH'  , GSDATAPAGESPATH . 'autosave/');  // data/pages/autosave/
define('GSDATADRAFTSPATH', GSDATAPAGESPATH . 'autosave/');  // data/pages/autosave/
define('GSDATAUPLOADPATH', GSDATAPATH      . 'uploads/');   // data/uploads/
define('GSTHUMBNAILPATH' , GSDATAPATH      . 'thumbs/');    // data/thumbs/
define('GSUSERSPATH'     , GSDATAPATH      . 'users/');     // data/users/
define('GSCACHEPATH'     , GSDATAPATH      . 'cache/');     // data/cache/

define('GSBACKUPSPATH'   , GSROOTPATH      . 'backups/');   // backups/
define('GSBACKUSERSPATH' , GSBACKUPSPATH   . 'users/');     // backups/users
define('GSTHEMESPATH'    , GSROOTPATH      . 'theme/');     // theme/


// reserved slug names, slugs named these will interfere with gs folder access
// these are checked against in changedata.php and auto incremented to avoid conflicts
$reservedSlugs = array($GSADMIN,'data','theme','plugins','backups');

// tab sidemenu structure reference
$tabdefinition = array(
	'pages'    => array('edit','menu-manager'),
	'upload'   => array(),
	'theme'    => array('theme-edit','components','snippets','sitemap'),
	'backups'  => array('archive'),
	'plugins'  => array(),
	'support'  => array('health-check','log'),
	'settings' => array('profile'),
	'profile'  => array('settings')
);

// sidemenu page parent tabs reference
$sidemenudefinition = array(
	'pages'        => '',
	'edit'         => 'pages',
	'menu-manager' => 'pages',
	'upload'       => '',
	'theme'        => '',
	'theme-edit'   => 'theme',
	'components'   => 'theme',
	'snippets'     => 'theme',
	'sitemap'      => 'theme',
	'backups'      => '',
	'archive'      => 'backups',
	'plugins'      => '',
	'support'      => '',
	'health-check' => 'support',
	'log'          => 'support',
	'settings'     => '',
	'profile'      => ''
);

$sidemenutitles = array(
	'support'      => "SIDE_SUPPORT_LOG",
	'health-check' => "SIDE_HEALTH_CHK",
	'log'          => "SIDE_VIEW_LOG",
	'sitemap'      => "SIDE_VIEW_SITEMAP",
	'components'   => "SIDE_COMPONENTS",
	'snippets'     => "SIDE_SNIPPETS",
	'theme-edit'   => "SIDE_EDIT_THEME",
	'theme'        => "SIDE_CHOOSE_THEME", // default
	'edit'         => "SIDE_CREATE_NEW",   // default
	'pages'        => "SIDE_VIEW_PAGES",
	'settings'     => "SIDE_GEN_SETTINGS",
	'profile'      => "SIDE_USER_PROFILE",
	'archives'     => "SIDE_WEB_ARCHIVES",
	'backups'      => "SIDE_PAGE_BAK"
	// 'backups'      => "SIDE_VIEW_BAK", 
	// 'docs'         => "SIDE_DOCUMENTATION",
);


// @todo centralize, add getter
$icondefinition = array(
	'TAB_pages'        => '<i class="far fa-fw fa-list-alt"></i>',// th th-list table database align-left list-ul list-alt files-o copy
	'TAB_edit'         => '<i class="fas fa-fw fa-edit"></i>',// edit fa-plus
	'TAB_menu-manager' => '<i class="fas fa-fw fa-sitemap"></i>',// list
	'TAB_upload'       => '<i class="fas fa-fw fa-copy"></i>',// upload upload-cloud-alt
	'TAB_theme'        => '<i class="fas fa-fw fa-image"></i>',// image
	'TAB_theme-edit'   => '<i class="fas fa-fw fa-code"></i>',// indent code
	'TAB_components'   => '<i class="fas fa-fw fa-cubes"></i>',// cubes
	'TAB_snippets'     => '<i class="fas fa-fw fa-quote-left"></i>',// quote-left cut cube
	'TAB_sitemap'      => '<i class="fas fa-fw fa-map"></i>',// sitemap globe
	'TAB_backups'      => '<i class="fas fa-fw fa-history"></i>',// history 
	'TAB_archive'      => '<i class="fas fa-fw fa-archive"></i>',// archive file-archive
	'TAB_plugins'      => '<i class="fas fa-fw fa-plug"></i>',// plug
	'TAB_support'      => '<i class="fas fa-fw fa-life-ring"></i>',// first-aid med-kit
	'TAB_health-check' => '<i class="fas fa-fw fa-medkit"></i>',// life-ring, clipboard-check
	'TAB_log'          => '<i class="fas fa-fw fa-file-alt"></i>',// paper-plane shield
	'TAB_settings'     => '<i class="fas fa-fw fa-cogs"></i>',// cog cogs sliders
	'TAB_profile'      => '<i class="fas fa-fw fa-address-card"></i>', // user address-card
	'TAB_logout'       => '<i class="fas fa-fw fa-fw fa-sign-out-alt"></i>',
	'TAB_welcome'      => '<i class="fas fa-fw fa-user-circle icon-left"></i>',
	'TAB_development'  => '<i class="fas fa-fw fa-flask"></i>',
	'TAB_debugmode'    => '<i class="fas fa-fw fa-wrench"></i>',
	'TAB_update'       => '<i class="fas fa-fw fa-lg fa-cloud-download"></i>',
	'TAB_load'         => '<i class="fas fa-fw fa-puzzle-piece"></i> ',
	// 'TABSM_settings'   => '<i class="fas fa-fw fa-cogs"></i> ',
	// 'TABSM_support'    => '<i class="fas fa-fw fa-life-ring"></i> ',
	// 'TABSM_health-check' => '<i class="fas fa-fw fa-life-ring"></i> ',
	// 'TABSM_health-check'=> '<i class="fas fa-fw fa-medkit"></i> ',
	'TAB_'             => '<i class="fas fa-fw %s"></i>',
	'SM_upload'        => '<i class="fas fa-fw fa-lg fa-upload"></i>', // bullseye, cloud-upload-alt
	'FILE_pdf'         => '<i class="far fa-fw fa-file-pdf"></i>',
	'FILE_xls'         => '<i class="far fa-fw fa-file-excel"></i>',
	'FILE_xlsx'        => '<i class="far fa-fw fa-file-excel"></i>',
	'FILE_doc'         => '<i class="far fa-fw fa-file-word"></i>',
	'FILE_docx'        => '<i class="far fa-fw fa-file-word"></i>',
	'FILE_ppt'         => '<i class="far fa-fw fa-file-powerpoint></i>',
	'FILE_css'         => '<i class="far fa-fw fa-file-code></i>',
	'FILE_IMAGE'       => '<i class="far fa-fw fa-file-image"></i>',
	'FILE_COMPRESSED'  => '<i class="far fa-fw fa-file-archive"></i>',
	'FILE_VECTOR'      => '<i class="far fa-fw fa-file-image"></i>',
	'FILE_FLASH'       => '<i class="far fa-fw fa-file-image"></i>',
	'FILE_VIDEO'       => '<i class="far fa-fw fa-file-video"></i>',
	'FILE_AUDIO'       => '<i class="far fa-fw fa-file-audio"></i>',
	'FILE_WEB'         => '<i class="far fa-fw fa-file-code"></i>',
	'FILE_SCRIPT'      => '<i class="far fa-fw fa-file-code"></i>',
	'FILE_DOCUMENT'    => '<i class="far fa-fw fa-file-alt"></i>',
	'FILE_SYSTEM'      => '<i class="far fa-fw fa-file"></i>',
	'FILE_MISC'        => '<i class="far fa-fw fa-file"></i>',
	'FILE_FOLDER'      => '<i class="fas fa-fw fa-folder"></i>',
	'ICO_draft'        => '<i class="fas fa-fw fa-pencil-alt"></i>',
	'ICO_timestamp'    => '<i class="far fa-fw fa-clock"></i>',
	'ICO_backup'       => '<i class="far fa-fw fa-file-archive"></i>',
	'ICO_info'         => '<i class="fas fa-fw fa-info-circle"></i>',
	'ICO_search'       => '<i class="fas fa-fw fa-search"></i>',
	'ICO_linkext'      => '<i class="fas fa-fw fa-external-link-alt"></i>',
	'ICO_plugon'       => '<i class="fas fa-fw fa-lg fa-toggle-on label-ok-color"></i>',
	'ICO_plugoff'      => '<i class="fas fa-fw fa-lg fa-toggle-off opaque30"></i>',
);

/**
 * Init debug mode
 */
if(defined('GSDEBUG') && (bool) GSDEBUG === true) {
	debugLog('GSDEBUG: TRUE');
	error_reporting(-1);
	ini_set('display_errors', 1);
	// $nocache = true;
} else if( defined('GSSUPPRESSERRORS') && (bool)GSSUPPRESSERRORS === true ) {
	debugLog('GSSUPPRESSERRORS: TRUE');	
	error_reporting(0);
	ini_set('display_errors', 0);
}

/*
 * Enable php error logging
 */
if(defined('GSERRORLOGENABLE') && (bool) GSERRORLOGENABLE === true){
	debugLog('GSERRORLOGENABLE: TRUE');
	ini_set('log_errors', 1);
	ini_set('error_log', GSDATAOTHERPATH .'logs/'. GSERRORLOGFILE);
}

/**
 * Basic file inclusions
 */
require_once('page_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: page_functions.php ' . headers_sent());

require_once('basic.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: basic.php ' . headers_sent());

require_once('template_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: template_functions.php ' . headers_sent());

require_once('theme_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: theme_functions.php ' . headers_sent());

require_once('filter_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: filter_functions.php ' . headers_sent());

require_once('sort_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: sort_functions.php ' . headers_sent());

require_once('logging.class.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: logging.class.php ' . headers_sent());

include_once(GSADMININCPATH.'configuration.php');

// @deprecated LEGACY $cookie_redirect
if(getDef('GSDEFAULTPAGE',true)){
	$cookie_redirect = getDef('GSDEFAULTPAGE');    // legacy redirect
}

// Add X-Frame-Options to HTTP header, so that page can only be shown in an iframe of the same site.
if(getDef('GSNOFRAME') !== false){
	if(getDef('GSNOFRAME') === GSBOTH) header_xframeoptions();
	else if((getDef('GSNOFRAME') === true || getDef('GSNOFRAME') === GSBACK) && !is_frontend()) header_xframeoptions();
	else if(getDef('GSNOFRAME') === GSFRONT && is_frontend()) header_xframeoptions();
}

/**
 * Bad stuff protection
 */
require_once('security_functions.php');
// debugLog('headers sent: security_functions.php ' . headers_sent());

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
	header('Expires: 0'); // Proxies.
	header("Last-Modified: " . $timestamp);
	header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
	header('Pragma: no-cache'); // HTTP 1.0.
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
 * @global (str) $SITEUSR       primary user id that installed GS, superuser
 * @global (str) $ASSETURL      url for asset loading in head depends on GSASSETURLREL and GSASSETSCHEMES settings
 * @global (str) $OLDLOCALE     store old locale before setcustomlocale
 * @global (str) $NEWLOCALE     store new locale before setcustomlocale
 * @global (bool) $SAFEMODE     safemode flag, disables plugins etc
 */

GLOBAL
 $dataw,
 $SITENAME,
 $SITEURL,
 $SITEURL_ABS,
 $SITEURL_REL,
 $TEMPLATE,
 $PRETTYURLS,
 $PERMALINK,
 $SITEEMAIL,
 $SITETIMEZONE,
 $SITELANG,
 $SITEUSR,
 $ASSETURL,
 $ASSETPATH,
 $OLDLOCALE,
 $NEWLOCALE,
 $SAFEMODE
;


// load website data from GSWEBSITEFILE (website.xml)
extract(getWebsiteData(true));

if(defined("GSDEBUGPATHS") && GSDEBUGPATHS){
// debugging paths
debugLog('GSBASE       = ' . GSBASE);
debugLog('GSROOTPATH   = ' . GSROOTPATH);
debugLog('GSADMINPATH  = ' . GSADMINPATH);
debugLog('SITEUSR      = ' . $SITEUSR);
debugLog('GSSITEURLREL = ' . getDef('GSSITEURLREL',true));
debugLog('SITEURL      = ' . getSiteURL());
debugLog('SITEURL_ABS  = ' . getSiteURL(true));
debugLog('SITEURL_REL  = ' . $SITEURL_REL);
debugLog('ASSETURL     = ' . $ASSETURL);
debugLog('ASSETPATH    = ' . $ASSETPATH);
debugLog('SITELANG     = ' . $SITELANG);
debugLog('GSLANG       = ' . getDef('GSLANG'));
// debugDie();
}

/**
 * Global user data
 *
 * @global  (str) $datau      user xml raw obj from GSUSERSPATH/userid.xml
 * @global  (str) $USR        holds the GS_ADMIN_USERNAME cookie value
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
debugLog("LANG = ".$LANG);
i18n_merge(null);           // load $LANG file into $i18n
i18n_mergeDefault();        // load GSDEFAULTLANG or GSMERGELANG lang into $i18n to override ugly missing {} tokens if set

//save php locale
setOldLocale();

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
if(!defined('GSCKETSTAMP')) define('GSCKETSTAMP',get_gs_version()); // ckeditor asset querystring for cache control 
$EDHEIGHT  = getEditorHeight();
$EDLANG    = getEditorLang();
$EDOPTIONS = getEditorOptions();
$EDTOOL    = getEditorToolbar();

$TIMEZONE  = getDefaultTimezone();
setTimezone($TIMEZONE);
debugLog("TIMEZONE: " . $TIMEZONE);


// Debug useful globals
$dump = array(
// 'dataw'        => $dataw,
// 'datau'        => $datau,
// 'dataa'        => $dataa,
'GSROOT'       => GSROOTPATH,
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
'OLDLOCALE'    => $OLDLOCALE,
'NEWLOCALE'    => $NEWLOCALE
// '_SERVER'      => $_SERVER,
);
// debugLog($dump);

/**
 * Check to make sure site is already installed
 */
if (notInInstall()) {
	$fullpath = suggest_site_path();

	# if there is no SITEURL set, then it's a fresh install. Start installation process
	# siteurl check is not good for pre 3.0 since it will be empty, so skip and run update first.
	if ($SITEURL == '' &&  get_gs_version() >= 3.0)	{
		if(getDef('GSLOGINUPGRADES',true)) serviceUnavailable();
		redirect($fullpath . $GSADMIN.'/install.php');
	}
	else {
	# if an update file was included in the install package, redirect there first
		if (file_exists(GSADMINPATH.'update.php') && !isset($_GET['updated']) && !getDef('GSDEBUGINSTALL'))	{
			if(getDef('GSLOGINUPGRADES',true)) serviceUnavailable();
			redirect($fullpath . $GSADMIN.'/update.php');
		}
	}

	if(!getDef('GSDEBUGINSTALL',true)){
		# if you've made it this far, the site is already installed so remove the installation files
		$filedeletionstatus = true;
		if (file_exists(GSADMINPATH.'install.php'))	{
			$filedeletionstatus = delete_file(GSADMINPATH.'install.php');
		}
		if (file_exists(GSADMINPATH.'setup.php'))	{
			$filedeletionstatus = delete_file(GSADMINPATH.'setup.php');
		}
		if (file_exists(GSADMINPATH.'update.php'))	{
			$filedeletionstatus = delete_file(GSADMINPATH.'update.php');
		}
		if (!$filedeletionstatus) {
			$error = sprintf(i18n_r('ERR_CANNOT_DELETE'), '<code>/'.$GSADMIN.'/install.php</code>, <code>/'.$GSADMIN.'/setup.php</code> or <code>/'.$GSADMIN.'/update.php</code>');
		}
	}

}

// set these for install, empty if website.xml doesnt exist yet
if(empty($SITEURL))      $SITEURL     = suggest_site_path();
if(empty($SITEURL_ABS))  $SITEURL_ABS = $SITEURL;
if(empty($SITEURL_REL))  $SITEURL_REL = $SITEURL;
if(empty($ASSETURL))     $ASSETURL    = $SITEURL;
if(empty($ASSETPATH))    $ASSETPATH   = $ASSETURL.tsl(getRelPath(GSADMINTPLPATH,GSADMINPATH));

/**
 * Include other files depending if they are needed or not
 */
require_once(GSADMININCPATH.'cookie_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: cookie_functions.php ' . headers_sent());

require_once(GSADMININCPATH.'assets.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: asset.php ' . headers_sent());

include_once(GSADMININCPATH.'plugin_functions.php');
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: plugin_functions.php ' . headers_sent());

// include core plugin for page caching, requires plugin functions for hooks
// @todo must stay after plugin_function for now, since it requires plugin_functions
include_once(GSADMININCPATH.'caching_functions.php');
if(defined('GSINITPAGECACHE') && constant('GSINITPAGECACHE') == true) init_pageCache(); // in case autoloading doesnt work for a particular instance.
if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: caching_functions.php ' . headers_sent());

if(getDef('GSSAFEMODE',true)) $SAFEMODE = true;
if($SAFEMODE){
	if(isset($_REQUEST['safemodeoff']) && is_logged_in()){
		disableSafeMode();
		redirect(myself(false));
	}
	else {
		$SAFEMODE = true;
		debugLog("SAFEMODE ON");
		$load['plugin'] = false;
		loadPluginData();
	}	
}

// load plugins functions

if(isset($load['plugin']) && $load['plugin']){

	if(function_exists('plugin_preload_callout')) plugin_preload_callout();	// @callout plugin_preload_callout callout before loading plugin files

	// Include plugins files in global scope
	loadPluginData();

	foreach ($live_plugins as $file=>$en) {
		if ($en=='true' && file_exists(GSPLUGINPATH . $file)){
			// debugLog('including plugin: ' . $file);
			include_once(GSPLUGINPATH . $file);
			exec_action('plugin-loaded'); // @hook plugin-loaded called after each plugin is included
			if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: ' . $file . ' - ' . headers_sent());
		}
	}
	exec_action('plugins-loaded'); // @hook plugins-loaded plugin files have been included

	// load api
	if(isPage('settings') || isPage('load')) {
		/* this core plugin only needs to be visible when you are viewing the
		settings page since that is where its sidebar item is. */
		if (getDef('GSEXTAPI',true)) {
			include_once('api.plugin.php');
			if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: api.plugin.php ' . headers_sent());
		}
	}

	# main hook for common.php
	exec_action('common'); // @hook common common.php has completed loading resoruces, base not yet loaded
	// debugLog('calling common_callout');
	if(function_exists('common_callout')) common_callout(); // @callout common_callout callout after common loaded, before templating
}

if(isset($_REQUEST['refreshcache'])) exec_action('request-refreshcache'); // @hook request-cacherefresh force pagecache refresh

/**
 * debug plugin global arrays
 */

if(defined('GSDEBUGARRAYS') && constant('GSDEBUGARRAYS') == true){
	debugLog($live_plugins);
	debugLog($plugin_info);
	debugLog($plugins);
	debugLog($pluginHooks);
}

if(isset($load['login']) && $load['login'] && getDef('GSALLOWLOGIN',true)){ require_once(GSADMININCPATH.'login_functions.php'); }

// do the template rendering
if(GSBASE) {
	require_once(GSADMINPATH.'base.php');
	if(defined("GSDEBUGHEADERS") && GSDEBUGHEADERS) debugLog('headers sent: base.php ' . headers_sent());
}

// common methods that are required before dependancy includes

/**
 * Debug Console Log
 * @since 3.1
 * @param $txt string
 */
function debugLog($mixed = null) {
	global $GS_debug;
	// add stamp and or elapsed times to strings
	if(gettype($mixed) == "string" && function_exists('get_execution_time') && defined('GSDEBUGLOGTIME') && constant('GSDEBUGLOGTIME') == true) $mixed = '|'.str_pad(get_execution_time(),5,0,STR_PAD_RIGHT) . "|" . (defined('GSDEBUGLOGDUR') && constant('GSDEBUGLOGDUR') == true ? str_pad(get_execution_duration(),5,0,STR_PAD_RIGHT) . '|' : '') . $mixed;
	array_push($GS_debug,$mixed);
	if(function_exists('debugLog_callout')) debugLog_callout($mixed); // @callout debugLog_callout (str) callout for each debugLog call, argument passed
	return $mixed;
}

/**
 * debug and die
 * outputs debuglog and dies
 * @since  3.4
 * @param  str $msg message to log
 */
function debugDie($msg = ""){
	if(empty($msg))debugLog(debug_backtrace());
	debugLog($msg);
	outputDebugLog();
	die();
}

/**
 * debug a backtrace
 * eg. 	`debugLogTrace(__FUNCTION__,123456);`
 * @param  str $msg exception message
 * @param  int $code exception code, useful as id for grouping etc
 * @param  int $cols $colwidth for dividers
 * @since 3.4
 */
function debugLogTrace($msg = '',$code = '',$cols = 100){
	$e      = new Exception($msg,(int)$code);
	$emsg   = $e->getMessage();
	$etrace = $e->getTraceAsString();
	$ecode  = $e->getCode() > 0 ? $e->getCode() : '';
	debugLog("$msg\nDEBUG BACKTRACE $ecode\n".str_repeat('=', $cols)."\n".$etrace."\n".str_repeat('-', $cols));
}

/**
 * Debug defines from an array
 * @param array assoc of keyed values [DEFINITIONNAME] => value
 */
function debugDefineArray($definitions){
	foreach($definitions as $definition => $value){
		debugLog($definition . ": " . (defined($definition) ? getDef($definition) : ""));
	}
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
 * Define from an array of global keys
 * @param array  DEFINITION keys to import from globals
 */
function GS_defineFromGlobals($definitions){
	foreach($definitions as $definition){
		if(isset($_GLOBALS[$definition])){
			$value = $_GLOBALS[$definition];
			if(!defined($definition)) define($definition,$value);
		}
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

function getGSRootPath($calculate = false){

	if($calculate){
		/**
		 * calculate root path if different than admin path
		 * @todo  experimental
		 *
		 * get data path, getcwd problems?
		 * get actual path SCRIPT_NAME
		 * path compare
		 * normalize paths
		 * explode paths
		 * reverse array
		 * array diff
		 * get key of fisrt diff, this is our path index
		 * slice the array then change the first dir to the real dir
		 * reverse, implode with slashes
		 */

		global $GSADMIN;

		// debugLog(phpinfo(32));
		$file = getcwd(); // get workign path, __DIR__ is NOT the same @todo add double check here
		$path = dirname($_SERVER['SCRIPT_NAME']); // get script path
		$file = str_replace("\\", "/", $file);    // normalize slashes
		
		// tts
		// $file = trim($file,"/");
		// $path = trim($path,"/");

 		// convert to arrays
		$pathpartsfile = explode("/",$file);
		$pathpartsfile = array_reverse($pathpartsfile);
		debugLog($pathpartsfile);
		
		$pathpartspath = explode("/",$path);		
		$pathpartspath = array_reverse($pathpartspath);
		debugLog($pathpartspath);
		
		// find index of first diff
		$pathdiff        = array_diff($pathpartspath,$pathpartsfile);
		$pathdiffindices = array_keys($pathdiff);
		$pathdiffindex   = isset($pathdiffindices[0]) ? $pathdiffindices[0] : 0;
		
		// remove everyting after the first diff
		$pathpartsfile = array_slice($pathpartsfile,$pathdiffindex,count($pathpartsfile));
		// replace dir with real dir using index
		$pathpartsfile[0] = $pathpartspath[$pathdiffindex];
		debugLog($pathpartsfile);

		// reassemble
		$pathpartsfile = array_reverse($pathpartsfile);
		$file = implode(DIRECTORY_SEPARATOR,$pathpartsfile);
		
		debugLog($file);
		return $file.DIRECTORY_SEPARATOR;
	}
	return dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR;
}

// debugging svn branch, set in configuration for now
if(isDebug() && isset($devbranch)) add_action("footer-pre","debugBranch",array($devbranch,(isset($devissue) ? $devissue : null)),1);
function debugBranch($branch = 'master',$issue = null){
   	echo '<div class="border"><p><i class="fab fa-fw fa-github-square"></i><b>GITHUB BRANCH:</b> ';
   	if(isset($branch)) echo ' <a class="label-reset label label-medium" href="https://github.com/GetSimpleCMS/GetSimpleCMS/tree/'.$branch.'" target="_BLANK">'.uppercase($branch).'</a>';
   	if(isset($issue)) echo ' <a class="label-reset label label-info" href="https://github.com/GetSimpleCMS/GetSimpleCMS/issues/'.$issue.'" target="_BLANK">GITHUB ISSUE</a>';
   	echo '</p></div><br>';
}

/* ?> */

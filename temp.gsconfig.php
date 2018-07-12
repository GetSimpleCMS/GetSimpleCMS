<?php
/**
 * GSConfig
 *
 * The base configurations for GetSimple
 *
 * @package GetSimple
 */

/** Prevent direct access */
if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
	die('You cannot load this page directly.');
};

/*****************************************************************************/
/** Below are constants that you can use to customize how GetSimple operates */

// Extra salt to secure your password with. Default is empty for backwards compatibility.
#define('GSLOGINSALT', 'your_unique_phrase');

// Turn off auto-generation of SALT and use a custom value. Used for cookies & upload security.
#define('GSUSECUSTOMSALT', 'your_new_salt_value_here');

// Default thumbnail width of uploaded image
# define('GSIMAGEWIDTH', '200');

// Change the administrative panel folder name
#define('GSADMIN', 'admin');

// Turn on debug mode
#define('GSDEBUG', true);

// Turn on safe mode
#define('GSSAFEMODE', true);

// Use root relative urls for site url in assets links and ckeditor saved links
#define('GSSITEURLREL',true);

// Use root relative urls for assets
# define('GSASSETURLREL',true);

// Turn off CSRF protection. Uncomment this if you keep receiving the error message "CSRF error detected..."
#define('GSNOCSRF', true);

// Set override CHMOD mode
#define('GSCHMODFILE', 0644);
#define('GSCHMODDIR', 0755);

// Disable chmod operations
# define('GSDOCHMOD',false);

// WYSIWYG editor height (default 500)
#define('GSEDITORHEIGHT', '400');

// WYSIWYG toolbars (advanced, basic or [custom config])
#define('GSEDITORTOOL', 'advanced');

// WYSIWYG editor language (default en)
#define('GSEDITORLANG', 'en');

// WYSIWYG Editor Options
#define('GSEDITOROPTIONS', '');

// Set email from address
#define('GSFROMEMAIL', 'noreply@get-simple.info');

// Set PHP locale
# http://php.net/manual/en/function.setlocale.php
#setlocale(LC_ALL, 'en_US');

// Define default timezone of server, accepts php timezone string
// valid timeszones can be found here http://www.php.net/manual/en/timezones.php
# define('GSTIMEZONE', 'America/Chicago');

// Forces suppression of php errors when GSDEBUG is false, despite php ini settings
# define('GSSUPPRESSERRORS',true);

// Disable check for Apache web server, default false
#define('GSNOAPACHECHECK', true);

// Disable header version check
#define('GSNOVERCHECK', true);

// Disable Sitemap generation and menu items
# define('GSNOSITEMAP',true);

// Enable auto meta descriptions from content excerpts when empty
# define('GSAUTOMETAD',true);

// Set default language for missing lang token merge,
// accepts a lang string, default is 'en_US', false to disable
# define('GSMERGELANG',false);

/**
* GS can prevent backend or frontend pages from being loaded inside a frame 
* this is done by sending an x-frame-options header, and helps protect against clickjacking attacks
* This is enabled by default for backend pages (true/GSBACK)
* setting GSNOFRAME to (false) will disable this behavior
* You can also customize this by passing the gs location definitions,
* GSFRONT, GSBACK or GSBOTH definitions enable this for front and/or backends
* define('GSNOFRAME',GSBOTH); # prevent in frames ALWAYS
*/
# define('GSNOFRAME',false);  # prevent in frames NEVER

// GS can format its xml files before saving them if you require human readable source for them
# define('GSFORMATXML',true);

// enable page drafts
# define('GSUSEDRAFTS',true);

// disable page stack when using drafts
# define('GSUSEPAGESTACK',false);

// enable editing theme root files in theme editor
# define('GSTHEMEEDITROOT',true);

// custom (str) csv list of page ids and order to show tabs
#define('GSTABS','pages,upload,theme,backups,plugins,snippets,components')


/**
 * DO NOT EDIT BELOW THIS LINE
 * 
 * definitions for GS 3.3 legacy appearance, disables stuff added in 3.4 ( mostly )
 * reference only do not uncomment this
'GSTABICONS'           => false,              // (bool) show icons on nav tabs
'GSTABS'               => 'pages,upload,theme,backups,plugins', // (str) csv list of page ids and order to show tabs
'GSWIDEPAGES'          => '',                 // (str-csv) pages to apply GSWIDTHWIDE on
'GSSTYLE'              => '',                 // (str-csv) default style modifiers
'GSAJAXSAVE'           => false,              // (bool) use ajax for saving themes, components, and pages
'GSPAGETABS'           => false,              // (bool) use tabbed interface for page edit ( no page options toggle when off )
'GSUSEDRAFTS'          => false,              // (bool) use page drafts
'GSTHUMBSSHOW'         => false,              // (bool) always show thumbnails
'GSSNIPPETSATTRIB'     => '',                 // (str) callback funcname for htmleditors used to init htmleditor
'GSCOMPONENTSATTRIB'   => '',                 // (str) callback funcname for codeeditors used to init codeeditor
'GSHEADERCLASS'        => 'gradient',         // (str) custom class to add to header eg. `gradient` to add 3.3 gradients back
 */

?>

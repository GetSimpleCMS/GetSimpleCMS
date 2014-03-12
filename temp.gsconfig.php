<?php
/**
 * GSConfig
 *
 * The base configurations for GetSimple	
 *
 * @package GetSimple
 */

/** Prevent direct access */
if (basename($_SERVER['PHP_SELF']) == 'gsconfig.php') { 
	die('You cannot load this page directly.');
}; 

/*****************************************************************************/
/** Below are constants that you can use to customize how GetSimple operates */ 

# Extra salt to secure your password with. Default is empty for backwards compatibility.
#define('GSLOGINSALT', 'your_unique_phrase');

# Turn off auto-generation of SALT and use a custom value. Used for cookies & upload security.
#define('GSUSECUSTOMSALT', 'your_new_salt_value_here');

# Default thumbnail width of uploaded image
define('GSIMAGEWIDTH', '200');

# Change the administrative panel folder name
#define('GSADMIN', 'admin');

# Turn on debug mode
#define('GSDEBUG', TRUE);

# Ping search engines upon sitemap generation?
define('GSDONOTPING', 1);

# Turn off CSRF protection. Uncomment this if you keep receiving the error message "CSRF error detected..."
#define('GSNOCSRF', TRUE);

# Set override CHMOD mode
#define('GSCHMOD', 0755);

# Enable Canonical Redirects?
#define('GSCANONICAL', 1);

# Use Uploadify to upload files?
#define('GSNOUPLOADIFY', 1);

# WYSIWYG editor height (default 500)
#define('GSEDITORHEIGHT', '400');

# WYSIWYG toolbars (advanced, basic or [custom config]) 
#define('GSEDITORTOOL', 'advanced');

# WYSIWYG editor language (default en)
#define('GSEDITORLANG', 'en');

# WYSIWYG Editor Options
#define('GSEDITOROPTIONS', '');

# Set email from address
#define('GSFROMEMAIL', 'noreply@get-simple.info');

# Autosave within edit.php. Value is the autosave interval in seconds
#define('GSAUTOSAVE', 900);

# Enable the External API to be shown on settings page 
#define('GSEXTAPI', 1);
	
# Set PHP locale
# http://php.net/manual/en/function.setlocale.php
#setlocale(LC_ALL, 'en_US');

# Define default timezone of server, accepts php timezone string
# valid timeszones can be found here http://www.php.net/manual/en/timezones.php
# define('GSTIMEZONE', 'America/Chicago');

# Disable loading of external CDN versions of scripts (jQuery/jQueryUI)
#define("GSNOCDN",true);

# Disable Codemirror theme editor
#define("GSNOHIGHLIGHT",true);

# Forces suppression of php errors when GSDEBUG is false, despite php ini settings
define('SUPPRESSERRORS',true);

# Disable check for Apache web server, default false
#define('GSNOAPACHECHECK', true);

# Disable header version check
#define('GSNOVERCHECK', true);

# Enable alternate admin styles, current style constants are
# GSSTYLE can be a comma delimied list of flags
# note: stylesheets are cached, flush cache after changing
#
# style flags:
# GSSTYLEWIDE = wide fluid
# GSSTYLE_SBFIXED = fixed sidemenu
# 
# eg. 
# define('GSSTYLE',GSSTYLE_SBFIXED);
# define('GSSTYLE',GSSTYLEWIDE);
#define('GSSTYLE',implode(',',array(GSSTYLEWIDE,GSSTYLE_SBFIXED)));

# Disable Sitemap generation and menu items
# define('GSNOSITEMAP',true);

# Enable auto meta descriptions from content excerpts when empty
# define('GSAUTOMETAD',true);

# Set default language for missing lang token merge, 
# accepts a lang string, default is 'en_US', false to disable
# define('GSMERGELANG',false);

?>

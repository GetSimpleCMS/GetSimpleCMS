<?php

/*
 * Configuration file for GetSimple
 * @since 2.0
 */

if (basename($_SERVER['PHP_SELF']) == 'gsconfig.php') { 
	die('You cannot load this page directly.');
}; 

# Extra salt to secure your password with. Default is empty for backwards compatibility.
#define('GSLOGINSALT', 'your_unique_phrase');

# Default thumbnail width of uploaded image
define('GSIMAGEWIDTH', '200');

# Turn on debug mode
#define('GSDEBUG', TRUE);

# Make login cookie available sitewide.
#define('GSCOOKIEISSITEWIDE', TRUE);

# Ping search engines upon sitemap generation?
#define('GSDONOTPING', 1);

# Set override CHMOD mode
#define('GSCHMOD', 0777);

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

# Turn off auto-generation of SALT and use a custom value. Used for cookies & upload security.
#define('GSUSECUSTOMSALT', 'your_new_salt_value_here');

# set email from address
#define('GSFROMEMAIL', 'noreply@get-simple.info');


# Set PHP locale
# http://php.net/manual/en/function.setlocale.php
#setlocale(LC_ALL, 'en_US');

?>
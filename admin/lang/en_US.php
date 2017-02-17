<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * English Language File for GetSimpleCMS
 *
 * @Date:       2013-2-1
 * @Version:    GetSimple 3.4.0
 * @Traductors: Chris Cagle 
 * 
 * @url:        http://get-simple.info
 * @package     GetSimple
 * @subpackage  Language
 */

$i18n = array(

/* 
* For: install.php
*/
"PHPVER_ERROR"            =>	"<b>Unable to continue:</b> PHP 5.1.3 or greater is required, you have ",
"SIMPLEXML_ERROR"         =>	"<b>Unable to continue:</b> <em>SimpleXML</em> is not installed",
"CURL_WARNING"            =>	"<b>Warning:</b> <em>cURL</em> Not Installed",
"WEBSITENAME_ERROR"       =>	"<b>Error:</b> There was a problem with your website title",
"WEBSITEURL_ERROR"        =>	"<b>Error:</b> There was a problem with your website URL",
"USERNAME_ERROR"          =>	"<b>Error:</b> Username was not set",
"EMAIL_ERROR"             =>	"<b>Error:</b> There was a problem with your email address",
"CHMOD_ERROR"             =>	"<b>Unable to continue:</b> Unable to write the configuration file. CHMOD 755 or 777 the <code>/data</code>, <code>/backups</code> folders &amp; sub-folders and retry.",
"EMAIL_COMPLETE"          =>	"Setup Complete",
"EMAIL_USERNAME"          =>	"Your username is",
"EMAIL_PASSWORD"          =>	"Your new password is",
"EMAIL_LOGIN"             =>	"Login here",
"EMAIL_THANKYOU"          =>	"Thank you for using",
"NOTE_REGISTRATION"       =>	"Your registration information has been sent to",
"NOTE_REGERROR"           =>	"<b>Error:</b> There was a problem sending out the registration information via email. Please make note of the password below",
"NOTE_USERNAME"           =>	"Your username is",
"NOTE_PASSWORD"           =>	"and your password is",
"INSTALLATION"            =>	"Installation",
"LABEL_WEBSITE"           =>	"Website Name",
"LABEL_BASEURL"           =>	"Website URL",
"LABEL_SUGGESTION"        =>	"Our suggestion is",
"LABEL_USERNAME"          =>	"Username",
"LABEL_DISPNAME"          =>	"Display Name",
"LABEL_EMAIL"             =>	"Email Address",
"LABEL_SITEABOUT"         =>	"Notes about this website",
"LABEL_INSTALL"           =>	"Install Now!",
"SELECT_LANGUAGE"         =>	"Select your language",
"CONTINUE_SETUP"          =>	"Continue with Setup",
"DOWNLOAD_LANG"           =>	"Download Languages",
"SITE_UPDATED"            =>	"Your site has been updated",
"SERVICE_UNAVAILABLE"     =>	"This page is temporarily unavailable",

/* 
* For: pages.php
*/
"MENUITEM_SUBTITLE"       =>	"menu item",
"HOMEPAGE_SUBTITLE"       =>	"homepage",
"PRIVATE_SUBTITLE"        =>	"private",
"EDITPAGE_TITLE"          =>	"Edit Page",
"EDITING_PAGE"            =>	"Editing Page: %s",
"VIEWPAGE_TITLE"          =>	"View Page",
"DELETEPAGE_TITLE"        =>	"Delete Page",
"PAGE_MANAGEMENT"         =>	"Page Management",
"TOGGLE_STATUS"           =>	"Toggle Stat<em>u</em>s",
"TOTAL_PAGES"             =>	"total pages",
"ALL_PAGES"               =>	"Pages",
"EXPAND_TOP"              =>	"Expand Top Parents",
"COLLAPSE_TOP"            =>	"Collapse Top Parents",
"MISSING_PARENT"          =>    " - <i>Missing Parent</i>",
"EDITING_PAGE_TITLE"      =>	"Editing Page: %s",

// drafts
"EDITING_DRAFT_TITLE"     =>	"Editing Page Draft: %s",
"LABEL_DRAFT"             =>	"DRAFT",
"DRAFT_LAST_SAVED"        =>	"Page Draft saved by <em>%s</em> on",
"PUBLISH"                 =>	"Publish",
"LABEL_PUBLISHED"         =>	"PUBLISHED",
"PAGE_NO_DRAFT"           =>    "Page does not have a draft",

/*
* For: edit.php
*/
"UNKNOWN"                 =>    "Unknown",
"PAGE_NOTEXIST"           =>	"The requested page does not exist",
"BTN_SAVEPAGE"            =>	"Save Page",
"BTN_SAVEUPDATES"         =>	"Save Updates",
"DEFAULT_TEMPLATE"        =>	"Default Template",
"NONE"                    =>	"None",
"PAGE"                    =>	"Page",
"NEW_PAGE"                =>	"New Page",
"PAGE_EDIT_MODE"          =>	"Edit Page",
"CREATE_NEW_PAGE"         =>	"Add New Page",
"VIEW"                    =>	"<em>V</em>iew",
"PAGE_OPTIONS"            =>	"Page Optio<em>n</em>s",
"SLUG_URL"                =>	"Custom URL (Slug)",
"TAG_KEYWORDS"            =>	"Tags &amp; Keywords",
"PARENT_PAGE"             =>	"Page Parent",
"TEMPLATE"                =>	"Page Template",
"KEEP_PRIVATE"            =>	"Page Visibility",
"ADD_TO_MENU"             =>	"Add this page to the menu",
"PRIORITY"                =>	"Priority",
"MENU_TEXT"               =>	"Menu Text",
"LABEL_PAGEBODY"          =>	"Page Body",
"CANCEL"                  =>	"Cancel",
"BACKUP_AVAILABLE"        =>	"Backup Available",
"MAX_FILE_SIZE"           =>	"Max file size",
"LAST_SAVED"              =>	"Page last saved by %s on",
"FILE_UPLOAD"             =>	"File Upload",
"OR"                      =>	"or",
"SAVE_AND_CLOSE"          =>	"Save &amp; Close",
"PAGE_UNSAVED"            =>	"Page has unsaved changes",
"TITLELONG"               =>	"Long Title",
"SUMMARY"                 =>	"Summary",
"METAROBOTS"              =>	"Robots",
"CONTENT"                 =>	"Content",
"OPTIONS"                 =>	"Options",
"META"                    =>	"Meta",

/* 
* For: upload.php
*/
"ERROR_UPLOAD"            =>	"There was a problem with the file upload",
"FILE_SUCCESS_MSG"        =>	"File Upload Success!",
"FILE_MANAGEMENT"         =>	"File Management",
"UPLOADED_FILES"          =>	"Uploaded Files",
"SHOW_ALL"                =>	"Show All",
"VIEW_FILE"               =>	"View File",
"DELETE_FILE"             =>	"Delete File",
"TOTAL_FILES"             =>	"total files &amp; folders",
"FILE_EXISTS_PROMPT"      =>    "File Exists, Overwrite?",
"FILES"                   =>    "files",

/* 
* For: logout.php
*/
"MSG_LOGGEDOUT"           =>	"You are now logged out.",

/* 
* For: index.php
*/
"LOGIN"                   =>	"Login",
"USERNAME"                =>	"Username",
"PASSWORD"                =>	"Password",
"FORGOT_PWD"              =>	"Forgot your password?",
"CONTROL_PANEL"           =>	"Control Panel Login",

/* 
* For: navigation.php
*/
"CURRENT_MENU"            =>	"Current Menu",
"NO_MENU_PAGES"           =>	"There are no pages that are set to appear within the main menu",

/* 
* For: theme-edit.php
*/
"TEMPLATE_FILE"           =>	"Template file <b>%s</b> has successfully been updated!",
"THEME_MANAGEMENT"        =>	"Theme Management",
"EDIT_THEME"              =>	"Theme Editor",
"EDITING_FILE"            =>	"Editing File",
"BTN_SAVECHANGES"         =>	"Save Changes",
"THEME_ROOT"              =>    "Configuration Files",
"UNSAVED_PROMPT"          =>    "This page has unsaved changes, continue anyway ?",
 
/* 
* For: support.php
*/
"SETTINGS_UPDATED"        =>	"Your settings have been updated",
"UNDO"                    =>	"Undo",
"SUPPORT"                 =>	"Support",
"SETTINGS"                =>	"Settings",
"ERROR"                   =>	"Error",
"BTN_SAVESETTINGS"        =>	"Save Settings",
"VIEW_FAILED_LOGIN"       =>	"View Failed Login Attempts",


/* 
* For: log.php
*/
"MSG_HAS_BEEN_CLR"        =>	" has been cleared",
"LOGS"                    =>	"Logs",
"VIEWING"                 =>	"Viewing",
"LOG_FILE"                =>	"Log File",
"VIEW_LOG_FILE"           =>	"View Log Files",
"CLEAR_ALL_DATA"          =>	"Clear all data from",
"CLEAR_THIS_LOG"          =>	"<em>C</em>lear This Log",
"LOG_FILE_ENTRY"          =>	"LOG FILE ENTRY",
"THIS_COMPUTER"           =>	"This Computer",

/* 
* For: backup-edit.php
*/
"BAK_MANAGEMENT"          =>	"Backup Management",
"ASK_CANCEL"              =>	"<em>C</em>ancel", // 'c' is the accesskey identifier
"ASK_RESTORE"             =>	"<em>R</em>estore", // 'r' is the accesskey identifier
"ASK_DELETE"              =>	"<em>D</em>elete", // 'd' is the accesskey identifier
"BACKUP_OF"               =>	"Backup of",
"BACKUP"                  =>	"Backup",
"PAGE_TITLE"              =>	"Page Title",
"YES"                     =>	"Yes",
"NO"                      =>	"No",
"DATE"                    =>	"Date",
"PERMS"                   =>	"Perms",
"RESTOREERROR"            =>    "Restore Failed",

/* 
* For: components.php
*/
"COMPONENTS"              =>	"Components",
"DELETE_COMPONENT"        =>	"Delete Component",
"ADD_COMPONENT"           =>	"<em>A</em>dd Component", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"         =>	"Save Components",
"ACTIVE"                  =>	"Active",
"COMPONENT_DELETED"       =>    "Component will be deleted upon next save <b>%s</b>",

/* 
* For: snippets.php
*/
"SNIPPETS"                =>	"Snippets",
"DELETE_SNIPPET"          =>	"Delete Snippet",
"EDIT"                    =>	"Edit",
"ADD_SNIPPET"             =>	"<em>A</em>dd Snippet", // 'a' is the accesskey identifier
"SAVE_SNIPPETS"           =>	"Save Snippets",

/* 
* For: sitemap.php
*/
"SITEMAP_ERROR"           =>	"Your sitemap could not be generated",
"SITEMAP_REFRESHED"       =>	"Your sitemap has been refreshed",
"VIEW_SITEMAP"            =>    "View Sitemap",

/* 
* For: theme.php
*/
"THEME_CHANGED"           =>	"Your theme has been changed successfully",
"CHOOSE_THEME"            =>	"Choose Your Theme",
"ACTIVATE_THEME"          =>	"Activate Theme",
"THEME_SCREENSHOT"        =>	"Theme Screenshot",
"THEME_PATH"              =>	"Active Theme Folder Location",

/* 
* For: resetpassword.php
*/
"RESET_PASSWORD"          =>	"Reset Password",
"YOUR_NEW"                =>	"Your new",
"PASSWORD_IS"             =>	"password is",
"ATTEMPT"                 =>	"Attempt",
"MSG_PLEASE_EMAIL"        =>	"Please enter the username registered on this system, and a new password will be sent to its email address.",
"SEND_NEW_PWD"            =>	"Send New Password",

/* 
* For: settings.php
*/
"GENERAL_SETTINGS"        =>	"General Settings",
"WEBSITE_SETTINGS"        =>	"Website Settings",
"LANGUAGE"                =>	"Language",
"USE_FANCY_URLS"          =>	"Enable Custom URLs - <b style=\"font-weight:100\">Typically requires that your host has <code>mod_rewrite</code> enabled</b>",
"PERMALINK"               =>	"Custom URL Structure",
"MORE"                    =>	"more",
"HELP"                    =>	"help",
"FLUSHCACHE"              =>	"Flush All Caches",
"FLUSHCACHE-SUCCESS"      =>	"Caches Flushed Successfully",
"URL_SETTINGS"            =>    "Custom URL Settings",

/*
 * For: Profile.php
 */
"USER_PROFILE"            =>    "User Profile",
"DISPLAY_NAME"            =>	"A name for public display that is not your username",
"ONLY_NEW_PASSWORD"       =>	"Only provide a password below if you want to change your current one",
"PROVIDE_PASSWORD"        =>	"You must provide a password",
"ENABLE_HTML_ED"          =>	"<b>Enable the HTML editor</b>",
"WARN_EMAILINVALID"       =>	"WARNING: This email address does not look valid!",
"NEW_PASSWORD"            =>	"New Password",
"CONFIRM_PASSWORD"        =>	"Confirm Password",
"PASSWORD_NO_MATCH"       =>	"Passwords do not match",
"LOCAL_TIMEZONE"          =>	"Local Timezone",
"NEW_USER"                =>    "New User",
"PASSWORD_TOO_SHORT"      =>    "Password is not long enough",

/*
* For: health-check.php
*/
"WEB_HEALTH_CHECK"        =>	"Website Health Check",
"VERSION"                 =>	"Version",
"CURR_VERSION"            =>	"Current Version: %s",
"UPG_NEEDED"              =>	"A newer version is available",
"CANNOT_CHECK"            =>	"Failed to check for upgrade",
"LATEST_VERSION"          =>	"You have the latest version",
"CHECK_MANUALLY"          =>	"Check Manually",
"SERVER_SETUP"            =>	"Server Setup",
"SERVER_IS"               =>	'Server reported as %s',
"OR_GREATER_REQ"          =>	"or greater is required",
"OK"                      =>	"OK",
"INSTALLED"               =>	"Installed",
"NOT_INSTALLED"           =>	"Not Installed",
"WARNING"                 =>	"Warning",
"DATA_FILE_CHECK"         =>	"Data File Integrity Check",
"PAGE_FILE_CHECK"         =>	"Page File Integrity Check",
"DIR_PERMISSIONS"         =>	"Directory Permissions",
"EXISTANCE"               =>	"%s Existence",
"MISSING_FILE"            =>	"Missing file",
"BAD_FILE"                =>	"Bad file",
"NO_FILE"                 =>	"No file",
"GOOD_D_FILE"             =>	"Good 'Deny' file",
"GOOD_A_FILE"             =>	"Good 'Allow' file",
"GOOD_FILE"               =>	"Good file",
"CANNOT_DEL_FILE"         =>	"Cannot Delete File",
"DOWNLOAD"                =>	"Download",
"WRITABLE"                =>	"Writable",
"NOT_WRITABLE"            =>	"Not Writable",
"NA"                      =>	"N/A",

/* 
* For: footer.php
*/
"POWERED_BY"              =>	"Powered by",

/* 
* For: backups.php
*/
"PAGE_BACKUPS"            =>	"Page Backups",
"ASK_DELETE_ALL"          =>	"<em>D</em>elete All",
"DELETE_ALL_BAK"          =>	"Delete all backups?",
"TOTAL_BACKUPS"           =>	"total backups",

/* 
* For: archive.php
*/
"SUCC_WEB_ARCHIVE"        =>	"An archive of your website has been successfully created",
"SUCC_WEB_ARC_DEL"        =>	"The seleted archive has been successfully deleted",
"WEBSITE_ARCHIVES"        =>	"Website Archives",
"ARCHIVE_DELETED"         =>	"Archive deleted successfully",
"CREATE_NEW_ARC"          =>	"Create a New Archive",
"ASK_CREATE_ARC"          =>	"<em>C</em>reate New Archive Now",
"CREATE_ARC_WAIT"         =>	"<b>Please Wait:</b> Creating website archive...",
"DOWNLOAD_ARCHIVES"       =>	"Download Archive",
"DELETE_ARCHIVE"          =>	"Delete Archive",
"TOTAL_ARCHIVES"          =>	"total archives",
"ARCHIVE_DL_DISABLED"     =>	"Archive Downloads are Currently Disabled",

/* 
* For: include-nav.php
*/
"WELCOME"                 =>	"Welcome", // used as 'Welcome USERNAME!'
"TAB_PAGES"               =>	"<em>P</em>ages",
"TAB_UPLOAD"              =>	"F<em>i</em>les",
"TAB_THEME"               =>	"<em>T</em>heme",
"TAB_BACKUPS"             =>	"<em>B</em>ackups",
"TAB_PLUGINS"             =>	"Plu<em>g</em>ins",
"TAB_SETTINGS"            =>	"<em>S</em>ettings",
"TAB_SUPPORT"             =>	"Supp<em>o</em>rt",
"TAB_LOGOUT"              =>	"<em>L</em>ogout",
"TAB_COMPONENTS"          =>	"Components",
"TAB_SNIPPETS"            =>	"Snippets",
"TAB_THEME-EDIT"          =>	"Theme Editor",
"TAB_EDIT"                =>	"Edit",
"TAB_MENU-MANAGER"        =>	"Menu",
"TAB_SITEMAP"             =>	"Sitemap",
"TAB_BACKUPS"             =>	"Backups",
"TAB_ARCHIVE"             =>	"Archives",
"TAB_SUPPORT"             =>	"Support",
"TAB_HEALTH-CHECK"        =>	"Health",
"TAB_LOG"                 =>	"Logs",
"TAB_PROFILE"             =>	"Profile",

"PLUGINS_NAV"             =>	"Plu<em>g</em>ins", // legacy
"TAB_FILES"               =>	"F<em>i</em>les", // legacy

/* 
* For: sidebar-files.php
*/
"BROWSE_COMPUTER"         =>	"Browse Your Computer",
"UPLOAD"                  =>	"Upload",
"DROP_FILES"              =>	"Drop Files Here",

/* 
* For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"        =>	"Supp<em>o</em>rt Resources",
"SIDE_HEALTH_CHK"         =>	"Website <em>H</em>ealth Check",
"SIDE_DOCUMENTATION"      =>	"Wiki Documentation",
"SIDE_VIEW_LOG"           =>	"<em>V</em>iew Logs",

/* 
* For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"       =>	"<em>V</em>iew Sitemap",
"SIDE_GEN_SITEMAP"        =>	"Generate Site<em>m</em>ap",
"SIDE_COMPONENTS"         =>	"<em>E</em>dit Components",
"SIDE_SNIPPETS"           =>	"Edit Snippets",
"SIDE_EDIT_THEME"         =>	"Edit T<em>h</em>eme",
"SIDE_CHOOSE_THEME"       =>	"Choose <em>T</em>heme",

/* 
* For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"         =>	"<em>C</em>reate New Page",
"SIDE_VIEW_PAGES"         =>	"View All <em>P</em>ages",

/* 
* For: sidebar-settings.php
*/
"SIDE_GEN_SETTINGS"       =>	"General <em>S</em>ettings",
"SIDE_USER_PROFILE"       =>	"<em>U</em>ser Profile",

/* 
* For: sidebar-backups.php
*/
"SIDE_VIEW_BAK"           =>	"View Page Backup",
"SIDE_WEB_ARCHIVES"       =>	"<em>W</em>ebsite Archives",
"SIDE_PAGE_BAK"           =>	"Page <em>B</em>ackups",

/* 
* For: error_checking.php
*/
"ER_PWD_CHANGE"           =>	"Don't forget to <a href=\"%s\">change your password</a> from that random generated one you have now...",
"ER_BAKUP_DELETED"        =>	"The backup has been deleted for <b>%s</b>",
"ER_REQ_PROC_FAIL"        =>	"The requested process failed",
"ER_YOUR_CHANGES"         =>	"Your changes to <b>%s</b> have been saved",
"ER_HASBEEN_REST"         =>	"<b>%s</b> has been restored",
"ER_HASBEEN_DEL"          =>	"<b>%s</b> has been deleted",
"ER_CANNOT_INDEX"         =>	"You cannot change the URL of the index page",
"ER_CANNOT_DRAFT"         =>	"You cannot change the URL of a draft page",
"ER_SETTINGS_UPD"         =>	"Your settings have been updated",
"ER_OLD_RESTORED"         =>	"Your old settings have been restored",
"ER_PROFILE_RESTORED"     =>	"The Profile has been restored",
"ER_NEW_PWD_SENT"         =>	"A new password has been sent to the email address provided",
"ER_SENDMAIL_ERR"         =>	"There was a problem sending the email. Please try again",
"ER_FILE_DEL_SUC"         =>	"File deleted successfully",
"ER_PROBLEM_DEL"          =>	"There was a problem deleting the file",
"ER_COMPONENT_SAVE"       =>	"Your components have been saved",
"ER_COMPONENT_REST"       =>	"Your components have been restored",
"ER_SNIPPET_SAVE"         =>	"Your snippets have been saved",
"ER_SNIPPET_REST"         =>	"Your snippets have been restored",
"ER_CANCELLED_FAIL"       =>	"<b>Cancelled:</b> This update has been cancelled",
"ER_PUBLISH_SUCCESS"      =>	"Draft of <b>%s</b> has been published",
"ER_PUBLISH_ERROR"        =>	"There was a problem publishing draft of <b>%s</b>",
"ER_SAFEMODE"             =>    "Safe Mode is Active",
"ER_SAFEMODE_DISALLOW"    =>    "Operation not allowed in Safe Mode",

/* 
* For: changedata.php
*/
"CANNOT_SAVE_EMPTY"       =>	"You cannot save a page with an empty title",
"META_DESC"               =>	"Meta Description",

/* 
* For: template_functions.php
*/
"FTYPE_COMPRESSED"        =>	"Compressed", // file-type archive
"FTYPE_VECTOR"            =>	"Vector",     // file-type vector
"FTYPE_FLASH"             =>	"Flash",      // file-type flash
"FTYPE_VIDEO"             =>	"Video",      // file-type video
"FTYPE_AUDIO"             =>	"Audio",      // file-type audio
"FTYPE_WEB"               =>	"Web",        // file-type web
"FTYPE_DOCUMENT"          =>	"Document",   // file-type document
"FTYPE_DOCUMENTS"         =>	"Documents",  // file-type document
"FTYPE_SYSTEM"            =>	"System",     // file-type system file
"FTYPE_MISC"              =>	"Misc",       // file-type Miscellaneous
"FTYPE_IMAGE"             =>	"Image",      // file-type image
"FTYPE_IMAGES"            =>	"Images",     // file-type image
"FTYPE_SCRIPT"            =>	"Script",     // file-type script
"IMAGES"                  =>	"Images",

/* 
* For: login_functions.php
*/
"FILL_IN_REQ_FIELD"       =>	"Please fill in all the required fields",
"LOGIN_FAILED"            =>	"Login failed. Please double check your Username and Password",
"INVALID_PASSWORD"        =>    "Invalid Password",
"INVALID_USER"            =>    "Invalid User",

/* 
* For: Locale and Date Format
*/
"LOCALE"                  =>	"en_US",           // locale to use
"DATE_FORMAT"             =>	"M j, Y",          // short date only format
"DATE_AND_TIME_FORMAT"    =>	"F jS, Y - g:i A", // date and time format
"TIME_FORMAT"             =>	"g:i A",           // time only format

/* 
* For: support.php
*/
"WELCOME_MSG"             =>	"Thank you for choosing GetSimple as your content management system!",
"WELCOME_P"               =>	"GetSimple makes managing a website as simple as possible with its best-in-class user interface. We strive to keep the system easy enough for anyone to use, yet powerful enough for a developer to enable all the features that are needed.</p><p><strong>Some first steps that might be useful:</strong></p>",
"GETTING_STARTED"         =>	"Getting Started",
"CSRF"                    =>    "CSRF Detected!",

/* 
* For: image.php
*/

"CURRENT_THUMBNAIL"       =>	"Current Thumbnail",
"RECREATE"                =>	"recreate",
"CREATE_ONE"              =>	"create one",
"IMG_CONTROl_PANEL"       =>	"Image Control Panel",
"ORIGINAL_IMG"            =>	"Original Image",
"CLIPBOARD_INSTR"         =>	"Select All",
"CREATE_THUMBNAIL"        =>	"Create Thumbnail",
"CROP_INSTR_NEW"          =>	"Click and drag to crop, hold <kbd>Ctrl</kbd> or <kbd>&#8984; Command</kbd> for square, <kbd>esc</kbd> to clear",
"CROP_TOGGLE_INPUTS"      =>    "Show / Hide Advanced",
"SELECT_DIMENTIONS"       =>	"Selection Dimentions",
"HTML_ORIG_IMG"           =>	"Original Image HTML",
"LINK_ORIG_IMG"           =>	"Original Image Link",
"HTML_THUMBNAIL"          =>	"Thumbnail HTML",
"LINK_THUMBNAIL"          =>	"Thumbnail Link",
"HTML_THUMB_ORIG"         =>	"Thumbnail-to-Image HTML",

/* 
* For: plugins.php
*/

"PLUGINS_MANAGEMENT"      =>	"Plugin Management",
"PLUGINS_INSTALLED"       =>	"plugins installed",
"PLUGIN_DISABLED"         =>	"Disabled Plugin",
"SHOW_PLUGINS"            =>	"Installed Plu<em>g</em>ins",
"PLUGIN_NAME"             =>	"Plugin",
"PLUGIN_DESC"             =>	"Description",
"PLUGIN_VER"              =>	"Version",
"PLUGIN_UPDATED"          =>	"Plugin Updated",



/***********************************************************************************
* SINCE Version 3.0
***********************************************************************************/

/* 
* For: setup.php
*/

"ROOT_HTACCESS_ERROR"     =>	"Failed to create .htaccess in root! Please copy <code>%s</code> to <code>.htaccess</code> and change <code>%s</code> to <code>%s</code>",
"REMOVE_TEMPCONFIG_ERROR" =>	"Failed to remove <code>%s</code>! Please do it manually.",
"MOVE_TEMPCONFIG_ERROR"   =>	"Failed to rename <code>%s</code> to <code>%s</code>! Please do it manually.",
"KILL_CANT_CONTINUE"      =>	"Cannot continue. Please fix errors and try again.",
"REFRESH"                 =>	"Refresh",
"BETA"                    =>	"beta",
"ALPHA"                   =>	"alpha",
"BETA_TITLE"              =>	"Beta / Bleeding Edge",
"ALPHA_TITLE"             =>	"Alpha / Non-Stable !",

/*
* Misc Cleanup Work
*/

# new to 3.0 
"HOMEPAGE_DELETE_ERROR"   =>	"You cannot delete your homepage", //deletefile
"NO_ZIPARCHIVE"           =>	"ZipArchive extension is not installed. Unable to continue", //zip
"REDIRECT_MSG"            =>	"If your browser does not redirect you, click <a href=\"%s\">here</a>", //basic
"REDIRECT"                =>	"Redirect", //basic
"DENIED"                  =>	"Denied", //sitemap
"DEBUG_MODE"              =>	"DEBUG MODE", //nav-include
"DOUBLE_CLICK_EDIT"       =>	"Double Click to Edit", //components
"THUMB_SAVED"             =>	"Thumbnail Saved", //image
"EDIT_COMPONENTS"         =>	"Edit Components", //components
"EDIT_SNIPPETS"           =>	"Edit Snippets", //snippets
"REQS_MORE_INFO"          =>	"For more information on the required modules, visit the <a href=\"%s\" target=\"_blank\" >requirements page</a>.", //install & health-check
"SYSTEM_UPDATE"           =>	"System Update", // update.php
"AUTHOR"                  =>	"Author", //plugins.php
"ENABLE"                  =>	"Activate", //plugins.php
"DISABLE"                 =>	"Deactivate", //plugins.php
"NO_THEME_SCREENSHOT"     =>	"This theme does not have a screenshot preview", //theme.php
"UNSAVED_INFORMATION"     =>	"You are about to leave this page and will lose any unsaved information.", //edit.php
"BACK_TO_WEBSITE"         =>	"Back to Website", //index & resetpassword
"SUPPORT_FORUM"           =>	"Support Forum", //support.php
"FILTER"                  =>	"Filte<em>r</em>", //pages.php
"UPLOADIFY_BUTTON"        =>	"Upload files and/or images...", //upload.php
"FILE_BROWSER"            =>	"File Browser", //filebrowser.php
"SELECT_FILE"             =>	"Select file", //filebrowser.php
"CREATE_FOLDER"           =>	"Create Folder", //upload.php
"THUMBNAIL"               =>	"Thumbnail", //filebrowser.php
"ERROR_FOLDER_EXISTS"     =>	"The folder you are trying to create already exists", //upload.php
"FOLDER_CREATED"          =>	"The new folder was successfully created: <b>%s</b>", //upload.php
"ERROR_CREATING_FOLDER"   =>	"There was an error creating the new folder", //upload.php
"DELETE_FOLDER"           =>	"Delete Folder", //upload.php
"FILE_NAME"               =>	"File Name", //multiple tr header rows
"FILE_SIZE"               =>	"Size", //multiple tr header rows
"ARCHIVE_DATE"            =>	"Archive Date", //archive.php
"CKEDITOR_LANG"           =>	"en", // edit.php ; set CKEditor language, don't forget to include CKEditor language file in translation zip	
# new to 3.1 
"XML_INVALID"             =>	"JSON Invalid", //template-functions.php
"XML_VALID"               =>	"JSON Valid",
"UPDATE_AVAILABLE"        =>	"Update to", //plugins.php
"STATUS"                  =>	"Status", //plugins.php
"CLONE"                   =>	"Clone", //edit.php
"CLONE_SUCCESS"           =>	"Successfully created %s", //pages.php
"COPY"                    =>	"Copy", //pages.php
"COPY_N"                  =>	"Copy %s", //pages.php
"CLONE_ERROR"             =>	"There was a problem trying to clone <b>%s</b>",  //pages.php
"AUTOSAVE_STATUS"         =>	'Autosaving is ON (%ss)', //edit.php
"AUTOSAVE_NOTIFY"         =>	'Page autosaved at %s', //edit.php
"AUTOSAVE_ERROR"          =>	'Autosaving is OFF (ERROR)', //edit.php
"MENU_MANAGER"            =>	'<em>M</em>enu Manager', //edit.php
"GET_PLUGINS_LINK"        =>	'Download <em>M</em>ore Plugins',
"LOG_FILE_EMPTY"          =>	"This log file is empty", //log.php
"SHARE"                   =>	"Share", //footer.php
"NO_PARENT"               =>	"No Parent", //edit.php
"REMAINING"               =>	"characters remaining", //edit.php
"NORMAL"                  =>	"Normal", //edit.php
"ERR_CANNOT_DELETE"       =>	"Cannot delete %s. Please do this manually.", //common.php
"ADDITIONAL_ACTIONS"      =>	"Other Actions", //edit.php
"ITEMS"                   =>	"items", //upload.php
"SAVE_MENU_ORDER"         =>	"Save Menu Order", //menu-manager.php
"MENU_MANAGER_DESC"       =>	"Drag-and-drop the menu items around until you have the order you want, then click the <strong>'Save Menu Order'</strong> button.", //menu-manager.php
"MENU_MANAGER_SUCCESS"    =>	"The new menu order has been saved", //menu-manager.php
"MINIMIZENOTIFY"          =>    "Editing in fullscreen, press F11 or ESC to minimize",

/* 
* For: api related pages
*/
"API_ERR_MISSINGPARAM"    =>	'parameter data does not exist',
"API_ERR_BADMETHOD"       =>	'method %s does not exist',
"API_ERR_AUTHFAILED"      =>	'authentication failed',
"API_ERR_AUTHDISABLED"    =>	'authentication disabled',
"API_ERR_NOPAGE"          =>	'requested page %s does not exist',
"API_CONFIGURATION"       =>	'API Configuration',
"API_ENABLE"              =>	'Enable the API',
"API_REGENKEY"            =>	'Regenerate Key',
"API_DISCLAIMER"          =>	"By enabling this API you are allowing any external application that has a copy of your key to have access to your website's data.<br/><b>Only share this key with applications you trust.</b>",
"API_REGEN_DISCLAIMER"    =>	"When you regenerate your API Key, you will need to enter the new key into any external application using this API to connect to your website.",
"API_CONFIRM"             =>	"ARE YOU SURE?",


"X"                       =>	"not translated",
/*
 * Default transliteration
 */
"TRANSLITERATION" => array(
  // Roman
  'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
  'Á'=>'a', 'É'=>'e', 'Í'=>'i', 'Ó'=>'o', 'Ú'=>'u',
  'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u',
  'À'=>'a', 'È'=>'e', 'Ì'=>'i', 'Ò'=>'o', 'Ù'=>'u',
  'ä'=>'a', 'ë'=>'e', 'ï'=>'i', 'ö'=>'o', 'ü'=>'u',
  'Ä'=>'a', 'Ë'=>'e', 'Ï'=>'i', 'Ö'=>'o', 'Ü'=>'u',
  'â'=>'a', 'ê'=>'e', 'î'=>'i', 'ô'=>'o', 'û'=>'u',
  'Â'=>'a', 'Ê'=>'e', 'Î'=>'i', 'Ô'=>'o', 'Û'=>'u',
  'ñ'=>'n', 'ç'=>'c',
  'Ñ'=>'n', 'Ç'=>'c',
  '¿'=>'', '¡'=>'',
  // special Czech chars with diacritics (except some)
  "ě"=>"e","Ě"=>"E","š"=>"s","Š"=>"S","č"=>"c",
  "Č"=>"c","ř"=>"r","Ř"=>"r","ž"=>"z","Ž"=>"z",
  "ý"=>"y","Ý"=>"y",
  "ů"=>"u","Ů"=>"u","ť"=>"t","Ť"=>"t",
  "ď"=>"d","Ď"=>"d","ň"=>"n","Ň"=>"n",
  //special Slovakian chars with diacritics (except some)
  "ĺ"=>"l","ľ"=>"l","ŕ"=>"r", 
  "Ĺ"=>"l","Ľ"=>"L","Ŕ"=>"r",
  // Polish
  "Ą"=>"a","Ć"=>"c","Ę"=>"e",
  "Ł"=>"l","Ń"=>"n",
  "Ś"=>"s","Ź"=>"z","Ż"=>"z",
  "ą"=>"a","ć"=>"c","ę"=>"e",
  "ł"=>"l","ń"=>"n",
  "ś"=>"s","ź"=>"z","ż"=>"z",
  // Russian
  "А"=>"a","Б"=>"b","В"=>"v",
  "Г"=>"g","Д"=>"d","Е"=>"e","Ё"=>"yo","Ж"=>"zh",
  "З"=>"z","И"=>"i","Й"=>"j","К"=>"k","Л"=>"l",
  "М"=>"m","Н"=>"n","О"=>"o","П"=>"p","Р"=>"r",
  "С"=>"s","Т"=>"t","У"=>"u","Ф"=>"f","Х"=>"h",
  "Ц"=>"c","Ч"=>"ch","Ш"=>"sh","Щ"=>"shh","Ъ"=>"'",
  "Ы"=>"y","Ь"=>"","Э"=>"e","Ю"=>"yu","Я"=>"ya",
  "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
  "е"=>"e","ё"=>"yo","ж"=>"zh","з"=>"z","и"=>"i",
  "й"=>"j","к"=>"k","л"=>"l","м"=>"m","н"=>"n",
  "о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t",
  "у"=>"u","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch",
  "ш"=>"sh","щ"=>"shh","ъ"=>"","ы"=>"y","ь"=>"",
  
  "э"=>"e","ю"=>"yu","я"=>"ya"
),

/*
* Additions for 3.1
*/
"DEBUG_CONSOLE"           =>	'Debug Console',


/*
* Generics
* @since 3.3
*/
"COPY_SUCCESS"            =>    "Successfully Copied %s",
"COPY_FAILURE"            =>    "Failed to Copy %s",
"IS_MISSING"              =>	"%s is missing",
"NOT_FOUND"               =>	"%s was not found",
"NOT_SET"                 =>	"%s is not set",
"TITLE"                   =>	"Title",
"DELETE"                  =>	"Delete",
"REMOVE"                  =>	"Remove",
"REMOVED"                 =>	"Removed",
"FILE"                    =>	"File",
"FOLDER"                  =>	"Folder",
"DIRECTORY"               =>	"Directory",
"CLOSE"                   =>	"Close",
"CLOSED"                  =>	"Closed",
"OPEN"                    =>	"Open",
"NEXT"                    =>	"Next",
"PREVIOUS"                =>	"Previous",
"BACK"                    =>	"Back",
"TOP"                     =>	"Top",
"BOTTOM"                  =>	"Bottom",
"LEFT"                    =>	"Left",
"RIGHT"                   =>	"Right",
"UP"                      =>	"Up",
"DOWN"                    =>	"Down",
"REDO"                    =>	"Redo",
"RESET"                   =>	"Reset",
"SAVE"                    =>	"Save",
"SHOW"                    =>	"Show",
"STATUS"                  =>	"Status",
"SUCCESS"                 =>	"Success",
"PASS"                    =>	"Pass",
"PASSED"                  =>	"Passed",
"FAIL"                    =>	"Fail",
"FAILURE"                 =>	"Failure",
"FAILED"                  =>	"Failed",
"INFO"                    =>	"Info",
"ALERT"                   =>	"Alert",
"MESSAGE"                 =>	"Message",
"PRINT"                   =>	"Print",
"VIEW"                    =>	"View",
"VIEWED"                  =>	"Viewed",
"REFRESH"                 =>	"Refresh",
"REFRESHED"               =>	"Refreshed",
"DOWNLOAD"                =>	"Download",
"THEME"                   =>	"Theme",
"COMPONENT"               =>	"Component",
"SNIPPET"                 =>	"Snippet",
"PLUGIN"                  =>	"Plugin",
"TOTAL"                   =>	"Total",
"COUNT"                   =>	"Count",
"ADD"                     =>	"Add",
"NEW"                     =>	"New",
"DAY"                     =>	"Day",
"MONTH"                   =>	"Month",
"YEAR"                    =>	"Year",
"GOOD"                    =>	"Good",
"BAD"                     =>	"Bad",
"ITEMS"                   =>	"Items",
"LIST"                    =>	"List",
"ORDER"                   =>	"Order",
"ORDERED"                 =>	"Ordered",
"MENU"                    =>	"Menu",
"LOG"                     =>	"Log",
"LOGGED"                  =>	"Logged",
"CONFIGURATION"           =>    "Configuration",
"CONFIG"                  =>    "Config",
"GEN_ENABLE"              =>	"Enable",
"ENABLED"                 =>	"Enabled",
"ON"                      =>    "On",
"GEN_DISABLE"             =>	"Disable",
"DISABLED"                =>	"Disabled",
"OFF"                     =>    "Off",
"ACTIVATE"                =>	"Activate",
"ACTIVATED"               =>	"Activated",
"INACTIVE"                =>	"Inactive",
"INACTIVATE"              =>	"Inactivate",
"INACTIVATED"             =>	"Inactivated",
"SERVER"                  =>	"Server",
"CANCELLED"               =>    "Cancelled",
"PERMITTED"               =>    "Permitted",
"NOT_PERMITTED"           =>    "Not Permitted",
"ALLOW"                   =>    "Allow",
"ALLOWED"                 =>    "Allowed",
"NOT_ALLOWED"             =>    "Not Allowed",
"APPROVE"                 =>    "Approve",
"APPROVED"                =>    "Approved",
"NOT_APPROVED"            =>    "Not Approved",
"VALID"                   =>    "Valid",
"INVALID"                 =>    "Invalid",
"INVALID_OPER"            =>    "Invalid Operation",
"ERROR_OCCURED"           =>    "An Error has Occured"

// already defined generics
# "ENABLE"                =>    "Activate"
# "DISABLE"               =>	"Deactivate", //plugins.php 
# "UNDO"                  =>	"Undo",
# "YES"                   =>	"Yes",
# "NO"                    =>	"No",
# "EDIT"                  =>	"Edit",
# "CANCEL"                =>	"Cancel",
# "DESCRIPTION"           =>	"Description",
# "ERROR"                 =>	"Error",
# "WARNING"               =>	"Warning",
# "NONE"                  =>	"None",
# "PAGE"                  =>	"Page",
# "DATE"                  =>	"Date",
# "OK"                    =>	"OK",
# "DENIED"                =>	"Denied",
# "SETTINGS"              =>	"Settings",

);

/* ?> */

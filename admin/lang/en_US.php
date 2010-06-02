<?php
/****************************************************
*
* @File: 				us_US.php
* @Package:			GetSimple
* @Subject:			US English language file
* @Date:				01 Sept 2009
* @Revision:		23 Jan 2010
* @Version:			GetSimple 2.0
* @Status:			Final
* @Traductors: 	Chris Cagle 	
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Unable to continue:</b> PHP 5.1.3 or greater is required, you have ",
"SIMPLEXML_ERROR"		=>	"<b>Unable to continue:</b> <em>SimpleXML</em> is not installed",
"CURL_WARNING"			=>	"<b>Warning:</b> <em>cURL</em> Not Installed",
"TZ_WARNING"				=>	"<b>Warning:</b> <em>date_default_timezone_set</em> is missing",
"WEBSITENAME_ERROR"	=>	"<b>Error:</b> There was a problem with your website title",
"WEBSITEURL_ERROR"	=>	"<b>Error:</b> There was a problem with your website URL",
"USERNAME_ERROR"		=>	"<b>Error:</b> Username was not set",
"EMAIL_ERROR"				=>	"<b>Error:</b> There was a problem with your email address",
"CHMOD_ERROR"				=>	"<b>Unable to continue:</b> Unable to write config file. <em>CHMOD 777</em> the folders /data, /backups and their sub-folders and retry",
"EMAIL_COMPLETE"		=>	"Setup Complete",
"EMAIL_USERNAME"		=>	"Your username is",
"EMAIL_PASSWORD"		=>	"Your new password is",
"EMAIL_LOGIN"				=>	"Login here",
"EMAIL_THANKYOU"		=>	"Thank you for using",
"NOTE_REGISTRATION"	=>	"Your registration information has been sent to",
"NOTE_REGERROR"			=>	"<b>Error:</b> There was a problem sending out the registration information via email. Please make note of the password below",
"NOTE_USERNAME"			=>	"Your username is",
"NOTE_PASSWORD"			=>	"and your password is",
"INSTALLATION"			=>	"Installation",
"LABEL_WEBSITE"			=>	"Website Name",
"LABEL_BASEURL"			=>	"Website Base URL",
"LABEL_SUGGESTION"	=>	"Our suggestion is",
"LABEL_USERNAME"		=>	"Username",
"LABEL_EMAIL"				=>	"Email Address",
"LABEL_INSTALL"			=>	"Install Now!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"	=>	"menu item",
"HOMEPAGE_SUBTITLE"	=>	"homepage",
"PRIVATE_SUBTITLE"	=>	"private",
"EDITPAGE_TITLE"		=>	"Edit Page",
"VIEWPAGE_TITLE"		=>	"View Page",
"DELETEPAGE_TITLE"	=>	"Delete Page",
"PAGE_MANAGEMENT"		=>	"Page Management",
"TOGGLE_STATUS"			=>	"Toggle Status",
"TOTAL_PAGES"				=>	"total pages",
"ALL_PAGES"					=>	"All Pages",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"The requested page does not exist",
"BTN_SAVEPAGE"			=>	"Save Page",
"BTN_SAVEUPDATES"		=>	"Save Updates",
"DEFAULT_TEMPLATE"	=>	"Default Template",
"NONE"							=>	"None",
"PAGE"							=>	"Page",
"NEW_PAGE"					=>	"New Page",
"PAGE_EDIT_MODE"		=>	"Page Editing Mode",
"CREATE_NEW_PAGE"		=>	"Create New Page",
"VIEW"							=>	"<em>V</em>iew", // 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"Page <em>O</em>ptions", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"To<em>g</em>gle Editor", // 'g' is the accesskey identifier
"SLUG_URL"					=>	"Slug/URL",
"TAG_KEYWORDS"			=>	"Tags &amp; Keywords",
"PARENT_PAGE"				=>	"Parent Page",
"TEMPLATE"					=>	"Template",
"KEEP_PRIVATE"			=>	"Keep Private?",
"ADD_TO_MENU"				=>	"Add to Menu",
"PRIORITY"					=>	"Priority",
"MENU_TEXT"					=>	"Menu Text",
"LABEL_PAGEBODY"		=>	"Page Body",
"CANCEL"						=>	"Cancel",
"BACKUP_AVAILABLE"	=>	"Backup Available",
"MAX_FILE_SIZE"			=>	"Max file size",
"LAST_SAVED"				=>	"Last Saved",
"FILE_UPLOAD"				=>	"File Upload",
"OR"								=>	"or",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"There was a problem with the file upload",
"FILE_SUCCESS_MSG"	=>	"Success! File location",
"FILE_MANAGEMENT"		=>	"File Management",
"UPLOADED_FILES"		=>	"Uploaded Files",
"SHOW_ALL"					=>	"Show All",
"VIEW_FILE"					=>	"View File",
"DELETE_FILE"				=>	"Delete File",
"TOTAL_FILES"				=>	"total files",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"Logged Out",
"MSG_LOGGEDOUT"			=>	"You are now logged out.",
"MSG_PLEASE"				=>	"Please log back in if you need to re-access your account", 

/* 
 * For: index.php
*/
"LOGIN"							=>	"Login",
"USERNAME"					=>	"Username",
"PASSWORD"					=>	"Password",
"FORGOT_PWD"				=>	"Forgot your password?",
"CONTROL_PANEL"			=>	"Control Panel",
"LOGIN_REQUIREMENT"	=>	"Login Requirements",
"WARN_JS_COOKIES"		=>	"Cookies and javascript need to be enabled in your browser to work properly",
"WARN_IE6"					=>	"Internet Explorer 6 may work, but it is not supported",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Current Menu",
"NO_MENU_PAGES" 		=> 	"There are no pages that are set to appear within the main menu",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Template file <b>%s</b> has successfully been updated!",
"THEME_MANAGEMENT" 	=> 	"Theme Management",
"EDIT_THEME" 				=> 	"Edit Theme",
"EDITING_FILE" 			=> 	"Editing File",
"BTN_SAVECHANGES" 	=> 	"Save Changes",
"EDIT" 							=> 	"Edit",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"	=> 	"Your settings have been updated",
"UNDO" 							=> 	"Undo",
"SUPPORT" 					=> 	"Support",
"SETTINGS" 					=> 	"Settings",
"ERROR" 						=> 	"Error",
"BTN_SAVESETTINGS" 	=> 	"Save Settings",
"EMAIL_ON_404" 			=> 	"Email administrator on 404 errors",
"VIEW_404" 					=> 	"View 404 Errors",
"VIEW_FAILED_LOGIN"	=> 	"View Failed Login Attempts",
"VIEW_TICKETS" 			=> 	"View Your Submitted Tickets",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 	=> 	" has been cleared",
"LOGS" 							=> 	"Logs",
"VIEWING" 					=> 	"Viewing",
"LOG_FILE" 					=> 	"Log File",
"CLEAR_ALL_DATA" 		=> 	"Clear all data from",
"CLEAR_THIS_LOG" 		=> 	"<em>C</em>lear This Log", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"LOG FILE ENTRY",
"THIS_COMPUTER"			=>	"This Computer",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Backup Management",
"ASK_CANCEL"				=>	"<em>C</em>ancel", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"<em>R</em>estore", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"<em>D</em>elete", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Backup of",
"PAGE_TITLE"				=>	"Page Title",
"YES"								=>	"Yes",
"NO"								=>	"No",
"DATE"							=>	"Date",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"Components",
"DELETE_COMPONENT"	=>	"Delete Component",
"EDIT"							=>	"Edit",
"ADD_COMPONENT"			=>	"<em>A</em>dd Component", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Save Components",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Sitemap Created! We also successfully pinged 4 search engines of the update",
"SITEMAP_ERRORPING"	=>	"Sitemap Created, however there was an error pinging one or more of the search engines",
"SITEMAP_ERROR"			=>	"Your sitemap could not be generated",
"SITEMAP_WAIT"			=>	"<b>Please Wait:</b> Creating website sitemap",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Your theme has been changed successfully",
"CHOOSE_THEME"			=>	"Choose Your Theme",
"ACTIVATE_THEME"		=>	"Activate Theme",
"THEME_SCREENSHOT"	=>	"Theme Screenshot",
"THEME_PATH"				=>	"Current theme path",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"		=>	"Reset Password",
"YOUR_NEW"					=>	"Your new",
"PASSWORD_IS"				=>	"password is",
"ATTEMPT"						=>	"Attempt",
"MSG_PLEASE_EMAIL"	=>	"Please enter the email address registered on this system, and a new password will be sent to you",
"SEND_NEW_PWD"			=>	"Send New Password",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"	=>	"General Settings",
"WEBSITE_SETTINGS"	=>	"Website Settings",
"LOCAL_TIMEZONE"		=>	"Local Timezone",
"LANGUAGE"					=>	"Language",
"USE_FANCY_URLS"		=>	"<b>Use Fancy URLs</b> - Requires that your host has mod_rewrite enabled",
"ENABLE_HTML_ED"		=>	"<b>Enable the HTML editor</b>",
"USER_SETTINGS"			=>	"User Login Settings",
"WARN_EMAILINVALID"	=>	"WARNING: This email address does not look valid!",
"ONLY_NEW_PASSWORD"	=>	"Only provide a password below if you want to change your current one",
"NEW_PASSWORD"			=>	"New Password",
"CONFIRM_PASSWORD"	=>	"Confirm Password",
"PASSWORD_NO_MATCH"	=>	"Passwords do not match",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Auto: 404 Error Encountered on",
"404_AUTO_MSG"			=>	"This is an automated message from your website",
"PAGE_CANNOT_FOUND"	=>	"A 'page not found' error was encountered on the",
"DOMAIN"						=>	"domain",
"DETAILS"						=>	"DETAILS",
"WHEN"							=>	"When",
"WHO"								=>	"Who",
"FAILED_PAGE"				=>	"Failed Page",
"REFERRER"					=>	"Referrer",
"BROWSER"						=>	"Browser",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"	=>	"Website Health Check",
"VERSION"						=>	"Version",
"UPG_NEEDED"				=>	"Upgrade needed to",
"CANNOT_CHECK"			=>	"Unable to check. Your version is",
"LATEST_VERSION"		=>	"Latest version installed",
"SERVER_SETUP"			=>	"Server Setup",
"OR_GREATER_REQ"		=>	"or greater is required",
"OK"								=>	"OK",
"INSTALLED"					=>	"Installed",
"NOT_INSTALLED"			=>	"Not Installed",
"WARNING"						=>	"Warning",
"DATA_FILE_CHECK"		=>	"Data File Integrity Check",
"DIR_PERMISSIONS"		=>	"Directory Permissions",
"EXISTANCE"					=>	"%s Existance",
"MISSING_FILE"			=>	"Missing file",
"BAD_FILE"					=>	"Bad file",
"NO_FILE"						=>	"No file",
"GOOD_D_FILE"				=>	"Good 'Deny' file",
"GOOD_A_FILE"				=>	"Good 'Allow' file",
"CANNOT_DEL_FILE"		=>	"Cannot Delete File",
"DOWNLOAD"					=>	"Download",
"WRITABLE"					=>	"Writable",
"NOT_WRITABLE"			=>	"Not Writable",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Powered by",
"PRODUCTION"				=>	"A %s Production",
"SUBMIT_TICKET"			=>	"Submit Ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Page Backups",
"ASK_DELETE_ALL"		=>	"<em>D</em>elete All",
"DELETE_ALL_BAK"		=>	"Delete all backups?",
"TOTAL_BACKUPS"			=>	"total backups",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"	=>	"Successful website archive!",
"SUCC_WEB_ARC_DEL"	=>	"Website archive successfully deleted",
"WEBSITE_ARCHIVES"	=>	"Website Archives",
"ARCHIVE_DELETED"		=>	"Archive deleted successfully",
"CREATE_NEW_ARC"		=>	"Create a New Archive",
"ASK_CREATE_ARC"		=>	"<em>C</em>reate New Archive Now",
"CREATE_ARC_WAIT"		=>	"<b>Please Wait:</b> Creating website archive...",
"DOWNLOAD_ARCHIVES"	=>	"Download Archive",
"DELETE_ARCHIVE"		=>	"Delete Archive",
"TOTAL_ARCHIVES"		=>	"total archives",

/* 
 * For: include-nav.php
*/
"WELCOME"						=>	"Welcome", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"<em>P</em>ages",
"TAB_FILES"					=>	"<em>F</em>iles",
"TAB_THEME"					=>	"<em>T</em>heme",
"TAB_BACKUPS"				=>	"<em>B</em>ackups",
"TAB_SETTINGS"			=>	"<em>S</em>ettings",
"TAB_SUPPORT"				=>	"Supp<em>o</em>rt",
"TAB_LOGOUT"				=>	"<em>L</em>ogout",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Browse Your Computer",
"UPLOAD"						=>	"Upload",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"	=>	"Supp<em>o</em>rt Settings &amp; Logs",
"SIDE_VIEW_LOG"			=>	"View Log",
"SIDE_HEALTH_CHK"		=>	"Website <em>H</em>ealth Check",
"SIDE_SUBMIT_TICKET"=>	"Submit Tic<em>k</em>et",
"SIDE_DOCUMENTATION"=>	"<em>D</em>ocumentation",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"	=>	"<em>V</em>iew Sitemap",
"SIDE_GEN_SITEMAP"	=>	"<em>G</em>enerate Sitemap",
"SIDE_COMPONENTS"		=>	"<em>E</em>dit Components",
"SIDE_EDIT_THEME"		=>	"Edit T<em>h</em>eme",
"SIDE_CHOOSE_THEME"	=>	"Choose <em>T</em>heme",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"<em>C</em>reate New Page",
"SIDE_VIEW_PAGES"		=>	"View All <em>P</em>ages",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"	=>	"General <em>S</em>ettings",
"SIDE_USER_PROFILE"	=>	"<em>U</em>ser Profile",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"View Page Backup",
"SIDE_WEB_ARCHIVES"	=>	"<em>W</em>ebsite Archives",
"SIDE_PAGE_BAK"			=>	"Page <em>B</em>ackups",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Don't forget to <a href=\"settings.php#profile\">change your password</a> from that random generated one you have now...",
"ER_BAKUP_DELETED"	=>	"The backup has been deleted for %s",
"ER_REQ_PROC_FAIL"	=>	"The requested process failed",
"ER_YOUR_CHANGES"		=>	"Your changes to %s have been saved",
"ER_HASBEEN_REST"		=>	"%s has been restored",
"ER_HASBEEN_DEL"		=>	"%s has been deleted",
"ER_CANNOT_INDEX"		=>	"You cannot change the URL of the index page",
"ER_SETTINGS_UPD"		=>	"Your settings have been updated",
"ER_OLD_RESTORED"		=>	"Your old settings have been restored",
"ER_NEW_PWD_SENT"		=>	"A new password has been sent to the email address provided",
"ER_SENDMAIL_ERR"		=>	"There was a problem sending the email. Please try again",
"ER_FILE_DEL_SUC"		=>	"File deleted successfully",
"ER_PROBLEM_DEL"		=>	"There was a problem deleting the file",
"ER_COMPONENT_SAVE"	=>	"Your components have been saved",
"ER_COMPONENT_REST"	=>	"Your components have been restored",
"ER_CANCELLED_FAIL"	=>	"<b>Cancelled:</b> The update to this file has been cancelled",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"	=>	"You cannot save an empty page",
"META_DESC" 				=> "Meta Description",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"	=>	"Compressed", //a file-type
"FTYPE_VECTOR"			=>	"Vector", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Video", //a file-type
"FTYPE_AUDIO"				=>	"Audio", //a file-type
"FTYPE_WEB"					=>	"Web", //a file-type
"FTYPE_DOCUMENTS"		=>	"Documents", //a file-type
"FTYPE_SYSTEM"			=>	"System", //a file-type
"FTYPE_MISC"				=>	"Misc", //a file-type
"IMAGES"						=>	"Images",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"	=>	"Please fill in all the required fields",
"LOGIN_FAILED"			=>	"Login failed. Please double check your Username and Password",

/* 
 * For: Date Format
*/
"DATE_FORMAT"									=>	"M j, Y", //please keep short
"DATE_AND_TIME_FORMAT"				=>	"F jS, Y - g:i A", //date and time


/***********************************************************************************
 * SINCE Version 2.0
***********************************************************************************/


/* 
 * For: welcome.php
*/
"WELCOME_MSG"				=>	"Thank you for choosing GetSimple as your CMS!",
"WELCOME_P"					=>	"GetSimple makes managing your website as simple as possible with its top-of-the-class user interface and the easiest templating system around.",
"GETTING_STARTED"		=>	"Getting Started",

/* 
 * For: install.php
*/

"SELECT_LANGUAGE"		=> "Select your language",
"CONTINUE_SETUP" 		=> "Continue with Setup",
"DOWNLOAD_LANG" 		=> "Download additional languages",

/* 
 * For: image.php
*/

"CURRENT_THUMBNAIL" => "Current Thumbnail",
"RECREATE" 					=> "recreate",
"CREATE_ONE" 				=> "create one",
"IMG_CONTROl_PANEL" => "Image Control Panel",
"ORIGINAL_IMG" 			=> "Original Image",
"CLIPBOARD_COPY" 		=> "Copy to clipboard",
"CLIPBOARD_INSTR" 	=> "Select All then <em>ctrl-c</em> or <em>command-c</em>",
"CREATE_THUMBNAIL" 	=> "Create Thumbnail",
"CROP_INSTR" 				=> "<em>ctrl-Q</em> or <em>command-Q</em> for square",
"SELECT_DIMENTIONS" => "Selection Dimentions",
"HTML_ORIG_IMG" 		=> "Original Image HTML",
"LINK_ORIG_IMG" 		=> "Original Image Link",
"HTML_THUMBNAIL" 		=> "Thumbnail HTML",
"LINK_THUMBNAIL" 		=> "Thumbnail Link",
"HTML_THUMB_ORIG" 	=> "Thumbnail-to-Image HTML",

/* 
 * For: plugins.php
*/

"PLUGINS_MANAGEMENT"=> "Plugin Management",
"PLUGINS_INSTALLED" => "Plugins Installed",
"SHOW_PLUGINS"			=> "Installed Plugi<em>n</em>s",
"PLUGINS_NAV" 			=> "Plugins",
"PLUGIN_NAME" 			=> "Name",
"PLUGIN_DESC" 			=> "Description",
"PLUGIN_VER" 				=> "Version",


/***********************************************************************************
 * SINCE Version 2.02
***********************************************************************************/


"PERMALINK" => "Custom Permalink Structure",
"MORE" => "more",
"HELP" => "help",


"" => "not translated"



);

?>
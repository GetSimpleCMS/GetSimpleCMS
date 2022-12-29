<?php

$FRONT_END_HOOKS = array(
  'theme-header' => 'Fired in <head> section of theme. Requires get_header() in template',
  'theme-footer' => 'Fired in the footer of the theme. Requires get_footer() in template',
  'content-top' => 'Fired right above content area of theme',
  'content-bottom' => 'Fired right below content area of theme',
  'index-pretemplate' => 'Called before your template files are rendered',
  'index-posttemplate' => 'Called after your template files are rendered',
  'error-404' => 'Called if the page does not exist, before rendering the error page'
);

$BACK_END_HOOKS = array(
  'header' => 'Called in the head section of the rendered page',
  'header-body' => 'Called in the body before output of the page',
  'admin-pre-header' => 'Called before the header.php template file is loaded (3.1+)',
  'footer' => 'Called in the footer section of the rendered page',
  'common' => 'Called immediately after the plugin functions are included in common.php (3.1+)',
  'logout' => 'Fired when a user logs out',
  'index-login' => 'Fired above the login form',
  'login-reqs' => 'Fired on the login sidebar',
  'resetpw-success' => 'Fired when password reset and successful',
  'resetpw-error' => 'Fired when password reset and error',
  'settings-user' => 'Fired before the settings user file is created',
  'settings-website' => 'Fired before the settings website page is created',
  'settings-cpsettings' => 'Fired before the settings cp_settings file is created',
  'settings-website-extras' => 'Fired on the settings page, before “save settings” button in the website section',
  'settings-user-extras' => 'Fired on the settings page, before “save settings” button in the user section',
  'sitemap-additem' => 'Allow insertion of a new sitemap XML entry',
  'save-sitemap' => 'Fired before the sitemap.xml file is saved',
  'theme-extras' => 'Fired after the theme screenshot',
  'theme-edit-extras' => 'Fired in the theme edit screen before the submit button',
  'welcome-link' => 'Allows additional links on the Welcome page',
  'welcome-doc-link' => 'Allows additional documentation links on the Welcome page',
  'healthcheck-extras' => 'Allows additional Health-check entries',
  'support-extras' => 'Allows additional support setting form entries',
  'support-save' => 'Fired before cp_settings.xml file is created, allows additional support-extras to be saved',
  'plugin-hook' => 'Fired before the Plugin page is rendered.',
  'archive-backup' => 'Fired when an archive backup has been created',
  'component-save' => 'Fired before components are saved',
  'component-extras' => 'Fired when creating component sections, allows additional form elements to be embedded',
  'logfile_delete' => 'fired when a logfile is deleted',
  'page-delete' => 'fired when a page is deleted',
  'changedata-save' => 'Called just before a page is saved',
  'changedata-aftersave' => 'Called after a page is saved (3.1+)',
  'caching-save' => 'Fired before pages.xml file (in data/other) is saved for Caching (3.1+)',
  'edit-extras' => 'Fired within the Page Options toggle-div within edit.php',
  'edit-content' => 'Creating additional data/fields after the textarea on edit.php',
  'file-uploaded' => 'Fired after a file has been successfully uploaded',
  'file-extras' => 'Fired at the end of the file list',
  'successful-login-start' => '',
  'successful-login-end' => ''
);

$CREATION_HOOKS = array(
  'backups-sidebar' => 'Sidebar item on Backups Page',
  'files-sidebar' => 'Sidebar item on Files Page',
  'pages-sidebar' => 'Sidebar item on Pages Page',
  'plugins-sidebar' => 'Sidebar item on Plugins Page',
  'settings-sidebar' => 'Sidebar item on Settings Page',
  'support-sidebar' => 'Sidebar item on Support Page',
  'theme-sidebar' => 'Sidebar item on Theme Page',
  'nav-tab' => 'Insert navigation bar tab'
);

?>
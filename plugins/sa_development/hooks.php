<?php

$FILTERS = array(
  'content'                 => 'Fiters page content data',
  'menuitems'               => 'Filters the menu html returned in get_navigation()',
  'pagecache'               => 'filters $pagesarray data before saving (3.3+)',
  'sitemap'                 => 'filters the $sitemap xmlobj before saving (3.3+)',
  'indexid'                 => 'filters the index page $id global',
  'data_index'              =>  'filters the global page $data_index obj'
);

$FRONT_END_HOOKS = array(
  'theme-header'            => 'Fired in <head> section of theme. Requires get_header() in template',
  'theme-footer'            => 'Fired in the footer of the theme. Requires get_footer() in template',
  'content-top'             => 'Fired right above content area of theme',
  'content-bottom'          => 'Fired right below content area of theme',
  'index-pretemplate'       => 'Called before your template files are rendered',
  'index-posttemplate'      => 'Called after your template files are rendered',
  'error-404'               => 'Called if the page does not exist, before rendering the error page'
);

$BACK_END_HOOKS = array(
  'admin-pre-header'        => 'Called before the header.php template file is loaded (3.1+)',
  'archive-backup'          => 'Fired when an archive backup has been created',
  'caching-save'            => 'DEPRECATED - Fired before pages.xml file (in data/other) is saved for Caching (3.1-3.3)',
  'changedata-aftersave'    => 'Called after a page is saved (3.1+)',
	'changedata-updateslug'   => 'Called when slug changed on an existing page',
  'changedata-save'         => 'Called just before a page is saved',
  'common'                  => 'Called immediately after the plugin functions are included in common.php (3.1+)',
  'component-extras'        => 'Fired when creating component sections, allows additional form elements to be embedded',
  'component-save'          => 'Fired before components are saved',
  'edit-content'            => 'Creating additional data/fields after the textarea on edit.php',
  'edit-extras'             => 'Fired within the Page Options toggle-div within edit.php',
  'file-extras'             => 'Fired at the end of the file list',
  'file-uploaded'           => 'Fired after a file has been successfully uploaded',
  'footer'                  => 'Called in the footer section of the rendered page',
	'download-file'           => 'Called when downloading files via download.php',	
  'header'                  => 'Called in the head section of the rendered page',
  'header-body'             => 'Called in the body before output of the page',
  'healthcheck-extras'      => 'Allows additional Health-check entries',
	'html-editor-init'        => 'Called after ckeditor js is output',
  'index-login'             => 'Fired above the login form',
  'logfile_delete'          => 'Fired when a logfile is deleted',
  'login-reqs'              => 'Fired on the login sidebar',
  'logout'                  => 'Fired when a user logs out',
  'page-delete'             => 'Fired when a page is deleted',
  'pagecache-aftersave'     => 'Fired after data/other/pages.xml pagecache file is successfully saved (3.3+)',
	'pages-main'              =>	'Fired when the pages maincontent in rendered',
  'plugin-hook'             => 'Fired before the Plugin page is rendered.',
  'resetpw-error'           => 'Fired when password reset and error',
  'resetpw-success'         => 'Fired when password reset and successful',
  'save-sitemap'            => 'Fired before the sitemap.xml file is saved',
  'settings-cpsettings'     => 'DEPRECATED - Fired before the settings cp_settings file is created (2.0-3.0)',
  'settings-user'           => 'Fired before the settings user file is created',
  'settings-user-extras'    => 'Fired on the settings page, before `save settings` button in the user section',
  'settings-website'        => 'Fired before the settings website page is created',
  'settings-website-extras' => 'Fired on the settings page, before `save settings` button in the website section',
  'sitemap-additem'         => 'DEPRECATED - Allow insertion of a new sitemap XML entry (-3.1)',
  'sitemap-aftersave'       => 'Called after the sitemap is successfully saved (3.3+)',
  'successful-login-end'    => 'Fired after authentication success and before redirect',
  'successful-login-start'  => 'Fired when before login authentication starts',
  'support-extras'          => 'Allows additional support setting form entries',
  'support-save'            => 'DEPRECATED - Fired before cp_settings.xml file is created, allows additional support-extras to be saved (-3.0)',
  'theme-edit-extras'       => 'Fired in the theme edit screen before the submit button',
  'theme-extras'            => 'Fired after the theme screenshot',
  'welcome-doc-link'        => 'Allows additional documentation links on the Welcome page',
  'welcome-link'            => 'Allows additional links on the Welcome page'
); 

$CREATION_HOOKS = array(
  'backups-sidebar'         => 'Sidebar item on Backups Page',
  'files-sidebar'           => 'Sidebar item on Files Page',
  'pages-sidebar'           => 'Sidebar item on Pages Page',
  'plugins-sidebar'         => 'Sidebar item on Plugins Page',
  'settings-sidebar'        => 'Sidebar item on Settings Page',
  'support-sidebar'         => 'Sidebar item on Support Page',
  'theme-sidebar'           => 'Sidebar item on Theme Page',
  'nav-tab'                 => 'Insert navigation bar tab'
);

?>

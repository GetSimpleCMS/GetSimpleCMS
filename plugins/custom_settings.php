<?php
/*
Plugin Name: GS Custom Settings
Description: A plugin for custom site, theme and plugin settings.
Version: 0.6.1
Author: Kevin Van Lierde
Author URI: http://webketje.com/
*/

// include customSettings class
require_once(GSPLUGINPATH . 'custom_settings/customsettings.class.php');

// initiate customSettings
customSettings::upgrade();
customSettings::createDataSubfolder();
$custom_settings = customSettings::retrieveAllSettings();
$custom_settings_dictionary = customSettings::mapAllSettings();
$custom_settings_lang = customSettings::getLangFile();
customSettings::loadJsLibs();

// register plugin
register_plugin(
	'custom_settings',
	$custom_settings_lang['title'],
	customSettings::$version,
	'Kevin Van Lierde',
	'http://webketje.com', 
	$custom_settings_lang['descr'],
	'site',
  'custom_settings_init'
);

// provide a way for other themes/ plugins to check 
// whether GS Custom Settings is active and what version
define('GS_CUSTOM_SETTINGS', customSettings::$version);

// GS hooks
add_action('nav-tab', 'createNavTab', array('site', 'custom_settings', $custom_settings_lang['tab_name']));

// front-end filter (WYSIWYG)
add_filter('content', 'custom_settings_filter');

// Show Tab function
function custom_settings_init() { customSettings::init(); }

// Plugin hooks
function custom_settings_filter($content) { return customSettings::contentFilter($content); }
function custom_settings_user_permissions() { customSettings::setUserPermission(); }
function mu_custom_settings_user_permissions() { customSettings::mu_setUserPermission(); }

// beneath used in both GS Custom Settings render hooks
function custom_settings_render($plugin, $output_func) {
	if (is_callable($output_func)) {
		echo '<div data-bind="visible: data()[state.tabSelection()] && ko.unwrap(data()[state.tabSelection()].tab.data.lookup) === \'' . $plugin . '\' ">';
		$output_func();
		echo '</div>';
	}
}
// stable API functions
function return_setting($tab, $setting, $prop=NULL) { 
	return customSettings::returnSetting($tab, $setting, $prop); 
}
function get_setting($tab, $setting, $echo=TRUE)    { 
	if ($echo == TRUE) customSettings::getSetting($tab, $setting, $echo); 
	else return customSettings::getSetting($tab, $setting, $echo); 
}
function get_i18n_setting($tab, $setting, $echo=TRUE) { 
	if ($echo == TRUE) customSettings::getI18nSetting($tab, $setting, $echo); 
	else return customSettings::getI18nSetting($tab, $setting, $echo); 
}
function return_setting_group($tab, $group, $prop=NULL) { 
	return customSettings::returnSettingGroup($tab, $group, $prop); 
}
function get_tab_link($tab=NULL, $linkText='settings') {
	global $custom_settings, $SITEURL;
	$id = $tab ? '#' . $tab : '';
	echo '<a href="' . $SITEURL . 'admin/load.php?id=custom_settings' . $id . '">' . $linkText . '</a>';
}

// use with caution
function remove_setting($tab, $setting)             { customSettings::removeSetting($tab, $setting); }
function set_setting($tab, $setting, $newValue)     { customSettings::setSetting($tab, $setting, $newValue); }


// Inter-plugin compatibility tweaks

// Fallback for GS 3.3- pluginIsActive function
if (!function_exists('pluginIsActive')) {
	function pluginIsActive($pluginid){
		global $live_plugins;
		return isset($live_plugins[$pluginid.'.php']) && ($live_plugins[$pluginid.'.php'] == 'true' || $live_plugins[$pluginid.'.php'] === true);
	}
}

// give priority to MultiUser plugin if available
// if MultiUser is used, the settings-user hook doesn't work, so use common (as used by same author's plugin GS Blog)
if (pluginIsActive('user-managment')) {
	add_action('common','mu_custom_settings_user_permissions');
} else {
	add_action('settings-user','custom_settings_user_permissions');
}

// Avoid conflicts with ItemManager assets
if (pluginIsActive('imanager') && isset($_GET['id']) && $_GET['id'] === 'custom_settings') {

	function gscs_imanager_compat() {
		dequeue_style('jqui', GSBACK);
		dequeue_style('imstyle', GSBACK);
		dequeue_style('imstylefonts', GSBOTH);
		dequeue_style('blueimp', GSBACK);
	}
	add_action('admin-pre-header', 'gscs_imanager_compat');
}
	
?>

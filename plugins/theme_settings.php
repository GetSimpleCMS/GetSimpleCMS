<?php
/*
Plugin Name: Theme Settings
Description: Supports themes with theme settings.
Version: 0.4
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net
*/

$thisfile = basename(__FILE__, ".php");

register_plugin(
	$thisfile, 
	'Theme Settings', 	
	'0.4', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Support settings pages for themes',
	'theme',
	'edit_theme_settings'  
);

i18n_merge('theme_settings') || i18n_merge('theme_settings', 'en_US');

require_once(GSPLUGINPATH.'theme_settings/settings.class.php');
if (ThemeSettings::isThemeConfigurable()) {
  add_action('theme-sidebar', 'createSideMenu', array($thisfile, i18n_r('theme_settings/SETTINGS_VIEW')));
} 

# ===== back end =====

function edit_theme_settings() {
  require_once(GSPLUGINPATH.'theme_settings/backend.class.php');
  $theme = ThemeSettings::getCurrentTheme();
  if (isset($_POST['save'])) {
    ThemeSettingsBackend::saveSettings($theme);
  } else if (isset($_REQUEST['reset'])) {
    ThemeSettingsBackend::resetSettings($theme);
  }
  ThemeSettingsBackend::showSettings($theme);
}

function get_schema_select($default=null) {
  require_once(GSPLUGINPATH.'theme_settings/backend.class.php');
  $theme = ThemeSettings::getCurrentTheme();
  ThemeSettingsBackend::outputSchemaSelect($theme, $default);
}

# ===== front end theme functions =====

function return_theme_settings($defaults=array()) {
	global $language;
  $settings = ThemeSettings::getSettings(null, $defaults);
  // integration of I18N plugin
  if (@$language) {
  	$oldsettings = $settings; // copy!
  	foreach ($oldsettings as $key => $value) {
  		if (substr($key,-3,1) == '_') {
  			$lang = substr($key,-2);
  			if ($lang == $language) {
  				$settings[substr($key,0,-3)] = $settings[$key];
  			}
  			unset($settings[$key]);
  		}
  	}
  }
  // remove empty settings, as they crash lessphp
  foreach ($settings as $key => $value) {
    if ($settings[$key] === null || $settings[$key] === '') unset($settings[$key]);
  }
  return $settings;
}

function return_theme_setting($name, $default=null) {
	global $language;
  $settings = ThemeSettings::getSettings();
  if (@$language && @$settings[$name.'_'.$language]) {
  	return $settings[$name.'_'.$language];
  }
  return @$settings[$name] ? $settings[$name] : $default;
}

function get_theme_setting($name, $default='', $isHtml=false) {
  $value = return_theme_setting($name, $default);
  echo $isHtml ? $value : htmlspecialchars($value);
}

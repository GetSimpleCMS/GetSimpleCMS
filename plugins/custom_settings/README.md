## GS Custom Settings
A GetSimple CMS plugin for custom site, theme and plugin settings.
- Version: 0.6.2
- Release Date: 05 November 2015
- Documentation: http://webketje.com/projects/gs-custom-settings
- Author: Kevin Van Lierde
- Author URL: http://webketje.com
- License: [Creative Commons Attribution-ShareAlike 4.0 International License](http://creativecommons.org/licenses/by-sa/4.0/)

#### Description
GS Custom Settings is a plugin for [GetSimple CMS](http://get-simple.info/) which lets webmasters/ site managers, theme and plugin developers implement and use their own custom settings for output, configuration, and cross-plugin/-theme communication. It's a bit like the [custom fields plugin](http://get-simple.info/extend/plugin/customfields/22/), but not for pages. The plugin offers 9 different types of input to choose from, 3 access levels, per-user editing permission, and an easy UI to create, import and export the settings. Once activated, a new tab 'Site', is added, where one will find all settings created with the plugin, grouped by sidebar tab.

#### Features

* Custom settings for site managers, plugin and theme developers
* 9 different setting types (select, radio, text, textarea, checkbox, color, image, date, section titles) + 3 fancy variants (FontAwesome)
* 3 access levels for settings (normal, hidden, locked)
* Output settings in pages with `(% setting:tab/setting %)` or in PHP with `get_setting('tab','setting')`
* Restrict user editing permission per user (also works with MultiUser 1.8.2+)
* Feature-rich editing in 'edit' mode with multiselect, batch setting adding/removing & keyboard shortcuts
* Responsive feedback through notifications
* Import (IE10+ & other browsers)/ Export settings for re-use through the GUI
* Build and export settings directly through the UI for your plugin/ theme
* Extend existing themes and plugins with custom settings
* Access settings from other themes and plugins
* Fully i18n, even custom theme and plugin settings I18n-enabled
* Available in English, German, French, Dutch, Spanish & Russian

#### Plugin functions
[More info](http://webketje.github.io/projects/gs-custom-settings/#functions)
````
get_setting($tab, $setting, $echo=FALSE)
// @param {string} $tab - lookup property of the tab to search in
// @param {string} $setting - lookup property of the setting to output
// @param {boolean} $echo - whether to echo the string. Useful if you need output value without echoing.
````

````
get_i18n_setting($tab, $setting, $echo=TRUE)
// @param {string} $tab - lookup property of the tab to search in
// @param {string} $setting - lookup property of the multilingual setting to output
// @param {boolean} [$echo=TRUE] - (optional) Whether to echo the multilingual setting. 
````

````
return_setting($tab, $setting, $prop=NULL)
// @param {string} $tab - lookup property of the tab to search in
// @param {string} $setting - lookup property of the setting to return
// @param {string} [$prop=NULL] - (Optional) A single property of the setting to return
````

````
return_setting_group($tab, $group, $prop=NULL)
// @param {string} $tab - lookup property of the tab to search in
// @param {string} $group - prefix in the lookup property, common to all settings in the group
// @param {string|boolean} [$prop=NULL] - (Optional) A single property to return for all settings, 
// or FALSE to return them fully
````

````
set_setting($tab, $setting, $value)
// @param {string} $tab - lookup property of the tab to search in
// @param {string} $setting - lookup property of the setting to return
// @param {string|array} $value - The property (ies) to modify. 
````

````
remove_setting($tab, $setting)
// @param {string} $tab - lookup property of the tab to search in
// @param {string} $setting - lookup property of the setting to remove
````
````
get_tab_link($tab=NULL, $linkText='settings')
// @param {string} [$tab=NULL] - (Optional) Empty, lookup property of  
// lookup property of a plugin (basename) or 'theme_settings', or a site tab
// @param {string} [$linkText='settings'] - (Optional) A custom text for the <a> element
````
#### Plugin hooks
[More info](http://webketje.com/projects/gs-custom-settings/#hooks)

````
add_action('custom-settings-load', $function)
// @param {string} $function - the function you want to execute before settings are loaded in the plugin UI
````

````
add_action('custom-settings-save', $function)
// @param {string} $function - the function you want to execute before settings are saved to their files
````

````
add_action('custom-settings-render-top', 'custom_settings_render', array($plugin, $function))
// @param {string} $plugin - basename of your plugin
// @param {string} $function - the function you want to execute before settings are loaded in the plugin UI
````

````
add_action('custom-settings-render-bottom', 'custom_settings_render', array($plugin, $function))
// @param {string} $plugin - basename of your plugin
// @param {string} $function - the function you want to execute before settings are loaded in the plugin UI
````
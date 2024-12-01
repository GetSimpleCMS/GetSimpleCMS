## GSconfig UI

GSconfig UI is a UI shim plugin for GetSimpleCMS' `gsconfig.php`. That means you can tweak the config settings straight from your admin backend, and what's more, without needing to reload the page, log on and off when you change salts, or flush the cache when you change the backend styles! **Important**: this plugin *does not* read into your existing config, so make sure to synchronize changes to the default config in the UI before hitting 'Save updates'.
### Plugin info ###
- Version: 0.2
- Release Date: 26 May 2015
- Documentation: [webketje.com/](http://webketje.github.io/projects/gs-custom-settings)
- Author: Kevin Van Lierde (Tyblitz)
- Author URL: [webketje.com](http://webketje.com)
- License: [Creative Commons Attribution-ShareAlike 4.0 International License](http://creativecommons.org/licenses/by-sa/4.0/)

### Features ###

* Instant config update from within your GS backend
* Nicely ordered settings per topic with additional help and links
* Tweak all GSconfig settings with just 1 or 2 clicks.
* Generate SALT's for your passwords right from the UI, without having to relog.
* Generate a custom CKeditor toolbar right from the UI.
* Reset all settings to their defaults.
* Check GSconfig settings easily by using the hook `add_action('gsconfig-load', $function)`, which gives you access to `$gsconfig`. To get a config setting simply do `$gsconfig['CONSTANT']`. If `null` is returned, the setting is commented out or === default value.
* All default GS Custom Settings features (search, export/ import)

### Installation ###


1. If you haven't already, install [GS Custom Settings 0.4+](http://get-simple.info/extend/plugin/gs-custom-settings/913/).
2. [Download the plugin](http://get-simple.info/extend/plugin/gsconfig-ui/938/), extract to plugins folder, activate in plugins tab and navigate to the GS Custom Settings 'Site' tab. In the sidebar you will see an item 'GSconfig UI'
3. Tweak all the settings you like :)

### Notes ###

* This plugin requires [GS Custom Settings 0.4+](http://get-simple.info/extend/plugin/gs-custom-settings/913/)
* If you had config changes before installing, make sure to synchronize them in the UI before hitting 'Save updates' the first time
* This plugin directly edits gsconfig.php. It is important that you do not have the same constant (even when commented out) twice or more in the file.
* The settings for `GSEDITORTOOL` and `GSEDITOROPTIONS` need some polishing.

### Preview ###

![](http://i.imgur.com/czkUu5o.png)
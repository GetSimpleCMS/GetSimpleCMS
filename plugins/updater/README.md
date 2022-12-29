Updater for GetSimple
=====================
Keep your GetSimple installation and plugins up to date.

[Download Updater from GetSimple Extend](http://get-simple.info/extend/plugin/updater/403/)

[Get the latest source for Updater from GitHub](https://github.com/RWJMurphy/GetSimple-Updater)

Features
--------
* Update your out-of-date plugins with a single click
* Update your GetSimple installation to the latest version

Installation
------------
1. Extract the zip file into your website's `plugins/` directory
2. In the Plugins page in your admin panel, there will be a new menu entry: Updater

Notes
-----
I cannot guarantee that the automatic updating process will work with *every*
plugin in Extend - it expects that plugins will follow the conventions laid out
in [plugins:creation - Standard for File & Folder Creation](http://get-simple.info/wiki/plugins:creation#standard_for_file_folder_creation)
, and will refuse to install / update plugins that don't. Exceptions to this 
can be configured if desired; see `updater/inc/config.php` in the source.

As always, make backups ([shameless plug for Simple Backups](http://get-simple.info/forum/topic/3638/simple-backups/))
of your site / plugins, just in case!

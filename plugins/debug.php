<?php
/*
Plugin Name: Plugin Insight
Description: Spits out $filters, $plugin_info & $plugins contents
Version: 1.1
Author: Chris Cagle
Author URI: http://www.cagintranet.com/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 													# ID of plugin, should be filename minus php
	'Debug Mode',			 									# Title of plugin
	'1.2', 														# Version of plugin
	'Mike Swan',												# Author of plugin
	'http://www.digimute.com/', 								# Author URL
	'Turn On/Off Debug Mode', 	# Plugin Description
	'plugins', 													# Page type of plugin
	'debug_show'  												# Function that displays content
);

# activate hooks
add_action('header','debug_show',array()); 
add_action('index-pretemplate','debug_show',array()); 

function debug_show() {
	define('GSDEBUG', TRUE);
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
}
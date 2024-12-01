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
	'Plugin Insight', 									# Title of plugin
	'1.1', 															# Version of plugin
	'Chris Cagle',											# Author of plugin
	'http://www.cagintranet.com/', 			# Author URL
	'Spits out $filters, $plugin_info & $plugins contents', 	# Plugin Description
	'plugins', 													# Page type of plugin
	'myplugin_show'  										# Function that displays content
);

# activate hooks
add_action('plugins-sidebar','createSideMenu',array($thisfile,'Plugin Insight')); 

function myplugin_show() {
	global $plugin_info;
	global $plugins;
	global $filters;
	
	echo '
	<style type="text/css">
	#load pre code {
			display:block;font-size:11px;width:560px;line-height:13px;
			white-space: pre-wrap; /* css-3 */
			white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
			white-space: -pre-wrap; /* Opera 4-6 */
			white-space: -o-pre-wrap; /* Opera 7 */
			word-wrap: break-word; /* Internet Explorer 5.5+ */}
	</style>
	';
	
	echo "<h3>Plugins Installed</h3>";
	echo "<pre><code>";
		print_r($plugin_info);
	echo "</code></pre>";
	
	echo "<h3>Hooks Called</h3>";
	echo "<pre><code>";
		print_r($plugins);
	echo "</code></pre>";
	
	echo "<h3>Filters Called</h3>";
	echo "<pre><code>";
		print_r($filters);
	echo "</code></pre>";
}
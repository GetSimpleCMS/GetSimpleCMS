<?php
/*
Plugin Name: FlatBlue Admin Theme
Description: A more modern look for the admin interface in GetSimple CMS
Version: 1.0
Author: PhireWare
Author URI: http://www.phireware.com/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,
	'FlatBlue',
	'1.0',
	'PhireWare',
	'http://www.phireware.com/',
	'A more modern look for the admin interface in GetSimple CMS',
	'theme'
);

register_style('flat-blue', $SITEURL.'plugins/flat-blue/css/style.css', 1.0);
queue_style('flat-blue',GSBOTH);
<?php
/*
Plugin Name: Color Picker
Description: Edit colors of CSS files using a color picker.
Version: 0.1
Revision: 23/April/2011
Author: singulae
Author URI: http://www.singulae.com/
*/



# get correct id for plugin
$thisfile=basename(__FILE__, ".php");


# register plugin
register_plugin(
	$thisfile, 										  # ID of plugin, should be filename minus php
	'Color Picker', 								  # Title of plugin
	'0.1', 											  # Version of plugin
	'singulae',										  # Author of plugin
	'http://www.singulae.com/',		 				  # Author URL
	'Edit colors of CSS files using a color picker.', # Plugin Description
	'plugins', 										  # Page type of plugin
	'colorpicker' 	 								  # Function that displays content
);


# hooks
add_action('theme-edit-extras','colorpicker_check_page',array());
 

# functions
function colorpicker(){};

function colorpicker_check_page(){

	$checkpage = strrpos($_SERVER['REQUEST_URI'], ".css");
	
	if ($checkpage) {
		echo'
		<link type="text/css" media="screen" rel="stylesheet" href="../plugins/colorpicker/css/colorpicker.css"/>
		<script type="text/javascript" src="../plugins/colorpicker/js/jquery.colorpicker.js"></script>
		<script type="text/javascript" src="../plugins/colorpicker/js/rgbcolor.js"></script>
		<script type="text/javascript" src="../plugins/colorpicker/js/colorpicker.plugin.js"></script>
		';
	};
};














	?>
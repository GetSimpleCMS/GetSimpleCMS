<?php

/******
	Auto installer for Simple Input Tabs.
	Simply place this file in the root of your distribution, alongside the simple_input_tabs.php file,
	and place the entire plugins contents alongside your theme files.
	If this file finds itself in the theme folder it will automatically move the plugin files to the
	correct plugin installation location. This way all files can be placed into themes at the same time.
	If your theme uses a functions.php file already, simply rename it temp.functions.php and it will be
	changed back automatically.

******/

define('FROM',GSTHEMESPATH.$TEMPLATE.'/');

function move_plugin_files($files){
	foreach($files as $file){
		if(file_exists(FROM.$file)){
			if(file_exists(GSPLUGINPATH.$file)){echo "File: $file, already exists.";}
			else{rename(FROM.$file, GSPLUGINPATH.$file) or die("Failed to move the file $file.");}
	}}
}
if(FROM == dirname(__FILE__).'/'){

	$move_list[] = 'small_plugin_toolkit.php';
	$move_list[] = 'simple_input_tabs.php';
	$move_list[] = 'simple_input_tabs';

	move_plugin_files($move_list);
}

if(file_exists(FROM.'temp.functions.php')){rename(FROM.'temp.functions.php', FROM.'functions.php');}
else { unlink(__FILE__); } // delete self.

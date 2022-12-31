<?php
/*
Plugin Name: Translate
Description: Everybody can translate GetSimple!
Version: 1.0
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 
	'Translate', 	
	'1.0', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Everybody can translate GetSimple!',
	'plugins',
	'translate_show'  
);

# i18n
i18n_merge('translate') || i18n_merge('translate', 'en_US');
if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'translate') {
  @session_start();
}

# activate filter
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, i18n_r('translate/TRANSLATE_VIEW')));

function translate_show() {
  include(GSPLUGINPATH.'translate/translate.php');
}


<?php
/*
Plugin Name: Theme Highlighter
Description: Syntax highlighting of theme files (CSS, Templates, JS) and components
Version: 1.1
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net
*/

$thisfile = basename(__FILE__, ".php");

register_plugin(
	$thisfile, 
	'Theme Highlighter', 	
	'1.1', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Syntax highlighting when editing themes and components',
	'',
	''  
);

add_action('header','theme_highlighter_header');
add_action('footer','theme_highlighter_footer');

function theme_highlighter_header(){
  if (basename($_SERVER['PHP_SELF']) == 'components.php' || 
      basename($_SERVER['PHP_SELF']) == 'theme-edit.php' ||
      (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'custom-admin-css') ||
      (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages' && isset($_GET['config']))) {
    include(GSPLUGINPATH.'theme_highlighter/header.php');
  }
}

//only displayed at the theme edit page
function theme_highlighter_footer() {
  if (basename($_SERVER['PHP_SELF']) == 'components.php' || 
      basename($_SERVER['PHP_SELF']) == 'theme-edit.php' ||
      (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'custom-admin-css') ||
      (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages' && isset($_GET['config']))) {
    include(GSPLUGINPATH.'theme_highlighter/footer.php');
  }
}


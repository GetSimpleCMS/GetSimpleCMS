<?php
/*
Plugin Name: sa_tags
Description: Sends header to disable cient based XSS protection
Version: 1.1
Author: Shawn Alverson
Author URI: http://www.shawnalverson.com/

*/

$PLUGIN_ID = "sa_xss_off";
$PLUGINPATH = "$SITEURL/plugins/$PLUGIN_ID/";
$sa_url="http://tablatronix.com/getsimple-cms/sa-x-xss-off/";

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,                  //Plugin id
	'SA X-XSS off', 	          //Plugin name
	'1.1', 		                  //Plugin version
	'Shawn Alverson',           //Plugin author
	$sa_url,                    //author website
	'Disables Client XSS Filters', //Plugin description
	'',                         //page type - on which admin tab to display
	''                          //main function (administration)
);

# activate action
add_action('admin-pre-header',$PLUGIN_ID."_action");

# Functions
function sa_xss_off_action(){
  if(pageCheck('edit.php') or pageCheck('components.php') or pageCheck('settings.php') or pageCheck('profile.php')) header("X-XSS-Protection: 0");
}

function pageCheck($page)
{
  return basename($_SERVER['PHP_SELF']) == $page;
}

?>
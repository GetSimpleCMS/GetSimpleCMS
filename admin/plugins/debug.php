<?php

/*
Plugin Name: Debug Plugin
Description: Enable disable Debugging
Version: 1,0
Author: Mike
Author URI: http://www.digimute.com/
*/


$plugin_info['debug'] = array(
  'pi_name' => 'Debug Plugin',
  'pi_version' =>'1.0',
  'pi_author' =>'Mike Swan',
  'pi_author_url' => 'http://www.digimute.com/',
  'pi_description' => 'Debug Plugin for GetSimple 2.0'
  );



add_action('index-posttemplate', 'debug_checkDebugFile',array());
add_action('plugin-sidebar','createSideMenu',array('debug','config','Debug Config')); 
	


function debug_checkDebugFile(){
	if (file_exists('data/other/debug.xml')) {
		echo "Debugging Enabled";
	}
}

	
	
?>

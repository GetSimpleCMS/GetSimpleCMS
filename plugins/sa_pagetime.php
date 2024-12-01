<?php
/*
Plugin Name: sa_pagetime
Description: Provides page generation times
Version: 0.1
Author: Shawn Alverson
Author URI: http://www.shawnalverson.com/

*/

$PLUGIN_ID = "sa_pagetime";
$sa_url = "http://tablatronix.com/getsimple-cms/sa-pagetime-plugin/";

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,                  //Plugin id
	'SA PageTime', 	            //Plugin name
	'1.0', 		                  //Plugin version
	'Shawn Alverson',           //Plugin author
	$sa_url,                    //author website
	'Adds page generation time to footer', //Plugin description
	'',                         //page type - on which admin tab to display
	''                          //main function (administration)
);

# add_action('admin-pre-header'', 'sa_pagetime_start');
add_action('footer', 'sa_pagetime_end_back');
add_action('theme-footer', 'sa_pagetime_end_front');

$sa_page_start;

sa_pagetime_start();

function sa_pagetime_start(){
  GLOBAL $sa_page_start;
  
  // start timer
  $load_time = microtime(); 
  $load_time = explode(' ',$load_time); 
  $load_time = $load_time[1] + $load_time[0]; 
  $sa_page_start = $load_time; 
}

function sa_pagetime_end_front(){
  sa_pagetime_end();
}

function sa_pagetime_end_back(){
  sa_pagetime_end("_admin");
}

function sa_pagetime_end($suffix=""){
  GLOBAL $sa_page_start;
  
  if($sa_page_start){
    // end  timer
    $load_time = microtime(); 
    $load_time = explode(' ',$load_time); 
    $load_time = $load_time[1] + $load_time[0]; 
    $page_end = $load_time; 
    $final_time = ($page_end - $sa_page_start); 
    $page_load_time = number_format($final_time, 4, '.', ''); 
    echo('<span id="pagetime'.$suffix.'">Page generated in ' . $page_load_time . ' seconds</span>'); 
  }
}

?>
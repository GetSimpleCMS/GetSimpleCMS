<?php

/*
Plugin Name: Test Plugin
Description: The first GetSimple Plugin
Version: 1,0
Author: Mike
Author URI: http://www.digimute.com/
*/

// 2 hooks are enabled in index.php on the root of the site. 
// index-pretemplate is called before your template files 
// index-posttemplate is called after your template files 
// this function should print the text below your site footer. 


add_action('index-posttemplate', 'printTxt',array('Test plugin working !!!'));
	

function printTxt($txt){
	echo $txt;
}



	
	
?>

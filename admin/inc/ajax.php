<?php

/**
 * Display Available Themes
 * 
 * This file spits out a list of available themes to the control panel. 
 * This is provided thru an ajax call.
 *
 * @package GetSimple
 * @subpackage Available-Themes
 */

// Include common.php
include('common.php');
login_cookie_check();

// JSON output of pages for ckeditor select
if(isset($_REQUEST['list_pages_json'])) {
	include_once('plugin_functions.php');	
	include_once('caching_functions.php');
	getPagesXmlValues();
	header('Content-type: application/json');	
	echo list_pages_json();
	die();
}

/* ?> */

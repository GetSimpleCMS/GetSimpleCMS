<?php
/****************************************************
*
* @File: 	index.php
* @Package:	GetSimple
* @Action:	Where it all starts. 	
*
*****************************************************/
 	
 	require_once('admin/inc/basic.php');
 	require_once('admin/inc/plugin_functions.php');
 	
 	
	if (file_exists('data/other/website.xml')) {
		$thisfile = 'data/other/website.xml';
		$data = getXML($thisfile);
		$SITENAME = $data->SITENAME;
		$SITEURL = $data->SITEURL;
		$TEMPLATE = $data->TEMPLATE;
	}
	
	// if there is no siteurl set, redirect user to install setup
	if (@$SITEURL == '') { header('Location: admin/install.php'); exit; }
	
	// redirect to ignore homepage called as /index/
	if (tsl($_SERVER['PHP_SELF']) == '/index/' ) { header('Location: '. $SITEURL ); exit; }
	
	// get page id (url slug) that is being passed via .htaccess mod_rewrite
	if (isset($_GET['id'])) { 
		$id = strtolower($_GET['id']);
	} else {
		$id = "index";
	}
	
	
	// define page, spit out 404 if it doesn't exist
	// it was a long night and I accidently set the 404 error as 403. Forgive me
	$file = "data/pages/". $id .".xml";
	$file_403 = "data/other/403.xml";
	if (! file_exists($file)) {
		if (file_exists($file_403)) {
			$file = $file_403;
			include('admin/inc/403-mailer.php');
		}
	}
	
	// get data from page
	$data_index = getXML($file);
	$title = $data_index->title;
	$date = $data_index->pubDate;
	$metak = $data_index->meta;
	$metad = $data_index->metad;
	$url = $data_index->url;
	$content = $data_index->content;
	$parent = $data_index->parent;
	$template_file = $data_index->template;
	$private = $data_index->private;
	
	if ($private == 'Y') {
		header('Location: 403');
		exit;
	}

	// fix submitted by Brian: http://get-simple.info/forum/viewtopic.php?id=117
	if ($url == '403') {
	    header('HTTP/1.0 404 Not Found');
	}

	//include template functions
	include('admin/inc/theme_functions.php'); 


	// include the functions.php page if it exists within the theme
	if ( file_exists("theme/".$TEMPLATE."/functions.php") ) {
		include("theme/".$TEMPLATE."/functions.php");	
	}
	
	// call pretemplate Hook
	exec_action('index-pretemplate');
	
	// include the template and template file set within theme.php and each page
	if ( (!file_exists("theme/".$TEMPLATE."/".$template_file)) || ($template_file == '') ) { $template_file = "template.php"; }
	include("theme/".$TEMPLATE."/".$template_file);
	
	// call posttemplate Hook
	exec_action('index-posttemplate');

?>
<?php
/****************************************************
*
* @File: 	index.php
* @Package:	GetSimple
* @Action:	Where it all starts. 	
*
*****************************************************/
 	
# Setup inclusions
$load['plugin'] = true;

# Relative
$relative = '';
$admin_relative = 'admin/inc/';
$lang_relative = 'admin/';
$base = true;

# Include common.php
include('admin/inc/common.php');

# get page id (url slug) that is being passed via .htaccess mod_rewrite
if (isset($_GET['id'])){ 
	$id = str_replace ('..','',$_GET['id']);
	$id = str_replace ('/','',$id);
	$id = strtolower($id);
} else {
	$id = "index";
}

# define page, spit out 404 if it doesn't exist
$file = "data/pages/". $id .".xml";
$file_404 = "data/other/404.xml";
if (! file_exists($file)) {
	if (file_exists($file_404))	{
		$file = $file_404;
		exec_action('error-404');
	}
}

# get data from page
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

# if page is private, send to 404 error page
if ($private == 'Y') {
	header('Location: 403');
	exit;
}

# if page does not exist, throw 404 error
if ($url == '403') {
	header('HTTP/1.0 404 Not Found');
}

# check for correctly formed url
if (defined('GSCANONICAL')) {
	if ($_SERVER['REQUEST_URI'] != find_url($url, $parent, 'relative')) {
		header('Location: '. find_url($url, $parent));
	}
}

# include the functions.php page if it exists within the theme
if ( file_exists("theme/".$TEMPLATE."/functions.php") ) {
	include("theme/".$TEMPLATE."/functions.php");	
}

# call pretemplate Hook
exec_action('index-pretemplate');

# include the template and template file set within theme.php and each page
if ( (!file_exists("theme/".$TEMPLATE."/".$template_file)) || ($template_file == '') ) { $template_file = "template.php"; }
include("theme/".$TEMPLATE."/".$template_file);

# call posttemplate Hook
exec_action('index-posttemplate');

?>
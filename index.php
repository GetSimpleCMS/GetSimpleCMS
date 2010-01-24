<?php
/****************************************************
*
* @File: 	index.php
* @Package:	GetSimple
* @Action:	Where it all starts. 	
*
*****************************************************/
 	
// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '';
$admin_relative = 'admin/inc/';
$lang_relative = 'admin/';
$base = true;

// Include common.php
include('admin/inc/common.php');
	
// redirect to ignore homepage called as /index/
if (tsl($_SERVER['PHP_SELF']) == '/index/' ) { header('Location: '. $SITEURL ); exit; }

// get page id (url slug) that is being passed via .htaccess mod_rewrite
if (isset($_GET['id']))
{ 
	$id = strtolower($_GET['id']);
} 
else 
{
	$id = "index";
}


// define page, spit out 404 if it doesn't exist
// it was a long night and I accidently set the 404 error as 403. Forgive me
$file = "data/pages/". $id .".xml";
$file_404 = "data/other/403.xml";
if (! file_exists($file))
{
	if (file_exists($file_404))
	{
		$file = $file_404;
		include($admin_relative. '404-mailer.php');
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

if ($url == '403') {
	header('HTTP/1.0 404 Not Found');
}


// include the functions.php page if it exists within the theme
if ( file_exists("theme/".$TEMPLATE."/functions.php") )
{
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
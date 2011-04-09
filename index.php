<?php
/**
 * Index
 *
 * Where it all starts	
 *
 * @package GetSimple
 * @subpackage FrontEnd
 */
 	
# Setup inclusions
$load['plugin'] = true;
if (file_exists('gsconfig.php')) {
	require_once('gsconfig.php');
}

# Relative
if (defined('GSADMIN')) {
	$GSADMIN = GSADMIN;
} else {
	$GSADMIN = 'admin';
}
$admin_relative = $GSADMIN.'/inc/';
$lang_relative = $GSADMIN.'/';
$base = true;

# Include common.php
include($GSADMIN.'/inc/common.php');

# get page id (url slug) that is being passed via .htaccess mod_rewrite
if (isset($_GET['id'])){ 
	$id = str_replace ('..','',$_GET['id']);
	$id = str_replace ('/','',$id);
	$id = lowercase($id);
} else {
	$id = "index";
}

# define page, spit out 404 if it doesn't exist
$file = GSDATAPAGESPATH . $id .'.xml';
$file_404 = GSDATAOTHERPATH . '404.xml';
$user_created_404 = GSDATAPAGESPATH . '404.xml';
if (! file_exists($file)) {
	if (file_exists($user_created_404)) {
		//user created their own 404 page, which overrides the default 404 message
		$file = $user_created_404;
	} elseif (file_exists($file_404))	{
		$file = $file_404;
	}
	exec_action('error-404');
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

# if page is private, check user
if ($private == 'Y') {
	if (isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME')) {
		//ok, allow the person to see it then
	} else {
		redirect('404');
	}
}

# if page does not exist, throw 404 error
if ($url == '404') {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
}

# check for correctly formed url
if (defined('GSCANONICAL')) {
	if ($_SERVER['REQUEST_URI'] != find_url($url, $parent, 'relative')) {
		redirect(find_url($url, $parent));
	}
}

# include the functions.php page if it exists within the theme
if ( file_exists(GSTHEMESPATH .$TEMPLATE."/functions.php") ) {
	include(GSTHEMESPATH .$TEMPLATE."/functions.php");	
}

# call pretemplate Hook
exec_action('index-pretemplate');

# include the template and template file set within theme.php and each page
if ( (!file_exists(GSTHEMESPATH .$TEMPLATE."/".$template_file)) || ($template_file == '') ) { $template_file = "template.php"; }
include(GSTHEMESPATH .$TEMPLATE."/".$template_file);

# call posttemplate Hook
exec_action('index-posttemplate');

?>
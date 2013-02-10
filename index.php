<?php
/**
 * Index
 *
 * Where it all starts	
 *
 * @package GetSimple
 * @subpackage FrontEnd
 */

/**
 *  GSCONFIG definitions
 */

if(!defined('GSSTYLEWIDE')) define('GSSTYLEWIDE','wide'); // wide style sheet

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

# Hook to load page Cache
exec_action('index-header');

# get page id (url slug) that is being passed via .htaccess mod_rewrite
if (isset($_GET['id'])){ 
	$id = str_replace ('..','',$_GET['id']);
	$id = str_replace ('/','',$id);
	$id = lowercase($id);
} else {
	$id = "index";
}

# define page, spit out 404 if it doesn't exist
$file_404 = GSDATAOTHERPATH . '404.xml';
$user_created_404 = GSDATAPAGESPATH . '404.xml';
if (!array_key_exists($id, $pagesArray)) {
	if (file_exists($user_created_404)) {
		//user created their own 404 page, which overrides the default 404 message
		$id='404';
	} elseif (file_exists($file_404))	{
		// this file is not cached so we need to read it in normally.
		$id=''; 		
	}
	exec_action('error-404');
}

if ($id==''){
	$data_index    = getXML($file_404);
	$title         = $data_index->title;
	$date          = $data_index->pubDate;
	$metak         = $data_index->meta;
	$metad         = $data_index->metad;
	$url           = $data_index->url;
	$content       = $data_index->content;
	$parent        = $data_index->parent;
	$template_file = $data_index->template;
	$private       = $data_index->private;	
} else {
	# get data from page cache
	$title         = $pagesArray[$id]['title'];
	$date          = $pagesArray[$id]['pubDate'];
	$metak         = $pagesArray[$id]['meta'];
	$metad         = $pagesArray[$id]['metad'];
	$url           = $pagesArray[$id]['url'];
	$content       = returnPageContent($id,'content',true);
	$parent        = $pagesArray[$id]['parent'];
	$template_file = $pagesArray[$id]['template'];
	$private       = $pagesArray[$id]['private'];

	// @todo: we might need this later, or abtract these both into methods
	// # get data from page
	// $file = GSDATAPAGESPATH . $id .'.xml';
	// $data_index    = getXML($file);  	
	// $title         = $data_index->title;
	// $date          = $data_index->pubDate;
	// $metak         = $data_index->meta;
	// $metad         = $data_index->metad;
	// $url           = $data_index->url;
	// $content       = $data_index->content;
	// $parent        = $data_index->parent;
	// $template_file = $data_index->template;
	// $private       = $data_index->private;
}

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
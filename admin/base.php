<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

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

// filter to modify page id request
$id = exec_filter('indexid',$id);
 // $_GET['id'] = $id; // support for plugins that are checking get?

$data_index = null;

// apply page data if page id exists
if (isset($pagesArray[$id])) {
	$data_index = getXml(GSDATAPAGESPATH . $id . '.xml');
}

// filter to modify data_index obj
$data_index = exec_filter('data_index',$data_index);

$file_404         = GSDATAOTHERPATH . GSSLUGNOTFOUND .'.xml'; // Legacy DEPRECATED
$user_created_404 = GSDATAPAGESPATH . GSSLUGNOTFOUND .'.xml'; // legacy DEPRECATED

// page not found handling
if(!$data_index) {
	$httpcode = GSSLUGNOTFOUND;
	$data_index = getHttpResponsePage($httpcode);
	exec_action('error-404'); // Legacy DEPRECATED
	exec_action('pagenotfound');
}

// is page private
if($data_index->private == 'Y' && !is_logged_in()){
	$httpcode = GSSLUGPRIVATE;
	$data_index = getHttpResponsePage($httpcode);
	exec_action('pageisprivate');
}

// failsafe to standard http responses if we still have no data
if(!$data_index && isset($httpcode)) {
	header($_SERVER["SERVER_PROTOCOL"].' '.$httpcode);
	die();
}

$title          = $data_index->title;
$date           = $data_index->pubDate;
$metak          = $data_index->meta;
$metad          = $data_index->metad;
$metarNoIndex   = $data_index->metarNoIndex;
$metarNoFollow  = $data_index->metarNoFollow;
$metarNoArchive = $data_index->metarNoArchive;
$url            = $data_index->url;
$content        = $data_index->content;
$parent         = $data_index->parent;
$template_file  = $data_index->template;
$private        = $data_index->private;

// after fields from dataindex, can modify globals here or do whatever by checking them
exec_action('index-post-dataindex');

# if page does not exist, throw http response header then output
$errorcode = GSHTTPPREFIX !== '' ? str_replace(GSHTTPPREFIX,'',$url) : $url;
if ($errorcode == GSSLUGNOTFOUND || $errorcode == GSSLUGPRIVATE) {
	header($_SERVER["SERVER_PROTOCOL"].' '.$errorcode);
}

# check for correctly formed url
if (getDef('GSCANONICAL',true)) {
	if ($_SERVER['REQUEST_URI'] != find_url($url, $parent, 'relative')) {
		redirect(find_url($url, $parent));
	}
}

if($load['template']){
	# call pretemplate Hook
	exec_action('index-pretemplate');

	// include theme
	includeTheme($TEMPLATE,$template_file);

	# call posttemplate Hook
	exec_action('index-posttemplate');
}

?>
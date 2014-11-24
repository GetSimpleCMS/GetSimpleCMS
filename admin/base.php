<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

# Hook to load page Cache
exec_action('index-header');

if(!$plugins) debugLog("GS ERROR: plugins is empty!");
if(!$pagesArray) debugLog("GS ERROR: pagesArray is empty!");

# get page id (url slug) that is being passed via .htaccess mod_rewrite
if (isset($_GET['id'])){
	$id = str_replace ('..','',$_GET['id']);
	$id = str_replace ('/','',$id);
	$id = lowercase($id);
} else {
	$id = "index";
}

// filter to modify page id request
$id = exec_filter('indexid',$id); // @filter indexid (str) filter the front end index id/slug
 // $_GET['id'] = $id; // @todo: do we need this for support for plugins that are checking get?

$data_index = null;

// load page data
if(isset($_GET['draft']) && is_logged_in() && pageHasDraft($id)){
	// display draft if specified else
	$data_index = getDraftXml($id);
}
else if(isset($pagesArray[$id])) {
	// apply page data if page id exists
	$data_index = getPageXml($id);
}

// filter to modify data_index obj
$data_index = exec_filter('data_index',$data_index); // @filter data_index (obj) filter the global $data_index that holds front end page load data

$file_404         = GSDATAOTHERPATH . GSSLUGNOTFOUND .'.xml'; // Legacy DEPRECATED
$user_created_404 = GSDATAPAGESPATH . GSSLUGNOTFOUND .'.xml'; // legacy DEPRECATED

// page not found handling
if(!$data_index) {
	debugLog('GS ERROR: data_index is empty!');
	$httpcode = GSSLUGNOTFOUND;
	$data_index = getHttpResponsePage($httpcode);
	exec_action('error-404'); // @hook error-404 Legacy 404 DEPRECATED
	exec_action('pagenotfound'); // @hook pagenotfound no page requested was not found
}
else{
	// is page private
	if($data_index->private == 'Y' && !is_logged_in()){
		$httpcode = GSSLUGPRIVATE;
		$data_index = getHttpResponsePage($httpcode);
		exec_action('pageisprivate'); // @hook pageisprivate page requested is marked private
	}
}
// failsafe to standard http responses if we still have no data
if(!$data_index) {
	if(isset($httpcode)){
		header($_SERVER["SERVER_PROTOCOL"].' '.$httpcode);
		$title         = "404 Not Found fallback";
		$url           = GSSLUGNOTFOUND;
		$template_file = '';
	} 
	else{
		debugLog('data_index and http fallback fail');
		die($title);	// ultimate fall through catch
	}	
}
else{
	$title          = $data_index->title;
	$titlelong      = $data_index->titlelong;
	$summary        = $data_index->summary;
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
}

exec_action('index-post-dataindex'); // @hook index-post-dataindex after global page fields set from dataindex

# if page does not exist, throw http response header then output
$errorcode = GSHTTPPREFIX !== '' ? str_replace(GSHTTPPREFIX,'',$url) : $url;
if ($errorcode == GSSLUGNOTFOUND || $errorcode == GSSLUGPRIVATE) {
	header($_SERVER["SERVER_PROTOCOL"].' '.$errorcode);
}

if($load['template']){
	# call pretemplate Hook
	exec_action('index-pretemplate'); // @hook index-pretemplate before including theme template files

	// include theme
	includeTheme($TEMPLATE,$template_file);

	# call posttemplate Hook
	exec_action('index-posttemplate'); // @hook index-posttemplate after including theme template files
}

?>
<?php 
/**
 * Download Files
 *
 * Forces the download of file types
 *
 * @package GetSimple
 * @subpackage Download
 */
 
// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

# check if all variables are set
if(isset($_GET['file'])) {
	
	$file = str_replace('../','',$_GET['file']);
	
	# get file extention (type)
	$extention = substr($file, strrpos($file, '.') + 1);

	# set content headers
	if ($extention == 'zip') {
		header("Content-disposition: attachment; filename=".$file);
	  header("Content-type: application/octet-stream");
	} elseif ($extention == 'mpg') {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: video/mpeg");
	} elseif ($extention == 'jpg' || $extention == 'jpeg' ) {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: image/jpeg");
	} elseif ($extention == 'txt' || $extention == 'log' ) {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: text/plain");
	} elseif ($extention == 'xml' ) {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: text/xml");
	} elseif ($extention == 'js' ) {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: text/javascript");
	} elseif ($extention == 'pdf' ) {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: text/pdf");
	} elseif ($extention == 'css' ) {
		header("Content-disposition: attachment; filename=".$file);
		header("Content-type: text/css");
	} 
	
	# plugin hook
	exec_action('download-file');
	
	# get file
	if (file_exists($file)) {		
		readfile($file, 'r');
	}
	exit;
	
} else {
	echo 'No such file found';
	die;
}

exit;

?>
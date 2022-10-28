<?php 
/**
 * Download Files
 *
 * Forces the download of file types
 * Allows downloads of any file in uploads and backups/zip
 *
 * @package GetSimple
 * @subpackage Download
 */
 
// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

// disable this entirely if not enabled
if(getdef('GSALLOWDOWNLOADS',true) === false) die(i18n('NOT_ALLOWED'));

# check if all variables are set
if(isset($_GET['file'])) {
	
	$file = removerelativepath($_GET['file']);

	// check that this file is safe to access
	$archivesafe = filepath_is_safe($file,GSBACKUPSPATH.DIRECTORY_SEPARATOR.'zip'); // check for archives
	if($archivesafe) check_for_csrf("archive", "download.php");                     // check archive nonce

	$filesafe = filepath_is_safe($file,GSDATAUPLOADPATH);      // check for uploads

	if(!($filesafe || $archivesafe)) die(i18n('NOT_ALLOWED')); // file specified is non existant or LFI! WE DIE
	
	$extention = getFileExtension($file);
	header("Content-disposition: attachment; filename=".$file);
	
	# set content headers
	if ($extention == 'zip') {
	  header("Content-type: application/octet-stream");
	} elseif ($extention == 'gz') {
		header("Content-type: application/x-gzip");
	} elseif ($extention == 'mpg') {
		header("Content-type: video/mpeg");
	} elseif ($extention == 'jpg' || $extention == 'jpeg' ) {
		header("Content-type: image/jpeg");
	} elseif ($extention == 'txt' || $extention == 'log' ) {
		header("Content-type: text/plain");
	} elseif ($extention == 'xml' ) {
		header("Content-type: text/xml");
	} elseif ($extention == 'js' ) {
		header("Content-type: text/javascript");
	} elseif ($extention == 'pdf' ) {
		header("Content-type: text/pdf");
	} elseif ($extention == 'css' ) {
		header("Content-type: text/css");
	} 
	
	# plugin hook
	exec_action('download-file'); // @hook download-file downloading an archive backup
	
	# get file
	// debugLog(ob_get_level()); // check buffering level for memory issues
	if (file_exists($file)) {
		readfile($file, 'r');
	}
	exit;
	
} else {
	echo 'No such file found';
	die;
}

exit;
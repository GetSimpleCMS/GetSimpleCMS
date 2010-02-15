<?php 
/****************************************************
*
* @File: 		download.php
* @Package:	GetSimple
* @Action:	downloads files 	
*
*****************************************************/
// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');
login_cookie_check();

# check if all variables are set
if(isset($_GET['type']) && isset($_GET['file'])) {
	
	# set content headers
	if ($_GET['type'] == 'zip') {
		header('Content-disposition: attachment; filename='.$_GET['file']);
	  header('Content-type: application/octet-stream');
	} elseif ($_GET['type'] == 'mpg') {
		header('Content-disposition: attachment; filename='.$_GET['file']);
		header('Content-type: video/mpeg');
	}
	
	# plugin hook
	exec_action('download-file');
	
	# get file
	if (file_exists($_GET['file'])) {		
		readfile($_GET['file'], 'r');
	}
	exit;
	
} else {
	echo 'No such file found';
	die;
}

exit;

?>
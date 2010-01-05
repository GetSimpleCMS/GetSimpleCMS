<?php 
/****************************************************
*
* @File: 		deletefile.php
* @Package:	GetSimple
* @Action:	Delete files across the control panel. 	
*
*****************************************************/


	require_once('inc/functions.php');
	require_once('inc/plugin_functions.php');
	$path = tsl('../data/other/');
	$bakpath = tsl('../backups/other/');
	$file = 'website.xml';
	$data = getXML($path . $file);
	global $SITENAME;
	global $SITEURL;
	
	$userid = login_cookie_check();


// are we deleting pages?
if (isset($_GET['id'])) { 
	$id = $_GET['id'];
	if ($id == 'index') {
		header('Location: pages.php?upd=edit-err&type=You cannot delete your homepage');
	} else {
		delete_file($id);
		header("Location: pages.php?upd=edit-success&id=". $id ."&type=delete");
	}
} 

// are we deleting archives?
if (isset($_GET['zip'])) { 
	$zip = $_GET['zip'];
	$status = delete_zip($zip);
	header("Location: archive.php?upd=del-". $status ."&id=". $zip);
} 

// are we deleting uploads?
if (isset($_GET['file'])) { 
	$file = $_GET['file'];
	delete_upload($file);
	header("Location: upload.php?upd=del-success&id=". $file);
} 



?>
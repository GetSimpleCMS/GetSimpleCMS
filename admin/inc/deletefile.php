<?php 
/****************************************************
*
* @File: 		deletefile.php
* @Package:	GetSimple
* @Action:	Delete files across the control panel. 	
*
*****************************************************/
	//disable or enable error reporting
	if (file_exists('../../data/other/debug.xml')) {
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 1);
	} else {
		error_reporting(0);
		@ini_set('display_errors', 0);
	}
	
	include('template_functions.php');

// are we deleting pages?
if (isset($_GET['id'])) { 
	$id = $_GET['id'];
	if ($id == 'index') {
		header('Location: ../pages.php?upd=edit-err&type=You cannot delete your homepage');
	} else {
		delete_file($id);
		header("Location: ../pages.php?upd=edit-success&id=". $id ."&type=delete");
	}
} 

// are we deleting archives?
if (isset($_GET['zip'])) { 
	$zip = $_GET['zip'];
	$status = delete_zip($zip);
	header("Location: ../archive.php?upd=del-". $status ."&id=". $zip);
} 

// are we deleting uploads?
if (isset($_GET['file'])) { 
	$file = $_GET['file'];
	delete_upload($file);
	header("Location: ../upload.php?upd=del-success&id=". $file);
} 



?>
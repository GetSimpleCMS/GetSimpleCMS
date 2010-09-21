<?php 
/****************************************************
*
* @File: 		deletefile.php
* @Package:	GetSimple
* @Action:	Delete files across the control panel. 	
*
*****************************************************/


// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');
login_cookie_check();

$nonce = $_GET['nonce'];

if(!check_nonce($nonce, "delete", "deletefile.php"))
	die("CSRF detected!");

// are we deleting pages?
if (isset($_GET['id'])) 
{ 
	$id = $_GET['id'];
	
	if ($id == 'index') 
	{
		header('Location: pages.php?upd=edit-err&type=You cannot delete your homepage');
	} 
	else 
	{
		delete_file($id);
		header("Location: pages.php?upd=edit-success&id=". $id ."&type=delete");
	}
} 

// are we deleting archives?
if (isset($_GET['zip'])) 
{ 
	$zip = $_GET['zip'];
	$status = delete_zip($zip);
	
	header("Location: archive.php?upd=del-". $status ."&id=". $zip);
} 

// are we deleting uploads?
if (isset($_GET['file'])) 
{ 
	$file = $_GET['file'];
	delete_upload($file);
	
	header("Location: upload.php?upd=del-success&id=". $file);
} 



?>
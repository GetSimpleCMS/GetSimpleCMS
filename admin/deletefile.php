<?php 
/**
 * Delete File
 *
 * Deletes Files based on what is passed to it 	
 *
 * @package GetSimple
 * @subpackage Delete-Files
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

$nonce = $_GET['nonce'];

if(!check_nonce($nonce, "delete", "deletefile.php")) {
	die("CSRF detected!");
}

// are we deleting pages?
if (isset($_GET['id'])) { 
	$id = $_GET['id'];
	
	if ($id == 'index') {
		redirect('pages.php?upd=edit-err&type='.urlencode(i18n_r('HOMEPAGE_DELETE_ERROR')));
	} else {
		exec_action('page-delete');
		delete_file($id);
		redirect("pages.php?upd=edit-success&id=". $id ."&type=delete");
	}
} 

// are we deleting archives?
if (isset($_GET['zip'])) { 
	$zip = $_GET['zip'];
	$status = delete_zip($zip);
	
	redirect("archive.php?upd=del-". $status ."&id=". $zip);
} 

// are we deleting uploads?
if (isset($_GET['file'])) { 
	$file = $_GET['file'];
	delete_upload($file);
	
	redirect("upload.php?upd=del-success&id=". $file);
} 



?>
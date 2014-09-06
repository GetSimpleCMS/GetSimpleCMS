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

check_for_csrf("delete", "deletefile.php");
	
// are we deleting pages?
if (isset($_GET['id'])) { 
	$id = $_GET['id'];
	
	if ($id == 'index') {
		redirect('pages.php?upd=edit-error&type='.urlencode(i18n_r('HOMEPAGE_DELETE_ERROR')));
	} else {	
		changeChildParents($id);
		$status = delete_page($id) ? 'success' : 'error';
		generate_sitemap();
		exec_action('page-delete');
		redirect("pages.php?upd=edit-".$status."&id=". $id ."&type=delete");
	}
}

// Delete archive
if (isset($_GET['zip'])) { 
	$zip    = $_GET['zip'];
	$status = delete_zip($zip) ? 'success' : 'error';
	
	redirect("archive.php?upd=del-". $status ."&id=". $zip);
} 

// Delete upload file
if (isset($_GET['file'])) {
	$path   = (isset($_GET['path'])) ? $_GET['path'] : "";
	$file   = $_GET['file'];
	$status = delete_upload($file, $path) ? 'success' : 'error';
	
	redirect("upload.php?upd=del-".$status."&id=". $file . "&path=" . $path);
} 


// Delete upload folders
if (isset($_GET['folder'])) {
	$path   = (isset($_GET['path'])) ? $_GET['path'] : "";
	$folder = $_GET['folder'];
	$status = delete_upload_dir($path . $folder) ? 'success' : 'error';

	redirect("upload.php?upd=del-".$status."&id=". $folder . "&path=".$path);
}

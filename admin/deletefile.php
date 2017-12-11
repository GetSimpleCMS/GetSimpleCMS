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
		exec_action('page-delete'); // @hook page-delete deleting page
		redirect("pages.php?upd=edit-".$status."&id=". $id ."&type=delete");
		die();
	}
}

// are we deleting page draft?
if (isset($_GET['draft'])) {
	$id = $_GET['draft'];
	$status = delete_draft($id) ? 'success' : 'error';
	exec_action('draft-delete'); // @hook draft-delete deleting a page draft
	redirect("pages.php?upd=edit-".$status."&id=". $id ."&type=delete");
	die();
}

// Delete archive
if (isset($_GET['zip'])) { 
	$zip    = $_GET['zip'];
	$status = delete_zip($zip) ? 'success' : 'error';
	exec_action('zip-delete');	// @hook zip-delete deleting archive zip
	redirect("archive.php?upd=del-". $status ."&id=". $zip);
	die();
} 

// Delete upload file
if (isset($_GET['file']) && getDef('GSALLOWUPLOADDELETE',true)) {
	$path   = (isset($_GET['path'])) ? $_GET['path'] : "";
	$file   = $_GET['file'];
	$status = delete_upload($file, $path) ? 'success' : 'error';
	exec_action('upload-delete');// @hook upload-delete deleting uploads file
	redirect("upload.php?upd=del-".$status."&id=". $file . "&path=" . $path);
	die();
} 

// Delete upload folders
if (isset($_GET['folder']) && getDef('GSALLOWUPLOADDELETE',true)) {
	$path   = (isset($_GET['path'])) ? $_GET['path'] : "";
	$folder = $_GET['folder'];
	$status = delete_upload_dir($path . $folder) ? 'success' : 'error';
	exec_action('upload-folder-delete'); // @hook upload-folder-delete deleting uploads folder
	redirect("upload.php?upd=del-".$status."&id=". $folder . "&path=".$path);
	die();
}

// Delete a log file
if (isset($_GET['log'])) {
	$log = $_GET['log'];
	delete_logfile($log);
	exec_action('logfile-delete'); //@hook logfile-delete deleting log file 
	redirect('log.php?success='.urlencode('Log '.var_out($log_name) . i18n_r('MSG_HAS_BEEN_CLR')));
}

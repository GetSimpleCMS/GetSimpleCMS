<?php
/****************************************************
*
* @File: 		zip.php
* @Package:	GetSimple
* @Action:	Creates an archive for the website 	
*
*****************************************************/

	// Setup inclusions
	$load['plugin'] = true;
	
	// Relative
	$relative = '../';
	
	// Include common.php
	include('inc/common.php');

// check validity of request
if ($_REQUEST['s'] === $SESSIONHASH) {

	require_once('inc/zip.class.php');
	
	$zipfile = new zipfile();
	$timestamp = date('Y-m-d-Hi');
	ini_set("memory_limit","600M"); 
	
	// paths and files to backup
	$paths = array($relative.'data', $relative.'theme'); //no trailing slash
	$files = array($relative.'.htaccess', $relative.'index.php', $relative.'gsconfig.php');	
	
	$zipfile->add_dir('getsimple');
	
	// cycle thru each path and file and add to zip file
	foreach ($paths as $path) {
		$dir_handle = @opendir($path) or die("Unable to open $path");
		ListDir($dir_handle,$path);
	}
	
	foreach ($files as $fl) 
	{
		$filedata = file_get_contents($fl);
		$zipfile->add_file($filedata, substr_replace($fl, 'getsimple', 0, 2));
	}
	
	// $listing is the list of all files and folders that were added to the backup
	//echo $listing;

	// create the final zip file
	$file = $relative. 'backups/zip/'. $timestamp .'_archive.zip';
	$fh = fopen($file, 'w') or die('Could not open file for writing!');	

	fwrite($fh, $zipfile->file()) or die('Could not write to file');
	fclose($fh);
	
	// redirect back to archive page with a success
	header('Location: archive.php?done');

} else {
	die('You do not have permission to execute this page');
}

exit;

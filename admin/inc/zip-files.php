<?php
/****************************************************
*
* @File: 		zip-files.php
* @Package:	GetSimple
* @Action:	Creates an archive for the website 	
*
*****************************************************/

/****************************************************
* @Function ListDir()
*****************************************************/
function ListDir($dir_handle,$path) {
	global $listing;
	global $zipfile;
	$listing .= "<ol>";
	while (false !== ($file = readdir($dir_handle))) {
	  $dir =$path.'/'.$file;
	  if(is_dir($dir) && $file != '.' && $file !='..' ) {
			$handle = @opendir($dir) or die("Unable to open file $file");
			$listing .= "<li>".$dir."</li>";
			$zipfile->add_dir($dir);
			ListDir($handle, $dir);
	  } elseif($file != '.' && $file !='..') {
			$listing .= "<li>".$dir."</li>";
			$filedata = file_get_contents($dir);
			$zipfile->add_file($filedata, $dir);
	  }
	}
	$listing .= "</ol>";
	closedir($dir_handle);
}
/***************************************************/

		//disable or enable error reporting
		if (file_exists('data/other/debug.xml')) 
		{
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_errors', 1);
		} 
		else 
		{
			error_reporting(0);
			@ini_set('display_errors', 0);
		}
		
		require_once('admin/inc/zip.class.php');
		
		$zipfile = new zipfile();
		$timestamp = date('Y-m-d-Hi');
		ini_set("memory_limit","600M"); 
		
		// paths and files to backup
		$paths = array("data","theme"); //no trailing slash
		$files = array(".htaccess", "index.php");	
		
		// cycle thru each path and file and add to zip file
		foreach ($paths as $path) 
		{
			$dir_handle = @opendir($path) or die("Unable to open $path");
			ListDir($dir_handle,$path);
		}
		
		foreach ($files as $fl) 
		{
			$filedata = file_get_contents($fl);
			$zipfile->add_file($filedata, $fl);
		}
		
		// $listing is the list of all files and folders that were added to the backup
		//echo $listing;

		// create the final zip file
		$file = 'backups/zip/'. $timestamp .'_archive.zip';
		$fh = fopen($file, 'w') or die('Could not open file for writing!');	
	
		fwrite($fh, $zipfile->file()) or die('Could not write to file');
		fclose($fh);
		
		// redirect back to archive page with a success
		header('Location: admin/archive.php?done');
?> 

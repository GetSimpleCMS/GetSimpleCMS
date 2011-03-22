<?php
/**
 * Zip Archive
 *
 * Creates a zip archive of the site
 *
 * @package GetSimple
 * @subpackage Backups
 */

	// Setup inclusions
	$load['plugin'] = true;
	

	// Include common.php
	include('inc/common.php');

// check validity of request
if ($_REQUEST['s'] === $SESSIONHASH) {
	
	
	# fix from hameau 
	//$timestamp = date('Y-m-d-Hi');
	$timestamp = gmdate('Y-m-d-Hi_s');
	
	
	ini_set("memory_limit","600M"); 

	$saved_zip_file = GSBACKUPSPATH.'zip/'. $timestamp .'_archive.zip';	
	
	$sourcePath = GSROOTPATH;
	if (!class_exists ( 'ZipArchive' , false)) {
		include('inc/ZipArchive.php');
	}
	if (class_exists ( 'ZipArchive' , false)) {
	
		$archiv = new ZipArchive();
		$archiv->open($saved_zip_file, ZipArchive::CREATE);
		$dirIter = new RecursiveDirectoryIterator($sourcePath);
		$iter = new RecursiveIteratorIterator($dirIter);
		
		foreach($iter as $element) {
		    /* @var $element SplFileInfo */
		    $dir = str_replace($sourcePath, '', $element->getPath()) . '/';
		    if ( strstr($dir, $GSADMIN.'/') || strstr($dir, 'backups/') ) {
		    	#don't archive these folders
		  	} else {
			    if ($element->isDir()) {
				     $archiv->addEmptyDir($dir);
			    } elseif ($element->isFile()) {
			        $file         = $element->getPath() .
			                        '/' . $element->getFilename();
			        $fileInArchiv = $dir . $element->getFilename();
			        // add file to archive 
			        $archiv->addFile($file, $fileInArchiv);
			    }
			  }
		}
		
		$archiv->addFile(GSROOTPATH.'.htaccess', '.htaccess' );
		$archiv->addFile(GSROOTPATH.'gsconfig.php', 'gsconfig.php' );
		
		// save and close 
		$status = $archiv->close();
		if (!$status) {
			redirect('archive.php?nozip');
		}
		
	} else {
		redirect('archive.php?nozip');	
	}
	// redirect back to archive page with a success
	redirect('archive.php?done');

} else {
	# page accessed directly - send back to archives page
	redirect('archive.php');
}

exit;

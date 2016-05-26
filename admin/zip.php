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
login_cookie_check();

// check validity of request
if ($_REQUEST['s'] === $SESSIONHASH) {
	
	$timestamp  = gmdate('Y-m-d-Hi_s');
	$zipcreated = true;
	
	set_time_limit (0);
	ini_set("memory_limit","800M"); 

	$saved_zip_file = GSBACKUPSPATH.'zip/'. $timestamp .'_archive.zip';	
	
	$sourcePath = str_replace('/', DIRECTORY_SEPARATOR, GSROOTPATH);
	if (!class_exists ( 'ZipArchive' , false)) {
		include('inc/ZipArchive.php'); // include zip archive shim
	}
	// attempt to use ziparchve class to create archive
	if (class_exists ( 'ZipArchive' , false)) {
		$archiv  = new ZipArchive();
		$archiv->open($saved_zip_file, ZipArchive::CREATE);
		$dirIter = new RecursiveDirectoryIterator($sourcePath);
		$iter = new RecursiveIteratorIterator($dirIter,
			         	RecursiveIteratorIterator::LEAVES_ONLY,
			        	RecursiveIteratorIterator::CATCH_GET_CHILD
			    	);
		
		foreach($iter as $element) {
		    /* @var $element SplFileInfo */
		    $dir = str_replace($sourcePath, '', $element->getPath()) . DIRECTORY_SEPARATOR;
		    if ( strstr($dir, $GSADMIN.DIRECTORY_SEPARATOR ) || strstr($dir, 'backups'.DIRECTORY_SEPARATOR )) {
  				#don't archive these folders admin, backups, ..
				} else if ($element->getFilename() != '..') { // FIX: if added to ignore parent directories
				  if ($element->isDir()) {
				     $archiv->addEmptyDir($dir);
			    } elseif ($element->isFile()) {
			        $file         = $element->getPath() .
			                        DIRECTORY_SEPARATOR  . $element->getFilename();
			        $fileInArchiv = $dir . $element->getFilename();
			        // add file to archive 
			        $archiv->addFile($file, $fileInArchiv);
			    }
			  }
		}

		// check if file exists, close will fail if bad file added, addfile always returns true
		if(file_exists(GSROOTPATH.'.htaccess'))    $archiv->addFile(GSROOTPATH.'.htaccess', '.htaccess' );
		if(file_exists(GSROOTPATH.'gsconfig.php')) $archiv->addFile(GSROOTPATH.'gsconfig.php', 'gsconfig.php' );
		
		// @todo testing custom extra files, will need a iter wrapper to get dirs, will add extra files to root
		if(getDef('GSBACKUPEXTRAS',true)){
			$extras = explode(',',getDef('GSBACKUPEXTRAS'));
			foreach($extras as $extra){		
				if(file_exists($extra)) $archiv->addFile($extra,basename($extra));
			}
		}

		// attempt to save and close
		$status = $archiv->close();
		if (!$status) {
			//ziparchive failed
			$zipcreated = false;
		}
		
	} else {
		// ziparchive non existant
		$zipcreated = false;	
	}
	if (!$zipcreated) {
		// fallback to exec tar -cvzf
		$zipcreated = archive_targz();
	}
	if (!$zipcreated) {
		// nothing worked, I give up
		redirect('archive.php?nozip');
	} 
	
	// @todo losing error handling and debugging here due to redirects
	// need some reporting to find old zip issues that are hard to reproduce

	// redirect back to archive page with a success
	redirect('archive.php?done');

} else {
	# page accessed directly - send back to archives page
	redirect('archive.php');
}

exit;

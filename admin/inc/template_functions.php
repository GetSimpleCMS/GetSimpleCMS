<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Template Functions
 *
 * These functions are used within the back-end of a GetSimple installation
 *
 * @package GetSimple
 * @subpackage Zip
 */


/**
 * Get Template
 *
 * @since 1.0
 *
 * @param string $name Name of template file to get
 * @param string $title Title to place on page
 * @return string
 */
function get_template($name, $title='** Change Me - Default Page Title **') {
	ob_start();
		$file = "template/" . $name . ".php";
		include($file);
		$template = ob_get_contents();
	ob_end_clean();
	echo $template;
}

/**
 * Filename ID
 *
 * Generates HTML code to place on the body tag of a page
 *
 * @since 1.0
 * @uses myself
 *
 * @return string
 */
function filename_id() {
	echo "id=\"". get_filename_id() ."\"";	
}

/**
 * Get Filename ID
 *
 * Returns the filename of the current file, minus .php
 *
 * @since 1.0
 * @uses myself
 *
 * @return string
 */
function get_filename_id() {
	$path = myself(FALSE);
	$file = basename($path,".php");	
	return $file;	
}


/**
 * Check Permissions
 *
 * Returns the CHMOD value of a particular file or path
 *
 * @since 2.0
 *
 * @param string $path File and/or path
 */
function check_perms($path) { 
  clearstatcache(); 
  if(!file_exists($path)) return false;  
  $configmod = substr(sprintf('%o', fileperms($path)), -4);  
	return $configmod;
} 


function ModeOctal2rwx($ModeOctal) { // enter octal mode, e.g. '644' or '2755'
    if ( ! preg_match("/[0-7]{3,4}/", $ModeOctal) )    // either 3 or 4 digits
        die("wrong octal mode in ModeOctal2rwx('<TT>$ModeOctal</TT>')");
	$Moctal = ((strlen($ModeOctal)==3)?"0":"").$ModeOctal;    // assume default 0
	$Mode3  = substr($Moctal,-3);    // trailing 3 digits, no sticky bits considered
	$RWX    = array ('---','--x','-w-','-wx','r--','r-x','rw-','rwx');    // dumb,huh?
	$Mrwx   = $RWX[$Mode3[0]].$RWX[$Mode3[1]].$RWX[$Mode3[2]];    // concatenate
    if (preg_match("/[1357]/", $Moctal[0])) $Mrwx[8] = ($Mrwx[8]=="-")?"T":"t";
    if (preg_match("/[2367]/", $Moctal[0])) $Mrwx[5] = ($Mrwx[5]=="-")?"S":"s";
    if (preg_match("/[4567]/", $Moctal[0])) $Mrwx[2] = ($Mrwx[2]=="-")?"S":"s";
    return $Mrwx;    // returns e.g. 'rw-r--r--' or 'rwxr-sr-x'
}

/**
 * Delete Zip File
 *
 * @since 1.0
 * @uses GSBACKUPSPATH
 *
 * @param string $id Zip filename to delete
 * @return string
 */
function delete_zip($id) { 
	$filepath = GSBACKUPSPATH . 'zip' . DIRECTORY_SEPARATOR;
	$file     = $filepath . $id;

	if(filepath_is_safe($file,$filepath)){
		return delete_file($file);
	}
} 

/**
 * Delete Uploaded File
 *
 * @since 1.0
 * @uses GSTHUMBNAILPATH
 * @uses GSDATAUPLOADPATH
 *
 * @param string $id Uploaded filename to delete
 * @param string $path Path to uploaded file folder
 * @return string
 */
function delete_upload($id, $path = "") { 
	$filepath = GSDATAUPLOADPATH . $path;
	$file     =  $filepath . $id;

	if(path_is_safe($filepath,GSDATAUPLOADPATH) && filepath_is_safe($file,$filepath)){
		$status = delete_file(GSDATAUPLOADPATH . $path . $id);
		if (file_exists(GSTHUMBNAILPATH.$path."thumbnail.". $id)) {
			delete_file(GSTHUMBNAILPATH.$path."thumbnail.". $id);
		}
		if (file_exists(GSTHUMBNAILPATH.$path."thumbsm.". $id)) {
			delete_file(GSTHUMBNAILPATH.$path."thumbsm.". $id);
		}
		return status;
	}	
} 

/**
 * Delete Upload Directory
 *
 * @since 1.0
 * @uses GSTHUMBNAILPATH
 * @uses GSDATAUPLOADPATH
 *
 * @param string $path relative path to uploaded file folder
 * @return string
 */
function delete_upload_dir($path){
	$target = GSDATAUPLOADPATH . $path;
	if (path_is_safe($target,GSDATAUPLOADPATH) && file_exists($target)) {
		$status = delete_folder($target);
		
		// delete thumbs folder
		if(file_exists(GSTHUMBNAILPATH . $path)) delete_dir(GSTHUMBNAILPATH . $path);
	
		return $status;
	}
}

/**
 * Delete Cache Files
 *
 * @since 3.1.3
 * @uses GSCACHEPATH
 *
 * @return mixed deleted count on success, null if there are any errors
 */
function delete_cache() { 
	$cachepath = GSCACHEPATH;
	
	$cnt     = 0;	
	$success = null;
	
	foreach(glob($cachepath.'*.txt') as $file){
		if(delete_file($file)) $cnt++;
		else $success = false;
	}	

	if($success === false) return null;
	return $cnt;
} 

/**
 * gets the backup filepath for a data file
 *
 * @since 3.4
 * @param  str $filepath filepath to get backup path
 * @return str           converted to backup filepath
 */
function getBackupFilePath($filepath){
	$pathparts = pathinfo($filepath);
	$filename  = $pathparts['filename'];
	$fileext   = $pathparts['extension'];
	$dirname   = $pathparts['dirname'];
	$bakpath   = getRelPath($dirname,GSDATAPATH);
	$bakfilepath = GSBACKUPSPATH.$bakpath.'/'. getBackupName($filename,$fileext);
	// debugLog(get_defined_vars());
	return $bakfilepath;
}

function getBackupName($filename, $fileext){
	return $filename . getDef('GSBAKFILEPREFIX') . '.' . $fileext . getDef('GSBAKFILESUFFIX');
}

function getPWDresetName($filename, $fileext){
	return $filename . getDef('GSRESETFILEPREFIX') . '.' . $fileext . getDef('GSRESETFILESUFFIX');
}


/**
 * Create Backup of a Data File
 * Copy file to backups, preserve paths structure
 * Only files in GSDATAPATH can be backed up!
 *
 * @since 3.4
 *
 * @param string $filepath filepath of datafile to backup
 * @return bool success
 */
function backup_datafile($filepath){
	if(!filepath_is_safe($filepath,GSDATAPATH)) return false;

	$bakfilepath = getBackupFilePath($filepath);
	$bakpath = dirname($bakfilepath);
 	// recusive create dirs
	create_dir($bakpath,getDef('GSCHMODDIR'),true);
	return copy_file($filepath,$bakfilepath);
}

/**
 * Restore Backup copy of a dataFile to where it belongs
 *
 * @since 3.4
 *
 * @param string $file filepath of data file to restore from backup, locked to GSDATAPATH
 * @param  bool $delete delete the backup
 * @return bool success
 */
function restore_datafile($filepath,$delete = true){
	if(!filepath_is_safe($filepath,GSDATAPATH)) return false;
	$bakfilepath = getBackupFilePath($filepath);

	// backup original before restoring
	if(file_exists($filepath)){
		rename_file($bakfilepath,$bakfilepath.'.tmp');
		move_file($filepath,$bakfilepath);
		$bakfilepath .= '.tmp';
	}

	if(!$delete) return copy_file($bakfilepath,$filepath);
	return move_file($bakfilepath,$filepath);
}

/**
 * Restore From Backup to custom destintation
 * source locked to GSBACKUPSPATH
 *
 * @since 3.4
 *
 * @param string $backfilepath filepath to backup file
 * @param string $destination  filepath retore to
 * @return bool success
 */
function restore_backup($bakfilepath,$destination){
	if(!filepath_is_safe($bakfilepath,GSBACKUPSPATH)) return false;
	return copy_file($bakfilepath,$destination);
}

/**
 * backup a page
 *
 * @since 3.4
 *
 * @param  str $id id of page to backup
 * @return bool     success
 */
function backup_page($id){
	backup_datafile(GSDATAPAGESPATH.$id.'.xml');
}

/**
 * backup a draft
 *
 * @since 3.4
 *
 * @param  str $id id of page to backup
 * @return bool     success
 */
function backup_draft($id){
	backup_datafile(GSDATADRAFTSPATH.$id.'.xml');
}

/**
 * Restore a page from backup
 *
 * @since 3.4
 *
 * @param  str $id id of page
 * @return bool     success
 */
function restore_page($id){
	restore_datafile(GSDATAPAGESPATH.$id.'.xml');
}

/**
 * Restore a page from backup
 *
 * @since 3.4
 *
 * @param  str $id id of page
 * @return bool     success
 */
function restore_draft($id){
	restore_datafile(GSDATADRAFTSPATH.$id.'.xml');
}

/**
 * Delete Pages File
 *
 * Deletes pages data file afer making backup
 *
 * @since 1.0
 * @uses GSBACKUPSPATH
 * @uses GSDATAPAGESPATH
 *
 * @param string $id File ID to delete
 * @param  bool $backup perform backup of file before deleting it
 */
function delete_page($id, $backup = true){
	if($backup) backup_datafile(GSDATAPAGESPATH.$id.'.xml');
	return delete_file(GSDATAPAGESPATH.$id.'.xml');
}

/**
 * Delete Pages Draft File
 *
 * Deletes pages draft data file afer making backup
 *
 * @since 3.4
 * @uses GSBACKUPSPATH
 * @uses GSDATADRAFTSPATH
 *
 * @param string $id File ID to delete
 * @param  bool $backup perform backup of file before deleting it
 */
function delete_draft($id, $backup = true){
	if($backup) backup_datafile(GSDATADRAFTSPATH.$id.'.xml');
	return delete_file(GSDATADRAFTSPATH.$id.'.xml');
}


/**
 * Clone a page
 * Automatically names page id to next incremental copy eg. "slug-n"
 * Clone title becomes "title [copy]""
 *
 * @param  str $id page id to clone
 * @return mixed   returns new url on succcess, bool false on failure
 */
function clone_page($id){
	list($cloneurl,$count) = getNextFileName(GSDATAPAGESPATH,$id.'.xml');
	// get page and resave with new slug and title
	$newxml = getPageXML($id);
	$newurl = getFileName($cloneurl);
	$newxml->url = getFileName($cloneurl);
	$newxml->title = $newxml->title.' ['.sprintf(i18n_r('COPY_N',i18n_r('COPY')),$count).']';
	$newxml->pubDate = date('r');
	$status = XMLsave($newxml, GSDATAPAGESPATH.$cloneurl);
	if($status) return $newurl;
	return false;
}

/**
 * get the next incremental filename
 *
 * @since 3.4
 * @param  str $path path to file
 * @param  str $file filename with extension
 * @return array     array('newfilename.ext',count)
 */
function getNextFileName($path,$file){
	$count = 1;
	$pathparts = pathinfo($path.$file);
	$filename  = $pathparts['filename'];
	$fileext   = '.'.$pathparts['extension'];

	$nextfilename =  $filename ."-".$count;
	$nextfile = $path.$filename . $fileext;

	if (file_exists($nextfile)) {
		while ( file_exists($nextfile) ) {
			$count++;
			$nextfilename = $filename .'-'. $count;
			$nextfile = $path . $nextfilename . $fileext;
		}
	}
	return array($nextfilename.$fileext,$count);
}

/**
 * Delete Pages Backup File
 *
 * @since 3.4
 *
 * @param string $id File ID to delete
 * @return bool success
 */
function delete_page_backup($id){
	$bakpagespath = GSBACKUPSPATH .getRelPath(GSDATAPAGESPATH,GSDATAPATH); // backups/pages/
	return delete_file($bakpagespath . getBackupName($id,'xml'));
}

/**
 * @deprecated 3.4 LEGACY
 */
function createBak($file, $filepath, $bakpath) {
	return backup_datafile($filepath . $file);
}
/**
 * @deprecated 3.4 LEGACY
 */
function delete_bak($id) { 
	return delete_page_backup($id);
}
/**
 * @deprecated 3.4 LEGACY
 */
function restore_bak($id) {
	restore_page($id);
}
/**
 * @deprecated 3.4 LEGACY
 */
function undo($file, $filepath, $bakpath) {
	return restore_datafile($filepath.$file);
}

/**
 * Delete Draft Backup File
 *
 * @since 3.4
 *
 * @param string $id File ID to delete
 * @return bool success
 */
function delete_draft_backup($id){
	$bakpagespath = GSBACKUPSPATH .getRelPath(GSDATADRAFTSPATH,GSDATAPATH); // backups/pages/
	return delete_file($bakpagespath. $id .".bak.xml");
}

/**
 * generate psuedo random password
 * excludes characters similar in appearance i,l,o,0,1
 * using mt_rand for strong can be improved
 *
 * @since  3.4
 * @param  integer $length      length of password
 * @param  string  $usecharsets string of charsets to include
 * @param  bool    $reuse       true, allow characters to be used more than once
 * @param  bool    $strong     true, use mt_rand instead of array_rand
 * @return str                  password
 */
function createRandomPassword($length = 8, $usecharsets = 'luds', $reuse = false, $strong = true)
{
	$allchars = array();
	$password = '';

	$charsets = array(
		'l' => 'abcdefghjkmnpqrstuvwxyz', // excluding i,l,o
		'u' => 'ABCDEFGHJKMNPQRSTUVWXYZ',
		'd' => '23456789', // excluding 0,1
		's' => '!@#$%&*?',
	);

	// combine charsets via usecharsets
	$sets = array_intersect_key($charsets,array_flip(str_split($usecharsets)));
	$numsets = count($sets);

	if($numsets < 1) die('no charsets specified');
	if($numsets > $length) die('length is to small');

	// prefill password with one from each set
	// also add set chars to all chars array
	foreach($sets as $key => $set)
	{
		$setary    = str_split($set);
		$setaryidx = $strong ? mt_rand(0, count($setary) - 1) : array_rand($setary);
		$password .= $setary[$setaryidx];

		if(!$reuse){
			unset($setary[$setaryidx]);
			$setary = array_values($setary); // reindex array
    }
		$allchars  = array_merge($allchars,$setary);
}

	// fill rest of password
	$numchars = count($allchars);
	for($i = 0; $i < $length - $numsets; $i++){
		$allcharsidx = $strong ? mt_rand(0, $numchars - 1) : array_rand($allchars);
		$password .= $allchars[$allcharsidx];

		if(!$reuse){
			unset($allchars[$allcharsidx]);
			$allchars = array_values($allchars);
			$numchars--;
		}
	}

	// shuffle for good measure
	$password = str_shuffle($password);

	if(strlen($password) < $length) die('an error occured');
	return $password;
}

/**
 * File Type Category
 *
 * Returns the category of an file based on its extension
 *
 * @since 1.0
 * @uses i18n_r
 *
 * @param string $ext
 * @return string
 */
function get_FileType($ext) {

	$ext = lowercase($ext);
	if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'pct' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png' ) {
		return i18n_r('IMAGES') .' Images';
	} elseif ( $ext == 'zip' || $ext == 'gz' || $ext == 'rar' || $ext == 'tar' || $ext == 'z' || $ext == '7z' || $ext == 'pkg' ) {
		return i18n_r('FTYPE_COMPRESSED');
	} elseif ( $ext == 'ai' || $ext == 'psd' || $ext == 'eps' || $ext == 'dwg' || $ext == 'tif' || $ext == 'tiff' || $ext == 'svg' ) {
		return i18n_r('FTYPE_VECTOR');
	} elseif ( $ext == 'swf' || $ext == 'fla' ) {
		return i18n_r('FTYPE_FLASH');	
	} elseif ( $ext == 'mov' || $ext == 'mpg' || $ext == 'avi' || $ext == 'mpeg' || $ext == 'rm' || $ext == 'wmv' ) {
		return i18n_r('FTYPE_VIDEO');
	} elseif ( $ext == 'mp3' || $ext == 'wav' || $ext == 'wma' || $ext == 'midi' || $ext == 'mid' || $ext == 'm3u' || $ext == 'ra' || $ext == 'aif' ) {
		return i18n_r('FTYPE_AUDIO');
	} elseif ( $ext == 'php' || $ext == 'phps' || $ext == 'asp' || $ext == 'xml' || $ext == 'js' || $ext == 'jsp' || $ext == 'sql' || $ext == 'css' || $ext == 'htm' || $ext == 'html' || $ext == 'xhtml' || $ext == 'shtml' ) {
		return i18n_r('FTYPE_WEB');
	} elseif ( $ext == 'mdb' || $ext == 'accdb' || $ext == 'pdf' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'csv' || $ext == 'tsv' || $ext == 'ppt' || $ext == 'pps' || $ext == 'pptx' || $ext == 'txt' || $ext == 'log' || $ext == 'dat' || $ext == 'text' || $ext == 'doc' || $ext == 'docx' || $ext == 'rtf' || $ext == 'wks' ) {
		return i18n_r('FTYPE_DOCUMENTS');
	} elseif ( $ext == 'exe' || $ext == 'msi' || $ext == 'bat' || $ext == 'download' || $ext == 'dll' || $ext == 'ini' || $ext == 'cab' || $ext == 'cfg' || $ext == 'reg' || $ext == 'cmd' || $ext == 'sys' ) {
		return i18n_r('FTYPE_SYSTEM');
	} else {
		return i18n_r('FTYPE_MISC');
	}
}

/**
 * ISO Timestamp
 * @todo  unused
 * @since 1.0
 *
 * @param string $dateTime
 * @return string
 */
function makeIso8601TimeStamp($dateTime) {
    if (!$dateTime) {
        $dateTime = date('Y-m-d H:i:s');
    }
    if (is_numeric(substr($dateTime, 11, 1))) {
        $isoTS = substr($dateTime, 0, 10) ."T".substr($dateTime, 11, 8) ."+00:00";
    } else {
        $isoTS = substr($dateTime, 0, 10);
    }
    return $isoTS;
}

/**
 * File Size
 *
 * @since 1.0
 *
 * @param string $s 
 * @return string
 */
function fSize($s) {
	$size = '<span>'. ceil(round(($s / 1024), 1)) .'</span> KB'; // in kb
	if ($s >= "1000000") {
		$size = '<span>'. round(($s / 1048576), 1) .'</span> MB'; // in mb
	}
	if ($s <= "999") {
		$size = '<span>&lt; 1</span> KB'; // in kb
	}
	
	return $size;
}

/**
 * Validate Email Address
 * @todo  remove fallbacks, 5.2 is min
 * @since 1.0
 *
 * @param string $email 
 * @return bool
 */
function check_email_address($email) {
    if (function_exists('filter_var')) {
    	// PHP 5.2 or higher
    	return (!filter_var((string)$email,FILTER_VALIDATE_EMAIL)) ? false: true;
    } else {
    	// old way
	    if (!preg_match("/[^@]{1,64}@[^@]{1,255}$/", $email)) {
	        return false;
	    }
	    $email_array = explode("@", $email);
	    $local_array = explode(".", $email_array[0]);
	    for ($i = 0; $i < sizeof($local_array); $i++) {
	        if (!preg_match("/(([A-Za-z0-9!#$%&'*+\/\=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/\=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
	            return false;
	        }
	    }
	    if (!preg_match("/\[?[0-9\.]+\]?$/", $email_array[1])) {
	        $domain_array = explode(".", $email_array[1]);
	        if (sizeof($domain_array) < 2) {
	            return false; // Not enough parts to domain
	        }
	        for ($i = 0; $i < sizeof($domain_array); $i++) {
	            if (!preg_match("/(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
	                return false;
	            }
	        }
	    }
	    return true;
	  }
}

/**
 * Do Regex
 *
 * @since 1.0
 *
 * @param string $text Text to perform regex on
 * @param string $regex Regex format to use
 * @return bool
 */
function do_reg($text, $regex) {
	if (preg_match($regex, $text)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Validate XML
 *
 * @since 3.3.0
 * @uses getXML
 *
 * @param string $file File to validate
 * @return bool
 */
function is_valid_xml($file) {
	$xmlv = getXML($file);
	if ($xmlv) return true;
}

/**
 * Generate Salt
 * @todo  cryptographically weak
 *
 * Returns a new unique salt
 * @return string
 */
function generate_salt() {
	return substr(sha1(mt_rand()),0,22);
}

/**
 * Get Admin Path
 *
 * Gets the path of the admin directory
 *
 * @since 1.0
 * @uses $GSADMIN
 * @uses GSROOTPATH
 * @uses tsl
 *
 * @return string
 */
function get_admin_path() {
	global $GSADMIN;
	return tsl(GSROOTPATH . $GSADMIN);
}

/**
 * Get Root Install Path
 *
 * Gets the path of the root installation directory
 *
 * @since 1.0
 *
 * @return string
 */
function get_root_path() {
	$pos  = strrpos(dirname(__FILE__),DIRECTORY_SEPARATOR.'inc');
	$adm  = substr(dirname(__FILE__), 0, $pos);
	$pos2 = strrpos($adm,DIRECTORY_SEPARATOR);
  	return tsl(substr(__FILE__, 0, $pos2));
}



/**
 * Check Current Menu
 *
 * Checks to see if a menu item matches the current page
 *
 * @since 1.0
 *
 * @param string $text
 * @return string
 */
function check_menu($text) {
	if(get_filename_id()===$text){
		echo 'class="current"';
	}
}

/**
 * Password Hashing
 *
 * Default function to create a hashed password for GetSimple
 *
 * @since 2.0
 * @uses GSLOGINSALT
 *
 * @param string $p 
 * @return string
 */
function passhash($p) {
	if(getDef('GSLOGINSALT') && getDef('GSLOGINSALT') != '') {
		$logsalt = sha1(getDef('GSLOGINSALT'));
	} else { 
		$logsalt = null; 
	}
	
	return sha1($p . $logsalt);
}

/**
 * Get Available Pages
 *
 * Lists all available pages for plugin/api use
 *
 * @since 2.0
 * @uses GSDATAPAGESPATH
 * @uses find_url
 * @uses getXML
 * @uses subval_sort
 *
 * @return array|string Type 'string' in this case will be XML 
 */
function get_available_pages() {
    $menu_extract = '';
    
	global $pagesArray;
    
    $pagesSorted = subval_sort($pagesArray,'title');
    if (count($pagesSorted) != 0) { 
		$count = 0;
		foreach ($pagesSorted as $page) {
			if ($page['private']!='Y'){
				$text       = (string)$page['menu'];
				$pri        = (string)$page['menuOrder'];
				$parent     = (string)$page['parent'];
				$title      = (string)$page['title'];
				$slug       = (string)$page['url'];
				$menuStatus = (string)$page['menuStatus'];
				$private    = (string)$page['private'];
				$pubDate    = (string)$page['pubDate'];
				$url        = find_url($slug,$parent);

			    $specific   = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);	        
			    $extract[]  = $specific;
			}
		} 
      return $extract;
    }
}

 
/**
 * Change all direct childens parents to new parent
 *
 * @since 3.4
 * @param str $parent parent slug to change
 * @param str $newparent new slug to change to
 */
function changeChildParents($parent, $newparent=null){
	global $pagesArray;
	getPagesXmlValues();
	foreach ($pagesArray as $page){
		if ( $page['parent'] == $parent ){
			$data = getPageXML($page['url']);
    		$data->parent=$newparent;
    		XMLsave($data, GSDATAPAGESPATH.$page['filename']);
		}
	}
}

// DEPRECATED
//  LEGACY, uses global url
function updateSlugs($existingUrl){
	GLOBAL $url;
	updateSlugsParents($existingUrl, $url);
}

/**
 * Get Link Menu Array
 * 
 * get an array of menu links sorted by heirarchy and indented
 * 
 * @uses $pagesSorted
 *
 * @since  3.3.0
 * @param string $parent
 * @param array $array
 * @param int $level
 * @return array menuitems title,url,parent
 */
function get_link_menu_array($parent='', $array=array(), $level=0) {
	
	global $pagesSorted;
	
	$items=array();
	// $pageList=array();

	foreach ($pagesSorted as $page) {
		if ($page['parent']==$parent){
			$items[(string)$page['url']]=$page;
		}	
	}	

	if (count($items)>0){
		foreach ($items as $page) {
		  	$dash="";
		  	if ($page['parent'] != '') {
	  			$page['parent'] = $page['parent']."/";
	  		}
			for ($i=0;$i<=$level-1;$i++){
				if ($i!=$level-1){
	  				$dash .= utf8_encode("\xA0\xA0"); // outer level
				} else {
					$dash .= '- '; // inner level
				}
			} 
			array_push($array, array( $dash . $page['title'], find_url($page['url'], $page['parent'])));
			// recurse submenus
			$array=get_link_menu_array((string)$page['url'], $array,$level+1);	 
		}
	}

	return $array;
}

/**
 * List Pages Json
 *
 * This is used by the CKEditor link-local plugin function: ckeditor_add_page_link()
 *
 * @author Joshas: mailto:joshas@gmail.com
 *
 * @since 3.0
 * @uses $pagesArray
 * @uses subval_sort
 * @uses GSDATAPAGESPATH
 * @uses getXML
 *
 * @returns array
 */
function list_pages_json(){	
	GLOBAL $pagesArray,$pagesSorted;

	$pagesArray_tmp = array();
	$count = 0;

	foreach ($pagesArray as $page) {
		if ($page['parent'] != '') { 
			$parentTitle = returnPageField($page['parent'], "title");
			$sort = $parentTitle .' '. $page['title'];		
		} else {
			$sort = $page['title'];
		}

		$page = array_merge($page, array('sort' => $sort));
		$pagesArray_tmp[$count] = $page;
		$count++;
	}

	$pagesSorted = subval_sort($pagesArray_tmp,'sort');

	$links = exec_filter('editorlinks',get_link_menu_array()); // @filter editorlinks (array) filter links array for ckeditor
	return json_encode($links);
}

/**
 * @deprecated since 3.3.0
 * moved to ckeditor config.js
 */
function ckeditor_add_page_link(){
	echo "
	<script type=\"text/javascript\">
	//<![CDATA[
	// ckeditor_add_page_link() DEPRECATED FUNCTION!
	//]]>
	</script>";
}

/**
 * get table row for pages display
 *
 * @since 3.4
 * @param  array $page   page array
 * @param  int $level    current level
 * @param  int $index    current index
 * @param  int $parent   parent index
 * @param  int $children number of children
 * @return str           html for table row
 */
function getPagesRow($page,$level,$index,$parent,$children){

	$indentation = $menu = '';

	// indentation
	$indent   = '<span class="tree-indent"></span>';
	$last     = '<span class="tree-indent indent-last">&ndash;</span>';
	$expander = '<span class="tree-expander tree-expander-expanded"></span>';

	// add indents based on level
	$indentation .= $level > 0 ? str_repeat($indent, $level-1) : '';
	$indentation .= $level > 0 ? $last : '';

	// add indents or expanders
	$isParent = $children > 0;
	$expander = $isParent ? $expander : '<span class="tree-indent"></span>';
	// $indentation = $indentation . $expander;

	// depth level identifiers
	$class  = 'depth-'.$level;
	$class .= $isParent ? ' tree-parent' : '';

	$menu .= '<tr id="tr-'.$page['url'] .'" class="'.$class.'" data-depth="'.$level.'">';

	// if ($page['parent'] != '') $page['parent'] = $page['parent']."/"; // why is this here ?
	if ($page['title'] == '' )      { $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; }
	if ($page['menuStatus'] != '' ) { $page['menuStatus'] = ' <span class="label label-ghost">'.i18n_r('MENUITEM_SUBTITLE').'</span>'; } else { $page['menuStatus'] = ''; }
	if ($page['private'] != '' )    { $page['private'] = ' <span class="label label-ghost">'.i18n_r('PRIVATE_SUBTITLE').'</span>'; } else { $page['private'] = ''; }
	if (pageHasDraft($page['url'])) { $page['draft']   = ' <span class="label label-ghost">'.lowercase(i18n_r('LABEL_DRAFT')).'</span>'; } else { $page['draft'] = ''; }
	if ($page['url'] == 'index' )   { $homepage = ' <span class="label label-ghost">'.i18n_r('HOMEPAGE_SUBTITLE').'</span>'; } else { $homepage = ''; }

	$pageTitle = cl($page['title']);

	$menu .= '<td class="pagetitle">'. $indentation .'<a title="'.i18n_r('EDITPAGE_TITLE').': '. var_out($page['title']) .'" href="edit.php?id='. $page['url'] .'" >'. $pageTitle .'</a>';
	$menu .= '<div class="showstatus toggle" >'. $homepage . $page['menuStatus'] . $page['private'] . $page['draft'] . '</div></td>'; // keywords used for filtering
	$menu .= '<td style="width:80px;text-align:right;" ><span>'. output_date($page['pubDate']) .'</span></td>';
	$menu .= '<td class="secondarylink" >';
	$menu .= '<a title="'.i18n_r('VIEWPAGE_TITLE').': '. var_out($page['title']) .'" target="_blank" href="'. find_url($page['url'],$page['parent']) .'">#</a>';
	$menu .= '</td>';

	if ($page['url'] != 'index' ) {
		$menu .= '<td class="delete" ><a class="delconfirm" href="deletefile.php?id='. $page['url'] .'&amp;nonce='.get_nonce("delete", "deletefile.php").'" title="'.i18n_r('DELETEPAGE_TITLE').': '. cl($page['title']) .'" >&times;</a></td>';
	} else {
		$menu .= '<td class="delete" ></td>';
	}

	$menu .= '</tr>';
	return $menu;
}

function getPagesRowMissing($ancestor,$level,$children){
	$menu = '<tr id="tr-'.$ancestor.'" class="tree-error tree-parent depth-'.$level.'" data-depth="'.$level.'"><td colspan="4" class="pagetitle"><a><strong>'. $ancestor.'</strong> '.i18n_r('MISSING_PARENT').'</a>';
	if ( fileHasBackup(GSDATAPAGESPATH.$ancestor.'.xml') ) {
		$menu.= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="backup-edit.php?p=view&amp;id='.$ancestor.'" target="_blank" >'.i18n_r('BACKUP_AVAILABLE').'</a>';
	}
	$menu.= "</td></tr>";
	return $menu;
}

/**
 * create a parent child bucket
 *
 * @since 3.4
 *
 * @param  array   $pages  pagesarray
 * @param  boolean $useref true: use references for values, false: empty
 * @return array   returns array keyed by parents, then keyed by url with values page refs or empty
 */
function getParentsHashTable($pages = array(), $useref = true){
	$pagesArray = $pages ? $pages : getPagesXmlValues();
	$ary        = array();

	foreach($pagesArray as $key => &$page){
		$parent = isset($page['parent']) ? $page['parent'] : '';
		$pageId = isset($page['url']) ? $page['url'] : null;
		if($pageId) $ary[$parent][$pageId] = $useref ? $page : '';
	}

	return $ary;
}

/**
 * gets a page array with heirachy data added to it
 *
 * @since 3.4
 * @param  array  $mypages pages array
 * @return array           pages array with order,depth,numchildren added
 */
function getPageDepths($mypages=array()){
	static $parents;     // parent lookup table
	static $pages;       // pagesarray
	static $newpages;    // new pagesarray
	static $keys;        // track processed pageIds

	static $parent = ''; // current parent being processed
	static $level  = 0;  // depth / indentation level
	static $iter   = 0;  // order / weight iteration counter

	$thisfunc = __FUNCTION__;

	if(!$keys)     $keys     = array();
	if(!$pages)    $pages    = $mypages;
	if(!$newpages) $newpages = array();
	if(!$parents)  $parents  = getParentsHashTable($pages); // use parent child lookup table for speed

	foreach ($parents[$parent] as $key => &$page) {
		$iter++;
		$keys[$key]  = '';

		// assert cyclical parent child
		if($page['parent'] == $page['url']) die("self parent ". $key);

		$pageId      = (string) $key;
		$numChildren = isset($parents[$pageId]) ? count($parents[$pageId]) : 0;

		$newpages[$pageId]                = $page;
		$newpages[$pageId]['order']       = $iter;
		$newpages[$pageId]['depth']       = $level;
		$newpages[$pageId]['numchildren'] = $numChildren;

		if(isset($parents[$pageId])){
			$level++;
			$parent = $pageId;
			$thisfunc();
			$level--;
		} else $parent ='';
	}

	// do missing ancestor checks, orphans are not previously processed since they have no root
	if($level == 0 and $parent==''){
		// debugLog('missing ancestor check');
		$level++;
		$ancestors = array_diff(array_keys($parents),array_keys($keys) );
		// debugLog($ancestors);

		foreach($ancestors as $key => $ancestor){
			if($ancestor !=='') {
				// check again to see if it was already removed from a previous loop
		 		if(!isset($keys[$ancestor])) {
		 			// Add this mising page to new array, then recurse on its children
		 			$iter++;
					$keys[$ancestor]  = '';

					$pageId      = $ancestor;
					$numChildren = isset($parents[$pageId]) ? count($parents[$pageId]) : 0;

					// this will cause issues if used for something else that tried to use a required field, since this will be missing all of them
					// @todo add a status flag instead of null ['url'] ?
					$newpages[$pageId]                = array(); 
					// $newpages[$pageId]['url']         = $ancestor;
					$newpages[$pageId]['order']       = $iter;
					$newpages[$pageId]['depth']       = $level-1;
					$newpages[$pageId]['numchildren'] = $numChildren;
					$parent = $ancestor;
		 			$thisfunc();
		 		}
		 	}
		}
	}

	return $newpages;
}

/**
 * Recursive list of pages
 *
 * Returns a recursive list of items for the main page
 *
 * @since 3.0
 * @uses $pagesSorted
 *
 * @param string $parent
 * @param string $menu
 * @param int $level
 *
 * @returns string
 */
function get_pages_menu($parent = '',$menu = '',$level = '') {
	global $pagesSorted;
	
	$pages = getPageDepths($pagesSorted); // use parent hash table for speed
	$depth = null;

	// get depth of requested parent, then get all subsequent children until we get back to our starting depth
	foreach($pages as $key => $page){

		// check for cyclical parent child and die
		if(isset($page['parent']) && $page['parent'] === $key) die("self parent > " . $key); 

		$level       = isset($page['depth']) ? $page['depth'] : 0;
		$numChildren = isset($page['numchildren']) ? $page['numchildren'] : 0;

		// if sublevel
		if($parent !== ''){
			// skip until we get to parent
			if($parent !== $key && $depth === null) continue;

			if($depth === null){
			 // set sub level starting depth
			 $depth = $page['depth']; continue;
			}
			else if(($page['depth'] == $depth)) return $menu; // we are back to starting depth so stop
			$level = $level - ($depth+1);
		}

		// provide special row if this is a missing parent
		if( !isset($page['url']) ) $menu .= getPagesRowMissing($key,$level,$numChildren); // use URL check for missing parents for now
		else $menu .= getPagesRow($page,$level,'','',$numChildren);
	}

	return $menu;
}

/**
 * Recursive list of pages for Dropdown menu
 *
 * Returns a recursive list of items for the main page
 *
 * @author Mike
 *
 * @since 3.0
 * @uses $pagesSorted
 *
 * @param string $parent
 * @param string $menu
 * @param int $level
 * 
 * @returns string
 */
function get_pages_menu_dropdown($parentitem, $menu,$level) {
	
	global $pagesSorted;
	global $parent; 
	
	$items=array();

	foreach ($pagesSorted as $page) {
		if ($page['parent']==$parentitem){
			$items[(string)$page['url']]=$page;
		}	
	}	

	if (count($items)>0){
		foreach ($items as $page) {
		  	$dash="";

		  	if ($page['parent'] != '') {
	  			$page['parent'] = $page['parent']."/";
	  		}

			for ($i=0;$i<=$level-1;$i++){
				if ($i!=$level-1){
	  				$dash .= '<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				} else {
					$dash .= '<span>&nbsp;&nbsp;&ndash;&nbsp;&nbsp;&nbsp;</span>';
				}
			} 

			if ($parent == (string)$page['url']) { $sel="selected"; } else { $sel=""; }

			$menu .= '<option '.$sel.' value="'.$page['url'] .'" >'.$dash.$page['url'].'</option>';
			$menu = get_pages_menu_dropdown((string)$page['url'], $menu,$level+1);	  	
		}
	}

	return $menu;
}

/**
 * Get API Details
 *
 * Returns the contents of an API url request
 *
 * This is needed because of the "XmlHttpRequest error: Origin null is not allowed by Access-Control-Allow-Origin"
 * error that javascript gets when trying to access outside domains sometimes. 
 *
 * @since 3.1
 * @uses GSADMININCPATH
 * @uses GSCACHEPATH
 *
 * @param string $type, default is 'core'
 * @param array $args, default is empty
 * 
 * @returns string
 */

function get_api_details($type='core', $args=null) {
	GLOBAL $debugApi,$nocache,$nocurl;

	include(GSADMININCPATH.'configuration.php');

	# core api details
	if ($type=='core') {
		# core version request, return status 0-outdated,1-current,2-bleedingedge
		$fetch_this_api = $api_url .'?v='.GSVERSION;
	}
	else if ($type=='plugin' && $args) {
		# plugin api details. requires a passed plugin i
		$apiurl = $site_link_back_url.'api/extend/?file=';
		$fetch_this_api = $apiurl.$args;
	}
	else if ($type=='custom' && $args) {
	# custom api details. requires a passed url
		$fetch_this_api = $args;
	} else return;
	
	// get_execution_time();
	debug_api_details("type: " . $type. " " .$args);
	debug_api_details("address: " . $fetch_this_api);

	# debug_api_details(debug_backtrace());

	if(!isset($api_timeout) or (int)$api_timeout<100) $api_timeout = 500; // default and clamp min to 100ms
	debug_api_details("timeout: " .$api_timeout);

	# check to see if cache is available for this
	$cachefile = md5($fetch_this_api).'.txt';
	$cacheExpire = 39600; // 11 minutes

	if(!$nocache) debug_api_details('cache check for ' . $fetch_this_api.' ' .$cachefile);
	else debug_api_details('cache check: disabled');

	$cacheAge = file_exists(GSCACHEPATH.$cachefile) ? filemtime(GSCACHEPATH.$cachefile) : '';

	if (!$nocache && !empty($cacheAge) && (time() - $cacheExpire) < $cacheAge ) {
		# grab the api request from the cache
		$data = read_file(GSCACHEPATH.$cachefile);
		debug_api_details('returning api cache ' . GSCACHEPATH.$cachefile);
	} else {	
		# make the api call
		if (function_exists('curl_init') && function_exists('curl_exec') && !$nocurl) {

			// USE CURL
			$ch = curl_init();

			if(!$ch){
				debug_api_details("curl init failed");
				return;
			}	

			// define missing curlopts php<5.2.3
			if(!defined('CURLOPT_CONNECTTIMEOUT_MS')) define('CURLOPT_CONNECTTIMEOUT_MS',156);
			if(!defined('CURLOPT_TIMEOUT_MS')) define('CURLOPT_TIMEOUT_MS',155);			
			
			// min cURL 7.16.2
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $api_timeout); // define the maximum amount of time that cURL can take to connect to the server 
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, $api_timeout);        // define the maximum amount of time cURL can execute for.
			curl_setopt($ch, CURLOPT_NOSIGNAL, 1);                     // prevents SIGALRM during dns allowing timeouts to work http://us2.php.net/manual/en/function.curl-setopt.php#104597
			curl_setopt($ch, CURLOPT_HEADER, false);                   // ensures header is not in output
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $fetch_this_api);

			if($debugApi){
				// $verbose = fopen(GSDATAOTHERPATH .'logs/curllog.txt', 'w+');			
				$verbose = tmpfile();				
				// curl_setopt($ch, CURLOPT_WRITEHEADER, $verbose );
				curl_setopt($ch, CURLOPT_HEADER, true); 
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				curl_setopt($ch, CURLOPT_STDERR, $verbose );
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);								
			}
				
			$data = curl_exec($ch);

			if($debugApi){
				debug_api_details("using curl");
				debug_api_details("curl version: ");
				debug_api_details(print_r(curl_version(),true));	
			
				debug_api_details("curl info:");
				debug_api_details(print_r(curl_getinfo($ch),true));
			
				if (!$data) {
					debug_api_details("curl error number:" .curl_errno($ch));
					debug_api_details("curl error:" . curl_error($ch));
				}

				debug_api_details("curl Verbose: ");
				debug_api_details(!rewind($verbose) . nl2br(htmlspecialchars(stream_get_contents($verbose))) );
				fclose($verbose);
				
				// output header and response then remove header from data
				$dataparts = explode("\r\n",$data);
				debug_api_details("curl Data: ");
				debug_api_details($data);
				$data = end($dataparts);

			}	

			curl_close($ch);

		} else if(ini_get('allow_url_fopen')) {  
			// USE FOPEN
			debug_api_details("using fopen");			
			$timeout = $api_timeout / 1000; // ms to float seconds
			// $context = stream_context_create();
			// stream_context_set_option ( $context, array('http' => array('timeout' => $timeout)) );
			$context = stream_context_create(array('http' => array('timeout' => $timeout))); 
			$data = read_file($fetch_this_api,false,$context);	
			debug_api_details("fopen data: " .$data);		
		} else {  
			debug_api_details("No api methods available");						
			return;
		}
	
		// debug_api_details("Duration: ".get_execution_time());	

		$response = json_decode($data);		
		debug_api_details('JSON:');
		debug_api_details(print_r($response,true),'');

		// if response is invalid set status to -1 error
		// and we pass on our own data, it is also cached to prevent constant rechecking

		if(!$response){
			$data = '{"status":-1}';
		}
		
		debug_api_details($data);
			save_file(GSCACHEPATH.$cachefile,$data);
			return $data;
		}
	return $data;
}

/**
 * [debug_api_details description]
 * @param  str $msg        msg to log
 * @param  string $prefix  prefix to log
 * @return str             log msg
 */
function debug_api_details($msg,$prefix = "API: "){
	GLOBAL $debugApi;
	if(!$debugApi && !getDef('GSDEBUGAPI',true)) return;
	debugLog($prefix.$msg);
}

/**
 * Get GetSimple Version
 *
 * Returns the version of this GetSimple installation
 *
 * @since 3.1
 * @uses GSADMININCPATH
 * @uses GSVERSION
 * 
 * @returns string
 */
function get_gs_version() {
	include(GSADMININCPATH.'configuration.php');
	return GSVERSION;
}


/**
 * Creates Sitemap
 *
 * Creates GSSITEMAPFILE (sitemap.xml) in the site's root.
 */
function generate_sitemap() {
	if(getDef('GSNOSITEMAP',true)) return;

	global $pagesArray;
	// Variable settings
	$SITEURL = getSiteURL(true);
	$path = GSDATAPAGESPATH;
	
	getPagesXmlValues(false);
	$pagesSorted = subval_sort($pagesArray,'menuStatus');
	
	if (count($pagesSorted) > 0)
	{ 
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
		$xml->addAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd', 'http://www.w3.org/2001/XMLSchema-instance');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		
		foreach ($pagesSorted as $page)
		{
			if ($page['url'] != '404')
			{		
				if ($page['private'] != 'Y')
				{
					// set <loc>
					$pageLoc = find_url($page['url'], $page['parent'],'full');
					// set <lastmod>
					$tmpDate = date("Y-m-d H:i:s", strtotime($page['pubDate']));
					$pageLastMod = makeIso8601TimeStamp($tmpDate);
					
					// set <changefreq>
					$pageChangeFreq = 'weekly';
					
					// set <priority>
					if ($page['menuStatus'] == 'Y') {
						$pagePriority = '1.0';
					} else {
						$pagePriority = '0.5';
					}
					
					//add to sitemap
					$url_item = $xml->addChild('url');
					$url_item->addChild('loc', $pageLoc);
					$url_item->addChild('lastmod', $pageLastMod);
					$url_item->addChild('changefreq', $pageChangeFreq);
					$url_item->addChild('priority', $pagePriority);
				}
			}
		}
		
		//create xml file
		$file = GSROOTPATH .GSSITEMAPFILE;
		$xml  = exec_filter('sitemap',$xml); // @filter sitemap (obj) filter the sitemap $xml obj

		$status = XMLsave($xml, $file);
		exec_action('sitemap-aftersave'); // @hook sitemap-aftersave after a sitemap data file was saved
		#sitemap successfully created
		return $status;
	}
	else return true;
}


/**
 * Creates tar.gz Archive 
 */
function archive_targz() {
	GLOBAL $GSADMIN;
	
	if(!function_exists('exec')) {
    	return false;
    	exit;
	}

	$timestamp           = gmdate('Y-m-d-Hi_s');
	$saved_zip_file_path = GSBACKUPSPATH.'zip/';
	$saved_zip_file      = $timestamp .'_archive.tar.gz';	
	$script_contents     = "tar -cvzf ".$saved_zip_file_path.$saved_zip_file." ".GSROOTPATH.".htaccess ".GSROOTPATH.GSCONFIGFILE." ".GSROOTPATH."data ".GSROOTPATH."plugins ".GSROOTPATH."theme ".GSROOTPATH.$GSADMIN."/lang > /dev/null 2>&1";

	debugLog('archive function exec called ' . __FUNCTION__);
	exec(escapeshellarg($script_contents), $output, $rc);
	
	if (file_exists($saved_zip_file_path.$saved_zip_file)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Check if a page is a public admin page
 * @return boolean true if page is non protected admin page
 */
function isAuthPage(){
	$page = get_filename_id(); 
	return $page == 'index' || $page == 'resetpassword';
}

/**
 * returns a query string with only the allowed keys
 * @since  3.3.0
 * 
 * @param  array $allowed array of querystring keys to keep
 * @return string built query string
 */
function filter_queryString($allowed = array()){
	parse_str($_SERVER['QUERY_STRING'], $query_string);
	$qstring_filtered = array_intersect_key($query_string, array_flip($allowed));
	$new_qstring      = http_build_query($qstring_filtered,'','&amp;');
	return $new_qstring;
}

/**
 * truncate a string, multibyte safe
 *
 * @since 3.4
 * @param  str $str      string to truncate
 * @param  int $numchars number of characters to return
 * @return str           truncated string
 */
function truncate($str,$numchars){
	return getExcerpt($str,$numchars,false,'',true,false);
}

/**
 * Get String Excerpt
 *
 * @since 3.3.2
 *
 * @uses strIsMultibyte
 * @uses cleanHtml
 * @uses preg_replace PCRE compiled with "--enable-unicode-properties"
 *
 * @param string $n Optional, default is 200.
 * @param bool $striphtml Optional, default true, true will strip html from $content
 * @param string $ellipsis
 * @param bool $break	break words, default: do not break words find whitespace and puntuation
 * @param bool $cleanhtml attempt to clean up html IF strip tags is false, default: true
 * @return string
 */
function getExcerpt($str, $len = 200, $striphtml = true, $ellipsis = '...', $break = false, $cleanhtml = true){
	$str = $striphtml ? trim(strip_tags($str)) : $str;
	$len = $len++; // zero index bump

	// setup multibyte function names
	$prefix = strIsMultibyte($str) ?  'mb_' : '';
	list($substr,$strlen,$strrpos) = array($prefix.'substr',$prefix.'strlen',$prefix.'strrpos');

	// string is shorter than truncate length, return
	if ($strlen($str) < $len) return $str;

	// if not break, find last word boundary before truncate to avoid splitting last word
	// solves for unicode whitespace and punctuation and a 1 character lookahead
	// hack,  replaces punc with space and handles it all the same for obtaining boundary index
	// REQUIRES that PCRE is compiled with "--enable-unicode-properties, @todo detect or supress ?
	if(!$break) $excerpt = preg_replace('/\n|\p{Z}|\p{P}+$/u',' ',$substr($str, 0, $len+1)); 

	$lastWordBoundaryIndex = !$break ? $strrpos($excerpt, ' ') : $len;
	$str = $substr($str, 0, $lastWordBoundaryIndex); 

	if(!$striphtml && $cleanhtml) return trim(cleanHtml($str)) . $ellipsis;
	return trim($str) . $ellipsis;	
}

/**
 * check if a string is multbyte
 * @since 3.3.2
 * 
 * @uses mb_check_encoding
 * 
 * @param  string $str string to check
 * @return bool      true if multibyte
 */
function strIsMultibyte($str){
	return function_exists('mb_check_encoding') && ! mb_check_encoding($str, 'ASCII') && mb_check_encoding($str, 'UTF-8');
}

/**
 * clean Html fragments by loading and saving from DOMDocument
 * Will only clean html body fragments,unexpected results with full html doc or containing <head> or <body>
 * it will also strip these in final result
 * 
 * @note supressing errors on libxml functions to prevent parse errors on non well-formed content
 * @since 3.3.2
 * @param  string $str string to clean up
 * @return string      return well formed html , with open tags being closed and incomplete open tags removed
 */
function cleanHtml($str){
	// setup encoding, required for proper dom loading
	$charsetstr = '<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$str;
	$dom_document = new DOMDocument();
	@$dom_document->loadHTML($charsetstr);
	// strip dom tags
	$html_fragment = preg_replace('/^<!DOCTYPE.+?>|<head.*?>(.*)?<\/head>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), @$dom_document->saveHTML()));	
	return $html_fragment;
}	


/**
 * get Page data for http response code
 * 
 * returns page xml for http response code, by checking for user page fallback to the core page
 * 
 * @since 3.4
 * @param  int $code http response code
 * @return obj       page xml
 */
function getHttpResponsePage($code){
	GLOBAL $pagesArray;

	if (isset($pagesArray[GSHTTPPREFIX . $code])) {
		// use user created http response page
		return getXml(GSDATAPAGESPATH . GSHTTPPREFIX . $code . '.xml');		
	} elseif (file_exists(GSDATAOTHERPATH . $code . '.xml'))	{
		// use default http response page
		return getXml(GSDATAOTHERPATH . $code . '.xml');	
	}	
}

/**
 * goto the default backend entrance page
 * determined from $_GET['redirect'] or GSDEFAULTPAGE
 * @todo  secure redirect better from injection
 */
function gotoDefaultPage(){
	if (isset($_GET['redirect'])) redirect(htmlentities($_GET['redirect']));
	else redirect(getDef('GSDEFAULTPAGE'));
}

/**
 * get the components xml data
 * returns an array of xmlobjs
 *
 * @since 3.4
 * 
 * @uses components
 * @uses GSDATAOTHERPATH
 * @uses getXML
 * @param  boolean $xml [description]
 * @return components data items xmlobj
 *
 */
function get_components_xml($refresh = false){
    global $components;
    if (!$components || $refresh) {
    	$components = get_collection_items(GSCOMPONENTSFILE);
    } 
    return $components;
}

/**
 * get xml for an individual component
 * returns an array since duplicates are possible on component slugs
 *
 * @since 3.4
 *
 * @param  str $id component id
 * @return array of simpleXmlObj matching slug
 */
function get_component_xml($id){
	return get_collection_item($id,get_components_xml());
}

/**
 * check if a component is enabled
 * @since  3.4
 * @param  str $id component id
 * @return bool     true if not disabled
 */
function componentIsEnabled($id){
	$item = get_component_xml($id);
	if(!$item) return false;
	return !(bool)(string) $item[0]->disabled;
}

/**
 * get the components xml data
 * returns an array of xmlobjs
 *
 * @since 3.4
 * 
 * @global snippets
 * @param  boolean $refresh refresh from file
 * @return components data items xmlobj
 *
 */
function get_snippets_xml($refresh = false){
    global $snippets;
    if (!$snippets || $refresh) {
    	$snippets = get_collection_items(GSSNIPPETSFILE);
    }
    return $snippets;
}

/**
 * get xml for an individual component
 * returns an array since duplicates are possible on component slugs
 *
 * @since 3.4
 *
 * @param  str $id component id
 * @return array of simpleXmlObj matching slug
 */
function get_snippet_xml($id){
	return get_collection_item($id,get_snippets_xml());
}

/**
 * check if a snippet is enabled
 * @since  3.4
 * @param  str $id snippet id
 * @return bool     true if not disabled
 */
function snippetIsEnabled($id){
	$item = get_snippet_xml($id);
	if(!$item) return false;
	return !(bool)(string) $item[0]->disabled;
}	


/**
 * get a collection of otherdata xml items
 * returns an array of xmlobjs
 *
 * @since 3.4
 * 
 * @uses GSDATAOTHERPATH
 * @uses getXML
 * @param  boolean $asset name of asset to get data form
 * @return components data items xmlobj
 *
 */
function get_collection_items($asset){	
	if (file_exists(GSDATAOTHERPATH.$asset)) {
		$data  = getXML(GSDATAOTHERPATH.$asset);
	    $items = $data->item;
	} else {
	    $items = array();
	}
    return $items;
}

/**
 * get xml for an individual otherdata item
 * returns an array since duplicates are possible on component slugs
 *
 * @since 3.4
 *
 * @param  str $id component id
 * @return array of simpleXmlObj matching slug
 */
function get_collection_item($id,$collection){
	// normalize id to match how we save it
	$id = to7bit($id, 'UTF-8');
	$id = clean_url($id);
	if(!$id) return;
	$item = $collection->xpath("//slug[.='".$id."']/..");
	
	// this returns an array due to no unique slug enforcement, so we grab first one atm
	// returning first one available
	return count($item) > 0 ? $item[0] : null;
}

/**
 * Output a collection item
 *
 * This will output the item requested. 
 * items are parsed for PHP within them if not $raw
 * Will only return the first component matching $id
 *
 * @since 3.4
 *
 * @param string $id This is the ID of the component you want to display
 * @param bool $force Force return of inactive components
 * @param bool $raw do not process php
 */
function output_collection_item($id, $collection, $force = false, $raw = false) {
	$item  = get_collection_item($id,$collection); 
	if(!$item) return;

	$disabled = (bool)(string)$item->disabled;
	if($disabled && !$force) return;

	if(!$raw) eval("?>" . strip_decode($item->value) . "<?php ");
	else echo strip_decode($item->value);
}

/**
 * @since  3.4
 * @deprecated
 */
function pingGoogleSitemaps(){
	return;
}

/**
 * Are we previewing a draft
 * @since  3.4
 * @return bool returns true if pre-viewing a draft
 */
function previewingDraft(){
	GLOBAL $id;
	return isset($id) && isset($_GET['draft']) && is_logged_in() && pageHasDraft($id);
}

/* ?> */

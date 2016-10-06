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


/**
 * converts octal modes to flags
 * 
 * @param string $ModeOctal octal string of permissions 3 or 4 digits 644 2755
 * @return string of moed flags  e.g. 'rw-r--r--' or 'rwxr-sr-x'
 */
function ModeOctal2rwx($ModeOctal) {
    if ( ! preg_match("/[0-7]{3,4}/", $ModeOctal) )
        die("wrong octal mode in ModeOctal2rwx('<TT>$ModeOctal</TT>')");
	$Moctal = ((strlen($ModeOctal)==3)?"0":"").$ModeOctal;    // assume default 0
	$Mode3  = substr($Moctal,-3);    // trailing 3 digits, no sticky bits considered
	$RWX    = array ('---','--x','-w-','-wx','r--','r-x','rw-','rwx');    // dumb,huh?
    $Mrwx = $RWX[$Mode3[0]].$RWX[$Mode3[1]].$RWX[$Mode3[2]];    // concatenate
    if (preg_match("/[1357]/", $Moctal[0])) $Mrwx[8] = ($Mrwx[8]=="-")?"T":"t";
    if (preg_match("/[2367]/", $Moctal[0])) $Mrwx[5] = ($Mrwx[5]=="-")?"S":"s";
    if (preg_match("/[4567]/", $Moctal[0])) $Mrwx[2] = ($Mrwx[2]=="-")?"S":"s";
    return $Mrwx;
}

/**
 * Delete Zip File
 *
 * @since 1.0
 * @uses GSBACKUPSPATH
 *
 * @param string $id Zip filename to delete
 * @return bool succces
 */
function delete_zip($id) { 
	$filepath = GSBACKUPSPATH . 'zip' . DIRECTORY_SEPARATOR;
	$file = $filepath . $id;

	if(filepath_is_safe($file,$filepath)){
		return delete_file($file);
	}
} 

/**
 * Delete Log File
 *
 * @since 3.4
 * @uses GSDATAOTHERPATH
 *
 * @param string $id log filename to delete
 * @return bool success
 */
function delete_logfile($id) { 
	$filepath = GSDATAOTHERPATH.'logs/';
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
 * @return bool success
 */
function delete_upload($id, $path = "") { 
	$filepath = GSDATAUPLOADPATH . $path;
	$file =  $filepath . $id;

	if(path_is_safe($filepath,GSDATAUPLOADPATH) && filepath_is_safe($file,$filepath)){
		$status = delete_file(GSDATAUPLOADPATH . $path . $id);
		if (file_exists(GSTHUMBNAILPATH.$path."thumbnail.". $id)) {
			delete_file(GSTHUMBNAILPATH.$path."thumbnail.". $id);
		}
		if (file_exists(GSTHUMBNAILPATH.$path."thumbsm.". $id)) {
			delete_file(GSTHUMBNAILPATH.$path."thumbsm.". $id);
		}
		return $status;
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
 * @return bool success
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
	
	$cnt = 0;	
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
	$bakfilepath = getBackupFilePath($filepath);
	
	if(!filepath_is_safe($bakfilepath,GSBACKUPSPATH)) return false;

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
	return backup_datafile(GSDATAPAGESPATH.$id.'.xml');
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
	return backup_datafile(GSDATADRAFTSPATH.$id.'.xml');
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
	return restore_datafile(GSDATAPAGESPATH.$id.'.xml');
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
	return restore_datafile(GSDATADRAFTSPATH.$id.'.xml');
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
	$filepath = GSDATAPAGESPATH;
	$file     = $filepath . $id . '.xml';

	if(filepath_is_safe($file,$filepath)){
		if($backup) backup_datafile($file);
		return delete_file($file);
	}
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
	$filepath = GSDATADRAFTSPATH;
	$file     = $filepath . $id . '.xml';

	if(filepath_is_safe($file,$filepath)){
		if($backup) backup_datafile($file);
		return delete_file($file);
	}
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
	$filepath = GSBACKUPSPATH .getRelPath(GSDATAPAGESPATH,GSDATAPATH); // backups/pages/						
	$file     = $filepath . getBackupName($id,'xml');

	if(filepath_is_safe($file,$filepath)){
		return delete_file($file);
	}
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
	$filepath = GSBACKUPSPATH .getRelPath(GSDATADRAFTSPATH,GSDATAPATH); // backups/pages/
	$file = $filepath . $bakpagespath. $id .".bak.xml";
	
	if(filepath_is_safe($file,$filepath)){
		return delete_file($file,$filepath);
	}	
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
	return i18n_r('FTYPE_'.get_FileTypeToken($ext));
}

function get_FileTypeToken($ext){
	if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'pct' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png' ) {
		return 'IMAGE';
	} elseif ( $ext == 'zip' || $ext == 'gz' || $ext == 'rar' || $ext == 'tar' || $ext == 'z' || $ext == '7z' || $ext == 'pkg' ) {
		return 'COMPRESSED';
	} elseif ( $ext == 'ai' || $ext == 'psd' || $ext == 'eps' || $ext == 'dwg' || $ext == 'tif' || $ext == 'tiff' || $ext == 'svg' ) {
		return 'VECTOR';
	} elseif ( $ext == 'swf' || $ext == 'fla' ) {
		return 'FLASH';	
	} elseif ( $ext == 'mov' || $ext == 'mpg' || $ext == 'avi' || $ext == 'mpeg' || $ext == 'rm' || $ext == 'wmv' || $ext == 'flv') {
		return 'VIDEO';
	} elseif ( $ext == 'mp3' || $ext == 'mp4' || $ext == 'wav' || $ext == 'wma' || $ext == 'midi' || $ext == 'mid' || $ext == 'm3u' || $ext == 'ra' || $ext == 'aif' ) {
		return 'AUDIO';
	} elseif ( $ext == 'xml' || $ext == 'css' || $ext == 'htm' || $ext == 'html' || $ext == 'xhtml' || $ext == 'shtml' ) {
		return 'WEB';
	} elseif ( $ext == 'phtml' || $ext == 'php' || $ext == 'php3' || $ext == 'php4' || $ext == 'php5' || $ext == 'phps' || $ext == 'asp' || $ext == 'js' || $ext == 'jsp' || $ext == 'sql') {
		return 'SCRIPT';
	} elseif ( $ext == 'mdb' || $ext == 'accdb' || $ext == 'pdf' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'csv' || $ext == 'tsv' || $ext == 'ppt' || $ext == 'pps' || $ext == 'pptx' || $ext == 'txt' || $ext == 'log' || $ext == 'dat' || $ext == 'text' || $ext == 'doc' || $ext == 'docx' || $ext == 'rtf' || $ext == 'wks' ) {
		return 'DOCUMENT';
	} elseif ( $ext == 'exe' || $ext == 'msi' || $ext == 'bat' || $ext == 'download' || $ext == 'dll' || $ext == 'ini' || $ext == 'cab' || $ext == 'cfg' || $ext == 'reg' || $ext == 'cmd' || $ext == 'sys' ) {
		return 'SYSTEM';
	} else {
		return 'MISC';
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
	if (gettype($xmlv) !== 'object' || !in_array(get_class($xmlv),array('SimpleXMLExtended','SimpleXML'))) {
		// debugLog($xmlv);
		return;
	}
	return true;
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
  $pos = strrpos(dirname(__FILE__),DIRECTORY_SEPARATOR.'inc');
  $adm = substr(dirname(__FILE__), 0, $pos);
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
	        $text = (string)$page['menu'];
	        $pri = (string)$page['menuOrder'];
	        $parent = (string)$page['parent'];
	        $title = (string)$page['title'];
	        $slug = (string)$page['url'];
	        $menuStatus = (string)$page['menuStatus'];
	        $private = (string)$page['private'];
					$pubDate = (string)$page['pubDate'];
	        $url = find_url($slug,$parent);
	        
	        $specific = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);
	        $extract[] = $specific;
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
	changeChildParents($existingUrl, $url);
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
	// pagesarray is sorted by file load, no specific or normalized sort order
	// pagesSorted attempts to sort by heirarchy parent children, in alphabetic order

	// @todo sort parent invalid filter
	$items = filterParent(getPages('sortParent'),$parent);

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

	// add indents based on level
	$indentation .= $level > 0 ? str_repeat($indent, $level-1) : '';
	$indentation .= $level > 0 ? $last : '';

	// add indents or expanders
	$isParent = $children > 0;
	// add expanders in php
	// $expander = '<span class="tree-expander tree-expander-expanded"></span>';
	// $expander = $isParent ? $expander : '<span class="tree-indent"></span>';
	// $indentation = $indentation . $expander;

	// depth level identifiers
	$class  = 'depth-'.$level;
	$class .= $isParent ? ' tree-parent' : '';

	$menu .= '<tr id="tr-'.$page['url'] .'" class="'.$class.'" data-depth="'.$level.'">';

	$pagetitle = $pagemenustatus = $pageprivate = $pagedraft = $pageindex = '';


	if ($page['title'] == '' )        { $pagetitle       = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>';} else { $pagetitle = $page['title']; }
	if ($page['menuStatus'] != '' )   { $pagemenustatus  = ' <span class="label label-ghost">'.i18n_r('MENUITEM_SUBTITLE').'</span>'; }
	if ($page['private'] != '' )      { $pageprivate     = ' <span class="label label-ghost">'.i18n_r('PRIVATE_SUBTITLE').'</span>'; } 
	if (getDef('GSUSEDRAFTS') && pageHasDraft($page['url']))   { $pagedraft       = ' <span class="label label-ghost">'.lowercase(i18n_r('LABEL_DRAFT')).'</span>'; }
	if ($page['url'] == getDef('GSINDEXSLUG'))     { $pageindex       = ' <span class="label label-ghost">'.i18n_r('HOMEPAGE_SUBTITLE').'</span>'; }
	if(dateIsToday($page['pubDate'])) { $pagepubdate     = ' <span class="datetoday">'. output_date($page['pubDate']) . '</span>';} else { $pagepubdate = '<span>'. output_date($page['pubDate']) . "</span>";}

	$menu .= '<td class="pagetitle break">'. $indentation .'<a title="'.i18n_r('EDITPAGE_TITLE').': '. var_out($pagetitle) .'" href="edit.php?id='. $page['url'] .'" >'. cl($pagetitle) .'</a>';
	$menu .= '<div class="showstatus toggle" >'. $pageindex .  $pagedraft . $pageprivate . $pagemenustatus .'</div></td>'; // keywords used for filtering
	$menu .= '<td style="width:80px;text-align:right;" ><span>'.$pagepubdate.'</span></td>';
	$menu .= '<td class="secondarylink" >';
	$menu .= '<a title="'.i18n_r('VIEWPAGE_TITLE').': '. var_out($pagetitle) .'" target="_blank" href="'. find_url($page['url'],$page['parent']) .'">#</a>';
	$menu .= '</td>';

	// add delete buttons, exclude index page
	if ($page['url'] != 'index' ) {
		$menu .= '<td class="delete" ><a class="delconfirm" href="deletefile.php?id='. $page['url'] .'&amp;nonce='.get_nonce("delete", "deletefile.php").'" title="'.i18n_r('DELETEPAGE_TITLE').': '. var_out($page['title']) .'" >&times;</a></td>';
	} else {
		$menu .= '<td class="delete" ></td>';
	}

	// add indexcolumn and tagcolumn for filtering
	$menu .= '<td class="indexColumn hidden">'.strip_tags(lowercase($pagetitle . $pageindex . $pagemenustatus . $pageprivate .$pagedraft)) .'</div></td>'; // keywords used for filtering
	$menu .= '<td class="tagColumn hidden">'.str_replace(',',' ',$page['meta']) . '</div></td>'; // keywords used for filtering
	
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
 * create a parent child bucket
 *
 * @since 3.4
 *
 * @param  array   $pages  pagesarray
 * @param  boolean $useref true: use references for values, false: empty
 * @return array   returns array keyed by parents, then keyed by url with values page refs or empty
 */
function getParentsSlugHashTable($pages = array(), $useref = true){
	$pagesArray = $pages ? $pages : getPagesXmlValues();
	$ary        = array();

	foreach($pagesArray as $key => &$page){
		$parent = isset($page['parent']) ? $page['parent'] : '';
		$pageId = isset($page['url']) ? $page['url'] : null;
		
		if(!empty($parent)){
			if (isset($ary[$parent])) $ary[$parent]['children'][] = $page['url'];
			else $ary[$parent] = array('id'=>$parent,'children'=>array($page['url']));
		} 
		// else $ary[] = array('id'=>$page['url']);
	}

	return $ary;
}

/**
 * gets a page array with heirachy data added to it
 * converts pages parent adjacancy list to flat table
 * id.[parent] -> rank[order],level[depth]
 *
 * @since 3.4
 * @param  array  $mypages pages array
 * @return array           pages array with order,depth,numchildren added
 */
function getPageDepths($pages=array(), $init = true){
	static $parents;     // parent lookup table
	// static $pages;       // pagesarray
	static $newpages;    // new pagesarray
	static $keys;        // track processed pageIds

	static $parent; // current parent being processed
	static $level;  // depth / indentation level
	static $iter;   // order / weight iteration counter

	$thisfunc = __FUNCTION__;

	if($init){
		$parent = ''; // current parent being processed
		$level  = 0;  // depth / indentation level
		$iter   = 0;  // order / weight iteration counter
	}

	if(!$keys || $init)     $keys     = array();
	// if(!$pages || $init)    $pages    = $mypages;
	if(!$newpages || $init) $newpages = array();
	if(!$parents  || $init)  $parents  = getParentsHashTable($pages); // use parent child lookup table for speed


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
			$thisfunc(array(),false);
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
					// @todo add full page template here , abstract page schema somewhere
					$newpages[$pageId]                = array(); 
					// $newpages[$pageId]['url']         = $ancestor;
					$newpages[$pageId]['order']       = $iter;
					$newpages[$pageId]['depth']       = $level-1;
					$newpages[$pageId]['numchildren'] = $numChildren;
					$parent = $ancestor;
					$thisfunc(array(),false);
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
function get_pages_menu_dropdown($parentitem, $menu, $level, $id = null, $idlevel = null) {
	
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

			if ($parent == (string)$page['url']){ $sel="selected"; } else { $sel=""; }
			
			// disable all children
			$disabled = '';
			if($id == $parentitem){
				$idlevel = $level;
			}
			if($idlevel && ($level >= $idlevel)) $disabled = 'disabled';
			if($idlevel && ($level < $idlevel)){
				$disabled = '';
				$idlevel = null;
			}

			$menu .= '<option '.$sel.' value="'.$page['url'] .'" '.$disabled.'>'.$dash.$page['url'].'</option>';
			$menu = get_pages_menu_dropdown((string)$page['url'], $menu,$level+1, $id, $idlevel);	  	
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
 * @param  bool $cached force cached check only, do not use curl
 * 
 * @returns string
 */

function get_api_details($type='core', $args=null, $cached = false) {
	GLOBAL $debugApi,$nocache,$nocurl;

	include(GSADMININCPATH.'configuration.php');

	if($cached){
		debug_api_details("API REQEUSTS DISABLED, using cache files only");
	}

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

	if(!$nocache || $cached) debug_api_details('cache file check - ' . $fetch_this_api.' ' .$cachefile);
	else debug_api_details('cache check: disabled');

	$cacheAge = file_exists(GSCACHEPATH.$cachefile) ? filemtime(GSCACHEPATH.$cachefile) : '';
	debug_api_details('cache age: ' . output_datetime($cacheAge));


	// api disabled and no cache file exists
	if($cached && empty($cacheAge)){
		debug_api_details('cache file does not exist - ' . GSCACHEPATH.$cachefile);
		debug_api_details();
		return '{"status":-1}';
	}

	if (!$nocache && !empty($cacheAge) && (time() - $cacheExpire) < $cacheAge ) {
		debug_api_details('cache file time - ' . $cacheAge . ' (' . (time() - $cacheAge) . ')' );
		# grab the api request from the cache
		$data = read_file(GSCACHEPATH.$cachefile);
		debug_api_details('returning cache file - ' . GSCACHEPATH.$cachefile);
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
			curl_setopt($ch, CURLOPT_TIMEOUT_MS, $api_timeout); // define the maximum amount of time cURL can execute for.
			curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // prevents SIGALRM during dns allowing timeouts to work http://us2.php.net/manual/en/function.curl-setopt.php#104597
			curl_setopt($ch, CURLOPT_HEADER, false); // ensures header is not in output
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
			debug_api_details();						
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
		debug_api_details();		
			return $data;
		}	
	debug_api_details();	
	return $data;
}

/**
 * [debug_api_details description]
 * @param  str $msg        msg to log
 * @param  string $prefix  prefix to log
 * @return str             log msg
 */
function debug_api_details($msg = null ,$prefix = "API: "){
	GLOBAL $debugApi;
	if(!$debugApi && !getDef('GSDEBUGAPI',true)) return;
	if(!isset($msg)) $msg = str_repeat('-',80);
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
			if ($page['url'] == '404') continue;  // exclude 404 page
			if ($page['url'] == '403') continue;  // exclude 403 page
			if ($page['private'] == 'Y') continue; // exclude private
			if (isset($page['metarNoIndex']) && $page['metarNoIndex'] == '1') continue; // exclude noindex

			// set <loc>
			$pageLoc = find_url($page['url'], $page['parent'],'full');
			// set <lastmod>
			$tmpDate = date("Y-m-d H:i:s", strtotime($page['pubDate']));
			$pageLastMod = makeIso8601TimeStamp($tmpDate);
			
			// set <changefreq>
			$pageChangeFreq = 'weekly'; // change freq
			
			// set <priority>
			// @todo withc multi menu support, which menu ? any ? add supporting functions
			if ($page['menuStatus'] == 'Y') {
				$pagePriority = '1.0'; // in menu priority
			} else {
				$pagePriority = '0.5'; // not in menu priority
			}
			
			//add to sitemap
			$url_item = $xml->addChild('url');
			$url_item->addChild('loc', $pageLoc);
			$url_item->addChild('lastmod', $pageLastMod);
			$url_item->addChild('changefreq', $pageChangeFreq);
			$url_item->addChild('priority', $pagePriority);
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

	$timestamp = gmdate('Y-m-d-Hi_s');
	$saved_zip_file_path = GSBACKUPSPATH.'zip/';
	$saved_zip_file = $timestamp .'_archive.tar.gz';	
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
	$new_qstring = http_build_query($qstring_filtered,'','&amp;');
	return $new_qstring;
}

/**
 * returns a query string with only the allowed keys
 * @since  3.4.0
 * 
 * @param  array $merge array of querystring keys to add or modify
 * @return string built query string
 */
function merge_queryString($merge = array()){
	parse_str($_SERVER['QUERY_STRING'], $query_string);
	$query_string = array_merge($query_string,$merge);
	$new_qstring = http_build_query($query_string,'','&amp;');
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
	// solves for unicode whitespace \p{Z} and punctuation \p{P} and a 1 character lookahead hack,
	// replaces punc with space so it handles the same for obtaining word boundary index
	// REQUIRES that PCRE is compiled with "--enable-unicode-properties, 
	// @todo detect or supress requirement, perhaps defined('PREG_BAD_UTF8_OFFSET_ERROR'), translit puntuation only might be an alternative
	// debugLog(defined('PREG_BAD_UTF8_OFFSET_ERROR'));
	if(!$break) $excerpt = preg_replace('/\n|\p{Z}|\p{P}+$/u',' ',$substr($str, 0, $len+1)); 

	$lastWordBoundaryIndex = !$break ? $strrpos($excerpt, ' ') : $len;
	$str = $substr($str, 0, $lastWordBoundaryIndex); 

	if(!$striphtml && $cleanhtml) return trim(cleanHtml($str)) . $ellipsis;
	return trim($str) . $ellipsis;	
}

/*
 * wrapper for getExcerpt for specific page
 * strip is performed but no filters are executed
 */
function getPageExcerpt($pageid,$len = 200, $striphtml = true, $ellipsis = '...', $break = false, $cleanhtml = true){
	$content = returnPageContent($pageid);
	if(getDef('GSCONTENTSTRIP',true)) $content = strip_content($content);	
	return getExcerpt($content,$len,$striphtml,$ellipsis,$break,$cleanhtml);
}

/**
 * PRCE compiled test
 * test if PCRE is compiled with UTF-8 and unicode property support
 */
function PCRETest(){
	if ( ! @preg_match('/^.$/u', 'ñ')) return false; // UTF-8 support
	if ( ! @preg_match('/^\pL$/u', 'ñ')) return false; // Unicode property support (enable-unicode-properties)
	return true;
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
 * @param  array $strip_tags optional elements to remove eg. array('style')
 * @return string      return well formed html , with open tags being closed and incomplete open tags removed
 */
function cleanHtml($str,$strip_tags = array()){
	// setup encoding, required for proper dom loading
	// @note
	// $dom_document = new DOMDocument('1.0', 'utf-8'); // this does not deal with transcoding issues, loadhtml will treat string as ISO-8859-1 unless the doc specifies it 
	// $dom_document->loadHTML(mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8')); // aternate option that might work...
	
	$dom_document = new DOMDocument();
	$charsetstr = '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
	@$dom_document->loadHTML($charsetstr.$str);
	
	foreach($strip_tags as $tag){
    	$elem = $dom_document->getElementsByTagName($tag);
    	while ( ($node = $elem->item(0)) ) {
        	$node->parentNode->removeChild($node);
	    }
	}

	// strip dom tags that we added, and ones that savehtml adds
	// strip doctype, head, html, body tags
	$html_fragment = preg_replace('/^<!DOCTYPE.+?>|<head.*?>(.*)?<\/head>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), @$dom_document->saveHTML()));	
	return $html_fragment;
}	

// @todo: now that I have some structure, i can probably reduce this into some array_filter functions, depending on speed these might be easier and faster to use.
// @todo: replace function checks with callable checks
// but it still requires a class or __invoke to pass arguments into the callback

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


/**
 * get htmleditor attributes for textareas
 * @param  str $class extra classes to add to element
 * @return str        html fragment
 */
function getHtmlEditorAttr($class){
	if(getDef('GSHTMLEDITINLINE',true)) $class .= ' inline';
 	return ' data-htmleditautoheight="'.(getDef('GSHTMLEDITAUTOHEIGHT',true) ? 'true' : 'false').'" 
 	 data-htmleditcompact="'.(getDef('GSHTMLEDITCOMPACT',true) ? 'true' : 'false').'" 
 	 data-htmleditinline="'.(getDef('GSHTMLEDITINLINE',true) ? 'true' : 'false') .'" 
 	 class="html_edit '.$class.'"
 	 data-mode="html" ';	
}

/**
 * get codeeditor attributes for textareas
 * @param  str $class extra classes to add to element
 * @return str        html fragment
 */
function getCodeEditorAttr($class){
	return ' data-codeeditautoheight="'.(getDef('GSCODEEDITAUTOHEIGHT',true) ? 'true' : 'false').'" 
	 data-codeeditcompact="'.(getDef('GSCODEEDITCOMPACT',true) ? 'true' : 'false').'" 
	 class="code_edit '.$class.'"
	 data-mode="php" ';
}

/**
 * get htmlEditor attributes for content textareas
 * @param  str $class extra classes to add to element
 * @return str        html fragment
 */
function getDefaultHtmlEditorAttr($class){
	return ' class="html_edit '.$class.'"';
}

/**
 * get codeEditor attributes for content textareas
 * @param  str $class extra classes to add to element
 * @return str        html fragment
 */
function getDefaultCodeEditorAttr($class){
	return ' class="code_edit '.$class.'"';
}

/**
 * get editor attributes for textareas
 * If func name not provided , we will attempt to get a function name from 'GS'.uppercase($collectionid).'ATTRIB'
 * eg. GSSNIPPETSATTRIB which it will execute and use for inserting into the textarea
 * @param  str $collectionid id for this kind of editor
 * @param  string $class        extra classes
 * @param  str $funcname     function name to call to get attributes
 * @return str               html fragment
 */
function getEditorAttribCallout($collectionid,$class = '',$funcname = null){
	if(!$funcname) $call = getDef('GS'.uppercase($collectionid).'ATTRIB');
	else $call = $funcname;
	if(function_exists($call)) return $call($class);
}

function getCollectionItemOutput($collectionid,$id,$item,$class = 'item_edit',$code = ''){

	$disabled = (bool)(string)$item->disabled;
	$readonly = (bool)(string)$item->readonly;

	$str = '';
	$str .= '<div class="compdiv codewrap" id="section-'.$id.'">';
	$str .= '<table class="comptable" ><tr>';
	$str .= '<td><b title="'.i18n_r('DOUBLE_CLICK_EDIT').'" class="comptitle editable">'. stripslashes($item->title) .'</b></td>';
	
	if(getDef('GSSHOWCODEHINTS',true) && !empty($code))
		$str .= '<td style="text-align:right;" ><code>&lt;?php '.$code.'(<span class="compslugcode">\''.$item->slug.'\'</span>); ?&gt;</code></td>';
	
	$str .= '<td class="compactive"><label class="" for="active[]" >'.i18n_r('ACTIVE').'</label>';
	$str .= '<input type="checkbox" class="compactive" name="component['.$id.'][active]" '. (!$disabled ? 'checked="checked"' : '') .' value="'.$id.'" /></td>';
	$str .= '<td class="delete" ><a href="javascript:void(0)" title="'.i18n_r('DELETE').' '. cl($item->title).'?" class="delcomponent" rel="'.$id.'" >&times;</a></td>';
	$str .= '</tr></table>';
	
	$str .= '<textarea id="editor_'.$id.'" name="component['.$id.'][val]"'.getEditorAttribCallout($collectionid,$class).'>'. stripslashes($item->value) .'</textarea>';
	$str .= '<input type="hidden" class="compslug" name="component['.$id.'][slug]" value="'. $item->slug .'" />';
	$str .= '<input type="hidden" class="comptitle" name="component['.$id.'][title]" value="'. stripslashes($item->title) .'" />';
	$str .= '<input type="hidden" class="compid" name="component['.$id.'][id]" value="'. $id .'" />';
	$str .= '</div>';
	return $str;
}

function getItemTemplate($collectionid,$class = 'item_edit noeditor',$code = ''){
	$item = array(
		'title'    => '',
		'slug'     => '',
		'value'    => '',
		'disabled' => '',
		'readonly' => ''
	);

	return getCollectionItemOutput($collectionid,'',(object)$item,$class,$code);
}

function outputCollection($collectionid,$data,$class='item_edit',$code = ''){
	if(!$data) return;
	$id = 0;
	if (count($data) != 0) {
		foreach ($data as $item) {
			$table = getCollectionItemOutput($collectionid,$id,$item,$class,$code);
			exec_action($collectionid.'-extras'); // @hook collectionid-extras called after each component html is added to $table
			echo $table; // $table is legacy for hooks that modify the var, they should now just output html directly
			$id++;
		}
	}
}

function outputCollectionTags($collectionid,$data){
	if(!$data) return;
	$numcomponents = count($data);

	echo '<div class="compdivlist">';

	# create list to show on sidebar for easy access
	$class = $numcomponents < 15 ? ' clear-left' : '';
	if($numcomponents > 1) {
		$id = 0;
		foreach($data as $item) {
			echo '<a id="divlist-' . $id . '" href="#section-' . $id . '" class="component'.$class.' comp_'.$item->title.'">' . $item->title . '</a>';
			$id++;
		}
	}

	exec_action($collectionid.'-list-extras'); // @hook collectionid-list-extras called after component sidebar list items (tags) 		
	echo '</div>';
}


/**
 * getCollectionItemSlug
 * get collection item slug, clean with default fallback
 * @since  3.4
 * @param  string $slug    slug
 * @param  string $default fallback slug
 * @return string          clean slug
 */
function getCollectionItemSlug($slug,$title = 'unknown'){
	$slug = trim($slug);
	if ( $slug == null || empty($slug)){
		if (!empty($title)){
			if(trim($title) == '') return;
		}
		$slug = $title;
	}
	$slug = prepareSlug($slug,$title);
	if(empty($slug)) return; // errormode return null
	return $slug;
}

function addComponentItem($xml,$title,$value,$active,$slug = null){
		$slug = getCollectionItemSlug($slug,$title);
		if($slug == null) return; // errormode return null

		$title    = safe_slash_html($title);
		$value    = safe_slash_html($value);
		$disabled = $active;
	
		if(!is_object($xml)) $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');

		# create the body of components.xml file
		$component = $xml->addChild('item');
		$c_note     = $component->addChild('title');
		$c_note->addCData($title);
		$component->addChild('slug', $slug);
		$c_note     = $component->addChild('value');
		$c_note->addCData($value);
		$c_note     = $component->addChild('disabled');
		$c_note->addCData($disabled);
	// debugLog(var_dump($component->asXML()));
	return $xml;
}

/**
 * date is today
 * @since 3.4
 * @param  int $timestamp timestamp
 * @return bool            true if timestamp is today
 */
function dateIsToday($timestamp){
	return date('Ymd') == date('Ymd', strtotime($timestamp));
}

/**
 * returns icon classes for file extensions
 * follow font-awesome naming, can be used for other stuff however
 * uses get_fileTypeToken to get generic categories ( same as filter ), then further refines icons we have
 * 
 * @param  str $filename name of file
 * @param  string $default  default to use when no match found
 * @return str           the class
 */
function getFileIconClass($filename = '',$default = 'file'){

	$ext = $token = '';
	if($filename !== ''){
		$ext   = getFileExtension($filename);
		$token = get_FileTypeToken($ext);
	}

	// generic file icons
	$tokens = array(
		'IMAGE'      => 'file-image',
		'COMPRESSED' => 'file-archive',
		'VECTOR'     => 'file-image',
		'FLASH'      => 'file-image',
		'VIDEO'      => 'file-video',
		'AUDIO'      => 'file-audio',
		'WEB'        => 'file',
		'SCRIPT'     => 'file-code',
		'DOCUMENT'   => 'file-text',
		'SYSTEM'     => 'file',
		'MISC'       => 'file'
	);

	// specific file icons
	$iconClasses = array(
		'pdf'    => 'file-pdf',
		'xls'    => 'file-excel',
		'xlsx'   => 'file-excel',
		'doc'    => 'file-word',
		'docx'   => 'file-word',
		'ppt'    => 'file-powerpoint'
	);

	$iconclass = $default;
	if(isset($tokens[$token]))    $iconclass = $tokens[$token];
	if(isset($iconClasses[$ext])) $iconclass = $iconClasses[$ext]; // override specific
	return $iconclass;
}

/**
 * get the filepath for a thumbnail
 * @param  str $file        filename of the thumbnail
 * @param  string $upload_path upload path
 * @param  string $type     the thumbnail type id
 * @return str              file path to the thumbnail file
 */
function getThumbnailFile($file, $upload_path = '',$type = 'thumbnail'){
	return 	GSTHUMBNAILPATH.tsl($upload_path).(!empty($type) ? '.' : '').$file;
}

/**
 * get the url for a thumbnail
 * @param  str $file        filename of the thumbnail
 * @param  string $upload_path upload path
 * @param  string $type        the thumbnail type id
 * @return str              url to the thumbnail asset
 */
function getThumbnailURI($file, $upload_path = '',$type = 'thumbnail'){
	GLOBAL $SITEURL;
	return tsl($SITEURL).getRelPath(GSTHUMBNAILPATH).tsl($upload_path).(!empty($type) ? '.' : '').$file;
}

/**
 * get the url for an upload file
 * @param  str $file        filename
 * @param  string $upload_path uploads path
 * @return str              url for this upload file asset
 */
function getUploadURI($file, $upload_path = ''){
	GLOBAL $SITEURL;
	return tsl($SITEURL).getRelPath(GSDATAUPLOADPATH).tsl($upload_path).$file;
}

/**
 * get array of thumbnails and info
 * @param  string  $upload_path the upload sub path
 * @param  string  $type        optional thumbnail type eg thumbsm, thumbnail to filter by
 * @param  string  $filename    optional filename to filter
 * @param  boolean $recurse     optional true: recurse into subdirectories
 * @return array                assoc array with thumbnail attributes
 */
function getThumbnails($upload_path = '', $type = '', $filename = '', $recurse = false){
	$thumbs_array = array();
	$files = directoryToArray(GSTHUMBNAILPATH.tsl($upload_path),$recurse);
	foreach($files as $file){
		$split     = strpos(basename($file),'.');
		$thumbtype = substr(basename($file),0,$split);
		$origfile  = substr(basename($file),$split+1);

		if(!empty($filename) && $filename !== $origfile) continue;

		if(empty($thumbtype) || (!empty($type) && $type !==  $thumbtype)){
			continue;
		}

		// debugLog('thumbnail ' . $file);
		$thumb = getimagesize($file);
		$thumb['width']       = $thumb[0]; unset($thumb[0]); 
		$thumb['height']      = $thumb[1]; unset($thumb[1]);
		$thumb['type']        = $thumb[2]; unset($thumb[2]);
		$thumb['attrib']      = $thumb[3]; unset($thumb[3]);

		$thumb['uploadpath']  = tsl(getRelPath($upload_path,GSTHUMBNAILPATH));
		$thumb['primaryfile'] = GSDATAUPLOADPATH . $thumb['uploadpath'] . $origfile;
		$thumb['filesize']    = filesize($file);
		$thumb['primaryurl']  = getUploadURI($origfile,$thumb['uploadpath']);
		$thumb['thumbfile']   = getThumbnailFile(basename($file),$upload_path,'');
		$thumb['thumburl']    = getThumbnailURI(basename($file),$upload_path,'');
		$thumb['thumbtype']   = $thumbtype;
		
		$thumbs_array[$upload_path.basename($file)] = $thumb;
	}
	return $thumbs_array;
}


/**
 * Generate standard thumbnails
 * @param  string $path path to image
 * @param  string $name file name
 * @uses   GD
 */

function genStdThumb($subpath,$file){
	// set thumbnail width from GSIMAGEWIDTH
	if (!getDef('GSIMAGEWIDTH')) {
		$width = 200; //New width of image  	
	} else {
		$width = getDef('GSIMAGEWIDTH');
	}

	generate_thumbnail($file,$subpath, 'thumbnail.'.$file, $width);
}

/**
 * generate a thumbnail
 * @param  str  $sub_path upload path
 * @param  str  $file     filename
 * @param  str  $out_file outfile name, can be null if show is true
 * @param  int  $w        desired width
 * @param  int  $h        desired max height, optional, will limit height and adjust width accordingly
 * @param  int  $quality  quality of image jpg and png
 * @param  bool $show     output to browser if true
 * @param  str $output_format optional output format, if not determining from out_file can be excusivly set (1|'GIF', 2|'JPG,'' 3|'PNG')
 * @param  boolean $upscale  true, allows image to scale up/zoom to fit thumbnail
 * @return bool            success
 */
function generate_thumbnail($file, $sub_path = '', $out_file = null, $w = null, $h = null, $crop = null, $quality = null, $show = false, $output_format = null, $upscale = false){
	//gd check, do nothing if no gd
	$php_modules = get_loaded_extensions();
	if(!in_arrayi('gd', $php_modules)) return false;

	$sub_path      = tsl($sub_path);
	$upload_folder = GSDATAUPLOADPATH.$sub_path;
	$thumb_folder  = GSTHUMBNAILPATH.$sub_path;
	$thumb_file    = isset($out_file) && !empty($out_file) ? $thumb_folder.$out_file : '';

	create_dir($thumb_folder);

	require_once('imagemanipulation.php');
	$objImage = new ImageManipulation($upload_folder.$file);
	if ( $objImage->imageok ) {
		if($upscale) $objImage->setUpscale(); // allow magnification
		if($quality) $objImage->setQuality($quality); // set quality for jpg or png
		if(isset($output_format)) $objImage->setOutputFormat($output_format); // setoutput format, ignored if out_file specifies extension
		
		if(isset($w) && isset($h)) $objImage->setImageWidth($w,$h); // if height set scale width and height
		elseif(isset($w)){
			$objImage->setImageWidth($w); // if only specifiying width, scale to width only
			// $objImage->resize($w); // constrains both dimensions to $size, same as setImageWidth($w,$w);
		}
		elseif(isset($h)){
			$objImage->setImageHeight($h); // if only specifiying width, scale to width only
		}		
		
		if(isset($crop)) $objImage->setAutoCrop($crop);

		// die(print_r($objImage));
		$objImage->save($thumb_file, $show);
		return $objImage;
	} else {
		return false;
	}
}


/* ?> */

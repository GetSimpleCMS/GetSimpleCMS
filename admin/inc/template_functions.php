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
	$path = myself(FALSE);
	$file = basename($path,".php");	
	echo "id=\"". $file ."\"";	
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
 * Delete Pages File
 *
 * Generates HTML code to place on the body tag of a page
 *
 * @since 1.0
 * @uses GSBACKUPSPATH
 * @uses GSDATAPAGESPATH
 *
 * @param string $id File ID to delete
 */
function delete_file($id) {
	$bakfile = GSBACKUPSPATH."pages/". $id .".bak.xml";
	$file = GSDATAPAGESPATH . $id .".xml";
	copy($file, $bakfile);
	unlink($file);
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
  $configmod = substr(sprintf('%o', fileperms($path)), -4);  
	return $configmod;
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
	unlink(GSBACKUPSPATH."zip/". $id);
	return 'success';
} 

/**
 * Delete Uploaded File
 *
 * @since 1.0
 * @uses GSTHUMBNAILPATH
 * @uses GSDATAUPLOADPATH
 *
 * @param string $id Uploaded filename to delete
 * @return string
 */
function delete_upload($id) { 
	unlink(GSDATAUPLOADPATH . $id);
	if (file_exists(GSTHUMBNAILPATH."thumbnail.". $id)) {
		unlink(GSTHUMBNAILPATH."thumbnail.". $id);
	}
	if (file_exists(GSTHUMBNAILPATH."thumbsm.". $id)) {
		unlink(GSTHUMBNAILPATH."thumbsm.". $id);
	}
	return 'success';
} 

/**
 * Delete Pages Backup File
 *
 * @since 1.0
 * @uses GSBACKUPSPATH
 *
 * @param string $id File ID to delete
 * @return string
 */
function delete_bak($id) { 
	unlink(GSBACKUPSPATH."pages/". $id .".bak.xml");
	return 'success';
} 

/**
 * Restore Pages Backup File
 *
 * @since 1.0
 * @uses GSBACKUPSPATH
 * @uses GSDATAPAGESPATH
 *
 * @param string $id File ID to restore
 */
function restore_bak($id) { 
	$file = GSBACKUPSPATH."pages/". $id .".bak.xml";
	$newfile = GSDATAPAGESPATH . $id .".xml";
	$tmpfile = GSBACKUPSPATH."pages/". $id .".tmp.xml";
	if ( !file_exists($newfile) ) { 
		copy($file, $newfile);
		unlink($file);
	} else {
		copy($file, $tmpfile);
		copy($newfile, $file);
		copy($tmpfile, $newfile);
		unlink($tmpfile);
	}
} 

/**
 * Create Random Password
 *
 * @since 1.0
 *
 * @return string
 */
function createRandomPassword() {
    $chars = "Ayz23mFGHBxPQefgnopRScdqrTU4CXYZabstuDEhijkIJKMNVWvw56789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= 8) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

/**
 * File Type Category
 *
 * Returns the category of an file based on it's extension
 *
 * @since 1.0
 * @uses i18n_r
 *
 * @param string $ext
 * @return string
 */
function get_FileType($ext) {

	$ext = strtolower($ext);
	if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'pct' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png' ) {
		return i18n_r('IMAGES');
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
 * Create Backup Pages File
 *
 * @since 1.0
 * @uses tsl
 *
 * @param string $file
 * @param string $filepath
 * @param string $bakpath
 * @return bool
 */
function createBak($file, $filepath, $bakpath) {
	$bakfile = '';
	if ( file_exists(tsl($filepath) . $file) ) {
		$bakfile = $file .".bak";
		copy($filepath . $file, $bakpath . $bakfile);
	}
	
	if ( file_exists($bakfile) ) {
		return true;
	} else {
		return false;
	} 
}

/**
 * ISO Timestamp
 *
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
 * Ping Sitemaps
 *
 * @since 1.0
 *
 * @param string $url_xml XML sitemap
 * @return bool
 */
function pingGoogleSitemaps($url_xml) {
   $status = 0;
   $google = 'www.google.com';
   $yahoo  = 'search.yahooapis.com';
   $bing 	 = 'www.bing.com';
   $ask 	 = 'submissions.ask.com';
   if( $fp=@fsockopen($google, 80) ) {
      $req =  'GET /webmasters/sitemaps/ping?sitemap=' .
              urlencode( $url_xml ) . " HTTP/1.1\r\n" .
              "Host: $google\r\n" .
              "User-Agent: Mozilla/5.0 (compatible; " .
              PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
              "Connection: Close\r\n\r\n";
      fwrite( $fp, $req );
      while( !feof($fp) ) {
         if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) ) {
            $status = intval( $m[1] );
            break;
         }
      }
      fclose( $fp );
   }
   
   if( $fp=@fsockopen($yahoo, 80) ) {
      $req =  'GET /SiteExplorerService/V1/updateNotification?appid=simpleManage&url=' .
              urlencode( $url_xml ) . " HTTP/1.1\r\n" .
              "Host: $yahoo\r\n" .
              "User-Agent: Mozilla/5.0 (compatible; " .
              PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
              "Connection: Close\r\n\r\n";
      fwrite( $fp, $req );
      while( !feof($fp) ) {
         if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) ) {
            $status = intval( $m[1] );
            break;
         }
      }
      fclose( $fp );
   }
   
   if( $fp=@fsockopen($bing, 80) ) {
      $req =  'GET /webmaster/ping.aspx?sitemap=' .
              urlencode( $url_xml ) . " HTTP/1.1\r\n" .
              "Host: $bing\r\n" .
              "User-Agent: Mozilla/5.0 (compatible; " .
              PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
              "Connection: Close\r\n\r\n";
      fwrite( $fp, $req );
      while( !feof($fp) ) {
         if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) ) {
            $status = intval( $m[1] );
            break;
         }
      }
      fclose( $fp );
   }
   
   if( $fp=@fsockopen($ask, 80) ) {
      $req =  'GET /ping?sitemap=' .
              urlencode( $url_xml ) . " HTTP/1.1\r\n" .
              "Host: $ask\r\n" .
              "User-Agent: Mozilla/5.0 (compatible; " .
              PHP_OS . ") PHP/" . PHP_VERSION . "\r\n" .
              "Connection: Close\r\n\r\n";
      fwrite( $fp, $req );
      while( !feof($fp) ) {
         if( @preg_match('~^HTTP/\d\.\d (\d+)~i', fgets($fp, 128), $m) ) {
            $status = intval( $m[1] );
            break;
         }
      }
      fclose( $fp );
   }
   
   return( $status );
}

/**
 * Undo
 *
 * @since 1.0
 * @uses tsl
 *
 * @param string $file
 * @param string $filepath
 * @param string $bakpath
 * @return bool
 */
function undo($file, $filepath, $bakpath) {
	$old_file = $filepath . $file;
	$new_file = tsl($bakpath) . $file .".bak";
	$tmp_file = tsl($bakpath) . $file .".tmp";
	copy($old_file, $tmp_file);
	copy($new_file, $old_file);
	copy($tmp_file, $new_file);
	unlink($tmp_file);
	
	if (file_exists($tmp_file)) {
		return false;
	} else {
		return true;
	}
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
	$size = '<b>'. ceil(round(($s / 1024), 1)) .'</b> KB'; // in kb
	if ($s >= "1000000") {
		$size = '<b>'. round(($s / 1048576), 1) .'</b> MB'; // in mb
	}
	if ($s <= "999") {
		$size = '<b>< 1</b> KB'; // in kb
	}
	
	return $size;
}

/**
 * Validate Email Address
 *
 * @since 1.0
 *
 * @param string $email 
 * @return bool
 */
function check_email_address($email) {
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
 * @since 1.0
 * @uses i18n_r
 * @uses getXML
 *
 * @param string $file File to validate
 * @return string
 */
function valid_xml($file) {
	$xmlv = @getXML($file);
	global $i18n;
	if ($xmlv) {
		return '<span class="OKmsg" >XML Valid - '.i18n_r('OK').'</span>';
	} else {
		return '<span class="ERRmsg" >XML Invalid - '.i18n_r('ERROR').'!</span>';
	}
}

/**
 * Generate Salt
 *
 * Contacts the GetSimple API and returns a new unique API key
 *
 * @since 1.0
 * @uses $api_url
 * @uses GSVERSION
 *
 * @return string
 */
function generate_salt() {
	
	global $api_url;
	
	$curl_URL = $api_url .'?r=true&v='.GSVERSION;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $curl_URL);
	$datac = curl_exec($ch);
	curl_close($ch);
	$apikey = json_decode($datac);
	return $apikey;
}

/**
 * Get Admin Path
 *
 * Gets the path of the admin directory
 *
 * @since 1.0
 * @uses $GSADMIN
 *
 * @return string
 */
function get_admin_path() {
	global $GSADMIN;
	$pos = strrpos(__FILE__,$GSADMIN);
	if ($pos === FALSE) die('Admin directory name changed!');
	return substr(__FILE__, 0, $pos+6);
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
	$pos = strrpos(dirname(__FILE__),'/inc');
	$adm = substr(dirname(__FILE__), 0, $pos);
	$pos2 = strrpos($adm,'/');
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
	if(defined('GSLOGINSALT') && GSLOGINSALT != '') { 
		$logsalt = sha1(GSLOGINSALT);
	} else { 
		$logsalt = null; 
	}
	
	return sha1($p . $logsalt);
}

?>
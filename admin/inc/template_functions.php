<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	template_functions.php
* @Package:	GetSimple
* @Action:	Functions used to help create the cp pages	
*
*****************************************************/
	
	
/*******************************************************
 * @function get_template
 * @param $name - name of template
 *
*/
function get_template($name, $title='** Change Me - Default Page Title **') {
	ob_start();
	$file = "template/" . $name . ".php";
	include($file);
	$template = ob_get_contents();
	ob_end_clean(); 
	echo $template;
}
/******************************************************/


/*******************************************************
 * @function filename_id
 * @returns returns the basename of the admin page in id=""
 *
*/
function filename_id() {
	$path = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES);
	$file = basename($path,".php");	
	echo "id=\"". $file ."\"";	
}
/******************************************************/


/*******************************************************
 * @function get_filename_id
 * @returns returns the basename of the admin page
 *
*/
function get_filename_id() {
	$path = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES);
	$file = basename($path,".php");	
	return $file;	
}
/******************************************************/





/*******************************************************
 * @function delete_file
 * @param $id - page to delete
 *
*/
function delete_file($id) {
	$bakfile = GSBACKUPSPATH."pages/". $id .".bak.xml";
	$file = GSDATAPAGESPATH . $id .".xml";
	copy($file, $bakfile);
	unlink($file);
}
/******************************************************/


/*******************************************************
 * @function check_perms
 * @param $path - path to get file permissions for
 *
*/
function check_perms($path) { 
  clearstatcache(); 
  $configmod = substr(sprintf('%o', fileperms($path)), -4);  
	return $configmod;
} 
/******************************************************/


/*******************************************************
 * @function delete_zip
 * @param $id - zip to delete
 *
*/
function delete_zip($id) { 
	unlink(GSBACKUPSPATH."zip/". $id);
	return 'success';
} 
/******************************************************/


/*******************************************************
 * @function delete_upload
 * @param $id - upload file to delete
 *
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
/******************************************************/


/*******************************************************
 * @function delete_bak
 * @param $id - page backup to delete
 *
*/
function delete_bak($id) { 
	unlink(GSBACKUPSPATH."pages/". $id .".bak.xml");
	return 'success';
} 
/******************************************************/


/*******************************************************
 * @function restore_bak
 * @param $id - page backup to restore to
 *
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
/******************************************************/


/*******************************************************
 * @function createRandomPassword
 * @returns random 6 character password
 *
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
/******************************************************/



/*******************************************************
 * @function get_FileType
 * @param $ext - extension of the file
 * @returns file type
 *
*/
function get_FileType($ext) {
	global $i18n;
	$ext = strtolower($ext);
	if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'pct' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png' ) {
		return $i18n['IMAGES'];
	} elseif ( $ext == 'zip' || $ext == 'gz' || $ext == 'rar' || $ext == 'tar' || $ext == 'z' || $ext == '7z' || $ext == 'pkg' ) {
		return $i18n['FTYPE_COMPRESSED'];
	} elseif ( $ext == 'ai' || $ext == 'psd' || $ext == 'eps' || $ext == 'dwg' || $ext == 'tif' || $ext == 'tiff' || $ext == 'svg' ) {
		return $i18n['FTYPE_VECTOR'];
	} elseif ( $ext == 'swf' || $ext == 'fla' ) {
		return $i18n['FTYPE_FLASH'];	
	} elseif ( $ext == 'mov' || $ext == 'mpg' || $ext == 'avi' || $ext == 'mpeg' || $ext == 'rm' || $ext == 'wmv' ) {
		return $i18n['FTYPE_VIDEO'];
	} elseif ( $ext == 'mp3' || $ext == 'wav' || $ext == 'wma' || $ext == 'midi' || $ext == 'mid' || $ext == 'm3u' || $ext == 'ra' || $ext == 'aif' ) {
		return $i18n['FTYPE_AUDIO'];
	} elseif ( $ext == 'php' || $ext == 'phps' || $ext == 'asp' || $ext == 'xml' || $ext == 'js' || $ext == 'jsp' || $ext == 'sql' || $ext == 'css' || $ext == 'htm' || $ext == 'html' || $ext == 'xhtml' || $ext == 'shtml' ) {
		return $i18n['FTYPE_WEB'];
	} elseif ( $ext == 'mdb' || $ext == 'accdb' || $ext == 'pdf' || $ext == 'xls' || $ext == 'xlsx' || $ext == 'csv' || $ext == 'tsv' || $ext == 'ppt' || $ext == 'pps' || $ext == 'pptx' || $ext == 'txt' || $ext == 'log' || $ext == 'dat' || $ext == 'text' || $ext == 'doc' || $ext == 'docx' || $ext == 'rtf' || $ext == 'wks' ) {
		return $i18n['FTYPE_DOCUMENTS'];
	} elseif ( $ext == 'exe' || $ext == 'msi' || $ext == 'bat' || $ext == 'download' || $ext == 'dll' || $ext == 'ini' || $ext == 'cab' || $ext == 'cfg' || $ext == 'reg' || $ext == 'cmd' || $ext == 'sys' ) {
		return $i18n['FTYPE_SYSTEM'];
	} else {
		return $i18n['FTYPE_MISC'];
	}
}
/******************************************************/



/*******************************************************
 * @function createBak
 * @param $file - file to backup
 * @param $filepath - path to backup file at
 *
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
/******************************************************/




/*******************************************************
 * @function makeIso8601TimeStamp
 * @param $dateTime - date to create iso timestamp from
 * @returns - iso timestamp
 *
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
/******************************************************/


/*******************************************************
 * @function pingGoogleSitemaps
 * @param $url_xml - xml file to ping to Google
 * @returns - status
 *
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
/******************************************************/


/*******************************************************
 * @function undo
 * @param $file - filename to undo changes to
 * @param $filepath - file location
 * @param $bakpath - backup file location
 *
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
/******************************************************/



/*******************************************************
 * @function fSize
 * @param $s - filesize
 * @returns formated file size
 *
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
/******************************************************/


/*******************************************************
 * @function check_email_address
 * @param $email - email address to check
 * @returns true or false validation check
 *
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


/*******************************************************
 * @function do_reg
 * @param $text - text to check
 * @param $regrex - regrex to check with
 * @returns true or false validation check
 *
*/
function do_reg($text, $regex) {
	if (preg_match($regex, $text)) {
		return true;
	} else {
		return false;
	}
}
/******************************************************/


/*******************************************************
 * @function valid_xml
 * @param $file - file to validate
 * @returns true or false validation check
 *
*/
function valid_xml($file) {
	$xmlv = @getXML($file);
	global $i18n;
	if ($xmlv) {
		return '<span class="OKmsg" >XML Valid - '.$i18n['OK'].'</span>';
	} else {
		return '<span class="ERRmsg" >XML Invalid - '.$i18n['ERROR'].'!</span>';
	}
}
/******************************************************/


/*******************************************************
 * @function is_ignore_word
 * @param $word - file to validate
 * @returns true if word should be ignored
 *
*/
function is_ignore_word($word) {
$stopwords = array("a","about","above","above","across","after","afterwards","again","against","all","almost","alone","along","already","also","although","always","am","among","amongst","amoungst","amount","an","and","another","any","anyhow","anyone","anything","anyway","anywhere","are","around","as",  "at","back","be","became","because","become","becomes","becoming","been","before","beforehand","behind","being","below","beside","besides","between","beyond","bill","both","bottom","but","by","call","can","cannot","cant","co","con","could","couldnt","cry","de","describe","detail","do","done","down","due","during","each","eg","eight","either","eleven","else","elsewhere","empty","enough","etc","even","ever","every","everyone","everything","everywhere","except","few","fifteen","fify","fill","find","fire","first","five","for","former","formerly","forty","found","four","from","front","full","further","get","give","go","had","has","hasnt","have","he","hence","her","here","hereafter","hereby","herein","hereupon","hers","herself","him","himself","his","how","however","hundred","ie","if","in","inc","indeed","interest","into","is","it","its","itself","keep","last","latter","latterly","least","less","ltd","made","many","may","me","meanwhile","might","mill","mine","more","moreover","most","mostly","move","much","must","my","myself","name","namely","neither","never","nevertheless","next","nine","no","nobody","none","noone","nor","not","nothing","now","nowhere","of","off","often","on","once","one","only","onto","or","other","others","otherwise","our","ours","ourselves","out","over","own","part","per","perhaps","please","put","rather","re","same","see","seem","seemed","seeming","seems","serious","several","she","should","show","side","since","sincere","six","sixty","so","some","somehow","someone","something","sometime","sometimes","somewhere","still","such","system","take","ten","than","that","the","their","them","themselves","then","thence","there","thereafter","thereby","therefore","therein","thereupon","these","they","thickv","thin","third","this","those","though","three","through","throughout","thru","thus","to","together","too","top","toward","towards","twelve","twenty","two","un","under","until","up","upon","us","very","via","was","we","well","were","what","whatever","when","whence","whenever","where","whereafter","whereas","whereby","wherein","whereupon","wherever","whether","which","while","whither","who","whoever","whole","whom","whose","why","will","with","within","without","would","yet","you","your","yours","yourself","yourselves","the");	
if (in_array(strtolower($word), $stopwords)) {
		return true;
	} else {
		return false;	
	}
}
/******************************************************/


/*******************************************************
 * @function generate_salt
 * @returns new salt value
 *
*/
function generate_salt() {
	
	global $api_url;
	global $site_version_no;
	
	$curl_URL = $api_url .'?r=true&v='.$site_version_no;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $curl_URL);
	$datac = curl_exec($ch);
	curl_close($ch);
	$apikey = json_decode($datac);
	return $apikey;
}
/******************************************************/


/*******************************************************
 * @function get_admin_path
 * @returns path to admin folder
 *
*/
function get_admin_path() {
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	$segments = explode(DIRECTORY_SEPARATOR, $path);
	$cut = array_keys($segments,'admin');
	rsort($cut);
	$new_segments = array_slice($segments, 0, $cut[0]+1);
	return implode('/', $new_segments) . '/';
}
/******************************************************/


/*******************************************************
 * @function get_root_path
 * @return path to root install folder
 *
*/
function get_root_path() {
	$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	$segments = explode(DIRECTORY_SEPARATOR, $path);
	$cut = array_keys($segments,'admin');
	rsort($cut);
	$new_segments = array_slice($segments, 0, $cut[0]);
	return implode('/', $new_segments) . '/';
}
/******************************************************/


/*******************************************************
 * @function check_menu
 * @param $text - text to check
 * @return echos class='current' if current filename==$txt
 *
*/
function check_menu($text) {
	if(get_filename_id()===$text){
		echo 'class="current"';
	}
}

/*******************************************************
 * @function passhash
 * @returns returns a hashed password
 *
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
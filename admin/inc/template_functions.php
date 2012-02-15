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
 * @param string $path Path to uploaded file folder
 * @return string
 */
function delete_upload($id, $path = "") { 
	unlink(GSDATAUPLOADPATH . $path . $id);
	if (file_exists(GSTHUMBNAILPATH.$path."thumbnail.". $id)) {
		unlink(GSTHUMBNAILPATH.$path."thumbnail.". $id);
	}
	if (file_exists(GSTHUMBNAILPATH.$path."thumbsm.". $id)) {
		unlink(GSTHUMBNAILPATH.$path."thumbsm.". $id);
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
	generate_sitemap();
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
    while ($i <= 5) {
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
 *
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
 * @since 1.0
 * @uses i18n_r
 * @uses getXML
 *
 * @param string $file File to validate
 * @return string
 */
function valid_xml($file) {
	$xmlv = getXML($file);
	global $i18n;
	if ($xmlv) {
		return '<span class="OKmsg" >'.i18n_r('XML_VALID').' - '.i18n_r('OK').'</span>';
	} else {
		return '<span class="ERRmsg" >'.i18n_r('XML_INVALID').' - '.i18n_r('ERROR').'!</span>';
	}
}

/**
 * Generate Salt
 *
 * Returns a new unique salt
 * @updated 3.0
 *
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
	if(defined('GSLOGINSALT') && GSLOGINSALT != '') { 
		$logsalt = sha1(GSLOGINSALT);
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
    
    $path = GSDATAPAGESPATH;
    $dir_handle = @opendir($path) or die("Unable to open $path");
    $filenames = array();
    while ($filename = readdir($dir_handle)) {
        $filenames[] = $filename;
    }
    closedir($dir_handle);
    
    $count="0";
    $pagesArray = array();
    if (count($filenames) != 0) {
        foreach ($filenames as $file) {
            if ($file == "." || $file == ".." || is_dir($path . $file) || $file == ".htaccess"  ) {
                // not a page data file
            } else {
								$data = getXML($path . $file);
                if ($data->private != 'Y') {
                    $pagesArray[$count]['menuStatus'] = $data->menuStatus;
                    $pagesArray[$count]['menuOrder'] = $data->menuOrder;
                    $pagesArray[$count]['menu'] = strip_decode($data->menu);
                    $pagesArray[$count]['parent'] = $data->parent;
                    $pagesArray[$count]['title'] = strip_decode($data->title);
                    $pagesArray[$count]['url'] = $data->url;
                    $pagesArray[$count]['private'] = $data->private;
                    $pagesArray[$count]['pubDate'] = $data->pubDate;
                    $count++;
                }
            }
        }
    }
    
    $pagesSorted = subval_sort($pagesArray,'title');
    if (count($pagesSorted) != 0) { 
      $count = 0;
      foreach ($pagesSorted as $page) {
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
      return $extract;
    }
}

  
/**
 * Update Slugs
 *
 * @since 2.04
 * @uses $url
 * @uses GSDATAPAGESPATH
 * @uses XMLsave
 *
 */
function updateSlugs($existingUrl, $newurl=null){
      
      if (!$newurl){
      	global $url;
      } else {
      	$url = $newurl;
      }

      $path = GSDATAPAGESPATH;
      $dir_handle = @opendir($path) or die("Unable to open $path");
      $filenames = array();
      while ($filename = readdir($dir_handle)) {
        $ext = substr($filename, strrpos($filename, '.') + 1);
        if ($ext=="xml"){
          $filenames[] = $filename;
        }
      }

      if (count($filenames) != 0) {
        foreach ($filenames as $file) {
          
          if ($file == "." || $file == ".." || is_dir(GSDATAPAGESPATH.$file) || $file == ".htaccess"  ) {
            // not a page data file
          } else {
            $thisfile = @file_get_contents(GSDATAPAGESPATH.$file);
            $data = simplexml_load_string($thisfile);
            if ($data->parent==$existingUrl){
              $data->parent=$url;
              XMLsave($data, GSDATAPAGESPATH.$file);
            }   
          } 
        }
      }
} 


/**
 * List Pages Json
 *
 * This is used by the CKEditor link-local plguin function: ckeditor_add_page_link()
 *
 * @author Joshas: mailto:joshas@gmail.com
 *
 * @since 3.0
 * @uses subval_sort
 * @uses GSDATAPAGESPATH
 * @uses getXML
 *
 * @returns array
 */
function list_pages_json() {
	// get local pages list for ckeditor local page link selector
	$path = GSDATAPAGESPATH;
	$filenames = getFiles($path);
	$count="0";
	$pagesArray = array();
	if (count($filenames) != 0) { 
		foreach ($filenames as $file) {
			if (isFile($file, $path, 'xml')) {
				$data = getXML($path .$file);
				$pagesArray[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
				$pagesArray[$count]['parent'] = $data->parent;
			if ($data->parent != '') { 
				$parentdata = getXML($path . $data->parent .'.xml');
				$parentTitle = $parentdata->title;
				$pagesArray[$count]['sort'] = $parentTitle .' '. $data->title;
			} else {
				$pagesArray[$count]['sort'] = $data->title;
			}
			$pagesArray[$count]['url'] = $data->url;
			$parentTitle = '';
			$count++;
			}
		}
	}
	$pagesSorted = subval_sort($pagesArray,'sort');
	$pageList = array();
	if (count($pagesSorted) != 0) { 
		foreach ($pagesSorted as $page) {
			if ($page['parent'] != '') {$page['parent'] = $page['parent']."/"; $dash = '- '; } else { $dash = ""; }
			if ($page['title'] == '' ) { $page['title'] = '[No Title] '.$page['url']; }
			array_push($pageList, array( $dash . $page['title'], find_url($page['url'],$page['parent'])));
		}
	}
	return json_encode($pageList);
}

/**
 * CKEditor Add Local Page Link
 *
 * This is used by the CKEditor to link to internal pages
 *
 * @author Joshas: mailto:joshas@gmail.com
 *
 * @since 3.0
 * @uses list_pages_json
 *
 * @returns array
 */
function ckeditor_add_page_link(){
	echo "
	<script type=\"text/javascript\">
	//<![CDATA[
	// Get a CKEDITOR.dialog.contentDefinition object by its ID.
	var getById = function(array, id, recurse) {
		for (var i = 0, item; (item = array[i]); i++) {
			if (item.id == id) return item;
				if (recurse && item[recurse]) {
					var retval = getById(item[recurse], id, recurse);
					if (retval) return retval;
				}
		}
		return null;
	};

	// modify existing Link dialog
	CKEDITOR.on( 'dialogDefinition', function( ev )	{
		if ((ev.editor != editor) || (ev.data.name != 'link')) return;

		// Overrides definition.
		var definition = ev.data.definition;
		definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original) {
			return function() {
				original.call(this);
					if (this.getValueOf('info', 'linkType') == 'localPage') {
						this.getContentElement('info', 'localPage_path').select();
					}
			};
		});

		// Overrides linkType definition.
		var infoTab = definition.getContents('info');
		var content = getById(infoTab.elements, 'linkType');

		content.items.unshift(['Link to local page', 'localPage']);
		content['default'] = 'localPage';
		infoTab.elements.push({
			type: 'vbox',
			id: 'localPageOptions',
			children: [{
				type: 'select',
				id: 'localPage_path',
				label: 'Select page:',
				required: true,
				items: " . list_pages_json() . ",
				setup: function(data) {
					if ( data.localPage )
						this.setValue( data.localPage );
				}
			}]
		});
		content.onChange = CKEDITOR.tools.override(content.onChange, function(original) {
			return function() {
				original.call(this);
				var dialog = this.getDialog();
				var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
				if (this.getValue() == 'localPage') {
					element.show();
					if (editor.config.linkShowTargetTab) {
						dialog.showPage('target');
					}
					var uploadTab = dialog.definition.getContents('upload');
					if (uploadTab && !uploadTab.hidden) {
						dialog.hidePage('upload');
					}
				}
				else {
					element.hide();
				}
			};
		});
		content.setup = function(data) {
			if (!data.type || (data.type == 'url') && !data.url) {
				data.type = 'localPage';
			}
			else if (data.url && !data.url.protocol && data.url.url) {
				if (path) {
					data.type = 'localPage';
					data.localPage_path = path;
					delete data.url;
				}
			}
			this.setValue(data.type);
		};
		content.commit = function(data) {
			data.type = this.getValue();
			if (data.type == 'localPage') {
				data.type = 'url';
				var dialog = this.getDialog();
				dialog.setValueOf('info', 'protocol', '');
				dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
			}
		};
	});
	//]]>
	</script>";
}


/**
 * Recursive list of pages
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
function get_pages_menu($parent, $menu,$level) {
	global $pagesSorted;
	
	$items=array();
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
	  				$dash .= '<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
				} else {
					$dash .= '<span>&nbsp;&nbsp;&ndash;&nbsp;&nbsp;&nbsp;</span>';
				}
			} 
			$menu .= '<tr id="tr-'.$page['url'] .'" >';
			if ($page['title'] == '' ) { $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; }
			if ($page['menuStatus'] != '' ) { $page['menuStatus'] = ' <sup>['.i18n_r('MENUITEM_SUBTITLE').']</sup>'; } else { $page['menuStatus'] = ''; }
			if ($page['private'] != '' ) { $page['private'] = ' <sup>['.i18n_r('PRIVATE_SUBTITLE').']</sup>'; } else { $page['private'] = ''; }
			if ($page['url'] == 'index' ) { $homepage = ' <sup>['.i18n_r('HOMEPAGE_SUBTITLE').']</sup>'; } else { $homepage = ''; }
			$menu .= '<td class="pagetitle">'. $dash .'<a title="'.i18n_r('EDITPAGE_TITLE').': '. cl($page['title']) .'" href="edit.php?id='. $page['url'] .'" >'. cl($page['title']) .'</a><span class="showstatus toggle" >'. $homepage . $page['menuStatus'] . $page['private'] .'</span></td>';
			$menu .= '<td style="width:80px;text-align:right;" ><span>'. shtDate($page['pubDate']) .'</span></td>';
			$menu .= '<td class="secondarylink" >';
			$menu .= '<a title="'.i18n_r('VIEWPAGE_TITLE').': '. cl($page['title']) .'" target="_blank" href="'. find_url($page['url'],$page['parent']) .'">#</a>';
			$menu .= '</td>';
			if ($page['url'] != 'index' ) {
				$menu .= '<td class="delete" ><a class="delconfirm" href="deletefile.php?id='. $page['url'] .'&amp;nonce='.get_nonce("delete", "deletefile.php").'" title="'.i18n_r('DELETEPAGE_TITLE').': '. cl($page['title']) .'" >&times;</a></td>';
			} else {
				$menu .= '<td class="delete" ></td>';
			}
			$menu .= '</tr>';
			$menu = get_pages_menu((string)$page['url'], $menu,$level+1);	  	
		}
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
	include(GSADMININCPATH.'configuration.php');
	
	# core api details
	if ($type=='core') {
		$fetch_this_api = $api_url .'?v='.GSVERSION;
	}
	
	# plugin api details. requires a passed plugin id
	if ($type=='plugin' && $args) {
		$apiurl = 'http://get-simple.info/api/extend/?file=';
		$fetch_this_api = $apiurl.$args;
	}
	
	# custom api details. requires a passed url
	if ($type=='custom' && $args) {
		$fetch_this_api = $args;
	}
		
	# check to see if cache is available for this
	$cachefile = md5($fetch_this_api).'.txt';

	if (file_exists(GSCACHEPATH.$cachefile) && time() - 40000 < filemtime(GSCACHEPATH.$cachefile)) {
		# grab the api request from the cache
		
		$data = file_get_contents(GSCACHEPATH.$cachefile);
	} else {	
		# make the api call
		if (function_exists('curl_exec')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $fetch_this_api);
			$data = curl_exec($ch);
			curl_close($ch);
		} else {
			$data = file_get_contents($fetch_this_api);
		}
		file_put_contents(GSCACHEPATH.$cachefile, $data);
  	chmod(GSCACHEPATH.$cachefile, 0644);
	}
	return $data;
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
 * Creates sitemap.xml in the site's root.
 */
function generate_sitemap() {
	
	// Variable settings
	global $SITEURL;
	$path = GSDATAPAGESPATH;
	$count="0";
	
	$filenames = getFiles($path);
	
	if (count($filenames) != 0)	{ 
		foreach ($filenames as $file)	{
			if ( isFile($file, $path, 'xml')) {
				$data = getXML($path . $file);
				if ($data->url != '404') {
					$status = $data->menuStatus;
					$pagesArray[$count]['url'] = $data->url;
					$pagesArray[$count]['parent'] = $data->parent;
					$pagesArray[$count]['date'] = $data->pubDate;
					$pagesArray[$count]['private'] = $data->private;
					$pagesArray[$count]['menuStatus'] = $data->menuStatus;
					$count++;
				}
			}
		}
	}
	
	$pagesSorted = subval_sort($pagesArray,'menuStatus');
	
	if (count($pagesSorted) != 0)
	{ 
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
		$xml->addAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd', 'http://www.w3.org/2001/XMLSchema-instance');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		
		foreach ($pagesSorted as $page)
		{	
			if ($page['private'] != 'Y')
			{
				// set <loc>
				$pageLoc = find_url($page['url'], $page['parent']);
				
				// set <lastmod>
				$tmpDate = date("Y-m-d H:i:s", strtotime($page['date']));
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
				exec_action('sitemap-additem');
			}
			
			//create xml file
			$file = GSROOTPATH .'sitemap.xml';
			exec_action('save-sitemap');
			XMLsave($xml, $file);
		}
	}
	
	if (!defined('GSDONOTPING')) {
		if (file_exists(GSROOTPATH .'sitemap.xml')){
			if( 200 === ($status=pingGoogleSitemaps($SITEURL.'sitemap.xml')))	{
				#sitemap successfully created & pinged
				return true;
			} else {
				error_log(i18n_r('SITEMAP_ERRORPING'));
				return i18n_r('SITEMAP_ERRORPING');
			}
		} else {
			error_log(i18n_r('SITEMAP_ERROR'));
			return i18n_r('SITEMAP_ERROR');
		}
	} else {
		#sitemap successfully created - did not ping
		return true;
	}
}



/**
 * Creates tar.gz Archive 
 *
 * Creates sitemap.xml in the site's root.
 */
function archive_targz() {
	if(!function_exists('exec')) {
    return false;
    exit;
	}
	$timestamp = gmdate('Y-m-d-Hi_s');
	$saved_zip_file_path = GSBACKUPSPATH.'zip/';
	$saved_zip_file = $timestamp .'_archive.tar.gz';	
	$script_contents = "tar -cvzf ".$saved_zip_file_path.$saved_zip_file." ".GSROOTPATH.".htaccess ".GSROOTPATH."gsconfig.php ".GSROOTPATH."data ".GSROOTPATH."plugins ".GSROOTPATH."theme ".GSROOTPATH."admin/lang > /dev/null 2>&1";
	exec($script_contents, $output, $rc);
	if (file_exists($saved_zip_file_path.$saved_zip_file)) {
		return true;
	} else {
		return false;
	}
}

?>

<?php
/**
 * Upload Files
 *
 * Displays and uploads files to the website
 *
 * @package GetSimple
 * @subpackage Files
 * @todo Remove relative paths ? not sure what this means
 */
 
// Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

$allowcreatefolder = getDef('GSALLOWUPLOADCREATE',true);
$allowdelete       = getDef('GSALLOWUPLOADDELETE',true);
$allowupload       = true;

if(isset($_GET['browse']) || (isset($browse) && $browse == true)){
	$allowcreatefolder = false;
	$allowdelete       = false;
	$allowupload       = getDef('GSALLOWBROWSEUPLOAD',true);
}

exec_action('load-upload');

$dirsSorted = $filesSorted = $foldercount = null;

if (isset($_GET['path']) && !empty($_GET['path'])) {
	$path      = str_replace('../','', $_GET['path']);
	$subFolder = tsl($path);
	$path      = tsl(GSDATAUPLOADPATH.$path);
	// die if path is outside of uploads
	if(!path_is_safe($path,GSDATAUPLOADPATH)) die();
} else { 
	$path      = GSDATAUPLOADPATH;
	$subFolder = '';
}

// if a file was uploaded
if (isset($_FILES['file'])) {
	
	$uploadsCount = count($_FILES['file']['name']);

	if($uploadsCount > 0) {
	 $errors   = array();
	 $messages = array();

	 for ($i=0; $i < $uploadsCount; $i++) {
		if ($_FILES["file"]["error"][$i] > 0)	{
			$errors[] = i18n_r('ERROR_UPLOAD');
		} else {
			
			//set variables
			$count     = '1';
			$file      = $_FILES["file"]["name"][$i];
			$fileext   = getFileExtension($file);
			$filename  = getFileName($file);
			
			$file_base = clean_img_name(to7bit($filename)) . '.'.$fileext;
			$file_loc  = $path . $file_base;
			
			//prevent overwriting						
			if(!isset($_POST['fileoverwrite']) && file_exists($file_loc)){
				list($file_base,$filecount) = getNextFileName($path,$file_base);
				$file_loc = $path.$file_base;
			}

			//validate file
			if (validate_safe_file($_FILES["file"]["tmp_name"][$i], $file_base)) {
				move_uploaded_file($_FILES["file"]["tmp_name"][$i], $file_loc);
				gs_chmod($file_loc);
				exec_action('file-uploaded');
				
				// generate thumbnail				
				genStdThumb($subFolder,$file_base);

				$messages[] = i18n_r('FILE_SUCCESS_MSG');
				if(requestIsAjax()){
					header("HTTP/1.0 200");
					die();
				}	
			} else {
				$messages[] = $_FILES["file"]["name"][$i] .' - '.i18n_r('ERROR_UPLOAD');
				if(requestIsAjax()){
					header("HTTP/1.0 403");
					i18n('ERROR_UPLOAD');
					die();
				}	
			}
			
			//successfull message
			
		}
	 }
	 // after uploading all files process messages
		if(sizeof($messages) != 0) { 
			foreach($messages as $msg) {
				$success = $msg.'<br />';
			}
		}
		if(sizeof($errors) != 0) {

			if(requestIsAjax()){
				header("HTTP/1.0 403");
				i18n('ERROR_UPLOAD');
				die();
			}

			foreach($errors as $msg) {
				$error = $msg.'<br />';
			}
		}
	}
}

// if creating new folder
if (isset($_GET['newfolder']) && $allowcreatefolder) {

	check_for_csrf("createfolder");	
	
	$newfolder = $_GET['newfolder'];
	// check for invalid chars
	$cleanname = clean_url(to7bit(strippath($newfolder), "UTF-8"));
	$cleanname = basename($cleanname);
	if (file_exists($path.$cleanname) || $cleanname=='') {
			$error = i18n_r('ERROR_FOLDER_EXISTS');
	} else {
		if (getDef('GSCHMOD')) {
			$chmod_value = GSCHMOD; 
		} else {
			$chmod_value = 0755;
		}
		if (create_dir($path . $cleanname, $chmod_value)) {
			//create folder for thumbnails
			$thumbFolder = GSTHUMBNAILPATH.$subFolder.$cleanname;
			if (!(file_exists($thumbFolder))) { create_dir($thumbFolder, $chmod_value); }
			$success = sprintf(i18n_r('FOLDER_CREATED'), $cleanname);
		}	else { 
			$error = i18n_r('ERROR_CREATING_FOLDER'); 
		}
	}
}

$pagetitle = i18n_r('FILE_MANAGEMENT');
get_template('header');

// check if host uses Linux (used for displaying permissions
$isUnixHost = !hostIsWindows();

function getUploadIcon($type){
	if($type == '.') $class = 'folder';
	else $class = getFileIconClass($type).'-o';
	return '<span class="fa fa-fw fa-'.$class.' icon-left"></span>';
}

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main" <?php if(!$allowupload) echo 'style="margin-right:0"'; ?>>
		<h3 class="floated"><?php echo i18n_r('UPLOADED_FILES'); ?><span id="filetypetoggle">&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo i18n_r('SHOW_ALL'); ?></span></h3>
		<div id="file_load">
		<?php
			$count      ="0";
			$dircount   ="0";
			$counter    = "0";
			$totalsize  = 0;
			$filesArray = array();
			$dirsArray  = array();
			$filterArray = array();

			$filenames  = getFiles($path);

			if (count($filenames) != 0) { 
				foreach ($filenames as $file) {
					if ($file == "." || $file == ".." || $file == ".htaccess" || $file == "index.php"){
            			// not a upload file
          			} 
          			elseif (is_dir($path . $file)) {
            			$dirsArray[$dircount]['name'] = $file;
            			clearstatcache();
						$ss = @stat($path . $file);
						$dirsArray[$dircount]['date'] = @date('M j, Y',$ss['mtime']);
            			$dircount++;
					}
					else {
						$filesArray[$count]['name'] = $file;
						$ext      = getFileExtension($file);
						$filetype = get_FileTypeToken($ext);
						$filesArray[$count]['type'] = lowercase($filetype);
						clearstatcache();
						$ss = @stat($path . $file);
						$filesArray[$count]['date'] = @date('M j, Y',$ss['ctime']);
						$filesArray[$count]['size'] = fSize($ss['size']);
						$totalsize = $totalsize + $ss['size'];
						$count++;
					}
				}
				 
				$filesSorted = subval_sort($filesArray,'name');
       			$dirsSorted  = subval_sort($dirsArray,'name');
			}
			echo '<div class="edit-nav clearfix" >';
			echo '<select id="imageFilter">';
			echo '<option value="all">'.i18n_r('SHOW_ALL').'</option>';
			
			if (count($filesSorted) > 0) {
				foreach ($filesSorted as $filter) {
					$filterArray[$filter['type']] = '';
				}
				if (count($filterArray) != 0) { 
					
					ksort($filterArray);
					foreach ($filterArray as $type => $value) {
						$sel = false;
						# check for filter querystring
						if(isset($_GET['type']) && $_GET['type'] == $type) $sel = true;
						if(isset($_GET['type']) && $_GET['type'] == 'images' && $type == 'image') $sel = true; // alias for image (images)
						if(count($filterArray) == 1 && isset($filterArray['image'])) $sel = true;
						echo '<option value="'.$type.'" '. ($sel ? 'selected' : '') .'>'.i18n_r('FTYPE_'.uppercase($type)).'</option>';
					}
				}
			}

		echo '</select>';

	   	exec_action(get_filename_id().'-edit-nav');
		echo "</div>";
		exec_action(get_filename_id().'-body'); 

		$pathParts = explode("/",$subFolder);
		$urlPath = null;

		// preserve querystring, but remove path
		$root = 'upload.php?' . merge_queryString(array('path'=>null));
		echo '<div class="h5 clearfix"><div class="crumbs">/ <a href="'.$root.'">'.i18n_r('FILES').'</a> / ';

		foreach ($pathParts as $pathPart){
		   if ($pathPart!=''){
		      $urlPath .= $pathPart.'/';
		      
		      echo '<a href="?path='.$urlPath.'">'.$pathPart.'</a> / ';
		   }
		}
		echo '</div>';
      	
      	if($allowcreatefolder){
      		echo '<div id="new-folder">
      			<a href="#" id="createfolder">'.i18n_r('CREATE_FOLDER').'</a>
				<form action="upload.php">&nbsp;<input type="hidden" name="path" value="'.$subPath.'" />
					<input type="hidden" name="nonce" value="'. get_nonce("createfolder") .'" />
					<input type="text" class="text" name="newfolder" id="foldername" /> 
					<input type="submit" class="submit" value="'.i18n_r('CREATE_FOLDER').'" />&nbsp; 
					<a href="#" class="cancel">'.i18n_r('CANCEL').'</a>
				</form>
			</div>';
		}
			
		echo '</div>';
      
		$showperms = $isUnixHost && isDebug() && function_exists('posix_getpwuid');

		echo '<table class="highlight" id="imageTable"><thead>'; 
		echo '<tr><th class="imgthumb" ></th><th>'.i18n_r('FILE_NAME').'</th>';
		echo '<th class="file_size right">'.i18n_r('FILE_SIZE').'</th>';
		if ($showperms) echo '<th class="file_perms right">'.i18n_r('PERMS').'</th>';
		echo '<th class="file_date right">'.i18n_r('DATE').'</th>';
		echo '<th class="file_actions"><!-- actions --></th></tr>';
		echo '</thead><tbody>';  
		if (count($dirsSorted) != 0) {
		$foldercount = 0;

		// show folders
		foreach ($dirsSorted as $upload) {
			# check to see if folder is empty
			$directory_delete = null;
			if ( check_empty_folder($path.$upload['name']) && $allowdelete ) {  
				$directory_delete = '<a class="delconfirm" title="'.i18n_r('DELETE_FOLDER').': '. rawurlencode($upload['name']) .'" href="deletefile.php?path='.$urlPath.'&amp;folder='. rawurlencode($upload['name']) . '&amp;nonce='.get_nonce("delete", "deletefile.php").'">&times;</a>';
			}
			$directory_size = '<span>'.folder_items($path.$upload['name']).' '.i18n_r('ITEMS').'</span>';
			
			echo '<tr class="all folder '.$upload['name'].'" >';
			// echo '<td class="imgthumb"><i class="file ext- fa fa-3x fa-fw fa-folder-o"></i></td>';
			echo '<td class="imgthumb"></td>';
			$adm = getRelPath($path,GSDATAUPLOADPATH) . rawurlencode($upload['name']);
			$folderhref = 'upload.php?' . merge_queryString(array('path'=>$adm));
			echo '<td class="break">'.getUploadIcon('.').'</span><a href="'.$folderhref.'" ><strong>'.htmlspecialchars($upload['name']).'</strong></a></td>';
			echo '<td class="file_size right"><span>'.$directory_size.'</span></td>';

		  // get the file permissions.
		if ($showperms) {
			$filePerms = substr(sprintf('%o', fileperms($path.$upload['name'])), -4);
			if($isUnixHost){
				$fileOwner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path.$upload['name'])) : '';
				$fileOwnerName = isset($fileOwner['name']) ? $fileOwner['name'] : '';
			} else {
				$fileOwnerName = getenv('USERNAME');
			}
			echo '<td style="width:70px;text-align:right;"><span>'.$fileOwnerName.'/'.$filePerms.'</span></td>';
		}
		
			echo '<td class="file_date right"><span class="'.(dateIsToday($upload['date']) ? 'datetoday' : '').'">'. output_date($upload['date']) .'</span></td>';
			echo '<td class="delete" >'.$directory_delete.'</td>';
			echo '</tr>';
			$foldercount++;
        }
     }

    // will regenerate all thumbnail. thumbsm. in current folder, ideally used when changing smthumb size
    // can take a very long time if you have massive images, it would be wise to keep folders small if using large gallary images
    // if you have a lot to regen simply delete the images form the thumbs folder and keep refreshing until they are all regenerated
	if(isset($_REQUEST['regenthumbsm']) || isset($_REQUEST['regenthumbnail'])) set_time_limit (120);
	$thumbsm_w = (int)getDef('GSTHUMBSMWIDTH');
	$thumbsm_h = (int)getDef('GSTHUMBSMHEIGHT');

    // show files
	if (count($filesSorted) != 0) { 			
		foreach ($filesSorted as $upload) {
			
			$counter++;
			$thumbnailLink = '';
			$primarylink = getRelPath(GSDATAUPLOADPATH).$urlPath. rawurlencode($upload['name']);
			
			echo '<tr class="all '.$upload['type'].'" >';
			echo '<td class="imgthumb" >';

			// handle images
			if ($upload['type'] == 'image') {
				$gallery           = 'rel="fancybox_i"';
				$pathlink          = 'image.php?i='.rawurlencode($upload['name']).'&amp;path='.$subPath;
				$thumbLink         = $urlPath.'thumbsm.'.$upload['name'];
				$thumbLinkEncoded  = $urlPath.'thumbsm.'.rawurlencode($upload['name']);
				$thumbLinkExternal = $urlPath.'thumbnail.'.$upload['name'];
				$ext = getFileExtension($upload['name']);

				// get thumbsm
				if (!file_exists(GSTHUMBNAILPATH.$thumbLink) || isset($_REQUEST['regenthumbsm'])) {					
					$imgSrc = '<img class="'.$ext.'" src="inc/thumb.php?src='. $urlPath . rawurlencode($upload['name']) .'&amp;dest='. $thumbLinkEncoded .'&amp;f=1&x='.$thumbsm_w.'&y='.$thumbsm_h.'" />';
				} else {
					$imgSrc = '<img class="'.$ext.'" src="'.tsl($SITEURL).getRelPath(GSTHUMBNAILPATH). $thumbLinkEncoded .'" />';
				}

				// thumbnail link lightbox
				echo '<a href="'. tsl($SITEURL).getRelPath($path). rawurlencode($upload['name']) .'" title="'. rawurlencode($upload['name']) .'" rel="fancybox_i" >'.$imgSrc.'</a>';

				# get external thumbnail link
				# if not exist generate it
				if (!file_exists(GSTHUMBNAILPATH.$thumbLinkExternal) || isset($_REQUEST['regenthumbnail'])) {
					genStdThumb($subPath,$upload['name']);					
				}
				
				$thumbnailLink = '<a href="'.tsl($SITEURL).getRelPath(GSTHUMBNAILPATH).$thumbLinkExternal.'" class="label label-ghost thumblinkexternal" data-fileurl="'.getRelPath(GSTHUMBNAILPATH).$thumbLinkExternal.'">'.i18n_r('THUMBNAIL').'</a>';

			} else {
				// other files
				$gallery      = '';
				$controlpanel = '';
				$pathlink     = tsl($SITEURL).getRelPath($path) . $upload['name'];
			}
			
			// name column linked
			echo '</td><td class="break">'.getUploadIcon($upload['name']).'<a title="'.i18n_r('VIEW_FILE').': '. htmlspecialchars($upload['name']) .'" href="'. $pathlink .'" class="primarylink" data-fileurl="'.$primarylink.'">'.htmlspecialchars($upload['name']) .'</a>'.$thumbnailLink.'</td>';
			
			// size column
			echo '<td class="file_size right"><span>'. $upload['size'] .'</span></td>';
     
			// file perms column
			if ($showperms) {
				$filePerms = substr(sprintf('%o', fileperms($path.$upload['name'])), -4);
				if($isUnixHost){
					$fileOwner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path.$upload['name'])) : '';
					$fileOwnerName = isset($fileOwner['name']) ? $fileOwner['name'] : '';
				} else {
					$fileOwnerName = getenv('USERNAME');
				}
				echo '<td style="width:70px;text-align:right;"><span>'.$fileOwnerName.'/'.$filePerms.'</span></td>';
			}

			echo '<td class="file_date right"><span class="'.(dateIsToday($upload['date']) ? 'datetoday' : '').'">'. output_date($upload['date']) .'</span></td>';			
			// delete
			echo '<td class="delete">';
			if($allowdelete) echo '<a class="delconfirm" title="'.i18n_r('DELETE_FILE').': '. htmlspecialchars($upload['name']) .'" href="deletefile.php?file='. rawurlencode($upload['name']) . '&amp;path=' . $urlPath . '&amp;nonce='.get_nonce("delete", "deletefile.php").'">&times;</a>';
			echo '</td></tr>';
		}
	}
	exec_action('file-extras'); // @hook file-extras after file list table rows
	echo '</tbody></table>';
	
	if ($counter > 0) { 
		$sizedesc = '('. fSize($totalsize) .')';
	} else {
		$sizedesc = '';
	}
	$totalcount = (int)$counter+(int)$foldercount;
	echo '<p><em><b><span id="pg_counter">'. $totalcount .'</span></b> '.i18n_r('TOTAL_FILES').' '.$sizedesc.'</em></p>';
	
	?>
		</div>
		</div>
	</div>
	<?php if($allowupload){ ?>
	<div id="sidebar" >
	<?php include('template/sidebar-files.php'); ?>
	</div>
	<?php } ?>
	
</div>
<?php get_template('footer'); ?>

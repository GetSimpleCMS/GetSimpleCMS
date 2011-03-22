<?php
/**
 * Upload Files
 *
 * Displays and uploads files to the website
 *
 * @package GetSimple
 * @subpackage Files
 * @todo Remove relative paths
 */
 
// Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();


$path = (isset($_GET['path'])) ? "../data/uploads/".$_GET['path'] : "../data/uploads/";
$subPath = (isset($_GET['path'])) ? $_GET['path'] : "";
$subFolder = (empty($subPath)) ? "" : $subPath."/";
$path = tsl($path);
// check if host uses Linux (used for displaying permissions
$isUnixHost = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? false : true);

// if a file was uploaded
if (isset($_FILES['file'])) {
	$uploadsCount = count($_FILES['file']['name']);
	if($uploadsCount > 0) {
	 $errors = array();
	 $messages = array();
	 for ($i=0; $i < $uploadsCount; $i++) {
		if ($_FILES["file"]["error"][$i] > 0)	{
			$errors[] = i18n_r('ERROR_UPLOAD');
		} else {
			
			//set variables
			$count = '1';
			$file_loc = $path . clean_img_name(to7bit($_FILES["file"]["name"][$i]));
			$base = clean_img_name(to7bit($_FILES["file"]["name"][$i]));
			
			//prevent overwriting
			while ( file_exists($file_loc) ) {
				$file_loc = $path . $count.'-'. clean_img_name(to7bit($_FILES["file"]["name"][$i]));
				$base = $count.'-'. clean_img_name(to7bit($_FILES["file"]["name"][$i]));
				$count++;
			}
			
			//create file
			move_uploaded_file($_FILES["file"]["tmp_name"][$i], $file_loc);
			
			//run file upload hook
			exec_action('file-uploaded');
			
			//successfull message
			$messages[] = i18n_r('FILE_SUCCESS_MSG').': <a href="'. $SITEURL .'data/uploads/'.$subFolder.$base.'">'. $SITEURL .'data/uploads/'.$subFolder.$base.'</a>';
		}
	 }
	 // after uploading all files process messages
		if(sizeof($messages) != 0) { 
			foreach($messages as $msg) {
				$success .= $msg.'<br />';
			}
		}
		if(sizeof($errors) != 0) {
			foreach($errors as $msg) {
				$error .= $msg.'<br />';
			}
		}
	}
}
// if creating new folder
if (isset($_GET['newfolder'])) {
	$newfolder = $_GET['newfolder'];
	// check for invalid chars
	$cleanname = clean_url(to7bit($newfolder, "UTF-8"));
	if (file_exists($path.$cleanname) || $cleanname=='') {
			$error = i18n_r('ERROR_FOLDER_EXISTS');
	} else {
		if (defined('GSCHMOD')) { 
			$chmod_value = GSCHMOD; 
		} else {
			$chmod_value = 0755;
		}
		if (mkdir($path . $cleanname, $chmod_value)) {
			//create folder for thumbnails
			$thumbFolder = GSTHUMBNAILPATH.$subFolder.$cleanname;
			if (!(file_exists($thumbFolder))) { mkdir($thumbFolder, $chmod_value); }
			$success = sprintf(i18n_r('FOLDER_CREATED'), $cleanname);
		}	else { 
			$error = i18n_r('ERROR_CREATING_FOLDER'); 
		}
	}
}
?>
<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('FILE_MANAGEMENT')); ?>

	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo i18n_r('FILE_MANAGEMENT'); ?></h1>
	<?php include('template/include-nav.php');?>
	<?php include('template/error_checking.php');?>
	<div class="bodycontent">
	<div id="maincontent">
		<div class="main" >
		<h3 class="floated"><?php echo i18n('UPLOADED_FILES'); ?><span id="filetypetoggle">&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo i18n('SHOW_ALL'); ?></span></h3>
		<div id="file_load">
		<?php
			$count="0";
      		$dircount="0";
			$counter = "0";
			$totalsize = 0;
			$filesArray = array();
      		$dirsArray = array();
      
			$filenames = getFiles($path);
			if (count($filenames) != 0) { 
				foreach ($filenames as $file) {
					if ($file == "." || $file == ".." || $file == ".htaccess" ){
            // not a upload file
          	} elseif (is_dir($path . $file)) {
            $dirsArray[$dircount]['name'] = $file;
            $dircount++;
					} else {
						$filesArray[$count]['name'] = $file;
							$ext = substr($file, strrpos($file, '.') + 1);
						$extention = get_FileType($ext);
						$filesArray[$count]['type'] = $extention;
						clearstatcache();
						$ss = @stat($path . $file);
						$filesArray[$count]['date'] = @date('M j, Y',$ss['ctime']);
						$filesArray[$count]['size'] = fSize($ss['size']);
						$totalsize = $totalsize + $ss['size'];
						$count++;
					}
				}
				$filesSorted = subval_sort($filesArray,'name');
        $dirsSorted = subval_sort($dirsArray,'name');
			}
			echo '<div class="edit-nav" >';
			echo '<select id="imageFilter">';
			echo '<option value="All">'.i18n_r('SHOW_ALL').'</option>';
			if (count($filesSorted) > 0) {
				foreach ($filesSorted as $filter) {
					$filterArr[] = $filter['type'];
				}
				if (count($filterArr) != 0) { 
					$filterArray = array_unique($filterArr);
					$filterArray = subval_sort($filterArray,'type');
					foreach ($filterArray as $type) {
						
						# check for image type
						if (strstr($type, ' Images')) { 
							$typeCleaned = 'Images';
							$typeCleaned_2 = str_replace(' Images', '', $type);
						} else {
							$typeCleaned = $type;
							$typeCleaned_2 = $type;
						}
						 
						echo '<option value="'.$typeCleaned.'">'.$typeCleaned_2.'</option>';
					}
				}
			}
			echo '</select><div class="clear" ></div></div>';

     
      $pathParts = explode("/",$subPath);
      $urlPath = null;
     
      echo '<div class="h5 clearfix"><div class="crumbs">/ <a href="upload.php">uploads</a> / ';

      foreach ($pathParts as $pathPart){
	       if ($pathPart!=''){
	          $urlPath .= $pathPart.'/';
	          
	          echo '<a href="?path='.$urlPath.'">'.$pathPart.'</a> / ';
	       }
      }
      echo '</div> <div id="new-folder">
      	<a href="#" id="createfolder">'.i18n_r('CREATE_FOLDER').'</a>
				<form action="upload.php">&nbsp;<input type="hidden" name="path" value="'.$subPath.'" /><input type="text" class="text" name="newfolder" id="foldername" /> <input type="submit" class="submit" value="'.i18n_r('CREATE_FOLDER').'" />&nbsp; <a href="#" class="cancel">'.i18n_r('CANCEL').'</a></form>
			</div></div>';
      
			
			
     echo '<table class="highlight" id="imageTable">'; 
     echo '<tr><th class="imgthumb" ></th><th>'.i18n_r('FILE_NAME').'</th><th style="text-align:right;">'.i18n_r('FILE_SIZE').'</th>';
     if (defined('GSDEBUG')){
     	echo '<th style="text-align:right;">'.i18n_r('PERMS').'</th>';
		$cols='4';
     } else {
     	$cols='3';
     }
     echo '<th style="text-align:right;">'.i18n_r('DATE').'</th><th></th></tr>';  
     if (count($dirsSorted) != 0) {
        foreach ($dirsSorted as $upload) {
        	
        	# check to see if folder is empty
        	$directory_delete = null;
        	if ( check_empty_folder($path.$upload['name']) ) {  
						$directory_delete = '<a class="delconfirm" title="'.i18n_r('DELETE_FOLDER').': '. $upload['name'] .'" href="deletefile.php?path='.$urlPath.'&amp;folder='. $upload['name'] . '&amp;nonce='.get_nonce("delete", "deletefile.php").'">X</a>';
					}
        	
          echo '<tr class="All folder" >';
          echo '<td class="imgthumb" ></td><td colspan="'.$cols.'">';
        
          $adm = substr($path . $upload['name'] ,  16); 
          echo '<img src="template/images/folder.png" width="11" /> <a href="upload.php?path='.$adm.'" ><strong>'.$upload['name'].'</strong></a>';
                   
          echo '</td>';
          echo '<td class="delete" >'.$directory_delete.'</td>';
          echo '</tr>';
        }
     }
			if (count($filesSorted) != 0) { 			
				foreach ($filesSorted as $upload) {
					$counter++;
					if ($upload['type'] == i18n_r('IMAGES') .' Images') {
						$cclass = 'iimage';
					} else {
						$cclass = '';
					}
					echo '<tr class="All '.$upload['type'].' '.$cclass.'" >';
					echo '<td class="imgthumb" >';
					if ($upload['type'] == i18n_r('IMAGES') .' Images') {
						$gallery = 'rel="facybox"';
						$pathlink = 'image.php?i='.$upload['name'].'&amp;path='.$subPath;
						$thumbLink = $urlPath.'thumbsm.'.$upload['name'];
						if (file_exists('../data/thumbs/'.$thumbLink)) {
							$imgSrc='<img src="../data/thumbs/'. $thumbLink .'" />';
						} else {
							$imgSrc='<img src="inc/thumb.php?src='. $urlPath . $upload['name'] .'&amp;dest='. $thumbLink .'&amp;x=65&amp;f=1" />';
						}
						echo '<a href="'. $path . $upload['name'] .'" title="'. $upload['name'] .'" rel="facybox" >'.$imgSrc.'</a>';
					} else {
						$gallery = '';
						$controlpanel = '';
						$pathlink = $path . $upload['name'];
					}
					echo '</td><td><a title="'.i18n_r('VIEW_FILE').': '. htmlspecialchars($upload['name']) .'" href="'. $pathlink .'" class="primarylink">'.htmlspecialchars($upload['name']) .'</a></td>';
					echo '<td style="width:80px;text-align:right;" ><span>'. $upload['size'] .'</span></td>';
             
		            
					if ($isUnixHost && defined('GSDEBUG')) {
						$filePerms = substr(sprintf('%o', fileperms($path.$upload['name'])), -4);
						$fileOwner = null;
						if (function_exists('posix_getpwuid')) {
							$fileOwner = posix_getpwuid(fileowner($path.$upload['name']));
						}
						if (($filePerms) && ($fileOwner['name'])){
							echo '<td style="width:70px;text-align:right;"><span>'.$fileOwner['name'].'/'.$filePerms.'</span></td>';
						}
					}
					
					echo '<td style="width:85px;text-align:right;" ><span>'. shtDate($upload['date']) .'</span></td>';
					echo '<td class="delete" ><a class="delconfirm" title="'.i18n_r('DELETE_FILE').': '. htmlspecialchars($upload['name']) .'" href="deletefile.php?file='. $upload['name'] . '&amp;path=' . $urlPath . '&amp;nonce='.get_nonce("delete", "deletefile.php").'">X</a></td>';
					echo '</tr>';
					exec_action('file-extras');
				}
			}
			echo '</table>';
			echo '<p><em><b>'. $counter .'</b> '.i18n_r('TOTAL_FILES').' ('. fSize($totalsize) .')</em></p>';
		?>	
		</div>
		</div>
	</div>
		<div id="sidebar" >
		<?php include('template/sidebar-files.php'); ?>
		</div>	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

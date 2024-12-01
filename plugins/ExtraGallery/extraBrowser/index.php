<?php
function stopMessage($message){
	echo '<div class="message">'.$message . '</div>';
	die;
}

require_once('../../../gsconfig.php');
$admin = defined('GSADMIN') ? GSADMIN : 'admin';

require_once("../../../${admin}/inc/common.php");
$loggedin = cookie_check();

if (!$loggedin) 
	stopMessage('Not logged in!');
	
if(!defined('IN_GS')){ 
	stopMessage('you cannot load this page directly.'); 
}

// check for csrf
if (!defined('GSNOCSRF') || (GSNOCSRF == FALSE) ) {
    $nonce = $_GET['nonce'];
    if(!check_nonce($nonce, 'browser', 'ExtraBrowser')) {
        stopMessage('CSRF detected!');
    }
}

require_once('../constants.php');

$folderImgSrc = '../plugins/ExtraGallery/img/folder.png';
$thumbSrc = EG_AJAXURL . '?nonce='.get_nonce("ajax", "ExtraGallery").'&mode=thumb-admin&img=';
$pluginId = EG_ID; //used for localization 


i18n_merge($pluginId, substr($LANG,0,2)) || i18n_merge($pluginId,'en');

if (isset($_GET['path'])) {
	$subPath = trim(preg_replace('/\.+\//','',$_GET['path']), '/'); //remove trailing multiple //
	$path = GSDATAUPLOADPATH.$subPath;
} else {
	$subPath = '';
	$path = GSDATAUPLOADPATH;
}
$path = tsl($path); //add trailing slash if not exists

$subPath = $subPath ? tsl($subPath) : $subPath;

$dirsArray = array();
$filesArray = array();

$dir_handle = @opendir($path) or stopMessage(i18n_r($pluginId.'/EB_FOLDER_NOT_FOUND'));
while ($file = readdir($dir_handle)) {
	if ($file == "." || $file == ".." || $file == ".htaccess" ){
		// not a upload file
	} elseif (is_dir($path . $file)) {
		$folder =  $file;
		$dirFiles = scandir($path . $folder);
		$dirFiles = is_array($dirFiles) ? $dirFiles : array();
		
		$dirsArray[$folder] = array('path' => $subPath.$folder, 'num' => 0);
		
		//count images in directory
		for ($i = 0; $i < count($dirFiles); $i++) {
			$file = $dirFiles[$i];
			if ($file == "." || $file == ".." || $file == ".htaccess" )
				continue;
				
			$ext = @strtolower(substr($file, strrpos($file, '.') + 1));
			if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
				$dirsArray[$folder]['num']++;
			}
		}
	} else {
		$ext = @strtolower(substr($file, strrpos($file, '.') + 1));
		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
			$ss = @stat($path . $file);
			list($width,$height) = getimagesize($path . $file);
			$filesArray[] = array('filename' => $file, 'filepath' => $subPath.$file,  'size' => fSize($ss['size']), 'width' => $width, 'height' => $height);
		}
	}
}

asort($filesArray);
asort($dirsArray);

if (!count($dirsArray) && !count($filesArray))
	stopMessage(i18n_r($pluginId.'/EB_FOLDER_EMPTY'));

	echo '<ul>';

if (count($dirsArray)){
	foreach ($dirsArray as $folderName => $val) {
		?><li class="folder" data-path="<?php echo $val['path']; ?>">
	<div class="img">
		<img src="<?php echo $folderImgSrc ?>"  />
	</div>
	<div class="details">
		<?php i18n($pluginId.'/EB_IMAGES_COUNT') ?> <?php echo $val['num']; ?> 
	</div>
	<div class="filename"><?php echo $folderName; ?></div>
</li><?php
	}
}

if (count($filesArray)){
	foreach ($filesArray as $f => $file) {
		?><li title="<?php echo $file['filename']; ?>" data-filepath="<?php echo $file['filepath']; ?>" data-width="<?php echo $file['width']; ?>" data-height="<?php echo $file['height']; ?>">
				<div class="img">
					<img src="<?php echo $thumbSrc.urlencode($file['filepath']); ?>"  />
				</div>
				<div class="selected-icon"></div>
				<div class="details"><?php echo $file['width'].'x'.$file['height'].' - '.$file['size']; ?></div>
				<div class="filename"><?php echo $file['filename']; ?></div>
			</li><?php
	}
}
echo '</ul>';


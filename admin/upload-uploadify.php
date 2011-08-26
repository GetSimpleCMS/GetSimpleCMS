<?php
/**
 * Upload Files Ajax
 *
 * Ajax action file for jQuery uploader
 *
 * @package GetSimple
 * @subpackage Files
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

if (!defined('GSIMAGEWIDTH')) {
	$width = 200; //New width of image  	
} else {
	$width = GSIMAGEWIDTH;
}
	
if ($_POST['sessionHash'] === $SESSIONHASH) {
	if (!empty($_FILES)){
		
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$name = clean_img_name(to7bit($_FILES['Filedata']['name']));
		$targetPath = (isset($_POST['path'])) ? GSDATAUPLOADPATH.$_POST['path']."/" : GSDATAUPLOADPATH;

		$targetFile =  str_replace('//','/',$targetPath) . $name;
		
		//validate file
		if (validate_safe_file($tempFile, $_FILES["Filedata"]["name"], $_FILES["Filedata"]["type"])) {
			move_uploaded_file($tempFile, $targetFile);
			if (defined('GSCHMOD')) {
				chmod($targetFile, GSCHMOD);
			} else {
				chmod($targetFile, 0644);
			}
			exec_action('file-uploaded');
		} else {
			i18n('ERROR_UPLOAD');
			exit;
		}
		
		   
		$ext = lowercase(pathinfo($name,PATHINFO_EXTENSION));	
		
		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' )	{
			
			$path = (isset($_POST['path'])) ? $_POST['path']."/" : "";
			$thumbsPath = GSTHUMBNAILPATH.$path;
			
			if (!(file_exists($thumbsPath))) {
				if (defined('GSCHMOD')) { 
					$chmod_value = GSCHMOD; 
				} else {
					$chmod_value = 0755;
				}
				mkdir($thumbsPath, $chmod_value);
			}
			echo $path;
			echo " ".$thumbsPath;
			
			//thumbnail for post
			$imgsize = getimagesize($targetFile);
			
			switch(lowercase(substr($targetFile, -3))){
			    case "jpg":
			        $image = imagecreatefromjpeg($targetFile);    
			    break;
			    case "png":
			        $image = imagecreatefrompng($targetFile);
			    break;
			    case "gif":
			        $image = imagecreatefromgif($targetFile);
			    break;
			    default:
			        exit;
			    break;
			}
			  
			$height = $imgsize[1]/$imgsize[0]*$width; //This maintains proportions
			
			$src_w = $imgsize[0];
			$src_h = $imgsize[1];
			
			$picture = imagecreatetruecolor($width, $height);
			imagealphablending($picture, false);
			imagesavealpha($picture, true);
			$bool = imagecopyresampled($picture, $image, 0, 0, 0, 0, $width, $height, $src_w, $src_h); 
			
			if($bool)	{	
				$thumbnailFile = $thumbsPath . "thumbnail." . $name;
				
			    switch(lowercase(substr($targetFile, -3))) {
			        case "jpg":
			            header("Content-Type: image/jpeg");
			            $bool2 = imagejpeg($picture,$thumbnailFile,85);
			        break;
			        case "png":
			            header("Content-Type: image/png");
			            imagepng($picture,$thumbnailFile);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
			            imagegif($picture,$thumbnailFile);
			        break;
			    }
			}
			
			imagedestroy($picture);
			imagedestroy($image);
			
			
			//small thumbnail for image preview
			$width = 65; //New width of image    
			$height = $imgsize[1]/$imgsize[0]*$width; //This maintains proportions
			
			$src_w = $imgsize[0];
			$src_h = $imgsize[1];
			    
			
			$picture = imagecreatetruecolor($width, $height);
			imagealphablending($picture, false);
			imagesavealpha($picture, true);
			$bool = imagecopyresampled($picture, $image, 0, 0, 0, 0, $width, $height, $src_w, $src_h); 
			
			if($bool)	{
				$thumbsmFile = $thumbsPath . "thumbsm." . $name;
				
			    switch(lowercase(substr($targetFile, -3))) {
			        case "jpg":
			            header("Content-Type: image/jpeg");
			            $bool2 = imagejpeg($picture,$thumbsmFile,85);
			        break;
			        case "png":
			            header("Content-Type: image/png");
			            imagepng($picture,$thumbsmFile);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
			            imagegif($picture,$thumbsmFile);
			        break;
			    }
			}
			
			imagedestroy($picture);
			imagedestroy($image);
		}	
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
} else {
	echo 'Wrong session hash!';
}
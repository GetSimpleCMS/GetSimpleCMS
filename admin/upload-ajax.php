<?php

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

if (!defined('GSIMAGEWIDTH')) {
	$width = 200; //New width of image  	
} else {
	$width = GSIMAGEWIDTH;
}
	
if ($_REQUEST['sessionHash'] === $SESSIONHASH) {
	if (!empty($_FILES))
	{
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$name = clean_img_name($_FILES['Filedata']['name']);
		$targetPath = GSDATAUPLOADPATH;
		$targetFile =  str_replace('//','/',$targetPath) . $name;
		
		move_uploaded_file($tempFile, $targetFile);
		$ext = strtolower(substr($name, strrpos($name, '.') + 1));	
		
		if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' )
		{
			//thumbnail for post
			$imgsize = getimagesize($targetFile);
			
			switch(strtolower(substr($targetFile, -3)))
			{
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
			
			if($bool)
			{
			    switch(strtolower(substr($targetFile, -3)))
				{
			        case "jpg":
			            header("Content-Type: image/jpeg");
			            $bool2 = imagejpeg($picture,"../data/thumbs/thumbnail.".$name,85);
			        break;
			        case "png":
			            header("Content-Type: image/png");
			            imagepng($picture,"../data/thumbs/thumbnail.".$name);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
			            imagegif($picture,"../data/thumbs/thumbnail.".$name);
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
			
			if($bool)
			{
			    switch(strtolower(substr($targetFile, -3)))
				{
			        case "jpg":
			            header("Content-Type: image/jpeg");
			            $bool2 = imagejpeg($picture,"../data/thumbs/thumbsm.".$name,85);
			        break;
			        case "png":
			            header("Content-Type: image/png");
			            imagepng($picture,"../data/thumbs/thumbsm.".$name);
			        break;
			        case "gif":
			            header("Content-Type: image/gif");
			            imagegif($picture,"../data/thumbs/thumbsm.".$name);
			        break;
			    }
			}
			
			imagedestroy($picture);
			imagedestroy($image);
		}	
		echo "1";
	}
} 
else 
{
	echo "0";	
}
?>
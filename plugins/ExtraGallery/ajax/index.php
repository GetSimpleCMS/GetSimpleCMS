<?php
///this file is for external ajax operations: deleting gallery, reading thumbnail, validating gallery name
require_once('../../../gsconfig.php');
$admin = defined('GSADMIN') ? GSADMIN : 'admin';

$load['plugin'] = true; //needed by EGTools
require_once("../../../${admin}/inc/common.php");
$loggedin = cookie_check();

if (!$loggedin) 
	die('Not logged in!');
	
if(!defined('IN_GS')){ 
	die('you cannot load this page directly.'); 
}

// check for csrf
if (!defined('GSNOCSRF') || (GSNOCSRF == FALSE) ) {
    $nonce = $_GET['nonce'];
    if(!check_nonce($nonce, "ajax", "ExtraGallery")) {
        die('CSRF detected!');
    }
}

if ( !in_array($_GET['mode'], array('gallery-delete', 'gallery-validate-name', 'image-crop',  'thumb-admin', 'thumb-create')) )
	die('unknown mode');

require_once('../constants.php');
require_once('../EGImage.php');
require_once('../EGSettings.php');
require_once('../EGGallery.php');
require_once('../EGTools.php');
require_once('../EGStorage.php');

switch($_GET['mode']){

	case 'gallery-delete':{ //validate gallery name is unique
        $instance = $_GET['instance']; 
        $name = $_GET['name']; //gall name
        
        if (EGGallery::delete($instance, $name)){
            EGTools::cleanUnusedThumbs();
            echo json_encode(1);
        }
        else
            echo json_encode(0);

		break;
	}	
    
    case 'gallery-validate-name':{ //validate gallery name is unique
        $instance = $_GET['instance']; 
        $name = $_GET['name']; //gall name
        
        if (EGGallery::validateGalleryName($instance, $name)){
            echo json_encode(1);
        }
        else{
            echo json_encode(0);
        }
		break;
	}		
    
    case 'thumb-admin':{ //renders thumbnail for image, used when image is added to gallery
		$img = @$_GET['img'];

		try {
			if (!filepath_is_safe(GSDATAUPLOADPATH.$img, GSDATAUPLOADPATH))
				throw new Exception('Source image not exists!');
			
			$sourcePath = GSDATAUPLOADPATH.$img;
			
			//file exists and its modification date is older than source
			$cachedExists = file_exists(EG_ADMINTHUMBS . $img) && @filemtime($sourcePath) <= filemtime(EG_ADMINTHUMBS . $img);
			
			if ($cachedExists){
				$sourcePath = EG_ADMINTHUMBS . $img;
			}

			$t = new EGImage($sourcePath);
			
			if (!$cachedExists){
				$t->resize(180, 120, 'fit', true);
				
				if ( !file_exists(dirname(EG_ADMINTHUMBS . $img)) ){ //directory not exists, prepare one, requsivly
					mkdir(dirname(EG_ADMINTHUMBS . $img), 0755, true);
				}
				
				$t->save(EG_ADMINTHUMBS . $img, 92);
			}
			
			$t->render(172800); //two days
		} catch (Exception $e) {
			EGImage::renderError( $e->getMessage(), 180, 120 );
		}
		break;
	}		
	
	case 'image-crop':{ //renders image, for cropping to fit in dialog size
		$img = $_GET['img'];
		$width = @(int)$_GET['w'];
		$height = @(int)$_GET['h'];
		
		try {
			if (!filepath_is_safe(GSDATAUPLOADPATH.$img, GSDATAUPLOADPATH))
				throw new Exception('Source image not exists!');

			if ($width <=0 || $height <=0)
				throw new Exception('Wrong size passed!');

			$t = new EGImage(GSDATAUPLOADPATH.$img);
			$t->resize($width, $height, 'fit', true);
			$t->render(0, 92); //no cache
			
		} catch (Exception $e) {
			EGImage::renderError( $e->getMessage() );
		}
		break;
	}	
	case 'thumb-create':{
        try {    
            $instance = $_GET['instance']; 
            $img = $_GET['img']; //filename
            $thumb = (int)$_GET['thumb']; //0 or 1
            $thumbMode = @$_GET['m']; //fill

            if ($thumbMode != 'fill'){
                $thumbMode = null;
                $cX = (int)$_GET['x']; //x
                $cY = (int)$_GET['y']; //y
                $cWidth = (int)$_GET['w']; //how much to cut width
                $cHeight = (int)$_GET['h']; //how much to cut height
            }
            
            //get instance settings
            $settings = EGSettings::load($instance);
        
            if (!$settings)
                throw new Exception('Unknown instance, settings empty!');
            
            if ( !$thumbMode && ( $cWidth <= 0 || $cHeight <= 0 || $cX < 0 || $cY < 0))
                throw new Exception('Wrong crop values passed! ' + $cWidth + ' ' + $cHeight + ' ' + $cX +' ' + $cY );
                
            if ( !$settings['thumbnails'][$thumb]['enabled'] )
                throw new Exception('Wrong thumb number or disabled!');	

            if ( $thumbMode && (!$settings['thumbnails'][$thumb]['width'] || !$settings['thumbnails'][$thumb]['height']) )
                throw new Exception('Cannot fill without width and height specified in thumb settings!');
                
            if (
                !$thumbMode &&
                ($settings['thumbnails'][$thumb]['width'] && $settings['thumbnails'][$thumb]['height']) &&
                (floor($settings['thumbnails'][$thumb]['width'] / $settings['thumbnails'][$thumb]['height'] * 100) / 100 != floor($cWidth / $cHeight * 100) / 100) //cut decimals
            ){
                throw new Exception('Wrong ratio of crop size!');
            }
			
			
            
            $sourcePath = GSDATAUPLOADPATH.$img;
        

            $i = new EGImage($sourcePath);
            
            if (!filepath_is_safe($sourcePath, GSDATAUPLOADPATH))
                throw new Exception('Source image not exists!');
                
            $fileName = basename($sourcePath);
            $extensionPos = strrpos($fileName, '.');
            $targetImg = substr($fileName, 0, $extensionPos ); //construct target path
            $parts = array('');
            
            if ($thumbMode){
                if ( $i->getWidth() < $settings['thumbnails'][$thumb]['width'] || $i->getHeight() < $settings['thumbnails'][$thumb]['height'] )
                    throw new Exception('Image to small to fill!');
            
                $parts[] = $thumbMode;
                $parts[] = $settings['thumbnails'][$thumb]['width'];
                $parts[] = $settings['thumbnails'][$thumb]['height'];
            }
            else{
                $parts[] = $cX;
                $parts[] = $cY;
                $parts[] = $cWidth;
                $parts[] = $cHeight;
                
                //calculate sizes for filename
                if ($settings['thumbnails'][$thumb]['width'] xor $settings['thumbnails'][$thumb]['height']){
                    //autocalculate width and height if one of them is missing
                    if( !$settings['thumbnails'][$thumb]['width'] )
                        $width = $cHeight / $cHeight * $cWidth;
                    else
                        $width = $settings['thumbnails'][$thumb]['width'];

                    if( !$settings['thumbnails'][$thumb]['height'] )
                        $height = $cWidth / $cWidth * $cHeight;
                    else
                        $height = $settings['thumbnails'][$thumb]['height'];
                        
                    if($width / $height != $cWidth / $cHeight ){
                        if( $width / $cWidth > $height / $cHeight ){
                            $width = $height / $cHeight * $cWidth;
                        }else{
                            $height = $width / $cWidth * $cHeight;
                        }
                    }
     
                    $parts[] = floor($width);
                    $parts[] = floor($height);
                }
                else if ($settings['thumbnails'][$thumb]['width'] && $settings['thumbnails'][$thumb]['height']){ 
                    $parts[] = $settings['thumbnails'][$thumb]['width'];
                    $parts[] = $settings['thumbnails'][$thumb]['height'];
                }   
                else{ //not specified sizes for thumb
                    $parts[] = $cWidth;
                    $parts[] = $cHeight;
                }
                
            }
            
            $i->destroy();
            
            $targetImg .= implode('-', $parts).substr($fileName, $extensionPos );
            
            //create full path
            $targetImg = substr($img , 0, strlen($img) - (strlen($fileName))) . $targetImg;
            
            $cachedExists = file_exists(EG_THUMBS . $targetImg) && filemtime($sourcePath) <= filemtime(EG_THUMBS . $targetImg);
            
            $path = EG_THUMBS . $targetImg;
            if (!$cachedExists){
                $path = $sourcePath;
            }

            $t = new EGImage($path);
            
            if (!$cachedExists){
                if ($thumbMode){
                    $t->resize($settings['thumbnails'][$thumb]['width'], $settings['thumbnails'][$thumb]['height'], $thumbMode, true);
                }
                else{
                    $t->crop($cX, $cY, $cWidth, $cHeight);
                    
                    //if thumb size exists
                    if ($settings['thumbnails'][$thumb]['width'] || $settings['thumbnails'][$thumb]['height'])
                        $t->resize($settings['thumbnails'][$thumb]['width'], $settings['thumbnails'][$thumb]['height'], 'fit', true);
                }
                
                if ( !file_exists(dirname(EG_THUMBS . $img)) ){ //directory not exists, prepare one, requsivly
                    if (mkdir(dirname(EG_THUMBS . $img), 0755, true))
						copy(GSPLUGINPATH . 'ExtraGallery/default-htaccess.txt', EG_THUMBS . '.htaccess'); //create htaccess that gives access :)
                }
                $t->save(EG_THUMBS . $targetImg, 95);
            }
            
            echo json_encode($targetImg);
            
        } catch (Exception $e) {
			echo json_encode(array('error' => $e->getMessage()));
		}
		break;
	}

}
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }


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

		$thumb = getimagesize($file);
		debugLog('thumbnail ' . $file);			
		$thumb['width']       = $thumb[0]; unset($thumb[0]); 
		$thumb['height']      = $thumb[1]; unset($thumb[1]);
		$thumb['type']        = $thumb[2]; unset($thumb[2]);
		$thumb['attrib']      = $thumb[3]; unset($thumb[3]);
		$thumb['uploadpath']  = tsl(getRelPath($upload_path,GSTHUMBNAILPATH));
		$thumb['primaryfile'] = GSDATAUPLOADPATH . $thumb['uploadpath'] . $origfile;
		$thumb['primaryurl']  = getUploadURI($origfile,$thumb['uploadpath']);
		$thumb['thumbfile']   = getThumbnailFile(basename($file),$upload_path,'');
		$thumb['thumburl']    = getThumbnailURI(basename($file),$upload_path,'');
		$thumb['thumbtype']   = $thumbtype;
		$thumbs_array[] = $thumb;
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

	generate_thumbnail($file,$subpath,$width);
}

/**
 * generate a thumbnail
 * @param  str  $sub_path upload path
 * @param  str  $file     filename
 * @param  int  $w        desired width
 * @param  int  $h        desired max height, optional, will limit height and adjust width accordingly 
 * @param  boolean $upscale  true, allows image to scale up/zoom to fit thumbnail
 * @return bool            success
 */
function generate_thumbnail($file, $sub_path = '', $w, $h = null, $upscale = false){
	//gd check, do nothing if no gd
	$php_modules = get_loaded_extensions();
	if(!in_arrayi('gd', $php_modules)) return false;

	$sub_path      = tsl($sub_path);
	$upload_folder = GSDATAUPLOADPATH.$sub_path;
	$thumb_folder  = GSTHUMBNAILPATH.$sub_path;

	create_dir($thumb_folder);

	$objImage = new ImageManipulation($upload_folder.$file);
	if ( $objImage->imageok ) {
		if($upscale) $objImage->setUpscale();
		if(isset($h)) $objImage->setImageWidth($w,$h); 
		else{
			$objImage->setImageWidth($w);
			// $objImage->resize($w); // constrains both dimensions to $size, same as setImageWidth($w,$w);
		}
		return $objImage->save($thumb_folder . 'thumbnail.' .$file);
	} else {
		return false;
	}
}


/**
 * ImageManipulation Class
 *
 * @author 	  Tech @ Talk In Code
 * @modified http://getsimple-cms.info
 * @link http://www.talkincode.com/
 * @version   1.0
 * @copyright 2009 Talk In Code
 *
 * @package GetSimple
 * @subpackage Images
 * @uses GD
 */
class ImageManipulation {

	/**
	 * An array to hold the settings for the image. Default values for
	 * images are set here.
	 *
	 * @var array
	 */
	public $image = array('targetx'=>0, 
						  'targety'=>0,
						  'quality'=>75,
						  'upscale'=>false
						);
	
	/**
	 * A boolean value to detect if an image has not been created. This
	 * can be used to validate that an image is viable before trying 
	 * resize or crop.
	 *
	 * @var boolean
	 */
	public $imageok = false;


	public function __destruct() { 
		if(isset($this->image['des']) && is_resource($this->image['des'])) { 
			imagedestroy($this->image['des']); 
		}
	}

    /**
     * Contructor method. Will create a new image from the target file.
	 * Accepts an image filename as a string. Method also works out how
	 * big the image is and stores this in the $image array.
     *
     * @param string $imgFile The image filename.
     */
	public function ImageManipulation($imgfile)
	{
		//detect image format
		//@todo: abstract use mime, and realpathparts not regex
		$this->image["format"] = $this->getFileImageType($imgfile);
		
		// convert image into usable format.
		if ( $this->image["format"] == "JPG" || $this->image["format"] == "JPEG" ) {
			//JPEG
			$this->image["format"] = "JPEG";
			$this->image["src"]    = ImageCreateFromJPEG($imgfile);
		} elseif( $this->image["format"] == "PNG" ){
			//PNG
			$this->image["format"] = "PNG";
			$this->image["src"]    = imagecreatefrompng($imgfile);
		} elseif( $this->image["format"] == "GIF" ){
			//GIF
			$this->image["format"] = "GIF";
			$this->image["src"]    = ImageCreateFromGif($imgfile);
		} elseif ( $this->image["format"] == "WBMP" ){
			//WBMP
			$this->image["format"] = "WBMP";
			$this->image["src"]    = ImageCreateFromWBMP($imgfile);
		} else {
			//DEFAULT
			$this->imageok = false;
			return false;
		}

		// Image is ok
		$this->imageok = true;
		
		// Work out image size
		$this->image['srcfile'] = $imgfile;
		$this->image["sizex"]   = imagesx($this->image["src"]);
		$this->image["sizey"]   = imagesy($this->image["src"]);
		$this->image["ratio"]   = $this->getRatio();
	}

    /**
     * Sets the height of the image to be created. The width of the image
	 * is worked out depending on the value of the height.
     *
     * @param int $height The height of the image.
     * @param int $max optional The max width of the image
     */
	public function setImageHeight($height=100, $max = null)
	{
		//height
		$this->image["sizey_thumb"]  = (int) $height;
		$this->image["sizex_thumb"]  = round($height*$this->image['ratio']);

		if($max) $this->max($max,0);
	}
	
    /**
     * Sets the width of the image to be created. The height of the image
	 * is worked out depending on the value of the width.
     *
     * @param int $size The width of the image.
     * @param int $max optional The max height of the image
     */
	public function setImageWidth($width=100, $max = null)
	{
		//width
		$this->image["sizex_thumb"]  = (int) $width;
		$this->image["sizey_thumb"]  = round($width/$this->image['ratio']);

		if($max) $this->max(0,$max);
	}

	/**
     * This method automatically sets the width and height depending
	 * on the dimensions of the image up to a maximum value.
     *
     * @param int $size The maximum size of the image.
     */
	public function resize($size=100)
	{
		$ratio = $this->image["ratio"];
		// debugLog($ratio);
		if(floor($ratio) > 0){
			$this->image["orientation"] = 'landscape';
			$this->image["sizex_thumb"] = (int) $size;			
			$this->image["sizey_thumb"] = round($size/$ratio);
		}
		else {
			$this->image["orientation"] = 'portrait';		
			$this->image["sizex_thumb"] = round($size*$ratio);			
			$this->image["sizey_thumb"] = (int) $size;			
		}
		// debugLog(print_r($this->image,true));
	}

	/**
	 * set thumb dimensions maximum values 
	 * when using setWidth or setHeight, this lets you set max values for opposites
	 * will recaculate thumb size to fit in these threshholds		
	 * @param  integer $x
	 * @param  integer $y
	 */
	public function max($x,$y = 0){
		if($y>0 && $this->image["sizey_thumb"] > $y){
			// debugLog('maxy');
			$this->image["sizey_thumb"] = $y;
			$this->image["sizex_thumb"] = round($y*$this->image['ratio']);
		}
		else if($x>0 && $this->image["sizex_thumb"] > $x){
			// debugLog('maxx');
			$this->image["sizex_thumb"] = $x;
			$this->image["sizey_thumb"] = round($x/$this->image['ratio']);
		}
	}

	public function getRatio()
	{
		return $this->image["sizex"] / $this->image["sizey"];
	}

	/**
     * This method sets the cropping values of the image. Be sure
	 * to set the height and with of the image if you want the
	 * image to be a certain size after cropping.
     *
     * @param int $x The x coordinates to start cropping from.
     * @param int $y The y coordinates to start cropping from.
	 * @param int $w The width of the crop from the x and y coordinates.
     * @param int $h The height of the crop from the x and y coordinates.
     */
	public function setCrop($x, $y, $w, $h)
	{
		$this->image["targetx"] = $x;
		$this->image["targety"] = $y;
		$this->image["sizex"] = $w;
		$this->image["sizey"] = $h;
	}

	/**
	 * set Upscale
	 * @param bool $bool true:allow thumbs to be scaled up to fit if original is smaller
	 */
	public function setUpscale($bool = true){
		$this->image['upscale'] = $bool;
	}
	
	/**
     * Sets the JPEG output quality.
     *
     * @param int $quality The quality of the JPEG image.
     */
	public function setJpegQuality($quality=75)
	{
		$this->image["quality"] = $quality;
	}

	public function setOutputFormat($format){
		$this->image["format_out"] = $format;
	}

	/**
     * Sets the PNG output quality.
     *
     * @param int $quality The quality of the PNG image.
     */
	public function setPngQuality($quality=0)
	{
        if (PHP_VERSION >= '5.1.2') {		
        	$quality = 9 - min( round($this->quality / 10), 9 );	
			$this->image["pngquality"] = $quality;
		}	
	}
	
	/**
     * Private method to run the imagecopyresampled() function with the parameters that have been set up.
	 * This method is used by the save() and show() methods.
	 * 
	 * change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function
     */
	private function createResampledImage()
	{
		if ( isset($this->image["sizex_thumb"]) && isset($this->image["sizey_thumb"]) ) {	
			// check if thumb is not larger than original	
			if ( $this->image['upscale'] || ($this->image["sizex_thumb"] < $this->image["sizex"] && $this->image["sizey_thumb"] < $this->image["sizey"]) ) {		
				// do thumbnail
				$this->image["des"] = ImageCreateTrueColor($this->image["sizex_thumb"], $this->image["sizey_thumb"]);
				$this->preserveAlpha();
				imagecopyresampled($this->image["des"], $this->image["src"], 0, 0, $this->image["targetx"], $this->image["targety"], $this->image["sizex_thumb"], $this->image["sizey_thumb"], $this->image["sizex"], $this->image["sizey"]);
				return;
			}
		}
		
		$this->image["des"] = ImageCreateTrueColor($this->image["sizex"], $this->image["sizey"]);
		$this->preserveAlpha();
		imagecopyresampled($this->image["des"], $this->image["src"], 0, 0, $this->image["targetx"], $this->image["targety"], $this->image["sizex"], $this->image["sizey"], $this->image["sizex"], $this->image["sizey"]);
			
	}
	
	/**
	 * preserve alpha channel
	 * @return boolean true:enable transparency alpha blending
	 */
	private function preserveAlpha($bool=true){
		imagealphablending($this->image["des"], !$bool);
		imagesavealpha($this->image["des"], $bool);		
	}

	/**
	 * attempt to get the image type from the file extension
	 * @param  str $file filename
	 * @return str       image type PNG,JPG ...
	 */
	private function getFileImageType($file){
		$format  = preg_replace("/.*\.(.*)$/", "\\1", $file);
		$format  = strtoupper($format);
		if(!in_array($format,array('GIF','PNG','JPG','JPEG','WBMP'))) return '';
		return $format;
	}

	/**
     * Shows the image to a browser. Sets the correct image format in a header.
     */
	public function show()
	{
		$this->save("",false);
	}

	/**
     * Saves the image to a given filename, if no filename is given then a default is created.
	 *
	 * @param string $save The new image filename.
     */	
	public function save($file="",$headers = false)
	{

		if(isset($this->image['format_out'])) $format = $this->image["format_out"];
		else{
			// get type from save filename or filein 
			$format = $this->getFileImageType($file);
			if($format == '') $format = $this->image["format"];
			$this->image["format_out"] = $format;
		}

		if($headers){
			header("Content-Type: image/".$format);
			$file = null;
		} else {
			if(empty($file)) {
				$this->image['success'] = false;
				return false;
			}
			$this->image['outfile'] = $file;
		}

		$success = false;
		$this->createResampledImage();

		if ( $format == "GIF" ) {
			// GIF
			// fallback to JPG is not supported
			if(function_exists('imageGIF')) $success = imageGIF($this->image["des"], $file);
			else {
				// gif not supported
				// $format == "JPG"; // fallback?
			}
		}
		else if ($format == "JPG" || $format == "JPEG" ) {
			// JPEG
			$success = imageJPEG($this->image["des"], $file, $this->image["quality"]);
		} elseif ( $format == "PNG" ) {
			// PNG
			$success = imagePNG($this->image["des"], $file);
		} elseif ( $format == "WBMP" ) {
			// WBMP
			$success = imageWBMP($this->image["des"], $file);
		}

		$this->image['success'] = $success;
		return $success;
	}
}

/* ?> */

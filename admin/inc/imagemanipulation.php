<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }


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
						  'pngquality'=>6,
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

		$imageinfo = getimagesize($imgfile);
		// debugLog($imageinfo);
		
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
		$this->image['srcfile']  = $imgfile;
		$this->image['sizex']    = $imageinfo[0];
		$this->image['sizey']    = $imageinfo[1];

		$this->image['channels'] = $imageinfo[2];
		$this->image['bits']     = $imageinfo['bits'];
		$this->image['mime']     = $imageinfo['mime'];
		$this->image['width']    = $this->image["sizex"];
		$this->image['height']   = $this->image["sizey"];
		$this->image["ratio"]    = $this->getRatio();
	}

	public function getImageMemory($adjust = 1.8){
		$image_width    = $this->image['width'];
		$image_height   = $this->image['height'];
		$image_bits     = $this->image['bits'];
		$image_channels = 4; // pngs do not calculate properly, stick to 4
		
		// bpp = (bitdepth) * (channels)
		// bits = (height) * (width) * (bpp)
		// bytes = bits / 8
		$size = round( ($image_width * $image_height * ($image_bits * $image_channels) / 8) ); 
		$cropsize = 0;

		if($this->crop == true){
			// $size = $size*2;
			list($image_width,$image_height) = $this->getCropSize();
			$cropsize += round( ($image_width * $image_height * ($image_bits * $image_channels) / 8) );
		}

		// print_r('src:'.toBytesShorthand($size,'m',true)."<br>");
		// print_r('crop:'.toBytesShorthand($cropsize,'m',true)."<br>");
		$size = $size + $cropsize;
		return $size = $size * $adjust;
	}


	public function getCropSize(){
		if($this->crop == true){
			$width  = $this->image['sizex'] - $this->image['targetx'];
			$height = $this->image['sizey'] - $this->image['targety'];
			return array($width,$height);
		}
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
		$this->crop = true;
		$this->image["targetx"] = (int)$x;
		$this->image["targety"] = (int)$y;
		$this->image["sizex"]   = (int)$w;
		$this->image["sizey"]   = (int)$h;
	}

	/**
	 * set Upscale
	 * @param bool $bool true:allow thumbs to be scaled up to fit if original is smaller
	 */
	public function setUpscale($bool = true){
		$this->image['upscale'] = $bool;
	}

	public function setQuality($quality = 75){
		// debugLog("setting quality: " . $quality);
		$this->setJpegQuality($quality);
		$this->setPngQuality($quality);
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

	/**
     * Sets the PNG output quality.
     *
     * @param int $quality The quality of the PNG image.
     */
	public function setPngQuality($quality=0)
	{
        if (PHP_VERSION >= '5.1.2') {		
        	$quality = 9 - min( round($this->image['quality'] / 10), 9 );	
			$this->image["pngquality"] = $quality;
		}	
	}

	public function setOutputFormat($format){
		$enum = array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'WBMP');
		if(is_int($format) && isset($enum[$format])){
			$format = $enum[$format];
		}
		if(!in_array($format,$enum)) return false;
		$this->image["format_out"] = strtoupper($format);
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
		$this->save("",true);
	}

	/**
     * Saves the image to a given filename, if no filename is given then a default is created.
	 *
	 * @param string $save The new image filename.
     */	
	public function save($file=null, $show = false)
	{

		// debugDie(print_r($this,true));

		if(isset($file) && empty($file)) $file = null;
		$showsave = $show && isset($file);

		if(isset($this->image['format_out'])) $format = $this->image["format_out"];
		else{
			// get type from save filename or filein 
			$format = $this->getFileImageType($file);
			if($format == '') $format = $this->image["format"];
			$this->image["format_out"] = $format;
		}

		if($show){
			// if showing output headers
			header("Content-Type: image/".$format);
		} else {
			if(empty($file)) {
				$this->image['success'] = false;
				return false;
			}
		}
		
		$this->image['outfile'] = $file;

		$success = false;
		$this->createResampledImage();

		// If $file is null these will output images instead of saving them
		if ( $format == "GIF" ) {
			// GIF
			// gif might not supported in certain versions of GD
			if(function_exists('imageGIF')){
				$success = imageGIF($this->image["des"], $file);
			}
			else {
				$success = false;
				debugLog(__FUNCTION__ . 'unsupported output format: ' . $format);
				$this->image['success'] = $success; 
				return $success;
			}
		}
		elseif ($format == "JPG" || $format == "JPEG" ) {
			// JPEG
			$success = imageJPEG($this->image["des"], $file, $this->image["quality"]);
		}
		elseif ( $format == "PNG" ) {
			// PNG
			$success = imagePNG($this->image["des"], $file, $this->image["pngquality"]);
		}
		elseif ( $format == "WBMP" ) {
			// WBMP
			$success = imageWBMP($this->image["des"], $file);
		}
		else{
			$success = false;
			debugLog(__FUNCTION__ . 'invalid output format');
			$this->image['success'] = $success;
			return $success;
		}

		$this->image['success'] = $success;
		
		// if saved and we also want to show, readfile
		if($showsave) readfile($this->image['outfile']);

		return $success;
	}
}

/* ?> */

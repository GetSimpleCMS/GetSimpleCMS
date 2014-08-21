<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Generate standard thumbnails
 * @param  string $path path to image
 * @param  string $name file name
 * @uses   GD
 */

function genStdThumb($path,$name){

	//gd check
	$php_modules = get_loaded_extensions();
	if(!in_arrayi('gd', $php_modules)) return;

	if (!getDef('GSIMAGEWIDTH')) {
		$width = 200; //New width of image  	
	} else {
		$width = GSIMAGEWIDTH;
	}

	$ext = getFileExtension($name);
	
	if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png' )	{
		
		$thumbsPath = GSTHUMBNAILPATH.$path;
		
		if (!(file_exists($thumbsPath))) {
			if (getDef('GSCHMOD')) {
				$chmod_value = GSCHMOD; 
			} else {
				$chmod_value = 0755;
			}
			echo $thumbsPath;
			mkdir($thumbsPath, $chmod_value);
		}
	}

	$targetFile = GSDATAUPLOADPATH.$path.$name;
	
	//thumbnail for post
	$imgsize = getimagesize($targetFile);
		
	switch($ext){
			case "jpeg":
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
					return;
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
	            $bool2 = imagejpeg($picture,$thumbnailFile,85);
	        break;
	        case "png":
	            imagepng($picture,$thumbnailFile);
	        break;
	        case "gif":
	            imagegif($picture,$thumbnailFile);
	        break;
	    }
	}
	
	imagedestroy($picture);
	imagedestroy($image);

	return true;
}


/**
 * ImageManipulation
 *
 * @author 	  Tech @ Talk In Code
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
							'quality'=>75);
	
	/**
	 * A boolean value to detect if an image has not been created. This
	 * can be used to validate that an image is viable before trying 
	 * resize or crop.
	 *
	 * @var boolean
	 */
	public $imageok = false;
	
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
		$this->image["format"] = preg_replace("/.*\.(.*)$/", "\\1", $imgfile);
		$this->image["format"] = strtoupper($this->image["format"]);
		
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
			return false;
		}

		// Image is ok
		$this->imageok = true;
		
		// Work out image size
		$this->image["sizex"]  = imagesx($this->image["src"]);
		$this->image["sizey"] = imagesy($this->image["src"]);
	}

    /**
     * Sets the height of the image to be created. The width of the image
	 * is worked out depending on the value of the height.
     *
     * @param int $height The height of the image.
     */
	public function setImageHeight($height=100)
	{
		//height
		$this->image["sizey_thumb"] = $height;
		$this->image["sizex_thumb"]  = ($this->image["sizey_thumb"]/$this->image["sizey"])*$this->image["sizex"];
	}
	
    /**
     * Sets the width of the image to be created. The height of the image
	 * is worked out depending on the value of the width.
     *
     * @param int $size The width of the image.
     */
	public function setImageWidth($width=100)
	{
		//width
		$this->image["sizex_thumb"]  = $width;
		$this->image["sizey_thumb"] = ($this->image["sizex_thumb"]/$this->image["sizex"])*$this->image["sizey"];
	}

	/**
     * This method automatically sets the width and height depending
	 * on the dimensions of the image up to a maximum value.
     *
     * @param int $size The maximum size of the image.
     */
	public function resize($size=100)
	{
		if ( $this->image["sizex"] >= $this->image["sizey"] ) {
			$this->image["sizex_thumb"]  = $size;
			$this->image["sizey_thumb"] = ($this->image["sizex_thumb"]/$this->image["sizex"])*$this->image["sizey"];
		} else {
			$this->image["sizey_thumb"] = $size;
			$this->image["sizex_thumb"]  = ($this->image["sizey_thumb"]/$this->image["sizey"])*$this->image["sizex"];
		}
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
     * Sets the JPEG output quality.
     *
     * @param int $quality The quality of the JPEG image.
     */
	public function setJpegQuality($quality=75)
	{
		//jpeg quality
		$this->image["quality"] = $quality;
	}

	/**
     * Shows the image to a browser. Sets the correct image format in a header.
     */
	public function show()
	{
		//show thumb
		header("Content-Type: image/".$this->image["format"]);

		$this->createResampledImage();
		
		if ( $this->image["format"]=="JPG" || $this->image["format"]=="JPEG" ) {
			//JPEG
			imageJPEG($this->image["des"], "", $this->image["quality"]);
		} elseif ( $this->image["format"] == "PNG" ) {
			//PNG
			imagePNG($this->image["des"]);
		} elseif ( $this->image["format"] == "GIF" ) {
			//GIF
			imageGIF($this->image["des"]);
		} elseif ( $this->image["format"] == "WBMP" ) {
			//WBMP
			imageWBMP($this->image["des"]);
		}
	}
	
	/**
     * Private method to run the imagecopyresampled() function with the parameters that have been set up.
	 * This method is used by the save() and show() methods.
     */
	private function createResampledImage()
	{
		/* change ImageCreateTrueColor to ImageCreate if your GD not supported ImageCreateTrueColor function*/
		if ( isset($this->image["sizex_thumb"]) && isset($this->image["sizey_thumb"]) ) {		
			$this->image["des"] = ImageCreateTrueColor($this->image["sizex_thumb"], $this->image["sizey_thumb"]);
			imagecopyresampled($this->image["des"], $this->image["src"], 0, 0, $this->image["targetx"], $this->image["targety"], $this->image["sizex_thumb"], $this->image["sizey_thumb"], $this->image["sizex"], $this->image["sizey"]);
		} else {
			$this->image["des"] = ImageCreateTrueColor($this->image["sizex"], $this->image["sizey"]);
			imagecopyresampled($this->image["des"], $this->image["src"], 0, 0, $this->image["targetx"], $this->image["targety"], $this->image["sizex"], $this->image["sizey"], $this->image["sizex"], $this->image["sizey"]);
		}	
	}
	
	/**
     * Saves the image to a given filename, if no filename is given then a default is created.
	 *
	 * @param string $save The new image filename.
     */	
	public function save($save="")
	{
		//save thumb
		if ( empty($save) ) {
			$save = strtolower("./thumb.".$this->image["format"]);
		}
		header("Content-Type: image/".$this->image["format"]);
		$this->createResampledImage();

		if ( $this->image["format"] == "JPG" || $this->image["format"] == "JPEG" ) {
			//JPEG
			imageJPEG($this->image["des"], $save, $this->image["quality"]);
		} elseif ( $this->image["format"] == "PNG" ) {
			//PNG
			imagePNG($this->image["des"], $save);
		} elseif ( $this->image["format"] == "GIF" ) {
			//GIF
			imageGIF($this->image["des"], $save);
		} elseif ( $this->image["format"] == "WBMP" ) {
			//WBMP
			imageWBMP($this->image["des"], $save);
		}
		
		header("Content-Type: text/html");
	}
}

/* ?> */

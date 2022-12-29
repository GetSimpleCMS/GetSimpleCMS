<?php
/**
 * Image class
 *
 * This class provides basic functionality for image manipulation using the GD library
 * Bases on https://github.com/bedeabza/Image/blob/master/src/Bedeabza/Image.php (author Dragos Badea	<bedeabza@gmail.com> )
 */
class EGImage
{


	/**
	 * @var array
	 */
	protected $_errors = array(
		'NotExists'         => 'The file %s does not exist',
		'NotReadable'       => 'The file %s is not readable',
		'Format'            => 'Unknown image format: %s',
		'GD'                => 'The PHP extension GD is not enabled',
		'WidthHeight'       => 'Please specify at least one of the width and height parameters',
		'CropDimExceed'     => 'The cropping dimensions must be smaller and within original ones',
		'InvalidResource'   => 'Invalid image resource provided',
		'CannotSave'   		=> 'Cannot save image file: %s'
	);

	/**
	 * @var string
	 */
	protected $_fileName = null;

	/**
	 * @var string
	 */
	protected $_format = null;

	/**
	 * @var array
	 */
	protected $_acceptedFormats = array('png','gif','jpeg');

	/**
	 * @var resource
	 */
	protected $_sourceImage = null;

	/**
	 * @var resource
	 */
	protected $_workingImage = null;

	/**
	 * @var array
	 */
	protected $_originalSize = null;

	
	/**
     * Renders image with error message
     */
	public static function renderError($message, $width = 300, $height = 200) {
		$im = ImageCreateTrueColor($width, $height);

        //try to fit text in new lines
        $colorInt = hexdec('FFFFFF');
        $h = imagefontheight(2);
        $fw = imagefontwidth(2);
        $txt = explode("\n", wordwrap($message, ($width / $fw), "\n"));
        $lines = count($txt);
        $color = imagecolorallocate($im, 0xFF & ($colorInt >> 0x10), 0xFF & ($colorInt >> 0x8), 0xFF & $colorInt);
        $y = 5;
        foreach ($txt as $text) {
            $x = (($width - ($fw * strlen($text))) / 2);
            imagestring($im, 2, $x, $y, $text, $color);
            $y += ($h + 4);
        }
       
		self::sendHeaders();
		imagejpeg($im);
		imagedestroy($im);
		die;
    }
	
	/**
	 * @param string $name
	 * @param int $expires in seconds
	 * @return void
	 */
	public static function sendHeaders($name = '', $format = 'jpeg', $expires = 0, $lastMod = null)
	{
		header('Content-type: image/'.$format);
		header("Content-Disposition: inline".($name ? "; filename=".$name : ''));
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', ($lastMod ? $lastMod : time())) . ' GMT');
		header("Cache-Control: maxage={$expires}");
		if($expires)
			header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
		header("Pragma: public");
	}
	
	/**
	 * @return int
	 */
	public function getWidth()
	{
		return $this->_originalSize[0];
	}

	/**
	 * @return int
	 */
	public function getHeight()
	{
		return $this->_originalSize[1];
	}
	
	
	/**
     * @param string|null $fileName
     */
	public function __construct($fileName)
	{			
		if(!file_exists($fileName) || !is_file($fileName))
            $this->_error('NotExists', $fileName);

        if(!is_readable($fileName))
            $this->_error('NotReadable', $fileName);

        $this->_originalSize    = getimagesize($fileName);
		
		$mime = explode('/', $this->_originalSize['mime']);
        $this->_format = array_pop($mime); //image/jpg for example

        if(!in_array($this->_format, $this->_acceptedFormats))
            $this->_error('Format', $this->_format);

        $this->_fileName = $fileName;
	}

	/**
	 * sharpening with transparent png gives some black pixels
	 
	 * @param int $width
	 * @param int $height
	 * @param int $mode
	 * @return void
	 */
	public function resize($width = null, $height = null, $mode = 'fit', $sharpen = false)
	{
		list($width, $height)   = $this->_calcDefaultDimensions($width, $height);
        $cropAfter              = false;
        $cropDimensions         = array();

		//original size are larger than required sizes than stop
		//if fit mode, only stop if width and height are larger than original size (not one side
		if ( 
			($this->_originalSize[0] == $width && $this->_originalSize[1] == $height) ||
			($mode == 'fill' && ($this->_originalSize[0] < $width || $this->_originalSize[1] < $height)) || 
			($mode != 'fill' && $this->_originalSize[0] < $width && $this->_originalSize[1] < $height )
		){
			$width = $this->_originalSize[0];
			$height = $this->_originalSize[1];
			return;
		}
		
		if(!$this->_sourceImage)
            $this->_setSourceImage();

		//reclaculate to preserve aspect ratio
		if($width/$height != $this->_originalSize[0]/$this->_originalSize[1]){
			//mark for cropping
			if($mode == 'fill'){
				$cropAfter = true;
				$cropDimensions = array($width, $height);
			}

			if(
				($width/$this->_originalSize[0] > $height/$this->_originalSize[1] && $mode == 'fit') ||
				($width/$this->_originalSize[0] < $height/$this->_originalSize[1] && $mode == 'fill')
			){
				$width = $height/$this->_originalSize[1]*$this->_originalSize[0];
			}else{
				$height = $width/$this->_originalSize[0]*$this->_originalSize[1];
			}
		}
        
        $width = round($width);
        $height = round($height);

		//create new image
		$this->_workingImage = $this->_createImage($width, $height);

		//move the pixels from source to new image
		imagecopyresampled($this->_workingImage, $this->_sourceImage, 0, 0, 0, 0, $width, $height, $this->_originalSize[0], $this->_originalSize[1]);
		
		if($sharpen && function_exists('imageconvolution')) {
			$intSharpness = $this->_findSharp($this->_originalSize[0], $width);
				$arrMatrix = array(
				array(-1, -2, -1),
				array(-2, $intSharpness + 12, -2),
				array(-1, -2, -1)
			);
			imageconvolution($this->_workingImage, $arrMatrix, $intSharpness, 0);
		}
		
		$this->_replaceAndReset($width, $height);

		if($cropAfter)
			$this->cropFromCenter($cropDimensions[0], $cropDimensions[1]);
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @return void
	 */
	public function crop($x = 0, $y = 0, $width = null, $height = null)
	{
		if( $width > $this->_originalSize[0] || $height > $this->_originalSize[1])
			$this->_error('CropDimExceed');
			
		list($width, $height) = $this->_calcDefaultDimensions($width, $height);
		
		if( $x + $width > $this->_originalSize[0] || $y + $height > $this->_originalSize[1] )
			$this->_error('CropDimExceed');
			
        if(!$this->_sourceImage)
            $this->_setSourceImage();

		//create new image
		$this->_workingImage = $this->_createImage($width, $height);

		//move the pixels from source to new image
		imagecopyresampled($this->_workingImage, $this->_sourceImage, 0, 0, $x, $y, $width, $height, $width, $height);
		$this->_replaceAndReset($width, $height);
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return void
	 */
	public function cropFromCenter($width, $height)
	{
		$x = (int)(($this->_originalSize[0] - $width) / 2);
		$y = (int)(($this->_originalSize[1] - $height) / 2);

		$this->crop($x, $y, $width, $height);
	}


	/**
	 * @param string $name
	 * @param int $quality
	 * @return void
	 */
	public function render($expires = 0, $quality = 100)
	{
		$fromFile = !$this->_sourceImage; //is from file or not
		
		self::sendHeaders($fromFile ? basename($this->_fileName) : '', $this->_format, $expires, !$fromFile ? null : filemtime($this->_fileName));
		
        if(!$this->_sourceImage)
            readfile($this->_fileName);
		else
			$this->_execute($quality, null);
		

		$this->destroy();
		die;
	}	
	
	/**
     * @param null|string $fileName
     * @param int $quality
     * @return void
     */
	public function save($fileName = null, $quality = 100)
	{			
		if(!$this->_sourceImage) //no source image, just re save 
            $this->_setSourceImage();

		$fileName = $fileName ? $fileName : $this->_fileName;

		if (!$this->_execute($quality, $fileName))
			$this->_error('CannotSave', $fileName);

		$this->_fileName = $fileName;

		$this->destroy(); //destroy image
	}	
	
	
	/**
	 * @return void
	 */
	public function destroy()
	{
		if($this->_sourceImage){
			imagedestroy($this->_sourceImage);
			$this->_sourceImage = null;
		}
	}
	
	/**
     * @param string $fileName
     * @return void
     */
    protected function _setSourceImage()
    {
        if(!function_exists('gd_info'))
            $this->_error('GD');

        $this->_sourceImage = $this->_createImageFromFile();
    }

	/**
	 * @param int $width
	 * @param int $height
	 * @return array
	 */
	protected function _calcDefaultDimensions($width = null, $height = null)
	{
		if(!$width && !$height)
			$this->_error('WidthHeight');

		//autocalculate width and height if one of them is missing
		if(!$width)
			$width = $height/$this->_originalSize[1]*$this->_originalSize[0];

		if(!$height)
			$height = $width/$this->_originalSize[0]*$this->_originalSize[1];

		return array($width, $height);
	}

	/**
	 * @return resource
	 */
	protected function _createImageFromFile()
	{
		$function = 'imagecreatefrom'.$this->_format;
		return $function($this->_fileName);
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return resource
	 */
	protected function _createImage($width, $height)
	{
		$function = function_exists('imagecreatetruecolor') ? 'imagecreatetruecolor' : 'imagecreate';
		$image = $function($width, $height);

		//special conditions for png transparence
		if($this->_format == 'png'){
			imagealphablending($image, false);
			imagesavealpha($image, true);
			imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocatealpha($image, 255, 255, 255, 127));
		}

		return $image;
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return void
	 */
	protected function _replaceAndReset($width, $height)
	{
		imagedestroy($this->_sourceImage);
		$this->_sourceImage = $this->_workingImage;

		$this->_originalSize[0] = $width;
		$this->_originalSize[1] = $height;
	}
	
	/* 
		sharpen images function 
	*/
	protected function _findSharp($intOrig, $intFinal) {
		$intFinal = $intFinal * (750.0 / $intOrig);
		$intA     = 80; //changed from 52
		$intB     = -0.27810650887573124;
		$intC     = .00047337278106508946;
		$intRes   = $intA + $intB * $intFinal + $intC * $intFinal * $intFinal;
		return max(round($intRes), 0);
	}

	/**
	 * @throws Exception
	 * @param string $code
	 * @param array $params
	 * @return void
	 */
	protected function _error($code, $param = '')
	{
		throw new Exception(sprintf($this->_errors[$code], $param));
	}


	/**
	 * @param string $fileName
	 * @param int $quality
	 * @return void
	 */
	protected function _execute($quality, $fileName = null)
	{
		$function = 'image'.$this->_format;
		
		return $function($this->_sourceImage, $fileName, $this->_getQuality($quality));
	}

	/**
	 * @param int $quality
	 * @return int|null
	 */
	protected function _getQuality($quality)
 {
     return match ($this->_format) {
         'gif' => null,
         'jpeg' => $quality,
         'png' => (int)($quality/10 - 1),
         default => null,
     };
 }
}
<?php
include('common.php');
login_cookie_check();

/**
 * ONLY AVAILABLE IF AUTHENTICATED
 * 
 * Thumbnail Image Generator
 *
 * REQUIREMENTS:
 * - PHP 4.0.6 and GD 2.0.1 or later
 * - May not work with GIFs if GD2 library installed on your server 
 * - does not support GIF functions in full
 *
 * Parameters:
 * - src - path to source image
 * - dest - path to thumb (where to save it)
 * - x - max width
 * - y - max height
 * - q - quality (applicable only to JPG, 1 to 100, 100 - best)
 * - t - thumb type. "-1" - same as source, 1 = GIF, 2 = JPG, 3 = PNG
 * - f - save to file (1) or output to browser (0).
 * - json - return image in json object including obj info and base64 image
 * 
 * Sample usage: 
 * 1. save thumb on server: 
 * thumb.php?src=test.jpg&dest=thumb.jpg&x=100&y=50
 * 2. output thumb to browser:
 * thumb.php?src=test.jpg&x=50&y=50&f=0
 *
 * @version 1.3
 *
 * @package GetSimple
 * @subpackage Images
*  @example http://127.0.0.1/getsimple/admin/inc/thumb.php?src=test/image.jpg&dest=test/thumbsm.image.jpg&f=1&x=80&y=160
 */ 

// Below are default values (if parameter is not passed)

// save to file (true) or output to browser (false)
$save_to_file = true;

// Quality for JPEG and PNG.
// 0 (worst quality, smaller file) to 100 (best quality, bigger file)
// Note: PNG quality is only supported starting PHP 5.1.2
$image_quality = 65;

// resulting image type (1 = GIF, 2 = JPG, 3 = PNG)
// enter code of the image type if you want override it
// or set it to -1 to determine automatically
$image_type = -1;

// maximum thumb side size
$max_x = 65;
$max_y = 130;

// cut image before resizing. Set to 0 to skip this.
$cut_x = 0;
$cut_y = 0;


$to_name = '';

if (isset($_REQUEST['f'])) {
  $save_to_file = intval($_REQUEST['f']) == 1;
}

if (isset($_REQUEST['src'])) {
  $from_name = str_replace('../','', urldecode($_REQUEST['src']));
}
else {
  die("Source file name must be specified.");
}

if (isset($_REQUEST['dest'])) {
  $to_name = str_replace('../','', urldecode($_REQUEST['dest']));
}
else if ($save_to_file) {
  die("Thumbnail file name must be specified.");
}

if (isset($_REQUEST['q'])) {
  $image_quality = intval($_REQUEST['q']);
}

if (isset($_REQUEST['t'])) {
  $image_type = intval($_REQUEST['t']);
}

if (isset($_REQUEST['x'])) {
  $max_x = intval($_REQUEST['x']);
}

if (isset($_REQUEST['y'])) {
  $max_y = intval($_REQUEST['y']);
}

// @todo cuts not implemented
if (isset($_REQUEST['ox'])) {
  $cut_x = intval($_REQUEST['ox']);
}

if (isset($_REQUEST['oy'])) {
  $cut_y = intval($_REQUEST['oy']);
}

$path_parts = pathinfo($from_name);


$file     = basename($from_name);
$sub_path = dirname($from_name);
$outfile  = $save_to_file ? basename($to_name) : null;
// debugLog($file);
// debugLog($sub_path);
// debugLog($outfile);

// travesal protection
if(!filepath_is_safe(GSDATAUPLOADPATH.$sub_path.$file,GSDATAUPLOADPATH,true,true)) die('invalid image');

// Debugging Request
// returns the imagemanipulation object json encoded, 
// add base64 encoded image data ['data']
// add filesize ['bytes']
// add url to image if it was saved ['url']
if(isset($_REQUEST['debug']) || isset($_REQUEST['json'])){
    ob_start();
    // $outfile = null;
}

// @todo: if needing to save as attachement from post, might need this else second request might be made with post data missing
// header('Content-Disposition: Attachment;filename='.$outfile);

$image = generate_thumbnail($file, $sub_path, $outfile, $max_y, $max_x, $image_quality, $show = true, $image_type);

if(isset($_REQUEST['debug']) || isset($_REQUEST['json'])){
    $output = ob_get_contents(); // get the image as a string in a variable
    ob_end_clean(); //Turn off output buffering and clean it
    header("Content-Type: text/json");
    
    // add filesize and base64 encoded image
    $image->image['bytes'] = strlen($output); // size in bytes
    $image->image['data'] = base64_encode($output);
    
    // remove resources and filepaths
    unset($image->image['src']);
    unset($image->image['des']);
    unset($image->image['srcfile']);
    unset($image->image['outfile']);

    // $image->image['debuglog'] = strip_tags($GS_DEBUG);;

    // add url to thumbnail
    if(isset($image->image['outfile'])) 
        $image->image['thumb_url'] = getThumbnailURI($outfile,$sub_path,'');
    
    // echo "<pre>",print_r($image->image,true),"</pre>";
    
    // json encode
    echo json_encode($image);
}

exit;

/* ?> */

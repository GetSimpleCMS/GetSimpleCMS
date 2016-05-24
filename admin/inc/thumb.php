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
 * - dest - path to thumb (where to save it) optional if output to browser
 * - w or x - max width
 * - h or y- max height
 * - q - quality (applicable only to JPG, 1 to 100, 100 - best)
 * - t - thumb type. "-1" - same as source, 1 = GIF, 2 = JPG, 3 = PNG, ignored if dest extension exists
 * - f - save to file (1) or output to browser (0).
 * - json - return image in json object including obj info and base64 image
 * - c - crop options, 0 = left/top, 1 = center, 2 = right/bottom ( only maked sense with w=h square images )
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
*  @example http://127.0.0.1/getsimple/admin/inc/thumb.php?src=test/image.jpg&dest=test/thumbsm.image.jpg&f=1&w=80&h=160
 */ 

// Below are default values (if parameter is not passed)

// output and save to file (true)
// output to browser only (false)
$save_to_file = true;

// Quality for JPEG and PNG.
// 0 (worst quality, smaller file) to 100 (best quality, bigger file)
// Note: PNG quality is only supported starting PHP 5.1.2
$image_quality = 75;

// resulting image type (1 = GIF, 2 = JPG, 3 = PNG)
// enter code of the image type if you want override it
// or set it to -1 to determine automatically
$image_type = -1;

// maximum thumb side size
$max_x = null;
$max_y = null;

// cut image before resizing. Set to 0 to skip this.
$cut_x = 0;
$cut_y = 0;

// auto crop image square, fit 1=left/top, 2=center, 3=right/bottom
$crop = null;

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

if ($save_to_file && (!isset($to_name) || empty($to_name))) {
  die("Thumbnail file name must be specified.");
}

if (isset($_REQUEST['q'])) {
  $image_quality = intval($_REQUEST['q']);
}

if (isset($_REQUEST['t'])) {
  $image_type = $_REQUEST['t'];
}

if (isset($_REQUEST['x'])) {
  $max_x = intval($_REQUEST['x']);
}

if (isset($_REQUEST['y'])) {
  $max_y = intval($_REQUEST['y']);
}

// allow w&h instead of x&y (which are confusing)
if (isset($_REQUEST['w'])) {
  $max_x = intval($_REQUEST['w']);
}

if (isset($_REQUEST['h'])) {
  $max_y = intval($_REQUEST['h']);
}

if(isset($_REQUEST['c'])){
	$crop = intval($_REQUEST['c']);
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
$sub_path = tsl(dirname($from_name));
$outfile  = $save_to_file ? basename($to_name) : null;

// if empty do not resize
if(empty($max_y)) $max_y = null;
if(empty($max_x)) $max_x = null;

// debugLog($file);
// debugLog($sub_path);
// debugLog($outfile);

// travesal protection
if(!filepath_is_safe(GSDATAUPLOADPATH.$sub_path.$file,GSDATAUPLOADPATH,true)) die('invalid src image');
if(!path_is_safe(GSTHUMBNAILPATH.dirname($to_name),GSTHUMBNAILPATH,true)) die('invalid dest image');

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
$image = generate_thumbnail($file, $sub_path, $outfile, $max_x, $max_y, $crop, $image_quality, $show = true, $image_type);

if(isset($_REQUEST['debug']) || isset($_REQUEST['json'])){
    $output = ob_get_contents(); // get the image as a string in a variable
    ob_end_clean(); //Turn off output buffering and clean it
    header("Content-Type: text/json");
    
    // add filesize and base64 encoded image
    $image->image['bytes'] = strlen($output); // size in bytes
    $image->imagedata = base64_encode($output);
    
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

<?php
/**
 * upload precheck, used to check if file already exists and upload is allowed before wasting bandwidth
 * For use with GSUPLOADER
 * 
 * @since  3.4
 * @package GetSimple
 * @subpackage Files
 */
 
include('inc/common.php');
login_cookie_check();

$path = (isset($_GET['path'])) ? $_GET['path'] . "/" : "";
$name = clean_img_name($_POST['filename']);

if(!subpath_is_safe(GSDATAUPLOADPATH, dirname($path.$name))) die(json_encode(array('error'=>'invalid path')));

echo json_encode(array('file_exists'=> file_exists(GSDATAUPLOADPATH.$path.$name)) );

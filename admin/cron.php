<?php
/**
 * Cron
 *
 * Allows websites to cron certain items of their installation 	
 *
 * @package GetSimple
 * @subpackage Scheduling
 * @link http://get-simple.info/docs/cron-setup
 */

if (basename($_SERVER['PHP_SELF']) == 'cron.php') { 
	die('You cannot load this page directly.');
}; 

// Relative
$relative = '';
$admin_relative = 'admin/inc/';
$lang_relative = 'admin/';
$base = true;

// Include common.php
include('admin/inc/common.php');
global $SITEURL;
global $SESSIONHASH;

//to regenerate the sitemap
$cURL = curl_init($SITEURL.'/admin/sitemap.php');
curl_setopt($cURL, CURLOPT_POST, 1);
curl_setopt($cURL, CURLOPT_POSTFIELDS, "s=".$SESSIONHASH);
curl_setopt($cURL, CURLOPT_HEADER, 0);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($cURL);
echo 'Sitemap: '.$res .'<br />';
curl_close($cURL);


// to make site backup
# this no longer works with the addition of nonce to the archive screen.  
#$cURL = curl_init($SITEURL.'/admin/zip.php');
#curl_setopt($cURL, CURLOPT_POST, 1);
#curl_setopt($cURL, CURLOPT_POSTFIELDS, "s=".$SESSIONHASH);
#curl_setopt($cURL, CURLOPT_HEADER, 0);
#curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
#$res = curl_exec($cURL);
#echo 'Archive: '.$res .'<br />';
#curl_close($cURL);

//complete
echo "Cron Completed at ".date('M-d-Y H:i') .'<br />---------------<br />';
return true;
exit; 
?>
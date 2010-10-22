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
if (file_exists('gsconfig.php')) {
	require_once('gsconfig.php');
}
// Relative
if (defined('GSADMIN')) {
	$GSADMIN = GSADMIN;
} else {
	$GSADMIN = 'admin';
}
$admin_relative = $GSADMIN.'/inc/';
$lang_relative = $GSADMIN.'/';
$base = true;

// Include common.php
include($GSADMIN.'/inc/common.php');
global $SITEURL;
global $SESSIONHASH;

//to regenerate the sitemap
$cURL = curl_init($SITEURL.'/'.$GSADMIN.'/sitemap.php');
curl_setopt($cURL, CURLOPT_POST, 1);
curl_setopt($cURL, CURLOPT_POSTFIELDS, "s=".$SESSIONHASH);
curl_setopt($cURL, CURLOPT_HEADER, 0);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($cURL);
echo 'Sitemap: '.$res .'<br />';
curl_close($cURL);


// to make site backup
# this no longer works with the addition of nonce to the archive screen.  
#$cURL = curl_init($SITEURL.'/'.$GSADMIN.'/zip.php');
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
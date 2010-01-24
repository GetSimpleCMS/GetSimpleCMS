<?php
/* 
* GetSimple cron.php
*
*/

if (basename($_SERVER['PHP_SELF']) == 'cron.php') { 
	die('You cannot load this page directly.');
}; 

//setup site URL variable
require_once('inc/basic.php');

$data = getXML('data/other/website.xml');
$SITEURL = $data->SITEURL;

//to regenerate the sitemap
$cURL = curl_init();
curl_setopt($cURL, CURLOPT_URL, $SITEURL .'/admin/sitemap.php');
curl_setopt($cURL, CURLOPT_HEADER, 1);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($cURL);
curl_close($cURL);


// to make site backup 
$cURL = curl_init();
$file = 'admin/inc/zip-files.php';
$newfile = 'zip.php';
copy($file, $newfile);
curl_setopt($cURL, CURLOPT_URL, $SITEURL .'/'.$newfile);
curl_setopt($cURL, CURLOPT_HEADER, 1);
curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($cURL);
curl_close($cURL);
unlink('zip.php');

//complete
return true;
exit; 
?>
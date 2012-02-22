<?php
/**
 * Configuration File
 *
 * @package GetSimple
 * @subpackage Config
 */

$site_full_name = 'GetSimple';
$site_version_no = '3.1';
$name_url_clean = lowercase(str_replace(' ','-',$site_full_name));
$site_link_back_url = 'http://get-simple.info/';
$ver_no_clean = str_replace('.','',$site_version_no);
$cookie_name = lowercase($name_url_clean) .'_cookie_'. $ver_no_clean;
if (isset($_GET['redirect'])){
	$cookie_redirect = $_GET['redirect'];
} else {	
	$cookie_redirect = 'pages.php';
}
$cookie_login = 'index.php';
$cookie_time = '7200';  // 2 hours 
$api_url = 'http://get-simple.info/api/start/v3.php';
if (!defined('GSVERSION')) define('GSVERSION', $site_version_no);

?>
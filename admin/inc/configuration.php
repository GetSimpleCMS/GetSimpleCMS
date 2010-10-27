<?php
/**
 * Configuration File
 *
 * @package GetSimple
 * @subpackage Config
 */

$site_full_name = 'GetSimple';
$site_version_no = '2.04&beta;';
$name_url_clean = lowercase(str_replace(' ','-',$site_full_name));
$site_link_back_url = 'http://get-simple.info/';
$ver_no_clean = str_replace('.','',$site_version_no);
$cookie_name = lowercase($name_url_clean) .'_cookie_'. $ver_no_clean;
$cookie_redirect = 'pages.php';
$cookie_login = 'index.php';
$cookie_time = '7200';  // 2 hours 
$api_url = 'http://get-simple.info/api/start/';
if (!defined('GSVERSION')) define('GSVERSION', $site_version_no);

?>
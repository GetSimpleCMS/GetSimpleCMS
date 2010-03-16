<?php
/****************************************************
*
* @File: 	  configuration.php
* @Package:	GetSimple
* @Action:	Sitewide settings for cookies. 	
*
*****************************************************/

	$site_full_name = 'GetSimple';
	$site_version_no = '2.01';
	$name_url_clean = strtolower(str_replace(' ','-',$site_full_name));
	$site_link_back_url = 'http://get-simple.info/';
	$ver_no_clean = str_replace('.','',$site_version_no);
	$cookie_name = strtolower($name_url_clean) .'_cookie_'. $ver_no_clean;
	$cookie_redirect = 'pages.php';
	$cookie_login = 'index.php';
	$cookie_time = '7200';  // 2 hours 
	$api_url = 'http://get-simple.info/api/start/'; 
?>
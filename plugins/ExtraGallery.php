<?php
/*
Plugin Name: Extra Gallery
Description: Extra Gallery is a backend plugin for creating galleries, with advanced features like: custom fields, thumbnails cropping, multi language content, easy image browsing. It can be installed in GS more than once (multi instances) to use it with different settings on each copy. Created galleries are available as structured PHP arrays to use in theme.
Version: 1.03
Author: Michał Gańko
Author URI: http://flexphperia.net
*/

require_once('ExtraGallery/constants.php');
require_once('ExtraGallery/EGSettings.php');
require_once('ExtraGallery/EGGallery.php');
require_once('ExtraGallery/EGStorage.php');

if (!is_frontend()){
    require_once('ExtraGallery/EGTools.php');
    require_once('ExtraGallery/EGBack.php');
    
	$g = new EGBack(basename(__FILE__,'.php'));
    $g->init();
}

//omly once
if (!function_exists('eg_return_gallery')){
	function eg_return_gallery ($name = null, $language = null, $instanceNum = 0){
		return EGStorage::returnFrontGallery($name, $language, $instanceNum);
	}
}


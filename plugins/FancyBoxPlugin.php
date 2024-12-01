<?php

/*
  Plugin Name: FancyBox Plugin
  Description: makes FancyBox available on front
  Version: 1.0
  Author: Patrick Rehder
  Author URI: http://www.lippo-design.de/
 */

$thisfile = basename(__FILE__, ".php");

register_plugin(
		$thisfile, 'FancyBox Plugin', '1.0', 'Patrick Rehder', 'http://www.lippo-design.de/', 'makes FancyBox available on front', 'theme', 'fancybox_plugin_show'
);

queue_script('jquery', GSFRONT);
queue_script('fancybox', GSFRONT);
queue_style('fancybox-css', GSFRONT);

register_script('FancyBoxConfig', $SITEURL . 'plugins/FancyBoxPlugin/js/FancyBoxConfig.js', '1.0', FALSE);
queue_script('FancyBoxConfig', GSFRONT);

function fancybox_plugin_show() {
	// nix machen
}

?>
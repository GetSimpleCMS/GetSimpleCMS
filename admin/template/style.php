<?php

/**
 * Admin Stylesheet
 *
 * @package GetSimple
 * @subpackage init
 */

header('Content-type: text/css');

$load['plugin'] = true;
include('../inc/common.php');

$offset = 30000;
#header ('Cache-Control: max-age=' . $offset . ', must-revalidate');
#header ('Expires: ' . gmdate ("D, d M Y H:i:s", time() + $offset) . ' GMT');
$nocache = true;
# check to see if cache is available for this
$cachefile = GSCACHEPATH.'stylesheet.txt';
if (file_exists($cachefile) && time() - 600 < filemtime($cachefile) && !$nocache) {
	echo "/* Cached copy, generated ".date('H:i', filemtime($cachefile))." '".$cachefile."' */\n";
	echo file_get_contents($cachefile);
	exit;
}

ob_start();

function compress($buffer) {
  $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); /* remove comments */
  $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); /* remove tabs, spaces, newlines, etc. */
  return $buffer;
}

$useadminxml = true;

if (file_exists(GSTHEMESPATH.'admin.xml') && $useadminxml) {
	#load admin theme xml file
	$theme = getXML(GSTHEMESPATH.'admin.xml');

	$header_base = trim($theme->header->base);

	$primary_0 = trim($theme->primary->darkest);
	$primary_1 = trim($theme->primary->darker);
	$primary_2 = trim($theme->primary->dark);
	$primary_3 = trim($theme->primary->middle);
	$primary_4 = trim($theme->primary->light);
	$primary_5 = trim($theme->primary->lighter);
	$primary_6 = trim($theme->primary->lightest);

	$secondary_0 = trim($theme->secondary->darkest);
	$secondary_1 = trim($theme->secondary->lightest);

	$label_0     = trim($theme->label->label_0);
	$label_1     = trim($theme->label->label_1);
	$label_2     = trim($theme->label->label_2);
	$label_3     = trim($theme->label->label_3);
	$label_4     = trim($theme->label->label_4);
	$label_5     = trim($theme->label->label_5);
	$label_6     = trim($theme->label->label_6);

}

# set default colors
if(!isset($primary_0))   $primary_0  	= '#0E1316'; # darkest
if(!isset($primary_1))   $primary_1  	= '#182227'; # darker
if(!isset($primary_2))   $primary_2  	= '#283840'; # dark
if(!isset($primary_3))   $primary_3  	= '#415A66'; # middle
if(!isset($primary_4))   $primary_4  	= '#618899'; # light
if(!isset($primary_5))   $primary_5  	= '#E8EDF0'; # lighter
if(!isset($primary_6))   $primary_6  	= '#AFC5CF'; # lightest
if(!isset($secondary_0)) $secondary_0	= '#9F2C04'; # darkest
if(!isset($secondary_1)) $secondary_1	= '#CF3805'; # lightest
if(!isset($label_0))     $label_0    	= '#F2F2F2'; # label_default
if(!isset($label_1))     $label_1    	= '#0B5584'; # label_info
if(!isset($label_2))     $label_2    	= '#008C00'; # label_ok
if(!isset($label_3))     $label_3    	= '#FF8500'; # label_warn
if(!isset($label_4))     $label_4    	= '#CC0000'; # label_error
if(!isset($label_5))     $label_5    	= '#FFFFFF'; # label_light
if(!isset($label_6))     $label_6    	= '#999999'; # label_medium

if(!isset($header_base)) $header_base 	= $primary_3; # middle

include(GSCSSMAINFILENAME);

if( isset($_GET['s']) and in_array('wide',explode(',',$_GET['s'])) ){
	$width      = getDef('GSWIDTH');
	$width_wide = getDef('GSWIDTHWIDE');
	$widepages  = explode(',',getDef('GSWIDEPAGES'));
	$widepagecss = '';

	if($width =='0' or $width == '') $width = 'none';

	foreach($widepages as $pageid){
		$widepagecss.= "#$pageid .wrapper {max-width: $width_wide;}\n";
	}

	include('css-wide.php');
	if(file_exists(GSTHEMESPATH.GSCSSCUSTOMFILENAME)) include(GSTHEMESPATH.GSCSSCUSTOMFILENAME);
}

exec_action('style-save'); // called after css files are included
file_put_contents($cachefile, compress(ob_get_contents()));
chmod($cachefile, 0644);

ob_end_flush();

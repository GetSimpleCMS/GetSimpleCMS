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

$useadminxml = true; // bypass for including admin.xml
$useadmincss = true; // bypass for including admin.css

if (file_exists(GSTHEMESPATH.'admin.xml') && $useadminxml) {
	#load admin theme xml file
	$theme = getXML(GSTHEMESPATH.'admin.xml');

	$header_base  = trim((string) $theme->header->base);

	$primary_0    = trim((string) $theme->primary->darkest);
	$primary_1    = trim((string) $theme->primary->darker);
	$primary_2    = trim((string) $theme->primary->dark);
	$primary_3    = trim((string) $theme->primary->middle);
	$primary_4    = trim((string) $theme->primary->light);
	$primary_5    = trim((string) $theme->primary->lighter);
	$primary_6    = trim((string) $theme->primary->lightest);

	$secondary_0  = trim((string) $theme->secondary->darkest);
	$secondary_1  = trim((string) $theme->secondary->lightest);

	$label_0      = trim((string) $theme->label->label_0);
	$label_1      = trim((string) $theme->label->label_1);
	$label_2      = trim((string) $theme->label->label_2);
	$label_3      = trim((string) $theme->label->label_3);
	$label_4      = trim((string) $theme->label->label_4);
	$label_5      = trim((string) $theme->label->label_5);
	$label_6      = trim((string) $theme->label->label_6);
}

# set default colors
$defaultcolors = array(
	'primary_0'  	=> '#0E1316', # darkest
	'primary_1'  	=> '#182227', # darker
	'primary_2'  	=> '#283840', # dark
	'primary_3'  	=> '#415A66', # middle
	'primary_4'  	=> '#618899', # light
	'primary_5'  	=> '#E8EDF0', # lighter
	'primary_6'  	=> '#AFC5CF', # lightest
	'secondary_0'	=> '#9F2C04', # darkest
	'secondary_1'	=> '#CF3805', # lightest
	'label_0'    	=> '#F2F2F2', # label_default
	'label_1'    	=> '#0B5584', # label_info
	'label_2'    	=> '#008C00', # label_ok
	'label_3'    	=> '#FF8500', # label_warn
	'label_4'    	=> '#CC0000', # label_error
	'label_5'    	=> '#FFFFFF', # label_light
	'label_6'    	=> '#999999'  # label_medium
);

foreach($defaultcolors as $var => $color){
	if(empty($$var)) $$var = $color;
}

// set default header_base to primary_3
if(empty($header_base)) $header_base = $primary_3;

// notfication backgrounds with custom opacity
$notify_opacity = '0.1';
$notify_0 = getRGBA($defaultcolors['label_0'],$notify_opacity);
$notify_1 = getRGBA($defaultcolors['label_1'],$notify_opacity);
$notify_2 = getRGBA($defaultcolors['label_2'],$notify_opacity);
$notify_3 = getRGBA($defaultcolors['label_3'],$notify_opacity);
$notify_4 = getRGBA($defaultcolors['label_4'],$notify_opacity);
$notify_5 = getRGBA($defaultcolors['label_5'],$notify_opacity);
$notify_6 = getRGBA($defaultcolors['label_6'],$notify_opacity);

// include main css file css.php
include(GSCSSMAINFILE);

// if GSTYLEWIDE ( default )
if( isset($_GET['s']) and in_array('wide',explode(',',$_GET['s'])) ){
	$width      = getDef('GSWIDTH');                  // get page width
	$width_wide = getDef('GSWIDTHWIDE');              // get wide page width
	$widepages  = explode(',',getDef('GSWIDEPAGES')); // get ids of pages that are wide
	$widepagecss = '';

	if($width =='0' or $width == '') $width = 'none'; // allow for no max-width

	// set max width for wide pages using custom wide width
	foreach($widepages as $pageid){
		$widepagecss.= "#$pageid .wrapper {max-width: $width_wide;}\n";
	}

	include('css-wide.php');
}

// include custom theme/admin.css if exists
if(file_exists(GSTHEMESPATH.GSCSSCUSTOMFILE) && $useadmincss) include(GSTHEMESPATH.GSCSSCUSTOMFILE);

exec_action('style-save'); // called after css files are included

// save cache
file_put_contents($cachefile, compress(ob_get_contents()));
chmod($cachefile, 0644);

ob_end_flush();


/**
* Convert a hexa decimal color code to its RGB equivalent
*
* @param string $hexStr (hexadecimal color value)
* @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
* @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
* @return array or string (depending on second parameter. Returns False if invalid hex color value)
*/                                                                                                 
function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
} 

/**
 * getRGBA convert any color to rgba with custom alpha
 * 
 * @param  string $str   css color value #hex, rgb(), rgba()
 * @param  string $alpha 
 * @return string        returns colors normalized as rgba()
 */
function getRGBA($str,$alpha = '1.0'){

	// $keywordsPattern = "/^[a-z]*$/";
    $hexPattern  = "/^#[0-9a-f]{3}([0-9a-f]{3})?$/i";
    $rgbPattern  = "/^rgb\(\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*\)$/";
    $rgbaPattern = "/^rgba\(\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*((0.[1-9])|[01])\s*\)$/";
    // $hslPattern  = "/^hsl\(\s*(0|[1-9]\d?|[12]\d\d|3[0-5]\d)\s*,\s*((0|[1-9]\d?|100)%)\s*,\s*((0|[1-9]\d?|100)%)\s*\)$/";
	
	if(preg_match($rgbaPattern,$str,$matches)) return 'rgba('.implode(',',array_slice($matches,1,3)).','.$alpha.')';	
	if(preg_match($rgbPattern,$str,$matches))  return 'rgba('.implode(',',array_slice($matches,1)).','.$alpha.')';	
	if(preg_match($hexPattern,$str,$matches))  return 'rgba('.hex2RGB($str,true).','.$alpha.')';

	return $str;	
}

<?php

/**
 * Admin Stylesheet
 *
 * @package GetSimple
 * @subpackage init
 */


$load['plugin'] = true;
include('../inc/common.php');

header('Content-type: text/css',true);

# check to see if cache is available for this
$cachefile = GSCACHEPATH.'stylesheet.txt';
if (file_exists($cachefile) && time() - 600 < filemtime($cachefile) && !$nocache && getDef('GSSTYLECACHEENABLE',true)) {
	echo "/* Cached copy, generated ".date('H:i', filemtime($cachefile))." '".$cachefile."' */\n";
	echo read_file($cachefile);
	exit;
}

ob_start();

if (file_exists(GSTHEMESPATH.getDef('GSADMINTHEMEFILE')) && getDef('GSADMINTHEMEENABLE',true)) {
	#load admin theme xml file
	$theme = getXML(GSTHEMESPATH.getDef('GSADMINTHEMEFILE'));

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

$labelAlphas = "\n";
$alphas = array('0.8','0.5','0.1'); // rgba alpha values to generate
// generate notfication and label backgrounds with custom opacities
// label_[0-6]_opacity(80,50,10)
// eg. label_3_80 = label_warn with 80% rgba opacity
for($i=0;$i<7;$i++){
	$var  = 'notify_'.$i;
	$$var = getRGBA($defaultcolors['label_'.$i],'0.1');
	foreach($alphas as $alpha){
		$labelAlphas .= '.label_'.$i.'_'.floor($alpha*100).' {background-color: '.getRGBA($defaultcolors['label_'.$i],$alpha)."!important ;}\n";
	}
	$labelAlphas .= "\n";
}

// You can modify style globals here
exec_action('style-init'); // @hook style-init fired before including css files

// include main css file css.php
include(GSCSSMAINFILE);

// output label alphas
echo "/* label alphas */\n";
echo $labelAlphas;

// if GSTYLEWIDE ( default )
if( isset($_GET['s']) and in_array('wide',explode(',',$_GET['s'])) ){
	$width       = getDef('GSWIDTH');                  // get page width
	$width_wide  = getDef('GSWIDTHWIDE');              // get wide page width
	$widepages   = explode(',',getDef('GSWIDEPAGES')); // get ids of pages that are wide
	$widepagecss = '';

	if($width =='0' or $width == '') $width = 'none'; // allow for no max-width

	// set max width for wide pages using custom wide width
	foreach($widepages as $pageid){
		$widepagecss.= "#$pageid .wrapper {max-width: $width_wide;}\n";
	}

	include('css-wide.php');
}

// include custom theme/admin.css if exists
if(file_exists(GSTHEMESPATH.getDef('GSCSSCUSTOMFILE')) && getDef('GSCSSCUSTOMENABLE',true)) include(GSTHEMESPATH.getDef('GSCSSCUSTOMFILE'));

// You can include your own css here
exec_action('style-save'); // @hook style-save called after css files are included before cache is saved

// save cache
$ob_get_contents = ob_get_contents();
save_file($cachefile, cssCompress($ob_get_contents));
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

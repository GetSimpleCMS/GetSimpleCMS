<?php
/**
 * Display Available Themes
 * 
 * This file spits out a list of available themes to the control panel. 
 * This is provided thru an ajax call.
 *
 * @package GetSimple
 * @subpackage Available-Themes
 */

// Include common.php
include('common.php');

// Make sure register globals don't make this hackable again.
if (isset($TEMPLATE)) unset($TEMPLATE);

/**
 * Sanitise first
 * @todo Maybe use Anti-XSS on this instead?
 */
if (isset($_GET['dir'])) {
	$TEMPLATE = '';
	$segments = explode('/',implode('/',explode('\\',$_GET['dir'])));
	foreach ($segments as $part) if ($part !== '..') $TEMPLATE .= $part.'/';
	$TEMPLATE = preg_replace('/\/+/','/',$TEMPLATE);
	if (strlen($TEMPLATE)<=0||$TEMPLATE=='/') unset($TEMPLATE);
}

// Send back list of theme files from a certain directory for theme-edit.php
if (isset($TEMPLATE)) {
	$TEMPLATE_FILE = ''; $template = ''; $theme_templates = '';

if ($template == '') { $template = 'template.php'; }
$themes_path = GSTHEMESPATH;
$templates = get_themes($TEMPLATE);
$theme_templates = '<span id="themefiles"><select class="text" id="theme_files" style="width:400px;" name="f" >';
$theme_templates .=get_theme_files($themes_path.$TEMPLATE);
$theme_templates .= "</select></span>";
	
echo $theme_templates;
}



?>
<?php
if (file_exists('../../gsconfig.php')) {
	include('../../gsconfig.php');
}

// Debugging
if (defined('GSDEBUG')){
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	@ini_set('display_errors', 0);
}

// Make sure register globals don't make this hackable again.
if (isset($TEMPLATE)) unset($TEMPLATE);

// Sanitise first
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
	$themes_path = "../../theme/". $TEMPLATE ."/";
	
	$themes_handle = @opendir($themes_path) or die("Unable to open $themes_path");
	
	while ($file = readdir($themes_handle)) 
	{
		if( is_file($themes_path . $file) && $file != "." && $file != ".." ) 
		{
			$templates[] = $file;
		}
	}

	sort($templates);
	
	$theme_templates .= '<select class="text" id="theme_files" style="width:225px;" name="f" >';
	
	foreach ($templates as $file) 
	{
		if ($TEMPLATE_FILE == $file) { $sel="selected"; } else { $sel=""; };
		$templatename=$file;
		$theme_templates .= '<option '.@$sel.' value="'.$file.'" >'.$templatename.'</option>';
  	}
	
	$theme_templates .= "</select>";
	
	echo $theme_templates;
	
}
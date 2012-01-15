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
        $templates = directoryToArray(GSTHEMESPATH . $TEMPLATE . '/', true);
		$allowed_extensions=array('php','css','js','html','htm');
        $theme_templates .= '<select class="text" id="theme_files" style="width:425px;" name="f" >';
        foreach ($templates as $file) {
		  $extension=pathinfo($file,PATHINFO_EXTENSION);
		  if (in_array($extension, $allowed_extensions)){
		  $filename=pathinfo($file,PATHINFO_BASENAME);
		  $filenamefull=substr(strstr($file,'/theme/'.$TEMPLATE.'/'),strlen('/theme/'.$TEMPLATE.'/'));   
		  if ($TEMPLATE_FILE == $filename){ 
		          $sel="selected"; 
		  } else { 
		          $sel="";
		  }
		  if ($filename == 'template.php'){ 
		          $templatename=i18n_r('DEFAULT_TEMPLATE'); 
		  } else { 
		          $templatename=$filenamefull; 
		  }
		  $theme_templates .= '<option '.$sel.' value="'.$templatename.'" >'.$templatename.'</option>';
		  }        
		}
        
        $theme_templates .= "</select>";
        
        echo $theme_templates;
}
?>
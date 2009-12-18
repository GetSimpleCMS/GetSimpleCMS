<?php
	//disable or enable error reporting
	if (file_exists('../../data/other/debug.xml')) {
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', 1);
	} else {
		error_reporting(0);
		@ini_set('display_errors', 0);
	}



// send back list of theme files from a certain directory for theme-edit.php
if (isset($_GET['dir'])) {
	
	$TEMPLATE = $_GET['dir'];
	$TEMPLATE_FILE = ''; $template = ''; $theme_templates = '';

	if ($template == '') { $template = 'template.php'; }
	$themes_path = "../../theme/". $TEMPLATE ."/";
	
	$themes_handle = @opendir($themes_path) or die("Unable to open $themes_path");
	while ($file = readdir($themes_handle)) {
		if( is_file($themes_path . $file) && $file != "." && $file != ".." ) {
			$templates[] = $file;
		}
	}

	sort($templates);
	
	$theme_templates .= '<select class="text" id="theme_files" style="width:225px;" name="f" >';
	
	foreach ($templates as $file) {
		if ($TEMPLATE_FILE == $file) { $sel="selected"; } else { $sel=""; };
		$templatename=$file;
		$theme_templates .= '<option '.@$sel.' value="'.$file.'" >'.$templatename.'</option>';
  	}
	$theme_templates .= "</select>";
	
	echo $theme_templates;
	
}
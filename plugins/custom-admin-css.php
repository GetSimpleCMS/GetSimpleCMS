<?php
/*
Plugin Name: Custom Admin CSS
Description: You can restyle the control panel with this plugin's custom css.
Version: 1.1
Author: Chris Cagle
Author URI: http://www.cagintranet.com/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,
	'Custom Admin CSS',
	'1.1',
	'Chris Cagle',
	'http://www.cagintranet.com/',
	'You can restyle the control panel with this plugin\'s custom css',
	'theme',
	'gscacss_showform'
);

# activate hooks
add_action('header', 'gscacss_showcss');
add_action('theme-sidebar','createSideMenu',array($thisfile,'Custom Admin CSS')); 

# functions
function gscacss_showcss() {
	$css_file = GSDATAUPLOADPATH. 'custom-css/custom-style.css';
	if (file_exists($css_file)) {
		global $SITEURL;
		echo '
		<!-- for Custom Admin CSS plugin -->
		<link type="text/css" rel="stylesheet" href="'.$SITEURL.'data/uploads/custom-css/custom-style.css" />
		'. "\n";
	}
}

function gscacss_pagecss() {
	echo '
	<!-- for Custom Admin CSS plugin -->
	<style type="text/css" >
		textarea#custom_css {font-family:monospace;font-size:12px;line-height:15px;}
	</style>	
	';
}

function gscacss_showform() {

	# css file
	$css_file = GSDATAUPLOADPATH. 'custom-css/custom-style.css';
	$css_folder = GSDATAUPLOADPATH. 'custom-css/';
	
	# Save CSS file
	if((isset($_POST['submit']))) {
		if (! file_exists($css_folder)) {
			mkdir($css_folder);
		}
		$fc = stripslashes(htmlspecialchars_decode($_POST['custom_css'], ENT_QUOTES));
		$fh = fopen($css_file, 'w') or die("can't open file");
		fwrite($fh, $fc);
		fclose($fh);
		$success = "Custom CSS Saved";
	}
	
	# get contents of css file
	if (file_exists($css_file)) {
		$css_val = htmlentities(file_get_contents($css_file), ENT_QUOTES, 'UTF-8');
	}
	
	#add css for form
	gscacss_pagecss();
	
	?>
	
	<h3>Custom Admin CSS Style</h3>
	
	<form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
	<p><textarea rows="50" id="custom_css" name="custom_css" ><?php echo @$css_val; ?></textarea></p>
	<p class="submit"><input type="submit" id="submit" class="submit" value="Save Custom CSS" name="submit" /></p>
	</form>

	<?php
}
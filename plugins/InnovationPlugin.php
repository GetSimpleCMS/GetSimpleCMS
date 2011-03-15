<?php
/*
Plugin Name: Innovation Theme Settings
Description: Settings for the default GetSimple 3.0 Theme: Innovation
Version: 1.0
Author: Chris Cagle
Author URI: http://www.cagintranet.com/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");
$innovation_file=GSDATAOTHERPATH .'InnovationSettings.xml';

# register plugin
register_plugin(
	$thisfile, 													# ID of plugin, should be filename minus php
	'Innovation Theme Settings', 									# Title of plugin
	'1.0', 															# Version of plugin
	'Chris Cagle',											# Author of plugin
	'http://www.cagintranet.com/', 			# Author URL
	'Settings for the default GetSimple 3.0 Theme: Innovation', 	# Plugin Description
	'theme', 													# Page type of plugin
	'innovation_show'  										# Function that displays content
);

# hooks
add_action('theme-sidebar','createSideMenu',array($thisfile,'Innovation Theme Settings')); 

# get XML data
if (file_exists($innovation_file)) {
	$x = getXML($innovation_file);
	$facebook = $x->facebook;
	$twitter = $x->twitter;
	$linkedin = $x->linkedin;
} else {
	$facebook = '';
	$twitter = '';
	$linkedin = '';
}


function innovation_show() {
	global $innovation_file, $facebook, $twitter, $linkedin,$success,$error;
	
	// submitted form
	if (isset($_POST['submit'])) {
		$facebook=null;	$twitter=null; $linkedin=null;
		
		# check to see if the URLs provided are valid
		if ($_POST['facebook'] != '') {
			if (validate_url($_POST['facebook'])) {
				$facebook = $_POST['facebook'];
			} else {
				$error .= 'Facebook URL is not valid. ';
			}
		}
		
		if ($_POST['twitter'] != '') {
			if (validate_url($_POST['twitter'])) {
				$twitter = $_POST['twitter'];
			} else {
				$error .= 'Twitter URL is not valid. ';
			}
		}
		
		if ($_POST['linkedin'] != '') {
			if (validate_url($_POST['linkedin'])) {
				$linkedin = $_POST['linkedin'];
			} else {
				$error .= 'LinkedIn URL is not valid.';
			}
		}
		
		# if there are no errors, dave data
		if (!$error) {
			$xml = @new SimpleXMLElement('<item></item>');
			$xml->addChild('facebook', $facebook);
			$xml->addChild('twitter', $twitter);
			$xml->addChild('linkedin', $linkedin);
			
			if (! $xml->asXML($innovation_file)) {
				$error = i18n_r('CHMOD_ERROR');
			} else {
				$x = getXML($innovation_file);
				$facebook = $x->facebook;
				$twitter = $x->twitter;
				$linkedin = $x->linkedin;
				$success = i18n_r('SETTINGS_UPDATED');
			}
		}
	}
	
	?>
	<h3>Innovation Theme Settings</h3>
	
	<?php 
	if($success) { 
		echo '<p style="color:#669933;"><b>'. $success .'</b></p>';
	} 
	if($error) { 
		echo '<p style="color:#cc0000;"><b>'. $error .'</b></p>';
	}
	?>
	
	<form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		
		<p><label for="inn_facebook" >Facebook URL</label><input id="inn_facebook" name="facebook" class="text" value="<?php echo $facebook; ?>" /></p>
		<p><label for="inn_twitter" >Twitter URL</label><input id="inn_twitter" name="twitter" class="text" value="<?php echo $twitter; ?>" /></p>
		<p><label for="inn_linkedin" >LinkedIn URL</label><input id="inn_linkedin" name="linkedin" class="text" value="<?php echo $linkedin; ?>" /></p>
		
		<p><input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" /></p>
	</form>
	
	<?php
}

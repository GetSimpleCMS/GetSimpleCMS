<?php
/*
Plugin Name: Innovation Theme Settings
Description: Settings for the default GetSimple Theme: Innovation
Version: 1.2
Author: Chris Cagle
Author URI: http://chriscagle.me
*/

# get correct id for plugin
$thisfile_innov=basename(__FILE__, ".php");
$innovation_file=GSDATAOTHERPATH .'InnovationSettings.xml';

# add in this plugin's language file
i18n_merge($thisfile_innov) || i18n_merge($thisfile_innov, 'en_US');

# register plugin
register_plugin(
	$thisfile_innov, 													# ID of plugin, should be filename minus php
	i18n_r($thisfile_innov.'/INNOVATION_TITLE'), 				# Title of plugin
	'1.2', 															# Version of plugin
	'Chris Cagle',											# Author of plugin
	'http://chriscagle.me', 			# Author URL
	i18n_r($thisfile_innov.'/INNOVATION_DESC'), 					# Plugin Description
	'theme', 														# Page type of plugin
	'innovation_show'  									# Function that displays content
);



# hooks
add_action('theme-sidebar','createSideMenu',array($thisfile_innov, i18n_r($thisfile_innov.'/INNOVATION_TITLE'))); 

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
	global $innovation_file, $facebook, $twitter, $linkedin, $thisfile_innov;
	$success=null;$error=null;
	
	// submitted form
	if (isset($_POST['submit'])) {
		$facebook=null;	$twitter=null; $linkedin=null;
		
		# check to see if the URLs provided are valid
		if ($_POST['facebook'] != '') {
			if (validate_url($_POST['facebook'])) {
				$facebook = $_POST['facebook'];
			} else {
				$error .= i18n_r($thisfile_innov.'/FACEBOOK_ERROR').' ';
			}
		}
		
		if ($_POST['twitter'] != '') {
			if (validate_url($_POST['twitter'])) {
				$twitter = $_POST['twitter'];
			} else {
				$error .= i18n_r($thisfile_innov.'/TWITTER_ERROR').' ';
			}
		}
		
		if ($_POST['linkedin'] != '') {
			if (validate_url($_POST['linkedin'])) {
				$linkedin = $_POST['linkedin'];
			} else {
				$error .= i18n_r($thisfile_innov.'/LINKEDIN_ERROR').' ';
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
	<h3><?php i18n($thisfile_innov.'/INNOVATION_TITLE'); ?></h3>
	
	<?php 
	if($success) { 
		echo '<p style="color:#669933;"><b>'. $success .'</b></p>';
	} 
	if($error) { 
		echo '<p style="color:#cc0000;"><b>'. $error .'</b></p>';
	}
	?>
	
	<form method="post" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		
		<p><label for="inn_facebook" ><?php i18n($thisfile_innov.'/FACEBOOK_URL'); ?></label><input id="inn_facebook" name="facebook" class="text" value="<?php echo $facebook; ?>" type="url" /></p>
		<p><label for="inn_twitter" ><?php i18n($thisfile_innov.'/TWITTER_URL'); ?></label><input id="inn_twitter" name="twitter" class="text" value="<?php echo $twitter; ?>" type="url" /></p>
		<p><label for="inn_linkedin" ><?php i18n($thisfile_innov.'/LINKEDIN_URL'); ?></label><input id="inn_linkedin" name="linkedin" class="text" value="<?php echo $linkedin; ?>" type="url" /></p>
		
		<p><input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" /></p>
	</form>
	
	<?php
}

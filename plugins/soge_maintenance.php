<?php

$thisfile=basename(__FILE__, ".php");

register_plugin(
	$thisfile, 									#The name of the plugin
	'Maintenance', 								#Title of plugin
	'0.2', 										#Version of plugin
	'Rogier van Straten',						#Author of plugin
	'http://www.straight-forward.eu/', 			#Author URL
	'Maintenance setting', 						#Plugin description
	'index', 									#Page type of plugin
	'soge_maintenance_setup'  					#Function that displays content
);

add_action('index-pretemplate','soge_maintenance_pre');

function soge_maintenance_setup(){
	;
}

function soge_maintenance_pre(){
	$file = GSDATAOTHERPATH.'maintenance.xml';
	$maintenancemsg = '';
  	if(file_exists($file)) {
	$data = getXML($file);
	$maintenancemode = $data->onoff;
	$maintenancemsg = $data->message;
	$unemail = $data->email;
	$showcontact = $data->emailonoff;
	if ($maintenancemode != '' ) {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta name="keywords" content="';
		get_page_meta_keywords();
		echo '"><head><title>';
		get_site_name();
		echo '</title></head>';
		echo '<body><h1 style="width:300px; margin:250px auto 0px; font-family:Arial, Helevtica, Sans-serif; font-weight:normal;">';
		get_site_name();
		echo '</h1>';
		echo '<div id="maintenance" style="width:300px; text-align:center; height:auto; margin:15px auto; background:#fafafa;  -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; border:1px solid #eeeeee;">';
		echo '<p>' . $maintenancemsg . '</p>';
		if ($showcontact != ''){
		echo '<p><a href="mailto:'.$unemail.'">'. $unemail .'</a></p>';	
		}
		echo '</div></body></html>';
		die;
		}
	}	
}

function soge_maintenance_create_xml() {
  	$file = GSDATAOTHERPATH.'maintenance.xml';
	$informationfile = GSDATAOTHERPATH.'user.xml';
	//create a xml file with the onoff setting
  	if(isset($_POST['maintenanceonoff'])) {
    	$xml = @new SimpleXMLElement('<maintenance></maintenance>');
    	$xml->addChild('onoff', $_POST['maintenanceonoff']);
	if(isset($_POST['maintenancemsg']) && $_POST['maintenancemsg'] != ''){
    	$xml->addChild('message', $_POST['maintenancemsg']);
		} else {
			$defaultmsg = 'Site is currently down for maintenance.';
			$xml->addChild('message', $defaultmsg);
		}
	if(isset($_POST['showemail'])){
		$xml->addChild('emailonoff', $_POST['showemail']);
	}
	if(isset($_POST['maintenanceemail']) && $_POST['maintenanceemail'] != ''){
	    $xml->addChild('email', $_POST['maintenanceemail']);
		} else {
			if (file_exists($informationfile)){
			$mailadress = '';
		    $emaildata = getXML($informationfile);
		    $mailadress = $emaildata->EMAIL;
			$xml->addChild('email', $mailadress);
			}
		}	
    	$xml->asXML($file);
  	} else {
		if (file_exists($file)){
			unlink($file);
		}
	}
}

function soge_maintenance_create_field() {
	$file = GSDATAOTHERPATH . 'maintenance.xml';
	$maintenancecheck = '';
	$maintenancemode = '';
	$showcontactme = '';
	
	//get info from xml
	if (file_exists($file)){
    $data = getXML($file);
    $maintenancemode = $data->onoff;
	$maintenancemsg = $data->message;
	$maintenanceemail = $data->email;
	$showcontact = $data->emailonoff;
	}
	
	//if enabled place check
	if ($maintenancemode != '' ) { $maintenancecheck = 'checked'; }
	echo '<input type="checkbox" name="maintenanceonoff" value="1" '.$maintenancecheck.'> &nbsp;<b>Enable site maintenance</b><br></p>';
	
	//when enabled dropdown text field, keeps the settings page clean
	if ($maintenancemode != '' ) {
	echo '<p><b>Maintenance Message:</b><br /><input class="text" name="maintenancemsg" type="text" value="'.$maintenancemsg.'" /></p>';
	if ($showcontact != '' ) { $showcontactme = 'checked'; }
	echo '<p><input type="checkbox" name="showemail" value="1" '.$showcontactme.'> &nbsp;';
	echo '<b>Display e-mail:</b><br /><input class="text" name="maintenanceemail" type="text" value="'.$maintenanceemail.'" /></p>';	
	}
}

add_action('settings-website-extras','soge_maintenance_create_field',array());
add_action('settings-website-extras','soge_maintenance_create_xml',array());
?>
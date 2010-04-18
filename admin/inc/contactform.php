<?php
if (file_exists('gsconfig.php')) {
	include('gsconfig.php');
}

// Debugging
if (defined('GSDEBUG')){
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	@ini_set('display_errors', 0);
}
	
	$err = '';
	
	if (file_exists(GSDATAOTHERPATH.'website.xml')) {
		$dataw = getXML(GSDATAOTHERPATH.'website.xml');
		$TIMEZONE = $dataw->TIMEZONE;
		$LANG = $dataw->LANG;
	}
	
	//set internationalization
	if($LANG != '') {
		include_once('admin/lang/'.$LANG.'.php');
	} else {
		include_once('admin/lang/en_US.php');
	}
	
	if( function_exists('date_default_timezone_set') ) { 
		date_default_timezone_set(@$TIMEZONE);
	}
	
	if (file_exists(GSDATAOTHERPATH.'user.xml')) {
		$data = getXML(GSDATAOTHERPATH.'user.xml');
		$EMAIL = $data->EMAIL;
	}
	
	if (isset($_POST['contact-submit'])) {
		
		if ( $_POST['contact']['pot'] != '' ) {
			$err .= "\n". $i18n['MSG_CAPTCHA_FAILED'];
		}
		
		if ( $_POST['contact']['email'] != '' ) {
			$from = $_POST['contact']['email'];
		} else {
			$from = 'no-reply@get-simple.info';
		}
		if ( $_POST['contact']['subject'] != '' ) {
			$subject = $_POST['contact']['subject'];
		} else {
			$subject = $i18n['CONTACT_FORM_SUB'];
		}		
		
		if ($err == '') {
		
			$server_name = getenv ("SERVER_NAME");       // Server Name
			$request_uri = getenv ("REQUEST_URI");       // Requested URI

			$headers = "From: ".$from."\r\n";
			$headers .= "Return-Path: ".$from."\r\n";
			$headers .= "Content-type: text/html\r\n";
			
			$temp = $_POST['contact'];
			$captcha = $_POST['contact']['pot'];
			
			unset($temp['pot']);
			unset($temp['contact-submit']);
			unset($temp['submit']);
			
			$body = $i18n['CONTACT_FORM_SUB'].' '.$i18n['FROM'].' http://'.$server_name.$request_uri.' <br />';
			$body .= "-----------------------------------<br /><br />";

			$xmlfile = "data/other/logs/contactform.log";
			if ( ! file_exists($xmlfile) ) { 
				$xml = new SimpleXMLExtended('<channel></channel>');
			} else {
				$xmldata = file_get_contents($xmlfile);
				$xml = new SimpleXMLExtended($xmldata);
			}
			$thislog = $xml->addChild('entry');
			$thislog->addChild('date', date('r'));
			$cdata = $thislog->addChild('from');
			$cdata->addCData($from);
			$cdata = $thislog->addChild('subject');
			$cdata->addCData($subject);
			$cdata = $thislog->addChild('captcha');
			$cdata->addCData($captcha);

			foreach ( $temp as $key => $value ) {
				$body .= ucfirst($key) .": ". $value ."<br />";
				$cdata = $thislog->addChild(clean_url($key));
				$cdata->addCData($value);
			}
			$result = mail($EMAIL,$subject,$body,$headers);
			
			XMLsave($xml, $xmlfile);
		
			//results
			if ($result) {
				echo '<p class="contactmsg success">'.$i18n['MSG_CONTACTSUC'].'</p>';
			} else {
				echo '<p class="contactmsg error">'.$i18n['MSG_CONTACTERR'].'</p>';
			}
		} else {
			echo '<p class="contactmsg error">'.$err.'</p>';
		}

	}
?>
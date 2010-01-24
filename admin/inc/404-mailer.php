<?php
//disable or enable error reporting
if (file_exists('data/other/debug.xml')) {
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', 1);
} else {
	error_reporting(0);
	@ini_set('display_errors', 0);
}

if (file_exists('data/other/website.xml')) {
	$dataw = getXML('data/other/website.xml');
	$TIMEZONE = $dataw->TIMEZONE;
	$LANG = $dataw->LANG;
}

//set internationalization
if($LANG != '') {
	include('admin/lang/'.$LANG.'.php');
} else {
	include('admin/lang/en_US.php');
}

class Browser { 
	private $props    = array("Version" => "0.0.0", "Name" => "unknown", "Agent" => "unknown") ; 

	public function __Construct() { 
		$browsers = array("firefox", "msie", "opera", "chrome", "safari", 
		"mozilla", "seamonkey",    "konqueror", "netscape", 
		"gecko", "navigator", "mosaic", "lynx", "amaya", 
		"omniweb", "avant", "camino", "flock", "aol"); 

		$this->Agent = strtolower($_SERVER['HTTP_USER_AGENT']); 
		foreach($browsers as $browser) { 
			if (preg_match("#($browser)[/ ]?([0-9.]*)#", $this->Agent, $match)) { 
				$this->Name = $match[1] ; 
				$this->Version = $match[2] ; 
				break ; 
			} 
		} 
	} 

	public function __Get($name) { 
		if (!array_key_exists($name, $this->props)) { 
			exit; 
		} 
		return $this->props[$name] ; 
	} 

	public function __Set($name, $val) { 
		if (!array_key_exists($name, $this->props)) { 
			SimpleError("No such property or function.", "Failed to set". $name, $this->props) ; 
			exit; 
		} 
		$this->props[$name] = $val ; 
	} 

} 

if( function_exists('date_default_timezone_set') && ($TIMEZONE != '' || stripos($TIMEZONE, '--')) ) { 
	date_default_timezone_set(@$TIMEZONE);
}

$thisfile = 'data/other/user.xml';
$data = getXML($thisfile);
$EMAIL = $data->EMAIL;

$thisfile = 'data/other/cp_settings.xml';
$datac = getXML($thisfile);
$FOUR04MONITOR = $datac->FOUR04MONITOR;
$message = '';

$ip = getenv ("REMOTE_ADDR");                // IP Address
$server_name = getenv ("SERVER_NAME");       // Server Name
$request_uri = getenv ("REQUEST_URI");       // Requested URI
$http_ref = getenv ("HTTP_REFERER");         // HTTP Referer
$error_date = date("D M j Y g:i:s a T");     // Error Date
$br = new Browser; 
$browser = $br->Name ." ". $br->Version;

$subject = $i18n['404_ENCOUNTERED'] .' '.$server_name;  
$message .= $i18n['404_AUTO_MSG']."...<br /><br />".$i18n['PAGE_CANNOT_FOUND']." ".$server_name. " ".$i18n['DOMAIN'].  
"<br /><br/>".$i18n['DETAILS'].":<br />----------------------------------------------------------------------".  
"<br />".$i18n['WHEN'].": ".$error_date.  
"<br />".$i18n['WHO'].": ".$ip.  
"<br />".$i18n['FAILED_PAGE'].": http://".$server_name.$request_uri.  
"<br />".$i18n['REFERRER'].": ".$http_ref.  
"<br />".$i18n['BROWSER'].": ".$browser;  


if ($FOUR04MONITOR == '1') 
{ 
	$status = sendmail($EMAIL,$subject,$message);
}

//404 logging
$xmlfile = "data/other/logs/404monitoring.log";
if ( ! file_exists($xmlfile) ) 
{ 
	$xml = new SimpleXMLExtended('<channel></channel>');
} 
else 
{
	$xmldata = file_get_contents($xmlfile);
	$xml = new SimpleXMLExtended($xmldata);
}

$thislog = $xml->addChild('entry');
$thislog->addChild('date', date('r'));
$cdata = $thislog->addChild('IP_Address');
$cdata->addCData($ip);
$cdata = $thislog->addChild('Failed_Page');
$cdata->addCData("http://".$server_name.$request_uri);
$cdata = $thislog->addChild('Referrer');
$cdata->addCData($http_ref);
$cdata = $thislog->addChild('Browser');
$cdata->addCData($browser);
$xml->asXML($xmlfile);
?>
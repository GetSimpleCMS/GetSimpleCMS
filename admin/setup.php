<?php 
/****************************************************
*
* @File: 		setup.php
* @Package:	GetSimple
* @Action:	Installs the website if it has never been setup before. 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

if($_POST['lang'] != '') { 	
	$LANG = $_POST['lang'];
}

// Include common.php
include('inc/common.php');
	
// Variables
if(defined('GSLOGINSALT')) { $logsalt = GSLOGINSALT;} else { $logsalt = null; }
$kill = ''; 
$PASSWD = ''; 
$status = ''; 
$err = null; 
$message = null; 
$random = null;

// Get user data
$file = 'user.xml';
$path = $relative. 'data/other/';
if (file_exists($path . $file)) 
{
	$data = getXML($path . $file);
	$USR = stripslashes($data->USR);
	$PASSWD = $data->PWD;
	$EMAIL = $data->EMAIL;
}

// get suggestion for website base url
$path_parts = pathinfo(htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES));
$path_parts = str_replace("/admin", "", $path_parts['dirname']);
$fullpath = "http://". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $path_parts ."/";	

// if the form was submitted...	
if(isset($_POST['submitted']))
{
	if($_POST['sitename'] != '') 
	{ 
		$SITENAME1 = htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'); 
	} 
	else 
	{ 
		$err .= $i18n['WEBSITENAME_ERROR'] .'<br />'; 
	}
	
	$urls = $_POST['siteurl']; 
	
	if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $urls)) 
	{
		$SITEURL1 = tsl($_POST['siteurl']); 
	} 
	else 
	{
		$err .= $i18n['WEBSITEURL_ERROR'] .'<br />'; 
	}
	
	if($_POST['user'] != '') 
	{ 
		$USR1 = $_POST['user'];
		$USR = $_POST['user'];
	} 
	else 
	{
		$err .= $i18n['USERNAME_ERROR'] .'<br />'; 
	}
	
	$email = $_POST['email'];
	if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9.\+=_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $email)) 
	{
		$err .= $i18n['EMAIL_ERROR'] .'<br />'; 
	} 
	else 
	{
		$EMAIL1 = $_POST['email'];
	}

	
	// if there were no errors, setup the site
	if ($err == '')	{
		$random = createRandomPassword();
		$PASSWD1 = passhash($random);
		
		// create new users.xml file
		$bakpath = $relative. "backups/other/";
		createBak($file, $path, $bakpath);
		
		$xml = @new SimpleXMLElement('<item></item>');
		$xml->addChild('USR', @$USR1);
		$xml->addChild('PWD', @$PASSWD1);
		$xml->addChild('EMAIL', @$EMAIL1);
		
		if (! $xml->asXML($path . $file)) 
		{
			$kill = $i18n['CHMOD_ERROR'];
		}
		
		$flagfile = $relative. "backups/other/user.xml.reset";
		copy($path . $file, $flagfile);
		
		// create new website.xml file
		$file = 'website.xml';
		$xmls = @new SimpleXMLExtended('<item></item>');
		$note = $xmls->addChild('SITENAME');
		$note->addCData($SITENAME1);
		$note = $xmls->addChild('SITEURL');
		$note->addCData(@$SITEURL1);
		$note = $xmls->addChild('TEMPLATE');
		$note->addCData('Default_Simple');
		$note = $xmls->addChild('TIMEZONE');
		$note->addCData(@$TIMEZONE);
		$note = $xmls->addChild('LANG');
		$note->addCData($LANG);

		$xmls->asXML($path . $file);
		
		// create new cp_settings.xml file
		$file = 'cp_settings.xml';
		$xmlc = @new SimpleXMLElement('<item></item>');
		$xmlc->addChild('HTMLEDITOR', '1');
		$xmlc->addChild('HELPSECTIONS', '1');
		$xmlc->addChild('PRETTYURLS', '');
		$xmlc->asXML($path . $file);
		
		
		// create index.xml page
		$init = $relative. "data/pages/index.xml"; 
		$temp = "inc/tmp/tmp-index.xml";
		
		if (! file_exists($init)) 
		{
			copy($temp,$init);
		}

		
		// create components.xml page
		$init = $relative. "data/other/components.xml";
		$temp = "inc/tmp/tmp-components.xml"; 
		
		if (! file_exists($init)) 
		{
			copy($temp,$init);
		}

		
		// create 403.xml page
		$init = $relative. "data/other/403.xml";
		$temp = "inc/tmp/tmp-403.xml"; 
		
		if (! file_exists($init)) 
		{
			copy($temp,$init);
		}

		
		// create root .htaccess page
		$init = $relative. ".htaccess";
		$temp_data = file_get_contents("inc/tmp/tmp.htaccess");
		$temp_data = str_replace('**REPLACE**',tsl($path_parts), $temp_data);
		
		$fp = fopen($init, 'w');
		
		fwrite($fp, $temp_data);
		fclose($fp);
		
		// send email to new administrator
		$subject  = $site_full_name .' '. $i18n['EMAIL_COMPLETE'];
		$message .= $i18n['EMAIL_USERNAME'] . ': '. stripslashes($_POST['user']);
		$message .= '<br>'. $i18n['EMAIL_PASSWORD'] .': '. $random;
		$message .= '<br>'. $i18n['EMAIL_LOGIN'] .': <a href="'.$SITEURL1.'admin/">'.$SITEURL1.'admin/</a>';
		$message .= '<br>'. $i18n['EMAIL_THANKYOU'] .' '.$site_full_name.'!';
		$status   = sendmail($EMAIL1,$subject,$message);
		
		// Set the login cookie, then redirect user to secure panel		
		create_cookie();
		
		header("Location: welcome.php"); 
	}
}
?>

<?php get_template('header', $site_full_name.' &raquo; '. $i18n['INSTALLATION']); ?>
	
	<h1><?php echo $site_full_name; ?> <span>&raquo;</span> <?php echo $i18n['INSTALLATION']; ?></h1>
</div>
</div>
<div class="wrapper">
	

	<?php
	
	// display errors or success messages 
	if ($status == 'success') 
	{
		echo '<div class="updated">'. $i18n['NOTE_REGISTRATION'] .' '. $_POST['email'] .'</div>';
	} 
	elseif ($status == 'error') 
	{
		echo '<div class="error">'. $i18n['NOTE_REGERROR'] .'.</div>';
	}
	
	if ($kill != '') 
	{
		echo '<div class="error">'. $kill .'</div>';
	}	
	
	if ($err != '') 
	{
		echo '<div class="error">'. $err .'</div>';
	}
	
	if ($random != '') 
	{
		echo '<div class="updated">'.$i18n['NOTE_USERNAME'].' <b>'. stripslashes($_POST['user']) .'</b> '.$i18n['NOTE_PASSWORD'].' <b>'. $random .'</b> &nbsp&raquo;&nbsp; <a href="index.php">'.$i18n['EMAIL_LOGIN'].'</a></div>';
	}
	
?>
	<div id="maincontent">
<?php if ($kill == '') { ?>
		<div class="main" >
	<h3><?php echo $site_full_name .' '. $i18n['INSTALLATION']; ?></h3>

	<form action="setup.php" method="post" accept-charset="utf-8" >
		<p><b><?php echo $i18n['LABEL_WEBSITE']; ?>:</b><br /><input class="text" name="sitename" type="text" value="<?php if(isset($_POST['sitename'])) { echo $_POST['sitename']; } ?>" /></p>
		<input name="siteurl" type="hidden" value="<?php if(isset($_POST['siteurl'])) { echo $_POST['siteurl']; } else { echo $fullpath;} ?>" />
		<input name="lang" type="hidden" value="<?php echo $LANG; ?>" />
		<p><b><?php echo $i18n['LABEL_USERNAME']; ?>:</b><br /><input class="text" name="user" type="text" value="<?php if(isset($_POST['user'])) { echo $_POST['user']; } ?>" /></p>
		<p><b><?php echo $i18n['LABEL_EMAIL']; ?>:</b><br /><input class="text" name="email" type="text" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; } ?>" /></p>
		<p><input class="submit" type="submit" name="submitted" value="<?php echo $i18n['LABEL_INSTALL']; ?>" /></p>
	</form>
	</div>
<?php } ?>
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>
<?php 
/**
 * Setup
 *
 * Second step of installation (install.php). Sets up initial files & structure
 *
 * @package GetSimple
 * @subpackage Installation
 */

// Setup inclusions
$load['plugin'] = true;

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
$path = GSDATAOTHERPATH;
if (file_exists($path . $file)) {
	$data = getXML($path . $file);
	$USR = stripslashes($data->USR);
	$PASSWD = $data->PWD;
	$EMAIL = $data->EMAIL;
}

$fullpath = suggest_site_path();	
$path_parts = suggest_site_path(true);   

// if the form was submitted...	
if(isset($_POST['submitted']))
{
	if($_POST['sitename'] != '') 
	{ 
		$SITENAME1 = htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'); 
	} 
	else 
	{ 
		$err .= i18n_r('WEBSITENAME_ERROR') .'<br />'; 
	}
	
	$urls = $_POST['siteurl']; 
	
	if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $urls)) 
	{
		$SITEURL1 = tsl($_POST['siteurl']); 
	} 
	else 
	{
		$err .= i18n_r('WEBSITEURL_ERROR') .'<br />'; 
	}
	
	if($_POST['user'] != '') 
	{ 
		$USR1 = $_POST['user'];
		$USR = $_POST['user'];
	} 
	else 
	{
		$err .= i18n_r('USERNAME_ERROR') .'<br />'; 
	}
	
	$email = $_POST['email'];
	if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9.\+=_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $email)) 
	{
		$err .= i18n_r('EMAIL_ERROR') .'<br />'; 
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
		$bakpath = GSBACKUPSPATH."other/";
		createBak($file, $path, $bakpath);
		
		$xml = new SimpleXMLElement('<item></item>');
		$xml->addChild('USR', $USR1);
		$xml->addChild('PWD', $PASSWD1);
		$xml->addChild('EMAIL', $EMAIL1);
		
		if (! XMLsave($xml, $path . $file) ) {
			$kill = i18n_r('CHMOD_ERROR');
		}
		
		$flagfile = $bakpath."user.xml.reset";
		copy($path . $file, $flagfile);
		
		// create new website.xml file
		$file = 'website.xml';
		$xmls = new SimpleXMLExtended('<item></item>');
		$note = $xmls->addChild('SITENAME');
		$note->addCData($SITENAME1);
		$note = $xmls->addChild('SITEURL');
		$note->addCData($SITEURL1);
		$note = $xmls->addChild('TEMPLATE');
		$note->addCData('Default_Simple');
		$note = $xmls->addChild('TIMEZONE');
		$note->addCData($TIMEZONE);
		$note = $xmls->addChild('LANG');
		$note->addCData($LANG);
		XMLsave($xmls, $path . $file);
		
		// create new cp_settings.xml file
		$file = 'cp_settings.xml';
		$xmlc = new SimpleXMLElement('<item></item>');
		$xmlc->addChild('HTMLEDITOR', '1');
		$xmlc->addChild('PRETTYURLS', '');
		XMLsave($xmlc, $path . $file);
		
		// create index.xml page
		$init = GSDATAPAGESPATH."index.xml"; 
		$temp = GSADMININCPATH."tmp/tmp-index.xml";
		
		if (! file_exists($init))	{
			copy($temp,$init);
		}

		
		// create components.xml page
		$init = $path."components.xml";
		$temp = GSADMININCPATH."tmp/tmp-components.xml"; 
		
		if (! file_exists($init)) 
		{
			copy($temp,$init);
		}

		
		// create 404.xml page
		$init = $path."404.xml";
		$temp = GSADMININCPATH."tmp/tmp-404.xml"; 
		
		if (! file_exists($init)) 
		{
			copy($temp,$init);
		}

		
		// create root .htaccess page
		$init = GSROOTPATH. ".htaccess";
		$temp_data = file_get_contents(GSADMININCPATH."tmp/tmp.htaccess");
		$temp_data = str_replace('**REPLACE**',tsl($path_parts), $temp_data);
		
		$fp = fopen($init, 'w');
		
		fwrite($fp, $temp_data);
		fclose($fp);
		if (!file_exists($init)) {
			$kill .= sprintf(i18n_r('ROOT_HTACCESS_ERROR'), $GSADMIN.'/inc/tmp/tmp.htaccess', '**REPLACE**', tsl($path_parts)) . '<br />';
		}
		
		// create gsconfig.php if it doesn't exist yet.
		$init = GSROOTPATH."gsconfig.php";
		$temp = GSROOTPATH."temp.gsconfig.php";
		if (file_exists($init)) {
			unlink($temp);
			if (file_exists($temp)) {
				$kill .= sprintf(i18n_r('REMOVE_TEMPCONFIG_ERROR'), $temp) . '<br />';
			}
		} else {
			rename($temp, $init);
			if (!file_exists($init)) {
				$kill .= sprintf(i18n_r('MOVE_TEMPCONFIG_ERROR'), $temp, $init) . '<br />';
			}
		}
		
		// send email to new administrator
		$subject  = $site_full_name .' '. i18n_r('EMAIL_COMPLETE');
		$message .= i18n_r('EMAIL_USERNAME') . ': '. stripslashes($_POST['user']);
		$message .= '<br>'. i18n_r('EMAIL_PASSWORD') .': '. $random;
		$message .= '<br>'. i18n_r('EMAIL_LOGIN') .': <a href="'.$SITEURL1.$GSADMIN.'/">'.$SITEURL1.$GSADMIN.'/</a>';
		$message .= '<br><br>'. i18n_r('EMAIL_THANKYOU') .' '.$site_full_name.'!';
		$status   = sendmail($EMAIL1,$subject,$message);
		
		// Set the login cookie, then redirect user to secure panel		
		create_cookie();
		
		if ($kill == '') {
			redirect("welcome.php");
		}
	}
}
?>

<?php get_template('header', $site_full_name.' &raquo; '. i18n_r('INSTALLATION')); ?>
	
	<h1><?php echo $site_full_name; ?> <span>&raquo;</span> <?php i18n('INSTALLATION'); ?></h1>
</div>
</div>
<div class="wrapper">
	

	<?php
	
	// display errors or success messages 
	if ($status == 'success') 
	{
		echo '<div class="updated">'. i18n_r('NOTE_REGISTRATION') .' '. $_POST['email'] .'</div>';
	} 
	elseif ($status == 'error') 
	{
		echo '<div class="error">'. i18n_r('NOTE_REGERROR') .'.</div>';
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
		echo '<div class="updated">'.i18n_r('NOTE_USERNAME').' <b>'. stripslashes($_POST['user']) .'</b> '.i18n_r('NOTE_PASSWORD').' <b>'. $random .'</b> &nbsp&raquo;&nbsp; <a href="welcome.php">'.i18n_r('EMAIL_LOGIN').'</a></div>';
	}
	
?>
	<div id="maincontent">
<?php if ($kill == '') { ?>
		<div class="main" >
	<h3><?php echo $site_full_name .' '. i18n_r('INSTALLATION'); ?></h3>

	<form action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
		<p><b><?php i18n('LABEL_WEBSITE'); ?>:</b><br /><input class="text" name="sitename" type="text" value="<?php if(isset($_POST['sitename'])) { echo $_POST['sitename']; } ?>" /></p>
		<input name="siteurl" type="hidden" value="<?php if(isset($_POST['siteurl'])) { echo $_POST['siteurl']; } else { echo $fullpath;} ?>" />
		<input name="lang" type="hidden" value="<?php echo $LANG; ?>" />
		<p><b><?php i18n('LABEL_USERNAME'); ?>:</b><br /><input class="text" name="user" type="text" value="<?php if(isset($_POST['user'])) { echo $_POST['user']; } ?>" /></p>
		<p><b><?php i18n('LABEL_EMAIL'); ?>:</b><br /><input class="text" name="email" type="text" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; } ?>" /></p>
		<p><input class="submit" type="submit" name="submitted" value="<?php i18n('LABEL_INSTALL'); ?>" /></p>
	</form>
	</div>
<?php } ?>
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>
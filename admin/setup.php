<?php 
/**
 * Setup
 *
 * Second step of installation (install.php). Sets up initial files & structure
 *
 * @package GetSimple
 * @subpackage Installation
 */

# setup inclusions
$load['plugin'] = true;
if(isset($_POST['lang']) && trim($_POST['lang']) != '') { $LANG = $_POST['lang']; }
include('inc/common.php');

# default variables
if(defined('GSLOGINSALT')) { $logsalt = GSLOGINSALT;} else { $logsalt = null; }
$kill = ''; 
$status = ''; 
$err = null; 
$message = null; 
$random = null;
$success = false;
$fullpath = suggest_site_path();	
$path_parts = suggest_site_path(true);   

# if the form was submitted, continue
if(isset($_POST['submitted'])) {
	if($_POST['sitename'] != '') { 
		$SITENAME = htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'); 
	} else { 
		$err .= i18n_r('WEBSITENAME_ERROR') .'<br />'; 
	}
	
	$urls = $_POST['siteurl']; 
	if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $urls)) {
		$SITEURL = tsl($_POST['siteurl']); 
	} else {
		$err .= i18n_r('WEBSITEURL_ERROR') .'<br />'; 
	}
	
	if($_POST['user'] != '') { 
		$USR = strtolower($_POST['user']);
	} else {
		$err .= i18n_r('USERNAME_ERROR') .'<br />'; 
	}
	
	if (! check_email_address($_POST['email'])) {
		$err .= i18n_r('EMAIL_ERROR') .'<br />'; 
	} else {
		$EMAIL = $_POST['email'];
	}

	# if there were no errors, continue setting up the site
	if ($err == '')	{
		
		# create new password
		$random = createRandomPassword();
		$PASSWD = passhash($random);
		
		# create user xml file
		$file = _id($USR).'.xml';
		createBak($file, GSUSERSPATH, GSBACKUSERSPATH);
		$xml = new SimpleXMLElement('<item></item>');
		$xml->addChild('USR', $USR);
		$xml->addChild('PWD', $PASSWD);
		$xml->addChild('EMAIL', $EMAIL);
		$xml->addChild('HTMLEDITOR', '1');
		$xml->addChild('TIMEZONE', $TIMEZONE);
		$xml->addChild('LANG', $LANG);
		if (! XMLsave($xml, GSUSERSPATH . $file) ) {
			$kill = i18n_r('CHMOD_ERROR');
		}
		
		# create password change trigger file
		$flagfile = GSUSERSPATH . _id($USR).".xml.reset";
		copy(GSUSERSPATH . $file, $flagfile);
		
		# create new website.xml file
		$file = 'website.xml';
		$xmls = new SimpleXMLExtended('<item></item>');
		$note = $xmls->addChild('SITENAME');
		$note->addCData($SITENAME);
		$note = $xmls->addChild('SITEURL');
		$note->addCData($SITEURL);
		$xmls->addChild('TEMPLATE', 'Innovation');
		$xmls->addChild('PRETTYURLS', '');
		$xmls->addChild('PERMALINK', '');
		if (! XMLsave($xmls, GSDATAOTHERPATH . $file) ) {
			$kill = i18n_r('CHMOD_ERROR');
		}
		
		# create default index.xml page
		$init = GSDATAPAGESPATH.'index.xml'; 
		$temp = GSADMININCPATH.'tmp/tmp-index.xml';
		if (! file_exists($init))	{
			copy($temp,$init);
			$xml = simplexml_load_file($init); 
			$xml->pubDate = date('r');
			$xml->asXML($init);
		}

		# create default components.xml page
		$init = GSDATAOTHERPATH.'components.xml';
		$temp = GSADMININCPATH.'tmp/tmp-components.xml'; 
		if (! file_exists($init)) {
			copy($temp,$init);
		}
		
		# create default 404.xml page
		$init = GSDATAOTHERPATH.'404.xml';
		$temp = GSADMININCPATH.'tmp/tmp-404.xml'; 
		if (! file_exists($init)) {
			copy($temp,$init);
		}

		# create root .htaccess file
		 if ( !function_exists('apache_get_modules') or in_arrayi('mod_rewrite',apache_get_modules())) {
		 	$temp = GSROOTPATH .'temp.htaccess';
		 	$init = GSROOTPATH.'.htaccess';
			
			if(file_exists($temp)) {				
				$temp_data = file_get_contents(GSROOTPATH .'temp.htaccess');
				$temp_data = str_replace('**REPLACE**',tsl($path_parts), $temp_data);
				$fp = fopen($init, 'w');
				fwrite($fp, $temp_data);
				fclose($fp);
				if (!file_exists($init)) {
					$kill .= sprintf(i18n_r('ROOT_HTACCESS_ERROR'), 'temp.htaccess', '**REPLACE**', tsl($path_parts)) . '<br />';
				} else if(file_exists($temp)){
					unlink($temp);
				}
			}	
		} 
	
		# create gsconfig.php if it doesn't exist yet.
		$init = GSROOTPATH.'gsconfig.php';
		$temp = GSROOTPATH.'temp.gsconfig.php';
		if (file_exists($init)) {
			if(file_exists($temp)) unlink($temp);
			if (file_exists($temp)) {
				$kill .= sprintf(i18n_r('REMOVE_TEMPCONFIG_ERROR'), 'temp.gsconfig.php') . '<br />';
			}
		} else {
			rename($temp, $init);
			if (!file_exists($init)) {
				$kill .= sprintf(i18n_r('MOVE_TEMPCONFIG_ERROR'), 'temp.gsconfig.php', 'gsconfig.php') . '<br />';
			}
		}
		
		# send email to new administrator
		$subject  = $site_full_name .' '. i18n_r('EMAIL_COMPLETE');
		$message .= '<p>'.i18n_r('EMAIL_USERNAME') . ': <strong>'. stripslashes($_POST['user']).'</strong>';
		$message .= '<br>'. i18n_r('EMAIL_PASSWORD') .': <strong>'. $random.'</strong>';
		$message .= '<br>'. i18n_r('EMAIL_LOGIN') .': <a href="'.$SITEURL.$GSADMIN.'/">'.$SITEURL.$GSADMIN.'/</a></p>';
		$message .= '<p><em>'. i18n_r('EMAIL_THANKYOU') .' '.$site_full_name.'!</em></p>';
		$status   = sendmail($EMAIL,$subject,$message);
		# activate default plugins
		change_plugin('anonymous_data.php',true);
		change_plugin('InnovationPlugin.php',true);

		# set the login cookie, then redirect user to secure panel		
		create_cookie();		
		$success = true;
	}
}

get_template('header', $site_full_name.' &raquo; '. i18n_r('INSTALLATION')); 

?>
	
		<h1><?php echo $site_full_name; ?></h1>
	</div>
</div>
<div class="wrapper">
	<div id="maincontent">
		<?php
			# display error or success messages 
			if ($status == 'success') {
				echo '<div class="updated">'. i18n_r('NOTE_REGISTRATION') .' '. $_POST['email'] .'</div>';
			} 
			elseif ($status == 'error') {
				echo '<div class="error">'. i18n_r('NOTE_REGERROR') .'.</div>';
			}
			if ($kill != '') {
				$success = false;
				echo '<div class="error">'. $kill .'</div>';
			}
			if ($err != '') {
				$success = false;				
				echo '<div class="error">'. $err .'</div>';
			}
			if ($random != ''){
				echo '<div class="updated">'.i18n_r('NOTE_USERNAME').' <b>'. stripslashes($_POST['user']) .'</b> '.i18n_r('NOTE_PASSWORD').' <b>'. $random .'</b> &nbsp&raquo;&nbsp; <a href="support.php?updated=2">'.i18n_r('EMAIL_LOGIN').'</a></div>';
				$_POST = null;
			}

	if (!$success) { ?>
		<div class="main" >
			<h3><?php echo $site_full_name .' '. i18n_r('INSTALLATION'); ?></h3>
			<form action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
				<input name="siteurl" type="hidden" value="<?php echo $fullpath; ?>" />
				<input name="lang" type="hidden" value="<?php echo $LANG; ?>" />
				<p><label for="sitename" ><?php i18n('LABEL_WEBSITE'); ?>:</label><input class="text" id="sitename" name="sitename" type="text" value="<?php if(isset($_POST['sitename'])) { echo $_POST['sitename']; } ?>" /></p>
				<p><label for="user" ><?php i18n('LABEL_USERNAME'); ?>:</label><input class="text" name="user" id="user" type="text" value="<?php if(isset($_POST['user'])) { echo $_POST['user']; } ?>" /></p>
				<p><label for="email" ><?php i18n('LABEL_EMAIL'); ?>:</label><input class="text" name="email" id="email" type="email" value="<?php if(isset($_POST['email'])) { echo $_POST['email']; } ?>" /></p>
				<p><input class="submit" type="submit" name="submitted" value="<?php i18n('LABEL_INSTALL'); ?>" /></p>
			</form>
		</div>
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>

<?php } ?>

<?php 
/**
 * Reset Password
 *
 * Resets the password for GetSimple control panel access
 *
 * @package GetSimple
 * @subpackage Login
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// Variable settings
$file = 'user.xml';

if (file_exists(GSDATAOTHERPATH . $file))
{
	$data = getXML(GSDATAOTHERPATH . $file);
	$USR = $data->USR;
	$EMAIL = $data->EMAIL;
}

if(isset($_POST['submitted']))
{
	$nonce = $_POST['nonce'];
	if(!check_nonce($nonce, "reset_password"))
		die("CSRF detected!");

	if(isset($_POST['email']))
	{
		if($_POST['email'] == $EMAIL)
		{
			// create new random password
			$random = createRandomPassword();
			
			// create new users.xml file
			$bakpath = GSBACKUPSPATH."other/";
			createBak($file, GSDATAOTHERPATH, $bakpath);
			
			$flagfile = GSBACKUPSPATH."other/user.xml.reset";
			copy(GSDATAOTHERPATH . $file, $flagfile);
			
			$xml = @new SimpleXMLElement('<item></item>');
			$xml->addChild('USR', @$USR);
			$xml->addChild('PWD', passhash($random));
			$xml->addChild('EMAIL', @$EMAIL);
			XMLsave($xml, GSDATAOTHERPATH . $file);
			
			// send the email with the new password
			$subject = $site_full_name .' '. i18n_r('RESET_PASSWORD') .' '. i18n_r('ATTEMPT');
			$message = "'". cl($SITENAME) ."' ". i18n_r('RESET_PASSWORD') ." ". i18n_r('ATTEMPT');
			$message .= '<br>-------------------------------------------------------<br>';
			$message .= "<br>". i18n_r('LABEL_USERNAME').": ". $USR;
			$message .= "<br>". i18n_r('NEW_PASSWORD').": ". $random;
			$message .= '<br><br>'. i18n_r('EMAIL_LOGIN') .': <a href="'.$SITEURL.'admin/">'.$SITEURL.'admin/</a>';
			exec_action('resetpw-success');
			$status = sendmail($EMAIL,$subject,$message);
			
			redirect("resetpassword.php?upd=pwd-".$status);
		}
		else
		{
			exec_action('resetpw-error');
			redirect("resetpassword.php?upd=pwd-error");
		} 
	}
	else 
	{
		redirect("resetpassword.php?upd=pwd-error");
	}
} 
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('RESET_PASSWORD')); ?>
<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('RESET_PASSWORD'); ?></h1>
</div>
</div>
<div class="wrapper">
	
<?php include('template/error_checking.php'); ?>

<div id="maincontent">
	<div class="main" >
	
	<h3><?php i18n('RESET_PASSWORD'); ?></h3>
	<p><?php i18n('MSG_PLEASE_EMAIL'); ?>.</p>
	
	<form class="fullform" action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" accept-charset="utf-8" >
		<input name="nonce" id="nonce" type="hidden" value="<?php echo get_nonce("reset_password");?>"/>
		<p><b><?php i18n('LABEL_EMAIL'); ?>:</b><br /><input class="text" name="email" type="text" value="" /></p>
		<p><input class="submit" type="submit" name="submitted" value="<?php echo i18n('SEND_NEW_PWD'); ?>" /></p>
	</form><p><a href="index.php"><?php i18n('LOGIN'); ?></a></p>
	</div>
	
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>
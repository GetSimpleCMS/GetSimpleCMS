<?php 
/**
 * Reset Password
 *
 * Resets the password for GetSimple control panel access
 *
 * @package GetSimple
 * @subpackage Login
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');

if(getDef('GSALLOWRESETPASS',true) === false) die();

if(isset($_POST['submitted'])){
	check_for_csrf("reset_password");	
		
	$randSleep = rand(250000,2000000); // random sleep for .25 to 2 seconds

	if(isset($_POST['username']) and !empty($_POST['username']))	{

		# user filename
		$file = _id($_POST['username']).'.xml';
		
		# get user information from existing XML file
		
		if (filepath_is_safe(GSUSERSPATH . $file,GSUSERSPATH) && file_exists(GSUSERSPATH . $file)) {
			$data   = getXML(GSUSERSPATH . $file);
			$userid = strtolower($data->USR);
			$EMAIL  = $data->EMAIL;
			
			if(strtolower($_POST['username']) === $userid) {
				# create new random password
				$random = createRandomPassword();
				// $random = '1234';
				
				# create backup
				backup_datafile(GSUSERSPATH.$file);
				
				# copy user file into password change trigger file
				$flagfile = GSUSERSPATH . getPWDresetName(_id($userid), 'xml');
				copy_file(GSUSERSPATH . $file, $flagfile);
				
				# change password and resave xml file
				$data->PWD = passhash($random); 
				$status = XMLsave($data, GSUSERSPATH . $file);
				
				# send the email with the new password
				$subject = $site_full_name .' '. i18n_r('RESET_PASSWORD') .' '. i18n_r('ATTEMPT');
				$message = "<p>". cl($SITENAME) ." ". i18n_r('RESET_PASSWORD') ." ". i18n_r('ATTEMPT').'</p>';
				$message .= "<p>". i18n_r('LABEL_USERNAME').": <strong>". $userid."</strong>";
				$message .= "<br>". i18n_r('NEW_PASSWORD').": <strong>". $random."</strong>";
				$message .= '<br>'. i18n_r('EMAIL_LOGIN') .': <a href="'.$SITEURL . $GSADMIN.'/">'.$SITEURL . $GSADMIN.'/</a></p>';
				exec_action('resetpw-success'); // @hook resetpw-success a user password reset occured
				$emailstatus = sendmail($EMAIL,$subject,$message);
				# if email fails, we do nothing, maybe handle this in the future
				# show the result of the reset attempt
				usleep($randSleep);
				redirect("resetpassword.php?upd=pwd-". ($status && $emailstatus ? 'success' : 'error'));
			} else{
				# username doesnt match listed xml username
				exec_action('resetpw-error'); // @hook resetpw-error a user password reset failed
				usleep($randSleep);
				redirect("resetpassword.php?upd=pwd-success");
			} 
		} else {
			# no user exists for this username, but do not show this to the submitter		
			usleep($randSleep);
			redirect("resetpassword.php?upd=pwd-success");
		}
	} else {
		
		# no username was submitted
		redirect("resetpassword.php?upd=pwd-error");
	}
} 

$pagetitle = i18n_r('RESET_PASSWORD');
get_template('header');

?>
</div>
</div>
<div class="wrapper clearfix">
	
	<?php include('template/error_checking.php'); ?>
	
	<div id="maincontent">
		<div class="main" >
		
		<h3><?php i18n('RESET_PASSWORD'); ?></h3>
		<p class="desc"><?php i18n('MSG_PLEASE_EMAIL'); ?></p>
		
		<form class="login" action="" class="entersubmit" method="post" >
			<input name="nonce" id="nonce" type="hidden" value="<?php echo get_nonce("reset_password");?>"/>
			<p><b><?php i18n('LABEL_USERNAME'); ?>:</b><br /><input class="text" name="username" type="text" value="" /></p>
			<p><input class="submit" type="submit" name="submitted" value="<?php echo i18n_r('SEND_NEW_PWD'); ?>" /></p>
		</form>
		<p class="cta"><a href="<?php echo $SITEURL; ?>"><?php i18n('BACK_TO_WEBSITE'); ?></a> &nbsp;
		<?php if(getDef('GSALLOWLOGIN',true)) { ?> | &nbsp; <a href="index.php"><?php echo i18n_r('CONTROL_PANEL'); ?></a>
		<?php } ?>
		</p>
		</div>
		
	</div>

<div class="clear"></div>
<?php get_template('footer'); ?>
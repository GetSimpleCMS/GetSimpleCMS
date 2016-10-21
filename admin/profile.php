<?php

/**
 * Settings
 *
 * Displays and changes website settings 
 *
 * @package GetSimple
 * @subpackage Settings
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-profile');

$showpermfail = true; // true, throw errors on failed permission attempts, else silently ignores your requests

// default permissions, allow based on is user superuser and gs permission definitions
$allowadd  = ($USR == getSuperUserId()) && getDef('GSPROFILEALLOWADD',true);
$allowedit = ($USR == getSuperUserId()) && getDef('GSPROFILEALLOWEDIT',true);

// init
$adding     = false; // flag for doing user creation
$editing    = false; // flag for doing user edit
$userid     = $USR;
$lang_array = getFiles(GSLANGPATH);

$pwd1 = $error = $success = $pwd2 = $editorchck = null;
$permerror = '';

if(isset($_REQUEST['add']))     $adding  = true;
else if(isset($_GET['userid'])) $editing = true;

if($adding){
	if(!exec_secfilter('profile-adduser', $allowadd)){ // @secfilter profile-adduser verify profile add new user
		$userid    = $USR;
		$permerror = i18n_r('ER_REQ_PROC_FAIL');
		$adding    = false;
	} 
	else{
		$userid = '';
	}
}

if($editing){
	$userid = _id($_GET['userid']);
	
	if($userid !== $USR){

		if(file_exists(GSUSERSPATH. _id($userid).'.xml')){
			if(!exec_secfilter('profile-edituser',$allowedit, array($userid))){ // @secfilter profile-edituser verify profile edit existing user
				$permerror = i18n_r('ER_REQ_PROC_FAIL');
				$editing = false;
			}
		}
		else {
			$permerror = i18n_r('INVALID_USER');
			$editing = false;
		}

		if(!$editing) $userid = $USR; // FAIL, set userid back to USR
	}
}


// throw errors
if(!empty($permerror) && $showpermfail) $error = $permerror;

// load user data if editing
if(!empty($userid)){
	$file = _id($userid) .'.xml';
	// file traversal protection and checks if file exists at the same time
	if(!filepath_is_safe(GSUSERSPATH . $file,GSUSERSPATH)) die(i18n_r('ER_REQ_PROC_FAIL'));
	
	if($editing && !file_exists(GSUSERSPATH.$file)) $error = i18n_r('INVALID_USER');
	// else populate data for user
	$data     = getXML(GSUSERSPATH . $file);
	$password = $data->PWD; // set password, since we dont need to resave it all the time
}
else{
	// empty user defaults
	$data = new stdClass();
	$data->HTMLEDITOR = true;
	$data->LANG     = '';
	$data->EMAIL    = '';
	$data->TIMEZONE = $SITETIMEZONE;
	$data->NAME     = '';
}

# if the undo command was invoked
if (isset($_GET['undo'])) {
	if($_GET['userid'] !== $userid) die(i18n_r('ER_REQ_PROC_FAIL')); // if not allowedtoedit then userid is $USR now, so stop undo actions
	check_for_csrf("undo");
	// perform undo
	
	// undo add new user
	if(isset($_GET['new'])){
		delete_file(GSUSERSPATH.$file);
		redirect('profile.php?success='.urlencode(strip_tags(sprintf(i18n_r('ER_HASBEEN_DEL'),$userid))));
	}

	// undo edit user
	restore_datafile(GSUSERSPATH.$file);
	redirect('profile.php?upd=profile-restored&userid='.$userid);
}

# was the form submitted?
if(isset($_POST['submitted']) && isset($_POST['user'])){
	check_for_csrf("save_profile");

	do{
		// if editing and post userid not match get userid
		// @todo perhaps use nonce here instead
		if($editing && $userid !== _id($_POST['user'])){
			$error = i18n_r('ER_REQ_PROC_FAIL');
			break;
		}

		$userid = _id($_POST['user']);
		$file   = $userid .'.xml';


		if($adding && path_is_safe(GSUSERSPATH . $file,GSUSERSPATH)){
		    $error = i18n_r('INVALID_USER'); // user already exists
		    break;
		}    
		
		if(!path_is_safe(dirname(GSUSERSPATH . $file),GSUSERSPATH,true)){
			$error = i18n_r('INVALID_USER');
			break;
		}

		debugLog("saving profile " . $userid);

	 	if(isset($_POST['name']))				$name       = var_in($_POST['name']);
	 	if(isset($_POST['email']))  			$email      = var_in($_POST['email'],'email');
	 	if(isset($_POST['timezone']))  			$timezone   = var_in($_POST['timezone']);
	 	if(isset($_POST['lang']))  				$lang       = var_in($_POST['lang']);
	 	if(isset($_POST['show_htmleditor']))	$htmleditor = var_in($_POST['show_htmleditor']);
	 	else $htmleditor = '';
		
		# check to see if passwords are changing
		if(isset($_POST['sitepwd']))         { $pwd1 = $_POST['sitepwd']; }
		if(isset($_POST['sitepwd_confirm'])) { $pwd2 = $_POST['sitepwd_confirm']; }
		
		// do password checking
		if ($pwd1 != $pwd2 || ($adding === true && (empty($pwd1) || $pwd1 !== $pwd2))){
			# passwords do not match if changing or adding users passwords
			$error = i18n_r('PASSWORD_NO_MATCH');
			$password = '';
		}
		else if($pwd1 != '' && strlen($pwd1) < getDef('GSPASSLENGTHMIN')){
			# password cannot be shorter than GSPASSLENGTH
			$error    = i18n_r('PASSWORD_TOO_SHORT');
			$password = '';
		}
		else if( $pwd1 != '' ){
			# password changed
			$newpassword = $pwd1; // set new password
			exec_action('profile-password-changed'); // @hook profile-password-changed a users password was changed
			$password = passhash($newpassword); // set new password
		}

		// check valid lang files
		if(isset($lang_array) && !in_array($lang.'.php', $lang_array) && !in_array($lang.'.PHP', $lang_array)) $lang = ''; 

		// create new xml
		$xml = new SimpleXMLElement('<item></item>');
		$xml->addChild('USR', $userid);
		$xml->addChild('NAME', $name);
		$xml->addChild('PWD', $password);
		$xml->addChild('EMAIL', $email);
		$xml->addChild('HTMLEDITOR', $htmleditor);
		$xml->addChild('TIMEZONE', $timezone);
		$xml->addChild('LANG', $lang);
		$data = $xml;

		if(!empty($error) || empty($password)) break;

		# create user xml file
		backup_datafile(GSUSERSPATH.$file);
		
		// remove pass word reset
		$resetfile = GSUSERSPATH . getPWDresetName(_id($userid), 'xml');
		if (file_exists($resetfile)) delete_file($resetfile);

		exec_action('settings-user'); // @hook settings-user LEGACY pre-save of a users settings
		exec_action('profile-save');  // @hook profiel-user pre-save of a users settings
		
		$status = XMLsave($xml, GSUSERSPATH . $file);

		if (!$status) {
			$error = i18n_r('CHMOD_ERROR');
			break;
		}

		# see new language file immediately
		if(!empty($lang)) include(GSLANGPATH.$lang.'.php');
		
		if($adding){
			$success = sprintf(i18n_r('ER_YOUR_CHANGES'), $userid).'. <a href="profile.php?undo&new&nonce='.get_nonce("undo").'&userid='.$userid.'">'.i18n_r('UNDO').'</a>';
			exec_action('profile-added'); // @hook user-added a user was added
			// redirect("?userid=".$userid.'&success='.$success);
			// @todo: cant redirect since we have no notifications for saving profiles
			// done adding
			$adding  = false;
			$editing = true;			
		}
		else {
			$success = sprintf(i18n_r('ER_YOUR_CHANGES'), $userid).'. <a href="profile.php?undo&nonce='.get_nonce("undo").'&userid='.$userid.'">'.i18n_r('UNDO').'</a>';
			exec_action('profile-edited'); // @hook user-edit  a user was edited
		}
	}
	while (false);
}

# are any of the control panel checkboxes checked?
if ($data->HTMLEDITOR != '' ) { $editorchck = 'checked'; }

# get all available language files
// if ($data->LANG == ''){ $LANG = GSDEFAULTLANG; }

$langs = '<option value="">-- '.i18n_r('NONE').' --</option>';
if (count($lang_array) != 0) {
	sort($lang_array);
	$sel = '';
	foreach ($lang_array as $lfile){
		$lfile = basename($lfile,".php");
		if ($data->LANG == $lfile) { $sel="selected"; }
		$langs .= '<option '.$sel.' value="'.$lfile.'" >'.$lfile.'</option>';
		$sel = '';
	}
}

$pagetitle = i18n_r('USER_PROFILE');
get_template('header');

$userheading = empty($userid) ? "<span>/ ". i18n_r('NEW_USER') ."</span>" : "<span>/ $userid </span>";

// if($adding)  $userheading = '<span>adding</span> ' . $userheading;
// if($editing) $userheading = '<span>editing</span> ' . $userheading;

?>

<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
			<h3 class="floated"><?php i18n('USER_PROFILE'); echo $userheading; ?></h3>
			<div class="edit-nav clearfix" >
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>
			
			<!-- user form -->
			<form class="largeform" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_profile"); ?>" />
			<?php if($adding === true){ ?> <input id="add" name="add" type="hidden" value="1" /> <?php } ?>
		
			<div class="leftsec">
				<p><label for="user" ><?php i18n('LABEL_USERNAME');?>:</label><input class="text" id="user" name="user" type="text" <?php echo $adding === true ? '' : 'readonly'; ?> value="<?php echo $userid; ?>" /></p>
			</div>
			<div class="rightsec">
				<p><label for="email" ><?php i18n('LABEL_EMAIL');?>:</label><input class="text" id="email" name="email" type="email" value="<?php echo $data->EMAIL; ?>" /></p>
				<?php if (! check_email_address($data->EMAIL)) {
					echo '<p style="margin:-15px 0 20px 0;color:#D94136;font-size:11px;" >'.i18n_r('WARN_EMAILINVALID').'</p>';
				}?>
			</div>
			<div class="clear"></div>
			<div class="leftsec">
				<p><label for="name" ><?php i18n('LABEL_DISPNAME');?>:</label>
				<span style="margin:0px 0 5px 0;font-size:12px;color:#999;" ><?php i18n('DISPLAY_NAME');?></span>			
				<input class="text" id="name" name="name" type="text" value="<?php echo $data->NAME; ?>" /></p>
			</div>		
			<div class="clear"></div>		
			<div class="leftsec">
				<p><label for="timezone" ><?php i18n('LOCAL_TIMEZONE');?>:</label>
				<select class="text" id="timezone" name="timezone"> 
				<?php if ($data->TIMEZONE == '') { echo '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>'; } else { echo '<option selected="selected"  value="'. $data->TIMEZONE .'">'. $data->TIMEZONE .'</option>'; } ?>
				<?php include('inc/timezone_options.txt'); ?>
				</select>
				</p>
			</div>
			<div class="rightsec">
				<p><label for="lang" ><?php i18n('LANGUAGE');?>: <span class="right"><a href="http://get-simple.info/docs/languages" target="_blank" ><?php i18n('MORE');?></a></span></label>
				<select name="lang" id="lang" class="text">
					<?php echo $langs; ?>
				</select>
				</p>
			</div>
			<div class="clear"></div>
			<div class="widesec">
				<p class="inline" ><input name="show_htmleditor" id="show_htmleditor" type="checkbox" value="1" <?php echo $editorchck; ?> /> &nbsp;<label for="show_htmleditor" ><?php i18n('ENABLE_HTML_ED');?></label></p>
			</div>
			<?php
				if($editing) exec_action('profile-extras-edit'); // @hook profile-extras-edit extra profile settings when editing existing users
				if($adding)  exec_action('profile-extras-add');  // @hook profile-extras-add extra profile settings when  adding new user
			
				if(!$editing && !$adding) exec_action('settings-user-extras'); // @hook settings-user-extras LEGACY extra user profile settings html, not enabled for edit and adds in 3.4
				exec_action('profile-extras'); // @hook profile-extras extra profile settings
			?>
			
			<p class="section" style="margin:0px 0 5px 10px;font-size:12px;color:#999;" ><?php $adding === true ? i18n('PROVIDE_PASSWORD') : i18n('ONLY_NEW_PASSWORD');?>:</p>
			<div class="leftsec">
				<p><label for="sitepwd" ><?php $adding === true ? i18n('PASSWORD') : i18n('NEW_PASSWORD');?>:</label><input autocomplete="off" class="text" id="sitepwd" name="sitepwd" type="password" value="" /></p>
			</div>
			<div class="rightsec">
				<p><label for="sitepwd_confirm" ><?php i18n('CONFIRM_PASSWORD');?>:</label><input autocomplete="off" class="text" id="sitepwd_confirm" name="sitepwd_confirm" type="password" value="" /></p>
			</div>
			<div class="clear"></div>
			
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitted" value="<?php i18n('BTN_SAVEUPDATES');?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="profile.php?cancel"><?php i18n('CANCEL'); ?></a>
			</p>
		</div><!-- /main -->
	</form>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-settings.php'); ?>
	</div>

</div>

<?php
get_template('footer'); 
/* ?> */

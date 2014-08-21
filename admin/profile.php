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

$allowedit  = true; // tmp flag for edit permission
$allowadd   = false; // tmp flag for create permission

$showpermfail = false; // true, throw errors on failed permission attempts, else silently ignores your requests
$permerror = '';      // init

// @todo these flags will probably be implemented as functions that can be manipulated and called out
// they will be context aware of the user being edited etc, to handle group heiracrhy and protected accounts

$adding     = false; // flag for doing user creation
$userid     = $USR;
$lang_array = getFiles(GSLANGPATH);

$pwd1 = $error = $success = $pwd2 = $editorchck = null;

// if not submitting setup userid based on edit or add of custom userid
if(!isset($_POST['submitted'])){
	if(isset($_GET['userid'])){
		// Editing an existing user
		$userid = _id($_GET['userid']); // set user id first so allowedit mechanism can check $userid directly
		if($userid !== $USR && !$allowedit) {
			$userid = $USR; // revert to $USR if not allowed
			// NOT ALLOWED TO EDIT
			$permerror = i18n_r('ER_REQ_PROC_FAIL');
		}
	}
	else if(isset($_GET['add'])){
		// adding a new user
		if(!$allowadd) {
			$userid = $USR;
			// NOT ALLOWED TO ADD
			$permerror = i18n_r('ER_REQ_PROC_FAIL');
		}
		else {
			$adding = true;
			$userid = '';
		}
	}
}

// throw errors
if(!empty($permerror) && $showpermfail) $error = $permerror;

// check if editing user is valid
if(!empty($userid)){
	$file = _id($userid) .'.xml';
	// file tranversal protection and checks if file exists at the same time
	if(!filepath_is_safe(GSUSERSPATH . $file,GSUSERSPATH)) die(i18n_r('ER_REQ_PROC_FAIL'));

	// else populate data for user
	$data  = getXML(GSUSERSPATH . $file);
	$password = $data->PWD;
}

# if the undo command was invoked
if (isset($_GET['undo'])) {
	if($_GET['userid'] !== $userid) die(i18n_r('ER_REQ_PROC_FAIL')); // if not allowedtoedit then userid is $USR now, so stop undo actions
	check_for_csrf("undo");	
	# perform undo
	$success = restore_datafile(GSUSERSPATH.$file);
	# redirect back to yourself to show the new restored data
	redirect('profile.php?upd=profile-restored&userid='.$userid);
}

# was the form submitted?
if(isset($_POST['submitted'])) {

	check_for_csrf("save_profile");	
		   
	// if adding a new user
	if(isset($_POST['add']) && $_POST['add'] == 1 && $allowadd && isset($_POST['user'])) {
		$adding = true;
		$userid = strtolower($_POST['user']);
		$file   = _id($userid) .'.xml';
		if(path_is_safe(GSUSERSPATH . $file,GSUSERSPATH)) die(i18n('INVALID_USER'));
		if(!path_is_safe(dirname(GSUSERSPATH . $file),GSUSERSPATH,true)) die(i18n('INVALID_USER'));
	}
	else if(isset($_POST['user']) && $allowedit){
		// editing an existing user other than self
		// @todo use custom nonce or hash checking to make sure username was not changed
		$userid = strtolower($_POST['user']);
		$file   = _id($userid) .'.xml';
		if(!path_is_safe(dirname(GSUSERSPATH . $file),GSUSERSPATH,true)) die(i18n('INVALID_USER'));
	}
 	if(isset($_POST['name']))				$name       = var_in($_POST['name']);
 	if(isset($_POST['email']))  			$email      = var_in($_POST['email'],'email');
 	if(isset($_POST['timezone']))  			$timezone   = var_in($_POST['timezone']);
 	if(isset($_POST['lang']))  				$lang       = var_in($_POST['lang']);
 	if(isset($_POST['show_htmleditor']))	$htmleditor = var_in($_POST['show_htmleditor']);
 	else $htmleditor = '';
		
	# check to see if passwords are changing
	if(isset($_POST['sitepwd'])) { $pwd1 = $_POST['sitepwd']; }
	if(isset($_POST['sitepwd_confirm'])) { $pwd2 = $_POST['sitepwd_confirm']; }
	if ($pwd1 != $pwd2 || ($adding === true && (empty($pwd1) || $pwd1 !== $pwd2) ) )	{
		#passwords do not match 
		$error = i18n_r('PASSWORD_NO_MATCH');
	} else {
		# password cannot be null
		if ( $pwd1 != '' ) {
			$password = passhash($pwd1);
		}
		
		// check valid lang files
		if(!in_array($lang.'.php', $lang_array) and !in_array($lang.'.PHP', $lang_array)) $lang = ''; 

		# create user xml file
		backup_datafile(GSUSERSPATH.$file);
		// remove pass word reset
		if (file_exists(GSUSERSPATH . _id($userid).'.xml.reset')) { delete_file(GSUSERSPATH . _id($userid).'.xml.reset'); }	

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

		exec_action('settings-user');
		
		if (! XMLsave($xml, GSUSERSPATH . $file) ) {
			$error = i18n_r('CHMOD_ERROR');
		}

		# see new language file immediately
		if(!empty($lang)) include(GSLANGPATH.$lang.'.php');
		
		if (!$error) {
			$success = sprintf(i18n_r('ER_YOUR_CHANGES'), $userid).'. <a href="profile.php?undo&nonce='.get_nonce("undo").'&userid='.$userid.'">'.i18n_r('UNDO').'</a>';

			if($adding) exec_action('user-added');
			else exec_action('user-edited');
		}
	}
}

# are any of the control panel checkboxes checked?
if ($data->HTMLEDITOR != '' ) { $editorchck = 'checked'; }

# get all available language files
// if ($data->LANG == ''){ $LANG = GSDEFAULTLANG; }

if (count($lang_array) != 0) {
	sort($lang_array);
	$sel = ''; $langs = '';
	foreach ($lang_array as $lfile){
		$lfile = basename($lfile,".php");
		if ($data->LANG == $lfile) { $sel="selected"; }
		$langs .= '<option '.$sel.' value="'.$lfile.'" >'.$lfile.'</option>';
		$sel = '';
	}
} else {
	$langs = '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>';
}

$pagetitle = i18n_r('USER_PROFILE');
get_template('header');

$userheading = empty($userid) ? "<span> / ". i18n_r('NEW_USER') ."</span>" : "<span> / $userid </span>";

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<form class="largeform" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_profile"); ?>" />
		<?php if($adding === true){ ?> <input id="add" name="add" type="hidden" value="1" /> <?php } ?>
		
		<div class="main">
			<div id="profile" class="" >
				<h3><?php i18n('USER_PROFILE'); echo $userheading; ?></h3>
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
				<p class="inline" ><input name="show_htmleditor" id="show_htmleditor" type="checkbox" value="1" <?php echo $editorchck; ?> /> &nbsp;<label for="show_htmleditor" ><?php i18n('ENABLE_HTML_ED');?></label></p>
				
				<?php exec_action('settings-user-extras'); ?>
				
				<p style="margin:0px 0 5px 0;font-size:12px;color:#999;" ><?php $adding === true ? i18n('PROVIDE_PASSWORD') : i18n('ONLY_NEW_PASSWORD');?>:</p>
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

			</div><!-- /section -->
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

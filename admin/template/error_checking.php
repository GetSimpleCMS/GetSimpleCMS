<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Error Checking
 *
 * Displays error and success messages	
 *
 * @package GetSimple
 */
 
	if ( file_exists(GSUSERSPATH._id($USR).".xml.reset") && get_filename_id()!='index' && get_filename_id()!='resetpassword' ) {
		echo '<div class="error">'.i18n_r('ER_PWD_CHANGE').'</div>';
	}
	if(isset($_GET['error'])) {
		$error = $_GET['error'];
	}
	if(isset($_GET['upd'])) {
		$update = $_GET['upd'];
	} else {
		$update = '';
	}
	
	if(!isset($err)) { $err = ''; }
	if(!isset($restored)) { $restored = ''; }
	
	if ($update == 'bak-success') { 
		echo '<div class="updated">'. sprintf(i18n_r('ER_BAKUP_DELETED'), $_GET['id']) .'</div>';
	} elseif (isset($error)) { 
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '. $error .'</div>';
	} elseif ($update == 'bak-err') { 
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_REQ_PROC_FAIL').'</div>';
	} elseif ($update == 'edit-success') { 
		echo '<div class="updated">';
		if ($ptype == 'edit') { 
			echo sprintf(i18n_r('ER_YOUR_CHANGES'), $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>';
		} elseif ($ptype == 'restore') {
			echo sprintf(i18n_r('ER_HASBEEN_REST'), $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>';
		} elseif ($ptype == 'delete') {
			echo sprintf(i18n_r('ER_HASBEEN_DEL'), $_GET['id']) .'. <a href="backup-edit.php?p=restore&id='. $_GET['id'] .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>';
		}
		echo '</div>';
	} elseif ($update == 'edit-index') { 
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_CANNOT_INDEX').'.</div>';
	} elseif ($update == 'edit-err') { 
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '. $ptype .'.</div>';
	} elseif ($restored == 'true') { 
		echo '<div class="updated">'.i18n_r('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
	} elseif (isset($_GET['rest']) && $_GET['rest']=='true') { 
		echo '<div class="updated">'.i18n_r('ER_OLD_RESTORED').'. <a href="support.php?undo&nonce='.get_nonce("undo", "support.php").'">'.i18n_r('UNDO').'</a></div>';
	} elseif ($update == 'pwd-success') {
		echo '<div class="updated">'.i18n_r('ER_NEW_PWD_SENT').'. <a href="index.php">'.i18n_r('LOGIN').'</a></div>';
	} elseif ($update == 'pwd-error') {
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_SENDMAIL_ERR').'.</div>';
	} elseif ($update == 'del-success') {
		echo '<div class="updated">'.i18n_r('ER_FILE_DEL_SUC').': <b>'.$_GET['id'].'</b></div>';
	} elseif ($update == 'del-error') {
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_PROBLEM_DEL').'.</div>';
	} elseif ($update == 'comp-success') {
		echo '<div class="updated">'.i18n_r('ER_COMPONENT_SAVE').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
	} elseif ($update == 'comp-restored') {
		echo '<div class="updated">'.i18n_r('ER_COMPONENT_REST').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
	} elseif (isset($_GET['cancel'])) {
		echo '<div class="error">'.i18n_r('ER_CANCELLED_FAIL').'</div>';
	}	elseif (isset($error)) {
		echo '<div class="error">'.$error.'</div>';
	}	elseif (isset($success)) {
		echo '<div class="updated">'.$success.'</div>';
	} elseif (isset($_GET['err'])) {
		echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.$_GET['err'].'</div>';
	} elseif (isset($_GET['success'])) {
		echo '<div class="updated">'.$_GET['success'].'</div>';
	} 
	?>
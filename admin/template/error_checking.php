<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Error Checking
 *
 * Displays error and success messages
 *
 * @package GetSimple
 *
 * You can pass $update(global) directly if not using a redirrect and querystring
 *
 */

 	// do not use these alerts if ajax requests as they will not be seen, and interfere with other alerts
	if ( !requestIsAjax() && $SAFEMODE == 1 ) {
		if(getDef('GSSAFEMODE',true)) doNotify(i18n_r('ER_SAFEMODE'),'error',true);
		else doNotify(i18n_r('ER_SAFEMODE').', <a href="?safemodeoff">'.i18n_r('DISABLE').'</a>','error',true);
	}

	if ( !requestIsAjax() && file_exists(GSUSERSPATH._id($USR).".xml.reset") && get_filename_id()!='index' && get_filename_id()!='resetpassword' ) {
		doNotify(sprintf(i18n_r('ER_PWD_CHANGE'),'profile.php'),'error',true);
	}

	if ( !requestIsAjax() && (!defined('GSNOAPACHECHECK') || GSNOAPACHECHECK == false) and !server_is_apache()) {
		doNotify(i18n_r('WARNING').': <a href="health-check.php">'.i18n_r('SERVER_SETUP').' non-Apache</a>','info');
	}

	if(!isset($update)) $update = '';
	if(isset($_GET['upd'])) 	$update  = var_in($_GET['upd']); // preset update tokens
	if(isset($_GET['id'])) 		$errid   = var_in($_GET['id']);  // preset id argument
	if(isset($_GET['old'])) 	$oldid   = var_in($_GET['old']); // preset old id argument

	if(isset($_GET['success'])) $success = var_in($_GET['success']); // generic success msg
	if(isset($_GET['error'])) 	$error   = var_in($_GET['error']);   // generic error msg
	// if(isset($_GET['err'])) 	$err     = var_in($_GET['err']); // deprecated not used

	if(isset($_GET['updated']) && $_GET['updated'] == 1) $success = i18n_r('SITE_UPDATED'); // RESERVED for update.php only for site upgrades

	$dbn = false; // debug notifications

	switch ( $update ) {
		case 'test' :
			$persistant = true;
			doNotify('info','',$persistant);
			doNotify('info','info',$persistant);
			doNotify('success','success',$persistant);
			doNotify('error','error',$persistant);
			doNotify('warning','warning',$persistant);

			$persistant = false;
			doNotify('info','',$persistant);
			doNotify('info','info',$persistant);
			doNotify('success','success',$persistant);
			doNotify('error','error',$persistant);
			doNotify('warning','warning',$persistant);			
		if(!$dbn) break;
		case 'bak-success':
			// backup delete success
			doNotify(sprintf(i18n_r('ER_BAKUP_DELETED'), $errid) .'</p>','success');
		if(!$dbn) break;
		case 'bak-err':
		    // backup general error
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_REQ_PROC_FAIL'),'error');
		if(!$dbn) break;
		case 'edit-success':
			
			if(!isset($ptype) && isset($_GET['ptype'])) $ptype = var_in($_GET['ptype']); // preset update tokens
			
			$draftqs = '';

			if(isset($_GET['upd-draft']) || (isset($upddraft) && $upddraft == true )){
				$draftqs = '&draft';
				$dispid      = $id . ' (' . titlecase(i18n_r('LABEL_DRAFT')) .')';
			} else $dispid = $id;

			if ($ptype == 'edit' && !isset($oldid)) {
				// page edit changes saved, undo, restore
				doNotify(sprintf(i18n_r('ER_YOUR_CHANGES'), $dispid) .'. <a href="backup-edit.php?p=restore&id='. $id . $draftqs .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','success',true);
			} elseif ($ptype == 'edit' && isset($oldid)) {
				// page edit changes saved, undo, restore with slug change
				doNotify(sprintf(i18n_r('ER_YOUR_CHANGES'), $dispid) .'. <a href="backup-edit.php?p=restore&id='. $oldid .'&new='.$id . $draftqs.'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','success',true);
			} elseif ($ptype == 'restore' && !isset($oldid)) {
				// page has been restored, undo, restore
				doNotify(sprintf(i18n_r('ER_HASBEEN_REST'), $dispid) .'. <a href="backup-edit.php?p=restore&id='. $id . $draftqs .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','info',true);
			} elseif ($ptype == 'restore' && isset($oldid)) {
				// page has been restored undo, restore with slug change
				doNotify(sprintf(i18n_r('ER_HASBEEN_REST'), $dispid) .'. <a href="backup-edit.php?p=restore&id='. $oldid .'&new='.$id . $draftqs.'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','info',true);
			} elseif ($ptype == 'delete') {
				// page has been deleted, undo, restore
				doNotify(sprintf(i18n_r('ER_HASBEEN_DEL'), $errid) .'. <a href="backup-edit.php?p=restore&id='. $errid .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','info',true);
			} else if($ptype == 'new'){
				// new page has been saved, undo, no restore, delete file
				doNotify(sprintf(i18n_r('ER_YOUR_CHANGES'), $dispid) .'. <a href="deletefile.php?id='. $id . $draftqs .'&nonce='.get_nonce("delete", "deletefile.php").'">'.i18n_r('UNDO').'</a>','success',true);
			}
		if(!$dbn) break;
		case 'publish-success':
			doNotify(sprintf(i18n_r('ER_PUBLISH_SUCCESS'),$id),'success');
		break;
		case 'publish-error':
			doNotify(sprintf(i18n_r('ER_PUBLISH_ERROR'),$id),'error');
		break;
		case 'clone-success':
			doNotify(sprintf(i18n_r('CLONE_SUCCESS'), '<a href="edit.php?id='.$errid.'">'.$errid.'</a>'),'success');
		if(!$dbn) break;
		case 'edit-index':
			// cannot edit index slug
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_CANNOT_INDEX'),'error');
		if(!$dbn) break;
		case 'draft-slug':
			// cannot edit draft slug
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_CANNOT_DRAFT'),'error');
		break;
		case 'edit-error':
			// page edit error generic, passed in via type=
			doNotify('<b>'.i18n_r('ERROR').':</b> '. i18n_r('ERROR_OCCURED'),'error');
		if(!$dbn) break;
		case 'pwd-success':
			doNotify(i18n_r('ER_NEW_PWD_SENT').'. <a href="index.php">'.i18n_r('LOGIN').'</a>','info',true,true);
		if(!$dbn) break;
		case 'pwd-error':
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_SENDMAIL_ERR').'.','error',true,true);
		if(!$dbn) break;
		case 'del-success':
			doNotify(i18n_r('ER_FILE_DEL_SUC').': <b>'.$errid.'</b>','success');
		if(!$dbn) break;
		case 'flushcache-success':
			doNotify(i18n_r('FLUSHCACHE-SUCCESS'),'success');
		if(!$dbn) break;
		case 'del-error':
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_PROBLEM_DEL').'.','error');
		if(!$dbn) break;
		case 'comp-success':
			doNotify(i18n_r('ER_COMPONENT_SAVE').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success',true);
		if(!$dbn) break;
		case 'comp-restored':
			doNotify(i18n_r('ER_COMPONENT_REST').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success',true);
		if(!$dbn) break;
		case 'snippet-success':
			doNotify(i18n_r('ER_SNIPPET_SAVE').'. <a href="snippets.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success');
		if(!$dbn) break;
		case 'snippet-restored':
			doNotify(i18n_r('ER_SNIPPET_REST').'. <a href="snippets.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success');
		if(!$dbn) break;
		case 'profile-restored':
			doNotify(i18n_r('ER_PROFILE_RESTORED').'. <a href="profile.php?undo&nonce='.get_nonce("undo").
				'&userid='.$userid.'">'.i18n_r('UNDO').'</a>','success',true);
		if(!$dbn) break;
		case 'settings-success':
			doNotify(i18n_r('ER_SETTINGS_UPD').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success',true);
		if(!$dbn) break;
		case 'settings-restored':
			doNotify(i18n_r('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success',true);
		if(!$dbn) break;
		case 'login-req':
			doNotify(i18n_r('FILL_IN_REQ_FIELD'),'error',true,true);
		if(!$dbn) break;
		case 'login-fail':
			doNotify(i18n_r('LOGIN_FAILED'),'error',true,true);
		break;

		default:
			if     (isset($error))          doNotify('<b>'.i18n_r('ERROR').':</b> '. $error,'error',true);
			elseif (isset($_GET['cancel'])) doNotify(i18n_r('ER_CANCELLED_FAIL'),'error');
			elseif (isset($_GET['logout'])) doNotify(i18n_r('MSG_LOGGEDOUT'),'info',true,true);
			elseif (!empty($err))           doNotify('<b>'.i18n_r('ERROR').':</b> '.$err,'error',true);
			elseif (isset($success))        doNotify($success,'success',false);
		break;
	}

	/**
	 * output a notification
	 * @param  str  $msg     the message text
	 * @param  string  $type    type of message success, error, info, warn
	 * @param  boolean $persist trueto make message not expire and dissapear
	 * @param  boolean $force   force the message to show on auth pages
	 */
	function doNotify($msg, $type = '', $persist = false, $force = false){
		// do not output notifications on auth pages to prevent nonce and data leakage, unless force is true
		if(isAuthPage() && !$force) return; 
		GLOBAL $dbn;
		if($dbn) $persist = true;
		debugLog('notify: ' . $type ." - ".cl($msg));
		echo '<div class="updated notify '. ($type == '' ? '' : 'notify_'.$type.' ') . (!$persist ? 'remove' : 'persist') . '"><p>'.$msg.'</p></div>';
	}

/* ?> */

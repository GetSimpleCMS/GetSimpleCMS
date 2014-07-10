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
	if ( !requestIsAjax() && file_exists(GSUSERSPATH._id($USR).".xml.reset") && get_filename_id()!='index' && get_filename_id()!='resetpassword' ) {
		echo '<div class="error"><p>'.i18n_r('ER_PWD_CHANGE').'</p></div>';
	}

	if ( !requestIsAjax() && (!defined('GSNOAPACHECHECK') || GSNOAPACHECHECK == false) and !server_is_apache()) {
		echo '<div class="error">'.i18n_r('WARNING').': <a href="health-check.php">'.i18n_r('SERVER_SETUP').' non-Apache</a></div>';
	}

	if(!isset($update)) $update = '';
	if(isset($_GET['upd'])) 	$update  = var_in($_GET['upd']);
	if(isset($_GET['success'])) $success = var_in($_GET['success']);
	if(isset($_GET['error'])) 	$error   = var_in($_GET['error']);
	// if(isset($_GET['err'])) 	$err     = var_in($_GET['err']); // deprecated not used
	if(isset($_GET['id'])) 		$errid   = var_in($_GET['id']);
	if(isset($_GET['updated']) && $_GET['updated'] == 1) $success = i18n_r('SITE_UPDATED'); // for update.php only

	switch ( $update ) {
		case 'bak-success':
			doNotify(sprintf(i18n_r('ER_BAKUP_DELETED'), $errid) .'</p>','success');
		break;
		case 'bak-err':
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_REQ_PROC_FAIL'),'error');
		break;
		case 'edit-success':
			if ($ptype == 'edit') {
				doNotify(sprintf(i18n_r('ER_YOUR_CHANGES'), $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','success');
			} elseif ($ptype == 'restore') {
				doNotify(sprintf(i18n_r('ER_HASBEEN_REST'), $id),'success');
			} elseif ($ptype == 'delete') {
				doNotify(sprintf(i18n_r('ER_HASBEEN_DEL'), $errid) .'. <a href="backup-edit.php?p=restore&id='. $errid .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>','success');
			}
		break;
		case 'clone-success':
			doNotify(sprintf(i18n_r('CLONE_SUCCESS'), '<a href="edit.php?id='.$errid.'">'.$errid.'</a>'),'success');
		break;
		case 'edit-index':
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_CANNOT_INDEX'),'error');
		break;
		case 'edit-error':
			doNotify('<b>'.i18n_r('ERROR').':</b> '. var_out($ptype),'error');
		break;
		case 'pwd-success':
			doNotify(i18n_r('ER_NEW_PWD_SENT').'. <a href="index.php">'.i18n_r('LOGIN').'</a>','info');
		break;
		case 'pwd-error':
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_SENDMAIL_ERR').'.','error');
		break;
		case 'del-success':
			doNotify(i18n_r('ER_FILE_DEL_SUC').': <b>'.$errid.'</b>','success');
		break;
		case 'flushcache-success':
			doNotify(i18n_r('FLUSHCACHE-SUCCESS'),'success');
		break;
		case 'del-error':
			doNotify('<b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_PROBLEM_DEL').'.','error');
		break;
		case 'comp-success':
			doNotify(i18n_r('ER_COMPONENT_SAVE').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success');
		break;
		case 'comp-restored':
			doNotify(i18n_r('ER_COMPONENT_REST').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success');
		break;
		case 'profile-restored':
			doNotify(i18n_r('ER_PROFILE_RESTORED').'. <a href="profile.php?undo&nonce='.get_nonce("undo").
				'&userid='.$userid.'">'.i18n_r('UNDO').'</a>','success');
		break;
		case 'settings-restored':
			doNotify(i18n_r('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>','success');
		break;

		default:
			if ( isset( $error ) )          doNotify('<b>'.i18n_r('ERROR').':</b> '. $error,'error');
			elseif (isset($_GET['cancel'])) doNotify(i18n_r('ER_CANCELLED_FAIL'),'error');
			elseif (isset($error))          doNotify($error,'error');
			elseif (!empty($err))           doNotify('<b>'.i18n_r('ERROR').':</b> '.$err,'error');
			elseif (isset($success))        doNotify($success,'success');
		break;
	}

	function doNotify($msg, $type = '', $persist = false){
		echo '<div class="updated notify '. ($type == '' ? '' : 'notify_'.$type.' ') . ($persist ? 'remove' : '') . '"><p>'.$msg.'</p></div>';
	}

/* ?> */

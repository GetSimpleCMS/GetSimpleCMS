<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Error Checking
 *
 * Displays error and success messages	
 *
 * @package GetSimple
 */
 
 /* 
 * Modified by Jorge H. [ http://www.jorgehoya.es ] on 07/09/2011
 */
 
	if ( file_exists(GSUSERSPATH._id($USR).".xml.reset") && get_filename_id()!='index' && get_filename_id()!='resetpassword' ) {
		echo '<div class="error">'.i18n_r('ER_PWD_CHANGE').'</div>';
	}
	$update = '';
	$err = '';
	$restored = '';
	if(isset($_GET['upd'])) $update = ( function_exists( "filter_var") ) ? filter_var ( $_GET['upd'], FILTER_SANITIZE_SPECIAL_CHARS)  : htmlentities($_GET['upd']);
	if(isset($_GET['success'])) $success = ( function_exists( "filter_var") ) ? filter_var ( $_GET['success'], FILTER_SANITIZE_SPECIAL_CHARS)  : htmlentities($_GET['success']);
	if(isset($_GET['error'])) $error = ( function_exists( "filter_var") ) ? filter_var ( $_GET['error'], FILTER_SANITIZE_SPECIAL_CHARS)  : htmlentities($_GET['error']);
	if(isset($_GET['err'])) $err = ( function_exists( "filter_var") ) ? filter_var ( $_GET['err'], FILTER_SANITIZE_SPECIAL_CHARS)  : htmlentities($_GET['err']);

	switch ( $update ) {
		case 'bak-success':
			echo '<div class="updated">'. sprintf(i18n_r('ER_BAKUP_DELETED'), (int)$_GET['id']) .'</div>';
		break;
		case 'bak-err':
			echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_REQ_PROC_FAIL').'</div>';
		break;
		case 'edit-success':
			echo '<div class="updated">';
			if ($ptype == 'edit') { 
				echo sprintf(i18n_r('ER_YOUR_CHANGES'), $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>';
			} elseif ($ptype == 'restore') {
				echo sprintf(i18n_r('ER_HASBEEN_REST'), $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>';
			} elseif ($ptype == 'delete') {
				echo sprintf(i18n_r('ER_HASBEEN_DEL'), (int)$_GET['id']) .'. <a href="backup-edit.php?p=restore&id='. (int)$_GET['id'] .'&nonce='.get_nonce("restore", "backup-edit.php").'">'.i18n_r('UNDO').'</a>';
			}
			echo '</div>';
		break;
		case 'edit-index':
			echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_CANNOT_INDEX').'.</div>';
		break;
		case 'edit-err':
			echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '. $ptype .'.</div>';
		break;
		case 'pwd-success':
			echo '<div class="updated">'.i18n_r('ER_NEW_PWD_SENT').'. <a href="index.php">'.i18n_r('LOGIN').'</a></div>';
		break;
		case 'pwd-error':
			echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_SENDMAIL_ERR').'.</div>';
		break;
		case 'del-success':
			echo '<div class="updated">'.i18n_r('ER_FILE_DEL_SUC').': <b>'.(int)$_GET['id'].'</b></div>';
		break;
		case 'del-error':
			echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.i18n_r('ER_PROBLEM_DEL').'.</div>';
		break;
		case 'comp-success':
			echo '<div class="updated">'.i18n_r('ER_COMPONENT_SAVE').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
		break;
		case 'comp-restored':
			echo '<div class="updated">'.i18n_r('ER_COMPONENT_REST').'. <a href="components.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
		break;
		
		/**/
		default:
			if ( isset( $error ) ) echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '. $error .'</div>';
			else if ($restored == 'true') echo '<div class="updated">'.i18n_r('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
			else if ( isset($_GET['rest']) && $_GET['rest']=='true' ) 
				echo '<div class="updated">'.i18n_r('ER_OLD_RESTORED').'. <a href="support.php?undo&nonce='.get_nonce("undo", "support.php").'">'.i18n_r('UNDO').'</a></div>';
			elseif (isset($_GET['cancel'])) echo '<div class="error">'.i18n_r('ER_CANCELLED_FAIL').'</div>';
			elseif (isset($error)) echo '<div class="error">'.$error.'</div>';
			elseif (!empty($err)) echo '<div class="error"><b>'.i18n_r('ERROR').':</b> '.$err.'</div>';
			elseif (isset($success)) echo '<div class="updated">'.$success.'</div>';
			elseif ( $restored == 'true') 
				echo '<div class="updated">'.i18n_r('ER_OLD_RESTORED').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a></div>';
		break;
		/**/
		
	}
	?>
	
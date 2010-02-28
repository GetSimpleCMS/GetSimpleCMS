<?php

	if (file_exists(GSDATAOTHERPATH."user.xml.reset")) {
		echo '<div class="error">'.$i18n['ER_PWD_CHANGE'].'</div>';
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
		echo '<div class="updated">'. sprintf($i18n['ER_BAKUP_DELETED'], $_GET['id']) .'</div>';
	} elseif (isset($error)) { 
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '. @$error .'</div>';
	} elseif ($update == 'bak-err') { 
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '.$i18n['ER_REQ_PROC_FAIL'].'</div>';
	} elseif ($update == 'edit-success') { 
		echo '<div class="updated">';
		if ($ptype == 'edit') { 
			echo sprintf($i18n['ER_YOUR_CHANGES'], $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'">'.$i18n['UNDO'].'</a>';
		} elseif ($ptype == 'restore') {
			echo sprintf($i18n['ER_HASBEEN_REST'], $id) .'. <a href="backup-edit.php?p=restore&id='. $id .'">'.$i18n['UNDO'].'</a>';
		} elseif ($ptype == 'delete') {
			echo sprintf($i18n['ER_HASBEEN_DEL'], $_GET['id']) .'. <a href="backup-edit.php?p=restore&id='. $_GET['id'] .'">'.$i18n['UNDO'].'</a>';
		}
		echo '</div>';
	} elseif ($update == 'edit-index') { 
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '.$i18n['ER_CANNOT_INDEX'].'.</div>';
	} elseif ($update == 'edit-err') { 
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '. @$ptype .'.</div>';
	} elseif ($err == 'false') {
		echo '<div class="updated">'.$i18n['ER_SETTINGS_UPD'].'. <a href="settings.php?undo">'.$i18n['UNDO'].'</a></div>';
	} elseif ($restored == 'true') { 
		echo '<div class="updated">'.$i18n['ER_OLD_RESTORED'].'. <a href="settings.php?undo">'.$i18n['UNDO'].'</a></div>';
	} elseif (@$_GET['rest'] == 'true') { 
		echo '<div class="updated">'.$i18n['ER_OLD_RESTORED'].'. <a href="support.php?undo">'.$i18n['UNDO'].'</a></div>';
	} elseif ($err == 'true') { 
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '. @$msg .'</div>';
	} elseif ($update == 'pwd-success') {
		echo '<div class="updated">'.$i18n['ER_NEW_PWD_SENT'].'. <a href="index.php">'.$i18n['LOGIN'].'</a></div>';
	} elseif ($update == 'pwd-error') {
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '.$i18n['ER_SENDMAIL_ERR'].'.</div>';
	} elseif ($update == 'del-success') {
		echo '<div class="updated">'.$i18n['ER_FILE_DEL_SUC'].': <b>'.$_GET['id'].'</b></div>';
	} elseif ($update == 'del-error') {
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '.$i18n['ER_PROBLEM_DEL'].'.</div>';
	} elseif ($update == 'comp-success') {
		echo '<div class="updated">'.$i18n['ER_COMPONENT_SAVE'].'. <a href="components.php?undo">'.$i18n['UNDO'].'</a></div>';
	} elseif ($update == 'comp-restored') {
		echo '<div class="updated">'.$i18n['ER_COMPONENT_REST'].'. <a href="components.php?undo">'.$i18n['UNDO'].'</a></div>';
	} elseif (isset($_GET['cancel'])) {
		echo '<div class="error">'.$i18n['ER_CANCELLED_FAIL'].'</div>';
	}	elseif (isset($error)) {
		echo '<div class="error">'.$error.'</div>';
	}	elseif (isset($success)) {
		echo '<div class="updated">'.$success.'</div>';
	} 
	?>
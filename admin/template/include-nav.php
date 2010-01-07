<?php
/****************************************************
*
* @File: 		include-nav.php
* @Package:	GetSimple
* @Action:	Template file for inserting the top navigation into the control panel. 	
*
*****************************************************/
?>

<?php if (cookie_check()) { 
		echo '<ul id="pill"><li class="leftnav"><a href="logout.php" accesskey="l" >'.$i18n['TAB_LOGOUT'].'</a></li><li class="rightnav" ><a href="settings.php#profile">'.$i18n['WELCOME'].' <b>'.$USR.'</b>!</a></li></ul>'; 
} ?>

<ul class="nav">
	<li><a class="pages" href="pages.php" accesskey="p" ><?php echo $i18n['TAB_PAGES'];?></a></li>
	<li><a class="files" href="upload.php" accesskey="f" ><?php echo $i18n['TAB_FILES'];?></a></li>
	<li><a class="theme" href="theme.php" accesskey="t" ><?php echo $i18n['TAB_THEME'];?></a></li>
	<li><a class="backups" href="backups.php" accesskey="b" ><?php echo $i18n['TAB_BACKUPS'];?></a></li>
	
	<?php 
	exec_action('nav-tab');
	?>
	
	
	<li><img class="toggle" id="loader" src="template/images/ajax.gif" alt=""/></li>
	<li class="rightnav" ><a class="settings first" href="settings.php" accesskey="s" ><?php echo $i18n['TAB_SETTINGS'];?></a></li>
	<li class="rightnav" ><a class="support last" href="support.php" accesskey="o" ><?php echo $i18n['TAB_SUPPORT'];?></a></li>
</ul>
<div class="clear" ></div>
</div>
</div>
	
</ul>
<div class="wrapper">
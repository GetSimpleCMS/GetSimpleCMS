<?php
/**
 * Navigation Include Template
 *
 * @package GetSimple
 */
 
if (cookie_check()) { 
	echo '<ul id="pill"><li class="leftnav"><a href="logout.php" accesskey="'. find_accesskey(i18n_r('TAB_LOGOUT')).'" >'.i18n_r('TAB_LOGOUT').'</a></li>';
	if (defined('GSDEBUG')) {
		echo '<li class="debug"><a href="http://get-simple.info/wiki/themes:tips" target="_blank">'.i18n_r('DEBUG_MODE').'</a></li>';
	}
	echo '<li class="rightnav" ><a href="settings.php#profile">'.i18n_r('WELCOME').' <b>'.$USR.'</b>!</a></li></ul>';
} 

//determine page type if plugin is being shown
if (get_filename_id() == 'load') {
	$plugin_class = $plugin_info[$plugin_id]['page_type'];
} else {
	$plugin_class = '';
}

?>

<ul class="nav <?php echo $plugin_class; ?>">
	<li><a class="pages" href="pages.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_PAGES'));?>" ><?php i18n('TAB_PAGES');?></a></li>
	<li><a class="files" href="upload.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_FILES'));?>" ><?php i18n('TAB_FILES');?></a></li>
	<li><a class="theme" href="theme.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_THEME'));?>" ><?php i18n('TAB_THEME');?></a></li>
	<li><a class="backups" href="backups.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_BACKUPS'));?>" ><?php i18n('TAB_BACKUPS');?></a></li>
	<li><a class="plugins" href="plugins.php" accesskey="<?php echo find_accesskey(i18n_r('PLUGINS_NAV'));?>" ><?php i18n('PLUGINS_NAV');?></a></li>
	
	<?php exec_action('nav-tab');	?>
	
	<li><img class="toggle" id="loader" src="template/images/ajax.gif" alt="" /></li>
	<li class="rightnav" ><a class="settings first" href="settings.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SETTINGS'));?>" ><?php i18n('TAB_SETTINGS');?></a></li>
	<li class="rightnav" ><a class="support last" href="support.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SUPPORT'));?>" ><?php i18n('TAB_SUPPORT');?></a></li>
	
	<!--
	 add this to any .nav <li> element to display a warning symbol on it. 
	 This will primarily be used when an update is needed or something is determined 'wrong'
	 code: <span class="warning">!</span>
	-->
</ul>
<div class="clear" ></div>
</div>
</div>
	
<div class="wrapper">
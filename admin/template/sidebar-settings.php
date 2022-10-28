<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Sidebar Settings Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
<li id="sb_settings" ><a href="settings.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_GEN_SETTINGS'));?>" <?php check_menu('settings');  ?> ><?php i18n('SIDE_GEN_SETTINGS'); ?></a></li>
<li id="sb_settingsprofile" class="last_sb"><a href="profile.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_USER_PROFILE'));?>" <?php check_menu('profile');?> ><?php i18n('SIDE_USER_PROFILE'); ?></a></li>
<?php exec_action("settings-sidebar"); // @hook settings-sidebar sidebar list html output  ?>
</ul>

<!-- sample css stuff -->
<!-- <div class="section">Section<p class="small">p small</p><input class="text" placeholder="input"></div>
<div class="edit-nav"><a href="#">Link</a><a href="#">Link</a></div>
<div class="clear"></div> -->

<p id="js_submit_line" ></p>

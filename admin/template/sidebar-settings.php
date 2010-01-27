<ul class="snav">
<li><a href="settings.php" accesskey="s" <?php check_menu('settings');  ?> ><?php echo $i18n['SIDE_GEN_SETTINGS']; ?></a></li>
<li><a href="settings.php#profile" accesskey="u" ><?php echo $i18n['SIDE_USER_PROFILE']; ?></a></li>
<?php exec_action("settings-sidebar"); ?>
</ul>

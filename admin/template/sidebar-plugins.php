<ul class="snav">
	<li><a href="plugins.php" <?php check_menu('plugins');  ?> accesskey="h" ><?php echo $i18n['SHOW_PLUGINS']; ?></a></li>
	<?php exec_action("plugins-sidebar"); ?>
</ul>
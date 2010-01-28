<ul class="snav">
	<li><a href="plugins.php?plugin=main" <?php if($_GET['plugin']=='main') {echo 'class="current"'; } ?> accesskey="h" ><?php echo $i18n['SHOW_PLUGINS']; ?></a></li>
	<?php exec_action("plugins-sidebar"); ?>
</ul>
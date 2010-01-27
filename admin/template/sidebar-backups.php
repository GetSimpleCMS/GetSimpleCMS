<ul class="snav">
	<li><a href="backups.php" <?php check_menu('backups');  ?> accesskey="b" ><?php echo $i18n['SIDE_PAGE_BAK']; ?></a></li>
	<?php if(get_filename_id()==='backup-edit') { ?><li><a href="#" class="current"><?php echo $i18n['SIDE_VIEW_BAK']; ?></a></li><?php } ?>
	<li><a href="archive.php" <?php check_menu('archive');  ?> accesskey="w" ><?php echo $i18n['SIDE_WEB_ARCHIVES']; ?></a></li>
	<?php exec_action("backups-sidebar"); ?>
</ul>
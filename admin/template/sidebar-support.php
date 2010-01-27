<ul class="snav">
	<li><a href="support.php"  <?php check_menu('support');  ?> accesskey="o" ><?php echo $i18n['SIDE_SUPPORT_LOG']; ?></a></li>
	<?php if(get_filename_id()==='log') { ?><li><a href="#"  class="current" ><?php echo $i18n['SIDE_VIEW_LOG']; ?></a></li><?php } ?>
	<li><a href="health-check.php" <?php check_menu('health-check');  ?> accesskey="h" ><?php echo $i18n['SIDE_HEALTH_CHK']; ?></a></li>
	<li><a href="http://get-simple.info/docs/" accesskey="d" ><?php echo $site_full_name; ?> <?php echo $i18n['SIDE_DOCUMENTATION']; ?></a></li>
	<?php exec_action("support-sidebar"); ?>
</ul>
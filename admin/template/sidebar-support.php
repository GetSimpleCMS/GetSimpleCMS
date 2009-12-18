<ul class="snav">
	<li><a href="support.php"  <?php if(get_filename_id()==='support') {echo 'class="current"'; } ?> accesskey="o" ><?php echo $i18n['SIDE_SUPPORT_LOG']; ?></a></li>
	<?php if(get_filename_id()==='log') { ?><li><a href="#"  class="current" ><?php echo $i18n['SIDE_VIEW_LOG']; ?></a></li><?php } ?>
	<li><a href="health-check.php" <?php if(get_filename_id()==='health-check') {echo 'class="current"'; } ?> accesskey="h" ><?php echo $i18n['SIDE_HEALTH_CHK']; ?></a></li>
	<li><a href="http://get-simple.info/docs/" accesskey="d" ><?php echo $site_full_name; ?> <?php echo $i18n['SIDE_DOCUMENTATION']; ?></a></li>
</ul>
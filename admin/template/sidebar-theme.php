<ul class="snav">
	<li><a href="theme.php"  <?php if(get_filename_id()==='theme') {echo 'class="current"'; } ?> accesskey="t" ><?php echo $i18n['SIDE_CHOOSE_THEME']; ?></a></li>
	<li><a href="theme-edit.php"  <?php if(get_filename_id()==='theme-edit') {echo 'class="current"'; } ?> accesskey="h" ><?php echo $i18n['SIDE_EDIT_THEME']; ?></a></li>
	<li><a href="components.php"  <?php if(get_filename_id()==='components') {echo 'class="current"'; } ?> accesskey="e" ><?php echo $i18n['SIDE_COMPONENTS']; ?></a></li>
	<li><a id="waittrigger" href="sitemap.php" accesskey="g" ><?php echo $i18n['SIDE_GEN_SITEMAP']; ?></a></li>
	<?php if (file_exists('../sitemap.xml')) { ?>
		<li><a href="../sitemap.xml" accesskey="v" ><?php echo $i18n['SIDE_VIEW_SITEMAP']; ?></a></li>
	<?php } ?>
</ul>




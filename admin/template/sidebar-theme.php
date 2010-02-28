<ul class="snav">
	<li><a href="theme.php"  <?php check_menu('theme');  ?> accesskey="t" ><?php echo $i18n['SIDE_CHOOSE_THEME']; ?></a></li>
	<li><a href="theme-edit.php"  <?php check_menu('theme-edit'); ?> accesskey="h" ><?php echo $i18n['SIDE_EDIT_THEME']; ?></a></li>
	<li><a href="components.php"  <?php check_menu('components'); ?> accesskey="e" ><?php echo $i18n['SIDE_COMPONENTS']; ?></a></li>
	<li><a id="waittrigger" href="sitemap.php?s=<?php echo $SESSIONHASH; ?>" accesskey="g" ><?php echo $i18n['SIDE_GEN_SITEMAP']; ?></a></li>
	<?php if (file_exists(GSROOTPATH.'sitemap.xml')) { ?>
		<li><a href="<?php echo $SITEURL; ?>sitemap.xml" accesskey="v" ><?php echo $i18n['SIDE_VIEW_SITEMAP']; ?></a></li>
	<?php } ?>
	<?php exec_action("theme-sidebar"); ?>
</ul>




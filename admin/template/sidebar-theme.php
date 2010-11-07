<?php
/**
 * Sidebar Themes Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li><a href="theme.php"  <?php check_menu('theme');  ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_CHOOSE_THEME'));?>" ><?php i18n('SIDE_CHOOSE_THEME'); ?></a></li>
	<li><a href="theme-edit.php"  <?php check_menu('theme-edit'); ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_EDIT_THEME'));?>" ><?php i18n('SIDE_EDIT_THEME'); ?></a></li>
	<li><a href="components.php"  <?php check_menu('components'); ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_COMPONENTS'));?>" ><?php i18n('SIDE_COMPONENTS'); ?></a></li>
	<li><a id="waittrigger" href="sitemap.php?s=<?php echo $SESSIONHASH; ?>" accesskey="<?php echo find_accesskey(i18n_r('SIDE_GEN_SITEMAP'));?>" ><?php i18n('SIDE_GEN_SITEMAP'); ?></a></li>
	<?php if (file_exists(GSROOTPATH.'sitemap.xml')) { ?>
		<li><a href="<?php echo $SITEURL; ?>sitemap.xml" accesskey="<?php echo find_accesskey(i18n_r('SIDE_VIEW_SITEMAP'));?>" ><?php i18n('SIDE_VIEW_SITEMAP'); ?></a></li>
	<?php } ?>
	<?php exec_action("theme-sidebar"); ?>
</ul>

<?php if(get_filename_id()==='components' || get_filename_id()==='theme-edit') { ?>
<p id="js_submit_line" ></p>
<?php } ?>




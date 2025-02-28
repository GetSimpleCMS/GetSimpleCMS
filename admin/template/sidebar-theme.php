<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Sidebar Themes Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li id="sb_theme" ><a href="theme.php"  <?php check_menu('theme');  ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_CHOOSE_THEME'));?>" ><?php i18n('SIDE_CHOOSE_THEME'); ?></a></li>
	<li id="sb_themeedit" ><a href="theme-edit.php"  <?php check_menu('theme-edit'); ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_EDIT_THEME'));?>" ><?php i18n('SIDE_EDIT_THEME'); ?></a></li>
	<li id="sb_components" ><a href="components.php"  <?php check_menu('components'); ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_COMPONENTS'));?>" ><?php i18n('SIDE_COMPONENTS'); ?></a></li>
	<li id="sb_snippets" <?php if(getDef('GSNOSITEMAP',true)) echo 'class="last_sb"'; ?>><a href="snippets.php"  <?php check_menu('snippets'); ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_SNIPPETS'));?>" ><?php i18n('SIDE_SNIPPETS'); ?></a></li>
	<?php if(!getDef('GSNOSITEMAP')){ ?> <li id="sb_sitemap" class="last_sb"><a href="sitemap.php" <?php check_menu('sitemap'); ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_VIEW_SITEMAP'));?>" ><?php i18n('SIDE_VIEW_SITEMAP'); ?></a></li> <?php }?>
	<?php exec_action("theme-sidebar"); // @hook theme-sidebar sidebar list html output  ?>
</ul>

<p id="js_submit_line" ></p>

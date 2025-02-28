<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Sidebar Backups Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li id="sb_backups" ><a href="backups.php" <?php check_menu('backups');  ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_PAGE_BAK'));?>" ><?php i18n('SIDE_PAGE_BAK'); ?></a></li>
	<?php if(isPage('backup-edit')) { ?><li id="sb_viewbackup" ><a href="#" class="current"><?php i18n('SIDE_VIEW_BAK'); ?></a></li><?php } ?>
	<li id="sb_archives" class="last_sb"><a href="archive.php" <?php check_menu('archive');  ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_WEB_ARCHIVES'));?>" ><?php i18n('SIDE_WEB_ARCHIVES'); ?></a></li>
	<?php exec_action("backups-sidebar"); // @hook backups-sidebar sidebar list html output ?>
</ul>

<p id="js_submit_line" ></p>

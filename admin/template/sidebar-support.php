<?php
/**
 * Sidebar Support Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li><a href="support.php"  <?php check_menu('support');  ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_SUPPORT_LOG'));?>" ><?php i18n('SIDE_SUPPORT_LOG'); ?></a></li>
	<?php if(get_filename_id()==='log') { ?><li><a href="#"  class="current" ><?php i18n('SIDE_VIEW_LOG'); ?></a></li><?php } ?>
	<li><a href="health-check.php" <?php check_menu('health-check');  ?> accesskey="<?php echo find_accesskey(i18n_r('SIDE_HEALTH_CHK'));?>" ><?php i18n('SIDE_HEALTH_CHK'); ?></a></li>
	<?php exec_action("support-sidebar"); ?>
</ul>
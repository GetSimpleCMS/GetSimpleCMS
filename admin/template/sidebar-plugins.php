<?php
/**
 * Sidebar Plugins Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li><a href="plugins.php" <?php check_menu('plugins');  ?> accesskey="<?php echo find_accesskey(i18n_r('SHOW_PLUGINS'));?>" ><?php i18n('SHOW_PLUGINS'); ?></a></li>
	<?php exec_action("plugins-sidebar"); ?>
</ul>
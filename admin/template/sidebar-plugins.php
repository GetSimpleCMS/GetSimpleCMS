<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Sidebar Plugins Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li id="sb_plugins"><a href="plugins.php" <?php check_menu('plugins');  ?> accesskey="<?php echo find_accesskey(i18n_r('SHOW_PLUGINS'));?>" ><?php i18n('SHOW_PLUGINS'); ?></a></li>
	<li id="sb_extend" class="last_sb"><a href="<?php echo $site_link_back_url; ?>extend/" target="_blank" accesskey="<?php echo find_accesskey(i18n_r('GET_PLUGINS_LINK'));?>" ><?php i18n('GET_PLUGINS_LINK'); ?></a></li>
	<?php exec_action("plugins-sidebar"); ?>
</ul>
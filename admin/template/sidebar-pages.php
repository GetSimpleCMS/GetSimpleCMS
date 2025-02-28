<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Sidebar Pages Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li id="sb_pages" ><a href="pages.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_VIEW_PAGES'));?>" <?php check_menu('pages');  ?>><?php i18n('SIDE_VIEW_PAGES'); ?></a></li>
	<li id="sb_newpage" ><a href="edit.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_CREATE_NEW'));?>" <?php if((!isset($_GET['id'])) && isPage('edit'))  { echo 'class="current"'; } ?>><?php i18n('SIDE_CREATE_NEW'); ?></a></li>
	<?php if((isset($_GET['id']) && $_GET['id'] != '') && isPage('edit')) { ?><li id="sb_pageedit" ><a href="#" class="current"><?php i18n('EDITPAGE_TITLE'); ?></a></li><?php } ?>
	<li id="sb_menumanager" class="last_sb"><a href="menu-manager.php" accesskey="<?php echo find_accesskey(i18n_r('MENU_MANAGER'));?>" <?php check_menu('menu-manager');  ?>><?php i18n('MENU_MANAGER'); ?></a></li>
	<?php exec_action("pages-sidebar"); // @hook pages-sidebar sidebar list html output  ?>
</ul>

<p id="js_submit_line" ></p>

<?php if(isPage('edit')) { ?>
	<?php if(getDef('GSAUTOSAVE',true)){ ?>
	<p id="autosavestatus"><?php echo sprintf(i18n_r("AUTOSAVE_STATUS"),(int)getDef('GSAUTOSAVEINTERVAL')); ?></p>
	<p id="autosavenotify"></p>
	<?php }
} ?>
<?php
/**
 * Sidebar Pages Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
<li><a href="pages.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_VIEW_PAGES'));?>" <?php check_menu('pages');  ?>><?php i18n('SIDE_VIEW_PAGES'); ?></a></li>
	<li><a href="edit.php" accesskey="<?php echo find_accesskey(i18n_r('SIDE_CREATE_NEW'));?>" <?php if((!isset($_GET['id'])) && (get_filename_id()==='edit'))  { echo 'class="current"'; } ?>><?php i18n('SIDE_CREATE_NEW'); ?></a></li>
	<?php if((isset($_GET['id']) && $_GET['id'] != '') && (get_filename_id()==='edit')) { ?><li><a href="#" class="current"><?php i18n('EDITPAGE_TITLE'); ?></a></li><?php } ?>
	<?php exec_action("pages-sidebar"); ?>
</ul>

<?php if(get_filename_id()==='edit') { ?>
<p id="js_submit_line" ></p>
<?php } ?>
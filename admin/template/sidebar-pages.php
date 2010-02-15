<ul class="snav">
	<li><a href="pages.php" accesskey="p" <?php check_menu('pages');  ?>><?php echo $i18n['SIDE_VIEW_PAGES']; ?></a></li>
	<li><a href="edit.php" accesskey="c" <?php if((@$_GET['uri'] == "") && (get_filename_id()==='edit'))  { echo 'class="current"'; } ?>><?php echo $i18n['SIDE_CREATE_NEW']; ?></a></li>
	<?php if(@$_GET['uri'] != '') { ?><li><a href="#" class="current"><?php echo $i18n['EDITPAGE_TITLE']; ?></a></li><?php } ?>
	<?php exec_action("pages-sidebar"); ?>
</ul>
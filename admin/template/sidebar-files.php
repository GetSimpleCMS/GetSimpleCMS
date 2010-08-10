<ul class="snav">
	<li><a href="upload.php" <?php check_menu('upload');  ?>><?php echo $i18n['FILE_MANAGEMENT'];?></a></li>
	<?php if(@$_GET['i'] != '') { ?><li><a href="#" class="current"><?php echo $i18n['IMG_CONTROl_PANEL'];?></a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); ?>
	
	<?php if (defined('GSNOUPLOADIFY')) { $ftpid=null; } else { $ftpid='id="mainftp"'; } ?>
	<li class="upload">	<form <?php echo $ftpid; ?> class="fullform" action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" enctype="multipart/form-data">
		<p><input type="file" name="file" id="file" /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<p><input type="submit" class="submit" name="submit" value="<?php echo $i18n['UPLOAD']; ?>" /></p>
	</form></li>
	<li style="float:right;"><small><?php echo $i18n['MAX_FILE_SIZE']; ?>: <strong><?php echo ini_get('upload_max_filesize'); ?>B</strong></small></li>
</ul>

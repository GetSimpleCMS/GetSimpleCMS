<ul class="snav">
	<li><a href="upload.php" <?php if(get_filename_id()==='upload') {echo 'class="current"'; } ?>><?php echo $i18n['FILE_MANAGEMENT'];?></a></li>
	<?php if(@$_GET['i'] != '') { ?><li><a href="#" class="current">Image Control Panel</a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); ?>
	
	<?php if (defined('GSNOUPLOADIFY')) { $ftpid=null; } else { $ftpid='id="mainftp"'; } ?>
	<li class="upload">	<form <?php echo $ftpid; ?> class="fullform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<p><input type="file" class="text" name="file" id="file" /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<p><input type="submit" class="submit" name="submit" value="<?php echo $i18n['UPLOAD']; ?>" /></p>
	</form></li>
	<li style="float:right;"><small><?php echo $i18n['MAX_FILE_SIZE']; ?>: <b><?php echo ini_get('upload_max_filesize'); ?>B</small></li>
</ul>




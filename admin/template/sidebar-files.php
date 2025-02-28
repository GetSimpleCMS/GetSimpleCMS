<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Sidebar Files Template
 *
 * @package GetSimple
 */

$path = (isset($_GET['path'])) ? $_GET['path'] : "";
$fileSizeLimitMB = toBytesShorthand(getMaxUploadSize(),'M',true);

?>

<ul class="snav">
	<li id="sb_upload"<?php if(!isset($_GET['i'])) echo'class="last_sb"'; ?> ><a href="upload.php" <?php check_menu('upload');  ?>><?php i18n('FILE_MANAGEMENT');?></a></li>
	<?php if(isset($_GET['i']) && $_GET['i'] != '') { ?><li id="sb_image" class="last_sb"><a href="#" class="current"><?php i18n('IMG_CONTROl_PANEL');?></a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); // @hook files-sidebar sidebar list html output  ?>

	<?php 
	// allow uploads?
	if(getDef('GSALLOWUPLOADS',true)){ 
	?>
	
	<li class="upload dispupload">
		<a style="margin-left:0" id="fileuploadlink" class="fileuploadlink" href="#">
			<span><?php echo i18n_r('UPLOADIFY_BUTTON'); ?></span>
			<span class="touch"><?php echo i18n_r('UPLOAD'); ?></span>
		</a>
	
		<div id="upload-queue" class="upload-queue">	
			<!-- Dropzone Template -->
			<div id="queue-item-template">
				<div class="queue-item-wrap">
					<div class="queue-item dz-preview dz-file-preview">
						<img data-dz-thumbnail>
						<div class="dz-filename">
					    	<span class="dz-process-mark"><span>&#x25ba;</span> </span>
					    	<span class="dz-success-mark"><span>&#x2713;</span> </span>
							<span class="dz-error-mark">&#x2717; </span>
							<span class="dz-name" data-dz-name></span><span class="size"> (<span class="dz-size" data-dz-size></span>)</span>
						</div>
						<div class="dz-error-message"><span data-dz-errormessage></span></div>
						<div class="progress">
							<div class="progress-bar" style="width: 0%;" data-dz-uploadprogress>
							<!--Progress Bar-->
							</div>
						</div>
					</div>
				<a class="dz-remove" href="javascript:undefined;" data-dz-remove>&times;</a>
				</div>
			</div>
			<!-- End Dropzone Template -->
		</div>	

	</li>

	<li id="gs-dropzone" class="uploaddropzone dispupload fileuploadlink">
		<?php echo getIcon("SM_upload"); ?>
		<span class="dz-message unselectable"><?php i18n('DROP_FILES'); ?></span>
	</li>
	
	<li style="float:right;" id="sb_filesize" class="dispupload"><small><?php i18n('MAX_FILE_SIZE'); ?>: <strong><?php echo $fileSizeLimitMB; ?></strong></small></li>
	<?php
	} // end allow uploads
	?>

</ul>

<div class="">
	<form class="uploadform" action="" method="post" enctype="multipart/form-data">
    	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo getMaxUploadSize();?>" />	
		<p><input class="" type="file" name="file[]" id="file" style="width:220px;" multiple /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" />
	</form>
</div>

<p id="js_submit_line" ></p>

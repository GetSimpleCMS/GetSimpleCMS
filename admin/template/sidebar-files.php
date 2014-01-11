<?php
/**
 * Sidebar Files Template
 *
 * @package GetSimple
 */
 
$path = (isset($_GET['path'])) ? $_GET['path'] : "";
?>
<ul class="snav">
	<li id="sb_upload"<?php if(!isset($_GET['i'])) echo'class="last_sb"'; ?> ><a href="upload.php" <?php check_menu('upload');  ?>><?php i18n('FILE_MANAGEMENT');?></a></li>
	<?php if(isset($_GET['i']) && $_GET['i'] != '') { ?><li id="sb_image" class="last_sb"><a href="#" class="current"><?php i18n('IMG_CONTROl_PANEL');?></a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); ?>

<?php if (!defined('GSNOUPLOADIFY')) { ?>	
	<hr><li class="upload" id="sb_uploadify" >
		<div id="uploadify"></div>
	<?php 
	
	// create Uploadify uploader
	$debug = isDebug() ? 'true' : 'false';
	$fileSizeLimit = toBytes(ini_get('upload_max_filesize'))/1024;
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		if(jQuery().uploadify) {
		$('#uploadify').uploadify({
			'debug'			: <?php echo $debug; ?>,
			'buttonText'	: '<?php echo i18n_r('UPLOADIFY_BUTTON'); ?>',
			'buttonCursor'	: 'pointer',
			'uploader'		: 'upload-uploadify.php',
			'swf'			: 'template/js/uploadify/uploadify.swf',
			'multi'			: true,
			'auto'			: true,
			'height'		: 25,
			'width'			: '100%',
			'requeueErrors'	: false,
			'fileSizeLimit'	: <?php echo $fileSizeLimit; ?>, // expects input in kb
			'cancelImage'	: 'template/images/cancel.png',
			'checkExisting'	: 'uploadify-check-exists.php?path=<?php echo $path; ?>',
			'formData'		: {
				'sessionHash' : '<?php echo $SESSIONHASH; ?>',
				'path' : '<?php echo $path; ?>'
			},
			onUploadProgress: function() {
				$('#loader').show();
			},
			'onUploadSuccess' : function(file, data, response) {
				alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
			},
			onUploadComplete: function() {
				$('#loader').fadeOut(500);
				$('#maincontent').load(location.href+' #maincontent > *');
			},
			onSelectError: function(file,errorCode,errorMsg) {
				notifyError('uploadify: ' + file + ' Error ' + errorCode +':'+errorMsg);
			},
			onUploadError: function(file,errorCode,errorMsg, errorString) {
				notifyError('uploadify: ' + errorMsg);
			}
		});
		}
	});
	</script>
	</li>
<?php } ?>
	<li style="float:right;" id="sb_filesize" ><small><?php i18n('MAX_FILE_SIZE'); ?>: <strong><?php echo (toBytes(ini_get('upload_max_filesize'))/1024)/1024; ?>MB</strong></small></li>
</ul>


<?php 
# show normal upload form if Uploadify is turned off 
if (defined('GSNOUPLOADIFY')) { ?>
	<form class="uploadform" action="upload.php?path=<?php echo $path; ?>" method="post" enctype="multipart/form-data">
		<p><input type="file" name="file[]" id="file" style="width:220px;" multiple /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" />
	</form>
<?php } else { ?>

	<!-- show normal upload form if javascript is turned off -->
	<noscript>
		<form class="uploadform" action="upload.php?path=<?php echo $path; ?>" method="post" enctype="multipart/form-data">
			<p><input type="file" name="file[]" id="file" style="width:220px;" multiple /></p>
			<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
			<input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" />
		</form>
	</noscript>

<?php } ?>
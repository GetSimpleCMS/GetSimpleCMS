<?php
/**
 * Sidebar Files Template
 *
 * @package GetSimple
 */
?>
<ul class="snav">
	<li><a href="upload.php" <?php check_menu('upload');  ?>><?php i18n('FILE_MANAGEMENT');?></a></li>
	<?php if(isset($_GET['i']) && $_GET['i'] != '') { ?><li><a href="#" class="current"><?php i18n('IMG_CONTROl_PANEL');?></a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); ?>
	
	<li class="upload">
	<?php if (defined('GSNOUPLOADIFY')) { ?>
	<form class="uploadform" action="<?php myself(); ?>" method="post" enctype="multipart/form-data">
		<p><input type="file" name="file" id="file" /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<p><input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" /></p>
	</form>
	<?php } else { ?>
		<div id="uploadify"></div>
	<?php 
	function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
			switch($last) {
				case 'g': $val *= 1024;
				case 'm': $val *= 1024;
				case 'k': $val *= 1024;
			}
		return $val;
	}
	// create Uploadify uploader
	$debug = (GSDEBUG == 1) ? 'true' : 'false';
	$path = (isset($_GET['path'])) ? $_GET['path'] : "";
	$fileSizeLimit = toBytes(ini_get('upload_max_filesize'))/1024;
	echo "
	<script type=\"text/javascript\">
	jQuery(document).ready(function() {
		if(jQuery().uploadify) {
		$('#uploadify').uploadify({
			'debug'			: ". $debug . ",
			'buttonText'	: '". i18n_r('UPLOADIFY_BUTTON') ."',
			'buttonCursor'	: 'pointer',
			'uploader'		: 'upload-uploadify.php',
			'swf'			: 'template/js/uploadify/uploadify.swf',
			'multi'			: true,
			'auto'			: true,
			'height'		: '25',
			'width'			: '100%',
			'requeueErrors'	: false,
			'fileSizeLimit'	: '".$fileSizeLimit."', // expects input in kb
			'cancelImage'	: 'template/images/cancel.png',
			'checkExisting'	: 'uploadify-check-exists.php?path=".$path."',
			'postData'		: {
				'sessionHash' : '". $SESSIONHASH ."',
				'path' : '". $path ."'
			},
			onUploadProgress: function() {
				$('#loader').show();
			},
			onUploadComplete: function() {
				$('#loader').fadeOut(500);
				$('#maincontent').load(location.href+' #maincontent','');
			},
			onSelectError: function(file,errorCode,errorMsg) {
				//alert(file + ' Error ' + errorCode +':'+errorMsg);
			},
			onUploadError: function(file,errorCode,errorMsg, errorString) {
				alert(errorMsg);
			}
		});
		}
	});
	</script>";
	} ?>
	</li>
	<li style="float:right;"><small><?php i18n('MAX_FILE_SIZE'); ?>: <strong><?php echo ini_get('upload_max_filesize'); ?>B</strong></small></li>
</ul>

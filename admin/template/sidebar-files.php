<?php
/**
 * Sidebar Files Template
 *
 * @package GetSimple
 */

$path = (isset($_GET['path'])) ? $_GET['path'] : "";
$fileSizeLimit = toBytes(ini_get('upload_max_filesize'))/1024;
$fileSizeLimitMB = (toBytes(ini_get('upload_max_filesize'))/1024)/1024;

?>

<ul class="snav">
	<li id="sb_upload"<?php if(!isset($_GET['i'])) echo'class="last_sb"'; ?> ><a href="upload.php" <?php check_menu('upload');  ?>><?php i18n('FILE_MANAGEMENT');?></a></li>
	<?php if(isset($_GET['i']) && $_GET['i'] != '') { ?><li id="sb_image" class="last_sb"><a href="#" class="current"><?php i18n('IMG_CONTROl_PANEL');?></a></li><?php } ?>
	
	<?php exec_action("files-sidebar"); ?>

	<hr><li class="upload">
	<a style="margin-left:0" id="fileuploadlink" href="#"><?php echo i18n_r('UPLOADIFY_BUTTON'); ?></a>	
	
	<div id="upload-queue" class="upload-queue">
		<!-- Dropzone Template -->
		<div id="queue-item-template">
			<div class="queue-item-wrap">
				<div class="queue-item dz-preview dz-file-preview">					
					<div class="dz-filename">
				    	<span class="dz-process-mark"><span>►</span></span>
				    	<span class="dz-success-mark"><span>✔</span></span>
						<span class="dz-error-mark">✘</span>				
						<span class="dz-name" data-dz-name></span><span class="size"> (<span class="dz-size" data-dz-size></span>)</span>
					</div>
					<div class="progress">						
						<div class="progress-bar" style="width: 0%;" data-dz-uploadprogress>
						<!--Progress Bar-->
						</div>					
					</div>				
				</div>
			</div>	
		</div>
		<!-- End Dropzone Template -->
	</div>	

	</li>
	<li style="float:right;" id="sb_filesize" ><small><?php i18n('MAX_FILE_SIZE'); ?>: <strong><?php echo (toBytes(ini_get('upload_max_filesize'))/1024)/1024; ?>MB</strong></small></li>
</ul>

	<script type="text/javascript">

	jQuery(document).ready(function() {

		$('.uploadform').hide();

		$("#fileuploadlink").dropzone({
			debug: false,
			forceFallback: false,
			maxFilesize: <?php echo $fileSizeLimitMB; ?>, // MB			
			parallelUploads: 1, // can be bumped
			url: 'upload.php?path=<?php echo $path;?>',
			uploadMultiple: true,
			paramName: 'file',
			createImageThumbnails: false,
			addRemoveLinks:true,
			dictCancelUpload: '×',
			dictRemoveFile: '×',
			fallback: function(){$('.uploadform').show(); $('.upload').hide();},
			// dictFallbackMessage: null,
			// dictFallbackText: null,
			previewTemplate: $("#queue-item-template").html(),
			previewsContainer: "#upload-queue",
			params	: {
				sessionHash : '<?php echo $SESSIONHASH; ?>',
				path : '<?php echo $path; ?>'
			},
		});


		// Remove the queue item
		removeFromQueue = function(file){
    		var slideDuration = 1000;
    		var removeDelay = 5000;
    		setTimeout(
    			function(){ 
    				$(file.previewElement).stop(true, true).fadeOut(slideDuration).slideUp({ duration: slideDuration, queue: false }); 
    			},
    			removeDelay
    		);
		}

		var myDropzone = Dropzone.forElement("#fileuploadlink");
		
		// after success, remove queue item
		myDropzone.on("processing", function(file) {
			$('#loader').show();
  		});

		// after success, remove queue item
		myDropzone.on("success", function(file) {
			removeFromQueue(file);
  		});

		// progress of total queue
		myDropzone.on("totaluploadprogress", function(progress) {
    		// console.log(progress);
    		// $(file.previewElement).delay(5000).slideUp();
  		});

    	myDropzone.on("complete", function(file) {
    		_this = Dropzone.forElement("#fileuploadlink");
      		if (_this.getQueuedFiles().length == 0) {
				$('#loader').fadeOut(500);
				$('#maincontent').load(location.href+' #maincontent > *');      			
      		}	
      	});

		// Debugging
		if(myDropzone.options.debug){
			var mockFile = { name: "Filename", size: 12345 };
			myDropzone.emit("addedfile", mockFile);
			myDropzone.emit("processing", mockFile);
			myDropzone.emit("uploadprogress", mockFile, 50);

			var mockFile = { name: "Long Filename", size: 12345 };
			myDropzone.emit("addedfile", mockFile);
			myDropzone.emit("processing", mockFile);
			myDropzone.emit("success", mockFile);

			var mockFile = { name: "Even_more_long_Very_Long_Filename", size: 12345 };
			myDropzone.emit("addedfile", mockFile);
			myDropzone.emit("processing", mockFile);			
			myDropzone.emit("error", mockFile);

			// myDropzone.emit("thumbnail", mockFile, "/image/url");
			// If you use the maxFiles option, make sure you adjust it to the
			// correct amount:
			// var existingFileCount = 1; // The number of files already uploaded
			// myDropzone.options.maxFiles = myDropzone.options.maxFiles - existingFileCount;
		}
	});
	</script>		

<div class="">
	<form class="uploadform" action="upload.php?path=<?php echo $path; ?>" method="post" enctype="multipart/form-data">
		<p><input class="" type="file" name="file[]" id="file" style="width:220px;" multiple /></p>
		<input type="hidden" name="hash" id="hash" value="<?php echo $SESSIONHASH; ?>" />
		<input type="submit" class="submit" name="submit" value="<?php i18n('UPLOAD'); ?>" />
	</form>
</div>

<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

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
	<a style="margin-left:0" id="fileuploadlink" href="#">
		<span><?php echo i18n_r('UPLOADIFY_BUTTON'); ?></span>
		<span class="touch"><?php echo i18n_r('UPLOAD'); ?></span>
	</a>	
	
	<div id="upload-queue" class="upload-queue">
		<!-- Dropzone Template -->
		<div id="queue-item-template">
			<div class="queue-item-wrap">
				<div class="queue-item dz-preview dz-file-preview">
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
	<li id="gs-dropzone" class="uploaddropzone">
		<span class="dz-message unselectable"><?php i18n('DROP_FILES'); ?></span>
	</li>
	<li style="float:right;" id="sb_filesize" ><small><?php i18n('MAX_FILE_SIZE'); ?>: <strong><?php echo (toBytes(ini_get('upload_max_filesize'))/1024)/1024; ?>MB</strong></small></li>
</ul>

	<script type="text/javascript">

	jQuery(document).ready(function() {

		var uploadSession = '<?php echo $SESSIONHASH; ?>';
		var uploadPath = '<?php echo $path; ?>';

		// workaroud for safari mutiple bug
		// disable mutiple or else we get empty uploads
	    if (Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor')>0){
	         $('input:file').removeAttr("multiple");
	    }    

		// detect touch devices, only mobiles, commented out feature spec
		var deviceAgent = navigator.userAgent.toLowerCase();
		var isTouchDevice = (
			// Modernizr.touch || 
			// ('ontouchstart' in document.documentElement) ||
			deviceAgent.match(/(iphone|ipod|ipad)/) ||
			deviceAgent.match(/(android)/)  || 
			deviceAgent.match(/(iemobile)/) || 
			deviceAgent.match(/iphone/i) || 
			deviceAgent.match(/ipad/i) || 
			deviceAgent.match(/ipod/i) || 
			deviceAgent.match(/blackberry/i) || 
			deviceAgent.match(/bada/i) || 
			false
		);

	    // handle asset not loaded
		if(window.Dropzone){
			// show
			$('.upload').show(); 
			$('#gs-dropzone').show(); 
			
			// hide fallback form
			$('.uploadform').hide();			
			
			// flag drop target for touch devices
			$("#gs-dropzone").toggle(!isTouchDevice);
			$('#fileuploadlink').toggleClass('touch',isTouchDevice); 
		}

		// Remove the queue item
		removeFromQueue = function(file){
			var slideDuration = 600;
			var removeDelay = 5000;
			setTimeout(
				function(){ 
					$(file.previewElement).stop(true, true).fadeOut(slideDuration).slideUp({ duration: slideDuration, queue: false }); 
				},
				removeDelay
			);
		}

		myDropzone = new Dropzone("#gs-dropzone",{
			clickable: '#fileuploadlink',
			// dictDefaultMessage : '',
			debug: false, // debugging
			forceFallback: false,
			maxFilesize: <?php echo $fileSizeLimitMB; ?>, // MB			
			parallelUploads: 1, // can be bumped
			url: 'upload.php?path=<?php echo $path;?>',
			uploadMultiple: true,
			paramName: 'file',
			createImageThumbnails: false,
			addRemoveLinks:true,
			dictCancelUpload: '',
			dictRemoveFile: '',
			fallback: function(){$('.uploadform').show(); $('.upload').hide(); $('#gs-dropzone').hide(); },
			// dictFallbackMessage: null,
			// dictFallbackText: null,
			previewTemplate: $("#queue-item-template").html(),
			previewsContainer: "#upload-queue",
			params	: {
				sessionHash : uploadSession,
				path : uploadPath
			},
			accept: checkfile,
	        sending: function(file, xhr, formData) {
                if(file.overwrite) formData.append('fileoverwrite', file.overwrite);
	        }
		});

		/**
		 * drop zone accept callout, checks if file exists
		 * if exists confirm overwrite, if no then it cancels, I suppose we could also rename
		 * if we submit uploader will increment name it
		 */
		function checkfile(file,done){
	        $.ajax({
	            url: 'uploadify-check-exists.php?path=<?php echo $path;?>',
	            data: {filename: file.name, name: file.name, type: file.type},
	            type: 'POST',
	            success: function(response)
	            {
	            	if(response == 1){
	            		if(confirm(file.name + "\n\n" + i18n('FILE_EXISTS_PROMPT') )){
	            			file.overwrite = 1;
	            			done();
	            		}	
	                	else done(i18n('CANCELLED'));
	            	} 
	            	else if(response == 0) done();
	            	else done(i18n('ERROR'));
	            },
	            error: function(response)
	            {
	                done(i18n('ERROR'));
	            }
	        });	
		}

		// while processing, show spinner
		myDropzone.on("processing", function(file) {
			$('#loader').show();
  		});

		// after success, remove queue item
		myDropzone.on("success", function(file) {
			if(!this.options.debug)	removeFromQueue(file);
  		});

		// progress of total queue
		// myDropzone.on("totaluploadprogress", function(progress) {
		// 	// Debugger.log(progress);
		// 	// $(file.previewElement).delay(5000).slideUp();
		// });
		
		// queue complete hide spinner, load content 
    	myDropzone.on("complete", function(file) {
      		if (this.getQueuedFiles().length == 0) {
				$('#loader').fadeOut(500);

				// #imageFilter seleced index to restore 
				var filterIdx = $('#imageFilter').prop("selectedIndex");

				$('#maincontent').load(location.href+' #maincontent > *', function(ev){
					$('#imageFilter').prop("selectedIndex",filterIdx);
					$('#imageFilter').trigger('change');
				});  
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
			myDropzone.emit("error", mockFile,'An Error Occured');

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

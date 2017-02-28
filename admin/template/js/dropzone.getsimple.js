jQuery(document).ready(function () {

	// init sidebar config object for dropzone
	dropzoneSidebarConfig = {
		clickable: '#fileuploadlink',
		fallback              : 
			function(){
				$('.snav').removeClass('dropzoneenabled');
				$('.uploadform').show();  
			},
		previewTemplate       : $("#queue-item-template").html(),
		previewsContainer     : "#upload-queue",

	};				

	// hide traditional form and show dropzone interface, special touch css
	if(window.Dropzone){
		$('.snav').addClass('dropzoneenabled');
		$('.uploadform').hide();
		if(isTouchDevice()) $('.snav').addClass('touch');
	}

	// add dropzone to element
	myDropzone = $('#gs-dropzone').addDropZone(dropzoneSidebarConfig);

	// processing handler, show spinner during processing
	myDropzone.on("processing", function(file) {
		$('#loader').show();
	});

	// success handler, remove queue item after success
	myDropzone.on("success", function(file) {
		if(!this.options.debug)	removeFromQueue(file);
	});

	// total progress handler for total queue length
	// myDropzone.on("totaluploadprogress", function(progress) {
	// 	// Debugger.log(progress);
	// 	// $(file.previewElement).delay(5000).slideUp();
	// });
	
	// queue complete handler, hide spinner, load new page content to update file list
	// @todo atm the entire page is updated after all files are loaded, in future will update in real time using json
	myDropzone.on("complete", function(file) {
  		if (this.getQueuedFiles().length == 0) {
			$('#loader').fadeOut(500);

			// store #imageFilter selected index to restore after load
			var filterIdx = $('#imageFilter').prop("selectedIndex");

			// load upload page new
			$('#maincontent').load(location.href+' #maincontent > *', function(ev){
				$('#imageFilter').prop("selectedIndex",filterIdx);
				$('#imageFilter').trigger('change');
				$(window).trigger('fileuploaded');
			});
  		}
  	});


	// callback for remove the queue item
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
});

	/**
	 * drop zone accept callout, checks if file exists
	 * if exists confirm overwrite, if no then it cancels
	 */
	function checkfile(file,done){
        $.ajax({
            url: 'upload-precheck.php?path='+uploadPath,
            data: {filename: file.name, name: file.name, type: file.type},
            type: 'POST',
            success: function(response)
            {

            	jsonresponse = $.parseJSON(response);
            	// Debugger.log(jsonresponse);
            	if(jsonresponse.file_exists == true){
            		resp = confirm(file.name + "\n\n" + i18n('FILE_EXISTS_PROMPT') )
            		if(resp){
            			// overwrite on ok
            			// @todo using int not boolean so we can add states in the future like rename, keep original etc.
            			file.overwrite = 1;
            			done();
            		}
                	else {
                		// rename on cancel
                		file.overwrite = 0;
                		done();
                		// done(i18n('CANCELLED'));
                	}	
            	}
            	else if(jsonresponse.file_exists == false) done();
            	else done(i18n('ERROR'));
            },
            error: function(response)
            {
                done(i18n('ERROR'));
            }
        });	
	}

	$.fn.addDropZone = function(config){
		var $this = $(this);
	
		if(!$this[0]){
			Debugger.log("gsdropzone: element does not exist, skipping");
			return;
		}
		
		defaultconfig = {
			debug                 : false, // debugging
			forceFallback         : false,
			maxFilesize           : maxFileSize, // MB
			parallelUploads       : 1, // can be bumped
			url                   : 'upload.php?path='+uploadPath,
			uploadMultiple        : true,
			paramName             : 'file',
			createImageThumbnails : false,
			addRemoveLinks        : true,
			dictCancelUpload      : '',
			dictRemoveFile        : '',
			params	              : 
				{
					sessionHash  : uploadSession,
					path : uploadPath				
				},
			accept                : checkfile,
			sending               : 
				function(file, xhr, formData) {
					if(file.overwrite == 1) formData.append('fileoverwrite', file.overwrite);
				}
		};			

		// use config arg if present and ignore user config
		if (typeof config == "undefined" || config === null){
			// Debugger.log('using default config');
			config = defaultconfig;
		}
		else config = jQuery.extend(true, {}, config, defaultconfig);

		// workaroud for safari mutiple bug
		// disable mutiple or else we get empty uploads
	    if (Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor')>0){
	        $('input:file').removeAttr("multiple");
	    }

		myDropzone = new Dropzone($this.get(0),config);

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

		return myDropzone;

};
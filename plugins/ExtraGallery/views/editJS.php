<?php
    global $TEMPLATE, $SITEURL;

    if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else { $EDLANG = i18n_r('CKEDITOR_LANG'); }
    if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else { $EDTOOL = 'basic'; }
    if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {  $EDOPTIONS = ''; }
    if ($EDTOOL == 'advanced') {
      $toolbar = "
          ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
          '/',
          ['Styles','Format','Font','FontSize']
      ";
    } elseif ($EDTOOL == 'basic') {
      $toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
    } else {
      $toolbar = GSEDITORTOOL;
    }
    
?>


$( document ).ready(function() {
    //remove duplicates but not sorting as jquery once
    Array.prototype.unique = function() {
        var a = [], l = this.length;
        for(var i=0; i<l; i++) {
          for(var j=i+1; j<l; j++)
                if (this[i] === this[j]) j = ++i;
          a.push(this[i]);
        }
        return a;
    };

    var $maincontent = $('#maincontent'),
		$editGallery = $('#editGallery'),
        $imagesTable = $editGallery.find('table.images'),
        $emptyRow = $imagesTable.find('tr.empty-row').remove(), //row used to generate rows
		$langSwitch = $('#langSwitch'),
		$cropDialog = $('#cropDialog'),
		$wysiwygDialog = $('#wysiwygDialog'),
        $sidebarDiv = $('#sidebarDiv'),
        $thumbPreview = $('#thumbPreview'),
        $positionPopup = $('#positionPopup'),
        $positionPopupOpener = null,
        $wait = $('#wait'),
        $nameInput = $editGallery.find('input[name="name"]'),
        ajaxUrl = '<?php echo EG_AJAXURL; ?>?nonce=<?php echo get_nonce("ajax", "ExtraGallery"); ?>',
        lastValidationName = '',
        lastValidationResult = false,
        mode = '<?php echo $mode ?>',
        message = '<?php echo $message ?>',
        isErrorMessage = '<?php echo $isErrorMessage ?>';
        
        
	var settings = <?php echo json_encode($settings) ?>,
        unsavedChanges = false,
		waitingThumbCount = 0,  //counter that shows for how many thumbnail creating were waiting
		isWaiting = false; //is currently waiting for end of operations, 
        
        
    //show startup message
    if (message){
        if (isErrorMessage)
            showErrorMessage(message);
        else
            showOkMessage(message);
    }
        
    <?php if ($mode == 'edit'): ?>
         var galleryData = <?php echo json_encode($galleryData); ?>;
    <?php endif; ?>
    
    var langLength = Math.max(1, settings['languages'].length);

    if (mode == 'edit'){
        $nameInput.val(galleryData['name']);
        
        if (!settings['title-disabled']){
            for (i = 0; i < langLength; i++) { 
                $editGallery.find('input[name="title-'+ i +'"]').val(galleryData['title'][i]);
            }
        }  
        
        var $rows = $();
        
        // console.time('creation');
        for (r = 0; r < galleryData.items.length; r++){ 
            var item = galleryData.items[r];
            $rows = $rows.add(createRow(item['filename'], item['width'], item['height'], item));
        }
        
        $rows.insertBefore($imagesTable.find('tbody > tr.add-row'));
        $rows = null;
        // console.timeEnd('creation');
    }   
    
    //sidebar container
    $sidebarDiv.appendTo('#sidebar').show()
        .sticky({
			topSpacing: 20,
			className : 'sticked'
		});
        
    //clone submit button
    $editGallery.find('input.submit').clone().prependTo($sidebarDiv);
    
    //clone add button
    $imagesTable.find('.add-row button.add').clone().prependTo($sidebarDiv);
	
	//languages are sticky when scrolling if exists
	if (settings.languages.length){
        $langSwitch.prependTo($sidebarDiv);
	}
    

	//------------------------ HANDLERS -----------------------------------------------
    //unsaved changes 
    var formDirty = function(){
        if (!unsavedChanges){
            $sidebarDiv.find('.unsaved').fadeIn();
        }
        unsavedChanges = true;
    };
    $editGallery.find('textarea,input,select').not($positionPopup.find('input')).on('change keydown paste', formDirty);

    window.onbeforeunload = function () {
        if (unsavedChanges) {
            return '<?php i18n(EG_ID.'/EDIT_JS_UNSAVED') ?>';
        }
    }
    
    //language switcher
    if (settings.languages.length){
        
        $langSwitch.find('select').change(function(){
            var $select = $(this),
                val = $select.val(),
                langIndex = $.inArray(val, settings.languages);

            //title switch
            if (!settings['title-disabled'] && settings.languages.length){
                $editGallery.find('input[name^="title"]').hide();
                
                $editGallery.find('input[name="title-'+ langIndex +'"]').show();
            }  

            // console.time('lang switch');
            //addClass is faster than show()
            $imagesTable.find('p.custom-field .lang-variant').removeClass('active').filter('.lang-'+ langIndex).addClass('active');

            //update statuses of wywsiwyg
            $imagesTable.find('p.custom-field').each(function(){
                var $p = $(this),
                    $status = $p.find('span.wysiwyg-status.active'); //current language span for wysiwyg status
                    
                if (!$status.length)
                    return; //continue

                updateWysiwygStatus($status, $p.find('input.active').val());
            });     
            // console.timeEnd('lang switch');
        });
    }
        
    //submit on right submit
    $('#sidebar input.submit').click(function(){
        $editGallery.submit();
    });   

    $editGallery.find('a.cancel').click(function(event){
        event.preventDefault();
        unsavedChanges = false;
        location.href = 'load.php?id=<?php echo $this->pluginId ?>&list';
    });
    
	//add row
    $('body').find('button.add.eg-button').click(function(event){
		$.extraBrowser({
			multipleSelection : true,
			addImage : onImageSelected
		});
    });     


	//delete row
    $imagesTable.on('click', '.eg-button.delete', function(){
        var $row = $(this).closest('tr');

        $row.find('> td').fadeOut('300').promise().done(function(){
            $row.find('p.custom-field').each(function(){ //destroy all wysiwyg
                if ($(this).data('cke'))
                    $(this).data('cke').destroy();
            });
            $row.remove();
        });
        
        formDirty();
    }); 
    
    
    $imagesTable.on('click', '.eg-button.position', function(event){
        var $source = $(this),
            show  = !$source.is($positionPopupOpener);
        
        hidePositionPopup();
        if (show)
            showPositionPopup($source);
            
        event.stopPropagation(); //do not propagate to document
    });
    
    // hiding position popup
    $(document).on('click', function (e) {
        var $target = $(e.target),
            isPopover = $target.is($positionPopup),
            inPopover = $target.closest($positionPopup).length > 0;

        //hide only if clicked on button or inside popover
        if (!isPopover && !inPopover && $positionPopupOpener ) 
            hidePositionPopup();
    });
    
    //input enter pressed
    $positionPopup.on('keypress', 'input', function(event){
        if (event.which == 13){
            $positionPopup.find('button').trigger('click');
            event.preventDefault();
        }
    });
    
    //user clikced on ok button
    $positionPopup.on('click', '.eg-button', function(event){
        event.preventDefault();
        
        var newPos = parseInt($positionPopup.find('input').val()),
            len = $imagesTable.find('.image-row').length;
            
        if (!isNaN(newPos) && newPos > 0 && newPos <= len){
            var $row = $positionPopupOpener.closest('.image-row'),
                currPos = $row.index()+1;
            
            if (currPos == newPos){
                hidePositionPopup();
                return;
            }
                
            if (newPos == 1 || newPos > 1 && newPos < len){
                newPos = newPos == 1 ? 0 : newPos - 1;
                $row.insertBefore($imagesTable.find('.image-row:eq('+newPos+')'));
            }
            else{
                $row.insertAfter($imagesTable.find('.image-row:eq('+(len-1)+')'));
            }
            
            formDirty();
            $.scrollTo($row, 300, {offset:-($(window).height() - $row.outerHeight()) / 2, onAfter: function(){
                $row.find('td.fields,td.image').animate({opacity: 0}, 250).animate({opacity: 1}, 250);
            }});
        }
        hidePositionPopup();
    });
    
    $imagesTable.on('click', '.eg-button.up,.eg-button.down', function(){
        var $button = $(this),
            $row = $button.closest('tr'),
            rH = $row.outerHeight(),
            isUp = $button.is('.up');
        
        if ( 
            (isUp && $row.index() == 0) ||
            (!isUp && $row.index() == $imagesTable.find('.image-row').length - 1) //one is add row
           )
        {
            return;
        }
		
		//animate position
		var $cloned = $row.clone(),
			$targetRow = isUp ? $row.prev() : $row.next(),
			nextTop = isUp ? $row.prev().position().top : $row.next().position().top,
			rH = $targetRow.outerHeight();
               
		$cloned.css({
			position: 'absolute',
			top : $row.position().top + 'px',
            backgroundColor :  $row.css('backgroundColor')
		})
        .find('p.filename').width($row.find('p.filename').outerWidth() + 'px'); //when filename is too long it will fix some issues
		$cloned.find('td.fields').width($row.find('td.fields').outerWidth() + 'px'); //another fixing
        
        $cloned.appendTo($imagesTable);
		
		$row.css('visibility', 'hidden');
		if (isUp)
			$row.insertBefore($targetRow);
		else
			$row.insertAfter($targetRow);
            
		$cloned.animate({top: (isUp ? '-=' : '+=') + (rH + (isUp ? 1 : -1))  }, 200, function(){
			$row.css('visibility', '');
			setTimeout(function(){
				$cloned.remove();
                formDirty();
			}, 50); //prevent flickering
		});
    });   

    $imagesTable.on('mouseover', '.eg-button.preview', function(){
        var $button = $(this),
            filename = '../<?php echo str_replace(GSROOTPATH, '', EG_THUMBS)  ?>' + $button.siblings('input[type="hidden"]').val(),
            offset = $button.offset(),
            bWidth = $button.outerWidth(),
            bHeight = $button.outerHeight(),
			thumbSizes = sizeFromName(filename), //find image sizes from filename
			maxWidth = $(window).width() - offset.left - bWidth - 100,
			maxHeight = $(window).height() - 250,
			fitSize = findFitSize(maxWidth, maxHeight, thumbSizes[0], thumbSizes[1]),
			percent = Math.round(fitSize[0] / thumbSizes[0] * 100),
			$img = $thumbPreview.find('img.thumb');

		//set img sizes
		$img.css({
			width: fitSize[0] + 'px',
			height: fitSize[1] + 'px'
		});
		
		$thumbPreview.find('.status.resolution span:first').text(thumbSizes[0]+'x'+thumbSizes[1]);
		
		if (percent < 100){
			$thumbPreview.find('.status.scaled').show().find('span').text(percent);
		}
		else{
			$thumbPreview.find('.status.scaled').hide()
		}
		
		var tHeight = $thumbPreview.outerHeight(),
			timeout = setTimeout(function(){
				$thumbPreview.stop(true,true); //be sure that is stopped
				
				appendBusyIndicator($thumbPreview);
			
				$img.on('load error', function(){
					removeBusyIndicator($thumbPreview);
					
					$img
						.css({
							visibility : 'visible',
							opacity: 0
						})
						.delay(80).animate({opacity: 1}); //delay after removing busy indicator
				})
				.attr('src', filename);
			
				//set its position
				$thumbPreview.css({
					top: Math.round(offset.top - tHeight / 2 + bHeight / 2) +  'px',
					left: Math.round(offset.left + bWidth + 8) + 'px'
				})
				.show();

			}, 200);


        $button.data('timeout', timeout);
    });   

    $imagesTable.on('mouseout', '.eg-button.preview', function(){
        var $button = $(this),
			$img = $thumbPreview.find('img.thumb'),
            timeout = $button.data('timeout');
            
		clearTimeout(timeout);
		$button.data('timeout', null);
		$thumbPreview.fadeOut(function(){
			$img.off()
				.removeAttr('src')
				.css('visibility', 'hidden');
			 
			removeBusyIndicator($thumbPreview); //remove if its not removed
		});
    });    
	
	//thumbnails clear buttons
    $imagesTable.on('click', '.eg-button.clear', function(){
		var $thumbTd = $(this).closest('td');
		
		setThumbFilename($thumbTd, '');
	});
	
    //thumbnails crop buttons
    $imagesTable.on('click', '.eg-button.crop', function(){   
        var $button = $(this);
        
        if ($button.attr('disabled')) //currently busy waiting for cropping thumb response
            return;
    
		var minWidth = 810, //960 - 150
			minHeight = 300,
			dialogWidth = $(window).width() - 100,
			dialogHeight = $(window).height() - 100,
			$row = $button.closest('tr.image-row'),
			$thumbTd = $button.closest('td'),
			thumbIdx = $thumbTd.index(), //thumbnail row
			thumbData = settings.thumbnails[thumbIdx],
            rowData = $row.data('data'),
			$zoomInfo = $cropDialog.find('.zoom-info'),
			$imageError = $cropDialog.find('.image-error'),
			$sizeInfo = $cropDialog.find('.size-info'),
            $buttonOk = $cropDialog.find('button.ok'),
            isError = $.inArray(thumbIdx, rowData.thumbErrorIndexes) > -1;
			
		//sizes of dialog window
		var borderWidth = parseInt($cropDialog.css('border-top-width')), //top is for FF in css call
			spaceH = 124 + (borderWidth * 2) + 20,//124 are content top + bottom, add 20 for space
			spaceW = (borderWidth * 2) + 20,
			areaHeight = dialogHeight - spaceH, 
			areaWidth = dialogWidth - spaceW;
			
		$zoomInfo.hide();
		$imageError.hide();
        $sizeInfo.find('.row').hide();
        
			
		$cropDialog.data('thumbTd', $thumbTd); //store thumbnail td
		$cropDialog.data('thumbIdx', thumbIdx); //store thumbnail number
		$cropDialog.data('rowData', rowData); //store rowData
		
		appendBusyIndicator($cropDialog);
        

		var fitSize = findFitSize(areaWidth, areaHeight, rowData.imgWidth, rowData.imgHeight),
			percent = Math.round(fitSize[0] / rowData.imgWidth  * 100);
			
			
		//set img sizes
		$cropDialog.find('.content img').css({
			width : fitSize[0] + 'px',
			height : fitSize[1] + 'px',
			opacity: 0
		})
		.on('load error', function(){ //detect when loaded
            var $img = $(this);
			removeBusyIndicator($cropDialog);
            
			$img.stop(true,true).animate({opacity: 1}, function(){
                if (isError)
                    return;
            
                //initiate jcrop
                $cropDialog.data('jcrop', $.Jcrop( $img.get(0), {   
                        aspectRatio : thumbData.width && thumbData.height ? thumbData.width / thumbData.height : undefined,
                        trueSize: [rowData.imgWidth, rowData.imgHeight],
                        onChange: function(selection){
                            var e = false;

                            if (
                                (thumbData.width && selection.w < thumbData.width) ||
                                (thumbData.height && selection.h < thumbData.height)
                               )
                                e = true;       

                            if (e){
                                $buttonOk.attr('disabled', 'disabled');
                                $cropDialog.addClass('area-error');
                            }
                            else{
                                $buttonOk.removeAttr('disabled').focus();
                                $cropDialog.removeClass('area-error');
                                
                                $cropDialog.data('selection', selection);
                            }
                        }
                    })
                );
            });
		})
		.attr('src', ajaxUrl + '&mode=image-crop&img=' + encodeURI(rowData.filename) + '&w=' + areaWidth + '&h=' + areaHeight);

		//find size
		dialogWidth = fitSize[0] + spaceW < minWidth ? minWidth : fitSize[0] + spaceW;
		dialogHeight = fitSize[1] + spaceH < minHeight ? minHeight : fitSize[1] + spaceH;
		
		$cropDialog.css({
			width : dialogWidth + 'px',
			height : dialogHeight + 'px'
		});
		
		if (percent < 100)
			$zoomInfo.show().find('span').text( percent );

		// set header text
		$cropDialog.find('h3 span').text(thumbData.label);

		// image do not meet requirements for this thumbnail or not
		if (isError)
			$imageError.show();
            
		// set tuhmbnail sizes
        if (thumbData.width !== '')
            $sizeInfo.find('.row:eq(0)').show().find('.cell:eq(1) span').text(thumbData.width);        
        if (thumbData.height !== '')
            $sizeInfo.find('.row:eq(1)').show().find('.cell:eq(1) span').text(thumbData.height);
		
		$cropDialog.egDialog({    

            onClose: function(){ 
                $cropDialog.data('thumbTd', null ); //remove reference
                $cropDialog.find('.content img').stop(true).off().removeAttr('src'); //stop animation in
                
                $cropDialog.data('jcrop') && $cropDialog.data('jcrop').destroy();

                $cropDialog.data('selection', null); //remove
                $cropDialog.data('rowData', null); //remove
                $cropDialog.data('thumbIdx', null); //remove thumbnail number
                removeBusyIndicator($cropDialog);
            }
        });
        
        
        
        // $cropDialog.Jcrop({ 
            // aspectRatio : thumbData.width && thumbData.height ? thumbData.width / thumbData.height : undefined,
            // trueSize: [rowData.imgWidth, rowData.imgHeight]
        // });
		
		$cropDialog.find('button.cancel').focus();
    });  
	
    //crop cancel button
	$cropDialog.find('button.cancel').click(function(){
		$cropDialog.egDialog('close');
	});  

	//crop ok button
	$cropDialog.find('button.ok').click(function(){
        if ($(this).attr('disabled'))
            return;
    
		var $thumbTd = $cropDialog.data('thumbTd'),
            selection = $cropDialog.data('selection'),
            rowData = $cropDialog.data('rowData'),
            thumbIdx = $cropDialog.data('thumbIdx');
		
        createThumb(rowData.filename, $thumbTd, thumbIdx, undefined, selection.x, selection.y, selection.w, selection.h);
		
		$cropDialog.egDialog('close');
        
	});  

    //wysiwyg button
    $imagesTable.on('click', '.eg-button.wysiwyg-edit', function(){
        var $button = $(this),
            $row = $button.closest('tr.image-row'),
            $p = $button.closest('p.custom-field'),
            $conectedInput = $p.find('.lang-variant.active');
        
        $wysiwygDialog.find('h3 span').text(settings.fields[$p.index('tr.image-row:eq('+$row.index()+') p.custom-field')].label);
        
        $wysiwygDialog.egDialog({onClose : function(){
			$wysiwygDialog.data('input', null); //reset reference
		}});
        
        if (!$wysiwygDialog.data('cke')){
            var w = CKEDITOR.appendTo($wysiwygDialog.find('.content').get(0), {
                skin : 'getsimple',
				startupFocus : true,
                forcePasteAsPlainText : true,
                elementMode : CKEDITOR.ELEMENT_MODE_NONE,
                language : '<?php echo $EDLANG; ?>',
                defaultLanguage : 'en',
                <?php if (file_exists(GSTHEMESPATH .$TEMPLATE."/editor.css")) { 
                    $fullpath = suggest_site_path();
                ?>
                contentsCss: '<?php echo $fullpath; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
                <?php } ?>
                entities : false, 
                uiColor : '#FFFFFF',
                baseHref : '<?php echo $SITEURL; ?>',
                toolbar : 'basic'
                <?php echo $EDOPTIONS; ?>,
                tabSpaces: 10,
                filebrowserBrowseUrl : 'filebrowser.php?type=all',
                filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
                height: '200px',
                resize_enabled : false
            });
            
            $wysiwygDialog.data('cke', w);
        }
        
        var cke = $wysiwygDialog.data('cke');
        cke.setData($conectedInput.val());
        
        $wysiwygDialog.data('input', $conectedInput); //save connected input
	});
    
    //wysiwyg dialog cancel button
	$wysiwygDialog.find('button.cancel').click(function(){
		$wysiwygDialog.egDialog('close');
	});  

    //wysiwyg dialog ok button
	$wysiwygDialog.find('button.ok').click(function(){
        var cke = $wysiwygDialog.data('cke'),
            $input = $wysiwygDialog.data('input'),
            data = cke.getData(),
            $status = $input.siblings('.wysiwyg-status');
            
        $input.val(data);

        updateWysiwygStatus($status, data);
        
		$wysiwygDialog.egDialog('close');
	});  
	
	//form submit handler
	$editGallery.submit(function(event){
        if (isWaiting){
            event.preventDefault();
			return;
        }
    
		if (waitingThumbCount){
            showWait();
            event.preventDefault();
			return;
		}

        var $rows = $imagesTable.find('.image-row'),
            areError = false,
            errorLangs = [];
        
        $editGallery.find('.not-valid').removeClass('not-valid'); //remove old validation errors
        
        //title input
        if (!settings['title-disabled']){
            var $titleInputs = $editGallery.find('input[name^="title"]');
            
            // if multilanguages than will be multi fields
            $titleInputs.each(function(){
                var $t = $(this);
                
                if (!$t.val().trim()){
                    $t.addClass('not-valid');
                    areError = true;
					
 					if (settings['languages'].length){ //is language field
                        var lang = $t.attr('name').substr(6); //minus title-
                        errorLangs.push(settings.languages[lang]);
					}
                }
            });
        }     
                
        if ( !$nameInput.val().trim()  || !$nameInput.val().match(/[0-9a-z-]+/i) ){
            $nameInput.addClass('not-valid');
            areError = true;
        }

        // console.time('validation');
		$rows.each(function(){
			var $row = $(this);
			
            var res = validateFields($row);
            
			if (!validateThumbs($row))
				areError = true;

            if (res.error){
                areError = true;
                errorLangs = errorLangs.concat(res.errorLanguages);
            }
		});

        // console.timeEnd('validation');
        
        errorLangs = errorLangs.unique(); //remove duplicates but not sorting as jquery.unique
        
        if (areError){
            var errors = ['<?php i18n(EG_ID.'/EDIT_JS_FIELD_ERRORS') ?>'];
            
            if (errorLangs.length)
                errors.push('<?php i18n(EG_ID.'/EDIT_JS_LANG_ERRORS') ?> <b>' + errorLangs.join(', ') + '</b>. <?php i18n(EG_ID.'/EDIT_JS_LANG_USE') ?>');

            showErrorMessage(errors.join('<br/>'));
            event.preventDefault();
            return;
        }
        
        if (    
            (mode == 'edit' && galleryData['name'] != $nameInput.val() && lastValidationName != $nameInput.val() || (mode == 'edit' && !lastValidationResult && galleryData['name'] != $nameInput.val())) ||
            (mode != 'edit' && (lastValidationName != $nameInput.val() || !lastValidationResult))
        ){
            validateGalleryName( $nameInput.val() );
            $nameInput.attr('disabled', 'disabled');
            lastValidationName = $nameInput.val();
            showWait();
            event.preventDefault();
            return;
        }
        
        //renumber fields
        $rows.each(function(index){
            var $row = $(this);
            
            $row.find('input[name="filename"]').attr('name', index + '-filename');
            
            $.each(settings.thumbnails, function(tIndex, thumbData){
                if (thumbData.enabled){
                    $row.find('.thumbnails td:eq('+tIndex+') input[type="hidden"]').attr('name', index + '-thumb-' + tIndex);
                }
            });
            
            $row.find('.fields p.custom-field').each(function(pIndex){
                var $p = $(this);
                
                $p.find('input,textarea,select').each(function(){
                    var $input = $(this),
                        name = $input.attr('name');
                    
                    $input.attr('name', index + '-' + pIndex + (name ? name : ''));
                });
            });	
		}); 
        
        unsavedChanges = false;
        
        //user changed gallery name so delete old one
        if (mode == 'edit' && galleryData['name'] != $nameInput.val()){
            $editGallery.append('<input type="hidden" name="delete" value="'+galleryData['name']+'" />')
        }
        //exit
	});

	//------------------------------- FUNCTIONS ----------------------------------------   
    //image selection handler
	function onImageSelected(filename, width, height, leftCalls){
            
        var $row = createRow(filename, width, height)
            .insertBefore($imagesTable.find('tbody > tr.add-row'));
			
        $.each(settings.thumbnails, function(index, thumb){
            if(
                (thumb['auto-crop'] && thumb.width && thumb.height) &&
                (width >= thumb.width && height >= thumb.height)
              ){
                createThumb(filename, $row.find('.thumbnails td:eq('+index+')'), index, 'fill'); 
            }
        });
		
		if(!leftCalls){
            setTimeout(function(){
                $.scrollTo( $row, 300, {offset:-($(window).height() - $row.outerHeight()) / 2});
            }, 150);
            formDirty();
		}
        
    }
	
	
    function showWait(){
        $wait.show();
        
        $editGallery.find('input.submit').add($sidebarDiv.find('input.submit')).attr('disabled', 'disabled');
        
        isWaiting = true;
    }
    
    function hideWait(){
        $wait.hide();
        $editGallery.find('input.submit').add($sidebarDiv.find('input.submit')).removeAttr('disabled');
        isWaiting = false;
    }
    
    
    function showErrorMessage(message){
        $('.notify_error').remove();

        if ($(window).scrollTop() > 130){
            $.scrollTo(0, 400);
        }
        var m = notifyError(message);
        
        setTimeout(function(){
            m.popit();
        }, 400);
    }  

    function showOkMessage(message){
        var m = notifyOk(message);
        setTimeout(function(){
            m.popit();
        }, 400);
    }
    
    
    function validateGalleryName(name){
        $.ajax({
            url: ajaxUrl,
            data: {
                mode        : 'gallery-validate-name',
                instance    : '<?php echo $this->instanceNum; ?>',
                name        : name                 
            },
            dataType: 'json'
        })
        .done(function(data){
            hideWait();
            $nameInput.removeAttr('disabled');
            
            if (!data){
                $nameInput.addClass('not-valid');
                showErrorMessage('Gallery name is not unique');
                lastValidationResult = false;
            }
            else{
                lastValidationResult = true;
                $editGallery.submit();
            }
        })
        .fail(function(){
            alert('Ajax call failed!');
        });
    };

    
    function createThumb(filename, $thumbTd, thumbIdx, m, x, y, w, h){
        waitingThumbCount++;
        $thumbTd.find('button:eq(0)').attr('disabled', 'disabled');
        
        $thumbTd.find('button:gt(0)').hide();
    
        appendBusyIndicator($thumbTd);

        $.ajax({
            url: ajaxUrl,
            data: {
                mode        : 'thumb-create',
                instance    : '<?php echo $this->instanceNum ?>',
                img         : filename, 
                thumb       : thumbIdx,
                m           : m,
                x           : x,
                y           : y,
                w           : w,
                h           : h                   
            },
            dataType: 'json'
        })
        .done(function(data){
            if (data.error){
                alert('Error: ' + data.error);
                return;
            }
            
            setThumbFilename($thumbTd, data);
        })
        .fail(function(){
            alert('Ajax call failed!');
        })
        .always(function(){
            removeBusyIndicator($thumbTd);
			waitingThumbCount--;
            
            if (isWaiting && waitingThumbCount == 0){
                hideWait();
				$editGallery.submit();
			}
        });
    };
    
    
	//set thumbs file name in hidden input and updates buttons
    function setThumbFilename($thumbTd, value, skipChangeEvent){
        if (value){ 
			$thumbTd.find('button').show().removeClass('not-valid');
            $thumbTd.find('button:eq(0)').removeAttr('disabled');
        }
        else{
			$thumbTd.find('button:not(".crop")').hide();
		}
		
		$thumbTd.find('input[type="hidden"]').val(value).trigger(skipChangeEvent ? '' : 'change');
        
    }   

	//updates status label (empty or filled) in wysiwyg
	function updateWysiwygStatus($statusElement, value){
        if (isEmptyWysiwygValue(value)){ //is empty
            $statusElement.text('<?php i18n(EG_ID.'/EDIT_JS_WYSIWYG_EMPTY') ?>');
        }
        else{
            $statusElement.text('<?php i18n(EG_ID.'/EDIT_JS_WYSIWYG_FILLED') ?>');
        }
    }

    //is wysiwyg empty
    function isEmptyWysiwygValue(value){
        return value && value.replace(/\s|<br\s*?\/>/mig, '') ? false : true;
    } 

	//creates image row
    function createRow(filename, width, height, itemData){
		var $new = $emptyRow.clone();

        $new.find('> .image img.preview-image').attr('src', ajaxUrl + '&mode=thumb-admin&img=' + encodeURI(filename));
        $new.find('> .fields .filename').text(filename);
		$new.find('> .image .resolution span').text(width + 'x' + height);
		
		// store data into data
        $new.data('data', {
            imgWidth : parseInt(width),
            imgHeight : parseInt(height),
			filename : filename
        });
				
		$new.find('> input[name="filename"]').val(filename);
        
        if(itemData){
            $.each(settings['thumbnails'], function(index, thumbData){
                if (!thumbData['enabled'])
                    return;
                    
                if (itemData['thumb-'+index]['filename']){
                    setThumbFilename($new.find('.thumbnails td').eq(index), itemData['thumb-'+index]['filename'], true);
                }
            });
        }
        
        //update statuses of wysiwyg, and fill data for fields
        $new.find('p.custom-field').each(function(index){
            var $p = $(this),
                $status = $p.find('span.wysiwyg-status.active'); //current language span for wysiwyg status
                
            if(itemData){
                for (i = 0; i < langLength; i++) { 
					if (settings['fields'][index]['type'] == 'checkbox' && itemData['languages'][i]['field-'+index])
						$p.find('> .lang-'+ i).prop('checked', true);
					else
						$p.find('> .lang-'+ i).val(itemData['languages'][i]['field-'+index]);
                }  
            }
                
            if (!$status.length)
                return; //continue

            updateWysiwygStatus($status, $p.find('> input.active').val());
        });
        
        validateImageSize($new); //validate image size for thumbnails

		//add new row 
		$new.removeClass('empty-row').addClass('image-row');

        return $new;
    }
    
    //validate image size for required thumbnails, by row
	function validateImageSize($row){

		var rowData = $row.data('data'),
            notValidSize = false,
			smallForThumbsIdx = [],
            requiredIndexes = [],
			failedSizeValidation = false;
					
			
		if(settings['required-width-comparator'] == 'range' && settings['required-width-ranges'].length){
			if (!validateSize('range', settings['required-width-ranges'], rowData.imgWidth))
				failedSizeValidation = true;
		}
		else if (settings['required-width'] !== ''){
			if (!validateSize(settings['required-width-comparator'], settings['required-width'], rowData.imgWidth))
				failedSizeValidation = true;
		}	

		if(settings['required-height-comparator'] == 'range' && settings['required-height-ranges'].length){
			if (!validateSize('range', settings['required-height-ranges'], rowData.imgHeight))
				failedSizeValidation = true;
		}
		else if (settings['required-height'] !== ''){
			if (!validateSize(settings['required-height-comparator'], settings['required-height'], rowData.imgHeight))
				failedSizeValidation = true;
		}

			
		if (!failedSizeValidation){
			$.each(settings.thumbnails, function( index, thumb ) {
				if ( thumb.enabled ){
					if ( thumb.required ){  
						requiredIndexes.push(index);
					}
					
					if (thumb.width !== '' && rowData.imgWidth < thumb.width){
						smallForThumbsIdx.push(index);
						return; //continue loop, to not add again when height fails
					}       

					if (thumb.height !== '' && rowData.imgHeight < thumb.height){
						smallForThumbsIdx.push(index);
					}
				}
			});
			
			rowData.thumbErrorIndexes = smallForThumbsIdx; //add indexes of thumbs that cropping should be blocked
			
			if (smallForThumbsIdx.length){ //image is not proper for thumbnails, show error
				
				
				//check that any required failed
				$.each(requiredIndexes, function(index, value){
					if ($.inArray(value, rowData.thumbErrorIndexes) > -1){
						$row.find('.thumbnails td:eq('+value+') button.crop').addClass('not-valid');
						notValidSize = true;
					}
				});
			}
		}
		
        
        if (notValidSize || failedSizeValidation){ 
			var $errorDiv = $row.find('.error-info');
			
			if(notValidSize)
				$errorDiv.append('<?php i18n(EG_ID.'/EDIT_JS_IMAGE_SMALL_FOR_THUMBS') ?>');
				
			if (failedSizeValidation)
				$errorDiv.append('<?php i18n(EG_ID.'/EDIT_JS_IMAGE_SMALL_REQUIRED') ?>');
			
            $row.find('input,textarea,select,button.wysiwyg-edit,button:not(.delete)').attr('disabled', 'disabled');
            $errorDiv.show();
        }
        
        rowData.notValidSize = notValidSize || failedSizeValidation;
        // alert(failedSizeValidation);
	}	
	
	function validateSize(comparator, targetValue, value){
        switch(comparator){
            case 'lte':{
                if (value > targetValue)
                    return false;
                break;
            }
            case 'gte':{
                if (value < targetValue) 
                    return false;
                break;
            }     
			case 'range':{
				var result = false;
				$.each(targetValue, function( index, range ) {
					var from = range[0],
						to = range[1];
						
					if (value >= from && value <= to)
						result = true;
				});
				if (!result)
					return false;
                break;
            }
            default:{ //=
                if (value != targetValue)
                   return false;
                break;
            }
        }
        
        return true;
    }
	
	function validateThumbs($row){
		var notValid = false;
	
		$.each(settings.thumbnails, function( index, thumb ) {
			if (thumb.enabled && thumb.required){
				var $td = $row.find('.thumbnails td').eq(index),
					$input = $td.find('input[type="hidden"]');
				
				if (!$input.val()){
					notValid = true;
					$td.find('button.crop').addClass('not-valid');
				}
			}
		});
		
		return !notValid;
	}
	
	
	//validates fields and highlights it whats required
	function validateFields($row){
		var result = {
                error : false,
                errorLanguages : []
            };
            
        //skip marking fields, coz image size is too small for required thumbs
        if ($row.data('data').notValidSize){
			result.error = true;
            return result;
        }

		$.each(settings.fields, function( index, field ) {
			if (field.required && field.type != 'checkbox'){
                var $p = $row.find('p.custom-field').eq(index),
                    $input = $p.find('.lang-variant').filter(':input'), //only inputs
                    isWysiwyg = field.type == 'wysiwyg';

                //if there are language variants, $input is multiple selector
                $input.each(function(index){
                    var $i = $(this);

                    var val = $i.val();
                    
                    val = val ? val.trim() : val;
                    
                    if ( isEmptyWysiwygValue(val) ){
                        result.error = true;
                          
                        if (index > 0){ //is language field
                            var lang = $i.attr('name').substr(1);
                            result.errorLanguages.push(settings.languages[lang]);
                        }else if(settings.languages.length){ //not language field but languages exists, add first lang
                            result.errorLanguages.push(settings.languages[0]);
                        }
                        
                        if (isWysiwyg){
                            $p.find('button.wysiwyg-edit').addClass('not-valid');
                        }else{
                            $i.addClass('not-valid');
                        }
   
                    }
                });

			}
		});

		return result;
	}
	

	//appends busy indicator to element
	function appendBusyIndicator($parent){
        var $i = $('<img />').addClass('busy-indicator').attr('src', '../plugins/ExtraGallery/img/ajax-loader.gif'); //prevents memory leak when row removed by jquery
		$parent.append($i);
	}	
	
	//removes busy indicator from element
	function removeBusyIndicator($parent){
		$parent.find('.busy-indicator').remove();
	}
    
    /* 
     * Shows popup to enter desired position
    */
    function showPositionPopup($source){
        $positionPopupOpener = $source;
        
        $positionPopupOpener.addClass('selected');
        
        var pos = $positionPopupOpener.position();
       
        $positionPopup.css({
            top : (pos.top - $positionPopup.outerHeight() / 2) + $positionPopupOpener.outerHeight() / 2 + 'px',
            left : pos.left + $source.outerWidth() + 7 + 'px'
        }).show();
        
        $positionPopup.find('input').focus();
    }   

    function hidePositionPopup(){
        if (!$positionPopupOpener)
            return;
    
        $positionPopupOpener.removeClass('selected');
        $positionPopupOpener = null;
        $positionPopup.hide();
        $positionPopup.find('input').val('');
        
    }
	
	//find sizes of image keeping aspect ratio that will fit into maxwidth max height area
	function findFitSize(maxWidth, maxHeight, imgWidth, imgHeight){
		
		var r = [];
		
		r[0] = maxWidth;
		r[1] = maxHeight;
		
		//width and height if they smaller than fit area
		if(imgWidth <= maxWidth && imgHeight <= maxHeight){
			r[0] = imgWidth;
			r[1] = imgHeight;
			return r;
		}

		//reclaculate to preserve aspect ratio (if aspect ratio is not the same)
		if(maxWidth / maxHeight != imgWidth / imgHeight){
			if(	maxWidth / imgWidth > maxHeight / imgHeight ){ //tall
				r[0] = maxHeight / imgHeight * imgWidth;
			}else{
				r[1] = maxWidth / imgWidth * imgHeight; //wide
			}
		}
		 //ceil as in php
		r[0] = Math.round(r[0]);
		r[1] = Math.round(r[1]);
		
		return r;
		
	}
	
	//finds image size from image name
	function sizeFromName (filepath){
		var slashPos = filepath.lastIndexOf('/') + 1,
			filename = filepath.substr(slashPos, filepath.lastIndexOf('.')),
			a = filename.split('-'),
			res = [];
			
		if (a.length < 2)
			throw 'Extra Gallery: Cannot find image size in file name.';

		res[0] = parseInt(a[a.length - 2]); //width
		res[1] = parseInt(a[a.length - 1]); //height
		
		return res;
	}
	
	

});
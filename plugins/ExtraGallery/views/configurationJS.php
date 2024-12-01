$( document ).ready(function() {
    var $mainContent = $('#maincontent'),
        $configForm = $('#configForm'),
        $submit = $configForm.find('input[type="submit"]'),
        $add = $('#add'),
        $fields = $('table.fields'),
        $emptyFieldsRow = $fields.find('tbody tr:last'),
        $emptyRangeRow = $configForm.find('.range-row').first(),
        $emptyThumnailSettings = $('.section.thumbnail');
        
	var settingsData = <?php echo json_encode($settings) ?>
        
    var fieldsData = settingsData['fields'];

    var thumbnailsData = settingsData['thumbnails'];
    
    var message = '<?php echo $message ?>',
        isErrorMessage = '<?php echo $isErrorMessage ?>';
        
    //show startup message
    if (message){
        if (isErrorMessage)
            showErrorMessage(message);
        else
            showOkMessage(message);
    }

    
    //----------------------------- funcitons and handlers -----------------------

    $fields.sortable({
        items : 'tbody tr',
        update : renumberFieldNames
    });
        
    $add.click(function(){
        $emptyFieldsRow.clone().insertBefore($emptyFieldsRow).show();
        renumberFieldNames();
    });  

    //delete button
    $fields.on('click', 'button.delete', function(){
        $(this).closest('tr').remove();
        renumberFieldNames();
    });   

    //selection change
    $fields.on('change', 'select[name="type"]', function(event){
        var $select = $(this),
            isSelectType = $select.val() == 'select';
            
            if(isSelectType){
                $select.siblings('.options').show();
            }else{
                $select.siblings('.options').hide();
            }
    });   

    //enabled toggle
    $configForm.on('change', '.section.thumbnail input[name="enabled"]', function(event){
        var $cbo = $(this),
            $section = $cbo.closest('.section');
        
        if (!$cbo.is(':checked'))
            $section.find('input,select').not(':first').attr('disabled', 'disabled');
        else{    
            $section.find('input,select').not(':first').removeAttr('disabled');
        }
    });
	
	//on changing select show range controls when needed
	$configForm.find('select[name$="comparator"]').on('change', function(){
		var $select = $(this),
			val = $select.val();
		
		if (val == 'range'){
			$select.next('input[type="text"]').hide(); //hide input text
			$select.siblings('.ranges').show();
		}
		else{
			$select.next('input[type="text"]').show();
			$select.siblings('.ranges').hide();
		}
	});	
	
	//range add button
	$configForm.find('.ranges button.add').on('click', function(){
		createRangeRow('', '', $(this).parent('.ranges').find('.range-row').last());
	});

	//range delete button
	$configForm.on('click', '.ranges button.delete', function(){
		$(this).closest('.range-row').remove();
	});
    
    $configForm.submit(function(event){
        
        var error = false,
            $fieldRows = $fields.find('tbody tr:not(:last)'),
            $thumbSections = $configForm.find('.section.thumbnail'),
            $tabLabelInput = $configForm.find('input[name="tab-label"]'),
            $langInput = $configForm.find('input[name="languages"]'),
            $requiredWidthComparator = $configForm.find('select[name="required-width-comparator"]'),
            $requiredWidth = $configForm.find('input[name="required-width"]'),
            $requiredWidthRanges = $configForm.find('input[name="required-width-ranges"]'),
            $requiredHeightComparator = $configForm.find('select[name="required-height-comparator"]'),
            $requiredHeight = $configForm.find('input[name="required-height"]'),
			$requiredHeightRanges = $configForm.find('input[name="required-height-ranges"]');
			
			
		//store all ranges into hidden input
		$configForm.find('.ranges').each(function(){
			// alert();
			var $rangeContainer = $(this),
				array = [];
			
			$rangeContainer.find('.range-row:visible').each(function(){
				var $row = $(this),
					rowA = [];
				
				$row.find('input').each(function(){
					rowA.push (parseInt($(this).val()));
				});
				array.push(rowA.join(','));
			});
			$rangeContainer.find('input[type="hidden"]').val(array.join(';'));
			
	
		});
			
        //validate options
        if ($tabLabelInput.val().trim() == ''){
            $tabLabelInput.addClass('not-valid');
            error = true;
        }
        else
            $tabLabelInput.removeClass('not-valid');
            
            
        if ( $langInput.val() && !$langInput.val().match(/^[a-z]+(,[a-z]+)*$/gi) ){
            error = true;
            $langInput.addClass('not-valid');
        }
        else{
            $langInput.removeClass('not-valid');
        }   
		
		if ($requiredWidth.val() || $requiredWidthRanges.val()){

			if($requiredWidthComparator.val() == 'range'){
				$requiredWidthComparator.siblings('.ranges').find('.range-row:visible input').each(function(){
					var $input = $(this),
						fail = !$input.val().match(/^[0-9]+$/);
					$input.toggleClass('not-valid', fail );
					
					if(fail)
						error = fail;
				});
			}
			else if($requiredWidth.val()){
				var fail = !$requiredWidth.val().match(/^[0-9]+$/);
	
				$requiredWidth.toggleClass('not-valid', fail);
				if(fail)
					error = fail;
			}
		}		
		
		if ($requiredHeight.val() || $requiredHeightRanges.val()){
	
			if($requiredHeightComparator.val() == 'range'){
				$requiredHeightComparator.siblings('.ranges').find('.range-row:visible input').each(function(){
					var $input = $(this),
						fail = !$input.val().match(/^[0-9]+$/);
					$input.toggleClass('not-valid', fail );
					
					if(fail)
						error = fail;
				});
			}
			else if($requiredHeight.val()){
				var fail = !$requiredHeight.val().match(/^[0-9]+$/);
	
				$requiredHeight.toggleClass('not-valid', fail);
				if(fail)
					error = fail;
			}
		}
		

        
        //validate all custom fields that has label
        $fieldRows.each(function(index){
            var $labelInput = $(this).find('input[name="label"]');
            
            if ($labelInput.val().trim() == ''){
                error = true;
                $labelInput.addClass('not-valid');
            }
            else{
                $labelInput.removeClass('not-valid');
            }
        });    

        
        //validate thumbnail seetings
        $thumbSections.each(function(index){
            var $thumbSection = $(this);
            
			//validate only if enabled
            if (!$thumbSection.find('input[name="enabled"]').is(':checked'))
                return;

            var $width = $thumbSection.find('input[name="width"]'),
                $height = $thumbSection.find('input[name="height"]'),
                $label = $thumbSection.find('input[name="label"]');
                
            if ( !$label.val().trim() ){
                $label.addClass('not-valid');   
                error = true;
            }
            else
                $label.removeClass('not-valid');         

            //if we have width check is it above 0 and integer
            if ( $width.val() !== '' && !isInt($width.val()) ){
                $width.addClass('not-valid');   
                error = true;
            }
            else
                 $width.removeClass('not-valid');        

            if ( $height.val() !== '' && !isInt($height.val()) ){
                $height.addClass('not-valid');   
                error = true;
            }
            else
                $height.removeClass('not-valid');

        });

        if (error){
            event.preventDefault();
            $('.notify_error').remove();
            $("html, body").animate({scrollTop: 0});
            notifyError('<?php i18n(EG_ID.'/CONF_VALIDATION') ?>').popit();
            return;
        }
        
        //number inputs names
        $fieldRows.each(function(index){
            $(this).find('input,select,textarea').each(function(){
                $(this).attr('name', 'field-' + index + '-'+$(this).attr('name'));
            });
        });   

        //number thumnails inputs names
        $thumbSections.each(function(index){
            $(this).find('input,checkbox,select').each(function(){
                $(this).attr('name', 'thumb-' + index + '-'+$(this).attr('name'));
            });
        });

    });
    

    function renumberFieldNames(){
        $fields.find('tbody tr:not(:last) td:first-child').each(function(index){
            $(this).html('field-' + (index));
        });
    }
	
	// function fillRanges(){
		
	// }
    
	//fills all fields data on start
    function fillFieldsData(){

		$configForm.find('select[name="required-width-comparator"]').val(settingsData['required-width-comparator']).trigger('change');
		$configForm.find('select[name="required-height-comparator"]').val(settingsData['required-height-comparator']).trigger('change');		

		//is range type and ranges whas defined
		if (settingsData['required-width-comparator'] == 'range' && settingsData['required-width-ranges'].length){
			$.each(settingsData['required-width-ranges'], function( index, range ) {
				createRangeRow(range[0], range[1], $configForm.find('select[name="required-width-comparator"]').siblings('.ranges').find('.range-row').last());
			});
		}
		
		if (settingsData['required-height-comparator'] == 'range' && settingsData['required-height-ranges'].length){
			$.each(settingsData['required-height-ranges'], function( index, range ) {
				createRangeRow(range[0], range[1], $configForm.find('select[name="required-height-comparator"]').siblings('.ranges').find('.range-row').last());
			});
		}


	
        $.each( fieldsData, function( index, field ) {
            var $row = $emptyFieldsRow.clone().insertBefore($emptyFieldsRow);
              
            if (field.required)
                $row.find('input[name="required"]').attr('checked', 'checked');
                
            $row.find('select[name="type"]').val(field.type); 
            if (field.type == 'select'){
                $row.find('div.options').show().find('[name="options"]').val(field.options.join("\n"));
            }
            $row.find('input[name="label"]').val(field.label);
            
            $row.show();
        });
        
        renumberFieldNames();
    }   

    function fillThumbnailsData(){
        $.each( thumbnailsData, function( index, thumb ) {
            var $thumbSection = $emptyThumnailSettings.clone().insertBefore($submit);
              
            if (thumb.required)
                $thumbSection.find('input[name="required"]').attr('checked', 'checked');            
                
            if (thumb.enabled)
                $thumbSection.find('input[name="enabled"]').attr('checked', 'checked');     
 
            $thumbSection.find('input[name="label"]').val(thumb.label);
            $thumbSection.find('input[name="width"]').val(thumb.width);
            $thumbSection.find('input[name="height"]').val(thumb.height);
            $thumbSection.find('select[name="auto-crop"]').val(thumb['auto-crop']);
           
                
            $thumbSection.find('h3').html('<?php i18n(EG_ID.'/CONF_THUMB_HEADER') ?>'.replace('%s', (index)) );
            
            $thumbSection.show();
        });
        
        //trigger change event to disable/enable subfields
        $configForm.find('.section.thumbnail input[name="enabled"]').trigger('change');
    }
	
	function createRangeRow(fromVal, toVal, $insertBefore){
		
		var $newRow = $emptyRangeRow.clone().show().insertBefore($insertBefore),
			$inputs = $newRow.find('input');
			
		$inputs.eq(0).val(fromVal);
		$inputs.eq(1).val(toVal);

	}
    
    function isInt(value) {
        return !isNaN(value) && parseInt(value) == value && parseInt(value) > 0;
    }
    
    function showErrorMessage(message){
        $('.notify_error').remove();

        if ($(window).scrollTop() > 130){
            $.scrollTo(0, 400);
        }
        notifyError(message).popit();
    }  

    function showOkMessage(message){
        notifyOk(message).popit();
    }
    
    
    //------------------------------- main logic ------------------------------------

	
	
    // fillRanges();
    fillFieldsData();
    fillThumbnailsData();
    


   
});
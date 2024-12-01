

jQuery(document).ready(function() { 

$("#editpages").tablesorter({	
	widgets: ['zebra'],
	sortList: [[0,0]],
})

.tablesorterPager({container: $("#pager")})


$('.askconfirm').jConfirmAction();

//$('#nav_DM_Matrix').insertAfter($('#nav_pages'));

$("button.form_submit").on("click", function(){
		errors=false;
		$('.required').each(function(index) { 
			if ($(this).removeClass('formerror'));
			if ($(this).val()=="") {
				$(this).addClass('formerror');
				errors=true;
			}
		})
		if (errors==false){
			$(this).closest('form').submit();
		}
		return false;
	});	

$('select#post-type').on("change",function() {
	fieldtype=$(this).val();
	switch (fieldtype){
		case 'dropdown':
			$('#fieldoptions').html($('#field-dropdown').html());
			$('#post-table').on("change",function() {
				fields = $('#post-table option:selected').attr('data-fields');
				$('#post-rows').find('option').remove().end();
				 var fieldArray = fields.split(',');
			    for(var i=0;i<fieldArray.length-1;i++){
			        $('#post-row').append('<option value="' + fieldArray[i] + '" >'+ fieldArray[i]  + '</option>');
			    }
			})
			break; 
		default: 
			$('#fieldoptions').html('');
			break; 
	}
})





$('#dm_addnew').on("click", function(){
	$('#DM_addnew_row').stop().slideUp();
	$(this).next().stop().slideToggle();
	return false;	
})
	
	
$('#addfield').on("click", function(){
	errors=false;
		$('.required').each(function(index) { 
			if ($(this).removeClass('error'));
			if ($(this).val()=="") {
				$(this).addClass('error');
				errors=true;
			}
		})
		if (errors==false){
			$(this).closest('form').submit();
		}
})


  $('.mtrx_but').button();
  $('.mtrx_but_add').button({icons:{primary: "mtrx_dbadd"}});


})


function addImageThumbNail(txt){
	el=txt.replace('post-','image-');
	filepath=$('#'+txt).val();
	filepath=filepath.replace('/uploads/','/thumbs/');
	var fileNameIndex = filepath.lastIndexOf("/") + 1;
	var filename = filepath.substr(fileNameIndex);
	filepath=filepath.replace(filename,'thumbsm.'+filename);
	$('#'+el).empty().append('<img src="'+filepath+'" alt="" />');
	
}

function makeSlug(element) {
    var Text = $('#'+element).val();
    Text = Text.toLowerCase();
    var regExp = /\s+/g;
    Text = Text.replace(regExp,'-');
    $('#' + element+"").val(Text);
}



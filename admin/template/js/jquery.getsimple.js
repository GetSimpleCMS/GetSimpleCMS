/*
 * GetSimple js file	
 */

function DeleteComp(id) {
	var message = $("#del-" + id).attr("title");
	var answer = confirm(message);
  $('#loader').show();
  if (answer){
  	$("#section-" + id).slideToggle('40000').remove();
  	if($("#divlist-" + id).length) {
  		$("#divlist-" + id).remove();
  	}  	
  }
  $('#loader').fadeOut(1000);
	return false;
};
			
function zebraRows(selector, className) {
	$(selector).parents('table').children().removeClass(className); 
  $(selector).addClass(className);  
};
			
function updateCoords(c) {
	$('#handw').show();
  $('#x').val(c.x);
  $('#y').val(c.y);
  $('#w').val(c.w);
  $('#h').val(c.h);
  $('#pich').html(c.h);
  $('#picw').html(c.w);
};

function checkCoords()
{
  if (parseInt($('#x').val())) return true;
  alert('Please select a crop region then press submit.');
  return false;
};
jQuery.fn.fadeToggle = function(speed, easing, callback) { 
   return this.animate({opacity: 'toggle'}, speed, easing, callback); 
};

jQuery.fn.wait = function(time, type) {
  time = time || 10000;
  type = type || "fx";
  return this.queue(type, function() {
    var self = this;
    setTimeout(function() {
       $(self).dequeue();
    }, time);
  });
};

jQuery(document).ready(function() { 
	
	
	// upload.php
	$('#mainftp').uploadify({
  	'uploader'	: 'template/js/uploadify/uploadify.swf',
  	'script'		: 'upload-ajax.php',
  	'multi'			: true,
  	'auto'			: true,
  	'height'		:	'17',
  	'width'			:	'190',
  	'buttonImg' : 'template/images/browse.png',
  	'cancelImg' : 'template/images/cancel.png',
		'folder'    : '../data/uploads/',
		'scriptData': { 'sessionHash' : $('#hash').val() },
		onProgress: function() {
		  $('#loader').show();
		},
		onAllComplete: function() {
		  $('#loader').fadeOut(500);
		  $("#imageTable").load(location.href+" #imageTable","");
		}	
	});
	$("#imageFilter").change(function(){
		$('#loader').show();
		var filterx = $(this).val();
		$("#imageTable tr").hide();
		if (filterx == 'Images'){
			$("#imageTable tr .imgthumb").show();
		} else {
			$("#imageTable tr .imgthumb").hide();
		}		
		$("#filetypetoggle").html('&nbsp;&nbsp;/&nbsp;&nbsp;' + filterx);
		$("#imageTable tr." + filterx)
			.removeClass('trodd')
			.show()
   		.filter(':odd')
   		.addClass('trodd');
   	$("#imageTable tr.deletedrow").hide();
   	$('#loader').fadeOut(500);
	});

    	
	//image.php	
	$("select#img-info").change(function() {
		var codetype = $(this).val();
		var code = $('p#'+ codetype).html();
		var originalBG = $('textarea.copykit').css('background-color'); 
		var fadeColor = "#FFFFD1"; 
		$('textarea.copykit')
			.fadeOut(500)
			.fadeIn(500)
			.html(code)
	});
	$(".select-all").live("click", function() {
    $('textarea.copykit').focus().select();
    return false;
  });
		
	
	// components.php
	$(".delconfirmcomp").live("click", function() {
		$('#loader').show();
		var message = $(this).attr("title");
		var answer = confirm(message);
	    if (answer){
	    	var compid = $(this).attr("rel");
	    	$(compid).slideToggle(500).remove();
	    }
	  $('#loader').fadeOut(500);
	  return false;
	});
	$("#addcomponent").live("click", function() {
		$('#loader').show();
		var id = $("#id").val();
		$("#divTxt").append('<div style="display:none;" class="compdiv" id="section-' + id + '"><table class="comptable"><tr><td><b>Title: </b><input type="text" class="text newtitle" name="title[]" value="" /></td><td class="delete"><a href="#" title="Delete Component:?" id="del-'+ id +'" onclick="DeleteComp('+ id +'); return false;" >X</a></td></tr></table><textarea name="val[]"></textarea><input type="hidden" name="slug[]" value="" /><input type="hidden" name="id[]" value="' + id + '" /><div>');
		$("#section-" + id).slideToggle('fast');
		id = (id - 1) + 2;
		$("#id").val(id);
		$('#loader').fadeOut(500);
		return false;
	});
	$("b.editable").dblclick(function () {
		var t = $(this).html();
		$(this).parents('.compdiv').find(".compslugcode").remove();
		$(this).parents('.compdiv').find("input.comptitle").remove();
		$(this).after('<b>Title: </b><input class="text newtitle titlesaver" name="title[]" value="'+t+'" />');
		$(this).parents('.compdiv').find("input.compslug").attr({ value: '' });
		$(this).remove();
	});

		
	// table functions
	$('table.highlight tr').hover( 
		function() {$(this).addClass('activeedit');}, 
		function() {$(this).removeClass('activeedit'); 
	});
	zebraRows('table.highlight tr:odd', 'trodd'); 
	$('table.paginate tr').quickpaginate( { perpage: 15, showcounter: true, pager : $("#page_counter") } );
	


	// other general functions
	$(".snav a.current").live("click", function() {
		return false;
	});
	$(".delconfirm").live("click", function() {
		var message = $(this).attr("title");
		var dlink = $(this).attr("href");
	    var answer = confirm(message);
	    var id=$(this).parents("tr").attr("id");
	    
	    if (answer){
	    	$('#loader').show();
	    	$("#"+id).addClass('deletedrow');
	    	$("#"+id).fadeOut(500, function(){
					$.ajax({
			       type: "GET",
			       url: dlink,
			       success: function(response){
			          $("#"+id).remove();
			          $("#page_counter").html("");
			          if($("#pg_counter").length) {
			        	  counter=$("#pg_counter").html();
				          $("#pg_counter").html(counter-1);
				      }
			          $('table.paginate tr').quickpaginate( { perpage: 15, showcounter: true, pager : $("#page_counter") } );
			          
			          //added by dniesel
                if($(response).find('div.error').html()) {
                  $('div.bodycontent').before('<div class="error">'+ $(response).find('div.error').html() + '</div>'); 
                }
                if($(response).find('div.updated').html()) {
                  $('div.bodycontent').before('<div class="updated">'+ $(response).find('div.updated').html() + '</div>'); 
                }
				     }
				  });
					$('#loader').fadeOut(500);
				});
	    } else {
	    	return false;
	    }
	    return false;
	});
	//$("input[type='text']:first", document.forms[0]).focus();
	$("#waittrigger").click(function(){
		$('#loader').fadeIn();
		$("#waiting").fadeIn(1000).fadeOut(1000).fadeIn(1000).fadeOut(1000).fadeIn(1000).fadeOut(1000).fadeIn(1000);
	});
	$(".updated").fadeOut(500).fadeIn(500);
	$(".error").fadeOut(500).fadeIn(500);
	$('a[rel*=facybox]').facybox()

	
	// edit.php
	$("#metadata_toggle").live("click", function() {
		$("#metadata_window").slideToggle('fast');
		$(this).toggleClass('current');
		return false;
	});
	$("#post-menu-enable").live("click", function() {
      $("#menu-items").slideToggle("fast");
	});
  if ($("#post-menu-enable").is(":checked")) { 
  } else {
     $("#menu-items").css("display","none");
  }
	$('#editftp').uploadify({
  	'uploader'	: 'template/js/uploadify/uploadify.swf',
  	'script'		: 'upload-ajax.php',
  	'multi'			: true,
  	'auto'			: true,
  	'height'		:	'17',
  	'width'			:	'190',
  	'buttonImg' : 'template/images/browse.png',
  	'cancelImg' : 'template/images/cancel.png',
		'folder'    : '../data/uploads/',
		'scriptData': { 'sessionHash' : $('#hash').val() },
		onProgress: function() {
		  $('#loader').show();
		},
		onAllComplete: function() {
		  $('#loader').fadeOut(500);
		}	
	});
	$('.set-example-text').example(function() {
		return $(this).attr('rel');
	}, {className: 'example-text'});  
  
  var edit_line = $('#submit_line span').html();
  $('#js_submit_line').html(edit_line);
  $("#js_submit_line input.submit").live("click", function() {
      $("#submit_line input.submit").trigger('click');
	});
  
  
  // pages.php
  $("#show-characters").live("click", function() {
  	 $(".showstatus").toggle();
  });
  
  
  
  // log.php
  $('ol.more li').reverseOrder();  
  $("ol.more").each(function() {
    $("li:gt(4)", this).hide(); /* :gt() is zero-indexed */
    $("li:nth-child(5)", this).after("<li class='more'><a href='#'>More...</a></li>"); /* :nth-child() is one-indexed */
  });
  $("li.more a").live("click", function() {
    var li = $(this).parents("li:first");
    li.parent().children().show();
    li.remove();
    return false;
  });
  
  
  
  // theme-edit.php
 	$("#theme-folder").change(function(){
    var thmfld = $(this).val();
	  $.ajax({
       type: "GET",
       url: "inc/ajax.php?dir="+thmfld,
       success: function(response){
         $("#themefiles").html(response);
	     }
	  });
	});





//end of javascript for getsimple
}); 

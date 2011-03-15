/* reverseOrder : jQuery order reverser plugin
 * Written by Corey H Maass for Arc90
 * (c) Arc90, Inc.
 * 
 * Licensed under:Creative Commons Attribution-Share Alike 3.0 http://creativecommons.org/licenses/by-sa/3.0/us/
 */
(function($){$.fn.reverseOrder=function(){return this.each(function(){$(this).prependTo($(this).parent())})}})(jQuery);

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
			
function updateCoords(c) {
	$('#handw').show();
  $('#x').val(c.x);
  $('#y').val(c.y);
  $('#w').val(c.w);
  $('#h').val(c.h);
  $('#pich').html(c.h);
  $('#picw').html(c.w);
};

function checkCoords() {
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

function attachFilterChangeEvent() {
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
		$("#imageTable tr." + filterx).show();
		$("#imageTable tr.folder").show();
		$("#imageTable tr:first-child").show();
   	$("#imageTable tr.deletedrow").hide();
   	$('#loader').fadeOut(500);
	});
}

jQuery(document).ready(function() { 
	// upload.php
	attachFilterChangeEvent();

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
  
  $("a#refreshlanguage").live("click", function($e) {
    var begin = $(this).attr('href');
    var ending = $("#lang option:selected").val();
    var page = begin + ending;
    $e.preventDefault();
	  document.location.href = page;
  });
		
	
	// components.php
	$(".delconfirmcomp").live("click", function($e) {
		$e.preventDefault();
		$('#loader').show();
		var message = $(this).attr("title");
		var answer = confirm(message);
	    if (answer){
	    	var compid = $(this).attr("rel");
	    	$(compid).slideToggle(500).remove();
	    }
	  $('#loader').fadeOut(500);
	});
	
	$("#addcomponent").live("click", function($e) {
		$e.preventDefault();
		$('#loader').show();
		var id = $("#id").val();
		$("#divTxt").append('<div style="display:none;" class="compdiv" id="section-' + id + '"><table class="comptable"><tr><td><b>Title: </b><input type="text" class="text newtitle" name="title[]" value="" /></td><td class="delete"><a href="#" title="Delete Component:?" id="del-'+ id +'" onclick="DeleteComp('+ id +'); return false;" >X</a></td></tr></table><textarea name="val[]"></textarea><input type="hidden" name="slug[]" value="" /><input type="hidden" name="id[]" value="' + id + '" /><div>');
		$("#section-" + id).slideToggle('fast');
		id = (id - 1) + 2;
		$("#id").val(id);
		$('#loader').fadeOut(500);
		$('#submit_line').fadeIn();
	});
	$("b.editable").dblclick(function () {
		var t = $(this).html();
		$(this).parents('.compdiv').find("input.comptitle").hide();
		$(this).after('<div id="changetitle"><b>Title: </b><input class="text newtitle titlesaver" name="title[]" value="'+t+'" /></div>');
		$(this).parents('.compdiv').find("input.compslug").val('');
		$(this).hide();
	});
	$("input.titlesaver").live("keydown", function () {
		var myval = $(this).val();
		$(this).parents('.compdiv').find(".compslugcode").html("'"+myval.toLowerCase()+"'");
		$(this).parents('.compdiv').find("b.editable").html(myval);
	}).live("focusout", function () {
		var myval = $(this).val();
		$(this).parents('.compdiv').find(".compslugcode").html("'"+myval.toLowerCase()+"'");
		$(this).parents('.compdiv').find("b.editable").html(myval);
		$("b.editable").show();
		$('#changetitle').remove();
	});

		
	// table functions
	if(jQuery().quickpaginate) {
		$('table.paginate tr').quickpaginate( { perpage: 15, showcounter: true, pager : $("#page_counter") } );
	}


	// other general functions
	$(".snav a.current").live("click", function($e) {
		$e.preventDefault();
	});
	$(".confirmation").live("click", function($e) {
		$('#loader').show();
		var message = $(this).attr("title");
		var answer = confirm(message);
	    if (!answer){
	    	$('#loader').fadeOut(500);
	    	return false;
	    }
	  $('#loader').fadeOut(500);
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
				      	if(jQuery().quickpaginate) {
				          $('table.paginate tr').quickpaginate( { perpage: 15, showcounter: true, pager : $("#page_counter") } );
				        }
				        
				        $('div.wrapper .updated').remove();
				        $('div.wrapper .error').remove();
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
	if(jQuery().fancybox) {
		$('a[rel*=facybox]').fancybox();
	}
	
	// edit.php
	$("#metadata_toggle").live("click", function($e) {
		$e.preventDefault();
		$("#metadata_window").slideToggle('fast');
		$(this).toggleClass('current');
	});
	$("#post-private").change(function(){
	  if ($("#post-private").is(":checked")) { 
	  	$("#post-private-wrap label").css("color", '#cc0000');
	  } else {
	    $("#post-private-wrap label").css("color", '#333333'); 
	  }
	});
	if ($("#post-private").is(":checked")) { 
  	$("#post-private-wrap label").css("color", '#cc0000');
  } else {
    $("#post-private-wrap label").css("color", '#333333'); 
  }
	$("#post-menu-enable").live("click", function() {
      $("#menu-items").slideToggle("fast");
	});
  if ($("#post-menu-enable").is(":checked")) { 
  } else {
     $("#menu-items").css("display","none");
  }
  
  var edit_line = $('#submit_line span').html();
  $('#js_submit_line').html(edit_line);
  $("#js_submit_line input.submit").live("click", function() {
      $("#submit_line input.submit").trigger('click');
	});
  
  
  // pages.php
  $("#show-characters").live("click", function() {
  	 $(".showstatus").toggle();
  	 $(this).toggleClass('current');
  });
  
  
  
  // log.php
  if(jQuery().reverseOrder) {
	  $('ol.more li').reverseOrder(); 
	} 
  $("ol.more").each(function() {
    $("li:gt(4)", this).hide(); /* :gt() is zero-indexed */
    $("li:nth-child(5)", this).after("<li class='more'><a href='#'>More...</a></li>"); /* :nth-child() is one-indexed */
  });
  $("li.more a").live("click", function($e) {
    $e.preventDefault();
    var li = $(this).parents("li:first");
    li.parent().children().show();
    li.remove();
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


	//title filtering on pages.php
	$('#filtertable').live("click", function($e) {
		$e.preventDefault();
		$("#filter-search").slideToggle();
		$(this).toggleClass('current');
		$('#filter-search #q').focus();
	});
	$("#filter-search #q").keydown(function($e){
		if($e.keyCode == 13) {
			$e.preventDefault();
		}
	});
	$("#editpages tr:has(td.pagetitle)").each(function(){
   var t = $(this).find('td.pagetitle').text().toLowerCase();
   $("<td class='indexColumn'></td>").hide().text(t).appendTo(this);
 	});
 	
	$("#filter-search #q").keyup(function(){
		var s = $(this).val().toLowerCase().split(" ");
		$("#editpages tr:hidden").show();
		$.each(s, function(){
    	$("#editpages tr:visible .indexColumn:not(:contains('" + this + "'))").parent().hide();
 		});
	});
	$("#filter-search .cancel").live("click", function($e) {
		$e.preventDefault();
		$("#editpages tr").show();
		$('#filtertable').toggleClass('current');
		$("#filter-search #q").val('');
		$("#filter-search").slideUp();
	});
	
	
	//create new folder in upload.php
	$('#createfolder').live("click", function($e) {
		$e.preventDefault();
		$("#new-folder form").show();
		$(this).hide();
		$('#new-folder #foldername').focus();
	});
	$("#new-folder .cancel").live("click", function($e) {
		$e.preventDefault();
		$("#new-folder #foldername").val('');
		$("#new-folder form").hide();
		$('#createfolder').show();
	});
	
//end of javascript for getsimple
}); 

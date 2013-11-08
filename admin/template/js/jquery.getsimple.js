/*
 * GetSimple js file    
 */

/* jQuery reverseOrder
 * Written by Corey H Maass for Arc90; (c) Arc90, Inc.
 */
(function($){$.fn.reverseOrder=function(){return this.each(function(){$(this).prependTo($(this).parent())})}})(jQuery);
/*
 * jQuery Capslock 0.4
 * Copyright (c) Arthur McLean
 */
(function($){$.fn.capslock=function(options){if(options)$.extend($.fn.capslock.defaults,options);this.each(function(){$(this).bind("caps_lock_on",$.fn.capslock.defaults.caps_lock_on);$(this).bind("caps_lock_off",$.fn.capslock.defaults.caps_lock_off);$(this).bind("caps_lock_undetermined",$.fn.capslock.defaults.caps_lock_undetermined);$(this).keypress(function(e){check_caps_lock(e)})});return this};function check_caps_lock(e){var ascii_code=e.which;var letter=String.fromCharCode(ascii_code);var upper=letter.toUpperCase();var lower=letter.toLowerCase();var shift_key=e.shiftKey;if(upper!==lower){if(letter===upper&&!shift_key){$(e.target).trigger("caps_lock_on")}else if(letter===lower&&!shift_key){$(e.target).trigger("caps_lock_off")}else if(letter===lower&&shift_key){$(e.target).trigger("caps_lock_on")}else if(letter===upper&&shift_key){if(navigator.platform.toLowerCase().indexOf("win")!==-1){$(e.target).trigger("caps_lock_off")}else{if(navigator.platform.toLowerCase().indexOf("mac")!==-1&&$.fn.capslock.defaults.mac_shift_hack){$(e.target).trigger("caps_lock_off")}else{$(e.target).trigger("caps_lock_undetermined")}}}else{$(e.target).trigger("caps_lock_undetermined")}}else{$(e.target).trigger("caps_lock_undetermined")}if($.fn.capslock.defaults.debug){if(console){console.log("Ascii code: "+ascii_code);console.log("Letter: "+letter);console.log("Upper Case: "+upper);console.log("Shift key: "+shift_key)}}}$.fn.capslock.defaults={caps_lock_on:function(){},caps_lock_off:function(){},caps_lock_undetermined:function(){},mac_shift_hack:true,debug:false}})(jQuery);


/* jcrop display */ 
function updateCoords(c) {
	var x = Math.floor(c.x);
	var y = Math.floor(c.y);
	var w = Math.floor(c.w);
	var h = Math.floor(c.h);
	$('#handw').show();
	$('#x').val(x);
	$('#y').val(y);
	$('#w').val(w);
	$('#h').val(h);
	$('#pich').html(h);
	$('#picw').html(w);
};

var Debugger = function () {}
Debugger.log = function (message) {
	try {
		console.log(message);
	} catch (exception) {
		return;
	}
}
 
/*
 * popit
 * element attention blink
 * ensures occurs only once
 * @param int $speed animation speed in ms
 */
$.fn.popit = function ($speed) {
	$speed = $speed || 500;
	$(this).each(function () {
		if ($(this).data('popped') != true) {
			$(this).fadeOut($speed).fadeIn($speed);
			$(this).data('popped', true);
		}
	});
	return $(this);
}
 
/*
 * closeit
 * fadeout close on delay
 * @param int $delay delay in ms
 */
$.fn.removeit = function ($delay) {
	$delay = $delay || 5000;
	$(this).each(function () {
		$(this).delay($delay).fadeOut(500);
	});
	return $(this);
}
 

/* notification functions */ 
function notifyOk($msg) {
	return notify($msg, 'ok');
}
 
function notifyWarn($msg) {
	return notify($msg, 'warning');
}
 
function notifyInfo($msg) {
	return notify($msg, 'info');
}
 
function notifyError($msg) {
	return notify($msg, 'error');
}
 
function notify($msg, $type) {
	if ($type == 'ok' || $type == 'warning' || $type == 'info' || $type == 'error') {
		var $notify = $('<div class="notify notify_' + $type + '"><p>' + $msg + '</p></div>');
		$('div.bodycontent').before($notify);
		return $notify;
	}
}
 
function clearNotify() {
	$('div.wrapper .notify').remove();
}
 
basename = function(str){
	return str.substring(0,str.lastIndexOf('/') ); 		
} 
 
jQuery(document).ready(function () {
	
	$("#tabs").tabs({
		activate: function(event, ui) {
			// set bookmarkable urls
			var hash = ui.newTab.context.hash;
			hash = hash.replace('#','');
			window.location.hash = "tab_"+hash;
		},
		create: function (event,ui) {
			// set active tab from hash
			if(window.location.hash){
				var selectedTabHash = window.location.hash.replace(/tab_/,"");
				var tabIndex = $( "#tabs li a" ).index($("#tabs li a[href='"+selectedTabHash+"']"));
				if(tabIndex > 0) $( "#tabs" ).tabs("option", "active", tabIndex );
			}	
		}
	});

	var loadingAjaxIndicator = $('#loader');
 
	function checkCoords() {
		if (parseInt($('#x').val())) return true;
		alert('Please select a crop region then press submit.');
		return false;
	};
 
	/* Listener for filter dropdown */
	function attachFilterChangeEvent() {
		$(document).on('change', "#imageFilter", function () {
			Debugger.log('attachFilterChangeEvent');
			loadingAjaxIndicator.show();
			var filterx = $(this).val();
			$("#imageTable").find("tr").hide();
			if (filterx == 'Images') {
				$("#imageTable").find("tr .imgthumb").show();
			} else {
				$("#imageTable").find("tr .imgthumb").hide();
			}
			$("#filetypetoggle").html('&nbsp;&nbsp;/&nbsp;&nbsp;' + filterx);
			$("#imageTable").find("tr." + filterx).show();
			$("#imageTable").find("tr.folder").show();
			$("#imageTable").find("tr:first-child").show();
			$("#imageTable").find("tr.deletedrow").hide();
			loadingAjaxIndicator.fadeOut(500);
		});
	}
 
	//upload.php
	attachFilterChangeEvent();
 
	//image.php 
	var copyKitTextArea = $('textarea.copykit');
	$("select#img-info").change(function () {
		var codetype = $(this).val();
		var code = $('p#' + codetype).html();
		var originalBG = $('textarea.copykit').css('background-color');
		var fadeColor = "#FFFFD1";
		copyKitTextArea.fadeOut(500).fadeIn(500).html(code);
	});
	$(".select-all").on("click", function () {
		copyKitTextArea.focus().select();
		return false;
	});
 
 
	//autofocus index.php & resetpassword.php fields on pageload
	$("#index input#userid").focus();
	$("#resetpassword input[name='username']").focus();
	var options = {
		caps_lock_on: function () {
			$(this).addClass('capslock');
		},
		caps_lock_off: function () {
			$(this).removeClass('capslock');
		},
		caps_lock_undetermined: function () {
			$(this).removeClass('capslock');
		}
	};
 
	$("input[type='password']").capslock(options);
 
 
	// components.php
	$(".delconfirmcomp").on("click", function ($e) {
		$e.preventDefault();
		loadingAjaxIndicator.show();
		var message = $(this).attr("title");
		var answer = confirm(message);
		if (answer) {
			var compid = $(this).attr("rel");
			$(compid).slideToggle(500).remove();
		}
		loadingAjaxIndicator.fadeOut(500);
	});

	$("#addcomponent").on("click", function ($e) {
		$e.preventDefault();
		loadingAjaxIndicator.show();
		var id = $("#id").val();
		$("#divTxt").prepend('<div style="display:none;" class="compdiv codewrap" id="section-' + id + '"><table class="comptable"><tr><td><b>Title: </b><input type="text" class="text newtitle" name="title[]" value="" /></td><td class="delete"><a href="#" title="Delete Component:?" class="delcomponent" id="del-' + id + '" rel="' + id + '" >&times;</a></td></tr></table><textarea name="val[]" class="code_edit"></textarea><input type="hidden" name="slug[]" value="" /><input type="hidden" name="id[]" value="' + id + '" /><div>');
		$("#section-" + id).slideToggle('fast');
		id = (id - 1) + 2;
		$("#id").val(id); // bump count
		loadingAjaxIndicator.fadeOut(500);
		$('#submit_line').fadeIn(); // fadein in case no components exist
		
		// add codemirror to new textarea
		var editor = jQuery().editorFromTextarea($("#divTxt").find('textarea').first().get(0));
		// retain autosizing but make sure the editor start larger than 1 line high
		$(editor.getWrapperElement()).find('.CodeMirror-scroll').css('min-height',100);
		editor.refresh();

		$("#divTxt").find('input').get(0).focus();
	});

	$("#maincontent").on("click",'.delcomponent', function ($e) {
		$e.preventDefault();
		var message = $(this).attr("title");
		var compid = $(this).attr("rel");
		var answer = confirm(message);
		if (answer) {
			loadingAjaxIndicator.show();
			var myparent = $(this).parents('.compdiv');
			myparent.slideUp('fast', function () {
				if ($("#divlist-" + compid).length) {
					$("#divlist-" + compid).remove();
				}
				myparent.remove();
			});
			loadingAjaxIndicator.fadeOut(1000);
		}
	});

	$("b.editable").dblclick(function () {
		var t = $(this).html();
		$(this).parents('.compdiv').find("input.comptitle").hide();
		$(this).after('<div id="changetitle"><b>Title: </b><input class="text newtitle titlesaver" name="title[]" value="' + t + '" /></div>');
		$(this).next('#changetitle').children('input').focus();
		$(this).parents('.compdiv').find("input.compslug").val('');
		$(this).hide();
	});

	$("#maincontent").on("keyup","input.titlesaver", function () {
		var myval = $(this).val();
		$(this).parents('.compdiv').find(".compslugcode").html("'" + myval.toLowerCase() + "'");
		$(this).parents('.compdiv').find("b.editable").html(myval);
	}).on("focusout", "input.titlesaver", function () {
		var myval = $(this).val();
		$(this).parents('.compdiv').find(".compslugcode").html("'" + myval.toLowerCase() + "'");
		$(this).parents('.compdiv').find("b.editable").html(myval);
		$(this).parents('.compdiv').find("input.comptitle").val(myval);
		$("b.editable").show();
		$('#changetitle').remove();
	});
 
 
	// other general functions
	$(".snav a.current").on("click", function ($e) {
		$e.preventDefault();
	});

	$(".confirmation").on("click", function ($e) {
		loadingAjaxIndicator.show();
		var message = $(this).attr("title");
		var answer = confirm(message);
		if (!answer) {
			loadingAjaxIndicator.fadeOut(500);
			return false;
		}
		loadingAjaxIndicator.fadeOut(500);
	});

	$(".delconfirm").on("click", function () {
		var message = $(this).attr("title");
		var dlink = $(this).attr("href");
		var mytr = $(this).parents("tr");
		mytr.css("font-style", "italic");
		var answer = confirm(message);
		if (answer) {
			if (!$(this).hasClass('noajax')) {
				loadingAjaxIndicator.show();
				mytr.addClass('deletedrow');
				mytr.fadeOut(500, function () {
					$.ajax({
						type: "GET",
						url: dlink,
						success: function (response) {
							mytr.remove();
							if ($("#pg_counter").length) {
								counter = $("#pg_counter").html();
								$("#pg_counter").html(counter - 1);
							}
 
							$('div.wrapper .updated').remove();
							$('div.wrapper .error').remove();
							if ($(response).find('div.error').html()) {
								$('div.bodycontent').before('<div class="error"><p>' + $(response).find('div.error').html() + '</p></div>');
								popAlertMsg();
							}
							if ($(response).find('div.updated').html()) {
								$('div.bodycontent').before('<div class="updated"><p>' + $(response).find('div.updated').html() + '</p></div>');
								popAlertMsg();
							}
						}
					});
					loadingAjaxIndicator.fadeOut(500);
				});
				return false;
			}
		} else {
			mytr.css('font-style', 'normal');
			return false;
		}
	});

	$("#waittrigger").click(function () {
		loadingAjaxIndicator.fadeIn();
		$("#waiting").fadeIn(1000).fadeOut(1000).fadeIn(1000).fadeOut(1000).fadeIn(1000).fadeOut(1000).fadeIn(1000);
	});
 
 
	/* Notifications */
 
	/*
	notifyError('This is an ERROR notification');
	notifyOk('This is an OK notification');
	notifyWarn('This is an WARNING notification');
	notifyInfo('This is an INFO notification');
	notify('message','msgtype');
	notifyError('This notification blinks and autocloses').popit(ms speed).closeit(ms delay);   
	*/
 
	function popAlertMsg() {
		/* legacy, see jquery extend popit() and closeit() */
		$(".updated").fadeOut(500).fadeIn(500);
		$(".error").fadeOut(500).fadeIn(500);
 
		$(".notify").popit(); // allows legacy use
	}
 
	popAlertMsg();
 
	if (jQuery().fancybox) {
		$('a[rel*=facybox]').fancybox({
			type: 'ajax',
			padding: 0,
			scrolling: 'auto'
		});
		$('a[rel*=facybox_i]').fancybox();
		$('a[rel*=facybox_s]').fancybox({
			type: 'ajax',
			padding: 0,
			scrolling: 'no'
		}).on('click',function(e){e.preventDefault();});
	}
 
	//plugins.php
	$("#maincontent").on("click", ".toggleEnable", function ($e) {
		$e.preventDefault();
 
		var loadingAjaxIndicator = $('#loader');
		document.body.style.cursor = "wait";
		loadingAjaxIndicator.show();
 
		var message = $(this).attr("title");
		var dlink = $(this).attr("href");
		var mytd = $(this).parents("td");
		var mytr = $(this).parents("tr");
 
		mytd.html('');
		mytd.addClass('ajaxwait ajaxwait_dark ajaxwait_tint_dark');
		$('.toggleEnable').addClass('disabled');
 
		$.ajax({
			type: "GET",
			dataType: "html",
			url: dlink,
			success: function (data, textStatus, jqXHR) {
				// Store the response as specified by the jqXHR object
				responseText = jqXHR.responseText;
 
				// remove scripts to prevent assets from loading when we create temp dom
				rscript = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;
 
				// create temp doms to reliably find elements
				$('#header').html($("<div>").append(responseText.replace(rscript, "")).find('#header > *'));
				$('#sidebar').html($("<div>").append(responseText.replace(rscript, "")).find('#sidebar > *'));
				$('#maincontent').html($("<div>").append(responseText.replace(rscript, "")).find('#maincontent > *'));
 
				document.body.style.cursor = "default";
				clearNotify();
				notifyOk('Plugin Updated').popit().removeit();
			},
			error: function (data, textStatus, jqXHR) {
				// These go in failures if we catch them in the future
				document.body.style.cursor = "default";
				mytd.removeClass('ajaxwait ajaxwait_dark ajaxwait_tint_dark');
				$('.toggleEnable').removeClass('disabled');
				loadingAjaxIndicator.fadeOut();
 
				clearNotify();
				notifyError('An error has occured');
			}
 
		});
	});
 
	function getElemLabel(element){
	   var label = $("label[for='"+element.attr('id')+"']")
	   if (label.length == 0) {
	     label = element.closest('label')
	   }
	   return label;
	}

	// edit.php
	function updateMetaDescriptionCounter(ev) {
		var element = $(ev.currentTarget);
		var label = getElemLabel(element);
		var countdown = label.find('.countdown');
		var charlimit = element.attr('data-maxLength');

		if(label && charlimit && countdown){
			var remaining = charlimit - element.val().length;
			countdown.text(remaining);
			if(remaining < 0) countdown.addClass('maxchars');
			else countdown.removeClass('maxchars');
		}
	}

	if ($('.charlimit').length) {
		$('.charlimit').change(updateMetaDescriptionCounter);
		$('.charlimit').keyup(updateMetaDescriptionCounter);
		$('.charlimit').trigger('change');
	}

	if ($("#edit input#post-title:empty").val() == '') {
		$("#edit input#post-title").focus();
	}

	$("#metadata_toggle").on("click", function ($e) {
		$e.preventDefault();
		$("#metadata_window").slideToggle('fast');
		$(this).toggleClass('current');
	});
 
	var privateLabel = $("#post-private-wrap label");
	$("#post-private").change(function () {
		if ($(this).val() == "Y") {
			privateLabel.css("color", '#cc0000');
		} else {
			privateLabel.css("color", '#333333');
		}
	});
	if ($("#post-private").val() == "Y") {
		privateLabel.css("color", '#cc0000');
	} else {
		privateLabel.css("color", '#333333');
	}
	$("#post-menu-enable").on("click", function () {
		$("#menu-items").slideToggle("fast");
	});
	if ($("#post-menu-enable").is(":checked")) {} else {
		$("#menu-items").css("display", "none");
	}
 
 	// adds sidebar submit buttons and fire clicks
	var edit_line = $('#submit_line span').html();
	$('#js_submit_line').html(edit_line);
	$("#js_submit_line input.submit").on("click", function () {
		$("#submit_line input.submit").trigger('click');
	});

	$("#save-close a").on("click", function ($e) {
		$e.preventDefault();
		$('input[name=redirectto]').val('pages.php');
		$("#submit_line input.submit").trigger('click');
	});
 
 
	// pages.php
	$("#show-characters").on("click", function () {
		$(".showstatus").toggle();
		$(this).toggleClass('current');
	});
 
 
	// log.php
	if (jQuery().reverseOrder) {
		$('ol.more li').reverseOrder();
	}
	$("ol.more").each(function () {
		$("li:gt(4)", this).hide(); /* :gt() is zero-indexed */
		$("li:nth-child(5)", this).after("<li class='more'><a href='#'>More...</a></li>"); /* :nth-child() is one-indexed */
	});
	$("li.more a").on("click", function ($e) {
		$e.preventDefault();
		var li = $(this).parents("li:first");
		li.parent().children().show();
		li.remove();
	});
 
 	// theme.php
	$("#theme_select").on('change',function (e) {
		var theme_new = $(this).val();
		var theme_url_old = $("#theme_preview").attr('src');
		// we dont have a global paths in js so work theme path out
		var theme_path = basename(basename(basename(theme_url_old)));	
		var theme_url_new = theme_path+'/'+theme_new+'/images/screenshot.png';
		$("#theme_preview").attr('src',theme_url_new);
		$("#theme_preview").css('visibility','visible');
		$('#theme_no_img').css('visibility','hidden');		
	});
 
	$("#theme_preview").on('error',function ($e) {
		$(this).css('visibility','hidden');
		$('#theme_no_img').css('visibility','visible');
	});

	///////////////////////////////////////////////////////////////////////////
	// theme-edit.php
	///////////////////////////////////////////////////////////////////////////
	$("#theme-folder").on('change',function (e) {
		var thmfld = $(this).val();
		if (checkChanged()) return; // todo: change selection back
		$('#theme_filemanager').html('Loading...');
		updateTheme(thmfld);
	});

	// editor theme selector
	$('#cm_themeselect').on('change',function(e){
		var theme = $(this).find(":selected").text();
		sendDataToServer('theme-edit.php','themesave='+theme);
		cm_theme_update(theme);		
	});

	function sendDataToServer(url,datastring){
		$.ajax({
			type: "POST",
			dataType: "html",
			url: url,
			data: datastring,
			success: function (data) {
				
			}	
		});
	}	

	// delegated on() handlers survive ajax replacement
	$(document).on('click',"#theme_filemanager a.file",function(e){
		// console.log('filechange');
		e.preventDefault();
		var thmfld = $("#theme-folder").val();
		// console.log($(this).attr('href'));
		if (checkChanged()) return;
		clearFileOpen();
		$(this).addClass('ext-wait'); // loading icon
		$(this).addClass('open'); // loading icon
		updateTheme('','_noload',$(this).attr('href')+'&ajax=1'); // ajax request
	});

	function checkChanged(){
		if($('#codetext').data('editor').hasChange == true){
			alert('This file has unsaved content, save or cancel before continuing');
			return true;
		}
	}

	function updateTheme(theme,file,url){

		// console.log(theme);
		var theme = theme == undefined ? '' : theme;
		var file  = file  == undefined ? '' : file;
		var url   = url   == undefined ? "theme-edit.php?t="+theme+'&f='+file : url;
		
		loadingAjaxIndicator.show('fast');
		$('#codetext').data('editor').setValue('');
		$('#codetext').data('editor').hasChange == false;
		$('#theme_edit_code').fadeTo('fast',0.3);

		$.ajax({
			type: "GET",
			cache: false,
			url: url,
			paramfile: file, // not sure if its ok to stuff local things here, but it takes it
			success: function( data ) {
				rscript      = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;						
				responseText = data.replace(rscript, "");
				response     = $($.parseHTML(data));

				/* dir tree */
				
				// using this var to prevent reloads on the filetree for now, 
				// can go away when we are sending proper ajax responses and not full html pages.
				if(this.paramfile!='_noload'){
					$('#theme_filemanager').html(response.find('#theme_filemanager > *') ); 
				}
				
				/* content */
				var newcontent = response.find('#codetext');
				$('#codetext').val(newcontent.val());
				$('#codetext').data('editor').setValue(newcontent.val());
				$('#codetext').data('editor').hasChange = false;

				/* form */
				var filename = response.find('#edited_file').val() ;
				$('#edited_file').val(filename);

				/* hook wrapper */
				$('#theme-edit-extras-wrap').html(response.find('#theme-edit-extras-wrap > *'));

				/* title */
				$('#theme_editing_file').html(filename);

				/* update editor mode */
				$('#codetext').data('editor').setOption('mode',getEditorMode(getExtension(filename)));
				$('#codetext').data('editor').refresh();
				
				clearFileWaits();
				loadingAjaxIndicator.fadeOut();
				$('#theme_edit_code').fadeTo('fast',1);

			}
		});

	}
 	
 	// removes loading icons
	function clearFileWaits(){
		$('#theme_filemanager a.ext-wait').removeClass('ext-wait'); 
	}

	// removes active file backgrounds
	function clearFileOpen(){
		$('#theme_filemanager a.open').removeClass('open'); 
	}

	// ajaxify theme submit
	$('#themeEditForm').on('submit',function(e){
		e.preventDefault();
		themeFileSave($('#codetext').data('editor'));
	});

	$('#themeEditForm .cancel').on('click',function(e){
		e.preventDefault();
		editor = $('#codetext').data('editor');
		if(editor){
			editor.hasChange = false;
			editor.setValue($(editor.getTextArea()).val());
		}	
		notifyWarn('Updates cancelled').removeit();
	});

	// ajax save theme file
	themeFileSave = function(cm){
		loadingAjaxIndicator.show('fast');

		cm.save(); // copy cm back to textarea

		var dataString = $("#themeEditForm").serialize();

		$.ajax({
			type: "POST",
			cache: false,
			url: 'theme-edit.php',
			data: dataString+'&submitsave=1&ajaxsave=1',
			success: function( response ) {
				$('div.wrapper .updated').remove();
				$('div.wrapper .error').remove();
				if ($(response).find('div.error').html()) {
					notifyError($(response).find('div.error').html()).popit().removeit();
				}
				else if ($(response).find('div.updated').html()) {
					notifyOk($(response).find('div.updated').html()).popit().removeit();
				}	
				else {
					notifyError("<p>ERROR</p>").popit().removeit();					
				}

				loadingAjaxIndicator.fadeOut();
				$('#codetext').data('editor').hasChange = false; // mark clean		
			}
		});
	}


	$('#compEditForm').submit(function(e) {
		console.log("onsubmit");
		e.preventDefault();

		loadingAjaxIndicator.show('fast');
		// $('#codetext').data('editor').setValue('');
		// $('#codetext').data('editor').hasChange == false;
		// $('#theme_edit_code').fadeTo('fast',0.3);
		
		cm_save_editors();
	    var url = "path/to/your/script.php"; // the script where you handle the form input.
		var dataString = $("#compEditForm").serialize();			

	    $.ajax({
	    	type: "POST",
			cache: false,
			url: 'components.php',
			data: dataString+'&submitted=1&ajaxsave=1',
			success: function( response ) {
				$('div.wrapper .updated').remove();
				$('div.wrapper .error').remove();
				if ($(response).find('div.error').html()) {
					notifyError($(response).find('div.error').html()).popit().removeit();
				}
				else if ($(response).find('div.updated').html()) {
					notifyOk($(response).find('div.updated').html()).popit().removeit();
				}	
				else {
					notifyError("<p>ERROR</p>").popit().removeit();					
				}

				loadingAjaxIndicator.fadeOut();
				// $('#codetext').data('editor').hasChange = false; // mark clean		
			}
	    });
	});

	componentSave = function(cm){
		
		loadingAjaxIndicator.show('fast');

		cm.save(); // copy cm back to textarea

		var dataString = $("#themeEditForm").serialize();

		$.ajax({
			type: "POST",
			cache: false,
			url: 'theme-edit.php',
			data: dataString+'&submitsave=1&ajaxsave=1',
			success: function( response ) {
				$('div.wrapper .updated').remove();
				$('div.wrapper .error').remove();
				if ($(response).find('div.error').html()) {
					notifyError($(response).find('div.error').html()).popit().removeit();
				}
				else if ($(response).find('div.updated').html()) {
					notifyOk($(response).find('div.updated').html()).popit().removeit();
				}	
				else {
					notifyError("<p>ERROR</p>").popit().removeit();					
				}

				loadingAjaxIndicator.fadeOut();
				$('#codetext').data('editor').hasChange = false; // mark clean		
			}
		});
	}

	function getExtension(file){
		var extension = file.substr( (file.lastIndexOf('.') +1) );
		return extension;
	}

	function getEditorMode(extension){
		var modes = {
			'php'  : 'application/x-httpd-php',
			'html' : 'text/html',
			'js'   : 'text/javascript',
			'css'  : 'text/css'
		};
		return extension in modes ? modes[extension] : modes['php'];
	}


	// tree folding
	$(document).on('click',"#theme_filemanager a.directory",function(e){
		$(this).toggleClass('dir-open');
		$(this).next("ul").slideToggle('fast');
	});


	// update editor theme and lazy load css file async and update theme on callback
	cm_theme_update = function(theme){
		// Debugger.log('updating codemirror theme: ' + theme);
		var parts = theme.split(' ');
		callback = function () {
			  cm_theme_update_editors(theme);
			  editorConfig.theme = theme;
			}
		if(theme == "default") cm_theme_update_editors(theme);
		else loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css", callback );
	}

	// set all editors themes
	cm_theme_update_editors = function(theme){
		// Debugger.log(theme);
		$('.code_edit').each(function(i, textarea){
			var editor = $(textarea).data('editor');
			// Debugger.log(editor);
			if(editor) {
				editor.setOption('theme',theme);	
				editor.refresh();
			}	
		});		
	}

	// save all editors
	cm_save_editors = function(theme){
		// Debugger.log(theme);
		$('.code_edit').each(function(i, textarea){
			var editor = $(textarea).data('editor');
			// Debugger.log(editor);
			if(editor) {
				editor.save();
			}	
		});		
	}

	///////////////////////////////////////////////////////////////////////////
	// title filtering on pages.php & backups.php
	///////////////////////////////////////////////////////////////////////////

	var filterSearchInput = $("#filter-search");
	$('#filtertable').on("click", function ($e) {
		$e.preventDefault();
		filterSearchInput.slideToggle();
		$(this).toggleClass('current');
		filterSearchInput.find('#q').focus();
	});
	$("#filter-search #q").keydown(function ($e) {
		if ($e.keyCode == 13) {
			$e.preventDefault();
		}
	});
	$("#editpages tr:has(td.pagetitle)").each(function () {
		var t = $(this).find('td.pagetitle').text().toLowerCase();
		$("<td class='indexColumn'></td>").hide().text(t).appendTo(this);
	});
	$("#filter-search #q").keyup(function () {
		var s = $(this).val().toLowerCase().split(" ");
		$("#editpages tr:hidden").show();
		$.each(s, function () {
			$("#editpages tr:visible .indexColumn:not(:contains('" + this + "'))").parent().hide();
		});
	});
	$("#filter-search .cancel").on("click", function ($e) {
		$e.preventDefault();
		$("#editpages tr").show();
		$('#filtertable').toggleClass('current');
		filterSearchInput.find('#q').val('');
		filterSearchInput.slideUp();
	});
 

	///////////////////////////////////////////////////////////////////////////
	// Upload.php
	///////////////////////////////////////////////////////////////////////////

	//create new folder in upload.php
	$('#createfolder').on("click", function ($e) {
		$e.preventDefault();
		$("#new-folder").find("form").show();
		$(this).hide();
		$("#new-folder").find('#foldername').focus();
	});
	$("#new-folder .cancel").on("click", function ($e) {
		$e.preventDefault();
		$("#new-folder").find("#foldername").val('');
		$("#new-folder").find("form").hide();
		$('#createfolder').show();
	});
 
	// upload.php ajax folder creation
	$('#new-folder form').submit(function () {
		loadingAjaxIndicator.show();
		var dataString = $(this).serialize();
		var newfolder = $('#foldername').val();
		var hrefaction = $(this).attr('action');
		$.ajax({
			type: "GET",
			data: dataString,
			url: hrefaction,
			success: function (response) {
				$('#imageTable').load(location.href + ' #imageTable >*', function () {
					attachFilterChangeEvent();
					$("#new-folder").find("#foldername").val('');
					$("#new-folder").find("form").hide();
					$('#createfolder').show();
					counter = parseInt($("#pg_counter").text());
					$("#pg_counter").html(counter++);
					$("tr." + newfolder + " td").css("background-color", "#F9F8B6");
					loadingAjaxIndicator.fadeOut();
				});
			}
		});
		return false;
	});
 
 	// catch all redirects for session timeout on HTTP 401 unauthorized
	$( document ).ajaxError(function( event, xhr, settings ) {
		// notifyInfo("ajaxComplete: " + xhr.status);
		if(xhr.status == 401){
			notifyInfo("Redirecting...");
			window.location.reload();
		}
	});
	
	// end of javascript for getsimple

});
 
// lazy loader for js and css
loadjscssfile = function(filename, filetype, callback){
	if (filetype=="js"){ //if filename is a external JavaScript file
		LazyLoad.js(filename,callback);
	}
	else if (filetype=="css"){ //if filename is an external CSS file
		LazyLoad.css(filename,callback);
	}
}
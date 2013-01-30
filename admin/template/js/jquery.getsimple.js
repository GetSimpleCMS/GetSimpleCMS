/* jQuery reverseOrder
 * Written by Corey H Maass for Arc90; (c) Arc90, Inc.
 */
(function($){$.fn.reverseOrder=function(){return this.each(function(){$(this).prependTo($(this).parent())})}})(jQuery);
/*
 * jQuery Capslock 0.4
 * Copyright (c) Arthur McLean
 */
(function($){$.fn.capslock=function(options){if(options)$.extend($.fn.capslock.defaults,options);this.each(function(){$(this).bind("caps_lock_on",$.fn.capslock.defaults.caps_lock_on);$(this).bind("caps_lock_off",$.fn.capslock.defaults.caps_lock_off);$(this).bind("caps_lock_undetermined",$.fn.capslock.defaults.caps_lock_undetermined);$(this).keypress(function(e){check_caps_lock(e)})});return this};function check_caps_lock(e){var ascii_code=e.which;var letter=String.fromCharCode(ascii_code);var upper=letter.toUpperCase();var lower=letter.toLowerCase();var shift_key=e.shiftKey;if(upper!==lower){if(letter===upper&&!shift_key){$(e.target).trigger("caps_lock_on")}else if(letter===lower&&!shift_key){$(e.target).trigger("caps_lock_off")}else if(letter===lower&&shift_key){$(e.target).trigger("caps_lock_on")}else if(letter===upper&&shift_key){if(navigator.platform.toLowerCase().indexOf("win")!==-1){$(e.target).trigger("caps_lock_off")}else{if(navigator.platform.toLowerCase().indexOf("mac")!==-1&&$.fn.capslock.defaults.mac_shift_hack){$(e.target).trigger("caps_lock_off")}else{$(e.target).trigger("caps_lock_undetermined")}}}else{$(e.target).trigger("caps_lock_undetermined")}}else{$(e.target).trigger("caps_lock_undetermined")}if($.fn.capslock.defaults.debug){if(console){console.log("Ascii code: "+ascii_code);console.log("Letter: "+letter);console.log("Upper Case: "+upper);console.log("Shift key: "+shift_key)}}}$.fn.capslock.defaults={caps_lock_on:function(){},caps_lock_off:function(){},caps_lock_undetermined:function(){},mac_shift_hack:true,debug:false}})(jQuery);

 
/*
 * GetSimple js file    
 */
function updateCoords(c) {
	$('#handw').show();
	$('#x').val(c.x);
	$('#y').val(c.y);
	$('#w').val(c.w);
	$('#h').val(c.h);
	$('#pich').html(c.h);
	$('#picw').html(c.w);
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
 */
$.fn.removeit = function ($delay) {
	$delay = $delay || 5000;
	$(this).each(function () {
		$(this).delay($delay).fadeOut(500);
	});
	return $(this);
}
 
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
	$(".select-all").live("click", function () {
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
	$(".delconfirmcomp").live("click", function ($e) {
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
	$("#addcomponent").live("click", function ($e) {
		$e.preventDefault();
		loadingAjaxIndicator.show();
		var id = $("#id").val();
		$("#divTxt").append('<div style="display:none;" class="compdiv" id="section-' + id + '"><table class="comptable"><tr><td><b>Title: </b><input type="text" class="text newtitle" name="title[]" value="" /></td><td class="delete"><a href="#" title="Delete Component:?" class="delcomponent" id="del-' + id + '" rel="' + id + '" >&times;</a></td></tr></table><textarea name="val[]"></textarea><input type="hidden" name="slug[]" value="" /><input type="hidden" name="id[]" value="' + id + '" /><div>');
		$("#section-" + id).slideToggle('fast');
		id = (id - 1) + 2;
		$("#id").val(id);
		loadingAjaxIndicator.fadeOut(500);
		$('#submit_line').fadeIn();
	});
	$('.delcomponent').live("click", function ($e) {
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
	$("input.titlesaver").live("keyup", function () {
		var myval = $(this).val();
		$(this).parents('.compdiv').find(".compslugcode").html("'" + myval.toLowerCase() + "'");
		$(this).parents('.compdiv').find("b.editable").html(myval);
	}).live("focusout", function () {
		var myval = $(this).val();
		$(this).parents('.compdiv').find(".compslugcode").html("'" + myval.toLowerCase() + "'");
		$(this).parents('.compdiv').find("b.editable").html(myval);
		$(this).parents('.compdiv').find("input.comptitle").val(myval);
		$("b.editable").show();
		$('#changetitle').remove();
	});
 
 
	// other general functions
	$(".snav a.current").live("click", function ($e) {
		$e.preventDefault();
	});
	$(".confirmation").live("click", function ($e) {
		loadingAjaxIndicator.show();
		var message = $(this).attr("title");
		var answer = confirm(message);
		if (!answer) {
			loadingAjaxIndicator.fadeOut(500);
			return false;
		}
		loadingAjaxIndicator.fadeOut(500);
	});
	$(".delconfirm").live("click", function () {
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
		});
	}
 
	//plugins.php
	$(".toggleEnable").live("click", function ($e) {
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
 
	// edit.php
	function updateMetaDescriptionCounter() {
		var remaining = 155 - jQuery('#post-metad').val().length;
		jQuery('#countdown').text(remaining);
		Debugger.log('Meta Description has ' + remaining + ' characters remaining');
	}
	if ($('#post-metad').length) {
		updateMetaDescriptionCounter();
		$('#post-metad').change(updateMetaDescriptionCounter);
		$('#post-metad').keyup(updateMetaDescriptionCounter);
	}
	if ($("#edit input#post-title:empty").val() == '') {
		$("#edit input#post-title").focus();
	}
	$("#metadata_toggle").live("click", function ($e) {
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
	$("#post-menu-enable").live("click", function () {
		$("#menu-items").slideToggle("fast");
	});
	if ($("#post-menu-enable").is(":checked")) {} else {
		$("#menu-items").css("display", "none");
	}
 
	var edit_line = $('#submit_line span').html();
	$('#js_submit_line').html(edit_line);
	$("#js_submit_line input.submit").live("click", function () {
		$("#submit_line input.submit").trigger('click');
	});
	$("#save-close a").live("click", function ($e) {
		$e.preventDefault();
		$('input[name=redirectto]').val('pages.php');
		$("#submit_line input.submit").trigger('click');
	});
 
 
	// pages.php
	$("#show-characters").live("click", function () {
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
	$("li.more a").live("click", function ($e) {
		$e.preventDefault();
		var li = $(this).parents("li:first");
		li.parent().children().show();
		li.remove();
	});
 
 
	///////////////////////////////////////////////////////////////////////////
	// theme-edit.php
	///////////////////////////////////////////////////////////////////////////

	$("#theme-folder").change(function(){
		var thmfld = $(this).val();
		if (checkChanged()) return; // todo: change selection back
		$('#theme_filemanager').html('Loading...');
		updateTheme(thmfld);
	});

 	// todo: maybe just use on submit event ?
	$('#themeEditForm .submit').on('click',function(e){
		e.preventDefault();
		themeFileSave(editor);
	});

	$('#themeEditForm .cancel').on('click',function(e){
		e.preventDefault();
		editor.hasChange = false;
		notifyWarn('Updates cancelled').removeit();
		//todo: reload file to discard changes
	});


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
		if(editor.hasChange == true){
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
		editor.setValue('');
		editor.hasChange == false;
		$('#theme_edit_code').fadeTo('fast',0.3);

		$.ajax({
			type: "GET",
			cache: false,
			url: url,
			paramfile: file, // not sure if its ok to stuff local things here, but it takes it
			success: function( data ) {
				
				rscript      = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;						
				responseText = data.replace(rscript, "");
				response     = $(data);

				/* dir tree */
				
				// using this var to prevent reloads on the filetree for now, 
				// can go away when we are sending proper ajax responses and not full html pages.
				if(this.paramfile!='_noload'){
					$('#theme_filemanager').html(response.find('#theme_filemanager > *') ); 
				}
				
				/* content */
				var newcontent = response.find('#codetext');
				$('#codetext').val(newcontent.val());
				editor.setValue(newcontent.val());
				editor.hasChange = false;

				/* form */
				var filename = response.find('#edited_file').val() ;
				$('#edited_file').val(filename);

				/* hook wrapper */
				$('#theme-edit-extras-wrap').html(response.find('#theme-edit-extras-wrap > *'));

				/* title */
				$('#theme_editing_file').html(filename);

				/* update editor mode */
				editor.setOption('mode',getEditorMode(getExtension(filename)));
				editor.refresh();
				
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
					notifyError($(response).find('div.error').html().popit().removeit());
				}
				if ($(response).find('div.updated').html()) {
					notifyOk($(response).find('div.updated').html()).popit().removeit();
				}	

				loadingAjaxIndicator.fadeOut();
				editor.hasChange = false; // mark clean		
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


	cm_theme_update = function(theme){
		// hack in new theme support until we update codemirror
		// init true if initializing codemirror

		var parts = theme.split(' ');
		//console.log(parts);

		// lazy load css file
		loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css")

		if(editor){
			var currTheme = editor.getOption('theme');
			$('.CodeMirror').removeClass().addClass('CodeMirror');
			$('.CodeMirror-gutter').removeClass().addClass('CodeMirror-gutter');
		}

		jQuery.each(parts,function(){
			//console.log(this);
			var theme = this;
			if(editor) editor.setOption('theme',theme);		
			$('.CodeMirror').addClass('cm-s-'+theme);
			$('.CodeMirror-gutter').addClass('cm-s-'+theme);
		});
	}

	///////////////////////////////////////////////////////////////////////////
	// title filtering on pages.php & backups.php
	///////////////////////////////////////////////////////////////////////////

	var filterSearchInput = $("#filter-search");
	$('#filtertable').live("click", function ($e) {
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
	$("#filter-search .cancel").live("click", function ($e) {
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
	$('#createfolder').live("click", function ($e) {
		$e.preventDefault();
		$("#new-folder").find("form").show();
		$(this).hide();
		$("#new-folder").find('#foldername').focus();
	});
	$("#new-folder .cancel").live("click", function ($e) {
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
 
 
loadjscssfile = function(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref)
}

 
	// end of javascript for getsimple
});
 
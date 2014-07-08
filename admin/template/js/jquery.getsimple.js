/**
 * GetSimple js file    
 * 
 */

/* jshint multistr: true */

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
}

Debugger = function () {};
Debugger.log = function (message) {
	try {
		console.log(message);
	} catch (exception) {
		return;
	}
};

// log = function(msg){ Debugger.log(msg) };
// Debugger.log('Debugger Init');

(function ( $ ) {
/*
 * popit
 * element attention blink
 * ensures occurs only once
 * @param int $speed animation speed in ms
 */
$.fn.popit = function ($speed) {
	$speed = $speed || 500;
	$(this).each(function () {
		if ($(this).data('popped') !== true) {
			$(this).fadeOut($speed).fadeIn($speed);
			$(this).data('popped', true);
		}
	});
	return $(this);
};

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
};

// overrides a method thats supposed to be called on a single node (a method like val)
$.fn.overrideNodeMethod = function(methodName, action) {
    var originalVal = $.fn[methodName];
    var thisNode = this;
    $.fn[methodName] = function() {
    	// if called on node, avoid recursion from callback
        if (this[0]==thisNode[0] && arguments.callee.caller !== action) {
            return action.apply(this, arguments);
        } else {
            return originalVal.apply(this, arguments);
        }
    };
};
 
/*
 * spinner
 *
 * adds ajax or wait spinner, configured via opts object or presets
 * inherits color from parent if not present
 * presets.gs is base opts, all others are extended and override
 * 
 * @since 3.4
 *
 */

$.fn.spin = function(opts, color, shim) {

	return this.each(function() {
		var $this = $(this),
		data = $this.data();

		if (data.spinner) {
			data.spinner.stop();
			delete data.spinner;
		}

		if(opts === undefined) opts = $.fn.spin.presets['gs'];

		if (opts !== false) {
			opts = $.extend(
				{ color: color || $this.css('color') },
				$.fn.spin.presets['gs'],
				$.fn.spin.presets[opts] || opts
			);
			
			if(opts.shim !== undefined && opts.shim === true){
				var shimElem = $('<div style="position:relative;display:inline-block;height:100%;width:100%"></div>');
				$this.append(shimElem);
				data.spinner = new Spinner(opts).spin($(shimElem)[0]);
			} else {
				data.spinner = new Spinner(opts).spin(this); return;
			}
		}

	});
};

$.fn.spin.presets = {
	tiny:  {  width: 2, radius: 2 },
	large: { width: 6, radius: 8 },
	xlarge: { width: 10, radius: 13 },
	gsdefault: { color : 'rgba(255, 255, 255, 0.8)' },
	gstable: { shim: true },
	gsfilemanager: { width:3, radius: 4,color : 'rgba(0, 0, 0, 0.6)',left: '13px' , top: '50%' },
	gs: {
		lines      : 9,          // The number of lines to draw
		length     : 0,          // The length of each line
		width      : 4,          // The line thickness
		radius     : 5,          // The radius of the inner circle
		corners    : 1,          // Corner roundness (0..1)
		rotate     : 0,         // The rotation offset
		direction  : 1,          // 1 clockwise, -1 counterclockwise
		// color     : '#FFF',  // #rgb or #rrggbb or array of colors
		speed      : 1.2,        // Rounds per second
		trail      : 45,         // Afterglow percentage
		opacity    : 0,          // Opacity of the lines
		shadow     : false,      // Whether to render a shadow
		hwaccel    : false,      // Whether to use hardware acceleration
		className  : 'spinner',  // The CSS class to assign to the spinner
		zIndex     : 2e9,        // The z-index (defaults to 2000000000)
		top        : '50%',      // Top position relative to parent
		left       : '50%'      // Left position relative to parent
	}
};

}( jQuery));

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
 
function basename(str){
	return str.substring(0,str.lastIndexOf('/') );
}
	
/**
 * generic i18n using array
 * @todo add sprintf
 */
function i18n(key){
	return GS.i18n[key] || key;
}

/**
 * get elements tagname
 */
function getTagName(elem){
	return $(elem).prop('tagName');
}

jQuery(document).ready(function () {

	$("#tabs").tabs({
		activate: function(event, ui) {
			// set bookmarkable urls
			var hash = ui.newTab.context.hash;
			hash = "tab_"+hash.replace('#','');
			window.location.replace(('' + window.location).split('#')[0] + '#' + hash);	// should not affect history
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

	var loadingAjaxIndicator;
	if($('#loader')[0]) initLoaderIndicator();

	function initLoaderIndicator(){
		// replace loader IMG with ajax loader
		$('#loader').remove();
		loadingAjaxIndicator = $($('#nav_loaderimg').spin('gsdefault').data('spinner').el).attr('id','loader').hide();
	}

	function checkCoords() {
		if (parseInt($('#x').val(),10)) return true;
		alert('Please select a crop region then press submit.');
		return false;
	}
   
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
	
	function focusCompEditor(selector){
		var editor = $(selector + ' textarea');		
		editor.focus();
	}

	// auto focus component editors
	$('#components div.compdivlist a').on('click', function(ev){
		focusCompEditor($(this).attr('href'));
		e.preventDefault();		
	});
	
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
		$("#divTxt").prepend('<div style="display:none;" class="compdiv codewrap" id="section-' + id + '"> \
			<table class="comptable"><tr><td><label>Title: </label><input type="text" class="text newtitle" name="title[]" value="" /></td> \
			<td class="delete"><a href="javascript:void(0)" title="Delete Component:?" class="delcomponent" id="del-' + id + '" rel="' + id + '" >&times;</a> \
			</td></tr></table> \
			<textarea name="val[]" class="code_edit" data-mode="php"></textarea><input type="hidden" name="slug[]" value="" /> \
			<input type="hidden" name="id[]" value="' + id + '" /><div>');
		$("#section-" + id).slideToggle('fast');
		id = (id - 1) + 2;
		$("#id").val(id); // bump count
		loadingAjaxIndicator.fadeOut(500);
		$('#submit_line').fadeIn(); // fadein in case no components exist
		
		// add codeditor to new textarea
		var textarea = $("#divTxt").find('textarea').first();
		Debugger.log($.isFunction($.fn.editorFromTextarea));
		if($.isFunction($.fn.editorFromTextarea)) textarea.editorFromTextarea();

		var editor = textarea.data('editor');
		// retain autosizing but make sure the editor start larger than 1 line high
		if(editor){
			$(editor.getWrapperElement()).find('.CodeMirror-scroll').css('min-height',100);
			editor.refresh();
		}	

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
		$(this).after('<div id="changetitle"><label>Title: </b><input class="text newtitle titlesaver" name="title[]" value="' + t + '" /></div>');
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

	$("#maincontent").on("click",".delconfirm", function () {
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
							response = $.parseHTML(response);
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
 
		// document.body.style.cursor = "wait";
		loadingAjaxIndicator.show();
 
		var message = $(this).attr("title");
		var dlink = $(this).attr("href");
		var mytd = $(this).parents("td");
		var mytr = $(this).parents("tr");

		mytd.html('').addClass('ajaxwait_tint_dark').spin('gstable');

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
 
				// document.body.style.cursor = "default";
				clearNotify();
				notifyOk(i18n('PLUGIN_UPDATED')).popit().removeit();
				initLoaderIndicator();
			},
			error: function (data, textStatus, jqXHR) {
				// These go in failures if we catch them in the future
				document.body.style.cursor = "default";
				mytd.removeClass('ajaxwait_tint_dark');
				$('.toggleEnable').removeClass('disabled');
				loadingAjaxIndicator.fadeOut();
				mytd.stop(); 
				clearNotify();
				notifyError(i18n('ERROR'));
			}
 
		});
	});
 
	function getElemLabel(element){
		var label = $("label[for='"+element.attr('id')+"']");
		if (label.length === 0) {
			label = element.closest('label');
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

	if ($("#edit input#post-title:empty").val() === '') {
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

	$(".save-close a").on("click", function ($e) {
		$e.preventDefault();
		$('input[name=redirectto]').val('pages.php');
		$("#submit_line input.submit").trigger('click');
	});
 
 
	// pages.php
	$("#show-characters").on("click", function () {
		if($(this).hasClass('current')) $(".showstatus").hide();
		else $(".showstatus").show() ;
		$(this).toggleClass('current');
	});
 
 
	// log.php
	if (jQuery().reverseOrder) {
		$('ol.more li').reverseOrder();
	}
	$("ol.more").each(function () {
		var show = 7; // how many to show
		$("li:gt("+(show-1)+")", this).hide(); /* :gt() is zero-indexed */
		if($("li:nth-child("+(show+1)+")", this)[0]) $("li:nth-child("+show+")", this).after("<li class='more'><a href='#'>More...</a></li>"); /* :nth-child() is one-indexed */
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
	
	// change gs theme dropdown
	$("#theme-folder").on('change',function (e) {
		var thmfld = $(this).val();
		if (checkChanged()) return; // todo: change selection back
		$('#theme_filemanager').html('Loading...');
		updateTheme(thmfld);
	});

	// codemirror editor theme select
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

	// theme-edit fileselector change
	// delegated on() handlers survive ajax replacement
	$(document).on('click',"#theme_filemanager a.file",function(e){
		// Debugger.log('filechange');
		e.preventDefault();
		var thmfld = $("#theme-folder").val();
		// Debugger.log($(this).attr('href'));
		if (checkChanged()) return;
		clearFileOpen();
		$(this).addClass('open').addClass('ext-wait');
		$(this).parent().spin('gsfilemanager'); // ajax spinner
		updateTheme('','_noload',$(this).attr('href')+'&ajax=1'); // ajax request
	});

	function checkChanged(){
		if($('#codetext').data('editor').hasChange === true){
			alert('This file has unsaved content, save or cancel before continuing');
			return true;
		}
	}

	// update theme-edit code editor
	function updateTheme(theme,file,url){

		// Debugger.log(theme);
		theme = theme === undefined ? '' : theme;
		file  = file  === undefined ? '' : file;
		url   = url   === undefined ? "theme-edit.php?t="+theme+'&f='+file : url;
		
		loadingAjaxIndicator.show();
		$('#codetext').data('editor').setValue('');
		$('#codetext').data('editor').hasChange = false;
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
		$('#theme_filemanager li .spinner').each(function(){$(this).closest('li').data('spinner').stop();}); 
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
			$('#theme_edit_code').fadeTo('fast',0.3).fadeTo('fast',1.0);
			editor.setValue($(editor.getTextArea()).val());
			editor.hasChange = false;
		}	
		notifyWarn('Updates cancelled').removeit();
	});

	// ajax save theme file
	themeFileSave = function(cm){
		loadingAjaxIndicator.show();

		if($(cm)[0] && $.isFunction(cm.save)){
			cm.save(); // copy cm back to textarea if editor has save method
		}	

		var dataString = $("#themeEditForm").serialize();

		$.ajax({
			type: "POST",
			cache: false,
			url: 'theme-edit.php',
			data: dataString+'&submitsave=1&ajaxsave=1',
			success: function( response ) {
				response = $.parseHTML(response); // jquery 1.9 html parsing fix
				$('div.wrapper .updated').remove();
				$('div.wrapper .error').remove();
				if ($(response).find('div.error').html()) {
					notifyError($(response).find('div.error').html()).popit().removeit();
				}
				if ($(response).find('div.updated').html()) {
					notifyOk($(response).find('div.updated').html()).popit().removeit();
				}	
				else {
					notifyError("<p>ERROR</p>").popit().removeit();					
				}

				loadingAjaxIndicator.fadeOut();
				$('#codetext').data('editor').hasChange = false; // mark clean		
			}
		});
	};

	// ajaxify components submit
	$('#compEditForm').on('submit',function(e){
		e.preventDefault();
		componentSave(e);
	});
	

	componentSave = function(e){

		Debugger.log("onsubmit");
		e.preventDefault();

		loadingAjaxIndicator.show();
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
				response = $.parseHTML(response);
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
	}

	function getExtension(file){
		var extension = file.substr( (file.lastIndexOf('.') +1) );
		return extension;
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
			};
		if(theme == "default") cm_theme_update_editors(theme);
		else loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css", callback );
	};

	// set all editors themes
	cm_theme_update_editors = function(theme){
		// Debugger.log(theme);
		$('.code_edit').each(function(i, textarea){
			var editor = $(textarea).data('editor');
			// Debugger.log(editor);
			// update all editor themes, unless they were modified manually
			if(editor && editor.getOption('theme') == editorTheme) {
				editor.setOption('theme',theme);	
				editor.refresh();
			}	
		});	
		editorConfig.theme = theme;		
		editorTheme = theme; // update global
	};

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
	};

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
	$("#maincontent").on("click",'#createfolder', function ($e) {
		$e.preventDefault();
		$("#new-folder").find("form").show();
		$(this).hide();
		$("#new-folder").find('#foldername').focus();
	});
	$("#maincontent").on("click","#new-folder .cancel", function ($e) {
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
					counter = parseInt($("#pg_counter").text(),10);
					$("#pg_counter").html(counter++);
					$("tr." + newfolder + " td").css("background-color", "#F9F8B6");
					loadingAjaxIndicator.fadeOut();
				});
			}
		});
		return false;
	});
 
	function scrollsidebar(){
		var elem = $('body.sbfixed #sidebar');

		if(!jQuery().scrollToFixed || !elem[0]){
			Debugger.log("sbfixed not enabled or scrolltofixed not loaded");
			return;
		}

		elem.scrollToFixed({ 
			marginTop: 15,
			limit: function(){ return $('#footer').offset().top - elem.outerHeight(true) - 15 ;} ,
			postUnfixed: function(){$(this).addClass('fixed') ;},
			postFixed: function(){$(this).removeClass('fixed') ;},
			postAbsolute: function(){$(this).removeClass('fixed') ;},

		});
	}

	// initialize fixed sidebar
	scrollsidebar();

	// CTRL+s save hotkey listener
	$(document).bind('keydown', function(e) {
		// Debugger.log('keydown: ' + e.which);
		var ctrlpress = navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey;
		// detect CTRL+S do save on all pages
		if(ctrlpress && (e.which == 83)) {
			Debugger.log('Ctrl+S pressed');
			dosave();
			e.preventDefault();
			return false;
		}
	});

	// catch all redirects for session timeout on HTTP 401 unauthorized
	$( document ).ajaxError(function( event, xhr, settings ) {
		// notifyInfo("ajaxComplete: " + xhr.status);
		if(xhr.status == 401){
			notifyInfo("Redirecting...");
			window.location.reload();
		}
	});
	
	// add tree folding to tree tables
	// addTableTree(minrows,mindepth,headerdepth)
	$('table.tree').addTableTree(1,1,1);

	// end of jQuery ready
});

function dosave(){
	Debugger.log('saving');
	// Debugger.log($("#submit_line input.submit"));
	$("#submit_line input.submit").trigger('click'); // should do form.submit handlers as well
}

// lazy loader for js and css
loadjscssfile = function(filename, filetype, callback){
	if (filetype=="js"){ //if filename is a external JavaScript file
		LazyLoad.js(filename,callback);
	}
	else if (filetype=="css"){ //if filename is an external CSS file
		LazyLoad.css(filename,callback);
	}
};

// prevent js access to cookies
if(!document.__defineGetter__) {
    Object.defineProperty(document, 'cookie', {
        get: function(){return '' ;},
        set: function(){return true ;},
    });
} else {
    document.__defineGetter__("cookie", function() { return '';} );
    document.__defineSetter__("cookie", function() {} );
}

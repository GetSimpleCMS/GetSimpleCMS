var editorConfig;
var editorUserConfig;
var editorTheme;
var editorMode;

var cm_modes = {
	'php'        : 'application/x-httpd-php',
	'htmlmixed'  : 'text/html',
	'html'       : 'text/html',
	'xml'        : 'application/xml',
	'javascript' : 'text/javascript',
	'js'         : 'text/javascript',
	'css'        : 'text/css',
	'markdown'   : 'text/x-markdown'
};

function getEditorMode(extension){
	return extension in cm_modes ? cm_modes[extension] : extension;
}

jQuery(document).ready(function () {
	initcodemirror();
});

	// setup codemirror instances and functions

	if(typeof editorTheme === 'undefined'){
		editorTheme = 'default';
	}

	editorMode = cm_modes['html'];

	// cmfold = function(cm){cm.foldCode(cm.getCursor(),{"widget":"...","minFoldSize":2});};

	editorConfig = {
		id                        : 'editorConfig',
		mode                      : editorMode,
		theme                     : editorTheme,
		lineNumbers               : true,
		indentWithTabs            : true,
		indentUnit                : 4,
		enterMode                 : "keep",
		tabMode                   : "shift",
		fixedGutter               : true,
		styleActiveLine           : true,
		matchBrackets             : true, // highlight matching brackets when cusrsor is next to one
		autoCloseBrackets         : true, // auto close brackets when typing
		autoCloseTags             : true, // auto close tags when typing
		// showTrailingSpace         : true, // adds the CSS class cm-trailingspace to stretches of whitespace at the end of lines.
		highlightSelectionMatches : true, // {showToken : /\w/}, for word boundaries
		// viewportMargin            : Infinity, // for autosizing, REMOVED for performance
		// lineWrapping              : true,
		// matchTags                 : true, // adds class CodeMirror-matchingtag to tags contents
		foldGutter                : true,
		gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
		saveFunction              : function(cm) { dosave(); },
		extraKeys: {
			// "Ctrl-Q" : function(cm) { foldFunc(cm, cm.getCursor().line); },
			// "Ctrl-Q" : function(cm) { cmfold(cm) },
			"F11"    : function(cm) { setFullScreen(cm, !isFullScreen(cm)); },
			"Esc"    : function(cm) { if (isFullScreen(cm)) setFullScreen(cm, false); },
			// "Ctrl-S" : function(cm) { customSave(cm); },
			"Ctrl-Space" : "autocomplete"
		}
	};

	// do not know what this does, looks like old ctrl+q fold debouncer
	// function keyEvent(cm, e) {
	//	if (e.keyCode == 81 && e.ctrlKey) {
	//		if (e.type == "keydown") {
	//			e.stop();
	//			setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
	//		}
	//		return true;
	//	}
	// }

	/**
	 * editorFromTextarea replaces a textarea with a codemirror editor
	 * @uses jquery collection $(this)
	 * @uses editorConfig
	 * @uses editorUserConfig
	 * @param editorConfig config obj
	 * @return jquery collection
	 */
	$.fn.editorFromTextarea = function(config){
		
		return $(this).each(function() {

			var $this = $(this);
			if(!$this.is("textarea")) return; // invalid element

			// use config arg if present and ignore user config
			if (typeof config == "undefined" || config === null){
				// Debugger.log('using default config');
				// Debugger.log(editorConfig);
				// Debugger.log(editorUserConfig);
				cm_config = jQuery.extend(true, {}, editorConfig, editorUserConfig);
			} else {
				// Debugger.log('using custom config');
				// Debugger.log(config);
				cm_config = jQuery.extend(true, {}, config);
			}
			
			// get mode override from data-mode attr if it exists
			if($this.data('mode')) cm_config.mode = getEditorMode($this.data('mode'));

			// Debugger.log(cm_config);
			// create codemirror instance from textarea DOM
			var editor = CodeMirror.fromTextArea($this.get(0), cm_config);

			// add reference to this editor to the textarea
			$this.data('editor', editor);

			// lazy load custom themes
			if(cm_config.theme != editorTheme && cm_config.theme != 'default'){
				var parts = cm_config.theme.split(' ');
				loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css",function(){editor.refresh();});
			}

			// init change listener
			editor.on('change', function(cm){
				cm.hasChange = true;
			});

			// var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder,'...');
			// editor.on("gutterClick", cmfold);

			// add resisable capability to codemirror
			$(editor.getWrapperElement()).resizable({
				// helper: "outline", // less intensive resizing
				autoHide : true, // hide the resize grips when unfocused
				minHeight: 25,
				start: function(e,ui) {
					ui.originalElement.css('min-height','25px'); // clamp min height				
				},
				resize: function(e,ui) {
					editor.setSize(null, $(this).height());
				},
				stop: function(e,ui) {
					// Debugger.log(ui.originalElement);
					ui.originalElement.css('min-height','25px'); // clamp min height
					ui.originalElement.css('max-height','none');
					editor.refresh();
				}
			});

			// replace jqueryui resize handle with custom icon
			$(editor.getWrapperElement()).find($('.ui-resizable-se')).removeClass('ui-icon')
                                                                     .addClass('handle')
                                                                     .html('&#x25e2;'); // U+25E2	e2 97 a2 BLACK LOWER RIGHT TRIANGLE

			if(CodeMirror){
				// setup autocomplete
				CodeMirror.commands.autocomplete = function(cm) {

					var mode = editorGetInnerMode(cm);
					Debugger.log('innermode: ' + mode);
					if (mode == 'xml') { //html depends on xml
						CodeMirror.showHint(cm, CodeMirror.hint.html);
					} else if (mode == 'javascript') {
						CodeMirror.showHint(cm, CodeMirror.hint.javascript);
					} else if (mode == 'css') {
						CodeMirror.showHint(cm, CodeMirror.hint.css);
					} else {
						CodeMirror.showHint(cm, CodeMirror.hint.anyword);
					}
				}
			}
			// adjust for window resizing awhen in fullscreen
			editor.on(window, "resize", function(e) {
				var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
				if (!showing) return;
				showing.CodeMirror.getWrapperElement().style.height = winHeight() + "px";
			});

			// add fixed fullscreen toggle
			fullscreen_button(editor);

		});
	};

	function initcodemirror(){
		// apply codemirror to class of .code_edit
		var elem= $(".code_edit").editorFromTextarea();
		setThemeSelected(editorTheme);
		cm_theme_update(editorTheme); // @todo: prevent overriding theme in custom configs
	}

	function editorGetInnerMode(cm){
		var doc     = cm.getDoc();
		var cursor = doc.getCursor();
		var mode    = CodeMirror.innerMode(cm.getMode(), cm.getTokenAt(cursor).state).mode.name;
		return mode;
	}

	function editorScrollVisible(cm){
		var wrap = cm.getWrapperElement();
		var scroller =  $(wrap).find('.CodeMirror-vscrollbar').css('display');
		return scroller == "block";
	}

	function customSave(cm){
		Debugger.log('saving');
		$("#submit_line input.submit").trigger('submit');
	}

    function winHeight() {
      return window.innerHeight || (document.documentElement || document.body).clientHeight;
    }

    function isFullScreen(cm) {
      return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
    }

    function toggleFullscreen(cm){
		setFullScreen(cm, !isFullScreen(cm));
    }

    function setFullScreen(cm, full) {
      var wrap = cm.getWrapperElement();
      if (full) {
        wrap.className += " CodeMirror-fullscreen";
        $(wrap).data('normalheight',$(wrap).css('height')); // store original height
        wrap.style.height = winHeight() + "px";
        document.documentElement.style.overflow = "hidden";
        $("body").addClass('fullscreen');
      } else {
        wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
        wrap.style.height = $(wrap).data('normalheight'); // restore original height
        document.documentElement.style.overflow = "";
        $("body").removeClass('fullscreen');
      }
      cm.refresh();
    }

	function setThemeSelected(theme){
		$("#cm_themeselect").val(theme);
	}

	function fullscreen_button(cm){
		var cmwrapper = $(cm.getWrapperElement());
		var scrolled = editorScrollVisible(cm);

		var button = cmwrapper.find(".overlay_but_fullscrn a");
		// Debugger.log(button);

		// if no button create it and add to editor
		if(button.length === 0){
			buttonhtml = $('<div class="overlay_but_fullscrn"></div>');
			button = $('<a href="javascript:void(0)"><i class="fa fa-arrows-alt"></i></a>').appendTo(buttonhtml);
			buttoncont = buttonhtml.appendTo(cmwrapper);
			button.on('click', cm,function(e){
				toggleFullscreen(e.data);
			});

			// events to watch for to adjust positioning accordingly
			cm.on('change', fullscreen_button);
			cm.on('update', fullscreen_button);
		}

		// adjust fullscreen button visibility and position
		button.toggleClass("scrolled",scrolled); // scrollbars
		button.toggleClass("hidden",cmwrapper.height() <= 25); // too small
	}

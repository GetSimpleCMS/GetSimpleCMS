var themeFileSave;
var editor;
var loadjscssfile;
jQuery(document).ready(function () {
	
	// do not know what this does, looks like old ctrl+q fold debouncer
	// function keyEvent(cm, e) {
	// 	if (e.keyCode == 81 && e.ctrlKey) {
	// 		if (e.type == "keydown") {
	// 			e.stop();
	// 			setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
	// 		}
	// 		return true;
	// 	}
	// }
	
	var defTheme = 'default';		

	if(typeof editor_theme != 'undefined' && editor_theme != 'default'){
		defTheme = editor_theme;
		var parts = defTheme.split(' ');
		loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css")
	}	

	var mode = 'php';

	var editorConfig = {
			lineNumbers: true,
			matchBrackets: true,
			indentUnit: 4,
			indentWithTabs: true,
			enterMode: "keep",
			mode: mode,
			tabMode: "shift",
			theme: defTheme,
			fixedGutter : true,
			styleActiveLine : true,
			// lineWrapping:true,
			matchBrackets : true, // highlight matching brackets when cusrsor is next to one
			autoCloseBrackets : true, // auto close brackets when typing
			// matchTags :true, // adds class CodeMirror-matchingbrackets to tags contents
			showTrailingSpace : true, // adds the CSS class cm-trailingspace to stretches of whitespace at the end of lines. 
			highlightSelectionMatches : true, // {showToken: /\w/}, // for word boundaries				
			extraKeys: {
				"Ctrl-Q" : function(cm) { foldFunc(cm, cm.getCursor().line); },
				"F11"    : function(cm) { setFullScreen(cm, !isFullScreen(cm)); },
				"Esc"    : function(cm) { if (isFullScreen(cm)) setFullScreen(cm, false); },
				"Ctrl-S" : function(cm) { customSave(cm);	}
			},
			saveFunction:  function(cm) { customSave(cm); },
	        viewportMargin: Infinity //for autosizing
	}

	/**
	 * editorFromTextarea replaces a textarea with a codemirror editor
	 * @param  dom or string of a textarea 
	 */
	$.fn.editorFromTextarea = function(textarea){	
		var editor = CodeMirror.fromTextArea(textarea, jQuery.extend({}, editorConfig));
		
		// add reference to this editor to the textarea
		$(textarea).data('editor', editor);

		editor.on('change', function(cm){
				cm.hasChange = true;
		});

		var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder,'...');
		editor.on("gutterClick", foldFunc);

		// add in resizing
		$(editor.getWrapperElement()).resizable({
			// helper: "outline", // less intensive resizing
			resize: function(e) {
				editor.setSize(null, $(this).height());
			},
			stop: function(e,ui) {
				// Debugger.log(ui.originalElement);
				ui.originalElement.css('max-height','none');
				editor.refresh();
			}
		});

		fullscreen_button(editor);			

		return editor;
	}

	// apply codemirror to class of .code_edit
	$(".code_edit").each(function(i,textarea) {	jQuery().editorFromTextarea(textarea); });	

	function editorScrollVisible(cm){
		var wrap = cm.getWrapperElement();		
		var scroller =  $(wrap).find('.CodeMirror-vscrollbar').css('display');
		return scroller == "block";
	}

	function customSave(cm){
		// Debugger.log('saving');
		themeFileSave(cm);
	}

    function isFullScreen(cm) {
      return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
    }

    function winHeight() {
      return window.innerHeight || (document.documentElement || document.body).clientHeight;
    }

    function toggleFullscreen(cm){
    	setFullScreen(cm, !isFullScreen(cm));
    }

    function setFullScreen(cm, full) {
      var wrap = cm.getWrapperElement();
      if (full) {
        wrap.className += " CodeMirror-fullscreen";
        wrap.style.height = winHeight() + "px";
        document.documentElement.style.overflow = "hidden";
      } else {
        wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
        wrap.style.height = "";
        document.documentElement.style.overflow = "";
      }
      cm.refresh();
    }

	CodeMirror.on(window, "resize", function() {
	    var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
	    if (!showing) return;
	    showing.CodeMirror.getWrapperElement().style.height = winHeight() + "px";
	});

	function setThemeSelected(theme){
		$("#cm_themeselect").val(theme);
	}

	function fullscreen_button(cm){
		var cmwrapper = $(cm.getWrapperElement());
		var scrolled = editorScrollVisible(cm);

		var button = cmwrapper.find(".overlay_but_fullscrn a");
		// Debugger.log(button);
		
		// if no button create it and add to editor
		if(button.length == 0){
			buttonhtml = $('<div class="overlay_but_fullscrn"></div>');
			button = $('<a href="#"><i class="icon-fullscreen"></i></a>').appendTo(buttonhtml);
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
		button.toggleClass("hidden",cm.getScrollInfo().height < 25); // too small
	}

	setThemeSelected(defTheme);
	cm_theme_update(defTheme);		
});

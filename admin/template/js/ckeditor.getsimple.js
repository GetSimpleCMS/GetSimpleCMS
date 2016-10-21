// var htmlEditorConfig;
var htmlEditorUserConfig;
// var editor; // legacy global editor , disabled
// @todo global js editor for plugins to add links to i18n_navigation

jQuery(document).ready(function () {
    initckeditor();
    if(htmlEditorConfig.timestamp) CKEDITOR.timestamp = htmlEditorConfig.timestamp;
});

// setup codemirror instances and functions

/**
 * htmlEditorFromTextarea replaces a textarea with a ckeditor instance
 * @uses jquery collection $(this)
 * @uses htmlEditorConfig
 * @uses htmlEditorUserConfig
 * @param htmlEditorConfig config obj
 * @return jquery collection
 */
$.fn.htmlEditorFromTextarea = function(config){

    return $(this).each(function() {

        var html_config;

        var $this = $(this);
        if(!$this.is("textarea")) return; // invalid element
        if($this.hasClass("noeditor")) return; // exclude        

        // use config arg if present and ignore user configs
        if (typeof config == "undefined" || config === null){
            // if not a config arg, merge main config and user configs
            // Debugger.log('using default config and user configs');
            // Debugger.log(htmlEditorConfig);
            // Debugger.log(htmlEditorUserConfig);
            html_config = jQuery.extend(true, {}, htmlEditorConfig, htmlEditorUserConfig);
        } else {
            // Debugger.log('using custom config');
            // Debugger.log(config);
            html_config = jQuery.extend(true, {}, config);
        }

        // get overrides from data-mode attr if it exists
        
        // autoheight will try to detect the content height and set its height to it
        if($this.data('htmleditautoheight') === true){
            // Debugger.log('editorautoheight');
            html_config.gsautoheight = true;
        }

        // compact hides ui parts when editor is not focused, namely toolbar        
        if($this.data('htmleditcompact') === true){
            // Debugger.log('editorcompact');
            html_config.gscompact = true;
        }

        // inline will insert the ckeditor inline editor ( no source mode at this time )
        if($this.data('htmleditinline') === true){
            // Debugger.log('editorinline');
            html_config.gscompact = false; // disable compact
            html_config.gsinline  = true;
        }

        // read only does not allow editing
        if($this.prop('readonly')){
            // Debugger.log('editor is readonly');
            html_config.readOnly = true;
        }
        
        // Debugger.log(html_config);

        // create ckeditor instance from textarea
        if(html_config.gsinline)
            var editor = CKEDITOR.inline($this.get(0),html_config);
        else
            var editor = CKEDITOR.replace($this.get(0),html_config);

        // add reference to this editor to the textarea
        $this.data('htmleditor', editor);

        // ctrl+s ajax save handler
        // ctrl+shift+s bypass ajaxsave
        editor.on('instanceReady', function (ev) {
            ev.editor.setKeystroke(CKEDITOR.CTRL + 83 /*S*/, 'customSave' );
            ev.editor.setKeystroke(CKEDITOR.SHIFT + CKEDITOR.CTRL + 83 /*S*/, 'customAltSave' );
            if(ev.editor.config.gsautoheight === true) cke_autoheight(ev.editor);
            if(ev.editor.config.gscompact === true) cke_editorfocus(ev.editor);

            ev.editor.on('resize',cke_editorResized);

            this.commands.maximize.on( 'exec', function( evt ) {
                Debugger.log('maximize cke');
                Debugger.log($('body').attr('class'));
                $('body').data('saveclasses',$('body').attr('class'));
                if(this.state == CKEDITOR.TRISTATE_ON){
                    $("body").removeClass('fullscreen');
                }
                else {
                    setTimeout(function(e){
                            if($('body').data('saveclasses') != undefined){
                                Debugger.log($('body').data('saveclasses'));
                                $('body').addClass($('body').data('saveclasses'));
                            }
                            $("body").addClass('fullscreen');
                        }
                        ,500
                    );
                }
            });   

        });

        // custom save function
        editor.addCommand( 'customSave',{
            exec : function( editor ){
                // Debugger.log('customsave');
                dosave(); // gs global save function
            }
        });

        editor.addCommand( 'customAltSave',{
            exec : function( editor ){
                // Debugger.log('customsave');
                dosavealt(); // gs global save function
            }
        });

        // trigger change event on original textarea for any form listeners

        // detect cke changes and trigger on original textarea
        editor.on( 'change', function() {
            // Debugger.log('cke change');
            // Debugger.log($(this.element.$));
            $(this.element.$).trigger('change');
        });

        // kludge onchange listener for cke source mode
        editor.on( 'mode', function() {
            _this = this;
            // Debugger.log('ckeditor mode change: '+ _this.mode);
            if ( _this.mode == 'source' ) {
                var editable = _this.editable();
                // $('textarea.cke_source').editorFromTextarea(); // @todo experminental use our own codemirror in ckeditor
                editable.attachListener( editable, 'input', function() {
                    // Debugger.log('ckeditor source input change');
                    _this.fire('change');
                });
            }
        });

    });
};


function htmledit_readonly(editor){
    editor.setReadOnly();
    cke_setheight(editor,100);    
}

function cke_editorResized(ev){
    // Debugger.log('editor was resized');
}

function cke_editorfocus(editor){
    if (editor && editor.config.gscompact == true) {
        // @todo ignore if fullscreen
        cke_hideui(editor); // @todo fall back to also hide all others if blur not working?
        editor.on('focus', function(event) {
            // Debugger.log('editor focused');
            cke_setheight(editor,1000); // @todo max height max plus n or just large
            cke_showui(editor);
        });

        // bind click events, prevent text selection on rapic clicking ( bind selectstart hack )
        $(editor).bind('selectstart dragstart', function(evt)
                                { evt.preventDefault(); return false; });

        editor.on('blur', function(event) {
            // Debugger.log('editor blurred');
            cke_autoheight(editor);
            cke_hideui(editor); 
        });
    }
}

function cke_showui(editor){
    editorelem = cke_geteditorelement(editor);
    editorelem.find(".cke_top").show();
    editorelem.find(".cke_path").show();
}

function cke_hideui(editor){
    editorelem = cke_geteditorelement(editor);
    editorelem.find(".cke_top").hide();
    editorelem.find(".cke_path").hide();
}

/**
 * ckeditor set height auto
 * get content height, and set ckeditor to match, with max clamp
 * @todo for some reason i was setting the iframe size before and after cke resize, workaround, or left over old code?
 * 
 * @param  obj editor
 */
function cke_autoheight(editor){
    if (editor && !cke_editorisinline(editor)) {
        // get content height for autosize
        var contentheight = cke_getcontentheight(editor);
        // Debugger.log('editor resize:' + editor.name + " changing height:" + contentheight);
        if(contentheight > 600) contentheight = 600; // @todo max height adjustable somewhere, will be smaller for collections than pages
        cke_setheight(editor,contentheight);
    }    
}

function cke_getcontentheight(editor){
    var editorname    = editor.name;
    var editorcontent = "#cke_" + editorname + " iframe";
    var editoriframe  = $(editorcontent);
    var contentheight = $(editoriframe.get(0).contentWindow.document).height();
    // var contentheight = editoriframe.get(0).contentWindow.document.body.offsetHeight;
    return contentheight;
}

function cke_setheight(editor,height){
    if(cke_editorisinline(editor)) return; // cannot set height if editor is inline
    // Debugger.log('ckeditor setting height ' + height);
    editor.resize( '100%', height, true );
    
    // set iframe height , but why ?
    // var editorcontent = "#cke_" + editor.name + " iframe";
    // $(editorcontent).css('height',height+'px');
}

function cke_geteditorelement(editor){
    return $("#cke_" + editor.name);
} 

function cke_editorisinline(editor){
    return editor.editable().isInline();
}
function initckeditor(){
    // apply ckeditor to class of .html_edit
    var editors = $(".html_edit").htmlEditorFromTextarea();
    // Debugger.log(editors);

    // @tddo backwards compatibility for i18n, set global editor to first editor
    // editor = $(editors[0]).data('htmleditor'); // hack in single editor, config.js and legacy uses this
}

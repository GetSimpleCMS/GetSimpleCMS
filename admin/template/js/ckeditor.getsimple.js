// var htmlEditorConfig;
var htmlEditorUserConfig;
// var editor;
// @todo global js editor for plugins to add links to i18n_navigation

jQuery(document).ready(function () {
    initckeditor();
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

        // Debugger.log(html_config);

        html_config.resize_dir             = 'both'; // resize in both directions
        // html_config.toolbarCanCollapse     = true;
        // html_config.toolbarStartupExpanded = false;

        // get overrides from data-mode attr if it exists
        if($this.data('editorcompact')){
            Debugger.log('editorcompact');
            html_config.toolbarCanCollapse     = false;
            html_config.toolbarStartupExpanded = true;
            html_config.compact = true;
        }

        if($this.prop('readonly')) html_config.readOnly = true;

        // create ckeditor instance from textarea
        if($this.hasClass('inline'))
            var editor = CKEDITOR.inline($this.get(0),html_config);
        else
            var editor = CKEDITOR.replace($this.get(0),html_config);

        // add reference to this editor to the textarea
        $this.data('htmleditor', editor);

        // ctr+s save handler
        editor.on('instanceReady', function (ev) {
            ev.editor.setKeystroke(CKEDITOR.CTRL + 83 /*S*/, 'customSave' );
            cke_autoheight(ev.editor);
            cke_editorfocus(ev.editor);
        });

        // custom save function
        editor.addCommand( 'customSave',{
            exec : function( editor ){
                // Debugger.log('customsave');
                dosave(); // gs global save function
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
                editable.attachListener( editable, 'input', function() {
                    // Debugger.log('ckeditor source input change');
                    _this.fire('change');
                });
            }
        });

    });
};

function cke_editorfocus(editor){
    if (editor && editor.config.compact == true) {
        cke_hideui(editor);
        editor.on('focus', function(event) {
            Debugger.log('editor focused');
            // cke_expandtoolbar(event.editor);
            editor.resize( '100%', 700, true );            
            cke_showui(editor); // @todo buggy generates selections where click is active and content moves
        });
        editor.on('blur', function(event) {
            Debugger.log('editor blurred');
            // cke_collapsetoolbar(event.editor);
            cke_autoheight(editor);
            cke_hideui(editor); // @todo buggy, only hide if another cke instance was clicked
        });
    }
}

function cke_expandtoolbar(editor){
    var expander = cke_geteditorelement(editor).find(".cke_toolbox_collapser");
    Debugger.log(expander.hasClass('cke_toolbox_collapser_min'));
    if(expander.hasClass('cke_toolbox_collapser_min')) $(expander).click();
}

function cke_collapsetoolbar(editor){
    var expander = cke_geteditorelement(editor).find(".cke_toolbox_collapser");
    Debugger.log(expander.hasClass('cke_toolbox_collapser_min'));
    if(!expander.hasClass('cke_toolbox_collapser_min')){
        $(expander).click(); // refocuses editor !
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

function cke_autoheight(editor){
    if (editor) {    
        var editorname    = editor.name;
        var editorcontent = "#cke_" + editorname + " iframe";
        var editoriframe  = $(editorcontent);
        var contentheight = editoriframe.contents().find("html").height();
        editoriframe.height(contentheight); // set height
        Debugger.log('editor resize:' + editorname + " changing height:" + contentheight);
        // $("#cke_" + editorname + " .cke_contents").attr('style',"height: "+ contentheight +"px;");
        if(contentheight > 500) contentheight = 500;
        editor.resize( '100%', contentheight, true );
    }    
}

function cke_geteditorelement(editor){
    return $("#cke_" + editor.name);
} 

function initckeditor(){
    // apply ckeditor to class of .html_edit
    var editors = $(".html_edit").htmlEditorFromTextarea();
    // Debugger.log(editors);

    // backwards compatibility for i18n, set global editor to first editor
    // editor = $(editors[0]).data('htmleditor');
}

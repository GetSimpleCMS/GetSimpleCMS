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

        // create ckeditor instance from textarea
        if($this.hasClass('inline'))
            var editor = CKEDITOR.inline($this.get(0),html_config);
        else
            var editor = CKEDITOR.replace($this.get(0),html_config);

        // add reference to this editor to the textarea
        $this.data('htmleditor', editor);

        // ctr+s save handler
        CKEDITOR.on('instanceReady', function (ev) {
            ev.editor.setKeystroke(CKEDITOR.CTRL + 83 /*S*/, 'customSave' );
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

function initckeditor(){
    // apply ckeditor to class of .html_edit
    var editors = $(".html_edit").htmlEditorFromTextarea();
    // Debugger.log(editors);

    // backwards compatibility for i18n, set global editor to first editor
    editor = $(editors[0]).data('htmleditor');
}

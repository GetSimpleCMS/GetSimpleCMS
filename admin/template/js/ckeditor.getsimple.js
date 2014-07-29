var htmlEditorConfig;
var htmlEditorUserConfig;

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

        var $this = $(this);
        if(!$this.is("textarea")) return; // invalid element

        // use config arg if present and ignore user config
        if (typeof config == "undefined" || config === null){
            // Debugger.log('using default config');
            // Debugger.log(htmlEditorConfig);
            // Debugger.log(htmlEditorUserConfig);
            html_config = jQuery.extend(true, {}, htmlEditorConfig, htmlEditorUserConfig);
        } else {
            // Debugger.log('using custom config');
            // Debugger.log(config);
            html_config = jQuery.extend(true, {}, config);
        }

        // Debugger.log(html_config);
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
        // @todo do a save first before trigger, if ckeditor not updated textarea

        // detect cke changes and trigger on original textarea
        editor.on( 'change', function() {
            // Debugger.log('cke change');
            // Debugger.log($(this.element.$));
            $(this.element.$).trigger('change');
        });

        // kludge onchange listener for cke source mode
        // @todo not working now
        editor.on( 'mode', function() {
            _this = this;
            Debugger.log('ckeditor mode change: '+ _this.mode);
            if ( _this.mode == 'source' ) {
                var editable = _this.editable();
                editable.attachListener( editable, 'input', function() {
                    Debugger.log('ckeditor source input change');
                    _this.fire('change');
                });
            }
        });

    });
};

function initckeditor(){
    // apply ckeditor to class of .html_edit
    var elem = $(".html_edit").htmlEditorFromTextarea();
}

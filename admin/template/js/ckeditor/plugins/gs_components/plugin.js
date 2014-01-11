CKEDITOR.plugins.add( 'gs_components',
{   
   requires : ['richcombo'], //, 'styles' ],
   init : function( editor )
   {
      
    var config = editor.config,
    lang = editor.lang.format;

    // Gets the list of tags from the settings.
    //var tags = []; //new Array();

    var xhReq = new XMLHttpRequest();
		 xhReq.open("GET", "template/js/ckeditor/plugins/gs_components/gs_components_json.php", false);
		 xhReq.send(null);
		 var serverResponse = xhReq.responseText;

      var tags = $.parseJSON(serverResponse);
      editor.ui.addRichCombo( 'gs_components',
         {
            label : "Insert",
            title :"Insert",
            voiceLabel : "Insert",
            className : 'cke_format',
            multiSelect : false,

            panel :
            {
               css : [ config.contentsCss, CKEDITOR.getUrl(CKEDITOR.skinName.split(",")[1]||"skins/"+CKEDITOR.skinName.split(",")[0]+"/") + "editor.css" ],
               voiceLabel : lang.panelVoiceLabel
            },

            init : function()
            {
               this.startGroup( "Components" );
               //this.add('value', 'drop_text', 'drop_label');
               for (var this_tag in tags){
                  this.add(tags[this_tag][0], tags[this_tag][1], tags[this_tag][2]);
               }
               this.startGroup( "Snippets" );
               for (var this_tag in tags){
                  this.add(tags[this_tag][0], tags[this_tag][1], tags[this_tag][2]);
               }               
            },

            onClick : function( value )
            {         
               editor.focus();
               editor.fire( 'saveSnapshot' );

               var selectText = editor.getSelection().getSelectedText();
                var tag=value;
                var tagIndex = tag.indexOf("(%");
                tag =  tag.substr(tagIndex);
                if (selectText!='') {
                  tag = tag.replace('content',selectText);
                }
                
               editor.insertHtml(tag.replace(/= /g,'="" ',tag));
               editor.fire( 'saveSnapshot' );
            }
         });
   }
});
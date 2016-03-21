/**
 * @fileOverview The "token" plugin.
 *
 */

'use strict';

function escapeRegExp(string){
    return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

( function() {
	CKEDITOR.plugins.add( 'token', {
		requires: 'widget,dialog',
		lang: 'en,ru',
		icons: 'token',
		hidpi: true,

		onLoad: function() {
			// Register styles for token widget frame.
			CKEDITOR.addCss( '.cke_token{background-color:#ff0}' );
		},

		init: function( editor ) {

			var lang = editor.lang.token;
			var tokenStart = '${';
            var tokenEnd = '}';
            if (typeof editor.config.tokenStart != 'undefined') {
                tokenStart = editor.config.tokenStart;
            }
            if (typeof editor.config.tokenEnd != 'undefined') {
                tokenEnd = editor.config.tokenEnd;
            }
            var tokenStartNum = tokenStart.length;
            var tokenEndNum = 0 - tokenEnd.length;

			// Register dialog.
			CKEDITOR.dialog.add( 'token', this.path + 'dialogs/token.js' );

			// Put ur init code here.
			editor.widgets.add( 'token', {
				// Widget code.
				dialog: 'token',
				pathName: lang.pathName,
				// We need to have wrapping element, otherwise there are issues in
				// add dialog.
				template: '<span class="cke_token"></span>',

				downcast: function() {
					return new CKEDITOR.htmlParser.text( tokenStart + this.data.name + tokenEnd );
				},

				init: function() {
					// Note that token markup characters are stripped for the name.
					this.setData( 'name', this.element.getText().slice( tokenStartNum, tokenEndNum ) );
				},

				data: function() {
					this.element.setText( tokenStart + this.data.name + tokenEnd );
				}
			} );

			editor.ui.addButton && editor.ui.addButton( 'CreateToken', {
				label: lang.toolbar,
				command: 'token',
				toolbar: 'insert,5',
				icon: 'token'
			} );
		},

		afterInit: function( editor ) {

            var tokenStart = '${';
            var tokenEnd = '}';
            if (typeof editor.config.tokenStart != 'undefined') {
                tokenStart = editor.config.tokenStart;
            }
            if (typeof editor.config.tokenEnd != 'undefined') {
                tokenEnd = editor.config.tokenEnd;
            }
            var tokenStartRegex = escapeRegExp(tokenStart);
            var tokenEndRegex = escapeRegExp(tokenEnd);
			var tokenReplaceRegex = new RegExp(tokenStartRegex + '([^' + tokenStartRegex + tokenEndRegex +'])+' + tokenEndRegex, 'g');

			editor.dataProcessor.dataFilter.addRules( {
				text: function( text, node ) {
					var dtd = node.parent && CKEDITOR.dtd[ node.parent.name ];

					// Skip the case when token is in elements like <title> or <textarea>
					// but upcast token in custom elements (no DTD).
					if ( dtd && !dtd.span )
						return;

					return text.replace( tokenReplaceRegex, function( match ) {
						// Creating widget code.
						var widgetWrapper = null,
							innerElement = new CKEDITOR.htmlParser.element( 'span', {
								'class': 'cke_token'
							} );

						// Adds token identifier as innertext.
						innerElement.add( new CKEDITOR.htmlParser.text( match ) );
						widgetWrapper = editor.widgets.wrapElement( innerElement, 'token' );

						// Return outerhtml of widget wrapper so it will be placed
						// as replacement.
						return widgetWrapper.getOuterHtml();
					} );
				}
			} );
		}
	} );

} )();

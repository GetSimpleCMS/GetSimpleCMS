/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here.
	config.resize_dir = 'vertical' // vertical resize
	config.toolbarCanCollapse = false; // hide toolbar collapse button
	config.dialog_backgroundCoverColor = '#000000';
};


CKEDITOR.on( 'dialogDefinition', function( ev )	{
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;
		ev.data.definition.resizable = CKEDITOR.DIALOG_RESIZE_NONE;

		if ( dialogName == 'link' ) {
			var infoTab = dialogDefinition.getContents( 'info' );
			//dialogDefinition.removeContents( 'target' );
			var advTab = dialogDefinition.getContents( 'advanced' );
			advTab.remove( 'advLangDir' );
			advTab.remove( 'advLangCode' );
			advTab.remove( 'advContentType' );
			advTab.remove( 'advTitle' );
			advTab.remove( 'advCharset' );
		}

		if ( dialogName == 'image' ) {
			var infoTab = dialogDefinition.getContents( 'info' );
			infoTab.remove( 'txtBorder' );
			infoTab.remove( 'txtHSpace' );
			infoTab.remove( 'txtVSpace' );
			infoTab.remove( 'btnResetSize' );
			dialogDefinition.removeContents( 'Link' );
			var advTab = dialogDefinition.getContents( 'advanced' );
			advTab.remove( 'cmbLangDir' );
			advTab.remove( 'txtLangCode' );
			advTab.remove( 'txtGenLongDescr' );
			advTab.remove( 'txtGenTitle' );
		}
});

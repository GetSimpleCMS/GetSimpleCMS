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

	config.toolbar_advanced = 
		[['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
		'/',
		['Styles','Format','Font','FontSize']];	

	config.toolbar_basic = 
		[['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']];

};

CKEDITOR.on( 'instanceReady', function( ev ) {
	var blockTags = ['div','h1','h2','h3','h4','h5','h6','p','pre','li','blockquote','ul','ol','table','thead','tbody','tfoot','td','th',];
	var rules = {
		indent : true,
		breakBeforeOpen : true,
		breakAfterOpen : false,
		breakBeforeClose : false,
		breakAfterClose : true
	};

	for (var i=0; i<blockTags.length; i++) {
		ev.editor.dataProcessor.writer.setRules( blockTags[i], rules );
	}
}); 

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

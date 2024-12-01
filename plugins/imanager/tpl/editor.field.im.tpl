<textarea id="[[id]]" class="[[class]]" name="[[name]]" >[[value]]</textarea>
<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">			
	var [[name]] = CKEDITOR.replace( '[[name]]', {
		skin : 'getsimple',
		forcePasteAsPlainText : true,
		language : '[[edlanguage]]',
		defaultLanguage : 'en',
		[[content-css]]
		entities : false,
		uiColor : '#FFFFFF',
		height: '[[edheight]]',
		baseHref : '[[siteurl]]',
		toolbar :
		[ [[toolbar]] ],
		[[edoptions]]
		tabSpaces:10,
		filebrowserBrowseUrl : 'filebrowser.php?type=all',
		filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
		filebrowserWindowWidth : '730',
		filebrowserWindowHeight : '500'
	});
	[[setup-editor]]
</script>
<div class="container">
	<!-- The file upload form used as target for the file upload widget -->
	<div id="fileupload" action="../plugins/imanager/upload/server/php/">
		<!-- Redirect browsers with JavaScript disabled to the origin page -->
		<noscript>JavaScript disabled</noscript>
		<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
		<div class="row fileupload-buttonbar">
			<div class="col-lg-7">
				<!-- The fileinput-button span is used to style the file input field as button -->
                <span class="file-upload button">
					<i class="fa fa-plus"></i>
                    <span>&nbsp;[[lang/add_files]]</span>
                    <input type="file" name="files[]" multiple>
                </span>
				<button type="submit" class="button start">
					<i class="fa fa-upload"></i>
					<span>&nbsp;[[lang/start_upload]]</span>
				</button>
				<button type="reset" class="button cancel">
					<i class="fa fa-times"></i>
					<span>&nbsp;[[lang/cancel_upload]]</span>
				</button>
				<button type="button" class="button delete">
					<i class="fa fa-trash"></i>
					<span>&nbsp;[[lang/delete_upload]]</span>
				</button>
				<input type="checkbox" class="toggle">
				<!-- The global file processing state -->
				<span class="fileupload-process"></span>
			</div>
			<!-- The global progress state -->
			<div class="col-lg-5 fileupload-progress fade">
				<!-- The global progress bar -->
				<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<div class="progress-bar progress-bar-success" style="width:0%;"></div>
				</div>
				<!-- The extended global progress state -->
				<div class="progress-extended">&nbsp;</div>
			</div>
		</div>
		<!-- The table listing the files available for upload/download -->
		<table role="presentation" class="table table-striped highlight"><tbody class="files"></tbody></table>
	</div>
</div>
<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
	<div class="slides"></div>
	<h3 class="title"></h3>
	<a class="prev">‹</a>
	<a class="next">›</a>
	<a class="close">×</a>
	<a class="play-pause"></a>
	<ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
    	<td>
    		<input class="pos" type="hidden" name="position[{%=file.position%}]" value="{%=file.name%}">
    	</td>
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
	<strong class="error text-danger"></strong>
	</td>
	<td>
	<p class="size">Processing...</p>
	<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
	</td>
	<td>
	{% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="fa fa-upload"></i>
                </button>
            {% } %}
	{% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="fa fa-times"></i>
                </button>
            {% } %}
	</td>
	</tr>
	{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade sortable">
    	<td>
    		<i class="fa fa-hand-o-up"></i>
    		<input class="pos" type="hidden" name="position[{%=file.position%}]" value="{%=file.name%}">
    	</td>
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
                <br />
                <input class="tit" type="text" placeholder="[[lang/imagetitle_placeholder]]" name="title[{%=file.position%}]" value="{%=file.title%}">
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
	{% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}&id=[[item-id]]&categoryid=[[currentcategory]]&fieldid=[[field]]&timestamp=[[timestamp]]"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="fa fa-trash"></i>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="fa fa-times"></i>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="../plugins/imanager/upload/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<!-- <script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script> -->
<script src="../plugins/imanager/upload/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<!-- <script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script> -->
<script src="../plugins/imanager/upload/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<!-- <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
<script src="../plugins/imanager/upload/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<!-- <script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script> -->
<script src="../plugins/imanager/upload/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="../plugins/imanager/upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="../plugins/imanager/upload/js/jquery.fileupload-ui.js"></script>
<script src="../plugins/imanager/upload/js/jquery.fileupload-jquery-ui.js"></script>
<!-- The main application script -->
<!--<script src="../plugins/imanager/upload/js/main.js"></script>-->
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="../plugins/imanager/upload/js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
<script>
function renumberImages(g) {
	$('.table tbody tr').each(function(i,tr) {
		$(tr).find('input').each(function(k,elem) {
			var name = $(elem).attr('name').replace(/\d+/, (i));
			//var name = $(elem).attr('name').replace(/[ \d+]/, (i));
			$(elem).attr('name', name);
		});
	});
}
$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '../plugins/imanager/upload/server/php/index.php?id=[[item-id]]&categoryid=[[currentcategory]]&fieldid=[[field]]&timestamp=[[timestamp]]'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

	// Load existing files:
	$('#fileupload').addClass('fileupload-processing');

	$.ajax({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},

		url: $('#fileupload').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#fileupload')[0]
	}).always(function () {
		$(this).removeClass('fileupload-processing');
	}).done(function (result) {
		$(this).fileupload('option', 'done')
			.call(this, $.Event('done'), {result: result});
	});
	$('.table tbody').sortable({
		items:"tr.sortable", handle:'td',
		update:function(e,ui) {
			renumberImages();
		}
	});
	renumberImages();
});
</script>
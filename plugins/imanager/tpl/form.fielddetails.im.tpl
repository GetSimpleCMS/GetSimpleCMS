<div class="manager-wrapper">
	<h3 class="menuglava">[[lang/field-details-titel]]</h3>
	<form class="largeform" id="editfied-details" action="load.php?id=imanager&fields&field=[[field-id]]" method="post" accept-charset="utf-8">
		<div>
			<div class="fieldarea">
				<label for="fieldid" class="im-left">[[lang/field_id]]</label>
				<p id="filedid" class="im-cat-info">[[field-id]]</p>
			</div>
			<div class="fieldarea">
				<label for="fieldname">[[lang/field_name]]</label>
				<p id="fieldname" class="im-cat-info">[[field_name]]</p>
			</div>
			<div class="fieldarea">
				<label for="fieldlabel">[[lang/field_label]]</label>
				<p id="fieldlabel" class="im-cat-info">[[field_label]]</p>
			</div>
			<div class="fieldarea">
				<label for="fieldid">[[lang/field_type]]</label>
				<p id="fieldid" class="im-cat-info">[[field_type]]</p>
			</div>
			<div class="fieldarea">
				<label for="fielddefault">[[lang/fields_default]]</label>
				<p class="field-info">[[lang/field_default_info]]</p>
				[[default]]
			</div>
			<div class="fieldarea">
				<label for="fieldinfo">[[lang/field_info]]</label>
				<p class="field-info">[[lang/field_info_info]]</p>
				<p><input id="fieldinfo" class="text-fields-left text" name="info" type="text" value='[[fieldinfo]]'></p>
			</div>
			<div class="fieldarea">
				<label for="fieldrequired">[[lang/field_required]]</label>
				<p><input id="fieldrequired" class="checkbox-fields-left" name="required" type="checkbox" value="1" [[fieldrequired]]></p>
			</div>
			<div class="fieldarea">
				<label for="min_field_input">[[lang/input_min_length]]</label>
				<p><input id="min_field_input" class="number-fields-left number" name="min_field_input" type="number" value="[[min_field_input]]"></p>
			</div>
			<div class="fieldarea">
				<label for="max_field_input">[[lang/input_max_length]]</label>
				<p><input id="max_field_input" class="number-fields-left number" name="max_field_input" type="number" value="[[max_field_input]]"></p>
			</div>

			<div class="fieldarea">
				<label for="areacss">[[lang/area_css]]</label>
				<p class="field-info">[[lang/area_css_info]]</p>
				<p><input id="areacss" class="text-fields-left text" name="areaclass" type="text" value="[[area_class]]"></p>

				<div class="fieldarea intern">
					<label for="labelcss">[[lang/label_css]]</label>
					<p class="field-info">[[lang/label_css_info]]</p>
					<p><input id="labelcss" class="text-fields-left text" name="labelclass" type="text" value="[[label_class]]"></p>
				</div>

				<div class="fieldarea intern">
					<label for="fieldcss">[[lang/field_css]]</label>
					<p class="field-info">[[lang/field_css_info]]</p>
					<p><input id="fieldcss" class="text-fields-left text" name="fieldclass" type="text" value="[[field_class]]"></p>
				</div>
			</div>
			[[fieldproperties]]
			<p><input name="submit" type="submit" class="submit" value="[[lang/savebutton]]" /></p>
		</div>
	</form>
</div>
<script>
	$(function() {
		$('form').on('submit', function (e) {
			var inputdefault = '';
			var editableval = $('#editable').html().replace(/"/g, "&#34;").replace(/"/g, "&#34;").replace(/</g, '&lt;').replace(/>/g, '&gt;');
			if(editableval !== '' && editableval !== undefined) {
				inputdefault = '<input type="text" name="default" value="' + editableval + '"/>';
				$('#editfied-details').append(inputdefault);
			}
		});
	});
</script>
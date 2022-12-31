<?php
/**
 * Category selector in page options
 */
$this->editorFilter =
<<<EOD
<div class="editorFilterWrapper">
	<div id="selectorForm" name="editorFilterForm" method="post">
		<fieldset>
			<legend>Select Category</legend>
			<label for="categorySelector">IM Category</label>
			<select name="imcat" id="imcat">
				[[options]]
			</select>
		</fieldset>
	</div>
</div>
EOD;


/**
 * Item data edit page body
 */
$this->editorBody =
<<<EOD
<div id="itemContent" class="manager-wrapper">[[inputs]]</div>
<script>
$(function() {
	$('#imcat').on('change', function() {
		$.submitform();
	});
	$.submitform = function() {
		var id = $('#imcat').val();
		var formData = {epcatid:id};
		$.ajax({
			type: "POST",
			data : formData,
			dataType : 'json',
			async: true,
			success: function(data)
			{
				//console.log(data);
				if(data.status == 1) {
					// destroy ckeditor instancess
					if(typeof(CKEDITOR) !== 'undefined') {
						for(name in CKEDITOR.instances) {
							if(name !== 'post-content') CKEDITOR.instances[name].destroy(true);
						}
					}
					$('#itemContent').show();
					$('#itemContent').html(data.output);
				} else {
					// destroy ckeditor instancess
					if(typeof(CKEDITOR) !== 'undefined') {
						for(name in CKEDITOR.instances) {
							if(name !== 'post-content') CKEDITOR.instances[name].destroy(true);
						}
					}
					$('#itemContent').show();
					$('#itemContent').html(data.output);
				}
			}
		});
	}
});
</script>
EOD;


/**
 * Item-ID row
 */
$this->itemInfo =
<<<EOD
	<div class="itemInfo"><p>Category: [[catid]] | Item: [[id]]</p></div>
EOD;


/**
 * Options template
 */
$this->selectOption =
<<<EOD
	<option value="[[value]]"[[selected]]>[[label]]</option>
EOD;


/**
 * Item fields wrapper
 */
$this->inputsArounder =
<<<EOD
		<div class="fieldsArounder">
			[[itemidrow]]
			[[fields]]
			[[imlink]]
			<input type="hidden" name="timestamp" value="[[timestamp]]">
			<input type="hidden" name="itemid" value="[[itemid]]">
			<input type="hidden" name="categoryid" value="[[categoryid]]">
		</div>
		<script>
			$(function() {
				for(var i in CKEDITOR.instances) {
					CKEDITOR.instances[i].on('change', function() {
						if(this.name != 'post-content') { this.updateElement(); }
					});
				}
			});
		</script>
EOD;


/**
 * ItemManager link
 */
$this->itemManagerLink =
<<<EOD
	<p>Open item in a new window or tab: <a href="[[link]]" target="_blank">ItemManager</a></p>
EOD;


/**
 * Item delete field
 */
$this->deleteInput =
<<<EOD
		<div class="fieldsArounder">
			<input type="hidden" name="categoryid" value="-1">
		</div>
EOD;

?>
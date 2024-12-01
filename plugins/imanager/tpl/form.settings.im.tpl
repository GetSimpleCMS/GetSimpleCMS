<div class="manager-wrapper">
<form class="largeform" action="load.php?id=imanager&settings&settings_edit" method="post" accept-charset="utf-8">
	<div class="im-tabs">
		<ul class="tab-links">
			<li class="active"><a href="#tab1">[[lang/general_settings]]</a></li>
			<li><a href="#tab2">[[lang/admin_settings]]</a></li>
		</ul>

		<div class="tab-content">
			<div id="tab1" class="tab active">
				<h3 class="menuglava">[[lang/general_settings]]</h3>
				[[lang/general_settings_tab_text]]
				<div class="fieldarea">
					<label for="maxcatname">[[lang/cat_max_length]]</label>
					<p><input id="maxcatname" class="number-fields-left number" name="maxcatname" type="number" value="[[maxcatname]]"></p>
				</div>
				<div class="fieldarea">
					<label for="maxfieldname">[[lang/field_max_length]]</label>
					<p><input id="maxfieldname" class="number-fields-left number" name="maxfieldname" type="number" value="[[maxfieldname]]"></p>
				</div>
				<div class="fieldarea">
					<label for="maxitemname">[[lang/item_max_length]]</label>
					<p><input id="maxitemname" class="number-fields-left number" name="maxitemname" type="number" value="[[maxitemname]]"></p>
				</div>

				<h2 class="menuglava">[[lang/search_settings_section]]</h2>

				<div class="fieldarea">
					<label for="i18nsearch">[[lang/i18nsearch_engine]]</label>
					<p class="field-info">[[lang/i18nsearch_engine_info]]</p>
					<p><input id="i18nsearch" class="checkbox-fields-left" name="i18nsearch" type="checkbox" value="1" [[i18nsearch]]></p>
					<div class="im-toggle" id="i18nsearch_fields">
						<div class="fieldarea intern">
							<label for="i18nsearchfields">[[lang/i18nsearch_field]]</label>
							<p class="field-info">[[lang/i18nsearch_field_info]]</p>
							<p><input id="i18nsearchfield" class="text-fields-left text" name="i18nsearchfield" type="text" value="[[i18nsearch_field]]"></p>
						</div>
						<div class="fieldarea intern">
							<label for="i18nsearchexcludes">[[lang/i18nsearch_exclude_category]]</label>
							<p class="field-info">[[lang/i18nsearch_exclude_category_info]]</p>
							<p><input id="i18nsearchexcludes" class="text-fields-left text" name="i18nsearchexcludes" type="text" value="[[exclude_categories]]"></p>
						</div>

						<div class="fieldarea intern">
							<label for="i18nsearchurl">[[lang/i18nsearch_url]]</label>
							<p class="field-info">[[lang/i18nsearch_url_info]]</p>
							<p><input id="i18nsearchurl" class="text-fields-left text" name="i18nsearchurl" type="text" value="[[i18nsearch_url]]"></p>
						</div>
						<div class="fieldarea intern">
							<label for="i18nsearchsegment">[[lang/i18nsearch_segment]]</label>
							<p class="field-info">[[lang/i18nsearch_segment_info]]</p>
							<p><input id="i18nsearchsegment" class="text-fields-left text" name="i18nsearchsegment" type="text" value="[[i18nsearch_segment]]"></p>
						</div>
						<div class="fieldarea intern">
							<label for="i18nsearchcontent">[[lang/i18nsearch_content]]</label>
							<p class="field-info">[[lang/i18nsearch_content_info]]</p>
							<p><input id="i18nsearchcontent" class="text-fields-left text" name="i18nsearchcontent" type="text" value="[[i18nsearch_content]]"></p>
						</div>
					</div>
				</div>

			</div>

			<div id="tab2" class="tab">
				<h3 class="menuglava">[[lang/admin_settings]]</h3>
				[[lang/backend_settings_tab_text]]
				<div class="fieldarea">
					<label for="timeformat">[[lang/date_time_format]]</label>
					<p><input id="timeformat" class="number-fields-left number" name="timeformat" type="text" value="[[timeformat]]"></p>
				</div>
				<h2 class="menuglava">[[lang/category_settings_section]]</h2>
				<div class="fieldarea">
					<label for="catorderby">[[lang/default_cat_orderby]]</label>
					<p class="field-info">[[lang/cat_orderby_description]]</p>
					<p><select class="small" name="catorderby">
							<option value="position" [[position]]>[[lang/position]]</option>
							<option value="name" [[name]]>[[lang/category_name]]</option>
							<option value="created" [[created]]>[[lang/date_created]]</option>
							<option value="updated" [[updated]]>[[lang/date_updated]]</option>
						</select></p>
				</div>
				<div class="fieldarea">
					<label for="catorder">[[lang/default_cat_order]]</label>
					<p><select class="small" name="catorder">
							<option value="asc" [[asc]]>[[lang/ascending]]</option>
							<option value="desc" [[desc]]>[[lang/descending]]</option>
						</select></p>
				</div>
				<div class="fieldarea">
					<label for="catfilter">[[lang/display_cat_filter]]</label>
					<p class="field-info">[[lang/display_cat_filter_info]]</p>
					<p><input id="catfilter" class="checkbox-fields-left" name="catfilter" type="checkbox" value="1" [[catfilter]]></p>
				</div>
				<div class="fieldarea">
					<label for="maxcatperpage">[[lang/cat_per_page]]</label>
					<p class="field-info">[[lang/cat_per_page_description]]</p>
					<p><select id="maxcatperpage" class="small" name="maxcatperpage">
						<option value="10" [[ten]]>10</option>
						<option value="20" [[twenty]]>20</option>
						<option value="30" [[thirty]]>30</option>
						<option value="40" [[forty]]>40</option>
						<option value="50" [[fifty]]>50</option>
					</select></p>
				</div>
				<div class="fieldarea">
					<label for="catbackup">[[lang/create_cat_backups]]</label>
					<p><input id="catbackup" class="checkbox-fields-left" name="catbackup" type="checkbox" value="1" [[catbackup]]></p>
					<div class="im-toggle" id="category">
						<div class="fieldarea intern">
							<label for="catbackupdir">[[lang/cat_backups_dir]]</label>
							<p class="field-info">[[lang/directory_musst_exist_info]]</p>
							<p><input id="fieldbackupdir" class="text-fields-left text" name="catbackupdir" type="text" value="[[catbackupdir]]"></p>
						</div>
						<div class="fieldarea intern">
							<label for="min_catbackup_days">[[lang/catbackup_min_time]]</label>
							<p class="field-info">[[lang/catbackup_min_time_info]]</p>
							<p><input id="min_catbackup_days" class="number-fields-left number" name="min_catbackup_days" type="number" value="[[min_catbackup_days]]"></p>
						</div>
					</div>
				</div>

				<!-- FIELDS SETTINGS -->

				<h2 class="menuglava">[[lang/field_settings_section]]</h2>
				<div class="fieldarea">
					<label for="fieldbackup">[[lang/create_field_backups]]</label>
					<p><input id="fieldbackup" class="checkbox-fields-left" name="fieldbackup" type="checkbox" value="1" [[fieldbackup]]></p>
					<div class="im-toggle" id="fields">
						<div class="fieldarea intern">
							<label for="fieldbackupdir">[[lang/field_backups_dir]]</label>
							<p class="field-info">[[lang/directory_musst_exist_info]]</p>
							<p><input id="fieldbackupdir" class="text-fields-left text" name="fieldbackupdir" type="text" value="[[fieldbackupdir]]"></p>
						</div>
						<div class="fieldarea intern">
							<label for="min_fieldbackup_days">[[lang/fieldbackup_min_time]]</label>
							<p class="field-info">[[lang/fieldbackup_min_time_info]]</p>
							<p><input id="min_fieldbackup_days" class="number-fields-left number" name="min_fieldbackup_days" type="number" value="[[min_fieldbackup_days]]"></p>
						</div>
					</div>
				</div>


				<!-- ITEM SETTINGS -->

				<h2 class="menuglava">[[lang/item_settings_section]]</h2>

				<!--<div class="fieldarea">
					<label for="itemlistdisplay1">[[lang/default_display_fields]]</label>
					<p class="field-info">[[lang/default_display_fields_info]]</p>
					<p><select id="itemlistdisplay1" class="small" name="itemlistdisplay1">
							<option value="name" [[display_name1]]>[[lang/item_name]]</option>
							<option value="created" [[display_created1]]>[[lang/date_created]]</option>
							<option value="updated" [[display_updated1]]>[[lang/date_updated]]</option>
						</select></p>
					<p><select id="itemlistdisplay2" class="small" name="itemlistdisplay2">
							<option value="name" [[display_name2]]>[[lang/item_name]]</option>
							<option value="created" [[display_created2]]>[[lang/date_created]]</option>
							<option value="updated" [[display_updated2]]>[[lang/date_updated]]</option>
						</select></p>
					<p><select id="itemlistdisplay3" class="small" name="itemlistdisplay3">
							<option value="name" [[display_name3]]>[[lang/item_name]]</option>
							<option value="created" [[display_created3]]>[[lang/date_created]]</option>
							<option value="updated" [[display_updated3]]>[[lang/date_updated]]</option>
						</select></p>
				</div>-->



				<div class="fieldarea">
					<label for="itemorderby">[[lang/default_item_orderby]]</label>
					<p class="field-info">[[lang/item_orderby_description]]</p>
					<p><select class="small" name="itemorderby">
							<option value="position" [[i_position]]>[[lang/position]]</option>
							<option value="name" [[i_name]]>[[lang/item_name]]</option>
							<option value="label" [[i_label]]>[[lang/item_label]]</option>
							<option value="active" [[i_active]]>[[lang/item_active]]</option>
							<option value="created" [[i_created]]>[[lang/date_created]]</option>
							<option value="updated" [[i_updated]]>[[lang/date_updated]]</option>
						</select></p>
				</div>
				<div class="fieldarea">
					<label for="itemorder">[[lang/default_item_order]]</label>
					<p><select class="small" name="itemorder">
							<option value="asc" [[i_asc]]>[[lang/ascending]]</option>
							<option value="desc" [[i_desc]]>[[lang/descending]]</option>
						</select></p>
				</div>
				<div class="fieldarea">
					<label for="itemfilter">[[lang/display_item_filter]]</label>
					<p class="field-info">[[lang/display_item_filter_info]]</p>
					<p><input id="itemfilter" class="checkbox-fields-left" name="itemfilter" type="checkbox" value="1" [[itemfilter]]></p>
				</div>
				<div class="fieldarea">
					<label for="maxitemperpage">[[lang/items_per_page]]</label>
					<p class="field-info">[[lang/items_per_page_description]]</p>
					<p><select id="maxitemperpage" class="small" name="maxitemperpage">
							<option value="10" [[i_ten]]>10</option>
							<option value="20" [[i_twenty]]>20</option>
							<option value="30" [[i_thirty]]>30</option>
							<option value="40" [[i_forty]]>40</option>
							<option value="50" [[i_fifty]]>50</option>
						</select></p>
				</div>
				<div class="fieldarea">
					<label for="itembackup">[[lang/create_item_backups]]</label>
					<p><input id="itembackup" class="checkbox-fields-left" name="itembackup" type="checkbox" value="1" [[itembackup]]></p>
					<div class="im-toggle" id="items">
						<div class="fieldarea intern">
							<label for="itembackupdir">[[lang/item_backups_dir]]</label>
							<p class="field-info">[[lang/directory_musst_exist_info]]</p>
							<p><input id="itemdbackupdir" class="text-fields-left text" name="itembackupdir" type="text" value="[[itembackupdir]]"></p>
						</div>
						<div class="fieldarea intern">
							<label for="min_itembackup_days">[[lang/itembackup_min_time]]</label>
							<p class="field-info">[[lang/itembackup_min_time_info]]</p>
							<p><input id="min_itembackup_days" class="number-fields-left number" name="min_itembackup_days" type="number" value="[[min_itembackup_days]]"></p>
						</div>
					</div>
				</div>
				<div class="fieldarea">
					<label for="itemactive">[[lang/item_enabled]]</label>
					<p class="field-info">[[lang/item_enabled_info]]</p>
					<p><input id="itemactive" class="checkbox-fields-left" name="itemactive" type="checkbox" value="1" [[itemactive]]></p>
				</div>
				<div class="fieldarea">
					<label for="uniqueitemname">[[lang/unique_itemname]]</label>
					<p class="field-info">[[lang/unique_itemname_info]]</p>
					<p><input id="uniqueitemname" class="checkbox-fields-left" name="uniqueitemname" type="checkbox" value="1" [[uniqueitemname]]></p>
				</div>


				<h2 class="menuglava">[[lang/imageupload_settings_section]]</h2>
				<div class="fieldarea">
					<label for="min_tmpimage_days">[[lang/tmp_itage_min_time]]</label>
					<p class="field-info">[[lang/tmp_itage_min_time_info]]</p>
					<p><input id="min_tmpimage_days" class="number-fields-left number" name="min_tmpimage_days" type="number" value="[[min_tmpimage_days]]"></p>
				</div>


			</div>

			<div id="tab3" class="tab">
				<p>Tab #3 content goes here!</p>
				<p>Donec pulvinar neque sed semper lacinia. Curabitur lacinia ullamcorper nibh; quis imperdiet velit eleifend ac. Donec blandit mauris eget aliquet lacinia! Donec pulvinar massa interdum ri.</p>
			</div>

			<div id="tab4" class="tab">
				<p>Tab #4 content goes here!</p>
				<p>Donec pulvinar neque sed semper lacinia. Curabitur lacinia ullamcorper nibh; quis imperdiet velit eleifend ac. Donec blandit mauris eget aliquet lacinia! Donec pulvinar massa interdum risus ornare mollis. In hac habitasse platea dictumst. Ut euismod tempus hendrerit. Morbi ut adipiscing nisi. Etiam rutrum sodales gravida! Aliquam tellus orci, iaculis vel.</p>
			</div>
		</div>
		<p class="im-buttonwrapper"><span><input class="submit" type="submit" name="settings_edit" value="[[lang/label_save_settings]]"></span></p>
	</div>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.im-tabs .tab-links a').on('click', function(e)  {
			var currentAttrValue = jQuery(this).attr('href');

			// Show/Hide Tabs
			$('.im-tabs ' + currentAttrValue).show().siblings().hide();

			// Change/remove current tab to active
			$(this).parent('li').addClass('active').siblings().removeClass('active');

			e.preventDefault();
		});


		if($("#catbackup").attr('checked')) {
			$("#category").show();
		}
		if($("#fieldbackup").attr('checked')) {
			$("#fields").show();
		}
		if($("#itembackup").attr('checked')) {
			$("#items").show();
		}
		if($("#i18nsearch").attr('checked')) {
			$("#i18nsearch_fields").show();
		}


		$("#catbackup").change(function(){
			$("#category").toggle();
		});
		$("#fieldbackup").change(function(){
			$("#fields").toggle();
		});
		$("#itembackup").change(function(){
			$("#items").toggle();
		});
		$("#i18nsearch").change(function(){
			$("#i18nsearch_fields").toggle();
		});

	});
	</script>
</form>
</div>
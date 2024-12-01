<div id="filterarea">
	<p class="label">[[lang/category_filter_title]]</p>
	<select class="text short" id="filterby" type="text" name="orderby">
		<option value="position" [[position]]>[[lang/position]]</option>
		<option value="name" [[name]]>[[lang/category_name]]</option>
		<option value="created" [[created]]>[[lang/date_created]]</option>
		<option value="updated" [[updated]]>[[lang/date_updated]]</option>
	</select>
	<select class="text short" id="option" type="text" name="option">
		<option value="asc" [[asc]] >ASC</option>
		<option value="desc" [[desc]] >DESC</option>
	</select>
	<p class="sm-label">[[lang/categories_per_page]]</p>
	<div id="im-nswitch">[[nswitch]]</div>
</div>
<!-- javascript in form.categorylist.im.tpl -->
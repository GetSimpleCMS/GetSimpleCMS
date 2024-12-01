<div id="filterarea">
	<p class="label">[[lang/category_filter_title]]</p>
	<ul class="selector-row">
		<li class="selector-col selector-50col">
			<select class="text short" id="filterby" type="text" name="orderby">
				<option value="position" [[position]]>[[lang/position]]</option>
				<option value="name" [[name]]>[[lang/category_name]]</option>
				<option value="created" [[created]]>[[lang/date_created]]</option>
				<option value="updated" [[updated]]>[[lang/date_updated]]</option>
			</select>
		</li>
		<li class="selector-col selector-50col">
			<select class="text short" id="option" type="text" name="option">
				<option value="asc" [[asc]] >ASC</option>
				<option value="desc" [[desc]] >DESC</option>
			</select>
		</li>
	</ul>
	<p class="sm-label">[[lang/categories_per_page]]</p>
	<div id="im-nswitch">[[nswitch]]</div>
</div>
<!-- javascript in form.categorylist.im.tpl -->
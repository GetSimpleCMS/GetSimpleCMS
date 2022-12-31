<div id="filterarea">
	<p class="label">[[lang/item_filter_title]]</p>
	<ul class="selector-row">
		<li class="selector-col selector-50col">
			<select class="text short" id="filterby" name="orderby">
				<option value="position" [[position]]>[[lang/position]]</option>
				<option value="name" [[name]]>[[lang/item_name]]</option>
				<option value="label" [[label]]>[[lang/item_label]]</option>
				<option value="active" [[active]]>[[lang/item_active]]</option>
				<option value="created" [[created]]>[[lang/date_created]]</option>
				<option value="updated" [[updated]]>[[lang/date_updated]]</option>
			</select>
		</li>
		<li class="selector-col selector-50col">
			<select class="text short" id="option" name="option">
				<option value="asc" [[asc]] >ASC</option>
				<option value="desc" [[desc]] >DESC</option>
			</select>
		</li>
	</ul>

	<ul class="selector-row">
		<li class="selector-col selector-35col">
			<select class="short" id="filterbyfield" name="orderbyfield">
				<option></option>
				[[fieldoptions]]
			</select>
		</li>
		<li class="selector-col selector-30col">
			<select class="short" id="filter" name="filter">
				<option value="eq" [[eq]] >=</option>
				<option value="geq" [[geq]] >&gt;=</option>
				<option value="leq" [[leq]] >&lt;=</option>
				<option value="g" [[g]] >&gt;</option>
				<option value="l" [[l]] >&lt;</option>
			</select>
		</li>
		<li class="selector-col selector-35col">
			<input class="short" id="filtervalue" type="text" onkeypress="return event.keyCode != 13;" name="filtervalue" value="[[filtervalue]]">
		</li>
	</ul>
	<p class="sm-label">[[lang/items_per_page]]</p>
	<div id="im-nswitch">[[nswitch]]</div>
</div>
<!-- javascript in form.itemlist.im.tpl -->
<div class="manager-wrapper">
	<form class="largeform" action="load.php?id=imanager&category&categoryupdate" method="post" accept-charset="utf-8">
		<div>
			<div id="cat-details">
				<h3 class="menuglava">[[lang/edit_category]]</h3>
				<p>[[lang/edit_category_info]]</p>
				<div class="fieldarea">
					<label for="catid" class="im-left">[[lang/category_id]]</label>
					[[infotext]]
					<p id="catid" class="im-cat-info">[[catid]]</p>
				</div>

				<div class="fieldarea">
					<label for="catname" >[[lang/category_name]]</label>
					[[infotext]]
					<p><input id="catname" class="text-fields-left text" name="name" type="text" value="[[catname]]"></p>
				</div>

				<div class="fieldarea">
					<label for="catslug" >[[lang/category_slug]]</label>
					[[infotext]]
					<p><input id="catslug" class="text-fields-left text" name="slug" type="text" value="[[catslug]]"></p>
				</div>

				<div class="fieldarea">
					<label for="catposition" >[[lang/category_position]]</label>
					[[infotext]]
					<p><input id="catposition" class="number-fields-left number" name="position" type="number" value="[[catposition]]"></p>
				</div>

				<div class="fieldarea">
					<label for="catcreated" >[[lang/category_created]]</label>
					[[infotext]]
					<p class="im-cat-info">[[created]]</p>
				</div>

				<div class="fieldarea">
					<label for="catcreated" >[[lang/category_updated]]</label>
					[[infotext]]
					<p class="im-cat-info">[[updated]]</p>
				</div>

				<input type="hidden" value="[[catid]]" name="id">
			</div>
			<p class="im-buttonwrapper"><span><input class="submit" type="submit" name="category_save" value="[[lang/label_save_settings]]"></span></p>
		</div>
	</form>
</div>
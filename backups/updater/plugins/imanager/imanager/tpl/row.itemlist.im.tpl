<tr class="sortable">
	<td class="im-drag"><i class="fa fa-hand-o-up"></i></td>
	<td class="im-pos"><span class="index">[[item-position]]</span><input type="hidden" class="position" name="[[item-id]]" value="[[item-position]]"></td>
	<td><a href="load.php?id=imanager&edit=[[item-id]]&page=[[page]]" title="Edit [[lang/item]]: [[item-name]]">[[item-name]]</a></td>
	<td>[[item-created]]</td>
	<td>[[item-updated]]</td>
	<td><a href="load.php?id=imanager&activate=[[item-id]]&page=[[page]]" class="switch_active" title="[[lang/activate_deactivate]] [[lang/item]]">[[item-checkuncheck]]</a></td>
	<td class="im-del"><a onclick="return confirm('[[lang/confirm_delete]]');" href="load.php?id=imanager&delete=[[item-id]]&page=[[page]]" title="Delete [[itemmanager-title]]: [[item-title]]"><i class="fa fa-times"></i></a></td>
</tr>
<div class="manager-wrapper">
<h3 class="menuglava">[[lang/fields_title]]</h3>
[[catselector]]
<p class="clear">[[lang/fields_description]]</p>
<p>[[lang/fields_usage_description]]</p>
<form method="post" id="customfieldsForm">
    <table id="editfields" class="edittable highlight">
        <thead>
            <tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
                <th>[[lang/fields_name]]</th>
                <th>[[lang/fields_label]]</th>
                <th style="width:100px;">[[lang/fields_type]]</th>
                <th>[[lang/fields_default]]</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            [[categorie_items]]    
            <tr>
                <td colspan="6">
                    <a href="#" class="add">[[lang/fields_create]]</a>
                </td>
                <td class="im-add">
                    <a href="#" class="add" title="[[lang/fields_add]]"><i class="fa fa-plus"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="cat" value="[[cat]]" />
    <input type="submit" name="save" value="[[lang/fields_sender]]" class="submit"/>
</form>
<script type="text/javascript">
    function renumberCustomFields() {
        $('#customfieldsForm table tbody tr').each(function(i,tr) {
            $(tr).find('input, select, textarea').each(function(k,elem) {
                var name = $(elem).attr('name').replace(/_\d+_/, '_'+(i)+'_');
                $(elem).attr('name', name);
            });
        });
    }
    $(function() {
        $('select[name$=_type]').change(function(e) {
            var val = $(e.target).val();
            var $ta = $(e.target).closest('td').find('textarea');
            if(val == 'dropdown') 
                $ta.css('display','inline'); 
            else 
                $ta.css('display','none');
        });
        $('a.deletefield').click(function(e) {
            $(e.target).closest('tr').remove();
            renumberCustomFields();
			return false;
        });
        $('a.add').click(function(e) {
            var $tr = $(e.target).closest('tbody').find('tr.hidden');
            $tr.before($tr.clone(true).removeClass('hidden').addClass('sortable'));
            renumberCustomFields();
			return false;
        });
        $('#customfieldsForm tbody').sortable({
            items:"tr.sortable", handle:'td',
            update:function(e,ui) { 
                renumberCustomFields(); 
            }
        });
        renumberCustomFields();
        [[js_element]]
    });
</script>
</div>
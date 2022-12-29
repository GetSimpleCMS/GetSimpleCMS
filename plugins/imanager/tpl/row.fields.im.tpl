<tr class="[[tr-class]]">
    <td class="im-drag">
        <i class="fa fa-hand-o-up"></i>
    </td>
    <td>
        [[field-details]]
    </td>
    <td>
        <input type="text" class="text" style="width:80px;padding:2px;" name="cf_[[i]]_key" value="[[key]]"/>
    </td>
    <td>
        <input type="text" class="text" style="width:140px;padding:2px;" name="cf_[[i]]_label" value="[[label]]"/>
    </td>
    <td>
        <select name="cf_[[i]]_type" class="text short" style="max-width:180px;" >
            <option value="text"[[selected-text]]>[[lang/text_field_value]]</option>
            <option value="longtext"[[selected-longtext]]>[[lang/longtext_field_value]]</option>
            <option value="dropdown"[[selected-dropdown]]>[[lang/dropdown_field_value]]</option>
            <option value="checkbox"[[selected-checkbox]]>[[lang/checkbox_field_value]]</option>
            <option value="editor"[[selected-editor]] >[[lang/editor_field_value]]</option>
            <option value="hidden"[[selected-hidden]]>[[lang/hidden_field_value]]</option>
            <option value="imageupload"[[selected-imageupload]]>[[lang/file_field_value]]</option>
			<option value="fileupload"[[selected-fileupload]]>[[lang/datafile_field_value]]</option>
            <option value="password"[[selected-password]]>[[lang/password_field_value]]</option>
            <option value="slug"[[selected-slug]]>[[lang/slug_field_value]]</option>
            <option value="chunk"[[selected-chunk]]>[[lang/chunk_field_value]]</option>
			<option value="datepicker"[[selected-datepicker]]>Datepicker</option>
            <option value="money"[[selected-money]]>[[lang/money_field_value]]</option>
        </select>
        <textarea class="text" style="[[area-display]]" rows="3" name="cf_[[i]]_options">[[area-options]]</textarea>
    </td>
    <td>
        <input type="text" class="text" style="width:100px;padding:2px;" name="cf_[[i]]_value" value="[[text-options]]"/>
    </td>
    <td class="im-del">
        <a href="#" class="deletefield" title="[[lang/delete]]"><i class="fa fa-times"></i></a>
        <input type="hidden" name="cf_[[i]]_id" value="[[id]]"/>
    </td>
</tr>

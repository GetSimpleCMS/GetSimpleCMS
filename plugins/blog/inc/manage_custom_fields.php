<?php
function show_custom_fields()
{
	$CustomFields = new CustomFields; 
	$customFields = $CustomFields->getCustomFields();
?>
<style> .hidden_main {display:none;} </style>
	<h3 class="floated"><?php echo i18n_r(BLOGFILE.'/MANAGE').' '.i18n_r(BLOGFILE.'/CUSTOM_FIELDS'); ?></h3>

	<p class="clear">
		<?php i18n(BLOGFILE.'/CUSTOMFIELDS_DESCR'); ?>
	</p>
	<form method="post" id="customfieldsForm">

 	<h3 style="font-size:15px;">Options Area (Options custom fields will be displayed in the "Post Options" section)</h3>
	<table id="editfields" class="edittable highlight options_cf">
		<thead>
			<tr>
				<th><?php i18n(BLOGFILE.'/NAME'); ?></th>
				<th><?php i18n(BLOGFILE.'/LABEL'); ?></th>
				<th style="width:100px;"><?php i18n(BLOGFILE.'/TYPE'); ?></th>
				<th><?php i18n(BLOGFILE.'/DEFAULT_VALUE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php 
 				$count_options = 0;
 				if(!empty($customFields['options']))
 				{
 					foreach($customFields['options'] as $customOptionsField)
	 				{
	 					get_custom_fields_list('sortable', $count_options, $customOptionsField, 'options');
	 					$count_options++;
	 				}
 				}
				 get_custom_fields_list('hidden', $count_options, null, 'options'); 
 				?>
			<tr>
				<td colspan="4"><a href="#" class="add_field">
					<?php i18n(BLOGFILE.'/ADD'); ?></a>
				</td>
				<td class="secondarylink">
					<a href="#" class="add_field" title="<?php i18n(BLOGFILE.'/ADD'); ?>">+</a>
				</td>
			</tr>
		</tbody>
	</table>
 	<h3 style="font-size:15px;">Main Area (Main custom fields will be under the "Post Options" section)</h3>
	<table id="editfields" class="edittable highlight main_cf">
		<thead>
			<tr>
				<th><?php i18n(BLOGFILE.'/NAME'); ?></th>
				<th><?php i18n(BLOGFILE.'/LABEL'); ?></th>
				<th style="width:100px;"><?php i18n(BLOGFILE.'/TYPE'); ?></th>
				<th><?php i18n(BLOGFILE.'/DEFAULT_VALUE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
 				<?php
 				$count_main = 0;
 				if(!empty($customFields['main']))
 				{
	 				foreach($customFields['main'] as $customMainField)
	 				{
	 					get_custom_fields_list('sortable', $count_main, $customMainField, 'main');
	 					$count_main++;
	 				}
 				}
				 get_custom_fields_list('hidden_main', $count_main, null, 'main'); 
			?>
			<tr>
				<td colspan="4"><a href="#" class="add_main_field">
					<?php i18n(BLOGFILE.'/ADD'); ?></a>
				</td>
				<td class="secondarylink">
					<a href="#" class="add_main_field" title="<?php i18n(BLOGFILE.'/ADD'); ?>">+</a>
				</td>
			</tr>
		</tbody>
	</table>
  <input type="submit" name="save_custom_fields" value="<?php i18n(BLOGFILE.'/SAVE'); ?>" class="submit"/>
	<script type="text/javascript" src="../plugins/blog/js/jquery-ui.sort.min.js"></script>
	<script>
		function renumberCustomFields() {
			$('.options_cf tbody tr').each(function(i,tr) {
				$(tr).find('input, select, textarea').each(function(k,elem) {
					var name = $(elem).attr('name').replace(/_\d+_/, '_'+(i)+'_');
					$(elem).attr('name', name);
				});
			});
		}
		function renumberMainCustomFields() {
			$('.main_cf tbody tr').each(function(i,tr) {
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
		      if (val == 'dropdown') $ta.css('display','inline'); else $ta.css('display','none');
		    });
		    $('a.delete_options').click(function(e) {
		      $(e.target).closest('tr').remove();
		      renumberCustomFields();
		    });
		    $('a.delete_main').click(function(e) {
		      $(e.target).closest('tr').remove();
		      renumberMainCustomFields();
		    });
			$('a.add_field').click(function(e) {
				var $tr = $(e.target).closest('tbody').find('tr.hidden');
				$tr.before($tr.clone(true).removeClass('hidden').addClass('sortable'));
				renumberCustomFields();
			});
			$('a.add_main_field').click(function(e) {
				var $tr = $(e.target).closest('tbody').find('tr.hidden_main');
				$tr.before($tr.clone(true).removeClass('hidden_main').addClass('sortable'));
				renumberMainCustomFields();
			});
			$('#customfieldsForm .main_cf tbody').sortable({
				items:"tr.sortable", handle:'td',
				update:function(e,ui) { renumberMainCustomFields(); }
			});
			$('#customfieldsForm .options_cf tbody').sortable({
				items:"tr.sortable", handle:'td',
				update:function(e,ui) { renumberCustomFields(); }
			});
			renumberCustomFields();
			renumberMainCustomFields();
		});
	</script>
<?php
}

function get_custom_fields_list($class='', $count=0, $customField=null, $area='options')
{
	$customFields  = new customFields;
	$area_un = $area;
	$reservedFields = $customFields->getReservedFields();
	if($customField == null)
	{
      $customField['key'] = (string) '';
      $customField['label'] = (string) '';
      $customField['type'] = (string) '';
      $customField['value'] = (string) '';
	}
	$area = $area.'_';
	$options = "\r\n";
	  if ($customField['type'] == 'dropdown' && count($customField['options']) > 0) 
	  {
	    foreach ($customField['options'] as $option) 
	    {
	    	$options .= $option . "\r\n";
	   	}
	  }
?>
      <tr class="<?php echo $class; ?>">
        <td>
        	<?php if(!in_array($customField['key'], $reservedFields)) { ?>
        		<input type="text" class="text" style="width:80px;padding:2px;" name="cf_<?php echo $area.$count; ?>_key" value="<?php echo $customField['key']; ?>"/>
        	<?php } else { ?>
        		<?php echo $customField['key']; ?>
        		<input type="hidden" name="cf_<?php echo $area.$count; ?>_key" value="<?php echo $customField['key']; ?>"/>
        	<?php } ?>
        </td>
        <td><input type="text" class="text" style="width:140px;padding:2px;" name="cf_<?php echo $area.$count; ?>_label" value="<?php echo $customField['label']; ?>"/></td>
        <td>
          <select name="cf_<?php echo $area.$count; ?>_type" class="text short" style="width:180px;padding:2px;" >
            <option value="text" <?php echo $customField['type']=='text' ? 'selected="selected"' : ''; ?>><?php i18n(BLOGFILE.'/TEXT_FIELD'); ?></option>
            <option value="textfull" <?php echo $customField['type']=='textfull' ? 'selected="selected"' : ''; ?>><?php i18n(BLOGFILE.'/LONG_TEXT_FIELD'); ?></option>
            <option value="dropdown" <?php echo $customField['type']=='dropdown' ? 'selected="selected"' : ''; ?>><?php i18n(BLOGFILE.'/DROPDOWN_BOX'); ?></option>
            <option value="checkbox" <?php echo $customField['type']=='checkbox' ? 'selected="selected"' : ''; ?>><?php i18n(BLOGFILE.'/CHECKBOX'); ?></option>
            <option value="textarea" <?php echo $customField['type']=='textarea' ? 'selected="selected"' : ''; ?>><?php i18n(BLOGFILE.'/WYSIWYG_EDITOR'); ?></option>
            <option value="title" <?php echo $customField['type']=='title' ? 'selected="selected"' : ''; ?>><?php i18n(BLOGFILE.'/TITLE'); ?></option>
            <option value="hidden">Hidden Field</option>
          </select>
          <textarea class="text" style="width:170px;height:50px;padding:2px;<?php echo $customField['type'] != 'dropdown' ? 'display:none' : ''; ?>" name="cf_<?php echo $area.$count; ?>_options"><?php echo $options; ?></textarea>
        </td>
        <td><input type="text" class="text" style="width:100px;padding:2px;" name="cf_<?php echo $area.$count; ?>_value" value="<?php echo $customField['value']; ?>"/></td>
        <td class="delete">
        	<?php if(!in_array($customField['key'], $reservedFields)) { ?>
        		<a href="#" class="delete<?php echo '_'.$area_un; ?>" title="">X</a>
        	<?php } ?>
        </td>
      </tr>
      <?php
}

function displayCustomFields($area='options')
{
	global $SITEURL;
	if(isset($_GET['edit_post']))
	{
		$id = $_GET['edit_post'];
		$file = BLOGPOSTSFOLDER . $id . '.xml';
		$data_edit = getXML($file);
	}

	// SimpleXML to read from
	$CustomFields = new customFields; 
	$customFields = $CustomFields->getCustomFields();
	$customFieldsArea = $customFields[$area];
	if (!$customFields || count($customFields) <= 0) return;
	?>
		<?php
		// Editor settings (copied from edit.php)
		if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = 'en'; }
		if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
		if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
		if ($EDTOOL == 'advanced') 
		{
			$toolbar = "
			['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
			'/',
			['Styles','Format','Font','FontSize']
			";
		} 
		elseif ($EDTOOL == 'basic') 
		{
			$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
		} 
		else {
			$toolbar = GSEDITORTOOL;
		}

		// Editor settings end
		$col = 0;
		$uploader_col = 0;

		foreach ($customFieldsArea as $the) 
		{
			$key = strtolower($the['key']);
			$label = $the['label'];
			$type = $the['type'];

			if (isset($_GET['edit_post']) && $_GET['edit_post'] != "") 
			{
				$value = $data_edit->$key;
			}
			else 
			{
				$value = ""; 
			}
			if ($col == 0) 
			{
				//echo '<div class="leftopt">';
			} 
			elseif($type == 'textfull' || $type == 'textarea') 
			{
				//echo '</div>';
			}
			if($col % 2)
			{
				$meta_class = "even_meta";
			}
			else
			{
				$meta_class="odd_meta";
			}
			switch($type)
			{
				case 'textfull': // draw a full width TextBox
					echo '<p style="width:100%;">';
					if($label != '') { echo '<label>'.$label.':</label>'; }
					echo '<input class="text" type="text" style="width:533px;" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" /></p>';
					$col += 2;
				break; 

				case 'dropdown':
					echo '<p class="'.$meta_class.'">';
					if($label != '') { echo '<label>'.$label.':</label>'; }
					echo '<select id="post-'.$key.'" name="post-'.$key.'" class="text shorts">';
					if($key == 'category')
					{
						category_dropdown();
					}
					else
					{
						foreach ($the['options'] as $option) 
						{
							$attrs = $value == $option ? ' selected="selected"' : '';
							echo '<option value="'.$option.'" '.$attrs.'>'.$option.'</option>';
						}
					}
					echo '</select></p>';
					$col++;
				break;

				case 'checkbox':
					if($value != '')
					{
						$checked = 'checked="checked"';
					}
					else
					{
						$checked = '';
					}
					if($label != '') { echo '<p class="'.$meta_class.'"><label>'.$label.'?</label>'; }
					echo '<input type="checkbox" class="checkp" id="post-'.$key.'" name="post-'.$key.'" value="on" '.$checked.'/></p><div style="clear:both;"></div>';
					$col++;
				break;

				case "textarea":
					echo '<p style="width:100%;">';
					if($label != '') { echo '<label>'.$label.':</label>'; }
					echo '<textarea id="post-'.$key.'" name="post-'.$key.'" style="width:635px !important; height:420px;line-height:18px;text-align:left;	color:#333;
					border:1px solid #aaa;">'.$value.'</textarea></p>';
					?>
					<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
					<script type="text/javascript">
					// missing border around text area, too much padding on left side, ...
					$(function() {
						CKEDITOR.replace( 'post-<?php echo $key; ?>', {
							skin : 'getsimple',
							forcePasteAsPlainText : false,
							language : '<?php echo $EDLANG; ?>',
							defaultLanguage : '<?php echo $EDLANG; ?>',
							entities : false,
							uiColor : '#FFFFFF',
							height: '200px',
							baseHref : '<?php echo $SITEURL; ?>',
							toolbar : [ <?php echo $toolbar; ?> ],
							<?php echo $EDOPTIONS; ?>
							filebrowserBrowseUrl : 'filebrowser.php?type=all',
							filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
							filebrowserWindowWidth : '730',
							filebrowserWindowHeight : '500'
						})
					});
					</script>
					<?php
					$col +=2;
				break;
				case 'text':
					default:
					echo '<p class="'.$meta_class.'">';
					if($label != '') { echo '<label>'.$label.':</label>'; }
					echo '<input class="text short" type="text" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" /></p>';
					$col++;
				break;
				case "hidden":
					echo '';
					echo '<input class="'.$meta_class.'" class="" type="hidden" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" />'; 
				break;
				case "title":
				 	echo '<p>';
					if($label != '') { echo '<label>'.$label.':</label>'; }
					echo '<input class="text title" name="post-'.$key.'" id="post-'.$key.'" type="text" value="'.$value.'" /></p>';
			}

			if ($uploader_col >= 3) 
				{
				echo "</tr>";
				$uploader_col = 0;
			}
			if ($col >= 2) 
			{
				echo "</tr>";
				$col = 0;
			}
		}
	echo '<div style="clear:both;"></div>';
}
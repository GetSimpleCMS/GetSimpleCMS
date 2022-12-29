<?php
  function items_customfields_invalid_name() {
    $stdfields = array();
    $names = array();
    for ($i=0; isset($_POST['cf_'.$i.'_key']); $i++) {
      if (in_array($_POST['cf_'.$i.'_key'], $stdfields)) $names[] = $_POST['cf_'.$i.'_key'];
    }
    return count($names) > 0 ? $names : null;
  }


  function items_customfields_save_them() {
    if (!copy(GSDATAOTHERPATH . IM_CUSTOMFIELDS_FILE, GSBACKUPSPATH . 'other/' . IM_CUSTOMFIELDS_FILE)) return false;
 		$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
    for ($i=0; isset($_POST['cf_'.$i.'_key']); $i++) {
      if ($_POST['cf_'.$i.'_key']) {
        $item = $data->addChild('item');
        $item->addChild('desc')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_key']), ENT_QUOTES));
        $item->addChild('label')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_label']), ENT_QUOTES));
        $item->addChild('type')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_type']), ENT_QUOTES));
        if ($_POST['cf_'.$i.'_value']) {
          $item->addChild('value')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_value']), ENT_QUOTES));
        }
        if ($_POST['cf_'.$i.'_options']) {
          $options = preg_split("/\r?\n/", rtrim(stripslashes($_POST['cf_'.$i.'_options'])));
          foreach ($options as $option) {
            $item->addChild('option')->addCData(htmlspecialchars($option, ENT_QUOTES));
          }
        }
      }
    }
 		XMLsave($data, GSDATAOTHERPATH . IM_CUSTOMFIELDS_FILE);
    return true;
  }

  function items_customfields_undo() {
    return copy(GSBACKUPSPATH . 'other/' . IM_CUSTOMFIELDS_FILE, GSDATAOTHERPATH . IM_CUSTOMFIELDS_FILE);
  }

  if (isset($_GET['undo'])) {
    if (items_customfields_undo()) {
      $msg = i18n_r('items/UNDO_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('items/UNDO_FAILURE');
    }
    $defs = im_customfield_def();
  } else if (isset($_POST['save'])) {
    $names = items_customfields_invalid_name();
    if (!$names && items_customfields_save_them()) {
      $msg = i18n_r('items/SAVE_SUCCESS').' <a href="load.php?id=items&undo">' . i18n_r('UNDO') . '</a>';
      $success = true;
      $defs = im_customfield_def();
    } else {
      if ($names) {
        $msg = i18n_r('items/SAVE_INVALID').' '.implode(', ', $names);
      } else {
        $msg = i18n_r('items/SAVE_FAILURE');
      }
      $defs = array();
      for ($i=0; isset($_POST['cf_'.$i.'_key']); $i++) {
        $cf = array();
        $cf['key'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_key']), ENT_QUOTES);
        $cf['label'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_label']), ENT_QUOTES);
        $cf['type'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_type']), ENT_QUOTES);
        $cf['value'] = htmlspecialchars(stripslashes($_POST['cf_'.$i.'_value']), ENT_QUOTES);
        $cf['options'] = preg_split("/\r?\n/", rtrim(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_value']), ENT_QUOTES)));
        $defs[] = $cf;
      }
      array_pop($defs); // remove the last hidden line
    }
  } else {
    $defs = im_customfield_def();
  }
?>
<label><?php i18n('items/CUSTOMFIELDS_TITLE'); ?></label>
<p class="clear"><?php i18n('items/CUSTOMFIELDS_DESCR'); ?></p>
<p><?php i18n('items/FUNCTIONS_DESCR'); ?></p>
<ul>
  <li><?php highlight_string('<?php getTheField(\'myname\'); ?>'); ?> <?php i18n('items/GET_CUSTOM_FIELD_DESCR'); ?></li>
  <li><?php highlight_string('<?php returnTheField(\'myname\'); ?>'); ?> <?php i18n('items/RETURN_CUSTOM_FIELD_DESCR'); ?></li>
</ul>
<p><?php i18n('items/USAGE_DESCR'); ?></p>
<form method="post" id="customfieldsForm">
  <table id="editfields" class="edittable highlight">
    <thead>
      <tr>
        <th><?php i18n('items/NAME'); ?></th>
        <th><?php i18n('items/LABEL'); ?></th>
        <th style="width:100px;"><?php i18n('items/TYPE'); ?></th>
        <th><?php i18n('items/DEFAULT_VALUE'); ?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
<?php
  $i = 0;
  if (count($defs) > 0) foreach ($defs as $def) {
    items_customfields_confline($i, $def, 'sortable');
    $i++;
  }
  items_customfields_confline($i, array(), 'hidden');
?>
      <tr>
        <td colspan="4"><a href="#" class="add"><?php i18n('items/ADD'); ?></a></td>
        <td class="secondarylink"><a href="#" class="add" title="<?php i18n('items/ADD'); ?>">+</a></td>
      </tr>
    </tbody>
  </table>
  <input type="submit" name="save" value="<?php i18n('items/SAVE'); ?>" class="submit"/>
</form>
<script type="text/javascript" src="../plugins/items/js/jquery-ui.sort.min.js"></script>
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
      if (val == 'dropdown') $ta.css('display','inline'); else $ta.css('display','none');
    });
    $('a.delete').click(function(e) {
      $(e.target).closest('tr').remove();
      renumberCustomFields();
    });
    $('a.add').click(function(e) {
      var $tr = $(e.target).closest('tbody').find('tr.hidden');
      $tr.before($tr.clone(true).removeClass('hidden').addClass('sortable'));
      renumberCustomFields();
    });
    $('#customfieldsForm tbody').sortable({
      items:"tr.sortable", handle:'td',
      update:function(e,ui) { renumberCustomFields(); }
    });
    renumberCustomFields();
<?php if (@$msg) { ?>
    $('div.bodycontent').before('<div class="updated" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
<?php } ?>
  });
</script>
<?php

function items_customfields_confline($i, $def, $class='') {
  $isdropdown = @$def['type'] == 'dropdown';
  $options = "\r\n";
  if ($isdropdown && count($def['options']) > 0) {
    foreach ($def['options'] as $option) $options .= $option . "\r\n";
  }
?>
      <tr class="<?php echo $class; ?>">
        <td><input type="text" class="text" style="width:80px;padding:2px;" name="cf_<?php echo $i; ?>_key" value="<?php echo @$def['key'];?>"/></td>
        <td><input type="text" class="text" style="width:140px;padding:2px;" name="cf_<?php echo $i; ?>_label" value="<?php echo @$def['label'];?>"/></td>
        <td>
          <select name="cf_<?php echo $i; ?>_type" class="text short" style="width:180px;padding:2px;" >
            <option value="text" <?php echo @$def['type']=='text' ? 'selected="selected"' : ''; ?> ><?php i18n('items/TEXT_FIELD'); ?></option>
            <option value="textfull" <?php echo @$def['type']=='textfull' ? 'selected="selected"' : ''; ?> ><?php i18n('items/LONG_TEXT_FIELD'); ?></option>
            <option value="dropdown" <?php echo @$def['type']=='dropdown' ? 'selected="selected"' : ''; ?> ><?php i18n('items/DROPDOWN_BOX'); ?></option>
            <option value="checkbox" <?php echo @$def['type']=='checkbox' ? 'selected="selected"' : ''; ?> ><?php i18n('items/CHECKBOX'); ?></option>
            <option value="textarea" <?php echo @$def['type']=='textarea' ? 'selected="selected"' : ''; ?> ><?php i18n('items/WYSIWYG_EDITOR'); ?></option>
            <option value="hidden" <?php echo @$def['type']=='hidden' ? 'selected="selected"' : ''; ?> >Hidden Field</option>
            <option value="uploader" <?php echo @$def['type']=='uploader' ? 'selected="selected"' : ''; ?> >Image Uploader</option> 
          </select>
          <textarea class="text" style="width:170px;height:50px;padding:2px;<?php echo !$isdropdown ? 'display:none' : ''; ?>" name="cf_<?php echo $i; ?>_options"><?php echo $options; ?></textarea>
        </td>
        <td><input type="text" class="text" style="width:100px;padding:2px;" name="cf_<?php echo $i; ?>_value" value="<?php echo @$def['value'];?>"/></td>
        <td class="delete"><a href="#" class="delete" title="<?php i18n('items/DELETE'); ?>">X</a></td>
      </tr>
<?php
}



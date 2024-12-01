<?php
  function i18n_customfields_invalid_names() {
    $stdfields = array('pubDate','title','url','meta','metad','menu','menuStatus','menuOrder',
                        'template','parent','content','private','creDate','user');
    $names = array(); 
    for ($i=0; isset($_POST['cf_'.$i.'_key']); $i++) {
      if (in_array($_POST['cf_'.$i.'_key'], $stdfields)) $names[] = $_POST['cf_'.$i.'_key'];
    }
    return count($names) > 0 ? $names : null;
  }


  function i18n_customfields_save_them() {
    if (file_exists(GSDATAOTHERPATH . I18N_CUSTOMFIELDS_FILE)) {
      if (!@copy(GSDATAOTHERPATH . I18N_CUSTOMFIELDS_FILE, GSBACKUPSPATH . 'other/' . I18N_CUSTOMFIELDS_FILE)) return false;
    }
 		$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
    for ($i=0; isset($_POST['cf_'.$i.'_key']); $i++) {
      if ($_POST['cf_'.$i.'_key']) {
        $item = $data->addChild('item');
        $item->addChild('desc')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_key']), ENT_QUOTES));
        $item->addChild('label')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_label']), ENT_QUOTES));
        $item->addChild('type')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_type']), ENT_QUOTES));
        if (@$_POST['cf_'.$i.'_value']) {
          $item->addChild('value')->addCData(htmlspecialchars(stripslashes($_POST['cf_'.$i.'_value']), ENT_QUOTES));
        }
        if (@$_POST['cf_'.$i.'_options']) {
          $options = preg_split("/\r?\n/", rtrim(stripslashes($_POST['cf_'.$i.'_options'])));
          foreach ($options as $option) {
            $item->addChild('option')->addCData(htmlspecialchars($option, ENT_QUOTES));
          } 
        }
        if (@$_POST['cf_'.$i.'_index']) {
          $item->addChild('index')->addCData(1);
        }
      }
    }
 		XMLsave($data, GSDATAOTHERPATH . I18N_CUSTOMFIELDS_FILE);
    return true;
  }

  function i18n_customfields_undo() {
    return copy(GSBACKUPSPATH . 'other/' . I18N_CUSTOMFIELDS_FILE, GSDATAOTHERPATH . I18N_CUSTOMFIELDS_FILE);
  }

  if (isset($_GET['undo']) && !isset($_POST['save'])) {
    if (i18n_customfields_undo()) {
      $msg = i18n_r('i18n_customfields/UNDO_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('i18n_customfields/UNDO_FAILURE');
    }
    $defs = i18n_customfield_defs();
  } else if (isset($_POST['save'])) {
    $names = i18n_customfields_invalid_names();
    if (!$names && i18n_customfields_save_them()) {
      $msg = i18n_r('i18n_customfields/SAVE_SUCCESS');
      if (file_exists(GSBACKUPSPATH . 'other/' . I18N_CUSTOMFIELDS_FILE)) {
        $msg .= ' <a href="load.php?id=i18n_customfields&undo">' . i18n_r('UNDO') . '</a>';
      }
      $success = true;
      $defs = i18n_customfield_defs();
    } else {
      if ($names) {
        $msg = i18n_r('i18n_customfields/SAVE_INVALID').' '.implode(', ', $names);
      } else {
        $msg = i18n_r('i18n_customfields/SAVE_FAILURE');
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
    $defs = i18n_customfield_defs();
  }
  $issearch = defined('I18N_ACTION_INDEX');
?>
<label><?php i18n('i18n_customfields/CUSTOMFIELDS_TITLE'); ?></label>
<p class="clear"><?php i18n('i18n_customfields/CUSTOMFIELDS_DESCR'); ?></p>
<p><?php i18n('i18n_customfields/FUNCTIONS_DESCR'); ?></p>
<ul>
  <li><code>&lt;?php get_custom_field('myname'); ?&gt;</code> <?php i18n('i18n_customfields/GET_CUSTOM_FIELD_DESCR'); ?></li>  
  <li><code>return_custom_field('myname')</code> <?php i18n('i18n_customfields/RETURN_CUSTOM_FIELD_DESCR'); ?></li>
</ul>
<p><?php i18n('i18n_customfields/USAGE_DESCR'); ?></p>
<form method="post" id="customfieldsForm">
  <table id="editfields" class="edittable highlight">
    <thead>
      <tr>
        <th><?php i18n('i18n_customfields/NAME'); ?></th>
        <th><?php i18n('i18n_customfields/LABEL'); ?></th>
        <th style="width:100px;"><?php i18n('i18n_customfields/TYPE'); ?></th>
        <th><?php i18n('i18n_customfields/DEFAULT_VALUE'); ?></th>
<?php if ($issearch) { ?>
        <th><?php i18n('i18n_customfields/INDEX'); ?></th>
<?php } ?>
        <th></th>
      </tr>
    </thead>
    <tbody>
<?php
  $i = 0; 
  if (count($defs) > 0) foreach ($defs as $def) {
    i18n_customfields_confline($i, $def, 'sortable', $issearch);    
    $i++;
  }
  i18n_customfields_confline($i, array(), 'hidden', $issearch); 
?> 
      <tr>
        <td colspan="5"><a href="#" class="add"><?php i18n('i18n_customfields/ADD'); ?></a></td>
        <td class="secondarylink"><a href="#" class="add" title="<?php i18n('i18n_customfields/ADD'); ?>">+</a></td>
      </tr>
    </tbody>
  </table>
  <input type="submit" name="save" value="<?php i18n('i18n_customfields/SAVE'); ?>" class="submit"/>
</form>
<script type="text/javascript" src="../plugins/i18n_customfields/js/jquery-ui.sort.min.js"></script>
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
<?php if ($issearch) { ?>
      var $cb = $(e.target).closest('tr').find('input[type=checkbox]');
      if (val == 'text' || val == 'textfull' || val == 'dropdown' || val == 'textarea' || val == 'checkbox') {
        $cb.show();
      } else {
        $cb.attr('checked',false).hide();
      }
<?php } ?>
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

function i18n_customfields_confline($i, $def, $class='', $issearch) {
  $isdropdown = @$def['type'] == 'dropdown';
  $indexable = !@$def['type'] || in_array(@$def['type'],array('text','textfull','dropdown','textarea', 'checkbox'));
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
            <option value="text" <?php echo @$def['type']=='text' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/TEXT_FIELD'); ?></option>
            <option value="textfull" <?php echo @$def['type']=='textfull' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/LONG_TEXT_FIELD'); ?></option>
            <option value="dropdown" <?php echo @$def['type']=='dropdown' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/DROPDOWN_BOX'); ?></option>
            <option value="checkbox" <?php echo @$def['type']=='checkbox' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/CHECKBOX'); ?></option>
            <option value="textarea" <?php echo @$def['type']=='textarea' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/WYSIWYG_EDITOR'); ?></option>
            <option value="image" <?php echo @$def['type']=='image' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/IMAGE'); ?></option>
            <option value="file" <?php echo @$def['type']=='file' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/FILE'); ?></option>
            <option value="link" <?php echo @$def['type']=='link' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_customfields/LINK'); ?></option>
          </select>
          <textarea class="text" style="width:170px;height:50px;padding:2px;<?php echo !$isdropdown ? 'display:none' : ''; ?>" name="cf_<?php echo $i; ?>_options"><?php echo $options; ?></textarea> 
        </td>
        <td><input type="text" class="text" style="width:100px;padding:2px;" name="cf_<?php echo $i; ?>_value" value="<?php echo @$def['value'];?>"/></td>
<?php if ($issearch) { ?>
        <td><input type="checkbox" name="cf_<?php echo $i; ?>_index" <?php echo @$def['index'] ? 'checked="checked"' : ''; ?> <?php echo !$indexable ? 'style="display:none"' : ''; ?> /></td>
<?php } ?>
        <td class="delete"><a href="#" class="delete" title="<?php i18n('i18n_customfields/DELETE'); ?>">X</a></td>
      </tr>
<?php 
}



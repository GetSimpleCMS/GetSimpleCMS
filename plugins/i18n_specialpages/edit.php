<?php
  global $SITEURL,$TEMPLATE;
  global $data_edit; // SimpleXML to read from

  $id = @$_GET['id'];
  $isI18N = function_exists('return_i18n_languages');
  // determine special page type
  $spname = null;
  if (isset($_GET['special'])) {
    // create a special page or change the type of page
    $spname = $_GET['special'];
  } else if (isset($data_edit) && isset($data_edit->special) && (string) $data_edit->special) {
    // edit a special page
    $spname = (string) $data_edit->special;
  } else if ($isI18N && isset($_GET['newid']) && strpos($_GET['newid'],'_') > 0) {
    // this language page should be the same as the default language page
    $id_base = substr($_GET['newid'], 0, strrpos($_GET['newid'],'_'));
    $data_base = getXML(GSDATAPAGESPATH . $id_base . '.xml');
    if (isset($data_base) && isset($data_base->special) && (string) $data_base->special) {
      $spname = (string) $data_base->special;
    }
  }
  if ($spname) {
    $spdef = I18nSpecialPages::getSettings($spname);
  }

  $creDate = @$data_edit->creDate ? (string) $data_edit->creDate : (string) @$data_edit->pubDate;
  $defs = null;
  if (@$spdef) {
    $defs = @$spdef['fields'];
    if (!$id && @$spdef['defaultcontent']) {
      global $content;
      $content = $spdef['defaultcontent'];
    }
  }
  echo '<input type="hidden" name="special-creDate" value="'.htmlspecialchars($creDate).'"/>';
  echo '<input type="hidden" name="post-special" value="'.htmlspecialchars($spname).'"/>';
  if (@$defs && count($defs) > 0) {
    echo '<table class="formtable specialtable" style="clear:both"><tbody>';
    $col = 0; $i = 0;
    foreach ($defs as $def) {
      $i++;
      $key = strtolower($def['name']);
      $label = $def['label'];
      $type = $def['type'];
      $value = htmlspecialchars($id ? (isset($data_edit->$key) ? $data_edit->$key : '') : (isset($def['value']) ? $def['value'] : ''), ENT_QUOTES);
      if ($col == 0) {
        echo '<tr>';
      } else if ($type == 'textfull' || $type == 'textarea' || $type == 'image' || $type == 'link' || $type == 'wysiwyg' || $type == 'file') {
        echo '<td></td></tr><tr>';
      }
      switch ($type){
        case 'textfull': // draw a full width TextBox
          echo '<td colspan="2"><b>'.$label.':</b><br />';
          echo '<input class="text" type="text" style="width:602px;" id="post-sp-'.$key.'" name="post-sp-'.$key.'" value="'.$value.'" /></td>'; 
          $col += 2;
          break; 
        case 'dropdown':
          echo '<td><b>'.$label.':</b><br />';
          echo '<select id="post-sp-'.$key.'" name="post-sp-'.$key.'" class="text" style="width:295px">';
          foreach ($def['options'] as $option) {
            $attrs = $value == $option ? ' selected="selected"' : '';
            echo '<option'.$attrs.'>'.$option.'</option>';
          }
          echo '</select></td>';
          $col++;
          break;
        case 'checkbox':
          echo '<td><b>'.$label.'?</b> &nbsp;&nbsp;&nbsp;';
          echo '<input type="checkbox" id="post-sp-'.$key.'" name="post-sp-'.$key.'" value="on" '.($value ? 'checked="checked"' : '').' style="width:auto;"/></td>'; 
          $col++;
          break; 
        case "textarea":
          echo '<td colspan="2"><b>'.$label.':</b><br />';
          echo '<textarea id="post-sp-'.$key.'" name="post-sp-'.$key.'" style="width:602px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></td>';
          $col +=2;
          break;
        case "wysiwyg":
          echo '<td colspan="2"><b>'.$label.':</b><br />';
          echo '<textarea id="post-sp-'.$key.'" name="post-sp-'.$key.'" style="width:602px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></td>';
          ?>
          <script type="text/javascript">
            $(function() {
              <?php I18nSpecialPagesBackend::outputCKEditorJS('post-sp-'.$key, 'editor_'.$i); ?>
            });
          </script>
          <?php
          $col +=2;
          break;
        case 'link':
          $w = 500;
          echo '<td colspan="2"><b>'.$label.':</b><br />';
          echo '<input class="text" type="text" style="width:'.$w.'px;" id="post-sp-'.$key.'" name="post-sp-'.$key.'" value="'.$value.'" />';
          echo ' <span class="edit-nav"><a id="browse-'.$key.'" href="#">'.i18n_r('i18n_specialpages/BROWSE_PAGES').'</a></span>';
          echo '</td>'; 
          $col += 2;
          ?>
          <script type="text/javascript">
            function fill_sp_<?php echo $i; ?>(url) {
              $('#post-sp-<?php echo $key; ?>').val(url);
            }
            $(function() { 
              $('#browse-<?php echo $key; ?>').click(function(e) {
                e.preventDefault();
                window.open('<?php echo $SITEURL; ?>plugins/i18n_specialpages/browser/pagebrowser.php?func=fill_sp_<?php echo $i; ?>&i18n=<?php echo $isI18N; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
              });
            });
          </script>
          <?php
          break; 
        case 'image':
        case 'file':
          $w = 500;
          echo '<td colspan="2"><b>'.$label.':</b><br />';
          echo '<input class="text" type="text" style="width:'.$w.'px;" id="post-sp-'.$key.'" name="post-sp-'.$key.'" value="'.$value.'" />';
          echo ' <span class="edit-nav"><a id="browse-'.$key.'" href="#">'.($type=='image' ? i18n_r('i18n_specialpages/BROWSE_IMAGES') : i18n_r('i18n_specialpages/BROWSE_FILES')).'</a></span>';
          echo '</td>'; 
          $col += 2;
          ?>
          <script type="text/javascript">
            function fill_sp_<?php echo $i; ?>(url) {
              $('#post-sp-<?php echo $key; ?>').val(url);
            }
            $(function() { 
              $('#browse-<?php echo $key; ?>').click(function(e) {
                e.preventDefault();
                window.open('<?php echo $SITEURL; ?>plugins/i18n_specialpages/browser/filebrowser.php?func=fill_sp_<?php echo $i; ?>&type=<?php echo $type=='image' ?'images' : ''; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
              });
            });
          </script>
          <?php
          break; 
        case 'text':
        default:
          echo '<td><b>'.$label.':</b><br />';
          echo '<input class="text short" type="text" style="width:295px;" id="post-sp-'.$key.'" name="post-sp-'.$key.'" value="'.$value.'" /></td>'; 
          $col++;
          break; 
      }
      if ($col >= 2) {
        echo "</tr>";
        $col = 0;
      }
    }
    if ($col == 1) echo "<td></td></tr>\r\n";
    echo "</tbody></table>\r\n";
  }
  echo '<script type="text/javascript">';
  echo "$(function() {\r\n";
  if (@$spdef['slug']) {
    if (!$id && !@$_GET['newid']) {
      $slug = strftime($spdef['slug']);
      echo "$('#post-id').val(".json_encode($slug).").closest('p').hide();\r\n";
    } else {
      echo "$('#post-id').closest('p').hide();\r\n";
    }
  }
  if (@$spdef['parent']) {
    $parent = $spdef['parent'];
    echo "if ($('#post-parent').val(".json_encode($parent).").val() == ".json_encode($parent).") $('#post-parent').closest('p').hide();\r\n"; 
  }
  if (@$spdef['tags'] && !$id) {
    $tags = $spdef['tags'];
    echo "$('#post-metak').val(".json_encode($tags).");\r\n";
  }
  if (@$spdef['template']) {
    $template = $spdef['template'];
    echo "if ($('#post-template').val(".json_encode($template).").val() == ".json_encode($template).") $('#post-template').closest('p').hide();\r\n"; 
  }
  $m = @$spdef['menu'];
  if (($m || $m == '0') && $isI18N) {
    if (!$id && $m == 'f') {
      echo "$('#post-menu-enable').attr('checked','checked');\r\n";
      echo "$('#post-menu-order option:first').attr('selected','selected');\r\n";
    } else if (!$id && $m == 'l') {
      echo "$('#post-menu-enable').attr('checked','checked');\r\n";
      echo "$('#post-menu-order option:last').attr('selected','selected');\r\n";
    }
    echo "$('#post-menu-enable').closest('p').hide();\r\n";
    if ($m == '0') {
      echo "$('#post-menu').closest('div').hide();\r\n";
    } else {
      echo "$('#menu-items').show();\r\n";
      echo "$('#post-menu-order').prev().add('#post-menu-order').hide();\r\n";
    }
  } else if ($m == '0') {
    echo "$('#post-menu-enable').closest('p').hide();\r\n";
    echo "$('#post-menu-order').closest('div').hide();\r\n";
  }
  echo "})\r\n";
  echo "</script>\r\n";

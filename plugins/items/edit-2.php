<?php
  global $SITEURL;
  global $data_edit;
  global $item_data;
  $id = @$_GET['edit'];
  $file = ITEMDATA . $id . '.xml';
  $data_edit = @getXML($file);
   // SimpleXML to read from
  $list_fields = im_customfield_def();
  if (!$list_fields || count($list_fields) <= 0) return;
	echo '<table><tr class="user_sub_tr"><td><h2 style="padding:0px;margin:0px;">'.IMTITLE.' Information: </h2></td></tr>';
  // Editor settings (copied from edit.php)
  if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = 'en'; }
  if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
  if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
  if ($EDTOOL == 'advanced') {
    $toolbar = "
		    ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
        '/',
        ['Styles','Format','Font','FontSize']
    ";
  } elseif ($EDTOOL == 'basic') {
    $toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
  } else {
    $toolbar = GSEDITORTOOL;
  }
  // Editor settings end
  $col = 0;
  $uploader_col = 0;
  foreach ($list_fields as $the) {
    $key = strtolower($the['key']);
    $label = $the['label'];
		$type = $the['type'];
    if ($_GET['edit'] != "") {$value = $data_edit->$key;}
    else {$value = ""; }
		if ($col == 0) {
      echo '<tr class="user_sub_tr">';
    } else if ($type == 'textfull' || $type == 'textarea') {
      echo '<td></td></tr><tr class="user_sub_tr">';
    }
		switch ($type){
			case 'textfull': // draw a full width TextBox
				echo '<td colspan="2"><b>'.$label.':</b><br />';
				echo '<input class="text" type="text" style="width:533px;" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" /></td>';
        $col += 2;
			  break; 
			case 'dropdown':
				echo '<td><b>'.$label.':</b><br />';
				echo '<select id="post-'.$key.'" name="post-'.$key.'" class="text shorts">';
        foreach ($the['options'] as $option) {
          $attrs = $value == $option ? ' selected="selected"' : '';
          echo '<option'.$attrs.'>'.$option.'</option>';
        }
        echo '</select></td>';
        $col++;
				break;
      case 'checkbox':
				echo '<td><b>'.$label.'?</b> &nbsp;&nbsp;&nbsp;';
        echo '<input type="checkbox" class="checkp" id="post-'.$key.'" name="post-'.$key.'" value="on" '.($value ? 'checked="checked"' : '').'/></td>';
        $col++;
  			break;
		
         case 'uploader': // draw a full width TextBox\
				echo '   <div class="uploader_container"> 
    <div id="file-uploader-'.$key.'"> 
        <noscript> 
            <p>Please enable JavaScript to use file uploader.</p>
            <!-- or put a simple form for upload here -->
        </noscript> 
    </div> 
         <script> 
    var uploader = new qq.FileUploader({
        // pass the dom node (ex. $(selector)[0] for jQuery users)
        element: document.getElementById(\'file-uploader-'.$key.'\'),
        // path to server-side upload script
        action: \'../plugins/items/uploader/server/php.php\',
        onSubmit: function(id, fileName){
        $(\'#post-'.$key.'\').attr(\'value\', fileName);
        }
    });
 
       // create uploader as soon as the DOM is ready
        // dont wait for the window to load
        window.onload = createUploader;
    </script><input type="hidden" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" /></div>';
                break;	
      case "textarea":
        echo '</table><p><b>'.$label.':</b><br />';
        echo '<textarea id="post-'.$key.'" name="post-'.$key.'" style="width:635px !important; height:420px;line-height:18px;text-align:left;	color:#333;
	border:1px solid #aaa;">'.$value.'</textarea></p><table><tr>';
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
	        toolbar : [ <?php echo $toolbar; ?> ]
			    <?php echo $EDOPTIONS; ?>
    })
  });
</script>
<?php
        $col +=2;
        break;
			case 'text':
      default:
				echo '<td><b>'.$label.':</b><br />';
				echo '<input class="text shorts" type="text" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" /></td>';
        $col++;
  			break;
        case "hidden":
        echo '';
        echo '<input class="" type="hidden" id="post-'.$key.'" name="post-'.$key.'" value="'.$value.'" /></tr>'; 
		}
		
		if ($uploader_col >= 3) {
      echo "</tr>";
      $uploader_col = 0;
    }
		if ($col >= 2) {
      echo "</tr>";
      $col = 0;
    }
	}
  if ($col == 1) echo '<td></td></tr>';
   echo "</table>";

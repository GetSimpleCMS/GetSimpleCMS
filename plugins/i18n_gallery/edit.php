<?php
function i18n_gallery_from_request($languages) {
  $gallery = array('items' => array());
  $gallery['title'] = @$_POST['post-title'];
  if (count($languages) > 0) foreach ($languages as $language) {
    if (@$_POST['post-title_'.$language]) $gallery['title_'.$language] = $_POST['post-title_'.$language];
  }
  $gallery['name'] = @$_POST['post-name'];
  $gallery['type'] = $type = @$_POST['post-type'];
  foreach ($_POST as $key => $value) {
    if (substr($key,0,strlen($type)+1) == $type.'-') $gallery[substr($key,strlen($type)+1)] = $value;
    if (substr($key,0,6) == 'extra-') $gallery[$key] = $value;
  }
  for ($i=0; isset($_POST['post-item_'.$i.'_filename']); $i++) {
    $filename = $_POST['post-item_'.$i.'_filename'];
    list($width,$height) = @getimagesize(GSDATAUPLOADPATH.$filename);
		$ss = @stat(GSDATAUPLOADPATH.$filename);
    $gal = array(
      'filename' => $filename,
      'title' => @$_POST['post-item_'.$i.'_title'],
      'tags' => @$_POST['post-item_'.$i.'_tags'],
      'description' => @$_POST['post-item_'.$i.'_description'],
      'longitude' => @$_POST['post-item_'.$i.'_longitude'],
      'latitude' => @$_POST['post-item_'.$i.'_latitude'],
      'size' => $ss['size'],
      'width' => $width,
      'height' => $height
    );
    if (count($languages) > 0) foreach ($languages as $language) {
      if (@$_POST['post-item_'.$i.'_title_'.$language]) $gal['title_'.$language] = $_POST['post-item_'.$i.'_title_'.$language];
      if (@$_POST['post-item_'.$i.'_description_'.$language]) $gal['description_'.$language] = $_POST['post-item_'.$i.'_description_'.$language];
    } 
    $gallery['items'][] = $gal;
  }
  return $gallery;
}

function i18n_gallery_save($gallery, $oldname) {
  if ($oldname) {
    if (!copy(GSDATAPATH.I18N_GALLERY_DIR.$oldname.'.xml', GSBACKUPSPATH.I18N_GALLERY_DIR.$oldname.'.xml')) return false;
  }
	$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><gallery></gallery>');
  foreach ($gallery as $key => $value) {
    if ($key != 'items' && $key != 'item') {
			$data->addChild($key)->addCData(stripslashes($value));	
    }
  }
  foreach ($gallery['items'] as $item) {
    $node = $data->addChild('item');
    foreach ($item as $key => $value) {
      $node->addChild($key)->addCData(stripslashes($value));
    }
    list($width,$height) = @getimagesize(GSDATAUPLOADPATH.$item['filename']);
    $node->addChild('width', $width);
    $node->addChild('height', $height);
		$ss = @stat(GSDATAUPLOADPATH.$item['filename']);
    $node->addChild('size', $ss['size']);
  }
	if (!XMLsave($data, GSDATAPATH.I18N_GALLERY_DIR.$gallery['name'].'.xml')) return false;
  if ($oldname && $oldname != $gallery['name']) unlink(GSDATAPATH.I18N_GALLERY_DIR.$oldname.'.xml');
  return true;
}

function i18n_gallery_save_undo($name, $newname) {
  if ($name != $newname && !unlink(GSDATAPATH.I18N_GALLERY_DIR.$newname.'.xml')) return false;
  if (!copy(GSBACKUPSPATH.I18N_GALLERY_DIR.$name.'.xml', GSDATAPATH.I18N_GALLERY_DIR.$name.'.xml')) return false;
  return true;
}

global $SITEURL, $gallery;
require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
$languages = array();
if (function_exists('return_i18n_default_language')) {
	$dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
  while ($filename = readdir($dir_handle)) {
    $pos = strpos($filename,'_');
    if ($pos !== false && strrpos($filename,'.xml') === strlen($filename)-4 && !is_dir(GSDATAPAGESPATH . $filename)) {
      $language = substr($filename,$pos+1,strlen($filename)-$pos-5);
      if (!in_array($language,$languages)) $languages[] = $language;
    }
  }
}
$success = false;
$name = @$_GET['name'];
if (!I18nGallery::checkPrerequisites()) {
  $msg = i18n_r('i18n_gallery/MISSING_DIR');
} else if (isset($_GET['undo']) && !isset($_POST['save'])) {
  $newname = @$_GET['new'] ? $_GET['new'] : $name;
  if (i18n_gallery_save_undo($name, $newname)) {
    $msg = i18n_r('i18n_gallery/UNDO_SUCCESS');
    $success = true;
  } else {
    $msg = i18n_r('i18n_gallery/UNDO_FAILURE');
  }
  $gallery = return_i18n_gallery(@$_GET['name']);
} else if (isset($_POST['save'])) {
  if (!@$_POST['post-name']) $_POST['post-name'] = clean_url(to7bit(@$_POST['post-title'], 'UTF-8'));
  if (!preg_match('/^[A-Za-z0-9-]+$/', @$_POST['post-name'])) {
    $msg = i18n_r('i18n_gallery/ERR_INVALID_NAME');
  } else if (!@$_POST['post-title']) {
    $msg = i18n_r('i18n_gallery/ERR_EMPTY_TITLE');
  } else if (!@$_POST['post-item_0_filename']) {
    $msg = i18n_r('i18n_gallery/ERR_NO_IMAGES');
  } else if (@$_POST['post-name'] != @$_GET['name'] && file_exists(GSDATAPATH.I18N_GALLERY_DIR.@$_POST['post-name'].'.xml')) {
    $msg = i18n_r('i18n_gallery/ERR_DUPLICATE_NAME');
  }
  $gallery = i18n_gallery_from_request($languages);
  if (!isset($msg)) {
    if (i18n_gallery_save($gallery, @$_GET['name'])) {
      $msg = i18n_r('i18n_gallery/SAVE_SUCCESS');
      if (@$name) $msg .= ' <a href="load.php?id=i18n_gallery&amp;edit&amp;name='.$name.'&amp;new='.@$_POST['post-name'].'&amp;undo">' . i18n_r('UNDO') . '</a>';
      $success = true;
      $gallery = return_i18n_gallery(@$_POST['post-name']); // reread
      $name = @$_POST['post-name'];
    } else {
      $msg = i18n_r('i18n_gallery/SAVE_FAILURE');
    }
  }
} else {
  $gallery = return_i18n_gallery(@$_GET['name']);
}
$settings = i18n_gallery_settings();
$w = intval(@$settings['adminthumbwidth']) > 0 ? intval($settings['adminthumbwidth']) : I18N_GALLERY_DEFAULT_THUMB_WIDTH;
$h = intval(@$settings['adminthumbheight']) > 0 ? intval($settings['adminthumbheight']) : I18N_GALLERY_DEFAULT_THUMB_HEIGHT;
$viewlink = function_exists('find_i18n_url') ? find_i18n_url('index',null) : find_url('index',null);
$viewlink .= (strpos($viewlink,'?') === false ? '?' : '&amp;') . 'name=' . $name . '&amp;preview-gallery';
$plugins = i18n_gallery_plugins();
$plugins = subval_sort($plugins,'name');
// default gallery type
if (!@$gallery['type']) $gallery['type'] = @$settings['type'] ? $settings['type'] : I18N_GALLERY_DEFAULT_TYPE;
?>
		<h3 class="floated" style="float:left"><?php $name ? i18n('i18n_gallery/EDIT_HEADER') : i18n('i18n_gallery/CREATE_HEADER'); ?></h3>

		<div class="edit-nav" >
      <p>
<?php if (count($languages) > 0) { ?>
        <?php i18n('i18n_gallery/LANGUAGE'); ?> &nbsp;
        <select name="gallerylang" class="text" id="gallerylang" style="width:auto;float:none;">
          <?php echo '<option value="" selected="selected">'.return_i18n_default_language().'</option>'; ?>
          <?php foreach ($languages as $language) echo '<option>'.$language.'</option>'; ?>
        </select>
<?php } ?>
<?php if (@$name) { ?>
  		  <a href="<?php echo $viewlink; ?>" target="_blank"><?php i18n('VIEW'); ?></a>
<?php } ?>
  			<a href="#" id="metadata_toggle"><?php i18n('i18n_gallery/GALLERY_OPTIONS'); ?></a>
      </p>
			<div class="clear" ></div>
		</div>	

    <form method="post" id="galleryForm" action="load.php?id=i18n_gallery&amp;edit&amp;name=<?php echo @$name; ?>" accept-charset="utf-8">

		  <p>
			  <label for="post-title" style="display:none;"><?php i18n('i18n_gallery/TITLE'); ?></label>
        <input type="text" class="text title lang lang_" id="post-title" name="post-title" value="<?php echo htmlspecialchars(@$gallery['title']); ?>"/>
<?php if (count($languages) > 0) foreach ($languages as $language) { ?>
        <input type="text" class="text title lang lang_<?php echo $language; ?>" name="post-title_<?php echo $language; ?>" value="<?php echo htmlspecialchars(@$gallery['title_'.$language]); ?>" style="display:none"/>
<?php } ?>
		  </p>
 
			<div style="display:none;" id="metadata_window" >
  			<div class="leftopt">
          <p>
            <label for="post-name"><?php i18n('i18n_gallery/NAME'); ?></label>
            <input type="text" class="text" id="post-name" name="post-name" value="<?php echo htmlspecialchars(@$gallery['name']); ?>"/>
          </p>
          <p>
            <label for="post-type"><?php i18n('i18n_gallery/TYPE'); ?></label>
            <select id="post-type" name="post-type" class="text">
<?php if (count($plugins) > 0) foreach ($plugins as $plugin) { ?>
              <option value="<?php echo $plugin['type']; ?>" <?php echo $plugin['type'] == @$gallery['type'] ? 'selected="selected"' : ''; ?>><?php echo htmlspecialchars($plugin['name']); ?></option>
<?php } ?>
            </select>
          </p>
<?php if (count($plugins) > 0) foreach ($plugins as $plugin) { ?>
          <p class="type type_<?php echo $plugin['type']; ?>" style="display:<?php echo $plugin['type'] == @$gallery['type'] ? 'block' : 'none'; ?>">
            <?php echo $plugin['description']; ?>
          </p>
<?php } ?>
        </div>

<?php if (count($plugins) > 0) foreach ($plugins as $plugin) { ?>
        <div class="rightopt">
    			<div class="type type_<?php echo $plugin['type']; ?>" style="display:<?php echo $plugin['type'] == @$gallery['type'] ? 'block' : 'none'; ?>">
            <?php call_user_func_array($plugin['edit'], array($gallery)); ?>
          </div>
        </div>
<?php } ?>
        <div style="clear:both"></div>
        <?php exec_action('gallery-extras'); ?>    
      </div>

			<table id="editgallery" class="edittable highlight">
        <thead>
          <tr>
            <th style="width:<?php echo $w; ?>px;"><?php i18n('i18n_gallery/IMAGE'); ?></th>
            <th>
              <span style="float:left;width:<?php echo 400-$w; ?>px"><?php i18n('i18n_gallery/FILENAME'); ?></span>
              <span style="float:left;width:120px;"><?php i18n('i18n_gallery/DIMENSIONS'); ?></span>
              <span style="float:left;width:80px;text-align:right"><?php i18n('i18n_gallery/SIZE'); ?></span> 
              <span style="clear:both;float:left;width:<?php echo 400-$w; ?>px"><?php i18n('i18n_gallery/TITLE'); ?></span>
              <span style="float:left;width:200px"><?php i18n('i18n_gallery/TAGS'); ?></span>
              <span style="clear:both;float:left;width:<?php echo 600-$w; ?>px"><?php i18n('i18n_gallery/DESCRIPTION'); ?></span>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody>
<?php 
  $i = 0;
  if (count(@$gallery['items']) > 0) foreach ($gallery['items'] as $item) { 
    if ($item['size'] >= 1000000) {
      $s = ceil($item['size'] / 1024 / 1024) . ' MB';
    } else if ($item['size'] >= 1000) {
      $s = ceil($item['size'] / 1024) . ' kB';
    } else {
      $s = $item['size'] . ' B';
    }
    $pos = strrpos($item['filename'],'/');
    if ($pos === false) $pos = -1;
    $thumbfile = substr($item['filename'], 0, $pos+1) . 'i18npic.' . ($w ? $w.'x' : '0x') . ($h ? $h.'.' : '0.') . substr($item['filename'], $pos+1);
    $thumbfile = substr($thumbfile , 0, strrpos($thumbfile ,'.')) . '.jpg';
    if (file_exists(GSDATAPATH.'thumbs/'.$thumbfile)) {
      $tlink = '../data/thumbs/'.$thumbfile;
    } else {
      $tlink = '../plugins/i18n_gallery/browser/pic.php?p='.urlencode($item['filename']).'&amp;w='.$w.'&amp;h='.$h;
    }
?>
          <tr>
            <td><a href="#" class="setimage"><img src="<?php echo $tlink; ?>"/></a></td>
            <td>
              <input type="hidden" name="post-item_<?php echo $i; ?>_filename" value="<?php echo htmlspecialchars($item['filename']); ?>"/>
              <span class="imagefile" style="float:left;width:<?php echo 400-$w; ?>px"><?php echo htmlspecialchars($item['filename']); ?></span>
              <span class="imagesize" style="float:left;width:120px;"><?php echo $item['width'] . " x " . $item['height']; ?></span>
              <span class="imagebytes" style="float:left;width:80px;text-align:right"><?php echo $s; ?></span> 
              <input type="text" class="text lang lang_" name="post-item_<?php echo $i; ?>_title" value="<?php echo htmlspecialchars(@$item['title']); ?>" 
                title="<?php echo htmlspecialchars(i18n_r('i18n_gallery/TITLE')); ?>" style="clear:both;float:left;width:<?php echo 383-$w; ?>px;margin-right:5px;"/>
<?php if (count($languages) > 0) foreach ($languages as $language) { ?>
              <input type="text" class="text lang lang_<?php echo $language; ?>" name="post-item_<?php echo $i; ?>_title_<?php echo $language; ?>" value="<?php echo htmlspecialchars(@$item['title_'.$language]); ?>" 
                title="<?php echo htmlspecialchars(i18n_r('i18n_gallery/TITLE')); ?>" style="clear:both;float:left;width:<?php echo 383-$w; ?>px;margin-right:5px;display:none;"/>
<?php } ?>
              <input type="text" class="text" name="post-item_<?php echo $i; ?>_tags" value="<?php echo htmlspecialchars(@$item['tags']); ?>" 
                title="<?php echo htmlspecialchars(i18n_r('i18n_gallery/TAGS')); ?>" style="float:left;width:188px"/>
              <textarea class="text lang lang_" name="post-item_<?php echo $i; ?>_description" title="<?php echo htmlspecialchars(i18n_r('i18n_gallery/DESCRIPTION')); ?>" 
                style="clear:both;float:left;width:<?php echo 588-$w; ?>px;height:14px;margin-top:2px;"><?php echo htmlspecialchars(@$item['description']); ?></textarea>
<?php if (count($languages) > 0) foreach ($languages as $language) { ?>
              <textarea class="text lang lang_<?php echo $language; ?>" name="post-item_<?php echo $i; ?>_description_<?php echo $language; ?>" title="<?php echo htmlspecialchars(i18n_r('i18n_gallery/DESCRIPTION')); ?>" 
                style="clear:both;float:left;width:<?php echo 588-$w; ?>px;height:14px;margin-top:2px;display:none;"><?php echo htmlspecialchars(@$item['description_'.$language]); ?></textarea>
<?php } ?>
            </td>
	          <td class="delete" >
              <a href="#" title="<?php i18n('i18n_gallery/DELETE_ITEM'); ?>">X</a>
              <input type="hidden" name="post-item_<?php echo $i; ?>_latitude" value="<?php echo htmlspecialchars($item['latitude']); ?>"/>
              <input type="hidden" name="post-item_<?php echo $i; ?>_longitude" value="<?php echo htmlspecialchars($item['longitude']); ?>"/>
              <span class="geo <?php echo $item['latitude'] && $item['longitude'] ? 'geo-yes' : ''; ?>"> </span>
            </td>
          </tr>
<?php 
    $i++;
  } 
?>
          <tr>
            <td colspan="2" class="add"><a href="#"><?php i18n('i18n_gallery/ADD_IMAGES'); ?></a></td>
            <td class="secondarylink add"><a href="#">+</a></td>
        </tbody>
      </table>
      <input type="submit" name="save" value="<?php i18n('i18n_gallery/SAVE_GALLERY'); ?>" class="submit"/>
      &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
      <a class="cancel" href="load.php?id=i18n_gallery&amp;overview"><?php i18n('CANCEL'); ?></a>
<?php if (@$name) { ?>
      &nbsp;/&nbsp; 
      <a class="cancel" href="load.php?id=i18n_gallery&amp;overview&amp;name=<?php echo $name; ?>&amp;delete"><?php i18n('i18n_gallery/DELETE'); ?></a>
<?php } ?>
    </form>
    <p style="text-align:center; margin:20px 0 0 0;">&copy; 2011-2013 Martin Vlcek - Please consider a <a href="http://mvlcek.bplaced.net/">Donation</a></p>
    <script type="text/javascript" src="../plugins/i18n_gallery/js/jquery-ui.sort.min.js"></script>
    <script type="text/javascript" src="../plugins/i18n_gallery/js/jquery.autogrow.js"></script>
    <script type="text/javascript">
      function getBytesAsText(size) {
        if (size >= 1000000) {
          return Math.ceil(size / 1024 / 1024) + ' MB';
        } else if (size >= 1000) {
          return Math.ceil(size / 1024) + ' kB';
        } else {
          return size + ' B';
        }
      }
      function addImage(filename, size, width, height, title, tags, description) {
        var i = ($('#editgallery tbody tr').size()-1);
        var html = '<tr>';
        html += '<td><a href="#" class="setimage"><img src="../plugins/i18n_gallery/browser/pic.php?p='+escape(filename)+'&amp;w=<?php echo $w; ?>&amp;h=<?php echo $h; ?>"/></a></td>\n';
        html += '<td>';
        html += '<input type="hidden" name="post-item_'+i+'_filename" value=""/>';
        html += '<span class="imagefile" style="float:left;width:<?php echo 400-$w; ?>px">' + $('<div/>').text(filename).html() + '</span>';
        html += '<span class="imagesize" style="float:left;width:120px;">' + width + ' x ' + height + '</span>';
        html += '<span class="imagebytes" style="float:left;width:80px;text-align:right">' + getBytesAsText(size) + '</span>'; 
        html += '<input type="text" class="text lang lang_" name="post-item_'+i+'_title" value="" style="clear:both;float:left;width:<?php echo 383-$w; ?>px;margin-right:5px;"/>';
<?php if (count($languages) > 0) foreach ($languages as $language) { ?>
        html += '<input type="text" class="text lang lang_<?php echo $language; ?>" name="post-item_'+i+'_title<?php echo $language ? '_'.$language : ''; ?>" value="" style="clear:both;float:left;width:<?php echo 383-$w; ?>px;margin-right:5px;display:none;"/>';
<?php } ?>
        html += '<input type="text" class="text" name="post-item_'+i+'_tags" value="" style="float:left;width:188px"/>';
        html += '<textarea class="text lang lang_" name="post-item_'+i+'_description" style="clear:both;float:left;width:<?php echo 588-$w; ?>px;margin-top:2px;height:14px;"></textarea>';
<?php if (count($languages) > 0) foreach ($languages as $language) { ?>
        html += '<textarea class="text lang lang_<?php echo $language; ?>" name="post-item_'+i+'_description<?php echo $language ? '_'.$language : ''; ?>" style="clear:both;float:left;width:<?php echo 588-$w; ?>px;margin-top:2px;height:14px;display:none;"></textarea>';
<?php } ?>
        html += '</td>\n';
	      html += '<td class="delete"><a href="#" title="<?php i18n('i18n_gallery/DELETE_ITEM'); ?>">X</a></td>\n';
        html += '<input type="hidden" name="post-item_'+i+'_latitude" value=""/>\n';
        html += '<input type="hidden" name="post-item_'+i+'_longitude" value=""/>\n';
        html += '<span class="geo"> </span>\n';
        html += '</tr>\n';
        $('#editgallery tbody tr:last').before(html);
        $tr = $('#editgallery tbody tr:last').prev();
        $tr.find('[name$=filename]').val(filename);
        if (title) $tr.find('[name$=title]').val(title);
        if (tags && tags.length > 0) $tr.find('[name$=tags]').val(tags.join(', '));
        if (description) $tr.find('[name$=description]').val(description);
        $tr.find('textarea').autogrow({ expandTolerance:1 });
        $tr.find('.delete a').click(deleteRow);
        $tr.find('a.setimage').click(setImage);
      }
      var currentRow = null;
      function replaceImage(filename, size, width, height, title, tags, description) {
        var imagefile = '../plugins/i18n_gallery/browser/pic.php?p='+escape(filename)+'&w=<?php echo $w; ?>&h=<?php echo $h; ?>';
      	currentRow.find('img').attr('src', imagefile);
      	currentRow.find('.imagefile').text(filename);
      	currentRow.find('.imagesize').text(width + ' x ' + height);
      	currentRow.find('.imagebytes').text(getBytesAsText(size)); 
        currentRow.find('[name$=filename]').val(filename);
        renumberRows();
      }
      function setImage(e) {
        currentRow = $(e.target).closest('tr');
        window.open('<?php echo $SITEURL; ?>plugins/i18n_gallery/browser/imagebrowser.php?func=replaceImage&w=<?php echo $w; ?>&h=<?php echo $h; ?>&autoclose=1', 
                'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
        return false;
      }
      function deleteRow(e) {
        $(e.target).closest('tr').remove();
        renumberRows();
        return false;
      } 
      function renumberRows() {
        $('#editgallery tbody tr').each(function(i,tr) {
          $(tr).find('input, select, textarea').each(function(k,elem) {
            var name = $(elem).attr('name').replace(/_\d+_/, '_'+i+'_');
            $(elem).attr('name', name);
          });
        });
      }
      $(function() {
        $('#editgallery .add a').click(function(e) {
          window.open('<?php echo $SITEURL; ?>plugins/i18n_gallery/browser/imagebrowser.php?func=addImage&w=<?php echo $w; ?>&h=<?php echo $h; ?>', 
                      'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
          return false;
        });
        $('#editgallery a.setimage').click(setImage);
        
        $('#editgallery textarea').autogrow({ expandTolerance:1 });
        $('#editgallery .delete a').click(deleteRow);
        $('#editgallery tbody').sortable({
          items:"tr", handle:'td',
          update:renumberRows
        });
        $('#post-type').click(function(e) {
          var val = $(e.target).val();
          $('.type').css('display','none');
          $('.type_'+val).css('display','block');
        });
<?php if (count($languages) > 0) { ?>
        $('#gallerylang').click(function(e) {
          var val = $(e.target).val();
          $('.lang').css('display','none');
          $('.lang_'+val).css('display','block');
        });
<?php } ?>
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
	      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>


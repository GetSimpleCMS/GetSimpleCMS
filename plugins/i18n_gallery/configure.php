<?php
  function i18n_gallery_settings_save($settings) {
    if (file_exists(GSDATAOTHERPATH.'i18n_gallery_settings.xml')) {
      if (!copy(GSDATAOTHERPATH.'i18n_gallery_settings.xml', GSBACKUPSPATH.'i18n_gallery_settings.xml')) return false;
    }
	  $data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><settings></settings>');
    foreach ($settings as $key => $value) $data->addChild($key)->addCData(stripslashes($value));	
	  if (!XMLsave($data, GSDATAOTHERPATH.'i18n_gallery_settings.xml')) return false;
    return true;
  }

  function i18n_gallery_settings_undo() {
    if (file_exists(GSBACKUPSPATH.'i18n_gallery_settings.xml')) {
      if (!copy(GSBACKUPSPATH.'i18n_gallery_settings.xml', GSDATAOTHERPATH.'i18n_gallery_settings.xml')) return false;
    } else {
      if (!unlink(GSDATAOTHERPATH.'i18n_gallery_settings.xml')) return false;
    }
    return true;
  }

  function i18n_gallery_delete_cache($dir) {
    $dir_handle = opendir($dir) or die("Unable to open $dir");
	  while ($filename = readdir($dir_handle)) {
      if (is_dir($dir.$filename) && $filename != '.' && $filename != '..') {
        i18n_gallery_delete_cache($dir.$filename.'/');
      } else if (substr($filename,0,8) == 'i18npic.') {
        if (!unlink($dir.$filename)) return false;
      }
    }
    return true;
  }

  include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
  if (isset($_GET['deletecache'])) {
    if (i18n_gallery_delete_cache(GSTHUMBNAILPATH)) {
      $msg = i18n_r('i18n_gallery/DELETE_CACHE_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('i18n_gallery/DELETE_CACHE_FAILURE');
    }
    $settings = i18n_gallery_settings(true);
  } else if (isset($_GET['undo']) && !isset($_POST['save'])) {
    if (i18n_gallery_settings_undo()) {
      $msg = i18n_r('i18n_gallery/UNDO_SETTINGS_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('i18n_gallery/UNDO_SETTINGS_FAILURE');
    }
    $settings = i18n_gallery_settings(true);
  } else if (isset($_POST['save'])) {
    $settings = array();
    foreach ($_POST as $key => $value) {
      if (substr($key,0,5) == 'post-') $settings[substr($key,5)] = $value;
    }
    if (i18n_gallery_settings_save($settings)) {
      $msg = i18n_r('i18n_gallery/SAVE_SETTINGS_SUCCESS');
      $msg .= ' <a href="load.php?id=i18n_gallery&amp;configure&amp;undo">' . i18n_r('UNDO') . '</a>';
      $success = true;
      $settings = i18n_gallery_settings(true);
    } else {
      $msg = i18n_r('i18n_gallery/SAVE_SETTINGS_FAILURE');
    }
  } else {
    $settings = i18n_gallery_settings(true);
  }
  $atw = intval(@$settings['adminthumbwidth']) > 0 ? intval($settings['adminthumbwidth']) : I18N_GALLERY_DEFAULT_THUMB_WIDTH;
  $ath = intval(@$settings['adminthumbheight']) > 0 ? intval($settings['adminthumbheight']) : I18N_GALLERY_DEFAULT_THUMB_HEIGHT;
?>
  <form method="post" class="largeform" id="settingsForm" action="load.php?id=i18n_gallery&amp;configure" accept-charset="utf-8">
    <h3><?php i18n('i18n_gallery/SETTINGS_HEADER'); ?></h3>
    <p><?php i18n('i18n_gallery/SETTINGS_DESCR'); ?></p>
    <div class="leftsec">
      <p class="inline">
        <label for="post-jquery"><?php i18n('i18n_gallery/DONT_INCLUDE_JQUERY'); ?></label>
        <input type="checkbox" id="post-jquery" name="post-jquery" value="0" <?php echo !i18n_gallery_check($settings,'jquery') ? 'checked="checked"' : ''; ?> />
      </p>
      <p class="inline">
        <label for="post-css"><?php i18n('i18n_gallery/DONT_INCLUDE_CSS'); ?></label>
        <input type="checkbox" id="post-css" name="post-css" value="0" <?php echo !i18n_gallery_check($settings,'css') ? 'checked="checked"' : ''; ?> />
      </p>
      <p>
        <label for="post-thumbwidth"><?php i18n('i18n_gallery/DEFAULT_THUMB_DIMENSIONS'); ?></label>
        <input type="text" class="text" id="post-thumbwidth" name="post-thumbwidth" value="<?php echo @$settings['thumbwidth']; ?>" style="width:5em"/>
        x
        <input type="text" class="text" id="post-thumbheight" name="post-thumbheight" value="<?php echo @$settings['thumbheight']; ?>" style="width:5em"/>
        &nbsp;
        <span id="thumbcrop-span">
          <input type="checkbox" id="post-thumbcrop" name="post-thumbcrop" value="1" <?php echo @$settings['thumbcrop'] ? 'checked="checked"' : ''; ?> style="vertical-align:middle"/> 
          <?php i18n('i18n_gallery/CROP'); ?>
        </span>
      </p>
    </div>
    <div class="rightsec">
    </div>
    <div class="clear"></div>
    <h3><?php i18n('i18n_gallery/ADMIN_SETTINGS_HEADER'); ?></h3>
    <div class="leftsec">
      <p>
        <label for="post-adminthumbwidth"><?php i18n('i18n_gallery/ADMIN_THUMB_DIMENSIONS'); ?></label>
        <input type="text" class="text" id="post-adminthumbwidth" name="post-adminthumbwidth" value="<?php echo $atw; ?>" style="width:5em"/>
        x
        <input type="text" class="text" id="post-adminthumbheight" name="post-adminthumbheight" value="<?php echo $ath; ?>" style="width:5em"/>
      </p>
    </div>
    <div class="rightsec">
    </div>
    <div class="clear"></div>
    <input type="submit" name="save" value="<?php i18n('i18n_gallery/SAVE_SETTINGS'); ?>" class="submit"/>
    &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
    <a class="cancel" href="load.php?id=i18n_gallery&amp;overview"><?php i18n('CANCEL'); ?></a>
    /
    <a class="cancel" href="load.php?id=i18n_gallery&amp;configure&amp;deletecache"><?php i18n('i18n_gallery/DELETE_CACHE'); ?></a>
  </form>
  <p style="text-align:center; margin:20px 0 0 0;">&copy; 2011-2013 Martin Vlcek - Please consider a <a href="http://mvlcek.bplaced.net/">Donation</a></p>
  <script type="text/javascript">
    function changeThumbSize() {
      var show = $.trim($('#post-thumbwidth').val()) != '' && $.trim($('#post-thumbheight').val()) != '';
      if (show) $('#thumbcrop-span').show(); else $('#thumbcrop-span').hide().find('input').attr('checked',false);
    }
    $(function() {
      $('#post-thumbwidth, #post-thumbheight').change(changeThumbSize);
      changeThumbSize();
<?php if (isset($msg)) { ?>
      $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
    });
  </script>


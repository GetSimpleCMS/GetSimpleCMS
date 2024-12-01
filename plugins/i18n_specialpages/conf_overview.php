<?php
  function i18n_specialpages_delete($name) {
    if (!copy(GSDATAOTHERPATH . 'i18n_special_' . $name . '.xml', GSBACKUPSPATH . 'other/i18n_special_' . $name . '.xml')) return false;
    if (!unlink(GSDATAOTHERPATH . 'i18n_special_' . $name . '.xml')) return false;
    require_once(GSPLUGINPATH.'i18n_specialpages/backend.class.php');
    I18nSpecialPagesBackend::updatePages($name, null, false);
    return true;
  }

  if (isset($_GET['delete'])) {
    $name = $_GET['delete'];
    if (i18n_specialpages_delete($name)) {
      $msg = i18n_r('i18n_specialpages/DELETE_SUCCESS');
      $msg .= ' <a href="load.php?id=i18n_specialpages&amp;config&amp;edit='.$name.'&amp;new='.$name.'&amp;undo">' . i18n_r('UNDO') . '</a>';
      $success = true;
    } else {
      $msg = i18n_r('i18n_specialpages/DELETE_FAILURE');
    }
  }
  $settings = i18n_specialpages_settings();
  $settings = subval_sort($settings,'title');
  if (!$settings) $settings = array();
  $link = 'load.php?id=i18n_specialpages&amp;config';
  $templates = array();
  $dir_handle = @opendir(GSPLUGINPATH.'i18n_specialpages/templates/');
  if ($dir_handle) {
    while ($filename = readdir($dir_handle)) {
      if (substr($filename,0,13) == 'i18n_special_' && substr($filename,-4) == '.xml') {
        $def = I18nSpecialPages::loadSettings(GSPLUGINPATH.'i18n_specialpages/templates/',$filename);
        $templates[] = $def;
      }
    }
  }
  $templates = subval_sort($templates,'title');
  if (!$templates) $templates = array();
?>
  <h3><?php i18n('i18n_specialpages/CONFIG_OVERVIEW_TITLE'); ?></h3>
  <p><?php i18n('i18n_specialpages/CONFIG_OVERVIEW_DESCR'); ?></p>
  <table id="editspecial" class="edittable highlight">
    <thead>
      <tr>
        <th><?php i18n('i18n_specialpages/TITLE'); ?></th>
        <th><?php i18n('i18n_specialpages/NAME'); ?></th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($settings as $def) { ?>
      <tr>
        <td><a href="<?php echo $link; ?>&amp;edit=<?php echo urlencode($def['name']); ?>"><?php echo htmlspecialchars($def['title']); ?></a></td>
        <td><?php echo htmlspecialchars($def['name']); ?></td>
        <td class="secondarylink"><a href="<?php echo $link; ?>&amp;edit&amp;copy=<?php echo urlencode($def['name']); ?>" title="<?php echo i18n_r('i18n_specialpages/COPY_PAGETYPE').htmlspecialchars($def['title']); ?>">C</a>
        <td class="delete"><a href="<?php echo $link; ?>&amp;delete=<?php echo urlencode($def['name']); ?>" title="<?php echo i18n_r('i18n_specialpages/DELETE_PAGETYPE').htmlspecialchars($def['title']); ?>">X</a>
      </tr>
      <?php } ?>
    </body>
  </table> 
  <p><a href="<?php echo $link; ?>&amp;edit"><?php i18n('i18n_specialpages/ADD_PAGETYPE'); ?></a></p>
  <?php if ($templates) { ?>
    <p><?php i18n('i18n_specialpages/CREATE_FROM_TEMPLATE'); ?></p>
    <ul>
      <?php foreach ($templates as $def) { ?>
        <li><a href="<?php echo $link; ?>&amp;edit&amp;template=<?php echo urlencode($def['name']); ?>"><?php echo htmlspecialchars($def['title']); ?></a></li>
      <?php } ?>
    </ul>
  <?php } ?>
    <p style="text-align:center; margin:20px 0 0 0;">&copy; 2012 Martin Vlcek - Please consider a <a href="http://mvlcek.bplaced.net/">Donation</a></p>
    <script type="text/javascript">
      $(function() {
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo @$success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
        $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>
  
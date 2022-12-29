<?php
  $settings = i18n_specialpages_settings();
  $settings = subval_sort($settings,'title');
  if (!$settings) $settings = array();
?>
  <h3><?php i18n('i18n_specialpages/CREATE_PAGE_TITLE'); ?></h3>
  <p><?php i18n('i18n_specialpages/CREATE_PAGE_DESCR'); ?></p>
  <table id="editspecial" class="edittable highlight">
    <thead>
      <tr>
        <th><?php i18n('i18n_specialpages/TITLE'); ?></th>
        <th><?php i18n('i18n_specialpages/NAME'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($settings as $def) { ?>
      <tr>
        <td><a href="edit.php?special=<?php echo urlencode($def['name']); ?>"><?php echo htmlspecialchars($def['title']); ?></a></td>
        <td><?php echo htmlspecialchars($def['name']); ?></td>
      </tr>
      <?php } ?>
    </body>
  </table> 

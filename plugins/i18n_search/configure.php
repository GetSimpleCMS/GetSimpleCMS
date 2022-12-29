<?php
  i18n_merge('i18n_search', 'en');
  $params = array();
  $success = false;
  $canUndo = false;
  if (isset($_POST['reset'])) {
    if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
      if (copy(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE, GSBACKUPSPATH.'other/'.I18N_SEARCH_SETTINGS_FILE)) {
        $canUndo = true;
      }
    }
    $success = !file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE) || unlink(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
    $msg = $success ? i18n_r('i18n_search/RESET_SUCCESS') : i18n_r('i18n_search/RESET_FAILURE');
    if ($success && $canUndo) $msg .= ' <a href="load.php?id=i18n_search&view=settings&undo">' . i18n_r('UNDO') . '</a>';
    delete_i18n_search_index();
  } else if (isset($_POST['save'])) {
    foreach (array('contentWeight','titleWeight','tagWeight','tagMode','minTagSize','maxTagSize','max','numWords') as $name) {
      if (isset($_POST[$name]) && is_numeric($_POST[$name])) $params[$name] = $_POST[$name];
    }
    foreach (array('showTags','showLanguage','showDate','showPaging','mark') as $name) {
      if (isset($_POST[$name])) $params[$name] = 1; else $params[$name] = 0; 
    }
    if (@$_POST['transliteration']) $params['transliteration'] = $_POST['transliteration'];
    if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
      if (copy(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE, GSBACKUPSPATH.'other/'.I18N_SEARCH_SETTINGS_FILE)) {
        $canUndo = true;
      }
    }
	  $data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><settings></settings>');
    foreach ($params as $key => $value) {
      $node = $data->addChild($key);
      $node->addCData((string) $value);
    }
	  XMLsave($data, GSDATAOTHERPATH . I18N_SEARCH_SETTINGS_FILE);
    $success = true;
    $msg = i18n_r('i18n_search/SAVE_SUCCESS'); 
    if ($canUndo) $msg .= ' <a href="load.php?id=i18n_search&view=settings&undo">' . i18n_r('UNDO') . '</a>'; 
    delete_i18n_search_index();
  } else if (isset($_REQUEST['undo'])) {
    if (file_exists(GSBACKUPSPATH.'other/'.I18N_SEARCH_SETTINGS_FILE)) {
      if (copy(GSBACKUPSPATH.'other/'.I18N_SEARCH_SETTINGS_FILE, GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
        $success = true;
      }
    }
    $msg = $success ? i18n_r('i18n_search/UNDO_SUCCESS') : i18n_r('i18n_search/UNDO_FAILURE');
    delete_i18n_search_index();
  }
  if (!isset($_POST['save'])) {
    if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
      $data = getXML(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
      if ($data) foreach ($data->children() as $child) {
        if (!array_key_exists($child->getName(), $params)) $params[$child->getName()] = (string) $child;
      }
    }
  }
  if (!array_key_exists('contentWeight',$params) || !is_numeric($params['contentWeight'])) $params['contentWeight'] = I18N_CONTENT_WEIGHT;
  if (!array_key_exists('titleWeight',$params) || !is_numeric($params['titleWeight'])) $params['titleWeight'] = I18N_TITLE_WEIGHT;
  if (!array_key_exists('tagWeight',$params) || !is_numeric($params['tagWeight'])) $params['tagWeight'] = I18N_TAG_WEIGHT;
  if (!array_key_exists('tagMode', $params) || !is_numeric($params['tagMode'])) $params['tagMode'] = I18N_TAGS_LANG_OR_DEFLANG;
  if (!array_key_exists('showTags', $params)) $params['showTags'] = true;
  if (!array_key_exists('minTagSize',$params) || !is_numeric($params['minTagSize'])) $params['minTagSize'] = I18N_MIN_TAG_SIZE;
  if (!array_key_exists('maxTagSize',$params) || !is_numeric($params['maxTagSize'])) $params['maxTagSize'] = I18N_MAX_TAG_SIZE;
  if (!array_key_exists('max',$params) || !is_numeric($params['max'])) $params['max'] = I18N_MAX_RESULTS;
  if (!array_key_exists('numWords',$params) || !is_numeric($params['numWords'])) $params['numWords'] = I18N_NUM_WORDS;
  if (!array_key_exists('showLanguage', $params)) $params['showLanguage'] = function_exists('return_i18n_default_language');
  if (!array_key_exists('showDate', $params)) $params['showDate'] = true;
  if (!array_key_exists('showPaging', $params)) $params['showPaging'] = true;
  if (!array_key_exists('mark', $params)) $params['mark'] = false;
  $view = @$_REQUEST['view'];
  if (!$view) $view = 'usage';
  $link = "load.php?id=i18n_search&view=settings";
?>
  <h3 class="floated" style="float:left"><?php echo i18n_r('i18n_search/CONFIGURATION'); ?></h3>
	<div class="edit-nav" >
    <p>
      <a href="<?php echo $link; ?>&view=usage" <?php echo $view=='usage' ? 'class="current"' : ''; ?> ><?php echo i18n_r('i18n_search/VIEW_USAGE'); ?></a>
      <a href="<?php echo $link; ?>&view=settings" <?php echo $view=='settings' ? 'class="current"' : ''; ?> ><?php echo i18n_r('i18n_search/VIEW_SETTINGS'); ?></a>
    </p>
    <div class="clear" ></div>
  </div>
<?php if ($view == 'settings') { ?>
  <form method="post" id="searchForm" action="<?php echo $link; ?>" style="clear:both">
    <table id="editsearch" class="edittable highlight">
      <tbody>
        <tr><td colspan="3"><strong><?php i18n('i18n_search/INDEX_SETTINGS'); ?></strong></td></tr>
        <tr><td colspan="3"><?php i18n('i18n_search/INDEX_DESCRIPTION'); ?></td></tr>
        <tr><td style="width:60%"><?php i18n('i18n_search/CONTENT_WEIGHT'); ?></td><td colspan="2"><input type="text" name="contentWeight" value="<?php echo htmlspecialchars(@$params['contentWeight']); ?>" style="width:3em" class="text"/></td></tr>
        <tr><td><?php i18n('i18n_search/TITLE_WEIGHT'); ?></td><td colspan="2"><input type="text" name="titleWeight" value="<?php echo htmlspecialchars(@$params['titleWeight']); ?>" style="width:3em" class="text"/></td></tr>
        <tr><td><?php i18n('i18n_search/TAG_WEIGHT'); ?></td><td colspan="2"><input type="text" name="tagWeight" value="<?php echo htmlspecialchars(@$params['tagWeight']); ?>" style="width:3em" class="text"/></td></tr>
        <tr>
          <td><?php i18n('i18n_search/TAG_MODE'); ?></td>
          <td colspan="2">
            <select name="tagMode" style="width:20em" class="text">
              <option value="<?php echo I18N_TAGS_LANG_OR_DEFLANG; ?>" <?php if ($params['tagMode'] == I18N_TAGS_LANG_OR_DEFLANG) echo 'selected="selected"'; ?> ><?php i18n('i18n_search/TAGS_LANG_OR_DEFLANG'); ?></option>
              <option value="<?php echo I18N_TAGS_ALWAYS_DEFLANG; ?>" <?php if ($params['tagMode'] == I18N_TAGS_ALWAYS_DEFLANG) echo 'selected="selected"'; ?> ><?php i18n('i18n_search/TAGS_ALWAYS_DEFLANG'); ?></option>
              <option value="<?php echo I18N_TAGS_ALWAYS_LANG; ?>" <?php if ($params['tagMode'] == I18N_TAGS_ALWAYS_LANG) echo 'selected="selected"'; ?> ><?php i18n('i18n_search/TAGS_ALWAYS_LANG'); ?></option>
            </select>
          </td>
        </tr>
        <tr><td colspan="3"><strong><?php i18n('i18n_search/SEARCHFORM_SETTINGS'); ?></strong></td></tr>
        <tr><td colspan="3"><?php i18n('i18n_search/SEARCHFORM_DESCRIPTION'); ?></td></tr>
        <tr><td><?php i18n('i18n_search/SHOW_TAGS'); ?></td><td><input type="checkbox" name="showTags" value="on" <?php echo @$params['showTags'] ? 'checked="checked"' : ''; ?>/></td><td>(showTags)</td></tr>
        <tr><td><?php i18n('i18n_search/MIN_TAG_SIZE'); ?></td><td><input type="text" name="minTagSize" value="<?php echo htmlspecialchars(@$params['minTagSize']); ?>" style="width:3em" class="text"/></td><td>(minTagSize)</td></tr>
        <tr><td><?php i18n('i18n_search/MAX_TAG_SIZE'); ?></td><td><input type="text" name="maxTagSize" value="<?php echo htmlspecialchars(@$params['maxTagSize']); ?>" style="width:3em" class="text"/></td><td>(maxTagSize)</td></tr>
        <tr><td colspan="3"><strong><?php i18n('i18n_search/SEARCHRESULT_SETTINGS'); ?></strong></td></tr>
        <tr><td colspan="3"><?php i18n('i18n_search/SEARCHRESULT_DESCRIPTION'); ?></td></tr>
        <tr><td><?php i18n('i18n_search/MAX'); ?></td><td><input type="text" name="max" value="<?php echo @$params['max']; ?>" style="width:3em" class="text"/></td><td>(max)</td></tr>
        <tr><td><?php i18n('i18n_search/NUM_WORDS'); ?></td><td><input type="text" name="numWords" value="<?php echo @$params['numWords']; ?>" style="width:3em" class="text"/></td><td>(numWords)</td></tr>
        <tr><td><?php i18n('i18n_search/SHOW_LANGUAGE'); ?></td><td><input type="checkbox" name="showLanguage" value="on" <?php echo @$params['showLanguage'] ? 'checked="checked"' : ''; ?>/></td><td>(showLanguage)</td></tr>
        <tr><td><?php i18n('i18n_search/SHOW_DATE'); ?></td><td><input type="checkbox" name="showDate" value="on" <?php echo @$params['showDate'] ? 'checked="checked"' : ''; ?>/></td><td>(showDate)</td></tr>
        <tr><td><?php i18n('i18n_search/SHOW_PAGING'); ?></td><td><input type="checkbox" name="showPaging" value="on" <?php echo @$params['showPaging'] ? 'checked="checked"' : ''; ?>/></td><td>(showPaging)</td></tr>
        <tr><td><?php i18n('i18n_search/MARK'); ?></td><td><input type="checkbox" name="mark" value="on" <?php echo @$params['mark'] ? 'checked="checked"' : ''; ?>/></td><td></td></tr>
        <tr><td colspan="3"><strong><?php i18n('i18n_search/TRANSLITERATION_SETTINGS'); ?></strong></td></tr>
        <tr><td colspan="3"><?php i18n('i18n_search/TRANSLITERATION_DESCRIPTION'); ?></td></tr>
        <tr><td><?php i18n('i18n_search/TRANSLITERATION'); ?></td><td><textarea name="transliteration" style="width:5em; height:100px;" class="text"><?php echo htmlspecialchars(@$params['transliteration']); ?></textarea></td><td></td></tr>
      </tbody>
    </table>
    <input type="submit" name="save" value="<?php i18n('i18n_search/SAVE_CONFIGURATION'); ?>" class="submit"/>
    <input type="submit" name="reset" value="<?php i18n('i18n_search/RESET_CONFIGURATION'); ?>" class="submit"/>
  </form>
  <script type="text/javascript">
    $(function() {
<?php if (isset($msg)) { ?>
      $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
<?php } ?>
    });
  </script>
<?php } else { ?>
  <p><?php i18n('i18n_search/USAGE_IN_PAGE'); ?></p>
  <code style="display:block;padding-left:2em;margin-bottom:1em;">(% searchform %)<br/>(% searchresults %)</code>
  <p><?php i18n('i18n_search/USAGE_IN_TEMPLATE'); ?></p>
  <code style="display:block;padding-left:2em;margin-bottom:1em;">&lt;?php get_i18n_search_form(array('slug'=>'search')); ?&gt;</code>
  <p><?php i18n('i18n_search/CUSTOMIZE_1'); ?> 
    <a href="<?php echo $link; ?>&view=settings"><?php echo i18n_r('i18n_search/VIEW_SETTINGS'); ?></a> 
    <?php i18n('i18n_search/CUSTOMIZE_2'); ?></p>
  <code style="display:block;padding-left:2em;margin-bottom:1em;">(% searchresults max:20 showLanguage:0 DATE_FORMAT:"%A, %d.%m.%Y - %H:%M" %)</code>
  <p><?php i18n('OR'); ?></p>
  <code style="display:block;padding-left:2em;margin-bottom:1em;">&lt;?php get_i18n_search_form(array('slug'=>'search','showTags'=>0)); ?&gt;</code>
  <p><?php i18n('i18n_search/CUSTOMIZE_3'); ?></p>
    <table id="editsearch" class="edittable highlight">
      <thead>
        <tr><th><?php i18n('i18n_search/PARAMETER_NAME'); ?></th><th><?php i18n('i18n_search/PARAMETER_DESCRIPTION'); ?></th></tr>
      </thead>
      <tbody>
        <tr><td colspan="2"><strong><?php i18n('i18n_search/SEARCHFORM_SETTINGS'); ?></strong></td></tr>
        <tr><td>slug</td><td><?php i18n('i18n_search/SLUG_DESCR'); ?></td></tr>
        <tr><td>showTags</td><td><?php i18n('i18n_search/SHOW_TAGS'); ?></td></tr>
        <tr><td>minTagSize</td><td><?php i18n('i18n_search/MIN_TAG_SIZE'); ?></td></tr>
        <tr><td>maxTagSize</td><td><?php i18n('i18n_search/MAX_TAG_SIZE'); ?></td></tr>
        <tr><td>ajax</td><td><?php i18n('i18n_search/AJAX_DESCR'); ?></td></tr>
        <tr><td>live</td><td><?php i18n('i18n_search/LIVE_DESCR'); ?></td></tr>
        <tr><td>GO</td><td><?php i18n('i18n_search/GO_DESCR'); ?></td></tr>
        <tr><td>PLACEHOLDER</td><td><?php i18n('i18n_search/PLACEHOLDER_DESCR'); ?></td></tr>
        <tr><td colspan="2"><strong><?php i18n('i18n_search/SEARCHRESULT_SETTINGS'); ?></strong></td></tr>
        <tr><td>live</td><td><?php i18n('i18n_search/LIVE_DESCR'); ?></td></tr>
        <tr><td>tags</td><td><?php i18n('i18n_search/TAGS_DESCR'); ?></td></tr>
        <tr><td>words</td><td><?php i18n('i18n_search/WORDS_DESCR'); ?></td></tr>
        <tr><td>addTags</td><td><?php i18n('i18n_search/ADDTAGS_DESCR'); ?></td></tr>
        <tr><td>addWords</td><td><?php i18n('i18n_search/ADDWORDS_DESCR'); ?></td></tr>
        <tr><td>lang</td><td><?php i18n('i18n_search/LANG_DESCR'); ?></td></tr>
        <tr><td>order</td><td><?php i18n('i18n_search/ORDER_DESCR'); ?></td></tr>
        <tr><td>max</td><td><?php i18n('i18n_search/MAX'); ?></td></tr>
        <tr><td>numWords</td><td><?php i18n('i18n_search/NUM_WORDS'); ?></td></tr>
        <tr><td>showLanguage</td><td><?php i18n('i18n_search/SHOW_LANGUAGE'); ?></td></tr>
        <tr><td>showDate</td><td><?php i18n('i18n_search/SHOW_DATE'); ?></td></tr>
        <tr><td>showPaging</td><td><?php i18n('i18n_search/SHOW_PAGING'); ?></td></tr>
        <tr><td>component</td><td><?php i18n('i18n_search/COMPONENT_DESCR'); ?></td></tr>
        <tr><td>idPrefix</td><td><?php i18n('i18n_search/IDPREFIX_DESCR'); ?></td></tr>
        <tr><td>tagClassPrefix</td><td><?php i18n('i18n_search/TAGCLASSPREFIX_DESCR'); ?></td></tr>
        <tr><td>HEADER</td><td><?php i18n('i18n_search/HEADER_DESCR'); ?></td></tr>
        <tr><td>NOT_FOUND</td><td><?php i18n('i18n_search/NOT_FOUND_DESCR'); ?></td></tr>
        <tr><td>DATE_LOCALE</td><td><?php i18n('i18n_search/DATE_LOCALE_DESCR'); ?></td></tr>
        <tr><td>DATE_FORMAT</td><td><?php i18n('i18n_search/DATE_FORMAT_DESCR'); ?></td></tr>
        <tr><td>FIRST_TEXT</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> "<<"</td></tr>
        <tr><td>FIRST_TITLE</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> "First page"</td></tr>
        <tr><td>PREV_TEXT</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> "<"</td></tr>
        <tr><td>PREV_TITLE</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> "Previous page"</td></tr>
        <tr><td>NEXT_TEXT</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> ">"</td></tr>
        <tr><td>NEXT_TITLE</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> "Next page"</td></tr>
        <tr><td>LAST_TEXT</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> ">>"</td></tr>
        <tr><td>LAST_TITLE</td><td><?php i18n('i18n_search/PAGING_DESCR'); ?> "Last page"</td></tr>
      </tbody>
    </table>
  <p><?php i18n('i18n_search/LANGUAGE_FILE_COMMENT'); ?></p>
<?php } ?>

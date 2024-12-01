<?php

  class I18nSpecialPagesEditor {
    
    public static function stripSlashes() {
      if (get_magic_quotes_gpc()) {
        foreach ($_GET as $key => $value) $_GET[$key] = stripslashes($value);
        foreach ($_POST as $key => $value) $_POST[$key] = stripslashes($value);
      }
    }
    
    public static function validate() {
      $msgs = array();
      if (!@$_POST['post-title']) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_EMPTY_TITLE');
      } else if (!@$_POST['post-name']) {
        $_POST['post-name'] = clean_url(to7bit(@$_POST['post-title'], 'UTF-8'));
      }
      if (!preg_match('/^[A-Za-z0-9-]+$/', @$_POST['post-name'])) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_INVALID_NAME');
      } else if ($_POST['post-name'] != @$_GET['edit'] && file_exists(GSDATAOTHERPATH.'i18n_special_'.$_POST['post-name'].'.xml')) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_NAME_EXISTS');
      }
      if (@$_POST['post-menu'] && $_POST['post-menu'] != '0' && !@$_POST['post-parent']) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_MISSING_PARENT');
      }
      $stdfields = array('pubDate','title','url','meta','metad','menu','menuStatus','menuOrder',
                          'template','parent','content','private','creDate','user','special',
                          'tags','creTime','pubTime');
      $emptyname = false;
      $emptylabel = false;
      $invalidname = false;
      $names = array(); 
      for ($i=0; isset($_POST['cf_'.$i.'_name']); $i++) {
        if (!$_POST['cf_'.$i.'_name'] && !$_POST['cf_'.$i.'_label']) continue;
        if (!$_POST['cf_'.$i.'_name']) {
          $emptyname = true;
        } else if (in_array($_POST['cf_'.$i.'_name'], $stdfields)) {
          $names[] = $_POST['cf_'.$i.'_name'];
        } else if (!preg_match('/^[A-Za-z0-9_-]+$/', $_POST['cf_'.$i.'_name'])) {
          $invalidname = true;
        }
        if (!$_POST['cf_'.$i.'_label']) {
          $emptylabel = true;
        }
      }
      if ($emptyname) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_EMPTY_FIELD_NAME');
      }
      if ($invalidname) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_INVALID_FIELD_NAME');
      }
      if ($names) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_RESERVED_FIELD_NAMES').' '.implode(', ', $names);
      }
      if ($emptylabel) {
        $msgs[] = i18n_r('i18n_specialpages/ERR_EMPTY_FIELD_LABEL');
      }
      return implode("<br />", $msgs);
    }
  
    public static function save($oldname) {
      if ($oldname && file_exists(GSDATAOTHERPATH . 'i18n_special_' . $oldname . '.xml')) {
        $olddata = getXML(GSDATAOTHERPATH . 'i18n_special_' . $oldname . '.xml');
        $oldslug = (string) $olddata->slug;
        if (!@copy(GSDATAOTHERPATH . 'i18n_special_' . $oldname . '.xml', GSBACKUPSPATH . 'other/i18n_special_' . $oldname . '.xml')) return false;
        if (!@unlink(GSDATAOTHERPATH . 'i18n_special_' . $oldname . '.xml')) return false;
      }
      $success = self::write(GSDATAOTHERPATH . 'i18n_special_' . $_POST['post-name'] . '.xml');
      $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
      if ($oldname && $olddata) { 
        require_once(GSPLUGINPATH.'i18n_specialpages/backend.class.php');
        I18nSpecialPagesBackend::updatePages($oldname, $_POST['post-name'], $_POST['post-slug'] && $oldslug != $_POST['post-slug']);
      }
      return $success;
    }  
     
    public static function write($file) { 
   		$data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><specialpage></specialpage>');
      $data->addChild('name', $_POST['post-name']);
      $data->addChild('title')->addCData($_POST['post-title']);
      $data->addChild('parent', $_POST['post-parent']);
      $data->addChild('tags')->addCData($_POST['post-tags']);
      $data->addChild('slug', $_POST['post-slug']);
      $data->addChild('template')->addCData($_POST['post-template']);
      $data->addChild('menu', $_POST['post-menu']);
      $data->addChild('headercomponent', $_POST['post-headercomponent']);
      foreach ($_POST as $key => $value) {
        if (substr($key,0,18) == 'post-showcomponent' || substr($key,0,20) == 'post-searchcomponent') {
          if (trim($value)) $data->addChild(substr($key,5))->addCData($value);
        }
      }
      $data->addChild('defaultcontent')->addCData($_POST['post-defaultcontent']);
      $fields = $data->addChild('fields');
      for ($i=0; isset($_POST['cf_'.$i.'_name']); $i++) {
        if ($_POST['cf_'.$i.'_name']) {
          $item = $fields->addChild('item');
          $item->addChild('name')->addCData($_POST['cf_'.$i.'_name']);
          $item->addChild('label')->addCData($_POST['cf_'.$i.'_label']);
          $item->addChild('type')->addCData($_POST['cf_'.$i.'_type']);
          if (@$_POST['cf_'.$i.'_value']) {
            $item->addChild('value')->addCData($_POST['cf_'.$i.'_value']);
          }
          if (@$_POST['cf_'.$i.'_options']) {
            $options = preg_split("/\r?\n/", rtrim($_POST['cf_'.$i.'_options']));
            foreach ($options as $option) {
              $item->addChild('option')->addCData($option);
            } 
          }
          if (@$_POST['cf_'.$i.'_index'] == '1' || @$_POST['cf_'.$i.'_index'] == '2' || @$_POST['cf_'.$i.'_index'] == '3') {
            $item->addChild('index', $_POST['cf_'.$i.'_index']);
          }
        }
      }
   		return XMLsave($data, $file);
    }
  
    public static function undo($name, $newname) {
      $newdata = getXML(GSDATAOTHERPATH . 'i18n_special_' . $newname . '.xml');
      $newslug = (string) $newdata->slug;
      if ($name != $newname && !unlink(GSDATAOTHERPATH.'i18n_special_'.$newname.'.xml')) return false;
      if (!copy(GSBACKUPSPATH.'other/i18n_special_'.$name.'.xml', GSDATAOTHERPATH.'i18n_special_'.$name.'.xml')) return false;
      $olddata = getXML(GSDATAOTHERPATH . 'i18n_special_' . $name . '.xml');
      $oldslug = (string) $olddata->slug;
      require_once(GSPLUGINPATH.'i18n_specialpages/backend.class.php');
      I18nSpecialPagesBackend::updatePages($name, $newname, $oldslug && $oldslug != $newslug);
      return true;
    }
    
  }

  I18nSpecialPagesEditor::stripSlashes();
  if (isset($_GET['undo']) && !isset($_POST['save'])) {
    $name = preg_match('/^[A-Za-z0-9-]+$/', @$_GET['edit']) ? $_GET['edit'] : null;
    if (I18nSpecialPagesEditor::undo($name, @$_GET['new'])) {
      $msg = i18n_r('i18n_specialpages/UNDO_SUCCESS');
      $success = true;
      if (function_exists('delete_i18n_search_index')) delete_i18n_search_index();
    } else {
      $msg = i18n_r('i18n_specialpages/UNDO_FAILURE');
    }
    $def = I18nSpecialPages::getSettings($name);
  } else if (isset($_POST['save'])) {
    $msg = I18nSpecialPagesEditor::validate();
    if (!$msg) unset($msg);
    if (!isset($msg) && !I18nSpecialPagesEditor::save(@$_GET['edit'])) {
      $msg = i18n_r('i18n_specialpages/SAVE_FAILURE');
    }
    if (!isset($msg)) {
      $msg = i18n_r('i18n_specialpages/SAVE_SUCCESS');
      $name = $_POST['post-name'];
      $oldname = preg_match('/^[A-Za-z0-9-]+$/', @$_GET['edit']) ? $_GET['edit'] : null;
      if ($oldname && file_exists(GSBACKUPSPATH.'other/i18n_special_'.$oldname.'.xml')) {
        $msg .= ' <a href="load.php?id=i18n_specialpages&amp;config&amp;edit='.$oldname.'&amp;new='.$name.'&amp;undo">' . i18n_r('UNDO') . '</a>';
      }
      $success = true;
      if (function_exists('delete_i18n_search_index')) delete_i18n_search_index();
      $def = I18nSpecialPages::getSettings($name);
    } else {
      $name = preg_match('/^[A-Za-z0-9-]+$/', @$_GET['edit']) ? $_GET['edit'] : null;
      $def = array();
      $def['name'] = $_POST['post-name'];
      $def['title'] = $_POST['post-title'];
      $def['parent'] = $_POST['post-parent'];
      $def['tags'] = $_POST['post-tags'];
      $def['slug'] = $_POST['post-slug'];
      $def['template'] = $_POST['post-template'];
      $def['menu'] = $_POST['post-menu'];
      $def['headercomponent'] = $_POST['post-headercomponent'];
      foreach ($_POST as $key => $value) {
        if (substr($key,0,18) == 'post-showcomponent' || substr($key,0,20) == 'post-searchcomponent') {
          $def[substr($key,5)] = $value;
        }
      }
      $def['fields'] = array();
      for ($i=0; isset($_POST['cf_'.$i.'_name']); $i++) {
        $cf = array();
        $cf['name'] = $_POST['cf_'.$i.'_name'];
        $cf['label'] = $_POST['cf_'.$i.'_label'];
        $cf['type'] = $_POST['cf_'.$i.'_type'];
        $cf['value'] = $_POST['cf_'.$i.'_value'];
        $cf['options'] = preg_split("/\r?\n/", rtrim($_POST['cf_'.$i.'_value']));
        $def['fields'][] = $cf;
      }
      array_pop($def['fields']); // remove the last hidden line
    }
  } else if (@$_GET['edit']) {
    $name = preg_match('/^[A-Za-z0-9-]+$/', @$_GET['edit']) ? $_GET['edit'] : null;
    $def = I18nSpecialPages::getSettings($name);
  } else if (@$_GET['copy']) {
    $name = '';
    $cpname = preg_match('/^[A-Za-z0-9-]+$/', @$_GET['copy']) ? $_GET['copy'] : null;
    $def = I18nSpecialPages::getSettings($cpname);
  } else if (@$_GET['template']) {
    $name = '';
    $tempname = preg_match('/^[A-Za-z0-9-]+$/', @$_GET['template']) ? $_GET['template'] : null;
    $def = I18nSpecialPages::loadSettings(GSPLUGINPATH.'i18n_specialpages/templates/', 'i18n_special_'.$tempname.'.xml');
  } else {
    $name = '';
    $def = array();
  }
  $issearch = defined('I18N_ACTION_INDEX');
  $isi18n = function_exists('i18n_init');
  $isi18nnav = function_exists('get_i18n_navigation');
  // get pages for parent and languages
  $pages = array();
  $languages = array();
  $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
  while ($filename = readdir($dir_handle)) {
    if (substr($filename,-4) == '.xml' && !is_dir(GSDATAPAGESPATH . $filename)) {
      $data = getXML(GSDATAPAGESPATH . $filename);
      if ($isi18n && strpos($filename,'_') !== false) {
        $lang = substr($filename, strpos($filename,'_')+1, -4);
        if (!in_array($lang, $languages)) $languages[] = $lang;
      } else {
        $tags = preg_split('/\s*,\s*/', trim(@$data['metak']));
        $special = false;
        foreach ($tags as $tag) {
          if (substr($tag,0,9) == '_special_') { $special = true; break; }
        }
        if (!$special) {
          $pages[substr($filename,0,-4)] = html_entity_decode(stripslashes($data->title),ENT_QUOTES,'UTF-8');
        }
      }
    }
  }
  sort($languages);
  asort($pages);
  // get templates
  global $TEMPLATE;
  $templates = array();
  $themes_handle = opendir(GSTHEMESPATH.$TEMPLATE) or die("Unable to open " . GSTHEMESPATH);
  while ($filename = readdir($themes_handle)) {
    if (substr($filename,-4) == '.php' && $filename != 'functions.php' && 
        substr($filename,-8) != '.inc.php' && !is_dir(GSTHEMESPATH.$TEMPLATE.$filename)) {
      $templates[] = $filename;
    }
  }
  sort($templates);
  $lf = "\r\n";
  $defaultsearchcontent = 
'<h3 class="search-entry-title">'.$lf.
'  <?php if ($showLanguage) { ?>'.$lf.
'  <span class="search-entry-language"><?php get_special_field(\'language\'); ?></span>'.$lf.
'  <?php } ?>'.$lf.
'  <a href="<?php get_special_field(\'link\',\'\',false); ?>">'.$lf.
'    <?php get_special_field(\'title\',\'\',false); ?>'.$lf.
'  </a>'.$lf.
'</h3>'.$lf.
'<?php if ($showDate) { ?>'.$lf.
'<div class="search-entry-date"><?php get_special_field_date(\'pubDate\', $dateFormat); ?></div>'.$lf.
'<?php } ?>'.$lf.
'<div class="search-entry-excerpt"><?php get_special_field_excerpt(\'content\', $numWords); ?></div>'.$lf;
?>
<h3 class="floated" style="float:left"><?php if ($name) i18n('i18n_specialpages/CONFIG_EDIT_TITLE'); else i18n('i18n_specialpages/CONFIG_CREATE_TITLE'); ?></h3>
<div class="edit-nav tab-links" >
  <p>
    <?php if ($issearch) { ?>
    <a href="#tab-search"><?php i18n('i18n_specialpages/TAB_SEARCH'); ?></a>
    <?php } ?>
    <a href="#tab-view"><?php i18n('i18n_specialpages/TAB_VIEW'); ?></a>
    <a href="#tab-fields"><?php i18n('i18n_specialpages/TAB_FIELDS'); ?></a>
    <a class="current" href="#tab-general"><?php i18n('i18n_specialpages/TAB_GENERAL'); ?></a>
  </p>
  <div class="clear" ></div>
</div>  

<form method="post" id="specialpagesForm" action="load.php?id=i18n_specialpages&amp;config&amp;edit=<?php echo $name; ?>">

  <div id="tab-general" class="tab">
    <p><?php i18n('i18n_specialpages/CONFIG_EDIT_GENERAL_DESCR'); ?></p>
    <p><input type="text" class="text title" name="post-title" value="<?php echo @$def['title']; ?>" /></p>
    <table id="editsp" class="edittable highlight">
      <tr>
        <td><label for="post-name"><?php i18n('i18n_specialpages/NAME'); ?></label></td>
        <td><input type="text" class="text" style="width:240px" id="post-name" name="post-name" value="<?php echo htmlspecialchars(@$def['name']); ?>" /></td> 
        <td><?php i18n('i18n_specialpages/NAME_DESCR'); ?></td>
      </tr>
      <tr>
        <td><label for="post-slug"><?php i18n('SLUG_URL'); ?></label></td>
        <td><input type="text" class="text" style="width:240px" id="post-slug" name="post-slug" value="<?php echo htmlspecialchars(@$def['slug']); ?>" /></td> 
        <td><?php i18n('i18n_specialpages/SLUG_URL_DESCR'); ?></td>
      </tr>
      <tr>
        <td><label for="post-parent"><?php i18n('PARENT_PAGE'); ?></label></td>
        <td>
          <select id="post-parent" name="post-parent" class="text" style="width:250px">
            <option value=""></option>
            <?php foreach ($pages as $slug => $title) { ?>
            <option value="<?php echo htmlspecialchars($slug); ?>" <?php if ($slug == @$def['parent']) echo 'selected="selected"'; ?> ><?php echo htmlspecialchars($title).' ('.htmlspecialchars($slug).')'; ?></option>  
            <?php } ?>
          </select>
        </td>
        <td><?php i18n('i18n_specialpages/PARENT_PAGE_DESCR'); ?></td>
      </tr>
      <tr>
        <td><label for="post-tags"><?php i18n('TAG_KEYWORDS'); ?></label></td>
        <td><input type="text" class="text" style="width:240px" id="post-tags" name="post-tags" value="<?php echo htmlspecialchars(@$def['tags']); ?>" /></td> 
        <td><?php i18n('i18n_specialpages/TAG_KEYWORDS_DESCR'); ?></td>
      </tr>
      <tr>
        <td><label for="post-template"><?php i18n('TEMPLATE'); ?></label></td>
        <td>
          <select id="post-template" name="post-template" class="text" style="width:250px">
            <option value=""></option>
            <?php foreach ($templates as $template) { ?>
            <option value="<?php echo htmlspecialchars($template); ?>" <?php if ($template == @$def['template']) echo 'selected="selected"'; ?> ><?php echo htmlspecialchars($template=='template.php' ? i18n_r('DEFAULT_TEMPLATE') : $template); ?></option>  
            <?php } ?>
          </select>
        </td>
        <td><?php i18n('i18n_specialpages/TEMPLATE_DESCR'); ?></td>
      </tr>
      <tr>
        <td><label for="post-menu"><?php i18n('i18n_specialpages/MENU'); ?></label></td>
        <td>
          <select id="post-menu" name="post-menu" class="text" style="width:250px">
            <option value=""></option>
            <option value="0" <?php if ('0' == @$def['menu']) echo 'selected="selected"'; ?> ><?php i18n('i18n_specialpages/MENU_NO'); ?></option>
            <?php if ($isi18nnav) { ?>
            <option value="f" <?php if ('f' == @$def['menu']) echo 'selected="selected"'; ?> ><?php i18n('i18n_specialpages/MENU_FIRST_POS'); ?></option>
            <option value="l" <?php if ('l' == @$def['menu']) echo 'selected="selected"'; ?> ><?php i18n('i18n_specialpages/MENU_LAST_POS'); ?></option>
            <?php } ?>
            <?php if (false) { ?>
            <option value="s" <?php if ('s' == @$def['menu']) echo 'selected="selected"'; ?> ><?php i18n('i18n_specialpages/MENU_SLUG_POS'); ?></option>
            <option value="r" <?php if ('r' == @$def['menu']) echo 'selected="selected"'; ?> ><?php i18n('i18n_specialpages/MENU_REVSLUG_POS'); ?></option>
            <?php } ?>
          </select>
        </td>
        <td><?php i18n('i18n_specialpages/MENU_DESCR'); ?></td>
      </tr>
    </table> 
  </div>
  
  <div id="tab-fields" class="tab" style="display:none;">
    <p><?php i18n('i18n_specialpages/CONFIG_EDIT_FIELDS_DESCR'); ?></p>
    <table id="editfields" class="edittable highlight">
      <thead>
        <tr>
          <th><?php i18n('i18n_specialpages/FIELD_NAME'); ?></th>
          <th><?php i18n('i18n_specialpages/FIELD_LABEL'); ?></th>
          <th style="width:100px;"><?php i18n('i18n_specialpages/FIELD_TYPE'); ?></th>
          <th><?php i18n('i18n_specialpages/FIELD_DEFAULT_VALUE'); ?></th>
  <?php if ($issearch) { ?>
          <th><?php i18n('i18n_specialpages/FIELD_INDEX'); ?></th>
  <?php } ?>
          <th></th>
        </tr>
      </thead>
      <tbody>
  <?php
    $i = 0; 
    if (count(@$def['fields']) > 0) foreach ($def['fields'] as $cf) {
      i18n_specialpages_confline($i, $cf, 'sortable', $issearch);    
      $i++;
    }
    i18n_specialpages_confline($i, array(), 'hidden', $issearch); 
  ?> 
        <tr>
          <td colspan="5"><a href="#" class="add"><?php i18n('i18n_specialpages/ADD_FIELD'); ?></a></td>
          <td class="secondarylink"><a href="#" class="add" title="<?php i18n('i18n_specialpages/ADD_FIELD'); ?>">+</a></td>
        </tr>
      </tbody>
    </table>
    <p>
      <input type="hidden" id="post-defaultcontent" name="post-defaultcontent" value="<?php echo htmlspecialchars(@$def['defaultcontent']); ?>" />
      <a class="setcontent" href="#"><?php i18n('i18n_specialpages/SET_DEFAULT_CONTENT'); ?></a>
    </p>
    <div id="ed_textarea" class="dialog" style="display:none">
      <textarea id="ed_ta" name="ed_ta" style="height:200px;"></textarea>
      <br/>
      <button class="valueok"><?php i18n('OK'); ?></button>
      <button class="valuecancel"><?php i18n('CANCEL'); ?></button>
    </div>
    <div id="ed_wysiwyg" class="dialog" style="display:none">
      <textarea id="ed_cke" name="ed_cke" style="height:500px;"></textarea>
      <button class="valueok"><?php i18n('OK'); ?></button>
      <button class="valuecancel"><?php i18n('CANCEL'); ?></button>
    </div>
  </div>
  
  <div id="tab-view" class="tab" style="display:none;">
    <p><?php i18n('i18n_specialpages/CONFIG_EDIT_VIEW_DESCR'); ?></p>
    <div class="compdiv">
      <label for="post-headercomponent"><?php i18n('i18n_specialpages/HEADERCOMPONENT'); ?></label>
      <textarea id="post-headercomponent" name="post-headercomponent" style="height:200px;"><?php echo htmlspecialchars(@$def['headercomponent']); ?></textarea>
    </div>
    <div class="compdiv i18n-sp-comp">
      <?php if ($isi18n && $languages) { ?>
        <div class="i18n-sp-langsel">
          <a class="current" href="#showcomponent"><?php echo return_i18n_default_language(); ?></a>
          <?php foreach ($languages as $lang) { ?>
            <a href="#showcomponent_<?php echo $lang; ?>"><?php echo $lang; ?></a>
          <?php } ?>
        </div>
      <?php } ?>
      <label for="post-showcomponent"><?php i18n('i18n_specialpages/SHOWCOMPONENT'); ?></label>
      <div class="i18n-sp-wrapper" id="showcomponent" style="clear:both;">
        <textarea id="post-showcomponent" name="post-showcomponent" style="height:200px"><?php echo htmlspecialchars(@$def['showcomponent']); ?></textarea>
      </div>
      <?php if ($isi18n && $languages) foreach ($languages as $lang) { ?>
      <div class="i18n-sp-wrapper" id="showcomponent_<?php echo $lang; ?>" style="clear:both;display:none">
        <textarea id="post-showcomponent_<?php echo $lang; ?>" name="post-showcomponent_<?php echo $lang; ?>" style="height:200px;"><?php echo htmlspecialchars(@$def['showcomponent_'.$lang]); ?></textarea>
      </div>
      <?php } ?>
    </div>
  </div>
  
  <?php if ($issearch) { ?>
  <div id="tab-search" class="tab" style="display:none;">
    <p><?php i18n('i18n_specialpages/CONFIG_EDIT_SEARCH_DESCR'); ?></p>
    <div class="compdiv i18n-sp-comp">
      <?php if ($isi18n && $languages) { ?>
        <div class="i18n-sp-langsel">
          <a class="current" href="#searchcomponent"><?php echo return_i18n_default_language(); ?></a>
          <?php foreach ($languages as $lang) { ?>
            <a href="#searchcomponent_<?php echo $lang; ?>"><?php echo $lang; ?></a>
          <?php } ?>
        </div>
      <?php } ?>
      <label for="post-searchcomponent"><?php i18n('i18n_specialpages/SEARCHCOMPONENT'); ?></label>
      <div class="i18n-sp-wrapper" id="searchcomponent" style="clear:both;">
        <textarea id="post-searchcomponent" name="post-searchcomponent" style="height:200px"><?php echo htmlspecialchars(@$def['searchcomponent']); ?></textarea>
      </div>
      <?php if ($isi18n && $languages) foreach ($languages as $lang) { ?>
      <div class="i18n-sp-wrapper" id="searchcomponent_<?php echo $lang; ?>" style="clear:both;display:none">
        <textarea id="post-searchcomponent_<?php echo $lang; ?>" name="post-searchcomponent_<?php echo $lang; ?>" style="height:200px;"><?php echo htmlspecialchars(@$def['searchcomponent_'.$lang]); ?></textarea>
      </div>
      <?php } ?>
    </div>
    <label for="post-searchcomponent"><?php i18n('i18n_specialpages/DEFAULT_SEARCH_COMPONENT'); ?></label>
    <pre style="font-size:10px;line-height:14px;"><?php echo htmlspecialchars($defaultsearchcontent); ?>
    </pre>
  </div>
  <?php } ?>
  
  <input type="submit" name="save" value="<?php i18n('i18n_specialpages/SAVE'); ?>" class="submit"/>
  &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
  <a class="cancel" href="load.php?id=i18n_specialpages&amp;config"><?php i18n('CANCEL'); ?></a>
  <?php if (@$_GET['edit']) { ?>
    &nbsp;/&nbsp; 
    <a class="cancel" href="load.php?id=i18n_specialpages&amp;config&amp;delete=<?php echo htmlspecialchars($name); ?>"><?php i18n('i18n_specialpages/DELETE'); ?></a>
  <?php } ?>
  
</form>

<script type="text/javascript" src="../plugins/i18n_specialpages/js/jquery-ui.sort.min.js"></script>
<script type="text/javascript">
  function renumberCustomFields() {
    $('#editfields tbody tr').each(function(i,tr) {
      $(tr).find('input, select, textarea').each(function(k,elem) {
        var name = $(elem).attr('name').replace(/_\d+_/, '_'+(i)+'_');
        $(elem).attr('name', name);
      });
    });
  }
  $(function() {
    <?php I18nSpecialPagesBackend::outputCKEditorJS('ed_cke', 'editor', 730, 500); ?>
    $('#editfields select[name$=_type]').change(function(e) {
      var val = $(e.target).val();
      var $ta = $(e.target).closest('td').find('textarea');
      if (val == 'dropdown') $ta.css('display','inline'); else $ta.css('display','none');
      if (val == 'textarea' || val == 'wysiwyg') {
        $(e.target).closest('tr').find('[name$=value]').hide();
        $(e.target).closest('tr').find('.setvalue').show();
      } else {
        $(e.target).closest('tr').find('[name$=value]').show();
        $(e.target).closest('tr').find('.setvalue').hide();
      }
<?php if ($issearch) { ?>
      var $index = $(e.target).closest('tr').find('[name$=_index]');
      if (val == 'text' || val == 'dropdown') {
        $index.show();
        $index.find('[value=1]').show();
        $index.find('[value=2]').show();
        $index.find('[value=3]').hide();
        if ($index.val() == 3) $index.val('');
      } else if (val == 'textfull' || val == 'textarea' || val == 'wysiwyg') {
        $index.show();
        $index.find('[value=1]').show();
        $index.find('[value=2]').hide();
        $index.find('[value=3]').hide();
        if ($index.val() != 1) $index.val('');
			} else if (val == 'checkbox') {
        $index.show();
        $index.find('[value=1]').hide();
        $index.find('[value=2]').hide();
        $index.find('[value=3]').show();
        if ($index.val() != 3) $index.val('');
			} else {
        $index.val('').hide();
      }
<?php } ?>
    });
    $('#editfields a.delete').click(function(e) {
      $(e.target).closest('tr').remove();
      renumberCustomFields();
      e.preventDefault();
    });
    $('#editfields a.add').click(function(e) {
      var $tr = $(e.target).closest('tbody').find('tr.hidden');
      $tr.before($tr.clone(true).removeClass('hidden').addClass('sortable'));
      renumberCustomFields();
      e.preventDefault();
    });
    $('#editfields tbody').sortable({
      items:"tr.sortable", handle:'td',
      update:function(e,ui) { renumberCustomFields(); }
    });
    renumberCustomFields();
    $('.i18n-sp-langsel a').click(function(e) {
      $(e.target).closest('.i18n-sp-langsel').find('a').removeClass('current');
      $(e.target).addClass('current');
      $(e.target).closest('.i18n-sp-comp').find('.i18n-sp-wrapper').hide();
      $($(e.target).attr('href')).show().find('textarea').focus();
      e.preventDefault();
    });
    $('.tab-links a').click(function(e) {
      $('.tab-links a').removeClass('current');
      $(e.target).addClass('current');
      $('.tab').hide();
      var tabsel = $(e.target).attr('href');
      $(tabsel).show();
      if (tabsel == '#tab-view') {
        $('#headercomponent textarea:visible').focus();
        $('#showcomponent textarea:visible').focus();
      } else if (tabsel == '#tab-search') {
        $('#searchcomponent textarea:visible').focus();
      }
      e.preventDefault();
    });
    var $valuefield;
    $('a.setcontent').click(function(e) {
      e.preventDefault();
      $valuefield = $('#post-defaultcontent');
      editor.setData($valuefield.val());
      $('#ed_wysiwyg').dialog();
    });
    $('a.setvalue').click(function(e) {
      e.preventDefault();
      $valuefield = $(e.target).closest('tr').find('[name$=value]');
      if ($(e.target).closest('tr').find('[name$=type]').val() == 'wysiwyg') {
        editor.setData($valuefield.val());
        $('#ed_wysiwyg').dialog();
      } else {
        $('#ed_ta').val($valuefield.val());
        $('#ed_textarea').dialog();
      }
    });
    $('#ed_wysiwyg .valueok').click(function(e) {
      e.preventDefault();
      $valuefield.val(editor.getData());
      editor.setData('');
      $('#ed_wysiwyg').dialog('close');
    });
    $('#ed_wysiwyg .valuecancel').click(function(e) {
      e.preventDefault();
      editor.setData('');
      $('#ed_wysiwyg').dialog('close');
    });
    $('#ed_textarea .valueok').click(function(e) {
      e.preventDefault();
      $valuefield.val($('#ed_ta').val());
      $('#ed_ta').val('');
      $('#ed_textarea').dialog('close');
    });
    $('#ed_textarea .valuecancel').click(function(e) {
      e.preventDefault();
      $('#ed_ta').val('');
      $('#ed_textarea').dialog('close');
    });
<?php if (@$msg) { ?>
    $('div.bodycontent').before('<div class="<?php echo @$success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
    $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
  });
</script>
<?php

function i18n_specialpages_confline($i, $def, $class='', $issearch) {
  $isdropdown = @$def['type'] == 'dropdown';
  $indexable = !@$def['type'] || in_array(@$def['type'],array('text','textfull','dropdown','textarea','wysiwyg','checkbox'));
  $options = $isdropdown && count($def['options']) > 0 ? implode("\r\n", $def['options']) : '';
  if (substr($options,0,2) == "\r\n") $options = "\r\n".$options; // textarea removes first line break!
?>
      <tr class="<?php echo $class; ?>">
        <td><input type="text" class="text" style="width:80px;padding:2px;" name="cf_<?php echo $i; ?>_name" value="<?php echo htmlspecialchars(@$def['name']); ?>"/></td>
        <td><input type="text" class="text" style="width:140px;padding:2px;" name="cf_<?php echo $i; ?>_label" value="<?php echo htmlspecialchars(@$def['label']); ?>"/></td>
        <td>
          <select name="cf_<?php echo $i; ?>_type" class="text short" style="width:160px;padding:2px;" >
            <option value="text" <?php echo @$def['type']=='text' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/TEXT_FIELD'); ?></option>
            <option value="textfull" <?php echo @$def['type']=='textfull' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/LONG_TEXT_FIELD'); ?></option>
            <option value="dropdown" <?php echo @$def['type']=='dropdown' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/DROPDOWN_BOX'); ?></option>
            <option value="checkbox" <?php echo @$def['type']=='checkbox' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/CHECKBOX'); ?></option>
            <option value="textarea" <?php echo @$def['type']=='textarea' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/TEXTAREA'); ?></option>
            <option value="wysiwyg" <?php echo @$def['type']=='wysiwyg' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/WYSIWYG_EDITOR'); ?></option>
            <option value="image" <?php echo @$def['type']=='image' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/IMAGE'); ?></option>
            <option value="file" <?php echo @$def['type']=='file' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/FILE'); ?></option>
            <option value="link" <?php echo @$def['type']=='link' ? 'selected="selected"' : ''; ?> ><?php i18n('i18n_specialpages/LINK'); ?></option>
          </select>
          <textarea class="text" style="width:150px;height:50px;padding:2px;<?php echo !$isdropdown ? 'display:none' : ''; ?>" name="cf_<?php echo $i; ?>_options"><?php echo htmlspecialchars($options); ?></textarea> 
        </td>
        <td>
          <input type="text" class="text" style="width:100px;padding:2px;<?php if (@$def['type']=='textarea' || @$def['type']=='wysiwyg') echo 'display:none'; ?>" name="cf_<?php echo $i; ?>_value" value="<?php echo htmlspecialchars(@$def['value']); ?>"/>
          <a href="#" class="setvalue" style="<?php if (@$def['type']!='textarea' && @$def['type']!='wysiwyg') echo 'display:none'; ?>"><?php i18n('i18n_specialpages/SET_DEFAULT'); ?></a>
        </td>
<?php if ($issearch) { ?>
        <td>
          <select name="cf_<?php echo $i; ?>_index" class="text short" style="width:65px;padding:2px;<?php if (!$indexable) echo 'display:none;'; ?>" >
            <option value="" <?php if (!@$def['index']) echo 'selected="selected"'; ?> ></option>
            <option value="1" <?php if ((string) @$def['index'] == '1') echo 'selected="selected"'; ?> <?php if (@$def['type']=='checkbox') echo 'style="display:none"'; ?> ><?php i18n('i18n_specialpages/INDEX_WORDS'); ?></option>
            <option value="2" <?php if ((string) @$def['index'] == '2') echo 'selected="selected"'; ?> <?php if (@$def['type']!='text' && @$def['type']!='dropdown') echo 'style="display:none"'; ?> ><?php i18n('i18n_specialpages/INDEX_AS_TAG'); ?></option>
            <option value="3" <?php if ((string) @$def['index'] == '3') echo 'selected="selected"'; ?> <?php if (@$def['type']!='checkbox') echo 'style="display:none"'; ?> ><?php i18n('i18n_specialpages/INDEX_NAME_AS_TAG'); ?></option>
          </select>
        </td>
<?php } ?>
        <td class="delete"><a href="#" class="delete" title="<?php i18n('i18n_specialpages/DELETE_FIELD'); ?>">X</a></td>
      </tr>
<?php 
}



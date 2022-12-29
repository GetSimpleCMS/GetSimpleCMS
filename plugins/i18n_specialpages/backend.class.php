<?php

if (function_exists('return_i18n_pages')) {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
}

class I18nSpecialPagesBackend {
  
  public static function redirect($js=false) {
    global $metak,$url;
    if (basename($_SERVER['PHP_SELF']) == 'edit.php') {
      $tags = preg_split('/\s*,\s*/', (string) $metak);
      $isspecial = false;
      foreach ($tags as $tag) if (substr($tag,0,9) == '_special_') { $isspecial = true; break; }
      if (!$isspecial) return;
      $link = 'load.php?id=i18n_base&edit&url='.$url;
      if (!$js) {
        header('Location: '.$link);
      } else {
      ?>
      </head>
      <body>
        <a href="<? echo htmlspecialchars($link); ?>">Continue to I18N Special Pages ...</a>
        <script type="text/javascript">window.location = <?php echo json_encode($link); ?></script>
      </body>
      </html>
      <?php
      }
      exit(0);
    }           
  }

  public static function header() {
    if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages' && isset($_GET['config'])) {
    ?>
      <style type="text/css">
        div.i18n-sp-langsel {
          float: right;
          text-align: right;
          padding-right: 20px;
        }
        div.i18n-sp-langsel a {
          border-color: #AAAAAA;
          border-style: solid;
          border-width: 1px 1px 0 1px;
          padding: 2px 8px;
          border-radius: 5px 5px 0 0; 
          -moz-border-radius: 5px 5px 0 0; 
          font-weight: normal !important;
          text-decoration: none !important;
          outline-style: none;
        }
        div.i18n-sp-langsel a.current {
          font-weight: bolder !important;
          border-width: 2px 2px 0 2px;
          padding: 3px 8px;
        }
        .i18n-sp-comp label {
          padding-bottom: 2px;
        }
        .dialog {
          display:none;
          position:absolute;
          background:white;
          opacity:1;
          border:1px black solid; 
          padding:5px 10px;
          box-shadow:10px 10px 20px #000000;
          -moz-box-shadow:10px 10px 20px #000000;
          -webkit-box-shadow:10px 10px 20px #000000;
        }
      </style>
      <script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
      <script type="text/javascript" src="../plugins/i18n_specialpages/js/jquery.dialog.js"></script>
    <?php  
    } else if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages' && isset($_GET['pages'])) {
      global $SITEURL;
      ?>  
      <link rel="stylesheet" href="<?php echo $SITEURL ?>plugins/i18n_specialpages/css/jquery.autocomplete.css" type="text/css" charset="utf-8" />
      <style type="text/css">
        #editpages tr.invisible, #editpages tr.nomatch { display: none; }
        #editpages tr.invisible.match { display: table-row; }
        #editpages tr.invisible.match a.title { color: gray; }
      </style>
      <script type="text/javascript" src="<?php echo $SITEURL ?>plugins/i18n_specialpages/js/jquery.autocomplete.min.js"></script>
      <?php
    } else if (basename($_SERVER['PHP_SELF']) == 'edit.php') {
    ?>
      <style type="text/css">
        form #metadata_window table.specialtable { width: 100%; }
        form #metadata_window table.specialtable td { width: 50%; }
        form #metadata_window table.specialtable td .cke_editor td:first-child { padding: 0; }
        form #metadata_window table.specialtable .cke_editor { width:610px; }
        form #metadata_window table.specialtable .cke_editor td.cke_top { border-bottom: 1px solid #AAAAAA; }
        form #metadata_window table.specialtable .cke_editor td.cke_contents { border: 1px solid #AAAAAA; }
        #specialpagesForm .hidden { display:none; }
      </style>
    <?php
      # hack to ensure that i18n special pages action is called last:
      global $plugins;
      for ($i=0; $i<count($plugins); $i++) {
        if ($plugins[$i]['function'] == 'i18n_specialpages_edit') {
          $item = $plugins[$i];
          unset($plugins[$i]);
          $plugins[] = $item;
          break;
        }
      }
    }
  }

  public static function strip($value) {
    return get_magic_quotes_gpc() ? stripslashes($value) : $value;
  }

  public static function save(){
    global $USR, $xml; // SimpleXML to save to
    require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
    $name = @$_POST['post-special'];
    if ($name) {
      $def = I18nSpecialPages::getSettings($name);
      if ($def) {
        // add a special tag (for search)
        $tags = array('_special_'.$name);
        $origtags = preg_split('/\s*,\s*/', trim(self::strip(@$_POST['post-metak'])));
        if (count($origtags) > 0) foreach ($origtags as $tag) {
          if ($tag && substr($tag,0,9) != '_special_') $tags[] = $tag;
        }
        unset($xml->meta);
        $xml->addChild('meta')->addCData(safe_slash_html(implode(', ',$tags)));
        // add field to identify special page type
        $xml->addChild('special', htmlspecialchars($name));
        // add special fields:
        if (count(@$def['fields']) > 0) foreach ($def['fields'] as $field) {
          $name = $field['name'];
          if (isset($_POST['post-sp-'.strtolower($name)])) { 
            $xml->addChild(strtolower($name))->addCData(self::strip($_POST['post-sp-'.strtolower($name)])); 
          }
        } 
      } 
    }
    // new field for creation date
    if (!isset($xml->creDate)) {
      $date = @$_POST['special-creDate'] ? $_POST['special-creDate'] : (string) $xml->pubDate;
      $xml->addChild('creDate', $date);
    }
    // new field for user
    if (isset($USR) && $USR && !isset($xml->user)) {
      $xml->addChild('user')->addCData($USR);
    }
  }
  
  public static function outputCustomizeCKEditorJS($editorvar) { // copied and modified from ckeditor_add_page_link()
    ?>
      // modify existing Link dialog
      CKEDITOR.on( 'dialogDefinition', function( ev ) {
        if ((ev.editor != <?php echo $editorvar; ?>) || (ev.data.name != 'link')) return;
    
        // Overrides definition.
        var definition = ev.data.definition;
        definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original) {
          return function() {
            original.call(this);
              if (this.getValueOf('info', 'linkType') == 'localPage') {
                this.getContentElement('info', 'localPage_path').select();
              }
          };
        });
    
        // Overrides linkType definition.
        var infoTab = definition.getContents('info');
        var content = getById(infoTab.elements, 'linkType');
    
        content.items.unshift(['Link to local page', 'localPage']);
        content['default'] = 'localPage';
        infoTab.elements.push({
          type: 'vbox',
          id: 'localPageOptions',
          children: [{
            type: 'select',
            id: 'localPage_path',
            label: 'Select page:',
            required: true,
            items: <?php echo self::listPagesJSON(); ?>,
            setup: function(data) {
              if ( data.localPage )
                this.setValue( data.localPage );
            }
          }]
        });
        content.onChange = CKEDITOR.tools.override(content.onChange, function(original) {
          return function() {
            original.call(this);
            var dialog = this.getDialog();
            var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
            if (this.getValue() == 'localPage') {
              element.show();
              if (<?php echo $editorvar; ?>.config.linkShowTargetTab) {
                dialog.showPage('target');
              }
              var uploadTab = dialog.definition.getContents('upload');
              if (uploadTab && !uploadTab.hidden) {
                dialog.hidePage('upload');
              }
            }
            else {
              element.hide();
            }
          };
        });
        content.setup = function(data) {
          if (!data.type || (data.type == 'url') && !data.url) {
            data.type = 'localPage';
          }
          else if (data.url && !data.url.protocol && data.url.url) {
            if (path) {
              data.type = 'localPage';
              data.localPage_path = path;
              delete data.url;
            }
          }
          this.setValue(data.type);
        };
        content.commit = function(data) {
          data.type = this.getValue();
          if (data.type == 'localPage') {
            data.type = 'url';
            var dialog = this.getDialog();
            dialog.setValueOf('info', 'protocol', '');
            dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
          }
        };
      });
    <?php
  }
  
  public static function outputCKEditorJS($fieldname, $editorvar, $width=730, $height=500) {
    global $SITEURL, $TEMPLATE;
    if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else { $EDLANG = i18n_r('CKEDITOR_LANG'); }
    if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else { $EDTOOL = 'basic'; }
    if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {  $EDOPTIONS = ''; }
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
    ?>
    <?php echo $editorvar; ?> = CKEDITOR.replace(<?php echo json_encode($fieldname); ?>, {
          skin : 'getsimple',
          forcePasteAsPlainText : true,
          language : '<?php echo $EDLANG; ?>',
          defaultLanguage : 'en',
          <?php if (file_exists(GSTHEMESPATH .$TEMPLATE."/editor.css")) { 
            $fullpath = suggest_site_path();
          ?>
          contentsCss: '<?php echo $fullpath; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
          <?php } ?>
          entities : true,
          uiColor : '#FFFFFF',
          height: '200px',
          baseHref : '<?php echo $SITEURL; ?>',
          toolbar : [ <?php echo $toolbar; ?> ]
          <?php echo $EDOPTIONS; ?>,
          tabSpaces:10,
          filebrowserBrowseUrl : 'filebrowser.php?type=all',
          filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
          filebrowserWindowWidth : '<?php echo $width; ?>',
          filebrowserWindowHeight : '<?php echo $height; ?>'
    });
    <?php
    self::outputCustomizeCKEditorJS($editorvar);
  }

  private static function listPagesJSON() {
    if (function_exists('find_i18n_url') && class_exists('I18nNavigationFrontend')) {
      $slug = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['newid']) ? $_GET['newid'] : '');
      $pos = strpos($slug, '_');
      $lang = $pos !== false ? substr($slug, $pos+1) : null;
      $structure = I18nNavigationFrontend::getPageStructure(null, false, null, $lang);
      $pages = array();
      $nbsp = html_entity_decode('&nbsp;', ENT_QUOTES, 'UTF-8');
      $lfloor = html_entity_decode('&lfloor;', ENT_QUOTES, 'UTF-8');
      foreach ($structure as $page) {
        $text = ($page['level'] > 0 ? str_repeat($nbsp,5*$page['level']-2).$lfloor.$nbsp : '').cl($page['title']);
        $link = find_i18n_url($page['url'], $page['parent'], $lang ? $lang : return_i18n_default_language());
        $pages[] = array($text, $link);
      }
      return json_encode($pages);
    } else {
      return list_pages_json();
    }
  }

  public static function updatePages($withName, $toName, $updateSlug = false) {
    require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
    if (!$withName) return;
    $def = $toName ? I18nSpecialPages::getSettings($toName) : null;
    // first handle all files without '_', as we need to build a slug update map, if I18N is installed (otherwise it doesn't matter)
    $updateMap = array();
    $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
    while ($filename = readdir($dir_handle)) {
      if (substr($filename,-4) == '.xml' && strrpos($filename,'_') === false && !is_dir(GSDATAPAGESPATH . $filename)) {
        $xml = simplexml_load_file(GSDATAPAGESPATH.$filename, 'SimpleXMLExtended');
        if ($withName == (string) @$xml->special) {
          $oldurl = (string) $xml->url;
          $xml = self::updatePageXML($xml, $def, $updateSlug);
          $updateMap[$oldurl] = (string) $xml->url;
          if ($oldurl != (string) $xml->url) unlink(GSDATAPAGESPATH . $oldurl . '.xml');
          XMLsave($xml, GSDATAPAGESPATH . ((string) $xml->url) . '.xml');
        }
      }
    }
    $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
    while ($filename = readdir($dir_handle)) {
      if (substr($filename,-4) == '.xml' && strrpos($filename,'_') !== false && !is_dir(GSDATAPAGESPATH . $filename)) {
        $xml = simplexml_load_file(GSDATAPAGESPATH.$filename, 'SimpleXMLExtended');
        if ($withName == (string) @$xml->special) {
          $oldurl = (string) $xml->url;
          $xml = self::updatePageXML($xml, $def, $updateSlug, $updateMap);
          if ($oldurl != (string) $xml->url) unlink(GSDATAPAGESPATH . $oldurl . '.xml');
          XMLsave($xml, GSDATAPAGESPATH . ((string) $xml->url) . '.xml');
        }
      }
    }
  }
  
  private static function updatePageXML($xml, $def, $updateSlug = false, $updateMap = array()) {
    if (!$def) {
      // special page type is deleted
      unset($xml->special);
      $tags = array();
      $origtags = @preg_split('/\s*,\s*/', (string) $xml->meta);
      if (count($origtags) > 0) foreach ($origtags as $tag) {
        if ($tag && substr($tag,0,9) != '_special_' && !in_array($tag, $tags)) $tags[] = $tag;
      }
      unset($xml->meta);
      $xml->addChild('meta')->addCData(implode(', ', $tags));
      return $xml; 
    }
    unset($xml->special);
    $xml->addChild('special', $def['name']);
    $tags = @preg_split('/\s*,\s*/', $def['tags']);
    if (!in_array('_special_'.$def['name'], $tags)) $tags[] = '_special_'.$def['name'];
    $origtags = @preg_split('/\s*,\s*/', (string) $xml->meta);
    if (count($origtags) > 0) foreach ($origtags as $tag) {
      if ($tag && substr($tag,0,9) != '_special_' && !in_array($tag, $tags)) $tags[] = $tag;
    }
    unset($xml->meta);
    $xml->addChild('meta')->addCData(implode(', ', $tags));
    if ($updateSlug && $def['slug']) {
      global $TIMEZONE;
      $isi18n = function_exists('i18n_init');
      if ($TIMEZONE != '') {
        if (function_exists('date_default_timezone_set')) {
          date_default_timezone_set($TIMEZONE);
        }
      }
      $oldurl = (string) $xml->url;
      $suffix = '';
      if ($isi18n) {
        $pos = strrpos($oldurl,'_');
        if ($pos > 0) {
          $suffix = substr($oldurl, $pos);
          $oldurl = substr($oldurl, 0, $pos); 
        }
      }
      unset($xml->url);
      if ($suffix && @$updateMap[$oldurl]) {
        // we cannot simply update the URL of a language file, as it must be the same as that of the default language
        $xml->addChild('url')->addCData($updateMap[$oldurl].$suffix);
      } else {
        $creDate = strtotime((string) $xml->creDate);
        $newurl = strftime($def['slug'], $creDate);
        $fullurl = $newurl . $suffix;
        if ($newurl != $oldurl) {
          // make sure that url does not yet exist:
          $i = 1;
          while (file_exists(GSDATAPAGESPATH . $fullurl . '.xml')) {
            $fullurl = $newurl . '-' . $i . $suffix;
            $i++;
          }
        }
        $xml->addChild('url')->addCData($fullurl);
      }
    }
    if ($def['parent']) {
      unset($xml->parent);
      $xml->addChild('parent')->addCData($def['parent']);
    }
    if ($def['template']) {
      unset($xml->template);
      $xml->addChild('template')->addCData($def['template']);
    }
    if ($def['menu']) {
      unset($xml->menuStatus);
      $xml->addChild('menuStatus')->addCData($def['menu'] == '0' ? 'N' : 'Y');
    }
    return $xml;
  }

  
}

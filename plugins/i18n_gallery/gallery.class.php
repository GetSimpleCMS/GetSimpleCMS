<?php

class I18nGallery {
  
  private static $settings = null;
  private static $plugins = null;
  
  
  public static function getSettings($reload=false) {
    if (self::$settings != null && !$reload) return self::$settings;
    self::$settings = array();
    if (file_exists(GSDATAOTHERPATH.'i18n_gallery_settings.xml')) {
      $data = getXML(GSDATAOTHERPATH.'i18n_gallery_settings.xml');
      if ($data) {
        foreach ($data as $key => $value) self::$settings[$key] = (string) $value;
      }
    }
    return self::$settings;
  }
  
  public static function getPlugins() {
    if (self::$plugins == null) {
      self::$plugins = array();
      $dir_handle = @opendir(GSPLUGINPATH.'i18n_gallery');
      while ($filename = readdir($dir_handle)) {
        if (substr($filename,0,7) == 'plugin_' && strrpos($filename,'.php') === strlen($filename)-4) {
          include_once(GSPLUGINPATH.'i18n_gallery/'.$filename);
        }
      }
    }
    return self::$plugins;
  }
  
  public static function registerPlugin($type, $name, $description, $edit_function, $header_function, $content_function) {
    self::$plugins[$type] = array(
      'type' => $type,
      'name' => $name,
      'description' => $description,
      'edit' => $edit_function,
      'header' => $header_function,
      'content' => $content_function
    );
  }
  
  public static function checkPrerequisites() {
    $success = true;
    $gdir = GSDATAPATH . I18N_GALLERY_DIR;
    if (!file_exists($gdir)) {
      $success = mkdir(substr($gdir,0,strlen($gdir)-1), 0777) && $success;
      $fp = fopen($gdir . '.htaccess', 'w');
      fputs($fp, 'Deny from all');
      fclose($fp);
    }
    $gdir = GSBACKUPSPATH . I18N_GALLERY_DIR;
    // create directory if necessary
    if (!file_exists($gdir)) {
      $success = @mkdir(substr($gdir,0,strlen($gdir)-1), 0777) && $success;
      $fp = @fopen($gdir . '.htaccess', 'w');
      if ($fp) {
        fputs($fp, 'Deny from all');
        fclose($fp);
      }
    }
    return $success;
  }
  
  public static function getGallery($name) {
    $gallery = array('items' => array());
    if (!file_exists(GSDATAPATH.'i18n_gallery/'.$name.'.xml')) return $gallery;
    $data = getXML(GSDATAPATH . I18N_GALLERY_DIR . $name . '.xml');
    if (!$data) return $gallery;
    $dofilter = basename($_SERVER['PHP_SELF']) == 'index.php';
    foreach ($data as $key => $value) {
      if ($key != 'item' && $key != 'items') {
        $gallery[$key] = (string) $value;
      } else {
        $include = true;
        if ($dofilter) {
          global $filters;
          $filename = (string) $value->filename;
          $tags = preg_split('/\s*,\s*/', (string) $value->tags);
          foreach ($filters as $filter)  {
            if ($filter['filter'] == 'image-veto') {
              if (call_user_func_array($filter['function'], array($name, $filename, $tags))) {
                $include = false;
                break;
              }
            }
          }
        }
        if ($include) {
          $item = array();
          foreach ($value as $itemkey => $itemvalue) {
            $item[$itemkey] = (string) $itemvalue;
          }
          $gallery['items'][] = $item;
        }
      }
    }
    return $gallery;
  }
  
  public static function getGalleryFromParams($params, $ignoreQuery=false, $ignoreSettings=false, $lang=null) {
    if (!$ignoreQuery) {
      if (!@$params['name'] && @$_GET['name']) $params['name'] = $_GET['name'];
      if (!@$params['type'] && @$_GET['type']) $params['type'] = $_GET['type'];
      if (!@$params['tags'] && @$_GET['imagetags']) $params['tags'] = $_GET['imagetags'];
    }
    if (!@$params['name']) return null;
    $gallery = self::getGallery($params['name']);
    if (!$gallery || !@$gallery['type']) return null;
    foreach ($params as $key => $value) $gallery[$key] = $value;
    if (@$params['tags']) {
      // filter images
      $tags = preg_split('/\s*,\s*/', trim($params['tags']));
      $newitems = array();
      foreach ($gallery['items'] as $item) {
        if (!@$item['tags']) continue;
        $itemtags = preg_split('/\s*,\s*/', trim($item['tags']));
        if (count(array_intersect($tags, $itemtags)) == count($tags)) $newitems[] = $item;
      }
      $gallery['items'] = $newitems;
    }
    if (!$ignoreSettings) {
      // add settings
      $settings = self::getSettings();
      if (!@$gallery['thumbwidth'] && !@$gallery['thumbheight']) {
        if (intval(@$settings['thumbwidth']) > 0 || intval(@$settings['thumbheight']) > 0) {
          $gallery['thumbwidth'] = intval(@$settings['thumbwidth']) > 0 ? intval($settings['thumbwidth']) : null;
          $gallery['thumbheight'] = intval(@$settings['thumbheight']) > 0 ? intval($settings['thumbheight']) : null;
          $gallery['thumbcrop'] = @$settings['thumbcrop'];
        } else {
          $gallery['thumbwidth'] = I18N_GALLERY_DEFAULT_THUMB_WIDTH;
          $gallery['thumbheight'] = I18N_GALLERY_DEFAULT_THUMB_HEIGHT;
          $gallery['thumbcrop'] = 0;
        }
      }
      if (count($settings) > 0) {
        if (!isset($gallery['jquery']) && isset($settings['jquery'])) $gallery['jquery'] = $settings['jquery'];
        if (!isset($gallery['css']) && isset($settings['css'])) $gallery['css'] = $settings['css'];
        if (!isset($gallery['js']) && isset($settings['js'])) $gallery['js'] = $settings['js'];
        if (!@$gallery['width'] && !@$gallery['height']) {
          if (intval(@$settings['width']) > 0) $gallery['width'] = intval($settings['width']);
          if (intval(@$settings['height']) > 0) $gallery['height'] = intval($settings['height']);
        }
      }
    }
    // get best language texts
    if (function_exists('return_i18n_languages')) {
      global $language;
      //$languages = return_i18n_languages();
      if (!$lang) $lang = $language;
      $deflang = return_i18n_default_language();
      $languages = @$lang && $lang != $deflang ? array($lang, $deflang) : array($deflang);
      foreach ($languages as $lang) {
        $fullkey = 'title' . ($lang == $deflang ? '' : '_' . $lang);
        if (isset($gallery[$fullkey])) { $gallery['_title'] = $gallery[$fullkey]; break; }
      }
      foreach ($gallery['items'] as &$item) {
        foreach ($languages as $lang) {
          $fullkey = 'title' . ($lang == $deflang ? '' : '_' . $lang);
          if (isset($item[$fullkey])) { $item['_title'] = $item[$fullkey]; break; }
        }
        foreach ($languages as $lang) {
          $fullkey = 'description' . ($lang == $deflang ? '' : '_' . $lang);
          if (isset($item[$fullkey])) { $item['_description'] = $item[$fullkey]; break; }
        }
      }
    } else {
      $gallery['_title'] = $gallery['title'];
      foreach ($gallery['items'] as &$item) {
        $item['_title'] = $item['title'];
        $item['_description'] = $item['description'];
      }
    }
    return $gallery;
  }
  
  public static function getGalleryFromParamString($paramstr, $ignoreQuery=false, $ignoreSettings=false, $lang=null) {
    $params = array();
    $paramstr = @$paramstr ? html_entity_decode(trim($paramstr), ENT_QUOTES, 'UTF-8') : '';
    while (preg_match('/^([a-zA-Z][a-zA-Z_-]*)[:=]([^"\'\s]*|"[^"]*"|\'[^\']*\')(?:\s|$)/', $paramstr, $pmatch)) {
      $key = $pmatch[1];
      $value = trim($pmatch[2]);
      if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1,strlen($value)-2);
      $params[$key] = $value;
      $paramstr = substr($paramstr, strlen($pmatch[0]));
    }
    return self::getGalleryFromParams($params, $ignoreQuery, $ignoreSettings, $lang);
  }

  public static function index($item) {
    $content = stripslashes(htmlspecialchars($item->data->content));
    if (preg_match_all("/\(%\s*(gallery)(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", $content, $matches)) {
      $i = 0;
      foreach ($matches[2] as $params) {
        $gallery = self::getGalleryFromParamString($params,true,true,$item->language);
        if ($gallery) {
          $text = '';
          foreach ($gallery['items'] as &$galitem) {
            $text .= $galitem['_title'] . ' ' . $galitem['_description'] . ' ';
          }
          $item->addContent('i18n_gallery_'.$i, html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
          $i++;
        }
      }
    }
  }
  
  
  
}
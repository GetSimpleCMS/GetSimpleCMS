<?php

class I18nSpecialPages {
  
  private static $settings = null;
  private static $complete = false;
  private static $fields = null;
  private static $item = null;
  
  public static function getSettings($name=null) {
    if ($name && self::$settings && isset(self::$settings[$name])) {
      // nothing to do - settings already loaded
    } else if (!self::$settings || !self::$complete) {
      if (!self::$settings) self::$settings = array();
      self::$complete = true;
      if ($dh = opendir(GSDATAOTHERPATH)) {
        while ($filename = readdir($dh)) {
          if (substr($filename,0,13) == 'i18n_special_' && substr($filename,strlen($filename)-4) == '.xml') {
            $n = substr($filename,13,strlen($filename)-17);
            if (isset(self::$settings[$n])) {
              // do nothing
            } else if ($name == null || $filename == 'i18n_special_'.$name.'.xml') {
              // load settings
              $sp = self::loadSettings(GSDATAOTHERPATH, $filename);
              self::$settings[$sp['name']] = $sp;
            } else {
              // do not process - settings are not complete
              self::$complete = false;
            }
          }
        }
        closedir($dh);
      }
    }
    return $name ? self::$settings[$name] : self::$settings;
  }
  
  public static function loadSettings($dir, $filename) {
    if (substr($dir,-1) != '/') $dir .= '/';
    $data = getXML($dir.$filename);
    $n = substr($filename,13,strlen($filename)-17);
    $sp = array();
    $sp['name'] = $n;
    $sp['title'] = (string) $data->title;
    $sp['parent'] = (string) $data->parent;
    $sp['tags'] = (string) $data->tags;
    $sp['slug'] = (string) $data->slug;
    $sp['template'] = (string) $data->template;
    $sp['menu'] = (string) $data->menu;
    $sp['headercomponent'] = (string) $data->headercomponent;
    foreach ($data as $child) {
      $key = $child->getName();
      if (substr($key,0,13) == 'showcomponent' || substr($key,0,15) == 'searchcomponent') {
        $sp[$key] = (string) $child;
      }
    }
    $sp['defaultcontent'] = (string) $data->defaultcontent;
    $items = $data->fields->item;
    if (count($items) > 0) {
      foreach ($items as $item) {
        $sf = array();
        $sf['name'] = (string) $item->name;
        $sf['label'] = (string) $item->label;
        $sf['type'] = (string) $item->type;
        $sf['value'] = (string) $item->value;
        if ($item->type == "dropdown") {
          $sf['options'] = array();
          foreach ($item->option as $option) {
            $sf['options'][] = (string) $option;
          }
        }
        $sf['index'] = (string) $item->index;
        $sp['fields'][] = $sf;
      }
    } 
    return $sp;
  }
  
  public static function initializeFields() {
    global $data_index, $data_index_orig;
    if (function_exists('i18n_init')) {
      i18n_init(); // make sure that I18N is initialized
      if (isset($data_index_orig)) self::getFields($data_index_orig);
    }
    self::getFields($data_index);
  }
  
  public static function setItem($item) {
    self::$item = $item;
  }
  
  public static function outputField($name, $default='', $isHTML=true) {
    if (self::$item) {
      if (@self::$item->$name) {
        echo $isHTML ? self::$item->$name : htmlspecialchars(self::$item->$name);
        return true;
      } else {
        echo $isHTML ? $default : htmlspecialchars($default);
        return false;
      }
    } else {
      if (@self::$fields[$name]) {
        echo $isHTML ? self::$fields[$name] : htmlspecialchars(self::$fields[$name]);
        return true;
      } else {
        echo $isHTML ? $default : htmlspecialchars($default);
        return false;
      }
    }
  }
  
  public static function getField($name, $default='') {
    if (@self::$item) {
      return @self::$item->$name ? self::$item->$name : $default;
    } else {
      return @self::$fields[$name] ? self::$fields[$name] : $default;
    }
  } 
  
  public static function getExcerpt($name, $length) {
    if (function_exists('get_i18n_search_results')) {
      require_once(GSPLUGINPATH.'i18n_search/searcher.class.php');
      return new I18nSearchExcerpt(self::getField($name), $length);
    } 
    return null;
  }
  
  public static function getDate($name, $format=null) {
    global $TIMEZONE;
    if ($TIMEZONE != '' && function_exists('date_default_timezone_set')) {
      date_default_timezone_set($TIMEZONE);
    }
    if (!$format) {
      $format = i18n_r('i18n_specialpages/DATE_FORMAT');
      if (!$format) $format = '%Y-%m-%d %H:%M:%S';
    }
    $date = @self::getField($name);
    if ($date && !is_numeric($date)) $date = strtotime($date); else $date = (int) $date;
    return $date ? strftime($format, $date) : null;
  }
  
  public static function outputDate($name, $format=null) {
    $s = self::getDate($name, $format);
    echo $s ? htmlspecialchars($s) : '';
  }
  
  public static function getImage($name, $w=null, $h=null, $crop=true) {
    global $SITEURL;
    if (!$w && !$h) return self::getField($name);
    $pic = self::getField($name);
    $pic = substr($pic, strpos($pic, 'data/uploads/')+13);
    if (!$pic) return null;
    return $SITEURL.'plugins/i18n_specialpages/browser/pic.php?'.
           ($w ? 'w='.urlencode($w).'&' : '').($h ? 'h='.urlencode($h).'&' : '').($crop ? 'c=1&' : '').
           'p='.urlencode($pic);
  }
  
  public static function outputImage($name, $title, $w=null, $h=null, $crop=true) {
    $link = self::getImage($name, $w, $h, $crop);
    if ($link) {
      echo '<img src="'.htmlspecialchars($link).'"';
      if ($title) {
        echo ' title="'.htmlspecialchars($title).'" alt="'.htmlspecialchars($title).'"';
      }
      echo ' />';
    }
  }

  public static function outputTags($slug=null, $separator=' ', $all=false) {
    $tags = @self::getField('tags', null);
    if ($tags && count($tags) > 0) {
      $type = @self::getField('special',null);
      $first = true;
      foreach ($tags as $tag) {
        if (substr($tag,0,1) != '_') {
          if (!$first) echo $separator; else $first = false;
          if ($slug) {
            $url = function_exists('find_i18n_url') ? find_i18n_url($slug,null) : find_url($slug,null);
            $url .= '?tags='.urlencode(str_replace(" ","_",$tag)).(!$all && $type ? ' _special_'.urlencode($type) : '');
            echo '<a href="'.htmlspecialchars($url).'">'.htmlspecialchars($tag).'</a>';
          } else {
            echo htmlspecialchars($tag);
          }
        }
      }
    }
  }

  public static function outputHeader($data) {
    if (!@$data->special) return null;
    $def = self::getSettings((string) $data->special);
    if ($def && @$def['headercomponent']) {
      $component = $def['headercomponent'];
      self::outputHeaderComponent($component);
    }
  }
  
  public static function processContent($data) {
    if (!@$data->special) return null;
    if (self::$fields === null) self::getFields($data);
    $def = self::getSettings((string) $data->special);
    if ($def && @$def['showcomponent']) {
      $component = $def['showcomponent'];
      if (function_exists('return_i18n_languages')) {
        $deflang = return_i18n_default_language();
        foreach (return_i18n_languages() as $lang) {
          if ($lang == $deflang) break;
          if (isset($def['showcomponent_'.$lang]) && $def['showcomponent_'.$lang]) {
            $component = $def['showcomponent_'.$lang];
            break;
          }
        }
      }
      return self::processShowComponent($component);
    } 
    return null; 
  }
  
  public static function outputSearchItem($item, $showLanguage, $showDate, $dateFormat, $numWords) {
    if (!@$item->special) return null;
    $def = self::getSettings((string) $item->special);
    if ($def && @$def['searchcomponent']) {
      $component = $def['searchcomponent'];
      if (function_exists('i18n_init')) {
        i18n_init(); // make sure I18N initialization is completed
        $deflang = return_i18n_default_language();
        foreach (return_i18n_languages() as $lang) {
          if ($lang == $deflang) break;
          if (isset($def['searchcomponent_'.$lang]) && $def['searchcomponent_'.$lang]) {
            $component = $def['searchcomponent_'.$lang];
            break;
          }
        }
      }
      self::setItem($item);
      self::outputSearchComponent($component, $showLanguage, $showDate, $dateFormat, $numWords);
      self::setItem(null);
      return true;
    } 
    return false; 
  }
  
  private static function processShowComponent($component) {
    ob_start();
    eval("?>" . $component . "<?php ");
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }
  
  private static function outputSearchComponent($component, $showLanguage, $showDate, $dateFormat, $numWords) {
    eval("?>" . $component . "<?php ");
  }
  
  private static function outputHeaderComponent($component) {
    eval("?>" . $component . "<?php ");
  }
  
  private static function getFields($data) {
    $stdfields = array('pubDate','title','url','meta','metad','menu','menuStatus','menuOrder',
                        'template','parent','content','private','user','creDate',
                        'tags','pubTime','creTime');
    if ($data) {
      $fields = array();
      $fields['url'] = $fields['slug'] = (string) $data->url;
      $fields['title'] = html_entity_decode(stripslashes((string) $data->title), ENT_QUOTES, 'UTF-8');
      $fields['content'] = html_entity_decode(stripslashes((string) $data->content), ENT_QUOTES, 'UTF-8');
      $fields['pubDate'] = (string) $data->pubDate;
      $fields['creDate'] = (string) $data->creDate;
      $fields['pubTime'] = @strtotime($data->pubDate);
      $fields['creTime'] = @strtotime($data->creDate);
      $fields['user'] = (string) $data->user;
      $fields['tags'] = preg_split('/\s*,\s*/', trim(html_entity_decode(stripslashes((string) $data->meta), ENT_QUOTES, 'UTF-8')));
      $fields['meta-description'] = html_entity_decode(stripslashes((string) $data->metad), ENT_QUOTES, 'UTF-8');
      foreach ($data->children() as $child) {
        if (!in_array($child->getName(), $stdfields) && (string) $child) {
          $fields[$child->getName()] = (string) $child;
        }
      }
      self::$fields = $fields;
    }
    return self::$fields;
  }
  
  
  
}
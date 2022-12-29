<?php

class I18nNavigationFrontend {

  private static $pages = null;

  public static function redirectIfLink() {
    if (function_exists('return_custom_field')) {
      if (function_exists('i18n_init')) i18n_init();
      if (function_exists('i18n_get_custom_fields')) i18n_get_custom_fields();
      $link = return_custom_field('link');
      if ($link) {
        header('Location: '.$link);
        exit(0);
      }
    }
  }

  public static function getPages() {
    if (self::$pages) return self::$pages;
    $cachefile = GSDATAOTHERPATH . I18N_CACHE_FILE;
    if (!I18N_USE_CACHE || !file_exists($cachefile)) {
      // read pages into associative array
      self::$pages = array();
      $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
      while ($filename = readdir($dir_handle)) {
        if (strrpos($filename,'.xml') === strlen($filename)-4 && !is_dir(GSDATAPAGESPATH . $filename)) {
          $data = getXML(GSDATAPAGESPATH . $filename);
          if (str_contains($filename,'_')) {
            $pos = strpos($data->url,'_');
            $url = substr($data->url,0,$pos);
            $lang = substr($data->url,$pos+1);
            if (!isset(self::$pages[$url])) {
              self::$pages[$url] = array('url' => $url);
            }
            $menu = ((string) $data->menu ? (string) $data->menu : (string) $data->title);
            $title = ((string) $data->title ? (string) $data->title : (string) $data->menu);
            if ($menu) self::$pages[$url]['menu_'.$lang] = stripslashes($menu);
            if ($title) self::$pages[$url]['title_'.$lang] = stripslashes($title);
            if (isset($data->link) && (string) $data->link) self::$pages[$url]['link_'.$lang] = (string) $data->link;
          } else {
            $url = (string) $data->url;
            if (!isset(self::$pages[$url])) {
              self::$pages[$url] = array('url' => $url);
            }
            self::$pages[$url]['menuStatus'] = (string) $data->menuStatus;
            self::$pages[$url]['menuOrder'] = (int) $data->menuOrder;
            self::$pages[$url]['menu'] = stripslashes($data->menu);
            self::$pages[$url]['title'] = stripslashes($data->title);
            self::$pages[$url]['parent'] = (string) $data->parent;
            self::$pages[$url]['private'] = (string) $data->private;
            self::$pages[$url]['tags'] = (string) stripslashes($data->meta);
            if (isset($data->link) && (string) $data->link) self::$pages[$url]['link'] = (string) $data->link;
          }
        }
      }
      // sort pages
      $urlsToDelete = array();
      $sortedpages = array();
      foreach (self::$pages as $url => $page) {
        if (isset($page['parent']) && $page['private'] != 'Y') {
          $sortedpages[] = array('url' => $url, 'parent' => $page['parent'],
             'sort' => sprintf("%s%03s%s", $page['parent'], $page['menuOrder'], $url));
        } else {
          $urlsToDelete[] = $url;
        }
      }
      $sortedpages = subval_sort($sortedpages,'sort');
      if (count($urlsToDelete) > 0) foreach ($urlsToDelete as $url) unset(self::$pages[$url]);
      // save cache file
      if (I18N_USE_CACHE) {
        $data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><pages></pages>');
        foreach ($sortedpages as $sortedpage) {
          $url = $sortedpage['url'];
          $page = self::$pages[$url];
          $pagedata = $data->addChild('page');
          foreach ($page as $key => $value) {
            $propdata = $pagedata->addChild($key);
            $propdata->addCData($value);
          }
        }
        XMLsave($data, $cachefile);
      }
    } else {
      $sortedpages = array();
      $data = getXML($cachefile);
      foreach ($data->page as $pagedata) {
        $url = '' . $pagedata->url;
        self::$pages[$url] = array();
        foreach ($pagedata as $propdata) {
          self::$pages[$url][$propdata->getName()] = (string) $propdata;
        }
        $sortedpages[] = array('url' => $url, 'parent' => self::$pages[$url]['parent']);
      }
    }
    // fill children
    self::$pages[null] = array();
    foreach ($sortedpages as $sortedpage) {
      $parent = $sortedpage['parent'];
      if (isset(self::$pages[$parent])) {
        if (!isset(self::$pages[$parent]['children'])) self::$pages[$parent]['children'] = array();
        self::$pages[$parent]['children'][] = $sortedpage['url'];
      }
    }
    return self::$pages;
  }

  public static function getPageStructure($slug=null, $menuOnly=true, $slugToIgnore=null, $lang=null) {
    $slug = '' . $slug;
    $structure = array();
    self::getPageStructureImpl($structure, $slug, $menuOnly, $slugToIgnore, $lang);
    if ($lang) {
      for ($i=count($structure)-1; $i>=0; $i--) {
        if ($structure[$i]['title'] == '') {
          if ($i+1 >= count($structure) || $structure[$i+1]['level'] <= $structure[$i]['level']) {
            array_splice($structure, $i, 1);
          } else {
            $structure[$i]['title'] = $structure[$i]['menu'] = '-';
          }
        }
      }
    }
    return $structure;
  }
  
  private static function getPageStructureImpl(&$structure, $slug, $menuOnly=true, $slugToIgnore=null, $lang=null) {
    $pages = self::getPages();
    if (!isset($pages[$slug])) return;
    $level = (count($structure) > 0 ? $structure[count($structure)-1]['level'] + 1 : 0);
    if (isset($pages[$slug]['children'])) {
      foreach ($pages[$slug]['children'] as $childslug) {
        if ($childslug != $slugToIgnore && (!$menuOnly || $pages[$childslug]['menuStatus'] == 'Y')) {
          if (!$lang || isset($pages[$childslug]['title_'.$lang])) {
            $title = $lang ? $pages[$childslug]['title_'.$lang] : $pages[$childslug]['title'];
            $menu = $pages[$childslug]['menu'];
            if ($lang && isset($pages[$childslug]['menu_'.$lang])) $menu = $pages[$childslug]['menu_'.$lang];
            $structure[] = array(
              'level' => $level,
              'url' => $childslug,
              'title' => $title,
              'menuStatus' => $pages[$childslug]['menuStatus'],
              'menu' => $menu,
              'parent' => $pages[$childslug]['parent']
            );
          } else {
            $structure[] = array(
              'level' => $level,
              'url' => $childslug,
              'title' => '',
              'menuStatus' => $pages[$childslug]['menuStatus'],
              'menu' =>'',
              'parent' => $pages[$childslug]['parent']
            );
          }
          self::getPageStructureImpl($structure, $childslug, $menuOnly, $slugToIgnore, $lang);
        }
      }
    }
  }

  public static function getMenu($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL) {
    $slug = '' . $slug;
    $pages = self::getPages();
    $breadcrumbs = array();
    for ($url = $slug; $url && isset($pages[$url]); $url = $pages[$url]['parent']) {
      array_unshift($breadcrumbs, $url);
    }
    array_unshift($breadcrumbs, null);
    // find last page in breadcrumbs that is displayed in the menu
    for ($icu = 0; $icu+1 < count($breadcrumbs) && $pages[$breadcrumbs[$icu+1]]['menuStatus'] == 'Y'; $icu++);
    $currenturl = $breadcrumbs[$icu];
    // menus to display
    if ($minlevel < 0 || $maxlevel < $minlevel || $minlevel >= count($breadcrumbs)) {
      return array();
    } else {
      return self::getMenuImpl($breadcrumbs, $currenturl, $breadcrumbs[$minlevel], $maxlevel-$minlevel+1, $show);
    }
  }
  
  private static function getMenuImpl($breadcrumbs, $currenturl, $url, $levels, $show=I18N_SHOW_NORMAL) {
    global $language; // only set if I18N base plugin is available
    $pages = self::getPages();
    if (!@$pages[$url] || $levels <= 0) return null;
    $deflang = function_exists('return_i18n_default_language') ? return_i18n_default_language() : null;
    $menu = array();
    if (isset($pages[$url]['children'])) {
      foreach ($pages[$url]['children'] as $childurl) {
        $showIt = true;
        if (($show & I18N_FILTER_MENU) && $pages[$childurl]['menuStatus'] != 'Y') $showIt = false;
        if ($showIt && ($show & I18N_FILTER_LANGUAGE)) {
          $fulltitlekey = 'title' . (!@$language || $language == $deflang ? '' : '_' . $language);
          if (!isset($pages[$childurl][$fulltitlekey])) $showIt = false;
        }
        if ($showIt) {
          global $filters;
          $params = array($childurl, $pages[$childurl]['parent'], 
                          preg_split('/\s*,\s*/', html_entity_decode(stripslashes(trim(@$pages[$childurl]['tags'])), ENT_QUOTES, 'UTF-8')));
          foreach ($filters as $filter)  {
            if ($filter['filter'] == I18N_FILTER_VETO_NAV_ITEM) {
              if (call_user_func_array($filter['function'], $params)) {
                $showIt = false; 
                break;
              }
            }
          }
        }
        if ($showIt) {
          $showChildren = !($show & I18N_FILTER_CURRENT) || in_array($childurl,$breadcrumbs);
          $children = $showChildren ? self::getMenuImpl($breadcrumbs, $currenturl, $childurl, $levels-1, $show) : null;
          $menu[] = array(
            'url' => $childurl, 
            'parent' => $pages[$childurl]['parent'],
            'menu' => self::getProperty($childurl,'menu',$deflang), 
            'title' => self::getProperty($childurl,'title',$deflang),
            'link' => self::getProperty($childurl,'link',$deflang),
            'currentpath' => in_array($childurl, $breadcrumbs),
            'current' => ($childurl == $currenturl),
            'children' => $children,
            'haschildren' => $showChildren ? count($children) > 0 : self::hasChildren($childurl, $show)
          );
        }
      }
    }
    return count($menu) > 0 ? $menu : null;
  }
  
  private static function hasChildren($url, $show=I18N_SHOW_NORMAL) {
    global $language; // only set if I18N base plugin is available
    $pages = self::getPages();
    if (!@$pages[$url] || !isset($pages[$url]['children'])) return false;
    $deflang = function_exists('return_i18n_default_language') ? return_i18n_default_language() : null;
    foreach ($pages[$url]['children'] as $childurl) {
      if (($show & I18N_FILTER_LANGUAGE)) {
        $fulltitlekey = 'title' . (!@$language || $language == $deflang ? '' : '_' . $language);
        if (isset($pages[$childurl][$fulltitlekey])) return true;
      } else {
        return true;
      }
    }
    return false;
  }
  
  private static function getProperty($url, $key, $deflang) {
    $pages = self::getPages();
    if ($deflang !== null) {
      $languages = return_i18n_languages();
      foreach ($languages as $language) {
        $fullkey = $key . ($language == $deflang ? '' : '_' . $language);
        if (isset($pages[$url][$fullkey])) return $pages[$url][$fullkey];
      }
    } else {
      if (isset($pages[$url][$key])) return $pages[$url][$key];
    }
    return null;
  }
  
  public static function outputMenu($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL, $component=null) {
    $slug = '' . $slug;
    $menu = self::getMenu($slug, $minlevel, $maxlevel, $show);
    if (isset($menu) && count($menu) > 0) {
      $html = self::getMenuHTML($menu, ($show & I18N_OUTPUT_TITLE), $component);
      echo exec_filter('menuitems',$html);
    }
  }

  private static function getMenuHTML(&$menu, $showTitles=false, $componentname=null) {
    $component = null;
    if ($componentname && file_exists(GSDATAOTHERPATH.'components.xml')) {
      $data = getXML(GSDATAOTHERPATH.'components.xml');
      if (count($data->item) != 0) foreach ($data->item as $item) {
        if ($componentname == $item->slug) { 
          $component = stripslashes(htmlspecialchars_decode($item->value, ENT_QUOTES));
          break;
        }
      }
    }
    return self::getMenuHTMLImpl($menu, $showTitles, $component);
  }

  public static function getMenuHTMLImpl(&$menu, $showTitles=false, $component=null) {
    $html = '';
    foreach ($menu as &$item) {
      if (!isset($component)) {
        $href = @$item['link'] ? $item['link'] : (function_exists('find_i18n_url') ? find_i18n_url($item['url'],$item['parent']) : find_url($item['url'],$item['parent']));
      }
      $urlclass = (preg_match('/^[a-z]/i',$item['url']) ? '' : 'x') . $item['url'];
      $parentclass = !$item['parent'] ? '' : (preg_match('/^[a-z]/i',$item['parent']) ? ' ' : ' x') . $item['parent'];
      $classes = $urlclass . $parentclass . 
                  ($item['current'] ? ' current' : ($item['currentpath'] ? ' currentpath' : '')) . 
                  (isset($item['children']) && count($item['children']) > 0 ? ' open' : ($item['haschildren'] ? ' closed' : ''));
      $text = $item['menu'] ? $item['menu'] : $item['title'];
      $title = $item['title'] ? $item['title'] : $item['menu'];
      if (isset($component)) {
        $navitem = new I18nNavigationItem($item, $classes, $text, $title, $showTitles, $component);
        $html .= self::getMenuItem($component, $navitem);
      } else {
        if ($showTitles) {
          $html .= '<li class="' . $classes . '"><a href="' . $href . '" >' . $title . '</a>';
        } else {
          $html .= '<li class="' . $classes . '"><a href="' . $href . '" title="' . htmlspecialchars(html_entity_decode($title, ENT_QUOTES, 'UTF-8')) . '">' . $text . '</a>';
        }
        if (isset($item['children']) && count($item['children']) > 0) {
          $html .= '<ul>' . self::getMenuHTMLImpl($item['children'], $showTitles, $component) . '</ul>';
        }
        $html .= '</li>' . "\n";
      }
    }
    return $html;
  }
  
  private static function getMenuItem($component, $item) {
    ob_start();
    eval("?>" . $component . "<?php ");
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  public static function getBreadcrumbs($slug) {
    $slug = '' . $slug;
    $pages = self::getPages();
    $breadcrumbs = array();
    $deflang = function_exists('return_i18n_default_language') ? return_i18n_default_language() : null;
    for ($url = $slug; $url && isset($pages[$url]); $url = $pages[$url]['parent']) {
      array_unshift($breadcrumbs, array(
            'url' => $url, 
            'parent' => $pages[$url]['parent'],
            'menu' => self::getProperty($url,'menu',$deflang), 
            'title' => self::getProperty($url,'title',$deflang),
      ));
    }
    return $breadcrumbs;
  }
  
  public static function outputBreadcrumbs($slug) {
    $slug = '' . $slug;
    $breadcrumbs = self::getBreadcrumbs($slug);
    foreach ($breadcrumbs as &$item) {
      $text = $item['menu'] ? $item['menu'] : $item['title'];
      $title = $item['title'] ? $item['title'] : $item['menu'];
      $url = function_exists('find_i18n_url') ? find_i18n_url($item['url'],$item['parent']) : find_url($item['url'],$item['parent']);
      echo ' &raquo; <span class="breadcrumb"><a href="' . $url . '" title="' . 
                strip_quotes($title) . '">' . $text . '</a></span>';
    }
  }

}

class I18nNavigationItem {
  
  private $text;
  private $title;
  private $deflang = null;
  private $data = array();
  
  public function __construct(private $item, private $classes, $text, $title, private $showTitles, private $component) {
    $this->text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $this->title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
    $this->deflang = function_exists('return_i18n_default_language') ? return_i18n_default_language() : null;
  }
  
  public function __get($name) {
    switch($name) {
      case 'id':
      case 'url':
      case 'slug': return $this->item['url'];
      case 'parent': return $this->item['parent'];
      case 'classes': return $this->classes;
      case 'text': return $this->text;
      case 'title': return $this->title;
      case 'current':
      case 'iscurrent':
      case 'isCurrent': return $this->item['current'];
      case 'currentpath':
      case 'currentPath':
      case 'iscurrentpath':
      case 'isCurrentPath': return $this->item['currentpath'];
      case 'haschildren':
      case 'hasChildren': return $this->item['haschildren'];
      case 'open':
      case 'isOpen': return isset($this->item['children']) && count($this->item['children']) > 0;
      case 'closed':
      case 'isClosed': return $this->item['haschildren'] && (!isset($this->item['children']) || count($this->item['children']) <= 0);
      case 'titles':
      case 'showtitles':
      case 'showTitles': return $this->showTitles;
      case 'link': 
        if (@$this->item['link']) {
          return $this->item['link'];
        } else if (function_exists('find_i18n_url')) {
          return find_i18n_url($this->item['url'], $this->item['parent']);
        } else {
          return find_url($this->item['url'], $this->item['parent']);
        }
      case 'simplelink': 
      case 'simpleLink': 
        if (@$this->item['link']) {
          return $this->item['link'];
        } else {
          return find_url($this->item['url'], $this->item['parent']);
        }
      case 'content': 
        return html_entity_decode(stripslashes((string) $this->getProp('content')), ENT_QUOTES, 'UTF-8');
      case 'tags': 
        return preg_split('/\s*,\s*/', trim(html_entity_decode(stripslashes((string) $this->getProp('meta')), ENT_QUOTES, 'UTF-8')));
      default:
        return (string) $this->getProp($name);
    }
  }
  
  public function outputChildren() {
    if (isset($this->item['children']) && count($this->item['children']) > 0) {
      echo I18nNavigationFrontend::getMenuHTMLImpl($this->item['children'], $this->showTitles, $this->component);
    }
  }
  
  private function getProp($name) {
    $value = null;
    if ($this->deflang !== null) {
      $languages = return_i18n_languages();
      foreach ($languages as $language) {
        if (!isset($this->data[$language])) {
          $this->data[$language] = @getXML(GSDATAPAGESPATH . $this->url.($language == $this->deflang ? '' : '_' . $language).'.xml');
        }
        if (@$this->data[$language]->$name) return $this->data[$language]->$name;
      }
      return null;
    } else {
      if (!isset($this->data[''])) {
        $this->data[''] = @getXML(GSDATAPAGESPATH . $this->url.'.xml');
      }
      return $this->data['']->$name;
    }
  }
  
}


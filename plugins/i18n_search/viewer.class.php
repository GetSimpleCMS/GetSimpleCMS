<?php

class I18nSearchViewer {
  
  private static $rssHeaders = array();
  
  # ===== PUBLIC FUNCTIONS =====
  
  public static function processContent($content) {
    return preg_replace_callback("/(<p>\s*)?\(%\s*(searchform|searchresults|tags|searchrss)(\s+(?:%[^%\)]|[^%])+)?\s*%\)(\s*<\/p>)?/", 
                                 'I18nSearchViewer::replaceContentMatch',$content);
  }

  public static function processPreTemplateForRSS() {
    global $SITEURL, $url, $parent, $content;
    $c = stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
    if (preg_match_all("/\(%\s*searchrss(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", $c, $matches)) {
      foreach ($matches[1] as $match) {
        $params = array();
        $paramstr = isset($match) ? html_entity_decode(trim($match), ENT_QUOTES, 'UTF-8') : '';
        while (preg_match('/^([a-zA-Z][a-zA-Z_-]*)[:=]([^"\'\s]*|"[^"]*"|\'[^\']*\')(?:\s|$)/', $paramstr, $pmatch)) {
          $key = $pmatch[1];
          $value = trim($pmatch[2]);
          if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1,strlen($value)-2);
          $params[$key] = $value;
          $paramstr = substr($paramstr, strlen($pmatch[0]));
        }
        if (@$params['name'] && isset($_GET[$params['name']])) {
          $params = self::getSearchParams($params);
          include(GSPLUGINPATH.'i18n_search/rss.php');
          die;
        } else {
          $href = function_exists('find_i18n_url') ? find_i18n_url($url,$parent) : find_url($url,$parent);
          $href .= (strpos($url,'?') === false ? '?' : '&').$params['name'];
          self::$rssHeaders[] = '<link rel="alternate" type="application/rss+xml" title="'.htmlspecialchars(@$params['title']).'" href="'.htmlspecialchars($href).'" />';
        }
      }
    }
  }
  
  public static function processHeaderForRSS() {
    if (self::$rssHeaders) echo implode("\n", self::$rssHeaders)."\n";
  }
  
  public static function displayRSSLink($params) {
    global $url, $parent, $SITEURL;
    if (!@$params['name']) return;
    $href = function_exists('find_i18n_url') ? find_i18n_url($url,$parent) : find_url($url,$parent);
    $href .= (strpos($url,'?') === false ? '?' : '&').$params['name'];
    echo '<a href="'.htmlspecialchars($href).'"><img src="'.$SITEURL.'plugins/i18n_search/images/rss.gif" alt="rss" width="12" height="12"/> '.htmlspecialchars(@$params['title']).'</a>';
  }
  
  public static function displaySearchForm($params=null) {
    $params = self::getSearchParams(is_array($params) ? $params : array());
    include(GSPLUGINPATH.'i18n_search/searchform.php');
  }
  
  public static function displaySearchResults($params=null) {
    $params = self::getSearchParams(is_array($params) ? $params : array());
    include(GSPLUGINPATH.'i18n_search/searchresults.php');
  }
  
  public static function displayTags($params=null) {
    $params = self::getSearchParams(is_array($params) ? $params : array());
    $minPercent = array_key_exists('minTagSize',$params) ? (int) $params['minTagSize'] : I18N_MIN_TAG_SIZE;
    $maxPercent = array_key_exists('maxTagSize',$params) ? (int) $params['maxTagSize'] : I18N_MAX_TAG_SIZE;
    self::displayTagsImpl($minPercent, $maxPercent, $params);
  }
  
  
  # ===== FUNCTION only for searchresults.php =====
  
  public static function displayTagsImpl($minPercent=100, $maxPercent=250, $params=null) {
    include(GSPLUGINPATH.'i18n_search/tags.php');
  }
  
  
  # ===== PRIVATE HELPER FUNCTIONS =====
  
  private static function replaceContentMatch($match) {
    global $args;
    $function = $match[2];
    $params = array();
    $paramstr = isset($match[3]) ? html_entity_decode(trim($match[3]), ENT_QUOTES, 'UTF-8') : '';
    while (preg_match('/^([a-zA-Z][a-zA-Z0-9_-]*)[:=]([^"\'\s]*|"[^"]*"|\'[^\']*\')(?:\s|$)/', $paramstr, $pmatch)) {
      $key = $pmatch[1];
      $value = trim($pmatch[2]);
      if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1,strlen($value)-2);
      $params[$key] = $value;
      $paramstr = substr($paramstr, strlen($pmatch[0]));
    }
    $replacement = '';
    if (@$match[1] && (!@$match[4] || $function == 'searchrss')) $replacement .= $match[1];
    ob_start();
    if ($function == 'searchform') {
      self::displaySearchForm($params);
    } else if ($function == 'searchresults') {
      self::displaySearchResults($params);
    } else if ($function == 'tags') {
      self::displayTags($params);
    } else if ($function == 'searchrss') {
      self::displayRSSLink($params);
    }
    $replacement .= ob_get_contents();
    ob_end_clean();
    if (@$match[4] && (!@$match[1] || $function == 'searchrss')) $replacement .= $match[4];
    return $replacement;
  }
  
  private static function getSearchParams($params) {
    global $language, $LANG;
    if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
      $data = getXML(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
      if ($data) foreach ($data->children() as $child) {
        if (!array_key_exists($child->getName(), $params)) $params[$child->getName()] = (string) $child;
      }
    }
    if (isset($language)) self::mergeTexts($language, $params);
    if (isset($LANG)) self::mergeTexts(substr($LANG,0,2), $params);
    self::mergeTexts('en', $params);
    return $params;  
  }
  
  private static function mergeTexts($lang, &$params) { 
    $i18n = array();
    if (!file_exists(GSPLUGINPATH.'i18n_search/lang/'.$lang.'.php')) return false;
    @include(GSPLUGINPATH.'i18n_search/lang/'.$lang.'.php'); 
    if (count($i18n) > 0) foreach ($i18n as $code => $text) {
      if (!array_key_exists($code, $params)) $params[$code] = $text;
    }
    return true;
  }
  
}

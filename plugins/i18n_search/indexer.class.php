<?php
# a class representing a page during the indexing
class I18nSearchPageItem {
  
  static private $isi18n;
  static private $defaultLanguage;

  private $data;
  private $defdata = null; // data of default language
  private $id;
  private $language = '';
  private $tags = array();
  private $creDate;
  private $pubDate;
  private $title = '';
  private $content = '';
  private $fields = array();

  public function __construct($pagedata) {
    if (!isset(self::$isi18n)) {
      self::$isi18n = function_exists('i18n_init');
      self::$defaultLanguage = function_exists('return_i18n_default_language') ? return_i18n_default_language() : '';
    }
    $this->data = $pagedata;
    if (!self::$isi18n) {
      $this->id = (string) $pagedata->url;
    } else if (($pos = strpos((string) $pagedata->url,'_')) === false) {
      $this->id = (string) $pagedata->url;
      $this->language = self::$defaultLanguage;
    } else {
      $this->id = substr((string) $pagedata->url, 0, $pos);
      $this->language = substr((string) $pagedata->url, $pos+1);
    }
    $metak = stripslashes(html_entity_decode($pagedata->meta, ENT_QUOTES, 'UTF-8'));
    $this->tags = preg_split("/\s*,\s*/", trim($metak), -1, PREG_SPLIT_NO_EMPTY);
    $this->title = html_entity_decode(stripslashes(htmlspecialchars_decode($pagedata->title)), ENT_QUOTES, 'UTF-8');
    $this->content = html_entity_decode(strip_tags(stripslashes(htmlspecialchars_decode($pagedata->content))), ENT_QUOTES, 'UTF-8');
    $this->pubDate = strtotime((string) $pagedata->pubDate);
    $this->creDate = isset($pagedata->creDate) ? @strtotime((string) $pagedata->creDate) : $this->pubDate;
  }
  
  public function __get($name) {
    switch ($name) {
      case 'data': return $this->data;
      case 'id': return $this->id;
      case 'fullId': return (string) $this->data->url;
      case 'language': return $this->language; 
      case 'pubDate': return $this->pubDate;
      case 'creDate': return $this->creDate;
      case 'tags': return $this->tags;
      case 'title': return $this->title;
      case 'content': return $this->content;
      case 'menuOrder':
        if ($this->id != (string) $this->data->url) return $this->getDefaultDataProp($name);
        return (int) $this->data->$name;
      case 'parent':
      case 'menuStatus':
      case 'private':
        if ($this->id != (string) $this->data->url) return $this->getDefaultDataProp($name); 
      default: return (string) $this->data->$name;
    }
  }
  
  private function getDefaultDataProp($name) {
    if (!$this->defdata) {
      $this->defdata = getXML(GSDATAPAGESPATH . $this->id . '.xml');
      if (!$this->defdata) return null;
    }
    switch ($name) {
      case 'menuOrder': 
        return (int) $this->defdata->$name;
      default:
        return (string) $this->defdata->$name;
    }
  }
  
  public function addTags($fieldname, $tagarray) {
    if (!in_array($fieldname, $this->fields)) {
      $this->fields[] = $fieldname;
      foreach ($tagarray as $tag) if (!in_array($tag,$this->tags)) $this->tags[] = $tag;
    }
  }
  
  public function addTitle($fieldname, $text) {
    if (!in_array($fieldname, $this->fields)) {
      $this->fields[] = $fieldname;
      $this->title .= ' ' . $text;
    }
  }

  public function addContent($fieldname, $text) {
    if (!in_array($fieldname, $this->fields)) {
      $this->fields[] = $fieldname;
      $this->content .= ' ' . $text;
    }
  }
}

// the indexer
class I18nSearchIndexer {

  static private $instance = null;
  static private $ismb = false; 
  static private $isi18n = false;
  static private $defaultLanguage;
  
  private $tags = array();  // $tags[$tag]["$id_$language"] = 1
  private $words = array(); // $words[$word]["$id_$language"] = num
  private $dates = array(); // $dates["$id_$language"] = "$pubDate $creDate"
  private $itemTags = array(); // $itemTags["$id_$language"] = array("tag1", ...)

  private $tagWeight = I18N_TAG_WEIGHT;
  private $titleWeight = I18N_TITLE_WEIGHT;
  private $contentWeight = I18N_CONTENT_WEIGHT;
  private $tagMode = I18N_TAGS_LANG_OR_DEFLANG;
  
  public static function index() {
    if (!self::$instance) {
      self::$instance = new I18nSearchIndexer();
      self::$ismb = function_exists('mb_ereg_search');
      if (self::$ismb) mb_regex_encoding('UTF-8');
      self::$isi18n = function_exists('i18n_init');
      self::$defaultLanguage = function_exists('return_i18n_default_language') ? return_i18n_default_language() : '';
    }
    self::$instance->indexPages();
    exec_action(I18N_ACTION_INDEX);
    self::$instance->processTags();
    self::$instance->save();
  }
  
  public static function addToIndex($id, $language, $creDate, $pubDate, $tags, $title, $content) {
    if (self::$instance) self::$instance->addItem($id, $language, $creDate, $pubDate, $tags, $title, $content);
  }
    
  public static function deleteIndex() {
    if (file_exists(GSDATAOTHERPATH . I18N_WORD_INDEX)) unlink(GSDATAOTHERPATH . I18N_WORD_INDEX);
    if (file_exists(GSDATAOTHERPATH . I18N_TAG_INDEX)) unlink(GSDATAOTHERPATH . I18N_TAG_INDEX);
    if (file_exists(GSDATAOTHERPATH . I18N_DATE_INDEX)) unlink(GSDATAOTHERPATH . I18N_DATE_INDEX);
  }
  
  private function __construct() {
    if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
      $data = getXML(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
      if (isset($data->contentWeight) && is_numeric((string) $data->contentWeight)) $this->contentWeight = (int) $data->contentWeight;
      if (isset($data->titleWeight) && is_numeric((string) $data->titleWeight)) $this->titleWeight = (int) $data->titleWeight;
      if (isset($data->tagWeight) && is_numeric((string) $data->tagWeight)) $this->tagWeight = (int) $data->tagWeight;
      if (isset($data->tagMode) && is_numeric((string) $data->tagMode)) $this->tagMode = (int) $data->tagMode;
    }
  }
  
  private function addWords($fullid, $text, $weight) {
    if (!$text) return;
    if (self::$ismb) {
      mb_ereg_search_init($text, "\w+");
      if (mb_ereg_search()) {
        $match = mb_ereg_search_getregs();
        do {
          $word = mb_strtolower($match[0], 'UTF-8');
          if (!isset($this->words[$word])) {
            $this->words[$word] = array($fullid => $weight);
          } else if (!isset($this->words[$word][$fullid])) {
            $this->words[$word][$fullid] = $weight;
          } else {
            $this->words[$word][$fullid] += $weight;
          }
          $match = mb_ereg_search_regs();
        } while ($match);
      }
    } else {
      preg_match_all("/\w+/", $text, $matches);
      foreach ($matches[0] as $word) {
        $word = strtolower($word);
        if (!isset($this->words[$word])) {
          $this->words[$word] = array($fullid => $weight);
        } else if (!isset($this->words[$word][$fullid])) {
          $this->words[$word][$fullid] = $weight;
        } else {
          $this->words[$word][$fullid] += $weight;
        }
      }
    }
  }
  
  public function addItem($id, $language, $creDate, $pubDate, $tags, $title, $content) {
    if (!$language || $language == self::$defaultLanguage) {
      $language = '';
      $fullid = $id;
    } else {
      $fullid = $id.'_'.$language;
    }
    $this->itemTags[$fullid] = $tags;
    $this->dates[$fullid] = $pubDate.' '.$creDate;
    $this->addWords($fullid, $title, $this->titleWeight);
    $this->addWords($fullid, $content, $this->contentWeight);
  }
  
  private function processTags() {
    foreach ($this->itemTags as $fullid => $tags) {
      if (self::$isi18n) {
        $pos = strrpos($fullid,"_");
        if ($pos !== false) {
          $id = substr($fullid,0,$pos);
          if ($this->tagMode == I18N_TAGS_ALWAYS_DEFLANG) {
            $tags = $this->itemTags[$id];
          } else if ($this->tagMode == I18N_TAGS_LANG_OR_DEFLANG && (!$tags || count($tags) <= 0)) {
            $tags = $this->itemTags[$id];
          }
        }
      }
      if (count($tags) > 0) {
        foreach ($tags as $tag) {
          if (self::$ismb) {
            $tag = mb_ereg_replace("[^\w]", "_", mb_strtolower($tag, 'UTF-8'));
          } else {
            $tag = preg_replace("/[^\w]/", "_", strtolower($tag));
          }
          $this->tags[$tag][$fullid] = 1;
        }
        $this->addWords($fullid, @implode(' ',$tags), $this->tagWeight);
      }
    }
  }
    
  private function indexPages() {
    global $filters;
    $private_pages = array();
    $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
    while ($filename = readdir($dir_handle)) {
      if (strrpos($filename,'.xml') === strlen($filename)-4 && !is_dir(GSDATAPAGESPATH . $filename) ) {
        $pagedata = getXML(GSDATAPAGESPATH . $filename);
        $item = new I18nSearchPageItem($pagedata);
        if ($item->private == 'Y') continue;
        // execute filter, but ignore return value
        foreach ($filters as $filter)  {
          if ($filter['filter'] == I18N_FILTER_INDEX_PAGE) {
            call_user_func_array($filter['function'], array($item));
          }
        }
        $this->addItem($item->id,$item->language,$item->creDate,$item->pubDate,$item->tags,$item->title,$item->content);
      }
    }
  }
    
  private function save() {
    // date file
    ksort($this->dates);
    $f = fopen(GSDATAOTHERPATH . I18N_DATE_INDEX, "w");
    foreach ($this->dates as $fullid => $date) {
      fputs($f, "$fullid $date\n");
    }
    fclose($f);
    // tag index file
    ksort($this->tags);
    $f = fopen(GSDATAOTHERPATH . I18N_TAG_INDEX, "w");
    foreach ($this->tags as $tag => $item) {
      fputs($f, $tag);
      foreach ($item as $id => $score) fputs($f, ' '.$id);
      fputs($f, "\n");
    }
    fclose($f);
    // word index file
    ksort($this->words);
    $f = fopen(GSDATAOTHERPATH . I18N_WORD_INDEX, "w");
    foreach ($this->words as $word => $item) {
      fputs($f, $word);
      foreach ($item as $fullid => $score) if ($score > 0) fputs($f, ' '.$fullid.':'.$score);
      fputs($f, "\n");
    }
    fclose($f);
  }

}

function i18n_search_index_item($id, $language, $creDate, $pubDate, $tags, $title, $content) {
  I18nSearchIndexer::addToIndex('#'.$id, $language, $creDate, $pubDate, $tags, $title, $content);
}

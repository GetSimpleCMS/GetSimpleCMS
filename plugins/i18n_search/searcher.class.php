<?php

# class representing an excerpt of an HTML content
# using the object in a string context will return the excerpt (as will obj->text)
# obj->more will return true, if there is more content
class I18nSearchExcerpt {
  
  private $text = '';
  private $more = false;
  private $moreText = '';
  
  # the length can be a positive number for the number of words
  # or a positive number followed by 'p' or 'pm' for the number of paragraphs (pm: add a <p>...</p> if there is more)
  # or a negative number for the whole content
  public function __construct($content, $excerptlength) {
    if (preg_match('/^(-?\d+)\s*([a-zA-Z]*)$/', $excerptlength, $match)) {
      $length = (int) $match[1];
      $unit = strtolower($match[2]);
      // remove place holders
      $content = preg_replace('/\(%.*?%\)/', '', $content);
      $content = preg_replace('/\{%.*?%\}/', '', $content);
      $content = trim($content);
      if ($unit == 'p' || $unit == 'pm') {
        $content = preg_replace('/<p(\s[^>]*)?>\s*<\/p>/', '', $content);
        $pos = 0;
        while ($length > 0 && ($nextpos = strpos($content,'</p>',$pos)) !== false) {
          $pos = $nextpos+4;
          $length--;  
        }
        $this->more = (strlen($content) > $pos);
        $this->text = ($pos > 0 ? substr($content, 0, $pos) : '');
        $this->moreText = ($unit == 'pm' && $this->more ? '<p>...</p>' : '');
      } else if ($unit == 'c' || $unit == 'cm') {
        $text = html_entity_decode(str_replace('&nbsp;',' ',strip_tags($content)), ENT_QUOTES, 'UTF-8');
        $text = trim($text);
        if (false && function_exists('mb_strlen') && mb_strlen($text,'UTF-8') > $length) {
          $this->text = htmlspecialchars(mb_substr($text,0,$length,'UTF-8'));
          $this->more = true;
          $this->moreText = $unit == 'cm' ? '...' : '';
        } else if (strlen($text) > $length) {
          $this->text = htmlspecialchars(substr($text,0,$length));
          $this->more = true;
          $this->moreText = $unit == 'cm' ? '...' : '';
        } else {
          $this->text = htmlspecialchars($text);
        }
      } else if ($length > 0) {
        $text = html_entity_decode(str_replace('&nbsp;',' ',strip_tags($content)), ENT_QUOTES, 'UTF-8');
        $text = trim($text);
        $excerpt = preg_split("/\s+/", $text, $length+1);
        if (count($excerpt) > $length) { 
          array_pop($excerpt);
          $this->more = true;
          $this->text = htmlspecialchars(implode(' ', $excerpt), ENT_NOQUOTES);
          $this->moreText = '...';
        } else {
          $this->text = htmlspecialchars(implode(' ', $excerpt), ENT_NOQUOTES);
        }
      } else if ($length < 0) {
        $this->text = $content;
      }
    }
  }
  
  public function __get($name) {
    switch ($name) {
      case 'text': return $this->text;
      case 'more': return $this->more;
      case 'moreText':
      case 'moretext': return $this->moreText;
      default: return null;
    }
  }
  
  public function __toString() {
    return $this->text . $this->moreText;
  }
}

# the base class for all search result items
class I18nSearchResultItem {

  private static $defaultLanguage = null;

  private $id;
  private $language;
  private $creDate;
  private $pubDate;
  private $score;

  public function __construct($id,$language,$creDate,$pubDate,$score) {
    if (self::$defaultLanguage === null) {
      self::$defaultLanguage = function_exists('return_i18n_default_language') ? return_i18n_default_language() : '';
    }
    $this->id = $id;
    $this->language = $language;
    $this->creDate = $creDate;
    $this->pubDate = $pubDate;
    $this->score = $score;
  }  
  
  public function __get($name) {
    switch ($name) {
      case 'id': return $this->id;
      case 'fullId': return $this->id . ($this->language && $this->language != self::$defaultLanguage ? '_'.$this->language : '');
      case 'language': return $this->language;
      case 'creDate': return $this->creDate;
      case 'pubDate': return $this->pubDate;
      case 'score': return $this->score;
      default: return $this->get($name);
    }
  }
  
  public function __isset($name) {
    return __get($name) != null;
  }
  
  protected function get($name) {
    return null;
  }

  public function getExcerpt($content, $excerptlength) {
    return new I18nSearchExcerpt($content, $excerptlength);
  }

}

// lazy initializing search result page
class I18nSearchResultPage extends I18nSearchResultItem {
  
  protected $data = null;
  protected $defdata = null; // default language page
  protected $tags = null;
  protected $title = null;
  protected $content = null;
  
  protected function get($name) {
    if (!$this->data) {
      $this->data = getXML(GSDATAPAGESPATH . $this->fullId . '.xml');
      if (!$this->data) return null;
    }
    switch ($name) {
      case 'tags':
        if ($this->tags == null) {
          $metak = html_entity_decode(strip_tags(stripslashes(htmlspecialchars_decode($this->data->meta))), ENT_QUOTES, 'UTF-8');
          $this->tags = preg_split("/\s*,\s*/", trim($metak), -1, PREG_SPLIT_NO_EMPTY);
        }
        return $this->tags;
      case 'title':
        if ($this->title == null) {
          $this->title = stripslashes(html_entity_decode($this->data->title, ENT_QUOTES, 'UTF-8'));
        }
        return $this->title; 
      case 'content':    
        if ($this->content == null) {
          $this->content = stripslashes(htmlspecialchars_decode($this->data->content, ENT_QUOTES));
        }
        return $this->content;
      case 'contenttext':
        if ($this->content == null) {
          $this->content = stripslashes(htmlspecialchars_decode($this->data->content, ENT_QUOTES));
        }
        return trim(strip_tags($this->content));
      case 'url':
        return $this->id;
      case 'slug': 
        return $this->fullId;
      case 'parent':
        return (string) $this->data->parent;
      case 'link':
        if (function_exists('find_i18n_url')) {
          return find_i18n_url($this->id, $this->parent, $this->language);
        } else {
          return find_url($this->fullId, $this->parent);
        }    
      case 'simplelink':   
        return find_url($this->fullId, $this->parent);
      case 'menuOrder':
        if ($this->id != $this->fullId) return $this->getDefaultDataProp($name);
        return (int) $this->data->$name;
      case 'parent':
      case 'menuStatus':
      case 'private':
        if ($this->id != $this->fullId) return $this->getDefaultDataProp($name);
      default: 
        return (string) $this->data->$name;
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

}

# the search class
class I18nSearcher {
  
  private $tags = null;
  private $words = null;
  private $language = null;
  private $transliteration = null;
  
  # for sorting
  private $sort_field = null;
  private $sort_order = '+';
  
  public static function &search($tags=null, $words=null, $order=null, $lang=null) {
    $results = array();
    if (!$tags && isset($_REQUEST['tags'])) $tags = trim($_REQUEST['tags']);
    if (!$words && isset($_REQUEST['words'])) $words = trim($_REQUEST['words']);
    if (!$tags && !$words) return $results;
    if ($tags && !is_array($tags)) $tags = trim($tags) != '' ? preg_split("/\s+/", trim($tags)) : array();
    if ($words && !is_array($words)) $words = trim($words) != '' ? preg_split("/\s+/", trim($words)) : array();
    $searcher = new I18nSearcher($tags, $words, $order, $lang);
    $results = $searcher->execute();
    return $results;
  }

  public static function &tags() {
    if (!file_exists(GSDATAOTHERPATH . I18N_WORD_INDEX)) create_i18n_search_index();
    $tags = array();
    $f = fopen(GSDATAOTHERPATH . I18N_TAG_INDEX, "r");
    while (($line = fgets($f)) !== false) {
      $items = preg_split("/\s+/", trim($line));
      if (substr($items[0],0,1) != '_') {
        $tag = preg_replace("/_+/", " ", $items[0]);
        $urls = array_slice($items,1);
        $tags[$tag] = $urls;
      }
    }
    fclose($f);
    ksort($tags);
    return $tags;
  }

  private function __construct($tags=null, $words=null, $order=null, $language=null) {
    if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
      $data = getXML(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
      if (isset($data->transliteration) && (string) $data->transliteration) {
        $this->transliteration = array();
        $lines = preg_split('/\r?\n/', (string) $data->transliteration);
        foreach ($lines as $line) {
          if (($pos = strpos($line,'=')) !== false) {
            $this->transliteration[trim(substr($line,0,$pos))] = trim(substr($line,$pos+1));
          }
        } 
        $words = $this->translate($words);
        if (count($this->transliteration) <= 0) $this->transliteration = null;
      }
    }
    $this->tags = is_array($tags) ? $tags : (trim($tags) != '' ? preg_split("/\s+/", trim($tags)) : array());
    $this->words = is_array($words) ? $words : (trim($words) != '' ? preg_split("/\s+/", trim($words)) : array());
    $this->language = $language;
    $order = trim($order);
    if (substr($order,0,1) == '+' || substr($order,0,1) == '-') {
      $this->sort_order = substr($order,0,1);
      $this->sort_field = substr($order,1);
    } else switch ($order) {
        case 'url': $this->sort_field = 'url'; $this->sort_order = '+'; break;
        case 'reverseurl': $this->sort_field = 'url'; $this->sort_order = '-'; break;
        case 'date': $this->sort_field = 'date'; $this->sort_order = '-'; break;
        case 'created': $this->sort_field = 'created'; $this->sort_order = '-'; break;
        case 'score': $this->sort_field = 'score'; $this->sort_order = '-'; break;
        default: 
          if ($order) {
            $this->sort_field = $order; $this->sort_order = '+';
          } else {
            $this->sort_field = 'score'; $this->sort_order = '-';
          }
    }
  }
  
  private function translate($s) {
    $result = $s;
    foreach ($this->transliteration as $from => $to) $result = str_replace($from, $to, $result);
    return $result;
  }
    
  private function compare_score($a, $b) {
    if ($a->score == $b->score) {
      $r = $a->pubDate - $b->pubDate;
      return $r != 0 ? $r : strcmp($a->fullId, $b->fullId);
    } else {
      return $a->score - $b->score;
    }
  }
  
  private function compare_url($a, $b) {
    return strcmp(strtolower($a->fullId), strtolower($b->fullId));
  }
  
  private function compare_date($a, $b) {
    $r = $a->pubDate - $b->pubDate;
    return $r != 0 ? $r : strcmp(strtolower($a->fullId), strtolower($b->fullId));
  }
  
  private function compare_created($a, $b) {
    $r = $a->creDate - $b->creDate;
    return $r != 0 ? $r : strcmp(strtolower($a->fullId), strtolower($b->fullId));
  }
  
  private function compare_field($a, $b) {
    $field = $this->sort_field;
    $fa = (string) $a->$field;
    $fb = (string) $b->$field;
    if (is_numeric($fa)) {
      $vala = floatval($fa);
    } else {
      $vala = strtotime($fa);
      if ($vala === false) $vala = $fa;
    }
    if (is_numeric($fb)) {
      $valb = floatval($fb);
    } else {
      $valb = strtotime($b->$field);
      if ($valb === false) $valb = $b->$field;
    }
    if (is_string($vala) && is_string($valb)) {
      if (function_exists('mb_strtolower')) {
        $r = strcmp(mb_strtolower($vala), mb_strtolower($valb)); // probably incorrect - should use Collator?
      } else {
        $r = strcmp(strtolower($vala), strtolower($valb));
      }
    } else if (is_string($vala)) {
      $r = 1;  # numbers before strings
    } else if (is_string($valb)) {
      $r = -1; # numbers before strings
    } else {
      $r = $vala - $valb;
    }
    return $r != 0 ? $r : strcmp($a->fullId, $b->fullId);
  }
    
  private function is_word($line, $word, $ismb) {
    $comp = $line;
    if ($this->transliteration) $comp = $this->translate(substr($line,0,strpos($line,' ')+1));
    if (($ismb && mb_strlen($word, 'UTF-8') < 3) || (!$ismb && strlen($word) < 3)) {
      return strncmp($comp, $word.' ', strlen($word.' ')) == 0;
    } else {
      return strncmp($comp, $word, strlen($word)) == 0;
    }
  }

  private function &execute() {
    $results = array();
    if (count($this->tags) <= 0 && count($this->words) <= 0) return $results;
    if (!file_exists(GSDATAOTHERPATH . I18N_WORD_INDEX)) create_i18n_search_index();
    $isi18n = function_exists('i18n_init');
    // use multibyte string functions?
    $ismb = function_exists('mb_ereg_replace');
    if ($ismb) mb_regex_encoding('UTF-8');
    // language?
    $defaultLanguage = function_exists('return_i18n_default_language') ? return_i18n_default_language() : '';
    $languages = function_exists('return_i18n_languages') ? ($this->language ? array($this->language) : return_i18n_languages()) : array($defaultLanguage);
    // scores per id
    $idScores = null;
    $tagIds = array();
    if ($this->tags && count($this->tags) > 0) {
      for ($i=0; $i<count($this->tags); $i++) {
        if ($ismb) {
          $this->tags[$i] = mb_ereg_replace("[^\w]", "_", mb_strtolower(trim($this->tags[$i]), 'UTF-8'));
        } else { 
          $this->tags[$i] = preg_replace("/[^\w]/", "_", strtolower(trim($this->tags[$i])));
        }
        $tagIds[$this->tags[$i]] = array();
      }
      $f = fopen(GSDATAOTHERPATH . I18N_TAG_INDEX, "r");
      while (($line = fgets($f)) !== false) {
        foreach ($this->tags as $tag) {
          if (strncmp($line, $tag.' ', strlen($tag)+1) == 0) {
            $fullids = preg_split("/\s+/", trim($line));
            unset($fullids[0]);
            foreach ($fullids as $fullid) {
              $tagIds[$tag][$fullid] = 1;
            }
          }  
        }
      }
      fclose($f);
      foreach ($this->tags as $tag) {
        if ($idScores === null) {
          $idScores = $tagIds[$tag]; 
        } else { 
          $idScores = array_intersect_key($idScores, $tagIds[$tag]);
        }
      }
    }
    if ($this->words && count($this->words) > 0) {
      $wordIds = array();
      for ($i=0; $i<count($this->words); $i++) {
        if ($ismb) {
          $this->words[$i] = mb_strtolower(trim($this->words[$i]), 'UTF-8');
        } else {
          $this->words[$i] = strtolower(trim($this->words[$i]));
        }
        $wordIds[$this->words[$i]] = array();
      }
      $f = fopen(GSDATAOTHERPATH . I18N_WORD_INDEX, "r");
      while (($line = fgets($f)) !== false) {
        foreach ($this->words as $word) {
          if ($this->is_word($line, $word, $ismb)) {
            $fullidAndScores = preg_split("/\s+/", trim($line));
            unset($fullidAndScores[0]);
            foreach ($fullidAndScores as $fullidAndScore) {
              $pos = strrpos($fullidAndScore,":");
              $score = (int) substr($fullidAndScore, $pos+1);
              $fullid = substr($fullidAndScore, 0, $pos);
              if (isset($wordIds[$word][$fullid])) {
                $wordIds[$word][$fullid] += $score;
              } else if ($idScores == null || isset($idScores[$fullid])) {
                $wordIds[$word][$fullid] = $score;
              }
            }
          }  
        }
      }
      fclose($f);
      foreach ($this->words as $word) {
        if ($idScores === null) {
          $idScores = $wordIds[$word];
        } else {
          $idScores = array_intersect_key($idScores, $wordIds[$word]);
          foreach ($idScores as $fullid => $score) {
            $idScores[$fullid] = $score * $wordIds[$word][$fullid];
          }
        }
      }
    }
    $filteredresults = array();
    if ($idScores && count($idScores) > 0) {
      $idPubDates = array();
      $idCreDates = array();
      $f = fopen(GSDATAOTHERPATH . I18N_DATE_INDEX, "r");
      while (($line = fgets($f)) !== false) {
        $items = preg_split("/\s+/", trim($line));
        $fullid = $items[0];
        if (count($items) >= 2 && isset($idScores[$fullid])) {
          $idPubDates[$fullid] = (int) $items[1];
          $idCreDates[$fullid] = count($items) >= 3 ? (int) $items[2] : (int) $items[1];
        }
      }
      fclose($f);
      foreach ($idScores as $fullid => $score) {
        if ($isi18n) {
          $pos = strrpos($fullid,"_");
          $language = $pos !== false ? substr($fullid, $pos+1) : $defaultLanguage;
          $id = $pos !== false ? substr($fullid, 0, $pos) : $fullid;
        } else {
          $language = '';
          $id = $fullid;
        }
        if (in_array($language, $languages)) { // ignore language, if not default and not requested by user
          if (substr($id,0,1) == '#') {
            $ispage = false;
            $id = substr($id,1);
          } else {
            $ispage = true;
          }
          if ($ispage) {
            $results[] = new I18nSearchResultPage($id,$language,$idCreDates[$fullid],$idPubDates[$fullid],$idScores[$fullid]);
          } else {
            global $filters;
            $item = null;
            foreach ($filters as $filter)  {
              if ($filter['filter'] == I18N_FILTER_SEARCH_ITEM) {
                $item = call_user_func_array($filter['function'], array($id,$language,$idCreDates[$fullid],$idPubDates[$fullid],$idScores[$fullid]));
                if ($item) break;
              }
            }
            $results[] = $item ? $item : new I18nSearchResultItem($id,$language,$idCreDates[$fullid],$idPubDates[$fullid],$idScores[$fullid]);
          }
        }
      }
      foreach ($results as $item) {
        global $filters;
        $vetoed = false;
        foreach ($filters as $filter)  {
          if ($filter['filter'] == I18N_FILTER_VETO_SEARCH_ITEM) {
            if (call_user_func_array($filter['function'], array($item))) {
              $vetoed = true; 
              break;
            }
          }
        }
        if (!$vetoed) $filteredresults[] = $item;
      }
      switch ($this->sort_field) {
        case 'url': usort($filteredresults, array($this,'compare_url')); break;
        case 'date': usort($filteredresults, array($this,'compare_date')); break;
        case 'created': usort($filteredresults, array($this,'compare_created')); break;
        case 'score': usort($filteredresults, array($this,'compare_score')); break;
        default: usort($filteredresults, array($this,'compare_field'));
      }
      if ($this->sort_order == '-') {
        $filteredresults = array_reverse($filteredresults);
      }
    }
    return $filteredresults;
  }

}

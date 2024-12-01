<?php

class I18nSearchMarker {
  
  private static $words = null;
  
  public static function getWords() {
    if (self::$words !== null) return self::$words;
    self::$words = array();
    if (@$_GET['mark']) self::$words = array_merge(self::$words, preg_split('/\s+/', trim($_GET['mark'])));
    $referer = @$_SERVER['HTTP_REFERER'];
    if ($referer) {
      if (preg_match('/(?:\?|&)words=([^&]+)(?:&|$)/', $referer, $match)) {
        self::$words = array_merge(self::$words, preg_split('/\s+/', trim(urldecode($match[1]))));
      }
      if (preg_match('/(?:\?|&)tags=([^&]+)(?:&|$)/', $referer, $match)) {
        self::$words = array_merge(self::$words, preg_split('/\s+/', trim(urldecode($match[1]))));
      }
      if (preg_match('/http:\/\/www\.google\.\w+\/.*(?:\?|&)q=([^&]+)(?:&|$)/', $referer, $match)) {
        self::$words = array_merge(self::$words, preg_split('/\s+/', trim(urldecode($match[1]))));
      }
      if (preg_match('/http:\/\/www\.bing\.\w+\/.*(?:\?|&)q=([^&]+)(?:&|$)/', $referer, $match)) {
        self::$words = array_merge(self::$words, preg_split('/\s+/', trim(urldecode($match[1]))));
      }
    }
    return self::$words;
  }
  
  public static function mark($html, $words=array()) {
    if (!$words || count($words) <= 0) return $html;
    $ismb = function_exists('mb_ereg_search');
    $inlineTags = 'b|i|em|strong|tt|big|small|strike|u|span|a';
    $ignoreTags = 'script|style|textarea';
    $pattern = '';
    foreach ($words as $word) {
      if ($pattern != '') $pattern .= '|';
      $pattern .= ($ismb ? mb_strtolower($word, 'UTF-8') : strtolower($word));
      if (($ismb && mb_strlen($word, 'UTF-8') >= 3) || (!$ismb && strlen($word) >= 3)) $pattern .= '\w*'; 
    }
    $newhtml = '';
    $currPart = '';
    $currWord = '';
    $ignore = false;
    if ($ismb) {
      mb_regex_encoding('UTF-8');
      mb_ereg_search_init($html, '(\w+)|([^<\w]+)|<(\/?[a-zA-Z-])[^>]*>|(<!--.*?-->|\(%.*?%\)|\{%.*?%\})');
      if (mb_ereg_search()) {
        $match = mb_ereg_search_getregs();
        do {
          if ($ignore || $match[2] || $match[4] ||
              ($match[3] && !mb_ereg_match("\/?($inlineTags)", mb_strtolower($match[3], 'UTF-8')))) {
            if ($currWord && mb_ereg_match($pattern, $currWord)) {
              $newhtml .= self::markIt($currPart); 
            } else {
              $newhtml .= $currPart;
            }
            $currPart = $currWord = '';
            if ($ignore) {
              if ($ignore && $match[3] && $match[3] == $ignore) $ignore = false;
            } else if ($match[3] && mb_ereg_match("$ignoreTags", mb_strtolower($match[3], 'UTF-8')) && substr($match[0], -2) != '/>') {
              $ignore = '/'.$match[3];
            }
            $newhtml .= $match[0];
          } else {
            if ($match[1]) $currWord .= mb_strtolower($match[1], 'UTF-8');
            $currPart .= $match[0];
          }
          $match = mb_ereg_search_regs();
        } while ($match);
        if ($currWord && mb_ereg_match($pattern, $currWord)) {
          $newhtml .= self::markIt($currPart);
        } else {
          $newhtml .= $currPart;
        }
      }
    } else {
      if (preg_match_all('/(\w+)|([^<\w]+)|<(\/?[a-zA-Z-])[^>]*>|(<!--.*?-->|\(%.*?%\)|\{%.*?%\})/i', 
                         $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
          if ($ignore || $match[2] || $match[4] ||
              ($match[3] && !preg_match("/^\/?($inlineTags)$/i", $match[3]))) {
            if ($currWord && preg_match("/^$pattern$/", $currWord)) {
              $newhtml .= self::markIt($currPart); 
            } else {
              $newhtml .= $currPart;
            }
            $currPart = $currWord = '';
            if ($ignore) {
              if ($ignore && $match[3] && $match[3] == $ignore) $ignore = false;
            } else if ($match[3] && preg_match("/^$ignoreTags$/i", $match[3]) && substr($match[0], -2) != '/>') {
              $ignore = '/'.$match[3];
            }
            $newhtml .= $match[0];
          } else {
            if ($match[1]) $currWord .= strtolower($match[1]);
            $currPart .= $match[0];
          }
        }
        if ($currWord && preg_match("/^$pattern$/", $currWord)) {
          $newhtml .= self::markIt($currPart);
        } else {
          $newhtml .= $currPart;
        }
      }
    }
    return $newhtml;
  }
  
  private static function markIt($currPart) {
    $result = '<span class="mark">';
    $result .= preg_replace('/<[^>]*>/', '</span>$0<span class="mark">', $currPart);
    $result .= '</span>';
    $result = preg_replace('/<span class="mark"><\/span>/', '', $result);
    return $result;
  }
  
}
?>
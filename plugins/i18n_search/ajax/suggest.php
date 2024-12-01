<?php

function translate($s) {
  global $transliteration;
  $result = $s;
  foreach ($transliteration as $from => $to) $result = str_replace($from, $to, $result);
  return $result;
}

function filter($items, $langs) {
  $filtered = array();
  foreach ($items as $item) {
    $pos = strrpos($item,'_');
    $lang = $pos !== false ? substr($item,$pos+1,strrpos($item,':')-$pos-1) : '';
    if (in_array($lang, $langs)) $filtered[] = $item;
  }
  return $filtered;
}


$q = trim(strtolower($_GET["q"]));
if (!$q) die;
header('Content-Type: text/plain');
// if set, use only words on pages in these languages (empty string = default language)
$langs = isset($_GET['langs']) ? preg_split('/,/',$_GET['langs']) : null;

global $transliteration;
$transliteration = null;
$datadir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR.'plugins')) . '/data/';
if (file_exists($datadir . 'other/i18n_search_settings.xml')) {
  $data = simplexml_load_file($datadir . 'other/i18n_search_settings.xml');
  if (isset($data->transliteration) && (string) $data->transliteration) {
    $transliteration = array();
    $lines = preg_split('/\r?\n/', (string) $data->transliteration);
    foreach ($lines as $line) {
      if (($pos = strpos($line,'=')) !== false) {
        $transliteration[trim(substr($line,0,$pos))] = trim(substr($line,$pos+1));
      }
    }
    $q = translate($q);
    if (count($transliteration) <= 0) $transliteration = null;
  }
}

$l = strlen($q);
$wordfile = $datadir . 'other/i18n_word_index.txt';
if (file_exists($wordfile)) {
  $f = fopen($wordfile, "r");
  while (($line = fgets($f)) !== false) {
    $word = substr($line, 0, strpos($line,' '));
    $comp = $transliteration ? translate($word) : $word;
    if (substr($comp,0,$l) == $q) {
      if ($langs !== null) {
        // check if there are pages in this language
        $items = preg_split('/\s+/',trim($line));
        array_shift($items);     
        $items = filter($items, $langs);
        if (count($items) > 0) echo "$word|$word\n";
      } else {
        echo "$word|$word\n";
      }
    }
  }
  fclose($f);
}

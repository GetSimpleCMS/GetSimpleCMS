<?php

function filter($items, $langs) {
  $filtered = array();
  foreach ($items as $item) {
    $pos = strrpos($item,'_');
    $lang = $pos !== false ? substr($item,$pos+1) : '';
    if (in_array($lang, $langs)) $filtered[] = $item;
  }
  return $filtered;
}

$tags = preg_split('/\s+/',trim(strtolower($_GET["tags"])));
// if set, use only tags on pages in these languages (empty string = default language)
$langs = isset($_GET['langs']) ? preg_split('/,/',$_GET['langs']) : null;
if (!$tags) die;
header('Content-Type: application/json');
$datadir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR.'plugins')) . '/data/';
$tagfile = $datadir . 'other/i18n_tag_index.txt';
$slugs = null;
$remainingtags = array();
if (file_exists($tagfile)) {
  $f = fopen($tagfile, "r");
  while (($line = fgets($f)) !== false) {
    $items = preg_split('/\s+/',trim($line));
    $tag = array_shift($items);
    if (in_array($tag,$tags)) {
      if ($langs !== null) $items = filter($items, $langs);
      if ($slugs == null) {
        $slugs = $items;
      } else {
        $slugs = array_values(array_intersect($slugs, $items));
      }
    }
  }
  if ($slugs != null && count($slugs) > 0) {
    rewind($f);
    while (($line = fgets($f)) !== false) {
      $items = preg_split('/\s+/',trim($line));
      $tag = array_shift($items);
      if ($langs !== null) $items = filter($items, $langs);
      $count = count(array_intersect($slugs, $items));
      if ($count > 0) {
        $remainingtags[$tag] = $count;
      }
    }
  }
  fclose($f);
}
echo json_encode($remainingtags);

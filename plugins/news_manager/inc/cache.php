<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager cache functions.
 */


/*******************************************************
 * @function nm_update_cache
 * @action store frequently accessed post data in cache files
 */
function nm_update_cache() {
  $posts = nm_get_cache_data();
  return nm_cache_to_xml($posts);
}


/*******************************************************
 * @function nm_get_cache_data
 * @return arrays with relevant post data
 */
function nm_get_cache_data() {
  $posts = array();
  if (file_exists(NMPOSTPATH)) {
    $files = getFiles(NMPOSTPATH);
    # collect all post data
    foreach ($files as $file) {
      if (isFile($file, NMPOSTPATH, 'xml')) {
        $data = getXML(NMPOSTPATH . $file);
        $time = strtotime($data->date);
        while (array_key_exists($time, $posts)) $time++;
        $posts[$time]['slug'] = basename($file, '.xml');
        $posts[$time]['title'] = strval($data->title);
        $posts[$time]['date'] = strval($data->date);
        $posts[$time]['tags'] = strval($data->tags);
        $posts[$time]['private'] = strval($data->private);
        $posts[$time]['image'] = strval($data->image);
        $posts[$time]['author'] = strval($data->author);
      }
    }
    krsort($posts);
  }
  return $posts;
}


/*******************************************************
 * @function nm_cache_to_xml
 * @action write post data to xml file
 */
function nm_cache_to_xml($posts) {
  $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
  foreach ($posts as $post) {
    $item = $xml->addChild('item');
    $elem = $item->addChild('slug');
    $elem->addCData($post['slug']);
    $elem = $item->addChild('title');
    $elem->addCData($post['title']);
    $elem = $item->addChild('date');
    $elem->addCData($post['date']);
    $elem = $item->addChild('tags');
    $elem->addCData($post['tags']);
    $elem = $item->addChild('private');
    $elem->addCData($post['private']);
    $elem = $item->addChild('image');
    $elem->addCData($post['image']);
    if (!empty($post['author'])) {
      $elem = $item->addChild('author');
      $elem->addCData($post['author']);
    }
  }
  return @XMLsave($xml, NMPOSTCACHE);
}

<?php

class I18nGalleryFrontend {
  
  public static function outputLink($gallery) {
    include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
    $url = @$gallery['url'] ? $gallery['url'] : 'index';
    $parent = @$gallery['parent'] ? $gallery['parent'] : null;
    $tags = @$gallery['tags'] ? $gallery['tags'] : null;
    $thumb = i18n_gallery_thumb($gallery);
    $title = $gallery['title'];
    if (function_exists('return_i18n_languages')) {
      $languages = return_i18n_languages();
      $deflang = return_i18n_default_language();
      foreach ($languages as $language) {
        $fullkey = 'title' . ($language == $deflang ? '' : '_' . $language);
        if (isset($gallery[$fullkey])) { $title = $gallery[$fullkey]; break; }
      }
    }
    $link = function_exists('find_i18n_url') ? find_i18n_url($url,$parent) : find_url($url,$parent);
    if ($tags) $link .= (strpos($link,'?') !== false ? '&' : '?').'imagetags='.urlencode($tags);
    if (isset($thumb)) {
      $item = @$gallery['items'][$thumb];
      if (!$item) $item = $gallery['items'][0];
      echo '<a href="'.htmlspecialchars($link).'" class="gallery-thumb-link">';
      echo '<img src="';
      i18n_gallery_thumb_link($gallery,$item);
      echo '" alt="'.htmlspecialchars($title).'" title="'.htmlspecialchars($title).'"/>';
      echo '</a>';  
    } else {
      echo '<a href="'.htmlspecialchars($link).'" class="gallery-title-link">';
      echo htmlspecialchars($title);
      echo '</a>';
    }
  }

  public static function outputGallery($gallery, $ignoreQuery=false) {
    global $LANG, $i18n_gallery_pic_used;
    include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
    if (function_exists('i18n_load_texts')) {
      i18n_load_texts('i18n_gallery');
    } else {  
      i18n_merge('i18n_gallery', substr($LANG,0,2)) || i18n_merge('i18n_gallery', 'en');
    }  
    $pic = @$gallery['pic'];
    if (!$ignoreQuery && isset($_GET['pic']) && !$i18n_gallery_pic_used) {
      if (strpos($_GET['pic'],':') === false) {
        $pic = intval($_GET['pic']);
        $i18n_gallery_pic_used = true;
      } else if (substr($_GET['pic'],0,strrpos($_GET['pic'],':')) == $gallery['name']) {
        $pic = intval(substr($_GET['pic'],strrpos($_GET['pic'],':')+1));
        $i18n_gallery_pic_used = true;
      }
    }
    $plugins = i18n_gallery_plugins();
    $plugin = @$plugins[$gallery['type']];
    if ($plugin) call_user_func_array($plugin['content'], array($gallery, $pic));
  }

}
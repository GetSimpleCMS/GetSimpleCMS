<?php
  $slug = @$params['slug'];
  $is_ajax = !isset($params['ajax']) || $params['ajax'];
  $addtags = isset($params['addTags']) ? preg_split('/\s+/', trim($params['addTags'])) : null;
  $reqtags = trim(@$_REQUEST['tags']) ? preg_split('/\s+/',trim($_REQUEST['tags'])) : null;
  $language = isset($params['lang']) ? $params['lang'] : null;

  // languages
  $isi18n = function_exists('return_i18n_languages');
  $defaultLanguage = $isi18n ? return_i18n_default_language() : '';
  $languages = $isi18n ? ($language ? array($language) : return_i18n_languages()) : null;

  // read tag file
  if (!file_exists(GSDATAOTHERPATH . I18N_WORD_INDEX)) create_i18n_search_index();
  $alltags = array();
  $f = fopen(GSDATAOTHERPATH . I18N_TAG_INDEX, "r");
  while (($line = fgets($f)) !== false) {
    $items = preg_split("/\s+/", trim($line));
    $tag = array_shift($items);
    if ($languages) {
      // filter items
      $filteredItems = array();
      foreach ($items as $item) {
        $pos = strrpos($item,'_');
        $lang = $pos !== false ? substr($item,$pos+1) : $defaultLanguage;
        if (in_array($lang, $languages)) $filteredItems[] = $item;
      }
      if (count($filteredItems) > 0) $alltags[$tag] = $filteredItems;
    } else {
      $alltags[$tag] = $items; // slugs or plugin specific ids
    }
  }
  fclose($f);
  ksort($alltags);

  // get all URLs or all URLs that have one of the "addTags"
  $allurls = null;
  if ($addtags) {
    foreach ($addtags as $tag) {
      if (isset($alltags[$tag])) {
        $allurls = $allurls ? array_values(array_intersect($allurls, $alltags[$tag])) : $alltags[$tag];
      } else {
        $allurls = array();
        break;
      }
    }
  } else {
    $allurls = array();
    foreach ($alltags as $tag => &$urls) {
      foreach ($urls as $url) {
        if (!in_array($url, $allurls)) $allurls[] = $url;
      }
    }
  }
  
  // get URLs that match the already selected tags
  $filteredurls = null;
  if ($reqtags) {
    foreach ($reqtags as $tag) {
      if (isset($alltags[$tag])) {
        $filteredurls = $filteredurls ? array_values(array_intersect($filteredurls, $alltags[$tag])) : $alltags[$tag];
      } else {
        $filteredurls = array();
      }
    }
  }

  $numPages = count($allurls);
  $diffPercent = $maxPercent - $minPercent;
  if (@$slug) {
    $pagedata = getXML(GSDATAPAGESPATH . $slug . '.xml');
    $link = function_exists('find_i18n_url') ? find_i18n_url($slug, (string) $pagedata->parent) : find_url($slug, (string) $pagedata->parent);
    $link .= (strpos($link,'?') !== false ? '&' : '?') . 'tags=';
  }
  foreach ($alltags as $tag => &$urls) {
    if (substr($tag,0,1) == '_') continue;
    $tagurls = array_intersect($urls, $allurls);
    if ($addtags && count($tagurls) <= 0) continue;
    $unavailable = $filteredurls && count(array_intersect($tagurls, $filteredurls)) <= 0;
    $classes = @$link || !$unavailable ? 'tag' : 'tag unavailable';
    echo (@$link ? '<a href="'.htmlspecialchars($link).urlencode($tag).'" ' : '<span ') .
         'style="font-size:'.(int)($minPercent + $diffPercent*count($tagurls)/$numPages).'%" class="'.$classes.'">' .
         htmlspecialchars(trim(preg_replace('/_+/',' ',$tag))) . 
         (@$link ? '</a> ' : '</span> ');
  }


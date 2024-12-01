<?php
if (!function_exists('i18n_search_display_with_component')) {
  function i18n_search_display_with_component($component, $item, $showLanguage, $showDate, $dateFormat, $numWords) {
    eval("?>" . $component . "<?php ");
  }
}
if (!function_exists('i18n_search_order_by_tags')) {
  function i18n_search_order_by_tags($items, $tags) {
    if (!is_array($tags)) $tags = preg_split('/\s*,\s*/', $tags);
    foreach ($tags as $i => $tag) $groups[$i] = array();
    foreach ($items as $item) {
      foreach ($tags as $i => $tag) {
        if ($tag == '*' || @in_array($tag, $item->tags)) $groups[$i][] = $item;
      }
    }
    $newitems = array();
    foreach ($groups as $group) $newitems = array_merge($newitems, $group);
    return $newitems;
  }
}
if (!function_exists('i18n_search_group_by_tags')) {
  function i18n_search_group_by_tags($items, $tags) {
    
  }
}
if (!function_exists('i18n_search_archive')) {
  function i18n_search_archive($items, $until) {
    
  }
}

  require_once(GSPLUGINPATH.'i18n_search/searcher.class.php');

  $i18n = &$params; // alias for i18n parsing
  $is_i18n = function_exists('return_i18n_default_language');
  if ($is_i18n && array_key_exists('i18n',$params) && !$params['i18n']) $is_i18n = false;
  $tags = array_key_exists('tags',$params) ? $params['tags'] : (isset($_REQUEST['tags']) ? trim($_REQUEST['tags']) : null);
  $words = array_key_exists('words',$params) ? $params['words'] : (isset($_REQUEST['words']) ? trim($_REQUEST['words']) : null);
  $alltags = array_key_exists('addTags',$params) ? $tags.' '.$params['addTags'] : $tags;
  $allwords = array_key_exists('addWords',$params) ? $words.' '.$params['addWords'] : $words;
  $order = array_key_exists('order',$params) ? $params['order'] : null;
  $lang = array_key_exists('lang',$params) ? $params['lang'] : null;
  $live = array_key_exists('live',$params) ? $params['live'] : null;
  $preview = array_key_exists('preview',$params) && $params['preview'];
  if (!$tags && !$words && !isset($_REQUEST['tags']) && !isset($_REQUEST['words']) && !$live) return;
  $headerText = @$i18n['HEADER'];
  $notFoundText = @$i18n['NOT_FOUND'];
  $showLanguage = $is_i18n && array_key_exists('showLanguage',$params) ? $params['showLanguage'] : $is_i18n;
  $showDate = array_key_exists('showDate',$params) ? $params['showDate'] : true;
  $dateLocale = @$i18n['DATE_LOCALE'];
  $dateFormat = @$i18n['DATE_FORMAT'];
  $numWords = array_key_exists('numWords',$params) ? $params['numWords'] : I18N_NUM_WORDS;
  $max = array_key_exists('max',$params) ? (int) $params['max'] : I18N_MAX_RESULTS; 
  $showPaging = array_key_exists('showPaging',$params) ? $params['showPaging'] : true;
  $componentName = array_key_exists('component',$params) ? $params['component'] : null;
  $idPrefix = array_key_exists('idPrefix', $params) ? $params['idPrefix'] : null;
  $tagClassPrefix = array_key_exists('tagClassPrefix', $params) ? $params['tagClassPrefix'] : null;
  
  $pageNum = $showPaging && isset($_REQUEST['page']) ? @((int) $_REQUEST['page']) - 1 : 0;
  $first = $pageNum * $max;

  $processFunction = array_key_exists('process', $params) ? $params['process'] : null;
  $processParameter = $processFunction && array_key_exists('parameter', $params) ? $params['parameter'] : null;
  # get all results
  $allresults = I18nSearcher::search($alltags, $allwords, $order, $lang);
  if ($processFunction) {
    if (!function_exists($processFunction) && function_exists('i18n_search_'.$processFunction)) {
      $processFunction = 'i18n_search_'.$processFunction;
    }
    # process results, e.g.
    $allresults = call_user_func($processFunction, $allresults, $processParameter);
  }
  # get requested result page
  $totalCount = count($allresults);
  if ($max > 0) {
    $results = array_slice($allresults, $first, $max); 
  } else if ($first > 0) { 
    $results = array_slice($results, $first);
  } else {
    $results = $allresults;
  }
  
  if (trim($headerText) != '') {
?>
  <h2 class="search-header"><?php echo $headerText; ?></h2>
<?php
  }
  if (count($results) <= 0 && !$live) {
?>
<p class="search-no-results"><?php echo htmlspecialchars($notFoundText); ?></p>
<?php
  } else {
    $numpages = 1 + (int)(($totalCount-1) / $max);
    $oldLocale = setlocale(LC_TIME,'0');
    if ($dateLocale) setlocale(LC_TIME, preg_split('/\s*,\s*/', $dateLocale));
    if ($componentName) {
      if (function_exists('return_i18n_component')) {
        $component = return_i18n_component($componentName);
      } else {
        if (file_exists(GSDATAOTHERPATH.'components.xml')) {
          $data = getXML(GSDATAOTHERPATH.'components.xml');
          if (count($data->item) != 0) foreach ($data->item as $item) {
            if ($componentName == $item->slug) { 
              $component = stripslashes(htmlspecialchars_decode($item->value, ENT_QUOTES));
              break;
            }
          }
        }
      }
    }
?>
<ul <?php if ($live) echo 'id="search-results-live"'; ?> class="search-results <?php if ($live) echo 'search-live'; ?>">
<?php
    $num = 0;
    foreach ($results as $item) {
      $id = $idPrefix ? $idPrefix . (++$num) : null;
      $classes = $live ? ' search-preview' : '';
      if ($tagClassPrefix) {
        foreach ($item->tags as $tag) {
          $classes .= ' '.$tagClassPrefix.preg_replace('/[^\w]/', '_', $tag);
        }
      }
?>
  <li <?php if ($id) echo 'id="'.$id.'"'; ?> class="search-entry <?php echo $classes; ?>">
<?php
      if (@$component) {
        i18n_search_display_with_component($component, $item, $showLanguage, $showDate, $dateFormat, $numWords);
      } else {
        global $filters;
        $done = false;
        // is there any plugin that displays/renders this item?
        foreach ($filters as $filter)  {
          if ($filter['filter'] == I18N_FILTER_DISPLAY_ITEM) {
            if (call_user_func_array($filter['function'], array($item, $showLanguage, $showDate, $dateFormat, $numWords))) {
              $done = true; 
              break; 
            }
          }
        }
        if (!$done) {
          // let's display the item in the standard way
          $link = !$is_i18n && @$item->simplelink ? $item->simplelink : $item->link;
?>
    <h3 class="search-entry-title">
<?php     if ($showLanguage) { ?>
      <span class="search-entry-language"><?php echo htmlspecialchars($item->language, ENT_NOQUOTES); ?></span>
<?php     } ?>
<?php     if ($link) { ?>
      <a href="<?php echo htmlspecialchars($link); ?>">
<?php     } ?>
        <?php echo htmlspecialchars($item->title, ENT_NOQUOTES); ?>
<?php     if ($link) { ?>
      </a>
<?php     } ?>
    </h3>
<?php     if ($showDate) { ?>
    <div class="search-entry-date"><?php echo strftime($dateFormat, $item->pubDate); ?></div>
<?php     } ?>
    <div class="search-entry-excerpt"><?php echo ''.$item->getExcerpt($item->content,$numWords); ?></div>
<?php   
        } 
      }      
?>
  </li>
<?php
    }
    if ($dateLocale) setLocale(LC_TIME, $oldLocale);
?>
</ul>
<?php
    if ($showPaging && $numpages > 1) {
      // determine link
      $link = function_exists('get_i18n_page_url') ? get_i18n_page_url(true) : get_page_url(true);
      $link .= (strpos($link,'?') !== false ? '&' : '?');
      if (@$_REQUEST['tags']) $link .= 'tags='.urlencode(@$_REQUEST['tags']) . '&';
      if (@$_REQUEST['words']) $link .= 'words='.urlencode(@$_REQUEST['words']) . '&';
      $link1 = substr($link, 0, -1);
      if (defined('PAGIFY_SEPARATOR')) {
        preg_match('/^([^\?]*[^\?\/])(\/?(\?.*)?)$/', $link, $match);
        $link = $match[1].PAGIFY_SEPARATOR.'%PAGE%'.@$match[2];
      } else {
        $link .= 'page=%PAGE%&';
      }
      $link = substr($link, 0, -1);
      if (function_exists('return_pagify_navigation')) {
        $pagingHtml = return_pagify_navigation($numpages, $pageNum, $link, $link1);
      } else {
        $link = htmlspecialchars($link);
        $pagingHtml = '<div class="search-results-paging">';
        if (@$i18n['FIRST_TEXT'] && $pageNum > 0) {
          $pagingHtml .= '<span class="first"><a href="'.$link1.'" title="'.htmlspecialchars(@$i18n['FIRST_TITLE']).'">'.htmlspecialchars(@$i18n['FIRST_TEXT']).'&nbsp;</a></span>';
        }
        if (@$i18n['PREV_TEXT'] && $pageNum > 0) {
          $pagingHtml .= '<span class="previous"><a href="'.($pageNum == 1 ? $link1 : str_replace('%PAGE%',$pageNum,$link)).'" title="'.htmlspecialchars(@$i18n['PREV_TITLE']).'">'.htmlspecialchars(@$i18n['PREV_TEXT']).'&nbsp;</a></span>';
        }
        for ($i=0; $i<$numpages; $i++) {
          if ($i == $pageNum) {
            $pagingHtml .= ' <span class="current">'.($i+1).'</span>';
          } else {
            $pagingHtml .= ' <span><a href="'.($i == 0 ? $link1 : str_replace('%PAGE%',$i+1,$link)).'">'.($i+1).'</a></span>';
          }
        }
        if (@$i18n['NEXT_TEXT'] && $pageNum < $numpages-1) {
          $pagingHtml .= ' <span class="next"><a href="'.str_replace('%PAGE%',$pageNum+2,$link).'" title="'.htmlspecialchars(@$i18n['NEXT_TITLE']).'">&nbsp;'.htmlspecialchars(@$i18n['NEXT_TEXT']).'</a></span>';
        }
        if (@$i18n['LAST_TEXT'] && $pageNum < $numpages-1) {
          $pagingHtml .= ' <span class="next"><a href="'.str_replace('%PAGE%',$numpages,$link).'" title="'.htmlspecialchars(@$i18n['LAST_TITLE']).'">&nbsp;'.htmlspecialchars(@$i18n['LAST_TEXT']).'</a></span>';
        }
        $pagingHtml .= '</div>';
      }
      echo "\n" . $pagingHtml;
    }
  }


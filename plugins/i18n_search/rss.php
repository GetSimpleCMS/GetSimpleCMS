<?php
  if (function_exists('i18n_init')) i18n_init();
  $is_i18n = function_exists('return_i18n_default_language');
  if ($is_i18n && array_key_exists('i18n',$params) && !$params['i18n']) $is_i18n = false;
  $lang = array_key_exists('lang',$params) ? $params['lang'] : null;
  $tags = array_key_exists('tags',$params) ? $params['tags'] : null;
  $words = array_key_exists('words',$params) ? $params['words'] : null;
  $order = array_key_exists('order',$params) ? $params['order'] : 'date';
  $max = array_key_exists('max',$params) ? intval($params['max']) : I18N_MAX_RESULTS; 
  $r = return_i18n_search_results($tags, $words, 0, $max, $order, $lang);
  $results = $r['results'];
  i18n_search_get_rss($results, $params);
  
  function i18n_search_get_rss($results, $params) {
    global $SITEURL,$LANG;
    $is_i18n = function_exists('return_i18n_default_language');
    $numWords = array_key_exists('numWords',$params) ? $params['numWords'] : I18N_NUM_WORDS;
    $lang = @$params['lang'] ? $params['lang'] : (@$LANG ? $LANG : 'en');
    $lang = strtolower(str_replace('_','-',$lang));
    header('Content-Type: application/rss+xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0">
<channel>
  <title><?php echo htmlspecialchars($params['title']); ?></title>
  <link><?php echo $SITEURL; ?></link>
  <description><?php echo htmlspecialchars(@$params['description']); ?></description>
  <language><?php echo $lang; ?></language>
  <copyright><?php echo $SITEURL; ?></copyright>
  <pubDate><?php echo date("r"); ?></pubDate>
<?php 
    foreach ($results as $item) { 
      $link = !$is_i18n && @$item->simplelink ? $item->simplelink : $item->link;
?>
  <item>
    <guid><?php echo $link; ?></guid>
    <title><?php echo htmlspecialchars($item->title); ?></title>
    <description><![CDATA[<?php echo $item->getExcerpt($item->content, $numWords); ?>]]></description>
    <link><?php echo $link; ?></link>
  </item>
<?php 
    } 
?>
</channel>
</rss>
<?php
  }


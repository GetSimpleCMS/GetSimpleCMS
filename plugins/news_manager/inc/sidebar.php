<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * Optional sidebar functions for the GetSimple News Manager Plugin.
 */


/*******************************************************
 * @function nm_list_recent
 * @action print a list with the latest posts (titles only)
 */
function nm_list_recent() {
  global $NMPAGEURL, $NMRECENTPOSTS;
  if ($NMPAGEURL == '') return;
  $posts = nm_get_posts_default();
  if (!empty($posts)) {
    echo '<ul class="nm_recent">',"\n";
    $posts = array_slice($posts, 0, $NMRECENTPOSTS, true);
    foreach ($posts as $post) {
      $url = nm_get_url('post') . $post->slug;
      $title = stripslashes($post->title);
      echo '  <li><a href="',$url,'">',$title,'</a></li>',"\n";
    }
    echo '</ul>',"\n";
  }
}


/*******************************************************
 * @function nm_list_archives
 * @action print a list of archives ordered by month or year
 * @param $args (since 3.3) array with optional parameters, or
                (since 3.0, deprecated) optional custom format (strftime), default "%B %Y" or "%Y" or as defined in language file
 */
function nm_list_archives($args = '') {
  global $NMPAGEURL, $NMSETTING;
  if ($NMPAGEURL == '') return;
  $defaults = array(
    'showcount' => false,
    'dateformat' => '',
    'tag' => null
  );
  if (!$args) {
    $args = $defaults;
  } else {
    if (!is_array($args)) // backwards NM 3.0 - deprecate
      $args = array('dateformat' => strval($args));
    $args = array_merge($defaults, $args);
  }
  $fmt = $args['dateformat'];
  $showcount = $args['showcount'];
  $archivesby = $NMSETTING['archivesby'];
  $tag = $args['tag'];
  if (!$tag) {
    $url = nm_get_url('archive');
  } else {
    $url = nm_get_url('tag').rawurlencode($tag);
    if (nm_get_option('tagarchives') == 'f')
      $url .= '/'.NMPARAMARCHIVE.'/';
    else
      $url .= '&amp;'.NMPARAMARCHIVE.'=';
  }
  $archives = array();
  foreach (nm_get_archives($archivesby, $tag) as $archive=>$slugs)
    $archives[$archive] = count($slugs);

  if (!empty($archives)) {
    echo '<ul class="nm_archives">',"\n";
    if (!$fmt) {
      if ($archivesby == 'y')
        $fmt = isset($i18n['news_manager/YEARLY_FORMAT']) ? $i18n['news_manager/YEARLY_FORMAT'] : '%Y';
      else
        $fmt = isset($i18n['news_manager/MONTHLY_FORMAT']) ? $i18n['news_manager/MONTHLY_FORMAT'] : '%B %Y';
    }
    foreach ($archives as $archive=>$count) {
      if ($archivesby == 'y') {
        # annual
        $y = $archive;
        $title = nm_get_date($fmt, mktime(0, 0, 0, 1, 1, $y));
      } else {
        # monthly
        list($y, $m) = str_split($archive, 4);
        $title = nm_get_date($fmt, mktime(0, 0, 0, $m, 1, $y));
      }
      echo '  <li><a href="',$url.$archive,'">',$title,'</a>';
      if ($showcount) echo '&nbsp;(',$count,')'; 
      echo '</li>',"\n";
    }
    echo '</ul>',"\n";
  }
}

/*******************************************************
 * @function nm_list_tag_archives
 * @action print a list of archives for given or current tag
 * @param $args array with optional parameters (like nm_list_archives)
 * @since 3.3
 */
function nm_list_tag_archives($args = array()) {
  if (!is_array($args)) $args = array();
  if (isset($args['tag'])) {
    # if tag is specified, do the same as...
    nm_list_archives($args);
  } else {
    # use current tag
    if (nm_is_tag()) {
      $args['tag'] = nm_single_tag_title('','',false);
      nm_list_archives($args);
    }
  }
}

/*******************************************************
 * @function nm_list_tags
 * @action print unique tags, popular tags are bigger.
 */
function nm_list_tags() {
  global $NMPAGEURL;
  if ($NMPAGEURL == '') return;
  $tags = array();
  foreach (nm_get_tags() as $tag=>$posts)
    if (substr($tag, 0, 1) != '_')
      $tags[$tag] = count($posts);
  if (!empty($tags)) {
    $min = min($tags);
    $max = max($tags);
    foreach ($tags as $tag=>$count) {
      $url = nm_get_url('tag').rawurlencode($tag);
      if ($min < $max && $count/$max > 0.5)
        echo '<a class="large" href="',$url,'">',htmlspecialchars($tag),'</a>',"\n";
      else
        echo '<a href="',$url,'">',htmlspecialchars($tag),'</a>',"\n";
    }
    echo "\n";
  }
}

/*******************************************************
 * @function nm_tag_list
 * @action display list of unique tags
 * @since 3.0
 * @param $args (since 3.3) array with optional parameters
 */
function nm_tag_list($args = null) {
  global $NMPAGEURL;
  if ($NMPAGEURL == '') return;
  $defaults = array(
    'showcount' => false,
    'classcurrent' => false
  );
  if (!$args || !is_array($args))
    $args = $defaults;
  else
    $args = array_merge($defaults, $args);
  if ($args['classcurrent']) {
    $classcurrent = $args['classcurrent'];
    $currenttag = nm_single_tag_title('', '', false);
  } else {
    $classcurrent = false;
  }
  $tags = array();
  foreach (nm_get_tags() as $tag=>$posts)
    if (substr($tag, 0, 1) != '_')
      $tags[$tag] = count($posts);
  if (!empty($tags)) {
    echo '<ul class="nm_tag_list">',"\n";
    foreach ($tags as $tag=>$count) {
      $url = nm_get_url('tag').rawurlencode($tag);
      if ($classcurrent && $tag == $currenttag)
        echo '  <li class="',htmlspecialchars($classcurrent),'">';
      else
        echo '  <li>';
      echo '<a href="',$url,'">',htmlspecialchars($tag),'</a>';
      if ($args['showcount'])
        echo '&nbsp;(',$count,')';
      echo '</li>',"\n";
    }
    echo '</ul>',"\n";
  }
}


/*******************************************************
 * @function nm_search
 * @action provide form to search posts by keyword(s)
 */
function nm_search() {
  global $NMPAGEURL, $i18n;
  if ($NMPAGEURL == '') return;
  $placeholder = (isset($i18n['news_manager/SEARCH_PLACEHOLDER'])) ? $i18n['news_manager/SEARCH_PLACEHOLDER'] : '';
  $url = nm_get_url();
  ?>
  <form id="search" action="<?php echo $url; ?>" method="post">
    <input type="text" class="text" name="keywords" value="" <?php
    if ($placeholder) echo 'placeholder="',htmlspecialchars($placeholder),'"';
    ?> /><!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="20" value="Ignore field. IE bug fix"
    /><![endif]--><input type="submit" class="submit" name="search" value="<?php i18n('news_manager/SEARCH'); ?>" />
  </form>
  <?php
}


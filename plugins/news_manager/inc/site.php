<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager front-end functions.
 */


/*******************************************************
 * @function nm_show_page
 * @param $index - page index (pagination)
 * @param $filter - if true, apply content filter
 * @action show posts on news page
 */
function nm_show_page($index=NMFIRSTPAGE, $filter=true) {
  global $NMPOSTSPERPAGE, $nmoption;
  $p1 = intval(NMFIRSTPAGE);
  $index = intval($index);
  $posts = nm_get_posts_default();
  $pages = array_chunk($posts, intval($NMPOSTSPERPAGE), true);
  if ($index >= $p1 && $index-$p1 < sizeof($pages))
    $posts = $pages[$index-$p1];
  else
    $posts = array();
  if (!empty($posts)) {
    $showexcerpt = nm_get_option('excerpt');
    if ($filter) ob_start();
    foreach ($posts as $post)
      nm_show_post($post->slug, $showexcerpt, false);
    if (sizeof($pages) > 1 && nm_get_option('shownav',true))
      nm_show_navigation($index, sizeof($pages));
    if ($filter) echo nm_ob_get_content(true);
    return true;
  } else {
    echo '<p>',i18n_r('news_manager/NO_POSTS'),'</p>';
    return false;
  }
}


/*******************************************************
 * @function nm_show_archive
 * @param $archive - unique archive id
 * @param $filter - if true, apply content filter
 * @action show posts by archive
 * @return true if posts shown
 */
function nm_show_archive($archive, $filter=true) {
  global $NMSETTING;
  $archives = nm_get_archives($NMSETTING['archivesby']);
  if (array_key_exists($archive, $archives)) {
    $showexcerpt = nm_get_option('excerpt');
    $posts = $archives[$archive];
    if ($filter) ob_start();
    foreach ($posts as $slug)
      nm_show_post($slug, $showexcerpt, false);
    if ($filter) echo nm_ob_get_content(true);
    return true;
  } else {
    echo '<p>',i18n_r('news_manager/NO_POSTS'),'</p>';
    return false;
  }
}

/*******************************************************
 * @function nm_show_tag_archive
 * @param $tag - tag to filter by
 * @param $archive - unique archive id
 * @param $filter - if true, apply content filter
 * @action show tagged posts by archive
 * @return true if posts shown
 * @since 3.3
 */
function nm_show_tag_archive($tag=null, $archive, $filter=true) {
  global $NMSETTING;
  $archives = nm_get_archives($NMSETTING['archivesby'], $tag);
  if (array_key_exists($archive, $archives)) {
    $showexcerpt = nm_get_option('excerpt');
    $posts = $archives[$archive];
    if ($filter) ob_start();
    foreach ($posts as $slug)
      nm_show_post($slug, $showexcerpt, false);
    if ($filter) echo nm_ob_get_content(true);
    return true;
  } else {
    echo '<p>',i18n_r('news_manager/NO_POSTS'),'</p>';
    return false;
  }
}


/*******************************************************
 * @function nm_show_tag
 * @param $tag - unique tag id
 * @param $filter - if true, apply content filter
 * @action show posts by tag
 * @return true if posts shown
 */
function nm_show_tag($tag, $filter=true) {
  $tag = nm_lowercase_tags($tag);
  $tags = nm_get_tags();
  if (array_key_exists($tag, $tags)) {
    $showexcerpt = nm_get_option('excerpt');
    $posts = $tags[$tag];
    $max = intval(nm_get_option('maxposts'));
    if ($max) $posts = array_slice($posts, 0, $max);
    if ($filter) ob_start();
    foreach ($posts as $slug)
      nm_show_post($slug, $showexcerpt, false);
    if ($filter) echo nm_ob_get_content(true);
    return true;
  } else {
    echo '<p>',i18n_r('news_manager/NO_POSTS'),'</p>';
    return false;
  }
}

/*******************************************************
 * @function nm_show_tag_page
 * @param $tag - unique tag id
 * @param $index - page index (pagination)
 * @param $filter - if true, apply content filter
 * @action show posts by tag with pagination
 * @return true if posts shown
 * @since 3.0
 */
function nm_show_tag_page($tag, $index=NMFIRSTPAGE, $filter=true) {
  global $NMPOSTSPERPAGE;
  $tag = nm_lowercase_tags($tag);
  $tags = nm_get_tags();
  if (array_key_exists($tag, $tags)) {
    $showexcerpt = nm_get_option('excerpt');
    $posts = $tags[$tag];
    $p1 = intval(NMFIRSTPAGE);
    $index = intval($index);
    $pages = array_chunk($posts, intval($NMPOSTSPERPAGE), true);
    if ($index >= $p1 && $index-$p1 < sizeof($pages)) {
      $posts = $pages[$index-$p1];
      if ($filter) ob_start();
      foreach ($posts as $slug)
        nm_show_post($slug, $showexcerpt, false);
      if (sizeof($pages) > 1 && nm_get_option('shownav',true))
        nm_show_navigation($index, sizeof($pages), $tag);
      if ($filter) echo nm_ob_get_content(true);
      return true;
    }
  }
  echo '<p>',i18n_r('news_manager/NO_POSTS'),'</p>';
  return false;
}


/*******************************************************
 * @function nm_show_search_results()
 * @action search posts by keyword(s)
 */
function nm_show_search_results() {
  $keywords = preg_split('/\s+/u',trim($_POST['keywords']),null,PREG_SPLIT_NO_EMPTY);
  if (empty($keywords)) {
    $posts = array();
  } else {
    $posts = nm_get_posts();
    $mb = function_exists('mb_stripos');
    foreach ($keywords as $keyword) {
      $match = array();
      foreach ($posts as $post) {
        $data = getXML(NMPOSTPATH.$post->slug.'.xml');
        $content = $data->title . $data->content;
        if (($mb && mb_stripos($content, $keyword, 0, 'UTF-8') !== false) || (!$mb && stripos($content, $keyword) !== false))
          $match[] = $post;
      }
      $posts = $match;
    }
  }
  if (!empty($posts)) {
    $showexcerpt = nm_get_option('excerpt');
    echo '<p>' . i18n_r('news_manager/FOUND') . '</p>',"\n";
    foreach ($posts as $post)
      nm_show_post($post->slug, $showexcerpt, false);
  } else {
    echo '<p>' . i18n_r('news_manager/NOT_FOUND') . '</p>',"\n";
  }
}

/*******************************************************
 * @function nm_reset_options
 * @param $pagetype news page type, can be 'single', 'main', 'archive', 'tag', 'search' or empty
 * @action set default or specific layout values
 * @since 3.0
 */
function nm_reset_options($pagetype='') {
  global $nmoption, $NMSETTING, $NMSHOWEXCERPT;
  $nmoption = array();

  # title link
  $nmoption['titlelink'] = ($NMSETTING['titlelink']=='Y' || ($NMSETTING['titlelink']=='P' && $pagetype != 'single'));

  # go back link
  if ($pagetype == 'single') {
    if ($NMSETTING['gobacklink'] == 'N')
      $nmoption['gobacklink'] = false;
    elseif ($NMSETTING['gobacklink'] == 'M')
      $nmoption['gobacklink'] = 'main';
    else
      $nmoption['gobacklink'] = true;
  }

  # tag separator
  $nmoption['tagseparator'] = ' ';

  # author
  $nmoption['showauthor'] = false;
  $nmoption['defaultauthor'] = '';

  # images
  if ( $NMSETTING['images'] == 'N'
    || ($pagetype == 'single' && $NMSETTING['images'] == 'P')
    || ($pagetype != 'main' && $NMSETTING['images'] == 'M') ) {
    $nmoption['showimages'] = false;
  } else {
    $nmoption['showimages'] = true;
  }
  $nmoption['imagewidth'] = intval($NMSETTING['imagewidth']);
  $nmoption['imageheight'] = intval($NMSETTING['imageheight']);
  $nmoption['imagecrop'] = ($NMSETTING['imagecrop'] == '1');
  $nmoption['imagealt'] = ($NMSETTING['imagealt'] == '1');
  $nmoption['imagelink'] = ($pagetype != 'single' && $NMSETTING['imagelink'] == '1');
  $nmoption['imagetitle'] = false;
  $nmoption['imageexternal'] = false;
  $nmoption['imagedefault'] = '';
  $nmoption['imagesizeattr'] = false;
  $nmoption['imagethumbnail'] = false;

  # custom settings
  if ($NMSETTING['enablecustomsettings'] == '1') {
    # extract settings
    foreach(preg_split('~\R~', $NMSETTING['customsettings']) as $line) {
      $line = trim($line);
      if ($line && strpos($line,'#') !== 0 && strpos($line,'//') !== 0) { // exclude empty and commented lines
        $arr = explode(' ',preg_replace("/[[:blank:]]+/"," ",$line));
        if (count($arr) > 1) {
          if (in_array($arr[0], array('main','single','archive','tag','search')))
            $customsettings[$arr[0]][$arr[1]] = implode(' ',array_slice($arr,2));
          else
            $customsettings['default'][$arr[0]] = implode(' ',array_slice($arr,1));
        }
      }
    }
    # process settings and strings
    foreach(array('default', $pagetype) as $type) {
      if (isset($customsettings[$type])) {
        foreach($customsettings[$type] as $key=>$value) {
          if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1,strlen($value)-2);
          if (strtoupper($key) == $key) {
            # language string
            nm_set_text($key, $value);
          } else {
            # setting
            $nmoption[strtolower($key)] = $value;
          }
        }
      }
    }
  }

  # html tags
  foreach (array(
    'markuppost'        => 'div',
    'markupposttitle'   => isset($nmoption['markuptitle']) ? nm_clean_markup($nmoption['markuptitle']) : 'h3', // backwards NM 3.0
    'markuppostdate'    => 'p',
    'markuppostauthor'  => 'p',
    'markuppostauthorname' => 'em',
    'markuppostimage'   => 'div',
    'markuppostcontent' => 'div',
    'markupposttags'    => 'p',
    'markupgoback'      => 'p',
    ) as $key=>$value)
      $nmoption[$key] = isset($nmoption[$key]) ? nm_clean_markup($nmoption[$key]) : $value;

  # fields
  if (isset($nmoption['showfields'])) {
    $nmoption['fields'] = explode(' ',preg_replace('/  +/', ' ',trim(str_replace(',',' ',$nmoption['showfields']))));
  } else {
    $nmoption['fields'] = array('title','date','author','image','content','tags');
  }

  # imagesize shorthand
  if (isset($nmoption['imagesize'])) {
    if ($nmoption['imagesize'] == 0 || $nmoption['imagesize'] == 'full') {
      $nmoption['imagewidth'] = 0;
      $nmoption['imageheight'] = 0;
      $nmoption['imagecrop'] = 0;
    } else {
      $imageparams = explode(' ',preg_replace('/  +/', ' ',trim(str_replace(',',' ',$nmoption['imagesize']))));
      $nmoption['imagewidth'] = isset($imageparams[0]) ? $imageparams[0] : 0;
      $nmoption['imageheight'] = isset($imageparams[1]) ? $imageparams[1] : 0;
      $nmoption['imagecrop'] = isset($imageparams[2]) ? $imageparams[2] : 0;
    }
  }

  # custom excerpt length
  if (isset($nmoption['excerptlength'])) {
    global $NMEXCERPTLENGTH;
    $NMEXCERPTLENGTH = $nmoption['excerptlength']; // workaround(*)
  }

  # more
  if (!isset($nmoption['more'])) $nmoption['more'] = false;

  # full/excerpt
  if (!isset($nmoption['excerpt'])) {
    if ($NMSHOWEXCERPT == 'Y' || in_array($pagetype, array('archive','search','tag')))
      $nmoption['excerpt'] = true;
    else
      $nmoption['excerpt'] = false; // full post
  }

  # readmore
  if (isset($nmoption['readmore'])) {
    if (strtolower($nmoption['readmore'][0]) == 'a') // custom setting - anything beginning with 'a' (all, Always, ...)
      $nmoption['readmore'] = 'a';
  } else {
    if ($NMSETTING['readmore'] == 'R')
      $nmoption['readmore'] = true;
    elseif ($NMSETTING['readmore'] == 'F')
      $nmoption['readmore'] = 'a';
    else
      $nmoption['readmore'] = false;
  }

  # tag pagination
  if (!isset($nmoption['tagpagination'])) {
    $nmoption['tagpagination'] = false;
  } else { // anything beginning with 'd' (Default, Dynamic...) or 'f' (Fancy, Folder...)
    $nmoption['tagpagination'] = strtolower($nmoption['tagpagination'][0]);
    if (!in_array($nmoption['tagpagination'], array('d','f')))
      $nmoption['tagpagination'] = false;
  }

  # tag archives
  if (!isset($nmoption['tagarchives'])) {
    $nmoption['tagarchives'] = false;
  } else { // anything beginning with 'd' (Default, Dynamic...) or 'f' (Fancy, Folder...)
    $nmoption['tagarchives'] = strtolower($nmoption['tagarchives'][0]);
    if (!in_array($nmoption['tagarchives'], array('d','f')))
      $nmoption['tagarchives'] = false;
  }

  # append custom classes for layout elements
  foreach (array(
    'classpost'           => 'nm_post'.($pagetype == 'single' ? ' nm_post_single' : ''),
    'classposttitle'      => 'nm_post_title',
    'classposttitlelink'  => '',
    'classpostdate'       => 'nm_post_date',
    'classpostauthor'     => 'nm_post_author',
    'classpostimage'      => 'nm_post_image',
    'classpostimagelink'  => '',
    'classpostcontent'    => 'nm_post_content',
    'classreadmore'       => 'nm_readmore',
    'classreadmorelink'   => '',
    'classposttags'       => 'nm_post_meta',
    'classgoback'         => 'nm_post_back',
    'classgobacklink'     => '',
    'classnav'            => 'nm_page_nav',
    ) as $key=>$value)
    $nmoption[$key] = !isset($nmoption[$key]) ? $value : nm_clean_classes($value.' '.$nmoption[$key]);

  # force full content if single post
  $nmoption['fullcontent'] = ($pagetype == 'single');

}


/*******************************************************
 * @function nm_show_post
 * @param $slug post slug
 * @param $showexcerpt - if TRUE, print only a short summary
 * @param $filter - if true, apply content filter
 * @param $single post page?
 * @action show the requested post on front-end news page, as defined by $nmoption values
 * @return true if post exists
 */
function nm_show_post($slug, $showexcerpt=false, $filter=true, $single=false) {
  global $nmoption, $nmdata;
  $file = NMPOSTPATH.$slug.'.xml';
  if (dirname(realpath($file)) == realpath(NMPOSTPATH)) // no path traversal
    $post = @getXML($file);
  if (!empty($post) && ($post->private != 'Y' || ($single && function_exists('is_logged_in') && is_logged_in()))) {
    $url     = nm_get_url('post') . $slug;
    $title   = strip_decode($post->title);
    $unixtime = strtotime($post->date);
    $date    = nm_get_date(i18n_r('news_manager/DATE_FORMAT'), $unixtime);
    $content = strip_decode($post->content);
    $image   = stripslashes($post->image);
    $metad   = stripslashes($post->metad);
    $tags = !empty($post->tags) ? explode(',', nm_lowercase_tags(strip_decode($post->tags))) : array();

    # save post data?
    $nmdata = ($single) ? compact('slug', 'url', 'title', 'content', 'image', 'tags', 'unixtime', 'metad') : array();

    if ($filter) ob_start();

    echo '  <',$nmoption['markuppost'],' class="',$nmoption['classpost'],'">',"\n";

    foreach ($nmoption['fields'] as $field) {
      switch($field) {

        case 'title':
          echo '    <',$nmoption['markupposttitle'],' class="',$nmoption['classposttitle'],'">';
          if ($nmoption['titlelink']) {
            $class = $nmoption['classposttitlelink'] ? ' class="'.$nmoption['classposttitlelink'].'"' : '';
            echo '<a',$class,' href="',$url,'">',htmlspecialchars($title),'</a>';
          } else {
            echo htmlspecialchars($title);
          }
          echo '</',$nmoption['markupposttitle'],'>',"\n";
          break;

        case 'date':
          echo '    <',$nmoption['markuppostdate'],' class="',$nmoption['classpostdate'],'">',i18n_r('news_manager/PUBLISHED'),' ',$date,'</',$nmoption['markuppostdate'],'>',"\n";
          break;

        case 'content':
          echo '    <',$nmoption['markuppostcontent'],' class="',$nmoption['classpostcontent'],'">';
          if ($nmoption['fullcontent']) {
            echo $content;
          } else {
            $slice = '';
            $class = '';
            $readmore = $nmoption['readmore'];
            if ($readmore)
              $class = $nmoption['classreadmorelink'] ? ' class="'.$nmoption['classreadmorelink'].'"' : '';
            if ($nmoption['more']) {
              $morepos = strpos($content, '<hr');
              if ($morepos !== false) {
                $slice = substr($content, 0, $morepos);
                if ($readmore)
                  $slice .= '      <p class="'.$nmoption['classreadmore'].'"><a'.$class.' href="'.$url.'">'.i18n_r('news_manager/READ_MORE').'</a></p>'."\n";
              }
            }
            if ($slice) {
              echo $slice;
            } else {
              if ($showexcerpt) {
                if (!$readmore)
                  echo nm_create_excerpt($content);
                elseif ($readmore === 'a')
                  echo nm_create_excerpt($content, $url, true);
                else
                  echo nm_create_excerpt($content, $url);
              } else {
                echo $content;
                if ($readmore === 'a')
                  echo '      <p class="',$nmoption['classreadmore'],'"><a',$class,' href="',$url,'">',i18n_r('news_manager/READ_MORE'),'</a></p>',"\n";
              }
            }
          }
          echo '    </',$nmoption['markuppostcontent'],'>',"\n";
          break;

        case 'tags':
          if ($tags) {
            echo '    <',$nmoption['markupposttags'],' class="',$nmoption['classposttags'],'"><b>',i18n_r('news_manager/TAGS'),':</b> ';
            $sep = '';
            foreach ($tags as $tag)
              if (substr($tag, 0, 1) != '_') {
                echo $sep,'<a href="',nm_get_url('tag').rawurlencode($tag),'">',htmlspecialchars($tag),'</a>';
                if ($sep == '') $sep = $nmoption['tagseparator'];
              }
            echo '</',$nmoption['markupposttags'],'>',"\n";
          }
          break;

        case 'image':
          $imageurl = $nmoption['showimages'] ? nm_get_image_url($image) : false;
          if ($imageurl) {
            $str = '';
            if (isset($nmoption['imageclass']))
              $str .= ' class="'.$nmoption['imageclass'].'"';
            if ($nmoption['imagesizeattr'] && $nmoption['imagewidth'] && $nmoption['imageheight'])
              $str .= ' width="'.$nmoption['imagewidth'].'" height="'.$nmoption['imageheight'].'"';
            $str .= $nmoption['imagealt']   ? ' alt="'.htmlspecialchars($title, ENT_COMPAT).'"' : ' alt=""';
            $str .= $nmoption['imagetitle'] ? ' title="'.htmlspecialchars($title, ENT_COMPAT).'"' : '';
            $str = '<img src="'.htmlspecialchars($imageurl).'"'.$str.' />';
            if ($nmoption['imagelink']) {
              if ($nmoption['imagelink'] !== 'full')
                $str = $url.'">'.$str.'</a>';
              else
                $str = htmlspecialchars(nm_get_image_url($image,0,0)).'">'.$str.'</a>';
              if ($nmoption['classpostimagelink'])
                $str = '<a class="'.$nmoption['classpostimagelink'].'" href="'.$str;
              else
                $str = '<a href="'.$str;
            }
            echo '    <',$nmoption['markuppostimage'],' class="',$nmoption['classpostimage'],'">',$str,'</',$nmoption['markuppostimage'],'>',"\n";
          }
          break;

        case 'author':
          if ($nmoption['showauthor']) {
            $author = nm_get_author_name_html(stripslashes($post->author));
            if (empty($author) && $nmoption['defaultauthor'])
              $author = $nmoption['defaultauthor'];
            if (!empty($author))
                echo '    <',$nmoption['markuppostauthor'],' class="',$nmoption['classpostauthor'],'">',i18n_r('news_manager/AUTHOR'),' <',$nmoption['markuppostauthorname'],'>',$author,'</',$nmoption['markuppostauthorname'],'></',$nmoption['markuppostauthor'],'>',"\n";
          }
          break;
      }
    }

    if (isset($nmoption['componentbottompost'])) {
      get_component($nmoption['componentbottompost']);
      echo "\n";
    }
    if ($single) {
      # show "go back" link?
      if ($nmoption['gobacklink']) {
        $goback = ($nmoption['gobacklink'] === 'main') ? nm_get_url() : 'javascript:history.back()';
        $class = $nmoption['classgobacklink'] ? ' class="'.$nmoption['classgobacklink'].'"' : '';
        echo '    <',$nmoption['markupgoback'],' class="'.$nmoption['classgoback'].'"><a',$class,' href="'.$goback.'">';
        i18n('news_manager/GO_BACK');
        echo '</a></',$nmoption['markupgoback'],'>',"\n";
      }
    }

    echo '  </',$nmoption['markuppost'],'>',"\n";

    if (isset($nmoption['componentafterpost'])) {
      get_component($nmoption['componentafterpost']);
      echo "\n";
    }

    if ($filter) echo nm_ob_get_content(true);

    return true;
  } else {
    echo '<p>' . i18n_r('news_manager/NOT_EXIST') . '</p>',"\n";
    return false;
  }
}


/*******************************************************
 * @function nm_show_navigation
 * @param $index - current page index
 * @param $total - total number of subpages
 * @param $tag - tag to filter by (optional)
 * @action provides links to navigate between subpages in main news or tag page
 */
function nm_show_navigation($index, $total, $tag=null) {
  $p1 = intval(NMFIRSTPAGE);
  if (!$tag) {
    $first = nm_get_url();
    $page = nm_get_url('page');
  } else {
    $first = nm_get_url('tag').rawurlencode($tag);
    if (nm_get_option('tagpagination') == 'f')
      $page = $first.'/'.NMPARAMPAGE.'/';
    else
      $page = $first.'&amp;'.NMPARAMPAGE.'=';
  }

  $container  = nm_clean_markup(nm_get_option('markupnavcontainer',''));
  $nav        = nm_clean_markup(nm_get_option('markupnav','div'));
  $item       = nm_clean_markup(nm_get_option('markupnavitem','span'));

  $clcontainer  = nm_clean_classes(nm_get_option('classnavcontainer',''));
  $clnav        = nm_clean_classes(nm_get_option('classnav'));
  $clprev       = nm_clean_classes(nm_get_option('classnavitemprev','previous'));
  $clnext       = nm_clean_classes(nm_get_option('classnavitemnext','next'));
  $cldisabled   = nm_clean_classes(nm_get_option('classnavitemdisabled','disabled'));
  $clcurrent    = nm_clean_classes(nm_get_option('classnavitemcurrent','current'));

  if ($container)
    echo "<$container",nm_class_attr($clcontainer),">\n";
  echo "<$nav",nm_class_attr($clnav),">\n";

  if (!nm_get_option('navoldnew',false)) {

    $prevnext = nm_get_option('navprevnext', '1');
    $showalways = (strtolower($prevnext[0]) == 'a'); // navPrevNext a[lways]
    if ($prevnext && $index > $p1) {
      echo " <$item",nm_class_attr($clprev),"><a href=\"";
      echo $index > $p1+1 ? $page.($index-1) : $first;
      echo "\" title=\"",i18n_r('news_manager/PREV_TITLE'),'">',i18n_r('news_manager/PREV_TEXT'),"</a></$item>\n";
    } else {
      if ($showalways)
        echo " <$item",nm_class_attr($clprev.' '.$cldisabled),"><span>",i18n_r('news_manager/PREV_TEXT'),"</span></$item>\n";
    }

    if (nm_get_option('navnumber',true)) {
      $end = nm_get_option('navendsize',1);
      $mid = nm_get_option('navmidsize',2);
      if (nm_get_option('navfixsize',true)) {
        # if near one of the two ends, adjust $mid to keep fixed number of items
        if ($index-$p1 <= $mid+$end) {
          $mid = $mid*2+$end-($index-$p1);
        } elseif ($index-$p1 >= $total-1-($mid+$end)) {
          $mid = $mid*2+$end-($total-1-($index-$p1));
        }
      }
      $ellipsis = nm_get_option('navellipsis','...');
      $clellipsis = nm_clean_classes(nm_get_option('classnavellipsis','ellipsis'));
      $gap = false;
      for ($i = 0; $i < $total; $i++) {
        if ($i+$p1 == $index) {
          echo " <$item",nm_class_attr($clcurrent),"><span>",$i+1,"</span></$item>\n";
          $gap = false;
        } else {
          if ( ($i+$p1 >= $index-$mid && $i+$p1 <= $index+$mid) || $i <= $end-1 || $i >= $total-$end) {
            echo " <$item><a href=\"";
            echo $i == 0 ? $first : $page.($i+$p1);
            echo "\">",$i+1,"</a></$item>\n";
            $gap = false;
          } else {
            if (!$gap) {
              echo " <$item",nm_class_attr($clellipsis.' '.$cldisabled),"><span>$ellipsis</span></$item>\n";
              $gap = true;
            }
          }
        }
      }
    }

    if ($prevnext && $index < $total-1+$p1) {
      echo " <$item",nm_class_attr($clnext),"><a href=\"",$page.($index+1);
      echo "\" title=\"",i18n_r('news_manager/NEXT_TITLE'),"\">",i18n_r('news_manager/NEXT_TEXT'),"</a></$item>\n";
    } else {
      if ($showalways)
        echo " <$item",nm_class_attr($clnext.' '.$cldisabled),"><span>",i18n_r('news_manager/NEXT_TEXT'),"</span></$item>\n";
    }

  } else {

    # Older/Newer navigation
    $clold = nm_clean_classes(nm_get_option('classnavitemold','left'));
    $clnew = nm_clean_classes(nm_get_option('classnavitemnew','right'));

    $showalways = (strtolower(substr(nm_get_option('navoldnew'),0,1)) == 'a'); // navOldNew a[lways]
    if ($index < $total-1+$p1) {
      echo "<$item",nm_class_attr($clold),">";
      echo "<a href=\"",$page.($index+1),"\">",i18n_r('news_manager/OLDER_POSTS'),"</a>";
      echo "</$item>\n";
    } else {
      if ($showalways)
        echo " <$item",nm_class_attr($clold.' '.$cldisabled),"><span>",i18n_r('news_manager/OLDER_POSTS'),"</span></$item>\n";
    }
    if ($index > $p1) {
      echo "<$item",nm_class_attr($clnew),">";
      echo "<a href=\"",(($index > $p1+1) ? $page.($index-1) : $first),"\">",i18n_r('news_manager/NEWER_POSTS'),"</a>";
      echo "</$item>\n";
    } else {
      if ($showalways)
        echo " <$item",nm_class_attr($clnew.' '.$cldisabled),"><span>",i18n_r('news_manager/NEWER_POSTS'),"</span></$item>\n";
    }

  }
  echo "</$nav>\n";
  if ($container) echo "</$container>\n";
}


/*******************************************************
 * @function nm_post_title
 * @param $before Text to place before the title. Defaults to ''
 * @param $after Text to place after the title. Defaults to ''
 * @param $echo Display (true) or return (false)
 * @action Display or return the post title. Returns false if not on single post page
 * @since 2.3
 */
function nm_post_title($before='', $after='', $echo=true) {
  global $nmdata;
  if (isset($nmdata['title']) && $nmdata['title']) {
    $title = $before.htmlspecialchars($nmdata['title'], ENT_QUOTES).$after;
    if ($echo) echo $title;
    return $title;
  } else {
    return false;
  }
}

/*******************************************************
 * @function nm_post_slug
 * @param $echo Display (true) or return (false)
 * @action Display or return the post id (slug)
 * @return slug or false if not on single post page
 * @since 3.0
 */
function nm_post_slug($echo=true) {
  global $nmdata;
  if (isset($nmdata['slug']) && $nmdata['slug']) {
    $slug = $nmdata['slug'];
    if ($echo) echo $slug;
    return $slug;
  } else {
    return false;
  }
}

/*******************************************************
 * @function nm_post_url
 * @param $echo Display (true) or return (false)
 * @action Display or return the post URL (ampersands escaped since 3.5)
 * @return URL or false if not on single post page
 * @since 3.0
 */
function nm_post_url($echo=true) {
  global $nmdata;
  if (isset($nmdata['url']) && $nmdata['url']) {
    $url = htmlspecialchars($nmdata['url']);
    if ($echo) echo $url;
    return $url;
  } else {
    return false;
  }
}

/*******************************************************
 * @function nm_post_excerpt
 * @param $len Length or null for default length (settings)
 * @param $ellipsis Custom string for the ellipsis or null for default
 * @param $echo Display (true) or return (false)
 * @action Display or return a post excerpt
 * @return excerpt or empty string
 * @since 3.0
 */
function nm_post_excerpt($len=null, $ellipsis=null, $echo=true) {
  global $nmdata, $NMEXCERPTLENGTH, $nmoption;
  if (isset($nmdata['content']) && $nmdata['content']) {
    if (!$len) $len = isset($nmoption['excerptlength']) ? $nmoption['excerptlength'] : $NMEXCERPTLENGTH; // workaround(*)
    if (!$ellipsis && $ellipsis !== '') $ellipsis = i18n_r('news_manager/ELLIPSIS');
    $break = nm_get_option('breakwords');
    $excerpt = nm_make_excerpt($nmdata['content'], $len, $ellipsis, $break);
    if ($echo) echo $excerpt;
    return $excerpt;
  } else {
    return '';
  }
}

/*******************************************************
 * @function nm_post_image_url
 * @param $width or null for default width (settings)
 * @param $height or null for default height (settings)
 * @param $crop 0, 1, false or true, or null for default crop option (settings)
 * @param $default URL or filename of image if post has no image
 * @param $echo Display (true) or return (false)
 * @action Display or return post image URL
 * @return image URL or empty string
 * @since 3.0
 */
function nm_post_image_url($width=null, $height=null, $crop=null, $default=null, $echo=true) {
  global $nmdata;
  if (isset($nmdata['image']) && $nmdata['image']) {
    $url = htmlspecialchars(nm_get_image_url($nmdata['image'], $width, $height, $crop, $default));
    if ($echo) echo $url;
    return $url;
  } else {
    return '';
  }
}

/***
frontend functions, since 3.0
@todo: descriptions
 ***/

// conditionals

function nm_is_site() {
  global $nmpagetype;
  return in_array('site', $nmpagetype);
}

function nm_is_single() {
  global $nmdata;
  return isset($nmdata['slug']);
}

function nm_is_main() {
  global $nmpagetype;
  return in_array('main', $nmpagetype);
}

function nm_is_tag($tag=null) {
  global $nmpagetype;
  if (in_array('tag', $nmpagetype)) {
    if (!$tag)
      return true;
    else
      return (isset($_GET[NMPARAMTAG]) && $tag == rawurldecode($_GET[NMPARAMTAG]));
  }
}

function nm_is_archive() {
  global $nmpagetype;
  return in_array('archive', $nmpagetype);
}

function nm_is_search() {
  global $nmpagetype;
  return in_array('search', $nmpagetype);
}

function nm_is_home() {
  global $nmpagetype;
  return in_array('home', $nmpagetype);
}

function nm_post_has_image() {
  global $nmdata;
  return (isset($nmdata['image']) && $nmdata['image']);
}

// check if single post has any tag or a certain tag
function nm_post_has_tag($tag=null) {
  global $nmdata;
  if ($nmdata) {
    if (!isset($tag) && $nmdata['tags'])
      return true;
    elseif (in_array($tag, $nmdata['tags']))
      return true;
  }
  return false;
}

// set general option
function nm_set_option($option, $value=true) {
  global $nmoption;
  if ($option) $nmoption[strtolower($option)] = $value;
}

// get option value, return $default if not defined
function nm_get_option($option, $default=false) {
  global $nmoption;
  if ($option) {
    $option = strtolower($option);
    if (isset($nmoption[$option]))
      return $nmoption[$option];
    else
      return $default;
  }
}

// images

function nm_set_image_size($width=null, $height=null, $crop=false) {
  global $nmoption;
  $nmoption['imagewidth'] = $width;
  $nmoption['imageheight'] = $height;
  $nmoption['imagecrop'] = $crop;
}

// custom text/language strings

function nm_set_text($i18nkey=null, $i18nvalue=null) {
  global $i18n;
  if ($i18nkey && $i18nvalue !== null)
    $i18n['news_manager/'.$i18nkey] = strval($i18nvalue);
}


// patch for <title> tag - single post or tag view
function nm_update_page_title() {
  global $title, $nmpagetitle;
  $nmpagetitle = false;
  if (nm_is_site() && nm_get_option('titletag',true) && !function_exists('nmt_set_gstitle')) {
    if (nm_is_single()) {
      $nmpagetitle = $title;
      $title = nm_post_title('', ' - '.$title, false);
    } elseif (nm_is_tag()) {
      $nmpagetitle = $title;
      $title = nm_single_tag_title('', ' - '.$title, false);
    }
  }
}

// restore original title - <title> tag patch
function nm_restore_page_title() {
  global $title, $nmpagetitle;
  if ($nmpagetitle !== false)
    $title = $nmpagetitle;
}

// get output buffer, optionally apply content filter
function nm_ob_get_content($filter=true) {
  $output = ob_get_contents();
  ob_end_clean();
  if ($filter) {
    return exec_filter('content', $output);
  } else {
    return $output;
  }
}


/***** since 3.1 ****/

// display or return current tag, if in single tag view
function nm_single_tag_title($before='', $after='', $echo=true) {
  global $nmsingletag;
  if ($nmsingletag) {
    $str = $before.htmlspecialchars($nmsingletag).$after;
    if ($echo) echo $str;
    return $str;
  } else {
    return false;
  }
}

// set post tags as meta keywords
function nm_update_meta_keywords() {
  global $metak, $nmdata;
  $tags = array();
  foreach ($nmdata['tags'] as $tag)
    if (substr($tag, 0, 1) != '_') $tags[] = $tag;
  $metak = htmlspecialchars(implode($tags, ', '), ENT_COMPAT, 'UTF-8');
}

function nm_class_attr($str='') {
  return $str ? ' class="'.trim($str).'"' : '';
}

// remove some special chars from custom markup
function nm_clean_markup($str) {
  return str_replace(array('<','>','"','\'',' '), '', $str);
}

// remove invalid chars in custom CSS class selectors
function nm_clean_classes($str) {
  return trim(str_replace(array(
    '~','@','^','&','*','+','=',',','.','/', // '!','$','%','(',')',
    '\'',';',':','"','?','>','<','[',']','\\','{','}','|','`','#'
    ), '', $str));
}

/***** since 3.2 ****/

// returns display name for specified user, ready to be echoed
// (loads user file if name not in global array $NMAUTHOR)
function nm_get_author_name_html($author='') {
  global $NMAUTHOR;
  if (!$NMAUTHOR) $NMAUTHOR = array();
  if (isset($NMAUTHOR[$author])) {
    $name = $NMAUTHOR[$author];
  } elseif (file_exists(GSUSERSPATH.$author.'.xml')) {
      $userxml = getXML(GSUSERSPATH.$author.'.xml');
      $name = !empty($userxml->NAME) ? htmlspecialchars($userxml->NAME) : $author;
      $NMAUTHOR[$author] = $name;
  } else {
    $name = $author;
  }
  return $name;
}

/***** since 3.3 *****/

function nm_post_date($fmt='', $echo=true) {
  global $nmdata;
  if (isset($nmdata['unixtime']) && $nmdata['unixtime']) {
    if (empty($fmt))
      $fmt = i18n_r('news_manager/DATE_FORMAT');
    $date = nm_get_date($fmt, $nmdata['unixtime']);
    if ($echo) echo $date;
    return $date;
  } else {
    return false;
  }
}

/***** since 3.5 *****/

function nm_get_header() {
  nm_fix_get_header_full();
}

function nm_get_i18n_header($full=true, $omit=null) {
  nm_fix_get_header_full('get_i18n_header', $omit);
}

function nm_fix_get_header_full($function='get_header', $param2=null) {
  // TODO: paginated, etc.
  $canonical = false;
  if (nm_is_single())
    $canonical = nm_post_url(false);
  elseif (nm_is_tag())
    $canonical = nm_get_url('tag').rawurlencode(nm_single_tag_title('','',false));
  elseif (nm_is_archive())
    $canonical = nm_get_url('archive').intval($_GET[NMPARAMARCHIVE]);
  if ($canonical) {
    $function(false, $param2);
    echo '<link rel="canonical" href="',$canonical,'" />',"\n";
  } else {
    $function(true, $param2);
  }
}

/***** since 3.6 *****/

// set GS meta description
function nm_update_meta_description() {
  global $metad, $nmdata;
  if ($nmdata['metad']) {
    $metad = htmlspecialchars($nmdata['metad']);
  } else {
    if (nm_get_option('autometad')) {
      $metad = nm_post_excerpt(150, null, false);
    }
  }
}

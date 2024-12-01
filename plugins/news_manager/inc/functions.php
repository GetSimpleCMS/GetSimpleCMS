<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager general functions.
 */


/*******************************************************
 * @function nm_get_posts
 * @param $all if true, include private and future posts
 * @return array with posts
 */
function nm_get_posts($all=false) {
  if (!$all) {
    static $posts = null; // lazy initialization
    if ($posts !== null)
      return $posts; // if already loaded, return
  }
  if (!file_exists(NMPOSTCACHE))
    nm_update_cache();
  $data = @getXML(NMPOSTCACHE);
  $now = time();
  $posts = array();
  foreach ($data->item as $item) {
    if ($all || $item->private != 'Y' && strtotime($item->date) < $now)
      $posts[] = $item;
  }
  return $posts;
}


/*******************************************************
 * @function nm_get_archives
 * @return array with monthly or yearly archives (keys) and post slugs (values)
 * @param $by month ('m') or year ('y'), default 'm' for monthly archives
 * @param $tag (since 3.3) tag to filter by
 */
function nm_get_archives($by='m', $tag=null) {
  $archives = array();
  $datefmt = ($by == 'y') ? 'Y' : 'Ym';
  if ($tag) {
    $posts = nm_get_posts();
    foreach ($posts as $post) {
      if (in_array($tag, explode(',', nm_lowercase_tags(strip_decode($post->tags))))) {
        $archive = date($datefmt, strtotime($post->date));
        $archives[$archive][] = $post->slug;
      }
    }
  } else {
    $posts = nm_get_posts_default();
    foreach ($posts as $post) {
      $archive = date($datefmt, strtotime($post->date));
      $archives[$archive][] = $post->slug;
    }
  }
  return $archives;
}


/*******************************************************
 * @function nm_get_tags
 * @return array with unique tags (keys) and posts (values)
 */
function nm_get_tags() {
  $tags = array();
  $posts = nm_get_posts();
  foreach ($posts as $post) {
    if (!empty($post->tags)) {
      foreach (explode(',', nm_lowercase_tags(strip_decode($post->tags))) as $tag)
        $tags[trim($tag)][] = $post->slug;
    }
  }
  ksort($tags);
  return $tags;
}


/*******************************************************
 * @function nm_get_languages
 * @return array with language files in NMLANGPATH
 */
function nm_get_languages() {
  $languages = array();
  $files = getFiles(NMLANGPATH);
  foreach ($files as $file) {
    if (isFile($file, NMLANGPATH, 'php')) {
      $lang = basename($file, '.php');
      $languages[$lang] = NMLANGPATH . $file;
    }
  }
  ksort($languages);
  return $languages;
}


/*******************************************************
 * @function nm_get_date
 * @param $format strftime or date format
 * @param $timestamp UNIX timestamp
 * @return date formatted according to $NMLANG (if strftime)
 */
function nm_get_date($format, $timestamp) {
  if (strpos($format, '%') !== false) {

    # strftime format
    
    global $NMLANG, $i18n;
    static $win = null;
    if ($win === null)
      $win = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
    
    static $months = null;
    if ($months === null) {
      if (isset($i18n['news_manager/MONTHLIST'])) {
        $months = explode(',',$i18n['news_manager/MONTHLIST']);
        if (count($months) != 12) $months = false;
      } else {
          $months = false;
      }
    }
    static $months_alt = null;
    if ($months_alt === null) {
      if (isset($i18n['news_manager/MONTHLIST_ALT'])) {
        $months_alt = explode(',',$i18n['news_manager/MONTHLIST_ALT']);
        if (count($months_alt) != 12) $months_alt = false;
      } else {
          $months_alt = false;
      }
    }
    
    $alt = strpos($format, '%EB') !== false;
    if ($alt) {
      $format = str_replace('%EB', '%B', $format);
      $custom = !empty($months_alt);
    } else {
      $custom = !empty($months) && strpos($format, '%B') !== false;
    }
      
    $locale = setlocale(LC_TIME, 0);
    if (array_key_exists('news_manager/LOCALE', $i18n)) {
      setlocale(LC_TIME, preg_split('/\s*,\s*/', trim($i18n['news_manager/LOCALE']), -1, PREG_SPLIT_NO_EMPTY));
    } else {
      # no locale in language file
      $lg = substr($NMLANG,0,2);
      setlocale(LC_TIME, $NMLANG.'.utf8', $lg.'.utf8', $NMLANG.'.UTF-8', $lg.'.UTF-8', $NMLANG, $lg);
    }
    if ($win) {
      # fixes for Windows
      $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format); // strftime %e parameter not supported
      $date = utf8_encode(strftime($format, $timestamp)); // strftime returns ISO-8859-1 encoded string
    } else {
      $date = strftime($format, $timestamp);
    }
    
    if ($custom) {
      $m = intval(strftime('%m', $timestamp));
      if ($m > 0 && $m < 13) {
        $orig = $win ? utf8_encode(strftime('%B', $timestamp)) : strftime('%B', $timestamp);
      } else {
        $custom = false;
      }
    }

    setlocale(LC_TIME, $locale);
    
    if ($custom) {
      $replace = $alt ? $months_alt[$m-1] : $months[$m-1];
      $date = str_replace($orig, $replace, $date);
    }

  } else {

    # date() format
  
    global $TIMEZONE;
    if ($TIMEZONE != '') date_default_timezone_set($TIMEZONE);
    $date = date($format, $timestamp);

  }
  return $date;
}


/*******************************************************
 * @function nm_get_url
 * @return url of front-end newspage, with optional query
 */
function nm_get_url($query=false) {
  global $PRETTYURLS, $NMPAGEURL, $NMPRETTYURLS;
  $str = '';
  $parent = nm_get_parent();
  $url = find_url($NMPAGEURL, $parent);
  if (strpos($url, '%parents%/') !== false) // I18N plugin's placeholder for multilevel URLs
    $url = str_replace('%parents%/', (empty($parent) ? '' : $parent.'/'), $url);
  if ($query) {
    switch($query) {
      case 'post':
        $query = NMPARAMPOST;
        break;
      case 'page':
        $query = NMPARAMPAGE;
        break;
      case 'tag':
        $query = NMPARAMTAG;
        break;
      case 'archive':
        $query = NMPARAMARCHIVE;
        break;
    }
    if ($PRETTYURLS == 1 && $NMPRETTYURLS == 'Y') {
      if ($query == NMPARAMPOST && defined('NMNOPARAMPOST') && NMNOPARAMPOST)
        $str = '';
      else
        $str = $query . '/';
      if (substr($url, -1) != '/')
        $str = '/' . $str;
    } else {
      $str = (strpos($url,'?') === false)? '?' : '&amp;';
      $str .= $query.'=';
    }
  }
  return $url . $str;
}

/*******************************************************
 * @function nm_get_parent
 * @return front-end newspage's parent slug
 * @since 2.4.0
 */
function nm_get_parent() {
  global $NMPARENTURL, $NMPAGEURL;
  if ($NMPARENTURL == '?') // lazy load
    $NMPARENTURL = ($NMPAGEURL == '') ? '' : returnPageField($NMPAGEURL, 'parent');
  return $NMPARENTURL;
}

/*******************************************************
 * @function nm_get_image_url
 * @param $pic image URL, full or relative to data/uploads/
 * @return absolute URL of thumbnail/image as defined by $nmoption settings / registers allowed sizes since 3.2
 * @since 3.0
 */
function nm_get_image_url($pic, $width=null, $height=null, $crop=null, $default=null) {
  global $SITEURL, $nmoption, $nmimagesizes;
  if (!$nmimagesizes)
    $nmimagesizes = array();
  $url = '';
  if (empty($pic)) {
    if ($default)
      $pic = $default;
    else
      if ($default !== '' && $nmoption['imagedefault'])
        $pic = $nmoption['imagedefault'];
  }
  if (!empty($pic)) {
    if (!isset($width)) $width = $nmoption['imagewidth'];
    if (!isset($height)) $height = $nmoption['imageheight'];
    if (!isset($crop)) $crop = $nmoption['imagecrop'];
    $pos = strpos($pic, '/data/uploads/');
    $uploads = ($pos !== false || !strpos($pic, '://'));
    if ($uploads || strpos($pic, '/data/thumbs/') !== false) {
      if ($pos !== false)
        $pic = substr($pic, $pos+14);
      $w = $width ? '&w='.$width : '';
      $h = $height ? '&h='.$height : '';
      if ($w == '' && $h == '' && $uploads) {
        $url = $SITEURL.'data/uploads/'.$pic;
      } else {
        $c = $crop ? '&c=1' : '';
        $gt = $nmoption['imagethumbnail'] ? '&gt=1' : '';
        $url = $SITEURL.'plugins/news_manager/browser/pic.php?p='.$pic.$w.$h.$c.$gt;
        # register image size for pic.php - since 3.2
        $size = array('w' => $width, 'h' => $height, 'c' => $crop);
        if (!in_array($size, $nmimagesizes)) {
          $nmimagesizes[] = $size;
          $file = NMDATAPATH.'images.'.($crop && $width && $height ? 'C' : '').($width ? $width.'x' : '0x').($height ? $height : '0').'.txt';
          if (!file_exists($file)) {
            file_put_contents($file, '');
          }
        }
      }
    } else {
      if ($nmoption['imageexternal'])
        $url = $pic;
    }
  }
  return $url;
}

/*******************************************************
 * @function nm_create_dir
 * @param $path full path of the directory
 * @action create the directory $path
 */
function nm_create_dir($path) {
  if (mkdir($path, 0777)) {
    $fh = fopen($path . '.htaccess', 'w');
    fwrite($fh, 'Deny from all');
    fclose($fh);
    return true;
  }
  return false;
}

/*******************************************************
 * @function nm_rename_file
 * @since 2.3.2
 * @param $oldfile origin file
 * @param $newfile destination file
 * @action rename or move a file - like rename() but safer (Windows)
 * @link http://www.php.net/manual/en/function.rename.php#56576
 */
function nm_rename_file($oldfile,$newfile) {
  if (!rename($oldfile,$newfile)) {
    if (copy ($oldfile,$newfile)) {
      unlink($oldfile);
      return TRUE;
    }
    return FALSE;
  }
  return TRUE;
}

/*******************************************************
 * @function nm_create_slug
 * @param $str string
 * @return a url friendly version of $str
 */
function nm_create_slug($str) {
  global $i18n;
  $str = trim($str);
  if (isset($i18n['TRANSLITERATION']) && is_array($translit=$i18n['TRANSLITERATION']) && count($translit>0)) {
    $str = str_replace(array_keys($translit),array_values($translit),$str);
  }
  $str = to7bit($str, 'UTF-8');
  $str = clean_url($str);
  return $str;
}


/*******************************************************
 * @function nm_create_excerpt
 * @param $content the post content
 * @param $url if not FALSE, URL to add "read more" link
 * @param $forcereadmore always add "read more" link, even if not truncated
 * @return a truncated version of the post content
 */
function nm_create_excerpt($content, $url=false, $forcereadmore=false) {
  global $NMEXCERPTLENGTH;
  $len = intval($NMEXCERPTLENGTH);
  if ($len == 0) {
    return '';
  } else {
    $ellipsis = i18n_r('news_manager/ELLIPSIS');
    $break = nm_get_option('breakwords');
    $class = nm_get_option('classreadmorelink');
    $class = $class ? ' class="'.$class.'"' : '';
    if ($url) {
      $readmorehtml = '<span class="'.nm_get_option('classreadmore','nm_readmore').'"><a'.$class.' href="'.$url.'">'.i18n_r('news_manager/READ_MORE').'</a></span>';
      if ($forcereadmore)
        $content = nm_make_excerpt($content, $len, $ellipsis, $break).' '.$readmorehtml;
      else
        $content = nm_make_excerpt($content, $len, $ellipsis.$readmorehtml, $break);
    } else {
      $content = nm_make_excerpt($content, $len, $ellipsis, $break);
    }
    return '<p>'.$content.'</p>';
  }
}


/*******************************************************
 * @function nm_make_excerpt
 * @param $content source string (html/text)
 * @param $len maximum excerpt length
 * @param $ellipsis optional string to be appended at the end (e.g. '...')
 * @param $break allow cutting off last word
 * @return excerpt without html tags and usual GS placeholders
 * @since 3.0
 */
function nm_make_excerpt($content, $len=200, $ellipsis='', $break=false) {
  $content = preg_replace('/\(%.*?%\)/', '', $content); // remove (% ... %)
  $content = preg_replace('/\{%.*?%\}/', '', $content); // remove {% ... %}
  
  # Remove HTML tags, including invisible text such as style and
  # script code, and embedded objects.  Add line breaks around
  # block-level tags to prevent word joining after tag removal.
  # http://nadeausoftware.com/articles/2007/09/php_tip_how_strip_html_tags_web_page
  $content = preg_replace(
    array(
      // Remove invisible content
      '@<head[^>]*?>.*?</head>@siu',
      '@<style[^>]*?>.*?</style>@siu',
      '@<script[^>]*?.*?</script>@siu',
      '@<object[^>]*?.*?</object>@siu',
      '@<embed[^>]*?.*?</embed>@siu',
      '@<applet[^>]*?.*?</applet>@siu',
      '@<noframes[^>]*?.*?</noframes>@siu',
      '@<noscript[^>]*?.*?</noscript>@siu',
      '@<noembed[^>]*?.*?</noembed>@siu',
      // Add line breaks before and after blocks
      '@</?((address)|(blockquote)|(center)|(del))@iu',
      '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
      '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
      '@</?((table)|(th)|(td)|(caption))@iu',
      '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
      '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
      '@</?((frameset)|(frame)|(iframe))@iu',
    ),
    array(
      ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
      "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
      "\n\$0", "\n\$0",
    ), $content);

  $content = strip_tags($content);
  $content = preg_replace('/\s+/u', ' ', str_replace('&nbsp;', ' ', $content)); // remove whitespace
  if (function_exists('mb_strlen')) {
    if (mb_strlen($content, 'UTF-8') > $len) {
      if ($break)
        $content = mb_substr($content, 0, $len, 'UTF-8');
      else
        $content = mb_substr($content, 0, mb_strrpos(mb_substr($content, 0, $len+1, 'UTF-8'), ' ', 'UTF-8'), 'UTF-8');
      $content .= $ellipsis;
    }
  } else {
    if (strlen($content) > $len) {
      if ($break)
        $content = substr($content, 0, $len);
      else
        $content = substr($content, 0, strrpos(substr($content, 0, $len+1), ' '));
      $content .= $ellipsis;
    }
  }
  return $content;
}

/*******************************************************
 * @function nm_i18n_merge
 * @action update the $i18n language array
 * @param $backend (since 3.3) if true, use current GS language - else use NM front-end language
 */
function nm_i18n_merge($backend = false) {
  if ($backend) {
    global $LANG;
    if (file_exists(NMLANGPATH.$LANG.'.php')) {
      $lang = $LANG;
    } else {
      $files = glob(NMLANGPATH.substr($LANG,0,2).'*.php');
      $fallback = reset($files);
      $lang = $fallback ? basename($fallback, '.php') : 'en_US';
    }
  } else {
    # frontend
    global $NMLANG;
    $lang = $NMLANG ? $NMLANG : null;
  }
  if ($lang && file_exists(NMLANGPATH.$lang.'.php')) {
    if (dirname(realpath(NMLANGPATH.$lang.'.php')) != realpath(NMLANGPATH)) die(''); // path traversal
    include(NMLANGPATH.$lang.'.php');
  } else {
    $i18n = array();
  }
  global $nm_i18n;
  if ($nm_i18n) {
    $nm_i18n = array_merge($i18n, $nm_i18n);
  } else {
    $nm_i18n = $i18n;
  }
  # add to global $i18n array
  global $i18n;
  foreach ($nm_i18n as $code=>$text)
    $i18n['news_manager/'.$code] = $text;
}


/*******************************************************
 * @function nm_header_include
 * @action insert necessary script/style sections into site header
 */
function nm_header_include() {
  if (isset($_GET['id']) && $_GET['id'] == 'news_manager' && (isset($_GET['edit']) || isset($_GET['settings']))) {
  ?>
  <style>
    .invalid {
      color: #D94136;
      font-size: 11px;
      font-weight: normal;
    }
  </style>
  <?php
  }
}


/*******************************************************
 * @function nm_display_message
 * @param $msg a string containing the message
 * @param $error if true, show as $msg as error, else as update
 * @param $backup when set, include undo link
 * @action display status messages on back-end pages
 */
function nm_display_message($msg, $error=false, $backup=null) {
  if (isset($msg)) {
    if (isset($backup))
      $msg .= ' <a href="load.php?id=news_manager&amp;restore='.$backup.'">'.i18n_r('UNDO').'</a>';
    ?>
    <script type="text/javascript">
      $(function() {
        $('div.bodycontent').before('<div class="<?php echo $error ? 'error' : 'updated'; ?>" style="display:block;">'+
          <?php echo json_encode($msg); ?>+'</div>');
        $(".updated, .error").fadeOut(500).fadeIn(500);
      });
    </script>
    <noscript>
      <div class="<?php echo $error ? 'error' : 'updated'; ?>" style="display:block;"><?php echo $msg; ?></div>
    </noscript>
    <?php
  }
}


/*******************************************************
 * @function nm_lowercase_tags
 * @param $str a string containing post tags
 * @action convert string to lowercase if "lowercasetags" enabled
 * @since 3.0
 */
function nm_lowercase_tags($str) {
  if (defined('NMLOWERCASETAGS') && NMLOWERCASETAGS)
    return lowercase($str);
  else
    return $str;
}

/*******************************************************
 * @function nm_generate_sitemap
 * @action trigger Sitemap update only if GetSimple 3.3+
 * @since 2.5
 */
function nm_generate_sitemap() {
  if (version_compare(GSVERSION, '3.3', '>=') && (!defined('NMNOSITEMAP') || !NMNOSITEMAP))
    generate_sitemap();
}

/*******************************************************
 * @function nm_update_sitemap_xml
 * @action add posts (and tag pages) to sitemap.xml, GetSimple 3.3+
 * @param xmlobj
 * @since 2.5
 */
function nm_update_sitemap_xml($xml) {
  if (!defined('NMNOSITEMAP') || !NMNOSITEMAP) {
    $posts = nm_get_posts();
    $tags = array();
    $excludetags = (defined('NMSITEMAPEXCLUDETAGS') && (NMSITEMAPEXCLUDETAGS === true || NMSITEMAPEXCLUDETAGS === 1));
    foreach ($posts as $post) {
      $url = nm_get_url('post').$post->slug;
      $file = NMPOSTPATH.$post->slug.'.xml';
      $date = makeIso8601TimeStamp(date('Y-m-d H:i:s', strtotime($post->date)));
      $item = $xml->addChild('url');
      $item->addChild('loc', $url);
      $item->addChild('lastmod', $date);
      $item->addChild('changefreq', 'monthly');
      $item->addChild('priority', '0.5');
      if (!$excludetags && !empty($post->tags)) {
        foreach (explode(',', nm_lowercase_tags(strip_decode($post->tags))) as $tag) {
          if (substr($tag, 0, 1) != '_') {
            if (!in_array($tag, $tags)) {
              $url = nm_get_url('tag').rawurlencode($tag);
              $item = $xml->addChild('url');
              $item->addChild('loc', $url);
              $item->addChild('lastmod', $date);
              $item->addChild('changefreq', 'monthly');
              $item->addChild('priority', '0.5');
              $tags[] = $tag;
            }
          }
        }
      }
    }
  }
  return $xml;
}

/*******************************************************
 * @function nm_update_extend_cache
 * @action hack to replace Extend API cache file
 * @uses $site_link_back_url (GetSimple's configuration.php)
 * @since 3.0
 */
function nm_update_extend_cache() {
  if (!is_frontend() && get_filename_id() == 'plugins') {
    include(GSADMININCPATH.'configuration.php');
    $url = $site_link_back_url.'api/extend/?id=541';
    $tempfile = GSCACHEPATH.md5($url).'.txt';
    $cachefile = GSCACHEPATH.md5($site_link_back_url.'api/extend/?file=news_manager.php').'.txt';
    get_api_details('custom', $url);
    if (file_exists($cachefile)) unlink($cachefile);
    @copy($tempfile, $cachefile);
  }
}

# since 3.0
# for templateFile custom setting (function may be renamed later)
function nm_switch_template_file($tempfile) {
  global $template_file, $TEMPLATE;
  # no path traversal and template exists
  if (strpos(realpath(GSTHEMESPATH.$TEMPLATE."/".$tempfile), realpath(GSTHEMESPATH.$TEMPLATE."/")) === 0 && file_exists(GSTHEMESPATH.$TEMPLATE."/".$tempfile))
    $template_file = $tempfile;
}

# since 3.1
# fix URLs with special permalink placeholders for the I18N plugin - backend only
function nm_patch_i18n_url($url) {
  return str_replace(array('%language%/','%nondefaultlanguage%/'), array('',''), $url);
}

## since 3.2

function nm_post_files_differ(&$posts) {
  $slugs = $files = array();
  foreach ($posts as $post)
    $slugs[] = (string)$post->slug;
  foreach (glob(NMPOSTPATH.'*.xml') as $file)
    $files[] = basename($file, '.xml');
  return (count($files) != count($slugs) || count(array_diff($slugs, $files)) > 0);
}

## since 3.3

function nm_add_mu_permissions() {
  if (function_exists('add_mu_permission')) {
    if (defined('NMMULTIUSERCUSTOMPERMS') && NMMULTIUSERCUSTOMPERMS) {
      add_mu_permission('news_manager_settings',i18n_r('news_manager/NM_SETTINGS'));
    }
  }
}

function nm_allow_settings() {
  global $USR;
  if (function_exists('check_user_permission')) {
    if (defined('NMMULTIUSERCUSTOMPERMS') && NMMULTIUSERCUSTOMPERMS) {
      return check_user_permission($USR, 'news_manager_settings');
    } else { // workaround for MU 1.8.x bug
      return check_user_permission($USR, 'THEME') || check_user_permission($USR, 'PLUGINS');
    }
  } else {
    return true;
  }
}

// patch for Multi User 1.8.2
function nm_update_mu_landing_dropdown() {
  if (function_exists('mm_admin'))
    if (basename($_SERVER['PHP_SELF']) == 'load.php' && isset($_GET['id']) && $_GET['id'] == 'user-managment') {
      ?>
      <script>
      $('select[id="userland"]').append($('<option>', {
         text: '<?php i18n('news_manager/PLUGIN_NAME'); ?>',
         value: 'load.php?id=news_manager'
      }));
      </script>
      <?php
  }
}

# since 3.4

# returns array excluding posts having certain tags
# - to be used in non-tag pages -main, archives- and some sidebar functions
# - temporary solution, will eventually be replaced/refactored
function nm_get_posts_default() {
  if (!defined('NMDEFAULTEXCLUDETAGGED')) {
    return nm_get_posts();
  } else {
    static $posts = null; // lazy init...
    if ($posts === null) {
      $posts = nm_get_posts();
      $tags = nm_lowercase_tags(NMDEFAULTEXCLUDETAGGED);
      $tags = explode(',', trim(preg_replace(array('/\s+/','/\s*,\s*/','/,+/'),array(' ',',',','), $tags), ' ,'));
      foreach ($posts as $k => $post)
        foreach (explode(',', nm_lowercase_tags(strip_decode($post->tags))) as $tag)
          if (in_array($tag, $tags)) {
            unset($posts[$k]);
            break;
          }

    }
    return $posts;
  }
}


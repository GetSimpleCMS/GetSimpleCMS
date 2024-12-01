<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager configuration functions.
 */


/*******************************************************
 * @function nm_env_check
 * @action check plugin environment
 */
function nm_env_check() {
  $env_ok = file_exists(NMPOSTPATH) || nm_create_dir(NMPOSTPATH);
  if ($env_ok && !file_exists(NMBACKUPPATH))
    $env_ok = nm_create_dir(NMBACKUPPATH);
  if ($env_ok && !file_exists(NMDATAPATH)) {
    if ($env_ok = nm_create_dir(NMDATAPATH))
      nm_update_cache();
  }
  if (!$env_ok)
    echo '<h3>News Manager</h3><p>' . i18n_r('news_manager/ERROR_ENV') . '</p>';
  return $env_ok;
}


/*******************************************************
 * @function nm_edit_settings
 * @action edit plugin configuration settings
 */
function nm_edit_settings() {
  global $PRETTYURLS, $PERMALINK, $NMPAGEURL, $NMPRETTYURLS, $NMLANG, $NMSHOWEXCERPT,
         $NMEXCERPTLENGTH, $NMPOSTSPERPAGE, $NMRECENTPOSTS, $NMSETTING;
  if (nm_allow_settings()) {
    if (defined('GSCANONICAL') && GSCANONICAL)
      nm_display_message('<b>Warning:</b> This plugin may not work properly if GSCANONICAL is enabled (gsconfig.php)', true); // not translated
    include(NMTEMPLATEPATH . 'edit_settings.php');
  } else {
    echo 'Access not allowed!';
  }
}


/*******************************************************
 * @function nm_save_settings
 * @action parse POST data and save to config file
 */
function nm_save_settings() {
  global $NMPAGEURL, $NMPRETTYURLS, $NMLANG, $NMSHOWEXCERPT, $NMEXCERPTLENGTH,
         $NMPOSTSPERPAGE, $NMRECENTPOSTS, $NMSETTING;
  if (!nm_allow_settings()) die('Forbidden!');
  $backup = array('page_url' => $NMPAGEURL, 'pretty_urls' => $NMPRETTYURLS);
  # parse $_POST
  $NMPAGEURL       = $_POST['page-url'];
  $NMPRETTYURLS    = isset($_POST['pretty-urls']) ? 'Y' : '';
  $NMLANG          = $_POST['language'];
  $NMSHOWEXCERPT   = $_POST['show-excerpt'] ? 'Y' : '';
  $NMEXCERPTLENGTH = intval($_POST['excerpt-length']);
  $NMPOSTSPERPAGE  = intval($_POST['posts-per-page']);
  $NMRECENTPOSTS   = intval($_POST['recent-posts']);
  # new settings since 3.0
  $NMSETTING = array();
  $NMSETTING['archivesby'] = $_POST['archivesby'];
  $NMSETTING['readmore'] = $_POST['readmore'];
  $NMSETTING['titlelink'] = $_POST['titlelink'];
  $NMSETTING['gobacklink'] = $_POST['gobacklink'];
  $NMSETTING['images'] = $_POST['images'];
  $NMSETTING['imagewidth'] = $_POST['imagewidth'];
  $NMSETTING['imageheight'] = $_POST['imageheight'];
  $NMSETTING['imagecrop'] = isset($_POST['imagecrop']);
  $NMSETTING['imagealt'] = isset($_POST['imagealt']);
  $NMSETTING['imagelink'] = isset($_POST['imagelink']);
  $NMSETTING['enablecustomsettings'] = isset($_POST['enablecustomsettings']);
  $NMSETTING['customsettings'] = get_magic_quotes_gpc()==0 ? $_POST['customsettings'] : stripslashes($_POST['customsettings']);
  # write settings to file
  if (nm_settings_to_xml()) {
    nm_generate_sitemap();
    nm_display_message(i18n_r('news_manager/SUCCESS_SAVE'));
  } else {
    nm_display_message(i18n_r('news_manager/ERROR_SAVE'), true);
  }
  # should we update .htaccess?
  if ($NMPRETTYURLS == 'Y') {
    if ($backup['pretty_urls'] != 'Y' || $backup['page_url'] != $NMPAGEURL)
      nm_display_message(sprintf(i18n_r('news_manager/UPDATE_HTACCESS'), 'load.php?id=news_manager&amp;htaccess'), true);
  }
  # clear registered image sizes for pic.php - since 3.2
  foreach (glob(NMDATAPATH.'images.*.txt') as $file) unlink ($file);
}


/*******************************************************
 * @function nm_settings_to_xml
 * @action write plugin settings to config file
 */
function nm_settings_to_xml() {
  global $NMPAGEURL, $NMPRETTYURLS, $NMLANG, $NMSHOWEXCERPT, $NMEXCERPTLENGTH,
         $NMPOSTSPERPAGE, $NMRECENTPOSTS, $NMSETTING;
  $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
  $obj = $xml->addChild('page_url', $NMPAGEURL);
  $obj = $xml->addChild('pretty_urls', $NMPRETTYURLS);
  $obj = $xml->addChild('language', $NMLANG);
  $obj = $xml->addChild('show_excerpt', $NMSHOWEXCERPT);
  $obj = $xml->addChild('excerpt_length', $NMEXCERPTLENGTH);
  $obj = $xml->addChild('posts_per_page', $NMPOSTSPERPAGE);
  $obj = $xml->addChild('recent_posts', $NMRECENTPOSTS);
  $obj = $xml->addChild('archivesby', $NMSETTING['archivesby']);
  $obj = $xml->addChild('readmore', $NMSETTING['readmore']);
  $obj = $xml->addChild('titlelink', $NMSETTING['titlelink']);
  $obj = $xml->addChild('gobacklink', $NMSETTING['gobacklink']);
  $obj = $xml->addChild('images', $NMSETTING['images']);
  $obj = $xml->addChild('imagewidth', $NMSETTING['imagewidth']);
  $obj = $xml->addChild('imageheight', $NMSETTING['imageheight']);
  $obj = $xml->addChild('imagecrop', $NMSETTING['imagecrop']);
  $obj = $xml->addChild('imagealt', $NMSETTING['imagealt']);
  $obj = $xml->addChild('imagelink', $NMSETTING['imagelink']);
  $obj = $xml->addChild('enablecustomsettings', $NMSETTING['enablecustomsettings']);
  $obj = $xml->addChild('customsettings');
  $obj->addCData($NMSETTING['customsettings']);
  $obj = $xml->addChild('ver', NMVERSION);
  return @XMLsave($xml, NMSETTINGS);
}


/*******************************************************
 * @function nm_generate_htaccess
 * @action generate a .htaccess sample config based on current settings
 */
function nm_generate_htaccess() {
  global $NMPAGEURL, $PERMALINK;
  $path = tsl(suggest_site_path(true));
  $prefix = '';
  $page =  '';
  # format prefix and page directions
  if ($NMPAGEURL != 'index') {
    if ( nm_get_parent() != '' && ($PERMALINK == '' || strpos($PERMALINK,'%parent%') !== false || strpos($PERMALINK,'%parents%') !== false) ) {
      $prefix = nm_get_parent().'/'.$NMPAGEURL.'/';
    } else {
      $prefix = $NMPAGEURL.'/';
    }
    $page = 'id='.$NMPAGEURL.'&';
  }
  # generate .htaccess contents
  $htaccess = file_get_contents(GSPLUGINPATH . 'news_manager/temp.htaccess');
  $htaccess = str_replace('**PATH**', $path, $htaccess);
  $htaccess = str_replace('**PREFIX**', $prefix, $htaccess);
  $htaccess = str_replace('**PAGE**', $page, $htaccess);
  $htaccess = htmlentities($htaccess, ENT_QUOTES, 'UTF-8');
  # show .htaccess
  include(NMTEMPLATEPATH . 'htaccess.php');
}


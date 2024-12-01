<?php
/*
Plugin Name: I18N Gallery
Description: Display image galleries (I18N enabled)
Version: 2.2.1
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

define('I18N_GALLERY_DIR', 'i18n_gallery/'); 
define('I18N_GALLERY_DEFAULT_TYPE', 'prettyphoto');
define('I18N_GALLERY_DEFAULT_THUMB_WIDTH', 160);
define('I18N_GALLERY_DEFAULT_THUMB_HEIGHT', 120);

# register plugin
register_plugin(
	$thisfile, 
	'I18N Gallery', 	
	'2.2.1', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Display image galleries (I18N enabled)',
	'i18n_gallery',
	'i18n_gallery_main'  
);

# load i18n texts
if (basename($_SERVER['PHP_SELF']) != 'index.php') { // back end only
  i18n_merge('i18n_gallery', substr($LANG,0,2));
  i18n_merge('i18n_gallery', 'en');
}

$i18n_gallery_on_page = null;
$i18n_gallery_pic_used = false;

# activate filter
add_action('header','i18n_gallery_header');
add_action('nav-tab', 'createNavTab', array('i18n_gallery', $thisfile, i18n_r('i18n_gallery/TAB'), 'overview'));
add_action('i18n_gallery-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_gallery/GALLERIES'), 'overview'));
add_action('i18n_gallery-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_gallery/CREATE_GALLERY'), 'create'));
add_action('i18n_gallery-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_gallery/EDIT_GALLERY'), 'edit', false));
add_action('i18n_gallery-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_gallery/SETTINGS'), 'configure'));

add_action('index-pretemplate','i18n_gallery_preview');
add_action('theme-header','i18n_gallery_theme_header');
add_filter('content','i18n_gallery_content');
add_filter('search-index-page', 'i18n_gallery_index');

function i18n_gallery_is_frontend() {
  return function_exists('get_site_url');
}

# ===== GENERAL FUNCTIONS =====

function i18n_gallery_register($type, $name, $description, $edit_function, $header_function, $content_function) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  return I18nGallery::registerPlugin($type, $name, $description, $edit_function, $header_function, $content_function);
}

function i18n_gallery_plugins() {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  return I18nGallery::getPlugins();
}

function i18n_gallery_settings($reload=false) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  return I18nGallery::getSettings();
}

function return_i18n_gallery($name) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  return I18nGallery::getGallery($name);
}


# ===== BACKEND HOOKS =====

function i18n_gallery_header() {
  include(GSPLUGINPATH.'i18n_gallery/header.php');
}


# ===== BACKEND PAGES =====

function i18n_gallery_main() {
  if (isset($_GET['overview'])) {
    include(GSPLUGINPATH.'i18n_gallery/overview.php');
  } else if (isset($_GET['create'])) {
    include(GSPLUGINPATH.'i18n_gallery/edit.php');
  } else if (isset($_GET['edit'])) {
    include(GSPLUGINPATH.'i18n_gallery/edit.php');
  } else if (isset($_GET['configure'])) {
    include(GSPLUGINPATH.'i18n_gallery/configure.php');
  }
}


# ===== FRONTEND HOOKS =====

function i18n_gallery_preview() {
  global $content;
  if (isset($_GET['preview-gallery'])) {
    if (function_exists('i18n_init')) i18n_init();
    $content = htmlspecialchars("<p>(% gallery %)</p>");
  }
}

function i18n_gallery_theme_header() {
  global $content;
  get_i18n_gallery_header_from_content(strip_decode($content));
}

function i18n_gallery_content($content) {
  global $i18n_gallery_on_page;
  $content = preg_replace_callback("/\(%\s*(gallerylink)(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", 'i18n_gallery_replace_link',$content);
  if (!$i18n_gallery_on_page) return $content;
  return preg_replace_callback("/(<p>\s*)?\(%\s*(gallery)(\s+(?:%[^%\)]|[^%])+)?\s*%\)(\s*<\/p>)?/", 'i18n_gallery_replace_gallery',$content);
}

function i18n_gallery_replace_gallery($match) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  require_once(GSPLUGINPATH.'i18n_gallery/frontend.class.php');
  $replacement = '';
  if (@$match[1] && (!isset($match[4]) || !$match[4])) $replacement .= $match[1];
  $gallery = I18nGallery::getGalleryFromParamString(@$match[3]);
  ob_start();
  if ($match[2] == 'gallery' && $gallery) {
    I18nGalleryFrontend::outputGallery($gallery);
  }
  $replacement .= ob_get_contents();
  ob_end_clean();
  if (!@$match[1] && isset($match[4]) && $match[4]) $replacement .= $match[4];
  return $replacement;
}

function i18n_gallery_replace_link($match) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  require_once(GSPLUGINPATH.'i18n_gallery/frontend.class.php');
  $replacement = '';
  $gallery = I18nGallery::getGalleryFromParamString(@$match[2]);
  ob_start();
  if ($match[1] == 'gallerylink' && $gallery) {
    I18nGalleryFrontend::outputLink($gallery);
  }
  $replacement .= ob_get_contents();
  ob_end_clean();
  return $replacement;
}


# ===== FRONTEND FUNCTIONS =====

function get_i18n_gallery_link($name, $params = null) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  require_once(GSPLUGINPATH.'i18n_gallery/frontend.class.php');
  if (is_array($name)) $params = $name; 
  else if (!@$params) $params = array('name' => $name);
  else $params['name'] = $name;
  $gallery = I18nGallery::getGalleryFromParams($params, true);
  I18nGalleryFrontend::outputLink($gallery);
}

function get_i18n_gallery_header($name, $params = null) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
  if (is_array($name)) $params = $name; 
  else if (!@$params) $params = array('name' => $name);
  else $params['name'] = $name;
  $gallery = I18nGallery::getGalleryFromParams($params, true);
  $plugins = I18nGallery::getPlugins();
  $plugin = @$plugins[$gallery['type']];
  if ($plugin) call_user_func_array($plugin['header'], array($gallery));
}

function get_i18n_gallery_header_from_content($content) {
  global $i18n_gallery_on_page;
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  if (preg_match_all("/\(%\s*(gallery)(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", $content, $matches)) {
    $plugins = I18nGallery::getPlugins();
    foreach ($matches[2] as $paramstr) {
      $gallery = I18nGallery::getGalleryFromParamString($paramstr);
      $plugin = @$plugins[$gallery['type']];
      if ($plugin) {
        include_once(GSPLUGINPATH.'i18n_gallery/helper.php');
        call_user_func_array($plugin['header'], array($gallery));
      }
    }
    $i18n_gallery_on_page = true;
  }
}

function get_i18n_gallery($name, $params = null) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  if (is_array($name)) $params = $name; 
  else if (!@$params) $params = array('name' => $name);
  else $params['name'] = $name;
  $gallery = I18nGallery::getGalleryFromParams($params);
  if ($gallery) {
    require_once(GSPLUGINPATH.'i18n_gallery/frontend.class.php');
    I18nGalleryFrontend::outputGallery($gallery);
  }
}

// indexing content for I18N Search plugin. $item is of type I18nSearchPageItem.
function i18n_gallery_index($item) {
  require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
  return I18nGallery::index($item);
}

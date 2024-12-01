<?php
/*
Plugin Name: I18N Base
Description: Internationalization based on slug/URL names (e.g. index, index_de, index_fr)
Version: 3.3.1
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

Public functions:
  return_i18n_default_language()
      returns the default language - the language of pages without language suffix
  return_i18n_languages()
      returns an array of user requested languages with the best first, e.g. ( 'de', 'fr', 'en' )
  return_i18n_available_languages($slug=null)
      returns the available languages for the site or page (if slug is not empty)
  return_i18n_page_data($slug)
      returns the xml data for the best fitting language version of the $slug
  return_i18n_lang_url($language=null)
      returns the URL to the current page in the given $language (if null, the default language is used)
      (you should use htmlspecialchars when outputting in a href to convert & to &amp;)
  return_i18n_setlang_url($language)
      returns the URL to the current page which also sets the preferred $language. If the current URL does not have a 
      parameter lang then this causes the page to be displayed in the given $language (if it exists).
      (you should use htmlspecialchars when outputting in a href to convert & to &amp;)
  get_i18n_page_url($echo=false)
      like get_page_url, but I18N enabled - ATTENTION: $echo=false WILL echo!!!
  find_i18n_url($slug,$parent,$language,$type='full')
      returns the URL to the page identified by $slug/$parent in the given $language (see also core function find_url)
      (you should use htmlspecialchars when outputting in a href to convert & to &amp;)
  return_i18n_component($slug)
      returns the component content (unprocessed)

The current language is available in the global variable $language.

Display functions:
  get_i18n_header()
      like get_header, but tags beginning with _ are ignored and the language is appended to the canonical URL
  get_i18n_content($slug)
      outputs the best fitting language content of the $slug. Returns true, if content found.
  get_i18n_component($id, $param1, ...)
      outputs the (localized) component. Returns true, if component found.
      Optionally parameters can be passed. They are available in the component as global array $args.
  get_i18n_link($slug)
      outputs a link to the given page in the best language

Functions to call for other plugins:
  i18n_init()
      loads the correct language version for the current page

Ignore user language:
      if you want to ignore the language(s) the user has set in his browser, add the following to gsconfig.php:
        define('I18N_IGN2.9ORE_USER_LANGUAGE',true);
      if you don't want to display the multi-language comment and default page on the pages view
      and you really only have one language, add the following to gsconfig.php:
        define('I18N_SINGLE_LANGUAGE', true);
                  
Fancy URLs:
      You can include a placeholder %language% in the fancy URL - then the language will be always included
      in the URL, e.g. %language%/%parent%/%slug%/ --> en/products/notebook1/
      You can also define a constant I18N_SEPARATOR in gsconfig.php, e.g. ':', which will result in URLs like
      products/notebook1:en/. The language will only be shown, if specifically requested.
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");
$i18n_initialized = false;
$i18n_languages = null;
$i18n_settings = null;

define('I18N_DEFAULT_LANGUAGE', 'en');
define('I18N_SETTINGS_FILE', 'i18n_settings.xml');
define('I18N_LANGUAGE_PARAM', 'lang');            # language parameter in URL, e.g. "...?lang=de"
define('I18N_SET_LANGUAGE_PARAM', 'setlang');     # parameter to set current language via GET/POST, e.g. "...?setlang=de"
define('I18N_LANGUAGE_COOKIE', 'language');       # cookie set, if the user selects a language with the set language param

// properties
define('I18N_PROP_DEFAULT_LANGUAGE', 'default_language');
define('I18N_PROP_URLS_TO_IGNORE', 'urls-to-ignore');
define('I18N_PROP_PAGES_VIEW', 'pages-view');
define('I18N_PROP_PAGES_SORT', 'pages-sort');

$i18n_base_tab = 'pages';
if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_base') {
  $i18n_base_tab = isset($_GET['sitemap']) ? 'theme' : 'pages';
}

i18n_load_texts('i18n_base');

# register plugin
register_plugin(
	$thisfile, 
	'I18N Base', 	
	'3.3.1', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	i18n_r('i18n_base/PLUGIN_DESCRIPTION'),
	$i18n_base_tab,
	'i18n_main'  
);

# activate filter
add_action('index-pretemplate', 'i18n_init');
add_action('edit-extras', 'i18n_base_edit'); 
add_action('pages-sidebar', 'i18n_base_sidebar_item', array($thisfile, i18n_r('i18n_base/PAGES')));
add_action('admin-pre-header', 'i18n_base_admin_pre_header'); // 3.1+ only
add_action('header', 'i18n_base_admin_header'); // 3.0+

# always add sidebar action - for adminbar plugin
add_action('theme-sidebar', 'i18n_base_sidebar_item', array($thisfile, i18n_r('SIDE_GEN_SITEMAP'), 'sitemap'));

if (function_exists('generate_sitemap')) {
  # patches for sitemap generation (GetSimple 3.1, 3.2)
  # also use this for GetSimple 3.3, as just using hook sitemap-aftersave would generate the sitemap twice - slow
  add_action('changedata-aftersave', 'i18n_base_patch_page_save');
  add_action('page-delete', 'i18n_base_patch_page_delete');
  // the non-i18n sitemap is also generated when you restore a backup page - no remedy for this in 3.1, 3.2
  if (!function_exists('var_out')) {
    // only for GetSimple 3.1, 3.2
    add_action('settings-website-extras', 'i18n_base_patch_settings');
  }
}
add_action('sitemap-aftersave', 'i18n_base_sitemap_aftersave'); // GetSimple 3.3+

# ===== BACKEND HOOKS =====

function i18n_base_sidebar_item($id, $txt, $action=null, $always=true) {
  $is31 = function_exists('generate_sitemap'); # GetSimple 3.1+
  if ($is31) { 
    createSideMenu($id, $txt, $action, $always);
  } else {
    $current = false;
    if (isset($_GET['id']) && $_GET['id'] == $id && (!$action || isset($_GET[$action]))) {
      $current = true;
    }
    if ($always || $current) {
      echo '<li><a href="load.php?id='.$id.($action ? '&amp;'.$action : '').'" '.($current ? 'class="current"' : '').' >'.$txt.'</a></li>';
    }
  }
}

function i18n_base_admin_pre_header() {
  require_once(GSPLUGINPATH.'i18n_base/backend.class.php');
  I18nBackend::processPreHeader();
}

function i18n_base_admin_header() {
  require_once(GSPLUGINPATH.'i18n_base/backend.class.php');
  I18nBackend::processHeader();
}

function i18n_base_edit() {
  include(GSPLUGINPATH.'i18n_base/editextras.php');
}

function i18n_base_sitemap_aftersave() {
  require_once(GSPLUGINPATH.'i18n_base/sitemap.class.php');
  I18nSitemap::generateSitemapWithoutPing();
}

function i18n_base_patch_page_save() {
  require_once(GSPLUGINPATH.'i18n_base/sitemap.class.php');
  I18nSitemap::executeOtherFunctions('changedata-aftersave', 'i18n_base_patch_page_save');
  I18nSitemap::patchSaveFile();
}

function i18n_base_patch_page_delete() {
  require_once(GSPLUGINPATH.'i18n_base/sitemap.class.php');
  I18nSitemap::executeOtherFunctions('page-delete', 'i18n_base_patch_page_delete');
  I18nSitemap::patchDeleteFile();
}

function i18n_base_patch_settings() {
  require_once(GSPLUGINPATH.'i18n_base/sitemap.class.php');
  I18nSitemap::patchSettings();
}


# ===== FRONTEND HOOKS =====

function i18n_init() {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  I18nFrontend::init();
}


# ===== FRONTEND FUNCTIONS =====

// load texts based on frontend/admin languages
function i18n_load_texts($plugin) {
  global $LANG, $language;
  if (basename($_SERVER['PHP_SELF']) == 'index.php') {
    // frontend language with I18N plugin is always two characters long
    i18n_merge($plugin, $language) ||
    i18n_merge($plugin, return_i18n_default_language()) || 
    i18n_merge($plugin, 'en');
  } else {
    i18n_merge($plugin, $LANG) ||
    (strlen($LANG) > 2 && i18n_merge($plugin, substr($LANG,0,2))) ||
    i18n_merge($plugin, 'en_US') ||
    i18n_merge($plugin, 'en');
  }
}

function return_i18n_default_language() {
  require_once(GSPLUGINPATH.'i18n_base/basic.class.php');
  return I18nBasic::getProperty(I18N_PROP_DEFAULT_LANGUAGE, I18N_DEFAULT_LANGUAGE);
}

function return_i18n_languages() {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getLanguages();
}

function return_i18n_available_languages($slug=null) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getAvailableLanguages($slug);
}

function return_i18n_page_data($slug) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getPageData($slug);
}

function get_i18n_content($slug, $force=false) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::outputContent($slug, $force);
}

function return_i18n_component($id) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getComponent($id);  
}

function get_i18n_component($id, $param1=null, $param2=null) {
  global $args;
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  if (func_num_args() > 1) { 
    $a = func_get_args(); array_shift($a); 
  } else if (isset($args)) {
    $a = $args;
  } else {
    $a = array();
  }
  return I18nFrontend::outputComponent($id, $a);
}

function get_i18n_page_url($echo=false) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  if (!$echo) { # to be compatible with get_page_url!!!
    echo I18nFrontend::getPageURL();
  } else {
    return I18nFrontend::getPageURL();
  }
}

function find_i18n_url($slug, $slugparent, $language=null, $type='full') {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getURL($slug, $slugparent, $language, $type);
}

function return_i18n_lang_url($language=null) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getLangURL($language);
}

function return_i18n_setlang_url($language) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::getSetLangURL($language);
}

function get_i18n_link($slug) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  return I18nFrontend::outputLinkTo($slug);  
}

function get_i18n_header($full=true, $omit=null) {
  require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');
  I18nFrontend::outputHeader($full, $omit);
}


# ===== BACKEND PAGES =====

function i18n_main() {
  if (isset($_GET['sitemap'])) {
    include(GSPLUGINPATH.'i18n_base/sitemap.php');
  } else {
    include(GSPLUGINPATH.'i18n_base/pages.php');
  }
}





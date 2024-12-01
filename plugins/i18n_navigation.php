<?php
/*
Plugin Name: I18N Navigation
Description: Multilevel navigation & breadcrumbs (I18N enabled)
Version: 3.3.1
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

The menu functions return a hierarchical menu based on the "parent", "menuStatus" and "menuOrder" attributes of the 
pages. The root menu consists of all pages with menuStatus = Y and no parent.
http://mvlcek/de/get-simple/i18n
Public functions:
  return_i18n_pages()
      returns an associative array of pages with the attributes url (slug), menuData, menuOrder, title, menu,
      parent (the parent's url/slug) and thehttp://mvlcek/de/get-simple/i18n other languages' titles and menus (as e.g. title_en, menu_en).
      additionally a sorted array of the children urls/slugs is available in the attribute children.
      the page with url/slug null contains the toplevel pages in the children array.
  return_i18n_page_structure($slug=null, $menuOnly=true, $slugToIgnore=null)
      returns the structure of the site in a flat sorted array where each entry is an associative array with
      the attributes url (slug), menuStatus, title, menu and level.
      If $slug is given, only the children (and their children, ...) of this page are returned.
      $menuOnly=false returns all pages, even if they are not in the menu.
      if $slugToIgnore is given, this page and all its children (...) are ignored.      
  return_i18n_menu_data($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL) {
      returns the menu tree from level $minlevel to $maxlevel, where the children of the $slug and of all (recursive)
      parents of the $slug are shown, if permitted by level and the menuStatus attribute of the child. A parent with
      menuStatus = N is not shown and neither are its children. With $show=I18N_SHOW_MENU all sub menus are shown independent
      of the current page, I18N_SHOW_PAGES shows all pages whether they are in the menu or not, I18N_SHOW_TITLES is the same
      but shows the titles instead of the menu texts and I18N_SHOW_LANGUAGE is like
      I18N_SHOW_NORMAL, but only shows pages in the current language
      A tree node has the attributes "parent", "url" (slug, String), "title", "menu" (localized strings), 
      "children" (array), "current" (true, if current page), "currentpath" (true, if in path to current page) and
      "haschildren" (true, if the page has children).
  return_i18n_breadcrumbs($slug)
      returns an array with breadcrumbs, each with attributes url, parent, menu, title

Display functions:
  get_i18n_navigation($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL)
      outputs the (localized) menu for the $slug (the menu tree returned by return_i18n_menu_data) as list items and sub lists.
      You must enclose the result in <ul>...</ul>
      The list items have the following classes:
        - slug of the page
        - slug of the page's parent
        - "current", if the page is the current page
        - "currentpath", if the page is a parent, grandparent, etc. of the current page
        - "open", if the page has children whose menu items are currently displayed
        - "closed", if the page has children whose menu items are currently not displayed
  get_i18n_breadcrumbs($slug)
      outputs the breadcrumbs each as " &raquo; <span class="breadcrumb"><a ...</a></span>"
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");
$i18n_pages = null;

# --- CHANGE THESE IF NEEDED - not recommended ---
define('I18N_USE_CACHE', true);
# --- CHANGE THESE END ---------

define('I18N_CACHE_FILE', 'i18n_menu_cache.xml'); # cache file in data/other

# parameter for the navigation function
define('I18N_FILTER_NONE', 0);     // (private pages are always filtered)
define('I18N_FILTER_CURRENT', 1);  // show children of given page and all siblings and parents/siblings
define('I18N_FILTER_LANGUAGE', 2); // show only pages in current language
define('I18N_FILTER_MENU', 4);     // show only menu pages
define('I18N_OUTPUT_MENU', 0);     // output menu title
define('I18N_OUTPUT_TITLE', 8);    // output page title

# show sub tree of current page including children of current page
define('I18N_SHOW_NORMAL', I18N_FILTER_MENU | I18N_FILTER_CURRENT | I18N_OUTPUT_MENU);
# show all menu entries independent of current page       
define('I18N_SHOW_MENU', I18N_FILTER_MENU | I18N_OUTPUT_MENU);         
# show all pages whether they are in the menu or not
define('I18N_SHOW_PAGES', I18N_FILTER_NONE | I18N_OUTPUT_MENU);        
# like I18N_SHOW_NORMAL, but only pages available in current language
define('I18N_SHOW_LANGUAGE', I18N_FILTER_MENU | I18N_FILTER_CURRENT | I18N_FILTER_LANGUAGE | I18N_OUTPUT_MENU);    
# show all pages whether they are in the menu or not, but show titles 
define('I18N_SHOW_TITLES', I18N_FILTER_NONE | I18N_OUTPUT_TITLE);       

# filter navigation items (vetoed items are removed from navigation)
#  - parameters: $url, $parent, $tags (tags as array)
#  - must return true, if item should not be included in the navigation
define('I18N_FILTER_VETO_NAV_ITEM', 'navigation-veto');

if (function_exists('i18n_load_texts')) {
  i18n_load_texts('i18n_navigation');
} else {  
  i18n_merge('i18n_navigation') || i18n_merge('i18n_navigation', 'en_US');
}

# register plugin
register_plugin(
	$thisfile, 
	'I18N Navigation', 	
	'3.3.1', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	i18n_r('i18n_navigation/PLUGIN_DESCRIPTION'),
	'pages',
	'i18n_navigation'  
);

# activate filter
add_action('edit-extras', 'i18n_navigation_edit'); 
add_action('html-editor-init', 'i18n_navigation_editor');
add_action('changedata-save', 'i18n_navigation_save'); 
add_action('page-delete', 'i18n_clear_cache'); // GetSimple 3.0+
add_action('pages-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_navigation/EDIT_NAVIGATION')));
add_action('index-pretemplate', 'i18n_check_redirect');
add_action('header', 'i18n_navigation_admin_header');

# workaround for page-delete in GetSimple 2.03:
if (!function_exists('get_site_version') && basename($_SERVER['PHP_SELF']) == 'deletefile.php') {
  i18n_clear_cache();
}
# refresh page cache after menu edit
@include_once(GSADMININCPATH.'caching_functions.php'); // workaround, because caching_functions is only included later
if (function_exists('create_pagesxml')) {
  add_action('menu-aftersave', 'create_pagesxml', array('true'));
}

# ===== BACKEND HOOKS =====

function i18n_navigation_admin_header() {
  require_once(GSPLUGINPATH.'i18n_navigation/backend.class.php');
  I18nNavigationBackend::outputHeader();
}

function i18n_clear_cache() {
  require_once(GSPLUGINPATH.'i18n_navigation/backend.class.php');
  I18nNavigationBackend::clearCache();
}


# ===== FRONTEND HOOKS =====

function i18n_check_redirect() {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  I18nNavigationFrontend::redirectIfLink();
}


# ===== FRONTEND FUNCTIONS =====

function return_i18n_pages() {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  return I18nNavigationFrontend::getPages();
}

function return_i18n_page_structure($slug=null, $menuOnly=true, $slugToIgnore=null) {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  return I18nNavigationFrontend::getPageStructure($slug, $menuOnly, $slugToIgnore);
}

function return_i18n_menu_data($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL) {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  return I18nNavigationFrontend::getMenu($slug, $minlevel, $maxlevel, $show);
}

function get_i18n_navigation($slug, $minlevel=0, $maxlevel=0, $show=I18N_SHOW_NORMAL, $component=null) {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  I18nNavigationFrontend::outputMenu($slug, $minlevel, $maxlevel, $show, $component);
}

function return_i18n_breadcrumbs($slug) {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  return I18nNavigationFrontend::getBreadcrumbs($slug);
}

function get_i18n_breadcrumbs($slug) {
  require_once(GSPLUGINPATH.'i18n_navigation/frontend.class.php');
  I18nNavigationFrontend::outputBreadcrumbs($slug);
}


# ===== BACKEND PAGES =====

function i18n_navigation() {
  include(GSPLUGINPATH.'i18n_navigation/structure.php');
}

function i18n_navigation_edit() {
  include(GSPLUGINPATH.'i18n_navigation/editextras.php');
}

function i18n_navigation_editor() {
  include(GSPLUGINPATH.'i18n_navigation/editorinit.php');
}

function i18n_navigation_save() {
  include(GSPLUGINPATH.'i18n_navigation/save.php');
  i18n_clear_cache();
}



<?php
/*
Plugin Name: I18N Search
Description: Search (I18N enabled!)
Version: 2.13.1
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

Public functions:
  delete_i18n_search_index()
  return_i18n_search_results($tags=null, $words=null, $first=0, $max=10, $order=null, $lang=null)
      returns an array of search results, each with the attributes url, parent, title, 
      date (UNIX timestamp), content, score and custom fields and
      a member function getExcerpt($content, $excerptLength)
  return_i18n_tags()
      returns an ordered array, where the key is the tag and the value is an array of
      urls (slugs) using that tag. Tags starting with "_" are ignored. 

Display functions:
  get_i18n_search_results($params)
      outputs the search results. params is an associative array and includes keys like 'tags'
      (to search for), 'words' (to search for), 'max' (number of results to display), ...
      If no tags or words are given they are taken from the request parameters 'tags' and 'words'
      A param 'numWords' < 0 will output the whole content of the page.
      Will use Pagify plugin for pagination, if installed.
  get_i18n_tags($params)
      outputs the ordered tags in spans with class "tag" and a font-size proportional to the 
      percentage of pages using it (within the percentage range specified by the $params['minTagSize']
      and $params['maxTagSize']).
      Tags starting with "_" are ignored.
  get_i18n_search_form($params)
      outputs a search form. $params['slug'] is the URL of the result page.
  get_i18n_search_rsslink($params)
      outputs a link to an RSS document, needs parameters 'title', 'name' and parameters specifying
      a search, see get_i18n_search_results.
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

define('I18N_WORD_INDEX', 'i18n_word_index.txt'); 
define('I18N_TAG_INDEX', 'i18n_tag_index.txt');
define('I18N_DATE_INDEX', 'i18n_date_index.txt');
define('I18N_SEARCH_SETTINGS_FILE', 'i18n_search_settings.xml');

# for sorting results - these can be changed in the search administration
define('I18N_CONTENT_WEIGHT', 1);
define('I18N_TITLE_WEIGHT', 5);
define('I18N_TAG_WEIGHT', 10);
define('I18N_MAX_RESULTS', 10);
define('I18N_NUM_WORDS', 30);
define('I18N_MIN_TAG_SIZE', 100);
define('I18N_MAX_TAG_SIZE', 250);
define('I18N_TAGS_ALWAYS_DEFLANG', 0);
define('I18N_TAGS_ALWAYS_LANG', 1);
define('I18N_TAGS_LANG_OR_DEFLANG', 2);

# action to add non-page items to index
#  - call i18n_search_index_item($id, $language, $creDate, $pubDate, $tags, $title, $content) to add an item
define('I18N_ACTION_INDEX', 'search-index');

# filter to add custom fields of a page to the index
#  - parameters: $item (of type I18nSearchPageItem)
#  - call methods addTags, addTitle, addContent to add fields to index
#  - return value is ignored
define('I18N_FILTER_INDEX_PAGE', 'search-index-page'); 

# filter to return an object of type I18nSearchResultItem for an indexed non-page item
#  - parameters: $id, $language, $creationDate, $publicationDate, $score 
#                (dates are UNIX timestamps, score is an integer)
#  - should return an object of a class extending I18nSearchResultItem 
define('I18N_FILTER_SEARCH_ITEM', 'search-item');     

# filter search results (vetoed items are removed from results)
#  - parameters: $item (of type I18nSearchResultItem or I18nSearchResultPage)
#  - must return true, if item should not be included in search results
define('I18N_FILTER_VETO_SEARCH_ITEM', 'search-veto');

# filter to display a search item
#  - parameters: $item, $showLanguage, $showDate, $dateFormat, $numWords
#                (item is of type I18nSearchResultItem, dateFormat for strftime)
#  - if the function handles the item, it must output the HTML
#  - must return true, if item was handled, false otherwise
define('I18N_FILTER_DISPLAY_ITEM', 'search-display');

# register plugin
register_plugin(
  $thisfile, 
  'I18N Search',   
  '2.13.1',     
  'Martin Vlcek',
  'http://mvlcek.bplaced.net', 
  'Search (I18N enabled!)',
  'plugins',
  'i18n_search_configure'  
);

# load i18n texts
if (basename($_SERVER['PHP_SELF']) != 'index.php') { // back end only
  i18n_merge('i18n_search', substr($LANG,0,2));
  i18n_merge('i18n_search', 'en');
}

# ===== BACKEND =====
add_action('changedata-save', 'delete_i18n_search_index'); 
add_action('page-delete', 'delete_i18n_search_index'); // GetSimple 3.0+
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_search/CONFIGURE'))); 

# ===== FRONTEND =====
add_action('index-pretemplate','i18n_search_pretemplate_for_rss');
add_action('index-pretemplate','i18n_search_pretemplate_for_mark');
add_action('theme-header','i18n_search_header_for_rss');
add_filter('content','i18n_search_content');
add_filter('search-index-page', 'i18n_search_index_page');


# ===== BACKEND HOOKS =====

# workaround for page-delete in GetSimple 2.03:
if (basename($_SERVER['PHP_SELF']) == 'deletefile.php') {
  delete_i18n_search_index();
}

# can also be called directly
function delete_i18n_search_index() {
  require_once(GSPLUGINPATH.'i18n_search/indexer.class.php');
  I18nSearchIndexer::deleteIndex();
}


# ===== INDEXING =====

function create_i18n_search_index() {
  require_once(GSPLUGINPATH.'i18n_search/indexer.class.php');
  I18nSearchIndexer::index();
}   

function i18n_search_index_page($item) {
  // virtual parent tag
  $parent = @$item->parent;
  if ($parent) $item->addTags('parent', array('_parent_'.$parent)); 
  // virtual date tags
  $pubDate = @$item->pubDate;
  $item->addTags('pubDate', array('_pub_'.date('Ym',$pubDate), '_pub_'.date('Y',$pubDate)));
  $creDate = @$item->creDate;
  if ($creDate) {
    $item->addTags('creDate', array('_cre_'.date('Ym',$creDate), '_cre_'.date('Y',$creDate)));
  }
  $menu = @$item->menuStatus;
  if ($menu == 'Y') $item->addTags('menuStatus', array('_menu'));
}


# ===== FRONTEND HOOKS =====

function i18n_search_content($content) {
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::processContent($content);
}

function i18n_search_pretemplate_for_rss() {
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::processPreTemplateForRSS();
}

function i18n_search_header_for_rss() {
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::processHeaderForRSS();
}

function i18n_search_pretemplate_for_mark() {
  if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
    $data = getXML(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
    if (!$data || !((string) $data->mark)) return;
    require_once(GSPLUGINPATH.'i18n_search/marker.class.php');
    $words = I18nSearchMarker::getWords();
    if ($words && count($words) > 0) {
      add_filter('content','i18n_search_mark');
    }
  }
}

function i18n_search_mark($content) {
  require_once(GSPLUGINPATH.'i18n_search/marker.class.php');
  return I18nSearchMarker::mark($content, I18nSearchMarker::getWords());
}


# ===== FRONTEND FUNCTIONS =====

function return_i18n_number_of_results($tags=null, $words=null) {
  require_once(GSPLUGINPATH.'i18n_search/searcher.class.php');
  $results = I18nSearcher::search($tags, $words);
  return $results ? count($results) : 0;
}

function &return_i18n_tags() {
  require_once(GSPLUGINPATH.'i18n_search/searcher.class.php');
  return I18nSearcher::tags();
}

function return_i18n_search_results($tags=null, $words=null, $first=0, $max=10, $order=null, $lang=null) {
  require_once(GSPLUGINPATH.'i18n_search/searcher.class.php');
  $results = I18nSearcher::search($tags, $words, $order, $lang);
  $count = count($results);
  if ($max > 0) $results = array_slice($results, $first, $max); else if ($first > 0) $results = array_slice($results, $first);
  return array('totalCount' => $count, 'first' => $first, 'results' => $results);
}

function get_i18n_search_rsslink($params) {
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::displayRSSLink($params);
}

function get_i18n_search_form($params=null) {
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::displaySearchForm($params);
}

function get_i18n_search_results($params=null) {
  # switch off paging by default as multiple paged search results are not supported
  if (!isset($params['showPaging'])) $params['showPaging'] = false;
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::displaySearchResults($params);
}

function get_i18n_tags($params=null) {
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  return I18nSearchViewer::displayTags($params);
}

# ===== BACKEND PAGES =====

function i18n_search_configure() {
  include(GSPLUGINPATH.'i18n_search/configure.php');
}


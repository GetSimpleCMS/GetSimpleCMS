<?php
/*
Plugin Name: I18N Special Pages
Description: Define, edit and display customized special pages (I18N enabled!)
Version: 1.3.5
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

This plugin should work by itself, but for an optimum experience you should also install
 - I18N
 - I18N Search
 - Theme Highlighter
 
Public functions:
  return_special_page_type()
      returns the special page type (or null, if it is an ordinary page)
  return_special_field($name, $default='')
      returns the special field's value or the default, if it is empty.
      For the field 'tags' an array of strings is returned.
  return_special_field_date($name, $format=null)
      returns a special field as date, where the field must be a parseable
      date or a numeric Unix timestamp. The format must be a valid parameter
      to strftime.
  return_special_field_excerpt($name, $length)
      returns an excerpt (type I18nSearchExcerpt) of the field if I18N Search 
      is installed. The field must have HTML content.
      The length can be a positive number for the number of words or a positive 
      number followed by 'p' or 'pm' for the number of paragraphs 
      (pm: add a <p>...</p> if there is more) or a negative number for the 
      whole content.
  return_special_field_image($name, $width=null, $height=null, $crop=true)
      returns the link to the image or it's scaled version if $width or $height is not null

Display functions:
  get_special_field($name, $default='', $isHTML=true)
      outputs the named field (or the default value, if the field is empty).
      Set $isHTML to false, if the content of the field is text instead
      of HTML or if you want to output it in an HTML tag attribute.
  get_special_field_date($name, $format=null)
      outputs a date field (see return_special_field_date)
  get_special_field_excerpt($name, $length)
      outputs an excerpt (see return_special_field_excerpt)
  get_special_tags($slug=null, $separator=' ', $all=false)
      outputs the tags (except the technical tags like _special_xxx) separated
      by the $separator. If $slug is set (to a I18N search result page), the 
      tags will be output as links for a search; if $all is false, only
      search results for that special page type will be found.
  get_special_field_image($name, $title=null, $width=null, $height=null, $crop=true)
      outputs an image tag with the image link given in the field $name and title/alt as $title.
      if $width and/or $height are given, the image is scaled.
      if $crop = true the image is also cropped to exactly fit the width/height ratio
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

$i18n_specialpages_tab = 'pages';
if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages') {
  $i18n_specialpages_tab = isset($_GET['create']) || isset($_GET['pages']) ? 'pages' : 'plugins';
}

# register plugin
register_plugin(
  $thisfile, 
  'I18N Special Pages',  
  '1.3.5',    
  'Martin Vlcek',
  'http://mvlcek.bplaced.net', 
  'Define special (custom) fields for page categories and provide customized editing and displaying (I18N enabled)',
  $i18n_specialpages_tab,
  'i18n_specialpages_main'  
);

if (basename($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
  # back end
  i18n_merge('i18n_specialpages', substr($LANG,0,2));
  i18n_merge('i18n_specialpages', 'en');
  add_action('pages-sidebar', 'i18n_specialpages_sidebar_item', array($thisfile, i18n_r('i18n_specialpages/PAGES'), 'pages'));
  add_action('pages-sidebar', 'i18n_specialpages_sidebar_item', array($thisfile, i18n_r('i18n_specialpages/CREATE_PAGE'), 'create'));
  add_action('plugins-sidebar', 'i18n_specialpages_sidebar_item', array($thisfile, i18n_r('i18n_specialpages/CONFIGURE'), 'config'));
  add_action('edit-extras', 'i18n_specialpages_edit');         // add hook to create new inputs on the edit screen.
  add_action('changedata-save', 'i18n_specialpages_save');     // add hook to save custom field values 
  //add_action('admin-pre-header', 'i18n_specialpages_redirect', array(false)); 
  //add_action('header', 'i18n_specialpages_redirect', array(true)); 
  add_action('header', 'i18n_specialpages_header', array(true)); 
  
  if (i18n_specialpages_gsversion() == '3.0') {
    // workaround for GetSimple 3.0:
    if (isset($_COOKIE['GS_ADMIN_USERNAME'])) setcookie('GS_ADMIN_USERNAME', $_COOKIE['GS_ADMIN_USERNAME'], 0, '/');
  }
} else {
  # front end
  if (function_exists('i18n_load_texts')) {
    i18n_load_texts('i18n_specialpages');
  } else {  
    i18n_merge('i18n_specialpages', substr($LANG,0,2)); 
    i18n_merge('i18n_specialpages', 'en');
  }
  add_action('index-pretemplate', 'i18n_specialpages_init');
  add_action('theme-header','i18n_specialpages_theme_header');
  add_filter('search-display', 'i18n_specialpages_search_display');
}
add_filter('search-index-page', 'i18n_specialpages_search_index');

# ===== GENERAL FUNCTIONS =====

function i18n_specialpages_settings($name=null) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::getSettings($name);
}

function i18n_specialpages_gsversion() {
  @include(GSADMININCPATH.'configuration.php');
  return GSVERSION;
}


# ===== FRONTEND HOOKS =====

function i18n_specialpages_init() {
  global $content, $data_index;
  if (@$data_index->special) {
    if (function_exists('i18n_init')) i18n_init();
    require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
    $newcontent = I18nSpecialPages::processContent($data_index);
    if ($newcontent !== null) $content = $newcontent;
  }
}

function i18n_specialpages_theme_header() {
  global $data_index;
  if (@$data_index->special) {
    require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
    I18nSpecialPages::outputHeader($data_index);
  }
}

function i18n_specialpages_search_index($item) {
  if (@$item->special) {
    require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
    $defs = I18nSpecialPages::getSettings();
    $fields = @$defs[(string) $item->special]['fields'];
    if (count($fields) > 0) foreach ($fields as $field) {
      if (@$field['index']) {
        $name = @$field['name'];
        if (@$field['type'] == 'wysiwyg') {
          $item->addContent($name, html_entity_decode(strip_tags($item->$name), ENT_QUOTES, 'UTF-8'));
        } else if (@$field['type'] == 'checkbox') {
          if ($item->$name) {
            $item->addTags($name, array($name));
          }
        } else if ((string) $field['index'] == '2') {
          $item->addTags($name, array(html_entity_decode($item->$name, ENT_QUOTES, 'UTF-8')));
        } else {
          $item->addContent($name, html_entity_decode($item->$name, ENT_QUOTES, 'UTF-8'));
        }
      }
    }
  }
}

function i18n_specialpages_search_display($item, $showLanguage, $showDate, $dateFormat, $numWords) {
  if (@$item->special) {
    require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
    return I18nSpecialPages::outputSearchItem($item, $showLanguage, $showDate, $dateFormat, $numWords);
  }
  return false;
}

# ===== FRONTEND FUNCTIONS =====

function return_special_page_type() {
  return return_special_field('special', null);
}

function return_special_field($name, $default='') {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::getField($name, $default);  
}

function return_special_field_date($name, $format=null) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::getDate($name, $format);  
}

function return_special_field_excerpt($name, $length) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::getExcerpt($name, $length);  
}

function return_special_field_image($name, $width=null, $height=null, $crop=true) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::getImage($name, $width, $height, $crop);  
}


function get_special_field($name, $default='', $isHTML=true) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::outputField($name, $default, $isHTML);
}

function get_special_field_date($name, $format=null) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::outputDate($name, $format);
}

function get_special_field_excerpt($name, $length) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  $excerpt = I18nSpecialPages::getExcerpt($name, $length);  
  echo $excerpt->text;
}

function get_special_tags($slug=null, $separator=' ', $all=false) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::outputTags($slug, $separator, $all);
}

function get_special_field_image($name, $title=null, $width=null, $height=null, $crop=true) {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  return I18nSpecialPages::outputImage($name, $title, $width, $height, $crop);  
}


# ===== BACKEND HOOKS =====

function i18n_specialpages_sidebar_item($id, $txt, $action=null, $always=true){
  $current = false;
  if (isset($_GET['id']) && $_GET['id'] == $id && (!$action || isset($_GET[$action]))) {
    $current = true;
  }
  if ($always || $current) {
    echo '<li><a href="load.php?id='.$id.($action ? '&amp;'.$action : '').'" '.($current ? 'class="current"' : '').' >'.$txt.'</a></li>';
  }
}


function i18n_specialpages_redirect($js=false) {  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  I18nSpecialPagesBackend::header($js);
}

function i18n_specialpages_header() {
  require_once(GSPLUGINPATH.'i18n_specialpages/backend.class.php');
  I18nSpecialPagesBackend::header();
}

function i18n_specialpages_edit() {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  require_once(GSPLUGINPATH.'i18n_specialpages/backend.class.php');
  include(GSPLUGINPATH.'i18n_specialpages/edit.php');
}

function i18n_specialpages_save() {
  require_once(GSPLUGINPATH.'i18n_specialpages/backend.class.php');
  I18nSpecialPagesBackend::save();
}

# ===== BACKEND PAGES =====

function i18n_specialpages_main() {
  require_once(GSPLUGINPATH.'i18n_specialpages/specialpages.class.php');
  if (isset($_GET['create'])) {
    include(GSPLUGINPATH.'i18n_specialpages/create.php');
  } else if (isset($_GET['pages'])) {
    include(GSPLUGINPATH.'i18n_specialpages/pages.php');
  } else if (isset($_GET['config']) && isset($_GET['edit'])) {
    include(GSPLUGINPATH.'i18n_specialpages/conf_edit.php');
  } else if (isset($_GET['config']) && isset($_GET['settings'])) {
    include(GSPLUGINPATH.'i18n_specialpages/conf_settings.php');
  } else if (isset($_GET['config'])) {
    include(GSPLUGINPATH.'i18n_specialpages/conf_overview.php');
  }
}


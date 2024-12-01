<?php 
/*
Plugin Name: I18N Custom Fields
Description: Custom Fields (I18N enabled)
Version: 1.9.3
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

Public functions:
  return_custom_field($name, $default='')
      return the value (or default) of the custom field with the given name for the current page

Display functions:
  get_custom_field($name, $default='')
      outputs the value (or default) of the custom field with the given name for the current page
      returns true, if the field exists and is not empty

*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

define('I18N_CUSTOMFIELDS_FILE', 'customfields.xml');

# register plugin
register_plugin(
	$thisfile,
	'I18N Custom Fields',
	'1.9.3',
  'Martin Vlcek',
  'http://mvlcek.bplaced.net/',
  'Custom fields for pages (I18N enabled) - based on Mike Swan\'s plugin',
	'plugins',
	'i18n_customfields_configure'
);

i18n_merge('i18n_customfields') || i18n_merge('i18n_customfields', 'en_US');

add_action('index-pretemplate', 'i18n_get_custom_fields'); // add hook to make custom field values available to theme
add_action('header', 'i18n_customfields_header');            // add hook to create styles for custom field editor.
add_action('edit-extras', 'i18n_customfields_edit');         // add hook to create new inputs on the edit screen.
add_action('changedata-save', 'i18n_customfields_save');     // add hook to save custom field values 
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, i18n_r('i18n_customfields/CUSTOMFIELDS_VIEW'))); 
add_filter('search-index-page', 'i18n_customfields_index');

if (!i18n_customfields_is_frontend() && i18n_customfields_gsversion() == '3.0') {
  // workaround for GetSimple 3.0:
  if (isset($_COOKIE['GS_ADMIN_USERNAME'])) setcookie('GS_ADMIN_USERNAME', $_COOKIE['GS_ADMIN_USERNAME'], 0, '/');
}


$i18n_customfield_defs = null;
$i18n_customfield_types = null;
$customfields = null;

function i18n_customfields_is_frontend() {
  return function_exists('get_site_url');
}

function i18n_customfields_gsversion() {
  @include(GSADMININCPATH.'configuration.php');
  return GSVERSION;
}

function i18n_customfield_defs() {
	global $i18n_customfield_defs, $i18n_customfield_types;
  if ($i18n_customfield_defs === null) {
    $i18n_customfield_defs = array();
    $i18n_customfield_types = array();
    $file = GSDATAOTHERPATH . I18N_CUSTOMFIELDS_FILE;
	  if (file_exists($file)) {
      $data = getXML($file);
		  $items = $data->item;
		  if (count($items) > 0) {
			  foreach ($items as $item) {
          $cf = array();
          $cf['key'] = (string) $item->desc;
          $cf['label'] = (string) $item->label;
          $cf['type'] = (string) $item->type;
          $cf['value'] = (string) $item->value;
          if ($item->type == "dropdown") {
            $cf['options'] = array();
            foreach ($item->option as $option) {
              $cf['options'][] = (string) $option;
            }
          }
          $cf['index'] = (bool) $item->index;
          $i18n_customfield_defs[] = $cf;
          $i18n_customfield_types[$cf['key']] = $cf['type'];
			  }
		  } 
	  }
  }
  return $i18n_customfield_defs;
}

function i18n_customfield_types() {
  global $i18n_customfield_types;
  if ($i18n_customfield_types === null) i18n_customfield_defs();
  return $i18n_customfield_types;
}

function i18n_customfields_save(){
	global $USR, $xml; // SimpleXML to save to
	$defs = i18n_customfield_defs();
  if (count($defs) > 0) {
    foreach ($defs as $def) {
      $key = $def['key'];
		  if(isset($_POST['post-'.strtolower($key)])) { 
			  $xml->addChild(strtolower($key))->addCData(stripslashes($_POST['post-'.strtolower($key)]));	
		  }
    }	
	}	
  // new field for creation date
  if (!isset($xml->creDate)) {
    if (@$_POST['creDate']) {
      $xml->addChild('creDate', $_POST['creDate']);
    } else {
      $xml->addChild('creDate', (string) $xml->pubDate);
    }
  }
  // new field for user
  if (isset($USR) && $USR && !isset($xml->user)) $xml->addChild('user')->addCData($USR);
}

function i18n_get_custom_fields() {
  global $customfields, $data_index, $data_index_orig;
  if ($customfields) return;
  if (function_exists('i18n_init')) {
    i18n_init(); // make sure that I18N is initialized
    if (isset($data_index_orig)) i18n_get_custom_fields_from($data_index_orig);
  }
  i18n_get_custom_fields_from($data_index);
}

function i18n_get_custom_fields_from($data) {
  global $customfields;
  $stdfields = array('pubDate','title','url','meta','metad','menu','menuStatus','menuOrder',
                      'template','parent','content','private');
  if (!isset($customfields)) $customfields = array();
  if ($data) foreach ($data->children() as $child) {
    if (!in_array($child->getName(), $stdfields) && (string) $child) {
      $customfields[$child->getName()] = (string) $child;
    }
  }
}

function get_custom_field($name, $default='') {
  global $customfields;
  if (@$customfields[$name]) {
    $content = $customfields[$name];
    $types = i18n_customfield_types();
    if (@$types[$name] == 'textarea') $content = exec_filter('content', $content);
    echo $content;
    return true;
  } else {
    echo $default;
    return false;
  }
}

function return_custom_field($name, $default='') {
  global $customfields;
  return @$customfields[$name] ? $customfields[$name] : $default;
} 

function return_custom_field_options($name) {
  $defs = i18n_customfield_defs();
  foreach ($defs as $def) {
    if ($def['key'] == $name) return @$def['options'];
  }
  return null;
}

if (!function_exists('get_page_creation_date')) {
  function get_page_creation_date($i = "l, F jS, Y - g:i A", $echo=true) {
  	global $date;
  	global $TIMEZONE;
  	if ($TIMEZONE != '') {
  		if (function_exists('date_default_timezone_set')) {
  			date_default_timezone_set($TIMEZONE);
  		}
  	}
    $creDate = return_custom_field('creDate');
  	$myVar = $creDate ? date($i, strtotime($creDate)) : null;
  	if ($echo) {
  		echo $myVar ? $myVar : '';
  	} else {
  		return $myVar;
  	}
  }
}

function i18n_customfields_header() {
?>
  <style type="text/css">
    form #metadata_window table.formtable td .cke_editor { width: 610px; }
    form #metadata_window table.formtable td .cke_editor td:first-child { padding: 0; }
    form #metadata_window table.formtable .cke_editor td.cke_top { border-bottom: 1px solid #AAAAAA; }
    form #metadata_window table.formtable .cke_editor td.cke_contents { border: 1px solid #AAAAAA; }
    #customfieldsForm .hidden { display:none; }
  </style>
<?php
}

function i18n_customfields_edit() {
  include(GSPLUGINPATH.'i18n_customfields/edit.php');
}

function i18n_customfields_configure(){
  include(GSPLUGINPATH.'i18n_customfields/configure.php');
}

// indexing content for I18N Search plugin. $item is of type I18nSearchPageItem.
function i18n_customfields_index($item) {
  $i18n_customfield_defs = i18n_customfield_defs();
  foreach ($i18n_customfield_defs as $def) {
    if (@$def['index']) {
      $name = @$def['key'];
      if (@$def['type'] == 'textarea') {
        $item->addContent($name, html_entity_decode(strip_tags($item->$name), ENT_QUOTES, 'UTF-8'));
      } else if (@$def['type'] == 'checkbox') {
      	if ($item->$name) {
      		$item->addTags($name, array($name));
      	}
      } else {
        $item->addContent($name, html_entity_decode($item->$name, ENT_QUOTES, 'UTF-8'));
      }
    }
  }
}

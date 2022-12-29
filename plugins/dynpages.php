<?php
/*
Plugin Name: DynPages
Description: Replace place holders in pages with dynamic content from components (I18N enabled!)
Version: 0.7.2
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

Just add {% component_name parameter1 ... %} to your page. It will be replaced
with the (dynamic) content of the component. If the {% ... %} is the only content 
in a paragraph <p>, the paragraph is removed first.

You can separate parameters with spaces or commas. If a parameter contains spaces or
commas, you have to enclose it in quotes or double quotes.

If the I18N plugin is installed the component in the correct language is displayed.

The parameters are stored in the global variable $args, so use "global $args;" to
access it in the component - always check for length and use defaults if a 
parameter is not given.
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 
	'DynPages', 	
	'0.7.2', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Replace place holders in pages with dynamic content from components (I18N enabled!)',
	'',
	''  
);

# activate filter
add_filter('content','dynpages_replace');

function dynpages_replace($content) {
  return preg_replace_callback("/(<p>\s*)?{%\s*([a-zA-Z0-9_-]+)(\s+(?:%[^%\}]|[^%])+)?\s*%}(\s*<\/p>)?/",'dynpages_replace_match',$content);
}

function dynpages_replace_match($match) {
  global $args;
  $replacement = '';
  if ($match[1] && (!isset($match[4]) || !$match[4])) $replacement .= $match[1];
  if (isset($args)) $saved_args = $args;
  $args = array();
  $paramstr = isset($match[3]) ? html_entity_decode(trim($match[3]), ENT_QUOTES, 'UTF-8') : '';
  while ($paramstr && preg_match('/^([^"\', ]*|"[^"]*"|\'[^\']*\')(\s+|\s*,\s*|$)/', $paramstr, $pmatch)) {
    $value = trim($pmatch[1]);
    if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1,strlen($value)-2);
    $args[] = $value;
    $paramstr = substr($paramstr, strlen($pmatch[0]));
  }
  ob_start();
  if (function_exists('get_i18n_component')) {
    get_i18n_component($match[2]);
  } else {
    get_component($match[2]);
  }
  $replacement .= ob_get_contents();
  ob_end_clean();
  if (isset($saved_args)) $args = $saved_args; else unset($args);
  if (!$match[1] && isset($match[4]) && $match[4]) $replacement .= $match[4];
  return $replacement;
}

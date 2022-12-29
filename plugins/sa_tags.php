<?php
/*
Plugin Name: sa_tags
Description: Provides interactive tag UI for tag input fields.
Version: 0.2
Author: Shawn Alverson
Author URI: http://www.shawnalverson.com/

Included 
Dependancies

jQueryAutocompletePlugin: https://github.com/agarzola/jQueryAutocompletePlugin
jQuery-Tags-Input(custom fork) https://github.com/tablatronix/jQuery-Tags-Input
xoxco / jQuery-Tags-Input: https://github.com/xoxco/jQuery-Tags-Input

*/

$SATAGS['PLUGIN_ID'] = "sa_tags";
$SATAGS['PLUGIN_PATH'] = $SITEURL.'plugins/'.$SATAGS['PLUGIN_ID'].'/';
$SATAGS['PLUGIN_URL'] = "http://tablatronix.com/getsimple-cms/sa-tags-plugin/";
$SATAGS['DEBUG'] = false;

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,                  //Plugin id
	'sa Tags', 	                //Plugin name
	'0.2', 		                  //Plugin version
	'Shawn Alverson',           //Plugin author
	$SATAGS['PLUGIN_URL'],      //author website
	'Adds interactive tagging', //Plugin description
	'',                         //page type - on which admin tab to display
	''                          //main function (administration)
);

$SATAGS['owner'] = "";

# activate filters
// use header hook if older than 3.1
if (pageUsesTags()) {
  if(floatval(GSVERSION) < 3.1){
    add_action('header', 'sa_tags_executeheader');
    $SATAGS['owner'] = "SA_";
  }  
  else{
    sa_tags_executeheader();
  }
}

# Functions
function pageUsesTags()
{
  if (basename($_SERVER['PHP_SELF']) == 'edit.php') return true;
  if (basename($_SERVER['PHP_SELF']) == 'loadtab.php' && $_REQUEST['item']=='i18n_gallery_edit') return true;
  if (basename($_SERVER['PHP_SELF']) == 'load.php' && $_REQUEST['id']=='i18n_gallery') return true;
  if (basename($_SERVER['PHP_SELF']) == 'load.php' && $_REQUEST['id']=='news_manager') return true;
  if (basename($_SERVER['PHP_SELF']) == 'load.php' && $_REQUEST['id']=='i18n_specialpages') return true;
  if (basename($_SERVER['PHP_SELF']) == 'load.php' && $_REQUEST['id']=='blog') return true;
  if (basename($_SERVER['PHP_SELF']) == 'load.php' && $_REQUEST['id']=='sa_welcome') return true;
}



# Backwards Compatability for 3.0
function SA_register_script($handle, $src, $ver, $in_footer=FALSE)
{
    // echo "sa_register_script";
    echo '<script type="text/javascript" src="'.$src.'"></script>'."\n";
}

function SA_queue_script($name,$where)
{
}

function SA_register_style($handle, $src, $ver)
{
    echo '<link rel="stylesheet" href="'.$src.'" type="text/css" charset="utf-8" />'."\n";
}

function SA_queue_style($name,$where)
{
}

function sa_tags_executeheader(){
  GLOBAL $SATAGS, $GSADMIN, $SITEURL;

	$PLUGIN_ID = $SATAGS['PLUGIN_ID'];
	$PLUGIN_PATH = $SATAGS['PLUGIN_PATH'];
	$owner = $SATAGS['owner'];
	
  // echo "sa_tags_executeheader";
  
  $regscript = $owner."register_script";
  $regstyle  = $owner."register_style";
  $quescript = $owner."queue_script";
  $questyle  = $owner."queue_style";
  
  $regscript($PLUGIN_ID, $PLUGIN_PATH.'js/sa_tags.js', '0.1', FALSE);
  
	// tags input
	# $regscript('jquery_tagsinput', $PLUGIN_PATH.'js/jquery.tagsinput.min.js', '1.0', FALSE);
  $regscript('jquery_tagsinput', $PLUGIN_PATH.'js/jquery.tagsinput.2.0.js', '2.0', FALSE);
  $regstyle($PLUGIN_ID, $PLUGIN_PATH.'css/sa.jquery.tagsinput.css', '0.1', 'screen');
  
	// jquery autocomplete
	# $regscript('jquery_autocomplete', $PLUGINPATH.'js/jquery.autocomplete.min.js', '1.2.2', FALSE);
  # $regscript('jquery_ui_autocomplete', $PLUGINPATH.'js/jquery.ui.autocomplete.js', '', FALSE);
  
	// jquery ui
	# $regscript('jquery_ui', $SITEURL.$GSADMIN.'/template/js/jquery-ui.min.js', '1.0', FALSE);
  # $regscript('jquery_ui_dependancies', $PLUGIN_PATH.'js/jquery-ui-1.8.19.custom.js', '', FALSE);
  $regstyle('jquery_ui_dependancies', $PLUGIN_PATH.'css/jquery-ui-1.8.20.custom.css', '', 'screen');
  
  $quescript($PLUGIN_ID,GSBACK); 
  $quescript('jquery_tagsinput',GSBACK); 
  $quescript('jquery_ui',GSBACK); 
  $quescript('jquery_ui_dependancies',GSBACK); 
  # $quescript('jquery_ui_autocomplete',GSBACK); 

  $questyle($PLUGIN_ID,GSBACK); 
  $questyle('jquery_ui_dependancies',GSBACK); 
  
}

?>
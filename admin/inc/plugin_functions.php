<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Plugin Functions
 *
 * @package GetSimple
 * @subpackage Plugin-Functions
 */

$plugins          = array();  // used for option names
$plugins_info     = array();
$filters          = array();
$live_plugins     = array();  // used for enablie/disable functions


/**
 * Include any plugins, depending on where the referring 
 * file that calls it we need to set the correct paths. 
*/
if (file_exists(GSPLUGINPATH)){
	$pluginfiles = getFiles(GSPLUGINPATH);
} 

$pluginsLoaded=false;


// Check if data\other\plugins.xml exists 
if (!file_exists(GSDATAOTHERPATH."plugins.xml")){
   create_pluginsxml();
} 

read_pluginsxml();        // get the live plugins into $live_plugins array

if (isset($_GET['set'])){
  change_plugin($_GET['set']);
  header('Location: plugins.php');
}

create_pluginsxml();      // check that plugins have not been removed or added to the directory

// load each of the plugins
foreach ($live_plugins as $file=>$en) {
  $pluginsLoaded=true;
  if (file_exists(GSPLUGINPATH . $file)){
  	require_once(GSPLUGINPATH . $file);
  }
}


/**
 * change_plugin
 * 
 * Enable/Disable a plugin
 *
 * @since 2.04
 * @uses $live_plugins
 *
 * @param $name
 */
function change_plugin($name){
  global $live_plugins;   
  
  if ($live_plugins[$name]=="true"){
    $live_plugins[$name]="false";
  } else {
    $live_plugins[$name]="true";
  }
  create_pluginsxml();
}


/**
 * read_pluginsxml
 * 
 * Read in the plugins.xml file and populate the $live_plugins array
 *
 * @since 2.04
 * @uses $live_plugins
 *
 */
function read_pluginsxml(){
  global $live_plugins;   
   
  $data = getXML(GSDATAOTHERPATH . "plugins.xml");
  $componentsec = $data->item;
  $count= 0;
  if (count($componentsec) != 0) {
    foreach ($componentsec as $component) {
      $live_plugins[(string)$component->plugin]=(string)$component->enabled;
    }
  }

}


/**
 * create_pluginsxml
 * 
 * If the plugins.xml file does not exists, read in each plugin 
 * and add it to the file. 
 * read_pluginsxml() is called again to repopulate $live_plugins
 *
 * @since 2.04
 * @uses $live_plugins
 *
 */
function create_pluginsxml(){
  global $live_plugins;   
  if (file_exists(GSPLUGINPATH)){
    $pluginfiles = getFiles(GSPLUGINPATH);
  }  
  $xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>'); 
  foreach ($pluginfiles as $fi) {
    $pathExt = pathinfo($fi,PATHINFO_EXTENSION );
    $pathName= pathinfo_filename($fi);
    $count=0;
    if ($pathExt=="php")
    {
      $components = $xml->addChild('item');  
      $c_note = $components->addChild('plugin');
      $c_note->addCData($fi);
      $c_note = $components->addChild('enabled');
      if (isset($live_plugins[(string)$fi])){
        $c_note->addCData($live_plugins[(string)$fi]);     
      } else {
         $c_note->addCData('true'); 
      } 
    }
  }    
  XMLsave($xml, GSDATAOTHERPATH."plugins.xml");
  read_pluginsxml();
}


/**
 * Add Action
 *
 * @since 2.0
 * @uses $plugins
 * @uses $live_plugins
 *
 * @param string $hook_name
 * @param string $added_function
 * @param array $args
 */
function add_action($hook_name, $added_function, $args = array()) {
	global $plugins;
	global $live_plugins; 
  
	$bt = debug_backtrace();
	$shift=count($bt) - 4;	// plugin name should be  
  	$caller = array_shift($bt);
	$realPathName=pathinfo_filename($caller['file']);
	$realLineNumber=$caller['line'];
	while ($shift > 0) {
		 $caller = array_shift($bt);
		 $shift--;
	}
  	$pathName= pathinfo_filename($caller['file']);

	if ((isset ($live_plugins[$pathName.'.php']) && $live_plugins[$pathName.'.php']=='true') || $shift<0 ){
		if ($realPathName!=$pathName) {
			$pathName=$realPathName;
			$lineNumber=$realLineNumber;
		} else {
			$lineNumber=$caller['line'];
		}
		
		$plugins[] = array(
			'hook' => $hook_name,
			'function' => $added_function,
			'args' => (array) $args,
			'file' => $pathName.'.php',
	    	'line' => $caller['line']
		);
	  } 
}

/**
 * Execute Action
 *
 * @since 2.0
 * @uses $plugins
 *
 * @param string $a Name of hook to execute
 */
function exec_action($a) {
	global $plugins;
	
	foreach ($plugins as $hook)	{
		if ($hook['hook'] == $a) {
			call_user_func_array($hook['function'], $hook['args']);
		}
	}
}

/**
 * Create Side Menu
 *
 * This adds a side level link to a control panel's section
 *
 * @since 2.0
 * @uses $plugins
 *
 * @param string $id ID of the link you are adding
 * @param string $txt Text to add to tabbed link
 */
function createSideMenu($id,$txt){
	$class=null;
  if (isset($_GET['id']) && $_GET['id'] == $id) {
		$class='class="current"';
	}
	echo '<li><a href="load.php?id='.$id.'" '.$class.' >'.$txt.'</a></li>';
}

/**
 * Create Navigation Tab
 *
 * This adds a top level tab to the control panel
 *
 * @since 2.0
 * @uses $plugins
 *
 * @param string $id Id of current page
 * @param string $txt Text to add to tabbed link
 */
function createNavTab($url,$txt) {
	echo "<li><a href='".$url."' class='plugins' />";
	echo $txt;
	echo "</a></li>";
}

/**
 * Register Plugin
 *
 * @since 2.0
 * @uses $plugin_info
 *
 * @param string $id Unique ID of your plugin 
 * @param string $name Name of the plugin
 * @param string $ver Optional, default is null. 
 * @param string $auth Optional, default is null. 
 * @param string $auth_url Optional, default is null. 
 * @param string $desc Optional, default is null. 
 * @param string $type Optional, default is null. This is the page type your plugin is classifying itself
 * @param string $loaddata Optional, default is null. This is the function that run on load
 */
function register_plugin($id, $name, $ver=null, $auth=null, $auth_url=null, $desc=null, $type=null, $loaddata=null) {
	global $plugin_info;
	
	$plugin_info[$id] = array(
	  'name' => $name,
	  'version' => $ver,
	  'author' => $auth,
	  'author_url' => $auth_url,
	  'description' => $desc,
	  'page_type' => $type,
	  'load_data' => $loaddata
	);

}

/**
 * Add Filter
 *
 * @since 2.0
 * @uses $filters
 * @uses $live_plugins
 *
 * @param string $id Id of current page
 * @param string $txt Text to add to tabbed link
 */
function add_filter($filter_name, $added_function) {
	global $filters;
  global $live_plugins;   
  $bt = debug_backtrace();
  $caller = array_shift($bt);
  $pathName= pathinfo_filename($caller['file']);
  	$filters[] = array(
  		'filter' => $filter_name,
  		'function' => $added_function
  	);
}

/**
 * Execute Filter
 *
 * Allows changing of the passed variable
 *
 * @since 2.0
 * @uses $filters
 *
 * @param string $script Filter name to execute
 * @param array $data
 */
function exec_filter($script,$data=array()) {
	global $filters;
	foreach ($filters as $filter)	{
		if ($filter['filter'] == $script) {
			$data = call_user_func_array($filter['function'], array($data));
		}
	}
	return $data;
}

?>
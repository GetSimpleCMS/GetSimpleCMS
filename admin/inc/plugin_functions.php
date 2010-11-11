<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Plugin Functions
 *
 * @package GetSimple
 * @subpackage Plugin-Functions
 */

$plugins = array();  // used for option names
$plugins_info = array();
$filters = array();

/**
 * Include any plugins, depending on where the referring 
 * file that calls it we need to set the correct paths. 
*/
if (file_exists(GSPLUGINPATH)){
	$pluginfiles = getFiles(GSPLUGINPATH);
} 

$pluginsLoaded=false;

foreach ($pluginfiles as $fi) {
	$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
	$pathName= pathinfo_filename($fi);
	if ($pathExt=="php")
	{
		$pluginsLoaded=true;
		require_once(GSPLUGINPATH . $fi);
	}
}

/**
 * Add Action
 *
 * @since 2.0
 * @uses $plugins
 *
 * @param string $hook_name
 * @param string $added_function
 * @param array $args
 */
function add_action($hook_name, $added_function, $args = array()) {
	global $plugins;
	
	$bt = debug_backtrace();
  $caller = array_shift($bt);
  
	$plugins[] = array(
		'hook' => $hook_name,
		'function' => $added_function,
		'args' => (array) $args,
		'file' => $caller['file'],
    'line' => $caller['line']
	);
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
	if ($_GET['id'] == $id) {
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
 *
 * @param string $id Id of current page
 * @param string $txt Text to add to tabbed link
 */
function add_filter($filter_name, $added_function) {
	global $filters;
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
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	plugins.php
* @Package:	GetSimple
* @Action:	Functions used by Plugin System. Beta ver 1.0	
*
*****************************************************/

$plugins = array();  // used for option names
$plugins_info = array();
$filters = array();

// include any plugins, depending on where the referring file that calls it we need to 
// set the correct paths. 

if (file_exists(GSPLUGINPATH)){
	$pluginfiles = getFiles(GSPLUGINPATH);
} 

$pluginsLoaded=false;

foreach ($pluginfiles as $fi) 
{
	$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
	$pathName= pathinfo($fi,PATHINFO_FILENAME );
	if ($pathExt=="php")
	{
		$pluginsLoaded=true;
		require_once(GSPLUGINPATH . $fi);
	}
}

 
/*******************************************************
 * @function add_action
 * @param $hook_name - name of hook
 * @param $added_funcion - name of user function to add
 * @param $args - arguments for function
*/

function add_action($hook_name, $added_funcion, $args = array()) 
{
	global $plugins;

	$plugins[] = array(
		'hook' => $hook_name,
		'function' => $added_funcion,
		'args' => (array) $args
	);

}

/*******************************************************
 * @function get_template
 * @param $a - name of Hook 
 *
*/
function exec_action($a) {
	global $plugins;
	
	foreach ($plugins as $hook)	{
		if ($hook['hook'] == $a) {
			call_user_func_array($hook['function'], $hook['args']);
			// http://us3.php.net/call_user_func_array
		}
	}

}

/*******************************************************
 * @function createSideMenu
 * @param $plugin  - Plugin Name
 * @param $page    - Page to call, without ".php"
 * @param $txt     - text to display on link 
 *
*/
function createSideMenu($id,$txt){
	$class="";
	if (@$_GET['id'] == @$id) {
		$class='class="current"';
	}

	echo '<li><a href="load.php?id='.$id.'" '.$class.' >'.$txt.'</a></li>';
}



/*******************************************************
 * @function createNavTab
 * @param $url - URL for link
 * @param $txt - text to display on link
*/
function createNavTab($url,$txt) {
	echo "<li><a href='".$url."' class='plugins' />";
	echo $txt;
	echo "</a></li>";
}


/*******************************************************
 * @function register_plugin
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


/*******************************************************
 * @function add_filter
*/
function add_filter($filter_name, $added_function) {
	global $filters;
	$filters[] = array(
		'filter' => $filter_name,
		'function' => $added_function
	);
}


/*******************************************************
 * @function exec_filter
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
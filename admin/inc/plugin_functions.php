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


// include any plugins, depending on where the referring file that calls it we need to 
// set the correct paths. 

if (file_exists('admin/plugins/'))
{
	$dir='admin/';
	$pluginfiles = getFiles($dir.'plugins/');
} 
elseif (file_exists('../admin/plugins/')) 
{
	$dir="../admin/";
	$pluginfiles = getFiles($dir.'plugins/');
} 
else 
{
	$dir="";
	$pluginfiles = getFiles('plugins/');
}

$pluginsLoaded=false;

foreach ($pluginfiles as $fi) 
{
	$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
	$pathName= pathinfo($fi,PATHINFO_FILENAME );
	if ($pathExt=="php")
	{
		$pluginsLoaded=true;
		require_once($dir.'plugins/'.$fi);
	}
}

// show plugins menu tab
if ($pluginsLoaded==true)
{
	add_action('nav-tab','createNavTab',array('plugins.php?plugin=main', $i18n['PLUGINS_NAV'])); 
 }

 
/*******************************************************
 * @function add_action
 * @param $hook_name - name of hook
 * @param $added_funcion - name of user function to add
 * @param $args - arguments for function
*/

function add_action($hook_name, $added_funcion, $args = null) 
{
	global $plugins;

	$plugins[] = array(
		'hook' => $hook_name,
		'function' => $added_funcion,
		'args' => $args
	);

}

/*******************************************************
 * @function get_template
 * @param $a - name of Hook 
 *
*/
function exec_action($a) 
{
	global $plugins;
	
	foreach ($plugins as $hook) 
	{
		if ($hook['hook'] == $a) 
		{
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
function createSideMenu($plugin,$page,$txt)
{
	$class="";
	
	if($_GET['plugin']==$plugin) 
	{
		$class="class='current'";
	}
	
	echo "<li><a href='plugins.php?plugin=".$plugin."&page=".$page."' ".$class." >";
	echo $txt;
	echo "</a></li>";
}

/*******************************************************
 * @function createNavTab
 * @param $url - URL for link
 * @param $txt - text to display on link
*/
function createNavTab($url,$txt)
{
	echo "<li><a href='".$url."' class='plugins' />";
	echo $txt;
	echo "</a></li>";
}
?>
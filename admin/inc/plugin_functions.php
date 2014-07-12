<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Plugin Functions
 *
 * @package GetSimple
 * @subpackage Plugin-Functions
 */


/**
 * Include any plugins, depending on where the referring 
 * file that calls it we need to set the correct paths. 
 *
 * @since  3.4
 * @uses  $live_plugins
*/
function loadPlugins(){
	GLOBAL $live_plugins, $pluginsLoaded;

	if (file_exists(GSPLUGINPATH)){
		$pluginfiles = getFiles(GSPLUGINPATH);
	} 

	// Check if data\other\plugins.xml exists 
	if (!file_exists(GSDATAOTHERPATH."plugins.xml")){
		create_pluginsxml();
	} 

	read_pluginsxml();        // get the live plugins into $live_plugins array

	if(!is_frontend()) create_pluginsxml();      // check that plugins have not been removed or added to the directory

	// load each of the plugins in global scope
	foreach ($live_plugins as $file=>$en) {
		# debugLog("plugin: $file" . " exists: " . file_exists(GSPLUGINPATH . $file) ." enabled: " . $en); 
		if ($en!=='true' || !file_exists(GSPLUGINPATH . $file)){
			if(!is_frontend() and get_filename_id() == 'plugins'){
		 		$apiback = get_api_details('plugin', $file);
		  		$response = json_decode($apiback);
		  		if ($response and $response->status == 'successful') {
					register_plugin( pathinfo_filename($file), $file, 'disabled', $response->owner, '', i18n_r('PLUGIN_DISABLED'), '', '');
		  		} else {
					register_plugin( pathinfo_filename($file), $file, 'disabled', 'Unknown', '', i18n_r('PLUGIN_DISABLED'), '', '');
		  		}
			} else {
				register_plugin( pathinfo_filename($file), $file, 'disabled', 'Unknown', '', i18n_r('PLUGIN_DISABLED'), '', '');
			}  
		}
	}

	$pluginsLoaded = true;	// does anyone use this ?
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
 * @param $active bool default=null, sets plugin active | inactive else toggle
 */
function change_plugin($name,$active=null){
	global $live_plugins;   

	if (isset($live_plugins[$name])){
		// set plugin active | inactive
		if(isset($active) and is_bool($active)) {
			$live_plugins[$name] = $active ? 'true' : 'false';	  		
			create_pluginsxml(true);
			return;
		}

		// else we toggle
		if ($live_plugins[$name]=="true"){
			$live_plugins[$name]="false";
		} else {
			$live_plugins[$name]="true";
		}

		create_pluginsxml(true);
	}
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
	if($data){
		$componentsec = $data->item;
		if (count($componentsec) != 0) {
			foreach ($componentsec as $component) {
			  $live_plugins[trim((string)$component->plugin)]=trim((string)$component->enabled);
			}
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
function create_pluginsxml($force=false){
	global $live_plugins;   
	$phpfiles = array();

	if (file_exists(GSPLUGINPATH)){
		$pluginfiles = getFiles(GSPLUGINPATH);
	}
	
	foreach ($pluginfiles as $fi) {
		if (lowercase(pathinfo($fi, PATHINFO_EXTENSION))=='php') {
			$phpfiles[] = $fi;
		}
	}
	
	if (!$force) {
		$livekeys = array_keys($live_plugins);
		if (count(array_diff($livekeys, $phpfiles))>0 || count(array_diff($phpfiles, $livekeys))>0) {
	  		$force = true;
		}
	}
	
	if ($force) {
		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>'); 
		foreach ($phpfiles as $fi) {
			$plugins = $xml->addChild('item');  
			$p_note  = $plugins->addChild('plugin');
			$p_note->addCData($fi);
			$p_note  = $plugins->addChild('enabled');
			
			if (isset($live_plugins[(string)$fi])){
				$p_note->addCData($live_plugins[(string)$fi]);     
			} else {
				$p_note->addCData('false'); 
			} 
		}

		XMLsave($xml, GSDATAOTHERPATH."plugins.xml");  
		read_pluginsxml();
	}
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
  
	$bt             = debug_backtrace();
	$shift          = count($bt) - 4;	// plugin name should be  
	$caller         = array_shift($bt);
	$realPathName   = pathinfo_filename($caller['file']);
	$realLineNumber = $caller['line'];

	while ($shift > 0) {
		 $caller = array_shift($bt);
		 $shift--;
	}

	$pathName = pathinfo_filename($caller['file']);

	if ((isset ($live_plugins[$pathName.'.php']) && $live_plugins[$pathName.'.php'] == 'true') || $shift < 0 ){
		if ($realPathName != $pathName) {
			$pathName   = $realPathName;
			$lineNumber = $realLineNumber;
		} else {
			$lineNumber = $caller['line'];
		}
		
		$plugins[] = array(
			'hook'     => $hook_name,
			'function' => $added_function,
			'args'     => (array) $args,
			'file'     => $pathName.'.php',
			'line'     => $caller['line']
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

function createSideMenu($id, $txt, $action = null, $always = true){
	$current = false;
	if (isset($_GET['id']) && $_GET['id'] == $id && (!$action || isset($_GET[$action]))) {
		$current = true;
	}
	if ($always || $current) {
		echo '<li id="sb_'.$id.'" class="plugin_sb"><a href="load.php?id='.$id.($action ? '&amp;'.$action : '').'" '.($current ? 'class="current"' : '').' >'.$txt.'</a></li>';
	}
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
 * @param string $klass class to add to a element
 */
function createNavTab($tabname, $id, $txt, $action = null) {
	global $plugin_info;
	$current = false;
	if (basename($_SERVER['PHP_SELF']) == 'load.php') {
		$plugin_id = @$_GET['id'];
		if ($plugin_info[$plugin_id]['page_type'] == $tabname) $current = true;
	}
	echo '<li id="nav_'.$id.'" class="plugin_tab"><a href="load.php?id='.$id.($action ? '&amp;'.$action : '').'" '.($current ? 'class="current"' : '').' >'.$txt.'</a></li>';
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
		'name'        => $name,
		'version'     => $ver,
		'author'      => $auth,
		'author_url'  => $auth_url,
		'description' => $desc,
		'page_type'   => $type,
		'load_data'   => $loaddata
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
function add_filter($filter_name, $added_function, $args = array()) {
  	global $filters;
	global $live_plugins;   

	$bt       = debug_backtrace();
	$caller   = array_shift($bt);
	$pathName = pathinfo_filename($caller['file']);

	$filters[] = array(
		'filter'   => $filter_name,
		'function' => $added_function,
		'active'   => false,
		'args'     => (array) $args		
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
			$key = array_search($script,$filters);
			if (!$filters[$key]['active']) {
				$filters[$key]['active'] = true;
				$data = call_user_func_array($filter['function'], array($data));
				$filters[$key]['active'] = false;
			}
		}
	}
	return $data;
}


/* ?> */

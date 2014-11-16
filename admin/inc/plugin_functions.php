<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Plugin Functions
 *
 * @package GetSimple
 * @subpackage Plugin-Functions
 */

/**
 * 
 * 	global array for storing all plugins greated from plugins registerplugin() call
 *	    $plugin_info[$id] = array(
 *	       'name'        => $name,
 *	       'version'     => $ver,
 *	       'author'      => $auth,
 *	       'author_url'  => $auth_url,
 *	       'description' => $desc,
 *	       'page_type'   => $type,
 *	       'load_data'   => $loaddata
 *	    );
 *
 *
 *	global array for storing action hook callbacks
 *	    $plugins[] = array(
 *	       'hook'     => hookname,
 *	       'function' => callback function name,
 *	       'args'     => (array) arguments to pass to function,
 *	       'file'     => caller filename obtained from backtrace,
 *	       'line'     => caller line obtained from backtrace,
 *	    );
 *
 *
 *	global array for storing filter callbacks
 *	$filters[] = array(
 *	    'filter'   => filtername,
 *	    'function' => callback function name,
 *	    'args'     => (array) arguments for callback,
 *	    'active'   => (bool) is processing anti-self-looping flag
 *	);
 *
*/

/**
 * Include any plugins, depending on where the referring 
 * file that calls it we need to set the correct paths. 
 *
 * @since  3.4
 * @uses  $live_plugins
*/
function loadPluginData(){
	if (file_exists(GSPLUGINPATH)){
		$pluginfiles = getFiles(GSPLUGINPATH);
	} 

	// Check if data\other\plugins.xml exists 
	if (!file_exists(GSDATAOTHERPATH."plugins.xml")){
		create_pluginsxml();
		registerInactivePlugins(get_filename_id() == 'plugins');
		return true;
	}

	read_pluginsxml();  // get the live plugins into $live_plugins array
	if(!is_frontend()) create_pluginsxml(get_filename_id() == 'plugins');  // only on backend check that plugin files have not changed, and regen
	
	registerInactivePlugins();
	return true;
}

/**
 * register the plugins that are not enabled
 * api checks are only done on plugins page
 *
 * @todo disabled plugins have a version of (str) 'disabled', should be 0 or null, leaving alone for now for legacy support
 *
 * @since 3.4
 * @uses $live_plugins;
 * @param  bool $apilookup lookup filename in api to get name and desc
 */
function registerInactivePlugins($apilookup = false){
	GLOBAL $live_plugins;
	// load plugins into $plugins_info

	foreach ($live_plugins as $file=>$en) {
		# debugLog("plugin: $file" . " exists: " . file_exists(GSPLUGINPATH . $file) ." enabled: " . $en); 
		if ($en!=='true' || !file_exists(GSPLUGINPATH . $file)){
			if($apilookup){
				// check api to get names of inactive plugins etc.
		 		$apiback  = get_api_details('plugin', $file);
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
}

/**
 * change_plugin
 * 
 * Enable/Disable a plugin
 *
 * @since 2.04
 * @uses $live_plugins
 *
 * @param str  $name pluginid
 * @param bool $active default=null, sets plugin active or inactive, default=toggle
 */
function change_plugin($name,$active=null){
	global $live_plugins;

	$name = pathinfo_filename($name).'.php'; // normalize to pluginid
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

		create_pluginsxml(true); // save change; @todo, currently reloads all files and recreates entire xml not just node, is wasteful
	}
}

/**
 * plugin_active
 * determine if a plugin is active
 *
 * @since 3.4
 * @param  string $pluginid
 * @return bool   returns true if active
 */
function plugin_active($pluginid){
	GLOBAL $live_plugins;
	return isset($live_plugins[$pluginid.'.php']) && ($live_plugins[$pluginid.'.php'] == 'true' || $live_plugins[$pluginid.'.php'] === true);
}


/**
 * read_pluginsxml
 * 
 * Read in the plugins.xml file and populate the $live_plugins array
 *
 * @since 2.04
 * @uses $live_plugins
 * @param obj $data pass in xml data instead of using plugins.xml file load
 *
 */
function read_pluginsxml($data = null){
  	global $live_plugins;   
   
	if(!$data) $data = getXML(GSDATAOTHERPATH . "plugins.xml");
	if($data){
   		$live_plugins= array(); // clean live_plugins
		$pluginitem = $data->item;
		if (count($pluginitem) != 0) {
			foreach ($pluginitem as $plugin) {
			  $live_plugins[trim((string)$plugin->plugin)]=trim((string)$plugin->enabled);
			}
		}

		return true;
	} 
}


/**
 * create_pluginsxml
 * 
 * Read in each plugin php file and add it to the plugins.xml file.
 * read_pluginsxml() is called to populate $live_plugins
 *
 * Does nothing if force is false and no file diff found
 * @todo  if this gets called before live plugins is loaded it will wipe your activated plugin state
 *
 * @since 2.04
 * @uses $live_plugins
 *
 * @param  bool $force force an update of plugins.xml regardless of diff check
 *
 */
function create_pluginsxml($force=false){
	GLOBAL $live_plugins;

	$pluginfiles = array();
	$success     = false;

	if (file_exists(GSPLUGINPATH)){
		$pluginfiles = getFiles(GSPLUGINPATH,'php');
	}
	else return; // plugin files path issue

	if (!$force) {
		$livekeys = array_keys($live_plugins);
		// check for file diff and use force to regen if count differs @todo better detection than just count
		if (count(array_diff($livekeys, $pluginfiles))>0 || count(array_diff($pluginfiles, $livekeys))>0) {
	  		$force = true;
		}
	}

	// create plugins.xml if missing or updating
	if ($force) {
		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
		foreach ($pluginfiles as $fi) {
			$plugins = $xml->addChild('item');
			$p_note  = $plugins->addChild('plugin');
			$p_note->addCData($fi);
			$p_note  = $plugins->addChild('enabled');

			// check live_plugins and set enables
			if (isset($live_plugins[(string)$fi])){
				$p_note->addCData($live_plugins[(string)$fi]);
			} else {
				$p_note->addCData('false');
			}
		}

		$success = XMLsave($xml, GSDATAOTHERPATH."plugins.xml");
		read_pluginsxml($xml);
	}

	return $success;
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
	$shift          = count($bt) - 3;	// plugin name should be  
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
	if (basename(getScriptFile()) == 'load.php') {
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
 * @param string $loaddata Optional, default is null. This is the callback funcname to run on load.php
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

	if(!$filters) return $data;
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

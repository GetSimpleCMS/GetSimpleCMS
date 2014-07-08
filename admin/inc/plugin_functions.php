<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Plugin Functions
 *
 * @package GetSimple
 * @subpackage Plugin-Functions
 */

$plugins_info = array();  // global array for storing all plugins greated from plugins registerplugin() call
/*
    $plugin_info[$id] = array(
       'name'        => $name,
       'version'     => $ver,
       'author'      => $auth,
       'author_url'  => $auth_url,
       'description' => $desc,
       'page_type'   => $type,
       'load_data'   => $loaddata
    );
*/

$live_plugins = array();  // global array for storing active plugins

$plugins = array();  // global array for storing action hook callbacks
/*
    $plugins[] = array(
       'hook'     => hookname,
       'function' => callback function name,
       'args'     => (array) arguments to pass to function,
       'file'     => caller filename obtained from backtrace,
       'line'     => caller line obtained from backtrace,
    );
*/

$filters = array();  // global array for storing filter callbacks
/*
    $filters[] = array(
        'filter'   => filtername,
        'function' => callback function name,
        'args'     => (array) arguments for callback,
        'active'   => (bool) is processing anti-self-looping flag
    );
*/

$GS_scripts = array();  // global array for storing queued script assets
/*
    $GS_scripts[$handle] = array(
        'name'      => handle,
        'src'       => src file,
        'ver'       => version,
        'in_footer' => in_footer,
        'where'     => 0 bitflag,
        'load'      => (bool) is queued,
        'queue'     => array of additional assets to queue
    );
*/
$GS_styles = array();  // glboal array for storing queued stylesheet assets
/*
    $GS_styles[$handle] = array(
        'name'      => handle,
        'src'       => src file,
        'ver'       => version,
        'media'     => style media eg. screen,print,
        'where'     => 0 bitflag,
        'load'      => (bool) is queued,
        'queue'     => array of additional assets to queue
        
    );
*/


// constants
// frontend, backend or both for script location load flags
define('GSFRONT',1);
define('GSBACK' ,2);
define('GSBOTH' ,3);

$ASSETURL=suggest_site_path(false,true);

$GS_script_assets = array(); // defines asset scripts
$GS_style_assets  = array();  // defines asset styles

$GS_asset_objects = array(); // holds asset js object names
$GS_asset_objects['jquery']    = 'jQuery';
$GS_asset_objects['jquery-ui'] = 'jQuery.ui'; 

$getsimple_ver     = GSVERSION;
$jquery_ver        = '1.9.0';
$jqueryui_ver      = '1.10.0';
$font_awesome_ver  = '4.0.3';
$fancybox_ver      = '2.0.4';
$scrolltofixed_ver = '0.0.1';
$codemirror_ver	   = '3.2.0';
$ckeditor_ver      = '4.4.1';

// long form
// $GS_script_assets = array(
// 	'jquery' => array(
// 		'cdn' => array(
// 			'url' => '//ajax.googleapis.com/ajax/libs/jquery/'.$jquery_ver.'/jquery.min.js',
// 			'ver' => $jquery_ver	
// 		),
// 		'local' => array(
// 			'url' => $ASSETURL.$GSADMIN.'/template/js/jquery/jquery-'.$jquery_ver.'.min.js',
// 			'ver' => $jquery_ver		
// 		)
// 	)	
// )

/**
 * Core assets
 */

// core
$GS_script_assets['getsimple']['local']['url']     = $ASSETURL.$GSADMIN.'/template/js/jquery.getsimple.js';
$GS_script_assets['getsimple']['local']['ver']     = $getsimple_ver;

// lazyload (lazy loading assets js/css)
$GS_script_assets['lazyload']['local']['url']      = $ASSETURL.$GSADMIN.'/template/js/lazyload.js';
$GS_script_assets['lazyload']['local']['ver']      = $getsimple_ver;

// gstree (collpaseble heirarchy table tree) 
$GS_script_assets['gstree']['local']['url']        = $ASSETURL.$GSADMIN.'/template/js/jquery-gstree.js';
$GS_script_assets['gstree']['local']['ver']        = $getsimple_ver;

// spin (ajax spinners)
$GS_script_assets['spin']['local']['url']          = $ASSETURL.$GSADMIN.'/template/js/spin.js';
$GS_script_assets['spin']['local']['ver']          = $getsimple_ver;

// dropzone (ajax/html uploader w drag and drop)
$GS_script_assets['dropzone']['local']['url']      = $ASSETURL.$GSADMIN.'/template/js/dropzone.js';
$GS_script_assets['dropzone']['local']['ver']      = $getsimple_ver;

// jcrop
$GS_script_assets['jcrop']['local']['url']        = $ASSETURL.$GSADMIN.'/template/js/jcrop/jquery.Jcrop.min.js';
$GS_script_assets['jcrop']['local']['ver']        = $getsimple_ver;
 $GS_style_assets['jcrop']['local']['url']        = $ASSETURL.$GSADMIN.'/template/js/jcrop/jquery.Jcrop.min.css';
 $GS_style_assets['jcrop']['local']['ver']        = $getsimple_ver;


/**
 * External assets
 */

// jquery
$GS_script_assets['jquery']['cdn']['url']          = '//ajax.googleapis.com/ajax/libs/jquery/'.$jquery_ver.'/jquery.min.js';
$GS_script_assets['jquery']['cdn']['ver']          = $jquery_ver;
$GS_script_assets['jquery']['local']['url']        = $ASSETURL.$GSADMIN.'/template/js/jquery/jquery-'.$jquery_ver.'.min.js';
$GS_script_assets['jquery']['local']['ver']        = $jquery_ver;

// jquery-ui
$GS_script_assets['jquery-ui']['cdn']['url']       = '//ajax.googleapis.com/ajax/libs/jqueryui/'.$jqueryui_ver.'/jquery-ui.min.js';
$GS_script_assets['jquery-ui']['cdn']['ver']       = $jqueryui_ver;
$GS_script_assets['jquery-ui']['local']['url']     = $ASSETURL.$GSADMIN.'/template/js/jqueryui/js/jquery-ui-'.$jqueryui_ver.'.custom.min.js';
$GS_script_assets['jquery-ui']['local']['ver']     = $jqueryui_ver;
 $GS_style_assets['jquery-ui']['local']['url']     =  $ASSETURL.$GSADMIN.'/template/js/jqueryui/css/custom/jquery-ui-'.$jqueryui_ver.'.custom.min.css';
 $GS_style_assets['jquery-ui']['local']['ver']     =  $jqueryui_ver;

// scrolltofixed
$GS_script_assets['scrolltofixed']['local']['url'] = $ASSETURL.$GSADMIN.'/template/js/jquery-scrolltofixed.js';
$GS_script_assets['scrolltofixed']['local']['ver'] = $scrolltofixed_ver;

// codemirror
$GS_script_assets['codemirror']['local']['url']    = $ASSETURL.$GSADMIN.'/template/js/codemirror/lib/codemirror-compressed.js';
$GS_script_assets['codemirror']['local']['ver']    = $codemirror_ver;
 $GS_style_assets['codemirror']['local']['url']    = $ASSETURL.$GSADMIN.'/template/js/codemirror/lib/codemirror.min.css';
 $GS_style_assets['codemirror']['local']['ver']    = $codemirror_ver;

// fancybox
$GS_script_assets['fancybox']['local']['url']      = $ASSETURL.$GSADMIN.'/template/js/fancybox/jquery.fancybox.pack.js';
$GS_script_assets['fancybox']['local']['ver']      = $fancybox_ver;
 $GS_style_assets['fancybox']['local']['url']      = $ASSETURL.$GSADMIN.'/template/js/fancybox/jquery.fancybox.css';
 $GS_style_assets['fancybox']['local']['ver']      = $fancybox_ver;
 // deprecated
 $GS_style_assets['fancybox-css']['local']['url']  = $GS_style_assets['fancybox']['local']['url'];
 $GS_style_assets['fancybox-css']['local']['ver']  = $GS_style_assets['fancybox']['local']['ver'];

// font-awesome icons
 $GS_style_assets['font-awesome']['cdn']['url']    = '//netdna.bootstrapcdn.com/font-awesome/'.$font_awesome_ver.'/css/font-awesome.min.css';
 $GS_style_assets['font-awesome']['cdn']['ver']    = $font_awesome_ver;
 $GS_style_assets['font-awesome']['local']['url']  = $ASSETURL.$GSADMIN.'/template/css/font-awesome.min.css';
 $GS_style_assets['font-awesome']['local']['ver']  = $font_awesome_ver;

// ckeditor
$GS_script_assets['ckeditor']['cdn']['url']        = '//cdn.ckeditor.com/'.$ckeditor_ver.'/full/ckeditor.js';
$GS_script_assets['ckeditor']['cdn']['ver']        = $ckeditor_ver;
$GS_script_assets['ckeditor']['local']['url']      = $ASSETURL.$GSADMIN.'/template/js/ckeditor/ckeditor.js';
$GS_script_assets['ckeditor']['local']['ver']      = $ckeditor_ver;

// gs codeeditor
$GS_script_assets['gscodemirror']['local']['url']  = $ASSETURL.$GSADMIN.'/template/js/codemirror.getsimple.js';
$GS_script_assets['gscodemirror']['local']['ver']  = $getsimple_ver;
$GS_script_assets['gscodemirror']['queue']['script']  = array('codemirror');
$GS_script_assets['gscodemirror']['queue']['style']   = array('codemirror');


/**
 * Register shared javascript/css scripts for loading into the header
 */

$infooter = false;
$nocdn = getDef('GSNOCDN',true);

preRegisterScript('jquery',       '', !$nocdn , false);
preRegisterScript('jquery-ui',    '', !$nocdn , false);
preRegisterScript('font-awesome', '', !$nocdn , $infooter);
preRegisterScript('getsimple',    '',   false , $infooter);
preRegisterScript('lazyload',     '',   false , $infooter);
preRegisterScript('spin',         '',   false , $infooter);
preRegisterScript('jcrop',        '',   false , $infooter);
preRegisterScript('dropzone',     '',   false , $infooter);
preRegisterScript('gstree',       '',   false , $infooter);
preRegisterScript('ckeditor',     '',   false , $infooter); // cdn disabled, http://cdn.ckeditor.com/, requires explicit setting of config and plugin paths to local
preRegisterScript('codemirror',   '',   false , $infooter);
preRegisterScript('fancybox',     '',   false , $infooter);
preRegisterScript('scrolltofixed','',   false , $infooter);

// gs aliases
preRegisterScript('gshtmleditor', $GS_script_assets['ckeditor'],     false , $infooter);
preRegisterScript('gscodeeditor', $GS_script_assets['gscodemirror'], false , $infooter);

preRegisterScript('gscrop',       $GS_script_assets['jcrop'],        false , $infooter);
preRegisterScript('gsuploader',   $GS_script_assets['dropzone'],     false , $infooter);

preRegisterStyle('font-awesome',  '', !$nocdn , 'screen');
preRegisterStyle('codemirror',    '',   false , 'screen');
preRegisterStyle('jcrop',         '',   false , 'screen');
preRegisterStyle('fancybox-css',  '',   false , 'screen'); // DEPRECATED legacy , unmatched id
preRegisterStyle('fancybox',      '',   false , 'screen');
preRegisterStyle('jquery-ui',     '',   false , 'screen');

/**
 * Queue our scripts and styles for the backend
 */
queue_script('jquery'        , GSBACK);
queue_script('jquery-ui'     , GSBACK);
queue_script('getsimple'     , GSBACK);
queue_script('lazyload'      , GSBACK);
queue_script('spin'          , GSBACK);
queue_script('gstree'        , GSBACK);
queue_script('fancybox'      , GSBACK);
queue_script('scrolltofixed' , GSBACK);

queue_style('fancybox'       , GSBACK);
queue_style('jquery-ui'      , GSBACK);
// queue_style('jquery-ui-theme', GSBACK); // unused, reserved for custom GS jquery ui theme
queue_style('font-awesome'   , GSBACK);

/**
 * Include any plugins, depending on where the referring 
 * file that calls it we need to set the correct paths. 
*/
if (file_exists(GSPLUGINPATH)){
	$pluginfiles = getFiles(GSPLUGINPATH);
} 

$pluginsLoaded = false;


// Check if data\other\plugins.xml exists 
if (!file_exists(GSDATAOTHERPATH."plugins.xml")){
	create_pluginsxml();
} 

read_pluginsxml();        // get the live plugins into $live_plugins array

if(!is_frontend()) create_pluginsxml();      // check that plugins have not been removed or added to the directory

// load each of the plugins
foreach ($live_plugins as $file=>$en) {
	$pluginsLoaded=true;
	# debugLog("plugin: $file" . " exists: " . file_exists(GSPLUGINPATH . $file) ." enabled: " . $en); 
	if ($en=='true' && file_exists(GSPLUGINPATH . $file)){
		require_once(GSPLUGINPATH . $file);
	} else {
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

/**
 * ------------------------------------------------------------------------------
 * BEGING FUNCTIONS
 * ------------------------------------------------------------------------------
 */

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


/**
 * preregister scripts
 * helper for using global arrays to build script asset registration
 * 
 * @since 3.4
 * @param  str  $id     id of script asset
 * @param  boolean $CDN    use cdn if available
 * @param  boolean $footer put in footer
 * @return bool
 */
function preRegisterScript($id,$config = array(),$CDN = false,$footer = false){
	GLOBAL $GS_script_assets;
	if(!$config && isset($GS_script_assets[$id])) $config = $GS_script_assets[$id];
	if(!$config) return;
	$queue = isset($config['queue']) ? $config['queue'] : null;
	if($CDN && isset($config['cdn'])) return register_script($id, $config['cdn']['url'], '', $footer, $queue); // no version for CDN benefits
	else return register_script($id, $config['local']['url'], $config['local']['ver'], $footer, $queue);
}

/**
 * Register Script
 *
 * Register a script to include in Themes
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script
 * @param string $src location of the src for loading
 * @param string $ver script version
 * @param boolean $in_footer load the script in the footer if true
 * @param array $queue array of script or style assets to auto queue
 */
function register_script($handle, $src, $ver, $in_footer = false, $queue = null){
	global $GS_scripts;
	$GS_scripts[$handle] = array(
		'name'      => $handle,
		'src'       => $src,
		'ver'       => $ver,
		'in_footer' => $in_footer,
		'where'     => 0,
		'load'      => false,
		'queue'     => $queue
	);
}

/**
 * De-Register Script
 *
 * Deregisters a script
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script to remove
 */
function deregister_script($handle){
	global $GS_scripts;
	if (array_key_exists($handle, $GS_scripts)){
		unset($GS_scripts[$handle]);
	}
}

/**
 * Queue Script
 *
 * Queue a script for loading
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script to load
 */
function queue_script($handle,$where){
	global $GS_scripts;
	if (array_key_exists($handle, $GS_scripts)){
		// load items queue
		if(isset($GS_scripts[$handle]['queue'])){
			$config = $GS_scripts[$handle]['queue'];
			if(isset($config['script'])) array_map('queue_script',$config['script'],array_fill(0,count($config['script']),$where));
			if(isset($config['style']))  array_map('queue_style',$config['style'],array_fill(0,count($config['style']),$where));
		}

		$GS_scripts[$handle]['load']  = true;
		$GS_scripts[$handle]['where'] = $GS_scripts[$handle]['where'] | $where;
	}
}

/**
 * De-Queue Script
 *
 * Remove a queued script
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script to load
 */
function dequeue_script($handle, $where){
	global $GS_scripts;
	if (array_key_exists($handle, $GS_scripts)){
		$GS_scripts[$handle]['load']  = false;
		$GS_scripts[$handle]['where'] = $GS_scripts[$handle]['where'] & ~ $where;
	}
}

/**
 * Get Scripts for front end
 *
 * @since 3.1 *
 * @param boolean $footer Load only script with footer flag set
 */
function get_scripts_frontend($footer = false){
	getScripts(GSFRONT,$footer);
}

/**
 * Get Scripts for backend
 *
 * @since 3.1 *
 * @param boolean $footer Load only script with footer flag set
 */
function get_scripts_backend($footer = false){
	getScripts(GSBACK,$footer);
}

/**
 * Get Scripts
 *
 * Echo and load scripts via queue depending on if in footer and on front or back
 *
 * @since 3.4
 * @uses $GS_scripts
 *
 * @param int $facing GSBACK or GSFRONT constant
 * @param boolean $footer Load only script with footer flag set
 */
function getScripts($facing = GSBACK, $footer = false){
	global $GS_scripts;
	if (!$footer){
		$facing === GSBACK ? get_styles_backend() : get_styles_frontend();
	}

	// debugLog($GS_scripts);
	foreach ($GS_scripts as $script){
		if ($script['load'] == true && ($script['where'] & $facing) ){
			if($footer !== $script['in_footer']) continue;
			echo '<script src="'.$script['src'].( !empty($script['ver']) ? '?v='.$script['ver'] : '' ) . '"></script>';
			cdn_fallback($script);	
		}
	}	
}


/**
 * Add javascript for cdn fallback to local
 * get_scripts_backend helper
 * @param  array $script gsscript array
 */
function cdn_fallback($script){
	GLOBAL $GS_script_assets, $GS_asset_objects;	
	if (getDef('GSNOCDN',true)) return; // if nocdn skip
	if($script['name'] == 'jquery' || $script['name'] == 'jquery-ui'){
		echo "<script>";
		echo "window.".$GS_asset_objects[$script['name']]." || ";
		echo "document.write('<!-- CDN FALLING BACK --><script src=\"".$GS_script_assets[$script['name']]['local']['url'].'?v='.$GS_script_assets[$script['name']]['local']['ver']."\"><\/script>');";
		echo "</script>";
	}					
}

/**
 * Queue Style
 *
 * Queue a Style for loading
 *
 * @since 3.1
 * @uses $GS_styles
 *
 * @param string $handle name for the Style to load
 */
function queue_style($handle,$where = 1){
	global $GS_styles;
	if (array_key_exists($handle, $GS_styles)){

		// load items queue
		if(isset($GS_scripts[$handle]['queue'])){
			$config = $GS_scripts[$handle]['queue'];
			if(isset($config['style']))  array_map('queue_style',$config['style'],array_fill(0,count($config['style']),$where));
		}

		$GS_styles[$handle]['load'] = true;
		$GS_styles[$handle]['where'] = $GS_styles[$handle]['where'] | $where;
	}
}

/**
 * De-Queue Style
 *
 * Remove a queued Style
 *
 * @since 3.1
 * @uses $GS_styles
 *
 * @param string $handle name for the Style to load
 */
function dequeue_style($handle,$where){
	global $GS_styles;
	if (array_key_exists($handle, $GS_styles)){
		$GS_styles[$handle]['load'] = false;
		$GS_styles[$handle]['where'] = $GS_styles[$handle]['where'] & ~$where;
	}
}

/**
 * preregister style
 * helper for using global arrays to build script asset registration
 * 
 * @since 3.4
 * @param  str  $id     id of style asset
 * @param  boolean $CDN    use cdn if available
 * @param  boolean $footer put in footer
 * @return bool
 */
function preRegisterStyle($id,$config = array(), $CDN = false, $media = 'screen'){
	GLOBAL $GS_style_assets;
	if(!$config && isset($GS_style_assets[$id])) $config = $GS_style_assets[$id];
	if(!$config) return;
	$queue = isset($config['queue']) ? $config['queue'] : null;
	if($CDN && isset($config['cdn'])) return register_style($id, $config['cdn']['url'], '', $media,$queue); // no version for CDN benefits
	else return register_style($id, $config['local']['url'], $config['local']['ver'], $media,$queue);
}


/**
 * Register Style
 *
 * Register a Style to include in Themes
 *
 * @since 3.1
 * @uses $GS_styles
 *
 * @param string $handle name for the Style
 * @param string $src location of the src for loading
 * @param string $ver Style version
 * @param string $media the media for this stylesheet
 * @param array $queue array of style assets to auto queue
 */
function register_style($handle, $src, $ver, $media, $queue = null){
	global $GS_styles;
	$GS_styles[$handle] = array(
		'name'  => $handle,
		'src'   => $src,
		'ver'   => $ver,
		'media' => $media,
		'where' => 0,
		'load'	=> false,
		'queue' => $queue
	);
}

/**
 * Get Styles Frontend
 * @since 3.1
 */
function get_styles_frontend(){
	getStyles(GSFRONT);
}

/**
 * Get Styles Backend
 * @since 3.1
  */
function get_styles_backend(){
	getStyles(GSBACK);
}


/**
 * Get Styles Backend
 *
 * Echo and load Styles on Front or Back
 *
 * @since 3.4
 * @uses $GS_styles
 *
 */
function getStyles($facing = GSBACK){
	global $GS_styles;
	foreach ($GS_styles as $style){
		if ($style['where'] & $facing ){
				if ($style['load'] == true){
					echo '<link href="'.$style['src']. ( !empty($script['ver']) ? '?v='.$script['ver'] : '' ) . '" rel="stylesheet" media="'.$style['media'].'">';
				}
		}
	}
}

/* ?> */

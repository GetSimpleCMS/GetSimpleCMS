<?php
/*
Plugin Name: Small Plugin Toolkit
Description: Adds additional hooks and functions for plugins to access.
Version: 1.5
Author: Jason Dixon
Author URI: http://internetimagery.com
*/

# get correct id for plugin
$thisfile=basename(__FILE__, '.php');

# register plugin
register_plugin(
	$thisfile, //Plugin id
	'Small Plugin Toolkit', 	//Plugin name
	'1.5', 		//Plugin version
	'Jason Dixon',  //Plugin author
	'http://internetimagery.com', //author website
	'<SPT>Dependency for some plugins.<SPT>', //Plugin description
	'', //page type - on which admin tab to display
	''  //main function (administration)
	);


/*			=======HOOKS YOU CAN USE=======
*		edit-above-content							= Above the default WYSISYG editor.
*		development-settings							= In the development section of the settings menu.
*		register-data								= Registers a section of the database to you.
*		dev-anytab									= Places Tab into development mode.

			=======FILTERS YOU CAN USE=======

*		(actionname)-exec							= Executes a function if the user inputs :["data"]:

			=======FUNCTIONS TO USE=======
*	Search:
		Toolkit_load($item);							= Loads from your database values that match the supplied key.
		Toolkit_template($token, $template, $theme);	= Searches specific template file for tokens.
		Toolkit_token();							= Provides a token to search for in the template.
		Toolkit_page($search);							= Searches all the pages for a specific text.
		Toolkit_array($array,$key);						= Searches for key name in multidimensional array.
		Toolkit_print($array);							= Print out an array.
*/


// hooks used
add_action('settings-website','SPT_save_mode');
add_action('plugin-hook','SPT_detect_plugs');
add_filter('content','SPT_filter_content');
$SPT_HOOKLIST['common'] = false;
$SPT_HOOKLIST['edit-extras'] = true;
$SPT_HOOKLIST['edit-content'] = false;
$SPT_HOOKLIST['settings-website-extras'] = true;
$SPT_HOOKLIST['header-body'] = false;
$SPT_HOOKLIST['footer'] = true;
$SPT_HOOKLIST['index-posttemplate'] = true;
$SPT_HOOKLIST['pages-sidebar'] = true;
$SPT_HOOKLIST['backups-sidebar'] = true;
$SPT_HOOKLIST['files-sidebar'] = true;
$SPT_HOOKLIST['plugins-sidebar'] = true;
$SPT_HOOKLIST['settings-sidebar'] = true;
$SPT_HOOKLIST['support-sidebar'] = true;
$SPT_HOOKLIST['theme-sidebar'] = true;
$SPT_HOOKLIST['nav-tab'] = true;
$SPT_HOOKLIST['admin-pre-header'] = false;


//			=======LANGUAGE TEXT=======
switch ($LANG){
	case '':// language goes here ie: 'en_US'
	$SPT_DEVTEXT['banner']			=	'';
	$SPT_DEVTEXT['mode']			=	'';
	$SPT_DEVTEXT['blurb']			=	'';
	$SPT_DEVTEXT['singular']		=	'';
	$SPT_DEVTEXT['plural']			=	'';
	$SPT_DEVTEXT['disable']			=	'';
	break;

	default:// Default language: en_US
	$SPT_DEVTEXT['banner']			=	'You can turn it off in the settings page.';
	$SPT_DEVTEXT['mode']			=	'Development Mode';
	$SPT_DEVTEXT['blurb']			=	'More customization options.';
	$SPT_DEVTEXT['singular']		=	'A plugin is using this toolkit:';
	$SPT_DEVTEXT['plural']			=	'Plugins using this toolkit:';
	$SPT_DEVTEXT['disable']			=	'Do not disable.';
	break;
}



//			=======DEVELOPMENT MODE=======

//	Check the status of Development Mode.
if(isset($_POST['DEVMODE'])&&$_POST['DEVMODE'] == 'checked'){
	define('DEVMODE', true);}
else if((string)$dataw->DEVMODE == 'checked'){ define('DEVMODE', true);}
else { define('DEVMODE', false);}


/**
 * Creates DEVELOPMENT MODE banner.
 *
 * @access private
 * @return void
 */
function SPT_header_body(){
	global $SPT_DEVTEXT;
	$text = i18n_r('VIEWING').' '.$SPT_DEVTEXT['mode'].' - '.$SPT_DEVTEXT['blurb'].' '.$SPT_DEVTEXT['banner'];
	echo '<div class="DEVMODE" style="text-align: center; background-color: #9F2C04; border-bottom: 1px solid #5d9589; left:0px; right:0px; top:0px; position:fixed; display:'; if(DEVMODE){echo 'visible';}else{echo 'none';} echo ';">'.$text.'</div>';
	if(DEVMODE){echo '<br>';}
}


/**
 * Add Development section to settings menu.
 *
 * @access public
 * @return void
 */
function SPT_settings_website_extras(){
	global $SPT_DEVTEXT;
	if(isset($_POST['submitted'])){
		if(isset($_POST['DEVMODE'])){
			$check = $_POST['DEVMODE'];}
		else{ $check = '';}}
	else if(DEVMODE){
		$check = 'checked';}
	else {$check = '';}?>
<h3><?php echo $SPT_DEVTEXT['mode']; ?></h3>
<label><input id="DEVCHK" type="checkbox" name="DEVMODE" value="checked" <?php echo $check; ?>>&nbsp <?php echo i18n_r('ENABLE').' '.$SPT_DEVTEXT['mode'];?> - <b style="font-weight:100"><?php echo $SPT_DEVTEXT['blurb'];?></b></label>
<div class="DEVMODE">
<?php exec_action('development-settings'); ?>
</div>
<script>
function show_dev(dur){if($("#DEVCHK").attr("checked")){$(".DEVMODE").stop().slideDown(dur);} else {$(".DEVMODE").slideUp(dur);}}
$(document).ready(function(){ show_dev(0);
$("#DEVCHK").click(function(){ show_dev(200);})
})</script><?php
}


/**
 * Save Development mode status to file.
 *
 * @access private
 * @return void
 */
function SPT_save_mode(){
	if(isset($_POST['DEVMODE']) && $_POST['DEVMODE']=='checked'){
		$savedata = 'checked';}
	else { $savedata = 'nope';}
	global $xmls;
	$note = $xmls->addChild('DEVMODE');
		$note->addCData($savedata);
}



//			=======DATABASE=======

// Pull out the data file information.
define('SPT_CONFIG',GSDATAOTHERPATH.'shared_config.data');
$SPT_XML = SPT_legacy('xml');
if(file_exists(SPT_CONFIG)){
	$SPT_filecontents = file_get_contents(SPT_CONFIG);
	$SPT_CONFIG = json_decode($SPT_filecontents,true);}
else{
	if($SPT_XML){
		$SPT_CONFIG = $SPT_XML; }
	else {
		$SPT_CONFIG = array();}}
$SPT_DATA_REGISTER = array();

/**
 * Register plugins data.
 *
 * @access private
 * @return void
 */
function SPT_data_register(){
	global $plugins, $SPT_CONFIG, $SPT_DATA_REGISTER;
	foreach ($plugins as $check)	{
		if ($check['hook'] == 'register-data') {
			$var = $check['function'];
			if(preg_match('/^[a-zA-Z0-9_]+$/',$var)&&!isset($GLOBALS[$var])){
				$file = explode('.', $check['file']);
				global ${$var};
				if(isset($SPT_CONFIG[$file[0]][$var])){
					$SPT_CONFIG[$file[0]][$var];}
				else {
					$SPT_CONFIG[$file[0]][$var] = array();}
				${$var} = $SPT_CONFIG[$file[0]][$var];
				SPT_check_post($var);
				$SPT_DATA_REGISTER[$file[0]][] = $var;}}
}}

/**
 * Save config data to file.
 *
 * @access private
 * @return void
 */
function SPT_store_database(){
	global $SPT_DATA_REGISTER, $SPT_CONFIG;
	foreach($SPT_DATA_REGISTER as $user=>$list){
		foreach($list as $reg){
			global ${$reg};
			$SPT_CONFIG[$user][$reg] = ${$reg};}}
	$data = json_encode($SPT_CONFIG);
	file_put_contents(SPT_CONFIG, $data);
}
function SPT_index_posttemplate(){
	SPT_store_database();}
function SPT_footer(){
	SPT_store_database();}

/**
 * Check POST data for Simple-form.
 *
 * @access private
 * @return void
 */
function SPT_check_post(mixed $data){// If simple form was submitted. Update the database.
	global ${$data};
	if(isset($_POST[$data.'-submit'])){
		$new_data = array();
		foreach($_POST as $k=>$v){
			if(str_contains($k, $data.'-')){
				$take = str_replace($data.'-', '', $k);
				$new_data[$take] = $v;}}
	${$data}['FORM'] = $new_data;
	}
}


/**
 * Toolkit_load function.
 *
 * Loads the requested key value from the database.
 *
 * @access public
 * @param bool $item (default: false)
 * @return void
 */
function Toolkit_load($item=false){// Load requested information.
	global $SPT_DATA_REGISTER;
	$user = SPT_get_user();
	$empty_array = array();
	if(isset($SPT_DATA_REGISTER[$user])){
		foreach($SPT_DATA_REGISTER[$user] as $check){
		global ${$check};
		if($item===false){
			$full_array = ${$check};}
		else{
			$full_array = Toolkit_array(${$check},$item);}
		if(is_array($full_array)){$empty_array = array_merge($full_array);}
		else if($full_array){$empty_array[] = $full_array;}
		}}
	return $empty_array;
}


/**
 * Toolkit_array function.
 *
 * Searches for a key name in a multidimensional array.
 *
 * @access public
 * @param mixed $array
 * @param mixed $item
 * @return void
 */
function Toolkit_array(mixed $array,mixed $item){// Search a multidimensional array for key name. Return array with all matches.
	global $TEMPSEARCH;
	$TEMPSEARCH = NULL;
	if(is_array($array)){
		SPT_array_search($array,$item);
		if(count($TEMPSEARCH) == 1){return reset($TEMPSEARCH);}}
	return $TEMPSEARCH;}

function SPT_array_search($array,$item){
	global $TEMPSEARCH;
	foreach($array as $k=>$v){
		if(is_array($v)){SPT_array_search($v,$item);}// Is there another level of array?
		if($k === $item){$TEMPSEARCH[] = $v;}}}



/**
 * Toolkit_print function.
 *
 * Prints out an array into a <pre> field for better visualisation.
 *
 * @access public
 * @param mixed $array
 * @return void
 */
function Toolkit_print(mixed $array){// Prints off an array in a pre field.
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}


/**
 * Grabs the calling plugins filename.
 *
 * @access private
 * @return void
 */
function SPT_get_user(){
	$list = debug_backtrace(false);
	$file = $list[1]['file'];
	$user = basename($file,'.php');
	return $user;}



//			=======SIMPLE FORM=======

/**
 * Toolkit_form function.
 *
 * Simple to use configuration form. Save and Load is handled as is the form field and save button.
 * Input array: type, name, label, class
 *
 * @access public
 * @param string $data_name
 * @param string $title
 * @param array $data_array
 * @return void
 */
function Toolkit_form($data_name,$title,$data_array){
	global ${$data_name};
	$pagename = SPT_get_URL();

	if(!is_array(reset($data_array))){ $data_array = array($data_array);}// Do we have more than one item?

	echo '<form action="'.$pagename.'" method="POST" accept-charset="utf-8">';// Initialise our form.
	if(isset($_POST[$data_name.'-submit'])){
		echo '<div class="updated">'.i18n_r('SETTINGS_UPDATED').'</div>';
	}
	echo '<h3>'.$title.'</h3>';
	echo '<input type="hidden" name="'.$data_name.'-url" value="'.$pagename.'" />';
	foreach($data_array as $array){
		echo Toolkit_element($data_name,$array);echo '<br>';}

	echo '<input class="submit" type="submit" name="'.$data_name.'-submit" value="'.i18n_r('BTN_SAVESETTINGS').'"/></form>';
}


//			=======FORM COMPONENT=======


/**
 * Toolkit_element function.
 *
 * Creates a specific form input. Can be used alone but is generally called by Simple Form.
 *
 * @access public
 * @param mixed $data
 * @param mixed $request
 * @return $return
 */
function Toolkit_element(mixed $data,mixed $request){// Make an element.
	$type = $request['type'];
	$name = $request['name'];
	if(isset($request['value'])){$value=$request['value'];}else{$value=NULL;}
	if(isset($request['label'])){$label=$request['label'];}else{$label=NULL;}
	if(isset($request['class'])){$class=$request['class'];}else{$class=NULL;}
	if(isset($request['override'])){$override=$request['override'];}else{$override=false;}
	global ${$data};
	if(isset(${$data}['FORM'][$name])){$info = ${$data}['FORM'][$name];}// Get data.
	else {if(is_array($value)){$info = $value[0];}else{$info = $value;}}// No data? Set default.
	if($class){$class = 'class="'.$class.'" ';} // Is class set?

	switch($type){

		case 'checkbox':
		if(!isset($value)){$value = 'checked';}
		$return = '<label><input type="checkbox" name="'.$data.'-'.$name.'" id="'.$name.'" '.$class.'value="'.$value.'" '.$info.'/> '.$label.'</label>';
		break;

		case 'textarea':
		$return = '<label>'.$label.'<textarea name="'.$data.'-'.$name.'" id="'.$name.'" '.$class.'/> '.$info.'</textarea></label>';
		break;

		case 'text':
		$return = '<label><input type="text" name="'.$data.'-'.$name.'" id="'.$name.'" '.$class.'value="'.$info.'"/>  '.$label.'</label>';
		break;

		case 'dropdown':
		$return = '<label>'.$label.'</label><select '.$class.' id="'.$name.'" name="'.$data.'-'.$name.'">';
		for($i=0;$i<=(count($value)-1);$i++){
			if(($i+1) == $info){$selected = 'selected';}else{$selected = NULL;}
			$return .= '<option value="'.($i+1).'" '.$selected.'>'.$value[$i].'</option>';
		}
		$return .='</select><br>';
		break;

		case 'hidden':
		$return = '<input type="hidden" name="'.$data.'-'.$name.'" id="'.$name.'" '.$class.'value="'.$info.'"/>';
		break;

		default:
		$return = $name;
		break;
		}
	return $return;
}



/**
 * Get current URL.
 *
 * @access private
 * @return $url
 */
function SPT_get_URL() {
$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
return $url;
}


//			=======SEARCH TEMPLATE=======

/**
 * Toolkit_template function.
 *
 * Scans the requested template file for function calls marked with the token function.
 *
 * @access public
 * @param bool $template_file (default: false)
 * @param bool $theme_folder (default: false)
 * @return $calls
 */
function Toolkit_template($template_file_here=false,$theme_folder_here=false){
	global $SITEURL, $url; // grab our globals to form url
	$page_data = SPT_load_template_remote($SITEURL, $url);
	$page_data = $page_data ? $page_data : SPT_load_template_locally($template_file_here, $theme_folder_here);

	// Search the contents for the data!
	$function_calls_found = array();
	$try_data_find_data = explode('||#search-items#||', $page_data);// Break up processed page.
        foreach($try_data_find_data as $request){
			$string = json_decode($request,true);
			if(is_array($string)){
				$function_calls_found[] = $string;
			}}

	return $function_calls_found;
}

/**
 * Load the page "remotely"
 *
 */
function SPT_load_template_remote($site, $url){
	$search_token = md5('token-search');
	return @file_get_contents($site.'index.php?id='.$url.'&'.$search_token); // Get page data
}


/**
 * Load the template locally if the link fails
 * (This could be considered redundant. But on the off chance that the weblink doesn't work it's here)
 */
function SPT_load_template_locally($template_file_here=false, $theme_folder_here=false){

	$holdtheglobal = array();//		We don't want to change any globals.
	foreach($GLOBALS as $k=>$v){
			$holdtheglobal[$k] = $v;
			${$k} = $v; } // generate an actual global

	if(!$template_file_here){$template_file_here = $template;}
	if(!$theme_folder_here ){$theme_folder_here  = $TEMPLATE;}
	$template_url_here = GSTHEMESPATH . $theme_folder_here . '/';
    if (file_exists($template_url_here.$template_file_here)) {// if file is found
    	ob_start();
    	include_once(GSADMININCPATH.'theme_functions.php');

    	if(file_exists($template_url_here.'functions.php')){include_once($template_url_here.'functions.php');}

    	define('CHECKING_FUNCTIONS', true);

    	include_once($template_url_here.$template_file_here);

        $contents_of_the_file = ob_get_contents();
        ob_end_clean();

    foreach($holdtheglobal as $k=>$v){
	    $GLOBALS[$k] = $v;}
    return $contents_of_the_file;
}}

/**
 * Toolkit_token function.
 *
 * Prints out the calling templates function and parameter names.
 *
 * @access public
 * @param bool $template (default: false)
 * @return void
 */
function Toolkit_token($template=false){// Manually set a token for template searching.
	$token_url = md5('token-search'); // we could be asking for tokens
	if(defined('CHECKING_FUNCTIONS') || array_key_exists($token_url, $_GET)){
		if(!$template){
			global $TEMPLATE; $template = $TEMPLATE;}
		$arglist = debug_backtrace(false);


/*		foreach($arglist as $arg){
			if(basename(dirname($arg['file'])) == $template){ // Find where the template calls file.
				$info = $arg['args'];
				$name = $arg['function'];
				break;}}

		echo '||#search-items#||{"'.$name.'":'.json_encode($info).'}||#search-items#||';}
*/
		echo '||#search-items#||'.json_encode($arglist).'||#search-items#||';}
}



//			=======SEARCH PAGES=======

/**
 * Toolkit_page function.
 *
 * Simple broad search of all page xml files for a supplied string. Returns page names and location of first match.
 *
 * @access public
 * @param mixed $search
 * @return $match
 */
function Toolkit_page(mixed $search){
	$text = strtolower($search);
	$deny = array('content','pubdate','title','url','meta','metad','menu','menuorder','menustatus','template','parent','content','private','author','cdata'); // Every page has these. So don't search for this string.
	if(in_array($text, $deny)){ return false; }
	$directory = GSDATAPAGESPATH;
	$files = getFiles($directory);
	$match = NULL;
	foreach($files as $check){
		if(substr($check,-4)=='.xml'){
			$data = file_get_contents($directory.$check);
			$test = stripos($data, $search);
			if($test){
				$match[] = array($check,$test);}}
	}
	if($match == NULL){ return false; }
	return $match;
}


//			=======DEVELOPMENT MODE HOOKS=======

function SPT_pages_sidebar(){
	global $SPT_DEVMODE;
	if($SPT_DEVMODE){ exec_action('dev-pages-sidebar'); }
}
function SPT_backups_sidebar(){
	global $SPT_DEVMODE;
	if($SPT_DEVMODE){ exec_action('dev-backups-sidebar'); }
}
function SPT_files_sidebar(){
	global $SPT_DEVMODE;
	if($SPT_DEVMODE){ exec_action('dev-files-sidebar'); }
}
function SPT_plugins_sidebar(){
	global $SPT_DEVMODE;
	if($SPT_DEVMODE){ exec_action('dev-plugins-sidebar'); }
}
function SPT_settings_sidebar(){
	global $SPT_DEVMODE;
	echo '<div class="DEVMODE">';
	exec_action('dev-settings-sidebar');
	echo '</div>';
}
function SPT_support_sidebar(){
	global $SPT_DEVMODE;
	if($SPT_DEVMODE){ exec_action('dev-support-sidebar'); }
}
function SPT_theme_sidebar(){
	global $SPT_DEVMODE;
	if($SPT_DEVMODE){ exec_action('dev-theme-sidebar'); }
}
function SPT_nav_tab(){
	global $SPT_DEVMODE;
//	echo '<span class="DEVMODE">';
	if($SPT_DEVMODE){ exec_action('dev-nav-tab');}
//	echo '</span>';
}


//			=======FILTER CONTENT FOR PLUGIN CALLS=======
/**
 * Grabs :["thing"]: calls from within content. Calls a filter for each one found.
 *
 * @access private
 * @return $repair
 */
function SPT_filter_content(mixed $text){
	Toolkit_token();

	$data = explode(':', $text);// Filtering for this code:  :["myitem"]:
	$repair = array_shift($data);
	$last = false;
	foreach($data as $info){
		$try = json_decode($info);
		if(is_array($try)){
			$filter = array_shift($try);
			$temp = exec_filter($filter.'-exec',$try);
			if($temp){$repair .= $temp;}
			$last = true;}
		else{
			if($last){ $repair .= $info; $last = false;}
			else{ $repair .= ':'.$info;}}
	}
	return $repair;
}



//			=======OTHER=======
// Prepare hooks - false = before
foreach($SPT_HOOKLIST as $hook=>$loc){//							Prep hooks.
	$func_name = 'SPT_'.str_replace('-', '_', $hook);
	$file_name = 'small_plugin_toolkit.php';
	if(!$loc){
		$add_to_list = array(
			'hook' => $hook,
			'function' => $func_name,
			'args' => array(),
			'file' => $file_name,
			'line' => '47');
		array_unshift($plugins, $add_to_list);
	}
}
		_debugLog($plugins);
// Prepare hooks - true = after
function SPT_common(){
	SPT_data_register();//		Register plugins to our database.
		_debugLog(__FUNCTION__." " .$SPT_HOOKLIST);
	global $plugins, $SPT_HOOKLIST;
	foreach($SPT_HOOKLIST as $hook=>$loc){
		_debugLog(__FUNCTION__." " .$SPT_HOOKLIST);
		$func_name = 'SPT_'.str_replace('-', '_', $hook);
		$file_name = 'small_plugin_toolkit.php';
		_debugLog(__FUNCTION__." " .$hook);
		if($loc){
			$plugins[] = array(
				'hook' => $hook,
				'function' => $func_name,
				'args' => array(),
				'file' => $file_name,
				'line' => '47');
			// debugLog($plugins);
		}
	}
}

// Create edit-above-content Hook.
function SPT_edit_extras(){
	echo '</div><div>';
	exec_action('edit-above-content');
	echo '</div><div id="default-WYSIWYG" style="clear: both; position: relative;"><div>';
}
function SPT_edit_content(){
	echo '<div id="content-block" style="position:absolute; height:100%; background: rgba(255,255,255,.8); top:0px; display: none; text-align: center;"></div></div>';
	?>
<script>
	var blockWidth = $(".main").width();// block the edit box
	$("#content-block").css({"width":blockWidth});
</script><?php
}



/**
 * Preload functions in admin, based on viewed page.
 *
 * @access private
 * @return void
 */
function SPT_admin_pre_header(){// Preload things in backed based on page name.
$page = basename($_SERVER['SCRIPT_FILENAME']);
switch ($page){
	case 'edit.php':
	global $FUNCTION_CALLS;
	$FUNCTION_CALLS = Toolkit_template(false,false,true); // Automatically run a template scan on edit page.
	break;
	}
}


/**
 * Detect plugins using this Toolkit.
 *
 * @access private
 * @return void
 */
function SPT_detect_plugs(){
	global $table, $SPT_HOOKLIST, $plugins, $SPT_DEVTEXT;
	$search = array('edit-above-content','dev-nav-tab','dev-theme-sidebar','dev-support-sidebar','dev-plugins-sidebar','dev-files-sidebar','dev-backups-sidebar','dev-pages-sidebar','register-data');// Long list of hooks this plugin uses
	$block = array('small_plugin_toolkit','caching_functions'); // ignore these.
	$list = array();
	$working = explode('<SPT>', $table); // Break up our description.
	$new_data = NULL;
	foreach($plugins as $look){
		if(in_array($look['hook'], $search)){ // Track plugins using these hooks.
			$file = str_replace('.php','',$look['file']);
			if(!in_array($file,$list)&&!in_array($file, $block)){
				$list[] = $file;}}}
	$list_len = count($list);//			If we have plugins using this toolkit, alert the user in plugin description.
	if($list_len > 0){$new_data = $SPT_DEVTEXT['disable'].' ';
		if($list_len == 1){ $new_data .= $SPT_DEVTEXT['singular'];}
		else{ $new_data .= $SPT_DEVTEXT['plural'];}
		$new_data .= '<br>';
	foreach($list as $plug){
		$new_data .= '<code style="background: lightgrey; padding:2px; border: 1px solid grey;
">'.$plug.'</code> ';}
	$table = $working[0].$new_data.$working[2];}
}

function SPT_legacy($leg){// legacy functions
	switch($leg){
		case 'xml':
		$file = GSDATAOTHERPATH.'Shared_Data.xml';
		if(file_exists($file)){
			$data = file_get_contents($file);
			return $data;}
	}
}

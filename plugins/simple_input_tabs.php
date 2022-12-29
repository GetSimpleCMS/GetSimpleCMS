<?php
/*
Plugin Name: Simple Input Tabs
Description: Adds additional user inputs where required in the theme.
Version: 2.6
Author: Jason Dixon
Author URI: http://simpleinputtabs.internetimagery.com
*/

# get correct id for plugin
$thisfile=basename(__FILE__, '.php');

# register plugin
register_plugin(
	$thisfile, //Plugin id
	'Simple Input Tabs', 	//Plugin name
	'2.6', 		//Plugin version
	'Jason Dixon',  //Plugin author
	'http://simpleinputtabs.internetimagery.com', //author website
	'Adds additional user inputs where required in the theme', //Plugin description
	'', //page type - on which admin tab to display
	'SIT_settings_menu'  //main function (administration)
	);
// Sort out hooks.
add_action('common','SIT_autoload_compatilitly_mode');
add_action('common','SIT_set_tabs_hook');
add_action('common','SIT_config');
add_action('header-body' , 'SIT_findSPT');
add_action('changedata-save','SIT_save_file');//				Intercept file save before it is saved.
add_filter('content','SIT_page_content_filter');//				Intercept content before it is put to page.
add_action('footer','SIT_change_edit_data');
add_action('settings-sidebar','createSideMenu',array('simple_input_tabs','Simple Input Tabs'));
add_action('register-data','SIT_settings');//					Register our database.





//				======DON'T CHANGE ANYTHING ABOVE THIS LINE======


//				======SIMPLE DEFAULT CONFIGURATION DEFAULTS======

/*	---------------------------------------------------------------------------------
*	IF YOU WISH TO TURN ON THIS PLUGINS' ERROR 404 MESSAGES, CHANGE: false TO: true.
*	---------------------------------------------------------------------------------*/
	$SIT_CONFIG['error404'] = false;
	/*-------------------------------------------------------------------------------*/


/*	---------------------------------------------------------------------------------
*	TO HIDE DEFAULT CONTENT APPEARING IN NEW PAGES, CHANGE: false TO: true.
*	---------------------------------------------------------------------------------*/
	$SIT_CONFIG['default-content'] = false;
	/*-------------------------------------------------------------------------------*/


/*	---------------------------------------------------------------------------------
*	CHANGE THE NUMBER TO DISPALY TABS IN DIFFERENT LOCATIONS:
*	1	=	Javascript tabs above the edit box. No page reloads.
*	2	=	Button tabs above the edit box. Page reloads each tab change.
*	3	=	Button tabs below the edit box. Classic mode. Page reloads.
*	0	=	No tabs visible. Don't use this.
*	---------------------------------------------------------------------------------*/
	$SIT_CONFIG['tab-location'] = 1;
	/*-------------------------------------------------------------------------------*/


/*	---------------------------------------------------------------------------------
*	IF YOU WANT TO GIVE PERMISSION TO HAVE PLUGINS PLACE DATA ON TOP OR BELOW YOUR
*	!!EXTRA!! CONTENT, CHANGE: false TO: true. (PLUGINS CAN STILL PLACE DATA ABOVE OR
*	BELOW YOUR 'MAIN' CONTENT SECTION REGARDLESS OF THIS SETTING.
*	---------------------------------------------------------------------------------*/
	$SIT_CONFIG['content-top'] = false;
	$SIT_CONFIG['content-bottom'] = false;
	/*-------------------------------------------------------------------------------*/



//				======COMPATIBILITY OVERRIDE======


/*	---------------------------------------------------------------------------------
*	IF YOU WANT TO FORCE SIMPLE INPUT TABS INTO COMPATIBILITY MODE, THEN CHANGE THE
*	CORRESPONDING PLUGINS VALUE TO: true.
*	e.g.	$SIT_COMPATIBILITY[i18n] = true;
*
*	BEST NOT TO ALTER THIS UNLESS YOU KNOW A PLUGIN IS THERE AND SIMPLE INPUT TABS
*	IS HAVING TROUBLE FINDING IT AUTOMATICALLY.
*	---------------------------------------------------------------------------------*/
	$SIT_COMPATIBILITY['i18n_base.php'] = false;//	--	--	--	--	--	i18n plugin.
	$SIT_COMPATIBILITY['small_plugin_toolkit.php'] = false;//	--	Small Plugin Toolkit (required)
	/*-------------------------------------------------------------------------------*/


//			======DON'T CHANGE ANYTHING BEYOND THIS LINE======


//				======SETTINGS MENU======

/*	---------------------------------------------------------------------------------
*	TURN OUR DATA INTO A GLOBAL ARRAY TO USE.
*	---------------------------------------------------------------------------------*/
function SIT_config(){
global $SIT_CONFIG, $SIT_settings;
if(isset($SIT_settings['FORM'])){
	foreach($SIT_CONFIG as $k=>$v){
		if(!isset($SIT_settings['FORM'][$k])){$SIT_CONFIG[$k] = false;}
		else{
			$SIT_CONFIG[$k] = $SIT_settings['FORM'][$k];
		}
}}
}	/*-------------------------------------------------------------------------------*/


/*	---------------------------------------------------------------------------------
*	SETTINGS MENU.
*	---------------------------------------------------------------------------------*/
function SIT_settings_menu(){
	$form = array(
		array(
		'type'=> 'dropdown',
		'name'=> 'tab-location',
		'label'=> SIT_text('TAB_STYLE'),
		'value'=> array(SIT_text('JAVA_TABS'),SIT_text('TOP_BUTTONS'),SIT_text('BOTTOM_BUTTONS'))),
		array(
		'type'=> 'checkbox',
		'name'=> 'default-content',
		'label'=> SIT_text('DEFAULT_TOGGLE')),
		array(
		'type'=> 'checkbox',
		'name'=> 'error404',
		'label'=> SIT_text('ERROR_TOGGLE')),
		array(
		'type'=> 'checkbox',
		'name'=> 'content-top',
		'label'=> SIT_text('TOP_CONTENT')),
		array(
		'type'=> 'checkbox',
		'name'=> 'content-bottom',
		'label'=> SIT_text('BOTTOM_CONTENT')));

	Toolkit_form('SIT_settings','Simple Input Tabs',$form);
}	/*-------------------------------------------------------------------------------*/


//				======TEMPLATE FUNCTIONS======

/*	---------------------------------------------------------------------------------
*	Functions below are for use in template files by template creators.
*	The idea is to have the option to keep consistency by using a naming convention.
*	You can still use GetSimpleCMS's normal functions, and they will do what they have always done.
*	get_page_content() should be replaced with insert_page_content() however.
*	---------------------------------------------------------------------------------*/

function insert_page_content($field = false,$blurb=NULL,$plugins=true){//			Output users content to page
	if(!$field){ $field = SIT_text('DEFAULT_TAB');}
	SIT_tab_content_output($field,$blurb,$plugins);}//					Use -> to output a different pages content.

function insert_page_header(){//								Output header information
	get_page_header();}

function insert_page_footer(){//								Output footer information
	get_page_footer();}

function insert_page_navigation($nav=NULL){//							Output page navigation
	if(function_exists('get_i18n_navigation')){
		get_i18n_navigation($nav);}
	else {
		get_navigation($nav);}}

function return_tab_content($location=false){//							Return a specific tabs content for processing.
	if(!$location){$location = SIT_text('DEFAULT_TAB');}
	$query = SIT_extract_tab_request($location);
	$data = SIT_hunt_page_data($query[0], bling($query[1]));
	if($data){return $data;}else{return false;}
}	/*-------------------------------------------------------------------------------*/



//				======COMPATIBILITY FUNCTIONS======

/*	---------------------------------------------------------------------------------
*	CHECK THE PLUGIN LIST FOR PLUGIN.
*	---------------------------------------------------------------------------------*/
function SIT_compat_check($check){
	global $live_plugins;
	if(array_key_exists($check, $live_plugins)&&$live_plugins[$check] == "true"){ return true; }
	else { return false; }
}	/*-------------------------------------------------------------------------------*/


/*	---------------------------------------------------------------------------------
*	AUTOMATICALLY ENABLE COMPATIBILITY IF PLUGIN EXISTS AND IS ACTIVE.
*	---------------------------------------------------------------------------------*/
function SIT_autoload_compatilitly_mode(){
global $SIT_COMPATIBILITY;
foreach($SIT_COMPATIBILITY as $k=>$v){
	if(!$v){
		$SIT_COMPATIBILITY[$k] = SIT_compat_check($k); }}
}	/*-------------------------------------------------------------------------------*/

/*	---------------------------------------------------------------------------------
*	SMALL PLUGIN TOOLKIT REQUIRED. WARNING MESSAGE IF NOT THERE.
*	---------------------------------------------------------------------------------*/
function SIT_findSPT(){
	global $SIT_COMPATIBILITY;
	if(!$SIT_COMPATIBILITY['small_plugin_toolkit.php']){ echo('<div class="error" >'.SIT_text('CANNOT_FIND').' <a href="http://get-simple.info/extend/plugin/small-plugin-toolkit/531/">Click here to get it.</a></div>'); }//											Helpful message to get the required plugin.
}	/*-------------------------------------------------------------------------------*/

/*	---------------------------------------------------------------------------------
*	COMPATIBILITY WITH I18N PLUGIN.
*	---------------------------------------------------------------------------------*/
function SIT_i18n_template_fix($url){//								Find the original template from the original file.
	global $SIT_COMPATIBILITY;
	if($SIT_COMPATIBILITY['i18n_base.php']){
		$language_list = return_i18n_available_languages();
		$split_template = explode('_', $url);
		$template_len = count($split_template);
		$template_lang = false;
		$check_template = $split_template[0];
		if($template_len>1){
		$template_lang = $split_template[$template_len-1];
		for($i=1;$i<=($template_len-2);$i++){//						Reassemble any underscores.
			$check_template .= '_' . $split_template[$i];}}
		if($template_lang && in_array($template_lang, $language_list)){//  make sure the file is a language file.
			$file = GSDATAPAGESPATH . $check_template . '.xml';
			if (file_exists($file)) {
				$data = getXML($file);
				return $data->template;
			} else { return false; }
		} else { return false; }
	} else { return false; }
}

function SIT_i18n_frontend_fix(){
	global $SIT_COMPATIBILITY, $content, $SIT_RAW_CONTENT_DATA;
	if($SIT_COMPATIBILITY['i18n_base.php']){
		$tempdata = SIT_content_scan($content);
		foreach($tempdata as $entry=>$data){
			$SIT_RAW_CONTENT_DATA[$entry] = $data;
			}
			$content = $SIT_RAW_CONTENT_DATA[SIT_text('DEFAULT_TAB')];
			}
}	/*-------------------------------------------------------------------------------*/

/*	---------------------------------------------------------------------------------
*	SET HOOKS.
*	---------------------------------------------------------------------------------*/
$add_to_list = array(
	array(
	'hook' => 'edit-content',
	'function' => 'SIT_edit_foot',
	'args' => array(),
	'file' => 'simple_input_tabs.php',
	'line' => '26'),
	array(
	'hook' => 'index-pretemplate',
	'function' => 'SIT_generate_data',
	'args' => array(),
	'file' => 'simple_input_tabs.php',
	'line' => '26'),
	);
foreach($add_to_list as $insert){
	array_unshift($plugins, $insert);}
function SIT_set_tabs_hook(){
	global $plugins;
	$plugins[] = array(
			'hook' => 'edit-above-content',
			'function' => 'SIT_tab_position',
			'args' => array(),
			'file' => 'simple_input_tabs.php',
	    'line' => '26'
	    );
	$plugins[] = array(
	'hook' => 'index-pretemplate',
	'function' => 'SIT_i18n_frontend_fix',
	'args' => array(),
	'file' => 'simple_input_tabs.php',
	'line' => '26'
	);
	// debugLog($plugins);
}	/*-------------------------------------------------------------------------------*/


//				======FRONT END FUNCTIONS======

/*	----------------------------------------------------------------------------------
*	If someone calls get_page_content(), warn them to change it in the 'display tab location' view.
*	---------------------------------------------------------------------------------*/
function SIT_page_content_filter($request){
	if(isset($_GET['display'])&&$_GET['display'] == 'preview'){// 				Are we looking at a preview?
		echo strip_decode('<center><h4>'.SIT_text('SECTION_CODE').'<br>&amp;lt;?php get_page_content(); ?&amp;gt;<br>'.SIT_text('REPLACE_CODE').'<br>&amp;lt;?php insert_page_content(); ?&amp;gt;</h4></center>');}
	else if(trim($request)==NULL){
		return SIT_error404();}
	else {return $request;}
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Output the data to the page in the correct place!
*	---------------------------------------------------------------------------------*/
function SIT_tab_content_output($request,$blurb,$plugins){
	global $SIT_CONFIG, $SIT_RAW_CONTENT_DATA,$url;

	$field = SIT_extract_tab_request($request);//						Get tab request.
	$tab = $field[1];
	$page = $field[0];
	if($tab[0] == '#'){$tab = substr($tab, 1); $hidden = true;}//				Clear any # sign.
	$tab = bling($tab);//					Fancy the tab.

	if(isset($_GET['display'])&&$_GET['display'] == 'preview'){//				Are we previewing the page?
		global $url;
		$style = 'style="cursor: help; text-align: center;"';
		if(isset($hidden)){$blurb .= ' This tab is hidden.';}//				Is tab hidden?
		$link_begin = '<element '.$style.'onClick="window.opener.location.reload(true);window.opener.location.href=\''.get_site_url(false).'/admin/edit.php?id=';
		echo $link_begin.$page.'&tab-view='.str_replace(' ', '+', $tab);
		echo '\';window.close();"><h2>'.$request.'</h2><p>'.$blurb.'</p></element>';
	}
	else if($page == $url && $tab == SIT_text('DEFAULT_TAB')){//				If main tab, output main.
		get_page_content();
	}
	else{//											Output content!
		if($SIT_CONFIG['content-top']){exec_action('content-top');}
		$info = SIT_hunt_page_data($page, $tab);
		if($plugins){$info = exec_filter('content', $info);}else{Toolkit_token();}
		$info = exec_filter($tab.'-tab-content', $info);
		if($info){echo $info;}else{echo SIT_error404();}
		if($SIT_CONFIG['content-bottom']){exec_action('content-bottom');}
	}
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Find and return the contents of a specified page / tab combination.
*	---------------------------------------------------------------------------------*/
function SIT_hunt_page_data($page,$tab){
	global $SIT_COMPATIBILITY, $url;
	if (trim($page==$url)){//								If no page specified. Use data from current page.
		global $SIT_RAW_CONTENT_DATA;
		$unpacked = $SIT_RAW_CONTENT_DATA;}
	else if($SIT_COMPATIBILITY['i18n_base.php']){//							If compatibility with i18n plugin is active.
		$file = return_i18n_page_data($page);//						Then let that plugin find the xml data for us.
		$content = strip_decode($file->content[0]);
		$unpacked = SIT_content_scan($content);}
	else {
	$file = GSDATAPAGESPATH . $page . '.xml';//					Find the page file and load it.
	if(file_exists($file)){
		$file = getXML($file);
		$content = strip_decode($file->content[0]);//					Get the content from the file.
		$unpacked = SIT_content_scan($content);}//					Extract the data from content.
	else{return NULL;}}
	if($unpacked && array_key_exists($tab, $unpacked)){return strip_decode($unpacked[$tab]);}
	else {return NULL;}//Send back the data.
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	If enabled, output an error 404 message.
*	---------------------------------------------------------------------------------*/
function SIT_error404(){
	global $SIT_CONFIG;
	if($SIT_CONFIG['error404']){
		return SIT_text('ERROR404');}
	else{
		return NULL;}
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Generate tab data from page content.
*	---------------------------------------------------------------------------------*/
function SIT_generate_data(){
	if(!isset($GLOBALS['SIT_RAW_CONTENT_DATA'])){
		global $SIT_RAW_CONTENT_DATA;
		global $content;
		$SIT_RAW_CONTENT_DATA = SIT_content_scan($content);
		$content = $SIT_RAW_CONTENT_DATA[SIT_text('DEFAULT_TAB')];}
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Javascript the edit page! Fancy!
*	---------------------------------------------------------------------------------*/
function SIT_change_edit_data(){
	global $SIT_LAST_TAB, $SIT_RAW_CONTENT_DATA, $SIT_TAB_LIST, $SIT_CONFIG, $HTMLEDITOR, $template;
	?>
	<script>
	<?php if($SIT_CONFIG['tab-location'] == 1){ ?>
//	php our variables into javascript for JAVATABS tm!
	currentTab = "<?php echo $SIT_LAST_TAB; ?>";
	pageData = <?php echo json_encode($SIT_RAW_CONTENT_DATA); ?>; //			Inital data.
	defaultTabData = <?php echo json_encode($SIT_TAB_LIST); ?>;//				Tab name and custom default data.
	changeEditor = CKEDITOR.instances["post-content"];//					Set editor
	defaultTabContent = <?php if(!$SIT_CONFIG['default-content']){echo 'true';}else{echo 'false';} ?>;


//	 Swap data.
	function dataReplace(data){
			var getdata = changeEditor.getData();
			getdata = $("<div/>").text(getdata).html();
			var newContent = $("<div/>").html(data).text();
			changeEditor.setData(newContent, function(){
			changeEditor.document.on("keyup", function(){ $('#editform').trigger('change');}); });
		return getdata;}
<?php } ?>

//	Set warning
function set_warning(info){
	if(info){
		$("#custom-warning").text(info).addClass('updated').fadeIn('slow');} //		Activate warning.
	else{
		$("#custom-warning").fadeOut(800); //						Deactivate warning.
	}
}
// block the editor
	function content_block(block){
		if(block){
			$("#content-block").html("<h3>"+block+"</h3>").css({"top":"-35px"}).fadeIn('slow');}
		else{
			$("#content-block").fadeOut('slow');}}


//	Lets go!
	$(document).ready(function(){

// Set up our new page warning.
var newPageTest = $("#post-id").val();
if(!newPageTest){
	$("#content-block").html("<?php echo '<h3>'.i18n_r('CANNOT_SAVE_EMPTY').'</h3>'; ?>").fadeIn(0);
	$("#post-id").keyup(function(event){
		content_block(false);
		$(this).unbind(event);});
	$("#post-title").keyup(function(event){
		content_block(false);
		$(this).unbind(event);});
}

//	trigger warning if template dropdown changes.
	$("#post-template").change(function(){
		if($(this).val() == "<?php echo $template; ?>"){
			set_warning(false); content_block(false);}
		else{
			set_warning("<?php echo SIT_text('CHANGE_TEMPLATE'); ?>"); content_block("<?php i18n('ACTIVATE_THEME'); ?>");
		}});

	<?php if($SIT_CONFIG['tab-location'] == 1){ ?>
//	draw attention to the preview tab. PRETTY!
	$("#pagepreview a").hover(function(){
			$(this).removeClass("pages");},
		function(){
			$(this).addClass("pages");
		});

//	Make the tabs... well... work.
		$(".toptab .pagetab a").click(function(event){
			event.preventDefault(); // prevent the links from working.
			// If in source mode. Send an error message.
			if(changeEditor.mode != 'wysiwyg'){
			set_warning("<?php echo SIT_text('SOURCE_MODE'); ?>");
			return; } else { set_warning(false);}
			// Change tab, visually.
			var newTab = $(this).text(); // set new tab.
			$(".toptab li a").addClass("pages").removeClass("active-tab"); // 	Change tab colours.
			$(this).removeClass("pages").addClass("active-tab");
			//	Check for user content. If no content, set default.
			if(!pageData[newTab]){
				if(defaultTabContent){// Unless default content is turned off.
					pageData[newTab] = defaultTabData[newTab];}}

			pageData[currentTab] = dataReplace(pageData[newTab]); //		Put in new data.
			currentTab = newTab;

			var saveInfo = JSON.stringify(pageData);//.escapeSpecialChars();
			$("#old-page-content").val(saveInfo); //				Save data to form.
			$("#current-tab-name").text(currentTab);//				Change the tab display text.
			$("#lastpage-view").val(currentTab); //					Save last tab to restore later.
		});<?php } ?>
	});
</script><?php
}	/*-------------------------------------------------------------------------------*/



//				======BACK END FUNCTIONS======

/*	---------------------------------------------------------------------------------
*	Prepare content for the editor.
*	---------------------------------------------------------------------------------*/
function SIT_prepare_content(){
	global $template, $TEMPLATE, $SIT_TAB_LIST, $SIT_LAST_TAB, $SIT_TAB_LIST, $data_edit, $SIT_COMPATIBILITY, $SIT_CONFIG, $HTMLEDITOR;

	if($SIT_CONFIG['tab-location'] == 1 && $HTMLEDITOR != 1){$SIT_CONFIG['tab-location'] = 2;}// check HTML editor.

	if(isset($_GET['newid'])){//								New file? Get template if i18n plugin is installed.
		$remote_template = SIT_i18n_template_fix($_GET['newid']);
		if($remote_template != false){
			$template = $remote_template;}}

	$SIT_TAB_LIST = SIT_template_scan($TEMPLATE,$template,'insert_page_content');//		Scan the template folder, gather content requests.
	if(isset($_GET['tab-view'])){//								Did we specify a tab?
		$SIT_LAST_TAB = $_GET['tab-view'];}
	else if(isset($data_edit->returnpage)){//						Check to see if a return page exists.
		$SIT_LAST_TAB = (string)$data_edit->returnpage;}//				If not, then set it to the default.
	else{$SIT_LAST_TAB = SIT_text('DEFAULT_TAB');}
	if(!array_key_exists($SIT_LAST_TAB,$SIT_TAB_LIST)){//					Check to see if the return tab is in the template.
		$SIT_LAST_TAB = key($SIT_TAB_LIST);}//						If not, then swap to a relevant tab.

}	/*-------------------------------------------------------------------------------*/


/*	---------------------------------------------------------------------------------
*	Place tabs above the editor.
*	---------------------------------------------------------------------------------*/
function SIT_tab_position(){//									Process data and display in the editor.
	global $SIT_RAW_CONTENT_DATA;
	global $SIT_LAST_TAB;//									Set a page to come back to after saving.
	global $SIT_TAB_LIST;//									Initialize our tab list.
	global $SIT_WARNING;//									Warning message.
	global $SIT_CONFIG;//									Load our configurations.
	global $content;//									Get page content.

	SIT_prepare_content();

	echo '<!--- '.SIT_text('SIT_ADDED')." ---> \n";//	Store record of the last tab viewed.
	echo '<input type="hidden" id="lastpage-view" name="lastpage-view" value="'.safe_slash_html($SIT_LAST_TAB).'">';
	//									Store raw content data for retrieval later.
	echo '<input type="hidden" id="old-page-content" name="old-page-content" value="'.safe_slash_html($content).'">';
	//									tabs default content.
	echo '<input type="hidden" id="default-tab-content" name="default-tab-content" value=\''.json_encode($SIT_TAB_LIST).'\'>';

	$SIT_RAW_CONTENT_DATA = SIT_content_scan($content);//					Extract the data from the page xml file.
	$newData = NULL;
	if(is_array($SIT_RAW_CONTENT_DATA) && array_key_exists($SIT_LAST_TAB, $SIT_RAW_CONTENT_DATA)){ $newData = $SIT_RAW_CONTENT_DATA[$SIT_LAST_TAB];}// Is data there?

	if($content == NULL){//									If the page is fresh add in welcome message.
		if(!$SIT_CONFIG['default-content']){
		$content = '<p></p><h1>'.SIT_text('WELCOME_NEW_PAGE').'</h1><p>'.SIT_text('WELCOME_PAGE_BLURB').'</p>';}
		else{ $content = NULL;}}
	else if($SIT_TAB_LIST == NULL){// 							If there were no insert_page_content calls, then insert default data.
		$content = $SIT_RAW_CONTENT_DATA[SIT_text('DEFAULT_TAB')];}
	else if($newData){
		$content = $newData;}//								If we have data, then output it.
	else{
		if(!$SIT_CONFIG['default-content']){ $content = $SIT_TAB_LIST[$SIT_LAST_TAB]; }
		else { $content = NULL; }}//							Otherwise we have a new tab. Output default content.


	echo '<div>';
	SIT_display_warning($SIT_WARNING);
	echo '<div id="custom-warning" style="display:none">YOU ARE TRESPASSING IN THE CODE!</div>';// set up the error message box.

	if($SIT_CONFIG['tab-location'] == 1){SIT_top_tabs();}else if($SIT_CONFIG['tab-location'] == 2){SIT_bottom_tabs();}
	echo '</div>';
}

/*	----------------------------------------------------------------------------------
*	Place content below the editor.
*	---------------------------------------------------------------------------------*/
function SIT_edit_foot(){//									Print out tabs below the text input box.
	global $SIT_COMPATIBILITY;
	global $SIT_LAST_TAB;//									Get the active tab name.
	global $SIT_TAB_LIST;//									Get the list of tab names.
	global $SIT_CONFIG;//									Get the active tab style.
	global $HTMLEDITOR;//

	echo '<!--- '.SIT_text('SIT_ADDED')." ---> \n";
	echo '<div id="content-block" style="position:absolute; height:100%; background: rgba(255,255,255,.8); top: ';
	if($SIT_CONFIG['tab-location'] == 1){echo '-35';}else{ echo '0';}
	echo 'px; display: none; text-align: center;"><h3>'.i18n_r('CANNOT_SAVE_EMPTY').'</h3></div>'; // block the edit box

	if($SIT_CONFIG['tab-location'] == 3){SIT_bottom_tabs();}//				Create some tabs for each input.
	if(count($SIT_TAB_LIST)>1){//								Only display this info if there is more than one tab.
	if($SIT_CONFIG['tab-location'] != 1){ echo SIT_text('WILL_SAVE').' ';} //		Only mention tabs save when not Javatabs.
	echo SIT_text('CURRENTLY_EDITING').' <strong><span id="current-tab-name" style="color:green">'.$SIT_LAST_TAB.'</span></strong>.<br/>';}
	//											Add a separate button that dispalys tab locations.
	echo '<p><img src="template/images/search.png"/> <a '.SIT_display_tab().' >'.SIT_text('PREVIEW_BUTTON').'</a></p>';
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Display warning if one exists!
*	---------------------------------------------------------------------------------*/
function SIT_display_warning($warning){
	if($warning!=0){
		echo '<div class="updated">';
		switch($warning){
			case 1://								There is a duplicate tab in the theme.
			echo SIT_text('DUPLICATE_TABS');
			break;

			case 2://								There are illegal tabs hidden.
			echo SIT_text('ILLEGAL_TABS');
			break;
			}
		echo '</div>';}
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Link for display tab.
*	---------------------------------------------------------------------------------*/
function SIT_display_tab(){
	global $SITEURL, $url;
	return 'href="'.$SITEURL.'index.php?id='.$url.'&display=preview" target="_blank"';
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Print out tabs on top of the edit box.
*	---------------------------------------------------------------------------------*/
function SIT_top_tabs(){
	global $SIT_LAST_TAB, $SIT_TAB_LIST;

//	echo '<input type="text" id="test" value="fill" style="width:600px;" />'; //		DEBUGGING. box to display things. Should be commented out.

	$value = 'class="nav toptab" style="position:relative; margin: 0px; float:left; width:648px;';
	echo '<ul '.$value.'"><li id="pagepreview"><a class="pages" style="cursor:help;"  '.SIT_display_tab().'><img src="template/images/search.png"/></a></li>';
	$tab_width = 0;
	foreach($SIT_TAB_LIST as $newtab=>$dump){
		if($tab_width > 80){ echo '</ul><ul '.$value.' margin-top: -5px; background: #f0f0f0;">'; $tab_width = 0;}
		$tab_width += strlen($newtab) + 4;
		echo '<li class="pagetab" ><a style="font-weight: bold !important;" href="#'.$newtab.'" ';
		if($SIT_LAST_TAB == $newtab){ echo 'class="active-tab"';} else { echo 'class="pages"';}
		echo '>'.$newtab.'</a></li>';}
	echo '</ul>';
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Print out tabs underneath the edit box.
*	---------------------------------------------------------------------------------*/
function SIT_bottom_tabs(){
	global $SIT_TAB_LIST, $SIT_LAST_TAB, $SIT_CONFIG;
	echo '<p>';
	foreach($SIT_TAB_LIST as $tab_name=>$value){
	$active_tab = '';
	$shadow_details = 'inset 0px 2px 3px 0px #acacac';//					Make a fancy shadow for the selected tab.
	echo '<span><input class="submit';
	if($tab_name == $SIT_LAST_TAB){//							Make a special style for the active tab,
		echo ' active-tab" style="background: #96f4b4; -webkit-box-shadow: '.$shadow_details.'; -moz-box-shadow:'.$shadow_details.'; box-shadow: '.$shadow_details.';" type="button"';
	} else {
	echo '" type="submit" ';}
	echo 'name="submitted" value="'.$tab_name.'" onclick="warnme=false;" /></span>';}
	echo '</p>';
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Parse the template file. Return values from within insert_page_content( ... );
*	Return with default tab if there are no calls to this plugin on the template.
*	---------------------------------------------------------------------------------*/
function SIT_template_scan($theme_folder,$theme_file,$function){
	global $SIT_WARNING, $SIT_CONFIG, $url;//						Establish warnings.
	$SIT_WARNING = 0;
	$validated_list = array();//								Establish empty array.
//	$template_url = GSTHEMESPATH . $theme_folder . '/' . $theme_file;//			Find the template file.
//	$the_file = file_get_contents($template_url);//						And scan the file.
	global $FUNCTION_CALLS; $result_list = array();

	foreach($FUNCTION_CALLS as $func_list){ // search list of function calls
		foreach($func_list as $func){ // search through functions backtrace
			if(array_key_exists('function', $func) && $func['function'] == 'insert_page_content'){ // pull out our function
				$result_list[] = $func['args']; // add its arguments to list
			}
		}
	}

	if($result_list){//									Check if we have any results. If not there were no function calls.
		foreach($result_list as $value){//						Go through each function and make a validated list of calls.
			if(!isset($value[0])){$value[0] = SIT_text('DEFAULT_TAB');}
			if(!isset($value[1])){$value[1] = NULL;}
			$request = SIT_extract_tab_request($value[0]);//			Grab tab request. page / tab
			$page = $request[0];
			$tab = $request[1];
			$ok = true;//								Ok to insert new tab?
		if($page == $url){//								Is this a tab request for this page?
			if(!preg_match('/^[\w_\-\s]+$/',$tab)){$SIT_WARNING = 2; $ok=false;}// Tab has invalid characters?
			if(array_key_exists($tab, $validated_list)){$SIT_WARNING = 1; $ok=false;} // Duplicate tabs?
			if($tab[0] == '#'){$SIT_WARNING = 0; $ok=false;}// Does tab have #? Suppress warning.
			if($ok){//								Start inserting data.
				if(!$value[1]){$value[1] = SIT_text('WELCOME_TAB_BLURB').' '.bling($tab);}// Make default message.
				$validated_list[bling($tab)] = $value[1];//			Put in data.
		}}}}
	if(!$SIT_CONFIG['error404']){ $SIT_WARNING = 0; }
	return $validated_list;
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Take a tab request and return the page / tab requested.
*	---------------------------------------------------------------------------------*/
function SIT_extract_tab_request($extract){
	global $url;
	$tab_find = explode('->', $extract); // extract page and tab parameters.
		if(count($tab_find)==2){
			if(!$tab_find[1]){$tab = SIT_text('DEFAULT_TAB');} else {$tab = $tab_find[1];}
				if(!$tab_find[0]){$page = $url;}
				else{$page = $tab_find[0];}}
		else {
			$tab = $tab_find[0]; $page = $url;}
	return array($page, $tab);
}	/*-------------------------------------------------------------------------------*/

/*	----------------------------------------------------------------------------------
*	Parse the content data. Return values from ||] ... [||
*	---------------------------------------------------------------------------------*/
function SIT_content_scan($string){
$json_attempt = json_decode($string,true); // try json first.
if(is_array($json_attempt)){ return $json_attempt; }
$string_array = explode('||', $string);//							Break up our string.
$content = NULL;//										Set an empty data variable. Lets fill it!
$previous_tab = NULL;//										Set tab memory.
$array_location = 1;//										Set string location. 1=beginning, 2=tab, 3=data.
foreach($string_array as $item){//								Begin extracting the content data.
	if (substr($item,0,1) == ']' && substr($item,-1) == '['){ //				Is the current item a tab?
		$item = str_replace(']', '', $item);
		$item = str_replace('[', '', $item);//						Take out the ] [ characters.
		$content[$item] = '';
		$previous_tab = $item;//							Save this tab in the memory, for next string.
		$array_location = 2;}//								Where are we in the data? We are working with tabs.
	else {//										Guess the item is not a tab...
		if ($array_location == 2){//							Last item was a tab. This must then be the data.
			$content[$previous_tab]=$item;//					Add content to the key of the previous tab.
			$array_location = 3;}//							We are now in the data.
		else if($array_location == 3){//						We are still in data. We accidentally grabbed ||.
			$content[$previous_tab].= '||'.$item;}//				Put them back in. Clean up our messes.
		else if($array_location == 1){//						If we are at the beginning, make it the default tab.
			$content[SIT_text('DEFAULT_TAB')] = $item;
			$previous_tab = SIT_text('DEFAULT_TAB');
			$array_location = 3;}
		}}
	return $content;//									Return the data!
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Make text look pretty. Gotta look presentable!
*	---------------------------------------------------------------------------------*/
function bling($text){
	return trim(ucwords(strtolower($text)));
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Intercepting the saving of the page file.
*	Extracting the users input data, and adding our own tags to it.
*	Sending back the result!
*	---------------------------------------------------------------------------------*/
function SIT_save_file(){
	global $xml, $url;//									Hijack the page save file.

	$i18n_template = SIT_i18n_template_fix($url);//	Get original file if i18n plugin is buggering with them.
	if($i18n_template != false){
		$xml->template[0] = '';
		$xml->template[0]->addCData($i18n_template);}

	if(isset($_POST['submitted'])){//							Probably pointless to check, but best to be safe.
		if($_POST['lastpage-view'] != NULL){
		$last_tab = $_POST['lastpage-view'];}//						Get the last tab that was open.
		else{$last_tab = SIT_text('DEFAULT_TAB');}
		if($_POST['submitted'] == i18n_r('BTN_SAVEUPDATES') || $_POST['submitted'] == i18n_r('BTN_SAVEPAGE')){
			$return_tab = $last_tab;//						Check to see if the page has just been saved
		} else {//									or if the tab has been changed.
			$return_tab = $_POST['submitted'];}//					Get the last tab viewed, so we can return there.
		$last = $xml->addChild('returnpage');//						Insert record of last tab viewed.
		$last->addCData($return_tab);

		if(get_magic_quotes_gpc()){//							Fix any problems with magic quotes... GRR MAGIC QUOTES! >:[]
			$_POST['old-page-content'] = stripslashes($_POST["old-page-content"]);
			$_POST['default-tab-content'] = stripslashes($_POST['default-tab-content']);}

		$tab_default_content = json_decode($_POST['default-tab-content'],true); //	Get tabs default content.
		$tab_def_name = SIT_text('DEFAULT_TAB');
		$new_information = (string)$xml->content[0];//					Pull out the newly entered information.
		$old_information = SIT_content_scan($_POST['old-page-content']);//		Original page information.
		$old_information[$last_tab] = $new_information;//				Insert the new information replacing the old.
		foreach($old_information as $k=>$v){
			$old_information[$k] = SIT_check_default($k,$v, $tab_default_content);}//Check if content is still default value.
		$test_array[$tab_def_name] = SIT_text('WELCOME_NEW_PAGE').SIT_text('WELCOME_PAGE_BLURB');
		$save_info = SIT_check_default($tab_def_name,$old_information[$tab_def_name],$test_array);
		//   place the default tab at the start of the file.
		unset($old_information[$tab_def_name]);// remove default tab from list.
		foreach($old_information as $k=>$v){//						Compile data back into single string.
			$each_tab = safe_slash_html('||]'.$k.'[||');//				Ensure the tab name is secure with safe_slash_html.
			$save_info .= $each_tab.$v;}//						Put em together.

		$xml->content[0] = '';//							Clear the XML data.
		$xml->content[0]->addCData($save_info);}//					Insert our altered information.
}	/*-------------------------------------------------------------------------------*/


/*	----------------------------------------------------------------------------------
*	Check if content is default. Erase it if so.
*	---------------------------------------------------------------------------------*/
function SIT_check_default($key,$data,$default){
	$remove = array("&nbsp;","\n","\t","\r");
	if(array_key_exists($key, $default)){
			$teststring1 = str_replace($remove, '', strip_decode($data));
			$teststring1 = trim(strip_tags($teststring1));//			Get string content.
			$teststring2 = strip_tags($default[$key]);
			if($teststring1 == $teststring2){return NULL;}
	}
	return $data;
}	/*-------------------------------------------------------------------------------*/


//				======LANGUAGE FUNCTIONS======

/*	---------------------------------------------------------------------------------
*	Check if there is an external language file, and if so use it.
*	Otherwise use one of the built in languages.
*	---------------------------------------------------------------------------------*/
if(file_exists(GSPLUGINPATH . 'simple_input_tabs/lang/'.$LANG.'.php')){
	$SIT_LANG_EXTERNAL = true;//								Check if there is an external language file,
	i18n_merge('simple_input_tabs');}//							if not then use a local language.
else{
	$SIT_LANG_EXTERNAL = false;
	SIT_internal_language();}

function SIT_text($words){//									Translate the request into the language.
	global $SIT_LANG_EXTERNAL;//								Get language location.
	if($SIT_LANG_EXTERNAL){//								Do we have an external language file?
		$lang = i18n_r('simple_input_tabs/'.$words);}
		else{//										Guess it must be a local language.
		global $SIT_I18N_LOCAL;
		$lang = $SIT_I18N_LOCAL[$words];}
	if($words == 'DEFAULT_TAB'){return bling($lang);}
	return $lang;
}	/*-------------------------------------------------------------------------------*/

/*	----------------------------------------------------------------------------------
*	Built in Language text. Can be translated directly in here if desired.
*	Simply copy/paste the code between "case" and "break" and add in your own translations.
*	---------------------------------------------------------------------------------*/
function SIT_internal_language(){
global $SIT_I18N_LOCAL, $LANG;
$SIT_I18N_LOCAL = match ($LANG) {
    '' => array(
    // Nice little message in the source code
    'SIT_ADDED' => '',
    //name of default tab!
    'DEFAULT_TAB' => '',
    // Welcome messages that appear when new pages and/or tabs are opened.
    'WELCOME_NEW_PAGE' => '',
    'WELCOME_PAGE_BLURB' => '',
    'WELCOME_TAB_BLURB' => '',
    //error messages
    'ERROR404' => '',
    'TAB_RETURN_PROBLEM' => '',
    'DUPLICATE_TABS' => '',
    'ILLEGAL_TABS' => '',
    //helpful messages
    'WILL_SAVE'			=> '',
    'CURRENTLY_EDITING' => '',
    'CHANGE_TEMPLATE' => '',
    'TAB_SECTION_NAME' => '',
    'PREVIEW_BUTTON' => '',
    // preview page
    'SECTION_CODE' => '',
    'REPLACE_CODE' => ''
    ),
    'es_ES' => array(
    // Nice little message in the source code
    'SIT_ADDED'		=>	'Agregado por el plugin de Simple Input Tabs',
    //name of default tab!
    'DEFAULT_TAB'		=>	'Principal',
    // Welcome messages that appear when new pages and/or tabs are opened.
    'WELCOME_NEW_PAGE'	=>	'&iexcl;Bienvenidos a la nueva p&aacute;gina!',
    'WELCOME_PAGE_BLURB'	=>	'Quita este texto y a&ntilde;ade tu propio contenido aqu&iacute;. En la parte inferior hay unas pesta&ntilde;as que ya tienen nombres. &Uacute;salas para agregar contenido a las diferentes &aacute;reas de tu p&aacute;gina web. No te olvides de a&ntilde;adir un t&iacute;tulo a tu p&aacute;gina, arriba.',
    'WELCOME_TAB_BLURB'	=>	'A&ntilde;adir el contenido de la secci&oacute;n:',
    //error messages
    'ERROR404' 		=>	' Error 404: No se ha encontrado el contenido solicitado.',
    'TAB_RETURN_PROBLEM' 	=>	'Hubo un problema con tu solicitud.',
    'DUPLICATE_TABS' 	=>	' La plantilla que has seleccionado tiene pesta&ntilde;as duplicadas con el mismo nombre.',
    'ILLEGAL_TABS' 		=>	'Hay una o m&aacute;s pesta&ntilde;as de tu plantilla que utilizan caracteres no v&aacute;lidos. Han permanecido ocultas.',
    //helpful messages
    'WILL_SAVE'		=>	'Cuando hagas clic en una pesta&ntilde;a de la parte superior, se guardar&aacute; y cambiar&aacute;n las entradas.',
    'CURRENTLY_EDITING'	=>	'Est&aacute;s editando el archivo:',
    'CHANGE_TEMPLATE'	=>	'Si cambias la plantilla de p&aacute;gina, tendr&aacute;s que guardar para actualizar tus pesta&ntilde;as.',
    'TAB_SECTION_NAME'	=>	'El contenido de esta pesta&ntilde;a va en:',
    'PREVIEW_BUTTON'	=>'Mostrar ubicaciones de pesta&ntilde;as.',
    'SOURCE_MODE'		=>	'No se pueden cambiar las pesta&ntilde;as en el c&oacute;digo de fuente.',
    // preview page
    'SECTION_CODE'		=>	' Esta secci&oacute;n predomina el c&oacute;digo:',
    'REPLACE_CODE'		=>	'Para un funcionamiento correcto, debes cambiar el c&oacute;digo a:',
    // Settings page
    'JAVA_TABS'		=>	'Responsive Tabs (Top)',
    'TOP_BUTTONS'		=>	'Top Tabs (Buttons)',
    'BOTTOM_BUTTONS'	=>	'Bottom Tabs (Buttons)',
    'TAB_STYLE'		=>	'Tab Style - <b style="font-weight:100">Choose location and type of Tabs to display.</b>',
    'DEFAULT_TOGGLE'	=>	'Hide Default Content - <b style="font-weight:100">Tick the box if you do not want fresh pages to have default content.</b>',
    'ERROR_TOGGLE'		=>	'Toggle Content Errors - <b style="font-weight:100">Toggle 404 errors occurring when there is no content.</b>',
    'TOP_CONTENT'		=>	'Top Content - <b style="font-weight:100">Allow plugins to place content above your extra tabs.</b>',
    'BOTTOM_CONTENT'	=>	'Bottom Content - <b style="font-weight:100">Allow plugins to access content area below your extra tabs.</b>',
    'CANNOT_FIND'		=>	'Simple Input Tabs cannot find the plugin Small Plugin Toolkit. It is required to operate.'
    ),
    default => array(
    // Nice little message in the source code
    'SIT_ADDED'		=>	'Added by the Simple Input Tabs Plugin',
    //name of default tab!
    'DEFAULT_TAB'		=>	'main',
    // Welcome messages that appear when new pages and/or tabs are opened.
    'WELCOME_NEW_PAGE'	=>	'Welcome to your new page!',
    'WELCOME_PAGE_BLURB'	=>	'Remove this text and add your content here. Use named tabs to add content to different areas of your web page. Don\'t forget to add a title to your page, above.',
    'WELCOME_TAB_BLURB'	=>	'Add your content to the section:',
    //error messages
    'ERROR404'		=>	'Error 404: Content requested was not found.',
    'TAB_RETURN_PROBLEM'	=>	'There was a problem with your Tab request.',
    'DUPLICATE_TABS'	=>	'Your selected Template has duplicate Tabs of the same name.',
    'ILLEGAL_TABS'		=>	'There are one or more Tabs in your template that use illegal characters. They have been hidden.',
    //helpful messages
    'WILL_SAVE'		=>	'Clicking a Tab above will SAVE and switch inputs.',
    'CURRENTLY_EDITING'	=>	'You are currently editing:',
    'CHANGE_TEMPLATE' 	=>	'If you change the Page Template, you will need to SAVE in order for your Tabs to update.',
    'TAB_SECTION_NAME' 	=>	'Content for this Tab goes in:',
    'PREVIEW_BUTTON' 	=>	'Display Tab Locations.',
    'SOURCE_MODE'		=>	'Cannot change tabs in source mode.',
    // preview page
    'SECTION_CODE'		=>	'This section is populated by the code:',
    'REPLACE_CODE'		=>	'For proper functionality you should change the code to:',
    // Settings page
    'JAVA_TABS'		=>	'Responsive Tabs (Top)',
    'TOP_BUTTONS'		=>	'Top Tabs (Buttons)',
    'BOTTOM_BUTTONS'	=>	'Bottom Tabs (Buttons)',
    'TAB_STYLE'		=>	'Tab Style - <b style="font-weight:100">Choose location and type of Tabs to display.</b>',
    'DEFAULT_TOGGLE'	=>	'Hide Default Content - <b style="font-weight:100">Tick the box if you do not want fresh pages to have default content.</b>',
    'ERROR_TOGGLE'		=>	'Toggle Content Errors - <b style="font-weight:100">Toggle 404 errors occurring when there is no content.</b>',
    'TOP_CONTENT'		=>	'Top Content - <b style="font-weight:100">Allow plugins to place content above your extra tabs.</b>',
    'BOTTOM_CONTENT'	=>	'Bottom Content - <b style="font-weight:100">Allow plugins to access content area below your extra tabs.</b>',
    'CANNOT_FIND'		=>	'Simple Input Tabs cannot find the plugin Small Plugin Toolkit. It is required to operate.'

    ),
};
}	/*-------------------------------------------------------------------------------*/

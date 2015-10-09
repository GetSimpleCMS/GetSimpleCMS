<?php 
/**
 * Edit Theme
 *
 * Allows you to edit a theme file
 *
 * @package GetSimple
 * @subpackage Theme
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-theme-edit');

# variable settings
$theme_options 		= ''; 
$template_file 		= ''; 
$template 			= $TEMPLATE; 
$theme_templates 	= '';

# were changes submitted?
if (isset($_GET['t'])) {
	$_GET['t'] = strippath($_GET['t']);
	if ($_GET['t'] && is_dir(GSTHEMESPATH . $_GET['t'].'/')) {
		$template = $_GET['t'];
	}
}
if (isset($_GET['f']) && !empty($_GET['f'])) {
	if (is_file(GSTHEMESPATH . $template.'/'.$_GET['f'])) {
		$template_file = $_GET['f'];
	}
}

if(isset($_POST['themesave'])){
	$themesave = var_in($_POST['themesave']);
	if($themesave == "default") setcookie('gs_editor_theme', '', time() - 3600); 
	else setcookie('gs_editor_theme',$themesave);
	return;
}


$themepath = GSTHEMESPATH.tsl($template);

// @todo add a way to exclude symblinks via config to add files here, or add them via alternative pathing
// allow plugins all directories to this and allow other paths by adding a registration method and a secfilter here

// allow themeroot, specially handle travesal protection, this breaks symblinks probably
if($template == '.' && getDef('GSTHEMEEDITROOT',true)){
	if(empty($template_file)){
		if(!path_is_safe($themepath.$template_file,GSTHEMESPATH,true)) die();
	}
	else if(!filepath_is_safe($themepath.$template_file,GSTHEMESPATH,true)) die();
}
else{
	# if no template is selected, use the default
	if ($template_file == '') {
		$template_file = 'template.php';
	}
	if(!filepath_is_safe($themepath.$template_file,GSTHEMESPATH,true)) die();
} 


# check for form submission
if(isset($_POST['submitsave'])){

	check_for_csrf("save");

	# save edited template file
	$filename = $_POST['edited_file'];
	$FileContents = get_magic_quotes_gpc() ? stripslashes($_POST['content']) : $_POST['content'];
	// prevent traversal
	if(!filepath_is_safe(GSTHEMESPATH . $filename,GSTHEMESPATH)) die(i18n_r('INVALID_OPER'));
	$status = save_file(GSTHEMESPATH . $filename,$FileContents);
	exec_action('theme-aftersave'); // @hook theme-aftersave after a theme-edit file save

	if($status) $success = sprintf(i18n_r('TEMPLATE_FILE'), $filename);
	else $error = i18n_r('ERROR');

	if(isset($_POST['ajaxsave'])){
		echo "<div>";
		include('template/error_checking.php');
		echo '<input id="nonce" name="nonce" type="hidden" value="'. get_nonce("save") .'" />';
		echo "</div>";
		die();
	}
}

// ajax file get request, send only the form with the file and content
if(isset($_GET['ajax'])){
	$content = !empty($template_file) ? read_file(GSTHEMESPATH . tsl($template) . $template_file) : '';
	?>
		<div>
			<form id="themeEditForm" action="<?php myself(); ?>?t=<?php echo $template; ?>&amp;f=<?php echo $template_file; ?>" method="post" >
				<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
				<textarea name="content" id="codetext" wrap='off'><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea>
				<input type="hidden" value="<?php echo tsl($template) . $template_file;?>"  <?php if(empty($template_file)) echo ' class="nofile"'; ?>  name="edited_file" id="edited_file" />
				<div id="theme-edit-extras-wrap"><?php exec_action('theme-edit-extras'); // @hook theme-edit-extras after theme-edit html output?></div>
				<p id="submit_line" >
					<span><input class="submit" type="submit" name="submitsave" value="<?php i18n('BTN_SAVECHANGES'); ?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="theme-edit.php?cancel"><?php i18n('CANCEL'); ?></a>
				</p>
			</form>
		</div>
	<?php
	die();
}


$allowed_extensions = explode(',',getDef('GSTHEMEEDITEXTS'));

# if no template is selected, use the default
if ($template == '') $template = GSTEMPLATEFILE;
$directory = GSTHEMESPATH . $template . '/';

# if no template is selected, use the default
if ($template_file == '' && file_exists($themepath.GSTEMPLATEFILE)) {
	$template_file = GSTEMPLATEFILE;
}

//////////////////////////////////////////////////
// File Manager
//////////////////////////////////////////////////

function createTemplateDropdown(){
	GLOBAL $template;
	# create themes dropdown
	$theme_options = '<select name="t" id="theme-folder" >';

	$templates = directoryToArray(GSTHEMESPATH, false);
	$theme_dir_array = array();

	foreach($templates as $file){
		if( is_dir($file) ) {
			// only a theme if GSTEMPLATEFILE  (template.php) exists
			if (file_exists($file.'/'.GSTEMPLATEFILE)){
				$sel="";
				$theme_dir_array[] = $file;
				$theme = basename($file);
				if ($template == $theme){
					$sel="selected";
				}

				$theme_options .= '<option '.$sel.' value="'.$theme.'" >'.$theme.'</option>';
			}
		}
	}

	// edit theme/root files
	if(getDef('GSTHEMEEDITROOT',true)){
		$sel = '';
		if($template == '.') $sel = 'selected';
		$theme_options .= '<option '. $sel .' value="." style="font-style:italic">'.i18n_r('THEME_ROOT').'</option>';
	}
	$theme_options .= '</select> ';

	# check to see how many themes are available
	if (count($theme_dir_array) == 1) $theme_options = '';

	return $theme_options;
}

/**
 * outputs a ul nested tree from directory array
 * @param  array   $array     directoryToMultiArray()
 * @param  boolean $hideEmpty omit empty directories if true
 * @return string
 */
function editor_array2ul($array, $hideEmpty = true, $recurse = true) {
	GLOBAL $allowed_extensions,$template_file,$template;

	$cnt = 0;

	$out="<ul>";
	foreach($array as $key => $elem){

		if(!is_array($elem['value'])){
			// Is a file
			$ext = getFileExtension($elem['value']);
			if( in_array($ext,$allowed_extensions)){

				$filename = $elem['value'];
				$filepath = $elem['path'];
				$filenamefull=substr(strstr($filepath.$filename,getRelPath(GSTHEMESPATH).$template.'/'),strlen(getRelPath(GSTHEMESPATH).$template.'/'));

				$open = editor_fileIsOpen($elem['path'],$elem['value']) ? ' open' : '';

				if ($filename == GSTEMPLATEFILE){
					$ext = 'theme';
					$filename=i18n_r('DEFAULT_TEMPLATE');
				}

				$link = myself(false).'?t='.$template.'&amp;f='.$filenamefull;
				$out.='<li><a href="'.$link.'"class="file ext-'.$ext.$open.'">'.$filename."</a></li>";
			}
		}
		else if($recurse){
			// Is a folder

			// Are we showing/hiding empty folders.
			// WILL NOT hide empty folders that contain at least 1 subfolder
			$empty = '';
			if(count($elem['value']) == 0){
				if($hideEmpty) continue;
				$empty = ' dir-empty'; // empty folder class
			}
			$out.='<li><a class="directory'.$empty.'">'.$key.'</a>'.editor_array2ul($elem['value']).'</li>';
		}
	}

	$out=$out."</ul>";
	return $out;
}

/**
 * checks if the template file is open for editing
 * @return bool true if template_file is being edited
 */
function editor_fileIsOpen($path,$file){
	GLOBAL $template,$template_file;
    $file = $path.$file;
    $filenamefull=substr(strstr($file,getRelPath(GSTHEMESPATH).$template.'/'),strlen(getRelPath(GSTHEMESPATH).$template.'/')); 
	return $template_file == $filenamefull;
}

/**
 * directory listing file order comparator
 * dirs first then files , in alphabetical order
 * @param array $a,$b directoryToMultiArray() arrays
 */
function editor_compareOrder($a, $b)
{
	$atype = $a['type'];
	$btype = $b['type'];

	// place directories first
	if ($atype!=$btype){
		return strcmp($atype,$btype);
	}

	// sort directories by key
	if($atype == 'directory' and $btype == 'directory'){
		return strcmp($a['dir'],$b['dir']);
	}

	// sort files by value
	if($atype == 'file' and $btype == 'file'){
		return strcmp($a['value'],$b['value']);
	}
}

/**
 * recursive sort
 * @param  array $array Input array to be sorted
 * @param  string $comparator compare function name
 * @return array        Sorted array
 */
function editor_recur_sort(&$array,$comparator) {
   foreach ($array as &$value) {
      if (is_array($value['value']) and count($value['value']>1)) editor_recur_sort($value['value'],$comparator);
   }
   return @uasort($array, $comparator);
}

// get themes files, sort then generate ul list heirachy
$recurse = $template !== '.';
$files   = directoryToMultiArray($directory,$recurse,$allowed_extensions);
editor_recur_sort($files, 'editor_compareOrder'); // custom sort, dir,file,nat sort
$fileList = editor_array2ul($files,$recurse,$recurse);

$theme_options = createTemplateDropdown();

$pagetitle = i18n_r('THEME_MANAGEMENT');
get_template('header');

include('template/include-nav.php');


// setup editor specs
switch (getFileExtension($template_file)) {
	case 'css':
		$mode = 'text/css';
		break;
	case 'js':
		$mode = 'text/javascript';
		break;
	case 'html':
		$mode = 'text/html';
		break;
	default:
		$mode = 'application/x-httpd-php';
}


?>
<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
		<h3 class="floated"><?php i18n('EDIT_THEME'); ?></h3>
		<div class="edit-nav clearfix" >
			<?php exec_action(get_filename_id().'-edit-nav'); ?>
		</div>		
		<?php exec_action(get_filename_id().'-body'); ?>				
	<!-- float wrapper -->
		<div id="theme_edit_wrap">

			<!-- left nav  -->
			<div id="theme_edit_nav">

				<!-- Theme Selector -->
				<div id="theme_edit_select">
				<form action="<?php myself(); ?>" method="get" accept-charset="utf-8" >
					<div class="well">
						<div>
							<?php echo $theme_options; ?>
							<noscript><input class="submit" type="submit" name="s" value="<?php i18n('EDIT'); ?>" /></noscript>
						</div>
					</div>
				</form>
				</div>

				<!-- File Tree -->
				<div id="theme_filemanager">
					<?php echo $fileList; ?>
				</div>
			</div>

			<div id="theme_edit_code" class="codewrap <?php if(empty($template_file)) echo 'readonly';?>">
				
				<div id="theme_editing" class="well">
				<?php i18n('EDITING_FILE'); ?>: <?php echo $SITEURL.getRelPath(GSTHEMESPATH).'<span id="theme_editing_file">'. tsl($template).$template_file .'</span>'; ?>
				<?php $content = !empty($template_file) ? read_file(GSTHEMESPATH . tsl($template) . $template_file) : ''; ?>
				</div>
		
		<form id="themeEditForm" action="<?php myself(); ?>?t=<?php echo $template; ?>&amp;f=<?php echo $template_file; ?>" method="post" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
			<textarea name="content" id="codetext" class="code_edit" data-mode="<?php echo $mode; ?>" wrap='off' ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea>
			<input type="hidden" value="<?php echo tsl($template) . $template_file; ?>" <?php if(empty($template_file)) echo ' class="nofile"'; ?> name="edited_file" id="edited_file" />
			<div id="theme-edit-extras-wrap"><?php exec_action('theme-edit-extras'); ?></div>
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitsave" value="<?php i18n('BTN_SAVECHANGES'); ?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="theme-edit.php?cancel"><?php i18n('CANCEL'); ?></a>
			<?php if(!getDef('GSNOHIGHLIGHT',true)) echo $themeselector; ?>	
			<span id="pagechangednotify"><?php i18n('PAGE_UNSAVED');?></span>
			</p>
		</form>
		</div>
	
			<!-- float clear -->
			<div class="clear"></div>
	</div>
		</div>	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
	</div>
</div>
<?php get_template('footer'); ?>

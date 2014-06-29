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
if (isset($_GET['f'])) {
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

$themepath = GSTHEMESPATH.$template.DIRECTORY_SEPARATOR;

// prevent traversal
if($template_file!='' and !filepath_is_safe($themepath.$template_file,$themepath)) die();

# if no template is selected, use the default
if ($template_file == '') {
	$template_file = 'template.php';
}

# check for form submission
if(isset($_POST['submitsave'])){

	check_for_csrf("save");	
	
	# save edited template file
	$SavedFile = $_POST['edited_file'];
	$FileContents = get_magic_quotes_gpc() ? stripslashes($_POST['content']) : $_POST['content'];	
	// prevent traversal
	if(!filepath_is_safe(GSTHEMESPATH . $SavedFile,$themepath)) die();	
	$fh = fopen(GSTHEMESPATH . $SavedFile, 'w') or die("can't open file");
	fwrite($fh, $FileContents);
	fclose($fh);

	$success = sprintf(i18n_r('TEMPLATE_FILE'), $SavedFile);
	
	if(isset($_POST['ajaxsave'])){
		echo "<div>";
		include('template/error_checking.php');
		echo "</div>";
		die();
	}
}

if(isset($_GET['ajax'])){
	$content = file_get_contents(GSTHEMESPATH . tsl($template) . $template_file);
	?>
		<form id="themeEditForm" action="<?php myself(); ?>?t=<?php echo $template; ?>&amp;f=<?php echo $template_file; ?>" method="post" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
			<textarea name="content" id="codetext" wrap='off' ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea>
			<input type="hidden" value="<?php echo tsl($template) . $template_file; ?>" name="edited_file" id="edited_file" />
			<div id="theme-edit-extras-wrap"><?php exec_action('theme-edit-extras'); ?></div>
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitsave" value="<?php i18n('BTN_SAVECHANGES'); ?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="theme-edit.php?cancel"><?php i18n('CANCEL'); ?></a>
			</p>
		</form>	
	<?php		
	die();	
}

# create themes dropdown
$themes_path = GSTHEMESPATH;
$themes_handle = opendir($themes_path);
$theme_options .= '<select name="theme-folder" id="theme-folder" >';	
while ($file = readdir($themes_handle)) {
	$curpath = $themes_path .'/'. $file;
	if( is_dir($curpath) && $file != "." && $file != ".." ) {
		$theme_dir_array[] = $file;
		$sel="";
		
		if (file_exists($curpath.'/template.php')){
			if ($template == $file){ 
				$sel="selected"; 
			}
			
			$theme_options .= '<option '.$sel.' value="'.$file.'" >'.$file.'</option>';
		}
	}
}
$theme_options .= '</select> ';

# check to see how many themes are available
if (count($theme_dir_array) == 1){ $theme_options = ''; }

$allowed_extensions=array('php','css','js','html','htm','txt','');

# if no template is selected, use the default
if ($template == '') { $template = 'template.php'; }
$templates = directoryToArray(GSTHEMESPATH . $template . '/', true);
$directory = GSTHEMESPATH . $template . '/';

$theme_templates .= '<span id="themefiles"><select class="text" id="theme_files" style="width:425px;" name="f" >';
$theme_templates .= createFileDropdown($templates);


//////////////////////////////////////////////////
// File Manager
//////////////////////////////////////////////////


function createFileDropdown($templates){
	GLOBAL $TEMPLATE_FILE,$template,$allowed_extensions;
	
	$theme_templates = '';

	foreach ($templates as $file){
		$extension=pathinfo($file,PATHINFO_EXTENSION);
		if (in_array($extension, $allowed_extensions)){
			$filename=pathinfo($file,PATHINFO_BASENAME);
			$filenamefull=substr(strstr($file,getRelPath(GSTHEMESPATH).$template.'/'),strlen(getRelPath(GSTHEMESPATH).$template.'/'));   
			if ($TEMPLATE_FILE == $filenamefull){ 
		        $sel="selected"; 
			} else { 
				$sel="";
			}
			
			if ($filename == 'template.php'){ 
				$templatename=i18n_r('DEFAULT_TEMPLATE'); 
			} else { 
				$templatename=$filenamefull; 
			}
			
			$theme_templates .= '<option '.$sel.' value="'.$templatename.'" >'.$templatename.'</option>';
		}
	}
	$theme_templates .= "</select></span>";
	return $theme_templates;
}

/**
 * outputs a ul nested tree from directory array
 * @param  array   $array     directoryToMultiArray()
 * @param  boolean $hideEmpty omit empty directories if true
 * @return string
 */
function editor_array2ul($array,$hideEmpty = true) {
	GLOBAL $allowed_extensions,$template_file,$template;
    
	$cnt = 0;

	$out="<ul>";
	foreach($array as $key => $elem){
		
		if(!is_array($elem['value'])){
		$ext = lowercase(pathinfo($elem['value'], PATHINFO_EXTENSION));
			
			// Is a file
			if( in_array($ext,$allowed_extensions)){

				$filename = $elem['value'];
				$filepath = $elem['path'];   
				$filenamefull=substr(strstr($filepath.$filename,getRelPath(GSTHEMESPATH).$template.'/'),strlen(getRelPath(GSTHEMESPATH).$template.'/')); 

				$open = editor_fileIsOpen($elem['path'],$elem['value']) ? ' open' : '';
				
				if ($filename == 'template.php'){
					$ext = 'theme';
					$filename=i18n_r('DEFAULT_TEMPLATE');        			
				}	
				
				$link = myself(false).'?t='.$template.'&amp;f='.$filenamefull;
				$out.='<li><a href="'.$link.'"class="file ext-'.$ext.$open.'">'.$filename."</a></li>";
			}
		}
		else {
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
 * dirs first, files in alphabetical order
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

// get theme files as ul tree
$files = directoryToMultiArray($directory,true,$allowed_extensions);
editor_recur_sort($files, 'editor_compareOrder');
$fileList = editor_array2ul($files);

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('THEME_MANAGEMENT')); 

include('template/include-nav.php');


// setup editor specs
switch (pathinfo($template_file,PATHINFO_EXTENSION)) {
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
		<h3><?php i18n('EDIT_THEME'); ?></h3>
		
		<!-- float wrapper -->
		<div id="theme_edit_wrap">

			<!-- left nav  -->
			<div id="theme_edit_nav">

				<!-- Theme Selector -->
				<div id="theme_edit_select">
					<div class="well"><?php echo $theme_options; ?>	</div>
				</div>

				<!-- File Tree -->
				<div id="theme_filemanager">
					<?php echo $fileList; ?>
				</div>
			</div>

			<div id="theme_edit_code" class="codewrap">
				
				<div id="theme_editing" class="well">
				<?php i18n('EDITING_FILE'); ?>: <?php echo $SITEURL.getRelPath(GSTHEMESPATH).' <b><span id="theme_editing_file">'. tsl($template).$template_file .'</span></b>'; ?>
				<?php $content = file_get_contents(GSTHEMESPATH . tsl($template) . $template_file); ?>
				</div>
		
		<form id="themeEditForm" action="<?php myself(); ?>?t=<?php echo $template; ?>&amp;f=<?php echo $template_file; ?>" method="post" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
			<textarea name="content" id="codetext" class="code_edit" data-mode="<?php echo $mode; ?>" wrap='off' ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea>
			<input type="hidden" value="<?php echo tsl($template) . $template_file; ?>" name="edited_file" id="edited_file" />
			<div id="theme-edit-extras-wrap"><?php exec_action('theme-edit-extras'); ?></div>
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitsave" value="<?php i18n('BTN_SAVECHANGES'); ?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="theme-edit.php?cancel"><?php i18n('CANCEL'); ?></a>
			<?php echo $themeselector; ?>	
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

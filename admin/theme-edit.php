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

# variable settings
login_cookie_check();
$theme_options 		= ''; 
$template_file 		= ''; 
$template 			= $TEMPLATE; 
$theme_templates 	= '';

# were changes submitted?
if (isset($_GET['t'])) {
	$_GET['t'] = strippath($_GET['t']);
	if ($_GET['t']&&is_dir(GSTHEMESPATH . $_GET['t'].'/')) {
		$template = $_GET['t'];
	}
}
if (isset($_GET['f'])) {
	if (is_file(GSTHEMESPATH . $template.'/'.$_GET['f'])) {
		$template_file = $_GET['f'];
	}
}

if(isset($_POST['themesave'])){
	setcookie('gs_editor_theme',$_POST['themesave']);
}

$themepath = GSTHEMESPATH.$template.DIRECTORY_SEPARATOR;
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

$themeselector = '
			<select id="cm_themeselect">
		    <option>default</option>
		    <option>ambiance</option>
		    <option>blackboard</option>
		    <option>cobalt</option>
		    <option>eclipse</option>
		    <option>elegant</option>
		    <option>erlang-dark</option>
		    <option>lesser-dark</option>
		    <option>monokai</option>
		    <option>neat</option>
		    <option>night</option>
		    <option>rubyblue</option>
		    <option>solarized dark</option>
		    <option>solarized light</option>
		    <option>twilight</option>
		    <option>vibrant-ink</option>
		    <option>xq-dark</option>
		</select>		
';

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
			<?php echo $themeselector; ?>				
			</p>
		</form>	
	<?php		
	die();	
}

# create themes dropdown
$themes_path = GSTHEMESPATH;
$themes_handle = opendir($themes_path);
$theme_options .= '<select class="text" name="t" id="theme-folder" >';	
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
			$filenamefull=substr(strstr($file,'/theme/'.$template.'/'),strlen('/theme/'.$template.'/'));   
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

function array2ul($array,$hideEmpty = true) {
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
				$filenamefull=substr(strstr($filepath.$filename,'/theme/'.$template.'/'),strlen('/theme/'.$template.'/')); 

				$open = fileIsOpen($elem['path'],$elem['value']) ? ' open' : '';
				
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
			// this will not hide empty folders that contain at least 1 subfolder
			$empty = '';
			if(count($elem['value']) == 0){
				if($hideEmpty) continue;
				$empty = ' dir-empty'; // empty folder class
			}	
			$out.='<li><a class="directory'.$empty.'">'.$key.'</a>'.array2ul($elem['value']).'</li>';
		}	
	}

	$out=$out."</ul>";
	return $out; 
}

function fileIsOpen($path,$file){
	GLOBAL $template,$template_file;
    $file = $path.DIRECTORY_SEPARATOR.$file;
    $filename=pathinfo($file,PATHINFO_BASENAME);
    $filenamefull=substr(strstr($file,'/theme/'.$template.'/'),strlen('/theme/'.$template.'/')); 
	return $template_file == $filenamefull;
}

function compareOrder($a, $b)
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

function recur_sort(&$array) {
   foreach ($array as &$value) {
      if (is_array($value['value']) and count($value['value']>1)) recur_sort($value['value']);
   }
   return @uasort($array, 'compareOrder');
}

$files = directoryToMultiArray($directory,true,$allowed_extensions);
recur_sort($files, 'compareOrder');
$fileList = array2ul($files);

if (!defined('GSNOHIGHLIGHT') || GSNOHIGHLIGHT!=true){
	register_script('codemirror', $SITEURL.'admin/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
	
	register_style('codemirror-css',$SITEURL.'admin/template/js/codemirror/lib/codemirror.css','screen',FALSE);
	register_style('codemirror-theme',$SITEURL.'admin/template/js/codemirror/theme/default.css','screen',FALSE);
	
	queue_script('codemirror', GSBACK);
	
	queue_style('codemirror-css', GSBACK);
	queue_style('codemirror-theme', GSBACK);

}

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('THEME_MANAGEMENT')); 
?>

<?php include('template/include-nav.php');

if (!defined('GSNOHIGHLIGHT') || GSNOHIGHLIGHT!=true){

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

	if(isset($_COOKIE['gs_editor_theme'])){
		$theme = $_COOKIE['gs_editor_theme'];
	}

?>

<script>

var themeFileSave;
var editor;
var loadjscssfile;
jQuery(document).ready(function () {
	
		function keyEvent(cm, e) {
			if (e.keyCode == 81 && e.ctrlKey) {
				if (e.type == "keydown") {
					e.stop();
					setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
				}
				return true;
			}
		}

		var themes = Array(
			'ambiance',
			'cobalt',
			'eclipse',
			'eclipse',
			'elegant',
			'erlang-dark',
			'lesser-dark',
			'monokai',
			'neat',
			'night',
			'rubyblue',
			'solarized dark',
			'solarized light',
			'twilight',
			'vibrant-ink',
			'xq-dark'
		);

		var customTheme = '<?php if(isset($theme)) echo $theme; ?>'; 

		var defTheme = 'default';		
		// var customTheme = themes[Math.floor(Math.random()*themes.length)];

		if(customTheme && customTheme != undefined){
			defTheme = customTheme;
			var parts = defTheme.split(' ');
			loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css")
		}	

		editor = CodeMirror.fromTextArea(document.getElementById("codetext"), {
			lineNumbers: true,
			matchBrackets: true,
			indentUnit: 4,
			indentWithTabs: true,
			enterMode: "keep",
			mode:"<?php echo $mode; ?>",
			tabMode: "shift",
			theme: defTheme,
			fixedGutter : true,
			extraKeys: {
				"Ctrl-Q" : function(cm) { foldFunc(cm, cm.getCursor().line); },
				"F11"    : function(cm) { setFullScreen(cm, !isFullScreen(cm)); },
				"Esc"    : function(cm) { if (isFullScreen(cm)) setFullScreen(cm, false); },
				"Ctrl-S" : function(cm) { customSave(cm);	}
			},
			saveFunction:  function() { customSave(cm); },
			onChange: function(){
				// console.log('content changed');
				editor.hasChange = true;
			}

		});

		var hlLine = editor.addLineClass(0, "background", "activeline");

		var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder,'...');
		editor.on("gutterClick", foldFunc);

		editor.on("cursorActivity", function() {

		  // line highlihghting
		  var cur = editor.getLineHandle(editor.getCursor().line);
		  if (cur != hlLine) {
		    editor.removeLineClass(hlLine, "background", "activeline");
		    hlLine = editor.addLineClass(cur, "background", "activeline");
		  }

		  // highlight matching
		  editor.matchHighlight("CodeMirror-matchhighlight");
		});

		function customSave(cm){
			console.log('saving');
			themeFileSave(cm);
		}

    function isFullScreen(cm) {
      return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
    }

    function winHeight() {
      return window.innerHeight || (document.documentElement || document.body).clientHeight;
    }

    function setFullScreen(cm, full) {
      var wrap = cm.getWrapperElement();
      if (full) {
        wrap.className += " CodeMirror-fullscreen";
        wrap.style.height = winHeight() + "px";
        document.documentElement.style.overflow = "hidden";
      } else {
        wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
        wrap.style.height = "";
        document.documentElement.style.overflow = "";
      }
      cm.refresh();
    }

		CodeMirror.on(window, "resize", function() {
		    var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
		    if (!showing) return;
		    showing.CodeMirror.getWrapperElement().style.height = winHeight() + "px";
		});

		function setThemeSelected(theme){
			$("#cm_themeselect").val(theme);
		}

		setThemeSelected(defTheme);

});

</script>
<?php 
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

			<div id="theme_edit_code">
				
<!-- 				<form action="<?php myself(); ?>" method="get" accept-charset="utf-8" >
		<p><?php echo $theme_options; ?><?php echo $theme_templates; ?>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="s" value="<?php i18n('EDIT'); ?>" /></p>
				</form> -->
		

				<div id="theme_editing" class="well">
				<?php i18n('EDITING_FILE'); ?>: <?php echo $SITEURL.'theme/ <b><span id="theme_editing_file">'. tsl($template).$template_file .'</span></b>'; ?>
				<?php $content = file_get_contents(GSTHEMESPATH . tsl($template) . $template_file); ?>
				</div>
		
		<form id="themeEditForm" action="<?php myself(); ?>?t=<?php echo $template; ?>&amp;f=<?php echo $template_file; ?>" method="post" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
			<textarea name="content" id="codetext" wrap='off' ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea>
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

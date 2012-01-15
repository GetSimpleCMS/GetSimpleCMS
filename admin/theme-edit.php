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
$TEMPLATE_FILE 		= ''; 
$template 			= ''; 
$theme_templates 	= '';

# were changes submitted?
if (isset($_GET['t'])) {
	$_GET['t'] = strippath($_GET['t']);
	if ($_GET['t']&&is_dir(GSTHEMESPATH . $_GET['t'].'/')) {
		$TEMPLATE = $_GET['t'];
	}
}
if (isset($_GET['f'])) {
	$_GET['f'] = strippath($_GET['f']);
	if ($_GET['f']&&is_file(GSTHEMESPATH . $TEMPLATE.'/'.$_GET['f'])) {
		$TEMPLATE_FILE = $_GET['f'];
	}
}

# check for form submission
if((isset($_POST['submitsave']))){
	
	# check for csrf
	if (!defined('GSNOCSRF') || (GSNOCSRF == FALSE) ) {
		$nonce = $_POST['nonce'];
		if(!check_nonce($nonce, "save")) {
			die("CSRF detected!");
		}
	}
	
	# save edited template file
	$SavedFile = $_POST['edited_file'];
	$FileContents = get_magic_quotes_gpc() ? stripslashes($_POST['content']) : $_POST['content'];	
	$fh = fopen(GSTHEMESPATH . $SavedFile, 'w') or die("can't open file");
	fwrite($fh, $FileContents);
	fclose($fh);
	$success = sprintf(i18n_r('TEMPLATE_FILE'), $SavedFile);
}

# if no template is selected, use the default
if (! $TEMPLATE_FILE) {
	$TEMPLATE_FILE = 'template.php';
}

# create themes dropdown
$themes_path = GSTHEMESPATH;
$themes_handle = opendir($themes_path);
$theme_options .= '<select class="text" style="width:225px;" name="t" id="theme-folder" >';	
while ($file = readdir($themes_handle)) {
	$curpath = $themes_path .'/'. $file;
	if( is_dir($curpath) && $file != "." && $file != ".." ) {
		$theme_dir_array[] = $file;
		$sel="";
		
		if (file_exists($curpath.'/template.php')){
			if ($TEMPLATE == $file){ 
				$sel="selected"; 
			}
			
			$theme_options .= '<option '.$sel.' value="'.$file.'" >'.$file.'</option>';
		}
	}
}
$theme_options .= '</select> ';

# check to see how many themes are available
if (count($theme_dir_array) == 1){ $theme_options = ''; }

# if no template is selected, use the default
if ($template == '') { $template = 'template.php'; }
$templates = directoryToArray(GSTHEMESPATH . $TEMPLATE . '/', true);
$theme_templates .= '<span id="themefiles"><select class="text" id="theme_files" style="width:425px;" name="f" >';
$allowed_extensions=array('php','css','js','html','htm');
foreach ($templates as $file){
  $extension=pathinfo($file,PATHINFO_EXTENSION);
  if (in_array($extension, $allowed_extensions)){
  $filename=pathinfo($file,PATHINFO_BASENAME);
  $filenamefull=substr(strstr($file,'/theme/'.$TEMPLATE.'/'),strlen('/theme/'.$TEMPLATE.'/'));   
  if ($TEMPLATE_FILE == $filename){ 
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


register_script('codemirror', $SITEURL.'admin/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
register_script('codemirror-search', $SITEURL.'admin/template/js/codemirror/lib/searchcursor.js', '0.2.0', FALSE);
register_script('codemirror-search-cursor', $SITEURL.'admin/template/js/codemirror/lib/search.js', '0.2.0', FALSE);
register_script('codemirror-dialog', $SITEURL.'admin/template/js/codemirror/lib/dialog.js', '0.2.0', FALSE);
register_script('codemirror-folding', $SITEURL.'admin/template/js/codemirror/lib/foldcode.js', '0.2.0', FALSE);

register_style('codemirror-css',$SITEURL.'admin/template/js/codemirror/lib/codemirror.css','screen',FALSE);
register_style('codemirror-theme',$SITEURL.'admin/template/js/codemirror/theme/default.css','screen',FALSE);
register_style('codemirror-dialog',$SITEURL.'admin/template/js/codemirror/lib/dialog.css','screen',FALSE);

queue_script('codemirror', GSBACK);
queue_script('codemirror-search', GSBACK);
queue_script('codemirror-search-cursor', GSBACK);
queue_script('codemirror-dialog', GSBACK);
queue_script('codemirror-folding', GSBACK);


queue_style('codemirror-css', GSBACK);
queue_style('codemirror-theme', GSBACK);
queue_style('codemirror-dialog', GSBACK);

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('THEME_MANAGEMENT')); 
?>

<?php include('template/include-nav.php'); ?>

<script>
window.onload = function() {
	  var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
	  function keyEvent(cm, e) {
	    if (e.keyCode == 81 && e.ctrlKey) {
	      if (e.type == "keydown") {
	        e.stop();
	        setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
	      }
	      return true;
	    }
	  }
	  function toggleFullscreenEditing()
	    {
	        var editorDiv = $('.CodeMirror-scroll');
	        if (!editorDiv.hasClass('fullscreen')) {
	            toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
	            editorDiv.addClass('fullscreen');
	            editorDiv.height('100%');
	            editorDiv.width('100%');
	            editor.refresh();
	        }
	        else {
	            editorDiv.removeClass('fullscreen');
	            editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
	            editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
	            editor.refresh();
	        }
	    }
      var editor = CodeMirror.fromTextArea(document.getElementById("codetext"), {
        lineNumbers: true,
        matchBrackets: true,
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
        theme:'default',
    	onGutterClick: foldFunc,
    	extraKeys: {"Ctrl-Q": function(cm){foldFunc(cm, cm.getCursor().line);},
    				"F11": toggleFullscreenEditing, "Esc": toggleFullscreenEditing},
        onCursorActivity: function() {
		   	editor.setLineClass(hlLine, null);
		   	hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
		}
      	});
     var hlLine = editor.setLineClass(0, "activeline");
    
     }
     
</script>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		
		
		<div class="main">
		<h3><?php i18n('EDIT_THEME'); ?></h3>
		<form action="<?php myself(); ?>" method="get" accept-charset="utf-8" >
		<p><?php echo $theme_options; ?><?php echo $theme_templates; ?>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="s" value="<?php i18n('EDIT'); ?>" /></p>
		</form>
		
		<p><b><?php i18n('EDITING_FILE'); ?>:</b> <code><?php echo $SITEURL.'theme/'. tsl($TEMPLATE) .'<b>'. $TEMPLATE_FILE; ?></b></code></p>
		<?php $content = file_get_contents(GSTHEMESPATH . tsl($TEMPLATE) . $TEMPLATE_FILE); ?>
		
		<form action="<?php myself(); ?>?t=<?php echo $TEMPLATE; ?>&amp;f=<?php echo $TEMPLATE_FILE; ?>" method="post" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
			<textarea name="content" id="codetext" wrap='off' ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea>
			<input type="hidden" value="<?php echo tsl($TEMPLATE) . $TEMPLATE_FILE; ?>" name="edited_file" />
			<?php exec_action('theme-edit-extras'); ?>
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitsave" value="<?php i18n('BTN_SAVECHANGES'); ?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="theme-edit.php?cancel"><?php i18n('CANCEL'); ?></a>
			</p>
		</form>
		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>

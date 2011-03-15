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
	$nonce = $_POST['nonce'];
	if(!check_nonce($nonce, "save")) {
		die("CSRF detected!");
	}
	
	# save edited template file
	$SavedFile = $_POST['edited_file'];
	$FileContents = stripslashes($_POST['content']);
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
	if( is_dir($curpath) && $file != "." && $file != ".." ) 
	{
		$theme_dir_array[] = $file;
		$sel="";
		
		if (file_exists($curpath.'/template.php'))
		{
			if ($TEMPLATE == $file)
			{ 
				$sel="selected"; 
			}
			
			$theme_options .= '<option '.$sel.' value="'.$file.'" >'.$file.'</option>';
		}
	}
}
$theme_options .= '</select> ';

# check to see how many themes are available
if (count($theme_dir_array) == 1){
	$theme_options = '';
}

# if no template is selected, use the default
if ($template == '') { $template = 'template.php'; }

$templates = get_themes($TEMPLATE);

$theme_templates .= '<span id="themefiles"><select class="text" id="theme_files" style="width:225px;" name="f" >';

foreach ($templates as $file)
{
	if ($TEMPLATE_FILE == $file) 
	{ 
		$sel="selected"; 
	} 
	else 
	{ 
		$sel="";
	}
	
	if ($file == 'template.php')
	{ 
		$templatename=i18n_r('DEFAULT_TEMPLATE'); 
	} 
	else 
	{ 
		$templatename=$file; 
	}
	
	$theme_templates .= '<option '.$sel.' value="'.$file.'" >'.$templatename.'</option>';
}

$theme_templates .= "</select></span>";
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('THEME_MANAGEMENT')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('THEME_MANAGEMENT'); ?> <span>&raquo;</span> <?php i18n('EDIT_THEME'); ?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
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
			<p><textarea name="content" id="codetext" wrap='off' ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea></p>
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

	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

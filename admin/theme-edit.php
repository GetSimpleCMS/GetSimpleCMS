<?php 
/****************************************************
*
* @File: 		theme-edit.php
* @Package:	GetSimple
* @Action:	Displays and changes website settings 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable settings
login_cookie_check();
$theme_options 		= ''; 
$TEMPLATE_FILE 		= ''; 
$template 			= ''; 
$theme_templates 	= '';

// Were changes submitted?
if (isset($_GET['t'])) {
	$_GET['t'] = strippath($_GET['t']);
	if ($_GET['t']&&is_dir($relative.'theme/'.$_GET['t'].'/')) {
		$TEMPLATE = $_GET['t'];
	}
}
if (isset($_GET['f'])) {
	$_GET['f'] = strippath($_GET['f']);
	if ($_GET['f']&&is_file($relative.'theme/'.$TEMPLATE.'/'.$_GET['f'])) {
		$TEMPLATE_FILE = $_GET['f'];
	}
}

// Save?
if((isset($_POST['submitsave'])))
{
	$nonce = $_POST['nonce'];
	if(!check_nonce($nonce, "save"))
		die("CSRF detected!");

	$SavedFile = $_POST['edited_file'];
	$FileContents = stripslashes(htmlspecialchars_decode($_POST['content'], ENT_QUOTES));

	$fh = fopen($relative. 'theme/'. $SavedFile, 'w') or die("can't open file");
	fwrite($fh, $FileContents);
	fclose($fh);
	$success = sprintf($i18n['TEMPLATE_FILE'], $SavedFile);
}


// No template file?
if (! $TEMPLATE_FILE)
{
	$TEMPLATE_FILE = 'template.php';
}


// Setup
$themes_path = $relative. 'theme';
$themes_handle = @opendir($themes_path);
$theme_options .= '<select class="text" style="width:225px;" name="t" id="theme-folder" >';	

while ($file = readdir($themes_handle))
{
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
			
			$theme_options .= '<option '.@$sel.' value="'.$file.'" >'.$file.'</option>';
		}
	}
}

$theme_options .= '</select> ';

// Set options to none
if (count($theme_dir_array) == 1)
{
	$theme_options = '';
}

// No template?
if ($template == '') { $template = 'template.php'; }

// Theme location
$themes_path = $relative. 'theme/'. $TEMPLATE .'/';

// Get files
$themes_handle = @opendir($themes_path);
while ($file = readdir($themes_handle))
{
	if( is_file($themes_path . $file) && $file != "." && $file != ".." )
	{
		$templates[] = $file;
	}
}

// Sort them
sort($templates);

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
		$templatename=$i18n['DEFAULT_TEMPLATE']; 
	} 
	else 
	{ 
		$templatename=$file; 
	}
	
	$theme_templates .= '<option '.@$sel.' value="'.$file.'" >'.$templatename.'</option>';
}

$theme_templates .= "</select></span>";
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['THEME_MANAGEMENT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['THEME_MANAGEMENT']; ?> <span>&raquo;</span> <?php echo $i18n['EDIT_THEME']; ?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		
		
		<div class="main">
		<h3><?php echo $i18n['EDIT_THEME']; ?></h3>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="get" accept-charset="utf-8" >
		<p><?php echo $theme_options; ?><?php echo $theme_templates; ?>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="s" value="<?php echo $i18n['EDIT']; ?>" /></p>
		</form>
		
		<p><b><?php echo $i18n['EDITING_FILE']; ?>:</b> <code><?php echo $SITEURL.'theme/'. tsl($TEMPLATE) .'<b>'. $TEMPLATE_FILE; ?></b></code></p>
		<?php $content = file_get_contents($relative. 'theme/'. tsl($TEMPLATE) . $TEMPLATE_FILE); ?>
		
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>?t=<?php echo $TEMPLATE; ?>&f=<?php echo $TEMPLATE_FILE; ?>" method="post" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save"); ?>" />
			<p><textarea name="content" id="codetext" ><?php echo htmlentities($content, ENT_QUOTES, 'UTF-8'); ?></textarea></p>
			<input type="hidden" value="<?php echo tsl($TEMPLATE) . $TEMPLATE_FILE; ?>" name="edited_file" />
			<?php exec_action('theme-edit-extras'); ?>
			<p><input class="submit" type="submit" name="submitsave" value="<?php echo $i18n['BTN_SAVECHANGES']; ?>" /> &nbsp;&nbsp;<?php echo $i18n['OR']; ?>&nbsp;&nbsp; <a class="cancel" href="theme-edit.php?cancel"><?php echo $i18n['CANCEL']; ?></a></p>
		</form>
		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
	</div>

	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
<?php 
/****************************************************
*
* @File: 		theme-edit.php
* @Package:	GetSimple
* @Action:	Displays and changes website settings 	
*
*****************************************************/

	require_once('inc/functions.php');
	require_once('inc/plugin_functions.php');
	$theme_options = ''; $TEMPLATE_FILE = ''; $template = ''; $theme_templates = '';
	
	$userid = login_cookie_check();

	// were changes submitted?
	if(isset($_GET['f'])) {
		$TEMPLATE_FILE = $_GET['f'];
	}
	if(isset($_GET['t'])) {
		$TEMPLATE = $_GET['t'];
	}
	
	if( (isset($_POST['submitsave'])) ) {
		$SavedFile = $_POST['edited_file'];
		$FileContents = stripslashes(htmlspecialchars_decode($_POST['content'], ENT_QUOTES));

		$fh = fopen('../theme/'. $SavedFile, 'w') or die("can't open file");
		fwrite($fh, $FileContents);
		fclose($fh);
		$success = sprintf($i18n['TEMPLATE_FILE'], $SavedFile);
	}
	
	
	if (! $TEMPLATE_FILE) {
		$TEMPLATE_FILE = 'template.php';
	}
	
	
	$themes_path = "../theme";
	$themes_handle = @opendir($themes_path);
	$theme_options .= '<select class="text" style="width:225px;" name="t" id="theme-folder" >';	

	while ($file = readdir($themes_handle)) {
		$curpath = $themes_path .'/'. $file;
		if( is_dir($curpath) && $file != "." && $file != ".." ) {
			$theme_dir_array[] = $file;
			$sel="";
			if (file_exists($curpath.'/template.php')) {
				if ($TEMPLATE == $file) { $sel="selected";}
				$theme_options .= '<option '.@$sel.' value="'.$file.'" >'.$file.'</option>';
			}
			
		}

  }
  	$theme_options .= '</select> ';
		
	
		if (count($theme_dir_array) == 1) {
			$theme_options = '';
		}
	
		if ($template == '') { $template = 'template.php'; }
		$themes_path = "../theme/". $TEMPLATE ."/";
		
		$themes_handle = @opendir($themes_path);
		while ($file = readdir($themes_handle)) {
			if( is_file($themes_path . $file) && $file != "." && $file != ".." ) {
				$templates[] = $file;
			}
		}
	
		sort($templates);
		
		$theme_templates .= '<span id="themefiles"><select class="text" id="theme_files" style="width:225px;" name="f" >';
		
		foreach ($templates as $file) {
			if ($TEMPLATE_FILE == $file) { $sel="selected"; } else { $sel=""; };
			if ($file == 'template.php') { $templatename=$i18n['DEFAULT_TEMPLATE']; } else { $templatename=$file; }
			$theme_templates .= '<option '.@$sel.' value="'.$file.'" >'.$templatename.'</option>';
	  	}
		$theme_templates .= "</select></span>";

	
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['THEME_MANAGEMENT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['THEME_MANAGEMENT']; ?> <span>&raquo;</span> <?php echo $i18n['EDIT_THEME']; ?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<?php 
	if (isset($success)) {
		echo '<div class="updated">'.$success.'</div>';
	}
	?>
<div class="bodycontent">
	
	<div id="maincontent">
		
		
		<div class="main">
		<h3><?php echo $i18n['EDIT_THEME']; ?></h3>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" accept-charset="utf-8" >
		<p><?php echo $theme_options; ?><?php echo $theme_templates; ?>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="s" value="<?php echo $i18n['EDIT']; ?>" /></p>
		</form>
		
		<p><b><?php echo $i18n['EDITING_FILE']; ?>:</b> <code><?php echo $SITEURL.'theme/'. tsl($TEMPLATE) .'<b>'. $TEMPLATE_FILE; ?></b></code></p>
		<?php $content = file_get_contents('../theme/'. tsl($TEMPLATE) . $TEMPLATE_FILE); ?>
		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>?t=<?php echo $TEMPLATE; ?>&f=<?php echo $TEMPLATE_FILE; ?>" method="post" >
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
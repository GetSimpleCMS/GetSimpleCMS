<?php 
/**
 * Theme
 *
 * @package GetSimple
 * @subpackage Theme
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-theme');

# was the form submitted?
if( (isset($_POST['submitted'])) && (isset($_POST['template'])) ) {

	check_for_csrf("activate");	
		
	# get passed value from form
	$newTemplate = var_in($_POST['template']);

	if(!path_is_safe(GSTHEMESPATH.$newTemplate,GSTHEMESPATH)) die();

	# backup old GSWEBSITEFILE (website.xml) file
	backup_datafile(GSDATAOTHERPATH.GSWEBSITEFILE);
	
	# udpate GSWEBSITEFILE (website.xml) file with new theme
	$xml = getXML(GSDATAOTHERPATH.GSWEBSITEFILE);
	$xml->editAddCData('TEMPLATE',$newTemplate);
	$status = XMLsave($xml,GSDATAOTHERPATH.GSWEBSITEFILE);
	
	$success = i18n_r('THEME_CHANGED');

	$TEMPLATE = $newTemplate; // set new global
}

# get available themes, using folder match required contain template.php
$themes = getDirs(GSTHEMESPATH,GSTEMPLATEFILE);
$theme_options 	= '';

foreach($themes as $theme){
	$sel       = $TEMPLATE == $theme ? 'selected' : '';
	$themename = $TEMPLATE == $theme ? $theme. ' ('.i18n_r('ACTIVE').')' : $theme;
	$theme_options .= '<option '.$sel.' value="'.$theme.'" >'.$themename.'</option>';
}

$pagetitle = i18n_r('THEME_MANAGEMENT');
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
			<h3 class="floated"><?php i18n('CHOOSE_THEME');?></h3>
			<div class="edit-nav clearfix" >
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>			
		<form action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
				<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("activate"); ?>" />			
			<?php	
				$theme_path = str_replace(GSROOTPATH,'',GSTHEMESPATH);
				if ( $SITEURL ) {	
					echo '<p><b>'.i18n_r('THEME_PATH').': &nbsp;</b> <code>'.$SITEURL.$theme_path.$TEMPLATE.'/</code></p>';
				}
			?>
				<p>
					<select id="theme_select" class="text"  name="template" >
					<?php echo $theme_options; ?>
					</select>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="submitted" value="<?php i18n('ACTIVATE_THEME');?>" />
				</p>
			</form>
			<?php
			 	if (file_exists(GSTHEMESPATH.$TEMPLATE.'/images/screenshot.png')) { 
					echo '<p><img id="theme_preview" src="../'.$theme_path.$TEMPLATE.'/images/screenshot.png" alt="'.i18n_r('THEME_SCREENSHOT').'" /></p>';
					echo '<span id="theme_no_img" style="visibility:hidden"><p><em>'.i18n_r('NO_THEME_SCREENSHOT').'</em></p></span>';				
				} else {
					echo '<p><img id="theme_preview" style="visiblity:hidden;"" src="../'.$theme_path.$TEMPLATE.'/images/screenshot.png" alt="'.i18n_r('THEME_SCREENSHOT').'" /></p>';				
					echo '<span id="theme_no_img"><p><em>'.i18n_r('NO_THEME_SCREENSHOT').'</em></p></span>';
				}

				exec_action('theme-extras'); //@hook theme-extras after theme html output
			?>
				
		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>
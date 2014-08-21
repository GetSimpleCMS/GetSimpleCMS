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

# was the form submitted?
if( (isset($_POST['submitted'])) && (isset($_POST['template'])) ) {

	check_for_csrf("activate");	
		
	# get passed value from form
	$newTemplate = var_in($_POST['template']);

	if(!path_is_safe(GSTHEMESPATH.$newTemplate,GSTHEMESPATH)) die();

	# backup old GSWEBSITEFILE (website.xml) file
	backup_datafile(GSDATAOTHERPATH.GSWEBSITEFILE);
	
	# udpate GSWEBSITEFILE (website.xml) file with new theme
	$xml  = new SimpleXMLExtended('<item></item>');
	$note = $xml->addChild('SITENAME');
	$note->addCData($SITENAME);
	$note = $xml->addChild('SITEURL');
	$note->addCData($SITEURL);
	$note = $xml->addChild('TEMPLATE');
	$note->addCData($newTemplate);
	$xml->addChild('PRETTYURLS', $PRETTYURLS);
	$xml->addChild('PERMALINK', $PERMALINK);
	XMLsave($xml, GSDATAOTHERPATH . GSWEBSITEFILE);
	
	$success = i18n_r('THEME_CHANGED');

	$TEMPLATE = $newTemplate; // set new global
}

# get available themes (only look for folders)
# @todo replace with getfiles

$themes = getDirs(GSTHEMESPATH,GSTEMPLATEFILE);
$theme_options 	= '';

foreach($themes as $theme){
	$sel = $TEMPLATE == $theme ? 'selected' : '';
	$theme_options .= '<option '.$sel.' value="'.$theme.'" >'.$theme.'</option>';
}

$pagetitle = i18n_r('THEME_MANAGEMENT');
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
		<h3><?php i18n('CHOOSE_THEME');?></h3>
		<form action="" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("activate"); ?>" />			
		<?php	
			$theme_path = str_replace(GSROOTPATH,'',GSTHEMESPATH);
			if ( $SITEURL ) {	
				echo '<p><b>'.i18n_r('THEME_PATH').': &nbsp;</b> <code>'.$SITEURL.$theme_path.$TEMPLATE.'/</code></p>';
			}
		?>
		<p><select id="theme_select" class="text" style="width:250px;" name="template" >
				<?php echo $theme_options; ?>
			</select>&nbsp;&nbsp;&nbsp;<input class="submit" type="submit" name="submitted" value="<?php i18n('ACTIVATE_THEME');?>" /></p>
		</form>
		<?php
		 	if (file_exists(GSTHEMESPATH.$TEMPLATE.'/images/screenshot.png')) { 
				echo '<p><img id="theme_preview" style="border:2px solid #333;" src="../'.$theme_path.$TEMPLATE.'/images/screenshot.png" alt="'.i18n_r('THEME_SCREENSHOT').'" /></p>';
				echo '<span id="theme_no_img" style="visibility:hidden"><p><em>'.i18n_r('NO_THEME_SCREENSHOT').'</em></p></span>';				
			} else {
				echo '<p><img id="theme_preview" style="visiblity:hidden;border:2px solid #333;" src="../'.$theme_path.$TEMPLATE.'/images/screenshot.png" alt="'.i18n_r('THEME_SCREENSHOT').'" /></p>';				
				echo '<span id="theme_no_img"><p><em>'.i18n_r('NO_THEME_SCREENSHOT').'</em></p></span>';
			}

			exec_action('theme-extras');
		?>
			
		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>
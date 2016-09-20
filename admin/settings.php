<?php 
/**
 * Settings
 *
 * Displays and changes website settings 
 *
 * @package GetSimple
 * @subpackage Settings
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-settings');

# variable settings
$fullpath   = suggest_site_path();
$lang_array = getFiles(GSLANGPATH);

# initialize these all as null
$error = $success = $prettychck = null;
$prettyinput = '';

# if the flush cache command was invoked
if (isset($_GET['flushcache'])) {
	delete_cache();
	exec_action('flushcache'); // @hook flushcache cache was deleted
	$update = 'flushcache-success';
}

# if the undo command was invoked
if (isset($_GET['undo'])) {
	check_for_csrf("undo");
	# perform undo
	restore_datafile(GSDATAOTHERPATH . GSWEBSITEFILE);
	generate_sitemap();
	
	# redirect back to yourself to show the new restored data
	redirect('settings.php?upd=settings-restored');
}

# was the form submitted?
if(isset($_POST['submitted'])) {

	check_for_csrf("save_settings");	
		
	# website-specific fields
	if(isset($_POST['sitename'])) { 
		$SITENAME = htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'); 
	}
	if(isset($_POST['siteurl'])) { 
		$SITEURLNEW = tsl($_POST['siteurl']); 
	}
	if(isset($_POST['permalink'])) { 
		$PERMALINK = var_in(trim($_POST['permalink']));
	}
	if(isset($_POST['template'])) { 
		// $TEMPLATE = $_POST['template'];
	}
	if(isset($_POST['prettyurls'])) {
	  $PRETTYURLS = $_POST['prettyurls'];
	} else {
		$PRETTYURLS = '';
	}
	if(isset($_POST['email'])) {
		$SITEEMAIL = var_in($_POST['email'],'email');
	}
	if(isset($_POST['timezone'])) {
		$SITETIMEZONE = var_in($_POST['timezone']);
	}
	if(isset($_POST['lang'])) {
		$SITELANG = var_in($_POST['lang']);
	}
	if(isset($_POST['about'])) {
		$SITEABOUT = var_in($_POST['about']);
	}

	// check valid lang files
	if(!in_array($SITELANG.'.php', $lang_array) and !in_array($SITELANG.'.PHP', $lang_array)) die("invalid lang"); 

	# create website xml file
	backup_datafile(GSDATAOTHERPATH . GSWEBSITEFILE);

    # udpate GSWEBSITEFILE (website.xml) file with new settings
	$xmls = getXML(GSDATAOTHERPATH.GSWEBSITEFILE,false);
	$xmls->editAddCData('SITENAME',$SITENAME);
	$xmls->editAddCData('SITEURL',$SITEURLNEW);
	$xmls->editAddCData('TEMPLATE',$TEMPLATE);
	$xmls->editAddChild('PRETTYURLS', $PRETTYURLS);
	$xmls->editAddChild('PERMALINK', var_out($PERMALINK));
	$xmls->editAddChild('EMAIL', $SITEEMAIL);
	$xmls->editAddChild('TIMEZONE', $SITETIMEZONE);
	$xmls->editAddChild('LANG', $SITELANG);
	$xmls->editAddChild('SITEUSR', $SITEUSR);
	$xmls->editAddChild('SITEABOUT', $SITEABOUT);
	
	exec_action('settings-website'); // @hook settings-website website data file before save
	
	if (! XMLsave($xmls, GSDATAOTHERPATH . GSWEBSITEFILE) ) {
		$error = i18n_r('CHMOD_ERROR');
	}

	if (!$error) {
		generate_sitemap();
		GLOBAL $SITEURLABS;
		if($SITEURLNEW !== $SITEURLABS) $SITEURLABS = $SITEURLNEW;
		// ALWAYS RELOAD ON SETTINGS SAVE, TO APPLY SITE WIDE VARAIBLE CHANGES
		redirect('settings.php?upd=settings-success');
	}
}

# are any of the control panel checkboxes checked?
$prettyinput = 'disabled';
if ($PRETTYURLS != '' ) { $prettychck = 'checked'; $prettyinput = '';}

# get all available language files
if ($SITELANG == ''){ $SITELANG = GSDEFAULTLANG; }

if (count($lang_array) != 0) {
	sort($lang_array);
	$sel = ''; $langs = '';
	foreach ($lang_array as $lfile){
		$lfile = basename($lfile,".php");
		if ($SITELANG == $lfile) { $sel="selected"; }
		$langs .= '<option '.$sel.' value="'.$lfile.'" >'.$lfile.'</option>';
		$sel = '';
	}
} else {
	$langs = '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>';
}

$pagetitle = i18n_r('GENERAL_SETTINGS');
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<form class="largeform" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_settings"); ?>" />
		
		<div class="main">
			<h3 class="floated"><?php i18n('WEBSITE_SETTINGS');?></h3>
			<div class="edit-nav clearfix" >
				<a id="flushcache" class="" title="<?php i18n('FLUSHCACHE'); ?>" href="?flushcache"><?php i18n('FLUSHCACHE'); ?></a>
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>
			<div class="leftsec">
				<p><label for="sitenameinput" ><?php i18n('LABEL_WEBSITE');?>:</label><input class="text" id="sitenameinput" name="sitename" type="text" value="<?php echo stripslashes($SITENAME); ?>" /></p>
			</div>
			<div class="rightsec">
				<p>
					<label for="siteurl" ><?php i18n('LABEL_BASEURL');?>:</label>
					<input class="text" id="siteurl" name="siteurl" type="url" value="<?php echo getSiteURL(true);?>" />
				</p>
				<?php	if ( $fullpath != getSiteURL(true) ) {	echo '<span class="input-warning" >'.i18n_r('LABEL_SUGGESTION').': &nbsp; <code>'.$fullpath.'</code></span>';	}	?>
			</div>
			<div class="clear"></div>
			<div class="leftsec">
				<p>
				<label for="timezone" ><?php i18n('LOCAL_TIMEZONE');?>:</label>
				<select class="text" id="timezone" name="timezone"> 
				<?php if ($SITETIMEZONE == '') { echo '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>'; } else { echo '<option selected="selected"  value="'. $SITETIMEZONE .'">'. $SITETIMEZONE .'</option>'; } ?>
				<?php include('inc/timezone_options.txt'); ?>
				</select>
				</p>
			</div>
			<div class="rightsec">
				<p>
					<label for="lang" ><?php i18n('LANGUAGE');?>: 
						<span class="right">
							<a href="http://get-simple.info/docs/languages" target="_blank" ><?php i18n('MORE');?></a>
						</span>
					</label>
					<select name="lang" id="lang" class="text">
					<?php echo $langs; ?>
					</select>
				</p>
			</div>
			<div class="clear"></div>
			<div class="leftsec">
				<p>
					<label for="email" ><?php i18n('LABEL_EMAIL');?>:</label>
					<input class="text" id="email" name="email" type="email" value="<?php echo var_out($SITEEMAIL); ?>" />
				</p>
				<?php if (! check_email_address($SITEEMAIL)) {
					echo '<span class="input-warning">'.i18n_r('WARN_EMAILINVALID').'</span>';
				}?>
			</div>
			<div class="widesec">
				<p>
					<label for="about" ><?php i18n('LABEL_SITEABOUT');?>:</label>
					<textarea class="text short" id="about" name="about" type="about" /><?php echo ($SITEABOUT); ?></textarea>
				</p>
			</div>
			<div class="clear"></div>
			<p></p>
			<h3><?php i18n('URL_SETTINGS');?></h3>
			<div class="wideopt">
				<p class="inline" >
					<input name="prettyurls" id="prettyurls" type="checkbox" value="1" <?php echo $prettychck; ?>  /> &nbsp;
					<label for="prettyurls" ><?php i18n('USE_FANCY_URLS');?></label>
				</p>
				<p>
					<label for="permalink"  class="clearfix"><?php i18n('PERMALINK');?>: 
						<span class="right">
							<a href="http://get-simple.info/docs/pretty_urls" target="_blank" ><?php i18n('MORE');?></a>
						</span>
					</label>
					<input class="text" name="permalink" id="permalink" type="text" placeholder="<?php echo getDef('GSDEFAULTPERMALINK');?>" value="<?php if(isset($PERMALINK)) { echo var_out($PERMALINK); } ?>" <?php echo $prettyinput;?> />
				</p>
			</div>
			<div class="clear"></div>
			<?php exec_action('settings-website-extras'); // @hook setting-website-extras after website settings html output ?>
			<p id="submit_line" >
				<span>
					<input class="submit" type="submit" name="submitted" value="<?php i18n('BTN_SAVESETTINGS');?>" />
				</span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; 
				<a class="cancel" href="settings.php?cancel"><?php i18n('CANCEL'); ?></a>
			</p>

		</div><!-- /main -->
	</form>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-settings.php'); ?>		
	</div>

</div>
<?php get_template('footer'); ?>
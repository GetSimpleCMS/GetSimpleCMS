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

# if the flush cache command was invoked
if (isset($_GET['flushcache'])) {
	delete_cache();
	$update = 'flushcache-success';
}

# if the undo command was invoked
if (isset($_GET['undo'])) {
	check_for_csrf("undo");
	# perform undo
	restore_datafile(GSWEBSITEFILE);
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
	if(!in_array($LANG.'.php', $lang_array) and !in_array($LANG.'.PHP', $lang_array)) die(); 

	# create website xml file
	backup_datafile(GSWEBSITEFILE);

	// new xml
	$xmls = new SimpleXMLExtended('<item></item>');
	$note = $xmls->addChild('SITENAME');
	$note->addCData($SITENAME);
	$note = $xmls->addChild('SITEURL');
	$note->addCData($SITEURLNEW);
	$note = $xmls->addChild('TEMPLATE');
	$note->addCData($TEMPLATE);
	$xmls->addChild('PRETTYURLS', $PRETTYURLS);
	$xmls->addChild('PERMALINK', $PERMALINK);
	$xmls->addChild('EMAIL', $SITEEMAIL);
	$xmls->addChild('TIMEZONE', $SITETIMEZONE);
	$xmls->addChild('LANG', $SITELANG);
	$xmls->addChild('SITEUSR', $SITEUSR);
	$xmls->addChild('SITEABOUT', $SITEABOUT);
	
	exec_action('settings-website'); // @hook settings-website website data file before save
	
	if (! XMLsave($xmls, GSDATAOTHERPATH . GSWEBSITEFILE) ) {
		$error = i18n_r('CHMOD_ERROR');
	}

	# see new language file immediately
	$newlang = getDefaultLang();
	include(GSLANGPATH.$newlang.'.php');
	
	if (!$error) {
		$success = i18n_r('ER_SETTINGS_UPD').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>';
		generate_sitemap();
	}
		
}

# are any of the control panel checkboxes checked?
if ($PRETTYURLS != '' ) { $prettychck = 'checked'; }

# get all available language files
if ($LANG == ''){ $LANG = GSDEFAULTLANG; }

if (count($lang_array) != 0) {
	sort($lang_array);
	$sel = ''; $langs = '';
	foreach ($lang_array as $lfile){
		$lfile = basename($lfile,".php");
		if ($LANG == $lfile) { $sel="selected"; }
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
				<p><label for="sitename" ><?php i18n('LABEL_WEBSITE');?>:</label><input class="text" id="sitename" name="sitename" type="text" value="<?php echo stripslashes($SITENAME); ?>" /></p>
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
				<p class="inline" >
					<input name="prettyurls" id="prettyurls" type="checkbox" value="1" <?php echo $prettychck; ?>  /> &nbsp;
					<label for="prettyurls" ><?php i18n('USE_FANCY_URLS');?></label>
				</p>
			<div class="leftsec">
				<p>
					<label for="permalink"  class="clearfix"><?php i18n('PERMALINK');?>: 
						<span class="right">
							<a href="http://get-simple.info/docs/pretty_urls" target="_blank" ><?php i18n('MORE');?></a>
						</span>
					</label>
					<input class="text" name="permalink" id="permalink" type="text" placeholder="%parent%/%slug%/" value="<?php if(isset($PERMALINK)) { echo $PERMALINK; } ?>" />
				</p>
			</div>
			<div class="rightsec">
				<p>
					<label for="email" ><?php i18n('LABEL_EMAIL');?>:</label>
					<input class="text" id="email" name="email" type="email" value="<?php echo $SITEEMAIL; ?>" />
				</p>
				<?php if (! check_email_address($SITEEMAIL)) {
					echo '<span class="input-warning">'.i18n_r('WARN_EMAILINVALID').'</span>';
				}?>
			</div>
			<div class="clear"></div>
			<div class="widesec">
				<p>
					<label for="about" ><?php i18n('LABEL_SITEABOUT');?>:</label>
					<textarea class="text short" id="about" name="about" type="about" /><?php echo $SITEABOUT; ?></textarea>
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
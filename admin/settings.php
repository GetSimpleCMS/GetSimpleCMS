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

# variable settings
$fullpath   = suggest_site_path();
$wfile      = 'website.xml';
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
	$bakpath = GSBACKUPSPATH .getRelPath(GSDATAOTHERPATH,GSDATAPATH); // backups/other/
	undo($wfile, GSDATAOTHERPATH, $bakpath);
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
		$SITEURL = tsl($_POST['siteurl']); 
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

	/*
	// email, timezone to replace gsconfig settings GSFROMEMAIL, GSTIMEZONE,  and global $lang on front end
	if(isset($_POST['email'])) { 
		$EMAIL = var_in($_POST['email'],'email');
	} 
	if(isset($_POST['timezone'])) { 
		$TIMEZONE = var_in($_POST['timezone']);
	}
	if(isset($_POST['lang'])) { 
		$LANG = var_in($_POST['lang']);
	}
	*/

	// check valid lang files
	if(!in_array($LANG.'.php', $lang_array) and !in_array($LANG.'.PHP', $lang_array)) die(); 

	# create website xml file
	$bakpath = GSBACKUPSPATH .getRelPath(GSDATAOTHERPATH,GSDATAPATH); // backups/other/
	createBak($wfile, GSDATAOTHERPATH, $bakpath);
	$xmls = new SimpleXMLExtended('<item></item>');
	$note = $xmls->addChild('SITENAME');
	$note->addCData($SITENAME);
	$note = $xmls->addChild('SITEURL');
	$note->addCData($SITEURL);
	$note = $xmls->addChild('TEMPLATE');
	$note->addCData($TEMPLATE);
	$xmls->addChild('PRETTYURLS', $PRETTYURLS);
	$xmls->addChild('PERMALINK', $PERMALINK);
	
	exec_action('settings-website');
	
	if (! XMLsave($xmls, GSDATAOTHERPATH . $wfile) ) {
		$error = i18n_r('CHMOD_ERROR');
	}

	# see new language file immediately
	include(GSLANGPATH.$LANG.'.php');
	
	if (!$error) {
		$success = i18n_r('ER_SETTINGS_UPD').'. <a href="settings.php?undo&nonce='.get_nonce("undo").'">'.i18n_r('UNDO').'</a>';
		generate_sitemap();
	}
		
}

# are any of the control panel checkboxes checked?
if ($PRETTYURLS != '' ) { $prettychck = 'checked'; }

# get all available language files
if ($LANG == ''){ $LANG = 'en_US'; }

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

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('GENERAL_SETTINGS')); 

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<form class="largeform" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_settings"); ?>" />
		
		<div class="main">
			<h3><?php i18n('WEBSITE_SETTINGS');?></h3>
			
			<div class="leftsec">
				<p><label for="sitename" ><?php i18n('LABEL_WEBSITE');?>:</label><input class="text" id="sitename" name="sitename" type="text" value="<?php if(isset($SITENAME1)) { echo stripslashes($SITENAME1); } else { echo stripslashes($SITENAME); } ?>" /></p>
			</div>
			<div class="rightsec">
				<p><label for="siteurl" ><?php i18n('LABEL_BASEURL');?>:</label><input class="text" id="siteurl" name="siteurl" type="url" value="<?php if(isset($SITEURL1)) { echo $SITEURL1; } else { echo $SITEURL; } ?>" /></p>
				<?php	if ( $fullpath != $SITEURL ) {	echo '<p style="margin:-15px 0 20px 0;color:#D94136;font-size:11px;" >'.i18n_r('LABEL_SUGGESTION').': &nbsp; <code>'.$fullpath.'</code></p>';	}	?>
			</div>
			<div class="clear"></div>
			
			<p class="inline" ><input name="prettyurls" id="prettyurls" type="checkbox" value="1" <?php echo $prettychck; ?>  /> &nbsp;<label for="prettyurls" ><?php i18n('USE_FANCY_URLS');?></label></p>
					
			<div class="leftsec">
				<p><label for="permalink"  class="clearfix"><?php i18n('PERMALINK');?>: <span class="right"><a href="http://get-simple.info/docs/pretty_urls" target="_blank" ><?php i18n('MORE');?></a></span></label><input class="text" name="permalink" id="permalink" type="text" placeholder="%parent%/%slug%/" value="<?php if(isset($PERMALINK)) { echo $PERMALINK; } ?>" /></p>
			<a id="flushcache" class="button" href="?flushcache"><?php i18n('FLUSHCACHE'); ?></a>
			</div>
			<div class="clear"></div>
			
			<?php exec_action('settings-website-extras'); ?>
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitted" value="<?php i18n('BTN_SAVESETTINGS');?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="settings.php?cancel"><?php i18n('CANCEL'); ?></a>
			</p>

		</div><!-- /main -->
	</form>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-settings.php'); ?>		
	</div>

</div>
<?php get_template('footer'); ?>
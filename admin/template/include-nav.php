<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Navigation Include Template
 *
 * @package GetSimple
 */

// @todo add icon code to this, so can add support for layered
// add prefix qualifiers
$icondefinition = array(
	'TAB_pages'        => '<i class="far fa-fw fa-list-alt"></i>',// th th-list table database align-left list-ul list-alt files-o copy
	'TAB_edit'         => '<i class="fa fa-fw fa-edit"></i>',// edit fa-plus
	'TAB_menu-manager' => '<i class="fa fa-fw fa-sitemap"></i>',// list
	'TAB_upload'       => '<i class="fa fa-fw fa-copy"></i>',// upload upload-cloud-alt
	'TAB_theme'        => '<i class="fa fa-fw fa-image"></i>',// image
	'TAB_theme-edit'   => '<i class="fa fa-fw fa-code"></i>',// indent code
	'TAB_components'   => '<i class="fa fa-fw fa-cubes"></i>',// cubes
	'TAB_snippets'     => '<i class="fa fa-fw fa-quote-left"></i>',// quote-left cut cube
	'TAB_sitemap'      => '<i class="fa fa-fw fa-map"></i>',// sitemap globe
	'TAB_backups'      => '<i class="fa fa-fw fa-history"></i>',// history 
	'TAB_archive'      => '<i class="fa fa-fw fa-archive"></i>',// archive file-archive
	'TAB_plugins'      => '<i class="fa fa-fw fa-plug"></i>',// plug
	'TAB_support'      => '<i class="fa fa-fw fa-life-ring"></i>',// first-aid med-kit
	'TAB_health-check' => '<i class="fa fa-fw fa-medkit"></i>',// life-ring, clipboard-check
	'TAB_log'          => '<i class="fa fa-fw fa-file-alt"></i>',// paper-plane shield
	'TAB_settings'     => '<i class="fa fa-fw fa-cogs"></i>',// cog cogs sliders
	'TAB_profile'      => '<i class="fa fa-fw fa-address-card"></i>', // user address-card
	'TAB_logout'       => '<i class="fa fa-fw fa-fw fa-sign-out-alt"></i>',
	'TAB_welcome'      => '<i class="fa fa-fw fa-user-circle icon-left"></i>',
	'TAB_development'  => '<i class="fa fa-fw fa-flask"></i>',
	'TAB_debugmode'    => '<i class="fa fa-fw fa-wrench"></i>',
	'TAB_update'       => '<i class="fa fa-fw fa-lg fa-cloud-download"></i>',
	'TAB_'    => ''
);

define('GSTABS','pages,edit,upload,theme,backups,plugins,snippets,components,settings,profile,theme-edit,menu-manager,sitemap,backups,archive,support,health-check,log'); // (str) csv list of page ids and order to show tabs

$debugInfoUrl = 'http://get-simple.info/docs/debugging';

if (cookie_check()) {
	echo '<ul id="pill"><li class="rightnav"><a href="logout.php" accesskey="'. find_accesskey(i18n_r('TAB_LOGOUT')).'" >'.i18n_r('TAB_LOGOUT').' '.$icondefinition["TAB_logout"].'</a></li>';
	echo '<li class="leftnav" ><a href="profile.php">'.$icondefinition["TAB_welcome"].i18n_r('WELCOME').' <strong>'.$USR.'</strong></a></li></ul>';	
}

//determine page type if plugin is being shown
if (get_filename_id() == 'load') {
	$plugin_class = $plugin_info[$plugin_id]['page_type'];
} else {
	$plugin_class = '';
}

?>
<h1 id="sitename"><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?> <i class="icon fa fa-external-link"></i></a></h1>

<ul class="nav secondary">
<li id="nav_loaderimg" ><img class="toggle" id="loader" src="template/images/ajax.gif" alt="" /></li>
<li class="rightnav" ><a class="settings first" href="settings.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SETTINGS'));?>" ><?php i18n('TAB_SETTINGS');?></a></li>
<li class="rightnav" ><a class="support last" href="support.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SUPPORT'));?>" ><?php i18n('TAB_SUPPORT');?></a></li>
<?php

// nav status labels
if(isDebug()) echo '<li class="rightnav"><a class="label label-error label_4_80" href="health-check.php#debuginfo" title="'.i18n_r('DEBUG_MODE').' - '.i18n_r('ON').'"><span>'.$icondefinition["TAB_debugmode"].'</span></a></li>';

if( allowVerCheck() ) {
	$verstatus = getVerCheck()->status;
	if($verstatus == 0){
		// update available newer than current
		echo '<li class="rightnav"><a class="label label-gold" href="health-check.php" title="'.i18n_r('UPG_NEEDED').'">'.$icondefinition["TAB_update"].'</span></a></li>';
	}
}
if(isBeta() || isAlpha()) echo '<li class="rightnav"><a class="label label-ghost" href="health-check.php" title="'.(isAlpha() ? i18n_r('ALPHA') : i18n_r('BETA')).' - '.i18n_r('WEB_HEALTH_CHECK').'"><span>'.$icondefinition["TAB_development"].' '. (isAlpha() ? i18n_r('ALPHA') : i18n_r('BETA')) .'</span></a></li>';

?>
</ul>

<ul class="nav <?php echo $plugin_class; ?>">

<?php 
	
	$tabs    = explode(',',getDef('GSTABS'));
	// $tabs  = array_keys($sidemenudefinition); // debug all
	$current = get_filename_id();
	// if current tab is not in GSTABS, then set its parent tab as current
	if(!in_array($current,$tabs)){
		if(isset($sidemenudefinition[$current]) && !empty($sidemenudefinition[$current])) $current = $sidemenudefinition[$current];
	}

	if($tabs){
		foreach($tabs as $tab){
			if(empty($tab)) continue;
			$tabtitle = i18n_r('TAB_'.uppercase($tab));
			$class = $tab == $current ? ' current' : '';
			$icon = "";
			if(getDef("GSTABICONS",true)) $icon = $icondefinition["TAB_".$tab]." ";
			echo '<li id="nav_'.$tab.'" ><a class="'.$tab.$class.'" href="'.$tab.'.php" accesskey="'. find_accesskey($tabtitle).'" >'.$icon.$tabtitle.'</a></li>';
		}
	}
	
	exec_action('nav-tab'); // @hook nav-tab backend after navigation tab list html output
?>
</ul>


</div>
</div>

<div class="wrapper">

<?php include('template/error_checking.php'); ?>
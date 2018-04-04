<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Navigation Include Template
 *
 * @package GetSimple
 */

// define('GSTABS','pages,edit,upload,theme,backups,plugins,snippets,components,settings,profile,theme-edit,menu-manager,sitemap,backups,archive,support,health-check,log'); // (str) csv list of page ids and order to show tabs

// @todo move to global location
$debugInfoUrl = 'http://get-simple.info/docs/debugging';

if (cookie_check()) {
	echo '<ul id="pill"><li class="rightnav"><a href="logout.php" accesskey="'. find_accesskey(i18n_r('TAB_LOGOUT')).'" >'.i18n_r('TAB_LOGOUT').' '.getIcon("TAB_logout").'</a></li>';
	echo '<li class="leftnav" ><a href="profile.php">'.getIcon("TAB_welcome").i18n_r('WELCOME').' <strong>'.$USR.'</strong></a></li></ul>';	
}

//determine page type if plugin is being shown
if (isPage('load')) {
	$plugin_class = $plugin_info[$plugin_id]['page_type'];
} else {
	$plugin_class = '';
}

?>
<h1 id="sitename"><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); echo '<span class="icon"> '.getIcon("ICO_linkext").'</span>';?></a></h1>

<ul class="nav secondary">
<li class="rightnav" ><a class="settings first" href="settings.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SETTINGS'));?>" ><?php i18n('TAB_SETTINGS');?></a></li>
<li class="rightnav" ><a class="support last" href="support.php" accesskey="<?php echo find_accesskey(i18n_r('TAB_SUPPORT'));?>" ><?php i18n('TAB_SUPPORT');?></a></li>
<?php

// nav status labels
if(isDebug()) echo '<li class="rightnav"><a class="label label-error label_4_80" href="health-check.php#debuginfo" title="'.i18n_r('DEBUG_MODE').' - '.i18n_r('ON').'"><span>'.getIcon("TAB_debugmode").'</span></a></li>';

if( allowVerCheck() ) {
	$verstatus = getVerCheck()->status;
	if($verstatus == 0){
		// update available newer than current
		echo '<li class="rightnav"><a class="label label-gold" href="health-check.php" title="'.i18n_r('UPG_NEEDED').'">'.getIcon("TAB_update").'</span></a></li>';
	}
}
if(isBeta() || isAlpha()) echo '<li class="rightnav"><a class="label label-ghost" href="health-check.php" title="'.(isAlpha() ? i18n_r('ALPHA') : i18n_r('BETA')).' - '.i18n_r('WEB_HEALTH_CHECK').'"><span>'.getIcon("TAB_development").' '. (isAlpha() ? i18n_r('ALPHA') : i18n_r('BETA')) .'</span></a></li>';

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
			if(getDef("GSTABICONS",true)) $icon = getIcon("TAB_".$tab)." ";
			echo '<li id="nav_'.$tab.'" ><a class="'.$tab.$class.'" href="'.$tab.'.php" accesskey="'. find_accesskey($tabtitle).'" >'.$icon.$tabtitle.'</a></li>';
		}
	}
	
	exec_action('nav-tab'); // @hook nav-tab backend after navigation tab list html output
?>
<li id="nav_loaderimg"><img class="toggle" id="loader" src="template/images/ajax.gif" alt="" /></li>
</ul>

</div>
</div>

<div class="wrapper">

<?php include('template/error_checking.php'); ?>
<?php
/**
 * All Plugins
 *
 * Displays all installed plugins 
 *
 * @package GetSimple
 * @subpackage Plugins
 */
 
// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

exec_action('load-plugins');

$pluginid = isset($_GET['set']) ? $_GET['set'] : null;
$nonce    = isset($_GET['nonce']) ? $_GET['nonce'] : null;

if ($pluginid){
	if(check_nonce($nonce, "set_".pathinfo_filename($pluginid), "plugins.php")) {
		$plugin = antixss($pluginid);
		$success = change_plugin($plugin);
		if(!is_null($success)) redirect('plugins.php?success='.urlencode(i18n_r('PLUGIN_UPDATED')));
	}

	redirect('plugins.php?error='.urlencode(i18n_r('ERROR_OCCURED')));
}

// Variable settings
$counter     = 0;
$table       = '';
$needsupdate = false;

plugin_info_update();
$plugin_info_sorted = subval_sort($plugin_info,'name');

foreach ($plugin_info_sorted as $pluginid=>$plugininfo) {

	$setNonce = '&amp;nonce='.get_nonce("set_".$pluginid,"plugins.php");

	$pluginver  = $plugininfo['version'] == 'disabled' ? 0 : $plugininfo['version'];

	if (pluginIsActive($pluginid)) {
		// $cls_Enabled  = 'hidden';
		$cls_Disabled = '';
		$trclass      = 'enabled';
		$icon         = '<a href="plugins.php?set='.$pluginid.$setNonce.'" title="'.i18n_r('DISABLE').'">'.getIcon("ICO_plugon").'</a>';
	} else {
		$cls_Enabled  = '';
		// $cls_Disabled = 'hidden';
		$trclass      = 'disabled';
		$icon         = getIcon("ICO_plugoff");
	}

	// get extend api for this plugin filename
	$updatelink = '';

	// api success
	if (isset($plugininfo['apipath'])) {
		$apiver  = $plugininfo['apiver'];
		$apipath = $plugininfo['apipath'];
		// show update available link
		if ($pluginver >0 && version_compare($apiver,$pluginver,'>')) {
			$updatelink  = '<br /><a class="updatelink" href="'.$apipath.'" target="_blank">'.i18n_r('UPDATE_AVAILABLE').' '.$apiver.'</a>';
			$needsupdate = true;
		}

		$plugin_title = '<a href="'.$apipath.'" target="_blank">'.$plugininfo['name'].'</a>';
	} else {
		// api fail , does not exist in extend
		$plugin_title = $plugininfo['name'];
	}

	$table .= '<tr id="tr-'.$counter.'" class="'.$trclass.'" >';
	$table .= '<td class="title break" >'.$icon.'&nbsp;&nbsp;<b>'.$plugin_title.'</b></td>';
	$table .= '<td class="break"><span>'.$plugininfo['description'].'</span>'; // desc empty if inactive

	// if plugin is active, show what we know from register_plugin, version , author
	if ($pluginver > 0){
		$table .= '<span><br /><b>'.i18n_r('PLUGIN_VER') .' '. $pluginver.'</b> &mdash; '.i18n_r('AUTHOR').': <a href="'.$plugininfo['author_url'].'" target="_blank">'.$plugininfo['author'].'</a></span>';
	}

  	$table.= $updatelink.'</td><td class="status" >';
	if(!pluginIsActive($pluginid)) $table.= '<a href="plugins.php?set='.$pluginid.$setNonce.'" class="toggleEnable '.$cls_Enabled.'" style="padding: 1px 3px;" title="'.i18n_r('ENABLE').': '.$plugininfo['name'] .'" >'.i18n_r('ENABLE').'</a>';
	else $table.= '<a href="plugins.php?set='.$pluginid.$setNonce.'" class="cancel toggleEnable '.$cls_Disabled.'" title="'.i18n_r('DISABLE').': '.$plugininfo['name'] .'" >'.i18n_r('DISABLE').'</a>';
  	$table .= '</td>';

	$table .= "</tr>\n";
	$counter++;
}

# set file trigger for plugin update notification, not implemented in core for anything
if ($needsupdate) {
	touch(GSCACHEPATH.GSPLUGINTRIGGERFILE);
	exec_action('plugin-update'); // @hook plugin-update a plugin update is available
} else {
	if (file_exists(GSCACHEPATH.GSPLUGINTRIGGERFILE)) {
		delete_file(GSCACHEPATH.GSPLUGINTRIGGERFILE);
	}
}

exec_action('plugin-hook');
$pagetitle = i18n_r('PLUGINS_MANAGEMENT');
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main" >
		<h3 class="floated"><?php i18n('PLUGINS_MANAGEMENT'); ?></h3>
		<div class="edit-nav clearfix" >
			<?php exec_action(get_filename_id().'-edit-nav'); ?>
		</div>		
		<?php exec_action(get_filename_id().'-body'); ?>		
		<?php if ($counter > 0) { ?>
			<table class="edittable highlight">
				<thead>
					<tr><th><?php i18n('PLUGIN_NAME'); ?></th><th><?php i18n('PLUGIN_DESC'); ?></th><th><?php i18n('STATUS'); ?></th></tr>
				</thead>
				<tbody>
					<?php echo $table; ?>
				</tbody>
			</table>
		<?php  } ?>
		
		
		<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php i18n('PLUGINS_INSTALLED'); ?>
		<?php 
		if ($counter == 0) { 
			echo ' - <a href="'.$site_link_back_url.'extend/" target="_blank" >'. str_replace(array('<em>','</em>'), '', i18n_r('GET_PLUGINS_LINK')) .'</a>';
		}
		?>	
		</em></p>
		
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-plugins.php'); ?>
	</div>

</div>

<?php get_template('footer'); ?>
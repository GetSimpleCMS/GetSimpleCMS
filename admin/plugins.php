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

$pluginid = isset($_GET['set']) ? $_GET['set'] : null;
$nonce    = isset($_GET['nonce']) ? $_GET['nonce'] : null;

if ($pluginid){
	if(check_nonce($nonce, "set_".pathinfo_filename($pluginid), "plugins.php")) {
		$plugin = antixss($pluginid);
		change_plugin($plugin);
		redirect('plugins.php?success='.i18n_r('PLUGIN_UPDATED'));
	}
	else redirect('plugins.php?error='.i18n_r('ERROR_OCCURED'));
}

// Variable settings
$counter     = 0;
$table       = '';
$needsupdate = false;

$plugin_info_sorted = subval_sort($plugin_info,'name');

foreach ($plugin_info_sorted as $pluginid=>$plugininfo) {

	$setNonce = '&amp;nonce='.get_nonce("set_".$pluginid,"plugins.php");

	// @todo disabled plugins have a version of (str) 'disabled', should be 0 or null
	$pluginver  = $plugininfo['version'] == 'disabled' ? 0 : $plugininfo['version'];

	if (plugin_active($pluginid)) {
		$cls_Enabled  = 'hidden';
		$cls_Disabled = '';
		$trclass      = 'enabled';
	} else {
		$cls_Enabled  = '';
		$cls_Disabled = 'hidden';
		$trclass      = 'disabled';
	}

	// get extend api for this plugin filename
	$api_data   = json_decode(get_api_details('plugin', $pluginid));
	$updatelink = '';

	// api success
	if (is_object($api_data) && $api_data->status == 'successful') {
		$apiver     = $api_data->version;
		$apipath    = $api_data->path;
		$apiname    = $api_data->name;

		// show update available link
		if ($pluginver >0 && version_compare($apiver,$pluginver,'>')) {
			$updatelink  = '<br /><a class="updatelink" href="'.$apipath.'" target="_blank">'.i18n_r('UPDATE_AVAILABLE').' '.$apiver.'</a>';
			$needsupdate = true;
		}

		$plugin_title = '<a href="'.$apipath.'" target="_blank">'.$apiname.'</a>';
	} else {
		// api fail , does not exist in extend
		$plugin_title = $plugininfo['name'];
	}

	$table .= '<tr id="tr-'.$counter.'" class="'.$trclass.'" >';
	$table .= '<td style="width:150px" ><b>'.$plugin_title.'</b></td>';
	$table .= '<td><span>'.$plugininfo['description'].'</span>'; // desc empty if inactive

	// if plugin is active, show what we know from register_plugin, version , author
	if ($pluginver > 0){
		$table .= '<span><br /><b>'.i18n_r('PLUGIN_VER') .' '. $pluginver.'</b> &mdash; '.i18n_r('AUTHOR').': <a href="'.$plugininfo['author_url'].'" target="_blank">'.$plugininfo['author'].'</a></span>';
	}

  	$table.= $updatelink.'</td><td style="width:60px;" class="status" >
  		<a href="plugins.php?set='.$pluginid.$setNonce.'" class="toggleEnable '.$cls_Enabled.'" style="padding: 1px 3px;" title="'.i18n_r('ENABLE').': '.$plugininfo['name'] .'" >'.i18n_r('ENABLE').'</a>
  		<a href="plugins.php?set='.$pluginid.$setNonce.'" class="cancel toggleEnable '.$cls_Disabled.'" title="'.i18n_r('DISABLE').': '.$plugininfo['name'] .'" >'.i18n_r('DISABLE').'</a>
  	</td>';

	$table .= "</tr>\n";
	$counter++;
}

# set file trigger for plugin update notification, not implemented in core for anything
if ($needsupdate) {
	touch(GSCACHEPATH.'plugin-update.trigger');
	exec_action('plugin-update');
} else {
	if (file_exists(GSCACHEPATH.'plugin-update.trigger')) {
		delete_file(GSCACHEPATH.'plugin-update.trigger');
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
		<h3><?php i18n('PLUGINS_MANAGEMENT'); ?></h3>
		
		<?php if ($counter > 0) { ?>
			<table class="edittable">
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
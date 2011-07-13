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

$pluginid 		=  isset($_GET['set']) ? $_GET['set'] : null;
$nonce    		= isset($_GET['nonce']) ? $_GET['nonce'] : null;

if ($pluginid){
	if(check_nonce($nonce, "set", "plugins.php")) {
	  $plugin=antixss($pluginid);	
	  change_plugin($pluginid);
	  redirect('plugins.php');
	}
}


// Variable settings
login_cookie_check();
$counter = 0; $table = null;

$pluginfiles = getFiles(GSPLUGINPATH);
sort($pluginfiles);
$needsupdate = false;
foreach ($pluginfiles as $fi) {
	$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
	$pathName = pathinfo_filename($fi);
	$setNonce='&nonce='.get_nonce("set","plugins.php");
	
	if ($pathExt=="php") {
		if ($live_plugins[$fi]=='true') {
			$cls_Enabled = 'hidden';
			$cls_Disabled = '';
			$trclass='enabled';
		} else {
			$cls_Enabled = '';
			$cls_Disabled = 'hidden';
			$trclass='disabled';
		}
		$table .= '<tr id="tr-'.$counter.'" class="'.$trclass.'" >';
		$table .= '<td><b>'.$plugin_info[$pathName]['name'] .'</b>';
		$api_data = json_decode(get_api_details('plugin', $fi));
		if ($api_data->status == 'successful') {
			if ($api_data->version != $plugin_info[$pathName]['version']) {
				$table .= '<br /><a class="updatelink" href="'.$api_data->path.'" target="_blank">'.i18n_r('UPDATE_AVAILABLE').' '.$api_data->version.'</a>';
				$needsupdate = true;
			}
		}
		$table .= '</td>';
		$table .= '<td><span>'.$plugin_info[$pathName]['description'] .'<br /><b>';
		$table .= i18n_r('PLUGIN_VER') .' '. $plugin_info[$pathName]['version'].'</b> &mdash; '.i18n_r('AUTHOR').': <a href="'.$plugin_info[$pathName]['author_url'].'" target="_blank">'.$plugin_info[$pathName]['author'].'</a></span></td>';
	  $table.= '<td style="width:60px;" class="status" >
	  	<a href="plugins.php?set='.$fi.$setNonce.'" class="toggleEnable '.$cls_Enabled.'" style="padding: 1px 3px;" title="'.i18n_r('ENABLE').': '.$plugin_info[$pathName]['name'] .'" >'.i18n_r('ENABLE').'</a>
	  	<a href="plugins.php?set='.$fi.$setNonce.'" class="cancel toggleEnable '.$cls_Disabled.'" title="'.i18n_r('DISABLE').': '.$plugin_info[$pathName]['name'] .'" >'.i18n_r('DISABLE').'</a>
	  </td>';	  
		$table .= "</tr>\n";
		$counter++;
	}	
}

# set trigger for plugin update notification
if ($needsupdate) {
	touch(GSCACHEPATH.'plugin-update.trigger');	
} else {
	if (file_exists(GSCACHEPATH.'plugin-update.trigger')) {
		unlink(GSCACHEPATH.'plugin-update.trigger');
	}
}	
?>

<?php exec_action('plugin-hook');?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('PLUGINS_MANAGEMENT')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('PLUGINS_MANAGEMENT'); ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
		<h3><?php i18n('PLUGINS_MANAGEMENT'); ?></h3>
		
		<?php if ($counter > 0) { ?>
			<table class="edittable">
				<tr><th><?php i18n('PLUGIN_NAME'); ?></th><th><?php i18n('PLUGIN_DESC'); ?></th><th><?php i18n('STATUS'); ?></th></tr>
				<?php echo $table; ?>
			</table>
		<?php  } ?>
		
		
		<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php i18n('PLUGINS_INSTALLED'); ?>
		<?php 
		if ($counter == 0) { 
			echo ' - <a href="http://get-simple.info/extend/" target="_blank" >'. str_replace(array('<em>','</em>'), '', i18n_r('GET_PLUGINS_LINK')) .'</a>';
		}
		?>	
		</em></p>
		
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-plugins.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>
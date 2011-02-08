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

// Variable settings
login_cookie_check();
$counter = '0';
$table = '';

$pluginfiles = getFiles(GSPLUGINPATH);
foreach ($pluginfiles as $fi)
{
	$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
	$pathName = pathinfo_filename($fi);
	
	if ($pathExt=="php")
	{
		$table .= '<tr id="tr-'.$counter.'" >';
		$table .= '<td><b>'.$plugin_info[$pathName]['name'] .'</b></td>';
		$table .= '<td><span>'.$plugin_info[$pathName]['description'] .'<br />';
		$table .= i18n_r('PLUGIN_VER') .' '. $plugin_info[$pathName]['version'].' &mdash; '.i18n_r('AUTHOR').': <a href="'.$plugin_info[$pathName]['author_url'].'" target="_blank">'.$plugin_info[$pathName]['author'].'</a></span></td>';
		if ($live_plugins[$fi]=='true'){
	    $table.= '<td style="width:60px;" ><a href="plugins.php?set='.$fi.'" class="cancel" >'.i18n_r('DISABLE').'</a></td>';	  
		} else {
		  $table.= '<td style="width:60px;" ><a href="plugins.php?set='.$fi.'">'.i18n_r('ENABLE').'</a></td>';
		}		
		$table .= "</tr>\n";
		$counter++;
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
		
		<table class="edittable highlight paginate">
			<?php echo $table; ?>
		</table>
		<div id="page_counter" class="qc_pager"></div> 
		<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php i18n('PLUGINS_INSTALLED'); ?></em></p>
			
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-plugins.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>
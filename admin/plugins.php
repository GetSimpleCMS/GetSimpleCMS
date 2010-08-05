<?php
/****************************************************
*
* @File: 	plugins.php
* @Package:	GetSimple
* @Action:	Displays all installed plugins. 	
*
*****************************************************/
 
// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

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
	$pathName= pathinfo($fi,PATHINFO_FILENAME );
	
	if ($pathExt=="php")
	{
		$table .= '<tr id="tr-'.$counter.'" >';
		$table .= '<td width="25%" ><b>'.$plugin_info[$pathName]['name'] .'</b></td>';
		$table .= '<td><span>'.$plugin_info[$pathName]['description'] .'<br />';
		$table .= $i18n['PLUGIN_VER'] .' '. $plugin_info[$pathName]['version'].' &nbsp;|&nbsp; By <a href="'.$plugin_info[$pathName]['author_url'].'" target="_blank">'.$plugin_info[$pathName]['author'].'</a></span></td>';
		$table .= "</tr>\n";
		$counter++;
	}	
}	
?>

<?php exec_action('plugin-hook');?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['PLUGINS_MANAGEMENT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['PLUGINS_MANAGEMENT']; ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			
		<h3><?php echo $i18n['PLUGINS_MANAGEMENT']; ?></h3>
		
		<table class="edittable highlight paginate">
			<?php echo $table; ?>
		</table>
		<div id="page_counter" class="qc_pager"></div> 
		<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php echo $i18n['PLUGINS_INSTALLED']; ?></em></p>
			
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-plugins.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>
<?php
/****************************************************
*
* @File: 		plugins.php
* @Package:	GetSimple
* @Action:	Displays all installed plugins. 	
*
*****************************************************/
 
	require_once('inc/functions.php');
	require_once('inc/plugin_functions.php');
	$userid = login_cookie_check();
	$path = tsl('plugins');
	$counter = '0';
	$table = '<tr class="head"><th width="25%" >'.$i18n['PLUGIN_NAME'].'</th><th>'.$i18n['PLUGIN_DESC'].'</th></tr>' . "\n";
	
	$pluginfiles = getFiles($path);
	foreach ($pluginfiles as $fi) {
		$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
		$pathName= pathinfo($fi,PATHINFO_FILENAME );
		if ($pathExt=="php"){
			$table .= '<tr id="tr-'.$counter.'" >';
			$table .= '<td><b>'.$plugin_info[$pathName]['pi_name'] .'</b></td>';
			$table .= '<td><span>'.$plugin_info[$pathName]['pi_description'] .'<br />';
			$table .= $i18n['PLUGIN_VER'] .' '. $plugin_info[$pathName]['pi_version'].' &nbsp;|&nbsp; By <a href="'.$plugin_info[$pathName]['pi_author_url'].'" target="_blank">'.$plugin_info[$pathName]['pi_author'].'</a></span></td>';
			$table .= "</tr>\n";
			$counter++;
		}	
	}	
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['PLUGINS_MANAGEMENT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['PLUGINS_MANAGEMENT']; ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			
		<?php 
		
		if (isset($_GET['plugin']) && isset($_GET['page'])) { 
			$pluginname = $_GET['plugin'];
			$page = $_GET['page'];
			include "plugins/".$pluginname."/".$page.".php";
		} else { ?>
		<h3><?php echo $i18n['PLUGINS_MANAGEMENT']; ?></h3>
		
		<table class="edittable highlight paginate">
			<?php echo $table; ?>
			
		</table>
		<p><em><b><?php echo $counter; ?></b> <?php echo $i18n['PLUGINS_INSTALLED']; ?></em></p>
		<?php 
		}
		?>	
			
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-plugins.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>
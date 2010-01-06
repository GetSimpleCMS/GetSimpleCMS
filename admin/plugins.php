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
	$table = '';
	
	$pluginfiles = getFiles($path);
	foreach ($pluginfiles as $fi) {
		$pathExt = pathinfo($fi,PATHINFO_EXTENSION );
		$pathName= pathinfo($fi,PATHINFO_FILENAME );
		if ($pathExt=="php"){

		$counter++;
		$table .= '<tr id="tr-'.$counter.'" >';
		$table .= '<td><a>'.$pathName .'</a></td>';
		$table .= '<td style="width:170px;text-align:right;" ><span>Ver1.0</span></td>';
		$table .= '</tr>';	
			
		}
	
		
	}
	
	
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['BAK_MANAGEMENT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> Plugin Management</h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			<label>Plugin Management</label>
			<table class="highlight paginate">
				<?php echo $table; ?>
				
			</table>
			<p><em><b><?php echo $counter; ?></b> Plugins Installed</em></p>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-plugins.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>
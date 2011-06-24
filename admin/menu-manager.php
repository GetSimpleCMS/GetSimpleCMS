<?php 
/**
 * Menu Manager
 *
 * Allows you to edit the current main menu hierarchy  
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// Variable Settings
login_cookie_check();

$dir_handle = opendir(GSDATAPAGESPATH) or die("Unable to open ". GSDATAPAGESPATH);
$filenames = array();
while ($filename = readdir($dir_handle)) {
	$filenames[] = $filename;
}

$count="0"; $data = '';

getPagesXmlValues();

$pagesSorted = subval_sort($pagesArray,'menuOrder');


?> 

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('PAGE_MANAGEMENT').' &raquo; '.i18n_r('MENU_MANAGER')); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('PAGE_MANAGEMENT'); ?> <span>&raquo;</span> <?php i18n('MENU_MANAGER'); ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			<h3><?php i18n('MENU_MANAGER');?></h3>
			
			<?php
				if (count($pagesSorted) != 0) { 
					echo '<table class="highlight">';
					echo '<tr ><th style="width:60px;">'.i18n_r('PRIORITY').'</th><th>'.i18n_r('MENU_TEXT').'</th><th>'.i18n_r('PAGE_TITLE').'</th><th></th><th></th></tr>';
					foreach ($pagesSorted as $page) {
						$sel = '';
						if ($page['menuStatus'] != '') { 
							
							if ($page['menuOrder'] == '') { 
								$page['menuOrder'] = "N/A"; 
							} 
							if ($page['menu'] == '') { 
								$page['menu'] = $page['title']; 
							}
							echo '<tr>
							<td style="width:35px;" >'.$page['menuOrder'].'</td>
							<td><strong>'. $page['menu'] .'</strong></td>
							<td>'. $page['title'] .'</td>
							<td><a href="edit.php?id='.$page['url'].'" target="_blank" >'.strip_tags(i18n_r('EDIT')).'</a></td>
							<td class="secondarylink" ><a href="'.find_url($page['url'], $page['parent']).'" target="_blank" >#</a></td>
							</tr>';
						}
					}
					echo '</table>';
				} else {
					echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';	
				}
				closedir($dir_handle);
			?>

		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>

	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

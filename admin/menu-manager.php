<?php 
/**
 * Menu Manager
 *
 * Allows you to edit the current main menu hierarchy  
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

# Setup
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

# save page priority order
if (isset($_POST['menuOrder'])) {
	$menuOrder = explode(',',$_POST['menuOrder']);
	$priority = 0;
	foreach ($menuOrder as $slug) {
		$file = GSDATAPAGESPATH . $slug . '.xml';
		if (file_exists($file)) {
			$data = getXML($file);
			if ($priority != (int) $data->menuOrder) {
				unset($data->menuOrder);
				$data->addChild('menuOrder')->addCData($priority);
				XMLsave($data,$file);
			}
		}
		$priority++;
	}
	create_pagesxml('true');
	$success = 'Menu order saved!';
}

# get pages
getPagesXmlValues();
$pagesSorted = subval_sort($pagesArray,'menuOrder');

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('PAGE_MANAGEMENT').' &raquo; '.str_replace(array('<em>','</em>'), '', i18n_r('MENU_MANAGER'))); 

?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('PAGE_MANAGEMENT'); ?> <span>&raquo;</span> <?php echo str_replace(array('<em>','</em>'), '', i18n_r('MENU_MANAGER')); ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			<h3><?php echo str_replace(array('<em>','</em>'), '', i18n_r('MENU_MANAGER')); ?></h3>
			
			<?php
				if (count($pagesSorted) != 0) { 
					echo '<form method="post" id="menuItemsOrder" action="">';
					echo '<table class="highlight" id="menu-order">';
					echo '<thead><tr ><th style="width:60px;">'.i18n_r('PRIORITY').'</th><th>'.i18n_r('MENU_TEXT').'</th><th>'.i18n_r('PAGE_TITLE').'</th><th></th><th></th></tr></thead><tbody>';
					foreach ($pagesSorted as $page) {
						$sel = '';
						if ($page['menuStatus'] != '') { 
							
							if ($page['menuOrder'] == '') { 
								$page['menuOrder'] = "N/A"; 
							} 
							if ($page['menu'] == '') { 
								$page['menu'] = $page['title']; 
							}
							echo '<tr id="page_'.$page['slug'].'">
							<td style="width:35px;" >'.$page['menuOrder'].'</td>
							<td><strong>'. $page['menu'] .'</strong></td>
							<td>'. $page['title'] .'</td>
							<td><a href="edit.php?id='.$page['url'].'" target="_blank" >'.strip_tags(i18n_r('EDIT')).'</a></td>
							<td class="secondarylink" ><a href="'.find_url($page['url'], $page['parent']).'" target="_blank" >#</a></td>
							</tr>';
						}
					}
					echo '</tbody></table>';
					echo '<div id="saveOrderBtn"></div>';
					echo '</form>';
 ?>
			<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
			<script>
			$("#menu-order tbody").sortable({
				opacity: 0.6,
				cursor: 'move',
				update: function() {
					order = [];
					$('#menu-order tbody').children('tr').each(function(idx, elm) {
						order.push(elm.id.split('_')[1])
					});
					$('#saveOrderBtn').html('<input type="hidden" name="menuOrder" value="'+order+'"><input class="submit" type="submit" value="Save Menu Order" />');
				}
			});
			</script>

			<?php
				} else {
					echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';	
				}
			?>

		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>

	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

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

exec_action('load-menu-manager');

# save page priority order
if (isset($_POST['menuOrder'])) {
	$menuOrder = explode(',',$_POST['menuOrder']);
	$priority = 0;
	foreach ($menuOrder as $slug) {
		$file = GSDATAPAGESPATH . $slug . '.xml';
		if (file_exists($file)) {
			$data = getPageXML($slug);
			if ($priority != (int) $data->menuOrder) {
				unset($data->menuOrder);
				$data->addChild('menuOrder')->addCData($priority);
				XMLsave($data,$file);
			}
		}
		$priority++;
	}
	create_pagesxml('true');
	$success = i18n_r('MENU_MANAGER_SUCCESS');
}

# get pages
getPagesXmlValues();
$pagesSorted = subval_sort($pagesArray,'menuOrder');

$pagetitle = strip_tags(i18n_r('MENU_MANAGER')).' &middot; '.i18n_r('PAGE_MANAGEMENT');
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php echo str_replace(array('<em>','</em>'), '', i18n_r('MENU_MANAGER')); ?></h3>
			<div class="edit-nav clearfix" >
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>				
			<p><?php i18n('MENU_MANAGER_DESC'); ?></p>
			<?php
				if (count($pagesSorted) != 0) { 
					echo '<form method="post" action="menu-manager.php">';
					echo '<ul id="menu-order" >';
					foreach ($pagesSorted as $page) {
						$sel = '';
						if ($page['menuStatus'] != '') { 
							
							if ($page['menuOrder'] == '') { 
								$page['menuOrder'] = "N/A"; 
							} 
							if ($page['menu'] == '') { 
								$page['menu'] = $page['title']; 
							}
							echo '<li class="clearfix" rel="'.$page['slug'].'">
											<strong>#'.$page['menuOrder'].'</strong>&nbsp;&nbsp;
											'. $page['menu'] .' <em>'. $page['title'] .'</em>
										</li>';
						}
					}
					echo '</ul>';
					echo '<div id="submit_line"><span>';
					echo '<input type="hidden" name="menuOrder" value=""><input class="submit" type="submit" value="'. i18n_r("SAVE_MENU_ORDER").'" />';
					echo '</span></div>';
					echo '</form>';
				} else {
					echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';	
				}
			
			/**
			 * NESTABLE TESTING
			 */
			exec_action('menu-manager-extras');

			$pages = getParentsHashTable();
			// _debugLog($pages);
			$str = getTree($pages);
			echo '<br/><h3>Nestable Test</h3><div id="menu-order-nestable" class="dd">'.$str.'</div>';

			function getTree($parents,$key = '',$str='',$level = 1,$index = 0,$outer = null,$inner = 'treecallout'){
				// _debugLog($key,$level);
				global $index;
				$str .= '<ol id="" class="dd-list">';
				foreach($parents[$key] as $parent=>$child){
					$index++;
					// _debugLog($parent);
					$str .= $inner($child,$level,$index);
					if(isset($parents[$parent])) {
						$str.= getTree($parents,$parent,'',$level+1,$index);
					}
					$str .= $inner($child,$level,$index,false);
				}
				$str .= '</ol>';
				return $str;
			}

			function treeCallout($child,$level,$index = 1,$open = true){
				return $open ? '<li class="dd-item clearfix" data-id="'.$child['url'].'"><div class="dd-handle"><strong>#'.$index.'</strong> '.$child['url'].'<em><div class="">'.$child['title'].'</div></em></div>' : '</li>';
			}

			
			/**
			 * /END NESTABLE TESTING
			 */
			
			?>

			<script>
				$("#menu-order").sortable({
					cursor: 'move',
					placeholder: "placeholder-menu",
					update: function() {
						var order = '';
						$('#menu-order li').each(function(index) {
							var cat = $(this).attr('rel');
							order = order+','+cat;
						});
						$('[name=menuOrder]').val(order);
					}
				});
				$("#menu-order").disableSelection();


				$('.dd').nestable({ /* config options */ });

				$('.dd').on('change', function() {
					/* on change event */
					Debugger.log(JSON.stringify($(this).nestable('serialize')));
				});

			</script>
			
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>

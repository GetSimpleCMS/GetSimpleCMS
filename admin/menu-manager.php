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
	if(trim($_POST['menuOrder']) == '') die('no data');
	if(!isset($_POST['menuid'])) $menuid = 'default';
	else $menuid = _id($_POST['menuid']);
	$status = save_file(GSDATAOTHERPATH.'menu_'.$menuid.'.json',json_encode(menuOrderSave(),true));
	$success = $status ? 'Success' : 'Error';
}

function initMenus(){
	global $menucache;
	$menucache = array();
}

function menuOrderSave(){
	global $pagesArray;
	$menuOrder = json_decode($_POST['menuOrder'],true);
	// debugLog($menuOrder);
	recurseJson($menuOrder);
	// debugLog($menuOrder);
	create_pagesxml('true');
	return $menuOrder;
}

function recurseJson(&$array,$parent = null,$depth = 0,$index = 0){
	// debugLog(__FUNCTION__ . ' ' . count($array));
	static $index;
	if($depth == 0) $index = 0;
	$order = 0;
	$depth++;
	
	foreach($array as $key=>&$value){
		if(isset($value['id'])){
			$order++;
			$index++;

			if(isset($parent)) $value['parent'] = $parent;
			
			storeMenuInPage($value['id'],(isset($parent) ? $parent : ''),$index);

			$value['data']          = array();
			$value['data']['url']   = generate_url($value['id']);
			$value['data']['path']  = generate_permalink($value['id'],'%path%/%slug%');
			$value['data']['depth'] = $depth;
			$value['data']['index'] = $index;
			$value['data']['order'] = $order;

			if(isset($value['children'])){
				$value['numchildren'] = count($value['children']);
				$children = &$value['children'];
				recurseJson($children,$value['id'],$depth,$index);
			}
		}
	}
}

function storeMenuInPage($pageid,$parent,$order){
	
	// debugLog(func_get_args());
	// debugLog(returnPageField($pageid,'url'));
	// debugLog(returnPageField($pageid,'parent'));
	// debugLog(returnPageField($pageid,'menuOrder'));
	if((string)returnPageField($pageid,'parent') == $parent && (int)returnPageField($pageid,'menuOrder') == $order) return;
	$file = GSDATAPAGESPATH . $pageid . '.xml';
	if (file_exists($file)) {
		$data = getPageXML($pageid);
		$data->parent->updateCData($parent);
		$data->menuOrder->updateCData($order);
		XMLsave($data,$file);
	}
}

function legacyMenuOrderSave(){
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
	return $success;
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
				$legacymenu = false;
				if (count($pagesSorted) != 0 && $legacymenu == true) { 
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
					// echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';	
				}
			
			/**
			 * NESTABLE TESTING
			 */
			
			if(!file_exists(GSDATAOTHERPATH.'menu_default.json')){
				// sort by menu order, filter menustatus, removes unaccessible menu items from tree
				$pagesSorted = filterKeyValueMatch(sortCustomIndex($pagesArray,'menuOrder'),'menuStatus','Y');
				// debugLog(getPagesFields('menuOrder',$pagesSorted));
				debugLog($pagesSorted);
				// create hash table from sorted filtered pages
				$parents = getParentsHashTable($pagesSorted);
			
				// debugLog($parents);
				$str = getTree($parents);
				echo '<div id="menu-order-nestable" class="dd">'.$str.'</div>';
			} 
			else {
				$menudata = read_file(GSDATAOTHERPATH.'menu_default.json');
				$menudata = json_decode($menudata,true);
				$str = getMenuTree($menudata);
				echo '<div id="menu-order-nestable" class="dd">'.$str.'</div>';
			}
			// $str = getTree(getParentsHashTable(sortCustomIndex($pagesArray,'menuOrder')),'','',1,0,'treeFilterCallout');
			// echo '<br/><h3>Nestable Unfiltered Test<span>shows non menus in lineage, buggy</span></h3><div id="menu-order-nestable" class="dd">'.$str.'</div>';

			echo '<form method="post" action="menu-manager.php">';
			echo '<div id="submit_line"><span>';
			echo '<input type="hidden" name="menuOrder" value=""><input class="submit" type="submit" value="'. i18n_r("SAVE_MENU_ORDER").'" />';
			echo '</span></div>';
			echo '</form>';

			exec_action('menu-manager-extras');


			function getPageMenuTitle($slug){
				$page = getPage($slug);
				return ($page['menu'] == '' ? $page['title'] : $page['menu']);
			}

			function getTree($parents,$key = '',$str='',$level = 0,$index = 0, $filter = null, $outer = 'treeCalloutOuter',$inner = 'treeCalloutInner'){
				// _debugLog($key,$level);
				static $index;
				if($level == 0) $index = 0;
				$level++;
				$order = 0;
				$str .= $outer($level,$index,$order);
				foreach($parents[$key] as $parent=>$child){
					$order++;
					if(isset($filter) && function_exists($filter) && $filter($child,$level,$index)) continue;
					$index++;
					// _debugLog($parent);
					$str .= $inner($child['url'],$level,$index,$order);
					if(isset($parents[$parent])) {
						$str.= getTree($parents,$parent,'',$level+1);
					}
					$str .= $inner($child['url'],$level,$index,$order,false);
				}
				$str .= $outer($child['url'],$level,$index,$order,false);
				return $str;
			}

			function getMenuTree($parents,$str='',$level = 0, $index = 0, $filter = null, $outer = 'treeCalloutOuter',$inner = 'treeCalloutInner'){
				if(!$parents) return;
				static  $index;
				if($level == 0) $index = 0;
				$order = 0;
				$str .= $outer($level,$index,$order);
				foreach($parents as $key=>$parent){
					if(!isset($parent['id'])) continue;
					if(isset($filter) && function_exists($filter) && $filter($id)) continue;
					$level = isset($parent['depth']) ? $parent['depth'] : $level+1;
					$index = isset($parent['index']) ? $parent['index'] : $index+1;
					$order = isset($parent['order']) ? $parent['order'] : $order+1;
					
					$str .= $inner($parent['id'],$level,$index,$order);
					if(isset($parent['children'])) {
						$str.= getMenuTree($parent['children'],'',$level);
					}
					$level--;
					$str .= $inner($parent['id'],$level,$index,$order,false);
				}
				$str .= $outer($parent['id'],$level,$index,$order,false);
				return $str;
			}

			function treeCalloutInner($id,$level,$index = 1,$order = 0,$open = true){
				$child = getPage($id);
				$str = $open ? '<li class="dd-item clearfix" data-id="'.$child['url'].'"><div class="dd-handle"><strong>'.$index.'.'.$level.'.'.$order.'</strong> '.getPageMenuTitle($child['url']).' <em>[' .$child['url'].']</em><div class="itemtitle"><em>'.$child['title'].'</div></em></div>' : '</li>';
				return $str;
			}

			function treeCalloutOuter($id,$level,$index = 1,$order = 0,$open = true){
				return $open ? '<ol id="" class="dd-list">' : '</ol>';
			}
			
			function treeFilterCallout($id,$level,$index,$order){
				$child = getPage($id);
				return $child['menuStatus'] !== 'Y';
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
					var order = JSON.stringify($(this).nestable('serialize'));
					$('[name=menuOrder]').val(order);
				});

			</script>
			
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>

</div>
<?php get_template('footer');

/* ?> */
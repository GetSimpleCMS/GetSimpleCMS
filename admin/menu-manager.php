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
	menuOrderSave();
}

function menuOrderSave(){
	$menuOrder = json_decode($_POST['menuOrder'],true);
	debugLog($menuOrder);
	recurseJson($menuOrder);
	// cleanJson($menuOrder);
	debugLog($menuOrder);

}

function recurseJson(&$array,$parent = null,$index = 0,$depth = 0){
	// debugLog(__FUNCTION__ . ' ' . count($array));
	$depth++;
	foreach($array as $key=>&$value){
		if(isset($value['id'])){
			$index++;
			// $array[$value['id']] = $value;
			if(isset($parent)) $value['parent'] = $parent;
			$value['url'] = generate_url($value['id']);
			$value['path'] = generate_permalink($value['id'],'%path%/%slug%');
			$value['depth'] = $depth;
			$value['index'] = $index;
			if(isset($value['children'])){
				$value['numchildren'] = count($value['children']);
				$children = &$value['children'];
				recurseJson($children,$value['id'],$index,$depth);
			}
		}
	}
}

function cleanJson(&$array){
	foreach($array as $key=>&$value){
		// debugLog($key." ".$value['id'] . ' ' . is_numeric($key));
		$children = &$value['children'];
		if(isset($value['children'])) cleanJson($children);
		if(is_numeric($key)){
			debugLog("unsetting " . $key .'=>' . $value['id']);
			$array[$key] = null;
			unset($array[$key]);
		}	
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

			// sort by menu order, filter menustatus, removes unaccessible menu items from tree
			$pagesSorted = filterKeyValueMatch(sortCustomIndex($pagesArray,'menuOrder'),'menuStatus','Y');
			// debugLog(getPagesFields('menuOrder',$pagesSorted));
			debugLog($pagesSorted);
			// create hash table from sorted filtered pages
			$parents = getParentsHashTable($pagesSorted);
			
			// debugLog($parents);
			$str = getTree($parents);
			echo '<br/><h3>Nestable Filtered Test<span>hides menus with no direct lineage</span></h3><div id="menu-order-nestable" class="dd">'.$str.'</div>';

			$str = getTree(getParentsHashTable(sortCustomIndex($pagesArray,'menuOrder')),'','',1,0,'treeFilterCallout');
			echo '<br/><h3>Nestable Unfiltered Test<span>shows non menus in lineage, buggy</span></h3><div id="menu-order-nestable" class="dd">'.$str.'</div>';

			function getPageMenuTitle($page){
				return ($page['menu'] == '' ? $page['title'] : $page['menu']) . ' [' . $page['menuStatus'] . '] ';
			}

			function getTree($parents,$key = '',$str='',$level = 1,$index = 0, $filter = null, $outer = 'treeCalloutOuter',$inner = 'treeCalloutInner'){
				// _debugLog($key,$level);
				global $index;
				$str .= $outer($level,$index);
				foreach($parents[$key] as $parent=>$child){
					if(isset($filter) && function_exists($filter) && $filter($child,$level,$index)) continue;
					$index++;
					// _debugLog($parent);
					$str .= $inner($child,$level,$index);
					if(isset($parents[$parent])) {
						$str.= getTree($parents,$parent,'',$level+1,$index);
					}
					$str .= $inner($child,$level,$index,false);
				}
				$str .= $outer($level,$index,false);;
				return $str;
			}


			function get_menu_tree($parent = '',$menu = '',$level = '') {
				global $pagesSorted;
				
				$pages = getPageDepths($pagesSorted); // use parent hash table for speed
				$depth = null;

				// get depth of requested parent, then get all subsequent children until we get back to our starting depth
				foreach($pages as $key => $page){

					// check for cyclical parent child and die
					if(isset($page['parent']) && $page['parent'] === $key) die("self parent > " . $key); 

					$level       = isset($page['depth']) ? $page['depth'] : 0;
					$numChildren = isset($page['numchildren']) ? $page['numchildren'] : 0;

					// if sublevel
					if($parent !== ''){
						// skip until we get to parent
						if($parent !== $key && $depth === null) continue;

						if($depth === null){
						 // set sub level starting depth
						 $depth = $page['depth']; continue;
						}	
						else if(($page['depth'] == $depth)) return $menu; // we are back to starting depth so stop
						$level = $level - ($depth+1);
					}	

					// provide special row if this is a missing parent
					if( !isset($page['url']) ) $menu .= getPagesRowMissing($key,$level,$numChildren); // use URL check for missing parents for now
					else $menu .= treeCalloutInner($page,$level,'','',$numChildren == 0);
			  	}

				return $menu;
			}

			function treeCalloutInner($child,$level,$index = 1,$open = true){
				return $open ? '<li class="dd-item clearfix" data-id="'.$child['url'].'"><div class="dd-handle"><strong>#'.$index.'</strong> '.getPageMenuTitle($child).' <em> - ' .$child['url'].'</em><div class="itemtitle"><em>'.$child['title'].'</div></em></div>' : '</li>';
			}


			function treeCalloutOuter($level,$index = 1,$open = true){
				return $open ? '<ol id="" class="dd-list">' : '</ol>';
			}
			
			function treeFilterCallout($child,$level,$index){
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


function heirarchyToAdjacency(){

}

function flatToParentHashtable(){
	// array('parentid'=>$parentId);
}

// save menu json array
// 
/* 

[{"id":"index-2"},{"id":"index"},{"id":"oldslug"},{"id":"parent-1","children":[{"id":"child-1b","children":[{"id":"child-1b-1"}]},{"id":"child-1a"}]},{"id":"ckeditor-test"},{"id":"menuorder-test"}]

[
    {
        "id": "index-2"
    },
    {
        "id": "index"
    },
    {
        "id": "oldslug"
    },
    {
        "children": [
            {
                "children": [
                    {
                        "id": "child-1b-1"
                    }
                ],
                "id": "child-1b"
            },
            {
                "id": "child-1a"
            }
        ],
        "id": "parent-1"
    },
    {
        "id": "ckeditor-test"
    },
    {
        "id": "menuorder-test"
    }
]
*/

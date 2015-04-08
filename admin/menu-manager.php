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
include 'inc/common.php';

login_cookie_check();

exec_action('load-menu-manager');

# save page priority order
if (isset($_POST['menuOrder'])) {
	if (trim($_POST['menuOrder']) == '') {
		die('no data');
	}

	if (!isset($_POST['menuid'])) {
		$menuid = 'default';
	} else {
		$menuid = _id($_POST['menuid']);
	}

	$status = save_file(GSDATAOTHERPATH . 'menu_' . $menuid . '.json', json_encode(menuOrderSave(), true));
	$success = $status ? 'Success' : 'Error';
}

# get pages
getPagesXmlValues();
$pagesSorted = subval_sort($pagesArray, 'menuOrder');

$pagetitle = strip_tags(i18n_r('MENU_MANAGER')) . ' &middot; ' . i18n_r('PAGE_MANAGEMENT');
get_template('header');

?>

<?php include 'template/include-nav.php';?>

<div class="bodycontent clearfix">

	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php echo str_replace(array('<em>', '</em>'), '', i18n_r('MENU_MANAGER'));?></h3>
			<div class="edit-nav clearfix" >
				<?php exec_action(get_filename_id() . '-edit-nav');?>
			</div>
			<?php exec_action(get_filename_id() . '-body');?>
			<p><?php i18n('MENU_MANAGER_DESC');?></p>
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
			echo '<li class="clearfix" rel="' . $page['slug'] . '">
											<strong>#' . $page['menuOrder'] . '</strong>&nbsp;&nbsp;
											' . $page['menu'] . ' <em>' . $page['title'] . '</em>
										</li>';
		}
	}
	echo '</ul>';
	echo '<div id="submit_line"><span>';
	echo '<input type="hidden" name="menuOrder" value=""><input class="submit" type="submit" value="' . i18n_r("SAVE_MENU_ORDER") . '" />';
	echo '</span></div>';
	echo '</form>';
} else {
	// echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';
}

/**
 * NESTABLE TESTING
 */

// if(!file_exists(GSDATAOTHERPATH.'menu_default.json')){
// 	// sort by menu order, filter menustatus, removes unaccessible menu items from tree
// 	$pagesSorted = filterKeyValueMatch(sortCustomIndex($pagesArray,'menuOrder'),'menuStatus','Y');
// 	// debugLog(getPagesFields('menuOrder',$pagesSorted));
// 	// debugLog($pagesSorted);
// 	// create hash table from sorted filtered pages
// 	$parents = getParentsHashTable($pagesSorted);

// 	// debugLog($parents);
// 	$str = getTree($parents);
// 	echo '<div id="menu-order-nestable" class="dd">'.$str.'</div>';
// }
// else {
// 	$menudata = read_file(GSDATAOTHERPATH.'menu_default.json');
// 	$menudata = json_decode($menudata,true);
// 	$str = getMenuTree($menudata);
// 	echo '<div class="leftsec">';
// 	echo '<div id="menu-order-nestable" class="dd">'.$str.'</div>';
// 	echo '</div>';
// }
// $str = getTree(getParentsHashTable(sortCustomIndex($pagesArray,'menuOrder')),'','',1,0,'treeFilterCallout');
// echo '<br/><h3>Nestable Unfiltered Test<span>shows non menus in lineage, buggy</span></h3><div id="menu-order-nestable" class="dd">'.$str.'</div>';

// OUTPUT PAGE SELECT
// $selectpages = filterKeyValueMatch(sortCustomIndex($pagesArray,'menuOrder'),'menuStatus','Y');
$parents = getParentsHashTable();
// debugLog($parents);

// echo "<select>";
// echo getTree($parents,'','',0, 0, null, 'selectCalloutOuter','selectCalloutInner');
// echo "</select>";

exec_action('menu-manager-extras');

$tree = readMenu('default');
// debugLog($tree);
$regen = false;
if (!$tree || $regen) {
	debugLog('tree not found');
	$tree = getMenuFromPages();
	_debugLog($tree);
	// _debugLog($tree['INDEX']);
	saveMenu('default', $tree);
	buildRefArray($tree);
	_debugLog($tree['FLAT']);
} else {
	buildRefArray($tree);
	// _debugLog($tree['FLAT']);
}

// _debuglog($tree['FLAT']);
// _debuglog($tree['FLAT']['index']);
// _debuglog($tree['FLAT']['index-2']['numchildren']);

// $tree['REFTEST'] = &$tree['NESTED']['parent-1']['children']['child-1c']['children'];
// $tree['REFTEST'] = &resolve_tree($tree['MENU'],explode('.','parent-1.children.child-1c.children'));

// var_export($tree['NESTED']['index']);

// _debugLog($tree['REFTEST']);
// _debugLog(serialize($tree));
// _debugLog(json_encode($tree));

// _debugLog($indexAry);

// _debugLog($tree);

// _debugLog(serialize($indexAry));

// $test = array('MENUTREE'=>&$tree,'MENUFLAT'=>&$indexAry);

// _debugLog(json_encode($test));

// SERIALIZE TEST, preserves refs
// $menufile = 'serialized.php';
// $status = save_file(GSDATAOTHERPATH.'menu_'.$menufile,serialize($tree));
// $tree = read_file(GSDATAOTHERPATH.'menu_'.$menufile);
// $tree = unserialize($tree);

// JSON Test, does not preserve refs, but we can store ref arrays and rebuild with flat map.
// $menufile = 'serialized.json';
// $tree = read_file(GSDATAOTHERPATH.'menu_'.$menufile);
// $tree = json_decode($tree,true);
// $status = save_file(GSDATAOTHERPATH.'menu_'.$menufile,json_encode($tree,true));

$str = getMenuTree($tree['NESTED']);

echo '<div class="rightsec">';
echo '<div id="menu-order-nestable" class="dd">' . $str . '</div>';
echo '</div>';

echo '<div class="clearfix"></div>';
// echo '<div class="clear">';

echo '<form method="post" action="menu-manager.php">';
echo '<div id="submit_line"><span>';
echo '<input type="hidden" name="menuOrder" value=""><input class="submit" type="submit" value="' . i18n_r("SAVE_MENU_ORDER") . '" />';
echo '</span></div>';
echo '</form>';

echo '<li id="nestable-template" class="dd-item clearfix" data-id="template"><div class="dd-handle"> template<div class="itemtitle"><em>template title</em></div></div></li>';
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
					Debugger.log(order);
					$('[name=menuOrder]').val(order);
				});

				$('#nestable-template').hide();
				$(".dd >ol").append($('#nestable-template').clone());
				$('.dd').nestable('collapseAll');

				// var size = parentLi.children('ol').first().children('li').length; // get parents ol li items
				// if(size == 1) parentLi.find('button[data-action=collapse]').show(); // unhide the collapse button

			</script>

		<div class="dd" id="nestable-json"></div>

		</div>
	</div>

	<div id="sidebar" >
		<?php include 'template/sidebar-pages.php';?>
	</div>

</div>
<?php get_template('footer');

/* ?> */
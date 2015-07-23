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

// check menuid else default
if (!isset($_REQUEST['menuid'])) {
	$menuid = 'default';
} else {
	$menuid = _id($_REQUEST['menuid']);
}

# save menu
if (isset($_POST['menuOrder'])) {
	if (trim($_POST['menuOrder']) == '' || trim($_POST['menuOrder']) == '[]') {
		die('no data');
	}

	$status  = newMenuSave($menuid,$_POST['menuOrder']);
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

legacyMenuManager($pagesSorted);

function legacyMenuManager($pages){

	if (count($pages) != 0) {
		echo '<form method="post" action="menu-manager.php">';
		echo '<ul id="menu-order" >';
		foreach ($pages as $page) {
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
		echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';
	}
}

$tree = getMenuData($menuid);
// debugLog($tree);
$str  = getMenuTree($tree,'mmCalloutInner', 'mmCalloutOuter', 'mmCalloutFilter');
// $str  = callIfCallable('mmCalloutOuter') . getMenuTreeMin($tree,'mmCalloutInner','mmCalloutOuter','mmCalloutFilter') . callIfCallable('mmCalloutOuter',null,false);

echo '<div class="widesec">';
echo '<div id="menu-order-nestable" class="dd">' . $str . '</div>';
echo '</div>';

echo '<div class="clearfix"></div>';
// echo '<div class="clear">';

exec_action('menu-manager-extras');

echo '<form method="post" action="menu-manager.php">';
echo '<div id="submit_line"><span>';
echo '<input type="text" class="hidden" name="menuid" value="'.$menuid.'">';
echo '<textarea type="text" class="text hidden" name="menuOrder" value=""></textarea>';
echo '<input class="submit" type="submit" value="' . i18n_r("SAVE_MENU_ORDER") . '" />';
echo '</span></div>';
echo '</form>';

// echo '<li id="nestable-template" class="dd-item clearfix" data-id="template"><div class="dd-handle"> template<div class="itemtitle"><em>template title</em></div></div></li>';

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

				$('.dd').nestable({
					expandBtnHTML   : '<button class="borderless" data-action="expand"><i class="tree-expander fa fa-play fa-fw"></i></button>',
					collapseBtnHTML : '<button class="borderless" data-action="collapse"><i class="tree-expander fa fa-play fa-fw fa-rotate-90"></i></button>'
				});

				$('.dd').on('change', function() {
					/* on change event */
					var order = JSON.stringify($(this).nestable('serialize'));
					Debugger.log(order);
					$('[name=menuOrder]').val(order);
				});

				$('.dd').trigger('change');
				// $('#nestable-template').hide();
				// $(".dd >ol").append($('#nestable-template').clone());
				// $('.dd').nestable('collapseAll');

				// var size = parentLi.children('ol').first().children('li').length; // get parents ol li items
				// if(size == 1) parentLi.find('button[data-action=collapse]').show(); // unhide the collapse button

			</script>

			<div class="dd" id="nestable-json"></div>

			<?php 
				// echo getMenuTreeMin($tree,'treeCalloutInner', 'treeCalloutOuter', 'mmCalloutFilter');
				echo getMenuTree($tree,'menuCalloutInner', 'treeCalloutOuter', 'menuCalloutFilter');
			?>

		</div>
	</div>

	<div id="sidebar" >
		<?php include 'template/sidebar-pages.php';?>
	</div>

</div>
<?php get_template('footer');

/* ?> */
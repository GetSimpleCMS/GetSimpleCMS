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
if (isset($_GET['menuid'])) $menuid = _id($_GET['menuid']);
else $menuid = 'default';

# save menu
if (isset($_POST['menuOrder'])) {
	if(!isset($_POST['menuid'])) die('missing menuid');
	if (trim($_POST['menuOrder']) == '' || trim($_POST['menuOrder']) == '[]') {
		die('no data');
	}

	$status  = newMenuSave(_id($_POST['menuid']),$_POST['menuOrder']);
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

// legacyMenuManagerOutput($pagesSorted);

GLOBAL $sortkeys;
// debugLog(getPages());
$presort = sortCustomIndexCallback(getPages(),'pubDate','prepare_date');
$sortkeys = array_keys($presort);

// debugLog($presort);
// debugLog($sortkeys);

$tree = getMenuDataNested($menuid);
// debugLog($tree);
$str  = getMenuTree($tree,true,'mmCallout', 'mmCalloutFilter');
// $str  = callIfCallable('mmCalloutOuter') . getMenuTreeMin($tree,'mmCalloutInner','mmCalloutOuter','mmCalloutFilter') . callIfCallable('mmCalloutOuter',null,false);

echo '<div class="widesec">';

// toggler
echo '<div id="roottoggle" class="unselectable tree-roottoggle nohighlight"><i class="tree-expander fa-rotate-90 fa fa-play fa-fw"></i><span class="label">Collapse Top Parents</span></div>';

// nestable container
echo '<div id="menu-order-nestable" class="dd">' . $str . '</div>';
echo '</div>';

echo '<div class="clearfix"></div>';
// echo '<div class="clear">';

exec_action('menu-manager-extras');

// form
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

				$('#roottoggle').on('click',function(){
					toggleMMTopAncestors();
				});

				function toggleMMTopAncestors(){
					var treeprefix = 'tree-';
					var nodecollapedclass = treeprefix + 'collapsed'
					var rootcollapsed = $("#roottoggle").hasClass("rootcollapsed");

					var treeexpanderclass  = treeprefix + 'expander';           // class for expander handles
					var treeexpandedclass = 'fa-rotate-90';
					var treecollapsedclass = '';
					
					// toggle label text
					var langstr = !rootcollapsed ? i18n('EXPAND_TOP') : i18n('COLLAPSE_TOP');
					$('#roottoggle .label').html(langstr);
					$("#roottoggle").toggleClass("rootcollapsed");
					$('#roottoggle').toggleClass(nodecollapsedclass,!rootcollapsed);
					
					var iconexpanded = '<i class="'+treeexpanderclass+' '+treeexpandedclass+' fa fa-play fa-fw"></i>';
					var iconcollapsed = '<i class="'+treeexpanderclass+' '+treecollapsedclass+' fa fa-play fa-fw"></i>';

					if(rootcollapsed){
						// $('.dd').nestable('expandAll');
						$('.dd').nestable('expandAllRoot');
						$('#roottoggle .tree-expander').addClass(treeexpandedclass);
					}
					else {
						// $('.dd').nestable('collapseAll');
						$('.dd').nestable('collapseAllRoot');
						$('#roottoggle .tree-expander').removeClass(treeexpandedclass);
					}
				}

			</script>

			<!-- <div class="dd" id="nestable-json"></div> -->

			<?php 
				// echo getMenuTree($tree,true,'treeCallout', 'menuCalloutFilter');
				// echo getMenuTree($tree,true,'menuCallout', 'menuCalloutFilter',array('currentpage'=>'index','classPrefix'=>'GS_'));
				// get_navigation_advanced('index');
				// get_navigation_advanced('index','','parent-1b',0);
				// echo "<ul>";
				// get_navigation('index');
				// echo "</ul>";

			?>

		</div>
	</div>

	<div id="sidebar" >
		<?php include 'template/sidebar-pages.php';?>
	</div>

</div>
<?php get_template('footer');

/* ?> */
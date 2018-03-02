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
else $menuid = GSMENUIDCORE; // default to menu core index

# save menu
if (isset($_POST['menuOrder'])) {
	if(!isset($_POST['menuid'])) die('post is missing menuid');
	if (trim($_POST['menuOrder']) == '' || trim($_POST['menuOrder']) == '[]') {
		die('post has no data');
	}

	$status  = newMenuSave(_id($_POST['menuid']),$_POST['menuOrder']);
	$success = $status ? 'Success' : 'Error';
}

$pagetitle = strip_tags(i18n_r('MENU_MANAGER')) . ' &middot; ' . i18n_r('PAGE_MANAGEMENT');
get_template('header');

include 'template/include-nav.php';

/** not implemented  **/
function getMenuSelect(){
	$menus = getMenus(true);
	if(!$menus) return;
	
	GLOBAL $menuid;
	echo "<select>";
	foreach($menus as $menu){
		$menu = str_replace("menu_","",$menu);
		$selected = $menuid == $menu ? "selected" : "";
		echo "<option $selected>$menu</option>";
	}
	echo "</select>";
}

function getMenuManagerTree($menuid){
	$tree = getMenuDataNested($menuid);
	return getMenuTree($tree,true,GSMENUMGRCALLOUT, GSMENUMGRFILTERCALLOUT);
}

$treestr  = getMenuManagerTree($menuid);
$count = '-'; // empty, updated via js

?>

<div class="bodycontent clearfix">

	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php echo str_replace(array('<em>', '</em>'), '', i18n_r('MENU_MANAGER'));?></h3>
			<div class="edit-nav clearfix" >
				<?php getMenuSelect(); ?>
				<?php exec_action(get_filename_id() . '-edit-nav');?>
			</div>
			<?php exec_action(get_filename_id() . '-body');?>
			<p><?php i18n('MENU_MANAGER_DESC');?></p>
			<div class="widesec">
				<!-- toggler -->
				<div id="roottoggle" class="unselectable tree-roottoggle nohighlight"><i class="tree-expander fa-rotate-90 fa fa-play fa-fw"></i><span class="label">Collapse Top Parents</span></div>
				<!-- nestable container -->
				<div id="menu-order-nestable" class="dd"><?php echo $treestr; ?></div>
			</div>
			<div class="clearfix"></div>
			<?php
			exec_action('menu-manager-extras'); // @hook menu-manager-extras add additional menu manager features here
			?>
			<p><em><b><span id="pg_counter"><?php echo $count; ?></span></b> <?php echo i18n_r('TOTAL_ITEMS');?></em></p>

			<!-- form -->
			<form method="post" action="menu-manager.php">
			<div id="submit_line"><span>
			<input type="text" class="hidden" name="menuid" value="<?php echo $menuid;?>">
			<textarea type="text" class="text hidden" name="menuOrder" value=""></textarea>
			<input class="submit" type="submit" value="<?php echo i18n_r("SAVE_MENU_ORDER"); ?>" />
			</span></div>
			</form>

			<?php // echo '<li id="nestable-template" class="dd-item clearfix" data-id="template"><div class="dd-handle"> template<div class="itemtitle"><em>template title</em></div></div></li>'; ?>

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

				// init nestable
				$('#menu-order-nestable').nestable({
					expandBtnHTML   : '<button class="borderless" data-action="expand"><i class="tree-expander fa fa-play fa-fw"></i></button>',
					collapseBtnHTML : '<button class="borderless" data-action="collapse"><i class="tree-expander fa fa-play fa-fw fa-rotate-90"></i></button>'
				});

				// init nestable on change handler
				$('#menu-order-nestable').on('change', function() {
					/* on change event */
					var order = JSON.stringify($(this).nestable('serialize'));
					Debugger.log(order);
					$('[name=menuOrder]').val(order);

					updateCount();
				});

				// init inputs
				$('#menu-order-nestable').trigger('change');

				// test template new item
				// $('#nestable-template').hide();
				// $(".dd >ol").append($('#nestable-template').clone());

				$('#roottoggle').on('click',function(){
					toggleMMTopAncestors();
				});

				function toggleMMTopAncestors(){
					var treeprefix         = 'tree-';
					var nodecollapedclass  = treeprefix + 'collapsed'
					var rootcollapsed      = $("#roottoggle").hasClass("rootcollapsed");
					
					var treeexpanderclass  = treeprefix + 'expander'; // class for expander
					var treeexpandedclass  = ' fa fa-play fa-fw fa-rotate-90';
					var treecollapsedclass = ' fa fa-play fa-fw';
					
					// toggle label text
					var langstr = !rootcollapsed ? i18n('EXPAND_TOP') : i18n('COLLAPSE_TOP');
					$('#roottoggle .label').html(langstr);
					$("#roottoggle").toggleClass("rootcollapsed");
					$('#roottoggle').toggleClass(nodecollapsedclass,!rootcollapsed);
					
					if(rootcollapsed){
						// expand top levels
						$('#menu-order-nestable').nestable('expandAllRoot');
						$('#roottoggle .tree-expander').removeClass(treecollapsedclass).addClass(treeexpandedclass);
					}
					else {
						// collapse top levels
						$('#menu-order-nestable').nestable('collapseAllRoot');
						$('#roottoggle .tree-expander').removeClass(treeexpandedclass).addClass(treecollapsedclass);
					}
				}

				function updateCount(){
					// update item count
					var count = $('#menu-order-nestable ol > li').length;
					$('#pg_counter').text(count);
				}

			</script>

			<?php 
				// TESTING
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
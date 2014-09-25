<?php
/**
 * All Pages
 *
 * Displays all pages 
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

exec_action('load-pages');

// Variable settings
$id      =  isset($_GET['id']) ? $_GET['id'] : null;
$ptype   = isset($_GET['type']) ? $_GET['type'] : null; 
$path    = GSDATAPAGESPATH;
$counter = '0';
$table   = '';

// cloning a page
if ( isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'clone') {

	check_for_csrf("clone", "pages.php");

	$status = clone_page($_GET['id']);
	if ($status !== false) {
		create_pagesxml('true');
		redirect('pages.php?upd=clone-success&id='.$status);
	} else {
		$error = sprintf(i18n_r('CLONE_ERROR'), $_GET['id']);
		redirect('pages.php?error='.$error);
	}
}

getPagesXmlValues(true);

$count = 0;
$pagesArray_tmp = array();

foreach ($pagesArray as $key =>$page) {
	if ($page['parent'] != '') { 
		$parentTitle = returnPageField($page['parent'], "title");
		$sort = $parentTitle .' '. $page['title'];		
		$sort = $parentTitle .' '. $page['title'];
	} else {
		$sort = $page['title'];
	}
	$page = array_merge($page, array('sort' => $sort));
	$pagesArray_tmp[$key] = $page;
	$count++;
}
// $pagesArray = $pagesArray_tmp;
$pagesSorted = subval_sort($pagesArray_tmp,'sort');
$table = get_pages_menu('','',0);

$pagetitle = i18n_r('PAGE_MANAGEMENT');
get_template('header');

?>

<?php include('template/include-nav.php'); ?>
	
<div class="bodycontent clearfix">
	
	<div id="maincontent">
	<?php exec_action('pages-main'); ?>
		<div class="main">
			<h3 class="floated"><?php i18n('PAGE_MANAGEMENT'); ?></h3>
			<div class="edit-nav clearfix" >
				<a href="javascript:void(0)" id="filtertable" accesskey="<?php echo find_accesskey(i18n_r('FILTER'));?>" ><?php i18n('FILTER'); ?></a>
				<a href="javascript:void(0)" id="show-characters" accesskey="<?php echo find_accesskey(i18n_r('TOGGLE_STATUS'));?>" ><?php i18n('TOGGLE_STATUS'); ?></a>
			</div>
			<div id="filter-search">
				<form><input type="text" autocomplete="off" class="text" id="q" placeholder="<?php echo strip_tags(lowercase(i18n_r('FILTER'))); ?>..." /> &nbsp; <a href="pages.php" class="cancel"><?php i18n('CANCEL'); ?></a></form>
			</div>
			
			<table id="editpages" class="edittable highlight striped paginate tree">
				<thead>
					<tr><th><?php i18n('PAGE_TITLE'); ?></th><th style="text-align:right;" ><?php i18n('DATE'); ?></th><th></th><th></th></tr>
				</thead>					
				<?php echo $table; ?>
			</table>
			<p><em><b><span id="pg_counter"><?php echo $count; ?></span></b> <?php i18n('TOTAL_PAGES'); ?></em></p>
			
		</div>
	</div><!-- end maincontent -->
	
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>

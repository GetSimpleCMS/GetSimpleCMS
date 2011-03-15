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

// Variable settings
login_cookie_check();
$id     =  isset($_GET['id']) ? $_GET['id'] : null;
$ptype    = isset($_GET['type']) ? $_GET['type'] : null; 
$path 		= GSDATAPAGESPATH;
$counter 	= '0';
$table 		= '';

//display all pages
$filenames = getFiles($path);

$count="0";
$pagesArray = array();
if (count($filenames) != 0) { 
	foreach ($filenames as $file) {
		if (isFile($file, $path, 'xml')) {
			$data = getXML($path .$file);
			$status = $data->menuStatus;
			$pagesArray[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
			$pagesArray[$count]['parent'] = $data->parent;
			$pagesArray[$count]['menuStatus'] = $data->menuStatus;
			$pagesArray[$count]['private'] = $data->private;
			if ($data->parent != '') { 
				$parentdata = getXML($path . $data->parent .'.xml');
				$parentTitle = $parentdata->title;
				$pagesArray[$count]['sort'] = $parentTitle .' '. $data->title;
			} else {
				$pagesArray[$count]['sort'] = $data->title;
			}
			$pagesArray[$count]['url'] = $data->url;
			$pagesArray[$count]['date'] = $data->pubDate;
			$parentTitle = '';
			$count++;
		}
	}
}

$pagesSorted = subval_sort($pagesArray,'sort');
$table = get_pages_menu('','',0);

?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('PAGE_MANAGEMENT')); ?>
	
	<h1>
		<a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('PAGE_MANAGEMENT'); ?> <span>&raquo;</span> <?php i18n('ALL_PAGES'); ?>		
	</h1>
	
	<?php 
		include('template/include-nav.php');
		include('template/error_checking.php'); 
	?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
			<h3 class="floated"><?php i18n('PAGE_MANAGEMENT'); ?></h3>
			<div class="edit-nav clearfix" ><p><a href="#" id="filtertable" ><?php i18n('FILTER'); ?></a><a href="#" id="show-characters" ><?php i18n('TOGGLE_STATUS'); ?></a></div>
			<div id="filter-search">
				<form><input type="text" autocomplete="off" class="text" id="q" placeholder="<?php echo lowercase(i18n_r('FILTER')); ?>..." /> &nbsp; <a href="pages.php" class="cancel"><?php i18n('CANCEL'); ?></a></form>
			</div>
			<table id="editpages" class="edittable highlight paginate">
				<tr><th><?php i18n('PAGE_TITLE'); ?></th><th style="text-align:right;" ><?php i18n('DATE'); ?></th><th></th><th></th></tr>
				<?php echo $table; ?>
			</table>
			<?php if(defined('GSPAGER')) { ?><div id="page_counter" class="qc_pager"></div><?php } ?>	
			<!-- p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php i18n('TOTAL_PAGES'); ?></em></p -->
			
		</div>
	</div><!-- end maincontent -->
	
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

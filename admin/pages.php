<?php
/****************************************************
*
* @File: 	pages.php
* @Package:	GetSimple
* @Action:	Edit or create new pages for the website. 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable settings
login_cookie_check();
$id 		= @$_GET['id'];
$ptype 		= @$_GET['type'];
$path 		= tsl('../data/pages/');
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
			//$pagesArray[$count]['title'] = $data->title;
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
$counter = "0";
if (count($pagesSorted) != 0) { 
	foreach ($pagesSorted as $page) {	
		$counter++;
		if ($page['parent'] != '') {$page['parent'] = $page['parent']."/"; $dash = '<span>&nbsp;&nbsp;&lfloor;&nbsp;&nbsp;&nbsp;</span>'; } else { $dash = ""; }
		$table .= '<tr id="tr-'.$page['url'] .'" >';
		if ($page['title'] == '' ) { $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; }
		if ($page['menuStatus'] != '' ) { $page['menuStatus'] = ' <sup>['.$i18n['MENUITEM_SUBTITLE'].']</sup>'; } else { $page['menuStatus'] = ''; }
		if ($page['private'] != '' ) { $page['private'] = ' <sup>['.$i18n['PRIVATE_SUBTITLE'].']</sup>'; } else { $page['private'] = ''; }
		if ($page['url'] == 'index' ) { $homepage = ' <sup>['.$i18n['HOMEPAGE_SUBTITLE'].']</sup>'; } else { $homepage = ''; }
		$table .= '<td>'. @$dash .'<a title="'.$i18n['EDITPAGE_TITLE'].': '. cl($page['title']) .'" href="edit.php?id='. $page['url'] .'" >'. cl($page['title']) .'</a><span class="showstatus toggle" >'. $homepage . $page['menuStatus'] . $page['private'] .'</span></td>';
		$table .= '<td style="width:70px;text-align:right;" ><span>'. shtDate($page['date']) .'</span></td>';
		$table .= '<td class="secondarylink" >';
		$table .= '<a title="'.$i18n['VIEWPAGE_TITLE'].': '. cl($page['title']) .'" target="_blank" href="'. find_url($page['url'],$page['parent']) .'">#</a>';
		$table .= '</td>';
		$table .= '<td class="delete" ><a class="delconfirm" href="deletefile.php?id='. $page['url'] .'&nonce='.get_nonce("delete", "deletefile.php").'" title="'.$i18n['DELETEPAGE_TITLE'].': '. stripslashes(strip_tags(html_entity_decode($page['title']))) .'" >X</a></td></tr>';
		
	}
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['PAGE_MANAGEMENT']); ?>
	
	<h1>
		<a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['PAGE_MANAGEMENT']; ?> <span>&raquo;</span> <?php echo $i18n['ALL_PAGES']; ?>		
	</h1>
	
	<?php 
		include('template/include-nav.php');
		include('template/error_checking.php'); 
	?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
			<label><?php echo $i18n['PAGE_MANAGEMENT']; ?></label>
			<div class="edit-nav" ><p><?php echo $i18n['TOGGLE_STATUS']; ?> &nbsp;<input type="checkbox" id="show-characters" value="" /></p><div class="clear" ></div></div>
			<table id="editpages" class="edittable highlight paginate">
				<?php echo $table; ?>
			</table>
			<div id="page_counter" class="qc_pager"></div> 	
			<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php echo $i18n['TOTAL_PAGES']; ?></em></p>
		</div>
	</div><!-- end maincontent -->
	
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
<?php
/****************************************************
*
* @File: 		backups.php
* @Package:	GetSimple
* @Action:	Displays all available page backups. 	
*
*****************************************************/
 
	require_once('inc/functions.php');
	require_once('inc/plugin_functions.php');
	$userid = login_cookie_check();
	$path = tsl('../backups/pages/');
	$counter = '0';
	$table = '';
	
	//delete all backup files if the ?deleteall session parameter is set
	if (isset($_GET['deleteall'])) {
		$filenames = getFiles($path);
		foreach ($filenames as $file) {
			if (file_exists($path . $file) ) {
				if (isFile($file, $path, 'bak')) {
						unlink($path . $file);
					}
			}
		}
	}


  //display all page backups
	$filenames = getFiles($path);
	
	$count="0";
	$pagesArray = array();
	if (count($filenames) != 0) { 
		foreach ($filenames as $file) {
			if (isFile($file, $path, 'bak')) {
				$data = getXML($path .$file);
				$status = $data->menuStatus;
				$pagesArray[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
				$pagesArray[$count]['url'] = $data->url;
				$pagesArray[$count]['date'] = $data->pubDate;
				$count++;
			}
		}
		$pagesSorted = subval_sort($pagesArray,'title');
	}
	if (count($pagesSorted) != 0) { 
		foreach ($pagesSorted as $page) {					
			$counter++;
			$table .= '<tr id="tr-'.$page['url'] .'" >';
			if ($page['title'] == '' ) { $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; }
			$table .= '<td><a title="'.$i18n['VIEWPAGE_TITLE'].': '. cl($page['title']) .'" href="backup-edit.php?p=view&uri='. $page['url'] .'">'. cl($page['title']) .'</a></td>';
			$table .= '<td style="width:70px;text-align:right;" ><span>'. shtDate($page['date']) .'</span></td>';
			$table .= '<td class="delete" ><a class="delconfirm" title="'.$i18n['DELETEPAGE_TITLE'].': '. cl($page['title']) .'?" href="backup-edit.php?p=delete&uri='. $page['url'] .'">X</a></td>';
			$table .= '</tr>';
		}
	}	
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['BAK_MANAGEMENT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['BAK_MANAGEMENT']; ?> <span>&raquo;</span> <?php echo $i18n['ALL_PAGES']; ?></h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
			<label><?php echo $i18n['PAGE_BACKUPS'];?></label>
			<div class="edit-nav" ><a href="backups.php?deleteall" class="delconfirm" title="<?php echo $i18n['DELETE_ALL_BAK'];?>" accesskey="d" ><?php echo $i18n['ASK_DELETE_ALL'];?></a><div class="clear" ></div></div>
			<table class="highlight paginate">
				<?php echo $table; ?>
			</table>
			<p><em><b><?php echo $counter; ?></b> <?php echo $i18n['TOTAL_BACKUPS'];?></em></p>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>

<?php get_template('footer'); ?>
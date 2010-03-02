<?php 
/****************************************************
*
* @File: 	navigation.php
* @Package:	GetSimple
* @Action:	Displays and changes website settings 	
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
	
	$dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open ". GSDATAPAGESPATH);
	$filenames = array();
	while ($filename = readdir($dir_handle)) {
		$filenames[] = $filename;
	}
	
	$count="0"; $data = '';
	$pagesArray = array();
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			if ($file == "." || $file == ".." || is_dir(GSDATAPAGESPATH . $file) || $file == ".htaccess"  ) {
				// not a page data file
			} else {
					$data = getXML(GSDATAPAGESPATH . $file);
				if ($data->private != 'Y') {
					$pagesArray[$count]['menuStatus'] = $data->menuStatus;
					$pagesArray[$count]['menuOrder'] = $data->menuOrder;
					$pagesArray[$count]['menu'] = html_entity_decode($data->menu, ENT_QUOTES, 'UTF-8');
					$pagesArray[$count]['url'] = $data->url;
					$pagesArray[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
					$count++;
				}
			}
		}
	}
	
	$pagesSorted = subval_sort($pagesArray,'menuOrder');
	if (count($pagesSorted) != 0) { 
		echo '<h3>'.$i18n['CURRENT_MENU'].'</h3><table id="navlist">';
		echo '<tr ><th style="width:60px;">'.$i18n['PRIORITY'].'</th><th>'.$i18n['MENU_TEXT'].'</th></tr>';
		foreach ($pagesSorted as $page) {
			$sel = '';
			if ($page['menuStatus'] != '') { 
				
				if ($page['menuOrder'] == '') { 
					$page['menuOrder'] = "N/A"; 
				} 
				if ($page['menu'] == '') { 
					$page['menu'] = $page['title']; 
				}
				echo '<tr><td style="width:35px;" >'.$page['menuOrder'].'</td><td><a href="'.$page['url'].'" target="_blank" >'. $page['menu'] .'</a></td></tr>';
			}
		}
		echo '</table>';
	} else {
		echo '<p>'.$i18n['NO_MENU_PAGES'].'.</p>';	
	}
	closedir($dir_handle);
					
?>
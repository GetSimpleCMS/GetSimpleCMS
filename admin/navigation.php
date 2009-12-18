<html>
<head>
<style>
#navlist {
font-family:arial;
font-size:13px;
border-collapse:collapse;
width:250px;
}
#navlist tr {border:1px solid #eee;}
#navlist tr td, #navlist tr th {padding:6px;}
#navlist tr th {text-align:left;background:#f9f9f9;}
a:link, a:visited {
color:#415A66;
text-decoration:underline;
font-weight:bold;
}
	h3 {
		font-size:16px;
		font-family:georgia;
		font-weight:normal;
		color:#CF3805;
		margin:0 0 10px 0;
		}
a:hover {
color:#333;
text-decoration:underline;
font-weight:bold;
}
</style>
</head>
<body>
<?php 
/****************************************************
*
* @File: 	navigation.php
* @Package:	GetSimple
* @Action:	Displays and changes website settings 	
*
*****************************************************/

	require_once('inc/functions.php');
	
	// get pages
	$path = "../data/pages";
	$dir_handle = @opendir($path) or die("Unable to open $path");
	$filenames = array();
	while ($filename = readdir($dir_handle)) {
		$filenames[] = $filename;
	}
	
	$count="0"; $data = '';
	$pagesArray = array();
	if (count($filenames) != 0) {
		foreach ($filenames as $file) {
			if ($file == "." || $file == ".." || is_dir("../data/pages/".$file) || $file == ".htaccess"  ) {
				// not a page data file
			} else {
					$thisfile = @file_get_contents('../data/pages/'.$file);
					$data = simplexml_load_string($thisfile);
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
</body>
</html>
<?php 
/**
 * Menu Preview
 *
 * Previews the current main menu hierarchy  
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
	
	$dir_handle = opendir(GSDATAPAGESPATH) or die("Unable to open ". GSDATAPAGESPATH);
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
					$pagesArray[$count]['parent'] = $data->parent;
					$pagesArray[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
					$count++;
				}
			}
		}
	}
	
	$pagesSorted = subval_sort($pagesArray,'menuOrder');
	
	
if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
global $LANG;
$LANG_header = preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_header; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo i18n_r('CURRENT_MENU'); ?></title>
	<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="template/style.php?v=<?php echo GSVERSION; ?>" media="screen" />
</head>
<body id="navigation" >	
 <div class="wrapper" style="width:540px;" >
  <div id="maincontent" style="width:540px;" >
	<div class="main" style="border:none;">
	
	<?php
	if (count($pagesSorted) != 0) { 
		echo '<h3>'.i18n_r('CURRENT_MENU').'</h3><table class="highlight" style="width:500px;" >';
		echo '<tr ><th style="width:60px;">'.i18n_r('PRIORITY').'</th><th>'.i18n_r('MENU_TEXT').'</th><th>'.i18n_r('PAGE_TITLE').'</th><th></th><th></th></tr>';
		foreach ($pagesSorted as $page) {
			$sel = '';
			if ($page['menuStatus'] != '') { 
				
				if ($page['menuOrder'] == '') { 
					$page['menuOrder'] = "N/A"; 
				} 
				if ($page['menu'] == '') { 
					$page['menu'] = $page['title']; 
				}
				echo '<tr>
				<td style="width:35px;" >'.$page['menuOrder'].'</td>
				<td><strong>'. $page['menu'] .'</strong></td>
				<td>'. $page['title'] .'</td>
				<td><a href="'.find_url($page['url'], $page['parent']).'" target="_blank" >'.strip_tags(i18n_r('VIEW')).'</a></td>
				<td><a href="edit.php?id='.$page['url'].'" target="_blank" >'.strip_tags(i18n_r('EDIT')).'</a></td>
				</tr>';
			}
		}
		echo '</table>';
	} else {
		echo '<p>'.i18n_r('NO_MENU_PAGES').'.</p>';	
	}
	closedir($dir_handle);
					
?>
	</div>
  </div>
 </div>	
</body>
</html>
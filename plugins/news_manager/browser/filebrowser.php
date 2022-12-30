<?php
/**
 * Basic File Browser for News Manager (based on I18N Custom Fields')
 *
 * Displays and selects file link to insert
 */
require('../../../gsconfig.php');
if (!defined('GSADMIN')) define('GSADMIN', 'admin');
include('../../../'.GSADMIN.'/inc/common.php');
$loggedin = cookie_check();
if (!$loggedin) die('Not logged in');
if (isset($_GET['path'])) {
  $subPath = preg_replace('/\.+\//','',$_GET['path']);
  $path = "../../../data/uploads/".$subPath;
  if (strpos(realpath($path), realpath(GSDATAUPLOADPATH)) !== 0) die(); // no path traversal
} else {
  $subPath = "";
  $path = "../../../data/uploads/";
}
$path = tsl($path);

// check if host uses Linux (used for displaying permissions
$isUnixHost = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? false : true);

$fullPath = htmlentities((string) $SITEURL."data/uploads/", ENT_QUOTES);
$sitepath = htmlentities((string) $SITEURL, ENT_QUOTES);

$func = preg_replace('/[^\w]/', '', @$_GET['func']);
$type = @$_GET['type'];
$sort = @$_GET['sort'];
if (empty($sort) && defined('NMIMAGESORT'))
	$sort = NMIMAGESORT; // custom default
if (!in_array($sort, array('name','size','date')))
	$sort = 'date'; // default

if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
global $LANG;
$LANG_header = preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_header; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo i18n_r('FILE_BROWSER'); ?></title>
	<link rel="shortcut icon" href="../../../<?php echo GSADMIN; ?>/favicon.png" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="../../../<?php echo GSADMIN; ?>/template/style.php?v=<?php echo GSVERSION; ?>" media="screen" />
	<style>
		.wrapper, #maincontent, #imageTable { width: 100% }
	</style>
	<script type='text/javascript'>
	function submitLink(url) {
		if(window.opener){
			window.opener.<?php echo $func; ?>(url);
		}
		window.close();
	}
	</script>
</head>
<body id="filebrowser" >	
 <div class="wrapper">
  <div id="maincontent">
	<div class="main" style="border:none;">
		<h3><?php echo i18n('UPLOADED_FILES'); ?><span id="filetypetoggle">&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo ($type == 'images' ? i18n('IMAGES') : i18n('SHOW_ALL') ); ?></span></h3>
<?php
	$count="0";
	$dircount="0";
	$counter = "0";
	$totalsize = 0;
	$filesArray = array();
	$dirsArray = array();
	$filesSorted = array();
	$dirsSorted = array();

	$filenames = getFiles($path);
	if (count($filenames) != 0) { 
		foreach ($filenames as $file) {
			if ($file == "." || $file == ".." || $file == ".htaccess" ){
			// not a upload file
			} elseif (is_dir($path . $file)) {
			  $dirsArray[$dircount]['name'] = $file;
			  $dircount++;
			} else {
				$filesArray[$count]['name'] = $file;
				$ext = substr($file, strrpos($file, '.') + 1);
				$extension = get_FileType($ext);
				$filesArray[$count]['type'] = $extension;
				clearstatcache();
				$ss = @stat($path . $file);
				$filesArray[$count]['date'] = @date('M j, Y',$ss['ctime']);
				$filesArray[$count]['sortdate'] = $ss['ctime'];
				$filesArray[$count]['size'] = fSize($ss['size']);
				$totalsize = $totalsize + $ss['size'];
				$count++;
			}
		}
		if ($sort == 'name') {
			$filesSorted = subval_sort($filesArray,'name');
		} elseif ($sort =='size') {
			$filesSorted = subval_sort($filesArray,'size','desc');
		} else {
			$filesSorted = subval_sort($filesArray,'sortdate','desc');
		}
		$dirsSorted = subval_sort($dirsArray,'name');
	}

	$pathParts=explode("/",$subPath);
	$urlPath="";

	echo '<div class="h5">/ <a href="?func='.$func.'&amp;type='.$type.'&amp;sort='.$sort.'">uploads</a> / ';
	foreach ($pathParts as $pathPart){
		if ($pathPart!=''){
			$urlPath.=$pathPart."/";
			echo '<a href="?path='.$urlPath.'&amp;func='.$func.'&amp;type='.$type.'&amp;sort='.$sort.'">'.$pathPart.'</a> / ';
		}
	}
	echo "</div>";

	echo '<table class="highlight" id="imageTable">';

	$linksort = '?path='.$urlPath.'&amp;func='.$func.'&amp;type='.$type.'&sort=';
	echo '<tr><th class="imgthumb" ></th><th></th><th>';
	echo ($sort == 'name') ? i18n_r('FILE_NAME') : '<a href="'.$linksort.'name">'.i18n_r('FILE_NAME').'</a>';
	echo '</th><th style="text-align:right;">';
	echo ($sort == 'size') ? i18n_r('FILE_SIZE') : '<a href="'.$linksort.'size">'.i18n_r('FILE_SIZE').'</a>';
	echo '</th><th style="text-align:right;">';
	echo ($sort == 'date') ? i18n_r('DATE') : '<a href="'.$linksort.'date">'.i18n_r('DATE').'</a>';
	echo '</th></tr>';

	if (count($dirsSorted) != 0) {       
		foreach ($dirsSorted as $upload) {
			echo '<tr class="All" >';  
			echo '<td class="" colspan="5">';
			$adm = ($subPath ? $subPath . "/" : "") . $upload['name']; 
			echo '<img src="../../../'.GSADMIN.'/template/images/folder.png" width="11" /> <a href="filebrowser.php?path='.$adm.'&amp;func='.$func.'&amp;type='.$type.'&amp;sort='.$sort.'" title="'. $upload['name'] .'"  ><strong>'.$upload['name'].'</strong></a>';
			echo '</td>';
			echo '</tr>';
		}
	}

	if (count($filesSorted) != 0) { 			
		foreach ($filesSorted as $upload) {
			$thumb = null; $thumbnailLink = null;
			$subDir = ($subPath == '' ? '' : $subPath.'/');
			$selectLink = 'title="'.i18n_r('SELECT_FILE').': '. htmlspecialchars(@$upload['name']) .'" href="javascript:void(0)" onclick="submitLink(\''.$fullPath.$subDir.$upload['name'].'\')"';

			if ($type == 'images') {
				if ($upload['type'] == i18n_r('IMAGES') .' Images') {
					# get internal thumbnail to show beside link in table
					$thumb = '<td class="imgthumb" style="display:table-cell" >';
					$thumbLink = $urlPath.'thumbsm.'.$upload['name'];
					if (file_exists('../../../data/thumbs/'.$thumbLink)) {
						$imgSrc='<img src="../../../data/thumbs/'. $thumbLink .'" />';
					} else {
						$imgSrc='<img src="../../../'.GSADMIN.'/inc/thumb.php?src='. $urlPath . $upload['name'] .'&amp;dest='. $thumbLink .'&amp;x=65&amp;f=1" />';
					}
					$thumb .= '<a '.$selectLink.' >'.$imgSrc.'</a>';
					$thumb .= '</td>';
					
					# get external thumbnail link
					$thumbLinkExternal = 'data/thumbs/'.$urlPath.'thumbnail.'.$upload['name'];
					if (file_exists('../../../'.$thumbLinkExternal)) {
					$thumbnailLink = '<span>&nbsp;&ndash;&nbsp;&nbsp;</span><a href="javascript:void(0)" onclick="submitLink(\''.$sitepath.$thumbLinkExternal.'\')">'.i18n_r('THUMBNAIL').'</a>';
					}
				}
				else { continue; }
			}

			$counter++;	

			echo '<tr class="All '.$upload['type'].'" >';
			echo ($thumb=='' ? '<td style="display: none"></td>' : $thumb);
			echo '<td><a '.$selectLink.' class="primarylink">'.htmlspecialchars($upload['name']) .'</a>'.$thumbnailLink.'</td>';
			echo '<td style="width:80px;text-align:right;" ><span>'. $upload['size'] .'</span></td>';

			// get the file permissions.
			if ($isUnixHost && defined('GSDEBUG')) {
				$filePerms = substr(sprintf('%o', fileperms($path.$upload['name'])), -4);
				$fileOwner = function_exists('posix_getpwuid') ? @posix_getpwuid(fileowner($path.$upload['name'])) : null;
				if ($filePerms && @$fileOwner['name']){
					echo '<td style="width:70px;text-align:right;"><span>'.$fileOwner['name'].'/'.$filePerms.'</span></td>';
				}
			}

			echo '<td style="width:85px;text-align:right;" ><span>'. shtDate($upload['date']) .'</span></td>';
			echo '</tr>';
		}

	}
	echo '</table>';
	echo '<p><em><b>'. $counter .'</b> '.i18n_r('TOTAL_FILES').' ('. fSize($totalsize) .')</em></p>';
?>	
	</div>
  </div>
 </div>	
</body>
</html>

<?php
/**
 * Browse uploaded files
 *
 * based on upload.php, might be even possible to modify upload.php to prevent code duplication
 *
 * @package GetSimple
 * @subpackage Files
 *
 * @todo: use GSROOTPATH or GSUPLOADPATH (problems with paths in wamp server on windows)
 * fix upload, that it keeps all GET arguments passed from CKEDITOR
 * fix styling, add uploadify support, add creation of new directories
 * cleanup unnecesarry code
 */
 
// Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

$path = (isset($_GET['path'])) ? "../data/uploads/".$_GET['path'] : "../data/uploads/";
$subPath = (isset($_GET['path'])) ? $_GET['path'] : "";
$path = tsl($path);
?>
<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('FILE_MANAGEMENT')); ?>
</div>
</div>
<div class="wrapper" style="width:100%;" >
	
	<div class="bodycontent">
	<div id="maincontent">
		<div class="main" style="border:none;" >
		<h3 class="floated"><?php echo i18n('IMAGES'); ?></h3>
		<div class="clear"></div>
		<div id="file_load">
		<?php
			$count="0";
      		$dircount="0";
			$counter = "0";
			$totalsize = 0;
			$filesArray = array();
      		$dirsArray = array();

      		
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
						$extention = get_FileType($ext);
						$filesArray[$count]['type'] = $extention;
						clearstatcache();
						$ss = @stat($path . $file);
						$filesArray[$count]['date'] = @date('M j, Y',$ss['ctime']);
						$filesArray[$count]['size'] = fSize($ss['size']);
						$totalsize = $totalsize + $ss['size'];
						$count++;
					}
				}
				$filesSorted = subval_sort($filesArray,'name');
        $dirsSorted = subval_sort($dirsArray,'name');
			}
			
		  echo '<table class="highlight" id="imageTable">'; 
    
     //echo "<tr><td>";     
     
     $pathParts=explode("/",$subPath);
     $urlPath="/";
     
     echo '<h5><img src="template/images/folder.png" width="13px" /> <a href="filebrowser.php?CKEditor=post-content&CKEditorFuncNum=2&langCode=en">uploads</a> / ';
     //echo "</td></tr>";
     foreach ($pathParts as $pathPart){
       if ($pathPart!=''){
          $urlPath.=$pathPart."/";
          echo '<a href="?path='.$urlPath.'">'.$pathPart.'</a> / ';
       }
     }
      echo "</h5>";
     if (count($dirsSorted) != 0) {       

        foreach ($dirsSorted as $upload) {
          echo '<tr class="All" >';  
          echo '<td class="folder" colspan="5">';
        
          $adm = substr($path . $upload['name'] ,  16); 
          echo '<img src="template/images/folder.png" width="11px" /> <a href="filebrowser.php?path='.$adm.'&CKEditor=post-content&CKEditorFuncNum=2&langCode=en" ><strong>'.$upload['name'].'</strong></a>';
                   
          echo '</td>';
          echo '</tr>';
        }
     }
			if (count($filesSorted) != 0) { 			
				foreach ($filesSorted as $upload) {
					$counter++;
					if ($upload['type'] == i18n_r('IMAGES') .' Images') {
		
						echo '<tr class="All '.$upload['type'].' iimage" >';
						echo '<td class="imgthumb" style="display:table-cell" >';
						if (file_exists('../data/thumbs/thumbsm.'.$upload['name'])) {
							echo '<a href="'. $path . $upload['name'] .'" title="'. $upload['name'] .'" ><img src="../data/thumbs/thumbsm.'.$upload['name'].'" /></a>';
						} else {
							echo '<a href="'. $path . $upload['name'] .'" title="'. $upload['name'] .'" ><img src="inc/thumb.php?src='. $upload['name'] .'&amp;dest=thumbsm.'. $upload['name'] .'&amp;x=65&amp;f=1" /></a>';
						}
						echo '</td><td><a title="'.i18n_r('INSERT_FILE').': '. htmlspecialchars($upload['name']) .'" href="javascript:void(0);" class="primarylink" onclick="insertFile(\'/data/uploads/'. $subPath . $upload['name'] .'\');">'.htmlspecialchars($upload['name']) .'</a></td>';
						echo '<td style="width:80px;text-align:right;" ><span>'. $upload['size'] .'</span></td>';
	             
						echo '<td style="width:85px;text-align:right;" ><span>'. shtDate($upload['date']) .'</span></td>';
						echo '<td class="delete" ><a class="delconfirm" title="'.i18n_r('DELETE_FILE').': '. htmlspecialchars($upload['name']) .'" href="deletefile.php?file='. $upload['name'] .'&amp;nonce='.get_nonce("delete", "deletefile.php").'">X</a></td>';
						echo '</tr>';
						exec_action('file-extras');
					}
				}
			} else {
				echo '<div id="imageTable"></div>';
			}
			echo '</table>';
		?>	
		</div>
		</div>
	</div>
	<div class="clear"></div>
	</div>

<script type="text/javascript">
// function to retrieve GET params
$.urlParam = function(name){
	var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
	if (results)
		return results[1]; 
	else
		return 0;
}

// insert file name and path to CKEDITOR and close window
function insertFile(filename){
	if(window.opener){
		if($.urlParam('CKEditor')){
			window.opener.CKEDITOR.tools.callFunction($.urlParam('CKEditorFuncNum'), filename);
		}
		else {alert('unhandled or you lost GET parameters - e.g. by uploading file');}
		window.close();
	}
}
</script>	
	
</body>
</html>
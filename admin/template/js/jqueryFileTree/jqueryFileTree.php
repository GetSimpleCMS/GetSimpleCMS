<?php
/****************************************************
* @Function ListDir()
*****************************************************/
function ListDir($dir_handle,$path) {
	global $listing;
	$listing .= "<ul>";
	while (false !== ($file = readdir($dir_handle))) {
	  $dir =$path.'/'.$file;
	  if(is_dir($dir) && $file != '.' && $file !='..' ) {
			$handle = @opendir($dir) or die("Unable to open file $file");
			$listing .= "<li>".$dir;
			ListDir($handle, $dir);
			$listing .= "</li>";
	  } elseif($file != '.' && $file !='..' && $file !='.htaccess') {
			$listing .= "<li>".$file."</li>";
	  }
	}
	$listing .= "</ul>";
	closedir($dir_handle);
}
/***************************************************/
// paths and files to backup
$paths = array("../../../../data/uploads", "../../../../data/thumbs"); //no trailing slash

// cycle thru each path and file and add to zip file
foreach ($paths as $path) {
	$dir_handle = @opendir($path) or die("Unable to open $path");
	ListDir($dir_handle,$path);
}

echo $listing;

?>
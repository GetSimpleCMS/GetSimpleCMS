<?php

$_POST['dir'] = urldecode($_POST['dir']);

if( file_exists($_POST['dir']) ) {
	$files = scandir($_POST['dir']);
	natcasesort($files);
	echo "shit";
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		echo '<ul class="jqueryFileTree" style="display: none;">';
		// All dirs
		foreach( $files as $file ) {
			if( file_exists($_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($_POST['dir'] . $file) ) {
				echo '<li class="directory collapsed"><a href="#" rel="' . htmlentities($_POST['dir'] . $file) . '">' . htmlentities($file) . '</a></li>';
			}
		}
		// All files
		foreach( $files as $file ) {
			if( file_exists($_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($_POST['dir'] . $file) ) {
				$ext = preg_replace('/^.*\./', '', $file);
				echo '<li class="file ext_'.$ext.'"><a href="#" rel="' . htmlentities($_POST['dir'] . $file) . '">' . htmlentities($file) . '</a></li>';
			}
		}
		echo "</ul>";	
	}
}

?>
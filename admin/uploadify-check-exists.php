<?php
/*
Uploadify v3.0.0
Copyright (c) 2010 Ronnie Garcia

Return true if the file exists
*/
include('inc/common.php');

$path = (isset($_GET['path'])) ? $_GET['path'] . "/" : "";
$name = clean_img_name($_POST['filename']);
if (file_exists('../data/uploads/' .$path . $name)) {
	echo 1;
} else {
	echo 0;
}
?>

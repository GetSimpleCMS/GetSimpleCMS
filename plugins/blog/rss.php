<?php
require_once('inc/common.php');
require_once('class/Blog.php');
$Blog = new Blog;
if(isset($_GET['filter']) && isset($_GET['value']))
{
	$filter = array();
	$filter['filter'] = $_GET['filter'];
	$filter['value'] = urldecode($_GET['value']);
}
else
{
	$filter = false;
}
echo $Blog->generateRSSFeed(false, $filter);
?>
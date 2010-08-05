<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 		header.php
* @Package:	GetSimple
* @Action:	Template file for inserting the 
*						header into the control panel. 	
*
*****************************************************/

global $LANG;
$LANG_header = preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $LANG_header; ?>" lang="<?php echo $LANG_header; ?>">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo $title ?></title>
	
	<!-- Javascript -->
	<script type="text/javascript" src="template/js/jquery.min.js"></script>
	<script type="text/javascript" src="template/js/uploadify/swfobject.js"></script>
	<script type="text/javascript" src="template/js/uploadify/jquery.uploadify.js"></script>
	<script type="text/javascript" src="template/js/facybox/jquery.facybox.js"></script>
	<script type="text/javascript" src="template/js/jquery.reverseorder.js"></script>
	<script type="text/javascript" src="template/js/jquery.quickpaginate.js"></script>
	<script type="text/javascript" src="template/js/jquery.example.min.js"></script>
	<script type="text/javascript" src="template/js/jcrop/jquery.Jcrop.min.js"></script>
	<script type="text/javascript" src="template/js/jquery.getsimple.js"></script>
	
	<!-- CSS Stylesheets -->
	<link rel="stylesheet" type="text/css" href="template/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="template/js/facybox/jquery.facybox.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="template/js/jcrop/jquery.Jcrop.css" media="screen" />
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/ie6.css" media="screen" /><![endif]-->
	
	<!-- IE Fixes -->
	<script type="text/javascript"><!--
		try {
			document.execCommand("BackgroundImageCache", false, true);
		} catch(err) {}
		/* IE6 flicker hack from http://dean.edwards.name/my/flicker.html */
	--></script>
	<noscript><style type="text/css">#metadata_window {display:block !important} </style></noscript>
	
	<?php exec_action('header'); ?>
	
</head>

<body <?php filename_id(); ?> >	
	<div class="header">
	<div class="wrapper">		
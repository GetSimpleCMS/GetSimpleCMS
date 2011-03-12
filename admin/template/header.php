<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 *
 * @package GetSimple
 */

global $LANG;
$LANG_header = preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG);
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_header; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo $title ?></title>
	<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
	<meta name="generator" content="GetSimple - <?php echo GSVERSION; ?>" /> 
	<meta name="robots" content="noindex, nofollow">
	
	
	<!-- iOS meta tags to optimize experience -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png"/>
	
	
	<!-- Javascript Plugins -->
	<script type="text/javascript" src="template/js/jquery.min.js?v=1.5.1"></script>
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!defined('GSNOUPLOADIFY')) ) { ?>
	<script type="text/javascript" src="template/js/uploadify/jquery.uploadify.js?v=3.0"></script>
	<?php } ?>
	<?php if(defined('GSPAGER')) { ?>
	<script type="text/javascript" src="template/js/jquery.quickpaginate.js"></script>
	<?php } ?>	
	<?php if(get_filename_id()=='image') { ?>
	<script type="text/javascript" src="template/js/jcrop/jquery.Jcrop.min.js"></script>
	<link rel="stylesheet" type="text/css" href="template/js/jcrop/jquery.Jcrop.css" media="screen" />
	<?php } ?>
	<?php if(get_filename_id()=='edit') { ?>
	<noscript><style>#metadata_window {display:block !important} </style></noscript>
	<?php } ?>
	
	<script type="text/javascript" src="template/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="template/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />		


	<!-- GetSimple specific files -->
	<script type="text/javascript" src="template/js/jquery.getsimple.js?v=<?php echo GSVERSION; ?>"></script>
	<link rel="stylesheet" type="text/css" href="template/style.php?v=<?php echo GSVERSION; ?>" media="screen" />
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/ie6.css?v=<?php echo GSVERSION; ?>" media="screen" />
	<![endif]-->
	
	<?php 
		# Plugin hook to allow insertion of stuff into the header
		exec_action('header'); 
	?>
	
</head>

<body <?php filename_id(); ?> >	
	<div class="header">
	<div class="wrapper">		
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 *
 * @package GetSimple
 */

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

	<?php if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!defined('GSNOUPLOADIFY')) ) { ?>
	<script type="text/javascript" src="template/js/uploadify/swfobject.js"></script>
	<script type="text/javascript" src="template/js/uploadify/jquery.uploadify.js"></script>
	<?php } ?>
	<?php if(get_filename_id()=='log') { ?>
	<script type="text/javascript" src="template/js/jquery.reverseorder.js"></script>
	<?php } ?>
	<?php if(defined('GSPAGER')) { ?>
	<script type="text/javascript" src="template/js/jquery.quickpaginate.js"></script>
	<?php } ?>	
	<?php if(get_filename_id()=='image') { ?>
	<script type="text/javascript" src="template/js/jcrop/jquery.Jcrop.min.js"></script>
	<link rel="stylesheet" type="text/css" href="template/js/jcrop/jquery.Jcrop.css" media="screen" />
	<?php } ?>
	<?php if(get_filename_id()=='edit') { ?>
	<noscript><style type="text/css">#metadata_window {display:block !important} </style></noscript>
	<?php } ?>

	<script type="text/javascript" src="template/js/facybox/jquery.facybox.js"></script>
	<link rel="stylesheet" type="text/css" href="template/js/facybox/jquery.facybox.css" media="screen" />		

	<script type="text/javascript" src="template/js/jquery.getsimple.js"></script>
	<link rel="stylesheet" type="text/css" href="template/style.css" media="screen" />
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/ie6.css" media="screen" /><![endif]-->
	
	<?php exec_action('header'); ?>
	
</head>

<body <?php filename_id(); ?> >	
	<div class="header">
	<div class="wrapper">		
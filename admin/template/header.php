<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 *
 * @package GetSimple
 */
 
global $SITENAME, $SITEURL;

$GSSTYLE = getDef('GSSTYLE') ? GSSTYLE : '';

if(get_filename_id()!='index') exec_action('admin-pre-header');
?>
<!DOCTYPE html>
<html lang="<?php echo get_site_lang(true); ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo $title ?></title>
	<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
	<meta name="generator" content="GetSimple - <?php echo GSVERSION; ?>" />
	<link rel="author" href="humans.txt" />
	<meta name="robots" content="noindex, nofollow">
	<link rel="apple-touch-icon" href="apple-touch-icon.png"/>
	<link rel="stylesheet" type="text/css" href="template/style.php?<?php echo 's='.$GSSTYLE.'&amp;v='.GSVERSION; ?>" media="screen" />
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/ie6.css?v=<?php echo GSVERSION; ?>" media="screen" /><![endif]-->
	<?php get_scripts_backend(); ?>
		
	<script type="text/javascript" src="template/js/jquery.getsimple.js?v=<?php echo GSVERSION; ?>"></script>		
	
	<!--[if lt IE 9]><script type="text/javascript" src="//html5shiv.googlecode.com/svn/trunk/html5.js" ></script><![endif]-->
	<?php if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!defined('GSNOUPLOADIFY')) ) { ?>
	<script type="text/javascript" src="template/js/uploadify/jquery.uploadify.js?v=3.0"></script>
	<?php } ?>
	<?php if(get_filename_id()=='image') { ?>
	<script type="text/javascript" src="template/js/jcrop/jquery.Jcrop.min.js"></script>
	<link rel="stylesheet" type="text/css" href="template/js/jcrop/jquery.Jcrop.css" media="screen" />
	<?php } ?>

	<?php 
	# Plugin hook to allow insertion of stuff into the header
	if(get_filename_id()!='index') exec_action('header'); 
	
	function doVerCheck(){
		if( get_filename_id()!='resetpassword' && 
			get_filename_id()!='index' && 
			!getDef('GSNOVERCHECK')
		){
			return true;	
		}
	}
	
	if( doVerCheck() ) { ?>
	<script>
		// check to see if core update is needed
		jQuery(document).ready(function() { 
			<?php 
				$data = get_api_details();
				if ($data)      {
					$apikey = json_decode($data);
					$verstatus = $apikey->status;
			?>
				var verstatus = <?php echo $verstatus; ?>;
				if(verstatus != 1) {
					$('a.support').parent('li').append('<span class="warning">!</span>');
					$('a.support').attr('href', 'health-check.php');
				}
			<?php  } ?>
		});
	</script>
	<?php } ?>
	
	
</head>

<body <?php filename_id(); ?> >	
	<div class="header" id="header" >
		<div class="wrapper clearfix">
 <?php exec_action('header-body'); ?>

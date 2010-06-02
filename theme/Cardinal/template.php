<?php
/****************************************************
*
* @File: 			template.php
* @Package:		GetSimple
* @Action:		Cardinal theme for the GetSimple CMS
*
*****************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

	<!-- Site Title -->
	<title><?php get_page_clean_title(); ?> &lt; <?php get_site_name(); ?></title>
	<?php get_header(); ?>
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/style.css" media="all" />

	<script type="text/javascript"><!--
		try {
			document.execCommand("BackgroundImageCache", false, true);
		} catch(err) {}
		/* IE6 flicker hack from http://dean.edwards.name/my/flicker.html */
	--></script>

</head>
<body id="<?php get_page_slug(); ?>" >

<div id="wrapper">

	<div id="header">
		
		<ul id="nav">
			<?php get_navigation(return_page_slug()); ?>
		</ul>
		
		<span class="logo2" href="<?php get_site_url(); ?>"><?php get_site_name(); ?></span>
		<a class="logo" href="<?php get_site_url(); ?>"><?php get_site_name(); ?></a>

	</div><!-- end header -->
	
	<div id="content">
		<h1><?php get_page_title(); ?></h1>	
			<div id="page-content">
				<div class="page-text">
					<?php get_page_content(); ?>
					<p class="page-meta">Published on &nbsp;<span><?php get_page_date('F jS, Y'); ?></span></p>
				</div>
			</div>
	</div>	
		
	<div id="sidebar">
		<div class="section">
			<?php get_component('sidebar');	?>
		</div>
		<div class="section credits">
			<p><?php echo date('Y'); ?> - <strong><?php get_site_name(); ?></strong></p>
			<p>
				Cardinal Theme by <a href="http://www.cagintranet.com" title="Pittsburgh Creative Agency" >Cagintranet</a><br />
				<?php get_site_credits(); ?>
			</p>
		</div>
	</div>
	
	<div class="clear"></div>
	
	<?php get_footer(); ?>
	
</div><!-- end wrapper -->
</body>
</html>
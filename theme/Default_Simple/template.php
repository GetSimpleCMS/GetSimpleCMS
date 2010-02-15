<?php
/****************************************************
*
* @File: 			template.php
* @Package:		GetSimple
* @Action:		Default theme for the GetSimple CMS
*
*****************************************************/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php get_page_clean_title(); ?> | <?php get_site_name(); ?>, <?php get_component('tagline'); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<?php get_header(); ?>
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" type="text/css" href="<?php get_theme_url(); ?>/default.css" media="all" />

	<script type="text/javascript"><!--
		try {
			document.execCommand("BackgroundImageCache", false, true);
		} catch(err) {}
		/* IE6 flicker hack from http://dean.edwards.name/my/flicker.html */
	--></script>
</head>

<body id="<?php get_page_slug(); ?>" >
<div class="wrapper">
	<div id="header">
		<a class="logo" href="<?php get_site_url(); ?>"><?php get_site_name(); ?></a>
		<p class="tagline"><?php get_component('tagline'); ?></p>
		<ul id="nav">
			<?php get_navigation(return_page_slug()); ?>
		</ul>
	</div><!-- end div#header -->

	<div id="bodycontent">
	
		<div class="post">
			<h1><?php get_page_title(); ?></h1>
			<div class="postcontent">
				<?php get_page_content(); ?>
			</div>
			<p class="meta" >
				<b>Permalink:</b> <?php get_page_url(); ?><br />
				<b>Last Saved:</b> <?php get_page_date('F jS, Y'); ?>
			</p>
		</div>
	</div><!-- end div#bodycontent -->
	
	<div id="sidebar">
		<div class="featured">
			<?php get_component('sidebar');	?>
			<div class="clear"></div>
		</div>
	</div><!-- end div#sidebar -->
	
	<div class="clear"></div>	
	
	<div id="footer">
		<p class="left-footer"><?php echo date('Y'); ?> <strong><?php get_site_name(); ?></strong></p>
		<p class="right-footer"><?php get_site_credits(); ?><br /><a href="http://www.cagintranet.com" title="GetSimple Creators" >Theme by Cagintranet</a></p>
		<div class="clear"></div>
		
		<?php get_footer(); ?>
		
	</div><!-- end div#footer -->

</div><!-- end div.wrapper -->

</body>
</html>
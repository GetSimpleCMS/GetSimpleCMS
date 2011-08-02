<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 			sidebar.php
* @Package:		GetSimple
* @Action:		Innovation theme for the GetSimple 3.0
*
*****************************************************/
?><aside id="sidebar">

	<div class="section" id="socialmedia" >
		<h2>Connect</h2>
		<div class="icons">
			
			<!-- Social Media URLs are set within this theme's settings plugin -->
			<?php if (defined('FACEBOOK')) { ?>
				<a href="<?php echo FACEBOOK; ?>"><img src="<?php get_theme_url(); ?>/assets/images/facebook.png" /></a>
			<?php } ?>
			<?php if (defined('TWITTER')) { ?>
				<a href="<?php echo TWITTER; ?>"><img src="<?php get_theme_url(); ?>/assets/images/twitter.png" /></a>
			<?php } ?>
			<?php if (defined('LINKEDIN')) { ?>
				<a href="<?php echo LINKEDIN; ?>"><img src="<?php get_theme_url(); ?>/assets/images/linkedin.png" /></a>
			<?php } ?>
			
			<img src="<?php get_theme_url(); ?>/assets/images/break.png" />
			
			<!-- addthis popup - you can add your username if you want analytics: http://www.addthis.com/help/customizing-addthis -->
			<div class="addthis_toolbox" style="display:inline;width:24px;" >
				<a href="http://www.addthis.com/bookmark.php?v=250" class="addthis_button_compact"><img src="<?php get_theme_url(); ?>/assets/images/share.png" /></a>
			</div>
			<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js"></script>
		</div>
	</div>
	
	
	<!-- wrap each sidebar section like this -->
	<div class="section">
		<?php get_component('sidebar');	?>
	</div>

	
</aside>
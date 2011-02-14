<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 			tempate.php
* @Package:		GetSimple
* @Action:		Innovation theme for the GetSimple 3.0
*
*****************************************************/


# Get this theme's settings based on what was entered within it's plugin. 
# This function is in functions.php 
Innovation_Settings();

# Include the header template
include('header.php'); 
?>
	
	<div class="wrapper clearfix">
		<!-- page content -->
		<article>
			<section>
				
				<!-- title and content -->
				<h1><?php get_page_title(); ?></h1>
				<?php get_page_content(); ?>
				
				<!-- page footer -->
				<div class="footer">
					<p>Published on <time datetime="<?php get_page_date('Y-m-d'); ?>" pubdate><?php get_page_date('F jS, Y'); ?></time></p>
				</div>
			</section>
			
		</article>
		
		<!-- include the sidebar template -->
		<?php include('sidebar.php'); ?>
	</div>

<!-- include the footer template -->
<?php include('footer.php'); ?>
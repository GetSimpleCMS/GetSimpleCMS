<?php 
Innovation_Settings();
include('header.php'); 
?>
	
	<div class="wrapper clearfix">
		<!-- page content -->
		<article>
			<section>
				
				<h1><?php get_page_title(); ?></h1>
				<?php get_page_content(); ?>
				
				<!-- page footer -->
				<div class="footer">
					<p>Published on <b><?php get_page_date('F jS, Y'); ?></b></p>
				</div>
			</section>
			
		</article>
	
		<?php include('sidebar.php'); ?>
	</div>
	
<?php include('footer.php'); ?>
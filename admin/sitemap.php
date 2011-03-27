<?php
/**
 * View Sitemap
 *
 * Displays your site's sitemap
 *
 * @package GetSimple
 * @subpackage Theme
 */
 
// Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();
if (!generate_sitemap()) {
	$error = generate_sitemap();
}
?>
<?php get_template('header', cl($SITENAME).' &raquo; '.strip_tags(i18n_r('SIDE_VIEW_SITEMAP'))); ?>

	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo strip_tags(i18n_r('SIDE_VIEW_SITEMAP')); ?></h1>
	<?php include('template/include-nav.php');?>
	<?php include('template/error_checking.php');?>
	<div class="bodycontent">
	<div id="maincontent">
		<div class="main" >
			<h3><?php echo i18n('SIDE_VIEW_SITEMAP'); ?></h3>
			
			<pre><code><?php echo htmlentities(formatXmlString(file_get_contents('../sitemap.xml')));?></code></pre>
		
		</div>
	</div>
		<div id="sidebar" >
		<?php include('template/sidebar-theme.php'); ?>
		</div>	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

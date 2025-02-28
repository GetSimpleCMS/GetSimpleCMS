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

exec_action('load-sitemap');
$sitemapfile = '../'.GSSITEMAPFILE;

if(!file_exists($sitemapfile) || isset($_GET['refresh'])){
	if(generate_sitemap() === true)	$success = i18n_r('SITEMAP_REFRESHED');
	else $error = i18n_r('SITEMAP_ERROR');
}

$pagetitle = strip_tags(i18n_r('SIDE_VIEW_SITEMAP'));
get_template('header');

?>

<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php echo i18n_r('SIDE_VIEW_SITEMAP'); ?></h3>
			<div class="edit-nav clearfix" >
				<a href="<?php echo $sitemapfile;?>" target="_blank" accesskey="<?php echo find_accesskey(i18n_r('VIEW'));?>" ><?php i18n('VIEW'); ?></a>
				<a href="sitemap.php?refresh" accesskey="<?php echo find_accesskey(i18n_r('REFRESH'));?>" ><?php i18n('REFRESH'); ?></a>
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>	
			<div class="unformatted">
				<code><?php
				if (file_exists($sitemapfile)) {
					echo htmlentities(formatXmlString(read_file($sitemapfile)));
				}
				?>
				</code>
			</div>
		</div>
	</div>
	<div id="sidebar" >
	<?php include('template/sidebar-theme.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>

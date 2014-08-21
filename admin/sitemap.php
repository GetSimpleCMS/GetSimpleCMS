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

$sitemap = generate_sitemap();
if ($sitemap !== true) {
	$error = $sitemap;
} else {
	if (isset($_GET['refresh'])) {
		$success = i18n_r('SITEMAP_REFRESHED');
	}
}

$pagetitle = strip_tags(i18n_r('SIDE_VIEW_SITEMAP'));
get_template('header');

$sitemapfile = '../'.GSSITEMAPFILE;
?>

<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php echo i18n_r('SIDE_VIEW_SITEMAP'); ?></h3>
			<div class="edit-nav clearfix" >
				<a href="<?php echo $sitemapfile;?>" target="_blank" accesskey="<?php echo find_accesskey(i18n_r('VIEW'));?>" ><?php i18n('VIEW'); ?></a>
				<a href="sitemap.php?refresh" accesskey="<?php echo find_accesskey(i18n_r('REFRESH'));?>" ><?php i18n('REFRESH'); ?></a>
			</div>
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

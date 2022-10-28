<?php 
/**
 * Support
 *
 * @package GetSimple
 * @subpackage Support
 */

# Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-support');

$pagetitle = i18n_r('SUPPORT');
get_template('header');

?>

<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
	
			<h3 class="floated"><?php i18n('GETTING_STARTED');?></h3>
			<div class="edit-nav clearfix" >
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>		
			<ul>
				<li><a href="http://get-simple.info/docs/" target="_blank" ><?php i18n('SIDE_DOCUMENTATION'); ?></a></li>
				<li><a href="http://get-simple.info/forum/" target="_blank" ><?php i18n('SUPPORT_FORUM'); ?></a></li>
				<li><a href="http://get-simple.info/extend/" target="_blank" ><?php echo str_replace(array('<em>','</em>'), '', i18n_r('GET_PLUGINS_LINK')); ?></a></li>
				<li><a href="share.php?term=<?php i18n('SHARE'); ?>" rel="fancybox" ><?php i18n('SHARE'); ?> GetSimple</a></li>
				<li><a href="https://github.com/GetSimpleCMS" target="_blank">Github SVN</a></li>
			</ul>
			
			<p><?php i18n('WELCOME_MSG'); ?> <?php i18n('WELCOME_P'); ?></p>
			
			<ul>
				<li><a href="health-check.php"><?php i18n('WEB_HEALTH_CHECK'); ?></a></li>
				<li><a href="edit.php"><?php i18n('CREATE_NEW_PAGE'); ?></a></li>
				<li><a href="upload.php"><?php i18n('UPLOADIFY_BUTTON'); ?></a></li>
				<li><a href="settings.php"><?php i18n('GENERAL_SETTINGS'); ?></a></li>
				<li><a href="theme.php"><?php i18n('CHOOSE_THEME'); ?></a></li>
				<?php exec_action('welcome-link'); // @hook welcome-link support welcome list links output ?>
				<?php exec_action('welcome-doc-link'); // @hook welcome-doc-link support welcome list links output?>
			</ul>
			
			<h3><?php i18n('SUPPORT');?></h3>
			<ul>
				<li><a href="log.php?log=failedlogins.log"><?php i18n('VIEW_FAILED_LOGIN');?></a></li>
				<li><a href="plugins.php"><?php i18n('PLUGINS_MANAGEMENT');?></a></li>
				<li><a href="backups.php"><?php i18n('PAGE_BACKUPS');?></a></li>
				<li><a href="archive.php"><?php i18n('WEBSITE_ARCHIVES');?></a></li>
				<li><a href="theme.php"><?php i18n('THEME_MANAGEMENT');?></a></li>
				<li><a href="sitemap.php"><?php i18n('VIEW_SITEMAP');?></a></li>
				<br/>
				<?php exec_action('support-extras'); // @hook support-extras  support links list html ?>
			</ul>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>

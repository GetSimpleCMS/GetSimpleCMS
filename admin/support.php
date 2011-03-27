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
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('SUPPORT') ); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('SUPPORT');?> <span>&raquo;</span> <?php i18n('SETTINGS');?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">

		<h3><?php i18n('SUPPORT');?></h3>
		<p>
			<a href="welcome.php" class="button" ><?php i18n('GETTING_STARTED'); ?></a><a href="http://get-simple.info/wiki/" class="button" target="_blank" ><?php i18n('SIDE_DOCUMENTATION'); ?></a><a href="http://get-simple.info/forum/" class="button" target="_blank" ><?php i18n('SUPPORT_FORUM'); ?></a>
		</p>

		<ol>
			<?php if (file_exists($path . 'logs/failedlogins.log')) { ?>
				<li><p><a href="log.php?log=failedlogins.log"><?php i18n('VIEW_FAILED_LOGIN');?></a></p></li>
			<?php } ?>
			<?php exec_action('support-extras'); ?>
		</ol>

		</div>
	</div>



	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
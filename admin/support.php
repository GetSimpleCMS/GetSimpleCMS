<?php 
/**
 * Support
 *
 * @package GetSimple
 * @subpackage Support
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

$path = GSDATAOTHERPATH;
$bakpath = GSBACKUPSPATH.'other/';

// if the undo command was invoked
if (isset($_GET['undo'])) { 
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "undo", "support.php"))
		die("CSRF detected!");	

	$ufile = 'cp_settings.xml';
	undo($ufile, $path, $bakpath);
	redirect('support.php?rest=true');
}

if (isset($_GET['restored'])) { 
	$restored = 'true'; 
} else {
	$restored = 'false';
}

// were changes submitted?
if(isset($_POST['submitted'])) {
	$success = i18n_r('SETTINGS_UPDATED').'. <a href="support.php?undo&nonce='.get_nonce("restore", "support.php").'">'.i18n_r('UNDO').'</a>';
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('SUPPORT') ); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('SUPPORT');?> <span>&raquo;</span> <?php i18n('SETTINGS');?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
		<h3><?php i18n('SIDE_VIEW_LOG');?></h3>
		<ol>
			<?php if (file_exists($path . 'logs/404monitoring.log')) { ?>
				<li><p><a href="log.php?log=404monitoring.log"><?php i18n('VIEW_404');?></a></p></li>
			<?php } ?>
			<?php if (file_exists($path . 'logs/failedlogins.log')) { ?>
				<li><p><a href="log.php?log=failedlogins.log"><?php i18n('VIEW_FAILED_LOGIN');?></a></p></li>
			<?php } ?>
			<?php if (file_exists($path . 'logs/tickets.log')) { ?>
				<li><p><a href="log.php?log=tickets.log"><?php i18n('VIEW_TICKETS');?></a></p></li>
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
<?php 
/****************************************************
*
* @File: 		welcome.php
* @Package:	GetSimple
* @Action:	Displays a welcome message	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');
	
// Variable settings
login_cookie_check();
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['WELCOME']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['WELCOME'];?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
			<p><strong><?php echo $i18n['WELCOME_MSG']; ?></strong></p> 
			<p><?php echo $i18n['WELCOME_P']; ?></p>
			
			<h3><?php echo $i18n['GETTING_STARTED']; ?></h3>
			<ul>
				<li><a href="health-check.php"><?php echo $i18n['WEB_HEALTH_CHECK']; ?></a></li>
				<li><a href="edit.php"><?php echo $i18n['CREATE_NEW_PAGE']; ?></a></li>
				<li><a href="upload.php"><?php echo $i18n['FILE_MANAGEMENT']; ?></a></li>
				<li><a href="settings.php"><?php echo $i18n['GENERAL_SETTINGS']; ?></a></li>
				<li><a href="theme.php"><?php echo $i18n['THEME_MANAGEMENT']; ?></a></li>
				<?php exec_action('welcome-link'); ?>
			</ul>
			<ul>
				<li><a href="http://get-simple.info/docs/"><?php echo str_replace(array("<em>","</em>"), '', $i18n['SIDE_DOCUMENTATION']); ?></a></li>
				<?php exec_action('welcome-doc-link'); ?>
			</ul>
		</div>
		
	</div>
	
	<div id="sidebar" >

	</div>	
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
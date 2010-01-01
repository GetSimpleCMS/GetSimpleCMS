<?php 
/****************************************************
*
* @File: 		welcome.php
* @Package:	GetSimple
* @Action:	Displays a welcome message	
*
*****************************************************/

	require_once('inc/functions.php');
	$file = 'website.xml';
	$path = tsl('../data/other/');
	global $SITENAME;
	global $SITEURL;
	
	$userid = login_cookie_check();
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
				<li><a href="pages.php"><?php echo $i18n['CREATE_NEW_PAGE']; ?></a></li>
				<li><a href="upload.php"><?php echo $i18n['FILE_MANAGEMENT']; ?></a></li>
				<li><a href="settings.php"><?php echo $i18n['GENERAL_SETTINGS']; ?></a></li>
				<li><a href="theme.php"><?php echo $i18n['THEME_MANAGEMENT']; ?></a></li>
			</ul>
			<ul>
				<li><a href="http://get-simple.info/docs/"><?php echo $i18n['SIDE_DOCUMENTATION']; ?></a></li>
			</ul>
		</div>
		
	</div>
	
	<div id="sidebar" >

	</div>	
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
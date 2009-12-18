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
			<p><strong>Thank you for choosing GetSimple as your CMS!</strong></p> 
			<p>GetSimple makes managing your website as simple as possible with its top-of-the-class user interface and the easiest templating system around.  </p>
			
			<h3>Getting Started</h3>
			<ul>
				<li><a href="pages.php">Create a New Page</a></li>
				<li><a href="upload.php">Upload Files</a></li>
				<li><a href="settings.php">Change my Settings</a></li>
				<li><a href="theme.php">Change my Theme</a></li>
			</ul>
			<ul>
				<li><a href="http://get-simple.info/docs/">GetSimple Documentation</a></li>
			</ul>
		</div>
		
	</div>
	
	<div id="sidebar" >

	</div>	
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
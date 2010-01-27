<?php 
/****************************************************
*
* @File: 		support.php
* @Package:	GetSimple
* @Action:	Displays and changes website settings 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';
$path = $relative. 'data/other/';
$bakpath = $relative. 'backups/other/';

// Include common.php
include('inc/common.php');
	
// Variable settings
login_cookie_check();

// if the undo command was invoked
if (isset($_GET['undo']))
{ 
	$ufile = 'cp_settings.xml';
	undo($ufile, $path, $bakpath);
	
	header('Location: support.php?rest=true');
}

if (isset($_GET['restored']))
{ 
	$restored = 'true'; 
} 
else 
{
	$restored = 'false';
}

// were changes submitted?
if(isset($_POST['submitted']))
{
	$FOUR04MONITOR = @$_POST['fouro4monitoring'];

	// create new cpsettings data file
	$ufile = 'cp_settings.xml';
	createBak($ufile, $path, $bakpath);
	$xmlc = @new SimpleXMLElement('<item></item>');
	$xmlc->addChild('HTMLEDITOR', @$HTMLEDITOR);
	$xmlc->addChild('PRETTYURLS', @$PRETTYURLS);
	$xmlc->addChild('FOUR04MONITOR', @$FOUR04MONITOR);
	exec_action('support-save');
	$xmlc->asXML($path . $ufile);

	$success = $i18n['SETTINGS_UPDATED'].'. <a href="support.php?undo">'.$i18n['UNDO'].'</a>';
}

//are any of the control panel checkboxes checked?
$four04chck = '';

if ($FOUR04MONITOR != '' ) { $four04chck = 'checked'; }
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['SUPPORT']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['SUPPORT'];?> <span>&raquo;</span> <?php echo $i18n['SETTINGS'];?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	
	<?php 
	if (isset($_GET['err'])) {
		echo '<div class="error"><b>'.$i18n['ERROR'].':</b> '.$_GET['err'].'</div>';
	} elseif (isset($_GET['success'])) {
		echo '<div class="updated">'.$_GET['success'].'</div>';
	} elseif (isset($success)) {
		echo '<div class="updated">'.$success.'</div>';
	}
	?>
<div class="bodycontent">
	
	<div id="maincontent">
<?php 
		if (isset($_GET['plugin']) && isset($_GET['page'])) { 
			include "plugins/".$_GET['plugin']."/".$_GET['page'].".php";
		} else { ?>	
		<div class="main">	
		<form class="largeform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
		<h3><?php echo $i18n['SUPPORT'];?> <?php echo $i18n['SETTINGS'];?></h3>
		<p><input name="fouro4monitoring" type="checkbox" value="1" <?php echo $four04chck; ?>  /> &nbsp;<?php echo $i18n['EMAIL_ON_404'];?>.</p>
		<p><input class="submit" type="submit" name="submitted" value="<?php echo $i18n['BTN_SAVESETTINGS'];?>" /></p>
		<?php exec_action('support-extras'); ?>
	</form>
	</div>
		
		
		<div class="main">
		<h3><?php echo $i18n['SIDE_VIEW_LOG'];?></h3>
		<ol>
			<?php if (file_exists($path . 'logs/404monitoring.log')) { ?>
				<li><p><a href="log.php?log=404monitoring.log"><?php echo $i18n['VIEW_404'];?></a></p></li>
			<?php } ?>
			<?php if (file_exists($path . 'logs/contactform.log')) { ?>
				<li><p><a href="log.php?log=contactform.log"><?php echo $i18n['VIEW_CONTACT_FORM'];?></a></p></li>
			<?php } ?>			
			<?php if (file_exists($path . 'logs/failedlogins.log')) { ?>
				<li><p><a href="log.php?log=failedlogins.log"><?php echo $i18n['VIEW_FAILED_LOGIN'];?></a></p></li>
			<?php } ?>
			<?php if (file_exists($path . 'logs/tickets.log')) { ?>
				<li><p><a href="log.php?log=tickets.log"><?php echo $i18n['VIEW_TICKETS'];?></a></p></li>
			<?php } ?>
		</ol>
		</div>
		<?php } ?>
	</div>



	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
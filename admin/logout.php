<?php
/****************************************************
*
* @File: 		logout.php
* @Package:	GetSimple
* @Action:	Logs the current user out of the cp 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// end it all :'(
kill_cookie($cookie_name);
exec_action('logout');
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['LOGGED_OUT']); ?>
	
<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['LOGGED_OUT'];?></h1>
</div>
</div>
<div class="wrapper">
<div id="maincontent">
	<div class="main" >
	<h3><?php echo $i18n['MSG_LOGGEDOUT'];?></h3>
	<p><?php echo $i18n['MSG_PLEASE'];?>. <a href="index.php"><?php echo $i18n['LOGIN'];?></a></p>
	</div>
</div>

<div class="clear"></div>

<?php get_template('footer'); ?>
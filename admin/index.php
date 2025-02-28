<?php
/**
 * Login
 *
 * Allows access to the GetSimple control panel
 *
 * @package GetSimple
 * @subpackage Login
 */

# Setup inclusions
$load['plugin'] = true;
$load['login']  = true;

// wrap all include and header output in output buffering to prevent sending before headers.
ob_start();
	include('inc/common.php');
	exec_action('load-login');
	if(!getDef('GSALLOWLOGIN',true)) redirect($SITEURL);
	$pagetitle = i18n_r('LOGIN');
	get_template('header');
ob_end_flush();

?>

</div>
</div>
<div class="wrapper">
<?php include('template/error_checking.php'); ?>
<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main" >
			<h3><?php echo cl($SITENAME); ?></h3>
			<?php 
				exec_action('index-login'); //@hook index-login 
				exec_action('login-main'); //@hook index-login 
			?>
			<form class="login entersubmit" action="<?php '?'. htmlentities($_SERVER['QUERY_STRING'], ENT_QUOTES); ?>" method="post">
				<p><b><?php i18n('USERNAME'); ?>:</b><br /><input type="text" class="text" id="userid" name="userid" /></p>
				<p><b><?php i18n('PASSWORD'); ?>:</b><br /><input type="password" class="text" id="pwd" name="pwd" /></p>
				<?php 
					exec_action('login-extras'); // @hook login-extras
				?>
				<p><input type="submit" name="submitted" class="submit" value="<?php i18n('LOGIN'); ?>" /></p>
			</form>
			<p class="cta" ><a href="<?php echo $SITEURL; ?>"><?php i18n('BACK_TO_WEBSITE'); ?></a> &nbsp; <?php if(getDef('GSALLOWRESETTPASSWORD',true)!== false){ ?>| &nbsp; <a href="resetpassword.php"><?php i18n('FORGOT_PWD'); ?></a> <?php } ?></p>
			<div class="reqs" ><?php exec_action('login-reqs'); // @hook login-reqs ?></div>
		</div>
	</div>
</div>
<?php get_template('footer'); ?>
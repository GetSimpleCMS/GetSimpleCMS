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

$path = GSDATAOTHERPATH;
$bakpath = GSBACKUPSPATH.'other/';

# if the undo command was invoked
if (isset($_GET['undo'])) { 
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "undo", "support.php")) {
		die("CSRF detected!");	
	}
	$ufile = 'cp_settings.xml';
	undo($ufile, $path, $bakpath);
	redirect('support.php?rest=true');
}

if (isset($_GET['restored'])) { 
	$restored = 'true'; 
} else {
	$restored = 'false';
}

# were changes submitted?
if(isset($_POST['submitted'])) {
	$success = i18n_r('SETTINGS_UPDATED').'. <a href="support.php?undo&nonce='.get_nonce("restore", "support.php").'">'.i18n_r('UNDO').'</a>';
}

$php_modules = get_loaded_extensions();
if (in_arrayi('curl', $php_modules)){
	$curl_URL = $api_url .'?v='.$site_version_no;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $curl_URL);
	$data = curl_exec($ch);
	curl_close($ch);
	if ($data !== false) {
		$apikey = json_decode($data);
		$verstatus = $apikey->status;
	} else {
		$apikey = null;
		$verstatus = null;
	}
} else {
	$verstatus = '10';
}
$verstatus = '0';
if ($verstatus == '0') {
	$latest    = isset($apikey) ? $apikey->latest : '';  
	$ver = i18n_r('WARNING').': '.$site_full_name.' '.i18n_r('UPG_NEEDED').' <b>'.$latest .'</b> &ndash; <a href="http://get-simple.info/download/" target="_blank" >'. i18n_r('DOWNLOAD').'</a>';
} elseif ($verstatus == '1') {
	$ver = null;
} elseif ($verstatus == '2') {
	$ver = null;
} else {
	$ver = i18n_r('WARNING').': '.i18n_r('CANNOT_CHECK').': <b>'.$site_version_no.'</b> &ndash; <a href="http://get-simple.info/download" target="_blank" >'. i18n_r('DOWNLOAD').'</a>';
}
			
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
			<?php 
				if ($ver) {
					echo '<li style="color:#cc0000;" ><p>'.$ver.'</p></li>';
				} 
			?>
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
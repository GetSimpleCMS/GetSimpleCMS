<?php 
/**
 * Install
 *
 * Initial step of installation. Redirects to setup.php if everything checks out OK
 *
 * @package GetSimple
 * @subpackage Installation
 */

# setup inclusions
$load['plugin'] = true;
if(isset($_GET['lang'])) {$LANG = $_GET['lang'];}
include('inc/common.php');

# variable setup
$php_modules = get_loaded_extensions();

// attempt to fix permissions issues
$dirsArray = array(
	GSDATAPATH, 
	GSDATAOTHERPATH, 
	GSDATAOTHERPATH.'logs/', 
	GSDATAPAGESPATH, 
	GSDATAUPLOADPATH, 
	GSTHUMBNAILPATH, 
	GSBACKUPSPATH, 
	GSBACKUPSPATH.'other/', 
	GSBACKUPSPATH.'pages/',
	GSBACKUPSPATH.'zip/',
	GSBACKUSERSPATH,
	GSUSERSPATH
);

foreach ($dirsArray as $dir) {
	$tmpfile = GSADMININCPATH.'tmp/tmp-404.xml';
	
	if (file_exists($dir)) 
	{
		chmod($dir, 0755);
		$result_755 = copy($tmpfile, $dir .'tmp.tmp');
		
		if (!$result_755) 
		{
			chmod($dir, 0777);
			$result_777 = copy($tmpfile, $dir .'tmp.tmp');
			
			if (!$result_777) 
			{
				$kill = i18n_r('CHMOD_ERROR');
			}
		}
	} 
	else 
	{
		mkdir($dir, 0755);
		$result_755 = copy($tmpfile, $dir .'tmp.tmp');
		if (!$result_755) 
		{
			chmod($dir, 0777);
			$result_777 = copy($tmpfile, $dir .'tmp.tmp');
			
			if (!$result_777) 
			{
				$kill = i18n_r('CHMOD_ERROR');
			}
		}
	}
	
	if (file_exists($dir .'tmp.tmp')) 
	{
		unlink($dir .'tmp.tmp');
	}
}


// get available language files
$lang_handle = opendir(GSLANGPATH) or die("Unable to open ".GSLANGPATH);

if ($LANG == '') { $LANG = 'en_US'; }

while ($lfile = readdir($lang_handle)) 
{
	if( is_file(GSLANGPATH . $lfile) && $lfile != "." && $lfile != ".." ) 
	{
		$lang_array[] = basename($lfile, ".php");
	}
}

if (count($lang_array) != 0) 
{
	sort($lang_array);
	$count="0"; $sel = ''; $langs = '';
	
	foreach ($lang_array as $larray) 
	{
		if ($LANG == $larray) { $sel="selected";}
		
		$langs .= '<option '.$sel.' value="'.$larray.'" >'.$larray.'</option>';
		$sel = '';
		$count++;
	}
} 
else 
{
	$langs = '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>';
}

# salt value generation
$api_file = GSDATAOTHERPATH.'authorization.xml';

if (! file_exists($api_file)) {
	if (defined('GSUSECUSTOMSALT')) {
		$saltval = sha1(GSUSECUSTOMSALT);
	} else {
		$saltval = generate_salt();
	}
	$xml = new SimpleXMLExtended('<item></item>');
	$note = $xml->addChild('apikey');
	$note->addCData($saltval);
	XMLsave($xml, $api_file);
}

# get salt value
$data = getXML($api_file);
$APIKEY = $data->apikey;

?>

<?php get_template('header', $site_full_name.' &raquo; '. i18n_r('INSTALLATION') ); ?>
	
	<h1><?php echo $site_full_name; ?> <span>&raquo;</span> <?php i18n('INSTALLATION'); ?></h1>
</div>
</div>
<div class="wrapper">
	
<?php
	if ($kill != '') {
		echo '<div class="error">'. $kill .'</div>';
	}	
?>

	<div id="maincontent">
	<div class="main" >
	<h3><?php echo $site_full_name .' '. i18n_r('INSTALLATION'); ?></h3>

			<table class="highlight healthcheck">
			<?php
			if (in_arrayi('curl', $php_modules)) {
				$curl_URL = $api_url .'?k='.$APIKEY.'&v='.$site_version_no;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_TIMEOUT, 2);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_URL, $curl_URL);
				$data = curl_exec($ch);
				curl_close($ch);
				if ($data !== false)	{
					$apikey = json_decode($data);
					$verstatus = $apikey->status;
				}	else {
					$apikey = null;
					$verstatus = null;
				}
			} else {
				$verstatus = '10';
			}
			
			if ($verstatus == '0') {
				$ver = '<span class="ERRmsg" >'. i18n_r('UPG_NEEDED') .' <b>'.$apikey->latest .'</b><br /><a href="http://get-simple.info/download" target="_blank" >'. i18n_r('DOWNLOAD').'</a></span>';
			} elseif ($verstatus == '1') {
				$ver = '<span class="OKmsg" ><b>'.$site_version_no.'</b> - '. i18n_r('LATEST_VERSION').'</span>';
			} elseif ($verstatus == '2') {
				$ver = '<span class="WARNmsg" ><b>'.$site_version_no.'</b> - '. i18n_r('BETA').'</span>';
			} else {
				$ver = '<span class="WARNmsg" >'. i18n_r('CANNOT_CHECK') .' <b>'.$site_version_no.'</b><br /><a href="http://get-simple.info/download/" target="_blank" >'. i18n_r('DOWNLOAD').'</a></span>';
			}
			?>
			<tr><td style="width:445px;" ><?php echo $site_full_name; ?> <?php i18n_r('VERSION'); ?></td><td><?php echo $ver; ?></td></tr>
			<tr><td>
			<?php
				if (version_compare(PHP_VERSION, "5.2", "<")) {
					echo 'PHP '.i18n_r('VERSION') .'</td><td><span class="ERRmsg" ><b>'. PHP_VERSION.'</b> - PHP 5.2 '.i18n_r('OR_GREATER_REQ') .' - '.i18n_r('ERROR') .'</span></td></tr>';
				} else {
					echo 'PHP '.i18n_r('VERSION') .'</td><td><span class="OKmsg" ><b>'. PHP_VERSION.'</b> - '.i18n_r('OK') .'</span></td></tr>';
				}
				
				if ($kill == '') {
					echo '<tr><td>Folder Permissions</td><td><span class="OKmsg" >'.i18n_r('OK') .' - '.i18n_r('WRITABLE') .'</span></td></tr>';
				}	else {
					echo '<tr><td>Folder Permissions</td><td><span class="ERRmsg" >'.i18n_r('ERROR') .' - '.i18n_r('NOT_WRITABLE') .'</span></td></tr>';
				}
				
				if  (in_arrayi('curl', $php_modules) ) {
					echo '<tr><td>cURL Module</td><td><span class="OKmsg" >'.i18n_r('INSTALLED') .' - '.i18n_r('OK') .'</span></td></tr>';
				} else{
					echo '<tr><td>cURL Module</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED') .' - '.i18n_r('WARNING') .'</span></td></tr>';
				}
				
				if  (in_arrayi('gd', $php_modules) ) {
					echo '<tr><td>GD Library</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK') .'</span></td></tr>';
				} else{
					echo '<tr><td>GD Library</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING') .'</span></td></tr>';
				}
				
				if  (in_arrayi('zip', $php_modules) ) {
					echo '<tr><td>ZipArchive</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
				} else{
					echo '<tr><td>ZipArchive</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
				}

				if (! in_arrayi('SimpleXML', $php_modules) ) {
					echo '<tr><td>SimpleXML Module</td><td><span class="ERRmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('ERROR').'</span></td></tr>';
				} else {
					echo '<tr><td>SimpleXML Module</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
				}

				if ( function_exists('apache_get_modules') ) {
					if(! in_arrayi('mod_rewrite',apache_get_modules())) {
						echo '<tr><td>Apache Mod Rewrite</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
					} else {
						echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					}
				} else {
					echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
				}

			?>
			</table>
			<p class="hint"><?php echo sprintf(i18n_r('REQS_MORE_INFO'), "http://get-simple.info/wiki/installation:requirements"); ?></p>
			<?php if ($kill != '') { ?>
				<p><?php i18n('KILL_CANT_CONTINUE');?> <a href="./" ><?php i18n('REFRESH');?></a></p>
			<?php } else {?>
			<form action="setup.php" method="post" accept-charset="utf-8" >
				<div class="leftsec">
					<p>
					<label for="lang" ><?php i18n('SELECT_LANGUAGE');?>:</label>
					<select name="lang" id="lang" class="text" onchange="window.location='install.php?lang=' + this.value;">
						<?php echo $langs; ?>
					</select><br />
					<a href="http://get-simple.info/wiki/languages" target="_blank" ><?php i18n('DOWNLOAD_LANG');?></a>
					</p>
				</div>
				<div class="clear"></div>
				<p><input class="submit" type="submit" name="continue" value="<?php i18n('CONTINUE_SETUP');?> &raquo;" /></p>
			</form>
			
			<small class="hint"></small>
			<?php } ?>
	</div>
</div>

<div class="clear"></div>
<?php get_template('footer'); ?>
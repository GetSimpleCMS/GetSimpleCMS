<?php 
/**
 * Install
 *
 * Initial step of installation. Redirects to setup.php if everything checks out OK
 *
 * @package GetSimple
 * @subpackage Installation
 */

$php_modules = get_loaded_extensions();
if(!in_array('simplexml', array_map('strtolower', $php_modules)) ) die('PHP SimpleXML Module NOT INSTALLED');

$kill = '';

# setup inclusions
$load['plugin'] = true;
if(isset($_GET['lang'])) {$LANG = $_GET['lang'];}
include('inc/common.php');

# variable setup

// attempt to fix permissions issues
$dirsArray = array(
	GSDATAPATH, 
	GSCACHEPATH,
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
	GSUSERSPATH,
	GSDATAPAGESPATH.'autosave/'
);

foreach ($dirsArray as $dir) {
	$tmpfile = GSADMININCPATH.'tmp/tmp-404.xml';
	if (file_exists($dir)) {
		chmod($dir, 0755);
		$result_755 = copy($tmpfile, $dir .'tmp.tmp');
		
		if (!$result_755) {
			chmod($dir, 0777);
			$result_777 = copy($tmpfile, $dir .'tmp.tmp');
			
			if (!$result_777) {
				$kill = i18n_r('CHMOD_ERROR');
			}
		}
	} else {
		mkdir($dir, 0755);
		$result_755 = copy($tmpfile, $dir .'tmp.tmp');
		if (!$result_755) {
			chmod($dir, 0777);
			$result_777 = copy($tmpfile, $dir .'tmp.tmp');
			
			if (!$result_777) {
				$kill = i18n_r('CHMOD_ERROR');
			}
		}
	}
	
	if (file_exists($dir .'tmp.tmp')) {
		unlink($dir .'tmp.tmp');
	}
}


// get available language files
$filenames = getFiles(GSLANGPATH);

if ($LANG == '') { $LANG = 'en_US'; }

foreach ($filenames as $lfile) {
	if( is_file(GSLANGPATH . $lfile) && $lfile != "." && $lfile != ".." ) {
		$lang_array[] = basename($lfile, ".php");
	}
}

if (count($lang_array) == 1) {
	$langs = '<b>'.i18n_r('LANGUAGE').'</b>: &nbsp;<code style="border:1px solid #ccc;background:#f9f9f9;padding:2px;display:inline-block;">'.$lang_array[0].'</code> &nbsp;&nbsp;';
} elseif (count($lang_array) > 1) {
	sort($lang_array);
	$count="0"; $sel = ''; 
	$langs = '<label for="lang" >'.i18n_r('SELECT_LANGUAGE').':</label>';
	$langs .= '<select name="lang" id="lang" class="text" onchange="window.location=\'install.php?lang=\' + this.value;">';
	
	foreach ($lang_array as $larray) {
		if ($LANG == $larray) { $sel="selected";}
		$langs .= '<option '.$sel.' value="'.$larray.'" >'.$larray.'</option>';
		$sel = '';
		$count++;
	}
	$langs .= '</select><br />';
} else {
	$langs = '<b>'.i18n_r('LANGUAGE').'</b>: &nbsp;<code style="color:red;">'.i18n_r('NONE').'</code> &nbsp;&nbsp;';
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
	if(! XMLsave($xml, $api_file) ){
			$kill = i18n_r('CHMOD_ERROR');
	}
}

# get salt value
$data = getXML($api_file);
$APIKEY = $data->apikey;

if(empty($APIKEY)){
		$kill = i18n_r('CHMOD_ERROR');
}

get_template('header', $site_full_name.' &raquo; '. i18n_r('INSTALLATION') ); 

?>
	
	<h1><?php echo $site_full_name; ?></h1>
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
			
			# check to see if there is a core update needed
			$data = get_api_details();
			if ($data)	{
				$apikey = json_decode($data);
				$verstatus = $apikey->status;
			}	else {
				$verstatus = null;
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
			<tr><td style="width:380px;" ><?php echo $site_full_name; ?> <?php i18n_r('VERSION'); ?></td><td><?php echo $ver; ?></td></tr>
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

				if (server_is_apache()) {
					echo '<tr><td>Apache web server</td><td><span class="OKmsg" >'.$_SERVER['SERVER_SOFTWARE'].' - '.i18n_r('OK').'</span></td></tr>';
					if ( function_exists('apache_get_modules') ) {
						if(! in_arrayi('mod_rewrite',apache_get_modules())) {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').' - '.i18n_r('WARNING').'</span></td></tr>';
						} else {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
						}
					} else {
						echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.i18n_r('INSTALLED').' - '.i18n_r('OK').'</span></td></tr>';
					}
				} else {
					if (!defined('GSNOAPACHECHECK') || GSNOAPACHECHECK == false) {
						echo '<tr><td>Apache web server</td><td><span class="ERRmsg" >'.$_SERVER['SERVER_SOFTWARE'].' - <b>'.i18n_r('ERROR').'</b></span></td></tr>';
					}
				}

			?>
			</table>
			<p class="hint"><?php echo sprintf(i18n_r('REQS_MORE_INFO'), "http://get-simple.info/docs/requirements"); ?></p>
			<?php if ($kill != '') { ?>
				<p><?php i18n('KILL_CANT_CONTINUE');?> <a href="./" ><?php i18n('REFRESH');?></a></p>
			<?php } else {?>
			<form action="setup.php" method="post" accept-charset="utf-8" >
				<div class="leftsec">
					<p>			
						<?php echo $langs; ?><a href="http://get-simple.info/docs/languages" target="_blank" ><?php i18n('DOWNLOAD_LANG');?></a>
						<noscript><a href="install.php?lang=" id="refreshlanguage" ><?php i18n('REFRESH');?></a> &nbsp;|&nbsp;</noscript> 
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
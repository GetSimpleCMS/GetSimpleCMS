<?php 
/****************************************************
*
* @File: 		health-check.php
* @Package:	GetSimple
* @Action:	Displays the log file passed to it 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');
login_cookie_check();
	
// Variable settings
$ref = htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES);
$data = @getXML(GSDATAOTHERPATH.'authorization.xml');
$APIKEY = $data->apikey;
login_cookie_check();
$php_modules = get_loaded_extensions();
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['SUPPORT'].' &raquo; '.$i18n['WEB_HEALTH_CHECK']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['SUPPORT'];?> <span>&raquo;</span> <?php echo $i18n['WEB_HEALTH_CHECK'];?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
			<h3><?php echo $site_full_name; ?> <?php echo $i18n['VERSION'];?></h3>
			<table class="highlight healthcheck">
				<?php
				if (in_arrayi('curl', $php_modules))
				{
					$curl_URL = $api_url .'?k='.$APIKEY.'&v='.$site_version_no;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_TIMEOUT, 2);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_URL, $curl_URL);
					$data = curl_exec($ch);
					curl_close($ch);
					if ($data !== false)
					{
						$apikey = json_decode($data);
						$verstatus = $apikey->status;
					}
					else
					{
						$apikey = null;
						$verstatus = null;
					}
				} 
				else 
				{
					$verstatus = '10';
				}
				
				if ($verstatus == '0') 
				{
					$ver = '<span class="ERRmsg" >'. $i18n['UPG_NEEDED'].' <b>'.$apikey->latest .'</b><br /><a href="http://get-simple.info/download">'. $i18n['DOWNLOAD'].'</a></span>';
				} 
				elseif ($verstatus == '1') 
				{
					$ver = '<span class="OKmsg" ><b>'.$site_version_no.'</b> - '. $i18n['LATEST_VERSION'].'</span>';
				} 
				elseif ($verstatus == '2') 
				{
					$ver = '<span class="WARNmsg" ><b>'.$site_version_no.'</b> - Beta / Bleeding Edge</span>';
				} 
				else 
				{
					$ver = '<span class="WARNmsg" >'. $i18n['CANNOT_CHECK'].' <b>'.$site_version_no.'</b><br /><a href="http://get-simple.info/download">'. $i18n['DOWNLOAD'].'</a></span>';
				}
				?>
				<tr><td style="width:345px;" ><?php echo $site_full_name; ?> <?php echo $i18n['VERSION'];?></td><td><?php echo $ver; ?></td></tr>
			</table>
			
			<h3><?php echo $i18n['SERVER_SETUP'];?></h3>
			<table class="highlight healthcheck">
				<tr><td style="width:345px;" >
				<?php
					if (version_compare(phpversion(), "5.1.3", "<")) {
						echo 'PHP '.$i18n['VERSION'].'</td><td><span class="ERRmsg" ><b>'. phpversion().'</b> - PHP 5.1.3 '.$i18n['OR_GREATER_REQ'].' - '.$i18n['ERROR'].'</span></td></tr>';
					} else {
						echo 'PHP '.$i18n['VERSION'].'</td><td><span class="OKmsg" ><b>'. phpversion().'</b> - '.$i18n['OK'].'</span></td></tr>';
					}

					if  (in_arrayi('curl', $php_modules) ) {
						echo '<tr><td>cURL Module</td><td><span class="OKmsg" >'.$i18n['INSTALLED'].' - '.$i18n['OK'].'</span></td></tr>';
					} else{
						echo '<tr><td>cURL Module</td><td><span class="WARNmsg" >'.$i18n['NOT_INSTALLED'].' - '.$i18n['WARNING'].'</span></td></tr>';
					}
					if  (in_arrayi('gd', $php_modules) ) {
						echo '<tr><td>GD Library</td><td><span class="OKmsg" >'.$i18n['INSTALLED'].' - '.$i18n['OK'].'</span></td></tr>';
					} else{
						echo '<tr><td>GD Library</td><td><span class="WARNmsg" >'.$i18n['NOT_INSTALLED'].' - '.$i18n['WARNING'].'</span></td></tr>';
					}
					if (! in_arrayi('SimpleXML', $php_modules) ) {
						echo '<tr><td>SimpleXML Module</td><td><span class="ERRmsg" >'.$i18n['NOT_INSTALLED'].' - '.$i18n['ERROR'].'</span></td></tr>';
					} else {
						echo '<tr><td>SimpleXML Module</td><td><span class="OKmsg" >'.$i18n['INSTALLED'].' - '.$i18n['OK'].'</span></td></tr>';
					}
					
					if (! function_exists('date_default_timezone_set') ) {
						echo '<tr><td>Default Timezone Function</td><td><span class="WARNmsg" >'.$i18n['NOT_INSTALLED'].' - '.$i18n['WARNING'].'</span></td></tr>';
					} else {
						echo '<tr><td>Default Timezone Function</td><td><span class="OKmsg" >'.$i18n['INSTALLED'].' - '.$i18n['OK'].'</span></td></tr>';
					}

					if ( function_exists('apache_get_modules') ) {
						if(! in_arrayi('mod_rewrite',apache_get_modules())) {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="WARNmsg" >'.$i18n['NOT_INSTALLED'].' - '.$i18n['WARNING'].'</span></td></tr>';
						} else {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.$i18n['INSTALLED'].' - '.$i18n['OK'].'</span></td></tr>';
						}
					} else {
						echo '<tr><td>Apache Mod Rewrite</td><td><span class="OKmsg" >'.$i18n['INSTALLED'].' - '.$i18n['OK'].'</span></td></tr>';
					}

	?>
			</table>
			
			<h3><?php echo $i18n['DATA_FILE_CHECK'];?></h3>
			<table class="highlight healthcheck">
				<?php 
						$base_path = '../data/';
						$path = $base_path .'pages';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td style="width:345px;" >'. tsl($path) . $file .'</td><td>' . @valid_xml(tsl($path) . $file) .'</td></tr>';
							}							
						}

						$base_path = '../data/';
						$path = $base_path .'other';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>'. tsl($path) . $file .'</td><td>' . @valid_xml(tsl($path) . $file) .'</td></tr>';
							}							
						}

						$base_path = '../data/';
						$path = $base_path .'other/logs';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path, '.log') ) {
								echo '<tr><td>'. tsl($path) . $file .'</td><td>' . @valid_xml(tsl($path) . $file) .'</td></tr>';
							}							
						}

						$path = '../backups/other';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>'. tsl($path) . $file .'</td><td>' . @valid_xml(tsl($path) . $file) .'</td></tr>';
							}							
						}

						$path = '../backups/pages';
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								echo '<tr><td>'. tsl($path) . $file .'</td><td>' . @valid_xml(tsl($path) . $file) .'</td></tr>';
							}							
						}
				?>
			</table>
			
			<h3><?php echo $i18n['DIR_PERMISSIONS'];?></h3>
			<table class="highlight healthcheck">
				<tr><td style="width:345px;" >../data/pages/</td><td><?php if( check_perms("../data/pages/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../data/pages/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../data/pages/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../data/other/</td><td><?php if( check_perms("../data/other/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../data/other/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../data/other/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../data/other/logs/</td><td><?php if( check_perms("../data/other/logs/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../data/other/logs/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../data/other/logs/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../data/thumbs/</td><td><?php if( check_perms("../data/thumbs/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../data/thumbs/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../data/thumbs/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../data/uploads/</td><td><?php if( check_perms("../data/uploads/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../data/uploads/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../data/uploads/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../backups/zip/</td><td><?php if( check_perms("../backups/zip/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../backups/zip/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../backups/zip/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../backups/pages/</td><td><?php if( check_perms("../backups/pages/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../backups/pages/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../backups/pages/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
				<tr><td>../backups/other/</td><td><?php if( check_perms("../backups/other/") >= '0755' ) { echo '<span class="OKmsg" >'. check_perms("../backups/other/") .' '.$i18n['WRITABLE'].' - '.$i18n['OK'].'</span>'; } else { echo '<span class="ERRmsg" >'. check_perms("../backups/other/") .' '.$i18n['NOT_WRITABLE'].' - '.$i18n['ERROR'].'!</span>'; } ?></td></tr>
			</table>

			
			<h3><?php echo sprintf($i18n['EXISTANCE'], '.htaccess');?></h3>
			<table class="highlight healthcheck">
				<tr><td style="width:345px;" >../data/</td><td> 
				<?php	
					$file = "../data/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo '<span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo '<span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo '<span class="OKmsg" >'.$i18n['GOOD_D_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
			</td></tr>

				<tr><td>../data/uploads/</td><td>
				<?php	
					$file = "../data/uploads/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.allow.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Allow from all')) {
							echo ' <span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo ' <span class="OKmsg" >'.$i18n['GOOD_A_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>../data/thumbs/</td><td> 
				<?php	
					$file = "../data/thumbs/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.allow.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Allow from all')) {
							echo ' <span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo ' <span class="OKmsg" >'.$i18n['GOOD_A_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>../data/pages/</td><td>
				<?php	
					$file = "../data/pages/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo ' <span class="OKmsg" >'.$i18n['GOOD_D_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>../plugins/</td><td>
				<?php	
					$file = "../plugins/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo ' <span class="OKmsg" >'.$i18n['GOOD_D_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>../data/other/</td><td> 
				<?php	
					$file = "../data/other/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo ' <span class="OKmsg" >'.$i18n['GOOD_D_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
				</td></tr>

				<tr><td>../data/other/logs/</td><td>
				<?php	
					$file = "../data/other/logs/.htaccess";
					if (! file_exists($file)) {
						copy ('inc/tmp/tmp.deny.htaccess', $file);
					} 
					if (! file_exists($file)) {
						echo ' <span class="WARNmsg" >'.$i18n['MISSING_FILE'].' - '.$i18n['WARNING'].'</span>';
					} else {
						$res = @file_get_contents($file);
						if ( !strstr($res, 'Deny from all')) {
							echo ' <span class="WARNmsg" >'.$i18n['BAD_FILE'].' - '.$i18n['WARNING'].'</span>';
						} else {
							echo ' <span class="OKmsg" >'.$i18n['GOOD_D_FILE'].' - '.$i18n['OK'].'</span>';
						}
					}
				?>
				</td></tr>
				
				<tr><td>../theme/</td><td>
				<?php	
					$file = "../theme/.htaccess";
					if (file_exists($file)) {
						unlink($file);
					} 
					if (file_exists($file)) {
						echo ' <span class="ERRmsg" >'.$i18n['CANNOT_DEL_FILE'].' - '.$i18n['ERROR'].'</span>';
					} else {
						echo ' <span class="OKmsg" >'.$i18n['NO_FILE'].' - '.$i18n['OK'].'</span>';
					}
				?>
				</td></tr>
				<?php exec_action('healthcheck-extras'); ?>
			</table>
	</div>
		
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>	
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
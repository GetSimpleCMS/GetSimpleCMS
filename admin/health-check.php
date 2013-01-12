<?php 
/**
 * Health Check
 *
 * Displays the status and health check of your installation	
 *
 * @package GetSimple
 * @subpackage Support
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();
$php_modules = get_loaded_extensions();

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('SUPPORT').' &raquo; '.i18n_r('WEB_HEALTH_CHECK')); 

$errorCnt = 0;

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
			<h3><?php echo $site_full_name; ?> <?php i18n('VERSION');?></h3>
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
					// upgrade recomended
					$ver = '<span id="hc_version" class="label label-error" ><b>'.$site_version_no.'</b><br /> '. i18n_r('UPG_NEEDED').' (<b>'.$apikey->latest .'</b>)<br /><a href="http://get-simple.info/download/">'. i18n_r('DOWNLOAD').'</a></span>';
				} elseif ($verstatus == '1') {
					// latest version
					$ver = '<span id="hc_version" class="label label-ok" ><b>'.$site_version_no.'</b><br />'. i18n_r('LATEST_VERSION').'</span>';
				} elseif ($verstatus == '2') {
					// bleeding edge
					$ver = '<span id="hc_version" class="label label-info" ><b>'.$site_version_no.'</b><br /> '. i18n_r('BETA').'</span>';
				} else {
					// cannot check
					$ver = '<span id="hc_version" class="label label-warn" ><b>'.$site_version_no.'</b><br />'. i18n_r('CANNOT_CHECK').'<br /><a href="http://get-simple.info/download">'. i18n_r('DOWNLOAD').'</a></span>';
				}
				?>
				<tr><td class="hc_item" ><?php echo $site_full_name; ?> <?php i18n('VERSION');?></td><td><?php echo $ver; ?></td></tr>
			</table>
			
			<h3><?php i18n('SERVER_SETUP');?></h3>
			<table class="highlight healthcheck">
				<tr>
				<?php
					echo '<td class="hc_item">PHP '.i18n_r('VERSION').'</td>';
					if (version_compare(PHP_VERSION, "5.2", "<")) {
						echo '<td><span class="ERRmsg"><b>'. PHP_VERSION.'</b><br/>PHP 5.2 '.i18n_r('OR_GREATER_REQ').'</span></td><td><span class="label label-error">'.i18n_r('ERROR').'</span></td>';
						$errorCnt++;						
					} else {
						echo '<td><b>'. PHP_VERSION.'</b></td><td><span class="label label-ok">'.i18n_r('OK').'</span></td>';
					}
					echo '</tr>';

					$apacheModules = array(
						"curl|cURL Module|warn",
						"gd|GD Library|warn",
						"zip|ZipArchive|warn",
						"SimpleXML|SimpleXML Module|error",
					);
		
					foreach ($apacheModules as $module) {

						list($mId,$mTitle,$mAlert) = explode('|',$module);

						if  (in_arrayi($mId, $php_modules)) {
							echo '<tr><td class="hc_item">'.$mTitle.'</td><td>'.i18n_r('INSTALLED').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td></tr>';
						} 
						else if($mAlert == "warn"){
							echo '<tr><td class="hc_item">'.$mTitle.'</td><td><span class="WARNmsg">'.i18n_r('NOT_INSTALLED').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING').'</span></td></tr>';
						}
						else if($mAlert == "error"){	
							echo '<tr><td class="hc_item">'.$mTitle.'</td><td><span class="ERRmsg" >'.i18n_r('NOT_INSTALLED').'</span></td><td><span class="label label-error">'.i18n_r('ERROR').'</span></td></tr>';
							$errorCnt++;				
						}	
					}

					if (server_is_apache()) {
						echo '<tr><td>Apache web server</td><td>'.i18n_r('INSTALLED').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td></tr>';
						// check mod_rewrite
						$moderewritestatus = hasModRewrite();
						if ( hasModRewrite() === false ) {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING').'</span></td></tr>';
						}
						else if( hasModRewrite() === true ) {
							echo '<tr><td>Apache Mod Rewrite</td><td>'.i18n_r('INSTALLED').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td></tr>';
						}
						else {
							echo '<tr><td>Apache Mod Rewrite</td><td>'.i18n_r('NA').'</td><td><span class="label label-info">'.i18n_r('NA').'</span></td></tr>';
						}
					} else {
						if (!defined('GSNOAPACHECHECK') || GSNOAPACHECHECK == false) {
							echo '<tr><td>Apache web server</td><td><span class="ERRmsg" >'.i18n_r('NOT_INSTALLED').'</span></td><td><span class="label label-error">'.i18n_r('ERROR').'</span></td></tr>';
							$errorCnt++;											
						}
					}
	?>
			</table>
			<p class="hint">
				<?php 
				$serveris = get_Server_Software();
				if(empty($serveris)) $serveris = i18n_r('NA');
				echo sprintf(i18n_r('SERVER_IS'), $serveris)."<br/>";
				echo sprintf(i18n_r('REQS_MORE_INFO'), "http://get-simple.info/wiki/installation:requirements"); ?>
			</p>
			
			<h3><?php i18n('DATA_FILE_CHECK');?></h3>
			<table class="highlight healthcheck">
				<?php 
	
				// Data File Integrity Check

					$dirsArray = array(
						GSDATAPAGESPATH, 
						GSDATAOTHERPATH, 
						GSDATAOTHERPATH.'logs/', 
						GSBACKUSERSPATH,
						GSDATAUPLOADPATH, 
					);			

					foreach($dirsArray as $path){
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								$relpath = '/'.str_replace(GSROOTPATH,'',$path);
								echo '<tr><td class="hc_item" >'.$relpath . $file .'</td>';
								if(is_valid_xml($path . $file)){
									echo '<td>' . i18n_r('XML_VALID').'</td><td><span class="label label-ok">'.i18n_r('OK') .'</span></td>';
								}									
								else {
									echo '<td>' . i18n_r('XML_INVALID').'</td><td><span class="label label-ok">'.i18n_r('ERROR') .'</span></td>';
									$errorCnt++;													
								}	
								echo '</tr>';
							}							
						}
					}

				?>
			</table>
			
			<h3><?php i18n('DIR_PERMISSIONS');?></h3>
			<table class="highlight healthcheck">
			<?php
				// Directory Permissions

					$dirsArray = array(
						GSDATAOTHERPATH.'plugins.xml',
						GSDATAOTHERPATH.'authorization.xml',
						GSDATAPAGESPATH, 
						GSDATAOTHERPATH, 
						GSDATAOTHERPATH.'logs/', 
						GSDATAUPLOADPATH,
						GSTHUMBNAILPATH,
						GSUSERSPATH,
						GSCACHEPATH,
						GSBACKUPSPATH.'zip/',
						GSBACKUPSPATH.'pages/',
						GSBACKUPSPATH.'other/',
						GSBACKUSERSPATH
					);		

					foreach($dirsArray as $path){
						$relpath = '/'.str_replace(GSROOTPATH,'',$path);
						$isFile = substr($relpath, -4,1) == '.';
						$writeOctal = $isFile ? '644' : '744';

						if($isFile) $relpath = i18n_r('FILE_NAME').": $relpath";
						
						echo "<tr><td class=\"hc_item\">$relpath</td><td>";
						
						if($isFile and !file_exists($path)) {
							echo '<span class="ERRmsg">'.i18n_r('MISSING_FILE').'</span><td><span class="label label-error" >'.i18n_r('ERROR').'</span></td>'; 							
							$errorCnt++;				
							continue;
						}

						$me = check_perms($path);
						debugLog($relpath." " .ModeOctal2rwx($me));
						echo '('.ModeOctal2rwx($me) .") $me ";

						if( $me >= $writeOctal ) { 
							echo i18n_r('WRITABLE').'<td><span class="label label-ok" > '.i18n_r('OK').'</span></td>'; 
						} 
						else { 
							echo '<span class="ERRmsg">'.i18n_r('NOT_WRITABLE').'</span><td><span class="label label-error" >'.i18n_r('ERROR').'</span></td>'; 
							$errorCnt++;											
						} 
						echo '</td></tr>';
					}		
			?>
			</table>

			
			<h3><?php echo sprintf(i18n_r('EXISTANCE'), '.htaccess');?></h3>
			<table class="highlight healthcheck">

				<?php	

				// htaccess existance

					$dirsArray = array(
						GSDATAPATH, 
						GSDATAUPLOADPATH, 
						GSUSERSPATH, 
						GSCACHEPATH,
						GSTHUMBNAILPATH, 
						GSDATAPAGESPATH, 
						GSPLUGINPATH, 
						GSDATAOTHERPATH, 
						GSDATAOTHERPATH.'logs/', 
						GSTHEMESPATH
					);

					$aDirs = array(
						GSDATAUPLOADPATH,
						GSTHUMBNAILPATH
					);

					$noFile = array(
						GSTHEMESPATH
					);

					foreach($dirsArray as $path){
						$relpath = '/'.str_replace(GSROOTPATH,'',$path);
						echo "<tr><td class=\"hc_item\" >$relpath</td>";
						
						$file = $path.".htaccess";
					
						if (!file_exists($file)) {

							// no file is all good
							if(in_array($path, $noFile)){
								echo '<td>'.i18n_r('NO_FILE').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td>';
								continue;
							}	
							
							// file is missing !
							echo '<td><span class="WARNmsg" >'.i18n_r('MISSING_FILE').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING').'</span></td>';
						} 
						else {			

							// no file preffered but we found one
							if(in_array($path, $noFile)){
								echo '<td>.htaccess</td><td><span class="label label-info">'.i18n_r('OK').'</span></td>';
								continue;
							}	

							$res = file_get_contents($file);
							
							if(in_array($path, $aDirs)){
								// file is allow file
								$AD = "Allow from all";
								$ADtran = 'GOOD_A_FILE';
							}	
							else {
								// file is deny file
								$AD = "Deny from all";
								$ADtran = 'GOOD_D_FILE';								
							}
								
							if ( !strstr($res, $AD) ) {
								// file is wrong!
								echo '<td><span class="WARNmsg">'.i18n_r('BAD_FILE').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING').'</span></td>';
							} else {
								// file is just right
								echo '<td>'.i18n_r($ADtran).'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td>';
							}

						}	
						echo "</tr>";						
					}	

					?>
			</table>
			<?php exec_action('healthcheck-extras'); ?>
	</div>
		
	</div>
	
	<div id="sidebar" >
		<?php 
		include('template/sidebar-support.php'); 
		if($errorCnt > 0){
			echo '<div id="hc_alert">'.i18n_r('STATUS').': <span class="label label-error">'.i18n_r('ERROR').'</span></div>';
		}
		?>
	</div>	

</div>
<?php get_template('footer'); ?>
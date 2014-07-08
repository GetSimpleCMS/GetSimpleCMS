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

$errorCnt = 0; // error counter, for error catch and flag

include('template/include-nav.php'); 

echo '<div class="bodycontent clearfix">	
	<div id="maincontent">
		<div class="main">';

			///////////////////////////////////////////////
			// Server Setup
			///////////////////////////////////////////////

			echo '<h3>' . $site_full_name .'</h3>
			<table class="highlight healthcheck">';
				
				# check to see if there is a core update needed
				$verdata   = getVerCheck();
				$verstatus = $verdata->status;
				$verstatus = $_GET['status']; // debugging
				$verstring = sprintf(i18n_r('CURR_VERSION'),'<b>'.$site_version_no.'</b>').'<hr>';
				if ($verstatus == '0') {
					// upgrade recomended
					$ver = '<div id="hc_version" class="label label-error" >'.$verstring. i18n_r('UPG_NEEDED').' (<b>'.$verdata->latest .'</b>)<br /><a href="'.$site_link_back_url.'download/">'. i18n_r('DOWNLOAD').'</a></div>';
				} elseif ($verstatus == '1') {
					// latest version
					$ver = '<div id="hc_version" class="label label-ok" >'.$verstring. i18n_r('LATEST_VERSION').'</div>';
				} elseif ($verstatus == '2') {
					// bleeding edge
					$ver = '<div id="hc_version" class="label '.(isAlpha() ? 'label-info' : 'label-info' ).'" >'.$verstring. (isAlpha() ? i18n_r('ALPHA') : i18n_r('BETA')) .'</div>';
				} else {
					// cannot check
					$ver = '<div id="hc_version" class="label label-warn" >'.$verstring. i18n_r('CANNOT_CHECK').'<br /><a href="'.$site_link_back_url.'download">'. i18n_r('CHECK_MANUALLY').'</a></div>';
				}
				?>
				<tr><td class="hc_item" ><?php echo $site_full_name; ?> <?php i18n('VERSION');?></td><td><?php echo $ver; ?></td></tr>
                <?php 
                if(getDef('GSADMIN') && GSADMIN!='admin') echo '<tr><td>GSADMIN</td><td><span class="hint">'.GSADMIN.'</span></td></tr>';
                
                if(getDef('GSLOGINSALT') && GSLOGINSALT!='') echo '<tr><td>GSLOGINSALT</td><td><span class="hint">'. i18n_r('YES').'</span></td></tr>';
                else echo '<tr><td>GSLOGINSALT</td><td><span class="hint">'. i18n_r('NO').'</span></td></tr>'; 
                
                if(getDef('GSUSECUSTOMSALT') && GSUSECUSTOMSALT!='') echo '<tr><td>GSUSECUSTOMSALT</td><td><span class="hint">'. i18n_r('YES').'</span></td></tr>';
				else echo '<tr><td>GSUSECUSTOMSALT</td><td><span class="hint">'. i18n_r('NO').'</span></td></tr>';                 
                ?>
			</table>
			
			<?php

			///////////////////////////////////////////////
			// Server Setup
			///////////////////////////////////////////////
			
			echo '<h3>'. i18n_r('SERVER_SETUP') .'</h3>
			<table class="highlight healthcheck">
				<tr>';

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
					if (!function_exists('chmod') ) {
						echo '<tr><td>chmod</td><td>'.i18n_r('NOT_INSTALLED').'</td><td><span class="label label-warn">'.i18n_r('ERROR').'</span></td></tr>';
					} else {
						echo '<tr><td>chmod</td><td>'.i18n_r('INSTALLED').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td></tr>';
					}

					if (server_is_apache()) {
						echo '<tr><td>Apache web server*</td><td>'.i18n_r('INSTALLED').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td></tr>';
						// check mod_rewrite
						$moderewritestatus = hasModRewrite();
						if ( $moderewritestatus === false ) {
							echo '<tr><td>Apache Mod Rewrite</td><td><span class="WARNmsg" >'.i18n_r('NOT_INSTALLED').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING').'</span></td></tr>';
						}
						else if( $moderewritestatus === true ) {
							echo '<tr><td>Apache Mod Rewrite</td><td>'.i18n_r('INSTALLED').'</td><td><span class="label label-ok">'.i18n_r('OK').'</span></td></tr>';
						}
						else {
							echo '<tr><td>Apache Mod Rewrite</td><td>'.i18n_r('NA').'</td><td><span class="label label-info">'.i18n_r('NA').'</span></td></tr>';
						}
					} else {
						if (!getDef('GSNOAPACHECHECK',true) || GSNOAPACHECHECK == false) {
							echo '<tr><td>Apache web server*</td><td><span class="ERRmsg" >'.i18n_r('NOT_INSTALLED').'</span></td><td><span class="label label-error">'.i18n_r('ERROR').'</span></td></tr>';
							$errorCnt++;											
						}
					}

				$disabled_funcs = ini_get('disable_functions');
                if(!empty($disabled_funcs)) echo '<tr><td colspan=2>PHP disable_functions<span class="hint"> ' . $disabled_funcs . '</span></td></tr>';
	?>
			</table>
			<p class="hint">
				<?php 
				$serveris = get_Server_Software();
				if(empty($serveris)) $serveris = i18n_r('NA');
				echo "*".sprintf(i18n_r('SERVER_IS'), $serveris)."<br/>";
				echo sprintf(i18n_r('REQS_MORE_INFO'), $site_link_back_url . "docs/requirements"); ?>
			</p>
			
			<?php

			///////////////////////////////////////////////
			// Data File Integrity Check
			///////////////////////////////////////////////

			echo '<h3>'. i18n_r('DATA_FILE_CHECK') .'</h3>
			<table class="highlight healthcheck">';

					$dirsArray = array(
						GSDATAOTHERPATH, 
						GSDATAOTHERPATH.'logs/', 
						GSBACKUSERSPATH
					);			

					$filesArray = array(
						GSDATAOTHERPATH."authorization.xml",
						GSDATAOTHERPATH."website.xml",
						GSDATAOTHERPATH."pages.xml",
						GSDATAOTHERPATH."components.xml",
						GSDATAOTHERPATH."plugins.xml"
					);

					foreach($dirsArray as $path){
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								$relpath = '/'.getRelPath($path);
								echo '<tr><td class="hc_item" >'.$relpath . $file .'</td>';
								if(is_valid_xml($path . $file)){
									echo '<td>' . i18n_r('XML_VALID').'</td><td><span class="label label-ok">'.i18n_r('OK') .'</span></td>';
								}									
								else {
									if(in_array($path.$file,$filesArray)) echo '<td><span class="ERRmsg">' . i18n_r('XML_INVALID').'</span></td><td><span class="label label-error">'.i18n_r('ERROR') .'</span></td>';
									else echo '<td><span class="WARNmsg">' . i18n_r('XML_INVALID').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING') .'</span></td>';
									$errorCnt++;													
								}	
								echo '</tr>';
							}							
						}
					}
			
			echo '</table>';

			echo '<h3>'. i18n_r('PAGE_FILE_CHECK') .'</h3>
			<table class="highlight healthcheck">';

						$path = GSDATAPAGESPATH;
						$data = getFiles($path);
						sort($data);
						foreach($data as $file) {
							if( isFile($file, $path) ) {
								$relpath = '/'.getRelPath($path);
								echo '<tr><td class="hc_item" >'.$relpath . $file .'</td>';
								if(is_valid_xml($path . $file)){
									echo '<td>' . i18n_r('XML_VALID').'</td><td><span class="label label-ok">'.i18n_r('OK') .'</span></td>';
								}									
								else {
									echo '<td><span class="WARNmsg">' . i18n_r('XML_INVALID').'</span></td><td><span class="label label-warn">'.i18n_r('WARNING') .'</span></td>';
									$errorCnt++;													
								}	
								echo '</tr>';
							}							
						}
			
			echo '</table>';
			
			///////////////////////////////////////////////
			// Directory Permissions
			///////////////////////////////////////////////

			echo '<h3>'. i18n_r('DIR_PERMISSIONS') .'</h3>
			<table class="highlight healthcheck">';
			
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
						GSBACKUPSPATH.getRelPath(GSDATAPAGESPATH,GSDATAPATH), // backups/pages/
						GSBACKUPSPATH.getRelPath(GSDATAOTHERPATH,GSDATAPATH), // backups/other/
						GSBACKUSERSPATH
					);		

					if (getDef('GSCHMOD')) {
						$writeOctal = GSCHMOD; 
					} else {
						$writeOctal = 0755;
					}

					foreach($dirsArray as $path){
						$relpath = '/'.getRelPath($path);
						$isFile = substr($relpath, -4,1) == '.';
						if(!$isFile) $writeOctal = 0744;

						if($isFile) $relpath = i18n_r('FILE_NAME').": $relpath";
						
						echo "<tr><td class=\"hc_item\">$relpath</td><td>";
						
						if($isFile and !file_exists($path)) {
							echo '<a name="error"></a><span class="ERRmsg">'.i18n_r('MISSING_FILE').'</span><td><span class="label label-error" >'.i18n_r('ERROR').'</span></td>'; 							
							$errorCnt++;				
							continue;
						}

						$me = check_perms($path);
						echo '('.ModeOctal2rwx($me) .") $me ";
						if( $me >= decoct($writeOctal) ) { 
							echo i18n_r('WRITABLE').'<td><span class="label label-ok" > '.i18n_r('OK').'</span></td>'; 
						} 
						else { 
							echo '<a name="error"></a><span class="ERRmsg">'.i18n_r('NOT_WRITABLE').'</span><td><span class="label label-error" >'.i18n_r('ERROR').'</span></td>'; 
							$errorCnt++;											
						} 
						echo '</td></tr>';
					}		
			
			echo '</table>';

			///////////////////////////////////////////////
			// htaccess existance
			///////////////////////////////////////////////
			if (server_is_apache()) { 
				echo '<h3>'. sprintf(i18n_r('EXISTANCE'), '.htaccess') .'</h3>';
				echo '<table class="highlight healthcheck">';

					$dirsArray = array(
						GSROOTPATH,
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

					$required = array(
						GSROOTPATH
					);						

					foreach($dirsArray as $path){
						$relpath = '/'.getRelPath($path);
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
							else if(in_array($path, $required)){
								// file is allow file
								$AD = "RewriteBase";
								$ADtran = 'GOOD_FILE';
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

				echo '</table>';
			}

			// call healthcheck-extras hook
			exec_action('healthcheck-extras');
			?>			
	</div>
		
	</div>
	
	<div id="sidebar" >
		<?php 
		include('template/sidebar-support.php'); 
		if($errorCnt > 0){
			echo '<div id="hc_alert">'.i18n_r('STATUS').': <a href="#error"><span class="label label-error">'.i18n_r('ERROR').'</span></a></div>';
		}
		?>
	</div>	

</div>

<?php get_template('footer'); ?>
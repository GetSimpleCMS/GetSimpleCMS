<?php
/****************************************************
*
* @File: 	footer.php
* @Package:	GetSimple
* @Action:	Template file for inserting the 
*			footer into the control panel. 	
*
*****************************************************/

global $i18n;

?>
		<div id="footer">
      	<?php 
      		include(GSADMININCPATH ."configuration.php");
      		if (cookie_check()) { 
      			echo '<p><a href="pages.php">'.$i18n['PAGE_MANAGEMENT'].'</a> &nbsp;&bull;&nbsp; <a href="upload.php">'.$i18n['FILE_MANAGEMENT'].'</a> &nbsp;&bull;&nbsp; <a href="theme.php">'.$i18n['THEME_MANAGEMENT'].'</a> &nbsp;&bull;&nbsp; <a href="backups.php">'.$i18n['BAK_MANAGEMENT'].'</a> &nbsp;&bull;&nbsp; <a href="plugins.php">'.$i18n['PLUGINS_MANAGEMENT'].'</a> &nbsp;&bull;&nbsp; <a href="settings.php">'.$i18n['GENERAL_SETTINGS'].'</a> &nbsp;&bull;&nbsp; <a href="support.php">'.$i18n['SUPPORT'].'</a></p>';
      		}
      		$site_credit_link = stripslashes(' &nbsp;&bull;&nbsp; <a href="'.$site_link_back_url.'">'.$i18n['POWERED_BY'].' '.$site_full_name.'</a> '.$i18n['VERSION'].' '. $site_version_no);
					echo '<p>'. sprintf($i18n['PRODUCTION'], '<a href="http://www.cagintranetworks.com/">Cagintranet Networks</a>') . $site_credit_link .'</p>';  
      	?>
      	
      	<?php exec_action('footer'); ?>

		</div>
	</div><!-- end .wrapper -->
</body>
</html>
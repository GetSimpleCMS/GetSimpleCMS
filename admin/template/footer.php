<?php
/**
 * Footer Admin Template
 *
 * @package GetSimple
 */

#global $i18n;

?>
		<div id="footer">
      	<?php 
      		include(GSADMININCPATH ."configuration.php");
      		if (cookie_check()) { 
      			echo '<p><a href="pages.php">'.i18n_r('PAGE_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="upload.php">'.i18n_r('FILE_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="theme.php">'.i18n_r('THEME_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="backups.php">'.i18n_r('BAK_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="plugins.php">'.i18n_r('PLUGINS_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="settings.php">'.i18n_r('GENERAL_SETTINGS').'</a> &nbsp;&bull;&nbsp; <a href="support.php">'.i18n_r('SUPPORT').'</a></p>';
      		}
      		$site_credit_link = stripslashes('<a href="'.$site_link_back_url.'">'.i18n_r('POWERED_BY').' '.$site_full_name.'</a> '.i18n_r('VERSION').' '. $site_version_no);
					echo '<p>'. $site_credit_link .'</p>';  
      	?>
      	
      	<?php exec_action('footer'); ?>

		</div>
	</div><!-- end .wrapper -->
</body>
</html>
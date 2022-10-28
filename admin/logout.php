<?php
/**
 * Logout
 *
 * Logs the user out of the GetSimple control panel
 *
 * @package GetSimple
 * @subpackage Login
 */

# Setup inclusions
$load['plugin'] = true;

ob_start();

	include('inc/common.php');

	# end it all :'(
	kill_cookie($cookie_name);
	exec_action('logout'); // @hook logout logging out after cookie destroyed

	// if debugging install , wipe website.xml to trigger reinstall
	if(getDef('GSDEBUGINSTALL',true) && getDef('GSDEBUGINSTALLWIPE',true)) delete_file(GSDATAOTHERPATH.GSWEBSITEFILE);

	# send back to login screen
	redirect('index.php?logout');
	
ob_end_flush();

?>
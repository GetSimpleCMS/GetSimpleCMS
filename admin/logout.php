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
include('inc/common.php');

# end it all :'(
kill_cookie($cookie_name);
kill_cookie('GS_ADMIN_USERNAME');
exec_action('logout');

# send back to login screen
redirect('index.php?success='.i18n_r('MSG_LOGGEDOUT'));
?>
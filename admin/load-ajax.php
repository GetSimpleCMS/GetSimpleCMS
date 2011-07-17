<?php
/**
 * Load Plugin Function via AJAX
 *
 * Loads the plugin function pass to it
 *
 * @package GetSimple
 * @subpackage Plugins
 */

# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

# call the requested function
$plugin_id = $_GET['id'];
global $plugin_info;

if (function_exists($_GET['func'])) {
	call_user_func_array($_GET['func'],array());
} else {
	return false;
}
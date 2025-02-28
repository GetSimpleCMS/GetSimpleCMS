<?php 
/**
 * Safemode Loader
 * enabled safemode automatically, if authenticated
 *
 * @package GetSimple
 * @subpackage Basic-Functions
 */

// Include common.php
$load['plugin'] = false;
include('inc/common.php');
if(!cookie_check()) redirect('index.php?redirect=safemode.php');
enableSafeMode();
gotoDefaultPage();

/* ?> */
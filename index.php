<?php
/**
 * Index
 *
 * Front-End public index
 *
 * @package GetSimple
 * @subpackage FrontEnd
 */

if(!defined('GSBASE'))          define('GSBASE',true);
if(!defined('GSADMINDEFAULT'))  define('GSADMINDEFAULT','admin');
if(!defined('GSCOMMON'))        define('GSCOMMON','/inc/common.php');
if(!defined('GSCONFIGFILE'))    define('GSCONFIGFILE','gsconfig.php');
if(!defined('GSSTYLEWIDE' ))    define('GSSTYLEWIDE','wide');
if(!defined('GSSTYLE_SBFIXED')) define('GSSTYLE_SBFIXED','sbfixed');
if(!defined('GSFRONT'))         define('GSFRONT',1);
if(!defined('GSBACK'))          define('GSBACK',2);
if(!defined('GSBOTH'))          define('GSBOTH',3); 

//load config and determine custom GSADMIN path
if (file_exists(GSCONFIGFILE)) require_once(GSCONFIGFILE);
$GSADMIN = defined('GSADMIN') ? GSADMIN : GSADMINDEFAULT;

// $load['template'] = false;
// $load['plugins'] = false;

# Include common.php
include($GSADMIN.GSCOMMON);

?>
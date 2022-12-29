<?php
if(!isset($_SESSION)){session_start();}
if(!isset($_SESSION['cat']) || is_null($_SESSION['cat'])) $_SESSION['cat'] = null;

// get correct id for plugin
$thisfile = basename(__FILE__, '.php');
// paths & file constants definitions
include($thisfile.'/lib/inc/_def.php');

register_plugin(
	$thisfile,
	'ItemManager',
	'2.3.5',
	'Juri Ehret',
	'http://ehret-studio.com',
	'A simple flat-file framework for GetSimple-CMS',
	'imanager',
	'im_render_backend'
);

// activate actions
add_action('admin-pre-header', 'ajaxGetLists');
add_action('nav-tab', 'createNavTab', array($thisfile, $thisfile, 'Manager'));
//add_action($thisfile.'-sidebar', 'im_render_backend', array('sidebar'));
/* i18n search stuff, not currently in use
add_action('search-index', 'i18nSearchImIndex');
add_filter('search-item', 'i18nSearchImItem');
add_filter('search-display', 'i18nSearchImDisplay'); */
/* include your own CSS for beautiful manager style */
register_style('jqui', IM_SITE_URL.'plugins/'.$thisfile.'/upload/js/jquery-ui/jquery-ui.css',  GSVERSION, 'screen');
register_style('imstyle', IM_SITE_URL.'plugins/'.$thisfile.'/css/im-styles.css', GSVERSION, 'screen');
register_style('blueimp',  IM_SITE_URL.'plugins/'.$thisfile.'/css/blueimp-gallery.min.css', GSVERSION, 'screen');
register_style('imstylefonts', IM_SITE_URL.'plugins/'.$thisfile.'/css/fonts/font-awesome/css/font-awesome.min.css', GSVERSION, 'screen');
queue_style('jqui', GSBACK);
queue_style('imstyle', GSBACK);
queue_style('imstylefonts', GSBOTH);
queue_style('blueimp', GSBACK);

// model
include(GSPLUGINPATH.'imanager/lib/Model.php');
// manager
include(GSPLUGINPATH.'imanager/lib/ItemManager.php');

/**
 * Core ItemManager's function, we use it to create an ItemManager instance
 *
 * @param string $name
 *
 * @return Im\ItemManager instance
 */
function imanager($name='')
{
	global $im;
	if($im === null) $im = new ItemManager();
	return !empty($name) ? $im->$name : $im;
}

/**
 * Loads ItemManager's backend, executed inside admin panel only
 */
function im_render_backend($arg=null)
{
	global $im;
	if(is_null($arg))
	{
		// check whether the user inside admin panel
		(!defined('IN_GS') && empty($_GET['id']) && $_GET['id'] != IM_NAME) or define('IS_ADMIN_PANEL', true);
		if($im === null) $im = imanager();
		if(defined('IS_ADMIN_PANEL'))
		{
			(!$im->config->injectActions) or $im->setActions();
			if($im->config->hiddeAdmin) {
				echo $im->config->adminDisabledMsg;
			} else
			{
				$im->admin->init();
				echo $im->admin->display();
			}
		}
	} else
	{
		if(defined('IS_ADMIN_PANEL')) echo $im->admin->display($arg);
	}
}

function ajaxGetLists()
{
	global $im;
	if(isset($_GET['getcatlist']) || isset($_GET['getitemlist']))
	{
		(!defined('IN_GS') && empty($_GET['id']) && $_GET['id'] != IM_NAME) or define('IS_ADMIN_PANEL', true);
		if($im === null) $im = imanager();
		if(defined('IS_ADMIN_PANEL'))
		{
			(!$im->config->injectActions) or $im->setActions();
			$im->admin->init();
			echo $im->admin->display();
			exit();
		}

	}
}

/**
 * Deprecated ItemManager's call, just for backward compatibility
 */
class IManager extends ItemManager {

	public function __construct() {
		parent::__construct();
	}
}
?>

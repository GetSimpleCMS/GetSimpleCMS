<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * Common variables used by the GetSimple News Manager Plugin.
 */

# path definitions
define('NMPOSTPATH', GSDATAPATH  . 'posts/');
define('NMBACKUPPATH', GSBACKUPSPATH  . 'posts/');
define('NMDATAPATH', GSDATAOTHERPATH  . 'news_manager/');
define('NMINCPATH', GSPLUGINPATH . 'news_manager/inc/');
define('NMLANGPATH', GSPLUGINPATH . 'news_manager/lang/');
define('NMTEMPLATEPATH', GSPLUGINPATH . 'news_manager/template/');

# file definitions
define('NMSETTINGS', NMDATAPATH . 'settings.xml');
define('NMPOSTCACHE', NMDATAPATH . 'posts.xml');

# URL parameters
if (!defined('NMPARAMPOST'))    define('NMPARAMPOST', 'post');
if (!defined('NMPARAMPAGE'))    define('NMPARAMPAGE', 'page');
if (!defined('NMPARAMARCHIVE')) define('NMPARAMARCHIVE', 'archive');
if (!defined('NMPARAMTAG'))     define('NMPARAMTAG', 'tag');
if (!defined('NMFIRSTPAGE'))    define('NMFIRSTPAGE', 1);

# includes
require_once(NMINCPATH . 'functions.php');
require_once(NMINCPATH . 'settings.php');
require_once(NMINCPATH . 'cache.php');
require_once(NMINCPATH . 'admin.php');
require_once(NMINCPATH . 'posts.php');
require_once(NMINCPATH . 'site.php');
require_once(NMINCPATH . 'sidebar.php');


# load settings
$data = @getXML(NMSETTINGS);
$NMPAGEURL       = isset($data->page_url) ? $data->page_url : ''; // default: no slug selected
$NMPRETTYURLS    = isset($data->pretty_urls) ? $data->pretty_urls : '';
$NMLANG          = isset($data->language) ? $data->language : 'en_US';
$NMSHOWEXCERPT   = isset($data->show_excerpt) ? $data->show_excerpt : '';
$NMEXCERPTLENGTH = isset($data->excerpt_length) ? $data->excerpt_length : '350';
$NMPOSTSPERPAGE  = isset($data->posts_per_page) ? $data->posts_per_page : '8';
$NMRECENTPOSTS   = isset($data->recent_posts) ? $data->recent_posts : '5';
# new settings (since 3.0)
$NMSETTING = array();
$NMSETTING['archivesby'] = isset($data->archivesby) ? $data->archivesby : 'm';
$NMSETTING['readmore'] = isset($data->readmore) ? $data->readmore : 'R';
$NMSETTING['titlelink'] = isset($data->titlelink) ? $data->titlelink : 'Y';
$NMSETTING['gobacklink'] = isset($data->gobacklink) ? $data->gobacklink : 'B';
$NMSETTING['images'] = isset($data->images) ? $data->images : 'N';
$NMSETTING['imagewidth'] = isset($data->imagewidth) ? $data->imagewidth : '';
$NMSETTING['imageheight'] = isset($data->imageheight) ? $data->imageheight : '';
$NMSETTING['imagecrop'] = isset($data->imagecrop) ? $data->imagecrop : '';
$NMSETTING['imagealt'] = isset($data->imagealt) ? $data->imagealt : '';
$NMSETTING['imagelink'] = isset($data->imagelink) ? $data->imagelink : '';
$NMSETTING['enablecustomsettings'] = isset($data->enablecustomsettings) ? $data->enablecustomsettings : '';
$NMSETTING['customsettings'] = isset($data->customsettings) ? $data->customsettings : '';

# other globals
$NMPARENTURL = '?'; // to be assigned elsewhere


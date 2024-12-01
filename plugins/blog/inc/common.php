<?php
$rootPath =  realpath('../../');

if(!function_exists('getXML'))
{
	require_once($rootPath.'/admin/inc/common.php');
	
}

if(!isset($thisfile)) { $plugin_file = GSPLUGINPATH.'blog.php'; } else { $plugin_file =  $thisfile; }
register_script('codemirror_js', $SITEURL.'plugins/blog/js/codemirror/lib/codemirror.js', '1.0', FALSE);

register_script('codemirror_javascript', $SITEURL.'plugins/blog/js/codemirror/mode/javascript/javascript.js', '1.0', FALSE);
register_script('codemirror_php', $SITEURL.'plugins/blog/js/codemirror/mode/php/php.js', '1.0',  FALSE);
register_script('codemirror_css_hl', $SITEURL.'plugins/blog/js/codemirror/mode/css/css.js', '1.0',  FALSE);
register_script('codemirror_clike', $SITEURL.'plugins/blog/js/codemirror/mode/clike/clike.js', '1.0',  FALSE);
register_script('codemirror_xml_hl', $SITEURL.'plugins/blog/js/codemirror/mode/xml/xml.js', '1.0',  FALSE);

register_style('codemirror_css', $SITEURL.'plugins/blog/js/codemirror/lib/codemirror.css', GSVERSION, 'screen');

queue_script('codemirror_js',GSBACK); 
queue_script('codemirror_javascript',GSBACK); 
queue_script('codemirror_php',GSBACK); 
queue_script('codemirror_css',GSBACK); 
queue_script('codemirror_clike',GSBACK); 
queue_script('codemirror_xml_hl',GSBACK); 
queue_script('codemirror_css_hl',GSBACK); 

queue_style('codemirror_css',GSBACK); 

define('BLOGFILE', $plugin_file);
define('BLOGSETTINGS', GSDATAOTHERPATH  . 'blog_settings.xml');
define('BLOGCATEGORYFILE', GSDATAOTHERPATH  . 'blog_categories.xml');
define('BLOGRSSFILE', GSDATAOTHERPATH  . 'blog_rss.xml');
define('BLOGPLUGINFOLDER', GSPLUGINPATH.'blog/');
define('BLOGPOSTSFOLDER', GSDATAPATH.'blog/');
define('BLOGCACHEFILE', GSDATAOTHERPATH  . 'blog_cache.xml');
define('BLOGCUSTOMFIELDS', GSDATAOTHERPATH  . 'blog_custom_fields.xml');
define('BLOGCUSTOMFIELDSFILE', 'blog_custom_fields.xml');

function formatPostDate($date)
{
	$Blog = new Blog;
	return $Blog->get_locale_date(strtotime($date), '%b %e, %Y');
}

?>
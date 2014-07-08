<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Theme Functions
 *
 * These functions are used within the front-end of a GetSimple installation
 *
 * @link http://get-simple.info/docs/theme-codex/
 *
 * @package GetSimple
 * @subpackage Theme-Functions
 */

/**
 * Get Page Content
 *
 * @since 1.0
 * @uses $content 
 * @uses exec_action
 * @uses exec_filter
 * @uses strip_decode
 *
 * @return string Echos.
 */
function get_page_content() {
	exec_action('content-top');
	$content = strip_decode(getPageGlobal('content'));
	$content = exec_filter('content',$content);
	echo $content;
	exec_action('content-bottom');
}

/**
 * Get Page Excerpt
 *
 * @since 2.0
 * @uses $content
 * @uses exec_filter
 * @uses strip_decode
 *
 * @param string $n Optional, default is 200.
 * @param bool $striphtml Optional, default false, true will strip html from $content
 * @param string $ellipsis Optional, Default '...', specify an ellipsis
 * @return string Echos.
 */
function get_page_excerpt($len=200, $striphtml=true, $ellipsis = '...') {
	GLOBAL $content;
	if ($len<1) return '';
	$content_e = strip_decode($content);
	$content_e = exec_filter('content',$content_e);
	echo getExcerpt($content_e, $len, $striphtml, $ellipsis);
	}


/**
 * Get Page Meta Keywords
 *
 * @since 2.0
 * @uses $metak
 * @uses strip_decode
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_meta_keywords($echo=true) {
	$str = encode_quotes(strip_decode(getPageGlobal('metak')));
	$str = exec_filter('metak',$str);	
	return echoReturn($str,$echo);
}

/**
 * Get Page Meta Description
 *
 * @since 2.0
 * @uses $metad
 * @uses strip_decode
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_meta_desc($echo=true) {
	$metad = getPageGlobal('metad');
	$description = '';

	if ($metad != '') {
		$description = encode_quotes(strip_decode($metad));
	}
	else if(getDef('GSAUTOMETAD',true))
	{
		// get meta from content excerpt
		if (function_exists('mb_substr')) { 
			$description = trim(mb_substr(strip_tags(strip_decode($content)), 0, 160));
		} else {
			$description = trim(substr(strip_tags(strip_decode($content)), 0, 160));
		}

		$description = str_replace('"','', $description);
		$description = str_replace("'",'', $description);
		$description = preg_replace('/\n/', " ", $description);
		$description = preg_replace('/\r/', " ", $description);
		$description = preg_replace('/\t/', " ", $description);
		$description = preg_replace('/ +/', " ", $description);
	}
	
	$str = exec_filter('metad',$description);

	return echoReturn($str,$echo);	
}

/**
 * Get Page Meta Robots
 *
 * @since 3.4
 * @uses $metarNoIndex, $metarNoFollow, $metarNoArchive
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string returns comma serperated list of robots
 */
function get_page_meta_robots($echo=true) {
	$arr = array();
	$arr[] = getPageGlobal('metarNoIndex') == 1 ? 'noindex' : 'index';
	$arr[] = getPageGlobal('metarNoFollow') == 1 ? 'nofollow' : 'follow';
	$arr[] = getPageGlobal('metarNoArchive') == 1 ? 'noarchive' : 'archive';

	$str = implode(',',$arr);
	$str = exec_filter('metar',$str);

	return echoReturn($str,$echo);		
}

/**
 * Get Page Meta Title for <title>
 *
 * @since 3.4
 * @uses $title
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_head_title($echo=true){
	$str = strip_tags(strip_decode(getPageGlobal('title')));
	return echoReturn(exec_filter('headtitle',$str),$echo);		
}

/**
 * Get Page Title
 *
 * @since 1.0
 * @uses $title
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_title($echo=true) {
	$str = strip_decode(getPageGlobal('title'));
	return echoReturn(exec_filter('pagetitle',$str),$echo);	
}

/**
 * Get Page Clean Title
 *
 * This will remove all HTML from the title before returning
 *
 * @since 1.0
 * @uses get_page_title()
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_clean_title($echo=true) {
	$str = strip_tags(get_page_title(false));
	return echoReturn($str,$echo);	
}

/**
 * Get Page Slug
 *
 * This will return the slug value of a particular page
 *
 * @since 1.0
 * @uses $url
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_slug($echo=true) {
	return echoReturn(exec_filter('pageslug',getPageGlobal('url')),$echo);
}

/**
 * Get Page Parent Slug
 *
 * This will return the slug value of a particular page's parent
 *
 * @since 1.0
 * @uses $parent
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_parent($echo=true) {
	return echoReturn(getPageGlobal('parent'),$echo);
}

/**
 * Get Page Date
 *
 * This will return the page's updated date/timestamp
 *
 * @since 1.0
 * @uses $date
 * @uses $TIMEZONE
 *
 * @param string $i Optional, default is "l, F jS, Y - g:i A"
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_date($i = "l, F jS, Y - g:i A", $echo=true) {
	global $TIMEZONE;
	if ($TIMEZONE != '') {
		if (function_exists('date_default_timezone_set')) {
			date_default_timezone_set($TIMEZONE);
		}
	}
	
	$str = formatDate($i, strtotime(getPageGlobal('date')));
	return echoReturn($str,$echo);	
}

/**
 * Get Page Full URL
 *
 * This will return the full url
 *
 * @since 1.0
 * @uses $parent
 * @uses $url
 * @uses $SITEURL
 * @uses $PRETTYURLS
 * @uses find_url
 *
 * @param bool $echo Optional, default is false. True will 'return' value 
 * @todo $echo is backwards!
 * @return string Echos or returns based on param $echo
 */
function get_page_url($echo=false) {
	return echoReturn(find_url(getPageGlobal('url'), getPageGlobal('parent')),!$echo);
}

/**
 * Get Page Header HTML
 *
 * This will return header html for a particular page. This will include the 
 * meta desriptions & keywords, canonical and title tags
 *
 * @since 1.0
 * @uses exec_action
 * @uses get_page_url
 * @uses strip_quotes
 * @uses get_page_meta_desc
 * @uses get_page_meta_keywords
 * @uses $metad
 * @uses $title
 * @uses $content
 * @uses $site_full_name from configuration.php
 * @uses GSADMININCPATH
 *
 * @return string HTML for template header
 */
function get_header($full=true) {
	include(GSADMININCPATH.'configuration.php');
	
	// meta description	
	$description = get_page_meta_desc(false);
	if(!empty($description)) echo '<meta name="description" content="'.$description.'" />'."\n";
	
	// meta robots
	$metarobots = get_page_meta_robots(false);
	if(!empty($metarobots)) echo '<meta name="robots" content="'.$metarobots.'" />'."\n";

	// meta keywords
	$keywords = get_page_meta_keywords(false);
	if (!empty($keywords)) echo '<meta name="keywords" content="'.$keywords.'" />'."\n";
	
	// canonical link
	$canonical =  exec_filter('linkcanonical',get_page_url(true));
	if ($full and !empty($canonical)) {
		echo '<link rel="canonical" href="'.$canonical.'" />'."\n";
	}

	// script queue
	get_scripts_frontend();
	
	exec_action('theme-header');
}

/**
 * Get Page Footer HTML
 *
 * This will return footer html for a particular page. Right now
 * this function only executes a plugin hook so developers can hook into
 * the bottom of a site's template.
 *
 * @since 2.0
 * @uses exec_action
 *
 * @return string HTML for template header
 */
function get_footer() {
	get_scripts_frontend(TRUE);
	exec_action('theme-footer');
}

/**
 * Get Site URL
 *
 * This will return the site's full base URL
 * This is the value set in the control panel
 *
 * @since 1.0
 * @uses $SITEURL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_url($echo=true) {
	return echoReturn(getPageGlobal('SITEURL'),$echo);
}

/**
 * Get Theme URL
 *
 * This will return the current active theme's full URL 
 *
 * @since 1.0
 * @uses $SITEURL
 * @uses $TEMPLATE
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_theme_url($echo=true) {
	global $SITEURL, $TEMPLATE;
	$str = trim($SITEURL . getRelPath(GSTHEMESPATH) . $TEMPLATE);
	return echoReturn($str,$echo);	
}

/**
 * Get Site's Name
 *
 * This will return the value set in the control panel
 *
 * @since 1.0
 * @uses $SITENAME
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_name($echo=true) {
	global $SITENAME;
	return echoReturn(cl($SITENAME),$echo);
}

/**
 * Get Administrator's Email Address
 * 
 * This will return the value set in the control panel
 * 
 * @deprecated as of 3.0
 *
 * @since 1.0
 * @uses $EMAIL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_email($echo=true) {
	global $EMAIL;
	$str = trim(stripslashes($EMAIL));
	return echoReturn($str,$echo);	
}


/**
 * Get Site Credits
 *
 * This will return HTML that displays 'Powered by GetSimple X.XX'
 * It will always be nice if developers left this in their templates 
 * to help promote GetSimple. 
 *
 * @since 1.0
 * @uses $site_link_back_url from configuration.php
 * @uses $site_full_name from configuration.php
 * @uses GSVERSION
 * @uses GSADMININCPATH
 *
 * @param string $text Optional, default is 'Powered by'
 * @return string 
 */
function get_site_credits($text ='Powered by ') {
	include(GSADMININCPATH.'configuration.php');
	
	$site_credit_link = '<a href="'.$site_link_back_url.'" target="_blank" >'.$text.' '.$site_full_name.'</a>';
	echo stripslashes($site_credit_link);
}

/**
 * Menu Data
 *
 * This will return data to be used in custom navigation functions
 *
 * @since 2.0
 * @uses GSDATAPAGESPATH
 * @uses find_url
 * @uses getXML
 * @uses subval_sort
 *
 * @param bool $xml Optional, default is false. 
 *				True will return value in XML format. False will return an array
 * @return array|string Type 'string' in this case will be XML 
 */
function menu_data($id = null,$xml=false) {
    global $pagesArray; 
    
    $menu_extract = '';
    $pagesSorted = subval_sort($pagesArray,'menuOrder');

    if (count($pagesSorted) != 0) { 
		$count = 0;
		if (!$xml){
			foreach ($pagesSorted as $page) {
				$text       = (string)$page['menu'];
				$pri        = (string)$page['menuOrder'];
				$parent     = (string)$page['parent'];
				$title      = (string)$page['title'];
				$slug       = (string)$page['url'];
				$menuStatus = (string)$page['menuStatus'];
				$private    = (string)$page['private'];
				$pubDate    = (string)$page['pubDate'];
			  
				$url = find_url($slug,$parent);
				$specific = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);

				if ($id == $slug) { 
					return $specific; 
					exit; 
				} else {
					$menu_extract[] = $specific;
				}
			} 

			return $menu_extract;
		} 
		else {

			$xml = '<?xml version="1.0" encoding="UTF-8"?><channel>';    
			foreach ($pagesSorted as $page) {
				$text       = $page['menu'];
				$pri        = $page['menuOrder'];
				$parent     = $page['parent'];
				$title      = $page['title'];
				$slug       = $page['url'];
				$pubDate    = $page['pubDate'];
				$menuStatus = $page['menuStatus'];
				$private    = $page['private'];
					
				$url = find_url($slug,$parent);

				$xml.="<item>";
				$xml.="<slug><![CDATA[".$slug."]]></slug>";
				$xml.="<pubDate><![CDATA[".$pubDate."]]></pubDate>";
				$xml.="<url><![CDATA[".$url."]]></url>";
				$xml.="<parent><![CDATA[".$parent."]]></parent>";
				$xml.="<title><![CDATA[".$title."]]></title>";
				$xml.="<menuOrder><![CDATA[".$pri."]]></menuOrder>";
				$xml.="<menu><![CDATA[".$text."]]></menu>";
				$xml.="<menuStatus><![CDATA[".$menuStatus."]]></menuStatus>";
				$xml.="<private><![CDATA[".$private."]]></private>";
				$xml.="</item>";
			}

		$xml.="</channel>";
		return $xml;
		}
	}
}

/**
 * get the components xml data
 * returns an array of xmlobjs
 *
 * @since 3.4
 * 
 * @uses components
 * @uses GSDATAOTHERPATH
 * @uses getXML
 * @param  boolean $xml [description]
 * @return components data items xmlobj
 *
 */
function get_components_xml(){
    global $components;
    if (!$components) {
        if (file_exists(GSDATAOTHERPATH.'components.xml')) {
        	$data = getXML(GSDATAOTHERPATH.'components.xml');
            $components = $data->item;
        } else {
            $components = array();
        }
    }
    return $components;
}

/**
 * get xml for an individual component
 * returns an array since duplicates are possible on component slugs
 *
 * @since 3.4.0
 *
 * @param  str $id component id
 * @return array of simpleXmlObj matching slug
 */
function get_component_xml($id){
	if(!$id) return;
	return get_components_xml()->xpath("//slug[.='".$id."']/..");	
}

/**
 * Get Component
 *
 * This will output the component requested. 
 * Components are parsed for PHP within them.
 * Will only return the first component matching $id
 *
 * @since 1.0
 *
 * @param string $id This is the ID of the component you want to display
 * @param bool $force Force return of inactive components
 * @param bool $raw do not process php
 */
function get_component($id, $force = false, $raw = false) {
	$components = get_components_xml();
	$component  = get_component_xml($id); // this returns an array due to no distinct slug enforcement
	if(!$component) return;

	$enabled = !(bool)($component[0]->disabled == 'true' || $component[0]->disabled == '1');
	if(!$enabled && !$force) return;

	if(!$raw) eval("?>" . strip_decode($component[0]->value) . "<?php ");
	else echo strip_decode($component[0]->value);
}

/**
 * See if a component exists
 * @since 3.4.0
 * @param  str $id component id
 * @return bool
 */
function component_exists($id){
	return !get_component_xml($id);
}

/**
 * Return Component
 * Returns a components output
 * 
 * @since 3.4.0
 * @return component buffered output
 */
function return_component(){
	ob_start();
	$args = func_get_args();
	call_user_func_array('get_component',$args);
	return ob_get_clean();
}

/**
 * Get Main Navigation
 *
 * This will return unordered list of main navigation
 * This function uses the menu opitions listed within the 'Edit Page' control panel screen
 *
 * @since 1.0
 * @uses GSDATAOTHERPATH
 * @uses getXML
 * @uses subval_sort
 * @uses find_url
 * @uses strip_quotes 
 * @uses exec_filter 
 *
 * @param string $currentpage This is the ID of the current page the visitor is on
 * @param string $classPrefix Prefix that gets added to the parent and slug classnames
 * @return string 
 */	
function get_navigation($currentpage,$classPrefix = "") {

	$menu = '';

	global $pagesArray;
	
	$pagesSorted = subval_sort($pagesArray,'menuOrder');
	if (count($pagesSorted) != 0) { 
		foreach ($pagesSorted as $page) {
			$sel = $classes = '';
			$url_nav = $page['url'];
			
			if ($page['menuStatus'] == 'Y') { 
				$parentClass = !empty($page['parent']) ? $classPrefix.$page['parent'] . " " : "";
				$classes     = trim( $parentClass.$classPrefix.$url_nav);

				if ("$currentpage" == "$url_nav") $classes .= " current active";
				if ($page['menu']  == '') { $page['menu']  = $page['title']; }
				if ($page['title'] == '') { $page['title'] = $page['menu']; }

				$menu .= '<li class="'. $classes .'"><a href="'. find_url($page['url'],$page['parent']) . '" title="'. encode_quotes(cl($page['title'])) .'">'.strip_decode($page['menu']).'</a></li>'."\n";
			}
		}
	}
	
	echo exec_filter('menuitems',$menu);
}

/**
 * Check if a user is logged in
 * 
 * This will return true if user is logged in
 *
 * @since 3.2
 * @uses get_cookie();
 * @uses $USR
 *
 * @return bool
 */	
function is_logged_in(){
	global $USR;
	if (isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME')) {
		return true;
	}
}	
	
/**
 * aliases
 * @deprecated as of 2.03
 * WHY?
 */	
function return_page_title() {
	return get_page_title(FALSE);
}
function return_parent() {
	return get_parent(FALSE);
}
function return_page_slug() {
  return get_page_slug(FALSE);
}
function return_site_ver() {
	return get_site_version(FALSE);
}	

/* ?> */

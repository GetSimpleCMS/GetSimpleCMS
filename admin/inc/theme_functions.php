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
	exec_action('content-top'); // @hook content-top before get content
	$content = strip_decode(getPageGlobal('content'));
	$content = exec_filter('content',$content); // @filter content (str) filter page content
	echo $content;
	exec_action('content-bottom');  // @hook content-bottom after get content
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
	$content_e = exec_filter('content',$content_e); // @filter content (str) filter page content in get_page_excerpt
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
	$str = exec_filter('metak',$str); // @filter metak (str) filter the meta keywords
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
	
	$str = exec_filter('metad',$description); // @filter metad (str) meta description in get_page_meta_desc

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
	$str = exec_filter('metar',$str); // @filter metar (str) meta robots in get_page_meta_robots

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
	$str = exec_filter('headtitle',$str); // @fitler headtitle (str) head title in get_page_head_title
	return echoReturn($str,$echo);
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
	$str = exec_filter('pagetitle',$str); // @fitler pagetitle (str) page title in get_page_title	
	return echoReturn($str,$echo);	
}

/**
 * Get Page Title
 *
 * @since 3.4
 * @uses $titlelong
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_title_long($echo=true) {
	$str = strip_decode(getPageGlobal('titlelong'));
	$str = exec_filter('pagetitlelong',$str); // @filter pagetitlelong (str) page title long in get_page_title_long
	return echoReturn($str,$echo);	
}

/**
 * Get Page Summary
 *
 * @since 3.4
 * @uses $summary
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_page_summary($echo=true) {
	$str = strip_decode(getPageGlobal('summary'));
	$str = exec_filter('pagesummary',$str); // @filter pagesummary (str) page summary in get_page_summary
	return echoReturn($str,$echo);	
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
	$str = exec_filter('pageslug',getPageGlobal('url')); // @filter pageslug (str) page slug in get_pagee_slug
 	return echoReturn($str,$echo);
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
	if ($full) {
		$canonical = exec_filter('linkcanonical',get_page_url(true)); // @filter linkcanonical (str) rel canonical link
		if(!empty($canonical)) echo '<link rel="canonical" href="'.$canonical.'" />'."\n";
	}

	// script queue
	get_scripts_frontend();
	
	exec_action('theme-header');  // @hook theme-header after get_header output html
}

/**
 * Get Page Footer for themes
 * executes theme-footer hook
 * Also gets all frontend scripts set to footer
 *
 * @since 2.0
 * @uses exec_action
 *
 * @return string HTML for template header
 */
function get_footer() {
	get_scripts_frontend(true);
	exec_action('theme-footer');  // @hook theme-footer after get_footer html output
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
 * @global $SITEEMAIL
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_email($echo=true) {
	global $SITEEMAIL;
	$str = trim(stripslashes($SITEEMAIL));
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
 * Get Component
 *
 * This will output the component requested. 
 * Components are parsed for PHP within them.
 * Will only return the first component matching $id
 *
 * @since 1.0
 *
 * @param string $id This is the ID of the component you want to display
 * @param bool $force Force return of disabled components
 * @param bool $raw do not process php
 */
function get_component($id, $force = false, $raw = false) {
	output_collection_item($id, get_components_xml(), $force, $raw);
}

/**
 * See if a component exists
 * @since 3.4
 * @param  str $id component id
 * @param  bool disabled include disabled snippets 
 * @return bool
 */
function component_exists($id, $disabled = false){
	if(!$disabled) return componentIsEnabled($id);
	return (bool)get_component_xml($id);
}

/**
 * See if a component is enabled
 * @since 3.4
 * @param  str $id component id
 * @return bool
 */
function component_enabled($id){
	return componentIsEnabled($id);
}

/**
 * Return Component
 * Returns a components output
 *
 * @since 3.4
 * @return component buffered output
 */
function return_component(){
	$args = func_get_args();	
	return catchOutput('get_component',$args);
}

/**
 * Get Snippet
 *
 * This will output the snippet requested. 
 * Will only return the first snippet matching $id
 *
 * @since 3.4
 *
 * @param string $id This is the ID of the snippet you want to display
 * @param bool $force Force return of inactive snippets
 * @param bool $raw do not process php
 */
function get_snippet($id, $force = false) {
	output_collection_item($id, get_snippets_xml(), $force, true);
}

/**
 * See if a snippet exists
 * @since 3.4
 * @param  str $id snippet id
 * @param  bool disabled include disabled snippets
 * @return bool
 */
function snippet_exists($id, $disabled = false){
	if(!$disabled) return snippetIsEnabled($id);
	return (bool)get_snippet_xml($id);
}

/**
 * See if a snippet is enabled
 * @since 3.4
 * @param  str $id snippet id
 * @return bool
 */
function snippet_enabled($id){
	return snippetIsEnabled($id);
}

/**
 * Return snippet
 * Returns a snippets output
 *
 * @since 3.4
 * @return snippet buffered output
 */
function return_snippet(){
	$args = func_get_args();
	return catchOutput('get_snippet',$args);
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
	
	echo exec_filter('menuitems',$menu); // @filter menuitems (str) menu items html in get_navigation
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
	return get_page_title(false);
}
function return_parent() {
	return get_parent(false);
}
function return_page_slug() {
  return get_page_slug(false);
}

/* ?> */

<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Page Functions
 *
 * Page getters, etc
 * 
 * @since  3.4
 * @author shawn_a
 * @todo  create wiki docs
 * @link http://get-simple.info/docs/pages
 *
 * @package GetSimple
 * @subpackage Page-Functions
 */


/**
 * get PAGES
 * optionally PAGES collection , by filtering with provided filterfunction
 *
 * @since  3.4
 * @param  callable $filterFunc function name for filter callout
 * @param  mixed ... variable number of arguments to pass to filterfunc
 * @return array  new pagesarray
 */
function getPages($filterFunc=null/*,...*/){
	GLOBAL $pagesArray;

	if(isset($filterFunc) && function_exists($filterFunc)){
		$args    = func_get_args();
		$args[0] = $pagesArray; // replace first argument (filterfunc) with PAGES
		return call_user_func_array($filterFunc, $args); // @todo why not call filterPageFunc() ?
	} else return $pagesArray;
}

/**
 * get a page
 * 
 * @since  3.4
 * @param  string $slug slug of page to return
 * @return array       page array
 */
function getPage($slug){
	global $pagesArray;
	return isset($pagesArray[$slug]) ? $pagesArray[$slug] : null;
}

/**
 * get all values of a single field from PAGES, uses array_column
 * uses PAGES if a PAGE collection is not passed
 * 
 * @since  3.4
 * @uses  array_column, backported
 * @uses  getPages
 * @param  string $field key of fields to return
 * @param  optional PAGES collection
 * @return array      new array of fields
 */
function getPagesFields($field,$pages = array()){
	if(!$pages) $pages = getPages(); // use global PAGES if not provided
	return array_column($pages,$field,'url');
}

/**
 * get page field value
 * @since  3.4
 * @param  str $pageId pageid
 * @param  str $field  fieldid
 * @return mixed field value
 * @param $raw   if true, prevent any processing of data, Caution, datatypes will vary if fallbacks to file occur
 * @param $cache if false, bypass cache and get directly from page file 
 */
function getPageFieldValue($pageId,$field,$raw = false, $cache = true){
	$page = getPage((string)$pageId);
	if ($cache && isset($page,$page[$field])){
		if(!$raw) return strip_decode($page[$field]);
		return $page[$field];
	} else {
		return getPageFieldFromFile($pageId,$field,$raw);
	}
}

/**
 * get page field directly from page file, bypasses page cache
 * @since  3.4
 * @param  str  $page  page id
 * @param  str  $field field id
 * @param  boolean $raw  return raw xml
 * @return returns field data from page xml
 */
function getPageFieldFromFile($page, $field, $raw = false){   
	$data = getPageXML($page);
	if(!$data) return;

	$data = $data->$field;
	if($raw) return $data; // return without any processing
	$data = stripslashes(htmlspecialchars_decode($data, ENT_QUOTES));
	return $data;
}

/**
 * get a content field from a page
 * supports custom content field keys
 * @param  str  $pageId   slug of page
 * @param  boolean $filtered perform content filter if true
 * @param  string  $field    content field key
 * @return str            content string
 */
function getPageContentField($pageId, $filtered = true, $field = 'content'){
	$content = getPageFieldValue($pageId,$field);
	if($filtered) return filterPageContent($content,$field);
	return $content;
}

/**
 * helper for filtering content
 * supports custom content field keys
 * @param  str $content content data
 * @param  str $field   content field key
 * @return str          result of content filtering
 */
function filterPageContent($content,$field = 'content'){
	$hook = $field; // can modify for filter hook prefix postfix conventions enforcement eg. "content_blargh"
	$content = exec_filter($hook,$content); // @filter content (str) filter page content in returnPageContent
	return $content;
}

/**
 * page is in menu
 * @since  3.4
 * @param  str $slug   page id
 * @param  sgtr $menuid menuid to check
 * @return bool         true if in menu specified
 */
function pageIsInMenu($slug,$menuid = null){
	if(!$menuid) $menuid = GSMENUIDCOREMENU;
	return null !== menuItemGetData($slug,$menuid);
}

/**
 * get a pages collection with these slugs, optionally sort pages to match order
 * @since  3.4
 * @param  array $keys array of keys to keep
 * @param  bool $sort sort filtered pages array by keys order if true
 * @return array       pages array with only the matching pages
 */
function getPagesMulti($keys,$sort = false){
	return filterPagesSlugs(getPages(),$keys,$sort);
}

/**
 * shortcut to get pages menu title
 * if menu title not explicitly set fallback to page title
 * @since  3.4
 * @param  str $slug page id
 * @return str page title
 */
function getPageMenuTitle($slug){
    $page = getPage($slug);
    return (trim($page['menu']) == '' ? $page['title'] : $page['menu']);
}


/**
 * PARENT GETTERS
 */

/**
 * get PAGE parent slug
 * gets parent info from coremenu
 * @param  str $pageId slug of PAGE to get parent of
 * @return str         parent of this page
 */
function getParent($pageId){
	return getParentByCoreMenu($pageId);
}

/**
 * get PAGE parent PAGE
 * alias for $pagesArray[$pagesArray['slug']['parent']]
 * @param  str $pageId slug of PAGE to get path for
 * @return str         parent PAGE object
 */
function getParentPage($pageId){
	$parentId = getParent($pageId);
	return getPage($parentId);
}

/**
 * get PAGE parents slugs
 * returns an array of all this pages parents slugs
 * @param  str $pageId slug of child
 * @return array       array of parents slugs
 */
function getParents($pageId){
	return getParentsByCoreMenu($pageId);
}

/**
 * get PAGE parents fields
 * returns an 1D array of a pages parents field values
 * @param  str $pageId slug of child
 * @param  str $key    key of field to return from parents
 * @param  str $filterfunc optional function
 * @return array       array of parents fields
 */
function getParentFields($pageId,$key = 'url',$filterFunc = null){
	$resArray = array();
	$parents = getParents($pageId);
	if(!$parents) return;
	foreach($parents as $parent){
		$value = ($key == 'url') ? $parent : getPageFieldValue($parent,$key); // optimize if we are asking for parent slugs, we already have them
		if(callIfCallable($filterFunc,$parent,$key) !== true) $resArray[] = $value;
	}

	return $resArray;
}

/**
 * get all page parent pages
 * returns an array of all this pages parents page-arrays
 * @param  str $pageId slug of child
 * @return array       PAGES collection of parents
 */
function getParentsPages($pageId){
	$parents = getParents($pageId);
	if(!$parents) return array();
	return getPagesMulti($parents,true);
}


/**
 * Get Page Parents - returns multi fields from parents
 *
 * Return an Array of pages that are parents of the requested page/slug with optional fields.
 * @since 3.4
 * @param $page - slug of the page to retrieve content
 * @param options - array of optional fields to return
 * @return Array of slug names and optional fields. 
 * 
 */
function getParentsMulti($page,$keys=array()){
	$pages = getParentsPages($page);
	$pages = filterKeysMatch($pages,$keys);
	return $pages;
}

/**
 * CHILDREN GETTERS
 */

/**
 * Get Page Children
 *
 * Return an Array of pages that are children of the requested page/slug
 *
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @return Array of slug names 
 * 
 */
function getChildren($page){
	return getChildrenByCoreMenu($page);
}

/**
 * Get Page Children - returns multi fields
 *
 * Return an Array of pages that are children of the requested page/slug with optional fields.
 * as of 3.4 array is keyed to match page keys
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param options - array of optional fields to return
 * @return Array of slug names and optional fields. 
 * 
 */
function getChildrenMulti($page,$options=array()){
	$pages = getChildrenPages($page);
	$pages = filterKeysMatch($pages,$options);
	return $pages;
}


/**
 * get page children pages, direct decendants only
 * returns an array of all this pages children page-arrays
 * @param  str $pageId slug of parent
 * @return array       PAGES collection of children
 */
function getChildrenPages($pageId){
	$children = getChildren($pageId);
	if(!$children) return array();
	return getPagesMulti($children,true);
}


/**
 * PATH GETTERS
 */

/**
 * get PAGE path
 * @param  str $pageId slug of PAGE to get path to
 * @return str         path/to/pageId
 */
function getPagePath($pageId){
	$path = menuItemGetField($pageid,'path');
	return $path;
}

/**
 * get page path fields
 * gets the field values for all parents in path and implodes them by delim
 * parent field - parent field - page field
 * eg. getPagePathField($parent,'title'),' - ')
 * output: parent 1 title - parent 2 title
 * @param  str $pageId slug of page
 * @param  str $field  field name
 * @param  str $delim  delimiter for implode
 * @return str         concatenated string of parent fields
 */
function getPagePathField($pageId,$field,$delim = '/'){
	$parents = getParentFields($pageId,$field);
	if($parents) return implode('/',array_reverse($parents)) . $delim . getPageFieldValue($pageId,$field);
	return $pageId;
}

/**
 * WRAPPERS MENU @IMPORT
 */

/**
 * wrapper for getting page parents via core menu
 * @param  str $pageId page id
 * @return str         parent id
 */
function getParentByCoreMenu($pageId){
	return menuItemGetParent($pageId,GSMENUIDCORE);
}

/**
 * wrapper for getting page parents via core menu
 * @param  str $pageId page id
 * @return str         parent id
 */
function getParentsByCoreMenu($pageId){
	return menuItemGetParents($pageId,GSMENUIDCORE);
}

/**
 * wrapper for getting page children via core menu
 * @param  str $pageId page id
 * @return str         parent id
 */
function getChildrenByCoreMenu($pageId){
	return menuItemGetChildren($pageId,GSMENUIDCORE);
}


/**
 * LEGACY
 */

/**
 * Return Page Field
 *
 * Retrieve the requested field from the given page cache, fallback to page file
 * If the field is "content" and not raw it will run it through content filter
 * @deprecated 3.4 should not return fields with different processing
 * @since 3.1
 * @param $page  slug of the page to retrieve content
 * @param $field the Field to display
 * @param $raw   if true, prevent any processing of data, use with caution as result can vary if falls back to page file
 * @param $cache if false, bypass cache and get directly from page file
 * 
 */
function returnPageField($page, $field, $raw = false, $cache = true){   
	if($field == 'content') return getPageContentField($page,!$raw);
	return getPageFieldValue($page,$field,$raw,$cache);
}

/**
 * Get Page Field
 *
 * Retrieve and display the requested field from the given page. 
 * @deprecated 3.4 get functions should not echo
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $field - the Field to display
 * 
 */
function getPageField($page,$field){   
	echo returnPageField($page,$field);
}

/**
 * alias for getPageField()
 * @deprecated 3.4 pointless
 */
function echoPageField($page,$field){
	getPageField($page,$field);
}

/**
 * Return Page Content
 *
 * Return the content of the requested page. 
 * Retreives from page file if content does not exist in cache
 * NOTE Performs content filter before returning by default.
 * @deprecated 3.4 confusing named function
 * @since 3.1
 * @param $page - slug of the page to retrieve content
 * @param $raw false - if true return raw xml, no strip, no filter
 * @param $nofilter false - if true skip content filter execution
 *
 */
function returnPageContent($page, $field='content', $raw = false, $nofilter = false){ 
	if($field == 'content') return getPageContentField($page, !$nofilter);
	return getPageFieldValue($page, $field, $raw, $cache);
}

/**
 * Get Page Content
 *
 * Retrieve and display the content of the requested page. 
 * As the Content is not cached the file is read in.
 * @deprecated due to echo nonsense
 * @since 2.0
 * @param $page - slug of the page to retrieve content
 *
 */
function getPageContent($page,$field='content'){   
	echo returnPageField($page,$field);
}

/**
 * reindex PAGES
 * will reset keys from url,
 * if you have a pagesarray that lost its keys after
 * using a function that does not maintain key indexes
 * @param  array  $pages PAGES, else use pagesArray
 * @return array  	     PAGES rekeyed
 */
function reindexPages($pages = array()){
	if(!$pages){
		GLOBAL $pagesArray;
		$pages = $pagesArray;
	}	
	reindexArray($pages,'url');
}

/**
 * abstractions / shorthand
 * these are not for here, they are for theme_functions
 * but 
 * @todo  clean up and move abstractions for themes
 */

// function get_pages(){
// 	return getPages();
// }

// function get_page_field_value($pageId,$field){
// 	return returnPageField($pageId,$field);
// }

// function get_page_children($pageId){
// 	return getPages('filterParent',$pageId);
// }

// function get_parent_slug($pageId){
// 	return getParent($pageId);
// }

// function get_parents_slugs($pageId){
// 	return getParents($pageId);
// }

// function get_parent_page($pageId){
// 	return getParentPage($pageId);
// }

// function get_parents_pages($pageId){
// 	return getParentsPages($pageId);
// }

// function get_page_path($pageId){
// 	return getPagePath($pageId);
// }

/*?>*/
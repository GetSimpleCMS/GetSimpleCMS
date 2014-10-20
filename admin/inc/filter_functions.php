<?php

// array helpers, php <php 5.6 do not support array_filter by keys and values, so we use our own
// these are not backports however

/**
 * filter an array using a callback function on subarrays
 * 
 * @param  array $array        array to filter
 * @param  callable $callback  callback that returns true or false
 * @param  array $callbackargs arguments for callback function, callable(array[n],args)
 * @return array filtered array
 */
function filterArray($array,$callback,$callbackargs){
	if (function_exists($callback)){
		foreach ($array as $key => $value) {
			// filter from array if callback returns true
			// callback($subarray,$callbackargs)
			if( $callback($value,$callbackargs) ){
				unset($array[$key]);
			}	
		}
		return $array;
	}
	return $array;	
}

/**
 * filter sub arrays using a callback function on keys
 * 
 * @param  array $array     array of arrays to filter
 * @param  callable $callback callback function that return true or false
 * @param  array $args     array or arguments for callback, callable(array[n]->key(array[n]),args)
 * @return array           array of arrays with select keys removed
 */
function filterSubArrayKey($array,$callback,$callbackargs){
	if (function_exists($callback)){

		foreach ($array as &$subarray) {
			foreach ($subarray as $key => $value) {
				// filter from array if callback returns true
				// callback($key,$callbackargs)					
				if( $callback($key,$callbackargs) ){
					unset($subarray[$key]);
				}
			}
		}
	} 
	// else debugLog(array(__FUNCTION__,'callback not reachable: ' . $callback));
	return $array;
}

/*
 * FILTER CORE FUNCTIONS
 *
 * internal pages array will be reffered to as `PAGES`
 * a custom array is reffered to as `PAGES collecton`
 * 
 */

/**
 * get PAGES
 * optionally filter with provided filterfunction
 *
 * @since  3.4
 * @param  callable $filterFunc function name for filter callout
 * @param  mixed ... variable number of arguments to pass to filterfunc
 * @return array  new pagesarray
 */
function getPages($filterFunc=null/*,...*/){
	GLOBAL $pagesArray;

	if(function_exists($filterFunc)){
		$args    = func_get_args();
		$args[0] = $pagesArray; // replace first argument (filterfunc) with PAGES
		return call_user_func_array($filterFunc, $args);
	} else return $pagesArray;
}

/**
 * get all values of a single field from PAGES, array_column
 * uses PAGES if a page collection is not passed
 * 
 * @since  3.4
 * @uses  array_column, backported
 * @uses  getPages
 * @param  string $field key of fields to return
 * @param  optional PAGES collection
 * @return array      new array of fields
 */
function getPagesFields($field,$pages = array()){
	if(!$pages) $pages = getPages();
	return array_column($pages,$field,'url');
}

/**
 * filter PAGES using a callback filter function on each page
 * remove page if callback returns true
 * helper for getPages
 *
 * @since  3.4
 * @param  array $pages PAGES collection
 * @param  callable $func  functionname to use as callback filter
 * @param  args $arg  args to pass on to func
 * @return array        new pagesarray
 */
function filterPageFunc($pages,$func,$arg){
	return filterArray($pages,$func,$arg);
}

/**
 * runs a custom callback function on subarray keys
 * removes sub array key if filter returns true
 *
 * @param  array $pages pagesarray
 * @param  mixed $arg   arguments for function
 * @return array        original array with subarray fields removed or not
 */
function filterPageFieldFunc($pages,$func,$arg){
	return filterSubArrayKey($pages,$func,$arg);
}


/**
 * helper for getPages with PAGES collection, doesn't really do anything
 * 
 * @todo  switch to get_func_args
 *
 * @since  3.4
 * @param  array $pages PAGES collection
 * @param  callable $func  functioname of function
 * @param  mixed $arg   args for filter function
 * @return array        new pagesarray
 */
function filterPagesFunc($pages,$func,$arg){
	if (function_exists($func)){
		$pages = $func($pages,$arg);
	}
	return $pages;
}

/*
 * Abstractions
 */

/**
 * wrapper for comparison function using PAGE key
 * compare(key,mykey)
 *
 * @since  3.4
 * @param  str $key    key
 * @param  mixed $args arguments for comparison func
 * @return bool        returns bool from comparison func to remove KEY from PAGE
 */
function filterKeyCmpFunc($key,$args/* array(key,comparisonfunc )*/){
	list($fieldkey,$func) = $args;
	if (function_exists($func))	return $func($key,$fieldkey);
	return false;
}

// FILTER PAGE KEY HELPERS

// main filter on key using a custom comparator function
function filterKeyFunc($pages,$key,$func){
	return filterPageFieldFunc($pages,'filterKeyCmpFunc',array($key,$func));
}

// filter on key index match array of keys
// use to return custom fieldset from PAGES collection
// eg. $newPages = getPages('filterKeysMatch',array('url','meta'));
// returns PAGES with only `url` and `meta` fields in PAGE subarrays
function filterKeysMatch($pages,$keys){
	return filterKeyFunc($pages,$keys,'filterInValuesCmp');
}
// alias for single key filterKeysMatch
// differs from getPagesFields in that this preserves inner array and keys
function filterKeyMatch($pages,$key){
	return filterKeysMatch($pages,array($key));
}

// FILTER PAGES HELPERS

/**
 * wrapper for a comparison function using PAGE field
 * compare(page[key],mykey->value) and returns its result
 *
 * @since  3.4
 * @param  array $page single page array
 * @param  mixed $args arguments for func
 * @return bool       returns bool from comparison function to remove PAGE from PAGES
 */
function filterKeyValueCmpFunc($page,$args/* array(key,value,comparisonfunc )*/){
	list($fieldkey,$fieldvalue,$func) = $args;
	if (function_exists($func))	return $func($page[$fieldkey],$fieldvalue);
	return false;
}

/**
 * filter PAGES on keys and values, using a key value comparison function 
*/
function filterKeyValueFunc($pages,$key,$value,$func){
	return filterPageFunc($pages,'filterKeyValueCmpFunc',array($key,$value,$func));
}

// filter on key value MATCHES value
// eg. $newPages = getPages('filterKeyValueMatch','menuStatus','Y');
function filterKeyValueMatch($pages,$key,$value){
	return filterKeyValueFunc($pages,$key,$value,'filterMatchCmp');
}

// filter on key value MATCHES value (case-insentitive)
// eg. $newPages = getPages('filterKeyValueMatch','menuStatus','y');
function filterKeyValueMatch_i($pages,$key,$value){
 	return filterKeyValueFunc($pages,$key,$value,'filterMatchiCmp');
}

/**
 * filter comparison functions
 * return true to filter
 * 
 * @todo  natives comparators return 0 if equal , wrappers should evaluate with (!== 0 || false) so we can use sort comparators for fitlers
 * 
 */

// EQUALS comparison
function filterMatchCmp($a,$b){
	return strcmp($a,$b) !== 0; // native , respects LC_COLLATE
	// return $a!==$b; // custom
}

// EQUALS case-insensitive comparison
// @uses lowercase (mbstring compat)
// @todo is strcmp utf-8 compatbile , also suffers from  type casting injection
function filterMatchiCmp($a,$b){
	// return strcasecmp($a,$b); // native, not mb safe?
	return strcmp(lowercase($a),lowercase($b)) !== 0; // custom
}

// BOOLEAN comparison
// casts to boolean before compare
// can probably use native str cmp since its binary safe, 
// but we might want to do some smart Y/N str noramlizing later on etc.
function filterMatchBoolCmp($a,$b){
	$a = (bool) $a;
	$b = (bool) $b;
	return $a!==$b;
}

// IN VALUES comparison
// match multiple values
// eg. filterKeyValueFunc($pagesArray,'menuOrder',array(1,2),'filterInValuesCmp');
function filterInValuesCmp($a,$b){
	return !in_array($a,$b);
}

// NOT IN VALUES comparison function
// eg. filterKeyValueFunc($pagesArray,'menuOrder',array(1,2),'filterNotInValuesCmp');
function filterNotInValuesCmp($a,$b){
	return in_array($a,$b);
}

/**
 * filter TAGS comparison function
 * splits comma delimited tag string then compares to array provided
 */
function filterTagsCmp($a,$b){
	if( is_array($b) ) return !array_intersect(getTagsAry($a,true),$b);
	return false;
}

/** 
 * filter TAGS case-insensitive comparison function
 */
function filterTagsiCmp($a,$b){
	$a = lowercase($a);
	return filterTagsCmp($a,$b);
}

/**
 * filter shortcuts
 */

/**
 * filter pages by tags
 * 
 * return pages with tags matching specified tags, or optionally exclude them via exclude flag
 * accepts an array or a csv string of keywords
 * eg. getPages('filterTags',array('test','test2'),false,true);
 * 
 * @since  3.4
 * @param  array   $pages   pagesarray
 * @param  mixed   $tags    array or keyword string of tags to filter by
 * @param  boolean $case    preserve case if true, default case-insensitive
 * @param  boolean $exclude invert filter, return pages not matching tags
 * @return array            fitlered pagesarray copy
 */
function filterTags($pages, $tags, $case = false, $exclude = false){
	if(!is_array($tags)) $tags  = getTagsAry($tags,$case); // convert to array

	// get pages filtered by key & values on 'meta' and an array of tags
	if($case) $pagesFiltered    = filterKeyValueFunc($pages,'meta',$tags,'filterTagsCmp');
	else $pagesFiltered         = filterKeyValueFunc($pages,'meta',array_map('lowercase',$tags),'filterTagsiCmp');
	
	if($exclude) $pagesFiltered = array_diff_key($pages,$pagesFiltered);
	
	return $pagesFiltered;
}

/*
 * aliases for filtertasgs exclude toggle
 */
function filterTagsMatch($pages, $tags, $case = false){
	filterTags($pages, $tags, $case);
}

function filterTagsNotMatch($pages, $tags, $case = false){
	filterTags($pages, $tags, $case, false);
}


// filter matching parent
// @todo tolowercase parent since it should be a slug
function filterParent($pages,$parent=''){
	return filterKeyValueMatch($pages,'parent',$parent);
}

// @todo date field filter and sorter
// probably only need sorter in core
// filter and sort by date field
// date format none = gs default,
// sort flags asc desc
// filter flags between, null start or null end lg gt
// equals mask for date match yyyy, mm, dd, no time
// datetime php min 5.3+ ,  so use unixtime evaluation
// multi sort 2 columns etc.

//
// sorters
// most use subval sort for now
// @todo any sortkey will remain in array, eg. sortbytitle and path key = 'path'

// @todo
// how to do sorters
// most will be a custom comparison sort
// will need to do case conversions
// will need date conversions
// will need multi sort
// will need multi sort using sort index faster to just sort fields you need and use the slug index
// as a sort index depending on the sort needed, this also can help avoid
// requiring a special multidimentional sorts and allowing any sort by slug pattern, and possibly cache sorts easier.
// subval sort is very inefficient in that it creates a tmp array adds sort key value to it then sorts it and then rebuids to tmp index,
// it is great but it might be possible to make it more efficient
// 
// eg uksort($array, "strnatcasecmp"); then resort main array as multi
// some more stuff here http://us2.php.net/array_multisort, supports sorting by sort array or multiple columns.
// multisort does not support local or natural sorting in php < 5.4 and 5.3 respectivly
// 
// Sorting utf-8 by locale is iffy
// strcoll() might be of some use
// 
// sorting by at least 2 columns, and fake columns such as external relationships, parent title / parent slug


/**
 * sortkey below sorts by a key with uasort without creating a seperate sorting array
 * but it uses a tmp global (@todo make static) and custom comparator
 */
function sortKey($pages,$key){
	// return subval_sort($pagesArray,$key);

	GLOBAL $sortkey;
	$sortkey = $key;
    function custom_sort($a,$b) {
    GLOBAL $sortkey;
       return $a[$sortkey]>$b[$sortkey];
    }
    uasort($pages, "custom_sort");

    unset($sortkey);
    return $pages;
}


// path = get all parents not just first
// function sortPathTitle($pages)
// function sortPath($pages)

/**
 * sort by "parent-title / page-title"
 * @param  array $pages pages array
 * @return array        sorted
 */
function sortParentTitle($pages){
	$seperator = ' - ';
	foreach ($pages as $slug => &$page) {
		$page['path'] = $page['parent'] ? $pages[$page['parent']]["title"] . $seperator : '';
		$page['path'] .= $page['title'];
	}
	return 	subval_sort($pages,'path');
}

// test using multi sort 
function sortParentTitleMulti($pages){
	$sort = array();
	foreach($pages as $slug => $page) {
    	$sort['title'][$slug] = $page['title'];
    	$sort['parenttitle'][$slug] = $page['parent'] ? $pages[$page['parent']]["title"] : '';
    }
    _debugLog($sort);
	# sort by event_type desc and then title asc
	array_multisort($sort['parenttitle'], SORT_ASC, $sort['title'], SORT_ASC,$pages);
	return $pages;
}

/**
 * sorts by "parent-slug / page-slug"
 * @param  array $pages pages array
 * @return array        sorted
 */
function sortParentPath($pages){
	$seperator = '/';
	foreach ($pages as $slug => &$page) {
		$page['path'] = $page['parent'] ? $pages[$page['parent']]["url"] . $seperator : '';
		$page['path'] .= $page['url'];
	}
	return 	subval_sort($pages,'path');
}

// in progress
function sortPageFunc($pages,$func=null){
     // Define the custom sort function
	uasort ( $pages,$func);
    return $pages;
}


function sortPageDateCmp($a,$b){
	// sort by date field ( using gs format )
}

/**
 * abstractions / shorthand
 */

function get_pages(){
	return getPages();
}

function getPageFieldValue($pageId,$field){
	return returnPageField($pageId,$field);
}

function get_page_children($pageId){
	return getPages('filterParent',$pageId);
}

// get direct children no recursive
function get_page_parent($pageId){
	$pagesArray = getPages();
	$parentId = $pagesArray[$pageId]['parent'];
	return $pagesArray[$parentId];
}

function get_page_parents($pageId){
	getParentPages($pageId);
}


// why did I use self rescursion ?
// I had a reason for this.
function getParentPagesRecurse($pageId,$pathAry = array()){
	$pagesArray = getPages();
	$pageParent = getPageFieldValue($pageId,'parent');
	if(empty($pageParent)) return $pathAry;

	foreach($pagesArray as $key => $page){
		if($key == $pageParent){
			_debugLog($key,$pageParent);
			$pathAry[$key] = $page;
			return getParentPages($key,$pathAry);
		}
	}

	return $pathAry;
}

function getParents($pageId){
	$pageparents = getPagesFields('parent');
	// _debugLog($pageparents);
	$parent = $pageId;
	$parents = array();
	// _debuglog($pageparents[$parent]);
	while(isset($pageparents[$parent])){
		$parent = $pageparents[$parent];
		$parents[] = $parent;
		_debuglog($parent);
	}
	return $parents;
}

/*?>*/
<?php

/*
 * FILTER CORE FUNCTIONS
 */

/**
 * getpagesarray filter with optional filterfunction
 *
 * @since  3.4
 * @param  callable $filterFunc function name for filter callout
 * @return array  new pagesarray
 */
function getPages($filterFunc=null){
	GLOBAL $pagesArray;

	if(function_exists($filterFunc)){
		$args=func_get_args();
		$args[0] = $pagesArray;
		return call_user_func_array($filterFunc, $args);
	} else return $pagesArray;
}

/**
 * get list of field values from pages array as column
 * 
 * @since  3.4
 * @uses  array_column
 * @uses  getPages
 * @param  string $field key of fields to return
 * @param  optional pages array
 * @return array      new array of fields
 */
function getPagesFields($field,$pages = array()){
	if(!$pages) $pages = getPages();
	return array_column($pages,$field,'url');
}

/**
 * filter PAGES using comparator function
 *
 * @since  3.4
 * @param  array $pages pages array
 * @param  callable $func  functionname to use as filter
 * @param  args $arg  args to pass on to func
 * @return array        new pagesarray
 */
function filterPageFunc($pages,$func,$arg){
	if (function_exists($func)){
		foreach ($pages as $pageId => $page) {
			if( $func($page,$arg) ) unset($pages[$pageId]);
		}
		return $pages;
	}
	return $pages;
}

/**
 * filter PAGE FIELDS using filter function
 * @todo  switch to get_func_args
 *
 * @since  3.4
 * @param  array $pages pages array
 * @param  callable $func  functioname of function
 * @param  mixed $arg   args for filter function
 * @return array        new pagesarray
 */
function filterPageFieldFunc($pages,$func,$arg){
	if (function_exists($func)){
		$pages = $func($pages,$arg);
	}
	return $pages;
}


/**
 * FILTER WITH CUSTOM COMPARE, FUNCTION CALLERS
 */

/**
 * runs a custom key value comparison filter on array
 *
 * @since  3.4
 * @param  array $page page array
 * @param  mixed $arg arguments for func
 * @return bool       returns true to filter from comparator function
 */
function filterKeyValueCmpFunc($page,$arg){
	list($key,$value,$func) = $arg;
	if (function_exists($func))	return $func($page[$key],$value);
	return false;
}

/**
 * runs a custom key comparison filter on subarray keys
 * removes sub array key if filter returns true
 *
 * @param  array $pages pagesarray
 * @param  mixed $arg   arguments for function
 * @return array        original array with subarray fields removed or not
 */
function filterKeyCmpFunc($pages,$arg){
	list($key,$func) = $arg;
	if (function_exists($func)){

		foreach ($pages as $pageKey => &$page) {

			foreach ($page as $fieldkey => $field) {
				if( $func($fieldkey,$key) ){
					unset($page[$fieldkey]);
				}
			}
		}
	}
	return $pages;
}

/*
 * Abstractions
 */

// main filter on key using a custom comparator function
function filterKeyFunc($pages,$key,$func){
	return filterPageFieldFunc($pages,'filterKeyCmpFunc',array($key,$func));
}

// main filter on key and value using a custom comparator function
function filterKeyValueFunc($pages,$key,$value,$func){
	return filterPageFunc($pages,'filterKeyValueCmpFunc',array($key,$value,$func));
}

// filter on key index matches key
// @todo: probably useless, 
// alternatives filterKeysMatch with single element array
// differs from getPagesFields in that this preserves inner array and keys
// eg. $newPages = filterKeyMatch($pagesArray,'meta');
// eg. $newPages = getPagesFields('meta');
function filterKeyMatch($pages,$key){
	return filterKeyFunc($pages,$key,'filterMatchCmp');
}

// filter on key index match array of keys
// use to filter pages by certain field keys
// eg. $newPages = getPages('filterKeysMatch',array('url','meta'));
function filterKeysMatch($pages,$keys){
	return filterKeyFunc($pages,$keys,'filterInValuesCmp');
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
 * natives return 0 if equal should evaluate with !== 0 or false since it can return null on failures 
 * in main funcs to allow for use with sort comparators
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
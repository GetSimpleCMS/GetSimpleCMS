<?php

/////////////
// TESTING //
/////////////

// if we have multiple menus, then there will be no concept of parents, we would be sorting by menus
// of course we can retian a single menu or parent heirarchy for page organization and legacy support

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
    	if(isset($page['parent']) && isset($pages[$page['parent']])){
    		$sort['parenttitle'][$slug] = $page['parent'] ? $pages[$page['parent']]["title"] : '';
    	} else $sort['parenttitle'][$slug] = '';
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

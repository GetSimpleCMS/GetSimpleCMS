<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Filter Functions
 *
 * Page getters, filters, sorters
 * callables are actually (str) function names, may support actual callables in future PHP > 5.2 requirements
 * 
 * @since  3.4
 * @author shawn_a
 * @todo  create wiki docs
 * @link http://get-simple.info/docs/filters
 *
 * @package GetSimple
 * @subpackage Filter-Functions
 */

/*
 * **************************************************************************** 
 * FILTER CORE FUNCTIONS
 * **************************************************************************** 
 *
 * definitions:
 * `PAGE` An individual page object typically a simpleXml obj but can also be an array
 * `PAGES` internal pages array, array of PAGE objects, usually the default $pagesArray cache
 * `PAGES collection` custom pages arrays, array of PAGE objects, usually a filtered array of pages
 * `pageId` a page unique id aka slug
 * 
 */

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

/**
 * filter a pages collection by an array of slugs, optionally sort to match order
 * @since  3.4
 * @param  array $keys array of keys to keep
 * @param  bool $sort sort filtered pages array by keys order if true
 * @return array       pages array with only the matching pages
 */
function filterPagesSlugs($pages,$slugs,$sort = false){
	if(!$slugs) return; // nothing to filter? then you get nothing back
	$ary = array_intersect_key($pages,array_flip($slugs)); // filter
	if(!$sort) return $ary;
	return arrayMergeSort($ary,$slugs,false); // sort
}


/**
 * **************************************************************************** 
 * FILTER PAGE KEY HELPERS
 * ****************************************************************************  
 */

/**
 * Filters page field keys
 * 
 * filter on key index match array of keys
 * used for getting a custom fieldset from PAGES collection
 * eg. $newPages = getPages('filterKeysMatch',array('url','meta'));
 * returns PAGES with only `url` and `meta` fields in PAGE subarrays, all other fields are ommited
 *
 * differs from getPagesFields in that this preserves the inner array and keys
 * 
 * @since  3.4
 * @param array $pages PAGES
 * @param array $keys array of field key names to return in pages collection
 * @return  array filtered PAGES
 */
function filterKeysMatch($pages,$keys){
	return filterKeyFunc($pages,$keys,'filterInValuesCmp');
}

/**
 * alias for filterKeysMatch with a single key
 * 
 * @since  3.4
 * @param array $pages PAGES
 * @param array $keys array of field key names to return in pages collection
 * @return  array filtered PAGES
 */
function filterKeyMatch($pages,$key){
	return filterKeysMatch($pages,array($key));
}

/**
 * **************************************************************************** 
 * FILTER PAGE HELPERS
 * ****************************************************************************  
 */

/**
 * filter PAGES on keys and values, using a key value comparison function 
 * 
 * @since  3.4
 * @param  array    $pages PAGES collection
 * @param  str      $key   field key name to filter on
 * @param  str      $value value to match field
 * @param  callable $func  comparison function name
 * @return array           filtered PAGES array
 */
function filterKeyValueFunc($pages,$key,$value,$func){
	return filterPageFunc($pages,'filterKeyValueCmpFunc',array($key,$value,$func));
}

/**
 * filter on key value MATCHES value
 * eg. $newPages = getPages('filterKeyValueMatch','menuStatus','Y');
 * 
 * @since 3.4
 * @param  array    $pages PAGES collection
 * @param  str      $key   field key name to filter on
 * @param  str      $value value to match field
 * @return array           filtered PAGES array
 */
function filterKeyValueMatch($pages,$key,$value){
	return filterKeyValueFunc($pages,$key,$value,'filterMatchCmp');
}

/**
 * filter on key value MATCHES value (case-insentitive)
 * eg. $newPages = getPages('filterKeyValueMatch','menuStatus','y');
 * 
 * @since 3.4
 * @param  array    $pages PAGES collection
 * @param  str      $key   field key name to filter on
 * @param  str      $value value to match field
 * @return array           filtered PAGES array
 */
function filterKeyValueMatch_i($pages,$key,$value){
 	return filterKeyValueFunc($pages,$key,$value,'filterMatchiCmp');
}

/**
 * filter on key value MATCHES boolean value (bool casting performed)
 * eg. $newPages = getPages('filterKeyValueMatchBool','menuStatus',true);
 * 
 * @since 3.4
 * @param  array    $pages PAGES collection
 * @param  str      $key   field key name to filter on
 * @param  str      $value value to match field
 * @return array           filtered PAGES array
 */
function filterKeyValueMatch_bool($pages,$key,$value){
 	return filterKeyValueFunc($pages,$key,$value,'filterMatchBoolCmp');
}


/**
 * **************************************************************************** 
 * filter comparison functions
 * **************************************************************************** 
 * 
 * return true to filter
 * 
 * @todo  natives comparators return 0 if equal , wrappers should evaluate with (!== 0 || false) so we can use sort comparators for filters
 * @todo  convert to standard string or array comparators , and use sort result sets
 */

/**
 * comparison function wrapper
 * wrapper for filterKeyCmpFunc on sub array keys
 * PAGES FIELD KEY comparison performed
 * @since  3.4
 * @param  array    $pages PAGES collection
 * @param  str      $key   field key name to filter on
 * @param  callable $func  callback function name
 * @return array           filtered PAGES array
 */
function filterKeyFunc($pages,$key,$func){
	return filterPageFieldFunc($pages,'filterKeyCmpFunc',array($key,$func));
}

/**
 * comparison function wrapper
 * KEY comparison performed, filters keys using key comparison
 * compare(key,mykey)
 *
 * @since  3.4
 * @param  str $key    key to compare
 * @param  mixed $args arguments for comparison func
 * @return bool        returns bool from comparison func to remove KEY from PAGE
 */
function filterKeyCmpFunc($key,$args/* array(key,comparisonfunc )*/){
	list($fieldkey,$func) = $args;
	if (function_exists($func))	return $func($key,$fieldkey);
	return false;
}

/**
 * comparison function wrapper 
 * PAGE FIELD KEY VALUE comparison
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
 * EQUALS comparison, $a==$b
 * @param  str $a string to compare
 * @param  str $b string to compare
 * @return bool   false if matches
 */
function filterMatchCmp($a,$b){
	return strcmp($a,$b) !== 0; // native , respects LC_COLLATE
	// return $a!==$b; // custom
}

/**
 * EQUALS case-insensitive comparison, lowercase($a)==lowercase($b)
 * @uses lowercase (mbstring compat)
 * @todo is strcmp utf-8 compatbile , also suffers from  type casting injection
 * @param  str $a string to compare
 * @param  str $b string to compare
 * @return bool   false if matches
 */
function filterMatchiCmp($a,$b){
	// return strcasecmp($a,$b); // native, not mb safe?
	return strcmp(lowercase($a),lowercase($b)) !== 0; // custom
}

/**
 * BOOLEAN comparison, (bool)$a==(bool)$b
 * casts to boolean before compare
 * @todo  could probably use native str cmp since its binary safe, 
 *        but may want to add "Y"/"N" str noramlizing later on etc. since we are not consistant across settings
 * @param  str $a string to compare
 * @param  str $b string to compare
 * @return bool   false if matches
 */
function filterMatchBoolCmp($a,$b){
	$a = (bool) $a;
	$b = (bool) $b;
	return $a!==$b;
}

/**
 * IN VALUES comparison, $a IN values('b0','b1','b2')
 * matches $a in multiple values $b
 * eg. filterKeyValueFunc($pagesArray,'menuOrder',array(1,2),'filterInValuesCmp');
 * @param  str   $a string to compare
 * @param  array $b array of values to compare
 * @return bool     false if $a matches no values
 */
function filterInValuesCmp($a,$b){
	return !in_array($a,$b);
}

/**
 * NOT IN VALUES comparison, $a NOT IN values('b0','b1','b2')
 * matches $a not in multiple values $b
 * eg. filterKeyValueFunc($pagesArray,'menuOrder',array(1,2),'filterNotInValuesCmp');
 * @param  str   $a string to compare
 * @param  array $b array of values to compare
 * @return bool     false if $a matches any value
 */
function filterNotInValuesCmp($a,$b){
	return in_array($a,$b);
}

/**
 * match any values, $a contains at least 1 from $b, (value OR value)
 * @param  str   $a array source to compare
 * @param  array $b array to compare
 * @return bool     false if $a values matche any value in $b
 */
function filterArrayMatchAnyCmp($a,$b){
	return !array_intersect($a,$b);
}

/**
 * match all values, $a contains all from $b, (value AND value)
 * @param  str   $a array source to compare
 * @param  array $b array to compare
 * @return bool     false if $a values match all $b values
 */
function filterArrayMatchAllCmp($a,$b){
	$matches = array_intersect($a,$b);
	return count($matches) !== count($b);
}


/**
 * ****************************************************************************
 * Filter shortcuts/aliases
 * ****************************************************************************
 */


/**
 * filter TAGS pre-process comparison functions
 * pre process splits $a(meta) comma delimited string then compares to array provided
 */
// match any
function filterTagsMatchAnyCmp($a,$b){
	return filterArrayMatchAnyCmp(tagsToAry($a,true),$b);
}
// lowercase match any
function filterTagsMatchAnyiCmp($a,$b){
	return filterTagsMatchAnyCmp(lowercase($a),$b);
}
// match all tags
function filterTagsMatchAllCmp($a,$b){
	return filterArrayMatchAllCmp(tagsToAry($a,true),$b);
}
// lowercase match all tags
function filterTagsMatchAlliCmp($a,$b){
	return filterTagsMatchAllCmp(lowercase($a),$b);
}

/**
 * filter pages by tags
 * 
 * return pages with tags matching any or all of specified tags
 * optionally exclude matches via exclude flag which inverts the resulting pages
 * 
 * accepts an array or a csv string of keywords
 * eg. getPages('filterTags',array('test','test2','позтюлант'),$case=false, $exclusive=false, $exclude=false);
 * 
 * @since  3.4
 * @param  array   $pages   pagesarray
 * @param  mixed   $tags    array or keyword string of tags to filter by
 * @param  boolean $case    preserve case if true, default case-insensitive
 * @param  boolean $exclusive require match ALL if true, else match ANY
 * @param  boolean $exclude invert filter, return pages not matching tags
 * @return array            filtered PAGES collection
 */
function filterTags($pages, $tags, $case = false, $exclusive = false, $exclude = false){
	
	$filterFunc = $exclusive ? 'filterTagsMatchAll' : 'filterTagsMatchAny';
	
	// if input tags not array, convert
	if(!is_array($tags)) $tags = tagsToAry($tags,$case);
	
	// if lowercase, normalize input tags to lowercase
	if(!$case){
		$tags = array_map('lowercase',$tags);
		$filterFunc .= 'i'; // change filterfunc to lowercase compare
	}
	
	$pagesFiltered = filterKeyValueFunc($pages,'meta',$tags,$filterFunc.'Cmp');
	
	if($exclude) $pagesFiltered = array_diff_key($pages,$pagesFiltered); // invert PAGES
	
	return $pagesFiltered;
}

/**
 * filter matching parent
 * @param  array $pages  PAGES collection
 * @param  string $parent parent slug to filter on 
 * @return array         PAGES collection
 */
function filterParent($pages,$parent=''){
	return filterKeyValueMatch($pages,'parent',lowercase($parent));
}


/**
 * invert a filtered page set by using it to filter PAGES
 * @param  array $pagesFiltered  a filtered PAGE collection
 * @param  array  $pages         (optional) PAGES collection to filteragainst, else use all pages
 * @return array                 items of $pages not in $pagesFiltered
 */
function filterInverse($pagesFiltered,$pages = array()){
	if(!$pages) $pages = getPages();
	return array_diff_key($pages,$pagesFiltered);
}

/*?>*/
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
		return call_user_func_array($filterFunc, $args);
	} else return $pagesArray;
}

/**
 * get all values of a single field from PAGES, array_column
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
 * matches $a to multiple values $b
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
 * matches $a to multiple values $b
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
 * @param  array  $pages         PAGES
 * @return array                 items of $pages not in $pagesFiltered
 */
function filterInverse($pagesFiltered,$pages = array()){
	if(!$pages) $pages = getPages();
	return array_diff_key($pages,$pagesFiltered);
}

/**
 * abstractions / shorthand
 * these are not for here, they are for theme_functions
 * but 
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


/**
 * get PAGE path
 * @param  str $pageId slug of PAGE to get path to
 * @return str         path/to/pageId
 */
function getPagePath($pageId){
	$parents = getParents($pageId);
	if($parents) return implode('/',array_reverse($parents)) . '/' . $pageId;
	return $pageId;
}

function getPagePathField($pageId,$field){
	$parents = getParentFields($pageId,$field);
	if($parents) return implode('/',array_reverse($parents)) . '/' . getPageFieldValue($pageId,$field);
	return $pageId;
}

/**
 * get PAGE parent slug
 * alias for $pagesArray['slug']['parent']
 * @param  str $pageId slug of PAGE to get parent of
 * @return str         parent of this page
 */
function getParent($pageId){
	$parentId  = returnPageField($pageId,'parent');
	return (string) $parentId;
}

/**
 * get PAGE parent PAGE
 * alias for $pagesArray[$pagesArray['slug']['parent']]
 * @param  str $pageId slug of PAGE to get path for
 * @return str         parent PAGE object
 */
function getParentPage($pageId){
	$pagesArray = getPages();
	$parentId   = $pagesArray[$pageId]['parent'];
	return $pagesArray[$parentId];
}

/**
 * get PAGE parents slugs
 * returns an array of all this pages parents slugs
 * @param  str $pageId slug of child
 * @return array       array of parents slugs
 */
function getParents($pageId){

	// return getParentFields($pageId,'id'); // @todo not working

	$pageparents = getPagesFields('parent');
	$parent      = getParent($pageId);
	$parents     = array();

	if(empty($parent)) return array();

	$parents[] = $parent;

	while(isset($pageparents[$parent])){
		$parent    = (string)$pageparents[$parent];
		if(!empty($parent))	$parents[] = $parent;
	}
	return $parents;
}


function getPageFieldValue($pageId,$field){
	return returnPageField($pageId,$field);
}


/**
 * get PAGE parents slugs
 * returns an array of all this pages parents slugs
 * @param  str $pageId slug of child
 * @return array       array of parents slugs
 */
function getParentFields($pageId,$key = 'parent'){
	$pageparents  = getPagesFields('parent');
	$parentValues = getPagesFields($key);
	$parent       = getParent($pageId);
	$values       = array();

	if(empty($parent)) return array();

	while(isset($pageparents[$parent]) && isset($parentValues[$parent])){
		$value    = (string)$parentValues[$parent];
		$parent   = (string)$pageparents[$parent];
		if(!empty($value))	$values[] = $value;
	}
	return $values;
}

/**
 * get page parent pages
 * returns an array of all this pages parents page-arrays
 * @param  str $pageId slug of child
 * @return array       PAGES collection of parents
 */
function getParentsPages($pageId){
	$pagesArray  = getPages();
	$pageparents = getPagesFields('parent');
	$parent      = $pageId;
	$parents     = array();
	while(isset($pageparents[$parent])){
		$parent = $pageparents[$parent];
		if(isset($pagesArray[$parent])){
			$parents[$parent] = $pagesArray[$parent];
		}
	}
	return $parents;
}


/*?>*/
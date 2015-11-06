<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * GetSimple Menu Manage and Manipuation functions
 * @package GetSimple
 * @subpackage menus_manage_functions.php
 */


/**
 * menu rebuild using recurseUpgradeTree
 * @param  [type] $slug    [description]
 * @param  [type] $newslug [description]
 * @return [type]          [description]
 */
function menuItemRebuildChange($args){
	/**
	 * as an alternative to using individual functions to manipulate flat array and then rebuild nest
	 * I can manipulate nest and recurseUpgrade as we would when submitting a new menu
	 * This might take longer but only needs to be done on heirarchy changes and involves signifigantly less logic
	 * possible actions
	 * array('rename',$slug, $newslug);
	 * array('move',$slug, $newparent);
	 * array('delete', $slug);
	 * array('insert', $slug, $parent, $after, $data);
	 */
	debugLog($args);
	$action = $args[0];

	// change raw menu, rebuild menu recurse
	$menu = getMenuDataArray();

	if($action == 'insert'){
		$slug       = $args[1];
		$parentslug = $args[2];
		$after      = $args[3];

		$item = array($slug => array('id' => $slug));
		$item = array(array('id' => $slug));

		if($parentslug){
			$parent = &getMenuItemTreeRef($menu, $parentslug);
			$pos = array_insert_after($parent['children'],$after,$item);
		}
		else {
			$pos = array_insert_after($menu[GSMENUNESTINDEX],$after,$item);
		}
	}

	if($action == 'rename'){
		$slug    = $args[1];
		$newslug = $args[2];

		$item = &getMenuItemTreeRef($menu,$slug);
		// manipulate
		$item['id'] = $newslug;
	}
	
	// change a parent, move the item to new parent or root
	if($action == 'move'){
		$slug = $args[1];
		$newparent = $args[2];

		$item = getMenuItemTreeRef($menu,$slug);
		_debugLog($item);

		// insert
		if($newparent) {
			$parent = &getMenuItemTreeRef($menu, $newparent);
			$parent['children'][] = $item;
		}
		else {
			// $menu[GSMENUNESTINDEX][$newparent]['children'][] = $item;
			$menu[GSMENUNESTINDEX][$slug] = $item;
		}

		_debugLog($menu[GSMENUNESTINDEX]['index']);

		// @todo delete not working, probably need to copy refernce or delete first
		$action = 'delete';
	}

	// remove an item, shift its children
	if($action == 'delete'){
		$slug = $args[1];

		$item = &getMenuItemTreeRef($menu,$slug);

		// move children to root to save
		if(isset($item['children'])){
			$menu[GSMENUNESTINDEX] = array_merge($menu[GSMENUNESTINDEX],$item['children']);
		}
		// $item = null; unset($item); // does not unset, but sure is easier

		// delete
		$parentslug = $menu[GSMENUFLATINDEX][$slug]['data']['parent'];
		if($parentslug) {
			$parent = &getMenuItemTreeRef($menu, $parentslug);
			// remove from parent
			$key = array_search($slug, $parent['children']);
	   		if($key !== false) unset($parent['children'][$key]);
		}
		else {
			// $menu[GSMENUNESTINDEX][$slug] = null;
			unset($menu[GSMENUNESTINDEX][$slug]);
		}
	}

	// @todo possible copy here to break refs, no longer needed
	$menunest = $menu[GSMENUNESTINDEX];

	// reindex
	$menunest = reindexMenuArray($menunest,true); // reindex if slug change
    
	// rebuild
    $menunew = array(); // new array
    $menunew = recurseUpgradeTree($menunest); // build full menu data, modify menunest ref
    $menunew[GSMENUNESTINDEX] = $menunest;     // re-join

	_debugLog($menunew);
    return $menunew;
}

// @tested
function menuItemRename($menu,$slug,$newslug){
	$menu[GSMENUFLATINDEX][$slug]['id'] = $newslug;
	debugLog($menu[GSMENUFLATINDEX]);
	$menu[GSMENUFLATINDEX] = reindexArray($menu[GSMENUFLATINDEX],'id'); // reindex so we save position in array
	$menu = menuItemPathChanged($menu,$newslug);
	// regen nest
	return $menu;
}

// @untested
function menuItemUpdate($menu,$slug,$data){
	$menu[GSMENUFLATINDEX][$slug]['data'] = $data;
	menuItemRefreshChildren($menu,$slug);
	return $menu;
}

// @untested
function menuItemMove($menu,$slug,$newparent){
	$item = getMenuItem($menu,$slug);
	menuItemDelete($menu,$slug);
	menuItemAdd($menu,$newparent,$item);
	menuItemRefreshChildren($menu,$slug);	
	return $menu;
}

// @untested
function menuItemDelete($menu,$slug){
	debugLog(__FUNCTION__);
	// if menu item has children hand off
	if(isset($menu[GSMENUFLATINDEX][$slug]['children'])) return menuItemDeleteParent($menu,$slug);
	unset($menu[GSMENUFLATINDEX][$slug]);
	return $menu;
}

// refactored up to here, ready for testing

// @untested
function menuItemDeleteParent($menu,$slug){
	debugLog(__FUNCTION__);	
	$item = $menu[GSMENUFLATINDEX][$slug];
	// move children to root
	foreach($item['children'] as $childslug){
		$child = $menu[GSMENUFLATINDEX][$childslug];
		$child['data']['parent'] = ''; // wipe parent
		menuItemAdd($menu,$childslug,$child); // move to root
		menuItemRefreshChildren($menu,$childslug); // update children
	}
	unset($menu[GSMENUFLATINDEX][$slug]);
	return $menu;
}

// @untested
function menuItemAdd($menu,$slug,$data){
	debugLog(__FUNCTION__);
	$menu[GSMENUFLATINDEX][$slug] = $data;
	return $menu;
}

// @untested
function menuItemParentChanged($menu,$slug){
	// [ ] parent changed, so refire any pathing functions.
	// [ ] parent was removed,renamed, or moved so path changes on its children
	// [ ] move to root
	// [ ] move to another parent
	// [ ] deleted, in which case children move to root
	$menu = menuIndexPrune($menu,array_keys($menu[GSMENUFLATINDEX])); // self prune
	return $menu;
}

// @untested
function menuIndexPrune($menu,$index){
	// detect menu removals and prune them
	$removed = array_diff(array_keys($menu[GSMENUFLATINDEX]), array_keys($index));
	debugLog($removed);
	foreach($removed as $key){
		$menu = menuItemDelete($menu,$key);
	}
	return $menu;
}

// @tested
function menuItemRefreshChildren($menu,$slug){
	// return;
	// fix up children values
	// this is a problem since the menu is not saved yet , and these callouts will eventually need the menu
	if(!isset($menu[GSMENUFLATINDEX][$slug]['children'])) return $menu;

	foreach($menu[GSMENUFLATINDEX][$slug]['children'] as $key => $value){
		// update parent on children in case it changed
		$menu[GSMENUFLATINDEX][$value]['data']['parent'] = $slug;
		// update dot path if any parent changed
		$menu = menuItemRebuilDotpath($menu,$value);
		// update paths
		// recurseUpgradeTreeCallout($menu[GSMENUFLATINDEX][$value],$id = $value,$parent = $slug);
		$menu = menuItemRefreshChildren($menu,$value);
	}

	return $menu;
}

/**
 * A menu path changed update any pathing dependancies
 * rebuild dot path, rebuild all children, to fix up parent and dotpaths, etc
 * @param  menu $menu menu data array
 * @param  string $slug slug that changed
 * @return array       menu data array
 */
function menuItemPathChanged($menu,$slug){
	$menu = menuItemRebuilDotpath($menu,$slug);
	$menu = menuItemRefreshChildren($menu,$slug);
	return $menu;
}

/**
 * REBUILDERS
 */

function menuItemRebuilDotpath($menu,$slug){
	$parent     = getMenuItemParent($menu,$slug);
	$parentpath = $parent['data']['dotpath'];
	$dotpath    = $parentpath . '.' . $slug;
	$menu[GSMENUFLATINDEX][$slug]['data']['dotpath'] = $dotpath;
	return $menu;
}

/**
 * rebuild a menus nested array
 * wipes GSMENUNESTINDEX, rebuilds it and adds it back in
 * @param  array $menu menu array
 * @return array       new menu array
 */
function menuRebuildNestArray($menu){
	// create temporary root '' parent , to help prime root recusrion, then removes it when done
	$menu[GSMENUFLATINDEX]['']['children'] = array_keys(getMenuItemRoots($menu));
	
	// get new nested tree
	$newtree = array();
	$newtree = menuNestRebuild($menu[GSMENUFLATINDEX]);

	// remove temporary root parent
	unset($menu[GSMENUFLATINDEX]['']);
	
	// add new nest tree onto menu
	if(isset($menu[GSMENUNESTINDEX])) unset($menu[GSMENUNESTINDEX]);
	$menu[GSMENUNESTINDEX] = $newtree['children'];

	return $menu;
}

/**
 * build a nested array from a parent hash table
 * returns nested array
 * requires a root parent '' in hash table, for use with menuRebuildNestArray
 * @param  array  &$menu menu array reference
 * @param  str  $slug  starting slug
 * @param  boolean $data  include data array
 * @param  boolean $ref   use references to flat menu for data
 * @return array          new nest array
 */
function menuNestRebuild(&$menu,$slug = '',$data = false,$ref = true){
	$thisfunc = __FUNCTION__;	
	$tree = array();
	$tree = $menu[$slug];
	
	// setup data array options, none, static or references
	if(!$ref && !$data) unset($tree['data']); // remove data, leaving only id,children nodes
	if($ref && isset($menu[$slug]['data'])) $tree['data'] =& $menu[$slug]['data']; // add data ref to flat array
	
	// no children to process
	if(!isset($menu[$slug]['children'])){
		return $tree;
	}

	unset($tree['children']); // unset children since they might not be assoc
	
	// recurse children
	foreach($menu[$slug]['children'] as $child){
		if(isset($menu[$child])){
			$tree['children'][$child] = $thisfunc($menu,$child,$data,$ref);
		}
	}
	return $tree;
}

/**
 * build data references onto menu nest array, 
 * modifies $menu by reference
 * nest menu should already exist, will not create it
 * @param  array &$menu    menu array by ref
 * @param  array &$parents menu nest sub array, optional
 */
function menuNestAddDataRefs(&$menu,&$parents = null){
	$thisfunc = __FUNCTION__;
	if(!$parents) $parents = &$menu[GSMENUNESTINDEX]; // primer for nest array
	// detect if root or children node passed in, auto negotiate
    if(isset($parents['id']) && isset($parents['children'])) $parents = $parents['children'];
    // recurse children
    foreach($parents as $key=>&$child){
		$child['data'] = &$menu[GSMENUFLATINDEX][$child['id']]['data']; // add data flat refs
		if(isset($child['children'])) $thisfunc($menu,$child['children']);
	}
}

/**
 * get only parent root menu items from flat array
 * will return orphans as root items
 * @param  array $menu menu array
 * @return array       menu containing only root level parents
 */
function getMenuItemRoots($menu){
	$roots = array();
	foreach($menu[GSMENUFLATINDEX] as $item){
		// if root item, or items parent does not exist
		if(empty($item['data']['parent']) || !isset($menu[GSMENUFLATINDEX][$item['data']['parent']])){
			$roots[$item['id']] = $item;
		}
	}
	return $roots;
}

/**
 * get a menu item as tree with reference
 * @param  array &$menu menu data
 * @param  string $slug  slug of page
 * @return array        nested array of menu item
 */
function &getMenuItemTreeRef(&$menu, $slug){
	$parenttree = getMenuItemParent($menu,$slug);
	// @todo abstract getMenuTreeByRef
	if($parenttree){
		$item = &resolve_tree($menu[GSMENUNESTINDEX], $parenttree['data']['dotpath']);
		$item = $item[$slug];
	}
	else $item = &$menu[GSMENUNESTINDEX][$slug];

	return $item;
}

/**
 * get a menu item flat form menudata
 * @since  3.4
 * @param  array $menu menu array
 * @param  string $id   page id
 * @return array       menu item array
 */
function getMenuItem($menu,$id = ''){
    if(isset($menu[GSMENUFLATINDEX]) && isset($menu[GSMENUFLATINDEX][$id])) return $menu[GSMENUFLATINDEX][$id];
}

/**
 * get menu items parent item from menu data
 * @since  3.4
 * @param  array $menu menu data
 * @param  string $slug page slug
 * @return array       menu item or null if no parent
 */
function getMenuItemParent($menu,$slug = ''){
	$item = getMenuItem($menu,$slug);
	if(!$item) return;

	if(!empty($item['data']['parent'])){
    	return getMenuItem($menu,$item['data']['parent']);
    }
}

// MENUID MENUITEM WRAPPERS

/**
 * get item by id from specific menuid
 * @since  3.4
 * @param  string $pageid page slug
 * @param  string $menuid menu id
 * @return array  menu item array
 */
function menuItemGetData($pageid,$menuid = null){
	$menu = getMenuDataArray($menuid);
	$item = getMenuItem($menu,$pageid);
	return $item;
}

/**
 * get menu item data field from specific menu
 * @since  3.4
 * @param  string $pageid page slug
 * @param  string $field field name
 * @param  string $menuid menu id
 * @return mixed  menu item field
 */
function menuItemGetField($pageid,$field,$menuid = null){
	$item = menuItemGetData($pageid,$menuid);
	if(!$item || !isset($item['data'][$field])) return;
	return $item['data'][$field];
}

/**
 * get menu item parent item from specific menu
 * @since  3.4
 * @param  string $pageid page slug
 * @param  string $menuid menu id
 * @return array  menu item array
 */
function menuItemGetParent($pageid,$menuid = null){
	$data = menuItemGetField($pageid,'parent',$menuid);
	if(!$data) return;
	return $data;
}

/**
 * get menu items ids of parents from menu id, optionally include self
 * @since  3.4
 * @param  string  $pageid      page slug
 * @param  string  $menuid      menu id
 * @param  boolean $includeself if true include pageid in output array
 * @return array                array of menu items ids
 */
function menuItemGetParents($pageid,$menuid = null,$includeself = false){
	$path    = menuItemGetField($pageid,'dotpath',$menuid);
	if(!$path) return;
	$parents = explode(".",trim($path,'.'));
	if($includeself !== true) array_pop($parents); // remove self from path
	return $parents;
}

/**
 * get menu item ids of children from menu id
 * @since  3.4
 * @param  string  $pageid      page slug
 * @param  string  $menuid      menu id
 * @return array                array of menu items ids
 */
function menuItemGetChildren($pageid,$menuid = null){
	$item = menuItemGetData($pageid,$menuid);
	if(isset($item['children'])) return $item['children'];
}
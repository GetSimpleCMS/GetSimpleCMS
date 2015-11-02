<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * GetSimple Menu Manage and Manipuation functions
 * @package GetSimple
 * @subpackage menus_manage_functions.php
 */

function menuItemRename($menu,$slug,$data){
	$item = $menu[GSMENUFLATINDEX][$slug];

	$menu[GSMENUFLATINDEX][$data['id']] = $data;
	$menu[GSMENUFLATINDEX][$data['id']]['children'] = $item['children'];
	menuItemRefreshChildren($menu,$slug);
	return $menu;
}

function menuItemUpdate($menu,$slug,$data){
	$menu[GSMENUFLATINDEX][$slug] = $data;
	menuItemRefreshChildren($menu,$slug);
	return $menu;
}

function menuItemMove($menu,$slug,$newparent){
	$item = getMenuItem($menu,$slug);
	menuItemDelete($menu,$slug);
	menuItemAdd($menu,$newparent,$item);
	return $menu;
}

function menuItemDelete($menu,$slug){
	unset($menu[GSMENUFLATINDEX][$slug]);
	return $menu;
}

function menuItemDeleteParent($menu,$slug){
	$item = getMenuItem($menu,$slug);
	// move children to root
	foreach($item['children'] as $child){
		$child['parent'] = '';
		menuItemAdd($menu,$child['id'],$child);
		menuItemRefreshChildren($menu,$slug);
	}
	menuItemDelete($slug);
	return $menu;
}

function menuItemAdd($menu,$slug,$data){
	$menu[GSMENUFLATINDEX][$slug] = $data;
	return $menu;
}

function menuItemParentChanged($menu,$slug){
	// [ ] parent changed, so refire any pathing functions.
	// [ ] parent was removed,renamed, or moved so path changes on its children
	// [ ] move to root
	// [ ] move to another parent
	// [ ] deleted, in which case children move to root
	$menu = menuIndexPrune($menu,array_keys($menu[GSMENUFLATINDEX])); // self prune
	return $menu;
}

function menuIndexPrune($menu,$index){
	// detect menu removals and prune them
	$removed = array_diff(array_keys($menu[GSMENUFLATINDEX]), array_keys($index));
	debugLog($removed);
	foreach($removed as $key){
		$menu = menuItemDelete($menu,$key);
	}
	return $menu;
}

function menuItemRefreshChildren($menu,$slug){
	// fix up children values
	// this is a problem since the menu is not saved yet , and these callouts will eventually need the menu
	foreach($menu[GSMENUFLATINDEX][$index]['children'] as $key => $value){
		recurseUpgradeTreeCallout($menu[GSMENUFLATINDEX][$key],$id = $key,$parent = $slug);
		debugLog($menu[GSMENUFLATINDEX][$index]);
	}

	return $menu;
}

/**
 * REBUILDERS
 */

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


// menuid menuitem wrappers

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

function menuItemGetChildren($pageid,$menuid = null){
	$item = menuItemGetData($pageid,$menuid);
	if(isset($item['children'])) return $item['children'];
}
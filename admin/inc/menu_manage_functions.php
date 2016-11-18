<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * GetSimple Menu Manage and Manipuation functions
 * @package GetSimple
 * @subpackage menus_manage_functions.php
 */

/*
 * - [ ] New Page ( )parent ( )child
 * - [ ] delete page ( )parent ( )child
 * - [ ] Restore Page
 * - [ ] Restore Slug Change
 * - [ ] publish draft
 * - [ ] toggling permalink should rebuild any cached urls or paths in menus or pagesarray
 *
 * inline modifications
 * page edit parent change GSMENUINLINEUPDATES
 * page cache diff sync, insert and remove modifications to handle manual file adjustments
 * sync parents with files ? Legacy in and out ?
 *
 * insert handling, insert needs to know parent, else inserts at end
 */

/**
 * menu rebuild using recurseUpgradeTree
 * directly modifies via tree, then runs through recurseUpgradeTree
 * DO NOT REFACTOR
 * @since  3.4
 * @param  $args  argument array
 * @param  $menu  menu data, optional
 */
function menuItemRebuildChange($args,$menu = null, $rebuild = true){
	/**
	 * 
	 * as an alternative to using individual functions to manipulate flat array and then rebuild nest
	 * This can manipulate nest and then `recurseUpgrade` as we would when submitting a new menu
	 * This might take longer but only needs to be done on heirarchy changes and involves signifigantly less logic
	 * might even keep in one function to not have to move references around via arguments and returns
	 * cons is you have to rebuild the entire thing, there is no subtree rebuild, however it could be added,
	 * it is a bit sloppy , but fairly useful as it allows menu mods inline from page edits without pesky popups
	 * can also defer rebuild until after a few changes, but not all.
	 *
	 * performing serial changes using this function, is problematic, 
	 * with 1000 items, this takes 1 second to rebuild, much too long for doing more than one
	 * this is because menuRebuildTree, although it is possible to defer rebuild, 
	 * and unfortunatly we cant defer rebuilds until after all items , as items will be missing and getMenuItemTreeRef check will fail
	 * 
	 * 
	 * POSSIBLE ACTIONS
	 * array('rename',$slug, $newslug);
	 * array('move',$slug, $newparent); // move $after not imlemented
	 * array('delete', $slug, $preservechildren = true);
	 * array('insert', $slug, $parent, $after);
	 * array('modify',$slug,$data('title'));
	 * 
	 * rename+move in one pass is not implemented yet, rare, called in chain by passing meu back in as arg for now
     * both = parent and child test, with children
     * 
     * TESTS / Problems
     * -[X] rename root level
     * -[X] rename child level
     * -[X] insert at root level
     * -[X] insert at child level
     * -[ ] insert after both (NI)
     * -[X] move to root level // @todo moves to bottom no sort done ?
     * -[X] move to parent level
     * -[ ] move parent to parent level (!! child gets duplicated at root of nest, and has wrong depth in flat)
     * -[X] move from root level both
     * -[x] move from parent level both
     * -[x] delete root level both
     * -[x] delete child level both
     * -[ ] delete with and without preserve parents flag
     * -[ ] clone page, uses its own action, inline parents (NI)
     * -[ ] publish page?
     * 
     * @todo  undo functionality, since we have no history atm, we cannot undo menu changes, if we change parent inline, then undo, then nothihg changes
     * for new we insert, for delete we delete, for slug change we rename, but we do not move, since we have no previous parent atm
     * 
     * @todo optimizations
     * rekeymenu can maybe be avoided, or limited to only when needed not always
     * rekey on slug changes, rename
     * avoid recurseupgrade tree for simple inserts ?
     * 
	 */
	
	// debugLog($args);
	$action = $args[0];
	$slug   = $args[1];

	if(!$menu) $menu = getMenuDataArray();

	if($action == 'modify'){
		$item = &getMenuItemTreeRef($menu,$slug);
		array_merge($item['data'],$data);
		array_merge($menu[GSMENUFLATINDEX]['data'],$data);
	}

	if($action == 'insert'){
		// @todo this could insert the slug directly onto flat $menu to optimize, since a reindex wouldnt be needed
		
		// check if item already exists
		$itemcheck = &getMenuItemTreeRef($menu,$slug);
		if($itemcheck) return $menu;
 
		$parentslug = isset($args[2]) ? $args[2] : '';
		$after      = isset($args[3]) ? $args[3] : '';
		$data       = isset($args[4]) ? $args[4] : '';
		debugLog(__FUNCTION__ . " inserting [$slug] under [$parentslug] after [$after]"); // debugging

		$item = array($slug => array('id' => $slug,'data' => array())); // pre indexed
		// $item = array(array('id' => $slug)); // non indexed requires reindex
		if(!empty($parentslug)){
			$parent = &getMenuItemTreeRef($menu, $parentslug);
			if(!isset($parent['children'])) $parent['children'] = array();
			
			$pos = array_insert_after($parent['children'],$after,$item);
			
			debugLog(__FUNCTION__ ." inserted at parent at position [$pos]"); // debugging
			if(!isset($parent['children'][$slug]))debugLog(__FUNCTION__ . " FAILED TO INSERT");
			// else debugLog($parent['children'][$slug]); // debugging
		}
		else {
			$pos = array_insert_after($menu[GSMENUNESTINDEX],$after,$item);
			
			debugLog(__FUNCTION__ ." inserted at root at position [$pos]"); // debugging
			if(!isset($menu[GSMENUNESTINDEX][$slug])) debugLog(__FUNCTION__ . " FAILED TO INSERT");
			// else debugLog($menu[GSMENUNESTINDEX][$slug]); // debugging
		}
		$menu[GSMENUFLATINDEX][$slug] = array('id' => $slug); // insert flat
	}

	if($action == 'rename'){
		$newslug = $args[2];
		debugLog(__FUNCTION__ . " renaming [$slug] to [$newslug]");
		if($slug == $newslug) return $menu;

		$item = &getMenuItemTreeRef($menu,$slug);
		// $parent = &getMenuItemTreeRef($menu,$item['data']['parent']); // debug check parent to see if rekey works
		$item['id'] = $newslug;
		
		$menu[GSMENUFLATINDEX][$newslug] = $menu[GSMENUFLATINDEX][$slug]; // rename flat
		unset($menu[GSMENUFLATINDEX][$slug]);

		if(!isset($menu[GSMENUFLATINDEX][$newslug])) debugLog(__FUNCTION__ . " FAILED TO RENAME");
		// else debugLog($menu[GSMENUFLATINDEX][$newslug]); // debugging

		if(isset($menu[GSMENUFLATINDEX][$slug])) debugLog(__FUNCTION__ . " FAILED TO REMOVE OLD");
	}
	
	// change a parent, move the item to new parent or root
	if($action == 'move'){
		$newparent = trim($args[2]);
		debugLog(__FUNCTION__ . " moving [$slug] to [$newparent]");

		$item = &getMenuItemTreeRef($menu,$slug);
		if(!isset($item)){
			debugLog(__FUNCTION__ . " moving item not found - [$slug]");
			return $menu;
		}	

		// parent is the same
		if($item['data']['parent'] == $newparent){
			debugLog(__FUNCTION__ . " moving parent is already there, skipping"); // debugging
			return $menu;
		}	

		// insert
		if(empty($newparent)) {
			debugLog(__FUNCTION__ . " moving NEWPARENT IS EMPTY, MOVING TO ROOT"); // debugging
			$menu[GSMENUNESTINDEX][$slug] = $item; // @todo copy ?
		}
		else {
			$parent = &getMenuItemTreeRef($menu, $newparent); // @todo careful can create node it is looking for, eg. "" parent
			$parent['children'][$slug] = $item;
		}
		
		// perform delete of original next
		// since this uses getMenuItemTreeRef, it assumes that flatindex has not been modified and still points to the old item
		// alternative would be to provide a safe nest loop resolver that does not use the flat dotpath resolver
		$action = 'delete';
		$args[2] = false; // set preserve children to false

		unset($item);
		unset($parent);
		// debugLog($menu);
	}
 
	// remove an item, shift its children
	if($action == 'delete'){

		$preservechildren = true;
		if(isset($args[2]) && $args[2] === false) $preservechildren = false;

		$item = &getMenuItemTreeRef($menu,$slug);

		debugLog(__FUNCTION__ . " deleteing [".$item['id']."]"); // debugging
		if(!$item){
			debugLog(__FUNCTION__ . " nothing to delete returning"); // debugging			
			return $menu;
		}

		// if preserving children, move them to root
		if($preservechildren && isset($item['children'])){
			debugLog(__FUNCTION__ . " preserving [$slug][children], moving to root"); // debugging
			// move to root, @todo could optionally move to closest parent using insert after logic
			$menu[GSMENUNESTINDEX] = array_merge($menu[GSMENUNESTINDEX],$item['children']);
		}

		$parentslug = trim($menu[GSMENUFLATINDEX][$slug]['data']['parent']);
		
		// delete by array path if has parent
		if(!empty($parentslug)) {
			debugLog(__FUNCTION__ . " delete via parent subtree " . $parentslug); // debugging
			$parent = &getMenuItemTreeRef($menu, $parentslug);
			
			// remove from parent by key else find by index
			if(isset($parent['children'][$slug])){
				unset($parent['children'][$slug]);
			}
	   		else {
	   			// search for index then delete
				$key = array_search($slug, array_keys($parent['children']));
				// debugLog("deleting $slug at $key ");
	   			if($key !== false) unset($parent['children'][$key]);
	   			else debugLog("delete search [$slug] not found");
	   		}
		}
		else {
			unset($menu[GSMENUNESTINDEX][$slug]);
		}

		unset($menu[GSMENUFLATINDEX][$slug]); // break flat
	}

	// break refs, they are no longer needed
	unset($item);
	unset($parent);
	// debugLog($menu);

	if($action == 'rename'){
		// reindex
		$menunest = $menu[GSMENUNESTINDEX];
		$menunest = reindexMenuArray($menunest,true); // reindex if slug changes only
		// @todo recurseUpgradeTree will fail to pick up data from old slug
		$menu[GSMENUNESTINDEX] = $menunest;
		// debugLog($menunest);
	}
    
	// rebuild
	if(!$rebuild){
		debugLog(__FUNCTION__ . ' SKIPPING REBUILD');
		return $menu;
	}	

	$menunew = menuRebuildTree($menu);
	debugLog(count($menunew[GSMENUFLATINDEX]) . " MENU ITEMS");	
	return $menunew;
}

function menuIntegrityCheck($menu){
	// compare keys from nest to flat, check for invalid keys, int and "data"
	// check for all structure flaws, null objects etc.
	// keys not match id
	// check for temp keys "rekeyed"
	// check parents are correct, empty if root etc.
	// provide health check warnings on post limits, max memory, max records for large menus
	$assert = "";
	if(isset($menu[GSMENUNESTINDEX][""])) $assert .= debugLog(__FUNCTION__ . ": ASSERT nested menu has empty key<br>");
	if(isset($menu[GSMENUFLATINDEX][""])) $assert .= debugLog(__FUNCTION__ . ": ASSERT flat menu has empty key<br>");

	foreach($menu[GSMENUFLATINDEX] as $key=>$menuitem){
		if(!$menuitem) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT flat menu has empty array<br>");
		if(!isset($menuitem['data'])) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT flat menu has empty data array<br>");
		if($key !== $menuitem['id']) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT flat menu key=id mismatch<br>");
		if(!is_string($key)) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT flat menu has integer key<br>");
	}

	foreach($menu[GSMENUNESTINDEX] as $key=>$menuitem){
		if(!$menuitem) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT NEST menu has empty array<br>");
		if(!isset($menuitem['data'])) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT NEST menu has empty data array<br>");
		if($key !== $menuitem['id']) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT NEST menu key=id mismatch<br>");
		if(!is_string($key)) $assert .= debugLog(__FUNCTION__ . " " . $key . " : ASSERT NEST menu has integer key<br>");
	}

	if(!empty($assert)) die($assert);
}

/**
 * wrapper for recurseUpgradeTree, rebuilding a menu tree
 */
function menuRebuildTree($menu){
	$menunest = $menu[GSMENUNESTINDEX];	

	$menunew = array(); // new array
    $menunew = recurseUpgradeTree($menunest); // build full menu data, modify menunest ref
    $menunew[GSMENUNESTINDEX] = $menunest;    // re-join ref
    return $menunew;
}

/**
 * rebuild a menus nested array
 * wipes GSMENUNESTINDEX, rebuilds it and adds it back in
 * @since  3.4
 * @param  array $menu menu array
 * @return array       new menu array
 */
function menuRebuildNestArray($menu){
	debugLog(__FUNCTION__);
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
 * @todo  maybe pass parent in case item does not yet exist in flat array
 * @param  array &$menu menu data
 * @param  string $slug  slug of page
 * @param  bool $returnnull if true return nulls instead of creating items
 * @return array        nested array of menu item
 */
function &getMenuItemTreeRef(&$menu, $slug, $create = false){

	// return null, or else return by reference will create the item
	if(!$create && !isset($menu[GSMENUFLATINDEX][$slug])) return $null;

	$parenttree = getMenuItemParent($menu,$slug);
	if($parenttree){
		$item = &resolve_tree($menu[GSMENUNESTINDEX], $parenttree['data']['dotpath']);
		$item = &$item[$slug]; // @todo <- dreference problem
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


// @untested
function menuIndexPrune($menu,$items){
	// detect menu removals and prune them
	debugLog(__FUNCTION__ . ' ' . count($items));
	// debugLog($items);
	if(!$items) return;

	foreach($items as $key){
		$menu = menuItemRebuildChange(array('delete',$key),$menu,false);
		// $menu = menuItemDelete($menu,$key);
	}
	return $menu;
}

function menuIndexAdd($menu,$items){
	// detect menu removals and add them
	debugLog(__FUNCTION__ . ' ' . count($items));
	// debugLog($items);
	if(!$items) return;

	foreach($items as $key){
		$parent = getPageFieldValue($key,'parent'); // @todo allow import of parent
		$menu   = menuItemRebuildChange(array('insert',$key,$parent),$menu,false);		
	}
	return $menu;
}

/**
 * update menu from page cache
 * will compare slugs in menu to slugs in pagecache and diff add or prune
 * then rebuild menutree and save
 * @since  3.4
 * @return array array of items added,pruned counts or empty array if none
 */
function menuPageCacheSync(){

	$pages = getPages();
	$menu  = getMenuDataArray();

	$deltaprune = array_diff(array_keys($menu[GSMENUFLATINDEX]), array_keys($pages));
	$deltaadd   = array_diff(array_keys($pages),array_keys($menu[GSMENUFLATINDEX]));

	if(!$pages || $deltaprune == count($pages) || $deltaadd == count($pages)) return debugLog(__FUNCTION__ . " something went wrong");
	
	debugLog(__FUNCTION__ . " prune " . count($deltaprune));
	debugLog(__FUNCTION__ . " insert " . count($deltaadd));

	if(!$deltaprune && !$deltaadd) return array();

	if($deltaadd)   $menunew = menuIndexAdd($menu,$deltaadd);
	if($deltaprune)	$menunew = menuIndexPrune($menu,$deltaprune);

	$menunew     = menuRebuildTree($menunew);
	$menusuccess = menuSave(GSMENUIDCORE,$menunew);
	return array(count($deltaadd),count($deltaprune));
}

/* unused */
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
 * @todo these page trigger functions are only affecting coremenu
 * this might need to update slug changes for all menus, so they do not just stop working
 * this is the problem with using slugs and not unique page ids for pages
 */

/**
 * a page was saved
 * if GSMENUINLINEUPDATES true, process parents
 * else only insert into menu
 * @param  stringq $id   page id
 * @param  object  $xml   page simplexmlobj
 * @param  bool    $isnew page is new
 * @return  bool menusave status
 */
function pageWasSaved($id,$xml,$isnew){
	if(!getDef('GSMENUINLINEUPDATES',true)) return;

	$action = $isnew ? "insert" : "move";
	if(getDef('GSMENUINLINEUPDATES',true)) $menudata = menuItemRebuildChange(array($action,$id,(string)$xml->parent));
	else if($acton=="insert") $menudata = menuItemRebuildChange(array($action,$id));

	if(isset($menudata)) return menuSave(GSMENUIDCORE,$menudata);	
}

/**
 * a page was cloned
 * insert new into menu
 * @param  string $id new page id
 * @param  string $oldid old page id
 * @return bool   menu save status
 */
function pageWasCloned($id,$oldid){
	// update menu, @todo not handling parent ?
	$menudata = menuItemRebuildChange(array('insert',$id,getParent($oldid),$oldid));
	if(isset($menudata)) return menuSave(GSMENUIDCORE,$menudata);	
}

/**
 * page has a page id change
 * @param  string $id    page id
 * @param  string $newid new page id
 * @return bool          menu save status
 */
function pageSlugHasChanges($id,$newid){
	// do insert if old slug is null
	if(!isset($id)) $menudata = menuItemRebuildChange(array('insert',$newid));
	// do delete if newid is null
	else if(!isset($newid)) $menudata = menuItemRebuildChange(array('delete',$id));
	// do rename if slug actually changed
	else $menudata = menuItemRebuildChange(array('rename',$id,$newid));
	if(isset($menudata)) return menuSave(GSMENUIDCORE,$menudata);
}
<?php


function menuItemRename($menu,$slug,$data){
	$item = $menu[GSMENUFLATINDEX][$slug];

	$menu[GSMENUFLATINDEX][$data['id']] = $data;
	$menu[GSMENUFLATINDEX][$data['id']]['children'] = $item['children'];
	// update children ?
	return $menu;
}

function menuItemUpdate($menu,$slug,$data){
	$menu[GSMENUFLATINDEX][$slug] = $data;
	// update children ?
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
		// update children  ?
	}
	menuItemDelete($slug);
	return $menu;	
}

function menuItemAdd($menu,$slug,$data){
	$menu[GSMENUFLATINDEX][$slug] = $data;
	return $menu;
}

function getMenuItem($menu,$slug){
	return $menu[GSMENUFLATINDEX][$slug];
}

function menuItemParentChanged($menu,$slug){
	// [ ] parent changed, so refire any pathing functions.
	// [ ] parent was removed,renamed, or moved so path changes on its children
	// [ ] move to root
	// [ ] move to another parent
	// [ ] deleted, in which case children move to root
	return $menu;
}

function menuIndexPrune($menu,$index){
	// detect menu removals and prune them
	$removed = array_diff(array_keys($menu[GSMENUFLATINDEX]), array_keys($index));
	debugLog($removed);
	foreach($removed as $key){
		$menu = menuItemDelete($menu,$key);
	}
	return $menu
}
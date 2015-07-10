<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Getsimple Menu Functions
 * @package GetSimple
 * @subpackage menus_functions.php
 */

define('GSMENUNESTINDEX','nested');
define('GSMENUFLATINDEX','flat');
define('GSMENUINDEXINDEX','indices');
define('GSMENULEGACY',true);


/**
 * imports menu from pages legacy style, menuOrder only sorted
 * @return array new menu sorted by menuOrder
 */
function importLegacyMenuFlat(){
	$pages = getPagesSortedByMenu();
	$pages = filterKeyValueMatch($pages,'menuStatus','Y');
	$menu  = importMenuFromPages($pages,true);
	return $menu; 
}

/**
 * imports menu from pages tree style, parent, title, menuorder sorted
 * @return array new menu nested tree sorted by heirarchy and menuOrder
 */
function importLegacyMenuTree(){
	$pages = getPagesSortedByMenuTitle();
	$pages = filterKeyValueMatch($pages,'menuStatus','Y');
	// @todo when menu filtered, does not retain pages with broken paths, problem for menus that should probably still show them
	$menu  = importMenuFromPages($pages);
	return $menu;
}

/**
 * build a nested menu array from pages array parent/menuorder
 * ( optionally filter by menustatus )
 * createa a parent hash table with references
 * builds nested array tree
 * recurses over tree and add depth, order, numchildren, (path and url)
 * @todo
 * create flat hash ref array for fast hash lookups ( like parent hash array but with refs to tree )
 * flat array ust be storable and restorable, refs must be rebuilt after form input and read from file.
 */
function importMenuFromPages($pages = null, $flatten = false){
	
	// if importing from 3.3.x do not generate tree, generate flat menu instead for legacy menuordering by menuOrder
	if($flatten) $parents = array(''=>$pages);
	else         $parents = getParentsHashTable($pages, true , true);
	
	$tree    = buildTreewHash($parents,'',false,true,'url');
	$nestary = recurseJson($tree); // recurse the tree and add stuff
	
	$nesttree[GSMENUNESTINDEX]  = &$tree;
	$nesttree[GSMENUFLATINDEX]  = &$nestary[GSMENUFLATINDEX];
	$nesttree[GSMENUINDEXINDEX] = $nestary[GSMENUINDEXINDEX];
	// buildRefArray();
	return $nesttree;
}

/**
 * builds a flat reference map from a nested tree
 * @unused
 * @param  array $menu     nested array
 * @param  array $flattree destination array
 * @return array           flat indexed array
 */
function buildRefArrayRecursive($menu,$flattree = array()){
	foreach($menu as $item){
		$flatree['item']['id'] = &$item;
		if(isset($item['children'])) buildRefArrayRecursive($item['children'],$flattree);
	}

	return $flattree;
}

/**
 * get nested array reference from flat index 
 * using flat index to resolve to nested
 * uses resolve_tree() to resolve flat path to nested
 * @
 * @param  array &$menu  nested array
 * @param  str $id       flat index
 * @return array         reference to nested subarray
 */
function &getRefArray(&$menu,$id){
	if(isset($menu[GSMENUFLATINDEX]) && isset($menu[GSMENUFLATINDEX][$id])) return $menu[GSMENUFLATINDEX][$id];
	$index = $menu[GSMENUINDEXINDEX][$id];
	$index = trim($index,'.');
	$index = str_replace('.','.children.',$index);
	$ref = &resolve_tree($menu[GSMENUNESTINDEX],explode('.',$index));
	return $ref;
}

/**
 * Build flat reference array onto nested tree
 * requires INDEX subarray
 * adds FLAT subarray references
 * @param  array &$menu  nested tree array
 * @return array         nested tree with flat reference array added
 */
function buildRefArray(&$menu){
	foreach($menu[GSMENUINDEXINDEX] as $key=>$index){
		$index = trim($index,'.');
		$index = str_replace('.','.children.',$index);
		// _debugLog($key,$index);
		$ref = &resolve_tree($menu[GSMENUNESTINDEX],explode('.',$index));
		if(isset($ref)) $menu[GSMENUFLATINDEX][$key] = &$ref;
	}
	return $menu;
}

/**
 * save menu file
 * @param  str $menuid menu id
 * @param  array $data array of menu data
 * @return bool        success
 */
function menuSave($menuid,$data){
	$menufileext = '.json';
	if(isset($data[GSMENUFLATINDEX])) unset($data[GSMENUFLATINDEX]);
	$status = save_file(GSDATAOTHERPATH.'menu_'.$menuid.$menufileext,json_encode($data));
	return $status;
}

/**
 * read menu file
 * @param  str $menuid menu id
 * @return array menudata
 * @return array menudata
 */
function menuRead($menuid){
	$menufileext = '.json';
	$menu     = read_file(GSDATAOTHERPATH.'menu_'.$menuid.$menufileext);
	$menu     = json_decode($menu,true);
	return $menu;
}

/**
 * save basic json menu
 * convert basic menu string to gs menu array
 * @param  str $jsonmenu json string of menu data
 * @return array gs menu data array
 */
function newMenuSave($menuid,$menu){
	$menu     = json_decode($menu,true);
	$menudata = recurseJson($menu); // build full menu data
	$menudata[GSMENUNESTINDEX] = $menu;
	_debugLog(__FUNCTION__,$menu);
	_debugLog(__FUNCTION__,$menudata);
	if(getDef('GSMENULEGACY',true)) exportMenuToPages($menudata); // legacy page support
	// $menudata[GSMENUNESTINDEX]=$menudata[GSMENUFLATINDEX];
	return menuSave($menuid,$menudata);
}

function MenuOrderSave_OLD(){
	$menuOrder = explode(',',$_POST['menuOrder']);
	$priority = 0;
	foreach ($menuOrder as $slug) {
		$file = GSDATAPAGESPATH . $slug . '.xml';
		if (file_exists($file)) {
			$data = getPageXML($slug);
			if ($priority != (int) $data->menuOrder) {
				unset($data->menuOrder);
				$data->addChild('menuOrder')->addCData($priority);
				XMLsave($data,$file);
			}
		}
		$priority++;
	}
	create_pagesxml('true');
	$success = i18n_r('MENU_MANAGER_SUCCESS');
	return $success;
}


/**
 * 
 * recurse a json menu object/array and add relative fields to it
 * 
 * adds url, path, depth, index, order nesting information
 * reindex as assoc array using 'id'
 * children subarray has same structure as roots
 *
 * input array (REF)
 *
 *  [0] = array(
 *    'id' => 'index',
 *    'children' => array()
 *  );
 * 
 * output array (REF)
 * 
 * ['index'] = array(
 *  'id' => 'index',
 *  'data' =>  array(
 *    'url' => '/dev/getsimple/master/',
 *    'path' => 'index',
 *    'depth' => 1
 *    'index' => 9,
 *    'order' => 7
 *  ),
 *  'parent' => '',
 *  'numchildren' => 1,
 *  'children' => array()
 * );
 *
 * returns a flat array containing flat references
 * and an indices array containing indexes and nested array paths
 * 
 * @param  array  &$array    reference to array, so values can be refs
 * @param  str     $parent   parent for recursion
 * @param  integer $depth    depth for recursion
 * @param  integer $index    index ofr recursion
 * @param  array   &$indexAry indexarray reference for recursion
 * @return array             new array with added data
 */
function recurseJson(&$array,$parent = null,$depth = 0,$index = 0,&$indexAry = array()){
	// debugLog(__FUNCTION__ . ' ' . count($array));
	
	// use temporary index to store currentpath
	if(!isset($indexAry['currpath'])) $indexAry['currpath'] = array();
	
	static $index;
	if($depth == 0) $index = 0;
	$order = 0;
	$depth++;
	
	array_push($indexAry['currpath'],$parent);

	foreach($array as $key=>&$value){
		if(isset($value['id'])){
			$order++;
			$index++;
			
			// skip rekeyed copies		
			// if(isset($value['data'])) continue;

			$id = $value['id'];

			$value['parent']        = isset($parent) ? $parent : '';
			$value['data']          = array();
			$value['data']['url']   = generate_url($id);
			$value['data']['path']  = generate_permalink($id,'%path%/%slug%');
			$value['data']['depth'] = $depth;
			$value['data']['index'] = $index;
			$value['data']['order'] = $order;

			// rekey array
			$array[$id]  = $value;
			// unset($array[$key]);

			$indexAry['flat'][$id] = &$array[$id]; // flat cannot be saved to json because of references
			// create a indices to paths, so we can rebuild flat array references on json load, if serializing this is not needed
			$indexAry['indices'][$id] = implode('.',$indexAry['currpath']).'.'.$id;

			if(isset($array[$id]['children'])){
				$array[$id]['numchildren'] = count($array[$id]['children']);
				$children = &$array[$id]['children'];
				recurseJson($children,$id,$depth,$index,$indexAry);
			} else $array[$id]['numchildren'] = 0;
		}

	}

	array_pop($indexAry['currpath']);
	if(!$indexAry['currpath']) unset($indexAry['currpath']);
	return $indexAry;
}

/**
 * save menu data to page files, refresh page cache
 * @uses  saveMenuDataToPage
 * @param  array $menu menu array
 */
function exportMenuToPages($menu){
	$pages = getPages();
	if(!$menu){ 
		debugLog('no menu to save');
		return;
	}
	foreach($pages as $page){
		$id = $page['url'];
		// if not in menu wipe page data
		if(!isset($menu['flat'][$id])){
			saveMenuDataToPage($id);
			continue;
		}
		// debugLog($menu['flat'][$id]);
		$parent = $menu['flat'][$id]['parent'];
		$order  = $menu['flat'][$id]['data']['index'];
		saveMenuDataToPage($id,$parent,$order);
	}

	// regen page cache
	create_pagesxml('true');
}

/**
 * set page data menu information
 * update page with parent and order, only if it differs
 * @param  str $pageid page id to save
 * @param  str $parent page parent
 * @param  int $order page order
 * @return  bool success
 */
function saveMenuDataToPage($pageid,$parent = '',$order =''){
	// do not save page if nothing changed
	if((string)returnPageField($pageid,'parent') == $parent && (int)returnPageField($pageid,'menuOrder') == $order) return;

	$file = GSDATAPAGESPATH . $pageid . '.xml';
	if (file_exists($file)) {
		$data = getPageXML($pageid);
		$data->parent->updateCData($parent);
		$data->menuOrder->updateCData($order);
		return XMLsave($data,$file);
	}
}

// passes page id to callouts, can be used on native parenthash arrays like parenthashtable, where children are values of page references or array, keys are parents
// array(
// 'parent' => array(
// 	 &$pagesArray['child1'],
// 	),
// )
function getTree($parents,$key = '',$str='',$level = 0,$index = 0, $filter = null, $inner = 'treeCalloutInner', $outer = 'treeCalloutOuter'){
	// _debugLog($key,$level);
	static $index;
	if($level == 0) $index = 0;
	$level++;
	$order = 0;
	$str .= $outer('',$level,$index,$order);
	foreach($parents[$key] as $parent=>$child){
		$order++;
		if(isset($filter) && function_exists($filter) && $filter($child,$level,$index)) continue;
		$index++;
		$str .= $inner($child['url'],$level,$index,$order);
		if(isset($parents[$parent])) {
			$str.= getTree($parents,$parent,'',$level+1,0,$filter,$inner,$outer);
		}
		$str .= $inner($child['url'],$level,$index,$order,false);
	}
	$str .= $outer($child['url'],$level,$index,$order,false);
	return $str;
}

// passes page id to callouts, use for your own arrays, also adds depth, index, and order if not exist, can be used on arrays with nested trees assumes
// 'children' subkey
// 
// array(
// 	array(
// 	 'id' => [page or menu array],
// 	 'children' => array()
// 	),
// )
function getMenuTree($parents,$str='',$level = 0, $index = 0, $filter = null, $inner = 'treeCalloutInner',$outer = 'treeCalloutOuter'){
	if(!$parents) return;
	static  $index;
	if($level == 0) $index = 0;
	$order = 0;
	$str .= $outer('',$level,$index,$order);
	foreach($parents as $key=>$parent){
		if(!isset($parent['id'])) continue;
		if(isset($filter) && function_exists($filter) && $filter($id)) continue;
		// use internal ordering if not present in array
		$level = isset($parent['depth']) ? $parent['depth'] : $level+1;
		$index = isset($parent['index']) ? $parent['index'] : $index+1;
		$order = isset($parent['order']) ? $parent['order'] : $order+1;
		
		$str .= $inner($parent['id'],$level,$index,$order);
		if(isset($parent['children'])) {
			$str.= getMenuTree($parent['children'],'',$level);
		}
		$level--;
		$str .= $inner($parent['id'],$level,$index,$order,false);
	}
	$str .= $outer('',$level,$index,$order,false);
	return $str;
}

// minimal tree output
// passes page to callouts, assumes everything you need in the array, to be used with menu/ index/ or ref arrays
function getMenuTreeMin($parents,$str='',$inner = 'treeCalloutInner',$outer = 'treeCalloutOuter'){
	if(!$parents) return;
	$str .= $outer();
	foreach($parents as $key=>$parent){
		if(!isset($parent['id'])) continue;
		$str .= $inner($parent);
		if(isset($parent['children'])) {
			$str.= getMenuTreeMin($parent['children'],'',$inner,$outer);
		}
		$str .= $inner($parent,false);
	}
	$str .= $outer(null,false);
	return $str;
}

function treeCalloutInner($id,$level,$index = 1,$order = 0,$open = true){
	$child     = getPage($id);
	$menuTitle = getPageMenuTitle($child['url']);
	$pageTitle = truncate($child['title'],30);
	// $pageTitle = '<strong>'.$child['title'].'.'.$level.'.'.$order.'</strong>';
	$pageTitle = $pageTitle.'.'.$child['menuOrder'] .'.'.$child['menuStatus'];
	// _debugLog($id,$child['menuStatus']);
	$class     = $child['menuStatus'] == 'Y' ? ' menu' : ' nomenu';

	$str = $open ? '<li class="dd-item clearfix" data-id="'.$child['url'].'"><div class="dd-itemwrap '.$class.'"><div class="dd-handle"> '.$menuTitle.'<div class="itemtitle"><em>'.$pageTitle.'</em></div></div></div>' : '</li>';
	return $str;
}

function treeCalloutOuter($id,$level,$index = 1,$order = 0,$open = true){
	return $open ? '<ol id="" class="dd-list">' : '</ol>';
}

function treeFilterCallout($id,$level,$index,$order){
	$child = getPage($id);
	return $child['menuStatus'] !== 'Y';
}

function menuCalloutInner($page,$open = true){
	if(!$open) return '</li>';

	$depth = $page['data']['depth'];
	$page = getPage($page['id']);
	
	$menutext = $page['menu'] == '' ? $page['title'] : $page['menu'];
	$menutitle = $page['title'] == '' ? $page['menu'] : $page['title'];
	$class = $page['parent'] . ' D' . $depth; 

	$str = '<li data-id="'.$page['url'].'" class="'.$class.'">';
	$str .= '<a href="'. find_url($page['url']) . '" title="'. encode_quotes(cl($menutitle)) .'">'.strip_decode($menutext).'</a>'."\n";

	return $str;
}

function menuCalloutOuter($page = null,$open = true){
	return $open ? '<ul id="">' : '</ul>';
}

function selectCalloutInner($id, $level, $index, $order, $open = true){
	if(!$open) return;
	$page = getPage($id);
	$disabled = $page['menuStatus'] == 'Y' ? 'disabled' : '';
	return '<option id="'.$id.'" '.$disabled. '>' .str_repeat('-',$level-1) . $page['title']. '</option>';
}

function selectCalloutOuter(){

}

function pagesToMenuOLD($pages,$parent = ''){
	static $array;
	if($parent == '') $array = array();
	foreach($pages[$parent] as $key=>$page){
		$newparent = $page['title'];
		_debugLog($key,$page);
		// _debugLog($key,$newparent);
		if($newparent != '' && isset($pages[$newparent])) $array[] = array('id' => $page['url'],'children' => pagesToMenu($pages,$newparent));
		else $array[]['id'] = $page['url'];
	}
	
	return $array;
}

function pagesToMenu($pages,$parent = 'index'){
	static $array;
	if($parent == '') $array = array();
	debugLog($parent);

	debugLog($pages[$parent]);
	foreach($pages[$parent] as $key=>$page){
		$newparent = $page['url'];
		// debugLog($key,$newparent);
		// _debugLog($key,$newparent);
		if(isset($pages[$newparent])){
			_debugLog($newparent,count($pages[$newparent]));
			$newarray = pagesToMenu($pages,$newparent);
			// $array[$parent]['children'][] = $newarray;
			// 
			$array[$parent][] = array('id' => $parent,'children' => $newarray);
		} 
		else $array[$parent][] = array('id' => $parent);
	}
	
	return $array;
}

// @todo build a nested tree array from flat array with `parent` and children array, children key is `children`
function buildTree(array $elements, $parentId = '') {
    $branch = array();

    foreach ($elements as $element) {
        if ($element['parent'] == $parentId) {
            $children = buildTree($elements, $element['url']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }

    return $branch;
}

/**
 * builds nested array from parent hash table array
 * 
 * @param  array  $elements     source array
 * @param  string  $parentId    starting parent
 * @param  boolean $preserve    true, preserve all fields, else only id and children are kept
 * @param  boolean $assoc       return namedarray using $idKey for keys ( breaks dups )
 * @param  string  $idKey       key for id field
 * @param  string  $id          key for output id
 * @param  string  $childrenKey key for children sub array
 * @return array                new array
 */
function buildTreewHash($elements, $parentId = '', $preserve = false, $assoc = true, $idKey = 'id', $id = 'id',$childrenKey = 'children') {
    $branch = array();
	$args   = func_get_args();

    foreach ($elements[$parentId] as $element) {
       	
       	// if missing index field skip, bad record
       	if(!isset($element[$idKey])) continue;
       	
       	$elementKey = $element[$idKey];
    	// use index as keys else int
       	$branchKey = $assoc ? $elementKey : count($branch);

       	// if element is a parent recurse its children
        if (isset($elements[$elementKey])) {
			$args[1]  = $elementKey;
			$children = call_user_func_array(__FUNCTION__,$args);
            // $children = buildTreewHash($elements, $elementKey);

            if ($children) {
            	// element has children, add children to branches
                // if preserving all fields
                if($preserve){
                	$element[$childrenKey] = $children;
                	$branch[$branchKey] = $element;
                }	
                else $branch[$branchKey] = array($id => $elementKey, $childrenKey => $children);
            }
        } 
        else{
        	// else only add element
        	if($preserve) $branch[$branchKey] = $element;
        	else $branch[$branchKey] = array($id => $elementKey);
        }	
    }

    return $branch;
}

/**
 * resolves a tree path to nested array
 * @since 3.4
 * @param  array &$tree array reference to tree
 * @param  array $path  array of path to desired branch/leaf
 * @return array        subarray from tree matching path
 */
function &resolve_tree(&$tree, $path) {
	if(empty($path)) return $tree;
	return resolve_tree($tree[$path[0]], array_slice($path, 1));
	// @todo why does this not work the same? must be some odd reference pass issue
	// return empty($path) ? $tree : resolve_tree($tree[$path[0]], array_slice($path, 1));
}

/**
 * shortcut to get page menu title
 * if menu title not explicitly set fallback to page title
 * @param  str $slug page id
 * @return str page title
 */
function getPageMenuTitle($slug){
	$page = getPage($slug);
	return ($page['menu'] == '' ? $page['title'] : $page['menu']);
}


/* ?> */
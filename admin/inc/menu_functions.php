<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Getsimple Menu Functions
 * @package GetSimple
 * @subpackage menus_functions.php
 */


function getMenuFromPages($inmenu = true){
	/**
	 * get pages ( filtered by menustatus )
	 * create parent hash table with references
	 * build nested array tree
	 * recurse over tree and add depth, order, numchildren, (path and url)
	 * @todo
	 * create flat hash ref array for fast hash lookups ( like parent hash array but with refs to tree )
	 * flat array ust be storable and restorable, refs must be rebuilt after form input and read from file.
	 */
	$pagesSorted = filterKeyValueMatch(sortCustomIndex(getpages(),'menuOrder'),'menuStatus','Y');
	$parents     = getParentsHashTable($inmenu ? $pagesSorted : null, true , true);
	$flattree    = buildTreewHash($parents,'',false,true,'url');
	// @todo does not retain pages with broken paths, problem for menus that should probably still show them.
	$indexAry    = recurseJson($flattree);
	$tree['NESTED'] = &$flattree;
	// $tree['FLAT']   = &$indexAry['flat'];
	$tree['INDEX']  = $indexAry['indices'];
	return $tree;
}


function buildRefArrayRecursive($menu,$flattree = array()){
	// static $flattree;
	// if(!$flattree) $flattree = array();
	foreach($menu as $item){
		$flatree['item']['id'] = &$item;
		if(isset($item['children'])) buildRefArrayRecursive($item['children'],$flattree);
	}

	return $flattree;
}


function &getRefArray(&$menu,$id){
	if(isset($menu['FLAT']) && isset($menu['FLAT'][$id])) return $menu['FLAT'][$id];
	$index = $menu['INDEX'][$id];
	$index = trim($index,'.');
	$index = str_replace('.','.children.',$index);
	$ref = &resolve_tree($menu['NESTED'],explode('.',$index));
	return $ref;
}

function buildRefArray(&$menu){
	foreach($menu['INDEX'] as $key=>$index){
		$index = trim($index,'.');
		$index = str_replace('.','.children.',$index);
		// _debugLog($key,$index);
		$ref = &resolve_tree($menu['NESTED'],explode('.',$index));
		if(isset($ref)) $menu['FLAT'][$key] = &$ref;
	}
	return $menu;
}

function initMenus(){
	global $menucache;
	$menucache = array();
}

function saveMenu($menuid,$data){
	$menufile = '.json';
	if(isset($data['FLAT'])) unset($data['FLAT']);
	$status   = save_file(GSDATAOTHERPATH.'menu_'.$menuid,json_encode($data));
	return $status;
}

function readMenu($menuid){
	$menufile = '.json';
	$menu     = read_file(GSDATAOTHERPATH.'menu_'.$menuid);
	$menu     = json_decode($menu,true);
	return $menu;
}

function menuOrderSave(){
	global $pagesArray;
	$menuOrder = json_decode($_POST['menuOrder'],true);
	// debugLog($menuOrder);
	recurseJson($menuOrder);
	// debugLog($menuOrder);
	create_pagesxml('true');
	return $menuOrder;
}

function recurseJson(&$array,$parent = null,$depth = 0,$index = 0,&$indexAry = array()){
	debugLog(__FUNCTION__ . ' ' . count($array));
	
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

			if(isset($parent)) $value['parent'] = $parent;
			
			// storeMenuInPage($value['id'],(isset($parent) ? $parent : ''),$index);

			$value['data']          = array();
			$value['data']['url']   = generate_url($value['id']);
			$value['data']['path']  = generate_permalink($value['id'],'%path%/%slug%');
			$value['data']['depth'] = $depth;
			$value['data']['index'] = $index;
			$value['data']['order'] = $order;
			
			$indexAry['flat'][$value['id']] = &$value;
			$indexAry['indices'][$value['id']] = implode('.',$indexAry['currpath']).'.'.$value['id'];

			if(isset($value['children'])){
				$value['numchildren'] = count($value['children']);
				$children = &$value['children'];
				recurseJson($children,$value['id'],$depth,$index,$indexAry);
			} else $value['numchildren'] = 0;
		}
	}

	array_pop($indexAry['currpath']);
	if(!$indexAry['currpath']) unset($indexAry['currpath']);
	return $indexAry;
}

function storeMenuInPage($pageid,$parent,$order){
	
	// debugLog(func_get_args());
	// debugLog(returnPageField($pageid,'url'));
	// debugLog(returnPageField($pageid,'parent'));
	// debugLog(returnPageField($pageid,'menuOrder'));
	if((string)returnPageField($pageid,'parent') == $parent && (int)returnPageField($pageid,'menuOrder') == $order) return;
	$file = GSDATAPAGESPATH . $pageid . '.xml';
	if (file_exists($file)) {
		$data = getPageXML($pageid);
		$data->parent->updateCData($parent);
		$data->menuOrder->updateCData($order);
		XMLsave($data,$file);
	}
}

function legacyMenuOrderSave(){
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

function getPageMenuTitle($slug){
	$page = getPage($slug);
	return ($page['menu'] == '' ? $page['title'] : $page['menu']);
}

// can be used on native arrays like parenthashtables
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
	$child = getPage($id);
	$debug = '<strong>'.$index.'.'.$level.'.'.$order.'</strong>';
	$str = $open ? '<li class="dd-item clearfix" data-id="'.$child['url'].'"><div class="dd-handle"> '.getPageMenuTitle($child['url']).' <em>[' .$child['url'].']</em><div class="itemtitle"><em>'.$child['title'].'</em></div></div>' : '</li>';
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
 * builds nested array from parent hash array
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


function &resolve_tree(&$tree, $path) {
	if(empty($path)) return $tree;
	return resolve_tree($tree[$path[0]], array_slice($path, 1));
	// @todo why does this not work the same? very odd
	// return empty($path) ? $tree : resolve_tree($tree[$path[0]], array_slice($path, 1));
}

function path(&$array, $path, $default = NULL, $delimiter = '.')
{
    if ( ! is_array($array))
    {
        // This is not an array!
        return $default;
    }

    if (is_array($path))
    {
        // The path has already been separated into keys
        $keys = $path;
    }
    else
    {
        if (array_key_exists($path, $array))
        {
            // No need to do extra processing
            return $array[$path];
        }

        if ($delimiter === NULL)
        {
            // Use the default delimiter
            $delimiter = $delimiter;
        }

        // Remove starting delimiters and spaces
        $path = ltrim($path, "{$delimiter} ");

        // Remove ending delimiters, spaces, and wildcards
        $path = rtrim($path, "{$delimiter} *");

        // Split the keys by delimiter
        $keys = explode($delimiter, $path);
    }

    do
    {
        $key = array_shift($keys);

        if (ctype_digit($key))
        {
            // Make the key an integer
            $key = (int) $key;
        }

        if (isset($array[$key]))
        {
            if ($keys)
            {
                if (is_array($array[$key]))
                {
                    // Dig down into the next part of the path
                    $array = $array[$key];
                }
                else
                {
                    // Unable to dig deeper
                    break;
                }
            }
            else
            {
                // Found the path requested
                return $array[$key];
            }
        }
        elseif ($key === '*')
        {
            // Handle wildcards

            $values = array();
            foreach ($array as $arr)
            {
                if ($value = path($arr, implode('.', $keys)))
                {
                    $values[] = $value;
                }
            }

            if ($values)
            {
                // Found the values requested
                return $values;
            }
            else
            {
                // Unable to dig deeper
                break;
            }
        }
        else
        {
            // Unable to dig deeper
            break;
        }
    }
    while ($keys);

    // Unable to find the value requested
    return $default;
}

/* ?> */
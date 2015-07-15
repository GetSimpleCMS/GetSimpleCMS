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
 * imports menu from pages flat legacy style, menuOrder sorted only
 * @since  3.4
 * @return array new menu sorted by menuOrder
 */
function importLegacyMenuFlat(){
    $pages = getPagesSortedByMenu();
    $pages = filterKeyValueMatch($pages,'menuStatus','Y');
    $menu  = importMenuFromPages($pages,true);
    return $menu; 
}

/**
 * imports menu from pages nested tree style, parent, title, menuorder sorted
 * @since  3.4
 * @return array new menu nested tree sorted by heirarchy and menuOrder
 */
function importLegacyMenuTree(){
    $pages = getPagesSortedByMenuTitle();
    // $pages = filterKeyValueMatch($pages,'menuStatus','Y');
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
 * @since  3.4
 * @param  array $pages pages collection to convert to menu
 * @param  bool  $flatten if true return menu as flat not nested ( legacy style )
 */
function importMenuFromPages($pages = null, $flatten = false){
    
    // if importing from 3.3.x do not generate tree, generate flat menu instead for legacy menuordering by menuOrder
    if($flatten) $parents = array(''=>$pages);
    else $parents = getParentsHashTable($pages, true , true); // get parent hash table of pages, useref, fixoprphans
    
    $tree    = buildTreewHash($parents,'',false,true,'url'); // get a nested array from hashtable, from root, norefs, assoc array
    // debugLog($tree);
    $flatary = recurseUpgradeTree($tree); // recurse the tree and add stuff to $tree, return flat reference array
    // debugLog($flatary);

    $nesttree[GSMENUNESTINDEX]  = &$tree; //add tree array to menu
    $nesttree[GSMENUFLATINDEX]  = &$flatary[GSMENUFLATINDEX]; // add flat array to menu
    $nesttree[GSMENUINDEXINDEX] = $flatary[GSMENUINDEXINDEX]; // add index array to menu
    // debugLog($nesttree);
    
    return $nesttree;
}

/**
 * builds a nested array from a parent hash table array
 *
 * input, parent=>children hash table
 * ['parentid'] = array('child1'=>(data),'child2'=>(data))
 *
 * output (nested)
 * ['parentid']['children']['child1'][data,childen]
 *
 * @since 3.4
 * @param  array   $elements    source array, parent hash table with or without child data
 * @param  string  $parentId    starting parent, root ''
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
            // recurse elements children
            $children = call_user_func_array(__FUNCTION__,$args);

            // if element has children, add children to branches
            if ($children) {
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
 * recurse a nested parent=>child array and add heirachy information to it
 * 
 * modifies passed nested array by reference, returns flat array with references
 * 
 * handles non assoc arrays with id field
 * 
 * add adjacency info, depth, index, order nesting information
 * adds pathing info, parent, numchildren, and children subarray
 * 
 * reindexes as assoc array using 'id'
 * 
 * children subarray has same structure as roots
 *
 * input array (&REF)
 *
 *  [0] = array(
 *    'id' => 'index',
 *    'children' => array()
 *  );
 * 
 * output array (&REF)
 * 
 * ['index'] = array(
 *  'id' => 'index',
 *  'data' =>  array(
 *    'url' => '/dev/getsimple/master/',
 *    'path' => 'index',
 *    'depth' => 1,
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
function recurseUpgradeTree(&$array,$parent = null,$depth = 0,$index = 0,&$indexAry = array()){
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

            // $value['parent']        = isset($parent) ? $parent : '';
            $value['data']          = array();
            $value['data']['depth'] = $depth;
            $value['data']['index'] = $index;
            $value['data']['order'] = $order;

            recurseTreeCallout($value,$id,$parent);

            // rekey array, needed for non assoc arrays
            // $array[$id]  = $value;
            // unset($array[$key]); // remove old key

            $indexAry['flat'][$id] = &$array[$id]; // flat cannot be saved to json because of references
            // create a indices to paths, so we can rebuild flat array references on json load, if serializing this is not needed
            $indexAry['indices'][$id] = implode('.',$indexAry['currpath']).'.'.$id;

            if(isset($array[$id]['children'])){
                $array[$id]['numchildren'] = count($array[$id]['children']);
                $children = &$array[$id]['children'];
                recurseUpgradeTree($children,$id,$depth,$index,$indexAry); // @todo replace with __FUNCTION__
            } else $array[$id]['numchildren'] = 0;
        }

    }

    array_pop($indexAry['currpath']);
    if(!$indexAry['currpath']) unset($indexAry['currpath']);
    return $indexAry;
}

/**
 * callout for recurse, to add custom data
 * @since  3.4
 * @param  array &$value ref array
 * @param  string $id     $id of value
 * @param  string $parent parent of value
 * @return array          copy of array
 */
function recurseTreeCallout(&$value,$id = '',$parent = ''){
    $value['data']['url']   = generate_url($id);
    $value['data']['path']  = generate_permalink($id,'%path%/%slug%');
    return $value; // non ref
}


/**
 * OUTPUT BUILDER FUNCTIONS
 */

/**
 * RECURSIVE TREE ITERATOR PARENT HASH TABLE
 * tree output from parent hashtable array
 * get tree from parent->child parenthashtable, where child is a pagesArray ref or copy of pages, heirachy info is ignored
 * passes page id ['url'] to callouts, can be used on native parenthash arrays like parenthashtable, 
 * where children are values of page references or array, keys are parents
 * 
 * generates $level, $index, $order for you
 * 
 * array(
 *     'parent' => array(
 *         &$pagesArray['url'=>'child1'],
 *      ),
 * )
 *
 * itemcallout($id,$level,$index,$order,$open)
 * 
 * @param  array   $parents array of parents
 * @param  string  $key     starting parent key
 * @param  string  $str     str for recursion append
 * @param  integer $level   level for recursion incr
 * @param  integer $index   index for recursion incr
 * @param  str     $filter  filter callout functionname
 * @param  string  $inner   inner element callout functionname
 * @param  string  $outer   outer element callout functionname
 * @return str              output string
 */
function getTree($parents,$key = '',$level = 0,$index = 0, $inner = 'treeCalloutInner', $outer = 'treeCalloutOuter', $filter = null){
    if(!$parents) return;
    static $index;
    if($level == 0) $index = 0;
    $order = 0;
    $str = '';
    $str .= callIfCallable($outer,null,true,$level,$index,$order);

    foreach($parents[$key] as $parent=>$child){
        if(callIfCallable($filter) === true) continue;
        
        $level = $level+1;
        $index = $index+1;
        $order = $order+1;

        $str .= callIfCallable($inner,$child,true,$level,$index,$order);

        if(isset($parents[$parent])) {
            $str.= getTree($parents,$parent,$level,$index,$inner,$outer,$filter);
        }
        $level--;
        $str .= callIfCallable($inner,$child,false,$level,$index,$order);
    }
    $str .= callIfCallable($outer,null,false,$level,$index,$order);
    return $str;
}

/**
 * RECURSIVE TREE ITERATOR NESTED ARRAY
 * tree output nested from children array
 * get tree from nested tree array with or without hierarchy info
 * passes page id ['id'] to callouts, can be used on menuarrays where children are subarrays with 'id' and 'children' fields
 * 
 * supports adjacency info, but will calculate $level, $index, $order for you if it does not exist
 * 
 * passes page id to callouts, use for your own arrays, also adds depth, index, and order if not exist, can be used on arrays with nested trees assumes
 * 'children' subkey
 * 
 * array(
 *   'id' => 'parent',
 *   'children' => array(
 *      'id' => 'child1',
 *      'children' => array()
 *      'depth' => (optional)
 *    ),
 * )
 *
 * itemcallout($child,$level,$index,$order,$open);
 *  
 * @param  array   $parents array of parents
 * @param  string  $key     starting parent key
 * @param  string  $str     str for recursion append
 * @param  integer $level   level for recursion incr
 * @param  integer $index   index for recursion incr
 * @param  str     $filter  filter callout functionname
 * @param  string  $inner   inner element callout functionname
 * @param  string  $outer   outer element callout functionname
 * @return str              output string
 */
function getMenuTreeExtra($parents,$level = 0, $index = 0, $inner = 'treeCalloutInner', $outer = 'treeCalloutOuter', $filter = null){
    if(!$parents) return;
    static  $index;
    // if($level == 0) $index = 0;

    $order = 0;
    $str   = '';
    $str  .= callIfCallable($outer,null,true,$level,$index,$order);

    // if a page subarray was passed
    if(isset($parents['id']) && isset($parents['children'])) $parents = $parents['children'];

    foreach($parents as $key=>$child){
        if(!isset($child['id'])) continue;
        if(callIfCallable($filter) === true) continue;
        // use internal ordering if not present in array
        // @todo menus use datasubkeys, incompatible
        $index = isset($child['index']) ? $child['index'] : $index+1;
        $level = isset($child['depth']) ? $child['depth'] : $level+1;
        $order = isset($child['order']) ? $child['order'] : $order+1;
        
        $str .= callIfCallable($inner,$child,true,$level,$index,$order);
        if(isset($child['children'])) {
            $str.= getMenuTreeExtra($child['children'],$level);
        }
        $level--;
        $str .= callIfCallable($inner,$child,false,$level,$index,$order);
    }
    $str .= callIfCallable($outer,null,false,$level,$index,$order);
    return $str;
}

/**
 * RECURSIVE TREE ITERATOR NESTED ARRAY
 * minimal tree output from nested children array
 * assumes `id` and `children` subkey
 * passes page array to callouts, assumes everything you need is in the array, to be used with menu/ index/ or ref arrays
 * does not calculate heirachy data or use it
 *
 * array(
 *   'id' => 'parent',
 *   'children' => array(
 *      'id' => 'child1',
 *      'children' => array()
 *      'depth' => (optional)
 *    ),
 * )
 * 
 * itemcallout($page,$open);
 *
 * @param array   $parents parents array
 * @param str     $str     recursive str for append
 * @param string  $inner   inner element callout functionname
 * @param string  $outer   outer element callout functionname
 * @return str             output string
 */
function getMenuTree($parents,$inner = 'treeCalloutInner',$outer = 'treeCalloutOuter',$filter = null){
    if(!$parents) return;
    $str  = '';
    $str .= callIfCallable($outer);
    
    // if a page subarray was passed
    if(isset($parents['id']) && isset($parents['children'])) $parents = $parents['children'];

    foreach($parents as $key=>$child){
        debugLog($child);
        if(!isset($child['id'])) continue;
        // if(callIfCallable($filter) === true) continue;
        $str .= callIfCallable($inner,$child);
        if(isset($child['children'])) {
            $str.= getMenuTree($child['children'],$inner,$outer,$filter);
        }
        $str .= callIfCallable($inner,$child,false);
    }
    $str .= callIfCallable($outer,null,false);
    return $str;
}

/**
 * generic tree outer callout function for recursive tree functions
 * outputs a basic list
 * @since  3.4
 * @param  array  $item item to feed this recursive iteration
 * @param  boolean $open is this nest open or closing
 * @return str     string to return to recursive callee
 */
function treeCalloutOuter($item = null,$open = true){
    return $open ? "<ul>" : "</ul>";
}

/**
 * generic tree inner callout function for recursive tree functions
 * outputs a basic list
 * @since  3.4
 * @param  array  $item item to feed this recursive iteration
 * @param  boolean $open is this nest open or closing
 * @return str     string to return to recursive callee
 */
function treeCalloutInner($item,$open = true){
    if(!isset($item['id'])) $item['id'] = $item['url'];
    $title = $item['id'];
    $title .= debugTreeCallout(func_get_args());
    return $open ? "<li>".$title : "</li>";
}

function debugTreeCallout($args){
    // debugging
    $debug = '';
    $item = $args[0];
    if(!isset($item['data'])){
        $item['data']['depth'] = $args[2];
        $item['data']['index'] = $args[3];
        $item['data']['order'] = $args[4];
        // $debug .= ' [' . $args[2] . ']';
    }
    $debug .= ' [' . $item['data']['index'].' -'.$item['data']['depth'].'.'.$item['data']['order'] . ']';
    return $debug;
}

/**
 * menu manager tree callout function
 * @since  3.4
 * @param  array  $item item to feed this recursive iteration
 * @param  boolean $open is this nest open or closing
 * @return str     string to return to recursive callee
 */
function mmCalloutInner($item,$open = true){
    $page      = is_array($item) && isset($item['id']) ? getPage($item['id']) : getPage($item);
    $menuTitle = getPageMenuTitle($page['url']);
    $pageTitle = truncate($page['title'],30);
    // $pageTitle = '<strong>'.$page['title'].'.'.$level.'.'.$order.'</strong>';
    $pageTitle = $pageTitle.'.'.$page['menuOrder'] .'.'.$page['menuStatus'];
    // _debugLog($page,$page['menuStatus']);
    $class     = $page['menuStatus'] == 'Y' ? ' menu' : ' nomenu';

    $str = $open ? '<li class="dd-item clearfix" data-id="'.$page['url'].'"><div class="dd-itemwrap '.$class.'"><div class="dd-handle"> '.$menuTitle.'<div class="itemtitle"><em>'.$pageTitle.'</em></div></div></div>' : '</li>';
    return $str;
}

/**
 * menu manager tree outer callout function
 * @since  3.4
 * @param  array  $item item to feed this recursive iteration
 * @param  boolean $open is this nest open or closing
 * @return str     string to return to recursive callee
 */
function mmCalloutOuter($page = null,$open = true){
    return $open ? '<ol id="" class="dd-list">' : '</ol>';
}

/**
 * HELPERS
 */

/**
 * shortcut to get pages menu title
 * if menu title not explicitly set fallback to page title
 * @since  3.4
 * @param  str $slug page id
 * @return str page title
 */
function getPageMenuTitle($slug){
    $page = getPage($slug);
    return ($page['menu'] == '' ? $page['title'] : $page['menu']);
}

/**
 * MENU IO FUNCTIONS
 */

/**
 * save menu file
 * remove GSMENUFLATINDEX if it exists ( cannot save refs in json )
 * @since  3.4
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
 * @since  3.4
 * @param  str $menuid menu id
 * @return array menudata
 * @return array menudata
 */
function menuRead($menuid){
    $menufileext = '.json';
    $menu        = read_file(GSDATAOTHERPATH.'menu_'.$menuid.$menufileext);
    $menu        = json_decode($menu,true);
    return $menu;
}


/**
 * save menu data to page files, refresh page cache
 * @since  3.4
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
        if(!isset($menu[GSMENUFLATINDEX][$id])){
            saveMenuDataToPage($id);
            continue;
        }
        // debugLog($menu[GSMENUFLATINDEX][$id]);
        $parent = $menu[GSMENUFLATINDEX][$id]['parent'];
        $order  = $menu[GSMENUFLATINDEX][$id]['data']['index'];
        saveMenuDataToPage($id,$parent,$order);
    }

    // regen page cache
    create_pagesxml('true');
}

/**
 * set page data menu information
 * update page with parent and order, only if it differs
 * @since  3.4
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

/**
 * GARBAGE
 */

/**
 * save basic json menu
 * convert basic menu string to gs menu array
 * @param  str $jsonmenu json string of menu data
 * @return array gs menu data array
 */
function newMenuSave($menuid,$menu){
    $menu     = json_decode($menu,true);
    $menudata = recurseTree($menu); // build full menu data
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

function treeFilterCallout($id,$level,$index,$order){
    $child = getPage($id);
    return $child['menuStatus'] !== 'Y';
}

/* ?> */
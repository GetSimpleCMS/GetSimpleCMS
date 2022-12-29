<?php
# these functions should be in the GetSimple Core:
if (!function_exists('i18n_merge')) {
  function i18n_merge($plugin, $language=null) {
    global $i18n, $LANG;
    return i18n_merge_impl($plugin, $language ? $language : $LANG, $i18n);
  }

  function i18n_merge_impl($plugin, $lang, &$globali18n) { 
    $i18n = array();
    if (!file_exists(GSPLUGINPATH.$plugin.'/lang/'.$lang.'.php')) return false;
    @include(GSPLUGINPATH.$plugin.'/lang/'.$lang.'.php'); 
    if (count($i18n) > 0) foreach ($i18n as $code => $text) {
      if (!array_key_exists($plugin.'/'.$code, $globali18n)) $globali18n[$plugin.'/'.$code] = $text;
    }
    return true;
  }
}

# GetSimple 3.0 function - compatibility
if (!function_exists('i18n')) {
  function i18n($name, $echo=true) {
	  global $i18n, $LANG;
	  if (array_key_exists($name, $i18n)) {
		  $myVar = $i18n[$name];
	  } else {
		  $myVar = '{'.$name.'}';
	  }
    if ($echo) echo $myVar; else return $myVar;
  } 
}

# GetSimple 3.0 function - compatibility
if (!function_exists('i18n_r')) {
  function i18n_r($name) {
    return i18n($name, false);
  }
}

function createSideMenuExt($id, $txt, $action=null, $always=true){
  $current = false;
  if (isset($_GET['id']) && $_GET['id'] == $id && (!$action || isset($_GET[$action]))) {
    $current = true;
  }
  if ($always || $current) {
    echo '<li><a href="loadext.php?id='.$id.($action ? '&amp;'.$action : '').'" '.($current ? 'class="current"' : '').' >'.$txt.'</a></li>';
  }
}

function createNavTabExt($tabname, $id, $txt, $action=null) {
  global $plugin_info;
  $current = false;
  if (basename($_SERVER['PHP_SELF']) == 'loadext.php') {
    $plugin_id = @$_GET['id'];
    if ($plugin_info[$plugin_id]['page_type'] == $tabname) $current = true;
  }
  echo '<li><a href="loadext.php?id='.$id.($action ? '&amp;'.$action : '').'" class="'.$tabname.($current ? ' current' : '').'">'.$txt.'</a></li>';
}

# install loadext.php into /admin if necessary
if(!file_exists(GSADMINPATH.'loadext.php')) {
  if (!copy(GSPLUGINPATH.'i18n_common/loadext.php', GSADMINPATH.'loadext.php')) { /* TODO? */ }
}

# add styles for active tab
add_action('header', 'i18n_common_header');

function i18n_common_header() {
?>
  <style type="text/css">
    #loadext .wrapper .nav li a.current {
      -moz-box-shadow: 2px -2px 2px rgba(0, 0, 0, 0.1);
      background: -moz-linear-gradient(center top , #FFFFFF 3%, #F6F6F6 100%) repeat scroll 0 0 transparent;
      color: #182227;
      font-weight: bold !important;
      text-shadow: 1px 1px 0 #FFFFFF;
    }
  </style>
<?php
} 

function i18n_is_frontend() {
  return basename($_SERVER['PHP_SELF']) == 'index.php';
}

function i18n_is_backend() {
  $admin = defined('GSADMIN') ? GSADMIN : 'admin';
  $pattern = "@/$admin/|\\\\$admin\\\\@";
  return preg_match($pattern,$_SERVER['PHP_SELF']);
}

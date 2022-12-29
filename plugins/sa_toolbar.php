<?php

/*
    revs:
    
    Fix for font end cookie expiring in 1 day, bumped to 180 days
    Added ability to modify top menu, see satb_hook_test()

    todo:
    
    cache menus on backend, so they are all available on the front end always
    do something about session timing out on front end when doing nothing on back end, dev testing etc.
    blind logout, using redirect for now.
    icons line wrap text on IOS, why

*/

/*
* @Plugin Name: sa_toolbar
* @Description: Admin toolbar
* @Version: 1.1
* @Author: Shawn Alverson
* @Author URI: http://tablatronix.com/getsimple-cms/sa-toolbar/
*
* @hook callouts: satb_toolbar_disp
*/

$SATB                = array();
$SATB['PLUGIN_ID']   = "sa_toolbar";
$SATB['PLUGIN_PATH'] = $SITEURL.'plugins/'.$SATB['PLUGIN_ID'].'/';
$SATB['PLUGIN_URL']  = "http://tablatronix.com/getsimple-cms/sa-toolbar-plugin/";
$SATB['owner']       = '';
$SATB['gsback']      = true;


// DEBUGGING GLOBAL
// ----------------------
$SATB['DEBUG'] = false;
// ----------------------

define('SATB_DEBUG',$SATB['DEBUG']);

# get correct id for plugin
$thisfile      = basename(__FILE__, ".php"); // Plugin File
$satb_pname    = 'SA Toolbar';               //Plugin name
$satb_pversion = '1.1';                      //Plugin version
$satb_pauthor  = 'Shawn Alverson';           //Plugin author
$satb_purl     = $SATB['PLUGIN_URL'];        //author website
$satb_pdesc    = 'SA Toolbar';               //Plugin description
$satb_ptype    = '';                         //page type - on which admin tab to display
$satb_pfunc    = '';                         //main function (administration)

# register plugin
register_plugin($thisfile,$satb_pname,$satb_pversion,$satb_pauthor,$satb_purl,$satb_pdesc,$satb_ptype,$satb_pfunc);

  
if(defined('SATB_DEBUG') and SATB_DEBUG == true){
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
}

// INIT

$SA_ADMINPATH = $SITEURL.$GSADMIN.'/';

add_action('logout','satb_logout');

if(sa_tb_user_is_admin()){

    satb_setTbCookie();
    
    $SATB_MENU_ADMIN = array(); // global admin menu
    $SATB_MENU_STATIC = array(); // global toolbar menu
    
    add_action('theme-footer', 'sa_toolbar');
    add_action('index-pretemplate', 'sa_init_i18n');
    if($SATB['gsback'] == true){
        add_action('footer', 'sa_toolbar');
        add_action('admin-pre-header', 'sa_init_i18n');
    }   

    add_action('sa_toolbar_disp','satb_hook_test');
    
    // asset queing
    // use header hook if older than 3.1
    if(floatval(GSVERSION) < 3.1){
        add_action('header', 'sa_tb_executeheader');
        $SATB['owner'] = "SA_tb_";
    }  
    else{ sa_tb_executeheader(); }

} 
else if (satb_checkTbCookie()){ 
    if(floatval(GSVERSION) < 3.1){
        add_action('header', 'sa_tb_executeheader');
        $SATB['owner'] = "SA_tb_";
    }  
    else{ sa_tb_executeheader(); }
    
    add_action('theme-footer', 'sa_toolbar',array(true));
}

// FUNCTIONS
// ----------------------------------------------------------------------------

function satb_logout(){
    // On logout redirect to page before logout redirects to index
    // requires get[toolbar'] presence
    if(isset($_GET['toolbar']) and isset($_GET['close']))   satb_clearTbCookie();
    if(isset($_GET['toolbar']) and isset($_SERVER['HTTP_REFERER'])) redirect($_SERVER['HTTP_REFERER']);
}

function satb_checkTbCookie(){
    satb_debugLog(isset($_COOKIE['GS_ADMIN_TOOLBAR']) and $_COOKIE['GS_ADMIN_TOOLBAR'] == '1'); 
    return isset($_COOKIE['GS_ADMIN_TOOLBAR']) and $_COOKIE['GS_ADMIN_TOOLBAR'] == '1';
}

function satb_setTbCookie(){
    // set cookie to 180 days
    setcookie('GS_ADMIN_TOOLBAR', 1, time() + 15552000,'/');
}

function satb_clearTbCookie(){
    setcookie('GS_ADMIN_TOOLBAR', 'null', time() - 3600,'/');   
}


function satb_debugLog(){
    GLOBAL $debugLogFunc;
        
    if(!defined('SATB_DEBUG') or SATB_DEBUG != true) return;
    if(function_exists('_debugLog')){
        $debugLogFunc = __FUNCTION__;
        _debugLog($args = func_get_args());
    } else {
        $args = func_get_args();
        $args = is_array($args) ? print_r($args,true) : $args;
        debugLog($args);
    }   
}


function sa_init_i18n(){
    global $LANG;
        // PRELOAD DEFAULT LANG FILES HERE
        // i18n_merge('anonymous_data') || i18n_merge('anonymous_data', 'en_US'); 
}

function sa_toolbar($login=null){
    // todo : refactor this a bit, whew
    
  GLOBAL $SATB,$SA_ADMINPATH,$SITEURL,$LANG,$USR,$datau,$SATB_MENU_ADMIN,$SATB_MENU_STATIC;

    $EMAIL = isset($datau) ? $datau->EMAIL : '';
    
    $gstarget = '_blank'; 

    // logo 
    $logo  = '<li><ul class="satb_nav"><li class="satb_menu satb_icon"><a class="satb_logo" title="GetSImple CMS ver. '.GSVERSION.'" href="#"><img src="'.$SATB['PLUGIN_PATH'].'assets/img/gsicon.png"></a><ul id="satb_logo_sub">';
    $logo .= '<li class=""><a href="http://get-simple.info" target="'.$gstarget.'">GetSimple CMS</a></li>';
    $logo .= '<li class=""><a href="http://get-simple.info/forums" target="'.$gstarget.'">Forums<span class="iconright">&#9656;</span></a>';
    $logo .= '<ul><li class=""><a href="http://get-simple.info/forum/search/new/" target="'.$gstarget.'">New Posts</a></li>';
    $logo .= '</ul></li>';
    $logo .= '<li class=""><a href="http://get-simple.info/extend/" target="'.$gstarget.'">Extend</a></li>';
    $logo .= '<li class=""><a href="http://get-simple.info/wiki/" target="'.$gstarget.'">Wiki</a></li>';
    $logo .= '<li class=""><a href="https://github.com/GetSimpleCMS/GetSimpleCMS" target="'.$gstarget.'">SVN</a></li>';
    $logo .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$gstarget.'"><i class="cssicon info"></i>About SA_toolbar</a></li>';

    // icon test
    /*  
    $test = '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon info"></i>Info Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon help"></i>Help Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon success"></i>Success Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon success-alt"></i>Success Alt Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon alert"></i>Alert Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon warning"></i>Warning Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon denied"></i>Denied Icon</a></li>';
    $test .= '<li class=""><a class="" href="http://get-simple.info/forum/topic/4141/sa-gs-admin-toolbar/" target="'.$target.'"><i class="cssicon ribbon"></i>Ribbon Icon</a></li>';
    */
    
    $logo .= '</ul></ul></li>'; 
    
    // login form
    if($login){
        echo '<div id="sa_toolbar"><ul class="">'.$logo.'</ul>
            <ul class="right">
            <ul class="satb_nav">
                <li id="satb_login" class="satb_menu">
                <a id="satb_login_link" href="#">'.i18n_r('LOGIN').'</a>
                    <ul id="satb_login_menu">           
                        <form action="'.$SA_ADMINPATH.'index.php?redirect='.$_SERVER['REQUEST_URI'].'" method="post">
                            <b>Username:</b><input type="text" id="userid" name="userid">
                            <b>Password:</b><input type="password" id="pwd" name="pwd">
                            <input class="submit" id="satb_login_submit" type="submit" name="submitted" value="Login">
                        </form>
                    </ul>
                </li>
            <li class="satb_menu tb_close"><a href="'.$SA_ADMINPATH.'logout.php?toolbar&close" title="Remove Bar"><strong>&times;</strong></a></li>                
            </ul>
            </ul>
        </div>';
        satb_jsOutput();        
        return; 
    }
    
    $editpath = $SA_ADMINPATH.'edit.php';
    
    if(function_exists('return_page_slug')){
        $pageslug = return_page_slug();
    } else {
        $pageslug = '';
    }   
    
    $tm = array(); // holds all tabs
    $sm = array(); // holds all sidemenus   
    
    $ptabs = sa_tb_get_PluginTabs();    // hold plugin tabs
        
    // tabs
    $tm = sa_tb_addMenu($tm,'pages','TAB_PAGES',$SA_ADMINPATH.'pages.php');
    $tm = sa_tb_addMenu($tm,'files','TAB_FILES',$SA_ADMINPATH.'upload.php');
    $tm = sa_tb_addMenu($tm,'theme','TAB_THEME',$SA_ADMINPATH.'theme.php');
    $tm = sa_tb_addMenu($tm,'backups','TAB_BACKUPS',$SA_ADMINPATH.'backups.php');
    $tm = sa_tb_addMenu($tm,'plugins','PLUGINS_NAV',$SA_ADMINPATH.'plugins.php');

    // merge in plugin nav-tabs
    $tm = array_merge($tm,$ptabs);  
        
    $tm = sa_tb_addMenu($tm,'support','TAB_SUPPORT',$SA_ADMINPATH.'support.php'); // custom
    $tm = sa_tb_addMenu($tm,'settings','TAB_SETTINGS',$SA_ADMINPATH.'settings.php'); // custom
    $tm = sa_tb_addMenu($tm,'logs','LOGS',$SA_ADMINPATH.'log.php'); // custom
    
    # satb_debugLog($ptabs);
    # satb_debugLog($tm);
    
    // default sidemenus
    $sm = sa_tb_addMenu($sm,'pages','SIDE_VIEW_PAGES',$SA_ADMINPATH.'pages.php');
    $sm = sa_tb_addMenu($sm,'pages','SIDE_CREATE_NEW',$SA_ADMINPATH.'edit.php');
    $sm = sa_tb_addMenu($sm,'pages','MENU_MANAGER',$SA_ADMINPATH.'menu-manager.php');
    $sm = sa_tb_addMenu($sm,'files','FILE_MANAGEMENT',$SA_ADMINPATH.'upload.php');
    $sm = sa_tb_addMenu($sm,'theme','SIDE_CHOOSE_THEME',$SA_ADMINPATH.'theme.php');
    $sm = sa_tb_addMenu($sm,'theme','SIDE_EDIT_THEME',$SA_ADMINPATH.'theme-edit.php');
    $sm = sa_tb_addMenu($sm,'theme','SIDE_COMPONENTS',$SA_ADMINPATH.'components.php');
    $sm = sa_tb_addMenu($sm,'theme','SIDE_VIEW_SITEMAP','../sitemap.xml');
    $sm = sa_tb_addMenu($sm,'backups','SIDE_PAGE_BAK',$SA_ADMINPATH.'backups.php');
    $sm = sa_tb_addMenu($sm,'backups','SIDE_WEB_ARCHIVES',$SA_ADMINPATH.'archive.php');
    $sm = sa_tb_addMenu($sm,'plugins','SHOW_PLUGINS',$SA_ADMINPATH.'plugins.php');
    // $sm = sa_tb_addMenu($sm,'plugins','anonymous_data/ANONY_TITLE',$SA_ADMINPATH.'load.php?id=anonymous_data'); // oops, forgot this was a plugin
    $sm = sa_tb_addMenu($sm,'support','SUPPORT',$SA_ADMINPATH.'support.php');
    $sm = sa_tb_addMenu($sm,'support','WEB_HEALTH_CHECK',$SA_ADMINPATH.'health-check.php');
    $sm = sa_tb_addMenu($sm,'settings','GENERAL_SETTINGS',$SA_ADMINPATH.'settings.php');
    $sm = sa_tb_addMenu($sm,'settings','SIDE_USER_PROFILE',$SA_ADMINPATH.'settings.php#profile');
    $sm = sa_tb_addMenu($sm,'logs','VIEW_FAILED_LOGIN',$SA_ADMINPATH.'log.php?log=failedlogins.log'); // custom
    
    $sm = sa_tb_get_PluginMenus($sm); // add plugin sidemenus to core sidemenus

    // these core sidemenus go at bottom
    $sm = sa_tb_addMenu($sm,'plugins','GET_PLUGINS_LINK','http://get-simple.info/extend');
    $sm = sa_tb_addMenu($sm,'settings','TAB_LOGOUT',$SA_ADMINPATH.'logout.php?toolbar'); // logout for convienence

    $logoutitem = $sm['settings'][count($sm['settings'])-1]; // logout is always last item
    $profileitem = $sm['settings'][1];  
    
    satb_automerge(array_merge($sm,$ptabs)); // auto load language files for found lang tokens
        
    // define menu parts

    // link target
    $target = satb_is_frontend() ? '_blank' : '_self'; 
    
    // init master admin menu
    $menu = '<li><ul class="satb_nav">
    <li class="satb_menu"><a href="#">Admin &#9662;</a>
    <ul>
    ';
        
    // DO HOOKS
    $SATB_MENU_ADMIN = $sm; // assign to global
    
    $SATB_MENU_STATIC['new'] = array('title'=>'+ '.satb_cleanStr(satb_geti18n('NEW_PAGE')),'url'=>$editpath);   
    if(function_exists('return_page_slug')){    
        $SATB_MENU_STATIC['edit'] = array('title'=>satb_cleanStr(satb_geti18n('EDIT')),'url'=>$editpath.'?id='.return_page_slug()); 
    }
    
    exec_action('sa_toolbar_disp'); // call hook        
    
    $sm = $SATB_MENU_ADMIN; // set back from global
        
    $separator = '<li class="separator"></li>';
    
    // debug mode indicator
    $debugicon = '<li class="satb_icon" title="'.ucwords(satb_cleanStr(satb_geti18n('DEBUG_MODE'))).' ON"><img src="'.$SATB['PLUGIN_PATH'].'assets/img/sa_tb_debugmode.png"></li>'; 
    
    // welcome user
    $sig  = '<ul class="satb_nav"><a id="avatar" href="http://gravatar.com/emails/"><li class="satb_menu"><img src="'.satb_get_gravatar( $EMAIL, 20, 'mm', 'g', false).'" style="width:20px;height:20px;" /></a>';
    $sig .= '<a class="welcome" href="#">'.i18n_r('WELCOME').', <strong>'.$USR.'</strong></a><ul>';
    $sig .= '<li class=""><a href="'.$profileitem['func'].'" target="'.$target.'">'.satb_cleanStr(satb_geti18n($profileitem['title'])).'</a></li>';
    $sig .= '<li class=""><a href="'.$logoutitem['func'].'"><i class="cssicon alert"></i>'.satb_cleanStr(satb_geti18n($logoutitem['title'])).'</a></li>';
    $sig .= '</ul></li>';
        
    $tm = satb_update_tabs($tm); // handle any empty or new tabs

    satb_debugLog('tabs array',$tm);
    satb_debugLog('sidemenus array',$sm);
    
    foreach($tm as $key=>$page){
        // loop tabs array
        
        $iscustomtab = sa_tb_array_index(sa_tb_array_index($page,0),'iscustom');
        
        // check if tab is plugin tab
        // picky note: built in tabs all use the first level sidemenu item as the default action, all plugins should follow this, arghhh        
        if( isset($ptabs[$key]) ){
            // tab is plugin, so convert lang wrapped titles only and set func and action url parts
            $tablink = $SA_ADMINPATH.'load.php?id=' . sa_tb_array_index(sa_tb_array_index($ptabs[$key],0),'func');
            $tablink .=  '&amp;' . sa_tb_array_index(sa_tb_array_index($ptabs[$key],0),'action');
            $title_i18n = true;
        } else {
            // tab is core
            $tablink = sa_tb_array_index(sa_tb_array_index($page,0),'func');
            // is tab custom
            if($iscustomtab){
                $title_i18n = true;
            } else {
                $title_i18n = false;
            }           
        }
        
        if($key != 'link'){
            $menu.= '<li' . (isset($ptabs[$key])? ' class="plugin" ':'') . '><a href="'.$tablink.'" target="'.$target.'">'.satb_cleanStr(satb_geti18n($tm[$key][0]['title'],$title_i18n));
            $menu.= (count($page) > 0) ? '<span class="iconright">&#9656;</span></a><ul>' : '</a><ul>';
        }
        
        // loop sidemenus for page
        if(isset($sm[$key])){
                
            foreach($sm[$key] as $submenu){
            
                $iscustomsm = sa_tb_array_index($submenu,'iscustom');
                $ispluginsm = sa_tb_array_index($submenu,'isplugin');           
            
                if( (isset($submenu['isplugin']) and $submenu['isplugin'] == true) or $iscustomtab or $iscustomsm) {
                    $title = satb_cleanStr(satb_geti18n($submenu['title'],true));               
                    $class = $iscustomtab || $iscustomsm ? 'custom' : 'plugin';
                    
                    if($iscustomtab and $key == 'link'){
                        $menu.='<li class="'.$class.'"><a href="'.$SA_ADMINPATH.'load.php?id='.$submenu['func'].(isset($submenu['action']) ? '&amp;'.$submenu['action'] : '').'" target="'.$target.'">'.$title.'</a></li>';                                        
                    } else {
                        $menu.='<li class="'.$class.'"><a href="'.$SA_ADMINPATH.'load.php?id='.$submenu['func'].(isset($submenu['action']) ? '&amp;'.$submenu['action'] : '').'" target="'.$target.'">'.$title.'</a></li>';                    
                    }   
                } else {
                    $title = satb_cleanStr(satb_geti18n($submenu['title']));    
                    $menu.='<li><a href="'.$submenu['func'].'" target="'.$target.'">'.$title.'</a></li>';
             }
            }
        }       
        
        if($key == 'link'){
            $menu.='</li>';
        } else {
            $menu.='</ul></li>';
        }
        
    }
    
    $menu.='</ul></li></ul>';
    
    echo '<div id="sa_toolbar">
    <ul class="">';
    echo $logo;
    echo $menu;
    echo $separator;
    
    // create top menu
    foreach($SATB_MENU_STATIC as $key=>$menutop){
        echo '<li class="satb_menu top_'.$key.'"><a href="'.$menutop['url'].'" target="'.$target.'">'.$menutop['title'].'</a></li>';        
        echo $separator;    
    }

    echo '</ul>';
    echo '<ul class="right">'.$sig.'</ul>';
    
    // debug indicator logic
    if((defined('GSDEBUG') and GSDEBUG == 1)){
        echo '<ul class="right">';
        echo $debugicon;
        echo '</ul>';
    }
    echo '</div>';
    
    satb_jsOutput();
    
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function satb_get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

    
function satb_jsOutput(){
?>
    
    <script type="text/javascript">
        $(document).ready(function() {

            $.fn.csspixels = function(property) {
                    return parseInt(this.css(property).slice(0,-2));
            };   

            $('body').append($('#sa_toolbar')); // prevents inheriting styles from #footer
            $('ul#pill').hide(); // hide backend header
            
            if ( $('#sa_toolbar').length > 0 ) {

                $('body').addClass('gs-toolbar'); // for special theme styling when toolbar is present, body.gs-toolbar elements{}
            
                // add margin to body to push down content
                bodytop = $('body').csspixels('margin-top');
                $('body').css('margin-top', (bodytop+28)+'px'); //todo: make the height dynamic based on navbar css
                
                // move background
                // $('body').css('background-position', '0px 28px');    
                
                // assign body z-index in case its auto
                // console.log($('body').css('z-index'));
                // $('body').css('z-index', 9998);  
            }   
        
        });
    </script>

<?php
    
}

function satb_cleanStr($str){
    return strip_tags($str);
}

function satb_automerge($array){
    global $LANG;
    
    // loop plugins for i18n text {}
    // index unique for single merge
    // merge plugin lang files in string for posible lang combinations ( which is inefficient )
    
    $i18n_merges = array();
        
    foreach($array as $menu){
        # satb_debugLog($menu);
        foreach($menu as $item){
        # satb_debugLog($item);         
            if(preg_match('/^\{(.*)\/(.*)\}$/',$item['title'],$matches)){
                # satb_debugLog($item);         
                if(isset($matches[1]) and isset($matches[2])){
                    $i18n_merges[] = trim($matches[1]);
                }   
            }   
        }   
    }

    $i18n_merges = array_unique($i18n_merges);
    
    foreach($i18n_merges as $merge){
        satb_debugLog('satb_automerge_custom',$merge);
        i18n_merge($merge, $LANG);      
        i18n_merge($merge, substr($LANG,0,2));                  
        # i18n_merge($matches[1],'en_US');      
    }
    
}

function satb_geti18n($str,$intags=false){
        if($intags == false){
            return i18n_r($str);    
        }
        else if(preg_match('/^\{(.*\/.*)\}$/',$str,$matches) ){
            return i18n_r($matches[1]);     
        }   
        else return $str;
}

function sa_tb_addMenu($array,$page,$title,$func){
    $array[$page][] = array('func'=>$func,'title'=>$title);
    return $array;
}


function satb_update_tabs($tm){
    // adds or removes tabs as needed
    
    GLOBAL $SATB_MENU_ADMIN;
    $smkeys = array_keys($SATB_MENU_ADMIN);
    $tmkeys = array_keys($tm);
        
    // new tabs
    foreach(array_diff($smkeys,$tmkeys) as $tab){
        # $tm = sa_tb_addMenu($tm,$tab,$tab,'');
        $tm[$tab][] = array('func'=>$tab,'title'=>$tab,'iscustom'=>true);
    }

    // empty tabs
    foreach(array_diff($tmkeys,$smkeys) as $tab){
        unset($tm[$tab]);
    }
    
    return $tm; 
}

function sa_tb_get_PluginTabs(){
  global $plugins;    
  $sa_plugins = $plugins;
  $plugintabs = array();
    
  foreach ($sa_plugins as $hook)    {
        if($hook['hook'] == 'nav-tab' and (isset($hook['args']) and isset($hook['args'][1]) and isset($hook['args'][2])) ){
            # $plugintabs[$hook['args'][1]] = $hook['args'][2];
            $plugintabs[$hook['args'][1]][] = array('title'=>$hook['args'][2],'func'=>$hook['args'][0],'action'=> isset($hook['args'][3]) ? $hook['args'][3] : null );
        }   
    }
    return $plugintabs;
}

function sa_tb_get_PluginMenus($pluginsidemenus = array(),$page = null){
  global $plugins;
  $sa_plugins = $plugins;

    # satb_debuglog($sa_plugins);
    
  foreach ($sa_plugins as $hook)    {
    if(substr($hook['hook'],-8,9) == '-sidebar'){
            # satb_debuglog($hook);     
            $tab = str_replace('-sidebar','',$hook['hook']);
            if(isset($hook['args']) and isset($hook['args'][0]) and isset($hook['args'][1])){
                $allowAll = true; // allow plugins that use their own callbacks instead of createSideMenu, even though it is a terrible idea
                if($hook['function'] == 'createSideMenu' or $allowAll){
                    $pluginsidemenus[$tab][] = array('title'=>$hook['args'][1],'func'=>$hook['args'][0],'action'=> isset($hook['args'][2]) ? $hook['args'][2] : null,'isplugin' => true,'file' => $hook['file'] );
                }
            }
        }   
    }
    
    # satb_debuglog($pluginsidemenus);
    return $pluginsidemenus;
}

function sa_tb_executeheader(){ // assigns assets to queue or header
  GLOBAL $SATB;

  # debugLog("sa_dev_executeheader");

  $PLUGIN_ID   = $SATB['PLUGIN_ID'];
  $PLUGIN_PATH = $SATB['PLUGIN_PATH'];
  $owner       = $SATB['owner'];
  
  $regscript = $owner."register_script";
  $regstyle  = $owner."register_style";
  $quescript = $owner."queue_script";
  $questyle  = $owner."queue_style";

  $regstyle($PLUGIN_ID, $PLUGIN_PATH.'assets/css/sa_toolbar.css', '0.1', 'screen');
  $questyle($PLUGIN_ID,GSBOTH);   
  
 $quescript('jquery',GSBOTH);   
}

function SA_tb_register_style($handle, $src, $ver){echo '<link rel="stylesheet" href="'.$src.'" type="text/css" charset="utf-8" />'."\n";}
function SA_tb_queue_style($name,$where){}
function SA_tb_register_script($handle, $src, $ver, $in_footer=FALSE){echo '<script type="text/javascript" src="'.$src.'"></script>'."\n";}
function SA_tb_queue_script($name,$where){}


function sa_tb_user_is_admin(){
  GLOBAL $USR;
    
  if (isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME')) {
    return true;
  }
}

function sa_tb_array_index($ary,$idx){ // handles all the isset error avoidance bullshit when checking an array for a key that might not exist
  if( isset($ary) and isset($idx) and isset($ary[$idx]) ) return $ary[$idx];
}

function satb_is_frontend() {
  GLOBAL $base;
        if(isset($base)) {
                return true;
        } else {
                return false;
        }
}


//  add_action('sa_toolbar_disp','satb_hook_test');

function satb_hook_test(){
    GLOBAL $SITEURL,$GSADMIN,$SATB,$SATB_MENU_ADMIN,$SATB_MENU_STATIC;
    if(SATB_DEBUG == false) return;
    
    // known issues / limitations
    // If you do not specify isplugin or iscustom, your string will be i18n decoded and wrapped in {} at this time
    
    // To add a link to an existing sub menu, use the pages name or plugin name as the arrays key
    $SATB_MENU_ADMIN['pages'][] = array('title'=>'my custom pages item','func'=>'#','iscustom'=>true);
    $SATB_MENU_ADMIN['backups'][] = array('title'=>'my custom backup item','func'=>'#','iscustom'=>true);
    
    // To add a link to a new sub menu, use the arkey for the menu name eg. "custom"
    $SATB_MENU_ADMIN['custom'][] = array('title'=>'my custom sub menu item','func'=>'#','iscustom'=>true);
    
    // TO add a single menu item link use 'link' as the page
    $SATB_MENU_ADMIN['link'][] = array('title'=>'my custom menu item link','func'=>'#','iscustom'=>true);
    
    // To remove an entire menu, remove it from the array
    unset($SATB_MENU_ADMIN['settings']); // remove settings entirely
    
    // To remove specific sub-menu items, you will have to loop through the array and remove items based on your criteria
    // * menu items contain a 'file' attribute which can help identify a specific plugins submenus
    
    // To change the edit button
    # $SATB_MENU_STATIC['edit'] = array('title'=> 'Custom Edit','url'=>'javascript:alert(\'javacript example\');');
    
    // To change the new page button
    # $SATB_MENU_STATIC['new'] = array('title'=> 'Custom New','url'=>$SA_ADMINPATH.'load.php?id=blog&create_post');
    $SATB_MENU_STATIC['blog'] = array('title'=> '+ New Blog Post','url'=>$SITEURL.$GSADMIN.'/'.'load.php?id=blog&create_post');
    $SATB_MENU_STATIC['special'] = array('title'=> '+ New Special Page','url'=>$SITEURL.$GSADMIN.'/'.'load.php?id=i18n_specialpages&create');
    
    // There is no way to add new top buttons yet, but its in the works.
    
    satb_debugLog($SATB_MENU_ADMIN);
}

?>

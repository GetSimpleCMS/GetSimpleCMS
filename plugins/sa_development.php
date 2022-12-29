<?php

/*
* @Plugin Name: sa_development
* @Description: Provides alterative debug console and error reporting
* @Version: 0.9
* @Author: Shawn Alverson
* @Author URI: http://tablatronix.com/getsimple-cms/sa-dev-plugin/
*/

/** config options and getter will merge $SA_DEV_USER_CONFIG for option override **/

// init timer

$stopwatch = new StopWatch();

$SA_DEV_CONFIG = array(
  'showsuppressederrors'       => false,
  'showerrorbacktracealways'   => true,  // showbacktrace for all errors ( notices|supressed ) perhaps use a special error reporting mask here
  'showerrorcontext'           => false, // show error context (dump local vars)
  'disablexdebug'              => false, // disable xdebug
  'overridexdebugvardump'      => true,  // overridexdebug var_dump ( only for var_dumps)
  'showrequestvars'            => true,  // @todo NI show get and post vars always
  'showerrorlevels'            => true,  // @todo NI show error reporting levels and changes
  'showerrors'                 => true,   // @todo NI use custom error handler
  'theme'                      => 'monokai' // 'monokai' or 'default'
);

function sa_dev_getconfig($id){
  GLOBAL $SA_DEV_CONFIG,$SA_DEV_USER_CONFIG;
  if(isset($SA_DEV_USER_CONFIG)) $SA_DEV_CONFIG = array_merge($SA_DEV_CONFIG,$SA_DEV_USER_CONFIG);
  return sa_array_index($SA_DEV_CONFIG,$id);
}

function sa_dev_setconfig($id,$value){
  GLOBAL $SA_DEV_USER_CONFIG;
  if(!isset($SA_DEV_USER_CONFIG)) $SA_DEV_USER_CONFIG = array();
  $SA_DEV_USER_CONFIG['id'] = $value;
}

// global to force console on front end even when not logged in
$SA_DEV_ON = isset($SA_DEV_ON) ? $SA_DEV_ON : false;

define('SA_DEBUG',false); // sa dev plugin debug for debugging itself

if(!function_exists('getRelPath')){
  function getRelPath($path,$root = GSROOTPATH ){
    $relpath = str_replace($root,'',$path);
    return $relpath;
  }
}

$PLUGIN_ID   = "sa_development";
$PLUGINPATH  = $SITEURL. getRelPath(GSPLUGINPATH) . '/sa_development/';
$sa_url      = 'http://tablatronix.com/getsimple-cms/sa-dev-plugin/';
// $SA_CM_THEME = "cm-s-default";
// $SA_CM_THEME = "cm-s-monokai";

// if(isset($_GET['theme'])) $SA_CM_THEME  = $_GET['theme'];

# get correct id for plugin
$thisfile    = basename(__FILE__, ".php");// Plugin File
$sa_pname    = 'SA Development';          //Plugin name
$sa_pversion = '0.9';                     //Plugin version
$sa_pauthor  = 'Shawn Alverson';          //Plugin author
$sa_purl     =  $sa_url;                  //author website
$sa_pdesc    =  'SA Development Suite';   //Plugin description
$sa_ptype    =  '';                       //page type - on which admin tab to display
$sa_pfunc    =  '';                       //main function (administration)

# register plugin
register_plugin($thisfile,$sa_pname,$sa_pversion,$sa_pauthor,$sa_url,$sa_pdesc,$sa_ptype,$sa_pfunc);

// INCLUDES
require_once('sa_development/hooks.php');
require_once('sa_development/sa_dev_functions.php');

if(sa_dev_getconfig('disablexdebug')) xdebug_overload_var_dump(false); // disable xdebug


if(SA_DEBUG==true){
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
}

// enable only when logged in
if((sa_user_is_admin() || $SA_DEV_ON) && (get_filename_id() != 'install' && get_filename_id() != 'setup' && get_filename_id() != 'update')){
  add_action('index-posttemplate', 'sa_debugConsole');
  if(SA_DEBUG==true){
    add_action('footer', 'sa_debugtest');         // debug logging @todo only backend debugtesting
    add_action('sa_dev_menu','sa_dev_menu_hook'); // debug dev menus hooks
  }
  add_action('footer', 'sa_debugConsole');

  // asset queuing
  // use header hook if older than 3.1
  if(floatval(GSVERSION) < 3.1){
    add_action('header', 'sa_dev_executeheader');
    $owner = "SA_dev_";
  }
  else{ sa_dev_executeheader(); }

}

// GLOBALS
$debugLogFunc = '_debugLog';

$SA_DEV_GLOBALS = array();
$SA_DEV_GLOBALS['show_filters']      = sa_getFlag('sa_sf');   // print filters
$SA_DEV_GLOBALS['show_hooks_front']  = sa_getFlag('sa_shf');  // print hooks frontend
$SA_DEV_GLOBALS['show_hooks_back']   = sa_getFlag('sa_shb');  // print hooks backend
$SA_DEV_GLOBALS['bmark_hooks_front'] = sa_getFlag('sa_bhf');  // benchmark hooks frontend
$SA_DEV_GLOBALS['bmark_hooks_back']  = sa_getFlag('sa_bhb');  // benchmark hooks backend
$SA_DEV_GLOBALS['live_hooks']        = sa_getFlag('sa_lh');   // live hooks dump
$SA_DEV_GLOBALS['php_dump']          = sa_getFlag('sa_php');  // php dump

$SA_DEV_BUTTONS = array();

$sa_console_sent = false;

$sa_phperr_init = error_reporting();
$sa_phperr = error_reporting();

$open_basedir_val = ini_get('open_basedir');
$overridexdebug = true;

if(sa_showingFilters()) add_action('common','create_pagesxml',array(true));

// var_dump(get_defined_vars());

// INIT
sa_initHookDebug();
sa_initFilterDebug(); // @todo this needs work


// FUNCTIONS

// ARG LOGIC
function sa_showingFilters(){
  // are we showing filters
  GLOBAL $SA_DEV_GLOBALS;
  // return true;
  return $SA_DEV_GLOBALS['show_filters'];
}

function sa_showingHooks(){
  // are we showing hooks
  GLOBAL $SA_DEV_GLOBALS;
  return $SA_DEV_GLOBALS['show_hooks_front'] || $SA_DEV_GLOBALS['show_hooks_back'];
}

function sa_bmarkingHooks(){
  // are we bmarking hooks
  GLOBAL $SA_DEV_GLOBALS;
  return $SA_DEV_GLOBALS['bmark_hooks_front'] || $SA_DEV_GLOBALS['bmark_hooks_back'];
}

function sa_liveHooks(){
  // are we bmarking hooks
  GLOBAL $SA_DEV_GLOBALS;
  return $SA_DEV_GLOBALS['live_hooks'];
}

function sa_phpDump(){
  // are we dumping php
  GLOBAL $SA_DEV_GLOBALS;
  return $SA_DEV_GLOBALS['php_dump'];
}

function sa_initHookDebug(){
  // add hooks for showing and bmarking them
  GLOBAL $SA_DEV_GLOBALS, $FRONT_END_HOOKS, $BACK_END_HOOKS;

  if(sa_bmarkingHooks()){
    # debugTitle('Debugging Hooks');
  }

  if(sa_showingHooks() || sa_bmarkingHooks()){
    foreach($FRONT_END_HOOKS as $key=>$value){
      if($SA_DEV_GLOBALS['bmark_hooks_front']) add_action($key, 'sa_bmark_hook_debug',array($key));
      if($SA_DEV_GLOBALS['show_hooks_front'])  add_action($key, 'sa_echo_hook',array($key));
    }

    foreach($BACK_END_HOOKS as $key=>$value){
      if($SA_DEV_GLOBALS['bmark_hooks_back']) add_action($key, 'sa_bmark_hook_debug',array($key));
      if($SA_DEV_GLOBALS['show_hooks_back'])  add_action($key, 'sa_echo_hook',array($key));
    }
  }
}

/*
  For debugging actual filters to ensure filter is functioning properly
  @uses $FILTERS from hooks.php
 */
function sa_initFilterDebug(){
  // add hooks for showing and bmarking them
  GLOBAL $SA_DEV_GLOBALS, $FILTERS, $filters;

  if(sa_showingFilters()){
    debugTitle('Debugging Filters');
    _debugLog(__FUNCTION__);
    _debugLog($FILTERS);
    foreach($FILTERS as $key=>$value){
     // _debugLog(__FUNCTION__,$key);
     add_filter($key, 'sa_echo_filter',array($key));
    }
  }
}

function sa_debugMenu(){ // outputs the dev menu
  GLOBAL $SA_DEV_GLOBALS, $SA_DEV_BUTTONS;

  $site = pageIsFrontend() ? 'front' : 'back';
  $sitecode = pageIsFrontend() ? 'f' : 'b';
  $sh = '?'.get_toggleqstring('show_hooks_'.$site,'sa_sh'.$sitecode).'#sa_debug_title';
  $bh = '?'.get_toggleqstring('bmark_hooks_'.$site,'sa_bh'.$sitecode).'#sa_debug_title';
  $lh = '?'.get_toggleqstring('live_hooks','sa_lh').'#sa_debug_title';
  $pd = '?'.get_toggleqstring('php_dump','sa_php').'#sa_debug_title';

  $reset = sa_dev_qstring('sa_sh'.$sitecode);
  $reset = '?'.sa_dev_qstring('sa_bh'.$sitecode,null,$reset);

  # debugLog($reset);
  # debugLog($sh);
  # debugLog($bh);

  $local_menu = array();
  $local_menu[] = array('title'=>'Reset','url'=> $reset);
  $local_menu[] = array('title'=>'Show Hooks','url'=> $sh,'on'=>$SA_DEV_GLOBALS['show_hooks_'.$site],'about'=>'Show hooks on page');
  $local_menu[] = array('title'=>'Time Hooks','url'=> $bh,'on'=>$SA_DEV_GLOBALS['bmark_hooks_'.$site],'about'=>'Log hook becnhmark times');
  $local_menu[] = array('title'=>'Live Hooks','url'=> $lh,'on'=>$SA_DEV_GLOBALS['live_hooks'],'about'=>'Log registered hooks');
  $local_menu[] = array('title'=>'Dump PHP','url'=> $pd,'on'=>$SA_DEV_GLOBALS['php_dump'],'about'=>'Dump PHP enviroment');

  echo '<div id="sa_dev_menu"><ul>';
  echo sa_dev_makebuttons($local_menu);
  exec_action('sa_dev_menu');
  if(count($SA_DEV_BUTTONS) > 0){
    echo '<li><b>|</b> </li>';
    echo sa_dev_makebuttons($SA_DEV_BUTTONS,true,10);
  }
  echo '</ul></div>';

}

function sa_dev_makebuttons($buttons,$custom=false,$startid = 0){ // creates individual dev buttons
  $buttonstr = '';
  $classon = '_on';
  $classcustom = '_custom';
  $id = $startid;

  foreach($buttons as $button){
    $class = 'class="sa_dev';
    $about = $button['title'];
    if($custom) $class.= $classcustom;
    if(isset($button['on'])) $class.= $button['on'] ? $classon : '';
    if(isset($button['about'])) $about= $button['about'] ;
    $buttonstr.='<li><a id="dev_but_'.$id.'" '.$class.'" href="'.$button['url'].'" title="'.$about.'">'.$button['title'].'</a></li>';
    $id++;
  }

  return $buttonstr;
}

function sa_dev_menu_hook(){ // debug for dev menu hook
  GLOBAL $SA_DEV_BUTTONS;
  $SA_DEV_BUTTONS[] = array('title'=>'Hooked Button off','url'=>'#','on'=>true);
  $SA_DEV_BUTTONS[] = array('title'=>'Hooked Button on','url'=>'#','on'=>false);
}


function sa_dev_executeheader(){ // assigns assets to queue or header
  GLOBAL $PLUGIN_ID, $PLUGINPATH, $owner;

  # debugLog("sa_dev_executeheader");

  $regscript = $owner."register_script";
  $regstyle  = $owner."register_style";
  $quescript = $owner."queue_script";
  $questyle  = $owner."queue_style";

  $regstyle($PLUGIN_ID, $PLUGINPATH.'css/sa_dev_style.css', '0.1', 'screen');
  $questyle($PLUGIN_ID,GSBOTH);

  queue_script('jquery',GSBOTH);
}

function sa_logRequests(){
  if(isset($_POST) and count($_POST) > 0){
    _debuglog('PHP $_POST variables',$_POST);
  }

  if(isset($_GET) and count($_GET) > 0){
    _debuglog('PHP $_GET variables',$_GET);
  }

}

function sa_debugConsole(){  // Display the log
  global $GS_debug,$stopwatch,$sa_console_sent,$sa_phperr_init,$SA_CM_THEME,$overridexdebug;

  if(!$sa_console_sent){
    sa_logRequests();

    if(sa_liveHooks()){
      # debugTitle('Debugging Hooks');
      sa_dumpLiveHooks();
    }

    if(sa_phpDump()){
      # debugTitle('PHP Dump');
      sa_dump_php();
    }

    sa_finalCallout();
  }

  # // tie to debugmode deprecated
  # if(defined('GSDEBUG') and !pageIsFrontend()) return;

    echo '<script type="text/javascript">'."\n";
    echo '(function ($) {';
    echo 'jQuery(document).ready(function() {'."\n";
    echo '$("h2:contains(\''. i18n_r('DEBUG_CONSOLE') .'\'):not(\'#sa_debug_header\')").remove();';

    $collapse = true;

    if($collapse){
      echo '
          //toggle the componenet with class msg_body
          $("#sa_gsdebug .titlebar").click(function(){

            if($(this).next().next(".sa_collapse").css("display")=="none"){
              $(this).next(".sa_expand").removeClass("sa_icon_closed").addClass("sa_icon_open");
            }

            $(this).next().next(".sa_collapse").slideToggle(200,function(){
              if($(this).css("display")=="none"){
                  $(this).prev(".sa_expand").removeClass("sa_icon_open").addClass("sa_icon_closed");
              }
            });
          });
      ';

    echo "
      function collapseAll(){
        $('.sa_collapse').hide();
        $('.sa_expand').removeClass('sa_icon_open').addClass('sa_icon_closed');
      }

      function expandAll(){
        $('.sa_collapse').show();
        $('.sa_expand').removeClass('sa_icon_closed').addClass('sa_icon_open');
      }

      $('#sa_gsdebug .collapseall').on('click',collapseAll);
      $('#sa_gsdebug .expandall').on('click',expandAll);

    ";
    echo "
    $('#sa_gsdebug').scroll(function(e){
      console.log('scrolling');
      var offset = $(this).scrollTop();
      $('#collapser').css('top',offset);
    });

    ";
    echo "\n});";
    echo '}(jQuery));';
    echo '</script>';
    }
    echo '<div id="sa_gsdebug-wrapper" class="fullwidth">
    <div class="sa_gsdebug-wrap">';

    if(!$sa_console_sent){
      echo '
      <span id="sa_debug_sig"><a href="http://tablatronix.com/getsimple-cms/sa-dev-plugin" target="_blank">sa_development</a></span>
      <a id="sa_debug_title" href="#sa_debug_title">'.i18n_r('DEBUG_CONSOLE').'</a>
      ';
      echo sa_debugMenu();
    }

    echo "\n";
    echo'<div id="sa_gsdebug" class="cm-s-'.sa_dev_getconfig('theme').'">';
    // echo'<div id="float"><div class="marker"></div></div>';
    echo '<span id="collapser" class="cm-keyword"><a class="collapseall">collapse</a><span> | </span><a class="expandall">expand</a></span>';
    echo '<pre>';

    if(!$sa_console_sent){
      echo 'GS Debug mode is: ' . ((defined('GSDEBUG') and GSDEBUG == 1) ? '<span class="cm-tag"><b>ON</b></span>' : '<span class="cm-error"><b>OFF</b></span>') . '<br />';
      echo 'PHP Error Level: <small><span class="cm-comment">(' . $sa_phperr_init . ') ' .error_level_tostring($sa_phperr_init,'|') . "</span></small>";

      // XDEBUG WARNINGS
      $xdebugstate = xdebug_overload_var_dump();
      if($xdebugstate){
        echo  '<div><span class="cm-tag">XDebug is overloading var_dump</span>';
        if($xdebugstate && $overridexdebug) xdebug_overload_var_dump(false);
        if(!xdebug_overload_var_dump()) echo  '<span class="cm-tag">, DISABLED</span></div>';
        else  echo  '<span class="cm-tag">. Unable to disable, output may not appear properly</span></div>';
        if($overridexdebug) xdebug_overload_var_dump($xdebugstate); // restore xdebug
      }

      echo "<span class='divider cm-comment'></span>";

    }else{
      echo 'Post footer alerts<br />';
    }

    if(count($GS_debug) == 0){
      echo('Log is empty');
    }
    else{
      foreach ($GS_debug as $log){
        // array found
        if(gettype($log) == 'array'){ echo _debugReturn("array found in debugLog",$log); }
        // obj found
        else if(gettype($log) == 'object'){ echo _debugReturn("object found in debugLog",$log); }
        // print_r array output found
        else if(preg_match('/^(Array\n\().*/',$log)){
          echo _debugReturn("print_r output found in debuglog",$log);
          # @todo remove the string() that wraps it $log → string(692) "array (
          # echo nl2br($log);
        }
        # if(gettype($log) == 'array'){ echo _debugReturn("array found in debugLog()",$log); } // todo: causes arg parsing on function name in quotes
        // String
        else echo '<div class="cm-default">'.$log.'</div>';
      }
    }
    echo '</pre>';
    echo '</div>';

    if($sa_console_sent != true){
      echo '
        <div id="sa_debug_footer">
          <span class="sa_icon_wrap"><span class="sa_icon sa_icon_time"></span>Runtime~: '. number_format(round(($stopwatch->elapsed()*1000),3),3) .' ms</span>
          <span class="sa_icon_wrap"><span class="sa_icon sa_icon_files"></span>Includes: '. count(get_required_files()) .'</span>
          <span class="sa_icon_wrap"><span class="sa_icon sa_icon_mempeak"></span>Peak Memory: '. byteSizeConvert(memory_get_peak_usage()) .'</span>
          <span class="sa_icon_wrap"><span class="sa_icon sa_icon_memlimit"></span>Mem Avail: '. ini_get('memory_limit') .'</span>
        ';
        if(empty($open_basedir_val)) echo '<span class="sa_icon_wrap"><span class="sa_icon sa_icon_diskfree"></span>Disk Avail: '. byteSizeConvert(disk_free_space("/")) .' / ' . byteSizeConvert(disk_total_space("/")) .'</span>';
        echo '</div>';
    }
    echo '</div></div>';

  $sa_console_sent = true;
}

/**
 * Uses dom to add node appending to simplexml
 */
function sxml_append(SimpleXMLElement $to, SimpleXMLElement $from) {
    $toDom = dom_import_simplexml($to);
    $fromDom = dom_import_simplexml($from);
    $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
}

// FILTER DEBUGGING
function sa_echo_filter($unfiltered,$args = array()){
  // echoes filters onto pages
  GLOBAL $FILTER,$SITEURL;

  // gs does not pass args to filters, so this is empty
  // would be nice to add this feature to core
  // find another way to enumerate the current filter I have forgotten atm

  $filterid = $args[0];
  // _debugLog(__FUNCTION__, $filterid);

  $pagesitem = <<<XML
  <item>
    <url>pagecache_filtered</url>
    <pubDate><![CDATA[Sun, 27 Oct 2013 09:30:11 -0500]]></pubDate>
    <title><![CDATA[pagecache filter]]></title>
    <url><![CDATA[pagecache_filtered]]></url>
    <meta><![CDATA[pagecache_filtered_1,pagecache_filtered_2]]></meta>
    <metad><![CDATA[pagecache_filtred]]></metad>
    <menu><![CDATA[pagecachefiltered]]></menu>
    <menuOrder><![CDATA[]]></menuOrder>
    <menuStatus><![CDATA[Y]]></menuStatus>
    <template><![CDATA[template.php]]></template>
    <parent><![CDATA[]]></parent>
    <private><![CDATA[]]></private>
    <author><![CDATA[pagecachefiltered]]></author>
    <slug><![CDATA[pagecache_filtered]]></slug>
    <filename><![CDATA[index.xml]]></filename>
  </item>
XML;

  $pagesitem_xml = simplexml_load_string($pagesitem);
  // _debugLog($pagesitem_xml);

  $sitemapitem = <<<XML
    <url>
      <loc>$SITEURL/sitemap_filtered</loc>
      <lastmod>2013-10-27T09:29:57+00:00</lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.5</priority>
    </url>
XML;

  $sitemap_xml = simplexml_load_string($sitemapitem);

  $style = 'style="background-color:#FFCCCC;font-size:12px;color:000;border:1px solid #CCC;padding:2px;margin:2px"';

  $filtercontent = array(
    'content'   => '<span '.$style.' title="' . $filter_id . '">This is content filtered</span>',
    'menuitems' => '<li><a href="#" '.$style.'>this is menutitems filtered</a></li>',
    'pagecache' => $pagesitem_xml,
    'sitemap'   => $sitemap_xml,
    'indexid'   => '404'
  );

  if(isset($filtercontent[$filterid])){
    if( is_object($filtercontent[$filterid]) && $filtercontent[$filterid]::class == 'SimpleXMLElement')  sxml_append($unfiltered,$filtercontent[$filterid]);
    else $unfiltered .= " " .$filtercontent[$filterid];
    // _debugLog($unfiltered);
    return $unfiltered;
  }
  else return $unfiltered;

}

// HOOK DEBUGGING
function sa_echo_hook($hook_id){
  // echoes hooks onto pages
  GLOBAL $FRONT_END_HOOKS, $BACK_END_HOOKS;
  $all_hooks = array_merge($FRONT_END_HOOKS, $BACK_END_HOOKS);
  echo '<span style="background-color:#FFCCCC;font-size:12px;color:000;border:1px solid #CCC;padding:2px;margin:2px" title="' . $all_hooks[$hook_id] . '">hook: '.$hook_id.'</span>';
}

function sa_bmark_hook_debug($hook_id){
  // benchmark hook call times to debug console
  sa_bmark_debug('hook: ' . $hook_id);
}

function sa_bmark_hook_print($hook_id){
  // benchmark hook call times to page
  sa_bmark_print($hook_id);
}

// TIMING BENCHMARKING FUNCTIONS
class StopWatch {
    public $total;
    public $time;

    public function __construct() {
        $this->total = $this->time = microtime(true);
    }

    public function clock() {
        return -$this->time + ($this->time = microtime(true));
    }

    public function elapsed() {
        return round(microtime(true) - $this->total,6);
    }

    public function reset() {
        $this->total=$this->time=microtime(true);
    }
}

/* echos do not use */
function sa_bmark_print($msg = ""){
    GLOBAL $stopwatch;
    echo("<span id=\"pagetime\">bmark: " . $msg . ": " . round($stopwatch->clock(),5) . " / " . round($stopwatch->elapsed(),5) ." seconds</span>");
}

function sa_bmark_debug($msg = ""){
    GLOBAL $stopwatch;
    // debugLog('<span class="titlebar cm-keyword"><span class="cm-default">bmark</span> : ' . number_format(round($stopwatch->elapsed(),5),5) . "<b> &#711;</b>" . number_format(round($stopwatch->clock(),5),5) . " " . $msg . '</span>');
    $collapsestr= '<span class="sa_expand sa_icon_time"></span>';
    GLOBAL $GS_debug;
    debugLog('<span class="titlebar cm-keyword">'.$msg.bmark_line().'</span>'.$collapsestr);
}

function sa_bmark_reset(){
  GLOBAL $stopwatch;
  $stopwatch->reset();
}


// output formatting

function sa_get_titlebar($file,$line,$text){
  $bmark_str = bmark_line();
  return 'title="(' . sa_get_path_rel($file) . ' ' . $line . ')">'.$text.$bmark_str.'</span>' ;
}

function sa_get_codeline($line,$codeline){
  return '<span class="cm-comment lineno">'.$line.':</span>' . $codeline;
}

// CORE FUNCTIONS
function _debugLog(/* variable arguments */){
  debuglogprepare(func_get_args());
}

function _debugReturn(/* variable arguments */){
  return vdump(func_get_args());
}

function debuglogprepare($args,$funcname = null){
  GLOBAL $GS_debug;
  if(sa_getErrorChanged()){
    debugTitle('PHP Error Level changed: <small>(' . error_reporting() . ') ' .error_level_tostring(error_reporting(),'|') . '</small>','notice');
  }

  $output = vdump(array($args),$funcname);
  // echo '<pre>'.print_r($output,true).'</pre>';
  array_push($GS_debug,$output);
}

function xdebug_overload_var_dump($enable = null){
  if(isset($enable)){
    if($enable === false) ini_set('xdebug.default_enable',0);
    else if($enable === true) ini_set('xdebug.default_enable',1);
  }

  // ini_set('xdebug.overload_var_dump',0);
  // ini_set('html_errors', 0);
  return ini_get('xdebug.default_enable') == 1 && ini_get('xdebug.overload_var_dump') == 1;
}

function vdump($args,$func = null){
    // print_r($args);
    local_debug($args);
    local_debug($func);

    GLOBAL $debugLogFunc,$overridexdebug;

    $debugstr = ''; // for local debugging because we can create infinite loops by using debuglog inside debuglogs

    if(isset($args) and gettype($args)!='array'){
      die('args missing');
      $args = func_get_args();
      $numargs = func_num_args();
    }else{
      $numargs = count($args);
    }

    // ! backtrace arguments are passed by reference !
    // todo: make this totally safe with no chance of modifying arguments. make copies of everything

    $backtrace = debug_backtrace();
    local_debug("<h2>bt </h2><pre>".print_r($backtrace,true)."</pre>");

    $dlfuncname = isset($func) ? $func : $debugLogFunc;
    local_debug("<h2>bt function</h2><pre>".print_r($dlfuncname,true)."</pre>");

    $lineidx =  sadev_btGetFuncIndex($backtrace,$dlfuncname);
    local_debug("<h2>bt lineindex</h2><pre>".print_r($lineidx,true)."</pre>");

    if(!isset($lineidx)) $lineidx = 1;
    $funcname = $backtrace[$lineidx]['function'];
    local_debug("<h2>bt funcname</h2><pre>".print_r($funcname,true)."</pre>");

    $file = isset($backtrace[$lineidx]['file']) ? $backtrace[$lineidx]['file'] : __FILE__; // php bug
    local_debug("<h2>bt file</h2><pre>".print_r($file,true)."</pre>");
    // @todo: handle evald code eg. [file] => /hsphere/local/home/salverso/tablatronix.com/getsimple_dev/plugins/i18n_base/frontend.class.php(127) : eval()'d code
    $line = isset($backtrace[$lineidx]['line']) ? $backtrace[$lineidx]['line'] : 0;
    local_debug("<h2>bt line</h2><pre>".print_r($line,true)."</pre>");

    $code = @file($file);

    if($line > 0) $codeline = $code!=false ? trim($code[$line-1]) : 'anon function call';
    else $codeline = '';

    local_debug("<h2>bt code</h2><pre>".print_r($codeline,true)."</pre>");
    local_debug("hr");
    /* Finding our originating call in the backtrace so we can extract the code line and argument nesting depth
     *
     * If using custom function, we have to remove all the get_func_arg array wrappers n deep
     * where n is the depth past the normal _debuglog function in the backtrace
     * each get_func_args wraps another array around the argument array
     * so we reduce it by as many levels as we need to get it back to the original args
     * we use a global function name to do this.
     * Still trying to figure out a way to figure out the originating call_user_func
     * it might be impossible since people might create a very advanced wrapper using debug levels arguments and args
     * one option would be to recursivly strip all nested arrays containing a single array
     * another is to use a wrapper function or class to add internal data to each get_func_array and wrappers must call this instead of get_func_array,
     *  makes nesting predictable, even if it adds more levels.
     *  It is possible I am simply missing something here that is obvious.
     */

    // reduce array depth and adjust arg count
    if($lineidx>1){
      for($i=0;$i<$lineidx-1;$i++){
        $args = $args[0];
      }
      $numargs = count($args);  // redo numargs, else it will stay the 1 from func_get_args
    }

    $arg1 = isset($args[0]) ? $args[0] : ''; // avoids constant isset checking in some logic below.
    // todo: breaks nulls

    #$argnames = preg_replace('/'. __FUNCTION__ .'\((.*)\)\s?;/',"$1",$codeline);
    $argstr = preg_replace('/.*'.$funcname.'\((.*)\)\s?;.*/',"$1",$codeline);
    $argnames = array();
    $argnames = sa_parseFuncArgs($argstr);
    $argn = 0;

    # debugLog(print_r($argstr,true));
    # debugLog(print_r($argnames,true));

    $collapsestr= '<span class="sa_expand sa_icon_open"></span><span class="sa_collapse">';
    $bmark_str = bmark_line();
    $str = "";

    if($numargs > 1 and gettype($arg1)=='string' and !empty($arg1) and ( gettype($args[1])!='string' or str_starts_with($argnames[1], '$'))){
      // if a string and more arguments, we treat first argumentstring as title, and shift it off the arg array
      $str.=('<span class="titlebar special" title="(' . sa_get_path_rel($file) . ' ' . $line . ')">'.htmlspecialchars($arg1).$bmark_str.'</span>');
      array_shift($args);
      array_shift($argnames);
      $numargs--;
      $str.= $collapsestr;
    }
    elseif($numargs > 1 || ( $numargs == 1 and (gettype($arg1)=='array' or gettype($arg1)=='object')) ){
      // if multiple arguments or an array, we add a header for the rows
      $str.=('<span class="titlebar array object multi"' . sa_get_titlebar($file,$line, sa_get_codeline($line,$codeline) ) );
      $str.= $collapsestr;
    }
    elseif($numargs == 1 and gettype($arg1)=='string' and !str_contains($argnames[0],'$')){
      // if string debug, basic echo, todo: this also catches functions oops
      $str=('<span class="string" title="(' . sa_get_path_rel($file) . ' ' . $line . ')">'.htmlspecialchars($arg1, ENT_QUOTES, 'UTF-8').'</span>');
      $str.= '<span>';
      return $str;
    }
    elseif($numargs == 0){
      // empty do backtrace
      $str.=('<span class="titlebar backtrace"'.sa_get_titlebar($file,$line, sa_get_codeline($line,$codeline) ) );
      $str.= $collapsestr;
      $str.= '<b>Backtrace</b><span class="cm-tag"> &rarr;</span><br />';
      $str.= nl2br(sa_debug_backtrace(2,$backtrace));
      $str.= '</span>';
      return $str;
    }
    else{
      // we add a slight divider for single line traces
      $str.="<span class='divider cm-comment'></span>";
    }
      ob_start();

      if(!is_array($args)) return; // how the does this happen?
      foreach ($args as $arg){
        # if($argn > 0) print("\n");
        if(isset($argnames[$argn])){
          $str.= '<span class="cm-variable"><b>' . trim($argnames[$argn]) . "</b></span> <span class='cm-tag'>&rarr;</span> ";
          if(gettype($arg) == 'array' and count($arg)>0) $str.= "\n"; // push array contents to new line
        }


        // prevent xdebugs var_dump overload from ruining output, tmp disable and restore
        $xdebugstate = xdebug_overload_var_dump();
        if($xdebugstate && $overridexdebug) xdebug_overload_var_dump(false);

        var_dump($arg);

        $dump = ob_get_clean();

        if(xdebug_overload_var_dump()) $str .= $dump; // safety in case xdebug could not be disabled
        else $str .= htmlspecialchars($dump,ENT_NOQUOTES);

        if($overridexdebug) xdebug_overload_var_dump($xdebugstate); // restore xdebug

        $argn++;
      }

   // cannot use this as it contains partial html from the collapse and headers from above
   #  debugLog("default output: " . $str."<br/>");

    $str = sa_dev_highlighting($str);
    $str = trim($str);
    return nl2br($str).'</span>';

    // debug with backtrace output
    // return nl2br($str).'<br>'.nl2br(sa_debug_backtrace(null,$backtrace)).'</span>';
    // return nl2br($str).'</span><pre>'.nl2br(print_r($backtrace,true)).'</pre>';
}

function local_debug($msg){
  // print_r($msg);
}

function sa_dev_highlighting($str){
    // added &? to datatypes for new reference output from var_dump
    // indented are for print_r outputs

    $str = preg_replace('/"('.PHP_EOL.')"/', urlencode(PHP_EOL), $str); // remove newlines when values

    $str = preg_replace('/=&gt;(\s+)/', ' &gt; ', $str); // remove whitespace
    // $str = preg_replace('/&gt; NULL/', '&gt; <span class="cm-def">NULL</span>', $str); // array nulls
    $str = preg_replace('/(\s)NULL/', ' <span class="cm-def">NULL</span>', $str); // string nulls, just 'NULL'
    $str = preg_replace('/}\n(\s+)\[/', "}\n\n".'$1[', $str);
    $str = preg_replace('/((?:&amp;)?float|(?:&amp;)?int)\((\-?[\d\.\-E]+)\)/',"<!-- 01 --><span class='cm-variable-2'>$1</span> <span class='cm-number'>$2</span>", $str); // float(n.n) | int(n)
    $str = preg_replace('/((?:&amp;)?float)\((\-?NAN+)\)/',                    "<!-- 02 --><span class='cm-variable-2'>$1</span> <span class='cm-def'>$2 <span class='cm-comment'>(Not a Number)</span></span>", $str); // float(NAN)
    $str = preg_replace('/((?:&amp;)?float)\((\-?INF+)\)/',                    "<!-- 03 --><span class='cm-variable-2'>$1</span> <span class='cm-def'>$2 <span class='cm-comment'>(Infinity)</span></span>", $str); // float(INF)
    $str = preg_replace('/(?:&amp;)?array\((\d+)\) {\s+}\n/',                  "<!-- 04 --><span class='cm-variable-2'>array&bull;$1</span> <span class='cm-bracket'><b>[]</b></span>", $str);
    $str = preg_replace('/(?:&amp;)?array\((\d+)\) {\n/',                      "<!-- 05 --><span class='cm-variable-2'>array&bull;$1</span> <span class='cm-bracket'>{</span>\n<span class='codeindent'>", $str);
      $str = preg_replace('/Array\n\(\n/',                                     "<!-- 06 -->\n<span class='cm-variable-2'>array</span> <span class='cm-bracket'>(</span>\n<span class='codeindent'>", $str);
      $str = preg_replace('/Array\n\s+\(\n/',                                  "<!-- 07 --><span class='cm-variable-2'>array</span> <span class='cm-bracket'>(</span>\n<span class='codeindent'>", $str);
      $str = preg_replace('/Object\n\s+\(\n/',                                 "<!-- 08 --><span class='cm-variable-2'>object</span> <span class='cm-bracket'>(</span>\n<span class='codeindent'>", $str);
    $str = preg_replace('/(?:&amp;)?string\((\d+)\) \"(.*)\"/',                "<!-- 09 --><span class='cm-variable-2'>str&bull;$1</span> <span class='cm-string'>'$2'</span>", $str); // &(opt)string(n) "string with "quotes" "
    $str = preg_replace('/(?:&amp;)?string\((\d+)\) \"([^"\']*)\"/s',          "<!-- 09 --><span class='cm-variable-2'>str&bull;$1</span> <span class='cm-string'>'$2'</span>", $str); // &(opt)string(n) "string with \n"
    $str = preg_replace('/(?:&amp;)?resource\((\d+)\) of type \((.*)\)/',      "<!-- 09 --><span class='cm-variable-2'>#$1</span> <span class='cm-keyword'>$2</span>", $str); // resource(#id) of type (resourcetype)
    $str = preg_replace('/\[\"(.+)\"\] &gt; /',                                "<!-- 10 --><span style='color:#666'>'<span class='cm-string'>$1</span>'</span> <span class='cm-tag'>&rarr;</span> ", $str);
      $str = preg_replace('/\[([a-zA-Z\s_]+)\]  &gt; /',                       "<!-- 11 --><span style='color:#666'>'<span class='cm-string'>$1</span>'</span> <span class='cm-tag'>&rarr;</span> ", $str);
      $str = preg_replace('/\[(\d+)\]  &gt; /',                                "<!-- 12 --><span style='color:#666'>[<span class='cm-string'>$1</span>]</span> <span class='cm-tag'>&rarr;</span> ", $str);
    $str = preg_replace('/\[(\d+)\] &gt; /',                                   "<!-- 13 --><span style='color:#666'>[<span class='cm-string'>$1</span>]</span> <span class='cm-tag'>&rarr;</span> ", $str);
    $str = preg_replace('/(?:&amp;)?object\((\S+)\)#(\d+) \((\d+)\) {\s+}\n/', "<!-- 14 --><span class='cm-variable-2'>obj&bull;$2</span> <span class='cm-keyword'>$1[$3]</span> <span class='cm-keyword'>{}</span>", $str);
    $str = preg_replace('/(?:&amp;)?object\((\S+)\)#(\d+) \((\d+)\) {\n/',     "<!-- 15 --><span class='cm-variable-2'>obj&bull;$2</span> <span class='cm-keyword'>$1[$3]</span> <span class='cm-keyword'>{</span>\n<span class='codeindent'>", $str);
    $str = str_replace('bool(false)',                                          "<!-- 16 --><span class='cm-variable-2'>bool&bull;</span><span class='cm-number'><b>false</b></span>", $str); // bool(false)
    $str = str_replace('&amp;bool(false)',                                     "<!-- 17 --><span class='cm-variable-2'>bool&bull;</span><span class='cm-number'><b>false</b></span>", $str); // &bool(false)
    $str = str_replace('bool(true)',                                           "<!-- 18 --><span class='cm-variable-2'>bool&bull;</span><span class='cm-number'><b>true</b></span>", $str);  // bool(true)
    $str = str_replace('&amp;bool(true)',                                      "<!-- 19 --><span class='cm-variable-2'>bool&bull;</span><span class='cm-number'><b>true</b></span>", $str);  // &bool(true)
    $str = preg_replace('/}\n/',                                               "<!-- 20 --></span>\n<span class='cm-bracket'>}</span>\n", $str); // closing ) bracket
      $str = preg_replace('/\)\n/',                                            "<!-- 21 --></span>\n<span class='cm-bracket'>)</span>\n", $str); // closing } bracket

    $str = str_replace("\n\n","\n",$str);
    # if($argn == 1) $str = str_replace("\n","",$str);
    return $str;
}

function sa_finalCallout(){
}

function sa_dev_ErrorHandler($errno, $errstr='', $errfile='', $errline='',$errcontext=array()){
    GLOBAL $sa_phperr_init;

    # _debugLog(error_reporting(),$errno, $errstr, $errfile, $errline);

    /*  Of particular note is that error_reporting() value will be 0 if
     *  the statement that caused the error was prepended by the @ error-control operator.
     */

    $errorReporting = error_reporting();

    // handle supressed errors
    $debugSuppressed  = sa_dev_getconfig('showsuppressederrors');

    $showingSuppressed = false; // flag

    if((defined('GSDEBUG') and GSDEBUG == 1) and $debugSuppressed == true){
      #$errorReporting = -1;
      #$errno=0;
      $showingSuppressed = true;
      $errno = 0;
    }

    // _debugLog(error_reporting(),$errno, $errstr, $errfile, $errline);

    // Ignore if error reporting is off, unless parse error
    if (!($errorReporting & $errno) and $errno!=E_PARSE and $showingSuppressed != true) {
        // This error code is not included in error_reporting
        // unless parse error , then we want user to know
        return;
    }

    // check if function has been called by an exception
    if(func_num_args() == 5) {
        // called by trigger_error()
        #$exception = null;
        #list($errno, $errstr, $errfile, $errline) = func_get_args();

        # $backtrace = array_reverse(debug_backtrace());

    }else {
        // caught exception
        $exc     = func_get_arg(0);
        $errno   = $exc->getCode();
        $errstr  = $exc->getMessage();
        $errfile = $exc->getFile();
        $errline = $exc->getLine();

        // $backtrace = $exc->getTrace();
        // _debugLog($backtrace);
    }

    $errorType = array(
               0                => '@SUPPRESSED',           // 0
               E_ERROR          => 'ERROR',                 // 1
               E_WARNING        => 'WARNING',               // 2
               E_PARSE          => 'PARSING ERROR',         // 4
               E_NOTICE         => 'NOTICE',                // 8
               E_CORE_ERROR     => 'CORE ERROR',            // 16
               E_CORE_WARNING   => 'CORE WARNING',          // 32
               E_COMPILE_ERROR  => 'COMPILE ERROR',         // 64
               E_COMPILE_WARNING => 'COMPILE WARNING',      // 128
               E_USER_ERROR     => 'USER ERROR',            // 256
               E_USER_WARNING   => 'USER WARNING',          // 512
               E_USER_NOTICE    => 'USER NOTICE',           // 1024
               E_STRICT         => 'STRICT NOTICE',         // 2048
               E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'  // 4096
              );

    // create error message
    if (array_key_exists($errno, $errorType)) {
        $err = $errorType[$errno];
    } else {
        $err = 'CAUGHT EXCEPTION';
    }

    $out = '';
    /* Don't execute PHP internal error handler */
    $collapsestr = '<span class="sa_expand sa_icon_open"></span><span class="sa_collapse">';
    $str = '<span class="ERROR"><span class="titlebar '.strtolower($err).'" title="(' . sa_get_path_rel($errfile) . ' ' . $errline . ')">PHP '.$err.bmark_line().'</span>'; 
    $str.= $collapsestr;
    $err = sa_debug_handler($errno, $errstr, $errfile, $errline, $errcontext);

    $out .= $str.$err;

    $backtraceall = sa_dev_getconfig('showerrorbacktracealways'); // @todo OPTION if false will not backtrace notices
    if( ($errno!== E_USER_NOTICE and $errno!== E_NOTICE and $errno !== 0) or $backtraceall == true){
      $out .= '<div><span class="cm-default"><b>Backtrace</b></span><span class="cm-tag"> &rarr; </span></div>';
      $backtrace = nl2br(sa_debug_backtrace(2)); // skip level = 2,  skipping sa_debug_backtrace(), sa_dev_errorhandler()
      $out .= $backtrace == '' ? 'backtrace not available' : $backtrace;
    }

    $out .= '</span>';
    debugLog($out);
    $showerrorcontext = sa_dev_getconfig('showerrorcontext'); // @todo OPTION show error context, array of all variables in scope when error occured
    if($showerrorcontext) _debugLog("ERROR context",$errcontext);

    switch ($errno) {
        case 0:
        case E_NOTICE:
        case E_USER_NOTICE:
        case E_WARNING:
        case E_USER_WARNING:
            return;
            break;

        default:
          # exit();
    }

    return true;
}

function sa_debug_handler($errno, $errstr, $errfile, $errline, $errcontext){
        $ret = '<span class="cm-default">'
        .'<span class="cm-variable-2">'.$errstr.'</span>'
        .'<span class="cm-comment"> in </span>'
        .'<span class="cm-bracket">[</span>'
        .'<span class="cm-atom" title="'.$errfile.'">'. sa_get_path_rel($errfile) .'</span>'
        .':'
        .'<span class="cm-string">'. $errline .'</span>'
        .'<span class="cm-bracket">]</span>'
        . '</span><span class="cm-comment divider" style="opacity:.8;"></span>';
    return $ret;
}

function sa_dev_handleShutdown() {
    GLOBAL $GS_debug,$sa_console_sent;
    $error = error_get_last();
    if($error !== NULL){
      if($sa_console_sent == true) $GS_debug = array(); // @todo why wipe this, so post doesnt contain all the same log?
      sa_dev_ErrorHandler($error['type'], $error['message'], $error['file'], $error['line'],array());
      sa_emptyDoc($error);
    }else {
      # echo "shutdown";
    }

    return true;
}

function sa_emptyDoc($error){
  GLOBAL $sa_console_sent, $SITEURL;
  if(isset($error['type']) and ($error['type'] === E_ERROR or $error['type'] === E_USER_ERROR)){
    $errorclass = 'sa_dev_error';
  } else {
    $errorclass='';
  }

  if(!$sa_console_sent){
    echo '<!DOCTYPE html>
      <html lang="en">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
        <title>GETSIMPLE DEVELOPMENT ERROR HANDLER</title>
        <link href="'.$SITEURL.'/plugins/sa_development/css/sa_dev_style.css?v=0.1" rel="stylesheet" media="screen">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js?v=1.7.1"></script>
        <style text/css>
          #sa_gsdebug-wrapper{
            /* fix for fatal broken pages, break out of container */
            position:absolute;
            left:0;
          }
        </style>
      </head>
      <body id="load" class="'.$errorclass .'"><div class="'.$errorclass .'">';
    sa_debugConsole();
    echo '</body></div></html>';
  }else {
    sa_debugConsole();
  }

}

if(sa_user_is_admin()){
  register_shutdown_function('sa_dev_handleShutdown');
  set_error_handler("sa_dev_ErrorHandler");
}

// unsorted functions

function error_level_tostring($intval, $separator){
    // credit to the_bug_the_bug @ php.net
    $errorlevels = array(
        // 4096  => 'E_RECOVERABLE_ERROR',
        2048  => 'STRICT',
        2047  => 'ALL',
        1024  => 'USER_NOTICE',
        512   => 'USER_WARNING',
        256   => 'USER_ERROR',
        128   => 'COMPILE_WARNING',
        64    => 'COMPILE_ERROR',
        32    => 'CORE_WARNING',
        16    => 'CORE_ERROR',
        8     => 'NOTICE',
        4     => 'PARSE',
        2     => 'WARNING',
        1     => 'ERROR');
    $result = '';
    foreach($errorlevels as $number => $name)
    {
        if (($intval & $number) == $number) {
            $result .= ($result != '' ? $separator : '').$name; }
    }
    return $result == '' ? 'NONE' : $result;
}

function sa_getErrorReporting()
{  // credit to DarkGool @ php.net
  $bit = ini_get('error_reporting');
  while ($bit > 0) {
      for($i = 0, $n = 0; $i <= $bit; $i = 1 * pow(2, $n), $n++) {
          $end = $i;
      }
      $res[] = $end;
      $bit = $bit - $end;
  }
  return $res;
}

function sa_setErrorReporting($int = 0){
  match ($int) {
      0 => error_reporting(0),
      1 => error_reporting(E_ERROR | E_WARNING | E_PARSE),
      2 => error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE),
      3 => error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)),
      4 => error_reporting(E_ALL ^ E_NOTICE),
      5 => error_reporting(E_ALL),
      default => error_reporting(E_ALL),
  };
}

function sa_getErrorChanged(){
  GLOBAL $sa_phperr;
  if($sa_phperr != error_reporting()){
    $sa_phperr = error_reporting();
    return true;
  }
}

// add_action('settings-website-extras','sa_settings_extras');

function sa_settings_extras(){
  // echo 'sa_settings_extras';
  include('sa_development/settings.php');
}

/**
  @fixed: @float(NAN 
  @fixed: @float(INF
  @fixed: @todo: backtrace does not show current function shows last include and stops

  @fixed: xdebug takes over var_dump, fucks it all up, disabling durign var_dump, @todo: make a function wrapper fo use anytime.
  @todo: automatic timestamp detection, detect unixtime strings and show time formatted
  @todo: auto colors highlighting and html syntax highlighting, rgb, and #hex, edtect html and run html highlight on it.
  @todo: append console to end of document attempt to take out of flow for fatal php errors, as they mess up layout, also load asset detection so not loading twice on fatal past header
  @todo: backtrace line show
  @todo: backtrace filename highlight better than path
  @todo: highlight path paths, filename as above
  @todo: php script timeout handling report the last function running
  @todo: javascript handlers, ajax handlers, error handlers, console output of php errors
  @todo: write proper parser to replace the preg replacers 
  @todo: showing supressed errors, does a post footer dump, missing normal debug console as if fatal
**/

?>

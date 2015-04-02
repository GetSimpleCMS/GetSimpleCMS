<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Getsimple Assets Init
 * @todo  add asset filters to allow for compressors modification etc
 * @package GetSimple
 * @subpackage assets
 */

$GS_scripts = array();  // global array for storing queued script assets
/*
    $GS_scripts[$handle] = array(
        'name'      => handle,
        'src'       => src file,
        'ver'       => version,
        'in_footer' => in_footer,
        'where'     => 0 bitflag,
        'load'      => (bool) is queued,
        'queue'     => array of additional assets to queue
    );
*/
$GS_styles = array();  // glboal array for storing queued stylesheet assets
/*
    $GS_styles[$handle] = array(
        'name'      => handle,
        'src'       => src file,
        'ver'       => version,
        'media'     => style media eg. screen,print,
        'where'     => 0 bitflag,
        'load'      => (bool) is queued,
        'queue'     => array of additional assets to queue
        
    );
*/

// frontend, backend or both for script location load flags


$GS_script_assets = array(); // defines asset scripts
$GS_style_assets  = array();  // defines asset styles

$GS_asset_objects = array(); // holds asset js object names
$GS_asset_objects['jquery']    = 'jQuery';
$GS_asset_objects['jquery-ui'] = 'jQuery.ui'; 


$VERSIONS = array(
  'getsimple'         =>  GSVERSION,
  'ckeditor'          => '4.4.6',
  'codemirror'        => '5.1.0',
  'dropzone'          => '4.0.1',
  'fancybox'          => '2.1.5',
  'font-awesome'      => '4.3.0',
  'jcrop'             => '0.9.12',
  'jquery'            => '2.1.3',
  'jqueryui'          => '1.11.3',
  'jquery-mousewheel' => '3.1.12',
  'scrolltofixed'     => '0.0.1',
  'spin.js'           => '2.0.1',
  'nestable'          => '1.2.2'
);

// long form
// $GS_script_assets = array(
// 	'jquery' => array(
// 		'cdn' => array(
// 			'url' => '//ajax.googleapis.com/ajax/libs/jquery/'.$VERSIONS['jquery'].'/jquery.min.js',
// 			'ver' => $VERSIONS['jquery']	
// 		),
// 		'local' => array(
// 			'url' => $ASSETURL.$GSADMIN.'/template/js/jquery/jquery-'.$VERSIONS['jquery'].'.min.js',
// 			'ver' => $VERSIONS['jquery']
// 		)
// 	)	
// )

/**
 * Core assets
 */

$ASSETPATH = $ASSETURL.tsl(getRelPath(GSADMINTPLPATH));

// core
$GS_script_assets['getsimple']['local']['url']     = $ASSETPATH.'js/jquery.getsimple.js';
$GS_script_assets['getsimple']['local']['ver']     = $VERSIONS['getsimple'];

// lazyload (lazy loading assets js/css)
$GS_script_assets['lazyload']['local']['url']      = $ASSETPATH.'js/lazyload.js';
$GS_script_assets['lazyload']['local']['ver']      = $VERSIONS['getsimple'];

// gstree (collpaseble heirarchy table tree) 
$GS_script_assets['gstree']['local']['url']        = $ASSETPATH.'js/jquery-gstree.js';
$GS_script_assets['gstree']['local']['ver']        = $VERSIONS['getsimple'];

// spin (ajax spinners)
$GS_script_assets['spin']['local']['url']          = $ASSETPATH.'js/spin.js';
$GS_script_assets['spin']['local']['ver']          = $VERSIONS['spin.js'];

// dropzone (ajax/html uploader w drag and drop)
$GS_script_assets['dropzone']['local']['url']      = $ASSETPATH.'js/dropzone.js';
$GS_script_assets['dropzone']['local']['ver']      = $VERSIONS['getsimple'];

// jcrop
$GS_script_assets['jcrop']['local']['url']        = $ASSETPATH.'js/jcrop/jquery.Jcrop.min.js';
$GS_script_assets['jcrop']['local']['ver']        = $VERSIONS['getsimple'];
$GS_script_assets['jcrop']['queue']['style']      = 'jcrop';

 $GS_style_assets['jcrop']['local']['url']        = $ASSETPATH.'js/jcrop/jquery.Jcrop.min.css';
 $GS_style_assets['jcrop']['local']['ver']        = $VERSIONS['getsimple'];


// jcrop
$GS_script_assets['nestable']['local']['url']     = $ASSETPATH.'js/nestable/jquery.nestable.js';
$GS_script_assets['nestable']['local']['ver']     = $VERSIONS['nestable'];
$GS_script_assets['nestable']['queue']['style']   = 'nestable';
 $GS_style_assets['nestable']['local']['url']     = $ASSETPATH.'js/nestable/jquery.nestable.css';
 $GS_style_assets['nestable']['local']['ver']     = $VERSIONS['nestable'];


/**
 * External assets
 */

// jquery
$GS_script_assets['jquery']['cdn']['url']          = '//ajax.googleapis.com/ajax/libs/jquery/'.$VERSIONS['jquery'].'/jquery.min.js';
$GS_script_assets['jquery']['cdn']['ver']          = $VERSIONS['jquery'];
$GS_script_assets['jquery']['local']['url']        = $ASSETPATH.'js/jquery/jquery-'.$VERSIONS['jquery'].'.min.js';
$GS_script_assets['jquery']['local']['ver']        = $VERSIONS['jquery'];

// jquery-ui
$GS_script_assets['jquery-ui']['cdn']['url']       = '//ajax.googleapis.com/ajax/libs/jqueryui/'.$VERSIONS['jqueryui'].'/jquery-ui.min.js';
$GS_script_assets['jquery-ui']['cdn']['ver']       = $VERSIONS['jqueryui'];
$GS_script_assets['jquery-ui']['local']['url']     = $ASSETPATH.'js/jqueryui/js/jquery-ui-'.$VERSIONS['jqueryui'].'.custom.min.js';
$GS_script_assets['jquery-ui']['local']['ver']     = $VERSIONS['jqueryui'];
 $GS_style_assets['jquery-ui']['local']['url']     = $ASSETPATH.'js/jqueryui/css/custom/jquery-ui-'.$VERSIONS['jqueryui'].'.custom.min.css';
 $GS_style_assets['jquery-ui']['local']['ver']     = $VERSIONS['jqueryui'];

// scrolltofixed
$GS_script_assets['scrolltofixed']['local']['url'] = $ASSETPATH.'js/jquery-scrolltofixed.js';
$GS_script_assets['scrolltofixed']['local']['ver'] = $VERSIONS['scrolltofixed'];

// codemirror
$GS_script_assets['codemirror']['local']['url']    = $ASSETPATH.'js/codemirror/lib/codemirror-compressed.js';
$GS_script_assets['codemirror']['local']['ver']    = $VERSIONS['codemirror'];
 $GS_style_assets['codemirror']['local']['url']    = $ASSETPATH.'js/codemirror/lib/codemirror.css';
 $GS_style_assets['codemirror']['local']['ver']    = $VERSIONS['codemirror'];

// fancybox
$GS_script_assets['fancybox']['local']['url']      = $ASSETPATH.'js/fancybox/jquery.fancybox.pack.js';
$GS_script_assets['fancybox']['local']['ver']      = $VERSIONS['fancybox'];
 $GS_style_assets['fancybox']['local']['url']      = $ASSETPATH.'js/fancybox/jquery.fancybox.css';
 $GS_style_assets['fancybox']['local']['ver']      = $VERSIONS['fancybox'];
$GS_script_assets['fancybox']['queue']['script']   = 'jquery-mousewheel';


$GS_script_assets['jquery-mousewheel']['local']['url']      = $ASSETPATH.'js/fancybox/jquery.mousewheel-3.0.6.pack.js';
$GS_script_assets['jquery-mousewheel']['local']['ver']      = $VERSIONS['jquery-mousewheel'];


 // FANCYBOX DEPRECATED
 $GS_style_assets['fancybox-css']['local']['url']  = $GS_style_assets['fancybox']['local']['url']; // @LEGACY 3.4
 $GS_style_assets['fancybox-css']['local']['ver']  = $GS_style_assets['fancybox']['local']['ver']; // @LEGACY 3.4

// font-awesome icons
 $GS_style_assets['font-awesome']['cdn']['url']    = '//netdna.bootstrapcdn.com/font-awesome/'.$VERSIONS['font-awesome'].'/css/font-awesome.min.css';
 $GS_style_assets['font-awesome']['cdn']['ver']    = $VERSIONS['font-awesome'];
 $GS_style_assets['font-awesome']['local']['url']  = $ASSETPATH.'css/font-awesome.min.css';
 $GS_style_assets['font-awesome']['local']['ver']  = $VERSIONS['font-awesome'];

// ckeditor
$GS_script_assets['ckeditor']['cdn']['url']        = '//cdn.ckeditor.com/'.$VERSIONS['ckeditor'].'/full/ckeditor.js';
$GS_script_assets['ckeditor']['cdn']['ver']        = $VERSIONS['ckeditor'];
$GS_script_assets['ckeditor']['local']['url']      = $ASSETPATH.'js/ckeditor/ckeditor.js';
$GS_script_assets['ckeditor']['local']['ver']      = $VERSIONS['ckeditor'];


/**
 * Core alias groups
 */

// gs codeeditor
$GS_script_assets['gscodemirror']['local']['url']     = $ASSETPATH.'js/codemirror.getsimple.js';
$GS_script_assets['gscodemirror']['local']['ver']     = $VERSIONS['getsimple'];
$GS_script_assets['gscodemirror']['queue']['script']  = 'codemirror';
$GS_script_assets['gscodemirror']['queue']['style']   = 'codemirror';

// gs htmleditor
$GS_script_assets['gsckeditor']['local']['url']     = $ASSETPATH.'js/ckeditor.getsimple.js';
$GS_script_assets['gsckeditor']['local']['ver']     = $VERSIONS['getsimple'];
$GS_script_assets['gsckeditor']['queue']['script']  = 'ckeditor';

// gs uploader
$GS_script_assets['gsdropzone']['local']['url']     = $ASSETPATH.'js/dropzone.getsimple.js';
$GS_script_assets['gsdropzone']['local']['ver']     = $VERSIONS['getsimple'];
$GS_script_assets['gsdropzone']['queue']['script']  = 'dropzone';

/**
 * Register shared javascript/css scripts for loading into the header
 */

$infooter = false;
$nocdn    = getDef('GSNOCDN',true);

// preregister all scripts assets into GS_scripts_assets, so they are available for queueing
preRegisterScript('jquery',       '', !$nocdn , false);
preRegisterScript('jquery-ui',    '', !$nocdn , false);
preRegisterScript('font-awesome', '', !$nocdn , $infooter);
preRegisterScript('getsimple',    '',   false , $infooter);
preRegisterScript('lazyload',     '',   false , $infooter);
preRegisterScript('spin',         '',   false , $infooter);
preRegisterScript('jcrop',        '',   false , $infooter);
preRegisterScript('gstree',       '',   false , $infooter);
preRegisterScript('ckeditor',     '',   false , $infooter); // cdn disabled, http://cdn.ckeditor.com/, requires explicit setting of config and plugin paths to local
preRegisterScript('codemirror',   '',   false , $infooter);
preRegisterScript('dropzone',     '',   false , $infooter);
preRegisterScript('fancybox',     '',   false , $infooter);
preRegisterScript('jquery-mousewheel',     '',   false , $infooter);
preRegisterScript('scrolltofixed','',   false , $infooter);
preRegisterScript('nestable',     '',   false , $infooter);

// gs aliases
preRegisterScript('gshtmleditor', $GS_script_assets['gsckeditor'],     false , $infooter);
preRegisterScript('gscodeeditor', $GS_script_assets['gscodemirror'],   false , $infooter);
preRegisterScript('gscrop',       $GS_script_assets['jcrop'],          false , $infooter);
preRegisterScript('gsuploader',   $GS_script_assets['gsdropzone'],     false , $infooter);

preRegisterStyle('font-awesome',  '', !$nocdn , 'screen');
preRegisterStyle('codemirror',    '',   false , 'screen');
preRegisterStyle('jcrop',         '',   false , 'screen');
preRegisterStyle('fancybox-css',  '',   false , 'screen'); // DEPRECATED legacy , styleid not matching scriptid is confusing.
preRegisterStyle('fancybox',      '',   false , 'screen');
preRegisterStyle('jquery-ui',     '',   false , 'screen');
preRegisterStyle('nestable',      '',   false , 'screen');

/**
 * Queue our scripts and styles for the backend
 */
queue_script('jquery'        , GSBACK);
queue_script('jquery-ui'     , GSBACK);
queue_script('getsimple'     , GSBACK);
queue_script('lazyload'      , GSBACK);
queue_script('spin'          , GSBACK);
queue_script('gstree'        , GSBACK);
queue_script('fancybox'      , GSBACK);
queue_script('scrolltofixed' , GSBACK);

queue_style('fancybox'       , GSBACK);
queue_style('jquery-ui'      , GSBACK);
// queue_style('jquery-ui-theme', GSBACK); // unused, reserved for custom GS jquery ui theme if ever needed
queue_style('font-awesome'   , GSBACK);


/**
 * ASSET FUNCTIONS
 */

/**
 * preregister scripts
 * helper for using global arrays to build script asset registration
 * 
 * @since 3.4
 * @param  str  $id     id of script asset
 * @param  boolean $CDN    use cdn if available
 * @param  boolean $footer put in footer
 * @return bool
 */
function preRegisterScript($id,$config = array(),$CDN = false,$footer = false){
  GLOBAL $GS_script_assets;
  if(!$config && isset($GS_script_assets[$id])) $config = $GS_script_assets[$id];
  if(!$config) return;
  $queue = isset($config['queue']) ? $config['queue'] : null;
  if($CDN && isset($config['cdn'])) return register_script($id, $config['cdn']['url'], '', $footer, $queue); // no version for CDN benefits
  else return register_script($id, $config['local']['url'], $config['local']['ver'], $footer, $queue);
}

/**
 * Register Script
 *
 * Register a script to include in Themes
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script
 * @param string $src location of the src for loading
 * @param string $ver script version
 * @param boolean $in_footer load the script in the footer if true
 * @param array $queue array of script or style assets to auto queue
 */
function register_script($handle, $src, $ver, $in_footer = false, $queue = null){
  global $GS_scripts;
  $GS_scripts[$handle] = array(
    'name'      => $handle,
    'src'       => $src,
    'ver'       => $ver,
    'in_footer' => $in_footer,
    'where'     => 0,
    'load'      => false,
    'queue'     => $queue
  );
}

/**
 * De-Register Script
 *
 * Deregisters a script
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script to remove
 */
function deregister_script($handle){
  global $GS_scripts;
  if (array_key_exists($handle, $GS_scripts)){
    unset($GS_scripts[$handle]);
  }
}

/**
 * Queue Script
 *
 * Queue a script for loading
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script to load
 */
function queue_script($handle,$where){
  global $GS_scripts;
  if (array_key_exists($handle, $GS_scripts)){
        // load items queue
        if(isset($GS_scripts[$handle]['queue'])){ 
            $config = $GS_scripts[$handle]['queue'];
            
            if(isset($config['script'])){
                if(!is_array($config['script'])) $config['script'] = explode(',',$config['script']);
                array_map('queue_script',$config['script'],array_fill(0,count($config['script']),$where));
            }

            if(isset($config['style'])){
                if(!is_array($config['style']))  $config['style'] = explode(',',$config['style']);
                array_map('queue_style',$config['style'],array_fill(0,count($config['style']),$where));
            }
        }

        $GS_scripts[$handle]['load']  = true;
        $GS_scripts[$handle]['where'] = $GS_scripts[$handle]['where'] | $where;
  }
}

/**
 * De-Queue Script
 *
 * Remove a queued script
 *
 * @since 3.1
 * @uses $GS_scripts
 *
 * @param string $handle name for the script to load
 */
function dequeue_script($handle, $where){
  global $GS_scripts;
  if (array_key_exists($handle, $GS_scripts)){
    $GS_scripts[$handle]['load']  = false;
    $GS_scripts[$handle]['where'] = $GS_scripts[$handle]['where'] & ~ $where;
  }
}

/**
 * Get Scripts for backend
 *
 * @since 3.1 *
 * @param boolean $footer Load only script with footer flag set
 */
function get_scripts_backend($footer = false){
  echo getScripts(GSBACK,$footer);
}

/**
 * Get Scripts
 *
 * Echo and load scripts via queue depending on if in footer and on front or back
 *
 * @since 3.4
 * @uses $GS_scripts
 *
 * @param int $facing GSBACK or GSFRONT constant
 * @param boolean $footer Load only script with footer flag set
 */
function getScripts($facing = GSBACK, $footer = false){
  global $GS_scripts;

  $str = '';

  if (!$footer){
    $facing === GSBACK ? get_styles_backend() : get_styles_frontend();
  }

  // debugLog($GS_scripts);
  foreach ($GS_scripts as $script){
    if ($script['load'] == true && ($script['where'] & $facing) ){
      if($footer !== $script['in_footer']) continue;
      $str.= '<script src="'.$script['src'].( !empty($script['ver']) ? '?v='.$script['ver'] : '' ) . '"></script>'."\n";
      cdn_fallback($script);  
    }
  }

  return $str;
}


/**
 * Add javascript for cdn fallback to local
 * get_scripts_backend helper
 * @param  array $script gsscript array
 */
function cdn_fallback($script){
  echo build_cdn_fallback($script);
}

function build_cdn_fallback($script){
  GLOBAL $GS_script_assets, $GS_asset_objects; 
  $str = '';
  if (getDef('GSNOCDN',true)) return; // if nocdn skip
  if($script['name'] == 'jquery' || $script['name'] == 'jquery-ui'){
    $str .= "<script>";
    $str .= "window.".$GS_asset_objects[$script['name']]." || ";
    $str .= "document.write('<!-- CDN FALLING BACK --><script src=\"".$GS_script_assets[$script['name']]['local']['url'].'?v='.$GS_script_assets[$script['name']]['local']['ver']."\"><\/script>');";
    $str .= "</script>\n";
  }

  return $str;    
}

/**
 * Queue Style
 *
 * Queue a Style for loading
 *
 * @since 3.1
 * @uses $GS_styles
 *
 * @param string $handle name for the Style to load
 */
function queue_style($handle,$where = 1){
  global $GS_styles;
  if (array_key_exists($handle, $GS_styles)){

    // load items queue
    if(isset($GS_scripts[$handle]['queue'])){
      $config = $GS_scripts[$handle]['queue'];
      if(isset($config['style']))  array_map('queue_style',$config['style'],array_fill(0,count($config['style']),$where));
    }

    $GS_styles[$handle]['load'] = true;
    $GS_styles[$handle]['where'] = $GS_styles[$handle]['where'] | $where;
  }
}

/**
 * De-Queue Style
 *
 * Remove a queued Style
 *
 * @since 3.1
 * @uses $GS_styles
 *
 * @param string $handle name for the Style to load
 */
function dequeue_style($handle,$where){
  global $GS_styles;
  if (array_key_exists($handle, $GS_styles)){
    $GS_styles[$handle]['load'] = false;
    $GS_styles[$handle]['where'] = $GS_styles[$handle]['where'] & ~$where;
  }
}

/**
 * preregister style
 * helper for using global arrays to build script asset registration
 * 
 * @since 3.4
 * @param  str  $id     id of style asset
 * @param  boolean $CDN    use cdn if available
 * @param  boolean $footer put in footer
 * @return bool
 */
function preRegisterStyle($id,$config = array(), $CDN = false, $media = 'screen'){
  GLOBAL $GS_style_assets;
  if(!$config && isset($GS_style_assets[$id])) $config = $GS_style_assets[$id];
  if(!$config) return;
  $queue = isset($config['queue']) ? $config['queue'] : null;
  if($CDN && isset($config['cdn'])) return register_style($id, $config['cdn']['url'], '', $media,$queue); // no version for CDN benefits
  else return register_style($id, $config['local']['url'], $config['local']['ver'], $media,$queue);
}


/**
 * Register Style
 *
 * Register a Style to include in Themes
 *
 * @since 3.1
 * @uses $GS_styles
 *
 * @param string $handle name for the Style
 * @param string $src location of the src for loading
 * @param string $ver Style version
 * @param string $media the media for this stylesheet
 * @param array $queue array of style assets to auto queue
 */
function register_style($handle, $src, $ver, $media, $queue = null){
  global $GS_styles;
  $GS_styles[$handle] = array(
    'name'  => $handle,
    'src'   => $src,
    'ver'   => $ver,
    'media' => $media,
    'where' => 0,
    'load'  => false,
    'queue' => $queue
  );
}


/**
 * Get Styles Backend
 * @since 3.1
  */
function get_styles_backend(){
  echo getStyles(GSBACK);
}


/**
 * Get Styles Backend
 *
 * Echo and load Styles on Front or Back
 *
 * @since 3.4
 * @uses $GS_styles
 *
 */
function getStyles($facing = GSBACK){
  global $GS_styles;
  $str = '';
  foreach ($GS_styles as $style){
    if ($style['where'] & $facing ){
        if ($style['load'] == true){
          $str .= '<link href="'.$style['src']. ( !empty($script['ver']) ? '?v='.$script['ver'] : '' ) . '" rel="stylesheet" media="'.$style['media'].'">'."\n";
        }
    }
  }
  return $str;
}

/* ?> */

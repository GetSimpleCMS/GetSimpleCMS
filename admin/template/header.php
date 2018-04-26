<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 * 
 * @package GetSimple
 */

// this is included anonymously, MUST SET GLOBALS!
GLOBAL $SITENAME, $SITEURL, $GSADMIN, $themeselector, $pagetitle, $SESSIONHASH, $SAFEMODE;

// special style flags
$GSSTYLE         = getDef('GSSTYLE') ? GSSTYLE : '';
$GSSTYLE_sbfixed = in_array('sbfixed',explode(',',$GSSTYLE));
$GSSTYLE_wide    = in_array('wide',explode(',',$GSSTYLE));

// set up body classes
$bodyclass='';
if( $GSSTYLE_sbfixed )          $bodyclass .= " sbfixed";
if( $GSSTYLE_wide )             $bodyclass .= " wide";
if( $SAFEMODE )                 $bodyclass .= " safemode";
if( getDef("GSTHUMBSSHOW",true))$bodyclass .= " forcethumbs";
if( getDef("GSPAGETABS",true))  $bodyclass .= " tabs";
if( getDef('GSNOSIDEBAR',true) && in_array(get_filename_id(),getDef('GSNOSIDEBAR',false,true))) $bodyclass .= " nosidebar";	

if( !$SAFEMODE && getDef('GSAJAXSAVE',true) ) $bodyclass .= " ajaxsave"; // ajaxsave enabled if GSAJAXSAVE and not SAFEMODE

if(!isPage('index')) exec_action('admin-pre-header'); // @hook admin-pre-header backend before header output

if(!isset($pagetitle)) $pagetitle = i18n_r(get_filename_id().'_title');
$title = $pagetitle.' &middot; '.cl($SITENAME);

?><!DOCTYPE html>
<html lang="<?php echo get_site_lang(true); ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo $title ?></title>
	<?php if(!isAuthPage()) { ?> <meta name="generator" content="GetSimple - <?php echo GSVERSION; ?>" />
	<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png"/>
	<?php } ?>
	<meta name="robots" content="noindex, nofollow">
	<link rel="stylesheet" type="text/css" href="template/style.php?<?php echo 's='.$GSSTYLE.'&amp;v='.GSVERSION; ?>" media="screen" />
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/css/ie6.css?v=<?php echo GSVERSION; ?>" media="screen" /><![endif]-->
<?php

	// setup some stuf here for now
	$cm_themes = explode(',',getDef('GSCODEEDITORTHEMES'));

	// build theme selector
	$themeselector = '<select id="cm_themeselect">\n<option>default</option>';
	foreach($cm_themes as $theme){
		$themeselector .= "<option>$theme</option>";
	}
	$themeselector .= '</select>';

	// js i18n tokens
	$jsi18nkeys = array(
		'ERROR',
		'ERROR_OCCURED',
		'EXPAND_TOP',
		'COLLAPSE_TOP',
		'FILE_EXISTS_PROMPT',
		'CANCELLED',
		'UNSAVED_INFORMATION',
		'UNSAVED_PROMPT',
		'CANNOT_SAVE_EMPTY',
		'COMPONENT_DELETED',
		'CANNOT_SAVE_EMPTY',
		'PAGE_UNSAVED',
		'MINIMIZENOTIFY',
		'SELECT_FILE'
	);

	// i18n for JS
	$jsi18n = array_combine($jsi18nkeys,array_map('i18n_r',$jsi18nkeys));

	?>
	<!--[if lt IE 9]><script type="text/javascript" src="//html5shiv.googlecode.com/svn/trunk/html5.js" ></script><![endif]-->
	<?php

	// load gscodeeditor
	if (!getDef('GSNOHIGHLIGHT',true) || getDef('GSNOHIGHLIGHT')!=true){
		queue_script('gscodeeditor', GSBACK);
	}

	if( (isPage('snippets') || isPage('edit') || isPage('backup-edit')) && getGSVar('HTMLEDITOR') ){
		queue_script('gshtmleditor',GSBACK);
	}

	// load gsuploader
	if( (isPage('upload') || isPage('filebrowser') || isPage('image')) && (getDef('GSUSEGSUPLOADER',true)) ){
		queue_script('gsuploader',GSBACK);
	}

	// load gscrop image editor
	if(isPage('image')){
		queue_script('gscrop',GSBACK);
		queue_style('gscrop',GSBACK);
	}
	
    // HTMLEDITOR INIT
    // ckeditor contentsCss(editor.css) from theme
    if (file_exists(GSTHEMESPATH .getGSVar('TEMPLATE')."/editor.css")) {
        $CKEcontentsCss = $SITEURL.getRelPath(GSTHEMESPATH).getGSVar('TEMPLATE').'/editor.css';
    }
    // ckeditor contentsCss(contents.css) override from user
    if (file_exists(GSTHEMESPATH .getDef('GSEDITORCSSFILE'))) {
        $CKEcontentsCss = $SITEURL.getRelPath(GSTHEMESPATH).getDef('GSEDITORCSSFILE');
    }
    // ckeditor customconfig
    if (file_exists(GSTHEMESPATH .getDef('GSEDITORCONFIGFILE'))) {
        $CKEconfigjs =  $SITEURL.getRelPath(GSTHEMESPATH).getDef('GSEDITORCONFIGFILE');
    }
    // ckeditor stylesheet
    if (file_exists(GSTHEMESPATH.getDef('GSEDITORSTYLESFILE'))) {
        $CKEstyleSet = getDef('GSEDITORSTYLESID').":".$SITEURL.getRelPath(GSTHEMESPATH).getDef('GSEDITORSTYLESFILE');
    }

    function isAutoSave(){
    	if(getDef('GSUSEDRAFTS',true) && !isset($_REQUEST['nodraft']) && isset($_REQUEST['id'])){
    		return true;
    	}
    	return false;
    }
    ?>

    <script type="text/javascript">
    	// @todo clean this up, use a better bridge to initialize config variables in js
    	
        // init gs namespace and i18n
        var GS     = {};
        GS.i18n    = <?php echo json_encode($jsi18n); ?>;
        GS.debug   = <?php echo isDebug() === true ? 'true' : 'false'; ?> ;
        GS.siteurl = '<?php echo $SITEURL; ?>';
        GS.uploads = '<?php echo tsl($SITEURL).getRelPath(GSDATAUPLOADPATH); ?>';

		var uploadSession = '<?php echo $SESSIONHASH; ?>';
		var uploadPath    = '<?php echo (isset($_GET['path'])) ? $_GET['path'] : ""; ?>';
		var maxFileSize   = '<?php echo toBytesShorthand(getMaxUploadSize(),'M'); ?>';
		
		<?php
        if(isset($_COOKIE['gs_editor_theme'])){
            // $editor_theme = var_out($_COOKIE['gs_editor_theme']);
            $editor_theme = var_out($_COOKIE['gs_editor_theme']);
            echo "// codemirror editortheme\n";
            echo '		var editorTheme = "'.$editor_theme."\";\n";
        }

        if(isPage('edit') && isAutoSave()){
        	$autosaveintvl = getdef('GSAUTOSAVEINTERVAL');
        	echo "		// edit autosave\n";
        	echo '		var GSAUTOSAVEPERIOD = ' . (!is_int($autosaveintvl) ? 10 : $autosaveintvl).";\n";
        } else echo "      var GSAUTOSAVEPERIOD = false;\n";
        ?>

        // ckeditor config obj shim for config
        if(typeof CKEDITOR == 'undefined'){
			CKEDITOR           = {};
			CKEDITOR.SHIM      = true;
			CKEDITOR.ENTER_P   = 1;
			CKEDITOR.ENTER_BR  = 2;
			CKEDITOR.ENTER_DIV = 3;
        }

        var htmlEditorConfig = {
            language                     : '<?php echo getGSVar('EDLANG'); ?>',
<?php       if(!empty($CKEcontentsCss)) echo "contentsCss                   : '$CKEcontentsCss',"; ?>
<?php       if(!empty($CKEconfigjs))    echo "customConfig                  : '$CKEconfigjs',"; ?>
<?php       if(!empty($CKEstyleSet))    echo "stylesSet                     : '$CKEstyleSet',"; ?>
            height                       : '<?php echo getGSVar('EDHEIGHT'); ?>',
            baseHref                     : '<?php echo getGSVar('SITEURL'); ?>'
            <?php if(getGSVar('EDTOOL')) echo ",toolbar: " . returnJsArray(getGSVar('EDTOOL')); ?>
<?php       if(getGSVar('EDOPTIONS')) echo ','.trim(getGSVar('EDOPTIONS')); ?>
			<?php if(getDef("GSCKETSTAMP",true)) echo ",timestamp : '".getDef("GSCKETSTAMP") . "'\n"; ?>
        };

        // wipe the ckeditor shim, so it does not interfere with the real one
        if(typeof CKEDITOR !== 'undefined'){
            if(CKEDITOR.SHIM == true) CKEDITOR = null;
        }

       <?php 
       if(isPage('snippets')) echo "htmlEditorConfig.height = '130px';"; ?>

    </script>

	<?php

	// load scripts after globals set
	get_scripts_backend();

	?>
    <script type="text/javascript">
		jQuery(document).ready(function () {
			// disable page editing during safemode
	       	$('body#edit.safemode :input').prop("disabled", true);
	    });
    </script>

    <?php
	# Plugin hook to allow insertion of stuff into the header
	if(!isAuthPage()) exec_action('header'); // @hook header backend before html head closes

	?>

<noscript>
	<style>
		body.tabs .tab{ display:block; clear:both;}
		body.tabs .tab fieldset legend{ display: block; }
		#cm_themeselect, #cm_themeselect_label { display:none;}
		#theme_filemanager ul ul {
			display: block;
		}
	</style>
</noscript>

</head>
<?php $headerclass = getDef('GSHEADERCLASS',true) ? getDef('GSHEADERCLASS') : ''?>
<body <?php filename_id(); echo ' class="'.$bodyclass.'"'; ?> >
	<div class="header <?php echo $headerclass; ?>" id="header" >
		<div class="wrapper clearfix">
 <?php exec_action('header-body'); // @hook header-body backend header body wrapper html ?>

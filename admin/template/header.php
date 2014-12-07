<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 *
 * @package GetSimple
 */

GLOBAL $SITENAME, $SITEURL, $GSADMIN, $themeselector, $pagetitle, $SESSIONHASH;

$GSSTYLE = getDef('GSSTYLE') ? GSSTYLE : '';
$GSSTYLE_sbfixed = in_array('sbfixed',explode(',',$GSSTYLE));
$GSSTYLE_wide    = in_array('wide',explode(',',$GSSTYLE));

$bodyclass="class=\"";
if( $GSSTYLE_sbfixed ) $bodyclass .= " sbfixed";
if( $GSSTYLE_wide )    $bodyclass .= " wide";
if( getDef('GSAJAXSAVE',true) ) $bodyclass .= " ajaxsave";
$bodyclass .="\"";

if(get_filename_id()!='index') exec_action('admin-pre-header'); // @hook admin-pre-header backend before header output

if(!isset($pagetitle)) $pagetitle = i18n_r(get_filename_id().'_title');
$title = $pagetitle.' &middot; '.cl($SITENAME);

?><!DOCTYPE html>
<html lang="<?php echo get_site_lang(true); ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo $title ?></title>
	<?php if(!isAuthPage()) { ?> <meta name="generator" content="GetSimple - <?php echo GSVERSION; ?>" />
	<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
	<link rel="author" href="humans.txt" />
	<link rel="apple-touch-icon" href="apple-touch-icon.png"/>
	<?php } ?>
	<meta name="robots" content="noindex, nofollow">
	<link rel="stylesheet" type="text/css" href="template/style.php?<?php echo 's='.$GSSTYLE.'&amp;v='.GSVERSION; ?>" media="screen" />
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/css/ie6.css?v=<?php echo GSVERSION; ?>" media="screen" /><![endif]-->
<?php

	// setup some stuf here for now
	$cm_themes = array(
		'3024-day',
		'3024-night',
		'ambiance',
		'base16-light',
		'base16-dark',
		'blackboard',
		'cobalt',
		'eclipse',
		'eclipse',
		'elegant',
		'erlang-dark',
		'lesser-dark',
		'mbo',
		'midnight',
		'monokai',
		'neat',
		'night',
		'paraiso-dark',
		'paraiso-light',
		'rubyblue',
		'solarized dark',
		'solarized light',
		'the-matrix',
		'twilight',
		'tomorrow-night-eighties',
		'vibrant-ink',
		'xq-dark',
		'xq-light'
	);

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
		'CANNOT_SAVE_EMPTY'
	);

	// i18n for JS
	$jsi18n = array_combine($jsi18nkeys,array_map('i18n_r',$jsi18nkeys));

	?>
	<!--[if lt IE 9]><script type="text/javascript" src="//html5shiv.googlecode.com/svn/trunk/html5.js" ></script><![endif]-->
	<?php

	if (!getDef('GSNOHIGHLIGHT',true) || getDef('GSNOHIGHLIGHT')!=true){
		queue_script('gscodeeditor', GSBACK);
	}

	if( ((get_filename_id()=='snippets') || (get_filename_id()=='edit') || (get_filename_id()=='backup-edit')) && getGlobal('HTMLEDITOR') ){
		queue_script('gshtmleditor',GSBACK);
	}

	if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!getDef('GSNOUPLOADIFY',true)) ){
		queue_script('gsuploader',GSBACK);
	}

	if(get_filename_id()=='image'){
		queue_script('gscrop',GSBACK);
		queue_style('gscrop',GSBACK);
	}

    // HTMLEDITOR INIT
    if (getGlobal('HTMLEDITOR') != '') {
        if (file_exists(GSTHEMESPATH .getGlobal('TEMPLATE')."/editor.css")) {
            $contentsCss = $SITEURL.getRelPath(GSTHEMESPATH).getGlobal('TEMPLATE').'/editor.css';
        }
    }

    ?>

    <script type="text/javascript">
    	// @todo clean this up, use a better bridge to initialize config variables in js
    	
        // init gs namespace and i18n
        var GS = {};
        GS.i18n = <?php echo json_encode($jsi18n); ?>;
        GS.debug = <?php echo isDebug() === true ? 'true' : 'false'; ?> ;

		var uploadSession = '<?php echo $SESSIONHASH; ?>';
		var uploadPath    = '<?php echo (isset($_GET['path'])) ? $_GET['path'] : ""; ?>';
		var maxFileSize   = '<?php echo toBytesShorthand(getMaxUploadSize().'M',false); ?>';
		
		<?php
        if(isset($_COOKIE['gs_editor_theme'])){
            // $editor_theme = var_out($_COOKIE['gs_editor_theme']);
            $editor_theme = var_out($_COOKIE['gs_editor_theme']);
            echo "// codemirror editortheme\n";
            echo '		var editorTheme = "'.$editor_theme."\";\n";
        }

        if(getDef('GSAUTOSAVE',true)){
        	echo "		// edit autosave\n";
        	echo '		var GSAUTOSAVEPERIOD = ' . getDef('GSAUTOSAVE').";\n";
        } else echo "      var GSAUTOSAVEPERIOD = false;\n";
        ?>

        // ckeditor config obj
        var htmlEditorConfig = {
            language                     : '<?php echo getGlobal('EDLANG'); ?>',
<?php       if(!empty($contentsCss)) echo "contentsCss                   : '$contentsCss',"; ?>
            height                       : '<?php echo getGlobal('EDHEIGHT'); ?>',
            baseHref                     : '<?php echo getGlobal('SITEURL'); ?>'
            <?php if(getGlobal('EDTOOL')) echo ",toolbar: " . returnJsArray(getGlobal('EDTOOL')); ?>
<?php       if(getGlobal('EDOPTIONS')) echo ','.trim(getGlobal('EDOPTIONS')); ?>
        };

       <?php if(get_filename_id() == 'snippets') echo "htmlEditorConfig.height = '100px';"; ?>

    </script>

	<?php

	get_scripts_backend();

	# Plugin hook to allow insertion of stuff into the header
	if(!isAuthPage()) exec_action('header'); // @hook header backend before html head closes

	?>

<noscript>
	<style>
		.tab{ display:block; clear:both;}
		.tab fieldset legend{ display: block; }
		#cm_themeselect, #cm_themeselect_label { display:none;}
		#theme_filemanager ul ul {
			display: block;
		}
	</style>
</noscript>

</head>
<?php $headerclass = getDef('GSHEADERCLASS',true) ? getDef('GSHEADERCLASS') : ''?>
<body <?php filename_id(); echo ' '.$bodyclass; ?> >
	<div class="header <?php echo $headerclass; ?>" id="header" >
		<div class="wrapper clearfix">
 <?php exec_action('header-body'); // @hook header-body backend header body wrapper html ?>

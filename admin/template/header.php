<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 *
 * @package GetSimple
 */
 
global $SITENAME, $SITEURL, $GSADMIN, $themeselector, $HTMLEDITOR;

$GSSTYLE = getDef('GSSTYLE') ? GSSTYLE : '';
$GSSTYLE_sbfixed = in_array('sbfixed',explode(',',$GSSTYLE));
$GSSTYLE_wide    = in_array('wide',explode(',',$GSSTYLE));

$bodyclass="class=\"";
if( $GSSTYLE_sbfixed ) $bodyclass .= " sbfixed";
if( $GSSTYLE_wide )    $bodyclass .= " wide";
$bodyclass .="\"";

if(get_filename_id()!='index') exec_action('admin-pre-header');

?>
<!DOCTYPE html>
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

	if(isset($_COOKIE['gs_editor_theme'])){
		$editor_theme = $_COOKIE['gs_editor_theme'];

		echo '<script>
			var editorTheme = "'.$editor_theme.'";
		</script>';

	}

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

	// build theme seldctor
	$themeselector = '<select id="cm_themeselect">\n<option>default</option>';
	foreach($cm_themes as $theme){
		$themeselector .= "<option>$theme</option>";
	}
	$themeselector .= '</select>';

	?>
	<!--[if lt IE 9]><script type="text/javascript" src="//html5shiv.googlecode.com/svn/trunk/html5.js" ></script><![endif]-->
	<?php
		
	if (!defined('GSNOHIGHLIGHT') || GSNOHIGHLIGHT!=true){
		queue_script('gscodeeditor', GSBACK);
	}

	if( ((get_filename_id()=='edit') || (get_filename_id()=='backup-edit')) && $HTMLEDITOR ){
		queue_script('gshtmleditor',GSBACK); 
	}

	if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!defined('GSNOUPLOADIFY')) ){
		queue_script('gsuploader',GSBACK); 
	}
	
	if(get_filename_id()=='image'){
		queue_script('gscrop',GSBACK);
		queue_style('gscrop',GSBACK);
	}

	get_scripts_backend(); 

	# Plugin hook to allow insertion of stuff into the header
	if(!isAuthPage()) exec_action('header'); 
	
	function doVerCheck(){
		return !isAuthPage() && !getDef('GSNOVERCHECK');
	}
	
	if( doVerCheck() ) { ?>
	<script type="text/javascript">		
		// check to see if core update is needed
		jQuery(document).ready(function() { 
			<?php 
				$data = get_api_details();
				if ($data) {
					$apikey = json_decode($data);
					
					if(isset($apikey->status)) {
						$verstatus = $apikey->status;
			?>
				var verstatus = <?php echo $verstatus; ?>;
				if(verstatus != 1) {
					<?php if(isBeta() || isAlpha()){ ?> $('.nav').append('<li class="rightnav statusbadge"><a href="health-check.php"><span class="info">i</span></a></li>');
					<?php } else { ?> $('a.support').parent('li').append('<li class="rightnav statusbadge"><a href="health-check.php"><span class="warning">!</span></a></li>'); <?php } ?>
					// $('a.support').attr('href', 'health-check.php');
				}
			<?php  }} ?>
		});
	</script>
	<?php } 

		$jsi18nkeys = array(
			'PLUGIN_UPDATED',
			'ERROR',
			'EXPAND_TOP',
			'COLLAPSE_TOP',
			'FILE_EXISTS_PROMPT',
			'CANCELLED'
		);
		
		$jsi18n = array_combine($jsi18nkeys,array_map('i18n_r',$jsi18nkeys));

	?>
	
	<script type="text/javascript">		
		// init gs namespace and i18n
		var GS = {};
		GS.i18n = <?php echo json_encode($jsi18n); ?>;
		GS.debug = <?php echo isDebug() === true ? 'true' : 'false'; ?> ;
	</script>

<noscript>
	<style>
		.tab{ display:block; clear:both;}
		.tab fieldset legend{ display: block; }
		#cm_themeselect { display:none;}
	</style>
</noscript>
	
</head>

<body <?php filename_id(); echo ' '.$bodyclass; ?> >	
	<div class="header" id="header" >
		<div class="wrapper clearfix">
 <?php exec_action('header-body'); ?>

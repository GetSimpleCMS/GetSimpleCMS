<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Header Admin Template
 *
 * @package GetSimple
 */
 
global $SITENAME, $SITEURL, $GSADMIN, $themeselector;

$GSSTYLE = getDef('GSSTYLE') ? GSSTYLE : '';

$bodyclass="class=\"";
if( in_array('sbfixed',explode(',',$GSSTYLE)) ) $bodyclass .= " sbfixed";
if( in_array('wide',explode(',',$GSSTYLE)) ) $bodyclass .= " wide";
$bodyclass .="\"";

if(get_filename_id()!='index') exec_action('admin-pre-header');

header('content-type: text/html; charset=utf-8');

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
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="template/ie6.css?v=<?php echo GSVERSION; ?>" media="screen" /><![endif]-->
<?php	

// code editor inits
if (!defined('GSNOHIGHLIGHT') || GSNOHIGHLIGHT!=true){
	debugLog("loading codemiror");
	register_script('codemirror', $SITEURL.$GSADMIN.'/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
	register_style('codemirror-css',$SITEURL.$GSADMIN.'/template/js/codemirror/lib/codemirror.css','screen',FALSE);
	
	queue_script('codemirror', GSBACK);
	queue_style('codemirror-css', GSBACK);
	queue_style('codemirror-theme', GSBACK);

}

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
	'cobalt',
	'eclipse',
	'eclipse',
	'elegant',
	'erlang-dark',
	'lesser-dark',
	'midnight',
	'monokai',
	'neat',
	'night',
	'rubyblue',
	'solarized dark',
	'solarized light',
	'twilight',
	'tomorrow-night-eighties',
	'vibrant-ink',
	'xq-dark',
	'xq-light'
);

$themeselector = '
		<select id="cm_themeselect">
			<option>default</option>
			<option>ambiance</option>
			<option>blackboard</option>
			<option>cobalt</option>
			<option>eclipse</option>
			<option>elegant</option>
			<option>erlang-dark</option>
			<option>lesser-dark</option>
			<option>monokai</option>
			<option>neat</option>
			<option>night</option>
			<option>rubyblue</option>
			<option>solarized dark</option>
			<option>solarized light</option>
			<option>twilight</option>
			<option>vibrant-ink</option>
			<option>xq-dark</option>
		</select>		
';

	get_scripts_backend(); ?>
		
	<script type="text/javascript" src="template/js/jquery.getsimple.js?v=<?php echo GSVERSION; ?>"></script>		
	<script type="text/javascript" src="template/js/lazyload.js?v=<?php echo GSVERSION; ?>"></script>		
	<script type="text/javascript" src="template/js/codemirror.getsimple.js?v=<?php echo GSVERSION; ?>"></script>		

	<!--[if lt IE 9]><script type="text/javascript" src="//html5shiv.googlecode.com/svn/trunk/html5.js" ></script><![endif]-->
	<?php if( ((get_filename_id()=='upload') || (get_filename_id()=='image')) && (!defined('GSNOUPLOADIFY')) ) { ?>
	<script type="text/javascript" src="template/js/uploadify/jquery.uploadify.js?v=3.0"></script>
	<?php } ?>
	<?php if(get_filename_id()=='image') { ?>
	<script type="text/javascript" src="template/js/jcrop/jquery.Jcrop.min.js"></script>
	<link rel="stylesheet" type="text/css" href="template/js/jcrop/jquery.Jcrop.min.css" media="screen" />
	<?php } ?>

	<?php 
	# Plugin hook to allow insertion of stuff into the header
	if(!isAuthPage()) exec_action('header'); 
	
	function doVerCheck(){
		return !isAuthPage() && !getDef('GSNOVERCHECK');
	}
	
	if( doVerCheck() ) { ?>
	<script>
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
					<?php if(isBeta()){ ?> $('a.support').parent('li').append('<span class="info">i</span>');
					<?php } else { ?> $('a.support').parent('li').append('<span class="warning">!</span>'); <?php } ?>
					$('a.support').attr('href', 'health-check.php');
				}
			<?php  }} ?>
		});
	</script>
	<?php } ?>
	
<noscript>
	<style>
		.tab{ display:block; clear:both;}
		.tab fieldset legend{ display: block; }
		#cm_themeselect { display:none;}
	</style>
</noscript>
	
</head>

<body <?php filename_id(); echo $bodyclass; ?> >	
	<div class="header" id="header" >
		<div class="wrapper clearfix">
 <?php exec_action('header-body'); ?>

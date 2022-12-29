<?php

/**
* GSMarkdown.php
* @name GS Editor plugin
*
* removes ckeditor and inserts lepture markdown editor in its place
* https://github.com/lepture
* 
* @version 0.1
* @author Shawn Alverson
* @link http://get-simple.info
* @file GSMarkdown.php
*/

$GSMarkdown = "GSMarkdown";

function init_GSMarkdown($GSMarkdown){
	$thisfile = basename(__FILE__, ".php");	// Plugin File
	$name     = $GSMarkdown;
	$version  = "0.2";
	$author   = "getsimple";
	$url      = "http://get-simple.info";
	$desc     = "Overrides ckeditor 3.x with custom editor";
	$type     = "";
	$func     = "";

	register_plugin($thisfile,$name,$version,$author,$url,$desc,$type,$func);
}

init_GSMarkdown($GSMarkdown);

if($HTMLEDITOR && get_filename_id() == 'edit'){
	add_action('common',$GSMarkdown.'_register_assets');
	add_action("header",$GSMarkdown.'_header',$GSMarkdown);
	add_action("load-edit",$GSMarkdown.'_edit_content');
}

function GSMarkdown_edit_content(){
	GLOBAL $HTMLEDITOR;
	$HTMLEDITOR = false;
	ob_start('GSMarkdown_obfilter');
}

function GSMarkdown_obfilter($buffer){
	return str_replace('<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>','',$buffer);
}

function GSMarkdown_header($GSMarkdown){
	?>
	
	<script type="text/javascript">
	
	jQuery(document).ready(function () {
		$('#post-content').meltdown();
	});

	jQuery(document).ready(function () {
		$('#post-content').data('mode','markdown');
		$('#post-content').editorFromTextarea();
	});

  	</script>
  	
  	<?php
}

function GSMarkdown_register_assets(){
	GLOBAL $GSMarkdown;

	// meltdown
	$ver = '1.0';
	$GS_script_assets['meltdown']['local']['url']       = getSiteURL() . getRelPath(GSPLUGINPATH) . $GSMarkdown.'/meltdown/js/jquery.meltdown.js';
	$GS_script_assets['meltdown']['local']['ver']       = $ver;
	$GS_style_assets['meltdown']['local']['url']        = getSiteURL() . getRelPath(GSPLUGINPATH) . $GSMarkdown.'/meltdown/css/meltdown.css';
	$GS_style_assets['meltdown']['local']['ver']        = $ver;
	
	$GS_script_assets['js-markdown-extra']['local']['url'] = getSiteURL() . getRelPath(GSPLUGINPATH) . $GSMarkdown.'/meltdown/js/lib/js-markdown-extra.js';
	$GS_script_assets['js-markdown-extra']['local']['ver'] = $ver;

	$GS_script_assets['rangyinputs']['local']['url'] = getSiteURL() . getRelPath(GSPLUGINPATH) . $GSMarkdown.'/meltdown/js/lib/rangyinputs-jquery.min.js';
	$GS_script_assets['rangyinputs']['local']['ver'] = $ver;

	$GS_script_assets['element_resize_detection']['local']['url'] = getSiteURL() . getRelPath(GSPLUGINPATH) . $GSMarkdown.'/meltdown/js/lib/element_resize_detection.js';
	$GS_script_assets['element_resize_detection']['local']['ver'] = $ver;
 	
 	// queue
	$GS_script_assets['meltdown']['queue']['script'] = 'js-markdown-extra,rangyinputs,element_resize_detection';
	$GS_script_assets['meltdown']['queue']['style']  = 'meltdown';

	preRegisterScript('js-markdown-extra', $GS_script_assets['js-markdown-extra']);
	preRegisterScript('rangyinputs', $GS_script_assets['rangyinputs']);
	preRegisterScript('element_resize_detection', $GS_script_assets['element_resize_detection']);
	preRegisterScript('meltdown', $GS_script_assets['meltdown']);
	
	preRegisterStyle('meltdown',  $GS_style_assets['meltdown']);

	// queue_script('js-markdown-extra',GSBACK);
	queue_script('meltdown',GSBACK);
}

?>
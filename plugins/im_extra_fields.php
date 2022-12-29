<?php

register_plugin(
	basename(__FILE__, '.php'),
	'IM Extra Page Fields',
	'1.0',
	'Juri Ehret',
	'http://ehret-studio.com',
	'Extends native GS page editor with ItemManager fields',
	'',
	''
);

/**
 * Hooked stuff designated by '__' (sample: __function_name())
 */
add_action('admin-pre-header', '__ajax_get_fields');
add_action('edit-extras', '__edit_page_extras');
add_action('edit-content', '__edit_page');
add_action('changedata-aftersave', '__save_item');
add_action('page-delete', '__delete_item');

if(preg_match('/\/'.(defined('GSADMIN') ? GSADMIN : 'admin').'\/edit.php/i', $_SERVER['REQUEST_URI'])){
	register_style('imextrastyle',$SITEURL.'plugins/im_extra_fields/inc/css/styles.css',  GSVERSION, 'screen');
	queue_style('imextrastyle', GSBACK);
}

function __edit_page_extras()
{
	include(__DIR__.'/im_extra_fields/inc/_inc.php');
	echo $parser->renderHeaderSelector();
}

function __edit_page()
{
	include(__DIR__.'/im_extra_fields/inc/_inc.php');
	echo $parser->renderBody();
}

function __save_item()
{
	include(__DIR__.'/im_extra_fields/inc/_inc.php');
	$processor->saveItem();
}

function __delete_item()
{
	include(__DIR__.'/im_extra_fields/inc/_inc.php');
	$processor->deleteItem();
}

function __ajax_get_fields()
{
	if(!isset($_POST['epcatid'])) return;
	login_cookie_check();
	include(__DIR__.'/im_extra_fields/inc/_inc.php');
	echo $parser->renderItemFields();
	exit();
}
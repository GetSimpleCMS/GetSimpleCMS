<?php

/*
 * TheMatrix, a plugin for GetSimple CMS 3.1
 * 
 * version 0.1
 *  
 * Copyright (c) 2012 Mike Swan mike@digimute.com
 *
 * Contributions have been made by:
 * Shawn A (github.com/tablatronix)
 *
 */


// Turn dubgging on 
$DM_Matrix_debug=true; 

require "DM_Matrix/include/sql4array.php";
require "DM_Matrix/include/DM_matrix_functions.php";

# get correct id for plugin
$thisfile_DM_Matrix = basename(__FILE__, '.php');

# add in this plugin's language file
i18n_merge($thisfile_DM_Matrix) || i18n_merge($thisfile_DM_Matrix, 'en_US');

# register plugin
register_plugin(
  $thisfile_DM_Matrix,
  'The Matrix',
  '0.1',
  'Mike Swan',
  'http://digimute.com/',
  'The Matrix',
  'DM_Matrix',
  'matrix_manager'
);

debugLog(''.$TIMEZONE);   

define('GSSCHEMAPATH',GSDATAOTHERPATH.'matrix');

// check and make sure the base folders are there. 
if (!is_dir(GSSCHEMAPATH)){
	mkdir(GSSCHEMAPATH);
	DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATEBASEFOLDER'));

} else {
	DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATEBASEFOLDERFAIL'));
}


$defaultDebug = true;
$schemaArray = array();
$item_title='Matrix';
$editing=false; 
$uri='';

$sql = new sql4array();
$mytable=array();

$DM_tables_cache = array(); // hold cached schema loads

// only load all our scripts and style if were on the MAtrix Plugin page
if (isset($_GET['id']) && $_GET['id']=="DM_Matrix"){
	register_script('DM_Matrix',$SITEURL.'plugins/DM_Matrix/js/DM_Matrix.js', '0.1',FALSE);
	queue_script('DM_Matrix', GSBACK);

	
	register_script('codemirror', $SITEURL.'admin/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
	queue_script('codemirror', GSBACK);
	if (file_exists(GSADMINPATH.'/template/js/codemirror/lib/searchcursor.js')){
		register_script('codemirror-search', $SITEURL.'admin/template/js/codemirror/lib/searchcursor.js', '0.2.0', FALSE);
		register_script('codemirror-search-cursor', $SITEURL.'admin/template/js/codemirror/lib/search.js', '0.2.0', FALSE);
		register_script('codemirror-dialog', $SITEURL.'admin/template/js/codemirror/lib/dialog.js', '0.2.0', FALSE);
		register_script('codemirror-folding', $SITEURL.'admin/template/js/codemirror/lib/foldcode.js', '0.2.0', FALSE);

		queue_script('codemirror-dialog', GSBACK);
		queue_script('codemirror-search', GSBACK);
		queue_script('codemirror-search-cursor', GSBACK);
		queue_script('codemirror-folding', GSBACK);
	} 
	
	register_style('codemirror-css',$SITEURL.'admin/template/js/codemirror/lib/codemirror.css','screen',FALSE);
	register_style('codemirror-theme',$SITEURL.'admin/template/js/codemirror/theme/default.css','screen',FALSE);		
	
	queue_style('codemirror-css', GSBACK);
	queue_style('codemirror-theme', GSBACK);
	
	register_script('DM_tablesorter',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.js', '0.1',FALSE);
	queue_script('DM_tablesorter', GSBACK);
	register_script('DM_tablepager',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.pager.js', '0.1',FALSE);
	queue_script('DM_tablepager', GSBACK);
	register_style('DM_tablesorter',$SITEURL.'plugins/DM_Matrix/css/blue/style.css','screen',FALSE);
	queue_style('DM_tablesorter', GSBACK);
	register_style('DM_tablepager',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.pager.css','screen',FALSE);
	queue_style('DM_tablepager', GSBACK);
	
	register_script('DM_Matrix_timepicker',$SITEURL.'plugins/DM_Matrix/js/timepicker.js', '0.1',FALSE);
	queue_script('DM_Matrix_timepicker', GSBACK);
	
	register_style('jquery-ui-css',$SITEURL.'plugins/DM_Matrix/css/redmond/jquery-ui-1.8.16.custom.css','screen',FALSE);
	queue_style('jquery-ui-css', GSBACK);
	queue_script('jquery-ui', GSBACK);	
	register_style('DM_Matrix_css',$SITEURL.'plugins/DM_Matrix/css/style.css', '0.1',FALSE);
	queue_style('DM_Matrix_css', GSBACK);
	
	register_script('ckeditor', $SITEURL.'admin/template/js/ckeditor/ckeditor.js', '0.2.0', FALSE);
	queue_script('ckeditor', GSBACK);
	
	register_script('askconfirm', $SITEURL.'plugins/DM_Matrix/js/jconfirm.jquery.js', '0.2.0', FALSE);
	queue_script('askconfirm', GSBACK);
	
}

add_action('nav-tab','createNavTab',array('DM_Matrix','DM_Matrix','The Matrix','action=matrix_manager&schema'));

add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Tables",'schema')); 
if (isset($_GET['edit'])){
	add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Edit Table",'edit')); 
}
if (isset($_GET['add'])){
	add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Add Record",'add')); 
}
if (isset($_GET['view'])){
  add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Records",'view')); 
}
add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "About",'about')); 

add_action('error-404','doRoute',array());


DM_getSchema();

if (isset($_GET['add']) && isset($_POST['post-addtable'])){
	DMdebuglog('Trying to add a new table: '.$_POST['post-addtable']);
	$ret=createSchemaTable($_POST['post-addtable'],$_POST['post-maxrecords'],array());
	if ($ret){
		$success="Table ".$_POST['post-addtable'].' created successfully';
	}
}

if (isset($_GET['add']) && isset($_GET['addrecord'])){
	$table=$_GET['add'];
	addRecordFromForm($table);
	}
	
if (isset($_GET['add']) && isset($_GET['updaterecord'])){
	$table=$_GET['add'];
	updateRecordFromForm($table);
	header('Location: load.php?id=DM_Matrix&action=matrix_manager&view='.$table);
	}

if (isset($_GET['view']) && isset($_GET['delete'])){
	$table=$_GET['view'];
	$id=$_GET['delete'];
	$ret=DM_deleteRecord($table,$id);
	if ($ret){
		$success="Record ".$table.' / '.$id.' Deleted';
	}
}

if (isset($_GET['schema']) && isset($_GET['drop'])){
	DM_getSchema();
	$table=$_GET['drop'];
	$ret=DM_deleteTable($table);
	if ($ret){
		$success="Dropped ".$table.' successfully';
	} else {
		$success="Unable to drop  ".$table.' successfully';
	}
}




if (!tableExists('_routes')){
	DMdebuglog('Creating table _routes ');
	$ret = createSchemaTable('_routes','0',array('route'=>'text','rewrite'=>'text'));
} 


if (isset($_GET['edit']) && isset($_GET['addfield'])){
	if (isset($_POST['post-cacheindex'])){
		$cacheindex=1;
	} else {
		$cacheindex=0;
	}
	if (isset($_POST['post-tableview'])){
		$tableview=1;
	} else {
		$tableview=0;
	}
	
	$field=array(
		'name'=>$_POST['post-name'],
		'type'=>$_POST['post-type'],
		'label'=>$_POST['post-label'],
		'description'=>$_POST['post-desc'],
		'cacheindex'=>$cacheindex,
		'tableview'=>$tableview
	);
	if ($_POST['post-type']=='dropdown'){
		$field['table']=$_POST['post-table'];
		$field['row']=$_POST['post-row'];
	}
	addSchemaField($_GET['edit'],$field,true);
	  //DM_saveSchema();
}

//Admin Content
function matrix_manager() {
global $item_title,$thisfile_DM_Matrix, $fieldtypes,$schemaArray, $sql, $mytable;
//Main Navigation For Admin Panel
?>

<div style="margin:0 -15px -15px -10px;padding:0px;">
	<h3 ><?php echo i18n_r($thisfile_DM_Matrix.'/DM_PLUGINTITLE') ?></h3>  
</div>
</div>

<div class="main" style="margin-top:-10px;">
<?php
//Alert Admin If Items Manager Settings XML File Is Directory Does Not Exist
if (isset($_GET['schema'])) {
		include "DM_Matrix/include/schema.php";
	} 
	elseif (isset($_GET['add']))	
	{
		include "DM_Matrix/include/add.php";
	}
	elseif (isset($_GET['edit']))
	{
		include "DM_Matrix/include/edit.php";
	} 
	elseif (isset($_GET['about']))
	{
		include "DM_Matrix/include/about.php";
	} 
	elseif (isset($_GET['view']))
	{
		include "DM_Matrix/include/view.php";
	} 		
}


//echo "<pre>";
//print_r($schemaArray);
//echo "</pre>";

<?php 
/****************************************************
*
* @File: 	customfields.php
* @Package:	GetSimple
* @Action:	Plugin to allow additional options on pages. 	
*
*****************************************************/

/*****************************************************
 * Changelog: 
 * 
 * 1.3 Added "attach" attribute to "ITEM"  
 */

# register plugin
register_plugin(
	'customfields',
	'Custom Fields',
	'1.4beta',
  	'Mike Swan',
  	'http://www.digimute.com/',
  	'Custom fields for pages',
	'plugins',
	'customfields_showform'
  );


add_action('index-pretemplate', 'getTagValues',array());    // add hook to make $tags values available to theme
add_action('edit-extras', 'createTagInputs',array());    // add hook to create new tag inputs on the edit screen.
add_action('changedata-save', 'saveTagValues',array());    // add hook to save  $tags values 
add_action('plugins-sidebar','createSideMenu',array('customfields','CustomFields Info')); // add a menu item to the sidebar
add_action('pages-sidebar','getPageTypes',array('true')); // add a menu item to the pages sidebar

$tags=array();


/*******************************************************
 * @function get_theme_tags
 * 
 */

function getTagValues(){
	global $data_index;
	global $tags;
	while (list($key, $val) = each($tags)) {
		$tags[$key]['value']=(string)$data_index->{$key};
	}
}


/*******************************************************
 * @function getTagsFromXML
 * 
 */
function getTagsFromXML(){
	global $data_index;
	global $TEMPLATE;
	global $SITEURL;
	global $tags;
	$file="";
	
	if (file_exists(GSDATAOTHERPATH.'customfields.xml')){
		$file=GSDATAOTHERPATH."customfields.xml";
	}
	if (file_exists(GSTHEMESPATH.$TEMPLATE.'/customfields.xml')){
		$file=GSTHEMESPATH . $TEMPLATE.'/customfields.xml';
	}
	
	
	if ($file!=""){
		//$file=GSDATAOTHERPATH."customfields.xml";
		$i=0;	
		$thisfile = file_get_contents($file);
		$data = simplexml_load_string($thisfile);
		$components = $data->item;
		if (count($components) != 0) {
			foreach ($components as $component) {
				$att = $component->attributes();
				$key=$component->desc;
				//$tags['$key']['test']=$component->label;
				$tags[(string)$key] =$key;
				$tags[(string)$key]=array();
				$tags[(string)$key]['attach'] =(string)$att['attach'];
				$tags[(string)$key]['label']=(string)$component->label;
				$tags[(string)$key]['type']=(string)$component->type;
				// for furture use
				if ($component->type=="dropdown"){
					// do dropdown 	
						//$att = $option->attributes();
						//$value =(string)$att['value'];
						//if ($value!=""){
						//	$value=$option;
						//}
					$tags[(string)$key]['options']=array();		
					$options=$component->option;	
					foreach ($options as $option) {
						$att = $option->attributes();
						$value =(string)$att['value'];
						if ($value==""){
							$value=$option;
						}
						$tags[(string)$key]['options']['option'][(string)$option]=array();
						$tags[(string)$key]['options']['option'][(string)$option]['value']=(string)$value;
						
					}
				}
				$tags[(string)$key]['value']="";
				$i++;
			}
		} 
	}
}


/*
 * Create new sidebar for page creation.
 * Relies on an Customfield called "pagetype"  
 * 
 */

function getPageTypes($flag){
	global $tags;	
	foreach ($tags['pagetype']['options']['option'] as $key=>$option) {
			$class='';
			if( isset($_GET['type']) && $_GET['type'] == $key)  { 
				$class='class="current"'; 
			}
			echo "<li><a style='background-color:#555' href=\"edit.php?type=".$key."\" ".$class.">Create New ".ucwords($key)." Page</a></li>";	
	}
	// quick hack to remove original 'Create New Page'  menu item. 
	if ($flag){
		echo "<script type='text/javascript'>$('.snav li:nth-child(2)').remove();</script>";
	}
}

/*******************************************************
 * @function saveTagValues
 * 
 */

function saveTagValues(){
	global $tags;
	global $note;
	global $xml;
	
	while (list($key, $val) = each($tags)) {
		if(isset($_POST['post-'.strtolower($key)])) { 
			$note = $xml->addChild(strtolower($key));
			$note->addCData($_POST['post-'.strtolower($key)]);	
		}	
	}	
}

/*******************************************************
 * @function create_inputs
 * 
 */
function createTagInputs(){
	global $tags;
	global $data_edit;
	$uri 		= @$_GET['id'];
	$path = GSDATAPAGESPATH;
	// get saved page data
	$file = $uri .'.xml';
	$data_edit = getXML($path . $file);
	$class="";
	echo "<tr><td><h2>Custom Fields</h2></td></tr>";
	$pagetype=$data_edit->pagetype;
	$pagetype=(isset($_GET['type']) && $_GET['type']!= '') ? $_GET['type'] : $data_edit->pagetype;
	global $tags;
	while (list($key, $val) = each($tags)) {	
		$attach=explode(" ", $tags[$key]['attach']);
		$to = $key;
		//echo $pagetype.":".$attach.":to:".$to."<br/>";
		foreach ($attach as $att){
		if ($att==$pagetype or $att=="all"){
			echo "<tr>";
			$typ=$tags[$key]['type'];
			switch($typ){
				// draw a full width TextBox
				case "textfull":
					echo "<td colspan='2'>";
					echo "<b>".$tags[$key]['label'].":</b><br />";
					echo "<input class=\"text fullwidth\" type=\"text\" id=\"post-".strtolower($key)."\" name=\"post-".strtolower($key)."\" value=\"".$data_edit->$key."\" /></td>"; 
				break; 
				case "text":
					echo "<td>";
					echo "<b>".$tags[$key]['label'].":</b><br />";
					echo "<input class=\"text short\" type=\"text\" id=\"post-".strtolower($key)."\" name=\"post-".strtolower($key)."\" value=\"".$data_edit->$key."\" /></td><td></td>"; 
				break;
				case "checkbox":
					echo "<td>";
					echo '<label class="clean" for="post-'.strtolower($key).'"><b>'.$tags[$key]['label'].'</b>&nbsp;&nbsp;&nbsp;</label>';
					if ($data_edit->$key=="Y"){
						echo "<input type=\"checkbox\" id=\"post-".strtolower($key)."\" name=\"post-".strtolower($key)."\" value=\"".$data_edit->$key."\" /></td><td></td>";
					} else {
						echo "<input type=\"checkbox\" id=\"post-".strtolower($key)."\" name=\"post-".strtolower($key)."\" value=\"".$data_edit->$key."\" /></td><td></td>";
					}
					
				break;
				case "pagetype":
					echo "<td>";
					echo "<b>".$tags[$key]['label'].":</b><br />";
					echo "<select id=\"post-".strtolower($key)."\" name=\"post-".strtolower($key)."\" class='".$class."'>";
					echo "<option value='".$data_edit->$key."'>".$data_edit->$key."</option>";
					foreach ($tags[$key]['options'] as $option) {
						echo "<option value='".$option."'>".$option."</option>";
					}
					echo "</select>";
					echo "</td>";
					break; 
				case "dropdown":
					echo "<td>";
					echo "<b>".$tags[$key]['label'].":</b><br />";
					echo "<select id=\"post-".strtolower($key)."\" name=\"post-".strtolower($key)."\" class='".$class."'>";
					echo "<option value='".$data_edit->$key."'>".$data_edit->$key."</option>";
					foreach ($tags[$key]['options']['option'] as $key=>$option) {
						echo "<option value='".(string)$option['value']."'>".$key."</option>";
					}
					echo "</select>";
					echo "</td>";
					break;				}
			echo "</tr>";
		}
		}
	}		
}


/*******************************************************
 * @function get_theme_tags
 * 
 */
function getCustomField($tag){
	global $tags;
	echo $tags[$tag]['value'];
}

/*******************************************************
 * @function get_theme_tags
 * 
 */
function returnCustomField($tag){
	global $tags;
	return $tags[$tag]['value'];
}


function customfields_showform(){
	global $tags;
	global $TEMPLATE;
	global $SITEURL;
	
	getTagsFromXML();
	$file="";
	if (file_exists(GSDATAOTHERPATH.'customfields.xml')){
		$file="Reading GLOBAL Customfields";
	}
	if (file_exists(GSTHEMESPATH.$TEMPLATE.'/customfields.xml')){
		$file="Reading Template Customfields";
	}

	
	if (@$_GET['action']=='edit'){
		require_once "customfields/edit.php";
	} else {		
	$table = '<thead><tr><th>Name</th><th>Label</th><th>Attach</th><th style="width:100px;">Type</th></tr></thead><tbody>';
	$counter=0;
	while (list($key, $val) = each($tags)) {
		$table .= '<tr id="tr-'.$counter.'" >';
		$table .= '<td>'.$key.'</td>';
		$table .= '<td>'.$tags[$key]['label'].'</td>';
		$table .= '<td>'.$tags[$key]['attach'].'</td>';
		$table .= '<td>'.$tags[$key]['type'].'</td>';
		$table .= '<input type="hidden" name="key[]" value="'. $key.'" />';
		$table .= '<input type="hidden" name="label[]" value="'.$tags[$key]['label'].'" />';
		$table .= '<input type="hidden" name="type[]" value="'. $tags[$key]['type'] .'" />';
		$table .= '</tr>';
		$counter++;
	}
	$table.="</tbody>";
	echo "<pre>";
print_r($tags);
echo "</pre>";
echo <<<HED
<label>Custom Fields - $file</label>

<p><br/><br/>This plugin allows Custom Fields on each page. <br/>
New fields can be accessed in your themes by using:  getCustomField(\tagname);  or   returnCustomField(\tagname);<br/></p>
<table  id="pluginTable">
$table
</table>
HED;

	}
}

// this is required to initialize the $tags array for the admin backend. 
getTagsFromXML();


?>
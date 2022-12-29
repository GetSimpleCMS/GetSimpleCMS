<?php

/**
 * Create a Schema folder
 * 
 * Creates a fodler for each of the Tables in the Schema. 
 *
 * @param string $name , Name of the table to create
 * @return boolean , whether table was created or not. 
 */
function createSchemaFolder($name){
	$ret = mkdir(GSSCHEMAPATH.'/'.$name);
	return $ret;
}


function addRoute($url,$route){
	createRecord('_routes',array('route'=>$url,'rewrite'=>$route));	
}


/**
 * Check if Table exists
 * 
 * Check if a table exists and return true or false
 *
 * @param string $table , Name of the table to test
 * @return boolean , whether table exists or not.
 */
function tableExists($table){
	global $schemaArray;
	if (array_key_exists($table, $schemaArray)) {
		return true;
	} else {
		return false;
	}
}


/** Load The main schema XML file and fill the array $schema
  */
function DM_getSchema($flag=false){
  global $schemaArray;	
  
  $file=GSSCHEMAPATH."/schema.xml";
  if (file_exists($file)){
  DMdebuglog('Schema file loaded...');
  // load the xml file and setup the array. 
	$thisfile_DM_Matrix = file_get_contents($file);
		$data = simplexml_load_string($thisfile_DM_Matrix);
		$components = @$data->item;
		if (count($components) != 0) {
			foreach ($components as $component) {
				$att = $component->attributes();
				$key=$component->name;
				//$schemaArray[(string)$key] =$key;
				$schemaArray[(string)$key]=array();				
				$schemaArray[(string)$key]['id']=(int)$component->id;
				$schemaArray[(string)$key]['maxrecords']=(int)$component->maxrecords;
				$fields=$component->field;	
				foreach ($fields as $field) {
					$att = $field->attributes();
					$type =(string)$att['type'];
					$desc=(string)$att['description'];
					$label=(string)$att['label'];
					$cacheindex=(string)$att['cacheindex'];
					$tableview=(string)$att['tableview'];
					
					$schemaArray[(string)$key]['fields'][(string)$field]=(string)$type;
					$schemaArray[(string)$key]['desc'][(string)$field]=(string)$desc;
					$schemaArray[(string)$key]['label'][(string)$field]=(string)$label;
					$schemaArray[(string)$key]['cacheindex'][(string)$field]=(string)$cacheindex;
					$schemaArray[(string)$key]['tableview'][(string)$field]=(string)$tableview;
					
					if ((string)$type=="dropdown"){
						$schemaArray[(string)$key]['table'][(string)$field]=(string)$att['table'];;
						$schemaArray[(string)$key]['row'][(string)$field]=(string)$att['row'];;
					}
					if ((string)$type=="checkbox"){
						$schemaArray[(string)$key]['label'][(string)$field]=(string)$att['label'];;
					}
					
					}
				
			}
		}
	}
}

function DM_saveSchema(){
	global $schemaArray;	
	$file=GSSCHEMAPATH."/schema.xml";
	$xml = @new SimpleXMLExtended('<channel></channel>');
	foreach ($schemaArray as $table=>$key){
		$pages = $xml->addChild('item');
		$pages->addChild('name',$table);
		$pages->addChild('id',$key['id']);
		$pages->addChild('maxrecords',$key['maxrecords']);
		
		if (isset($key['fields'])){		
			foreach($key['fields'] as $field=>$type){
				//$options=$schemaArray[$table]['options'];

				$field=$pages->addChild('field',$field);
				$field->addAttribute('type',$type);
				$field->addAttribute('tableview',@$schemaArray[$table]['tableview'][(string)$field]);
				$field->addAttribute('cacheindex',@$schemaArray[$table]['cacheindex'][(string)$field]);
				$field->addAttribute('description',@$schemaArray[$table]['desc'][(string)$field]);
				$field->addAttribute('label',@$schemaArray[$table]['label'][(string)$field]);
				
				if ($type=='dropdown'){
					$field->addAttribute('table',@$schemaArray[$table]['table'][(string)$field]);
					$field->addAttribute('row',@$schemaArray[$table]['row'][(string)$field]);
				}
				
			}
		}
	}
	$ret = $xml->asXML($file);
	DM_getSchema(true);
	return $ret;
}

function createRecord($name,$data=array()){
	global $schemaArray;
	$id=getNextRecord($name);
	DMdebuglog('record:'.$id);
	$file=GSSCHEMAPATH.'/'.$name."/".$id.".xml";
	$xml = @new SimpleXMLExtended('<channel></channel>');
	$pages = $xml->addChild('item');
	$pages->addChild('id',$id);
	foreach ($data as $field=>$txt){
		$pages->addChild($field,$txt);	
	}
	$xml->asXML($file);
	DMdebuglog('file:'.$file);
	$schemaArray[$name]['id']=$id+1;
	$ret=DM_saveSchema();
	return $ret;
}


function DM_deleteRecord($table,$id){
	$file=GSSCHEMAPATH.'/'.$table."/".$id.".xml";
	if (file_exists($file)){
		unlink($file);
		return true;
	} else {
		return false;
	}
}

function DM_deleteTable($table){
	$numRecords=DM_getNumRecords($table);
	if ($numRecords==0){
		if (is_dir(GSSCHEMAPATH.'/'.$table)) {
			$ret=rmdir(GSSCHEMAPATH.'/'.$table);
			dropSchemaTable($table);
		} else {
			$ret=false;
		}
	} else {
		$ret=false;
	}
	return $ret;
}

function addRecordFromForm($tbl){
		debugLog("adding form");
		global $fieldtypes,$schemaArray;
		$tempArray=array();	
		foreach ($schemaArray[$tbl]['fields'] as $field=>$type)
		{
			if (isset($_POST["post-".$field]))
			{
				$data=DM_manipulate($_POST["post-".$field], $type); 
				$tempArray[(string)$field]=$data;
			}
		}

		createRecord($tbl, $tempArray);		
}

function updateRecordFromForm($tbl){
		debugLog("updating record from form...");
		global $fieldtypes,$schemaArray;
		$tempArray=array();	
		foreach ($schemaArray[$tbl]['fields'] as $field=>$type)
		{
			if (isset($_POST["post-".$field]))
			{
				$data=DM_manipulate($_POST["post-".$field], $type); 
				$tempArray[(string)$field]=$data;
			}
		}

		updateRecord($tbl,$_POST['post-id'], $tempArray);		
}


function DM_manipulate($field, $type){
	return match ($type) {
     "datetimepicker" => (int)strtotime($field),
     "datepicker" => (int)strtotime($field),
     "texteditor" => safe_slash_html($field),
     "textarea" => safe_slash_html($field),
     "codeeditor" => safe_slash_html($field),
     default => $field,
 };
		
}

function doRoute(){
	global $file,$id,$uri;
	$myquery = "select route,rewrite from _routes";  
	$uriRoutes=DM_query($myquery);
	$uri = trim(str_replace('index.php', '', $_SERVER['REQUEST_URI']), '/#');
	$parts=explode('/',$uri);
	foreach ($uriRoutes as $routes){
		if ($parts[1]==$routes['route']){
			$file=GSDATAPAGESPATH . str_replace('.php','.xml',$routes['rewrite']);
			$id=pathinfo($routes['rewrite'],PATHINFO_FILENAME);
		}
	}
}


function updateRecord($name,$record,$data=array()){
	global $schemaArray;
	DMdebuglog('updating record:'.$name.'/'.$record);
	$file=GSSCHEMAPATH.'/'.$name."/".$record.".xml";
	$xml = @new SimpleXMLExtended('<channel></channel>');
	$pages = $xml->addChild('item');
	$pages->addChild('id',$record);
	foreach ($data as $field=>$txt){
		$pages->addChild($field,$txt);	
	}
	$xml->asXML($file);
	DMdebuglog('file:'.$file);
	$ret=DM_saveSchema();
	return $ret;
}

/**
 * Get the next record ID
 *
 * returns the next record ID in the sequence
 *
 * @param string $name , Name of the table to create
 * @return string , Record ID 
 */
function getNextRecord($name){
	global $schemaArray;
	DMdebuglog($name.":returned:".$schemaArray[$name]['id']);
	return $schemaArray[$name]['id'];
}


/**
 * Create a new Table
 *
 * Creates a new table in the Schema, by creating a folder for the files and adding data to the schema
 *
 * @param string $name , Name of the table to create
 * @param array $fields , array of fields and types to create, default is to create an id (int) fields
 * @return boolean , whether table was created or not. 
 */
function createSchemaTable($name, $maxrecords=0, $fields=array()){
	global $schemaArray, $thisfile_DM_Matrix;
	if (array_key_exists($name , $schemaArray)){
		DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATETABLEFAIL'));
		return false;
	}
	$schemaArray[(string)$name] =array();
	$schemaArray[(string)$name]['id']=0;
	$schemaArray[(string)$name]['maxrecords']=$maxrecords;
	if (!in_array('id', $schemaArray)){
		$schemaArray[(string)$name]['fields']['id']='int';
		$schemaArray[(string)$name]['desc']['id']='id Field';
		$schemaArray[(string)$name]['cacheindex']['id']='1';
		$schemaArray[(string)$name]['tableview']['id']='1';
	}
	foreach ($fields as $field=>$value) {
		$schemaArray[(string)$name]['fields'][(string)$field]=(string)$value;
	}
	createSchemaFolder($name);		
	$ret=DM_saveSchema();
	DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATETABLESUCCESS'));
	return $ret;
}

/**
 * Drop a Schema table
 *
 * Delete a Schema Table from the system 
 * 
 * Todo: Need to check the folder is empty before delting. 
 *
 * @param string $name , Name of the table to create
 */
function dropSchemaTable($name){
	global $schemaArray;
	unset($schemaArray[(string)$name]);
	$ret=DM_saveSchema();	
	return $ret;
}

/**
 * Add a field to a table
 *
 * Creates a new Field in the table. 
 *
 * @param string $name , Name of the table 
 * @param array $fields , array of fields and types to create
 * @param boolean, whether to save the Schema after adding the field, default to true
 * @return boolean , whether field was created or not. 
 */
function addSchemaField($name,$fields=array(),$save=true){
	global $schemaArray;
	
	// used for field param to schema table key translation
	$fieldsKeys = array(
	'type' => 'fields',
	'label' => 'label',
	'description' => 'desc',
	'desc' => 'desc',
	'cacheindex' => 'cacheindex',
	'tableview' => 'tableview',
	'table' => 'table',
	'row' => 'row'	
	);
	
	foreach($fields as $key=>$value){
			if( (($key == 'table' or $key=='row') and $fields['type'] != 'dropdown') or $key=='name') continue;
			$schemaArray[(string)$name][$fieldsKeys[$key]][(string)$fields['name']]=(string)$value;		
	}

	if ($save==true) {
		$ret=DM_saveSchema();
		$ret=true;
	} else {
		$ret=true;
	}

	return $ret;
}

/**
 * Delete a field from a table
 *
 * Delete a Field(s) from a table.  
 *
 * @param string $name , Name of the table 
 * @param array $fields , array of fields to delete from the table
 * @return boolean , whether table was created or not. 
 */
function deleteSchemaField($name,$fields=array(),$save=true){
	global $schemaArray;
	foreach ($fields as $field) {
		unset($schemaArray[(string)$name]['fields'][(string)$field]);
	}
	if ($save==true) {
		$ret=DM_saveSchema();
	} else {
		$ret=true;
	}
	return $ret;
}


function getSchemaTable($name,$query=''){
	global $returnArray;
	$table=array();
	$path = GSSCHEMAPATH.'/'.$name."/";
	  $dir_handle = @opendir($path) or die("Unable to open $path");
	  $filenames = array();
	  while ($filename = readdir($dir_handle)) {
		$ext = substr($filename, strrpos($filename, '.') + 1);
		$fname=substr($filename,0, strrpos($filename, '.'));
		if ($ext=="xml"){
			$thisfile_DM_Matrix = file_get_contents($path.$filename);
			$data = simplexml_load_string($thisfile_DM_Matrix);
			//$count++;   
			$id=$data->item;
			$idNum=$id->id;
			foreach ($id->children() as $opt=>$val) {
				//$pagesArray[(string)$key][(string)$opt]=(string)$val;
				$table[(int)$idNum][(string)$opt]=(string)$val;
			}		
		}
	  }
	if ($query!=''){
		$returnArray=$table;
		$sql=new sql4array();
		$table = $sql->query($query);
	}
	return $table;
}


function DM_getRecord($name, $record){
	$table=array();
	$path = GSSCHEMAPATH.'/'.$name."/";
	$filename=$record.".xml";
	$thisfile_DM_Matrix = file_get_contents($path.$filename);
	$data = simplexml_load_string($thisfile_DM_Matrix);
	 //$count++;   
	$id=$data->item;
	$idNum=$id->id;
	foreach ($id->children() as $opt=>$val) {
		   //$pagesArray[(string)$key][(string)$opt]=(string)$val;
		$table[(string)$opt]=(string)$val;
	}		
	return $table;
}


// get the number of records for a given table. 
function DM_getNumRecords($table){
	$numRecords=0;
	  $path = GSSCHEMAPATH.'/'.$table."/";
	  $dir_handle = @opendir($path) or die("Unable to open $path");
	  while ($filename = readdir($dir_handle)) {
		$ext = substr($filename, strrpos($filename, '.') + 1);
		if ($ext=="xml"){
			$numRecords++;	
		}
	  }		
	  return $numRecords;
}


function DM_editForm($table, $record){
	global $schemaArray;	
	global $returnArray;
	global $TEMPLATE;
	global $SITEURL;
	$formValues=DM_getRecord($table,$record);

	echo '<ul class="fields">';
	foreach ($schemaArray[$table]['fields'] as $field=>$value) {

	if ($field!="id"){
	?>
	
		<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo $field; ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo $schemaArray[$table]['desc'][$field]; ?></p>
				<?php displayFieldType($field, $value,$table,isset($formValues[$field]) ? $formValues[$field]:''); ?>
			</div>
		</li>
	
	<?php
	} else {
	?>
	<li class="InputfieldName Inputfield_id ui-widget" id="wrap_Inputfield_id">
		<label class="ui-widget-header fieldstateToggle" for="Inputfield_id">id</label>
		<div class="ui-widget-content">
			<input id="post-id" name="post-id" value="<?php echo $record; ?>" type="text" readonly="readonly">
		</div>
	</li>

		
	<?php	
		}
	}
?>

	<li class="fieldsubmit Inputfield_submit_save_field ui-widget" id="wrap_Inputfield_submit">
		<label class="ui-widget-header fieldstateToggle" for="Inputfield_submit">Save Record</label>
		<div class="ui-widget-content">
			<button id="Inputfield_submit" class="mtrx_but_add" name="submit_save_field" value="Submit" type="submit">Save This Record</button>
		</div>
	</li>

</ul>
<?php
DM_outputCKHeader();	
}


function DM_outputCKHeader(){
	global $TEMPLATE;
	global $SITEURL;
	$dateformat=i18n('DATE_FORMAT',false);
	$dateformat = str_replace('Y', 'yy', $dateformat);
	$dateformat = str_replace('j', 'd', $dateformat);
	
	if (defined('GSEDITORHEIGHT')) { $EDHEIGHT = GSEDITORHEIGHT .'px'; } else {	$EDHEIGHT = '500px'; }
		if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = i18n_r('CKEDITOR_LANG'); }
		if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
		if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
			
		if ($EDTOOL == 'advanced') {
			$toolbar = "
					['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
			 '/',
			 ['Styles','Format','Font','FontSize']
		 ";
			} elseif ($EDTOOL == 'basic') {
			$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
		} else {
			$toolbar = GSEDITORTOOL;
		}
		?>
		
			<script type="text/javascript">
			
			CKEDITOR.replaceAll(function(textarea,config){
				// converts all textareas with class of 'DMckeditor' to ckeditor instances.
				if (textarea.className!="DMckeditor") return false; //for only assign a class
				jQuery.extend(config,
				{
					forcePasteAsPlainText : true,
					language : '<?php echo $EDLANG; ?>',
					defaultLanguage : 'en',
					<?php if (file_exists(GSTHEMESPATH .$TEMPLATE."/editor.css")) { 
						$fullpath = suggest_site_path();
					?>
					contentsCss : '<?php echo $fullpath; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
					<?php } ?>
					entities : false,
					uiColor : '#FFFFFF',
					height : '<?php echo $EDHEIGHT; ?>',
					baseHref : '<?php echo $SITEURL; ?>',
					toolbar : 
					[
					<?php echo $toolbar; ?>
					]
					<?php echo $EDOPTIONS; ?>,
					tabSpaces : 10,
					filebrowserBrowseUrl : 'filebrowser.php?type=all',
					filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
					filebrowserWindowWidth : '730',
					filebrowserWindowHeight : '500',
					skin : 'getsimple'
				});				
			});
			
			$('.datepicker').each(function(){
			    $(this).datepicker({ dateFormat: '<?php echo $dateformat; ?>' });
			});
			
			$('.datetimepicker').each(function(){
				$(this).datetimepicker({ 
					dateFormat: '<?php echo $dateformat; ?>',
					timeFormat: 'hh:mm'
				})
			})
			</script>
<?php	
}


function DM_createForm($name){
	global $schemaArray;	
	global $TEMPLATE;
	global $SITEURL;
	echo '<ul class="fields">';
	if(isset($schemaArray[$name])){
		foreach ($schemaArray[$name]['fields'] as $field=>$value) {

		if ($field!="id"){
		?>
		
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo $field; ?></label>
				<div class="ui-widget-content">
					<p class="description"><?php echo $schemaArray[$name]['desc'][$field]; ?></p>
					<?php displayFieldType($field, $value,$name); ?>
				</div>
			</li>
		
		<?php
		} else {
		?>
		<li class="InputfieldHidden Inputfield_id ui-widget" id="wrap_Inputfield_id">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_id">id</label>
			<div class="ui-widget-content">
				<input id="Inputfield_id" name="id" value="0" type="hidden">
			</div>
		</li>

			
		<?php	
			}
		}
	}	
?>

	<li class="fieldsubmit Inputfield_submit_save_field ui-widget" id="wrap_Inputfield_submit">
		<label class="ui-widget-header fieldstateToggle" for="Inputfield_submit">Save Record</label>
		<div class="ui-widget-content">
			<button id="Inputfield_submit" class="mtrx_but_add" name="submit_save_field" value="Submit" type="submit">Save This Record</button>
		</div>
	</li>

</ul>
<?php
DM_outputCKHeader();
}


function displayFieldType($name, $type, $schema,$value=''){
	global $schemaArray;
	global $pagesArray;
	global $TEMPLATE;
	global $SITEURL;
	// flags for javascript code. 
	$codeedit=false;
	$datepick=false;
	$datetimepick=false;
	$textedit=false;
	$ckeditor=false;
	$options='';
	// get caching info in case we need it. 
	getPagesXmlValues();
	
	// Get the filed type
	switch ($type){
		// int field
		case "int":
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="text" size="50" maxlength="128" value="'.$value.'"></p>';
			break; 		
	// normal text field
		case "text":
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="text" size="50" maxlength="128" value="'.$value.'"></p>';
			break; 
		// long text field, full width		
		case "textlong":
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="text" size="115" maxlength="128" value="'.$value.'"></p>';
			break;
		// Slug/Title
		case "slug":
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="text" size="115" onkeyup="makeSlug(\'post-'.$name.'\');" maxlength="128" value="'.$value.'"></p>';
			break;
		// Checkbox
		case "checkbox":
			$label=$schemaArray[$schema]['label'][$name];
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="checkbox" '. ($value=='on' ? 'checked' : '') .'> '.$label.'</p>';
			break;
		// Dropdown box of existing pages on the site. Values are skug/url 
		case "pages":
			echo '<p><select id="post-'.$name.'" name="post-'.$name.'">';
			echo '<option value=""></option>';
			foreach ($pagesArray as $page){
				$page['url']==$value ? $options=' selected ' : $options='';
				echo '<option value="'.$page['url'].'" '.$options.'>'.$page['title'].'</option>';
			}
			echo '</select></p>';
			break;
		// a dropdown of current templates
		case "templates":
			$theme_templates='';
			$themes_path = GSTHEMESPATH . $TEMPLATE;
			$themes_handle = opendir($themes_path) or die("Unable to open ". GSTHEMESPATH);		
			while ($file = readdir($themes_handle))	{		
				if( isFile($file, $themes_path, 'php') ) {		
					if ($file != 'functions.php' && !strpos(strtolower($file), '.inc.php')) {		
				  $templates[] = $file;		
				}		
				}		
			}			
			sort($templates);	
			foreach ($templates as $file){
				$file==$value ? $options=' selected ' : $options='';			
				$theme_templates .= '<option value="'.$file.'" '.$options.'>'.$file.'</option>';
			}
			echo '<p><select  id="post-'.$name.'" name="post-'.$name.'">';
			echo $theme_templates;
			echo '</select></p>';
			break;
		// Datepicker. Use settings page to set the front end date format, saved as Unix timestamp
		case "datepicker";
				echo '<p><input id="post-'.$name.'" class="datepicker required" name="post-'.$name.'" type="text" size="50" maxlength="128" value="'. ((isset($value) and  $value!='') ? date(i18n('DATE_FORMAT',false),(int)$value) : '') .'"></p>';
       			$datetimepick=true;
			break;
		// DateTimepicker. Use settings page to set the front end date format, saved as Unix timestamp
		case "datetimepicker";
			echo '<p><input id="post-'.$name.'" class="datetimepicker required" name="post-'.$name.'" type="text" size="50" maxlength="128"  value="'. ((isset($value) and  $value!='') ? date(i18n('DATE_FORMAT',false).' H:i',(int)$value) : '') .'"></p>';  
       		$datepick=true;
			break;
		// Dropdown from another Table/column 
		case "dropdown":
			$table=$schemaArray[$schema]['table'][$name];
			$column=$schemaArray[$schema]['row'][$name];
			$maintable=getSchemaTable($table);
			echo '<p><select  id="post-'.$name.'" name="post-'.$name.'">';
			echo '<option></option>';
			foreach ($maintable as $row){
				if(isset($row[$column])){
					$options = $row[$column]==$value ? ' selected ' : '';
					echo '<option value="'.$row[$column].'" '.$options.'>'.$row[$column].'</option>';
				}
			}
			echo '</select></p>';
			break;
		case 'image':
			echo '<p><input class="text imagepicker" type="text" id="post-'.$name.'" name="post-'.$name.'"  value="'.$value.'" />';
			echo ' <span class="edit-nav"><a id="browse-'.$name.'" href="javascript:void(0);">Browse</a></span>';
			echo '<div id="image-'.$name.'"></div>';
			echo '</p>'; 
		
		?>
		<script type="text/javascript">
		  $(function() { 
			$('#browse-<?php echo $name; ?>').click(function(e) {
			  window.open('<?php echo $SITEURL; ?>admin/filebrowser.php?CKEditorFuncNum=1&func=addImageThumbNail&returnid=post-<?php echo $name; ?>&type=images', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
			});
		  });
		</script>
		<?php
		break;
		case 'filepicker':
			echo '<p><input class="text imagepicker" type="text" id="post-'.$name.'" name="post-'.$name.'"  value="'.$value.'" />';
			echo ' <span class="edit-nav"><a id="browse-'.$name.'" href="javascript:void(0);">Browse</a></span>';
			echo '</p>'; 
		
		?>
		<script type="text/javascript">
		  $(function() { 
			$('#browse-<?php echo $name; ?>').click(function(e) {
			  window.open('<?php echo $SITEURL; ?>admin/filebrowser.php?CKEditorFuncNum=1&returnid=post-<?php echo $name; ?>&type=all', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
			});
		  });
		</script>
		<?php
		break;
		// Textarea converted to a code editor.
		case "codeeditor":
			echo '<p><textarea class="codeeditor" id="post-'.$name.'" name="post-'.$name.'" style="width:513px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></p>';
			$codeedit=true;
			break;
		// texteditor converted to CKEditor
		case "texteditor":
		case "wysiwyg":
			echo '<p><textarea class="DMckeditor" id="post-'.$name.'" name="post-'.$name.'" style="width:513px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></p>';
			break;
		// Textarea Plain
		case "textarea":
			echo '<p><textarea class="textarea" id="post-'.$name.'" name="post-'.$name.'" style="width:513px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></p>';
			
			break;
		default:
			echo "Unknown"; 
	}
	
	
	if ($codeedit){
		?>
			<script type="text/javascript">
			jQuery(document).ready(function() { 
				  var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
				  function keyEvent(cm, e) {
					if (e.keyCode == 81 && e.ctrlKey) {
					  if (e.type == "keydown") {
						e.stop();
						setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
					  }
					  return true;
					}
				  }
				  function toggleFullscreenEditing()
					{
						var editorDiv = $('.CodeMirror-scroll');
						if (!editorDiv.hasClass('fullscreen')) {
							toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
							editorDiv.addClass('fullscreen');
							editorDiv.height('100%');
							editorDiv.width('100%');
							editor.refresh();
						}
						else {
							editorDiv.removeClass('fullscreen');
							editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
							editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
							editor.refresh();
						}
					}
				  var editor = CodeMirror.fromTextArea(document.getElementById("post-<?php echo $name; ?>"), {
					lineNumbers: true,
					matchBrackets: true,
					indentUnit: 4,
					indentWithTabs: true,
					enterMode: "keep",
					tabMode: "shift",
					theme:'default',
					mode: "text/html",
					onGutterClick: foldFunc,
					extraKeys: {"Ctrl-Q": function(cm){foldFunc(cm, cm.getCursor().line);},
								"F11": toggleFullscreenEditing, "Esc": toggleFullscreenEditing},
					onCursorActivity: function() {
						editor.setLineClass(hlLine, null);
						hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
					}
					});
				 var hlLine = editor.setLineClass(0, "activeline");
				
				})
				 
			</script>
			<?php
	}
}

function DM_query($query,$cache=true){
	$sql=new sql4array();
	$sql->createFromGlobals(false);
	$tables = $sql->get_tablenames($query);

	foreach($tables as $table){
		if(!isset($DM_tables_cache[$table]) or $cache==false) $DM_tables_cache[$table] = getSchemaTable($table);
		$sql->asset($table,$DM_tables_cache[$table]);
	}

	return $sql->query($query);
}

function DMdebuglog($log){
	global $DM_Matrix_debug;
	if ($DM_Matrix_debug){
		debuglog($log);
	}
}

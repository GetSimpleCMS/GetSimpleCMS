		<h2><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SHOWTABLE') ?></h2>
		<table id="editpages" class="tablesorter">
		<thead>
			<tr>
				<th class='sort'><?php echo i18n_r($thisfile_DM_Matrix.'/DM_TABLENAME') ?></th>
				<th class='sort' ><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NUMRECORDS') ?></th>
				<th class='sort'><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NUMFIELDS') ?></th>
				<th style="width:75px;"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_OPTIONS') ?></th>
			</tr>
		</thead>
		<tbody>
		<?php 
	$tables=0;    
		foreach($schemaArray as $schema=>$key){
			$fieldcnt = isset($key['fields']) ? count($key['fields']) : '0';
			$numRecords=DM_getNumRecords($schema);
			$maxRecords=$key['maxrecords'];
			
			//if (substr($schema,0,1)!="_"){
				$schemaName=$schema;
				if ($fieldcnt > 1){
					echo "<tr><td><a href='load.php?id=DM_Matrix&action=matrix_manager&view=".$schema."' >".$schema."</a></td>";
				} else {
					echo "<tr><td>".$schema."</td>";	
				}
				echo "<td>".$numRecords." / ".$maxRecords."</td>";
				echo "<td>".$fieldcnt."</td>";
				echo "<td>";
				echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schema."'>";
				if ($schema!='_routes'){
					echo "<img src='../plugins/DM_Matrix/images/edit.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_EDITTABLE')."' /></a>";
				} else {
					echo "<img src='../plugins/DM_Matrix/images/blank.png' title='' />";
				}
				if ($fieldcnt > 1 && (($numRecords<$maxRecords) or ($maxRecords==0))){
					echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&add=".$schema."'>";
					echo "<img src='../plugins/DM_Matrix/images/add.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_ADDRECORD')."' /></a>";
				} else {
					echo "<img src='../plugins/DM_Matrix/images/blank.png' title='' />";
				}
				if ($numRecords==0 && $schema!='_routes'){
		  		// todo: add drop table functionality
					 echo " <a href='load.php?id=DM_Matrix&action=matrix_manager&schema&drop=".$schema."' class='askconfirm' title='Delete Table $schema ?'>";
					 echo "<img src='../plugins/DM_Matrix/images/delete.png' title='Delete Table $schema' /></a>";
				}        
				echo "</td></tr>";
		$tables++;
			//}
		}
	if ($tables==0){
	  echo '<tr><td colspan="4">No Tables defined</td></tr>';	
	}		
		?>
		</tbody>
		</table>
		<div id="pager" class="pager">
		<form>
			<img src="../plugins/DM_Matrix/images/first.png" class="first"/>
			<img src="../plugins/DM_Matrix/images/prev.png" class="prev"/>
			<input type="text" class="pagedisplay"/>
			<img src="../plugins/DM_Matrix/images/next.png" class="next"/>
			<img src="../plugins/DM_Matrix/images/last.png" class="last"/>
			<select class="pagesize">
				<option selected="selected"  value="10">10</option>
				<option value="20">20</option>
				<option value="30">30</option>
				<option  value="40">40</option>
			</select>
		</form>
		</div>
		<form method="post" action="load.php?id=DM_Matrix&schema&action=matrix_manager&add">
		<ul class="fields">
		
		<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDTABLE') ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDTABLE_DESC') ?></p>
				<input type="text" class="required" id="post-addtable" name="post-addtable" />	
				<br/><br/>
				<p class="description">Max Number of records, leave blank for unlimited</p>
				<input type="text" id="post-maxrecords" name="post-maxrecords" />	
				<br/><br/>
				<button id="Inputfield_submit" class="mtrx_but_add form_submit" name="addtable" id="addtable" value="Submit" type="button">Add Table</button>
			</div>
		</li>
		</ul>
		</form>
	

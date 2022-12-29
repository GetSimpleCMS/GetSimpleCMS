<?php	
	$table=$_GET['view'];
		$fields=array();
		$tableheader='';
		$count=0;
		if(isset($schemaArray[$table]) && isset($schemaArray[$table]['fields'])){
		  foreach($schemaArray[$table]['fields'] as $schema=>$key){
			if ($schemaArray[$table]['tableview'][$schema]==1){
			  $fields[$count]['name']=$schema;
			  $fields[$count]['type']=$key;
			  
			  $tableheader.="<th class='sort'>".$schema."</th>";
			}
			$count++;
		  }
		}
		if ($table=='_routes'){
			echo "<h2>Manage Routes</h2>";
		} else {
			echo "<h2>Manage Records: ".$table."</h2>";
		}
	?>
		<table id="editpages" class="tablesorter">
		<thead><tr><?php echo $tableheader; ?><th>Opts</th></tr></thead>
		<tbody>
		<?php 
		getPagesXmlValues();
		$mytable=getSchemaTable($table);
		$record_cnt = 0;
		if(isset($mytable)){
		  foreach($mytable as $key=>$value){
			#$fields = isset($mytable[$key]['fields']) ? $mytable[$key]['fields'] : array();
			#$id = 0;
			echo "<tr>";
			foreach ($fields as $field){
			  if ($field['name']=='id') $id=$mytable[$key][$field['name']];
			  if ($field['type']=='datepicker'){
				$data= isset($mytable[$key][$field['name']]) ? date(i18n('DATE_FORMAT',false),$mytable[$key][$field['name']]) : '<b>NULL</b>';
			  } elseif ($field['type']=='datetimepicker') {
				$data= isset($mytable[$key][$field['name']]) ? date(i18n('DATE_FORMAT',false).' H:i',$mytable[$key][$field['name']]) : '<b>NULL</b>';
			  } else {
				$data= isset($mytable[$key][$field['name']]) ? $mytable[$key][$field['name']] : '<b>NULL</b>';
			  }
			  echo "<td>".$data."</td>"; 
			}
			echo "<td><a href='load.php?id=DM_Matrix&action=matrix_manager&add=".$table."&field=".$id."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Record ".$id."' /></a>";
			//todo delete functionality
			echo " <a href='load.php?id=DM_Matrix&action=matrix_manager&view=".$table."&delete=".$id."' title='Delete Record ".$id." ?' class='askconfirm'><img src='../plugins/DM_Matrix/images/delete.png' title='Delete Record ".$id."' /></a>";
			echo "</td></tr>";
			$record_cnt++;
		  }
		$maxRecords=$record_cnt;
		} else {
			
		}  
		if($record_cnt==0){
		  echo '<tr><td colspan="'.($count+1).'">Table has no records</td></tr>';	 
		}
		?>
		
		</tbody>
		</table>
	<?php 
	$numRecords=$record_cnt;
	$maxRecords=$schemaArray[$table]['maxrecords'];
	
	if ($maxRecords==0 or $numRecords < $maxRecords){
		echo "<a class='mtrx_but_add' id='matrix_recordadd' href='load.php?id=DM_Matrix&action=matrix_manager&add=".$table."'>Add Record</a>";
	} else {
		echo "<p class='error'>Max Number of Records Reached</p>";
	}
	?>     
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
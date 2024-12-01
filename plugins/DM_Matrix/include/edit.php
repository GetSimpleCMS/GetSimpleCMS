<?php
			
		$schemaname=$_GET['edit'];
		echo "<h2>Edit Schema: ".$schemaname."</h2>";
		?>
		<table id="edittable" class="tablesorter">
		<thead><tr><th>Name</th><th >Type</th><th style="width:75px;">Options</th></tr>
		</thead>
		<tbody>
		<?php 
		if( isset($schemaArray[$schemaname]['fields'])){
			foreach($schemaArray[$schemaname]['fields'] as $schema=>$key){
				echo "<tr><td>".$schema."</td><td>".$key."</td>";
				if ($schema!="id"){
					echo "<td><a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schemaname."&field=".$schema."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Field' /></a></td>";
				} else {
					echo "<td></td>";
				}
				echo "</tr>";
			}
		}	
		?>
		
		</tbody>
		</table>
		<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&edit=<?php echo $schemaname; ?>&addfield">
		<?php if (isset($_GET['field'])){
			$formName = $_GET['field'];
			$formType = $schemaArray[$_GET['edit']]['fields'][$_GET['field']];
			$formDesc= $schemaArray[$_GET['edit']]['desc'][$_GET['field']];
			$formLabel = $schemaArray[$_GET['edit']]['label'][$_GET['field']];
			$formHeading = $schemaArray[$_GET['edit']]['desc'][$_GET['field']];
			$formCacheIndex = $schemaArray[$_GET['edit']]['cacheindex'][$_GET['field']];
			$formTableView = $schemaArray[$_GET['edit']]['tableview'][$_GET['field']];
			if ($formType=='dropdown'){
				$formTable = $schemaArray[$_GET['edit']]['table'][$_GET['field']];
				$formTableRow = $schemaArray[$_GET['edit']]['row'][$_GET['field']];
			}
			$editing=true;
			echo '<h3>Editing Field : '.$_GET['field'].'</h3>'; 
			$editing=true;
			
		} else {
			echo '<h3>Add New Field</h3>';
			$formName = "";
			$formType = "";
			$formDesc= "";
			$formLabel = "";
			$formHeading = "";
			$formCacheIndex = "";
			$formTableView = "";
			$formTable = "";
			$formTableRow = "";
		}
		?>
		<ul class="fields">
			<li class="ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Name</label>
				<div class="ui-widget-content">
					<p class="description">Any combination of ASCII letters [a-z], numbers [0-9], or underscores (no dashes or spaces).</p>
					<input type="text" id="post-name" name="post-name" class="required" size="25" <?php echo " value='".$formName."'"; ?> >
				</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Type</label>
				<div class="ui-widget-content">
					<p class="description">After selecting your field type, you may be presented with additional configuration options specific to the field type you selected.</p>
					<select id="post-type" name="post-type" class="required">
						<option value=""></option>
						
						<?php 
						$types=array('int','slug','text','textlong','checkbox','pages','dropdown','templates','datepicker','datetimepicker','image','filepicker','textarea','codeeditor','wysiwyg'); 
						foreach ($types as $type){
							if ($formType==$type){
								$sel=" selected ";
							} else {
								$sel="";
							}
							echo "<option value='".$type."' ".$sel.">".$type."</option>"; 
						}
						?>	
					</select>
					<div id="fieldoptions">
						<?php 
						if ($formType=='dropdown'){
						?>
						<div id='field-dropdown' >
							<br/>
							<p class="description">Please Select a Table</p>
							<select id="post-table" name="post-table" >
								<option value=""></option>
								<?php 
									foreach($schemaArray as $schema=>$key){
										echo '<option value="'.$schema.'" data-fields="';
											foreach ($schemaArray[$schema]['fields'] as $field=>$key){
												echo $field.',';
											}
										
										echo '"';
										if ($schema==$formTable) echo " selected ";
										echo ' ">'.$schema.'</option>';	
									}
								
								?>
							</select>
							<p class="description">Please select a row from the table</p>
							<select id="post-row" name="post-row" >
								<option></option>
								<?php 
									foreach ($schemaArray[$formTable]['fields'] as $field=>$key){
										echo '<option ';
										if ($field==$formTableRow) echo " selected ";
										echo '>'.$field.'</option>';
									}
								?>
							</select>
						</div>
						<?php	
						}
						?>
					</div>	
				</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a label</label>
			<div class="ui-widget-content">
				<p class="description">Add a label for this Field.</p>
				<input type="text" <?php echo " value='".$formLabel."'"; ?> id="post-label" name="post-label" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a Description</label>
			<div class="ui-widget-content">
				<p class="description">Additional information describing this field and/or instructions on how to enter the content.</p>
				<input type="text" <?php echo " value='".$formDesc."'"; ?> id="post-desc" name="post-desc" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Additional Options</label>
			<div class="ui-widget-content">
				<p class="description">Additional options for this Field</p>
				<input class="hidden" type="checkbox" id="post-cacheindex" name="post-cacheindex" <?php if ($formCacheIndex=='1') echo " checked "; ?> >
				<!--&nbsp;Allow this field to be indexed<br/> -->
				
				<input type="checkbox" id="post-tableview" name="post-tableview" <?php if ($formTableView=='1') echo " checked "; ?>>
				&nbsp;Show in Table View
				
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_submit">
				<label class="ui-widget-header fieldStateToggle" for="field_submit">Save this Field</label>
				<div class="ui-widget-content">
					<button id="field_submit" class="mtrx_but_add form_submit" name="submit" value="Save Field" type="submit">Save Field</button>
				</div>
			</li>
		</form>
		</ul>
		<!-- hidden elements for additional options on fields -->
		<div id='field-dropdown' class='hidden'>
			<br/>
			<p class="description">Please Select a Table</p>
			<select id="post-table" name="post-table" >
				<option value=""></option>
				<?php 
					foreach($schemaArray as $schema=>$key){
						echo '<option value="'.$schema.'" data-fields="';
							foreach ($schemaArray[$schema]['fields'] as $field=>$key){
								echo $field.',';
							}
						echo '" ">'.$schema.'</option>';	
					}
				
				?>
			</select>
			<p class="description">Please select a rown from the table</p>
			<select id="post-row" name="post-row" >
				<option></option>
			</select>
		</div>
<?php 
$table="";
$state=false;
$file='../data/other/debug.xml';

// see if were submitting the form
if (isset($_POST['submit'])){
	// if so either delete or create the debug.xml file
	if ($_POST['submit']=="Turn Off"){
		unlink($file);
	} 
	if ($_POST['submit']=="Turn On"){
		$ourFileHandle = fopen($file, 'w') or die("can't open file");
		fclose($ourFileHandle);
	} 
}

// check if debug.xml exists
if (file_exists($file)) {
		$state=true;
}

$table .= '<tr id="tr-1" >';
$table .= '<form action="plugins.php?plugin=debug&page=config" method="POST">';
if ($state==true){
	// if enabled print disable button
	$table .= '<td>Debugging is Enabled</td>';
	$table .= '<td style="width:100px;" ><input type="submit" value="Turn Off" name="submit" /></td>';
} else {
	// if enabled print enable button
	$table .= '<td>Debugging is Disabled</td>';
	$table .= '<td style="width:70px;" ><input type="submit" value="Turn On" name="submit" /></td>';
}
$table .= "</form>";
$table .= '</tr>';
?>
<label>Debugging</label>
<table class="highlight paginate">
<?php 

echo $table; ?>
	
</table>

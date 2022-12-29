<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

function kt_build_h(){
	global $SITEURL;
	if(!isset($_GET['old']) && !isset($_GET['new']))
		$olnew = 'old';
	else{
		$olnew = (isset($_GET['old'])) ? 'new' : 'old';
		$olnew = (isset($_GET['new'])) ? 'old' : 'new';
	}
	$kturl = $SITEURL . 'admin/load.php?id=kt-blocklogin&'. $olnew . '=1';
	$kth5 = '<h5>Click <a href="'.$kturl .'">here </a>to see the ' . $olnew .' log data file</h5>';
	return $kth5;
}

function kt_quick_check($counter, $timestamp){
	$currenttime = time();
	return ( ($counter %3 ==0)  && ($currenttime - $timestamp < 3600 ) ) ? 'Blocked' : 'Not Blocked';
}

function kt_build_table($fileToOpen = KTFAILEDPATH) {
	$retstring = '';
	$handle = new XMLReader();
	$handle->open($fileToOpen,LIBXML_NOBLANKS) ;
	while($handle->read()){
		if($handle->hasAttributes)
			$retstring .= '<tr><td scope = "row" >' . str_replace('ip','',$handle->name) . '</td><td>' . $handle->getAttribute('counter') . '</td><td>' . kt_quick_check($handle->getAttribute('counter'),$handle->getAttribute('start')) . '</td><td>'. date('e'). '</td><td>' . date( 'M d Y g:i a', $handle->getAttribute('start')) . '</td>' ;
	}
	if($retstring == '')
		$retstring = '<tr><td colspan="5"> None Found</td></tr>' ;
	echo $retstring ;
}

$kturl = (file_exists(KTFAILEDPATHBU)) ? kt_build_h() : '';

?>
	<div id= 'kt-display-table'> 
	<?php echo $kturl?>
	<table>
		<thead>
			<tr>
				<th scope="col">IP Adress</th>
				<th scope="col">Total Failed Attempts</th>
				<th scope="col">Current Status</th>
				<th scope="col">Time Zone</th>
				<th scope="col">Last Login Attempt</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(isset($_GET['old'])) 
				kt_build_table(KTFAILEDPATHBU);
			else 
				kt_build_table(KTFAILEDPATH);
			?>
		</tbody>
		
	</table>
	</div>
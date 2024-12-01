<?php
# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,
	'Display errors log',
	'1',
	'Cumbe (Miguel Embuena Lance)',
	'http://www.cumbe.es/',
	'Description:  Save and display errors 404, and other errors.',
	'support', //page type
	'display_error'
);

//set internationalization
if (basename($_SERVER['PHP_SELF']) != 'index.php') { 
	// BACKEND only

	i18n_merge('displayerror', $LANG);
	i18n_merge('displayerror', 'en_US');

	//add to sidebar of support
	add_action('support-sidebar','createSideMenu', array('displayerror', stripslashes(html_entity_decode(i18n_r('displayerror/cbsee_errors')))));

} else {
	//FRONTEND
	
	//save errors 404
	add_action('error-404', 'saveurl');
}

///////////////////////////////////////////////
//////   BACKEND - ADMIN - PAGES     //////////
///////////////////////////////////////////////

function display_error(){
	global $i18n, $LANG;

	$log_path = GSDATAOTHERPATH.'logs/';

	$action = '';
	$action1 = '';
	$classdeletelog = '';
	$classdelete404 = '';
	$classlog = '';	
	$class404 = '';
	$classsum404 = '';
	$class = 'class="current"';
	if (isset($_GET['action'])){
		if (@$_GET['action'] == 'deletelog') {
			$action = 'deletelog';
			$classdeletelog = $class;
		}
		if ($_GET['action'] == 'delreglog') {
			$action = 'delreglog';
			//$nreglog = $_GET['nreglog'];
		}
		if (@$_GET['action'] == 'delete404') {
			$action = 'delete404';
			$classdelete404 = $class;
		}
		if ($_GET['action'] == 'delreg404') {
			$action = 'delreg404';
		}
		if ($_GET['action'] == 'delsumreg404') {
			$action = 'delsumreg404';
			$action1 = 'sum404';
			$classsum404 = $class;
		}
		if($_GET['action'] == 'log') {
			$action = 'log';
			$classlog = $class;
		}
		if ($_GET['action'] == '404') {
			$action = '404';
			$class404 = $class;
			if (isset($_GET['action1'])){
				if ($_GET['action1'] == 'sum404') {
					$action1 = 'sum404';
					$classsum404 = $class;
				}
			}
		}

	}	
?>	
	<div style="text-align: center;"><h3 style="margin-bottom: 10px;"><?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cbsee_errors')))); ?></h3></div>
	<div style="margin: 0px auto 10px; text-align: center;">
		<div class="edit-nav">
			<a href="log.php?log=failedlogins.log" title="'.stripslashes(html_entity_decode($i18n['VIEW_FAILED_LOGIN'])).'"><?php echo strtoupper(stripslashes(html_entity_decode($i18n['VIEW_FAILED_LOGIN']))); ?></a>
			<a <?php echo $class404; ?> href="load.php?id=displayerror&amp;action=404" title=" <?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cb404log'))); ?>"> <?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cb404log')))); ?></a>
			<a <?php echo $classlog; ?> href="load.php?id=displayerror&amp;action=log" title=" <?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbgslog'))); ?>"> <?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cbgslog')))); ?></a>
		</div>
	</div>
	<div class="clear"></div>

<?php
	global $SITEURL;

	if ($action != ''){
?>
<script type="text/javascript">
	function HideBoxError(){
		document.getElementById("boxerror").style.visibility = "hidden";
		document.getElementById("boxerror").style.display = "none";
	}
	function set_cbCheck(cbcheck, count, maxim){
		for (q=maxim; q>= (maxim-count); q--){
			document.getElementById(cbcheck+q).checked = document.getElementById(cbcheck).checked;
		}	
	}
</script>

		<div style="margin-top:10px;">
<?php

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == 'deletelog') {
			//Remove log of getsimple
			$log_name = 'errorlog.txt';
			$log_file = $log_path . $log_name;
			if(file_exists($log_file)) {
				unlink($log_file);
				exec_action('logfile_delete');
?>
				<label>Log <?php echo $log_name;?></label> <?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbdeletedfile'))); ?>
			  	</div>
				</div>
				</div>
				<div id="sidebar" >
				<?php include('template/sidebar-support.php'); ?>
				</div>	
				<div class="clear"></div>
				</div>
				<?php get_template('footer'); ?>
<?php
	                	exit;
			}
		}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == 'delreglog'){
			if (isset($_REQUEST['data'])) {
				$data = $_REQUEST['data'];
				//Remove 1 record of log GS error
				$log_name = 'errorlog.txt';
				$log_file = $log_path . $log_name;
				if($file = @fopen($log_file,"r")) {
					$content = file("$log_file");
				}
				foreach ($data as $nreg) {
					if (isset($content[$nreg])) { 
						unset($content[$nreg]); 
					}
				}
				fclose( $file );
				$content_new = implode('', $content);
				$file = fopen($log_file, 'w');
				fwrite($file, $content_new);
				fclose( $file );
			}
			$action = 'log';
?>
			<div id="boxerror" style="display: block; visibility: visible; color: #308000; text-align: center; font-size: 14px; width: 300px; padding: 5px 0px; background-color: #FFFBC1; border: 1px solid #E6DB55;"><?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbregdel'))); ?></div>

<script type="text/javascript">
        setTimeout(HideBoxError,5000);    
</script>
<?php
		}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == 'delete404') {
			//Remove log of errors 404
			$log_name = 'error404.log';
			$log_file = $log_path . $log_name;
			if(file_exists($log_file)) {
				unlink($log_file);
				exec_action('logfile_delete');
?>
				<label>Log <?php echo $log_name;?></label> <?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbdeletedfile'))); ?>
			  	</div>
				</div>
				</div>
				<div id="sidebar" >
				<?php include('template/sidebar-support.php'); ?>
				</div>	
				<div class="clear"></div>
				</div>
				<?php get_template('footer'); ?>
<?php
	                	exit;
			}
		}
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == 'delreg404'){
			if (isset($_REQUEST['data'])) {
				$data = $_REQUEST['data'];
				//Remove 'n' records of error 404
				$log_name = 'error404.log';
				$log_file = $log_path . $log_name;
				$domDocument = new DomDocument();
				$domDocument->preserveWhiteSpace = FALSE; 
				$domDocument->load($log_file);
				$domNodeList = $domDocument->documentElement;
    	    	$domNodeList = $domDocument->getElementsByTagname('entry');
				$count = 0;
				foreach ($data as $nreg) {
					$ndL = $domNodeList ->item($nreg)->parentNode;
					$ndL -> removeChild($domNodeList ->item($nreg));
					$count ++;
				}	
				//save log file modified
				$domDocument->save($log_file);
			}
			$action = '404';
?>
			<div id="boxerror" style="display: block; visibility: visible; color: #308000; text-align: center; font-size: 14px; width: 300px; padding: 5px 0px; background-color: #FFFBC1; border: 1px solid #E6DB55;"><?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbregdel'))); ?></div>

<script type="text/javascript">
        setTimeout(HideBoxError, 5000);    
</script>
<?php
		}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == 'delsumreg404'){
			if (isset($_REQUEST['data'])) {
				$data = $_REQUEST['data'];

				//Remove 'n' records of error 404 filters by url
				$log_name = 'error404.log';
				$log_file = $log_path . $log_name;
				$domDocument = new DomDocument();
				$domDocument->preserveWhiteSpace = FALSE; 
				$domDocument->load($log_file);

				//DOMXPath for filter
				$xpath = new DOMXPath($domDocument);
				foreach ($data as $regurl){
					$verN = $xpath->query("entry[failed_page='".trim($regurl)."']");
					$num = 	$verN->length;
					foreach ($verN as $entry) {
						$delreg = $entry->parentNode;
						$delreg->removeChild($entry);
					}
				}
				$domDocument->save($log_file);
				$action = '404';
?>
				<div id="boxerror" style="display: block; visibility: visible; color: #308000; text-align: center; font-size: 14px; width: 300px; padding: 5px 0px; background-color: #FFFBC1; border: 1px solid #E6DB55;"><?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbregdel'))); ?></div>

<script type="text/javascript">
        setTimeout(HideBoxError, 5000);    
</script>
<?php
			}
		}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == 'log') {
			//see errors of log GetSimple
			$log_name = 'errorlog.txt';
			$log_file = $log_path . $log_name;
			if($file = @fopen($log_file,"r")) {
?>
				<h3 style="margin-bottom: 5px;"><?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cberrorsrecgs')))); ?></h3>
				<div class="edit-nav">	
					<a <?php echo $classdeletelog; ?> onClick= "return confirm(&quot;<?php i18n('displayerror/cbdelfilelog'); ?>&quot;)" href="load.php?id=displayerror&amp;action=deletelog" title="<?php stripslashes(html_entity_decode(i18n_r('displayerror/cbdelfilelog'))); ?>"><?php strtoupper(stripslashes(html_entity_decode(i18n('displayerror/cbdelfilelog')))); ?></a>
				</div>
				<div class="clear"></div>
<?php
				$content = file("$log_file");
				$maximo = (count($content)>200) ? 200 : count($content);
?>
				<h2 style="margin-bottom: 7px; text-decoration: underline;"> <?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbnumberlines'))).': '.count($content).'. '.stripslashes(html_entity_decode(i18n_r('displayerror/cblast'))).' '.$maximo.' '.stripslashes(html_entity_decode(i18n_r('displayerror/cblines'))); ?>:</h2>
			 	<form action="load.php?id=displayerror&amp;action=delreglog" method="POST">
					<table class="edittable highlight">
					<tbody><tr><th style="text-align: right;"><input id="checklog" type="checkbox" style="margin-top:3px;" title="<?php i18n('displayerror/cball'); ?>" onChange="set_cbCheck(&quot;checklog&quot;,&quot;<?php echo $maximo; ?>&quot;,&quot;<?php echo count($content)-1; ?>&quot;)" /></th><th style="font-size: 11px; font-weight: normal; text-transform: lowercase;">(<?php i18n('displayerror/cball'); ?>)</th><th><input type="submit" name="deleteselect" value="<?php i18n('displayerror/cbdeleteselect'); ?>" style="float:right;"/></th></tr>
<?php
					$count = 1;
					for ($q = count($content); $q >= count($content) - $maximo && $q>0; $q--){
?>						<tr>						
							<td rowspan="2" style="vertical-align: middle;"><input id="checklog<?php echo ($q-1); ?>" type="checkbox" style="margin-top:3px;" value="<?php echo ($q-1); ?>" title="<?php echo i18n_r('displayerror/cbndelc').' '.($q-1); ?>" name="data[]" /></td>
							<td><b><?php echo i18n_r('displayerror/cbdate').'</b>: '.substr($content[$q-1], 0, strpos($content[$q-1], ']')+1); ?></td>
							<td></td>
						</tr>
						<tr>
							<td colspan="2"><?php echo substr($content[$q-1], strpos($content[$q-1], ']')+1, strlen($content[$q-1])); ?>
						</tr>
					
<?php
					} 
?>
					</tbody>
					</table>
				</form>
			
<?php
				@fclose($file);
			} else {
				echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cbfilenoexits')))).'<br >';
			}
		}

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////
		if ($action == '404') {
			//see errors 404
			$log_name = 'error404.log';
			$log_file = $log_path . $log_name;
			if (file_exists($log_file)){
?>
				<h3 style="margin-bottom: 5px;"><?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cberrorsrec404')))); ?>:</h3>
				<div class="edit-nav">
					<a <?php echo $classdelete404; ?> onClick= "return confirm(&quot;<?php echo i18n_r('displayerror/cbdelfile404'); ?>&quot;)" href="load.php?id=displayerror&amp;action=delete404" title="<?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbdelfile404'))); ?>"><?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cbdelfile404')))); ?></a>
					<a <?php echo $classsum404; ?> href="load.php?id=displayerror&amp;action=404&amp;action1=sum404" title="<?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbsummarize404'))); ?>"><?php echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cbsummarize')))); ?></a>
				</div>
				<div class="clear"></div>
<?php
				if ($action1 == ''){
					// displays indivivuals errors 404 
					$log_data = getXML($log_file);
					$count = count($log_data);
?>
					<h2 style="margin-bottom: 7px; text-decoration: underline;"> <?php echo stripslashes(html_entity_decode(i18n_r('displayerror/cbnumber'))).' '. stripslashes(html_entity_decode(i18n_r('displayerror/cbdata'))).': <span style="color: #777777; font-family: Times New Roman;">'.count($log_data); ?></span></h2>
					<form action="load.php?id=displayerror&amp;action=delreg404" method="POST">
						<table class="edittable highlight" id="sum404">
						<tbody><tr><th style="text-align: right;"><input id="404check" type="checkbox" style="margin-top:3px;" title="<?php i18n('displayerror/cball'); ?>" onChange="set_cbCheck(&quot;404check&quot;,&quot;<?php echo count($log_data); ?>&quot;,&quot;<?php echo count($log_data)-1; ?>&quot;)" /></th><th style="font-size: 11px; font-weight: normal; text-transform: lowercase;">(<?php i18n('displayerror/cball'); ?>)<input type="submit" name="deleteselect" value="<?php i18n('displayerror/cbdeleteselect'); ?>" style="float:right;"/></th><th style="text-align: center;">IP</th><th style="text-align: center;">Spam?</th></tr>
<?php
						for ($q = $count; $q >0; $q --){
							$log = $log_data ->xpath("entry[position()=".($q)."]");
?>
							<tr>
								<td rowspan="2" class="secondarylink" style="vertical-align: middle; width: 5%;"><input id="404check<?php echo ($q-1); ?>" type="checkbox" style="margin-top:3px;" value="<?php echo ($q-1); ?>" title="<?php echo i18n_r('displayerror/cbndelc').' '.($q-1); ?>" name="data[]"> </td>
								<td style="width:70px;"><b><?php echo i18n_r('displayerror/cbdate').'</b>: '.$log[0]->date; ?></td>
								<td style="text-align: center; width: 5%;"><a href="http://www.geobytes.com/IpLocator.htm?GetLocation&IpAddress=<?php echo $log[0]->ip_address; ?>" target="_blank" ><?php echo $log[0]->ip_address; ?></a></td>
								<td style="text-align: center; width: 5%;"><?php echo checkspam($log[0]->ip_address); ?></td>
								<tr><td colspan="3">URL: <?php echo $log[0]->failed_page; ?></td></tr>
							</tr>
							<?php $count --; 
						} ?>
						</tbody>
						</table>
					</form>
<?php
				}

//////////////////////////////////////////////////////////////////////////////////
				if ($action1 == 'sum404') {
					// displays summarize errors 404 
					$log_data = getXML($log_file);
   					$domDocument = new DomDocument();
					$domDocument->load($log_file);

					//DOMXPath for filter
					$xpath = new DOMXPath($domDocument);
					$verN = $xpath->query("entry/failed_page");			
					$num = 	$verN->length;			
					echo '<br />';		
					$count = 0;
					if (!is_null($verN)) {
						foreach ($verN as $node) {
							$cbdata[$count] = $node->nodeValue;
							$count ++;
						}
						$cbdata1 = array_values(array_unique($cbdata));

						echo '<h2 style="margin-bottom: 7px; text-decoration: underline;">'.i18n_r('displayerror/cbnumber').' '.i18n_r('displayerror/cbdata').': <span style="color: #777777; font-family: Times New Roman;">'.count($cbdata1).'</span></h2>';
						
						foreach($cbdata1 as $key=>$value){
							$verN = $xpath->query("entry[failed_page='$value']");
							$num = 	$verN->length;		
							$cuantos[$key]=	$num;
						}
						arsort($cuantos);
?>
						<form action="load.php?id=displayerror&amp;action=delsumreg404" method="POST">
							<table class="edittable highlight" id="sum404">
								<tbody><tr><th style="text-align: right;"><input id="404scheck" type="checkbox" style="margin-top:3px;" title="<?php i18n('displayerror/cball'); ?>" onChange="set_cbCheck(&quot;404scheck&quot;,&quot;<?php echo count($cbdata1); ?>&quot;)" /></th><th  style="font-size: 11px; font-weight: normal; text-transform: lowercase;">(<?php i18n('displayerror/cball'); ?>)<input type="submit" name="deleteselect" value="<?php i18n('displayerror/cbdeleteselect'); ?>" style="float:right;"/></th><th style="text-align: center;">VISITS</th></tr>
<?php
								$count = 1;
								foreach ($cuantos as $key=>$value){ ?>
									<tr>
										<td class="secondarylink" style="width: 5%;"><input id="404scheck<?php echo ($count-1); ?>" type="checkbox" style="margin-top:3px;" value="<?php echo htmlentities($cbdata1[$key]); ?>" name="data[]"> </td>
										<td style="width:70%;"><b>Url</b>: <?php echo $cbdata1[$key]; ?></td>
										<td style="text-align: center; width: 5%;"><?php echo $value; ?></td>
									</tr>
									<?php $count ++; ?>
						
								<?php } ?>
								</tbody>
							</table>
						</fom>
<?php
					}
				}
			} else {
				echo strtoupper(stripslashes(html_entity_decode(i18n_r('displayerror/cbfilenoexits')))); ?><br >
<?php
			}	
		}
		echo '</div>'."\n";
	}

}

function checkspam($ip){
//http://cleantalk.org/wiki/doku.php/bl_requests_en
//http://cleantalk.org/blacklists?format=json&email=stop_email@example.com&ip=127.0.0.1&domain=example.com
	$pageSpec = 'http://cleantalk.org/blacklists?format=json&ip='.$ip;
    $ch = curl_init($pageSpec);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $tmp = curl_exec ($ch);
    curl_close ($ch);
	$successi = stripos($tmp, ':');
	$successp = stripos($tmp, ',');
	$success = substr($tmp, $successi+1, $successp-($successi+1) );
	$appearsi = strrpos($tmp, ':');
	$appearsp = strrpos($tmp, '}}');
	$appears = substr($tmp, $appearsi+1, $appearsp-($appearsi+1) );
	if ($success == '1'){
		return ($appears > 0) ? '<a href="http://cleantalk.org/blacklists/'.$ip.'" target="_blank">Yes</a>': 'No' ;
	}
}

/////////////////////////////////////////////////////
//////   FRONT END: check error 404        //////////
/////////////////////////////////////////////////////

function saveurl(){

	$err_file= GSDATAOTHERPATH.'logs/error404.log';

	//if not exists file => create this file
	if (!file_exists($err_file)) {       
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	} else {
		$xml = getXML($err_file);
	}
	$dater = date("r");
	$thislog = $xml->addChild('entry');
		$thislog->addChild('date', $dater);
		$cdata = $thislog->addChild('ip_address');
			$ip = getenv("REMOTE_ADDR");
			$cdata->addCData(htmlentities($ip));
		$cdata = $thislog->addChild('failed_page');
			$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$cdata->addCData(htmlentities($url));
	XMLsave($xml, $err_file);
}

?>

<?php 
/****************************************************
*
* @File: 		log.php
* @Package:	GetSimple
* @Action:	Displays the log file passed to it 	
*
*****************************************************/

	require_once('inc/functions.php');
	require_once('inc/plugin_functions.php');
	$file = 'website.xml';
	$path = tsl('../data/other/');
	global $SITENAME;
	global $SITEURL;
	
	$userid = login_cookie_check();

	$log_name = $_GET['log'];
	$log_path = tsl('../data/other/logs/');
	$log_file = $log_path . $log_name;
	
	if (@$_GET['action'] == 'delete') {
		unlink($log_file);
		header('Location: support.php?success=Log '.$log_name . $i18n['MSG_HAS_BEEN_CLR']);
		exit;
	}
	
	if(file_exists($log_file)) {
		$log_data = getXML($log_file);
	}


?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['SUPPORT'].' &raquo; '.$i18n['LOGS']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['SUPPORT'];?> <span>&raquo;</span> <?php echo $i18n['VIEWING'];?> &lsquo;<span class="filename" ><?php echo @$log_name; ?></span>&rsquo;</h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
			<label><?php echo $i18n['VIEWING'];?> <?php echo $i18n['LOG_FILE'];?>: &lsquo;<em><?php echo @$log_name; ?></em>&rsquo;</label>
			<div class="edit-nav" >
				<a href="log.php?log=<?php echo $log_name; ?>&action=delete" accesskey="c" class="delconfirm" title="<?php echo $i18n['CLEAR_ALL_DATA'];?> <?php echo $log_name; ?>?" /><?php echo $i18n['CLEAR_THIS_LOG'];?></a>
				<div class="clear"></div>
			</div>
			<ol class="more" >
				<?php 
				$count = 1;

				foreach ($log_data as $log) {
					echo '<li><p style="font-size:11px;line-height:15px;" ><b style="line-height:20px;" >'.$i18n['LOG_FILE_ENTRY'].'</b><br />';
					foreach($log->children() as $child) {
					  $name = $child->getName();
					  echo '<b>'. ucwords($name) .'</b>: ';
					  
					  $d = $log->$name;
					  $n = strtolower($child->getName());
					  $ip_regex = '/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/';
					  $url_regex = @"((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)";
					  
					  
					  //check if its an url address
					  if (do_reg($d, $url_regex)) {
					  	$d = '<a href="'. $d .'" target="_blank" >'.$d.'</a>';
					  }
					  
					  //check if its an ip address
					  if (do_reg($d, $ip_regex)) {
					  	if ($d == $_SERVER['REMOTE_ADDR']) {
					  		$d = $i18n['THIS_COMPUTER'].' (<a href="http://ws.arin.net/whois/?queryinput='. $d.'" target="_blank" >'.$d.'</a>)';
					  	} else {
					  		$d = '<a href="http://ws.arin.net/whois/?queryinput='. $d.'" target="_blank" >'.$d.'</a>';
					  	}
					  }
					  
					  //check if its an email address
					  if (check_email_address($d)) {
					  	$d = '<a href="mailto:'.$d.'">'.$d.'</a>';
					  }
					  
					  //check if its a date
					  if ($n === 'date') {
					  	$d = lngDate($d);
					  }
					  	
					  echo $d;
					  echo ' <br />';
					}
					echo "</p></li>";
					$count++;
				}				
				
				?>
			</ol>
		</div>
		
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>	
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
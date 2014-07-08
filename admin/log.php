<?php 
/**
 * View Log
 *
 * Displays the log file passed to it 
 *
 * @package GetSimple
 * @subpackage Support
 */


// Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

// Variable Settings
$log_name = isset($_GET['log']) ? $_GET['log'] : '';
$log_path = GSDATAOTHERPATH.'logs/';
$log_file = $log_path . $log_name;

$whois_url = 'http://whois.arin.net/rest/ip/';

// filepath_is_safe returns false if file does nt exist
if(!isset($log_name) || !filepath_is_safe($log_file,$log_path)) $log_data = false;

if (isset($_GET['action']) && $_GET['action'] == 'delete' && strlen($log_name)>0) {
	check_for_csrf("delete");
	unlink($log_file);
	exec_action('logfile_delete');
	redirect('log.php?success='.urlencode('Log '.$log_name . i18n_r('MSG_HAS_BEEN_CLR')));
}

if (!isset($log_data)) $log_data = getXML($log_file);

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('SUPPORT').' &raquo; '.i18n_r('LOGS')); 

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
		<?php if(empty($log_name)){
			echo '<h3 class="floated">'.i18n_r('VIEW_LOG_FILE').'</h3><div class="clear"></div>';
			echo '<ul>';
			echo '<li><a href="log.php?log=failedlogins.log">Failed Logins</a></li>';
			echo '<li><a href="log.php?log=logins.log">Logins</a></li>';
			echo '</ul>';
		}
		else { ?>	
			<h3 class="floated"><?php echo i18n_r('VIEW_LOG_FILE');?><span> / <?php echo var_out($log_name); ?></span></h3>
			<div class="edit-nav" >
				<a href="log.php?log=<?php echo $log_name; ?>&action=delete&nonce=<?php echo get_nonce("delete"); ?>" accesskey="<?php echo find_accesskey(i18n_r('CLEAR_ALL_DATA'));?>" title="<?php i18n('CLEAR_ALL_DATA');?> <?php echo $log_name; ?>?" /><?php i18n('CLEAR_THIS_LOG');?></a>
				<div class="clear"></div>
			</div>
			
			<?php if (!$log_data){
				 echo '<p><em>'.i18n_r('LOG_FILE_EMPTY').'</em></p>'; 
			} else { ?>	 
			<ol class="more" >
				<?php 
				$count = 1;

				if ($log_data) {
					foreach ($log_data as $log) {
						echo '<li><p style="font-size:11px;line-height:15px;" >';
						foreach($log->children() as $child) {
						  $name = $child->getName();
						  echo '<b>'. stripslashes(ucwords($name)) .'</b>: ';
						  
						  $d = $log->$name;
						  $n = lowercase($child->getName());
						  $ip_regex = '/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/';
						  $url_regex = @"((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)";
						  
						  
						  //check if its an url address
						  if (do_reg($d, $url_regex)) {
							$d = '<a href="'. $d .'" target="_blank" >'.$d.'</a>';
						  }
						  
						  //check if its an ip address
						  if (do_reg($d, $ip_regex)) {
							if ($d == $_SERVER['REMOTE_ADDR']) {
								$d = i18n_r('THIS_COMPUTER').' (<a href="'. $whois_url . $d.'" target="_blank" >'.$d.'</a>)';
							} else {
								$d = '<a href="'. $whois_url . $d.'" target="_blank" >'.$d.'</a>';
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
							
						  // echo stripslashes($d);
						  echo $d;
						  echo ' <br />';
						}
						echo "</p></li>";
						$count++;
					}
				} 
				
				?>
			</ol>
			<?php } ?>
			<?php } ?>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-support.php'); ?>
	</div>	

</div>
<?php get_template('footer'); ?>
<?php
/*
Plugin Name: Send Anonymous Data
Description: GetSimple needs your anonymous data so we can understand our users better
Version: 1.0
Author: Chris Cagle
Author URI: http://get-simple.info/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,
	'Send Anonymous Data',
	'1.0',
	'Chris Cagle',
	'http://get-simple.info/',
	'GetSimple needs your anonymous data so we can understand our users better. It\'s completely voluntary and will help us improve our product.',
	'plugin',
	'gs_anonymousdata'
);

# activate hooks
add_action('plugins-sidebar','createSideMenu',array($thisfile,'Send Anonymous Data')); 

if ( ! function_exists('get_tld_from_url')){ 
	function get_tld_from_url( $url ){
		$tld = null;
		$url_parts = parse_url( (string) $url );
		if( is_array( $url_parts ) && isset( $url_parts[ 'host' ] ) )	{
			$host_parts = explode( '.', $url_parts[ 'host' ] );
			if( is_array( $host_parts ) && count( $host_parts ) > 0 )	{
				$tld = array_pop( $host_parts );
			}
		}
		return $tld;
	}
}
if ( ! function_exists('glob_recursive')){ 
	function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
      $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
	}
}

function gs_anonymousdata() {
	
	#grab data from this installation
	if(isset($_POST['preview'])) {
		global $LANG, $TIMEZONE, $SITEURL, $live_plugins;
		
		$php_modules = get_loaded_extensions();
		if (! in_arrayi('curl', $php_modules) ) {
			$missing_modules[] = 'curl';
			$email_not_curl = true;
		}	else {
			$email_not_curl = false;
		}
		if (! in_arrayi('gd', $php_modules) ) {
			$missing_modules[] = 'GD';
		}
		if (! in_arrayi('zip', $php_modules) ) {
			$missing_modules[] = 'ZipArchive';
		}
		if (! in_arrayi('SimpleXML', $php_modules) ) {
			$missing_modules[] = 'SimpleXML';
		} 
		if ( function_exists('apache_get_modules') ) {
			if(! in_arrayi('mod_rewrite',apache_get_modules())) {
				$missing_modules[] = 'mod_rewrite';
			}
		}
		
		$preview_data = @new SimpleXMLExtended('<data></data>');
		$preview_data->addChild('submission_date', date('c'));
		$preview_data->addChild('getsimple_version', get_site_version(false));
		$preview_data->addChild('language', $LANG);
		$preview_data->addChild('timezone', $TIMEZONE);
		$preview_data->addChild('php_version', PHP_VERSION);
		$preview_data->addChild('server_type', PHP_OS);
		$preview_data->addChild('modules_missing', json_encode($missing_modules));
		$preview_data->addChild('number_pages', folder_items(GSDATAPAGESPATH)-1);
		$preview_data->addChild('number_plugins', count($live_plugins));
		$preview_data->addChild('number_files', count(glob_recursive(GSDATAUPLOADPATH.'*')));
		$preview_data->addChild('number_themes', folder_items(GSTHEMESPATH));
		$preview_data->addChild('number_backups', count(getFiles(GSBACKUPSPATH.'zip')));
		$preview_data->addChild('number_users', folder_items(GSUSERSPATH)-1);
		$preview_data->addChild('domain_tld', get_tld_from_url($SITEURL));
		$preview_data->addChild('install_date', date('m-d-Y', filemtime(GSDATAOTHERPATH .'.htaccess')));
		$preview_data->addChild('category', $_POST['category']);
		$preview_data->addChild('link_back', $_POST['link_back']);
		XMLsave($preview_data, GSDATAOTHERPATH . 'anonymous_data.xml');
	}
	
	# post data to server
	if(isset($_POST['send'])) {
		$xml = file_get_contents(GSDATAOTHERPATH . 'anonymous_data.xml');
		$success = 'We successfully received your data. Thank you for helping the GetSimple project.';
		
		$php_modules = get_loaded_extensions();
		if (in_arrayi('curl', $php_modules)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, 4);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, 'http://get-simple.info/api/anonymous/?data='.urlencode($xml));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
			$result = curl_exec($ch);
			curl_close($ch);
		} else {
			sendmail('chris@get-simple.info','Anonymous Data Submission',$xml);
		}
		
	}
	?>
	<style>
		form#anondata p {margin-bottom:5px;}
		form#anondata label {display:block;width:220px;float:left;line-height:35px;}
		form#anondata select.text {width:auto;float:left;}
	</style>
	<h3>Send Anonymous Website Data</h3>
	
	<?php 
	if($success) { 
		echo '<p style="color:#669933;"><b>'. $success .'</b></p>';
	} 
	if($error) { 
		echo '<p style="color:#cc0000;"><b>'. $error .'</b></p>';
	}
	?>
	
	<form method="post" id="anondata" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		
		<?php if($preview_data) { ?>
			<p>This is the exact data that we are going to send back to our servers for processing. If agree with the data below, please click the '<strong>Send Data</strong>' button. Thank you again for participating.</p>
			<pre><code><?php echo htmlentities(formatXmlString(file_get_contents(GSDATAOTHERPATH . 'anonymous_data.xml')));?></code></pre>
			<p class="submit"><br /><input type="submit" class="submit" value="Send Data" name="send" /></p>		
		<?php } else { ?> 
			<p>
				GetSimple needs to find out what type of people and businesses are using our product. This form allows you to voluntarily send us <u>completely
				anonymous data</u> about your website. This data includes things like the type of server you're on, your PHP version and how many pages and files you currently have.
				We would be very grateful if you could take a few minutes to submit this data to us as it is essential for the continued growth of 
				our CMS product.
			</p>
			<p>Complete the optional fields below, then click the '<strong>Preview Data Submission</strong>' button to preview your data before it's sent to our servers. </p>
			<p class="clearfix" ><label>Website Category:</label>
					<select name="category" class="text">
						<option value=""></option>
						<option value="Arts">Arts</option>
						<option value="Business">Business</option>
						<option value="Children">Children</option>
						<option value="Computer &amp; Internet">Computer &amp; Internet</option>
						<option value="Culture &amp; Religion">Culture &amp; Religion</option>
						<option value="Education">Education</option>
						<option value="Employment">Employment</option>
						<option value="Entertainment">Entertainment</option>
						<option value="Money &amp; Finance">Money &amp; Finance</option>
						<option value="Food">Food</option>
						<option value="Games">Games</option>
						<option value="Government">Government</option>
						<option value="Health &amp; Fitness">Health &amp; Fitness</option>
						<option value="HighTech">HighTech</option>
						<option value="Hobbies &amp; Interests">Hobbies &amp; Interests</option>
						<option value="Law">Law</option>
						<option value="Life Family Issues">Life Family Issues</option>
						<option value="Marketing">Marketing</option>
						<option value="Media">Media</option>
						<option value="Misc">Misc</option>
						<option value="Movies &amp; Television">Movies &amp; Television</option>
						<option value="Music &amp; Radio">Music &amp; Radio</option>
						<option value="Nature">Nature</option>
						<option value="Non-Profit">Non-Profit</option>
						<option value="Personal Homepages">Personal Homepages</option>
						<option value="Pets">Pets</option>
						<option value="Home &amp; Garden">Home &amp; Garden</option>
						<option value="Real Estate">Real Estate</option>
						<option value="Science &amp; Technology">Science &amp; Technology</option>
						<option value="Shopping &amp; Services">Shopping &amp; Services</option>
						<option value="Society">Society</option>
						<option value="Sports">Sports</option>
						<option value="Tourism">Tourism</option>
						<option value="Transportation">Transportation</option>
						<option value="Travel">Travel</option>
						<option value="X-rated">X-rated</option>
					</select>
			</p>
			<p class="clearfix" ><label>Do you link back to GetSimple?</label><select class="text" name="link_back"><option></option><option value="yes" >Yes</option><option value="no" >No</option></select></p>
			<p style="color:#cc0000;font-size:11px;" >* Please only submit this data once per website AND AFTER the site has been completed. Thank you.</p>
			<p class="submit"><br /><input type="submit" class="submit" value="Preview Data Submission" name="preview" /></p>
		<?php  } ?>
	</form>

	<?php

}
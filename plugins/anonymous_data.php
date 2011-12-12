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
	'GetSimple needs your anonymous data so we can understand our users better. It\'s completely anonymous and will help us out immensely.',
	'plugin',
	'gs_anonymousdata'
);

# activate hooks
add_action('plugins-sidebar','createSideMenu',array($thisfile,'Send Anonymous Data')); 

function gs_anonymousdata() {
	
	#grab data from this installation
	if((isset($_POST['preview']))) {
		global $LANG, $TIMEZONE;
		include(GSADMININCPATH ."configuration.php");
		$preview_data = @new SimpleXMLExtended('<data></data>');
		$preview_data->addChild('submission_date', date('c'));
		$preview_data->addChild('getsimple_version', $site_version_no);
		$preview_data->addChild('language', $LANG);
		$preview_data->addChild('timezone', $TIMEZONE);
		$preview_data->addChild('php_version', PHP_VERSION);
		$preview_data->addChild('server_type', $server_type);
		$preview_data->addChild('modules_missing', $missing_modules);
		$preview_data->addChild('number_pages', $count_pages);
		$preview_data->addChild('number_plugins', $count_plugins);
		$preview_data->addChild('number_files', $count_files);
		$preview_data->addChild('number_backups', $count_backups);
		$preview_data->addChild('number_users', $count_users);
		$preview_data->addChild('domain_tld', $domain_tld);
		$preview_data->addChild('original_install_date', $est_install_date);
		$preview_data->addChild('category', $_POST['category']);
		$preview_data->addChild('link_back', $_POST['link_back']);
		XMLsave($preview_data, GSDATAOTHERPATH . 'anonymous_data.xml');
	}
	
	# post data to server
	if((isset($_POST['send']))) {
		$xml = file_get_contents(GSDATAOTHERPATH . 'anonymous_data.xml');
		$success = 'Successfully sent your data. Thank you for helping the GetSimple project.';
		header("Location:". $_SERVER ['REQUEST_URI']."&success=".urlencode($success));
	}
	?>
	<style>
		form#anondata p {margin-bottom:5px;}
		form#anondata label {display:block;width:220px;float:left;line-height:35px;}
		form#anondata select.text {width:auto;float:left;}
	</style>
	<h3>Send Anonymous Website Data</h3>
	
	<form method="post" id="anondata" action="<?php	echo $_SERVER ['REQUEST_URI']?>">
		
		<?php if($preview_data) { ?>
			<p>This is the exact data that we are going to send back to our servers for processing. If agree with the data below, please click the '<strong>Send Data</strong>' button. Thank you.</p>
			<pre><code><?php echo htmlentities(formatXmlString(file_get_contents(GSDATAOTHERPATH . 'anonymous_data.xml')));?></code></pre>
			<p class="submit"><br /><input type="submit" class="submit" value="Send Data" name="send" /></p>		
		<?php } else { ?> 
			<p>
				GetSimple needs to find out what type of people and businesses are using our product. This form allows you to voluntarily send us <u>completely
				anonymous data</u> about your website. This includes things like the type of server and PHP your hosted with and how many pages and files you have.
				We are very grateful if you could take a few minutes to submit this data to us as it is essential for the continued growth of 
				our CMS product.
			</p>
			<p>Fill as many of the optional fields below as you want, then click the '<strong>Preview Data Submission</strong>' button to preview the data before it's sent to our servers. </p>
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
			<p class="clearfix" ><label>Do you link back to GetSimple?</label><select class="text" name="link_back"><option value=""></option><option value="yes" >Yes</option><option value="no" >No</option></select></p>
			<p style="color:#cc0000;font-size:11px;" >* Please only submit this form once per website - after it has been fully setup and completed. Thank you.</p>
			<p class="submit"><br /><input type="submit" class="submit" value="Preview Data Submission" name="preview" /></p>
		<?php  } ?>
	</form>

	<?php
}
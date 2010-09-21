<?php 
/****************************************************
*
* @File: 		settings.php
* @Package:	GetSimple
* @Action:	Displays and changes website settings 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable settings
login_cookie_check();
$file			= 'user.xml';
$path 		= $relative. 'data/other/';
$bakpath 	= $relative. 'backups/other/';
$data 		= getXML($path . $file);
$USR 			= stripslashes($data->USR);
$PASSWD 	= $data->PWD;
$EMAIL 		= $data->EMAIL;
$err 			= '';

// if the undo command was invoked
if (isset($_GET['undo']))
{ 
	$nonce = $_GET['undo'];
	if(!check_nonce($nonce, "undo"))
		die("CSRF detected!");

	$ufile = 'user.xml';
	undo($ufile, $path, $bakpath);
	
	$ufile = 'website.xml';
	undo($ufile, $path, $bakpath);
	
	$ufile = 'cp_settings.xml';
	undo($ufile, $path, $bakpath);
	
	// Redirect
	header('Location: settings.php?restored=true');
}

if (isset($_GET['restored']))
{ 
	$restored = 'true'; 
} 
else 
{
	$restored = 'false';
}

// were changes submitted?
if(isset($_POST['submitted']))
{
	$nonce = $_POST['nonce'];
	if(!check_nonce($nonce, "save_settings"))
		die("CSRF detected!");	


	if(isset($_POST['sitename'])) { 
		$SITENAME = htmlentities($_POST['sitename'], ENT_QUOTES, 'UTF-8'); 
	}
	
	if(isset($_POST['siteurl'])) { 
		$SITEURL = tsl($_POST['siteurl']); 
	}
	
	if(isset($_POST['user'])) { 
		$USR = $_POST['user']; 
	} 
	
	if(isset($_POST['email'])) { 
		$EMAIL = $_POST['email']; 
	} 
	
	if(isset($_POST['template'])) { 
		$TEMPLATE = $_POST['template']; 
	}
	
	if(isset($_POST['timezone'])) { 
		$TIMEZONE = $_POST['timezone']; 
	}
	
	if(isset($_POST['lang'])) { 
		$LANG = $_POST['lang']; 
	}
	
	if(isset($_POST['permalink'])) { 
		$PERMALINK = $_POST['permalink']; 
	}

	$HTMLEDITOR = @$_POST['show_htmleditor']; 
	$PRETTYURLS = @$_POST['prettyurls']; 
	
	// Update passwords
	$pwd1 = '';
	$pwd2 = '';
	
	if(isset($_POST['sitepwd'])) { $pwd1 = $_POST['sitepwd']; }
	if(isset($_POST['sitepwd_confirm'])) { $pwd2 = $_POST['sitepwd_confirm']; }
	
	// are we resetting the password?
	if ($pwd1 != $pwd2)
	{ 
		$err = "true";
		$msg = $i18n['PASSWORD_NO_MATCH'];
	} 
	else 
	{
		
		// password cannot be null
		if ( $pwd1 != '' ) { 
			$PASSWD = passhash($pwd1); 
		}	
		
		// create new user data file
		$ufile = 'user.xml';
		createBak($ufile, $path, $bakpath);
		if (file_exists($bakpath . 'user.xml.reset')) { unlink($bakpath . 'user.xml.reset'); }	

		$xml = @new SimpleXMLElement('<item></item>');
		$xml->addChild('USR', @$USR);
		$xml->addChild('PWD', @$PASSWD);
		$xml->addChild('EMAIL', @$EMAIL);
		exec_action('settings-user');
		XMLsave($xml, $path . $ufile);
		
		// create new site data file
		$ufile = 'website.xml';
		createBak($ufile, $path, $bakpath);
		$xmls = @new SimpleXMLExtended('<item></item>');
		$note = $xmls->addChild('SITENAME');
		$note->addCData($SITENAME);
		$note = $xmls->addChild('SITEURL');
		$note->addCData(@$SITEURL);
		$note = $xmls->addChild('TEMPLATE');
		$note->addCData(@$TEMPLATE);
		$note = $xmls->addChild('TIMEZONE');
		$note->addCData(@$TIMEZONE);
		$note = $xmls->addChild('LANG');
		$note->addCData(@$LANG);
		exec_action('settings-website');
		XMLsave($xmls, $path . $ufile);
		
		//see new language file immediately
		include('lang/'.$LANG.'.php');

		// create new cpsettings data file
		$ufile = 'cp_settings.xml';
		createBak($ufile, $path, $bakpath);
		$xmlc = @new SimpleXMLElement('<item></item>');
		$xmlc->addChild('HTMLEDITOR', @$HTMLEDITOR);
		$xmlc->addChild('PRETTYURLS', @$PRETTYURLS);
		$xmlc->addChild('PERMALINK', @$PERMALINK);
		exec_action('settings-cpsettings');
		XMLsave($xmlc, $path . $ufile);
		
		$err = "false";
	}
}

//are any of the control panel checkboxes checked?
$editorchck = ''; $prettychck = '';

if ($HTMLEDITOR != '' ) { $editorchck = 'checked'; }
if ($PRETTYURLS != '' ) { $prettychck = 'checked'; }

// get what we think the 'website base url' should be
$path_parts = pathinfo(htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES));
$path_parts = str_replace("/admin", "", $path_parts['dirname']);
$fullpath = tsl("http://". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $path_parts);

// get available language files
$lang_path = "lang/";
$lang_handle = @opendir($lang_path) or die("Unable to open $lang_path");
if ($LANG == ''){ $LANG = 'en_US'; }

while ($lfile = readdir($lang_handle))
{
	if( is_file($lang_path . $lfile) && $lfile != "." && $lfile != ".." )
	{
		$lang_array[] = basename($lfile, ".php");
	}
}

if (count($lang_array) != 0)
{
	sort($lang_array);
	$count	= '0'; 
	$sel 	= ''; 
	$langs 	= '';
	
	foreach ($lang_array as $larray)
	{
		if ($LANG == $larray)
		{ 
			$sel="selected";
		}
		
		$langs .= '<option '.@$sel.' value="'.$larray.'" >'.$larray.'</option>';
		$sel = '';
		$count++;
	}
} 
else 
{
	$langs = '<option value="" selected="selected" >-- '.$i18n['NONE'].' --</option>';
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['GENERAL_SETTINGS']); ?>
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['GENERAL_SETTINGS'];?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
<div class="bodycontent">
	
	<div id="maincontent">
		<form class="largeform" action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_settings"); ?>" />
		<div class="main">
		<h3><?php echo $i18n['WEBSITE_SETTINGS'];?></h3>

		<p><b><?php echo $i18n['LABEL_WEBSITE'];?>:</b><br /><input class="text" name="sitename" type="text" value="<?php if(isset($SITENAME1)) { echo stripslashes($SITENAME1); } else { echo stripslashes($SITENAME); } ?>" /></p>

		<p><b><?php echo $i18n['LABEL_BASEURL'];?>:</b><br /><input class="text" name="siteurl" type="text" value="<?php if(isset($SITEURL1)) { echo $SITEURL1; } else { echo $SITEURL; } ?>" /></p>
		<?php	if ( $fullpath != $SITEURL ) {	echo '<p style="margin:-15px 0 20px 0;color:#D94136;font-size:11px;" >'.$i18n['LABEL_SUGGESTION'].': &nbsp; <code>'.$fullpath.'</code></p>';	}	?>
		
		<p><b><?php echo $i18n['LOCAL_TIMEZONE'];?>:</b><br />
		<? if( (isset($_POST['timezone'])) ) { $TIMEZONE = $_POST['timezone']; } ?>
		<select class="text" name="timezone"> 
		<?php if ($TIMEZONE == '') { echo '<option value="" selected="selected" >-- '.$i18n['NONE'].' --</option>'; } else { echo '<option selected="selected"  value="'. $TIMEZONE .'">'. $TIMEZONE .'</option>'; } ?>
		<option value="Kwajalein">(GMT-12:00) International Date Line West</option><option value="Pacific/Samoa">(GMT-11:00) Midway Island, Samoa</option><option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option><option value="America/Anchorage">(GMT-09:00) Alaska</option><option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US &amp; Canada)</option><option value="America/Tijuana">(GMT-08:00) Tijuana, Baja California</option><option value="America/Denver">(GMT-07:00) Mountain Time (US &amp; Canada)</option><option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option><option value="America/Phoenix">(GMT-07:00) Arizona</option><option value="America/Regina">(GMT-06:00) Saskatchewan</option><option value="America/Tegucigalpa">(GMT-06:00) Central America</option><option value="America/Chicago">(GMT-06:00) Central Time (US &amp; Canada)</option><option value="America/Mexico_City">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option><option value="America/New_York">(GMT-05:00) Eastern Time (US &amp; Canada)</option><option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option><option value="America/Indiana/Indianapolis">(GMT-05:00) Indiana (East)</option><option value="America/Caracas">(GMT-04:30) Caracas</option><option value="America/Halifax">(GMT-04:00) Atlantic Time (Canada)</option><option value="America/Manaus">(GMT-04:00) Manaus</option><option value="America/Santiago">(GMT-04:00) Santiago</option><option value="America/La_Paz">(GMT-04:00) La Paz</option><option value="America/St_Johns">(GMT-03:30) Newfoundland</option><option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option><option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option><option value="America/Godthab">(GMT-03:00) Greenland</option><option value="America/Montevideo">(GMT-03:00) Montevideo</option><option value="America/Argentina/Buenos_Aires">(GMT-03:00) Georgetown</option><option value="Atlantic/South_Georgia">(GMT-02:00) Mid-Atlantic</option><option value="Atlantic/Azores">(GMT-01:00) Azores</option><option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option><option value="Europe/London">(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option><option value="Atlantic/Reykjavik">(GMT) Monrovia, Reykjavik</option><option value="Africa/Casablanca">(GMT) Casablanca</option><option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option><option value="Europe/Sarajevo">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option><option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option><option value="Africa/Algiers">(GMT+01:00) West Central Africa</option><option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option><option value="Europe/Minsk">(GMT+02:00) Minsk</option><option value="Africa/Cairo">(GMT+02:00) Cairo</option><option value="Europe/Helsinki">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option><option value="Europe/Athens">(GMT+02:00) Athens, Bucharest, Istanbul</option><option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option><option value="Asia/Amman">(GMT+02:00) Amman</option><option value="Asia/Beirut">(GMT+02:00) Beirut</option><option value="Africa/Windhoek">(GMT+02:00) Windhoek</option><option value="Africa/Harare">(GMT+02:00) Harare, Pretoria</option><option value="Asia/Kuwait">(GMT+03:00) Kuwait, Riyadh</option><option value="Asia/Baghdad">(GMT+03:00) Baghdad</option><option value="Africa/Nairobi">(GMT+03:00) Nairobi</option><option value="Asia/Tbilisi">(GMT+03:00) Tbilisi</option><option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option><option value="Asia/Tehran">(GMT+03:30) Tehran</option><option value="Asia/Muscat">(GMT+04:00) Abu Dhabi, Muscat</option><option value="Asia/Baku">(GMT+04:00) Baku</option><option value="Asia/Yerevan">(GMT+04:00) Yerevan</option><option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option><option value="Asia/Karachi">(GMT+05:00) Islamabad, Karachi</option><option value="Asia/Tashkent">(GMT+05:00) Tashkent</option><option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option><option value="Asia/Colombo">(GMT+05:30) Sri Jayawardenepura</option><option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option><option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option><option value="Asia/Novosibirsk">(GMT+06:00) Almaty, Novosibirsk</option><option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option><option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option><option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option><option value="Asia/Beijing">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option><option value="Asia/Ulaanbaatar">(GMT+08:00) Irkutsk, Ulaan Bataar</option><option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur, Singapore</option><option value="Asia/Taipei">(GMT+08:00) Taipei</option><option value="Australia/Perth">(GMT+08:00) Perth</option><option value="Asia/Seoul">(GMT+09:00) Seoul</option><option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option><option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option><option value="Australia/Darwin">(GMT+09:30) Darwin</option><option value="Australia/Adelaide">(GMT+09:30) Adelaide</option><option value="Australia/Sydney">(GMT+10:00) Canberra, Melbourne, Sydney</option><option value="Australia/Brisbane">(GMT+10:00) Brisbane</option><option value="Australia/Hobart">(GMT+10:00) Hobart</option><option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option><option value="Pacific/Guam">(GMT+10:00) Guam, Port Moresby</option><option value="Asia/Magadan">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option><option value="Pacific/Fiji">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option><option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option><option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option> </select>
		</p>
		
		<p><b><?php echo $i18n['LANGUAGE'];?>:</b><br />
		<select name="lang" class="text">
			<?php echo $langs; ?>
		</select> &nbsp;<a href="http://get-simple.info/download/languages/" style="font-size:11px;" ><?php echo $i18n['MORE'];?></a>
		</p>
		
		<p><b><?php echo $i18n['PERMALINK'];?>:</b><br /><input class="text" name="permalink" type="text" value="<?php if(isset($PERMALINK)) { echo $PERMALINK; } ?>" /> &nbsp;<a href="http://get-simple.info/docs/permalinks/" style="font-size:11px;" ><?php echo $i18n['HELP'];?></a></p>

		
		<p><input name="prettyurls" id="prettyurls" type="checkbox" value="1" <?php echo $prettychck; ?>  /> &nbsp;<label class="clean" for="prettyurls" ><?php echo $i18n['USE_FANCY_URLS'];?>.</label><br />
		<input name="show_htmleditor" id="show_htmleditor" type="checkbox" value="1" <?php echo $editorchck; ?> /> &nbsp;<label class="clean" for="show_htmleditor" ><?php echo $i18n['ENABLE_HTML_ED'];?></label></p>
		
		<?php exec_action('settings-website-extras'); ?>
		
		<p><input class="submit" type="submit" name="submitted" value="<?php echo $i18n['BTN_SAVESETTINGS'];?>" /></p>
		
		</div>
		
		<div id="profile" class="main">
		<h3><?php echo $i18n['USER_SETTINGS'];?></h3>
		<p><b><?php echo $i18n['LABEL_USERNAME'];?>:</b><br /><input class="text" name="user" type="text" value="<?php if(isset($USR1)) { echo $USR1; } else { echo $USR; } ?>" /></p>
		<p><b><?php echo $i18n['LABEL_EMAIL'];?>:</b><br /><input class="text" name="email" type="text" value="<?php if(isset($EMAIL1)) { echo $EMAIL1; } else { echo $EMAIL; } ?>" /></p>
		<?php if (! check_email_address($EMAIL)) {
			echo '<p style="margin:-15px 0 20px 0;color:#D94136;font-size:11px;" >'.$i18n['WARN_EMAILINVALID'].'</p>';
		}?>
		<p style="margin:20px 0 5px 0;font-size:12px;color:#999;" ><?php echo $i18n['ONLY_NEW_PASSWORD'];?>:</p>
		<p><b><?php echo $i18n['NEW_PASSWORD'];?>:</b><br /><input autocomplete="off" class="text" name="sitepwd" type="password" value="" /></p>
		<p><b><?php echo $i18n['CONFIRM_PASSWORD'];?>:</b><br /><input autocomplete="off" class="text" name="sitepwd_confirm" type="password" value="" /></p>
		<?php exec_action('settings-user-extras'); ?>
		<p><input class="submit" type="submit" name="submitted" value="<?php echo $i18n['BTN_SAVESETTINGS'];?>" /></p>
	</form>
	</div>
	</div>



	
	<div id="sidebar" >
		<?php include('template/sidebar-settings.php'); ?>		
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
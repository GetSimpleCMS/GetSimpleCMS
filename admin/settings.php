<?php 
/**
 * Settings
 *
 * Displays and changes website settings 
 *
 * @package GetSimple
 * @subpackage Settings
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// Variable settings
login_cookie_check();
$file			= 'user.xml';
$path 		= GSDATAOTHERPATH;
$bakpath 	= GSBACKUPSPATH.'other/';
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
	redirect('settings.php?restored=true');
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
		$msg = i18n_r('PASSWORD_NO_MATCH');
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

$fullpath = suggest_site_path();

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
	$langs = '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>';
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('GENERAL_SETTINGS')); ?>
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('GENERAL_SETTINGS');?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
<div class="bodycontent">
	
	<div id="maincontent">
		<form class="largeform" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
		<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("save_settings"); ?>" />
		<div class="main">
		<h3><?php i18n('WEBSITE_SETTINGS');?></h3>

		<p><b><?php i18n('LABEL_WEBSITE');?>:</b><br /><input class="text" name="sitename" type="text" value="<?php if(isset($SITENAME1)) { echo stripslashes($SITENAME1); } else { echo stripslashes($SITENAME); } ?>" /></p>

		<p><b><?php i18n('LABEL_BASEURL');?>:</b><br /><input class="text" name="siteurl" type="text" value="<?php if(isset($SITEURL1)) { echo $SITEURL1; } else { echo $SITEURL; } ?>" /></p>
		<?php	if ( $fullpath != $SITEURL ) {	echo '<p style="margin:-15px 0 20px 0;color:#D94136;font-size:11px;" >'.i18n_r('LABEL_SUGGESTION').': &nbsp; <code>'.$fullpath.'</code></p>';	}	?>
		
		<p><b><?php i18n('LOCAL_TIMEZONE');?>:</b><br />
		<? if( (isset($_POST['timezone'])) ) { $TIMEZONE = $_POST['timezone']; } ?>
		<select class="text" name="timezone"> 
		<?php if ($TIMEZONE == '') { echo '<option value="" selected="selected" >-- '.i18n_r('NONE').' --</option>'; } else { echo '<option selected="selected"  value="'. $TIMEZONE .'">'. $TIMEZONE .'</option>'; } ?>
		<?php include('inc/timezone_options.txt'); ?>
		</select>
		</p>
		
		<p><b><?php i18n('LANGUAGE');?>:</b><br />
		<select name="lang" class="text">
			<?php echo $langs; ?>
		</select> &nbsp;<a href="http://get-simple.info/download/languages/" style="font-size:11px;" ><?php i18n('MORE');?></a>
		</p>
		
		<p><b><?php i18n('PERMALINK');?>:</b><br /><input class="text" name="permalink" type="text" value="<?php if(isset($PERMALINK)) { echo $PERMALINK; } ?>" /> &nbsp;<a href="http://get-simple.info/docs/permalinks/" style="font-size:11px;" ><?php i18n('HELP');?></a></p>

		
		<p><input name="prettyurls" id="prettyurls" type="checkbox" value="1" <?php echo $prettychck; ?>  /> &nbsp;<label class="clean" for="prettyurls" ><?php i18n('USE_FANCY_URLS');?>.</label><br />
		<input name="show_htmleditor" id="show_htmleditor" type="checkbox" value="1" <?php echo $editorchck; ?> /> &nbsp;<label class="clean" for="show_htmleditor" ><?php i18n('ENABLE_HTML_ED');?></label></p>
		
		<?php exec_action('settings-website-extras'); ?>
		
		<p><input class="submit" type="submit" name="submitted" value="<?php i18n('BTN_SAVESETTINGS');?>" /></p>
		
		</div>
		
		<div id="profile" class="main">
		<h3><?php i18n('USER_SETTINGS');?></h3>
		<p><b><?php i18n('LABEL_USERNAME');?>:</b><br /><input class="text" name="user" type="text" value="<?php if(isset($USR1)) { echo $USR1; } else { echo $USR; } ?>" /></p>
		<p><b><?php i18n('LABEL_EMAIL');?>:</b><br /><input class="text" name="email" type="text" value="<?php if(isset($EMAIL1)) { echo $EMAIL1; } else { echo $EMAIL; } ?>" /></p>
		<?php if (! check_email_address($EMAIL)) {
			echo '<p style="margin:-15px 0 20px 0;color:#D94136;font-size:11px;" >'.i18n_r('WARN_EMAILINVALID').'</p>';
		}?>
		<p style="margin:20px 0 5px 0;font-size:12px;color:#999;" ><?php i18n('ONLY_NEW_PASSWORD');?>:</p>
		<p><b><?php i18n('NEW_PASSWORD');?>:</b><br /><input autocomplete="off" class="text" name="sitepwd" type="password" value="" /></p>
		<p><b><?php i18n('CONFIRM_PASSWORD');?>:</b><br /><input autocomplete="off" class="text" name="sitepwd_confirm" type="password" value="" /></p>
		<?php exec_action('settings-user-extras'); ?>
		<p><input class="submit" type="submit" name="submitted" value="<?php i18n('BTN_SAVESETTINGS');?>" /></p>
	</form>
	</div>
	</div>



	
	<div id="sidebar" >
		<?php include('template/sidebar-settings.php'); ?>		
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
<?php 
/**
 * Update
 *
 * Provides any updating to the system the first time it is run
 *
 * @package GetSimple
 * @subpackage Init
 */


function check_php_requirements(){
	$kill = false;
	$php_required_exts = array('xml','simplexml','dom','json');

	$php_modules = get_loaded_extensions();
	$php_modules = array_map('strtolower', $php_modules);
	foreach($php_required_exts as $ext){
		if(!in_array($ext, $php_modules )){
			echo("PHP $ext extension NOT INSTALLED<br/>\n");
			$kill = 1;
		}
	}
	if($kill) die('Getsimple Install Cannot Continue');
}
check_php_requirements();


$load['plugin'] = true;
include('inc/common.php');

/* delete caches */
delete_cache();

/* 
 * Updates below here 
 */

$message = null;

$create_dirs = array(
	GSCACHEPATH,
	GSAUTOSAVEPATH,
	GSBACKUPSPATH, 
	GSBACKUPSPATH . getRelPath(GSDATAOTHERPATH,GSDATAPATH), // backups/other/
	GSBACKUPSPATH . getRelPath(GSDATAPAGESPATH,GSDATAPATH), // backups/pages/
	GSBACKUSERSPATH,
	GSBACKUPSPATH .'zip/'	
);

// files to be created
$create_files = array();

// deprecatd files to be removed
$delete_files = array(
	GSADMININCPATH.'xss.php',
	GSADMININCPATH.'nonce.php',
	GSADMININCPATH.'install.php',
	GSADMINPATH.'load-ajax.php',
	GSADMINPATH.'cron.php',
	GSADMINPATH.'loadtab.php',
	GSADMINPATH.'upload-uploadify.php',
	GSADMINPATH.'uploadify-check-exists.php'
);


function msgOK($msg){
	return '<div class="notify">'.$msg.'</div>';
}

function msgError($msg){
	return '<div class="notify notify_error">'.$msg.'</div>';
}

# create default 404.xml page
$init = GSDATAOTHERPATH.GSHTTPPREFIX.'404.xml';
$temp = GSADMININCPATH.'tmp/tmp-404.xml'; 
if (! file_exists($init)) {
	if(copy_file($temp,$init)) $message.= msgOK(sprintf(i18n_r('COPY_SUCCESS'),'tmp/404.xml'));
	else $message.= msgError(sprintf(i18n_r('COPY_FAILURE'),'tmp/404.xml'));
}

# create default 403.xml page
$init = GSDATAOTHERPATH.GSHTTPPREFIX.'403.xml';
$temp = GSADMININCPATH.'tmp/tmp-403.xml'; 
if (! file_exists($init)) {
	if(copy_file($temp,$init)) $message.= msgOK(sprintf(i18n_r('COPY_SUCCESS'),'tmp/403.xml'));
	else $message.= msgError(sprintf(i18n_r('COPY_FAILURE'),'tmp/403.xml'));
}

/* create new folders */
foreach($create_dirs as $dir){
	if (!file_exists($dir)) {
		$status = create_dir($dir);
		if($status) $message.= msgOK(sprintf(i18n_r('FOLDER_CREATED'),$dir));
		else $error.= msgError(i18n_r('ERROR_CREATING_FOLDER') . "<br /> - $dir");
	}
}

# remove the pages.php plugin if it exists.
if (file_exists(GSPLUGINPATH.'pages.php'))	{
	delete_file(GSPLUGINPATH.'pages.php');
}

/* check for legacy version of user.xml */
/* check and perform 2.x - 3.x upgrade */
if (file_exists(GSDATAOTHERPATH .'user.xml')) {
	
	# make new users folder
	if (!file_exists(GSUSERSPATH)) {
		$status = create_dir(GSUSERSPATH);
		if (!$status) { 
			$error .= msgError('Unable to create the folder /data/users/');	
		} else {
			$message .= msgOK('Created the folder /data/users/');
		}
	}

	# make new backup users folder
	if (!file_exists(GSBACKUSERSPATH)) {
		$status = create_dir(GSBACKUSERSPATH);
		if (!$status) {
			$error .= msgError('Unable to create the folder /backup/users/');	
		} else {
			$message .=  msgOK('Created the folder /backup/users/');
		}
	}

	# get $USR data
	$datau      = getXML(GSDATAOTHERPATH .'user.xml');
	$datac      = getXML(GSDATAOTHERPATH .'cp_settings.xml');
	$dataw      = getXML(GSDATAOTHERPATH .GSWEBSITEFILE);
	
	$USR        = _id(stripslashes($datau->USR));
	$EMAIL      = $datau->EMAIL;
	$PASSWD     = $datau->PWD;
	$HTMLEDITOR = $datac->HTMLEDITOR;
	$PRETTYURLS = $datac->PRETTYURLS;
	$PERMALINK  = $datac->PERMALINK;
	$TIMEZONE   = $datac->TIMEZONE;
	$LANG       = $datac->LANG;
	$SITENAME   = stripslashes($dataw->SITENAME);
	$SITEURL    = $dataw->SITEURL;
	$TEMPLATE   = $dataw->TEMPLATE;
	
	
	# creating new user file
	$xml = new SimpleXMLElement('<item></item>');
	$xml->addChild('USR', $USR);
	$xml->addChild('PWD', $PASSWD);
	$xml->addChild('EMAIL', $EMAIL);
	$xml->addChild('HTMLEDITOR', $HTMLEDITOR);
	$xml->addChild('TIMEZONE', $TIMEZONE);
	$xml->addChild('LANG', $LANG);
	$status = XMLsave($xml, GSUSERSPATH . _id($USR) .'.xml');	
	gs_chmod(GSUSERSPATH . _id($USR) .'.xml');
	if (!$status) {
		$error .= msgError('Unable to create new  '._id($USR).'.xml file!');	
	} else {
		$message .= msgOK('Created new '._id($USR).'.xml file');
	}
	
	# rename old wesbite.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_website.xml')) {
		$status = rename_file(GSDATAOTHERPATH .'website.xml', GSDATAOTHERPATH .'_legacy_website.xml');
		if (!$status) {
			$error .= msgError('Unable to rename website.xml to _legacy_website.xml');	
		} else {
			$message .= msgOK('Renamed website.xml to _legacy_website.xml');
		}
	}
	
	#creating new website file
	$xml = new SimpleXMLElement('<item></item>');
	$xml->addChild('SITENAME', $SITENAME);
	$xml->addChild('SITEURL', $SITEURL);
	$xml->addChild('TEMPLATE', $TEMPLATE);
	$xml->addChild('PRETTYURLS', $PRETTYURLS);
	$xml->addChild('PERMALINK', $PERMALINK);
	$status = XMLsave($xml, GSDATAOTHERPATH .GSWEBSITEFILE);	
	if (!$status) {
		$error .= msgError('Unable to update '.GSWEBSITEFILE.' file!');	
	} else {
		$message .= msgOK('Created updated '.GSWEBSITEFILE.' file');
	}
	
	
	# rename old user.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_user.xml')) {
		$status = rename_file(GSDATAOTHERPATH .'user.xml', GSDATAOTHERPATH .'_legacy_user.xml');
		if (!$status) {
			$error .= msgError('Unable to rename user.xml to _legacy_user.xml');	
		} else {
			$message .= msgOK('Renamed user.xml to _legacy_user.xml');
		}
	}

	# rename old cp_settings.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_cp_settings.xml')) {
		$status = rename_file(GSDATAOTHERPATH .'cp_settings.xml', GSDATAOTHERPATH .'_legacy_cp_settings.xml');
		if (!$status) {
			$error .= msgError('Unable to rename cp_settings.xml to _legacy_cp_settings.xml');	
		} else {
			$message .= msgOK('Renamed cp_settings.xml to _legacy_cp_settings.xml');
		}
	}
	/* end update */
} 

// 3.4.0
// update new permalink setting, if permalink, enable pretty urls toggle
$dataw       = getXML(GSDATAOTHERPATH .GSWEBSITEFILE);
$permalink   = trim((string) $dataw->PERMALINK);
$fileversion = trim((string) $dataw->GSVERSION);
if($fileversion!='3.4.0'){
	if(!empty($permalink)) $dataw->editAddChild('PRETTYURLS', '1');
	$dataw->editAddChild('GSVERSION', '3.4.0');
}
if (!XMLsave($dataw, GSDATAOTHERPATH . GSWEBSITEFILE) ) {
	$error .= i18n_r('CHMOD_ERROR');
}

// redirect to health check or login and show updated notice
$redirect = cookie_check() ? "health-check.php?updated=1" : "index.php?updated=1";

// If no errors or messages, then we did nothing, just continue automatically
if(!isset($error) && !isset($message)) redirect($redirect);

// we already showed a notice, pass updated so it gets deleted, no indication, 
$redirect = cookie_check() ? "health-check.php?updated=2" : "index.php?updated=2";

// show errors or messages
if(isset($error)) $message.= i18n_r('ER_REQ_PROC_FAIL');
else $message.= "<p><div class=\"notify notify_ok\">".i18n_r('SITE_UPDATED')."</div></p>";

$pagetitle = $site_full_name.' &middot; '. i18n_r('SYSTEM_UPDATE');
get_template('header');

?>
	
	<h1><?php echo $site_full_name; ?></h1>
</div> 
</div><!-- Closes header -->
<div class="wrapper">
	
	<div id="maincontent">
		<div class="main" >
			<h3><?php i18n('SYSTEM_UPDATE'); ?></h3>
			
			<?php 
				echo "$message";
				echo '<p><a href="'.$redirect.'">'.i18n_r('CONTINUE_SETUP').'</a></p>';
			?>
			
		</div>
	</div>
	<div class="clear"></div>
	<?php get_template('footer'); ?> 
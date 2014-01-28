<?php 
/**
 * Update
 *
 * Provides any updating to the system the first time it is run
 *
 * @package GetSimple
 * @subpackage Init
 */

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
	GSAUTOSAVEPATH
);

$create_files = array();

$delete_files = array(
	GSADMININCPATH.'xss.php',
	GSADMININCPATH.'nonce.php',
	GSADMININCPATH.'install.php',
	GSADMINPATH.'load-ajax.php',
	GSADMINPATH.'cron.php',
	GSADMINPATH.'loadtab.php'
);


function msgOK($msg){
	return '<div class="notify">'.$msg.'</div>';
}

function msgError($msg){
	return '<div class="notify notify_error">'.$msg.'</div>';
}

/* create new folders */
foreach($create_dirs as $dir){
	if (!file_exists($dir)) {  	
		if (defined('GSCHMOD')) { 
		 $chmod_value = GSCHMOD; 
		} else {
		 $chmod_value = 0755;
		}
		$status = mkdir($dir, $chmod_value);
		if($status) $message.= msgOK(sprintf(i18n_r('FOLDER_CREATED'),$dir));
		else $error.= msgError(i18n_r('ERROR_CREATING_FOLDER') . "<br /> - $dir");
	}
}


/* check for legacy version of user.xml */
if (file_exists(GSDATAOTHERPATH .'user.xml')) {
	
	
	# make new users folder
	if (!file_exists(GSUSERSPATH)) {
		$status = mkdir(GSUSERSPATH, 0777);
		chmod(GSUSERSPATH, 0777);
		if (!$status) { 
			$error .= msgError('Unable to create the folder /data/users/');	
		} else {
			$message .= msgOK('Created the folder /data/users/');
		}
	}

	# make new backup users folder
	if (!file_exists(GSBACKUSERSPATH)) {
		$status = mkdir(GSBACKUSERSPATH, 0777);
		chmod(GSBACKUSERSPATH, 0777);
		if (!$status) {
			$error .= msgError('Unable to create the folder /backup/users/');	
		} else {
			$message .=  msgOK('Created the folder /backup/users/');
		}
	}

	# get $USR data
	$datau = getXML(GSDATAOTHERPATH .'user.xml');
	$datac = getXML(GSDATAOTHERPATH .'cp_settings.xml');
	$dataw = getXML(GSDATAOTHERPATH .'website.xml');
	$USR = _id(stripslashes($datau->USR));
	$EMAIL = $datau->EMAIL;
	$PASSWD = $datau->PWD;
	$HTMLEDITOR = $datac->HTMLEDITOR;
	$PRETTYURLS = $datac->PRETTYURLS;
	$PERMALINK = $datac->PERMALINK;
	$TIMEZONE = $datac->TIMEZONE;
	$LANG = $datac->LANG;
	$SITENAME = stripslashes($dataw->SITENAME);
	$SITEURL = $dataw->SITEURL;
	$TEMPLATE = $dataw->TEMPLATE;
	
	
	# creating new user file
	$xml = new SimpleXMLElement('<item></item>');
	$xml->addChild('USR', $USR);
	$xml->addChild('PWD', $PASSWD);
	$xml->addChild('EMAIL', $EMAIL);
	$xml->addChild('HTMLEDITOR', $HTMLEDITOR);
	$xml->addChild('TIMEZONE', $TIMEZONE);
	$xml->addChild('LANG', $LANG);
	$status = XMLsave($xml, GSUSERSPATH . _id($USR) .'.xml');	
	chmod(GSUSERSPATH . _id($USR) .'.xml', 0777);
	if (!$status) {
		$error .= msgError('Unable to create new  '._id($USR).'.xml file!');	
	} else {
		$message .= msgOK('Created new '._id($USR).'.xml file');
	}
	
	
	# rename old wesbite.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_website.xml')) {
		$status = rename(GSDATAOTHERPATH .'website.xml', GSDATAOTHERPATH .'_legacy_website.xml');
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
	$status = XMLsave($xml, GSDATAOTHERPATH .'website.xml');	
	if (!$status) {
		$error .= msgError('Unable to update website.xml file!');	
	} else {
		$message .= msgOK('Created updated website.xml file');
	}
	
	
	# rename old user.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_user.xml')) {
		$status = rename(GSDATAOTHERPATH .'user.xml', GSDATAOTHERPATH .'_legacy_user.xml');
		if (!$status) {
			$error .= msgError('Unable to rename user.xml to _legacy_user.xml');	
		} else {
			$message .= msgOK('Renamed user.xml to _legacy_user.xml');
		}
	}

	# rename old cp_settings.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_cp_settings.xml')) {
		$status = rename(GSDATAOTHERPATH .'cp_settings.xml', GSDATAOTHERPATH .'_legacy_cp_settings.xml');
		if (!$status) {
			$error .= msgError('Unable to rename cp_settings.xml to _legacy_cp_settings.xml');	
		} else {
			$message .= msgOK('Renamed cp_settings.xml to _legacy_cp_settings.xml');
		}
	}
	/* end update */
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

get_template('header', $site_full_name.' &raquo; '. i18n_r('SYSTEM_UPDATE')); 

?>
	
	<h1><?php echo $site_full_name; ?></h1>
</div> 
</div><!-- Closes header -->
<div class="wrapper">
	<?php // include('template/error_checking.php'); ?>
	
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
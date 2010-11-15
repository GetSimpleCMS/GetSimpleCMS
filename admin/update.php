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


/* 
 * Updates below here 
 */
$error = '';
$message = null;

/* check for legacy version of user.xml */
if (file_exists(GSDATAOTHERPATH .'user.xml')) {
	
	
	# make two new users folder
	if (!file_exists(GSUSERSPATH)) {
		$status = mkdir(GSUSERSPATH, 0777);
		chmod(GSUSERSPATH, 0777);
		if (!$status) { 
			$error .= 'Unable to create the folder /data/users/<br />';	
		} else {
			$message .= '<li>Created the folder /data/users/</li>';
		}
	}

	# make two new backup users folder
	if (!file_exists(GSBACKUSERSPATH)) {
		$status = mkdir(GSBACKUSERSPATH, 0777);
		chmod(GSBACKUSERSPATH, 0777);
		if (!$status) {
			$error .= 'Unable to create the folder /backup/users/<br />';	
		} else {
			$message .= '<li>Created the folder /backup/users/</li>';
		}
	}

	# get $USR data
	$datau = getXML(GSDATAOTHERPATH .'user.xml');
	$datac = getXML(GSDATAOTHERPATH .'cp_settings.xml');
	$dataw = getXML(GSDATAOTHERPATH .'website.xml');
	$USR = stripslashes($datau->USR);
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
		$error .= 'Unable to create new  '._id($USR).'.xml file!<br />';	
	} else {
		$message .= '<li>Created new '._id($USR).'.xml file</li>';
	}
	
	
	# rename old wesbite.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_website.xml')) {
		$status = rename(GSDATAOTHERPATH .'website.xml', GSDATAOTHERPATH .'_legacy_website.xml');
		if (!$status) {
			$error .= 'Unable to rename website.xml to _legacy_website.xml<br />';	
		} else {
			$message .= '<li>Renamed website.xml to _legacy_website.xml</li>';
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
		$error .= 'Unable to update website.xml file!<br />';	
	} else {
		$message .= '<li>Created updated website.xml file</li>';
	}
	
	
	# rename old user.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_user.xml')) {
		$status = rename(GSDATAOTHERPATH .'user.xml', GSDATAOTHERPATH .'_legacy_user.xml');
		if (!$status) {
			$error .= 'Unable to rename user.xml to _legacy_user.xml<br />';	
		} else {
			$message .= '<li>Renamed user.xml to _legacy_user.xml</li>';
		}
	}

	# rename old cp_settings.xml
	if (!file_exists(GSDATAOTHERPATH .'_legacy_cp_settings.xml')) {
		$status = rename(GSDATAOTHERPATH .'cp_settings.xml', GSDATAOTHERPATH .'_legacy_cp_settings.xml');
		if (!$status) {
			$error .= 'Unable to rename cp_settings.xml to _legacy_cp_settings.xml<br />';	
		} else {
			$message .= '<li>Renamed cp_settings.xml to _legacy_cp_settings.xml</li>';
		}
	}
	/* end update */
} 

?>
<?php get_template('header', $site_full_name.' &raquo; '. i18n_r('SYSTEM_UPDATE')); ?>
	
	<h1><?php echo $site_full_name; ?> <span>&raquo;</span> <?php i18n('SYSTEM_UPDATE'); ?></h1>
</div>
</div>
<div class="wrapper">
	<?php 
		include('template/error_checking.php'); 
	?>
	
	<div id="maincontent">
		<div class="main" >
			<h3><?php i18n('SYSTEM_UPDATE'); ?></h3>
			
			<?php 
				echo $message; 
				echo '<p><a href="./">Login</a></p>';
		
			?>
			
		</div>
	
		<div class="clear"></div>
<?php get_template('footer'); ?> 

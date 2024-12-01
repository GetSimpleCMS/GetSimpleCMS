<?php
/*
Plugin Name: Migrate site
Description: Migrate site to another domain
Version: 0.1
Author: Andrejus Semionovas
Author URI: http://pigios-svetaines.eu/
*/

i18n_merge('migrate') || i18n_merge('migrate','en_US');

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

global $SITEURL;

# register plugin
register_plugin(
	$thisfile,							//Plugin id
	'Migrate site ',					//Plugin name
	'0.1',								//Plugin version
	'Andrejus Semionovas',				//Plugin author 
	'http://pigios-svetaines.eu',		//author website
	'Migrate site to another domain',	//Plugin description
	'backups',							//page type - on which admin tab to display
	'migrate_settings'					//main function (administration)
);

add_action('backups-sidebar','createSideMenu',array($thisfile,'Migrate site'));

function migrate_settings(){ ?>
<style>
fieldset {
	border-radius: 6px;
}
.inner-divs {
    clear: both;
    padding: 10px 20px;
}
#maincontent .migrate_input {
	float: right;
	color: #333;
	border: 1px solid #AAA;
	padding: 5px;
	font-family: Arial,Helvetica Neue,Helvetica,sans-serif;
	font-size: 12px;
	border-radius: 2px;
	width: 60%;
	margin-left: 10px;
}
.container.inner {
	margin: 10px;
}
.container .left, .container .right {
	float: left;
}
.container .left, .container .right {
	width: 50%;
}
#all_arch label, .inner label {
    display: inline-block;
	margin-left: 6px;
	cursor: pointer;
}
.change-button {
    margin: 20px;
	line-height: 14px !important;
    background-color: #182227;
    color: #CCC;
    font-weight: bold;
    text-decoration: none;
    text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2);
    transition: all 0.1s ease-in-out 0s;
    font-size: 10px;
    text-transform: uppercase;
    display: block;
    padding: 3px 10px;
    border-radius: 3px;
    background-repeat: no-repeat;
    background-position: 94% center;
    cursor: pointer;
    border: medium none;
}
.change-button:hover {
    background-color: #CF3805;
    color: #FFF;
    font-weight: bold;
    text-decoration: none;
    line-height: 14px !important;
    text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2);
}
#all_arch {
    padding-left: 20px;
}
#all_arch span {
    padding-left: 8px;
}
</style>
<?php	
	global $content, $SITEURL;
	if(isset($_POST['do_replace']) && $_POST['do_replace']) {
		if(isset($_POST['xml_replace']) && $_POST['xml_replace'] == 0) {
			xml_replace($_POST['exist_name'], $_POST['new_name'], 0);
		}
		if(isset($_POST['xml_replace']) && $_POST['xml_replace'] == 1) {
			if(isset($_POST['all_arch']) && $_POST['all_arch'] == 1) $all_arch = true;
			else $all_arch = false;
			xml_replace($_POST['exist_name'], $_POST['new_name'], 1, $all_arch);
			unset($_POST['xml_replace']);
		}
		unset($_POST['xml_replace']);
		unset($_POST['do_replace']);
	}
	if(isset($_POST['file_delete']) && $_POST['file_delete']) {
		unlink('../plugins/migrate/arch/'.$_POST['file_delete']);
		unset($_POST['file_delete']);
	}
?>
	<h3 class="floated" style="float:left;margin-bottom: 20px;"><?php i18n('migrate/TITLE'); ?></h3>
	<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" class="migrate" name="migrate">
		<fieldset class="container widesec">
			<div class="inner-divs">
				<span class="input-span"><?php i18n('migrate/EXIST_NAME'); ?></span>
				<input type="text" name="exist_name" class="migrate_input" value="<?php echo $SITEURL; ?>"/>
			</div>
			<div class="inner-divs">
				<span class="input-span"><?php i18n('migrate/NEW_NAME'); ?></span>
				<input type="text" name="new_name" class="migrate_input" value=""/>
			</div>
		<fieldset class="container inner">
			<div class="left">
				 <label><input type="radio" id="xml_replace" name="xml_replace" value=0 onclick="checkbutton()" /><label for="xml_replace"><?php i18n('migrate/REPLACE'); ?></label>
			</div>
			<div class="right">
				 <label><input type="radio" id="xml_archive" name="xml_replace" value=1 checked onclick="checkbutton()" /><label for="xml_archive"><?php i18n('migrate/ARCHIVE'); ?></label>
			</div>
		</fieldset>
		<div class="widesec" id="all_arch">
			 <input type="checkbox" name="all_arch" id="allfile" value=1 /><label for="allfile"><?php i18n('migrate/ALL_INCLUDE'); ?></label>
		</div>
		<input type="submit" name="do_replace" class="change-button" value="<?php i18n('migrate/SUBMIT'); ?>" onclick="return checkform()"/>
		</fieldset>
		
	</form>
	
<script type="text/javascript">
	function checkbutton() {
		if (document.getElementById("xml_archive").checked) {
			document.getElementById('all_arch').style.visibility = "visible";
		}
		if (document.getElementById("xml_replace").checked) {
			document.getElementById('all_arch').style.visibility = "hidden";
		}
	}
	
	function checkform() {
		if(document.migrate.new_name.value == "" || document.migrate.exist_name.value == document.migrate.new_name.value) {
			if(document.migrate.new_name.value == "") {
				var empty_field='<?php i18n('migrate/NOT_NEW'); ?>';
				alert(empty_field);
				return false;
			}
			if(document.migrate.exist_name.value == document.migrate.new_name.value) {
				var ident_field='<?php i18n('migrate/IDENTICAL'); ?>';
				alert(ident_field);
				return false;
			}
		} else {
			var lastnChar = document.migrate.new_name.value.substr(document.migrate.new_name.value.length - 1);
			var lasteChar = document.migrate.exist_name.value.substr(document.migrate.exist_name.value.length - 1);
			var httpnChar = document.migrate.new_name.value.substr(0, 4);
			var httpeChar = document.migrate.exist_name.value.substr(0, 4);
			if(lastnChar=="/" && lasteChar!="/" || lastnChar!="/" && lasteChar=="/") {
				alert(lastnChar+" Backslash not indent "+lasteChar);
				return false;
			}
			if(httpnChar=="http" && httpeChar!="http" || httpnChar!="http" && httpeChar=="http") {
				alert(httpnChar+" HTTP not indent "+httpeChar);
				return false;
			}
			document.migrate.submit();
			return true;
		}
		
	}
	
	jQuery(function(){
		setTimeout(function() {
			jQuery(".fancy-message").hide('slow');
		}, 20000);
	});
	
	
</script>
<?php
	$dir_arch = GSPLUGINPATH.'migrate/arch';
	$arch_handle = false;
	if (is_dir($dir_arch)) {
		if ($dh = opendir($dir_arch)) { 
			while (($file = readdir($dh)) !== false) {
				if($file == "." ||  $file == ".." || is_dir($dir_arch.'/'.$file) || $file == ".htaccess") { continue; }
				else {
					$arch_handle = true;
				}
			}
		}
	}
	if($arch_handle) {
	?>
	<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" class="zipfiles" name="zipfiles">
		<fieldset class="container widesec">
		<legend style="padding: 0px 6px 0px 6px;margin: 6px;"><?php i18n('migrate/FOR_DOWNLOAD'); ?></legend><?php
		$dh = opendir($dir_arch);
		while (($file = readdir($dh)) !== false) {
			if($file == "." ||  $file == ".." || is_dir($dir_arch.'/'.$file) || $file == ".htaccess") { continue; }
			else { ?>
			<div class="inner">
			<div style="width: 70%;float: left;">
				<a href="<?php echo $SITEURL.'plugins/migrate/arch/'.$file; ?>" target="_blank"><?php echo $file; ?></a>
				
			</div>
			<div style="float: left;"><?php echo round(filesize('../plugins/migrate/arch/'.$file) * .0009765625, 3); ?> Kb</div>
			<div style="width: 10%;float: right;text-align: right;" style="text-align: right;">
				<button class="filedelete" name="file_delete" value="<?php echo $file; ?>" style="width: 20px;height: 20px; margin-right: 20px; background:url(<?php echo $SITEURL.'plugins/migrate/images/deletef.png' ?>) red center no-repeat;cursor: pointer;border: 1px solid #F00;border-radius: 4px;" title="<?php i18n('migrate/DEL_FILE'); ?>"></button>
			</div>
			</div><?php
			}
		} ?>
		</fieldset>
	</form>
	<?php
	}
}
function replaceXMLdata($xml, $element, $repl_string){
	/* Handle empty Taggs for storage */
	foreach($xml->children() as $child) {
		$var_empt = $child->getName();
		$var_emp = $child;
		if(!strlen($var_emp)>0) {
			$xml->$var_empt = '';
		}
    }
	/* Replace old node data with new one */
	$new_child = $xml->addChild($element);
	$domReplace = dom_import_simplexml($new_child);
	$no   = $domReplace->ownerDocument;
	$domReplace->appendChild($no->createCDATASection($repl_string));
	$domToChange = dom_import_simplexml($xml->$element);
	$nodeImport  = $domToChange->ownerDocument->importNode($domReplace, TRUE);
	$domToChange->parentNode->replaceChild($nodeImport, $domToChange);
	return $xml;
}

function xml_replace($exist_name, $new_name, $mode=1, $alls=false) {
	$dir = GSROOTPATH.'data/pages';

	if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
		$total = 0;
		$files_change = 0;
		$no_modified = 0;
        while (($file = readdir($dh)) !== false) {
            if($file == "." ||  $file == ".." || is_dir($dir.'/'.$file) || $file == ".htaccess") { continue; }
			else {
				$ext = substr($file, strrpos($file, '.') + 1);
				if(in_array($ext, array("xml","XML"))) {
					$file_xml = simplexml_load_file(GSROOTPATH.'data/pages/'.$file);
					$file_content = $file_xml->content;
					$str_nr = str_ireplace($exist_name, $new_name, $file_content, $count);
					if($count>0 || $alls == 1) {
						if($count>0) {
							$total += $count;
							$files_change = $files_change + 1;
						}
						else { $no_modified = $no_modified + 1; }
						if(!is_dir(GSPLUGINPATH.'migrate/mods')) {
							if ( ! @mkdir(GSPLUGINPATH.'migrate/mods', 0777, true) ) {
								die('Failed to create folder...');
								return false;
							}
						}
						$file_xml = replaceXMLdata($file_xml, 'content', $str_nr);
						if($mode == 1) XMLsave($file_xml, GSPLUGINPATH.'migrate/mods/'.$file);
						if($mode == 0) XMLsave($file_xml, GSROOTPATH.'data/pages/'.$file);
					}
				}
			}
        } 
        closedir($dh);

		$file_xml = simplexml_load_file(GSROOTPATH.'data/other/website.xml');
		$file_content = $file_xml->SITEURL;
		
		$str_nr = str_ireplace($exist_name, $new_name, $file_content, $countw);

		$file_xml = replaceXMLdata($file_xml, 'SITEURL', $str_nr);
		if(!is_dir(GSPLUGINPATH.'migrate/mods')) {
			if ( ! @mkdir(GSPLUGINPATH.'migrate/mods', 0777, true) ) {
				die('Failed to create folder...');
				return false;
			}
		}
		if($mode == 1) $file_xml->saveXML(GSPLUGINPATH.'migrate/mods/website.xml');
		if($mode == 0) $file_xml->saveXML(GSROOTPATH.'data/other/website.xml');
		if($countw>0) {
			$total += $countw;
			$files_change = $files_change + 1;
		}
		
		if($total > 0 && $mode == 1) {
			if(!is_dir(GSPLUGINPATH.'migrate/arch')) {
				if ( ! @mkdir(GSPLUGINPATH.'migrate/arch', 0777, true) ) {
					die('Failed to create folder...');
					return false;
				}
				$myfile = fopen(GSPLUGINPATH.'migrate/arch/'.'.htaccess', 'w') or die('Unable to open file!');
				fwrite($myfile, 'Allow from All');
				fclose($myfile);
			}
			$timestamp = gmdate('Y-m-d-Hi');
					$zipcreated = true;

					set_time_limit (0);
					ini_set("memory_limit","800M"); 
					$new_domain = str_replace(array("http://", "https://", "/", "."), array("","","_","_"), $new_name);
					$zip_path = GSPLUGINPATH.'migrate/arch/'.$new_domain.'_'.$timestamp .'.zip';
					$sourcePath = str_replace('/', DIRECTORY_SEPARATOR, GSPLUGINPATH.'migrate/mods/');
					if (!class_exists ( 'ZipArchive' , false)) {
						include('inc/ZipArchive.php');
					}
					if (class_exists ( 'ZipArchive' , false)) {
						
						$zip = new ZipArchive();
						$result = $zip->open($zip_path, ZipArchive::CREATE);
;
						if ($result === TRUE && is_readable(GSPLUGINPATH.'migrate/mods')) {
							foreach (new DirectoryIterator(GSPLUGINPATH.'migrate/mods') as $fileInfo) {
								if(!$fileInfo->isDot()) {
									if($fileInfo->getFilename() != 'website.xml') {
										if (($zip->addFile ($fileInfo->getPathname(), "data/pages/".$fileInfo->getFilename())) === TRUE) {
											$theoreticaly_added = TRUE;
										}
									}
									if($fileInfo->getFilename() == 'website.xml') {
										if (($zip->addFile ($fileInfo->getPathname(), "data/other/".$fileInfo->getFilename())) === TRUE) {
											$theoreticaly_added = TRUE;
										}
									}
								}
							}
						}
						$status = $zip->close();
					}
					else { ?>
						<div class="fancy-message" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #DFF0D8; color: #3C8C8F;"><p><?php i18n('migrate/ZIP_NOT'); ?></p></div><?php
					}
			if (is_readable(GSPLUGINPATH.'migrate/mods')) {
				foreach (new DirectoryIterator(GSPLUGINPATH.'migrate/mods') as $fileInfo) {
					if(!$fileInfo->isDot()) {
						unlink($fileInfo->getPathname());
					}
				}

			}
			if($status && $theoreticaly_added) { ?>
				<div class="fancy-message" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #DFF0D8; color: #3C8C8F;"><p><?php i18n('migrate/ARCH_FILE'); i18n('migrate/ALL_REPLACE'); echo $total; i18n('migrate/ALL_FILES'); echo $files_change; (isset($no_modified) && !empty($no_modified) && $no_modified!=$total?i18n('migrate/TOTAL_FILES'):''); echo (isset($no_modified) && !empty($no_modified) && $no_modified!=$total?$no_modified:''); ?></p></div><?php
			}
		}
		if($mode == 0) { ?>
				<div class="fancy-message" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #DFF0D8; color: #3C8C8F;"><p><?php i18n('migrate/REPL_FILE'); ?><?php i18n('migrate/ALL_REPLACE'); echo $total; i18n('migrate/ALL_FILES'); echo $files_change; ?></p></div><?php
		}
    } else { ?>
		<div class="fancy-message" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #F2DEDE; color: #A94442;"><p><?php i18n('migrate/NOT_DIR_ACCESS'); echo $dir; ?></p></div><?php
	}
	} else { ?>
		<div class="fancy-message" style="border: 1px solid; padding: 20px 10px 10px 10px; border-radius: 4px; margin-bottom: 20px; background: #F2DEDE; color: #A94442;"><p><?php i18n('migrate/NOT_DIR_EXIST'); echo $dir; ?></p></div><?php
	}
}
?>

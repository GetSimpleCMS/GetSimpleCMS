<?php
/*
Plugin Name: Plugin Downloader
Description: Allows downloading of getsimple plugins from within admin panel. All Files are coming directly from the get-simple.info extend.
Version: 1.0
Author: Mike Henken
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile, 		
	'Extend Downloader',
	'1.0',
	'Mike Henken',	
    'http://www.michaelhenken.com/',  
	'Downloads and installs plugins, themes and languages', 
	'plugins', 	
	'extend_download'  	
);

# activate hooks
add_action('plugins-sidebar','createSideMenu',array($thisfile,'Download Plugins'));
define('ExtendDownloadFile', GSDATAOTHERPATH  . 'extend-download.xml');

class ExtendDownloader 
{
	//Check if extend file exists. If it does not create it.
	//Displays plugin navigation
	public function __construct() 
	{
		if (!file_exists(ExtendDownloadFile)) 
		{
			$this->refreshPlugin();
			echo '<div class="error"><strong>Please Read Before Using This Plugin:</strong><br/>
			This extend downloader only downloads the plugin zipfile from the Get-Simple extend and then extracts the files into the correct folder on your website.<br/>
			Some plugins require more steps to succesfully work. We recommend you read the documentation (the entire description & optionally support thread as well) for the plugin before
			installing it.
			</div>';
		}	
		require_once(GSPLUGINPATH.'extend-download/markdown.php');
?>
		<script language="javascript">
			function decision(message, url){
				if(confirm(message)) location.href = url;
			}
		</script>
		<style type="text/css">
			.leftsec {
				border: 1px solid #C3C3C3 !important;
				box-shadow: 0 0 4px rgba(0, 0, 0, 0.06) !important;
				width:305px;
				margin-top:20px;
				padding-left:5px;
				padding-top:5px;
				clear:left;
			}

			.rightsec {
			 border: 1px solid #C3C3C3 !important;
				box-shadow: 0 0 4px rgba(0, 0, 0, 0.06) !important;
				width:305px;
				margin-left:20px;
				margin-top:20px;
				padding-left:5px;
				padding-top:5px;
			}
			.thesec {
				margin-top:0px;
			}
			.asubmit {
				padding:2px;
			}
			.text {
				width:200px !important;
				margin-right:10px !important;
				margin-left:10px !important;
				margin-bottom:10px !important;
			}
			.extend_specs_left {
				width:240px;
				height:100px;
				float:left;
			}		
			.extend_specs_right {
				width:300px;
				float:left;
				margin-left:20px;
				text-align
			}
			.extend_spec_label{
				font-size:15px;
				font-weight:bold;
				float:left;
			}
		</style>
		<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;"> 	
			<h3 class="floated">Extend Downloader</h3>	
			<a href="load.php?id=extend-download&refresh" title="Refresh Extend Data"><img src="../plugins/extend-download/refresh.png" style="float:right;margin-left:20px;" /></a>
			<div class="edit-nav clearfix" style="">
				<a href="load.php?id=extend-download&extend_type=languages">Languages</a>
				<a href="load.php?id=extend-download&extend_type=themes">Themes</a>
				<a href="load.php?id=extend-download">Plugins</a>
			</div>
		</div>
		</div>
		<div class="main" style="margin-top:-10px;">
<?php		
	}
	
	//gets data from getsimple api and writes it to xml file
	public function refreshPlugin() 
	{
		$this->downloadAllPlugins($this->returnAllPlugins());
	}
	
	//return specific data from extend item
	public function returnAPIdata($id, $getExtendNode)
	{
		$my_plugin_id = $id; // replace this with yours

		$apiback = file_get_contents('http://get-simple.info/api/extend/?id='.$my_plugin_id);
		$response = json_decode($apiback);
		if ($response->status == 'successful') 
		{
			// Successful api response sent back. 
			$ExtendNodeData = $response->$getExtendNode;
		}
        return $ExtendNodeData;
	}
	
	//returns an array of all items in extend
	private function returnAllPlugins() 
	{
		$apiback = file_get_contents('http://get-simple.info/api/extend/all.php');
		$json = preg_replace('/:\s*\'(([^\']|\\\\\')*)\'\s*([},])/e', "':'.json_encode(stripslashes('$1')).'$3'", $apiback);
		$json = json_decode($apiback, TRUE);
		return $json;
	}
		
	public function ver_check()
	{
		include(GSADMININCPATH.'configuration.php');
		return GSVERSION;
	}

	public function pluginInstalledCheck($filename)
	{
		if($filename != '' && file_exists(GSPLUGINPATH.$filename))
		{
			return true;
		}
	}
	
	//checks to make sure the extend item meets the requirements of the extend downloader
	public function zipCheck($extendtype, $pluginurl, $pluginfile, $pluginfilename)
	{
		//Save extend zip file to server
		$data = file_get_contents($pluginurl);
		$fp = fopen($pluginfile, "wb");
		fwrite($fp, $data);
		fclose($fp);
		
		if($extendtype == "languages" || $extendtype == "themes")
		{
			return true;
		}
		elseif($extendtype == "plugins")
		{
			$zip = new ZipArchive;
			if ($zip->open(GSADMINPATH.$pluginfile) === TRUE) 
			{
				if($zip->statName($pluginfilename) != "")
				{
					return true;
				}
				else 
				{
					return false;
				}
				$zip->close();
			}
		}
	}
	
	//uses the array returned from returnAllPlugins() to create the xml file containing all extend item data
	public function downloadAllPlugins($AllPluginsArray)
	{
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$pluginChild = $xml->addChild('plugins');		
		foreach ($AllPluginsArray as $json_item)
		{
			if($json_item['category'] == 'Plugin')
			{
				$pluginChildDetails = $pluginChild->addChild('plugin');
				$pluginChildDetails->addAttribute('id', $json_item['id']);
				$pluginChildDetails->addChild('id', $json_item['id']);
				$pluginChildDetails->addChild('filename_id', $json_item['filename_id']);
				$pluginChildName = $pluginChildDetails->addChild('name');
				$pluginChildName->addCData($json_item['name']);
				$pluginChildDetails->addChild('version', $json_item['version']);
				$pluginChildDetails->addChild('category', $json_item['category']);
				$pluginChildTags = $pluginChildDetails->addChild('tags');
				$pluginChildTags->addCData($json_item['tags']);
				$pluginChildDetails->addChild('tested_earliest', $json_item['tested_earliest']);
				$pluginChildDetails->addChild('tested_latest', $json_item['tested_latest']);
				$pluginChildDetails->addChild('updated_date', $json_item['updated_date']);
				$pluginChildDetails->addChild('support_url', $json_item['support_url']);
				$pluginChildDetails->addChild('author_url', $json_item['author_url']);
				$pluginChildDescription = $pluginChildDetails->addChild('description');
				$pluginChildDescription->addCData($json_item['description']);
				$pluginChildDetails->addChild('path', $json_item['path']);
				$pluginChildDetails->addChild('file', $json_item['file']);
				$pluginChildDetails->addChild('downloads', $json_item['downloads']);
				$pluginChildDetails->addChild('rating', $json_item['rating']);
			}
		}
		$themeChild = $xml->addChild('themes');
		foreach ($AllPluginsArray as $json_item)
		{
			if($json_item['category'] == 'Theme')
			{
				$themeChildDetails = $themeChild->addChild('theme');
				$themeChildDetails->addAttribute('id', $json_item['id']);
				$themeChildDetails->addChild('id', $json_item['id']);
				$themeChildDetails->addChild('filename_id', $json_item['filename_id']);
				$themeChildName = $themeChildDetails->addChild('name');
				$themeChildName->addCData($json_item['name']);
				$themeChildDetails->addChild('version', $json_item['version']);
				$themeChildDetails->addChild('category', $json_item['category']);
				$themeChildTags = $themeChildDetails->addChild('tags');
				$themeChildTags->addCData($json_item['tags']);
				$themeChildDetails->addChild('tested_earliest', $json_item['tested_earliest']);
				$themeChildDetails->addChild('tested_latest', $json_item['tested_latest']);
				$themeChildDetails->addChild('updated_date', $json_item['updated_date']);
				$themeChildDetails->addChild('support_url', $json_item['support_url']);
				$themeChildDetails->addChild('author_url', $json_item['author_url']);
				$themeChildDescription = $themeChildDetails->addChild('description');
				$themeChildDescription->addCData($json_item['description']);
				$themeChildDetails->addChild('path', $json_item['path']);
				$themeChildDetails->addChild('file', $json_item['file']);
				$themeChildDetails->addChild('downloads', $json_item['downloads']);
				$themeChildDetails->addChild('rating', $json_item['rating']);
			}
		}
		$languageChild = $xml->addChild('languages');
		foreach ($AllPluginsArray as $json_item)
		{
			if($json_item['category'] == 'Language')
			{
				$languageChildDetails = $languageChild->addChild('language');
				$languageChildDetails->addAttribute('id', $json_item['id']);
				$languageChildDetails->addChild('id', $json_item['id']);
				$languageChildDetails->addChild('filename_id', $json_item['filename_id']);
				$languageChildName = $languageChildDetails->addChild('name');
				$languageChildName->addCData($json_item['name']);
				$languageChildDetails->addChild('version', $json_item['version']);
				$languageChildDetails->addChild('category', $json_item['category']);
				$languageChildTags = $languageChildDetails->addChild('tags');
				$languageChildTags->addCData($json_item['tags']);
				$languageChildDetails->addChild('tested_earliest', $json_item['tested_earliest']);
				$languageChildDetails->addChild('tested_latest', $json_item['tested_latest']);
				$languageChildDetails->addChild('updated_date', $json_item['updated_date']);
				$languageChildDetails->addChild('support_url', $json_item['support_url']);
				$languageChildDetails->addChild('author_url', $json_item['author_url']);
				$languageChildDescription = $languageChildDetails->addChild('description');
				$languageChildDescription->addCData($json_item['description']);
				$languageChildDetails->addChild('path', $json_item['path']);
				$languageChildDetails->addChild('file', $json_item['file']);
				$languageChildDetails->addChild('downloads', $json_item['downloads']);
				$languageChildDetails->addChild('rating', $json_item['rating']);
			}
		}
		//Save XML File
		if(XMLsave($xml, ExtendDownloadFile))
		{
				echo '<div class="updated">Extend File Has Succesfully Created/Updated</div>';
		}
	}
	
	//unzips file and displays success or error message
	public function unZipFile($id,$extendtype)
	{
		$pluginurl = $this->returnAPIdata($id, 'file');
		$pluginfile = basename($pluginurl);
		$pluginname = $this->returnAPIdata($id, 'name');
		$pluginfilename = $this->returnAPIdata($id, 'filename_id');
		
		if($extendtype == 'plugins')
		{
			$extendpath = GSPLUGINPATH;
		}
		elseif($extendtype == 'themes')
		{
			$extendpath = GSTHEMESPATH;
		}
		elseif($extendtype == 'languages')
		{
			$extendpath = GSLANGPATH;
		}
		
		//function to unzip the zip
		function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true)
		{
			if ($zip = zip_open($src_file))
			{
				if ($zip)
				{
				  $splitter = ($create_zip_name_dir === true) ? "." : "/";
				  if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";

				  create_dirs($dest_dir);

				  while ($zip_entry = zip_read($zip))
				  {

					$pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
					if ($pos_last_slash !== false)
					{
					  create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
					}

					if (zip_entry_open($zip,$zip_entry,"r"))
					{

					  $file_name = $dest_dir.zip_entry_name($zip_entry);

					  if ($overwrite === true || $overwrite === false && !is_file($file_name))
					  {
						$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

						file_put_contents($file_name, $fstream );
						chmod($file_name, 0755);
					  }

					  zip_entry_close($zip_entry);
					}
				  }
				  zip_close($zip);
				}
			  }
			  else
			  {
				return false;
			  }
		  return true;
		}
	
		/**
		 * This function creates recursive directories if it doesn't already exist
		 *
		 * @param String  The path that should be created
		 *
		 * @return  void
		 */
		function create_dirs($path)
		{
		  if (!is_dir($path))
		  {
			$directory_path = "";
			$directories = explode("/",$path);
			array_pop($directories);

			foreach($directories as $directory)
			{
			  $directory_path .= $directory."/";
			  if (!is_dir($directory_path))
			  {
				mkdir($directory_path);
				chmod($directory_path, 0777);
			  }
			}
		  }
		}
		
		
		 /* Unzip the source_file in the destination dir
		 *
		 * @param   string      The path to the ZIP-file.
		 * @param   string      The path where the zipfile should be unpacked, if false the directory of the zip-file is used
		 * @param   boolean     Indicates if the files will be unpacked in a directory with the name of the zip-file (true) or not (false) (only if the destination directory is set to false!)
		 * @param   boolean     Overwrite existing files (true) or not (false)
		 *
		 * @return  boolean     Succesful or not
		 */
		$plugin_check = $this->zipCheck($extendtype, $pluginurl, $pluginfile, $pluginfilename);
		if ($plugin_check == true)
		{		 
			$success = unzip($pluginfile, $extendpath, true, true);	
			if($success)
			{
				print '<div class="updated">'.$pluginname.' was succesfully downloaded.</div>';
				unlink(GSADMINPATH.$pluginfile);
			}
		}
		elseif($plugin_check != true || !$success )
		{
			?>
			<div class="error">
				The <?php echo $pluginname; ?> was not installed correctly. You are going to have to manually install this plugin.<br/>
				You could download that file here: <a href="<?php echo $pluginurl; ?>"><?php echo $pluginurl; ?></a>
			</div>
			<?php
		}
	}
	
	//shows list of plugins available for download
	public function showPluginsDownload()
	{
		?>
		<h3 class="floated">Download Plugins</h3>	
		<div class="edit-nav clearfix" style="">
			<a href="load.php?id=extend-download&extend_type=plugins&download&download_id=10&refresh">Update Plugin Downloader</a>
		</div>
		<div class="plugin_padding" style="padding:10px 0px;">
			<table class="highlight">
		<?php 		
		$extendData = getXML(GSDATAOTHERPATH.'extend-download.xml');
		global $plugin_info;
		foreach ($extendData->plugins->plugin as $extendItem)
		{	
			$plugin_installed = 'No';
			$plugin_version = '';
			$download_confirmation = '<a href="load.php?id=extend-download&extend_type=plugins&download&download_id='.$extendItem->id.'">Download</a>';
			if($this->pluginInstalledCheck($extendItem->filename_id) == true)
			{
				$needsupdate = false;
				$fi = $extendItem->filename_id;
				$pathName = pathinfo_filename($fi);
				$plugin_installed = 'Yes';
				$plugin_version = '<span class="extend_spec_label">Current Installed Plugin Version:&nbsp;</span>'.$plugin_info[$pathName]['version'];
				$download_confirmation = '<a href="#" onclick="decision(\'Are You Sure You Want To Update '.$extendItem->name.'?\',\'load.php?id=extend-download&extend_type=plugins&download&download_id='.$extendItem->id.'\')">Update</a>';
			}
			?>
			<tr>
				<td>
					<h3 style="margin-bottom:10px;"><?php echo $extendItem->name; ?>&nbsp;&nbsp;&nbsp;<?php echo $download_confirmation; ?></h3>
					<div class="extend_specs_left">
						<span class="extend_spec_label">Version:&nbsp;</span><?php echo $extendItem->version; ?><br/>
						<span class="extend_spec_label">Updated Date:&nbsp;</span><?php echo $extendItem->updated_date; ?><br/>
						<span class="extend_spec_label"><a href="<?php echo $extendItem->support_url; ?>" target="_blank">Support URL</span><br/>
						<span class="extend_spec_label"><a href="<?php echo $extendItem->author_url; ?>" target="_blank">Author URL</a></span>
					</div>
					<div class="extend_specs_right">
						<span class="extend_spec_label">Tested Earliest:&nbsp;</span><?php echo $extendItem->tested_earliest; ?><br/>
						<span class="extend_spec_label">Tested Latest:&nbsp;</span><?php echo $extendItem->tested_latest; ?><br/>
						<span class="extend_spec_label">Is this plugin installed?:&nbsp;</span> <?php echo $plugin_installed; ?><br/>
						<?php echo $plugin_version; ?>
						
					</div>
					<div style="clear:both;"></div>
					<div style="width:620px;">
						<?php echo Markdown(stripslashes(html_entity_decode($extendItem->description))); ?>
					</div>
				</td>
			</tr>		
			<?php } ?>
			</table>
		</div>
		<?php
	}
	
	//shows list of themes available for download
	public function showThemesDownload()
	{
		?>
		<h3>Download Themes</h3>
		<div class="plugin_padding" style="padding:10px 0px;">
			<table class="highlight">
		<?php 		
		$extendData = getXML(GSDATAOTHERPATH.'extend-download.xml');
		foreach ($extendData->themes->theme as $extendItem)
		{
			?>
			<tr>
				<td>
					<h3 style="margin-bottom:10px;"><?php echo $extendItem->name; ?>&nbsp;&nbsp;&nbsp;<a href="load.php?id=extend-download&extend_type=themes&download&download_id=<?php echo $extendItem->id; ?>">Download Or Update</a></h3>
					<div class="extend_specs_left">
						<span class="extend_spec_label">Version:&nbsp;</span><?php echo $extendItem->version; ?><br/>
						<span class="extend_spec_label">Updated Date:&nbsp;</span><?php echo $extendItem->updated_date; ?><br/>
						<span class="extend_spec_label"><a href="<?php echo $extendItem->support_url; ?>" target="_blank">Support URL</span><br/>
						<span class="extend_spec_label"><a href="<?php echo $extendItem->author_url; ?>" target="_blank">Author URL</a></span>
					</div>
					<div class="extend_specs_right">
						<span class="extend_spec_label">Tested Earliest:&nbsp;</span><?php echo $extendItem->tested_earliest; ?><br/>
						<span class="extend_spec_label">Tested Latest:&nbsp;</span><?php echo $extendItem->tested_latest; ?><br/>
					</div>
					<div style="clear:both;"></div>
					<div style="width:620px;">
						<?php echo Markdown(stripslashes(html_entity_decode($extendItem->description))); ?>
					</div>
				</td>
			</tr>		
			<?php } ?>
			</table>
		</div>
		<?php
	}
	
	//shows list of languages available to download
	//NOTE: language downloads have not yet been tested to make sure they will work with extend downloader. Most of them work correctly but I am current looking for a way to verify that a php file exists in the root of the zip file
	public function showLanguageDownload()
	{
		?>
		<h3>Download Languages</h3>
		<div class="plugin_padding" style="padding:10px 0px;">
			<table class="highlight">
		<?php 		
		$extendData = getXML(GSDATAOTHERPATH.'extend-download.xml');
		foreach ($extendData->languages->language as $extendItem)
		{
			?>
			<tr>
				<td>
					<h3 style="margin-bottom:10px;"><?php echo $extendItem->name; ?>&nbsp;&nbsp;&nbsp;<a href="load.php?id=extend-download&download&extend_type=languages&download_id=<?php echo $extendItem->id; ?>">Download Or Update</a></h3>
					<p>
						<?php echo Markdown(stripslashes(html_entity_decode($extendItem->description))); ?>
					</p>
				</td>
			</tr>		
			<?php } ?>
			</table>
		</div>
		<?php
	}
}

//initiate class and functions
function extend_download() 
{
	$ExtendDownloader = new ExtendDownloader;
	
	if(isset($_GET['download']))
	{
		$ExtendDownloader->unZipFile($_GET['download_id'],$_GET['extend_type']);
	}
	
	if(isset($_GET['download_check']))
	{
		$ExtendDownloader->zipCheck($_GET['download_id']);
	}
	
	if(isset($_GET['refresh']))
	{
		$ExtendDownloader->refreshPlugin();
	}
	
	if(!isset($_GET['extend_type']) || $_GET['extend_type'] == 'plugins')
	{
		$ExtendDownloader->showPluginsDownload();
	}
	elseif(isset($_GET['extend_type']) && $_GET['extend_type'] == "themes")
	{
		$ExtendDownloader->showThemesDownload();
	}
	elseif(isset($_GET['extend_type']) && $_GET['extend_type'] == "languages")
	{
		$ExtendDownloader->showLanguageDownload();
	}
	
}
?>
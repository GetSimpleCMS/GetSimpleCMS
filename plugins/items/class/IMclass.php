<?php 

class ItemsManager
{
	public function __construct()
	{
			//Path for uploaded images/files to be placed
			$end_path = GSDATAUPLOADPATH.'items';
			
			//Alert Admin If Items Manager Settings XML File Is Directory Does Not Exist
			if (!file_exists(ITEMDATA)) 
			{
				mkdir(GSDATAPATH.'items', 0755);
				$ourFileName = GSDATAPATH.'items/.htaccess';
				$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
				$stringData = "Allow from all";
				fwrite($ourFileHandle, $stringData);
				fclose($ourFileHandle);
				if (!file_exists(ITEMDATA)) 
				{
					echo '<h3>'.IMTITLE.' Manager</h3><p>The directory "<i>'.GSDATAPATH.'items</i>"
					does not exist. It is required for this plugin to function properly.
					Please create it manually and make sure it is writable.</p>';
				}
				else
				{
					echo '<div class="updated"><strong>The below directory has been succesfully created:</strong><br/>"'.ITEMDATA.'"</div>';
				}
			}
			if(!file_exists($end_path))
			{
				mkdir($end_path, 0755);
				$ourFileName = $end_path.'/.htaccess';
				$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
				$stringData = "Allow from all";
				fwrite($ourFileHandle, $stringData);
				fclose($ourFileHandle);
				if (!file_exists($end_path)) 
				{
					echo '<h3>'.IMTITLE.' Manager</h3><p>The directory "<i>'.$end_path.'</i>"
					does not exist. It is required for this plugin to function properly.
					Please create it manually and make sure it is writable.</p><p>You will also need to create a .htaccess document and place it in the "'.$end_path.'" folder. The .htaccess file needs to contain the following line of code:<br/>Allow from all</p>';
				}
				else
				{
					echo '<div class="updated"><strong>The below directory has been succesfully created:</strong><br/>"'.$end_path.'"</div>';
				}
			}
			if(!file_exists(ITEMDATAFILE))
			{
				$this->processImSettings();
			}
	}
	
	public function admin_header()
	{
		?>
		<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
			<h3 class="floated"><?php echo IMTITLE; ?></h3>  
			<div class="edit-nav clearfix" style="">
				<a href="load.php?id=item_manager&settings" <?php if (isset($_GET['settings'])) { echo 'class="current"'; } ?>>Settings</a>
				<a href="load.php?id=item_manager&fields" <?php if (isset($_GET['fields'])) { echo 'class="current"'; } ?>>Custom Fields</a>
				<a href="load.php?id=item_manager&category" <?php if (isset($_GET['category'])) { echo 'class="current"'; } ?>>Manage Categories</a>
				<a href="load.php?id=item_manager&edit" <?php if (isset($_GET['edit']) && $_GET['edit'] == "") { echo 'class="current"'; } ?>>Add New</a>
				<a href="load.php?id=item_manager&view" <?php if (isset($_GET['view'])) { echo 'class="current"'; } ?>>View All</a>
			</div> 
		</div>
		</div>
		<div class="main" style="margin-top:-10px;">
		<?php
	}
	
	public function getItemsAdmin()
	{
		$items = array();
		$files = getFiles(ITEMDATA);
		foreach ($files as $file) 
		{
			if (is_file(ITEMDATA . $file) && preg_match("/.xml$/", $file)) 
			{
				$items[] = $file;
			}
		}
		sort($items);
		return array_reverse($items);
	}
	
	public function showItemsAdmin()
	{
		$items = $this->getItemsAdmin();
		if(!isset($_GET['item_type']) || $item_type == "view")
		{
			if (!empty($items)) 
			{
				echo '<h3>All '.IMTITLE.'</h3><table class="highlight">';
				foreach ($items as $item) 
				{
					$id = basename($item, ".xml");
					$file = ITEMDATA . $item;
					$data = @getXML($file);
					$date = $data->date;
					$title = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
					?>
					<tr>
						<td>
							<a href="load.php?id=item_manager&edit=<?php echo $id; ?>" title="Edit <?php echo IMTITLE; ?>: <?php echo $title; ?>">
							<?php echo $title; ?>
							</a>
							<span style="font-size:9px; color:#a0a0a0; margin-left:5px"><?php echo $data->category;?></span>
						</td>
						<td style="text-align: right;">
							<span><?php echo $date; ?></span>
						</td>
						<td class="switch_visible">
							<a href="load.php?id=item_manager&visible=<?php echo $id; ?>" class="switch_visible" style="text-decoration:none" title="Visible <?php echo IMTITLE; ?>: <?php echo $title; ?>?">
							<?php 
							if (!isset($data->visible) || $data->visible == true)
							{ 
								echo '<font color="#333333">V</font>';
							}
							else
							{
								echo '<font color="#acacac">V</font>';
							}
							?>
							</a>
						</td>
						<td class="switch_promo">
							<a href="load.php?id=item_manager&promo=<?php echo $id; ?>" class="switch_promo" style="text-decoration:none" title="Promo <?php echo IMTITLE; ?>: <?php echo $title; ?>?">
							<?php 
							if (!isset($data->promo) || $data->promo == true)
							{  
								echo '<font color="#333333">P</font>';
							}
							else
							{
								echo '<font color="#acacac">P</font>';
							}
							?>
							</a>
						</td>
						<td class="delete">
							<a href="load.php?id=item_manager&delete=<?php echo $id; ?>" class="delconfirm" title="Delete <?php echo IMTITLE; ?>: <?php echo $title; ?>?">
							X
							</a>
						</td>
					</tr>
					<?php
				}
				echo '</table>';
			}
		}
		echo '<p><b>' . count($items) . '</b> '.IMTITLE.'</p>';
	}
	
	public function showCustomFieldsAdmin()
	{
		include(GSPLUGINPATH.'items/edit-2.php');
	}
	
	public function showEditItem($id)
	{
		  $file = ITEMDATA . $id . '.xml';
		  $data = @getXML($file);
		  $title = @stripslashes($data->title);
		  $category = @stripslashes($data->category);
		  $content = @stripslashes($data->content);
		  $excerpt = @stripslashes($data->excerpt);
		  ?>
		<h3><?php if (empty($data)) echo 'Create New'; else echo 'Edit'; echo IMTITLE;?><?php //$the = im_customfield_def(); foreach ($the as $thee) {echo $thee[type];}  ?></h3>
		<form class="largeform" action="load.php?id=item_manager" method="post" accept-charset="utf-8">
			<input name="id" type="hidden" value="<?php echo $id; ?>" />
			<p>
				<input class="text title" name="post-title" type="text" value="<?php if($title != "") { echo $title; } else { echo "Title"; } ?>" onFocus="if(this.value == 'Title') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'Title';}" style="width:350px;float:left;"/>
				<select class="text" style="width:250px;float:left;margin-left:20px;padding:5px;font-size:14px;" name="category">
					 <?php
						if($category == "")
						{
							echo "<option value=\"\">Choose Category..</option>";
						}
						$category_file = getXML(ITEMDATAFILE);
						
						foreach($category_file->categories->category as $the_fed)
						{
							if($category == $the_fed)
							{
								$select_box = "selected";
							}
							else { 
								$select_box = ""; 
							}
							echo "<option value=\"$the_fed\" $select_box>$the_fed</option>";
						}
					 ?>
				</select>
			</p>
			<div style="clear:both">&nbsp;</div>
			<link href="../plugins/items/uploader/client/fileuploader.css" rel="stylesheet" type="text/css">
			<script src="../plugins/items/uploader/client/fileuploader.js" type="text/javascript"></script>
			<?php $this->showCustomFieldsAdmin(); ?>
			<p>
				<input name="submit" type="submit" class="submit" value="Save <?php echo IMTITLE; ?>" />
				&nbsp;&nbsp;or&nbsp;&nbsp;
				<a href="load.php?id=item_manager" class="cancel" title="Cancel">Cancel</a>
			</p>
		</form>
  <?php
	}
	
	public function processItem()
	{ 
		$id = clean_urls(to7bits($_POST['post-title'], "UTF-8"));
		$file = ITEMDATA . $id . '.xml';
		$title = $_POST['post-title'];
		$category = $_POST['category'];
		$content = safe_slash_htmll($_POST['post-content']);  

		if (!file_exists($file)) 
		{
			$date = date('j M Y');
		} 
		else 
		{
			$data = @getXML($file);
			$date = $data->date;
		}

		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$xml->addChild('title', empty($title) ? '(no title)' : $title);
		$xml->addChild('slug', $id);
		$xml->addChild('visible', true);
		$xml->addChild('date', $date);
		$xml->addChild('category', $category);
		$note = $xml->addChild('content');  
		$note->addCData($content); 
		$newse = im_customfield_def();

		foreach ($newse as $thes) 
		{
			$keys = $thes['key'];
			if(isset($_POST['post-'.$keys])) 		
			{
				if($keys != "content" && $keys != "excerpt")			
				{	
					$tmp = $xml->addChild($keys);
					$tmp->addCData($_POST['post-'.$keys]);
				}
			}
		}
		XMLsave($xml, $file);

		if (!is_writable($file))
		{
			echo '<div class="error">Unable to write '.IMTITLE.' data to file</div>';
		}
		else
		{
			echo '<div class="updated">The '.IMTITLE.' has been succesfully saved</div>';
		}

		$this->showItemsAdmin();
	}
	
	public function switchVisibleItem($id)
	{
		$file = ITEMDATA . $id . '.xml';
		if (!file_exists($file))
		{
			echo 'file dont exist';
		}
		else
		{
			$data = @getXML($file);
			if (!isset($data->visible) || $data->visible == false)
			{
				$data->visible = true;
				$action = 'unhidden';
			}
			else
			{
				$data->visible = false;
				$action = 'hidden';
			}
			XMLsave($data, $file);

			if (!is_writable($file))
			{
				echo '<div class="error">Unable to write '.IMTITLE.' data to file</div>';
			}
			else
			{
				echo '<div class="updated">The '.IMTITLE.' has been succesfully '.$action.'</div>';
			}
		}
		$this->showItemsAdmin();
	}
	
	public function switchPromotedItem($id)
	{
		$file = ITEMDATA . $id . '.xml';
		if (!file_exists($file))
		{
			echo 'file dont exist';
		}
		else
		{
			$data = @getXML($file);
			if (!isset($data->promo) || $data->promo == false)
			{
				$data->promo = true;
				$action = 'promoted';
			}
			else
			{
				$data->promo = false;
				$action = 'unpromoted';
			 }
			XMLsave($data, $file);
		 
			if (!is_writable($file))
			{
				echo '<div class="error">Unable to write '.IMTITLE.' data to file</div>';
			}
			else
			{
				echo '<div class="updated">The '.IMTITLE.' has been succesfully '.$action.'</div>';
			}
		}
		$this->showItemsAdmin();	
	}
	
	public function deleteItem($id)
	{
		$file = ITEMDATA . $id . '.xml';
		if (file_exists($file))
			unlink($file);
		if (file_exists($file))
			echo '<div class="error">Unable to delete the '.IMTITLE.'</div>';
		else
			echo '<div class="updated">The '.IMTITLE.' has been deleted</div>';
		$this->showItemsAdmin();
	}
	
	public function showEditCategories()
	{
		 global $PRETTYURLS;
		if(file_exists(ITEMDATAFILE))
			{
				$category_file = getXML(ITEMDATAFILE);
			}
		?>
		<h3>Add &amp; Manage Categories</h3>
		<form class="largeform" action="load.php?id=item_manager&category&category_edit" method="post" accept-charset="utf-8">
		  <div class="leftsec">
			<p>
			  <label for="page-url">Add New Category:</label>
			  <input class="text" type="text" name="new_category" value="" />
			</p>
		  </div>
		  <div class="clear"></div>
		  <table class="highlight">
		  <tr>
		  <th>Category Name</th><th>Delete Category</th>
		  </tr>
		  <?php
		if(file_exists(ITEMDATAFILE))
		{
			foreach($category_file->categories->category as $the_fed)
			{
				echo '
				<tr><td>'.$the_fed.'</td><td><a href="load.php?id=item_manager&category&deletecategory='.$the_fed.'">X</a></td></tr>
			';
			}
		}
		  ?>
		  </table>
		  <p>
			<span>
			  <input class="submit" type="submit" name="category_edit" value="Add Category" />
			</span>
		  </p>
		</form>
		<?php
	}

	
	public function showImSettings()
	{
		if(file_exists(ITEMDATAFILE))
		{
		  $category_file = getXML(ITEMDATAFILE);
			$file_url = $category_file->item->pageurl;
			$file_title = $category_file->item->title;
			$file_page = $category_file->item->pageurl;
			$file_page_details = $category_file->item->detailspage;
			$file_results_page = $category_file->item->resultspage;
		}
		?>
		<h3>Item Manager Settings</h3>
		<form class="largeform" action="load.php?id=item_manager&settings&settings_edit" method="post" accept-charset="utf-8">
			<div class="leftsec">
				<p>
					<label for="page-url">Choose Item Manager Title</label>
					<input type="text" class="text" name="item-title" value="<?php echo $file_title; ?>" />
				</p>
			</div>
		 <div class="rightsec">
			<p>
			  <label for="page-url">Choose Page To Display Results</label>
			  
			  <select class="text" name="page-url">

			  <?php
			  $pages = get_available_pages();
			  foreach ($pages as $page) {
				$slug = $page['slug'];
				if ($slug == $file_url)
				  echo "<option value=\"$slug\" selected=\"selected\">$slug</option>\n";
				else
				  echo "<option value=\"$slug\">$slug</option>\n";
			  }
			  ?>
			  </select>
			</p>
		  </div>
		 <div class="leftsec">
			<p>
			  <label for="page-url">Choose Page To Display <strong>Details Page</strong></label>
			  
			  <select class="text" name="detailspage">

			  <?php
			  $pages = get_available_pages();
			  foreach ($pages as $page) {
				$slug = $page['slug'];
				if ($slug == $file_page_details)
				  echo "<option value=\"$slug\" selected=\"selected\">$slug</option>\n";
				else
				  echo "<option value=\"$slug\">$slug</option>\n";
			  }
			  ?>
			  </select>
			</p>
		  </div>  
		  <div class="clear"></div>
		  <h2 style="margin-bottom:0px"><strong>Advanced Settings</strong></h2>
		 <div class="leftsec">
			<p style="margin-top:0px">
			  <h3>Results Page Coding</h3>
			  <p style="width:600px;"><strong>This Feature Should Be Used By Experianced Users Only<br/><br />
			  1. You can use any html, css, javascript, or php in this textarea<br/><br />
			  2. The Title Field Can Be Retrieved By Typing <?php highlight_string('<?php echo $data->title; ?>'); ?><br /><br />
			  3. The Category Field Can Be Retrieved By Typing <?php highlight_string('<?php echo $data->category; ?>'); ?><br /><br />
			  4. Custom Fields Can Be Retrieved By Tpying <?php highlight_string('<?php echo $data->nameofcustomfield; ?>'); ?><br /><br />
			  5. The Category Field Can Be Retrieved By Typing <?php highlight_string('<?php echo $data->category; ?>'); ?><br /><br />
			  6. The CONTENT Of The Post Can Be Retrieved By Typing <?php highlight_string('<?php echo $content; ?>'); ?><br /><br />
			  7. The URL Of The Post Can Be Retrieved By Typing <?php highlight_string('<?php echo $url; ?>'); ?><br /><br />
			  </strong></p>
		 <textarea name="resultspage">
		  <?php
		  echo stripcslashes($file_results_page);
		  ?>
		 </textarea>
			</p>
		  </div>  
			<div class="clear"></div>
		  <p>
			<span>
			  <input class="submit" type="submit" name="settings_edit" value="Submit Settings" />
			</span>
		  </p>
		</form>
		<?php
	}
	
	public function processImSettings()
	{
		$category_file = getXML(ITEMDATAFILE);
		//Page URL
		if(isset($_POST['page-url']))
		{
			$file_url = $_POST['page-url'];
		}
		elseif(isset($category_file->item->pageurl))
		{
			$file_url = $category_file->item->pageurl;
		}
		else
		{
			$file_url = ITEMSLISTPAGE;
		}
		
		//Item Title
		if(isset($_POST['item-title']))
		{
			$file_title = $_POST['item-title'];
		}
		elseif(isset($category_file->item->title))
		{
			$file_title = $category_file->item->title;
		}
		else
		{
			$file_title = IMTITLE;
		}
		
		//Details Page
		if(isset($_POST['detailspage']))
		{
			$file_page_details = $_POST['detailspage'];
		}
		elseif(isset($category_file->item->detailspage))
		{
			$file_page_details = $category_file->item->detailspage;
		}
		else
		{
			$file_page_details = ITEMPAGE;
		}
		
		//Results Page
		if(isset($_POST['resultspage']))
		{
			$file_results_page = safe_slash_html($_POST['resultspage']);
		}
		elseif(isset($category_file->item->resultspage))
		{
			$file_results_page = $category_file->item->resultspage;
		}
		else 
		{
			$file_results_page = '
			<style>
				.m_pic {
					width:160px;
					float:left;
					border:1px solid white;
					padding:1px;margin-top:0px;
				}
				.thatable tr td h2 {
					margin:5px;
					font-size:15px;
					margin-toP:6px;
					margin-top:0px;
					padding-top:0px;
				}
				.thetable {
					margin-bottom:30px;
				}
				.thetable td h2{
					font-size:17px;
				}
			</style>
			<table width="100%" class="thetable">
				<tr>
					<td class="resize_img" width="175" valign="top">
						<div><img src="<?php echo $SITEURL; ?>/data/uploads/items/<?php echo $data->image1; ?>" class="m_pic"/></div>
					</td>
					<td valign="top">
						<h2 style=""><?php echo $data->title; ?> - <span class="title_development"><?php echo $data->category; ?></span> - <a href="<?php echo $url; ?>" style="font-size:13px;">View Details</a></h2>
						<p style="margin:0px;margin-left:4px;text-align:left;">
							&nbsp;
						</p>
						<p style="margin:0px;margin-left:4px;text-align:left;">
							<?php echo $content; ?>.. <a href="<?php echo $url; ?>">Read more</a>
						</p>
					</td>
				</tr>
			</table>
			';
		}
		if(file_exists(ITEMDATAFILE))
		{	
			$category_file = getXML(ITEMDATAFILE);
		}
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
		
			$item_xml = $xml->addChild('item');
			
			//Set Title Variable And And Write To XML FIle
			$item_xml->addChild('title', $file_title);
			
			//Set Page URL Variable And Write To XML FIle
			$item_xml->addChild('pageurl', $file_url);
			
			//Set Details Page And Write To XML File
			$item_xml->addChild('detailspage', $file_page_details);
			
			//Set Results Page Coding And Write To XML File
			$note = $item_xml->addChild('resultspage');  
			$note->addCData($file_results_page); 
			
			//Add Categories
			$category = $xml->addChild('categories');
			if(file_exists(ITEMDATAFILE))
			{		
				foreach($category_file->categories->category as $the_fed)
				{
					$category_uri = $the_fed;
					if($category_uri == $_GET['deletecategory'])
					{
					
					}
					else
					{
						$category->addChild('category', $category_uri);
					}
				}
			}
			if(isset($_POST['new_category']) && $_POST['new_category'] != "")
			{
				$category->addChild('category', $_POST['new_category']);
			}	
			
		//Save XML File
		XMLsave($xml, ITEMDATAFILE);
	}
}

//Clean URL For Slug
function clean_urls($text)  {
	$text = strip_tags(lowercase($text));
	$code_entities_match = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=','.');
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','','','');
	$text = str_replace($code_entities_match, $code_entities_replace, $text);
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = rtrim($text, "-");
	return $text;
}

function to7bits($text,$from_enc="UTF-8") {
	if (function_exists('mb_convert_encoding')) {
		$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
	}
	$text = preg_replace(
	array('/&szlig;/','/&(..)lig;/','/&([aouAOU])uml;/','/&(.)[^;]*;/'),array('ss',"$1","$1".'e',"$1"),$text);
	return $text;
}

//Function To Clean Posted Content
function safe_slash_htmll($text) {
		if (get_magic_quotes_gpc()==0) 
		{		
			$text = addslashes(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
		}
		else 
		{		
			$text = htmlentities($text, ENT_QUOTES, 'UTF-8');	
		}
		return $text;
}
?>
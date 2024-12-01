<?php
class DisplayImItems
{
	public $tags;
	
	public function __construct()
	{
		//include common file
		require_once(GSPLUGINPATH.'items/common.php');
		
		//set all custom fields available
		$this->getAllImCustomFields();
		
		//set custom fields data from the individual item's xml file
		$this->getCustomfields();
	}
	
	//Get all custom fields created
	public function getAllImCustomFields()
	{
		if (file_exists(GSDATAOTHERPATH.'plugincustomfields.xml')){
			$file=GSDATAOTHERPATH."plugincustomfields.xml";
			$i=0;
			$thisfile = file_get_contents($file);
			$data = simplexml_load_string($thisfile);
			$components = $data->item;
			if (count($components) != 0) {
				foreach ($components as $component) {
					$key=$component->desc;
					//$tags['$key']['test']=$component->label;
					$this->tags[(string)$key] =$key;
					$this->tags[(string)$key]=array();
					$this->tags[(string)$key]['label']=(string)$component->label;
					$this->tags[(string)$key]['type']=(string)$component->type;
					// for furture use
					if ($component->type=="dropdown"){
						// do dropdown
						$this->tags[(string)$key]['options']=array();
						$options=$component->option;
						foreach ($options as $option) {
							$this->tags[(string)$key]['options'][]=(string)$option;
						}
					}
					$this->tags[(string)$key]['value']="";
					$i++;
				}
			}
		}	
	}
	
	//get custom fields from idividual item's xml file
	public function getCustomfields()
	{
		  if(isset($_GET['item'])) {
		  global $date;
		  $file =  ITEMDATA . $_GET['item'] . '.xml';
		  if(file_exists($file)){
		  $date = getXML($file);
		  
			 while (list($key, $val) = each($this->tags))
			 {
				$this->tags[$key]['value']=(string)$date->{$key};
			 }
		  }
		}
	}
	
	//Returns the data for custom field passed to function
	public function GetField($tag)
	{
			global $SITEURL;
			if (isset($_GET['item']) && file_exists(ITEMDATA.$_GET['item'].'.xml')){
			 if($tag == "title")
			  {	$post_item = $_GET['item'];
				  $title_data = getXML(ITEMDATA.$post_item.'.xml');
				   return $title_data->title;
			  //	echo $post_item;
			 }
			 elseif ($this->tags[$tag]['type'] == "textarea")
			  {
				  $the_tag_content = $this->tags[$tag]['value'];
				  $the_tag_content = strip_decode($the_tag_content);
				  return $the_tag_content;
			 }
			 elseif($this->tags[$tag]['type'] == "uploader")
			  {
				 return $SITEURL.'data/uploads/items/'.$this->tags[$tag]['value'];
			 }
			 else
			  {
				  return $this->tags[$tag]['value'];
			 }

			}
	}
	
	//get and filter all items
	private function filterAllItems()
	{
		$pages = array();
		$itemdir = ITEMDATA;
		$dir_handle = @opendir($itemdir);
		while ($filename = readdir($dir_handle)) {
		  if (strrpos($filename,'.xml') === strlen($filename)-4) {
			$data = getXML($itemdir . $filename);
			if (isset($data->visible)) {
			$visible = $data->visible;
			}
			else {$visible = true;}

			if (isset($data->promo)) {
			$promo = $data->promo;
			}
			else {$promo = true;}

			$pages[] = array('name' => (string) $filename, 'category' => (string) $data->category, 'visible' => $visible, 'promo' => $promo);
		  }
		}
		asort($pages);
		// sort by user function:
		return $pages;
	}
	
	//print all items
	public function printAllItems($page)
	{
		global $SITEURL;
		$data = getXML(ITEMDATA.$page['name'], 'SimpleXMLElement', LIBXML_NOCDATA);
		// $url = ($PRETTYURLS == 1) ? $SITEURL . $data->url : $SITEURL . 'index.php?id=' . $data->url;
		// Extract and filter content	 
		$content = strip_decode($data->content);
		$content = htmlspecialchars_decode($content, ENT_QUOTES);
		$content = strip_tags($content);
		$content = str_replace("&nbsp;", " ", $content);
		$content = substr($content,0,220);
		$b_url = $data->slug;
		// $url = preg_match('/\?/', $b_url) ? '&' : '?';
		$url = str_replace(' ', '-', $b_url);
		$url = $SITEURL.ITEMPAGE."/?item=".$url; //generowanie linku do strony produktu
		// Print result
		if(file_exists(ITEMSFILE))
		{	
			$item_manager_file = getXML(ITEMSFILE);	
		}
		if(isset($item_manager_file->item->resultspage))
		{
			$file_results_page = eval("?>" . strip_decode($item_manager_file->item->resultspage) . "<?php ");
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
							</p>
							<p style="margin:0px;margin-left:4px;text-align:left;">
							<?php echo $content; ?>.. <a href="<?php echo $url; ?>">Read more</a>
							</p>
						</td>
					</tr>
				</table>
			';
			$file_results_page = eval("?>" . strip_decode($file_results_page) . "<?php ");
		}
	}
	
	public function getAllItems()
	{
		$pages = $this->filterAllItems();
		if(count($pages) > 0)
		{
			foreach ($pages as $page) 
			{	 
				//parameter to check whether there is Visible
				if ($page['visible'] == true){
					if(isset($_GET['category']) && $page['category'] == urldecode($_GET['category']))
					{
						$this->printAllItems($page);
					}
					elseif(!isset($_GET['category']))
					{
						$this->printAllItems($page);
					}
				}
			}
		}
		else 
		{
			echo '<p>Sorry, your search returned no hits.</p>';
		}	
	}
	
	//Show categories for sidebar
	public function showCategories()
	{
		global $SITEURL;
		if(file_exists(ITEMSFILE))
		{
			$category_file = getXML(ITEMSFILE);
			foreach($category_file->categories->category as $the_fed)
			{	
				$category = $the_fed;
				$url = $SITEURL.$item_file_url."/?category=$category";
				echo "<li><a href=\"$url\">$category</a></li>";
			}
			echo '<br/><li><a href="'.$SITEURL.$item_file_url.'/">View All Categories</a></li>';
		}
	}
}
?>
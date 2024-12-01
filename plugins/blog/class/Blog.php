<?php
/** 
* The Blog Cass
* Handles all major methods
* 
* @return void 
*/  
class Blog 
{
	/** 
	* Construct
	* Creates data/blog_posts directory if it is not already created.
	* Creates blog category file if it is not yet created
	* Creates blog settings file if it is not yet created
	* Crates blog rss feed auto importer file if it is not yet created
	* 
	* @return void
	*/  
	public function __construct()
	{
		//Create data/blog_posts directory
		if(!file_exists(BLOGPOSTSFOLDER))
		{
			$create_post_path = mkdir(BLOGPOSTSFOLDER);
			if($create_post_path)
			{
				echo '<div class="updated">'.i18n_r(BLOGFILE.'/DATA_BLOG_DIR').'</div>';
			}
			else
			{
				echo '<div class="error"><strong>'.i18n_r(BLOGFILE.'/DATA_BLOG_DIR_ERR').'</strong><br/>'.i18n_r(BLOGFILE.'/DATA_BLOG_DIR_ERR_HINT').'</div>';
			}
		}
		if(!file_exists(BLOGCATEGORYFILE))
		{
			$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
			$create_category_file = XMLsave($xml, BLOGCATEGORYFILE);
			if($create_category_file)
			{
				echo '<div class="updated">'.i18n_r(BLOGFILE.'/DATA_BLOG_CATEGORIES').'</div>';
			}
			else
			{
				echo '<div class="error"><strong>'.i18n_r(BLOGFILE.'/DATA_BLOG_CATEGORIES_ERR').'</strong></div>';
			}
		}
		if(!file_exists(BLOGRSSFILE))
		{
			$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
			$create_rss_file = XMLsave($xml, BLOGRSSFILE);
			if($create_rss_file)
			{
				echo '<div class="updated">'.i18n_r(BLOGFILE.'/DATA_BLOG_RSS').'</div>';
			}
			else
			{
				echo '<div class="error"><strong>'.i18n_r(BLOGFILE.'/DATA_BLOG_RSS_ERR').'</strong></div>';
			}
		}
		if(!file_exists(BLOGSETTINGS))
		{
			$css_code ='.blog_post_thumbnail {&#xD;
	width:200px;&#xD;
	height:auto;&#xD;
	float:left;&#xD;
	padding-right:10px;&#xD;
	padding-bottom:10px;&#xD;
}&#xD;
&#xD;
.blog_post_container {&#xD;
	clear:both;&#xD;
}					';
			 $settings_array = array('blogurl' => "index",
									 'lang' => "en_US",
									 'excerptlength' => '350',
									 'postformat' => 'N',
									 'postperpage' => '8',
									 'recentposts' => '4',
									 'prettyurls' => 'N',
									 'autoimporter' => 'N',
									 'autoimporterpass' => 'passphrase',
									 'displaytags' => 'Y',
									 'rsstitle' => '',
									 'rssdescription' => '',
									 'comments' => '',
									 'postthumbnail' => 'N',
									 'displaydate' => 'Y',
									 'previouspage' => i18n_r(BLOGFILE.'/NEWER_POSTS'),
									 'nextpage' => i18n_r(BLOGFILE.'/OLDER_POSTS'),
									 'displaycss' => 'Y',
									 'csscode' => $css_code,
									 'rssfeedposts' => '10');
			$create_rss_file = $this->saveSettings($settings_array);
			if($create_rss_file)
			{
				echo '<div class="updated">'.i18n_r(BLOGFILE.'/BLOG_SETTINGS').' '.i18n_r(BLOGFILE.'/WRITE_OK').'</div>';
			}
			else
			{
				echo '<div class="error"><strong>'.i18n_r(BLOGFILE.'/BLOG_SETTINGS').' '. i18n_r(BLOGFILE.'/DATA_FILE_ERROR').'</strong></div>';
			}
		}
		if(!file_exists(BLOGCUSTOMFIELDS))
		{
			$custom_fields_file = BLOGPLUGINFOLDER.'inc/reserved_blog_custom_fields.xml';
      		if(!copy($custom_fields_file, BLOGCUSTOMFIELDS))
      		{
      			echo '<div class="error"><strong>Catastrophic ERROR!!!</strong> - You are going to need to copy the contents of the below file, save it as a new document namned "blog_custom_fields.xml" and then move it to the "'.GSDATAOTHERPATH.'" folder!<br/><strong>XML File To Copy:</strong> '.BLOGCUSTOMFIELDS.'</div>';
      		}
		}
	}

	/** 
	* Lists All Blog Posts
	* 
	* @param $array bool if true an array containing each posts filename and publish date will be returned instead of only the filename
	* @param $sort_dates bool if true the posts array will be sorted by post date -- THIS REQUIRES $array param TO BE TRUE
	* @return array the filenames & paths of all posts
	*/  
	public function listPosts($array=false, $sort_dates=false)
	{
		$all_posts = glob(BLOGPOSTSFOLDER . "/*.xml");
		if(count($all_posts) < 1)
		{
			return false;
		}
		else
		{
			$count = 0;			
			if($array==false)
			{
				return $all_posts;
			}
			else
			{
				foreach($all_posts as $post)
				{
					$data = getXML($post);
					$posts[$count]['filename'] = $post;
					$posts[$count]['date'] = (string) $data->date;
					$posts[$count]['category'] = (string) $data->category;
					$posts[$count]['tags'] = (string) $data->tags;
					if(isset($data->author)) { $posts[$count]['authur'] = (string) $data->author; }
					$count++;
				}
				if($sort_dates != false && $array != false)
				{
					usort($posts, array($this, 'sortDates'));  
				}
				return $posts;
			}
		}
	}

	public function filterPosts($filter, $value)
	{
		$posts = $this->listPosts(true, true);
		$count = 0;
		foreach($posts as $post)
		{
			if($filter == 'category')
			{
				if($post['category'] == $value)
				{
					$filtered_posts[$count] = $post;
				}
			}
			elseif($filter == 'tags')
			{
				if(strpos($post['tags'], $value) !== false)
				{
					$filtered_posts[$count] = $post;
				}
			}
			if($filter == 'date')
			{
				$date = date();
				$date = strtotime($date);
				if((strtotime("-$value days") < $date && $date < strtotime("-$value days")))
				{
					$filtered_posts[$count] = $post;
				}
			}
			$count++;
		}
		if(empty($filtered_posts))
		{
			$filtered_posts = array();
		}
		return $filtered_posts;
	}

	/** 
	* Get Data From Settings File
	* 
	* @param $field the node of the setting to retrieve
	* @return string requested blog settings data
	*/  
	public function getSettingsData($field)
	{
		$settingsData = getXML(BLOGSETTINGS);
		if(is_object($settingsData->$field))
		{
			return $settingsData->$field;	
		}
		else
		{
			return false;
		}
	}

	/** 
	* Get A Blog Post
	* 
	* @param $post_id the filename of the blog post to retrieve
	* @return array blog xml data
	*/  
	public function getPostData($post_id)
	{
		$post = getXML($post_id);
		return $post;
	}

	/** 
	* Saves a post submitted from the admin panel
	* 
	* @param $post_data the post data (eg: 'XML_FIELD_NAME => $POSTDATA')
	* @todo clean up this method... Not happy about it's messiness!
	* @return bool
	*/  
	public function savePost($post_data)
	{
		if ($post_data['slug'] != '')
		{
			$slug = $this->blog_create_slug($post_data['slug']);
		}
		else
		{
			$slug = $this->blog_create_slug($post_data['title']);
		}
		$file = BLOGPOSTSFOLDER . "$slug.xml";
		if($post_data['current_slug'] == '' || $post_data['current_slug'] != $post_data['slug'])
		{
			# delete old post file
			if ($post_data['current_slug'] != '')
			{
				unlink(BLOGPOSTSFOLDER . $post_data['current_slug'] . '.xml');
			}
			# do not overwrite existing files
			if (file_exists($file)) 
			{
				$count = 0;
				while(file_exists($file))
				{
					$file = BLOGPOSTSFOLDER . "$slug-" . ++$count . '.xml';
					$slug .= "-$count";
				}
			}
		}
		else
		{
			unlink(BLOGPOSTSFOLDER . $post_data['current_slug'] . '.xml');
		}


		if($post_data['date'] != '')
		{
			$date = $post_data['date'];
		} 
		else
		{
			$date = date('m/d/Y h:i:s a', time());
		}
		if($post_data['tags'] != '')
		{
			$tags = str_replace(array(' ', ',,'), array('', ','),$post_data['tags']);
		}
		else
		{
			$tags = '';
		}

		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		foreach($post_data as $key => $value)
		{
			if($key == 'current_slug' || $key == 'time')
			{

			}
			elseif($key == 'slug')
			{
				$node = $xml->addChild($key);
				$node->addCData($slug);
			}
			elseif($key == 'title')
			{
				$title = safe_slash_html($value);
				$node = $xml->addChild($key);
				$node->addCData($title);
			}
			elseif($key == 'date')
			{
				$node = $xml->addChild($key);
				$node->addCData($date);
			}
			elseif($key == 'content')
			{
  			  $content = safe_slash_html($value);
				$node = $xml->addChild($key);
				$node->addCData($content);
			}
			elseif($key == 'tags')
			{
				$node = $xml->addChild($key);
				$node->addCData($tags);
			}
			else
			{
				$node = $xml->addChild($key);
				$node->addCData($value);
			}
		}
		    $tags = str_replace(array(' ', ',,'), array('', ','), safe_slash_html($post_data['tags']));
		if (! XMLsave($xml, $file))
		{
			return false;
		}
		else
		{
			$this->createPostsCache();
			return true;
		}

	}

	/** 
	* Deletes a blog post
	* 
	* @param $post_id id of the blog post to delete
	* @return bool
	*/  
	public function deletePost($post_id)
	{
		if(file_exists(BLOGPOSTSFOLDER.$post_id.'.xml'))
		{
			$delete_post = unlink(BLOGPOSTSFOLDER.$post_id.'.xml');
			if($delete_post)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/** 
	* Saves category added or edited
	* 
	* @param $category the category name
	* @param $existing whether the category exists already
	* @todo  use $existing param to edit a category instead of deleteing it. This would also need to go through and change the category for any posts using the edited category
	* @return bool
	*/  
	public function saveCategory($category, $existing=false)
	{
		$category_file = getXML(BLOGCATEGORYFILE);
		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		foreach($category_file->category as $ind_category)
		{
			$parent_nodes_node = $xml->addChild('category');
				$parent_nodes_node->addCData($ind_category);
		}
		$parent_nodes_node = $xml->addChild('category');
			$parent_nodes_node->addCData($category);
		$add_category = XMLsave($xml, BLOGCATEGORYFILE);
		if($add_category)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/** 
	* Deletes a category
	* 
	* @param $catgory Category to delete
	* @return bool
	*/  
	public function deleteCategory($category)
	{
		$category_file = getXML(BLOGCATEGORYFILE);
		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		foreach($category_file->category as $ind_category)
		{
			if($ind_category == $category)
			{
				//Do Nothing (Deletes Category)
			}
			else
			{
				$xml->addChild('category', $ind_category);
			}
		}
		$delete_category = XMLsave($xml, BLOGCATEGORYFILE);
		if($delete_category)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/** 
	* Saves RSS feed added or edited
	* 
	* @param $new_rss array all of the posts data
	* @param $existing whether the rss is new
	* @todo  posssible add functionality of editing a feed using the $existing param. Not sure if this is even needed
	* @return bool
	*/  
	public function saveRSS($new_rss, $existing=false)
	{
		$rss_file = getXML(BLOGRSSFILE);
		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		$count = 0;
		foreach($rss_file->rssfeed as $rss_feed)
		{
			$rss_atts = $rss_feed->attributes();
			$rss = $xml->addChild('rssfeed');

			$rss->addAttribute('id', $count);

			$rss_name = $rss->addChild('feed');				
			$rss_name->addCData($rss_feed->feed);
			
			$rss_category = $rss->addChild('category');	
			$rss_category->addCData($rss_feed->category);
			$count++;
		}
		$newfeed = $xml->addChild('rssfeed');
		$newfeed->addAttribute('id', $count);
		$newfeed_name = $newfeed->addChild('feed');
		$newfeed_name->addCData($new_rss['name']);
		$newfeed_category = $newfeed->addChild('category');
		$newfeed_category->addCData($new_rss['category']);

		$add_rss = XMLsave($xml, BLOGRSSFILE);
		if($add_rss)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/** 
	* Delete RSS Feed
	* 
	* @param $feed_id RSS feed to delete
	* @return bool
	*/  
	public function deleteRSS($feed_id)
	{
		$rss_file = getXML(BLOGRSSFILE);
		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		$count = 0;
		foreach($rss_file->rssfeed as $rss_feed)
		{
			$rss_atts = $rss_feed->attributes();
			if($feed_id == $rss_atts['id'])
			{

			}
			else
			{
				$rss = $xml->addChild('rssfeed');

				$rss->addAttribute('id', $count);

				$rss_name = $rss->addChild('feed');				
				$rss_name->addCData($rss_feed->feed);

				$rss_category = $rss->addChild('category');	
				$rss_category->addCData($rss_feed->category);
			}
			$count++;
		}
		$delete_rss = XMLsave($xml, BLOGRSSFILE);
		if($delete_rss)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/** 
	* Save Blog Plugin Settings
	* 
	* @param array $post_data The array of each xml node to be added. The key for each array item will be the node and the value will be the nodes contents
	* @return bool
	*/  
	public function saveSettings($post_data)
	{

		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		foreach($post_data as $key => $value)
		{
			$parent_nodes_node = $xml->addChild($key);
				$parent_nodes_node->addCData($value);
		}
		$blog_settings = XMLsave($xml, BLOGSETTINGS);
		if($blog_settings)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/** 
	* Gets fields for blog post xml files
	* 
	* @param $array bool if the xml nodes should be returned as an array (true) or a object (null or false)
	* @todo this function will be very usefull once custom fields are implemented. For now it is here for preparation for the inevitable!
	* @return array xml nodes if $array param is true
	* @return object xml nodes if $array param is false
	*/  
	public function getXMLnodes($array=false)
	{
		$cfData = getXML(BLOGCUSTOMFIELDS);
		$blog_data = array('current_slug' => '', 'thumbnail' => '');
		foreach($cfData->item as $custom_field)
		{
			$value = (string) $custom_field->desc;
			$blog_data[$value] = '';
		}

		if($array == false)
		{
			return $blog_data = (object) $blog_data;
		}
		else
		{
			return $blog_data;
		}
  	}

	/** 
	* Generates link to blog or blog area
	* 
	* @param $query string Optionally you can provide the type of blog url you are looking for (eg: 'post', 'category', 'archive', etc..)
	* @return url to requested blog area
	*/  
	public function get_blog_url($query=FALSE) 
	{
		$Blog = new Blog;
		global $SITEURL, $PRETTYURLS;
		$blogurl = $Blog->getSettingsData("blogurl");
		$data = getXML(GSDATAPAGESPATH . $blogurl . '.xml');
		$url = find_url($blogurl, $data->parent);

		if($query) 
		{
			if($query == 'rss')
			{
				$url = $SITEURL.'plugins/blog/rss.php';
			}
			elseif($PRETTYURLS == 1 && $Blog->getSettingsData("prettyurls") == 'Y')
			{
				$url .= $query . '/';
			}
			elseif($blogurl == 'index')
			{
				$url = $SITEURL . "index.php?$query=";
			}
			else
			{
				$url = $SITEURL . "index.php?id=$blogurl&$query=";
			}
		}
		return $url;
	}

	/** 
	* Creates slug for blog posts
	* 
	* @return string the generated slug
	*/  
	public function blog_create_slug($str) 
	{
		$str = to7bit($str, 'UTF-8');
		$str = clean_url($str);
		return $str;
	}

	/** 
	* Gets available blog plugin langauges
	* 
	* @return array available langauges
	*/  
	public function blog_get_languages() 
	{
		$count = 0;
		foreach(glob(BLOGPLUGINFOLDER."lang/*.php") as $filename)
		{
			$filename = basename(str_replace(".php", "", $filename));
			$languages[$count] = $filename;
			$count++;
		}
		return $languages;
	}

	/** 
	* Create Excerpt for post
	* 
	* @param $content string the content to be excerpted
	* @param $start int the starting character to create excerpt from
	* @param $maxchars int the amount of characters excerpt should be
	* @return string The created excerpt
	*/  
	public function create_excerpt($content, $start, $maxchars)
	{
		$maxchars = (int) $maxchars;
		$content = substr($content, $start, $maxchars);
		$pos = strrpos($content, " ");
		if ($pos>0) 
		{
			$content = substr($content, $start, $pos);
		}
		$content = htmlspecialchars_decode(strip_tags(strip_decode($content)));
		$content = str_replace(i18n_r(BLOGFILE.'/READ_FULL_ARTICLE'), "", $content);
		return $content;
	}

	/** 
	* Gets and sorts archives for blog
	* 
	* @return array archives
	*/  
	public function get_blog_archives() 
	{
		$posts = $this->listPosts();
		$archives = array();
		foreach ($posts as $file) 
		{
			$data = getXML($file);
			$date = strtotime($data->date);
			$title = $this->get_locale_date($date, '%B %Y');
			$archive = date('Ym', $date);
			if (!array_key_exists($archive, $archives))
			{
				$archives[$archive] = $title;
			}
		}
		krsort($archives);
		return $archives;
	}

	/** 
	* Generates search results
	* 
	* @param $keyphrase string the keyphrase to search for
	* @return array Search results
	*/  
	public function searchPosts($keyphrase)
	{
		$keywords = @explode(' ', $keyphrase);
		$posts = $this->listPosts();
		foreach ($keywords as $keyword) 
		{
			$match = array();
			$count = 0;
			foreach ($posts as $file) 
			{
				$data = getXML($file);
				$content = $data->title . $data->content;
				$slug = $data->slug;
				if (stripos($content, $keyword) !== FALSE)
				{
					$match[$count] = $file;
				}

				$count++;
			}
			$posts = $match;
		}
		return $posts;
	}

	/** 
	* get_locale_date
	* @param $timestamp UNIX timestamp
	* @return string date according to lang
	*/  
	public function get_locale_date($timestamp, $format) 
	{
		$locale = setlocale(LC_TIME, NULL);
		setlocale(LC_TIME, $this->getSettingsData("lang"));
		$date = strftime($format, $timestamp);
		setlocale(LC_TIME, $locale);
		return $date;
	}

	/** 
	* Generates RSS Feed of posts
	* 
	* @return bool
	*/  
	public function generateRSSFeed($save=true, $filtered=false)
	{
		global $SITEURL;

		$post_array = glob(BLOGPOSTSFOLDER . "/*.xml");
		if($save == true)
		{
			$locationOfFeed = $SITEURL."rss.rss";
			$posts = $this->listPosts(true, true);
		}
		else
		{
			$locationOfFeed = $SITEURL."plugins/blog/rss.php";
			if($filtered != false)
			{
				$posts = $this->filterPosts($filtered['filter'], $filtered['value']);
			}
			else
			{
				$posts = $this->listPosts(true, true);
			}
		}

		$RSSString      = "";
		$RSSString     .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$RSSString     .= "<rss version=\"2.0\"  xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
		$RSSString     .= "<channel>\n";
		$RSSString     .= "<title>".$this->getSettingsData("rsstitle")."</title>\n";
		$RSSString     .= "<link>".$locationOfFeed."</link>\n";
		$RSSString     .= "<description>".$this->getSettingsData("rssdescription")."</description>\n";
		$RSSString     .= "<lastBuildDate>".date("D, j M Y H:i:s T")."</lastBuildDate>\n";
		$RSSString     .= "<language>".str_replace("_", "-",$this->getSettingsData("lang"))."</language>\n";

		$limit = $this->getSettingsData("rssfeedposts");
		array_multisort(array_map('filemtime', $post_array), SORT_DESC, $post_array); 
		$post_array = array_slice($post_array, 0, $limit);

		foreach ($posts as $post) 
		{
			$blog_post = simplexml_load_file($post['filename']);
			$RSSDate    = $blog_post->date;
			$RSSTitle   = $blog_post->title;
			$RSSBody 	= html_entity_decode(str_replace("&nbsp;", " ", substr(htmlspecialchars(strip_tags($blog_post->content)),0,200)));
			$ID 		= $blog_post->slug;
			$RSSString .= "<item>\n";
			$RSSString .= "\t  <title>".$RSSTitle."</title>\n";
			$RSSString .= "\t  <link>".$this->get_blog_url('post').$ID."</link>\n";
			$RSSString .= "\t  <guid>".$ID."</guid>\n";
			$RSSString .= "\t  <description>".$RSSBody."</description>\n";
			$RSSString .= "\t  <category>".$blog_post->category."</category>\n";
			$RSSString .= "</item>\n";
		}

		$RSSString  .= '<atom:link href="'.$locationOfFeed."\" rel=\"self\" type=\"application/rss+xml\" />\n";
		$RSSString .= "</channel>\n";
		$RSSString .= "</rss>\n";

		if($save==true)
		{
			if(!$fp = fopen(GSROOTPATH."rss.rss",'w'))
			{
				echo "Could not open the rss.rss file";
				exit();
			}
			if(!fwrite($fp,$RSSString))
			{
				echo "Could not write to rss.rss file";
				exit();
			}
			fclose($fp);
		}
		else
		{
			return $RSSString;
		}
	}

	/** 
	* Creates Blog Posts Cache File
	* 
	* @return bool
	*/  
	public function createPostsCache()
	{
		$posts = $this->listPosts(true, true);
		$count = 0;
		$xml = new SimpleXMLExtended('<?xml version="1.0"?><item></item>');
		foreach($posts as $post)
		{
			$data = getXML($post['filename']);
			$new_post = $xml->addChild("post");
			foreach($data as $key => $value)
			{
				$post_parent = $new_post->addChild($key);
				$post_parent->addCData($value);
			}
		}
		$save_cache = XMLsave($xml, BLOGCACHEFILE);
	}

	/** 
	* Sorts dates of blog posts (launched through usort function)
	* 
	* @param $a $b array the data to be sorted (from usort)
	* @return bool
	*/  
	public function sortDates($a, $b)
	{
		$a = strtotime($a['date']); 
		$b = strtotime($b['date']); 
		if ($a == $b) 
		{ 
			return 0; 
		} 
		else
		{  
			if($a<$b) 
			{ 
				return 1; 
			} 
			else 
			{ 
				return -1; 
			} 
		} 
	}

	public function regexReplace($content) 
	{
		$the_callback = preg_match('/{\$\s*([a-zA-Z0-9_]+)(\s+[^\$]+)?\s*\$}/', $content, $matches);
		if(isset($matches[0]))
		{
			$display_post_data = str_replace('{$ ', '', $matches[0]);
			$display_post_data = str_replace(' $}', '', $display_post_data);
			echo str_replace($matches[0],$display_post_data,$content);
		}
		else
		{
			return $content;
		}
	}

	public function getIndPostData($data, $node)
	{

	}
}
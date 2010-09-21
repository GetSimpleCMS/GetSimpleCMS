<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	theme_functions.php
* @Package:	GetSimple
* @Action:	Functions used by themes. 	
*
*****************************************************/

//****************************************************//
//** PAGE SPECIFIC FUNCTIONS                        **//
//**                                                **//
//** Functions to display specific page data.       **//
//****************************************************//
	
	function get_page_content() {
		global $content;
		exec_action('content-top');
		$content = stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
		$content = exec_filter('content',$content);
		echo $content;
		exec_action('content-bottom');
	}
	
	function get_page_excerpt($n=200, $html=false) {
		global $content;
		$content_e = stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
		$content_e = exec_filter('content',$content_e);
		
		if (!$html) {
			$content_e = strip_tags($content_e);
		}
		
		if (function_exists('mb_substr')) { 
			$content_e = trim(mb_substr($content_e, 0, $n)) . '...';
		} else {
			$content_e = trim(substr($content_e, 0, $n)) . '...';
		}

		echo $content_e;
	}

	function get_page_meta_keywords() {
		global $metak;
		echo stripslashes(htmlspecialchars_decode(@$metak, ENT_QUOTES));
	}
	
	function get_page_meta_desc() {
		global $metad;
		echo stripslashes(htmlspecialchars_decode(@$metad, ENT_QUOTES));
	}
	
	function get_page_title() {
		global $title;
		echo stripslashes(htmlspecialchars_decode($title, ENT_QUOTES));
	}
	
	function return_page_title() {
		global $title;
		return stripslashes(htmlspecialchars_decode($title, ENT_QUOTES));
	}
	
	function get_page_clean_title() {
		global $title;
		echo strip_tags(stripslashes(htmlspecialchars_decode($title, ENT_QUOTES)));
	}
	
	function get_page_slug() {
		global $url;
		echo $url;
	}
	
	function get_parent() {
		global $parent;
		echo @$parent;
	}
	
	function return_parent() {
		global $parent;
		return @$parent;
	}
	
	function return_page_slug() {
		global $url;
		return $url;
	}
	
	function get_page_date($i = "l, F jS, Y - g:i A") {
		global $date;
		global $TIMEZONE;
		if ($TIMEZONE != '') {
			if (function_exists('date_default_timezone_set')) {
				date_default_timezone_set($TIMEZONE);
			}
		}
		echo date($i, strtotime($date));
	}
	

	function get_page_url($a=false) {
		global $url;
		global $SITEURL;
		global $PRETTYURLS;
		global $parent;

		if (!$a) {
			echo find_url($url, $parent);
		} else {
			return find_url($url, $parent);
		}
		
	}
	

//****************************************************//
//** SITEWIDE FUNCTIONS                             **//
//**                                                **//
//** Functions to display sitewide data.            **//
//****************************************************//

	function get_header() {
		global $url;
		global $SITEURL;
		global $PRETTYURLS;
		global $metak;
		global $metad;
		global $title;
		global $content;
		global $parent;
		include_once('configuration.php');
		if (function_exists('mb_substr')) { 
			$description = trim(mb_substr(strip_tags(stripslashes(htmlspecialchars_decode(@$content, ENT_QUOTES))), 0, 160));
		} else {
			$description = trim(substr(strip_tags(stripslashes(htmlspecialchars_decode(@$content, ENT_QUOTES))), 0, 160));
		}
		if ($metad != '') {
			$description = stripslashes(htmlspecialchars_decode(@$metad, ENT_QUOTES));
		} else {
			$description = str_replace('"','', $description);
			$description = str_replace("'",'', $description);
			$description = preg_replace('/\n/', " ", $description);
			$description = preg_replace('/\r/', " ", $description);
			$description = preg_replace('/\t/', " ", $description);
			$description = preg_replace('/ +/', " ", $description);
		}
		$keywords = stripslashes(htmlspecialchars_decode(@$metak, ENT_QUOTES));
		echo '<meta name="description" content="'.strip_quotes($description).'" />'."\n";
		echo '	<meta name="keywords" content="'.strip_quotes($keywords).'" />'."\n";
		echo '	<link rel="canonical" href="'. get_page_url(true) .'" />'."\n";
		echo '	<meta name="generator" content="'. $site_full_name .' - '. $site_version_no .'" />'."\n";
		
		exec_action('theme-header');
	
	}

	function get_footer() {
		exec_action('theme-footer');
	}

	function get_site_url() {
		global $SITEURL;
		echo $SITEURL;
	}
	
	function get_theme_url() {
		global $SITEURL;
		global $TEMPLATE;
		echo trim($SITEURL . "theme/" . $TEMPLATE);
	}
	
	function get_site_name() {
		global $SITENAME;
		echo stripslashes($SITENAME);
	}

	function get_site_email() {
		global $EMAIL;
		echo stripslashes($EMAIL);
	}
	
	function return_site_ver() {
		include('configuration.php');
		echo $site_version_no;
	}
	
	function get_site_credits() {
		include('configuration.php');
		$site_credit_link = '<a href="'.$site_link_back_url.'" title="Open Source and Free CMS" >Powered by '.$site_full_name.'</a> Version '. $site_version_no;
		echo stripslashes($site_credit_link);
	}

function menu_data($id = null,$xml=false) {
        $menu_extract = '';
        global $PRETTYURLS;
        global $SITEURL;
        
        $path = GSDATAPAGESPATH;
        $dir_handle = @opendir($path) or die("Unable to open $path");
        $filenames = array();
        while ($filename = readdir($dir_handle)) {
            $filenames[] = $filename;
        }
        closedir($dir_handle);
        
        $count="0";
        $pagesArray = array();
        if (count($filenames) != 0) {
            foreach ($filenames as $file) {
                if ($file == "." || $file == ".." || is_dir(GSDATAPAGESPATH . $file) || $file == ".htaccess"  ) {
                    // not a page data file
                } else {
										$data = getXML(GSDATAPAGESPATH . $file);
                    if ($data->private != 'Y') {
                        $pagesArray[$count]['menuStatus'] = $data->menuStatus;
                        $pagesArray[$count]['menuOrder'] = $data->menuOrder;
                        $pagesArray[$count]['menu'] = $data->menu;
                        $pagesArray[$count]['parent'] = $data->parent;
                        $pagesArray[$count]['title'] = $data->title;
                        $pagesArray[$count]['url'] = $data->url;
                        $pagesArray[$count]['private'] = $data->private;
                        $pagesArray[$count]['pubDate'] = $data->pubDate;
                        $count++;
                    }
                }
            }
        }
        
        $pagesSorted = subval_sort($pagesArray,'menuOrder');
        if (count($pagesSorted) != 0) { 
            $count = 0;
            if (!$xml){
            foreach ($pagesSorted as $page) {
                    $text = (string)$page['menu'];
                    $pri = (string)$page['menuOrder'];
                    $parent = (string)$page['parent'];
                    $title = (string)$page['title'];
                    $slug = (string)$page['url'];
                    $menuStatus = (string)$page['menuStatus'];
                    $private = (string)$page['private'];
										$pubDate = (string)$page['pubDate'];
                    
                    $url = find_url($slug,$parent);
                    
                    $specific = array("slug"=>$slug,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);
                    
                    if ($id == $slug) { 
                        return $specific; 
                        exit; 
                    } else {
                        $menu_extract[] = $specific;
                    }
                
            } 
            return $menu_extract;
            } else {
            $xml = '<?xml version="1.0" encoding="UTF-8"?><channel>';    
            foreach ($pagesSorted as $page) {
                    $text = $page['menu'];
                    $pri = $page['menuOrder'];
                    $parent = $page['parent'];
                    $title = $page['title'];
                    $slug = $page['url'];
                    $pubDate = $page['pubDate'];
                    $menuStatus = $page['menuStatus'];
                    $private = $page['private'];
                   	
                    $url = find_url($slug,$parent);
                    
                    $xml.="<item>";
                    $xml.="<slug><![CDATA[".$slug."]]></slug>";
                    $xml.="<pubDate><![CDATA[".$pubDate."]]></pubDate>";
                    $xml.="<url><![CDATA[".$url."]]></url>";
                    $xml.="<parent><![CDATA[".$parent."]]></parent>";
                    $xml.="<title><![CDATA[".$title."]]></title>";
                    $xml.="<menuOrder><![CDATA[".$pri."]]></menuOrder>";
                    $xml.="<menu><![CDATA[".$text."]]></menu>";
                    $xml.="<menuStatus><![CDATA[".$menuStatus."]]></menuStatus>";
                    $xml.="<private><![CDATA[".$private."]]></private>";
                    $xml.="</item>";
                    
            }
            $xml.="</channel>";
            return $xml;
            }
        }
    }

	function get_component($id) {
		if (file_exists(GSDATAOTHERPATH.'components.xml')) {
			$data = getXML(GSDATAOTHERPATH.'components.xml');
			$components = $data->item;
			
			if (count($components) != 0) {
				foreach ($components as $component) {
					if ($id == $component->slug) { 
						eval("?>" . stripslashes(htmlspecialchars_decode($component->value, ENT_QUOTES)) . "<?php "); 
					}
				}
			}
		}
	}


//****************************************************//
//** FUNCTION: get_navigation()                     **//
//**                                                **//
//** Returns the main menu of the site.             **//
//****************************************************//	
	function get_navigation($currentpage) {
		
		global $PRETTYURLS;
		global $SITEURL;
		$menu = '';

		$path = GSDATAPAGESPATH;
		$dir_handle = @opendir($path) or die("Unable to open $path");
		$filenames = array();
		while ($filename = readdir($dir_handle)) {
			$filenames[] = $filename;
		}
		
		$count="0";
		$pagesArray = array();
		if (count($filenames) != 0) {
			foreach ($filenames as $file) {
				if ($file == "." || $file == ".." || is_dir(GSDATAPAGESPATH . $file) || $file == ".htaccess"  ) {
					// not a page data file
				} else {
					$data = getXML(GSDATAPAGESPATH . $file);
					if ($data->private != 'Y') {
						$pagesArray[$count]['menuStatus'] = $data->menuStatus;
						$pagesArray[$count]['menuOrder'] = $data->menuOrder;
						$pagesArray[$count]['menu'] = stripslashes(htmlspecialchars_decode($data->menu, ENT_QUOTES));
						$pagesArray[$count]['url'] = $data->url;
						$pagesArray[$count]['title'] = stripslashes(htmlspecialchars_decode($data->title, ENT_QUOTES));
						$pagesArray[$count]['parent'] = $data->parent;
						$count++;
					}
				}
			}
		}
		
		$pagesSorted = subval_sort($pagesArray,'menuOrder');
		if (count($pagesSorted) != 0) { 
			foreach ($pagesSorted as $page) {
				$sel = ''; $classes = '';
				$url_nav = $page['url'];
				
				if ($page['menuStatus'] == 'Y') { 
					if ("$currentpage" == "$url_nav") { $classes = "current ". $page['parent'] ." ". $url_nav; } else { $classes = trim($page['parent'] ." ". $url_nav); }
					if ($page['menu'] == '') { $page['menu'] = $page['title']; }
					if ($page['title'] == '') { $page['title'] = $page['menu']; }
					$menu .= '<li class="'. $classes .'" ><a href="'. find_url($page['url'],$page['parent']) . '" title="'. strip_quotes($page['title']) .'">'.$page['menu'].'</a></li>'."\n";
				}
			}
			
			
		}
		
		closedir($dir_handle);
		
		echo exec_filter('menuitems',$menu);
		#echo $menu;
	}	
?>
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/****************************************************
*
* @File: 	theme_functions.php
* @Package:	GetSimple
* @Action:	Functions used by themes. 	
*
*****************************************************/

$thisfile = file_get_contents('data/other/cp_settings.xml');
$datac = simplexml_load_string($thisfile);
$PRETTYURLS = $datac->PRETTYURLS;

//****************************************************//
//** PAGE SPECIFIC FUNCTIONS                        **//
//**                                                **//
//** Functions to display specific page data.       **//
//****************************************************//
	
	function get_page_content() {
		global $content;
		exec_action('content-top');
		echo stripslashes(htmlspecialchars_decode($content, ENT_QUOTES));
		exec_action('content-bottom');
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
		if ($parent != '' ) { $parent = tsl($parent); } 
		if ($url == 'index' ) { $urls = ''; } else { $urls = $url; }
		
		if ($PRETTYURLS == '1') {
			if (!$a) {
				echo $SITEURL . $parent . $urls;
			} else {
				return $SITEURL . $parent . $urls;
			}
		} else {
			if (!$a) {
				if ($urls != '') {
					$inter = 'index.php?id=';
				} else {
					$inter = '';
				}
				echo $SITEURL . $inter . $urls; 
				} else {
				return $SITEURL . $inter . $urls;
			}
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
		require_once('configuration.php');
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
		echo '<meta name="description" content="'.$description.'" />'."\n";
		echo '	<meta name="keywords" content="'.$keywords.'" />'."\n";
		echo '	<link rel="canonical" href="'. get_page_url(true) .'" />'."\n";
		echo '	<meta name="generator" content="'. $site_full_name .' - '. $site_version_no .'" />'."\n";
		
		exec_action('theme-head');
	
	}
	
	function get_site_url() {
		global $SITEURL;
		echo $SITEURL;
	}
	
	function get_theme_url() {
		global $SITEURL;
		global $TEMPLATE;
		echo $SITEURL . "theme/" . $TEMPLATE;
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
        
        $path = "data/pages";
        $dir_handle = @opendir($path) or die("Unable to open $path");
        $filenames = array();
        while ($filename = readdir($dir_handle)) {
            $filenames[] = $filename;
        }
        
        $count="0";
        $pagesArray = array();
        if (count($filenames) != 0) {
            foreach ($filenames as $file) {
                if ($file == "." || $file == ".." || is_dir("data/pages/".$file) || $file == ".htaccess"  ) {
                    // not a page data file
                } else {
                    $thisfile = @file_get_contents('data/pages/'.$file);
                    $data = simplexml_load_string($thisfile);
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
                    
                    if ($PRETTYURLS == '1') {
                        if ($parent != '') {$parent = tsl($parent); } 
                        if ($slug == 'index' ) { $slugs = ''; } else { $slugs = $slug; } 
                        $url = $SITEURL . @$parent . $slugs;
                    } else {
                        $url = $SITEURL .'index.php?id='.$slugs; 
                    }
                    
                    $specific = array("slug"=>$slugs,"url"=>$url,"parent_slug"=>$parent,"title"=>$title,"menu_priority"=>$pri,"menu_text"=>$text,"menu_status"=>$menuStatus,"private"=>$private,"pub_date"=>$pubDate);
                    
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
                    if ($PRETTYURLS == '1') {
                        if ($parent != '') {$parent = tsl($parent); } 
                        if ($slug == 'index' ) { $slugs = ''; } else { $slugs = $slug; }  
                        $url = $SITEURL . @$parent . $slugs;
                    } else {
                        $url = $SITEURL .'index.php?id='.$slugs; 
                    }
                    
                    $xml.="<item>";
                    $xml.="<slug><![CDATA[".$slugs."]]></slug>";
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
        
        closedir($dir_handle);
    }

	function get_component($id) {
		if (file_exists('data/other/components.xml')) {
			$thisfile = file_get_contents("data/other/components.xml");
			$data = simplexml_load_string($thisfile, NULL, LIBXML_NOCDATA);
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

		$path = "data/pages";
		$dir_handle = @opendir($path) or die("Unable to open $path");
		$filenames = array();
		while ($filename = readdir($dir_handle)) {
			$filenames[] = $filename;
		}
		
		$count="0";
		$pagesArray = array();
		if (count($filenames) != 0) {
			foreach ($filenames as $file) {
				if ($file == "." || $file == ".." || is_dir("data/pages/".$file) || $file == ".htaccess"  ) {
					// not a page data file
				} else {
					$thisfile = @file_get_contents('data/pages/'.$file);
					$data = simplexml_load_string($thisfile);
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
					if ("$currentpage" == "$url_nav") { $classes = "current ". $url_nav; } else { $classes = $url_nav; }
					if ($page['menu'] == '') { $page['menu'] = $page['title']; }
					if ($page['title'] == '') { $page['title'] = $page['menu']; }
					if ($PRETTYURLS == '1') { 
						if ($page['parent'] != '' ) { $page['parent'] = tsl($page['parent']); } 
						if ($url_nav == 'index' ) { $url_nav = ''; }
						$menu .= '<li class="'. $classes .'" ><a href="'. $SITEURL . @$page['parent'] . $url_nav .'" title="'. $page['title'] .'">'.$page['menu'] .'</a></li>'."\n";
					} else {
						$menu .= '<li class="'. $classes .'" ><a href="'. $SITEURL .'index.php?id='.$url_nav.'" title="'. $page['title'] .'">'.$page['menu'].'</a></li>'."\n";
					}
				}
			}
			
			echo $menu;
		}
		
		closedir($dir_handle);
	}	


//****************************************************//
//** FUNCTION: set_contact_page()                   **//
//**                                                **//
//** Includes the setup for a contact page.         **//
//****************************************************//
	function set_contact_page() {
		global $EMAIL;
		$style='
			<style>.pot {display:none;}</style>
		';
		echo $style;
		include('contactform.php');
	}

?>
<?php
/*
Plugin Name: anchors
Description: Adds anchors to headers
Version: 1.0
Author: Shawn Alverson
Author URI: http://www.shawnalverson.com/

*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,                  //Plugin id
	'anchors', 	                //Plugin name
	'1.0', 		                  //Plugin version
	'Shawn Alverson',           //Plugin author
	'http://shawnalverson.com/',//author website
	'Adds anchors to headings', //Plugin description
	'',                         //page type - on which admin tab to display
	''                          //main function (administration)
);

# activate filter 
add_filter('content','add_anchors'); 

# Functions
function add_anchors($contents) {
		# $contents = get_content(return_page_slug());
		$new_contents = add_header_ids($contents);
		# $toc = generate_toc($new_contents);
		# echo"filter content<pre>".$contents."</pre>";		
		return $new_contents;
}

function return_toc($slug = ""){ // returns table of contents as a list
	# slug defaults to current slug if not passed as arg
  $slug = (isset($slug) and $slug!="") ? $slug : return_page_slug();
	$page_content = get_content($slug);
	
	if($page_content) {
		# Really shouldnt do add_headers twice everytime, probably need to save this as a xml file	
		$contents = add_header_ids($page_content['content']);		
		$toc = generate_toc($contents);
		return $toc;
	}
	else{
		// echo "No content for slug " . $slug;
		return false;
	}
}

function get_toc($slug = ""){ // echos table of contents as a list
	echo return_toc($slug);
}

function content_filter_test($content){
  return "test";
}

function add_header_ids($contents){
	$dom_document = new DOMDocument();
	@$dom_document->loadHTML($contents);
		
	$xpath = new DOMXPath($dom_document);
	$headers = $xpath->query("//h1 | //h2 | //h3 | //h4 | //h5 | //h6");
	
  $dump = "";
  
	foreach ($headers as $header) {
    /*
      Populates header "-"."id" and "title" if they do not exist with headers value
    */
  
		$dump .= $header->nodeName . "|" . $header->getAttribute('class'). "|" . $header->getAttribute('id') . "|" . trim($header->nodeValue) . "<br>";
		$header_id_orig = $header->getAttribute('id');
		$header_title_orig = $header->getAttribute('title');

    $header_value = getCleanNodeValue($header);
    $header_id_new = "";
    
		if (!isset($header_id_orig) or $header_id_orig == "") {
      if($header_value!=""){
        $header_id_new = str_replace(" ","-",$header_value);			
        $header->setAttribute('id',"-".$header_id_new);	
      }
		}
    
		if (!isset($header_title_orig) or $header_title_orig == "") {
			$header_title_new = $header_value;
      if($header_title_new!=""){
        $header->setAttribute('title',$header_title_new);	
      }
    }
    
	}
	
  // removed the doctype and parent tags savehtml wants to give us
	$html_fragment = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom_document->saveHTML()));	 
	return $html_fragment;
}

function getCleanNodeValue($node){
	$node_value = "";
  $node_value = trim($node->nodeValue);
	$node_value = strip_tags($node_value);
	$node_value = stripslashes($node_value);
  return $node_value;
}

function generate_toc($contents,$type = "asc",$depth = 6,$class = null){
	# $depth not implemented
	# $class not implemented
	$list = "ul";	
	
	$dom_document = new DOMDocument();
	@$dom_document->loadHTML($contents);
	
	$xpath = new DOMXPath($dom_document);
  $query = "";
  // $classquery = "[@class='$class']"; // doesnt support multi class
  $classquery = " [contains(concat(\" \", normalize-space(@class), \" \"), \" $class \")] ";
  
  $depth = (is_numeric($depth) && intval($depth) <= 6 && intval($depth) > 0) ? $depth = $depth : $depth = 6;
  # echo "Depth: $depth <br>";
  
  for($i=1;$i<=$depth;$i++){
    $query.= $i==1 ? "//h$i" : " | //h$i";
    if(isset($class)){ $query.= $classquery ;}
  }
  
  # echo "Query: $query <br> ";
  
	$headers = $xpath->query($query);
	
  if($type=="abs"){  return get_toc_abs($headers,$list);  }
  else  return get_toc_asc($headers,$list);
  
}	

function get_toc_asc($headers,$list){
  // Ascenders Only Tree
  // Only ascending headers shown, descenders are ignored, efficient and clean menus

  $tocstr = "";
  $startlvl = 1;
	$lvl = 0;
	$prevlvl = 0;
  
  if($headers){
		foreach ($headers as $header) {
			 # echo $header->nodeName . "|" . $header->getAttribute('class'). "|" . $header->getAttribute('id') . "|" . trim($header->nodeValue) . "<br>";	 
			 $id = $header->getAttribute('id');
			 $title = $header->getAttribute('title');
			 $tag = $header->nodeName;
			 $thislvl = intval(substr($tag,'1'));
				 
			if($title!=""){
				// $title .=  "($tag) ($lvl) ($thislvl)";
				$anchor = "<a href=\"#$id\">$title</a>";
				
				// if($prevlvl == 0){ $tocstr.="<ol>"; }
				
				if($thislvl > $prevlvl){
					$tocstr.= "\n<$list>\n";
					$lvl++;
				}
				else if($thislvl < $prevlvl){
					$tocstr.="</li>\n";
					// any step down falls back to root.
					if($lvl >= 2) { $tocstr.= str_repeat("</$list>\n</li>\n", $lvl-$startlvl); }
					$lvl = $startlvl;
				}
				else{
					$tocstr.="</li>\n";
				}
				
				$tocstr.=	"<li> " . $anchor;
				$prevlvl = $thislvl;			
			}
		}
		
		$tocstr.= str_repeat("</li></$list>\n", $lvl);	
	}
	
	return $tocstr;
}	

function get_toc_abs($headers,$list){
  // Absolute Tree
  // Absolulty positioned heirarchy indentation
  
  $tocstr = "";
  $startlvl = 1;
	$lvl = 0;
	$prevlvl = 0;
    
	foreach ($headers as $header) {
		 # echo $header->nodeName . "|" . $header->getAttribute('class'). "|" . $header->getAttribute('id') . "|" . trim($header->nodeValue) . "<br>";	 
		 $id = $header->getAttribute('id');
		 $title = $header->getAttribute('title');
		 $tag = $header->nodeName;
		 $thislvl = intval(substr($tag,'1'));
		 	 
		if($title!=""){
			# $title .=  "($tag) ($lvl) ($thislvl)";
			$anchor = "<a href=\"#$id\">$title</a>";
			
      // if($prevlvl == 0){ $tocstr.="<ol>"; }
      
      if($thislvl > $prevlvl){
				if($thislvl-$prevlvl > 1 and ($thislvl > 1)){
					$tocstr.= str_repeat("<ol><li>\n", ($thislvl-$prevlvl)-1); 				
				}	
				
				$tocstr.= "\n<ol>\n";
        $lvl++;
			}
			else if($thislvl < $prevlvl){
        $tocstr.="</li>\n";

				if($prevlvl - $thislvl > 1) $tocstr.= str_repeat("</ol>\n</li>\n", ($prevlvl - $thislvl));
				else $tocstr.= "</ol></li>\n";
				

        $lvl = $startlvl;
      }
			else{
        $tocstr.="</li>\n";
			}
      
      $tocstr.=	"<li> " . $anchor;
			$prevlvl = $thislvl;			
		}
	}

  $tocstr.= str_repeat("</li></ol>\n", $thislvl);	
	return $tocstr;
}	

function get_content($page){

    $item = array();

    $path = "data/pages";
		$file = 'data/pages/'.$page.'.xml';
    $data = getXML($file);
		
		# echo"raw content <pre>" .$data->content . "</pre>";
		
		# $item['content'] = stripslashes(htmlspecialchars_decode($data->content, ENT_QUOTES));
		# $item['content'] = 	stripslashes($data->content);
		$item['content'] = strip_decode($data->content);
		
		# echo"item content<pre>" .$item['content'] . "</pre>";
		
		# global $content;
		# echo"global content<pre>".$content."</pre>";
		
   	$item['title'] = $data->title;
    $item['pubDate'] = $data->pubDate;
    $item['url'] = $data->url;
    $item['private'] = $data->private;
    $item['parent'] = $data->parent;
    $item['menuOrder'] = $data->menuOrder;
    
    return $item;
}

function unit_test_toc($count=10){
	$str = $count . " Random Headers<br/>";
	$h = 1;
	for($i=0;$i<$count;$i++){
		$h = rand(rand(1,$h),6); // a bit less random but makes better trees
		# $h = rand(1,6);
		# $h = rand($h,6);
		$str.= "<h$h class=\"toc h$h\"> H$h Heading $i</h$h>";
	}
	
	return $str;
}

// (?:(?<key>\w+)\s*\=\s*"(?<value>[^"]*(?:""[^"]*)*)") | (?:(?<key>\w+)\s*\=\s*(?<value>[^"\s]*)) | (?:(?<key>\w+)\s*)
// single quote qupport (?:(?<key>\w+)\s*\=\s*["'](?<value>[^"']*(?:["']{2}[^"']*)*)["']) | (?:(?<key>\w+)\s*\=\s*(?<value>[^"'\s]*)) | (?:(?<key>\w+)\s*)

$regex = <<<EOD
(?:(?<key>\w+)\s*\=\s*["'](?<value>[^"']*(?:["']{2}[^"']*)*)["']) | (?:(?<key>\w+)\s*\=\s*(?<value>[^"'\s]*)) | (?:(?<key>\w+)\s*)
EOD;


?>
<?php

/* 
release notes
0.3
Fix to remove extraneous head and meta charset tags.
*/

/*
Plugin Name: SA TOC Anchors
Description: Adds anchors to headers
Version: 0.3
Author: Shawn Alverson
Author URI: http://www.shawnalverson.com/

*/


$sa_url = "http://tablatronix.com/getsimple-cms/sa-toc-plugin/";

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");

# register plugin
register_plugin(
	$thisfile,                  //Plugin id
	'SA TOC / Anchors',         //Plugin name
	'0.3', 		                  //Plugin version
	'Shawn Alverson',           //Plugin author
	$sa_url,                    //author website
	'Adds Table of Contents and anchors headings', //Plugin description
	'',                         //page type - on which admin tab to display
	''                          //main function (administration)
);

# activate filter 
add_filter('content','add_toc'); 


# Init

$SA_TOC_DEBUG = false;

$allowargs = array(
  'depth' => function ($input) {
      return filter_var(intval($input), FILTER_VALIDATE_INT, array(1, 6));
  },
  'class' => function ($input) {
      return filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
  },
  'asc'   => function ($input) {
      return true;
  },
  'abs'   => function ($input) {
      return true;
  },
  'ul'    => function ($input) {
      return true;
  },
  'ol'    => function ($input) {
      return true;
  },
  'notoc' => function ($input) {
      return true;
  },
  'debug' => function ($input) {
      return true;
  },
  'demo'  => function ($input) {
      return true;
  },
);

define('SA_TOC_TRIGGER', 'sa_toc');
$sa_charsetstr = '<meta http-equiv="content-type" content="text/html; charset=utf-8">';

# Functions

function add_toc($contents) {
  GLOBAL $SA_TOC_DEBUG;
  
  $trigger = "sa_toc";
  
  $expansion = getExpansion($trigger,$contents,true);
  
  if($expansion){
    $contents = add_header_ids($contents);   
    while(getExpansion($trigger,$contents,true) == true){
      $contents = preg_replace("/\(%\s*(".$trigger.")(\s+(?:%[^%\)]|[^%])+)?\s*%\)/", process_trigger($trigger,$contents),$contents,1);       
    }  
  }
    
  return $contents;   
}
  
function filterArgs($args){
  GLOBAL $allowargs;
  
  $filteredargs = array_intersect_key($args, $allowargs);
  cleanArgs($filteredargs,$allowargs);
  return $filteredargs;
}

function cleanArgs(&$array,$filter){
  foreach($array as $key=>$value){
    if(!$filter[$key]($value)) unset($array[$key]);
  }  
}

function getExpansion($trigger,$contents,$noargs = false){

  // todo: get lookaheads and lookbehind implemented so you can put non executing code in pages for examples.
  
  // $lookb = "<code>";
  // $looka = "</code>";
  
  // $pattern = "/(?<!".$lookb.")\(%\s*(".$trigger.")(\s+(?:%[^%\)]|[^%])+)?\s*%\)(?!".$looka.")/";
  // $pattern = "/(?<=".$look.")\(%\s*(".$trigger.")(\s+(?:%[^%\)]|[^%])+)?\s*%\)(?=".$look.")/";
  $pattern = "/\(%\s*(".$trigger.")(\s+(?:%[^%\)]|[^%])+)?\s*%\)/";
  
  if(preg_match($pattern,$contents,$matches)){
    # echo "<h2>Trigger was Found</h2>"; print_r($matches);
    if(!empty($matches[2]) && !$noargs){
      // get arguments
      # echo "<h2>Trigger has arguments</h2>";      
      return array(true,'args'=>get_args(trim($matches[2])) );
    }
    else
    {
      return array(true);
    }
  }
  
}

function get_args($str){
  /*
    Gets key value pairs from a string
    pairs are deimited by space

    values can be defined as either
      key=value_no_spaces
      key='value spaces'
      key = "value spaces"
      key
     * spaces in the pair assigment do not matter
  */
  
  // TODO: make delimiters variable

  $regex = <<<EOD
  /(?J)(?:(?P<key>\w+)\s*\=\s*["'](?P<value>[^"']*(?:["']{2}[^"']*)*)["']) | (?:(?P<key>\w+)\s*\=\s*(?<value>[^"'\s]*)) | (?:(?P<key>\w+)\s*)/ix
EOD;
  
  $args = Array();
  
  preg_match_all($regex,$str,$argsraw,PREG_SET_ORDER);
  
  if($argsraw){
    $cnt = count($argsraw);
    for($i=0;$i<$cnt;$i++){
      # echo $argsraw[$i]['key'] . " = " . $argsraw[$i]['value'] . "<br>";
      $args[strtolower($argsraw[$i]['key'])] = $argsraw[$i]['value'];
    }
  }  
  
  return $args;
}   


// deprecated
function get_toc($slug = ""){ // returns table of contents as a list
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

// deprecated
function print_toc($slug = ""){ // echos table of contents as a list
	echo return_toc($slug);
}


function add_header_ids($contents){
	GLOBAL $sa_charsetstr;
  // Adds ids and titles to all headings so they can be used as anchors
	$dom_document = new DOMDocument();
  // $dom_document = new DOMDocument('1.0', 'utf-8');
  // mb_convert_encoding($htmlUTF8Page, 'HTML-ENTITIES', "UTF-8");   
	@$dom_document->loadHTML($sa_charsetstr . $contents);
		
	$xpath = new DOMXPath($dom_document);
	$headers = $xpath->query("//h1 | //h2 | //h3 | //h4 | //h5 | //h6");
	
  $dump = "";
  
	foreach ($headers as $header) {
    /*
      Populates header id "-"."id" and "title" if they do not exist, with headers value
    */
  
		$dump .= $header->nodeName . "|" . $header->getAttribute('class'). "|" . $header->getAttribute('id') . "|" . trim($header->nodeValue) . "<br>";
		$header_id_orig = $header->getAttribute('id');
		$header_title_orig = $header->getAttribute('title');

    $header_value = getCleanNodeValue($header);
    $header_id_new = "";
    
    # echo $dump;
    
    # echo "<pre>".bin2hex($header_value)."</pre>";
    
		if (!isset($header_id_orig) or $header_id_orig == "") {
      if($header_value!="" && $header_value!=" "){
        $header_id_new = str_replace(" ","-",$header_value);			
        $header->setAttribute('id',"-".$header_id_new);	// todo: might add some protection here like truncation 
      }
		}
    
		if (!isset($header_title_orig) or $header_title_orig == "") {
			$header_title_new = $header_value;
      if($header_title_new!="" && $header_title_new!=" "){
        $header->setAttribute('title',$header_title_new);	
      }
    }
    
	}
	
  // removed the doctype,head, meta and parent tags savehtml wants to give us
	$html_fragment = preg_replace('/^<!DOCTYPE.+?>|<head.*?>(.*)?<\/head>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom_document->saveHTML()));	 
	return $html_fragment;
}

function getCleanNodeValue($node){
	$node_value = "";
  $node_value = trim($node->nodeValue);
  $node_value = htmltrim($node_value);
	$node_value = strip_tags($node_value);
	$node_value = stripslashes($node_value);
  return $node_value;
}

function htmltrim($string)
{
  $pattern = '(?:[ \t\n\r\x0B\x00\x{A0}\x{AD}\x{2000}-\x{200F}\x{201F}\x{202F}\x{3000}\x{FEFF}]|&nbsp;|<br\s*\/?>)+';
  return preg_replace('/^' . $pattern . '|' . $pattern . '$/u', '', $string);
}

function process_trigger($trigger,$contents){
  GLOBAL $SA_TOC_DEBUG;
  
  $expansion = getExpansion($trigger,$contents);
  $args = array();
  
  if(!empty($expansion['args'])){
    $args = filterArgs($expansion['args']);  
    if(isset($args['debug'])) $SA_TOC_DEBUG = true;  
  }
  
  return generate_toc_w_args($contents,$args);
}

function generate_toc_w_args($contents,$args = Array()){
  // For calling with argument array instead of individual arguments.
  // debugArray($args);

  $type = 'asc';
  $depth = 6;
  $class = null;
  $list = "ul";
  
  if(isset($args['ul']))  $list = 'ul';
  if(isset($args['ol']))  $list = 'ol';
  if(isset($args['abs']))  $type = 'abs';
  if(isset($args['asc']))  $type = 'asc';
  if(!empty($args['depth']))  $depth = $args['depth'];
  if(!empty($args['class']))  $class = $args['class'];
  
  return generate_toc($contents,$type,$depth,$class,$list); 
}


function generate_toc($contents,$type = "asc",$depth = 6,$class = null,$list="ul"){
  GLOBAL $SA_TOC_DEBUG,$sa_charsetstr;
  
  if($SA_TOC_DEBUG) debugArray(compact(explode(' ', 'type depth class list')));
	
	$dom_document = new DOMDocument();
	@$dom_document->loadHTML($sa_charsetstr . $contents);	
	
	$xpath = new DOMXPath($dom_document);
  $query = "";
  // $classquery = "[@class='$class']"; // deprecated - doesnt support multi class, new code below
  $classquery = " [contains(concat(\" \", normalize-space(@class), \" \"), \" $class \")] ";
  
  // clamp that shit down
  $depth = (is_numeric($depth) && intval($depth) <= 6 && intval($depth) > 0) ? $depth = $depth : $depth = 6;
  # echo "Depth: $depth <br>";
  
  // add class filter to queries
  for($i=1;$i<=$depth;$i++){
    $query.= $i==1 ? "//h$i" : " | //h$i";
    if(isset($class)){ $query.= $classquery ;}
  }
  
  # echo "Query: $query <br> ";
  
	$headers = $xpath->query($query);
	  
  if($headers->length>0){
    if($type=="abs"){  return get_toc_abs($headers,$list);  }
    else  return get_toc_asc($headers,$list);
  }
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
					$tocstr.= str_repeat("<$list><li>\n", ($thislvl-$prevlvl)-1); 				
				}	
				
				$tocstr.= "\n<$list>\n";
        $lvl++;
			}
			else if($thislvl < $prevlvl){
        $tocstr.="</li>\n";

				if($prevlvl - $thislvl > 1) $tocstr.= str_repeat("</$list>\n</li>\n", ($prevlvl - $thislvl));
				else $tocstr.= "</$list></li>\n";
				

        $lvl = $startlvl;
      }
			else{
        $tocstr.="</li>\n";
			}
      
      $tocstr.=	"<li> " . $anchor;
			$prevlvl = $thislvl;			
		}
	}

  $tocstr.= str_repeat("</li></$list>\n", $thislvl);	
	return $tocstr;
}	

function get_content($page){
    // not used
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
	$str = "<i>" . $count . " Random Headers</i><br/><br/>";
	$h = 1;
	for($i=0;$i<$count;$i++){
		# $h = rand(rand(1,$h),6); // a bit less random but makes better trees
		$h = rand(1,6);
		# $h = rand($h,6);
		$str.= "<h$h class=\"toc h$h\"> ". ($i+1) .". Heading $h</h$h>";
	}
	
	return $str;
}


function content_filter_test($content){
  return "test";
}


function debugArray($array,$print=true){
   $x = "";
   $x.=print_r('<pre>',!$print);
   $x.=print_r($array,!$print);
   $x.=print_r('</pre>',!$print); 
   if(!$print) return $x;
}       


?>

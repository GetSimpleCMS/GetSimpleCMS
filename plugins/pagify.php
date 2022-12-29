<?php
/*
Plugin Name: Pagify
Description: Split long content into pages
Version: 1.4
Author: Martin Vlcek
Author URI: http://mvlcek.bplaced.net

To split a page into chunks, either call pagify_set_size(<size>) from your template
or add a tag/keyword "_pagify <size>" to your page, e.g. "poem, _pagify 500".
<size> can contain a unit, e.g. 1000 or 1000b (1000 bytes), 20w (20 words), 
100c (100 characters), 3p (3 paragraphs = top level tags).
To split a page on <hr>, just add a tag "_pagify" without a size.
To see the complete page, add a parameter "complete" to the URL.

To have fancy URLs, put the following line into gsconfig.php
  define('PAGIFY_SEPARATOR',';');
and add a rule like the following in the root .htaccess
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule /?([A-Za-z0-9_-]+);(\d+)/?$ index.php?id=$1&page=$2 [QSA,L]
Fancy URLs will not work on the home page.
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");
$pagify_size = null;  // page size for each of the pages
$pagify_pages = 5;    // number of page links to show around current page
$pagify = true;

# register plugin
register_plugin(
	$thisfile, 
	'Pagify', 	
	'1.4', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Split long content into pages',
	'',
	''  
);

# activate filter
add_filter('content','pagify');

function pagify_set_size($size, $pages=5) {
  global $pagify_size, $pagify_pages;
  $pagify_size = $size;
  $pagify_pages = $pages;
}

function pagify_off() {
  global $pagify;
  $pagify = false;
}

function pagify_on() {
  global $pagify;
  $pagify = true;
}

function pagify($content) {
  global $pagify, $pagify_size, $pagify_pages, $metak;
  if (!$pagify) return $content;
  # get size and unit of content
  // check tags
  $tags = preg_split("/\s*,\s*/", $metak);
  foreach ($tags as $tag) {
    if (substr($tag,0,strlen('_pagify')) == '_pagify') {
      $pagify_size = trim(substr($tag,strlen('_pagify')));
      if (!$pagify_size) $pagify_size = 'hr';
    }
  }
  if (isset($_REQUEST['complete'])) {
    if ($pagify_size == 'hr') {
      // remove hr:
      return join("\r\n", preg_split("@\s*<hr\s*/?>\s*@i", $content));
    } else {
      return $content;
    }
  } else {
    $pageNum = isset($_REQUEST['page']) ? @((int) $_REQUEST['page']) - 1 : 0;
    return return_pagify_content($content, $pagify_size, $pageNum);
  }
}
 
# return the $pageNum page of size $pageSize of $content including the paging navigation
#  $content: the content to pagify
#  $pageSize: size and optional unit, e.g. '1000' or '1000b' (100 bytes), '20w' (20 words), 100c (100 characters), 3p (3 paragraphs) or hr to split on <hr>
#  $pageNum: page number to display
#  $link: link to go to other pages, must include a placeholder '%PAGE%' for the page number (starting with 0)
function return_pagify_content($content, $pageSize, $pageNum=0, $link=null) {
  if (!preg_match('/^hr|(\d+)\s*([a-zA-Z]*)$/',$pageSize,$match)) return $content;
  if ($pageSize == 'hr') {
    $pages = preg_split("@\s*<hr\s*/?>\s*@i", $content);
  } else {
    $size = (int) $match[1];
    $unit = @$match[2] ? strtolower($match[2]) : 'b';
    if ($size <= 0) return $content;
    # split content into pages  
    $ismb = function_exists('mb_strlen');
    $pages = array();
    $tagStack = array();
    $pageStart = 0;
    $pos = 0;
    $currsize = 0;
    while (($lpos = strpos($content,'<',$pos)) !== false) {
      if ($unit == 'c') { # count characters
        $text = html_entity_decode(trim(substr($content,$pos,$lpos-$pos)),ENT_QUOTES,'UTF-8');
        $currsize += $ismb ? mb_strlen($text) : strlen($text);
      } else if ($unit == 'w') { # count words
        $spaces = preg_match_all('/\s+/',trim(substr($content,$pos,$lpos-$pos)),$matches);
        if ($spaces > 0) $currsize += $spaces + 1;
      } else if ($unit == 'b') { # bytes
        $currsize = $lpos - $pageStart;
      }
      if (!$tagStack && $currsize >= $size) {
        array_push($pages, substr($content, $pageStart, $lpos - $pageStart));
        $pageStart = $lpos;
        $currsize = 0;
      }
      $gpos = strpos($content,'>',$lpos+1);
      if ($gpos !== false) {
        $tag = substr($content,$lpos,$gpos-$lpos+1);
        $spos = strpos($tag,' ');
        if (substr($tag,1,1) == '/') {
          $tagname = $spos !== false ? substr($tag,2,$spos-2) : substr($tag,2,strlen($tag)-3);
          while ($tagStack && array_pop($tagStack) != $tagname);
          if (!$tagStack) {
            if ($unit == 'p') { # increment paragraphs
              $currsize += 1;
            } else if ($unit == 'b') { # bytes
              $currsize = $gpos - $pageStart + 1;
            }
            if ($currsize >= $size) {
              array_push($pages, substr($content, $pageStart, $gpos - $pageStart + 1));
              $pageStart = $gpos+1;
              $currsize = 0;
            }
          }
        } else if (substr($tag,strlen($tag)-2,1) == '/') {
          $tagname = $spos !== false ? substr($tag,1,$spos-1) : substr($tag,1,strlen($tag)-3);
        } else {
          $tagname = $spos !== false ? substr($tag,1,$spos-1) : substr($tag,1,strlen($tag)-2);
          array_push($tagStack, $tagname);
        }
        $pos = $gpos+1;
      } else {
        $pos = strlen($content);
      }
    }
    if (strlen($content) > $pageStart && trim(substr($content, $pageStart))) {
      array_push($pages, substr($content, $pageStart));
    } else if (strlen($content) == 0) {
      array_push($pages, '');
    }
  }
  return $pages[$pageNum] . "\n" . return_pagify_navigation(count($pages), $pageNum, $link);
}

# return the paging navigation
#  $numPages: total number of pages
#  $pageNum: page number to display
#  $link: link to go to other pages, must include a placeholder '%PAGE%' for the page number (starting with 0)
function return_pagify_navigation($numPages, $pageNum=0, $link=null, $link1=null) {
  global $pagify_pages;
  if ($pageNum < 0 && $pageNum >= $numPages) $pageNum = 0;
  $pagingHtml = '';
  if ($numPages > 1) {
    if (function_exists('i18n_init')) {
      global $language;
      i18n_merge('pagify', substr($language,0,2)) || i18n_merge('pagify', 'en');
    } else {  
      global $LANG;
      i18n_merge('pagify', substr($LANG,0,2)) || i18n_merge('pagify', 'en');
    }
    if ($link == null) {
      $link1 = function_exists('get_i18n_page_url') ? get_i18n_page_url(true) : get_page_url(true);
      if (defined('PAGIFY_SEPARATOR')) {
        preg_match('/^([^\?]*[^\?\/])(\/?(\?.*)?)$/', $link1, $match);
        $link = htmlspecialchars($match[1].PAGIFY_SEPARATOR.'%PAGE%'.@$match[2]);
      } else {
        $link .= !str_contains($link1,'?') ? '?page=%PAGE%' : '&page=%PAGE%';
      }
    }
    $link = htmlspecialchars($link);
    $pagingHtml .= '<div class="paging">';
    if ($pageNum > 0) {
      $pagingHtml .= ' <span class="first active"><a href="'.($link1 ? $link1 : str_replace('%PAGE%',1,$link)).'" title="'.i18n_r('pagify/TITLE_FIRST').'">'.i18n_r('pagify/FIRST').'</a></span>';
      $pagingHtml .= ' <span class="previous active"><a href="'.($link1 && $pageNum == 1 ? $link1 : str_replace('%PAGE%',$pageNum,$link)).'" title="'.i18n_r('pagify/TITLE_PREVIOUS').'">'.i18n_r('pagify/PREVIOUS').'</a></span>';
    } else {
      $pagingHtml .= '<span class="first inactive">'.i18n_r('pagify/FIRST').'</span>';
      $pagingHtml .= ' <span class="previous inactive">'.i18n_r('pagify/PREVIOUS').'</span>';
    }
    $min = $pageNum - (int) ($pagify_pages/2);
    $max = $pageNum + (int) ($pagify_pages/2);
    if ($min < 0) {
      $min = 0;
      $max = min($numPages-1, $pagify_pages-1);
    } else if ($max >= $numPages) {
      $max = $numPages-1;
      $min = max(0, $max - $pagify_pages + 1);
    }
    if ($min > 0) $pagingHtml .= ' <span class="ellipsis">'.i18n_r('pagify/ELLIPSIS').'</span>';
    for ($i=$min; $i<=$max; $i++) {
      if ($i == $pageNum) {
        $pagingHtml .= ' <span class="current">'.($i+1).'</span>';
      } else {
        $t = i18n_r('pagify/TO_PAGE');
        $pagingHtml .= ' <span><a href="'.($link1 && $i == 0 ? $link1 : str_replace('%PAGE%',$i+1,$link)).'" title="'.(trim($t) ? $t.($i+1) : '').'">'.($i+1).'</a></span>';
      }
    }
    if ($max < $numPages-1) $pagingHtml .= ' <span class="ellipsis">'.i18n_r('pagify/ELLIPSIS').'</span>';
    if ($pageNum < $numPages-1) {
      $pagingHtml .= ' <span class="next active"><a href="'.str_replace('%PAGE%',$pageNum+2,$link).'" title="'.i18n_r('pagify/TITLE_NEXT').'">'.i18n_r('pagify/NEXT').'</a></span>';
      $pagingHtml .= ' <span class="last active"><a href="'.str_replace('%PAGE%',$numPages,$link).'" title="'.i18n_r('pagify/TITLE_LAST').'">'.i18n_r('pagify/LAST').'</a></span>';
    } else {
      $pagingHtml .= ' <span class="next inactive">'.i18n_r('pagify/NEXT').'</span>';
      $pagingHtml .= ' <span class="last inactive">'.i18n_r('pagify/LAST').'</span>';
    }
    $pagingHtml .= ' </div>';
  }
  return $pagingHtml;
}

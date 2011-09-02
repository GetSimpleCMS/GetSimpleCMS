<?php 
/**
 * Page Edit Action
 *
 * Code to either create or edit a page. This is the action page  
 * for the form on edit.php	
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// check form referrer - needs siteurl and edit.php in it. 
if (isset($_SERVER['HTTP_REFERER'])) {
	if ( !(strpos(str_replace('http://www.', '', $SITEURL), $_SERVER['HTTP_REFERER']) === false) || !(strpos("edit.php", $_SERVER['HTTP_REFERER']) === false)) {
		echo "<b>Invalid Referer</b><br />-------<br />"; 
		echo 'Invalid Referer: ' . htmlentities($_SERVER['HTTP_REFERER'], ENT_QUOTES);
		die('Invalid Referer');
	}
}

login_cookie_check();
	
if (isset($_POST['submitted'])) {
	
	// check for csrf
	if (!defined('GSNOCSRF') || (GSNOCSRF == FALSE) ) {
		$nonce = $_POST['nonce'];
		if(!check_nonce($nonce, "edit", "edit.php")) {
			die("CSRF detected!");	
		}
	}
	
	if ( $_POST['post-title'] == '' )	{
		redirect("edit.php?upd=edit-err&type=".urlencode(i18n_r('CANNOT_SAVE_EMPTY')));
	}	else {
		
		$url="";$title="";$metad=""; $metak="";	$cont="";
		
		// is a slug provided?
		if ($_POST['post-id']) { 
			$url = $_POST['post-id'];
			if (isset($i18n['TRANSLITERATION']) && is_array($translit=$i18n['TRANSLITERATION']) && count($translit>0)) {
				$url = str_replace(array_keys($translit),array_values($translit),$url);
			}
			$url = to7bit($url, "UTF-8");
			$url = clean_url($url); //old way
		} else {
			if ($_POST['post-title'])	{ 
				$url = $_POST['post-title'];
				if (isset($i18n['TRANSLITERATION']) && is_array($translit=$i18n['TRANSLITERATION']) && count($translit>0)) {
					$url = str_replace(array_keys($translit),array_values($translit),$url);
				}
				$url = to7bit($url, "UTF-8");
				$url = clean_url($url); //old way
			} else {
				$url = "temp";
			}
		}
	
	
		//check again to see if the URL is empty
		if ( $url == '' )	{
			redirect("edit.php?upd=edit-err&type=".urlencode(i18n_r('CANNOT_SAVE_EMPTY')));
		}
		
		
		// was the slug changed on an existing page?
		if ( isset($_POST['existing-url']) ) {
			if ($_POST['post-id'] != $_POST['existing-url']){
				// dont change the index page's slug
				if ($_POST['existing-url'] == 'index') {
					$url = $_POST['existing-url'];
					redirect("edit.php?id=". urlencode($_POST['existing-url']) ."&upd=edit-index&type=edit");
				} else {
					exec_action('changedata-updateslug');
					updateSlugs($_POST['existing-url']);
					$file = GSDATAPAGESPATH . $url .".xml";
					$existing = GSDATAPAGESPATH . $_POST['existing-url'] .".xml";
					$bakfile = GSBACKUPSPATH."pages/". $_POST['existing-url'] .".bak.xml";
					copy($existing, $bakfile);
					unlink($existing);
				} 
			} 
		}
		
		$file = GSDATAPAGESPATH . $url .".xml";
		
		// format and clean the responses
		if(isset($_POST['post-title'])) 			{	$title = safe_slash_html($_POST['post-title']);	}
		if(isset($_POST['post-metak'])) 			{	$metak = safe_slash_html($_POST['post-metak']);	}
		if(isset($_POST['post-metad'])) 			{	$metad = safe_slash_html($_POST['post-metad']);	}
		if(isset($_POST['post-author'])) 			{	$author = safe_slash_html($_POST['post-author']);	}
		if(isset($_POST['post-template'])) 		{ $template = $_POST['post-template']; }
		if(isset($_POST['post-parent'])) 			{ $parent = $_POST['post-parent']; }
		if(isset($_POST['post-menu'])) 				{ $menu = safe_slash_html($_POST['post-menu']); }
		if(isset($_POST['post-menu-enable'])) { $menuStatus = "Y"; } else { $menuStatus = ""; }
		if(isset($_POST['post-private']) ) 		{ $private = safe_slash_html($_POST['post-private']); }
		if(isset($_POST['post-content'])) 		{	$content = safe_slash_html($_POST['post-content']);	}
		if(isset($_POST['post-menu-order'])) 	{ 
			if (is_numeric($_POST['post-menu-order'])) 
			{
				$menuOrder = $_POST['post-menu-order']; 
			} 
			else 
			{
				$menuOrder = "0";
			}
		}		
		//check to make sure we dont overwrite any good files upon create
		if ( file_exists($file) && ($url != $_POST['existing-url']) ) {
			$count = "1";
			$file = GSDATAPAGESPATH . $url ."-".$count.".xml";
			while ( file_exists($file) ) {
				$count++;
				$file = GSDATAPAGESPATH . $url ."-".$count.".xml";
			}
			$url = $url .'-'. $count;
		}

		
		// if we are editing an existing page, create a backup
		if ( file_exists($file) ) 
		{
			$bakfile = GSBACKUPSPATH."pages/". $url .".bak.xml";
			copy($file, $bakfile);
		}
		
		
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$xml->addChild('pubDate', date('r'));

		$note = $xml->addChild('title');
		$note->addCData($title);
		
		$note = $xml->addChild('url');
		$note->addCData($url);
		
		$note = $xml->addChild('meta');
		$note->addCData($metak);
		
		$note = $xml->addChild('metad');
		$note->addCData($metad);
		
		$note = $xml->addChild('menu');
		$note->addCData($menu);
		
		$note = $xml->addChild('menuOrder');
		$note->addCData($menuOrder);
		
		$note = $xml->addChild('menuStatus');
		$note->addCData($menuStatus);
		
		$note = $xml->addChild('template');
		$note->addCData($template);
		
		$note = $xml->addChild('parent');
		$note->addCData($parent);
		
		$note = $xml->addChild('content');
		$note->addCData($content);
		
		$note = $xml->addChild('private');
		$note->addCData($private);
		
		$note = $xml->addChild('author');
		$note->addCData($author);

		exec_action('changedata-save');
		if (isset($_POST['autosave']) && $_POST['autosave'] == 'true') {
			XMLsave($xml, GSAUTOSAVEPATH.$url);
		} else {
			XMLsave($xml, $file);
		}
		
		//ending actions
		exec_action('changedata-aftersave');
		generate_sitemap();
		
		// redirect user back to edit page 
		if (isset($_POST['autosave']) && $_POST['autosave'] == 'true') {
			echo 'OK';
		} else {
			
			if ($_POST['redirectto']!='') {
				$redirect_url = $_POST['redirectto'];
			} else {
				$redirect_url = 'edit.php';
			}
			
			if ($url == $_POST['existing-url']) {
				redirect($redirect_url."?id=". $url ."&upd=edit-success&type=edit");
			} else {
				redirect($redirect_url."?id=". $url ."&old=".$_POST['existing-url']."&upd=edit-success&type=edit");
			}
		}
	}
} else {
	redirect('pages.php');
}
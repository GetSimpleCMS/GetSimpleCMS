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

$autoSaveDraft = false; // auto save to autosave drafts

$bakpagespath = GSBACKUPSPATH .getRelPath(GSDATAPAGESPATH,GSDATAPATH); // backups/pages/					

login_cookie_check();

// check form referrer - needs siteurl and edit.php in it. 
if (isset($_SERVER['HTTP_REFERER'])) {
	if ( !(strpos(str_replace('http://www.', '', $SITEURL), $_SERVER['HTTP_REFERER']) === false) || !(strpos("edit.php", $_SERVER['HTTP_REFERER']) === false)) {
		echo "<b>Invalid Referer</b><br />-------<br />"; 
		echo 'Invalid Referer: ' . htmlentities($_SERVER['HTTP_REFERER'], ENT_QUOTES);
		die('Invalid Referer');
	}
}

if (isset($_POST['submitted'])) {
	check_for_csrf("edit", "edit.php");	
	
	if ( trim($_POST['post-title']) == '' )	{
		redirect("edit.php?upd=edit-error&type=".urlencode(i18n_r('CANNOT_SAVE_EMPTY')));
	}	else {
		
		$url="";$title="";$metad=""; $metak="";	$cont="";
		
		// is a slug provided?
		if ($_POST['post-id']) { 
			$url = trim($_POST['post-id']);
			if (isset($i18n['TRANSLITERATION']) && is_array($translit=$i18n['TRANSLITERATION']) && count($translit>0)) {
				$url = str_replace(array_keys($translit),array_values($translit),$url);
			}
			$url = to7bit($url, "UTF-8");
			$url = clean_url($url); //old way
		} else {
			if ($_POST['post-title'])	{ 
				$url = trim($_POST['post-title']);
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
		if ( trim($url) == '' )	{
			$url = 'temp';
		}
		
		$oldslug = "";

		// was the slug changed on an existing page?
		if ( isset($_POST['existing-url']) ) {
			$oldslug = $_POST['existing-url'];
			if ($_POST['post-id'] != $oldslug){
				// dont change the index page's slug
				if ($oldslug == 'index') {
					$url = $oldslug;
					redirect("edit.php?id=". urlencode($oldslug) ."&upd=edit-index&type=edit");
				} else {
					exec_action('changedata-updateslug');
					updateSlugs($oldslug);
					// do backup
					$file = GSDATAPAGESPATH . $url .".xml";
					$existing = GSDATAPAGESPATH . $oldslug .".xml";
					$bakfile = $bakpagespath. $oldslug .".bak.xml";
					copy($existing, $bakfile); // copy to backup folder
					unlink($existing); // delete page, wil resave new one here
				} 
			} 
		}
		
		$file = GSDATAPAGESPATH . $url .".xml";
		
		// format and clean the responses
		// content
		if(isset($_POST['post-title'])) 			{ $title       = safe_slash_html($_POST['post-title']);	}
		if(isset($_POST['post-titlelong']))			{ $titlelong   = safe_slash_html($_POST['post-titlelong']);	}
		if(isset($_POST['post-summary']))			{ $summary     = safe_slash_html($_POST['post-summary']);	}
 		if(isset($_POST['post-content'])) 			{ $content     = safe_slash_html($_POST['post-content']); }
 		// options
 		if(isset($_POST['post-author'])) 			{ $author      = safe_slash_html($_POST['post-author']);	}
 		if(isset($_POST['post-template'])) 			{ $template    = $_POST['post-template']; }
 		if(isset($_POST['post-parent'])) 			{ $parent      = $_POST['post-parent']; }
 		if(isset($_POST['post-menu'])) 				{ $menu        = safe_slash_html($_POST['post-menu']); }
 		if(isset($_POST['post-menu-enable'])) 		{ $menuStatus  = "Y"; } else { $menuStatus = ""; }
 		if(isset($_POST['post-menu-order'])) 		{ $menuOrder   = is_numeric($_POST['post-menu-order']) ? $_POST['post-menu-order'] : "0"; }
 		if(isset($_POST['post-private']) ) 			{ $private     = safe_slash_html($_POST['post-private']); }
 		// meta
		if(isset($_POST['post-metak'])) 			{ $meta        = $metak = safe_slash_html($_POST['post-metak']);	}
		if(isset($_POST['post-metad'])) 			{ $metad       = safe_slash_html($_POST['post-metad']);	}
		
		//robots
		if(isset($_POST['post-metar-noindex']))	 	$metarNoIndex   = 1;
		else $metarNoIndex = 0; 
		if(isset($_POST['post-metar-nofollow']))	$metarNoFollow  = 1;
		else $metarNoFollow = 0; 
		if(isset($_POST['post-metar-noarchive']))	$metarNoArchive = 1;
		else $metarNoArchive = 0; 

		// If saving a new file do not overwrite existing, get next incremental filename, file-count.xml
		if ( (file_exists($file) && $url != $oldslug) ||  in_array($url,$reservedSlugs) ) {
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
			$bakfile = $bakpagespath. $url .".bak.xml";
			copy($file, $bakfile);
		}
		
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$xml->addChild('pubDate', date('r'));

		$fields = array(
			'title',
			'titlelong',
			'summary',
			'url',
			'author',
			'template',
			'parent',
			'menu',
			'menuStatus',
			'menuOrder',
			'private',
			'meta',
			'metad',
			'metarNoIndex',
			'metarNoFollow',
			'metarNoArchive',
			'content'
		);

		foreach($fields as $field){
			$note = $xml->addChild($field);
			$note->addCData($$field);
		}

		exec_action('changedata-save');
		if (isset($_POST['autosave']) && $_POST['autosave'] == 'true' && $autoSaveDraft == true) {
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
			
			if ($url == $oldslug) {
				redirect($redirect_url."?id=". $url ."&upd=edit-success&type=edit");
			} else {
				redirect($redirect_url."?id=". $url ."&old=".$oldslug."&upd=edit-success&type=edit");
			}
		}
	}
} else {
	redirect('pages.php');
}
<?php 
/****************************************************
*
* @File: 		changedata.php
* @Package:	GetSimple
* @Action:	Code to either create or edit a page.
*						This is the action page for the form on 
*						edit.php
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');
	
if (isset($_POST['submitted']))
{
	if ( ($_POST['post-title'] == '') && ($_POST['post-uri'] == '') && ($_POST['post-content'] == '')  )
	{
		header("Location: edit.php?upd=edit-err&type=".$i18n['CANNOT_SAVE_EMPTY']);
		exit;
	} 
	else 
	{
		
		$url="";$title="";$metad=""; $metak="";	$cont="";
		
		// is a slug provided?
		if ($_POST['post-uri']) 
		{ 
			$url = $_POST['post-uri'];
		} 
		else 
		{
			if ($_POST['post-title'])
			{ 
				$url = $_POST['post-title'];
			} 
			else 
			{
				$url = "temp";
			}
		}
		
		$url = to7bit($url, "UTF-8");
		$url = clean_url($url); //old way
		
		
		// was the slug changed on an existing page?
		if ( isset($_POST['existing-url']) ) 
		{
			if ($_POST['post-uri'] != $_POST['existing-url'])
			{
				// dont change the index page's slug
				if ($_POST['existing-url'] == 'index') 
				{
					$url = $_POST['existing-url'];
					header("Location: edit.php?uri=". $_POST['existing-url'] ."&upd=edit-index&type=edit");
					exit;
				} 
				else 
				{
					$file = "../data/pages/". @$url .".xml";
					$existing = "../data/pages/". $_POST['existing-url'] .".xml";
					$bakfile = "../backups/pages/". $_POST['existing-url'] .".bak.xml";
					copy($existing, $bakfile);
					unlink($existing);
				} 
			} 
		}
		
		$file = "../data/pages/". @$url .".xml";
		
		// format and clean the responses
		if(isset($_POST['post-title'])) { $title = htmlentities($_POST['post-title'], ENT_QUOTES, 'UTF-8'); }
		if(isset($_POST['post-metak'])) { $metak = htmlentities($_POST['post-metak'], ENT_QUOTES, 'UTF-8'); }
		if(isset($_POST['post-metad'])) { $metad = htmlentities($_POST['post-metad'], ENT_QUOTES, 'UTF-8'); }
		if(isset($_POST['post-template'])) { $template = $_POST['post-template']; }
		if(isset($_POST['post-parent'])) { $parent = $_POST['post-parent']; }
		if(isset($_POST['post-menu'])) { $menu = htmlentities($_POST['post-menu'], ENT_QUOTES, 'UTF-8'); }
		if(isset($_POST['post-menu-enable'])) { $menuStatus = "Y"; } else { $menuStatus = ""; }
		if(isset($_POST['post-private'])) { $private = "Y"; } else { $private = ""; }
		if(isset($_POST['post-content'])) { $content = htmlentities($_POST['post-content'], ENT_QUOTES, 'UTF-8'); }
		
		if(isset($_POST['post-menu-order'])) 
		{ 
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
		if ( file_exists($file) && ($_POST['post-uri'] != $_POST['existing-url']) ) 
		{
			$count = "1";
			$file = "../data/pages/". $url ."-".$count.".xml";
			
			while ( file_exists($file) ) 
			{
				$count++;
				$file = "../data/pages/". $url ."-".$count.".xml";
			}
			
			$url = $url .'-'. $count;
		}

		
		// if we are editing an existing page, create a backup
		if ( file_exists($file) ) 
		{
			$bakfile = "../backups/pages/". $url .".bak.xml";
			copy($file, $bakfile);
		}
		
		
		$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
		$xml->addChild('pubDate', date('r'));

		$note = $xml->addChild('title');
		$note->addCData(@$title);
		
		$note = $xml->addChild('url');
		$note->addCData(@$url);
		
		$note = $xml->addChild('meta');
		$note->addCData(@$metak);
		
		$note = $xml->addChild('metad');
		$note->addCData(@$metad);
		
		$note = $xml->addChild('menu');
		$note->addCData(@$menu);
		
		$note = $xml->addChild('menuOrder');
		$note->addCData(@$menuOrder);
		
		$note = $xml->addChild('menuStatus');
		$note->addCData(@$menuStatus);
		
		$note = $xml->addChild('template');
		$note->addCData(@$template);
		
		$note = $xml->addChild('parent');
		$note->addCData(@$parent);
		
		$note = $xml->addChild('content');
		$note->addCData(@$content);
		
		$note = $xml->addChild('private');
		$note->addCData(@$private);

		exec_action('changedata-save');

		$xml->asXML($file);
		
		// redirect user back to edit page 
		header("Location: edit.php?uri=". $url ."&upd=edit-success&type=edit");
	}
}
?>
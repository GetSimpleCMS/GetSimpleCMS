<?php
/****************************************************
*
* @File: 	sitemap.php
* @Package:	GetSimple
* @Action:	Creates sitemap.xml in the site's root. 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// check validity of request
if ($_REQUEST['s'] === $SESSIONHASH) {

	// Variable settings
	$path = $relative. 'data/pages/';
	$count="0";
	
	$filenames = getFiles($path);
	
	if (count($filenames) != 0)
	{ 
		foreach ($filenames as $file)
		{
			if (isFile($file, $path, 'xml'))
			{
				$data = getXML($path . $file);
				$status = $data->menuStatus;
				$pagesArray[$count]['url'] = $data->url;
				$pagesArray[$count]['parent'] = $data->parent;
				$pagesArray[$count]['date'] = $data->pubDate;
				$pagesArray[$count]['private'] = $data->private;
				$pagesArray[$count]['menuStatus'] = $data->menuStatus;
				$count++;
			}
		}
	}
	
	$pagesSorted = subval_sort($pagesArray,'menuStatus');
	
	if (count($pagesSorted) != 0)
	{ 
		$xml = @new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
		$xml->addAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd', 'http://www.w3.org/2001/XMLSchema-instance');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		
		foreach ($pagesSorted as $page)
		{	
			if ($page['private'] != 'Y')
			{
				// set <loc>
				$pageLoc = find_url($page['url'], $page['parent']);
				
				// set <lastmod>
				$tmpDate = date("Y-m-d H:i:s", strtotime($page['date']));
				$pageLastMod = makeIso8601TimeStamp($tmpDate);
				
				// set <changefreq>
				$pageChangeFreq = 'weekly';
				
				// set <priority>
				if ($page['menuStatus'] == 'Y') {
					$pagePriority = '1.0';
				} else {
					$pagePriority = '0.5';
				}
				
				//add to sitemap
				$url_item = $xml->addChild('url');
				$url_item->addChild('loc', $pageLoc);
				$url_item->addChild('lastmod', $pageLastMod);
				$url_item->addChild('changefreq', $pageChangeFreq);
				$url_item->addChild('priority', $pagePriority);
				exec_action('sitemap-additem');
			}
			
			//create xml file
			$file = $relative .'sitemap.xml';
			exec_action('save-sitemap');
			XMLsave($xml, $file);
		}
	}
	
	// Variables for website
	$spath 		= $relative .'data/other/';
	$sfile 		= "website.xml";
	$data 		= getXML($spath . $sfile);
	$SITEURL 	= $data->SITEURL;
	
	if (!defined('GSDONOTPING')) {
		if (file_exists($relative .'sitemap.xml')){
			if( 200 === ($status=pingGoogleSitemaps($SITEURL.'sitemap.xml')))	{
				$response = $i18n['SITEMAP_CREATED'];
				header('location: theme.php?success=' . $response);
				exit;
			} else {
				$response = $i18n['SITEMAP_ERRORPING'];
				header('location: theme.php?err=' . $response);
				exit;
			}
		} else {
			$response = $i18n['SITEMAP_ERROR'];
			header('location: theme.php?err=' . $response);	
			exit;
		}
	} else {
		$response = $i18n['SITEMAP_ERRORPING'];
		header('location: theme.php?success=' . $response);
		exit;
	}
} else {
	die('You do not have permission to execute this page');
}

exit;
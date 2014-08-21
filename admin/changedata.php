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
// @todo why only here, maybe we should add this to everything, although easily circumventable
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
		if(isset($_POST['post-title'])){
			$title = trim(safe_slash_html($_POST['post-title']));
			$title = truncate($title,GSTITLEMAX); // limit titles to 70 characters
		}

		// is a slug provided?
		if ($_POST['post-id']) {
			$url = truncate($_POST['post-id'],GSFILENAMEMAX); // limit slug/filenames to 70 chars
			// @todo abstract this translit stuff
			$url = doTransliteration($url);
			$url = to7bit($url, "UTF-8");
			$url = clean_url($url); //old way
		} else {
			if ($title)	{
				$url = $title;
				$url = doTransliteration($url);
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
				if ($oldslug == 'index') {
					// prevent change of index page's slug
					redirect("edit.php?id=". urlencode($oldslug) ."&upd=edit-index&type=edit");
				} else {
					exec_action('changedata-updateslug');
					updateSlugs($oldslug);
					// do backup
					backup_page($oldslug);
					delete_page($oldslug);
				}
			}
		}

		// format and clean the responses
		// content
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
		// @todo abstract into method for getting incremental file names
		if ( (file_exists(GSDATAPAGESPATH . $url .".xml") && $url != $oldslug) ||  in_array($url,$reservedSlugs) ) {
			list($newfilename,$count) = getNextFileName(GSDATAPAGESPATH,$url.'.xml');
			$url = $url .'-'. $count;
		}

		// create new xml
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

		// backup before overwriting
		if(file_exists(GSDATAPAGESPATH . $url .".xml")) backup_page($url);

		if (isset($_POST['autosave']) && $_POST['autosave'] == '1' && $autoSaveDraft == true) {
			XMLsave($xml, GSAUTOSAVEPATH . $url . '.xml');
		} else {
			XMLsave($xml, GSDATAPAGESPATH . $url .".xml");
		}

		//ending actions
		exec_action('changedata-aftersave');
		generate_sitemap();

		/**
		 * do changedata ajax save checking for legacy
		 * @param  str $url     [description]
		 * @param  str $oldslug [description]
		 */
		function changedataAjaxSave($url,$oldslug){
			if(isset($_POST['ajaxsave'])){
				// ajax response wrapper, still using html parsing for now
				echo "<div>";

				// if this was an autosave add autosave response
				if(isset($_POST['autosave']) && $_POST['autosave'] == '1'){
					echo '<div class="autosavenotify">'.sprintf(i18n_r('AUTOSAVE_NOTIFY'),output_time(date())).'</div>';
				}

				// setup error checking vars and include error checking for notifications
				$id     = $url;
				$update = 'edit-success';
				$ptype  = 'edit';
				if($url !== $oldslug) $oldid = $oldslug; // if slug was changed set $oldid
				include('template/error_checking.php');

				// send new inputs for slug changes and new nonces
				echo '<input id="nonce" name="nonce" type="hidden" value="'. get_nonce("edit", "edit.php") .'" />';
	            echo '<input id="existing-url" name="existing-url" type="hidden" value="'. $url .'" />';
				echo "</div>";
				die();
			}
		}

		// if ajax we are done
		changedataAjaxSave($url,$oldslug);

		// redirect user back to edit page

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
} else {
	redirect('pages.php');
}
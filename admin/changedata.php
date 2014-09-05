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
$autoSaveDraft = false; // auto save to autosave drafts

// Include common.php
include('inc/common.php');
login_cookie_check();

/**
 * create a page xml obj
 *
 * @since 3.4
 * @param  str      $title     title of page
 * @param  str      $url       optional, url slug of page, if null title is used
 * @param  array   	$data      optional, array of data fields for page
 * @param  boolean 	$overwrite optional, overwrite exisitng slugs, if false auto increments slug id
 * @return obj                 xml object of page
 */
function createPageXml($title, $url = null, $data = array(), $overwrite = false){
	GLOBAL $reservedSlugs;

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

	// setup url, falls back to title if not set
	if(!isset($url)) $url = $title;
	debugLog(gettype($url));
	$url = prepareSlug($url); // prepare slug, clean it, translit, truncate

	$title = truncate($title,GSTITLEMAX); // truncate long titles

	// If overwrite is false do not use existing slugs, get next incremental slug, eg. "slug-count"
	if ( !$overwrite && (file_exists(GSDATAPAGESPATH . $url .".xml") ||  in_array($url,$reservedSlugs)) ) {
		list($newfilename,$count) = getNextFileName(GSDATAPAGESPATH,$url.'.xml');
		$url = $url .'-'. $count;
	}

	// store url and title in data, if passed in param they are ignored
	$data['url'] = $url;
	$data['title'] = $title;

	// create new xml
	$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
	$xml->addChild('pubDate', date('r'));

	foreach($fields as $field){
		$node = $xml->addChild($field);
		if(isset($data[$field])) $node->addCData($data[$field]); // saving all cdata for some reason
	}

	// debugLog(__FUNCTION__ . ': page created with slug of ' . $xml->url);
	return $xml;
}

/**
 * save a page to xml
 *
 * @since  3.4
 * @param  obj $xml simplexmlobj of page
 * @return bool success
 */
function savePageXml($xml){
	$url = $xml->url;
	if(!isset($url) || trim($url) == '') die('empty slug');
	// backup before overwriting
	if(file_exists(GSDATAPAGESPATH . $url .".xml")) backup_page($url);
	return XMLsave($xml, GSDATAPAGESPATH . $url .".xml");
}

/**
 * prepare a slug to gs standads
 * sanitizes, performs translist for filename, truncates to GSFILENAMEMAX
 *
 * @since  3.4
 * @param  str $slug slug to normalize
 * @param  str $default default slug to substitute if conversion empties it
 * @return str       new slug
 */
function prepareSlug($slug, $default = 'temp'){
	$slug = truncate($slug,GSFILENAMEMAX);
	$slug = doTransliteration($slug);
	$slug = to7bit($slug, "UTF-8");
	$slug = clean_url($slug); //old way @todo what does that mean ?
	if(trim($slug) == '' && $default) return $default;
	return $slug;
}


if (isset($_POST['submitted'])) {
	check_for_csrf("edit", "edit.php");

	// check for missing required fields
	if ( !isset($_POST['post-title']) || trim($_POST['post-title']) == '' )	{
		// no title, throw CANNOT_SAVE_EMPTY
		redirect("edit.php?upd=edit-error&type=".urlencode(i18n_r('CANNOT_SAVE_EMPTY')));
	}

	// flag for new page, true, false existing
	$pageIsNew = !isset($_POST['existing-url']) || trim($_POST['existing-url']) == '';

	$postslug = $oldslug = null;
	$oldslug  = (isset($_POST['existing-url']) && trim($_POST['existing-url']) !=='') ? $_POST['existing-url'] : null;
	$postslug = (isset($_POST['post-id']) && trim($_POST['post-id']) !=='') ? $_POST['post-id'] : null;

	$slugHasChanged = !$pageIsNew && ($oldslug !== $postslug);

	// setup title
	$title = safe_slash_html($_POST['post-title']);

	// if attempting to change index throw ER_CANNOT_INDEX
	if ($slugHasChanged && $oldslug === 'index') redirect("edit.php?id=". urlencode($oldslug) ."&upd=edit-index&type=edit");

	// format and clean the responses
	$data = array();

	if(isset($_POST['post-titlelong']))			{ $data['titlelong']   = safe_slash_html($_POST['post-titlelong']);	}
	if(isset($_POST['post-summary']))			{ $data['summary']     = safe_slash_html($_POST['post-summary']);	}
	if(isset($_POST['post-content'])) 			{ $data['content']     = safe_slash_html($_POST['post-content']); }
	// options
	if(isset($_POST['post-author'])) 			{ $data['author']      = safe_slash_html($_POST['post-author']);	}
	if(isset($_POST['post-template'])) 			{ $data['template']    = $_POST['post-template']; }
	if(isset($_POST['post-parent'])) 			{ $data['parent']      = $_POST['post-parent']; }
	if(isset($_POST['post-menu'])) 				{ $data['menu']        = safe_slash_html($_POST['post-menu']); }
	if(isset($_POST['post-menu-enable'])) 		{ $data['menuStatus']  = "Y"; } else { $menuStatus = ""; }
	if(isset($_POST['post-menu-order'])) 		{ $data['menuOrder']   = is_numeric($_POST['post-menu-order']) ? $_POST['post-menu-order'] : "0"; }
	if(isset($_POST['post-private']) ) 			{ $data['private']     = safe_slash_html($_POST['post-private']); }
	// meta
	if(isset($_POST['post-metak'])) 			{ $data['meta']        = $metak = safe_slash_html($_POST['post-metak']);	}
	if(isset($_POST['post-metad'])) 			{ $data['metad']       = safe_slash_html($_POST['post-metad']);	}
	
	//robots
	if(isset($_POST['post-metar-noindex']))	 	$data['metarNoIndex']   = 1;
	else $data['metarNoIndex'] = 0; 
	if(isset($_POST['post-metar-nofollow']))	$data['metarNoFollow']  = 1;
	else $data['metarNoFollow'] = 0; 
	if(isset($_POST['post-metar-noarchive']))	$data['metarNoArchive'] = 1;
	else $data['metarNoArchive'] = 0; 

	$xml = createPageXml($title,$postslug,$data);

	debugLog((string)$xml->url);
	$url = $xml->url;
	// UPDATE SLUGS IF IT CHANGED
	// @todo need new slug
	if ($slugHasChanged){
		exec_action('changedata-updateslug');
		updateSlugs($oldslug,$xml->url); // update childrens parent slugs to new slug
		delete_page($oldslug); // backup and delete the page
	}

	exec_action('changedata-save');
	$xml = exec_filter('page-save',$xml);

	savePageXml($xml);

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

	if(isset($oldslug)){
		if ($url == $oldslug) {
			// redirect save new file
		redirect($redirect_url."?id=". $url ."&upd=edit-success&type=edit");
		} else {
			// redirect new slug, undo for old slug
			redirect($redirect_url."?id=". $url ."&old=".$oldslug."&upd=edit-success&type=edit");
		}	

	}
	else {
		// redirect new slug
		redirect($redirect_url."?id=". $url ."&upd=edit-success&type=new"); 
	}

} else {
	redirect('pages.php');
}
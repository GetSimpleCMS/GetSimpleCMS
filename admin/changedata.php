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
login_cookie_check();

$autoSaveDraft = false; // auto save to autosave drafts
$draft = (isset($_GET['nodraft']) || isset($_POST['post-nodraft']) || !getDef('GSUSEDRAFTS',true)) ? false : true; // (bool) using draft pages

if(isset($_GET['publish']) && isset($_GET['id'])){
	$id     = var_in($_GET['id']);
	$status = publishDraft($id);
	if($status){
		exec_action('draft-publish'); // triggers page cache rebuild
		generate_sitemap(); // regenerates sitemap
	}
	redirect("pages.php?id=". $id ."&upd=publish-".$status ? 'success' : 'error');
	die();
}

if (isset($_POST['submitted'])) {
	check_for_csrf("edit", "edit.php");
	// check for missing required fields
	if ( !isset($_POST['post-title']) || trim($_POST['post-title']) == '' )	{
		// no title, throw CANNOT_SAVE_EMPTY
		// @todo this loses $id, we only get here if js is disabled
		redirect("edit.php?upd=edit-error&type=".urlencode(i18n_r('CANNOT_SAVE_EMPTY')));
	}

	// flag for new page, true, false existing
	$pageIsNew = !isset($_POST['existing-url']) || trim($_POST['existing-url']) == '';

	$postslug = $oldslug = null;
	$oldslug  = (isset($_POST['existing-url']) && trim($_POST['existing-url']) !=='') ? $_POST['existing-url'] : null;
	$postslug = (isset($_POST['post-id']) && trim($_POST['post-id']) !=='') ? $_POST['post-id'] : null;

	$slugHasChanged = !$pageIsNew && ($oldslug !== $postslug);
	$overwrite = !$pageIsNew && !$slugHasChanged;

	// setup title
	$title = safe_slash_html($_POST['post-title']);

	// if attempting to change index throw ER_CANNOT_INDEX
	if ($slugHasChanged && $oldslug === 'index') redirect("edit.php?id=". urlencode($oldslug) ."&upd=edit-index&type=edit");
	// if attemping to change slug on draft page throw ER_CANNOT_DRAFT
	if ($slugHasChanged && $draft) redirect("edit.php?id=". urlencode($oldslug) ."&upd=draft-slug&type=edit");

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

	// overwrite set for editing pages only, else we autoincrement slug if newpage or slughaschanged
	$xml = createPageXml($title,$postslug,$data,$overwrite);
	$url = (string)$xml->url; // legacy global

	if(!$draft){
		// if the slug changed update children
		if ($slugHasChanged){
			exec_action('changedata-updateslug');
			changeChildParents($oldslug,$url); // update childrens parent slugs to the new slug
			delete_page($oldslug); // backup and delete the page
		}
		exec_action('changedata-save'); // @hook changedata-save prior to saving a page
		$xml = exec_filter('page-save',$xml);
		savePageXml($xml);
		exec_action('changedata-aftersave');
		
		// genen sitemap if published save
		generate_sitemap();
	}
	else {;
		exec_action('changedata-save-draft');
		$xml = exec_filter('draft-save',$xml);
		saveDraftXml($xml);
		exec_action('changedata-aftersave-draft');
	}

	/**
	 * do changedata ajax save checking for legacy
	 * @param  str $url     [description]
	 * @param  str $oldslug [description]
	 */
	function changedataAjaxSave($url,$oldslug){
		global $draft;
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
			$upddraft = $draft;
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

	// redirect user back to edit page or redirectto
	if (isset($_POST['redirectto']) && $_POST['redirectto']!='') $redirect_url = $_POST['redirectto'];
	else $redirect_url = 'edit.php';

	if($pageIsNew) $redirect_url .= "?id=". $url ."&upd=edit-success&type=new"; // new page
	if($slugHasChanged) $redirect_url .= "?id=". $url ."&old=".$oldslug."&upd=edit-success&type=edit"; // update with new slug
	else $redirect_url .= "?id=". $url ."&upd=edit-success&type=edit"; // update

	if($draft) $redirect_url .= "&upd-draft";
	// add nodraft arg if we are force editing a live page
	if(getDef('GSUSEDRAFTS',true) && !$draft) $redirect_url .= '&nodraft';
	redirect($redirect_url);

} else {
	// nothing submitted
	redirect('pages.php');
}

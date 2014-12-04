<?php
/**
 * Edit Backups
 *
 * View the current backup of a given page
 *
 * @package GetSimple
 * @subpackage Backups
 */

# setup
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-backup-edit');

# get page url to display
if ($_GET['id'] != '') {
	$id   = $_GET['id'];
	$file = getBackupName($id,'xml');

	$draft = isset($_GET['draft']); // (bool) using draft pages
	if($draft) $path = GSBACKUPSPATH .getRelPath(GSDATADRAFTSPATH,GSDATAPATH); // backups/drafts/
	else $path = GSBACKUPSPATH .getRelPath(GSDATAPAGESPATH,GSDATAPATH); // backups/pages/

	$data       = getXML($path . $file);
	$title      = htmldecode($data->title);
	$pubDate    = $data->pubDate;
	$parent     = $data->parent;
	$metak      = htmldecode($data->meta);
	$metad      = htmldecode($data->metad);
	$url        = $data->url;
	$content    = htmldecode($data->content);
	$private    = $data->private;
	$template   = $data->template;
	$menu       = htmldecode($data->menu);
	$menuStatus = $data->menuStatus;
	$menuOrder  = $data->menuOrder;
} else {
	redirect('backups.php?upd=bak-err');
}

if ($private != '' ) { $private = '<span style="color:#cc0000">('.i18n_r('PRIVATE_SUBTITLE').')</span>'; } else { $private = ''; }
if ($menuStatus == '' ) { $menuStatus = i18n_r('NO'); } else { $menuStatus = i18n_r('YES'); }

// are we going to do anything with this backup?
if ($_GET['p'] != '') {
	$p = $_GET['p'];
} else {
	redirect('backups.php?upd=bak-err');
}

if ($p == 'delete') {
	// deleting page backup
	check_for_csrf("delete","backup-edit.php");
	if($draft) $status = delete_draft_backup($id) ? 'success' : 'err';
	else $status = delete_page_backup($id) ? 'success' : 'err';
	redirect("backups.php?upd=bak-".$status."&id=".$id);
}

elseif ($p == 'restore') {
	// restoring page backup
	check_for_csrf("restore", "backup-edit.php");

	if($draft){
		restore_draft($id);   // restore old slug file
		redirect("edit.php?id=". $id ."&upd=edit-success&type=restore");
	}

	if (isset($_GET['new'])) {
		$newid = $_GET['new'];
		// restore page by old slug id
		changeChildParents($newid, $id); // update parents and children
		restore_page($id);        // restore old slug file
		delete_page($newid);      // backup and delete live new slug file

		redirect("edit.php?id=". $id ."&nodraft&old=".$_GET['new']."&upd=edit-success&type=restore");
	} else {
		restore_page($id);   // restore old slug file
		redirect("edit.php?id=". $id ."&nodraft&upd=edit-success&type=restore");
	}

}

$pagetitle = i18n_r('BAK_MANAGEMENT').' &middot; '.i18n_r('VIEWPAGE_TITLE');
get_template('header');

$draftqs = $draft ? '&amp;draft' : '';

?>

<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">

	<div id="maincontent">
		<div class="main" >
		<h3 class="floated"><?php i18n('BACKUP');?> <span> / <?php echo $url; ?></span></h3>
		<?php if($draft){ ?><div class="title label secondary-lightest-back label-inline"><?php i18n('LABEL_DRAFT'); ?></div> <?php } ?>
		<div class="edit-nav clearfix" >
			 <a href="backup-edit.php?p=restore<?php echo $draftqs; ?>&amp;id=<?php echo var_out($id); ?>&amp;nonce=<?php echo get_nonce("restore", "backup-edit.php"); ?>" 
			 	accesskey="<?php echo find_accesskey(i18n_r('ASK_RESTORE'));?>" ><?php i18n('ASK_RESTORE');?></a> 
			 <a href="backup-edit.php?p=delete<?php echo $draftqs; ?>&amp;id=<?php echo var_out($id); ?>&amp;nonce=<?php echo get_nonce("delete", "backup-edit.php"); ?>" 
			 	title="<?php i18n('DELETEPAGE_TITLE'); ?>: <?php echo $title; ?>?" 
			 	id="delback" 
			 	accesskey="<?php echo find_accesskey(i18n_r('ASK_DELETE'));?>" 
			 	class="delconfirm noajax" ><?php i18n('ASK_DELETE');?></a>
			<?php exec_action(get_filename_id().'-edit-nav'); ?>
		</div>
		<?php exec_action(get_filename_id().'-body'); ?>				
		<table class="simple highlight" >
		<tr><td class="title" ><?php i18n('PAGE_TITLE');?>:</td><td><b><?php echo cl($title); ?></b> <?php echo $private; ?></td></tr>
		<tr><td class="title" ><?php i18n('BACKUP_OF');?>:</td><td>
			<?php 
			if(isset($id)) {
					echo '<a target="_blank" href="'. find_url($url, $parent) .'">'. find_url($url, $parent) .'</a>'; 
			} 
			?>
		</td></tr>
		<tr><td class="title" ><?php i18n('DATE');?>:</td><td><?php echo output_datetime($pubDate); ?></td></tr>
		<tr><td class="title" ><?php i18n('TAG_KEYWORDS');?>:</td><td><em><?php echo $metak; ?></em></td></tr>
		<tr><td class="title" ><?php i18n('META_DESC');?>:</td><td><em><?php echo $metad; ?></em></td></tr>
		<tr><td class="title" ><?php i18n('MENU_TEXT');?>:</td><td><?php echo $menu; ?></td></tr>
		<tr><td class="title" ><?php i18n('PRIORITY');?>:</td><td><?php echo $menuOrder; ?></td></tr>
		<tr><td class="title" ><?php i18n('ADD_TO_MENU');?></td><td><?php echo $menuStatus; ?></td></tr>
		</table>
		
		<textarea id="codetext" wrap='off' style="background:#f4f4f4;padding:4px;width:635px;color:#444;border:1px solid #666;" readonly ><?php echo strip_decode($content); ?></textarea>

		</div>
		
		<?php if ($HTMLEDITOR != '') { ?>
		<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
		var editor = CKEDITOR.replace( 'codetext', {
			language        : '<?php echo $EDLANG; ?>',
			<?php if (file_exists(GSTHEMESPATH .$TEMPLATE."/editor.css")) { 
				$fullpath = $SITEURL;
			?>
			contentsCss     : '<?php echo $fullpath.getRelPath(GSTHEMESPATH).$TEMPLATE; ?>/editor.css',
			<?php } ?>
			height          : '<?php echo $EDHEIGHT; ?>',
			baseHref        : '<?php echo $SITEURL; ?>',
			toolbar         : [['Source']],
			removePlugins: 'image,link,elementspath,resize'
		});
		// set editor to read only mode
		editor.on('mode', function (ev) {
			if (ev.editor.mode == 'source') {
				$('#cke_contents_codetext .cke_source').attr("readonly", "readonly");
			}
			else {
				var bodyelement = ev.editor.document.$.body;
				bodyelement.setAttribute("contenteditable", false);
			}		
		});
		</script>
		
		<?php } ?>
		
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>

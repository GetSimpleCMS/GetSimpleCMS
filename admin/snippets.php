<?php
/**
 * Snippets
 *
 * Displays and creates static snippets 	
 *
 * @package GetSimple
 * @subpackage Snippets
 * @link http://get-simple.info/docs/what-are-snippets
 */
 
# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-snippets');

# variable settings
$update = $table = $list = '';

# check to see if form was submitted
if (isset($_POST['submitted'])){

	check_for_csrf("modify_snippets");

	# create backup file for undo
	backup_datafile(GSDATAOTHERPATH.GSSNIPPETSFILE);
	
	# start creation of top of components.xml file
	if (count($_POST['component']) != 0) {
		$status = $error = null;
		$compxml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
		
		foreach ($_POST['component'] as $component)	{
			$id     = $component['id']; // unused
			$slug   = $component['slug'];
			$value  = $component['val'];
			$title  = $component['title'];
			$active = isset($component['active']) ? 0 : 1; // checkbox
			
			$slug = getCollectionItemSlug($slug,$title);
			if($slug == null){
				// add corrupt data protection, prevent deleting components if something critical is missing
				if(empty($slug)) $error = 'an error occured, missing slug';
			}
			else {
				if(is_object(get_collection_item($slug,$compxml))){
					$error = sprintf(i18n_r('DUP_SLUG',"Duplicate slug - [%s]"),$slug);
				}
				$status = addComponentItem($compxml,$title,$value,$active,$slug); // @todo, check for problems $xml is passed by identifier
				if(!$status) $error = i18n_r("ERROR_OCCURRED");
			}
		}
		if(!$error){
			exec_action('snippet-save'); // @hook snippet-save before saving components data file
			$status = XMLsave($compxml, GSDATAOTHERPATH.GSSNIPPETSFILE);
			if(!$status) $error = i18n_r("ERROR_OCCURRED");
			get_components_xml(true);
		}	
	}
	$update = empty($error) ? 'snippet-success' : 'error';
}

# if undo was invoked
if (isset($_GET['undo'])) {
	
	# perform the undo
	restore_datafile(GSDATAOTHERPATH.GSSNIPPETSFILE);
	check_for_csrf("undo");
	if(!requestIsAjax()) redirect('snippets.php?upd=snippet-restored'); // redirect to prevent refresh undos
	// undos are not ajax, ??
	// $update = 'snippet-restored';
	// get_snippets_xml(true);
}

# create components form html
$collectionData = get_snippets_xml();
$numitems       = $collectionData ? count($collectionData) : 0;
$pagetitle      = i18n_r('SNIPPETS');
get_template('header');

include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
			<h3 class="floated"><?php echo i18n_r('EDIT_SNIPPETS');?></h3>
			<div class="edit-nav clearfix" >
				<a href="javascript:void(0)" id="addcomponent" accesskey="<?php echo find_accesskey(i18n_r('ADD_SNIPPET'));?>" ><?php i18n('ADD_SNIPPET');?></a>
				<!--<?php if(!getDef('GSNOHIGHLIGHT',true)){
				echo $themeselector; ?>	
				<label>Theme</label>
			<?php } ?>	-->
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>
			<form id="compEditForm" class="manyinputs" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
				<input type="hidden" id="id" value="<?php echo $numitems; ?>" />
				<input type="hidden" id="nonce" name="nonce" value="<?php echo get_nonce("modify_snippets"); ?>" />

				<div id="divTxt"></div>
				<?php outputCollection('snippets',$collectionData,'','get_snippet'); ?>
				<p id="submit_line" class="<?php echo $numitems > 0 ? '' : ' hidden'; ?>" >
					<span><input type="submit" class="submit" name="submitted" id="button" value="<?php i18n('SAVE_SNIPPETS');?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="snippets.php?cancel"><?php i18n('CANCEL'); ?></a>
				</p>
			</form>
			<div id="comptemplate" class="hidden"><?php echo getItemTemplate('snippets',' noeditor','get_snippet'); ?></div>			
		</div>
	</div>
	
	<div id="sidebar">
		<?php include('template/sidebar-theme.php'); ?>
		<?php outputCollectionTags('snippets',$collectionData); ?>
	</div>

</div>
<?php get_template('footer'); ?>
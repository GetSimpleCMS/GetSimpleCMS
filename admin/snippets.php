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
$asset  = GSDATAOTHERPATH.GSSNIPPETSFILE;
$id     = "snippet";

# check to see if form was submitted
if (isset($_POST['submitted'])){
	check_for_csrf("modify_".$id);
	$error  = saveCollection($id,$asset);
	$update = empty($error) ? $id.'-success' : 'error';
	if(!$error) get_snippets_xml(true);
}

# if undo was invoked
if (isset($_GET['undo'])) {
	
	# perform the undo
	restore_datafile($asset);
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
			<form id="compEditForm" class="manyinputs watch" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
				<input type="hidden" id="id" value="<?php echo $numitems; ?>" />
				<input type="hidden" id="nonce" name="nonce" value="<?php echo get_nonce("modify_snippet"); ?>" />

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
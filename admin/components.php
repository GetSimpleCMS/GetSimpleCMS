<?php
/**
 * Components
 *
 * Displays and creates static components 	
 *
 * @package GetSimple
 * @subpackage Components
 * @link http://get-simple.info/docs/what-are-components
 */
 
# setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

exec_action('load-components');

# variable settings
$update = $table = $list = '';

# check to see if form was submitted
if (isset($_POST['submitted'])){
	$value  = $_POST['val'];
	$slug   = $_POST['slug'];
	$title  = $_POST['title'];
	$ids    = $_POST['id'];
	$active = $_POST['active'];

	check_for_csrf("modify_components");

	# create backup file for undo
	backup_datafile(GSDATAOTHERPATH.GSCOMPONENTSFILE);
	
	# start creation of top of components.xml file
	$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	if (count($ids) != 0) { 
		
		$ct = 0; $coArray = array();
		foreach ($ids as $id)		{
			if ($title[$ct] != null) {
				if ( $slug[$ct] == null )	{
					$slug_tmp  = to7bit($title[$ct], 'UTF-8');
					$slug[$ct] = clean_url($slug_tmp); 
					$slug_tmp  = '';
				}
				
				$coArray[$ct]['id']    = $ids[$ct];
				$coArray[$ct]['slug']  = $slug[$ct];
				$coArray[$ct]['title'] = safe_slash_html($title[$ct]);
				$coArray[$ct]['value'] = safe_slash_html($value[$ct]);
				$coArray[$ct]['disabled'] = !in_array($coArray[$ct]['id'],$active);
			}
			$ct++;
		}
		
		$ids = subval_sort($coArray,'title');
		
		$count = 0;
		foreach ($ids as $comp)	{
			# create the body of components.xml file
			$newitems = $xml->addChild('item');
			$c_note     = $newitems->addChild('title');
			$c_note->addCData($comp['title']);
			$newitems->addChild('slug', $comp['slug']);
			$c_note     = $newitems->addChild('value');
			$c_note->addCData($comp['value']);
			$c_note     = $newitems->addChild('disabled');
			$c_note->addCData($comp['disabled']);			
			$count++;
		}
	}
	exec_action('component-save'); // @hook component-save before saving components data file
	XMLsave($xml, GSDATAOTHERPATH.GSCOMPONENTSFILE);
	$update = 'comp-success';
	get_components_xml(true);
}

# if undo was invoked
if (isset($_GET['undo'])) { 
	
	# perform the undo
	restore_datafile(GSDATAOTHERPATH.GSCOMPONENTSFILE);
	check_for_csrf("undo");
	if(!requestIsAjax()) redirect('components.php?upd=comp-restored'); // redirect to prevent refresh undos
	$update = 'comp-restored';
	get_components_xml(true);
}

# create components form html
$collectionData = get_components_xml();
$numitems       = $collectionData ? count($collectionData) : 0;
$pagetitle      = i18n_r('COMPONENTS');
get_template('header');
	
include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
		<div class="main">
			<h3 class="floated"><?php echo i18n_r('EDIT_COMPONENTS');?></h3>
			<div class="edit-nav clearfix" >
				<a href="javascript:void(0)" id="addcomponent" accesskey="<?php echo find_accesskey(i18n_r('ADD_COMPONENT'));?>" ><?php i18n('ADD_COMPONENT');?></a>
				<?php if(!getDef('GSNOHIGHLIGHT',true)){
				echo $themeselector; ?>	
				<label id="cm_themeselect_label"><?php i18n('THEME'); ?></label>
			<?php } ?>
				<?php exec_action(get_filename_id().'-edit-nav'); ?>
			</div>		
			<?php exec_action(get_filename_id().'-body'); ?>
			<form id="compEditForm" class="manyinputs" action="<?php myself(); ?>" method="post" accept-charset="utf-8" >
				<input type="hidden" id="id" value="<?php echo $numitems; ?>" />
				<input type="hidden" id="nonce" name="nonce" value="<?php echo get_nonce("modify_components"); ?>" />

				<div id="divTxt"></div>
				<?php outputCollection('components',$collectionData,'','get_component'); ?>
				<p id="submit_line" class="<?php echo $numitems > 0 ? '' : ' hidden'; ?>" >
					<span><input type="submit" class="submit" name="submitted" id="button" value="<?php i18n('SAVE_COMPONENTS');?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="components.php?cancel"><?php i18n('CANCEL'); ?></a>
				</p>
			</form>
			<div id="comptemplate" class="hidden"><?php echo getItemTemplate('components',' noeditor','get_component'); ?></div>			
		</div>
	</div>
	
	<div id="sidebar">
		<?php include('template/sidebar-theme.php'); ?>
		<?php outputCollectionTags('components',$collectionData); ?>
	</div>

</div>
<?php get_template('footer'); ?>
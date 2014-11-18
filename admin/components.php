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
$componentsfile	= GSDATAOTHERPATH.GSCOMPONENTSFILE;
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
			$components = $xml->addChild('item');
			$c_note     = $components->addChild('title');
			$c_note->addCData($comp['title']);
			$components->addChild('slug', $comp['slug']);
			$c_note     = $components->addChild('value');
			$c_note->addCData($comp['value']);
			$c_note     = $components->addChild('disabled');
			$c_note->addCData($comp['disabled']);			
			$count++;
		}
	}
	exec_action('component-save'); // @hook component-save before saving components data file
	XMLsave($xml, GSDATAOTHERPATH.GSCOMPONENTSFILE);
	$update = 'comp-success';
	// redirect('components.php?upd=comp-success');
}

# if undo was invoked
if (isset($_GET['undo'])) { 
	
	# perform the undo
	restore_datafile(GSDATAOTHERPATH.GSCOMPONENTSFILE);
	$update = 'comp-restored';
	check_for_csrf("undo");		
	// redirect('components.php?upd=comp-restored');
}

# create components form html
$data          = getXML(GSDATAOTHERPATH.GSCOMPONENTSFILE);
$componentsec  = $data->item;
$numcomponents = count($componentsec);

// $componentsec = subval_sort($data->item,'title'); // sorted on save probably not necessary at this time

function getComponentOutput($id,$component,$class = 'code_edit'){

	$disabled = (bool)(string)$component->disabled;
	$readonly = (bool)(string)$component->readonly;

	$str = '';
	$str .= '<div class="compdiv codewrap" id="section-'.$id.'">';
	$str .= '<table class="comptable" ><tr>';
	$str .= '<td><b title="'.i18n_r('DOUBLE_CLICK_EDIT').'" class="comptitle editable">'. stripslashes($component->title) .'</b></td>';
	$str .= '<td style="text-align:right;" ><code>&lt;?php get_component(<span class="compslugcode">\''.$component->slug.'\'</span>); ?&gt;</code></td>';
	$str .= '<td class="compactive"><label class="" for="active[]" >'.i18n_r('ACTIVE').'</label>';
	$str .= '<input type="checkbox" name="active[]" '. (!$disabled ? 'checked="checked"' : '') .' value="'.$id.'" /></td>';
	$str .= '<td class="delete" ><a href="javascript:void(0)" title="'.i18n_r('DELETE_COMPONENT').': '. cl($component->title).'?" class="delcomponent" rel="'.$id.'" >&times;</a></td>';
	$str .= '</tr></table>';
	$str .= '<textarea name="val[]" class="'.$class.'" data-mode="php" '.$readonly.'>'. stripslashes($component->value) .'</textarea>';
	$str .= '<input type="hidden" class="compslug" name="slug[]" value="'. $component->slug .'" />';
	$str .= '<input type="hidden" class="comptitle" name="title[]" value="'. stripslashes($component->title) .'" />';
	$str .= '<input type="hidden" name="id[]" value="'. $id .'" />';
	$str .= '</div>';
	return $str;
}

function getComponentTemplate(){
	$component = array(
		'title'  => '',
		'slug'   => '',
		'value'  => '',
		'disabled' => ''
	);

	return getComponentOutput('',(object)$component,'');
}

function outputComponents($data){
	$id = 0;
	$componentsec = $data->item;
	if (count($componentsec) != 0) {
		foreach ($componentsec as $component) {
			$table = getComponentOutput($id,$component);
			exec_action('component-extras'); // @hook component-extras called after each component html is added to $table
			echo $table; // $table is legacy for hooks that modify the var, they should now just output html directly
			$id++;
		}
	}
}

function outputComponentTags($data){
	$componentsec  = $data->item;
	$numcomponents = count($componentsec);

	echo '<div class="compdivlist">';

	# create list to show on sidebar for easy access
	$class = $numcomponents < 15 ? ' clear-left' : '';
	if($numcomponents > 1) {
		$item = 0;
		foreach($componentsec as $component) {
			echo '<a id="divlist-' . $item . '" href="#section-' . $item . '" class="component'.$class.' comp_'.$component->title.'">' . $component->title . '</a>';
			$item++;
		}
	}

	exec_action('component-list-extras'); // @hook component-list-extras called after component sidebar list items (tags) 		
	echo '</div>';
}

$pagetitle = i18n_r('COMPONENTS');
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
				<input type="hidden" id="id" value="<?php echo $numcomponents; ?>" />
				<input type="hidden" id="nonce" name="nonce" value="<?php echo get_nonce("modify_components"); ?>" />

				<div id="divTxt"></div>
				<?php outputComponents($data); ?>
				<p id="submit_line" class="<?php echo $numcomponents > 0 ? '' : ' hidden'; ?>" >
					<span><input type="submit" class="submit" name="submitted" id="button" value="<?php i18n('SAVE_COMPONENTS');?>" /></span> &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="components.php?cancel"><?php i18n('CANCEL'); ?></a>
				</p>
			</form>
			<div id="comptemplate" class="hidden"><?php echo getComponentTemplate(); ?></div>			
		</div>
	</div>
	
	<div id="sidebar">
		<?php include('template/sidebar-theme.php'); ?>
		<?php outputComponentTags($data); ?>
	</div>

</div>
<?php get_template('footer'); ?>
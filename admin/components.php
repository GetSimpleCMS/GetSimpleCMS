<?php
/****************************************************
*
* @File: 		components.php
* @Package:	GetSimple
* @Action:	Displays and creates static components 
*						of the website. 	
*
*****************************************************/
 
// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable settings
$userid 	= login_cookie_check();
$file 		= "components.xml";
$path 		= GSDATAOTHERPATH;
$bakpath 	= GSBACKUPSPATH .'other/';
$update 	= ''; $table = ''; $list='';

// if the components are being saved...
if (isset($_POST['submitted']))
{
	$value = $_POST['val'];
	$slug = $_POST['slug'];
	$title = $_POST['title'];
	$ids = $_POST['id'];
	$nonce = $_POST['nonce'];	

	if(!check_nonce($nonce, "modify_components"))
		die("CSRF detected!");

	// create backup file for undo           
	createBak($file, $path, $bakpath);
	
	//top of xml file
	$xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
	if (count($ids) != 0)
	{ 
		
		$ct = 0; $coArray = array();
		foreach ($ids as $id)
		{
			if ( ($title[$ct] != null) && ($value[$ct] != null) )
			{
				if ( $slug[$ct] == null )
				{
					$slug_tmp = to7bit($title[$ct], 'UTF-8');
					$slug[$ct] = clean_url($slug_tmp); 
					$slug_tmp = '';
				}
				
				$coArray[$ct]['id'] = $ids[$ct];
				$coArray[$ct]['title'] = htmlentities($title[$ct], ENT_QUOTES, 'UTF-8');
				$coArray[$ct]['slug'] = $slug[$ct];
				$coArray[$ct]['value'] = htmlentities($value[$ct], ENT_QUOTES, 'UTF-8');
			}
			$ct++;
		}
		
		$ids = subval_sort($coArray,'title');
		
		$count = 0;
		foreach ($ids as $comp)
		{
			//body of xml file
			$components = $xml->addChild('item');
			$c_note = $components->addChild('title');
			$c_note->addCData(@$comp['title']);
			$components->addChild('slug', @$comp['slug']);
			$c_note = $components->addChild('value');
			$c_note->addCData(@$comp['value']);
			$count++;
		}
	}
	exec_action('component-save');
	XMLsave($xml, $path . $file);
	header('Location: components.php?upd=comp-success');
}

// if undo was invoked
if (isset($_GET['undo']))
{ 
	$nonce = $_GET['nonce'];	
	if(!check_nonce($nonce, "undo"))
		die("CSRF detected!");

	undo($file, $path, $bakpath);
	header('Location: components.php?upd=comp-restored');
}

//create list of components for html
$data = getXML($path . $file);
$componentsec = $data->item;
$count= 0;
if (count($componentsec) != 0) {
	foreach ($componentsec as $component) {
		$table .= '<div class="compdiv" id="section-'.@$count.'"><table class="comptable" ><tr><td><b title="Double Click to Edit" class="editable">'. stripslashes(@$component->title) .'</b></td>';
		$table .= '<td style="text-align:right;" ><code>&lt;?php get_component(<span class="compslugcode">\''.@$component->slug.'\'</span>); ?&gt;</code></td><td class="delete" >';
		$table .= '<a href="#" title="'.$i18n['DELETE_COMPONENT'].': '. cl(@$component->title).'?" id="del-'.$count.'" onClick="DeleteComp(\''.$count.'\'); return false;" >X</a></td></tr></table>';
		$table .= '<textarea name="val[]">'. stripslashes(@$component->value) .'</textarea>';
		$table .= '<input type="hidden" class="compslug" name="slug[]" value="'. @$component->slug .'" />';
		$table .= '<input type="hidden" class="comptitle" name="title[]" value="'. @stripslashes($component->title) .'" />';
		$table .= '<input type="hidden" name="id[]" value="'. @$count .'" />';
		exec_action('component-extras');
		$table .= '</div>';
		$count++;
	}
}
	// Create list for easy access
	$listc = '';
	if($count > 3) {
		$item = 0;
		foreach($componentsec as $component) {
			$listc .= '<a id="divlist-' . @$item . '" href="#section-' . @$item . '" class="component">' . @$component->title . '</a>';
			$item++;
		}
	}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['COMPONENTS']); ?>

	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['THEME_MANAGEMENT'];?> <span>&raquo;</span> <?php echo $i18n['COMPONENTS'];?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
<div class="bodycontent">
	
	<div id="maincontent">
	<div class="main">
	<label><?php echo $i18n['EDIT'];?> <?php echo $i18n['COMPONENTS'];?></label>
	<div class="edit-nav" >
		<a href="#" id="addcomponent" accesskey="a" ><?php echo $i18n['ADD_COMPONENT'];?></a>
		<div class="clear"></div>
	</div>
	
	<form class="manyinputs" action="<?php echo htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES); ?>" method="post" accept-charset="utf-8" >
		<input type="hidden" id="id" value="<?php echo @$count; ?>" />
		<input type="hidden" id="nonce" name="nonce" value="<?php echo get_nonce("modify_components"); ?>" />
		<p><input type="submit" class="submit" name="submitted" id="button" value="<?php echo $i18n['SAVE_COMPONENTS'];?>" /> &nbsp;&nbsp;<?php echo $i18n['OR']; ?>&nbsp;&nbsp; <a class="cancel" href="theme.php"><?php echo $i18n['CANCEL']; ?></a></p>

		<div id="divTxt"></div> 
		<?php echo $table; ?>
		<p><input type="submit" class="submit" name="submitted" id="button" value="<?php echo $i18n['SAVE_COMPONENTS'];?>" /> &nbsp;&nbsp;<?php echo $i18n['OR']; ?>&nbsp;&nbsp; <a class="cancel" href="components.php?cancel"><?php echo $i18n['CANCEL']; ?></a></p>
	</form>
	</div>
	</div>
	
	<div id="sidebar">
		<?php include('template/sidebar-theme.php'); ?>
		<?php if ($listc != '') { echo '<div class="compdivlist">'.$listc .'</div>'; } ?>
	</div>

	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
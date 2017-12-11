<?php 
/**
 * Images
 *
 * Displays information on the passed image
 *
 * @package GetSimple
 * @subpackage Images
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

exec_action('load-image');

// Variable Settings
$subPath = (isset($_GET['path'])) ? $_GET['path'] : "";
if ($subPath != '') $subPath = tsl($subPath);

$uploadsPath      = GSDATAUPLOADPATH;
$uploadsPathRel   = getRelPath(GSDATAUPLOADPATH);
$thumbPathRel     = getRelPath(GSTHUMBNAILPATH);

$src              = strippath($_GET['i']);
$thumb_folder     = GSTHUMBNAILPATH.$subPath;
$src_folder       = $uploadsPath;
$src_url          = tsl($SITEURL).$uploadsPathRel.$subPath;
$thumb_folder_rel = $thumbPathRel.$subPath;
$thumb_url        = tsl($SITEURL).$thumb_folder_rel;

if (!filepath_is_safe($src_folder . $subPath . $src,GSDATAUPLOADPATH)) redirect("upload.php");

// handle jcrop thumbnail creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ( matchArrayAll(array('x','y','w','h'),array_keys($_POST)) )) {

	exec_action('image-crop');

	$x = (int)$_POST['x'];
	$y = (int)$_POST['y'];
	$w = (int)$_POST['w'];
	$h = (int)$_POST['h'];

	$max = 10000; // set a max to prevent excessive processing injections

	if( $x<$max && $y<$max && $w<$max && $h<$max ){
		require_once('inc/imagemanipulation.php');
		$objImage = new ImageManipulation($src_folder . $subPath .$src);
		if ( $objImage->imageok ) {
			$objImage->setCrop($x,$y,$w,$h);
			$objImage->save($thumb_folder . 'thumbnail.' .$src);
			$success = i18n_r('THUMB_SAVED');
		} else {
			$error = i18n('ERROR');
		}
	}
}

$thumb_exists = $thwidth = $thheight = $thtype = $athttr = '';

list($imgwidth, $imgheight, $imgtype, $imgattr) = getimagesize($src_folder .$subPath. $src);

if (file_exists($thumb_folder . 'thumbnail.' . $src)) {
	list($thwidth, $thheight, $thtype, $athttr) = getimagesize($thumb_folder . 'thumbnail.'.$src);
	$thumb_exists = ' &nbsp; | &nbsp; <a href="'.$thumb_url . 'thumbnail.'. rawurlencode($src) . '?t='.time(). '" rel="fancybox_i" >'.i18n_r('CURRENT_THUMBNAIL').'</a> <code>'.$thwidth.'x'.$thheight.'</code>';
}else{
	// if thumb is missing recreate it
	if(genStdThumb($subPath,$src)){
		// @todo check if file exists before getimagesize
		list($thwidth, $thheight, $thtype, $athttr) = getimagesize($thumb_folder . 'thumbnail.'.$src);
		$thumb_exists = ' &nbsp; | &nbsp; <a href="'.$thumb_url . 'thumbnail.'. rawurlencode($src) .'?t='.time().' rel="fancybox_i" >'.i18n_r('CURRENT_THUMBNAIL').'</a> <code>'.$thwidth.'x'.$thheight.'</code>';
	}
}

$pagetitle = i18n_r('IMAGES').' &middot; '.var_out($src).' &middot; '.i18n_r('FILE_MANAGEMENT');
get_template('header');

function breadcrumbs($path,$root = ''){
	$paths = dirname($path);
	$file  = basename($path);
	$paths = str_replace('.','',$paths);
	if(empty($paths)) return $file;

	$paths = explode('/',$paths);
	$pathlink = $root;
	$array = '';
	foreach($paths as $crumb){
		$pathlink .= $crumb.'/';
		$array[] ='<a href="'.$pathlink.'">'.$crumb.'</a>';
	}

	$array[] = basename($path);
	return implode(' / ',$array);
}

include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	<div id="maincontent">
			
		<div class="main">
		<h3 class="floated"><?php i18n('IMG_CONTROl_PANEL');?><span class="crumbs">
		<a href="upload.php"><?php echo i18n_r('FILES'); ?></a> / <?php echo  breadcrumbs(var_out($subPath.$src),'upload.php?path=');?></span></h3>
		<div class="edit-nav clearfix" >
			<?php exec_action(get_filename_id().'-edit-nav'); ?>
		</div>
		<?php exec_action(get_filename_id().'-body');
			
			echo '<div class="thumbs clearfix">';

			echo '<div class="thumbcontainer"><a href="'.$src_url .rawurlencode($src).'" rel="fancybox_i" >';
			// echo '<div><img src="'.$thumb_url . 'thumbsm.'. rawurlencode($src).'"></div>';
			echo '<div><img src="'.$src_url . rawurlencode($src).'"></div>';
			echo i18n_r('ORIGINAL_IMG') .'<br/><code>'.$imgwidth.'x'.$imgheight .'</code>';
			echo "</a></div>";

			echo '<div class="thumbcontainer"><a href="'.$thumb_url . 'thumbnail.'. rawurlencode($src) .'?t='.time().'" rel="fancybox_i" >';
			echo '<div><img src="'.$thumb_url . 'thumbnail.'. rawurlencode($src).'?'.time().'"></div>';
			echo i18n_r('CURRENT_THUMBNAIL') .'<br/><code>'.$thwidth.'x'.$thheight .'</code>';
			echo "</a></div>";
			
			echo "</div>";


			?>

			<form>
				<select class="text" id="img-info" >
					<option selected value="code-img-link" ><?php i18n('LINK_ORIG_IMG');?></option>
					<option value="code-img-html" ><?php i18n('HTML_ORIG_IMG');?></option>
					<?php if(!empty($thumb_exists)) { ?>
					<option value="code-thumb-html" ><?php i18n('HTML_THUMBNAIL');?></option>
					<option value="code-thumb-link" ><?php i18n('LINK_THUMBNAIL');?></option>
					<option value="code-imgthumb-html" ><?php i18n('HTML_THUMB_ORIG');?></option>
					<?php } ?>
				</select>
				<textarea class="copykit" ><?php echo $src_url. rawurlencode($src); ?></textarea>
				<p style="color:#666;font-size:11px;margin:-10px 0 0 0"><a href="javascript:void(0)" class="select-all" ><?php i18n('CLIPBOARD_INSTR');?></a></p>
			</form>
			<div class="toggle">
				<p id="code-img-html">&lt;img src="<?php echo $src_url. rawurlencode($src); ?>" class="gs_image" height="<?php echo $imgheight; ?>" width="<?php echo $imgwidth; ?>" alt=""></p>
				<p id="code-img-link"><?php echo $src_url. rawurlencode($src); ?></p>
				<?php if(!empty($thumb_exists)) { ?>
				<p id="code-thumb-html">&lt;img src="<?php echo $thumb_url.'thumbnail.'. rawurlencode($src); ?>" class="gs_image gs_thumb" height="<?php echo $thheight; ?>" width="<?php echo $thwidth; ?>" alt=""></p>
				<p id="code-thumb-link"><?php echo $thumb_url.'thumbnail.'.rawurlencode($src); ?></p>
				<p id="code-imgthumb-html">&lt;a href="<?php echo $src_url. rawurlencode($src); ?>" class="gs_image_link" >&lt;img src="<?php echo $thumb_url.'thumbnail.'.rawurlencode($src); ?>" class="gs_thumb" height="<?php echo $thheight; ?>" width="<?php echo $thwidth; ?>" alt="" />&lt;/a></p>
				<?php } ?>
			</div>
			<?php 
				exec_action('image-extras'); // @hook image-extras provide extra image features here
			?>
	</div>

<?php
$jcrop = !empty($thumb_exists);
if($jcrop){ ?>
	
	<div class="main">
	<h3 class="floated"><?php i18n('CREATE_THUMBNAIL');?></h3>
	<div class="clearfix" ></div>
	<div style="color:#666;font-size:11px;"><?php i18n('CROP_INSTR_NEW');?></div><br/>
	<form id="jcropform" class="noenter" action="<?php myself(); ?>?i=<?php echo rawurlencode($src); ?>&amp;path=<?php echo $subPath; ?>" method="post" onsubmit="return checkCoords();">
	<div id="jcrop_open">
	    <img src="<?php echo $src_url .rawurlencode($src); ?>" id="cropbox" />
		<div>
		<div id="handw" class="" ><?php i18n('SELECT_DIMENTIONS'); ?><br /><span id="picw">0</span> x <span id="pich">0</span></div>
	    <input id="cropsave" type="submit" class="submit" value="<?php i18n('CREATE_THUMBNAIL');?>" /> &nbsp;
	    </div>
	    <div class="clearfix"></div>
	    <Br/>
	    <!-- This is the form that our event handler fills -->
	    <a id="cropinputoggle" href="javascript:void(0);" data-toggle="cropinputs"><?php i18n('CROP_TOGGLE_INPUTS'); ?></a>
	    <div id="cropinputs" class="toggle">
	      <div class="inline"><label for="x">X</label><input class="jcropinput" type="" id="x" name="x" value="0"/></div>
	      <div class="inline"><label for="y">Y</label><input class="jcropinput" type="" id="y" name="y" value="0"/></div>
	      <div class="inline"><label for="w">W</label><input class="jcropinput" type="" id="w" name="w" value="0"/></div>
	      <div class="inline"><label for="h">H</label><input class="jcropinput" type="" id="h" name="h" value="0"/></div>
	    </div>
	    </form>
	</div>
	</div>
	
<?php } ?>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-files.php'); ?>
	</div>	

	<script>
	  jQuery(document).ready(function() { 
	    	
	  		jcrop_container = $("#cropbox");

			$(window).load(function(){
				jcrop_container.Jcrop({
					onChange: updateCoords,
					onSelect: updateCoords,
					onRelease: updateCoordsReset,
					// onDblClick: jcropDblClick,
					boxWidth: $('#jcrop_open').width(), 
					boxHeight: 800,
					bgColor: 'black',
					bgOpacity: 0.3,
					bgFade: true,
					borderOpacity: 0.8,
					handleOpacity: 0.4,
					drawBorders: false,
					handleSize: '5px'
			  	});

				getApi = function(){
					return 	jcrop_container.Jcrop('api');
				}

				jcrop_container.data('jcrop',getApi());

				getOptions = function(){
					api = getApi();
					return api.opt;
				}

				jcropClear = function(){
					getApi().deleteAll();
				}

				// custom function to clear selection, jcrop no longer includes this functionality
				$.Jcrop.prototype.deleteAll = function() {
				  var _this = this;
				  this.ui.multi.forEach(function(item){
				    // _this.deleteSelection(item); // not sure the difference
				  	_this.removeSelection(item)
				  });
				  $('.jcrop-shades > div').width(0).height(0); // remove shades
				  updateCoordsReset();
				};

				// bind to events to catch control/command for aspect control and Esq for clear
				// @todo blocking F5 in chrome for some reason, it should not block any propagation
				$('.jcrop-active').bind('keydown mousemove mousedown',function (e) {
					// console.log('event: ' + e.type + " " + e.keyCode);
					if(e.type == 'keydown' && e.keyCode == 27){
						jcropClear();
					}
					
					var options = getOptions();

					if(e.ctrlKey || e.metaKey) {
						// console.log(options);
						if(options.aspectRatio != 1){
							// console.log("aspectratio ON");
							getApi().setOptions({ aspectRatio: 1 });
							// jcrop_api.focus(); // probably not needed setoptions reloads the entire thing
							return;
						}
					} 
					else {
						if(options.aspectRatio == 1){
							// console.log("aspectratio OFF");
							getApi().setOptions({ aspectRatio: 0 });
							// api.focus();
						}							
					}
				});

			});
		
		});
	</script>
	
	</div>
<?php get_template('footer'); ?>

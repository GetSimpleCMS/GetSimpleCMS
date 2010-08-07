<?php 
/****************************************************
*
* @File: 		log.php
* @Package:	GetSimple
* @Action:	Displays the log file passed to it 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable Settings
login_cookie_check();

$src = strippath($_GET['i']);
$thumb_folder = $relative.'data/thumbs/';
$src_folder = $relative.'data/uploads/';

if (!is_file($src_folder . $src)) header("Location: upload.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	require('inc/imagemanipulation.php');
	
	$objImage = new ImageManipulation($src_folder . $src);
	if ( $objImage->imageok ) 
	{
		$objImage->setCrop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
		//$objImage->show();
		$objImage->save($thumb_folder . 'thumbnail.' .$src);
		$success = "Thumbnail Saved";
	} 
	else 
	{
		echo 'Error!';
	}
}

list($imgwidth, $imgheight, $imgtype, $imgattr) = getimagesize($src_folder . urlencode($src));

if (file_exists($thumb_folder . 'thumbnail.' . $src)) 
{
	list($thwidth, $thheight, $thtype, $athttr) = getimagesize($thumb_folder . urlencode('thumbnail.'.$src));
	$thumb_exists = ' &nbsp; | &nbsp; <a href="'.$thumb_folder . 'thumbnail.'. $src .'" rel="facybox" >'.$i18n['CURRENT_THUMBNAIL'].'</a> <code>'.$thwidth.'x'.$thheight.'</code>';
} 
else 
{
	$thumb_exists = ' &nbsp; | &nbsp; <a href="#jcrop_open">'.$i18n['CREATE_ONE'].'</a>';
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '.$i18n['FILE_MANAGEMENT'].' &raquo; '.$i18n['IMAGES']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['FILE_MANAGEMENT'];?> <span>&raquo;</span> <?php echo $i18n['IMAGES'];?></h1>
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	<div id="maincontent">
			
		<div class="main">
		<h3><?php echo $i18n['IMG_CONTROl_PANEL'];?></h3>
	
			<?php echo '<p><a href="'.$src_folder . $src .'" rel="facybox" >'.$i18n['ORIGINAL_IMG'].'</a> <code>'.$imgwidth.'x'.$imgheight .'</code>'. $thumb_exists .'</p>'; ?>

			<form><select class="text" id="img-info">
				<option selected="selected" value="code-img-html" ><?php echo $i18n['HTML_ORIG_IMG'];?></option>
				<option value="code-img-link" ><?php echo $i18n['LINK_ORIG_IMG'];?></option>
				<option value="code-thumb-html" ><?php echo $i18n['HTML_THUMBNAIL'];?></option>
				<option value="code-thumb-link" ><?php echo $i18n['LINK_THUMBNAIL'];?></option>
				<option value="code-imgthumb-html" ><?php echo $i18n['HTML_THUMB_ORIG'];?></option>
			</select>
			<textarea class="copykit" >&lt;img src="<?php echo tsl($SITEURL) .'data/uploads/'. $src; ?>" class="gs_image" alt=""></textarea>
			<p style="color:#666;font-size:11px;margin:-10px 0 0 0"><?php echo $i18n['CLIPBOARD_COPY'];?>: <a href="#" class="select-all" ><?php echo $i18n['CLIPBOARD_INSTR'];?></a></p>
		</form>
			<div class="toggle">
				<p id="code-img-html">&lt;img src="<?php echo tsl($SITEURL) .'data/uploads/'. $src; ?>" class="gs_image" alt=""></p>
				<p id="code-img-link"><?php echo tsl($SITEURL) .'data/uploads/'. $src; ?></p>
				<p id="code-thumb-html">&lt;img src="<?php echo tsl($SITEURL) .'data/thumbs/thumbnail.'.$src; ?>" class="gs_image gs_thumb" alt=""></p>
				<p id="code-thumb-link"><?php echo tsl($SITEURL) .'data/thumbs/thumbnail.'.$src; ?></p>
				<p id="code-imgthumb-html">&lt;a href="<?php echo tsl($SITEURL) .'data/uploads/'. $src; ?>" class="gs_image_link" >&lt;img src="<?php echo tsl($SITEURL) .'data/thumbs/thumbnail.'.$src; ?>" class="gs_thumb" alt="" />&lt;/a></p>
			</div>
	</div>
	
	<div id="jcrop_open" class="main">

    <img src="<?php echo $src_folder . $src; ?>" id="cropbox" />
    

		<div id="handw" class="toggle" ><?php echo $i18n['SELECT_DIMENTIONS']; ?><br /><span id="picw"></span> x <span id="pich"></span></div>
 
    <!-- This is the form that our event handler fills -->
    <form id="jcropform" action="image.php?i=<?php echo $src; ?>" method="post" onsubmit="return checkCoords();">
      <input type="hidden" id="x" name="x" />
      <input type="hidden" id="y" name="y" />
      <input type="hidden" id="w" name="w" />
      <input type="hidden" id="h" name="h" />
      <input type="submit" class="submit" value="<?php echo $i18n['CREATE_THUMBNAIL'];?>" /> &nbsp; <span style="color:#666;font-size:11px;"><?php echo $i18n['CROP_INSTR'];?></span>

    </form>

		</div>
	
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-files.php'); ?>
	</div>	
	
	<div class="clear"></div>
	
	<script language="Javascript">
	  jQuery(document).ready(function() { 
	    		
			$(window).load(function(){
			var api = $.Jcrop('#cropbox',{
		    onChange: updateCoords,
		    onSelect: updateCoords,
		    boxWidth: 585, 
		    boxHeight: 500
		  }); 
		  var isCtrl = false;
			$(document).keyup(function (e) {
				api.setOptions({ aspectRatio: 0 });
				api.focus();
				if(e.which == 17) isCtrl=false;
			}).keydown(function (e) {
				if(e.which == 17) isCtrl=true;
				if(e.which == 81 && isCtrl == true) {
					api.setOptions({ aspectRatio: 1 });
					api.focus();
				}
			});
		});
		
	});
	</script>
	
	</div>
<?php get_template('footer'); ?>
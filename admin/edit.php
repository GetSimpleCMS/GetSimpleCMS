<?php
/**
 * Page Edit
 *
 * Edit or create new pages for the website.	
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');

// Variable settings
$userid = login_cookie_check();

// Get passed variables
$id 		=  isset($_GET['id']) ? $_GET['id'] : null;
$uri    = isset($_GET['uri']) ? $_GET['uri'] : null; 
$ptype    = isset($_GET['type']) ? $_GET['type'] : null;    
$nonce    = isset($_GET['nonce']) ? $_GET['nonce'] : null;
$path 		= GSDATAPAGESPATH;

// Page variables reset
$theme_templates = ''; 
$parents_list = ''; 
$keytags = '';
$parent = '';
$template = '';
$menuStatus = ''; 
$private = ''; 
$menu = ''; 
$content = '';
$title = '';
$url = '';
$metak = '';
$metad = '';

if ($id)
{
	// get saved page data
	$file = $id .'.xml';
	
	if (!file_exists($path . $file))
	{ 
		redirect('pages.php?error='.urlencode(i18n_r('PAGE_NOTEXIST')));
	}

	$data_edit = getXML($path . $file);
	$title = stripslashes($data_edit->title);
	$pubDate = $data_edit->pubDate;
	$metak = stripslashes($data_edit->meta);
	$metad = stripslashes($data_edit->metad);
	$url = $data_edit->url;
	$content = stripslashes($data_edit->content);
	$template = $data_edit->template;
	$parent = $data_edit->parent;
	$menu = stripslashes($data_edit->menu);
	$private = $data_edit->private;
	$menuStatus = $data_edit->menuStatus;
	$menuOrder = $data_edit->menuOrder;
	$buttonname = i18n_r('BTN_SAVEUPDATES');
} 
else 
{
	$buttonname = i18n_r('BTN_SAVEPAGE');
}


// MAKE SELECT BOX OF AVAILABLE TEMPLATES
if ($template == '') { $template = 'template.php'; }

$themes_path = GSTHEMESPATH . $TEMPLATE;
$themes_handle = opendir($themes_path) or die("Unable to open ". GSTHEMESPATH);		
while ($file = readdir($themes_handle))	{		
	if( isFile($file, $themes_path, 'php') ) {		
		if ($file != 'functions.php') {		
      $templates[] = $file;		
    }		
	}		
}		
		
sort($templates);

foreach ($templates as $file)
{
	if ($template == $file)
	{ 
		$sel="selected"; 
	} 
	else
	{ 
		$sel=""; 
	}
	
	if ($file == 'template.php')
	{ 
		$templatename=i18n_r('DEFAULT_TEMPLATE'); 
	} 
	else 
	{ 
		$templatename=$file;
	}
	
	$theme_templates .= '<option '.$sel.' value="'.$file.'" >'.$templatename.'</option>';
}

// SETUP CHECKBOXES
$sel_m = ($menuStatus != '') ? 'checked' : '' ;
$sel_p = ($private != '') ? 'checked' : '' ;
if ($menu == '') { $menu = $title; } 
?>		


<?php get_template('header', cl($SITENAME).' &raquo; '.i18n_r('PAGE_MANAGEMENT')); ?>
	
	<h1>
		<a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php i18n('PAGE_MANAGEMENT'); ?> <span>&raquo;</span> <?php if(isset($data_edit)) { echo i18n_r('PAGE').' &lsquo;<span class="filename" >'. $url .'</span>&rsquo;'; } else { echo i18n_r('NEW_PAGE'); } ?>		
	</h1>
	
	<?php 
		include('template/include-nav.php');
		include('template/error_checking.php'); 
	?>
	
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">
		
		<h3 class="floated"><?php if(isset($data_edit)) { i18n('PAGE_EDIT_MODE'); } else { i18n('CREATE_NEW_PAGE'); } ?></h3>	

		<!-- pill edit navigation -->
		<div class="edit-nav" >
			<?php 
			if( (isset($id)) && ($private != 'Y' )) {
				echo '<a href="', find_url($url, $parent) ,'" target="_blank" accesskey="', find_accesskey(i18n_r('VIEW')), '" >', i18n_r('VIEW'), ' </a>';
			} 
			?>
			<a href="#" id="metadata_toggle" accesskey="<?php echo find_accesskey(i18n_r('PAGE_OPTIONS'));?>" ><?php i18n('PAGE_OPTIONS'); ?></a>
			<div class="clear" ></div>
		</div>	
			
		<form class="largeform" id="editform" action="changedata.php" method="post" accept-charset="utf-8" >
			<input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("edit", "edit.php"); ?>" />			


			<!-- page title toggle screen -->
			<p id="edit_window">
				<label for="post-title" style="display:none;"><?php i18n('PAGE_TITLE'); ?></label>
				<input class="text title" id="post-title" name="post-title" type="text" value="<?php echo $title; ?>" placeholder="<?php i18n('PAGE_TITLE'); ?>" />
			</p>
				

			<!-- metadata toggle screen -->
			<div style="display:none;" id="metadata_window" >
			<div class="leftopt">
				<p>
					<label for="post-id"><?php i18n('SLUG_URL'); ?>:</label>
          <input class="text short" type="text" id="post-id" name="post-id" value="<?php echo $url; ?>" <?php echo ($url=='index'?'readonly="readonly" ':''); ?>/>
				</p>
				<p>
					<label for="post-parent"><?php i18n('PARENT_PAGE'); ?>:</label>
					<select class="text short" id="post-parent" name="post-parent"> 
						<?php 
						$path 		= GSDATAPAGESPATH;
						$counter 	= '0';
						
						//get all pages
						$filenames = getFiles($path);
						
						$count="0";
						$pagesArray = array();
						if (count($filenames) != 0) { 
							foreach ($filenames as $file) {
								if (isFile($file, $path, 'xml')) {
									$data = getXML($path .$file);
									$status = $data->menuStatus;
									$pagesArray[$count]['parent'] = $data->parent;
									if ($data->parent != '') { 
										$parentdata = getXML($path . $data->parent .'.xml');
										$parentTitle = $parentdata->title;
										$pagesArray[$count]['sort'] = $parentTitle .' '. $data->title;
									} else {
										$pagesArray[$count]['sort'] = $data->title;
									}
									$pagesArray[$count]['url'] = $data->url;
									$parentTitle = '';
									$count++;
								}
							}
						}
						$pagesSorted = subval_sort($pagesArray,'sort');
						$ret=get_pages_menu_dropdown('','',0);
						
						if ($parent == null) { $none="selected"; } else { $none=""; }
						
						// Create base option
						echo '<option '.$none.' value="" ></option>';
						echo $ret;
						?>
					</select>
				</p>			
				<p>
					<label for="post-template"><?php i18n('TEMPLATE'); ?>:</label>
					<select class="text short" id="post-template" name="post-template" >
						<?php echo $theme_templates; ?>
					</select>
				</p>
				
				<p class="inline">
					<label for="post-menu-enable" ><?php i18n('ADD_TO_MENU'); ?></label> &ndash; <span><a href="navigation.php" rel="facybox" ><?php echo strip_tags(i18n_r('VIEW')); ?></a></span>&nbsp;&nbsp;&nbsp;<input type="checkbox" id="post-menu-enable" name="post-menu-enable" <?php echo $sel_m; ?> /><br />
				</p>
				<div id="menu-items">
					<span style="float:left;width:84%" ><label for="post-menu"><?php i18n('MENU_TEXT'); ?></label></span><span style="float:left;width:10%;" ><label for="post-menu-order"><?php i18n('PRIORITY'); ?></label></span>
					<div class="clear"></div>
					<input class="text" style="width:79%;" id="post-menu" name="post-menu" type="text" value="<?php echo $menu; ?>" />&nbsp; <select class="text"  style="width:16%" id="post-menu-order" name="post-menu-order" >
					<?php if(isset($menuOrder)) { 
						if($menuOrder == 0) {
							echo '<option value="" selected>-</option>'; 
						} else {
							echo '<option value="'.$menuOrder.'" selected>'.$menuOrder.'</option>'; 
						}
					} ?>
						<option value="">-</option>
						<?php
						$i = 1;
						while ($i <= 20) { 
							echo '<option value="'.$i.'">'.$i.'</option>';
							$i++;
						}
						?>
					</select>
				</div>				
			</div>
			
			<div class="rightopt">
				<p>
					<label for="post-metak"><?php i18n('TAG_KEYWORDS'); ?>:</label>
					<input class="text short" id="post-metak" name="post-metak" type="text" value="<?php echo $metak; ?>" />
				</p>
				<p>
					<label for="post-metad"><?php i18n('META_DESC'); ?>:</label>
					<textarea class="text" id="post-metad" name="post-metad" ><?php echo $metad; ?></textarea>
				</p>
				<p class="inline" id="post-private-wrap" >
					<label for="post-private" ><?php i18n('KEEP_PRIVATE'); ?></label> &nbsp;&nbsp;&nbsp; <input type="checkbox" id="post-private" name="post-private" <?php echo $sel_p; ?> />
				</p>

			</div>
			<div class="clear"></div>
			<?php exec_action('edit-extras'); ?>		

			</div>	<!-- / metadata toggle screen -->
				
		
			<!-- page body -->
			<p>
				<label for="post-content" style="display:none;"><?php i18n('LABEL_PAGEBODY'); ?></label>
				<textarea id="post-content" name="post-content"><?php echo $content; ?></textarea>
			</p>
			
			<?php exec_action('edit-content'); ?> 
			
			<?php if(isset($data_edit)) { 
				echo '<input type="hidden" name="existing-url" value="'. $url .'" />'; 
			} ?>	
			
			<p id="submit_line" >
				<span><input class="submit" type="submit" name="submitted" value="<?php echo $buttonname; ?>" onclick="warnme=false;" /></span>&nbsp;&nbsp;
				<?php i18n('OR'); ?>&nbsp;&nbsp;
				<a class="cancel" href="pages.php?cancel" title="<?php i18n('CANCEL'); ?>"><?php i18n('CANCEL'); ?></a><?php if(isset($url) && $url!='index' ) { ?>&nbsp;/&nbsp;<a class="cancel" href="deletefile.php?id=<?php echo $url; ?>&amp;nonce=<?php echo get_nonce("delete","deletefile.php"); ?>" title="<?php i18n('DELETEPAGE_TITLE'); ?>" ><?php i18n('ASK_DELETE'); ?></a><?php } ?>
			</p>
			
			<small><?php 
					if (isset($pubDate)) { 
						echo i18n_r('LAST_SAVED').': '. lngDate($pubDate).'&nbsp; ';
					}
					if ( file_exists(GSBACKUPSPATH.'pages/'.$url.'.bak.xml') ) {	
						echo '-&nbsp; <a href="backup-edit.php?p=view&amp;id='.$url.'" >'.i18n_r('BACKUP_AVAILABLE').'</a>';
					} 
			?></small>
		</form>
		
		<?php 
			if (defined('GSEDITORHEIGHT')) { $EDHEIGHT = GSEDITORHEIGHT .'px'; } else {	$EDHEIGHT = '500px'; }
			if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {	$EDLANG = i18n_r('CKEDITOR_LANG'); }
			if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {	$EDTOOL = 'basic'; }
			if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {	$EDOPTIONS = ''; }
			
			if ($EDTOOL == 'advanced') {
				$toolbar = "
						['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
	          '/',
	          ['Styles','Format','Font','FontSize']
	      ";
			} elseif ($EDTOOL == 'basic') {
				$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
			} else {
				$toolbar = GSEDITORTOOL;
			}
		?>
		<?php if ($HTMLEDITOR != '') { ?>
		<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>

			<script type="text/javascript">
			
			var editor = CKEDITOR.replace( 'post-content', {
	        skin : 'getsimple',
	        forcePasteAsPlainText : true,
	        language : '<?php echo $EDLANG; ?>',
	        defaultLanguage : 'en',
	        <?php if (file_exists(GSTHEMESPATH .$TEMPLATE."/editor.css")) { 
	        	$fullpath = suggest_site_path();
	        ?>
            contentsCss: '<?php echo $fullpath; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
          <?php } ?>
	        entities : true,
	        uiColor : '#FFFFFF',
			height: '<?php echo $EDHEIGHT; ?>',
			baseHref : '<?php echo $SITEURL; ?>',
	        toolbar : 
	        [
	        <?php echo $toolbar; ?>
			]
			<?php echo $EDOPTIONS; ?>,
					tabSpaces:10,
	        filebrowserBrowseUrl : 'filebrowser.php?type=all',
					filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
	        filebrowserWindowWidth : '730',
	        filebrowserWindowHeight : '500'
    		});
			</script>
			
			<?php
				# CKEditor setup functions
				ckeditor_add_page_link();
				exec_action('html-editor-init'); 
			?>
			
		<?php } ?>
		
		
		
		<script type="text/javascript">
			/* Warning for unsaved Data */
    	var warnme = false;	
			window.onbeforeunload = function () {
		    if (warnme) {
		      return "<?php i18n('UNSAVED_INFORMATION'); ?>";
		    }
			}
			
			jQuery(document).ready(function() { 
				$('input,textarea,select').change(function(){
	    		warnme = true;
	    	});	
			});
		</script>
	</div>
	</div><!-- end maincontent -->
	
	
	<div id="sidebar" >
		<?php include('template/sidebar-pages.php'); ?>	
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>

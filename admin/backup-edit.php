<?php
/****************************************************
*
* @File: 		backup-edit.php
* @Package:	GetSimple
* @Action:	View the current backup of a given page. 	
*
*****************************************************/

// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable Settings
$userid = login_cookie_check();

// get page url to display
if ($_GET['id'] != '') 
{
	$id = $_GET['id'];
	$file = $id .".bak.xml";
	$path = GSBACKUPSPATH .'pages/';
	
	$data = getXML($path . $file);
	$title = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
	$pubDate = $data->pubDate;
	$parent = $data->parent;
	$metak = html_entity_decode($data->meta, ENT_QUOTES, 'UTF-8');
	$metad = html_entity_decode($data->metad, ENT_QUOTES, 'UTF-8');
	$url = $data->url;
	$content = html_entity_decode($data->content, ENT_QUOTES, 'UTF-8');
	$private = $data->private;
	$template = $data->template;
	$menu = html_entity_decode($data->menu, ENT_QUOTES, 'UTF-8');
	$menuStatus = $data->menuStatus;
	$menuOrder = $data->menuOrder;
} 
else 
{
	header('Location: backups.php?upd=bak-err');
}

if ($private != '' ) { $private = '('.$i18n['PRIVATE_SUBTITLE'].')'; } else { $private = ''; }
if ($menuStatus == '' ) { $menuStatus = $i18n['NO']; } else { $menuStatus = $i18n['YES']; }

// are we going to do anything with this backup?
if ($_GET['p'] != '') 
{
	$p = $_GET['p'];
} 
else 
{
	header('Location: backups.php?upd=bak-err');
}

if ($p == 'delete') 
{
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "delete", "backup-edit.php"))
		die("CSRF detected!");	

	delete_bak($id);
	header("Location: backups.php?upd=bak-success&id=".$id);
} 
elseif ($p == 'restore') 
{
	$nonce = $_GET['nonce'];
	if(!check_nonce($nonce, "restore", "backup-edit.php"))
		die("CSRF detected!");	

	restore_bak($id);
	header("Location: edit.php?id=". $id ."&upd=edit-success&type=restore");
}
?>

<?php get_template('header', cl($SITENAME).' &raquo; '. $i18n['BAK_MANAGEMENT'].' &raquo; '.$i18n['VIEWPAGE_TITLE']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $i18n['BAK_MANAGEMENT']; ?> <span>&raquo;</span> <?php echo $i18n['VIEWING'];?> &lsquo;<span class="filename" ><?php echo @$url; ?></span>&rsquo;</h1>
	
	<?php include('template/include-nav.php'); ?>
	<?php include('template/error_checking.php'); ?>
	<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main" >
		<label><?php echo $i18n['BACKUP_OF'];?> &lsquo;<em><?php echo @$url; ?></em>&rsquo;</label>
		
		<div class="edit-nav" >
			 <a href="backups.php" accesskey="c" ><?php echo $i18n['ASK_CANCEL'];?></a> <a href="backup-edit.php?p=restore&id=<?php echo $id; ?>&nonce=<?php echo get_nonce("restore", "backup-edit.php"); ?>" accesskey="r" ><?php echo $i18n['ASK_RESTORE'];?></a> <a href="backup-edit.php?p=delete&id=<?php echo $id; ?>&nonce=<?php echo get_nonce("delete", "backup-edit.php"); ?>" title="<?php echo $i18n['DELETEPAGE_TITLE']; ?>: <?php echo $title; ?>?" accesskey="d" class="delconfirm" ><?php echo $i18n['ASK_DELETE'];?></a>
			<div class="clear"></div>
		</div>
		
		<table class="simple" >
		<tr><td style="width:105px;" ><b><?php echo $i18n['PAGE_TITLE'];?>:</b></td><td><b><?php echo cl(@$title); ?></b> <?php echo $private; ?></td></tr>
		<tr><td><b><?php echo $i18n['BACKUP_OF'];?>:</b></td><td>
			<?php 
			if(isset($id)) {
					echo '<a target="_blank" href="'. find_url($url, $parent) .'">'. find_url($url, $parent) .'</a>'; 
			} 
			?>
			
			
		</td></tr>
		<tr><td><b><?php echo $i18n['DATE'];?>:</b></td><td><?php echo lngDate($pubDate); ?></td></tr>
		<tr><td><b><?php echo $i18n['TAG_KEYWORDS'];?>:</b></td><td><em><?php echo @$metak; ?></em></td></tr>
		<tr><td><b><?php echo $i18n['META_DESC'];?>:</b></td><td><em><?php echo @$metad; ?></em></td></tr>
		<tr><td><b><?php echo $i18n['MENU_TEXT'];?>:</b></td><td><?php echo @$menu; ?></td></tr>
		<tr><td><b><?php echo $i18n['PRIORITY'];?>:</b></td><td><?php echo @$menuOrder; ?></td></tr>
		<tr><td><b><?php echo $i18n['ADD_TO_MENU'];?>?</b></td><td><?php echo @$menuStatus; ?></td></tr>
		</table>
		
		<textarea id="codetext" style="background:#fefefe;width:570px;height:400px;padding:4px;border:1px solid #ccc;" ><?php echo stripslashes(htmlspecialchars_decode(@$content, ENT_QUOTES)); ?></textarea>

		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>
	
	<div class="clear"></div>
	</div>
<?php get_template('footer'); ?>
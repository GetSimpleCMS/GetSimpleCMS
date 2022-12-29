<?php
function i18n_gallery_compare_title($a, $b) {
  return strcmp($a['title'],$b['title']);
}

function i18n_gallery_delete_it($name) {
  if (!copy(GSDATAPATH.I18N_GALLERY_DIR.$name.'.xml', GSBACKUPSPATH.I18N_GALLERY_DIR.$name.'.xml')) return false;
  if (!unlink(GSDATAPATH.I18N_GALLERY_DIR.$name.'.xml')) return false;
  return true;
}

require_once(GSPLUGINPATH.'i18n_gallery/gallery.class.php');
$success = false;
if (!I18nGallery::checkPrerequisites()) {
  $msg = i18n_r('i18n_gallery/MISSING_DIR');
} else if (isset($_GET['delete'])) {
  $name = $_GET['name'];
  if (i18n_gallery_delete_it($name)) {
    $msg = i18n_r('i18n_gallery/DELETE_SUCCESS').' <a href="load.php?id=i18n_gallery&amp;edit&amp;name='.$name.'&amp;new='.$name.'&amp;undo">' . i18n_r('UNDO') . '</a>';
    $success = true;
  } else {
    $msg = i18n_r('i18n_gallery/DELETE_FAILURE');
  }
}
$galleries = array();
$gdir = GSDATAPATH . I18N_GALLERY_DIR;
$dir_handle = @opendir($gdir);
while ($filename = readdir($dir_handle)) {
  if (strrpos($filename,'.xml') === strlen($filename)-4) {
    $data = getXML($gdir . $filename);
    $galleries[] = array('name' => (string) $data->name, 'title' => (string) $data->title, 'pubDate' => (string) $data->pubDate);
  }
}
usort($galleries, 'i18n_gallery_compare_title');
$viewlink = function_exists('find_i18n_url') ? find_i18n_url('index',null) : find_url('index',null);
$viewlink .= (strpos($viewlink,'?') === false ? '?' : '&amp;') . 'preview-gallery&amp;name=';

?>
    <h3 class="floated" style="float:left"><?php echo i18n_r('i18n_gallery/OVERVIEW_HEADER'); ?></h3>
    <p class="clear"><?php i18n('i18n_gallery/OVERVIEW_DESCR'); ?></p>
		<table id="editgalleries" class="edittable highlight">
      <thead>
        <tr>
          <th style='font-weight:800'><?php i18n('i18n_gallery/GALLERY_TITLE'); ?></th>
          <th style='font-weight:800'><?php i18n('i18n_gallery/GALLERY_CODE'); ?></th>
          <th style='font-weight:800'><?php i18n('DATE'); ?></th>
          <th></th>
        </tr>
      <tbody>
<?php foreach ($galleries as $gallery) { ?>
        <tr>
          <td><a href="load.php?id=i18n_gallery&amp;edit&amp;name=<?php echo @$gallery['name']; ?>" title="<?php echo i18n_r('i18n_gallery/EDITGALLERY_TITLE').': '.cl(@$gallery['title']); ?>"><?php echo htmlspecialchars(@$gallery['title']); ?></a></td>
          <td>(% gallery name=<?php echo @$gallery['name']; ?> %)</td>
          <td><?php echo shtDate($gallery['pubDate']); ?></td>
          <td class="secondarylink">
      		  <a href="<?php echo $viewlink.@$gallery['name']; ?>" title="<?php echo i18n_r('i18n_gallery/VIEWGALLERY_TITLE').': '. cl($gallery['title']); ?>" target="_blank">#</a>
          </td>
          <td class="delete">
            <a href="load.php?id=i18n_gallery&amp;overview&amp;name=<?php echo @$gallery['name']; ?>&amp;delete" title="<?php echo i18n_r('i18n_gallery/DELETEGALLERY_TITLE').': '. cl($gallery['title']); ?>">X</a>
          </td>
        </tr>
<?php } ?>
      </tbody>
    </table>
    <p style="text-align:center; margin:20px 0 0 0;">&copy; 2011-2013 Martin Vlcek - Please consider a <a href="http://mvlcek.bplaced.net/">Donation</a></p>
    <script type="text/javascript">
      $(function() {
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
	      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>


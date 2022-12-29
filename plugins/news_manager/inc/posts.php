<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager post management functions.
 */


/*******************************************************
 * @function nm_edit_post
 * @param $slug - post slug
 * @action edit or create posts
 */
function nm_edit_post($slug = '') {
  global $HTMLEDITOR;
  $newpost = ($slug === '');
  if ($newpost) {
    $title   = '';
    $date    = '';
    $time    = '';
    $tags    = '';
    $private = '';
    $metad   = '';
    $image   = '';
    $content = '';
  } else {
    $file = NMPOSTPATH.$slug.'.xml';
    if (dirname(realpath($file)) != realpath(NMPOSTPATH)) die(''); // path traversal
    # get post data, if it exists
    $data    = @getXML($file);
    $title   = @stripslashes($data->title);
    $date    = isset($data->date) ? date('Y-m-d', strtotime($data->date)) : '';
    $time    = isset($data->date) ? date('H:i', strtotime($data->date)) : '';
    $tags    = isset($data->tags) ? str_replace(',', ', ', stripslashes($data->tags)) : '';
    $private = @$data->private != '' ? 'checked' : '';
    $metad   = @stripslashes($data->metad);
    $image   = @stripslashes($data->image);
    $content = @stripslashes($data->content);
    if (isset($data->author))
      $author = stripslashes($data->author);
  }
  # show edit post form
  include(NMTEMPLATEPATH . 'edit_post.php');
  if (!$newpost) {
    $mtime = date(i18n_r('DATE_AND_TIME_FORMAT'), filemtime($file));
    echo '<small>',i18n_r('news_manager/LAST_SAVED'),': ',$mtime,'</small>';
  }
  # wysiwyg editor
  if (isset($HTMLEDITOR) && $HTMLEDITOR != '')
    include(NMTEMPLATEPATH . 'ckeditor.php');
}


/*******************************************************
 * @function nm_save_post
 * @action write $_POST data to xml file
 */
function nm_save_post() {
  # create a backup if necessary
  if (isset($_POST['current-slug'])) {
    $file = $_POST['current-slug'] . '.xml';
    if (dirname(realpath(NMPOSTPATH.$file)) != realpath(NMPOSTPATH)) die(''); // path traversal
    @nm_rename_file(NMPOSTPATH . $file, NMBACKUPPATH . $file);
  }
  # empty titles are not allowed
  if (empty($_POST['post-title']) || trim($_POST['post-title']) == '')
    $_POST['post-title'] = '[No Title]';
  # set initial slug and filename
  if (!empty($_POST['post-slug']))
    $slug = nm_create_slug($_POST['post-slug']);
  else {
    $slug = nm_create_slug($_POST['post-title']);
    if ($slug == '') $slug = 'post';
  }
  $file = NMPOSTPATH.$slug.'.xml';
  # do not overwrite other posts
  if (file_exists($file)) {
    $count = 1;
    $file = NMPOSTPATH.$slug.'-'.$count.'.xml';
    while (file_exists($file))
      $file = NMPOSTPATH.$slug.'-'.++$count.'.xml';
    $slug = basename($file, '.xml');
  }
  # create undo target if there's a backup available
  if (isset($_POST['current-slug']))
    $backup = $slug . ':' . $_POST['current-slug'];
  # collect $_POST data
  $title     = safe_slash_html($_POST['post-title']);
  $timestamp = strtotime($_POST['post-date'] . ' ' . $_POST['post-time']);
  $date      = $timestamp ? date('r', $timestamp) : date('r');
  $tags      = nm_lowercase_tags(trim(preg_replace(array('/\s+/','/\s*,\s*/','/,+/'),array(' ',',',','),safe_slash_html(trim($_POST['post-tags']))),','));
  $tags      = implode(',', array_unique(explode(',', $tags))); // remove dupe tags
  $private   = isset($_POST['post-private']) ? 'Y' : '';
  $metad     = safe_slash_html($_POST['post-metad']);
  $image     = safe_slash_html($_POST['post-image']);
  $content   = safe_slash_html($_POST['post-content']);
  if (defined('NMSAVEAUTHOR') && NMSAVEAUTHOR) {
    if (isset($_POST['author'])) {
      $author  = safe_slash_html($_POST['author']);
    } else {
      global $USR;
      $author = $USR ? $USR : '';
    }
  }
  # create xml object
  $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
  $obj = $xml->addChild('title');
  $obj->addCData($title);
  $obj = $xml->addChild('date');
  $obj->addCData($date);
  $obj = $xml->addChild('tags');
  $obj->addCData($tags);
  $obj = $xml->addChild('private');
  $obj->addCData($private);
  $obj = $xml->addChild('metad');
  $obj->addCData($metad);
  $obj = $xml->addChild('image');
  $obj->addCData($image);
  $obj = $xml->addChild('content');
  $obj->addCData($content);
  if (isset($author)) {
    $obj = $xml->addChild('author');
    $obj->addCData($author);
  }
  # write data to file
  if (@XMLsave($xml, $file) && nm_update_cache()) {
    nm_generate_sitemap();
    nm_display_message(i18n_r('news_manager/SUCCESS_SAVE'), false, @$backup);
  } else {
    nm_display_message(i18n_r('news_manager/ERROR_SAVE'), true);
  }
}


/*******************************************************
 * @function nm_delete_post
 * @param $slug - post slug
 * @action deletes the requested post
 */
function nm_delete_post($slug) {
  $file = $slug.'.xml';
  # path traversal?
  if (dirname(realpath(NMPOSTPATH.$file)) != realpath(NMPOSTPATH)) {
    nm_display_message('<b>Error:</b> incorrect path', true); // not translated
  } else {
      # delete post
      if (file_exists(NMPOSTPATH . $file)) {
        if (nm_rename_file(NMPOSTPATH.$file, NMBACKUPPATH.$file) && nm_update_cache()) {
          nm_generate_sitemap();
          nm_display_message(i18n_r('news_manager/SUCCESS_DELETE'), false, $slug);
        } else {
          nm_display_message(i18n_r('news_manager/ERROR_DELETE'), true);
        }
      }
  }
}


/*******************************************************
 * @function nm_restore_post
 * @param $target - string containing target(s)
 * @action restores a backup of the requested post
 */
function nm_restore_post($backup) {
  if (strpos($backup, ':')) {
    # revert to the previous version of a post
    list($current, $backup) = explode(':', $backup);
    $current .= '.xml';
    $backup .= '.xml';
    if (dirname(realpath(NMPOSTPATH.$current)) == realpath(NMPOSTPATH) && dirname(realpath(NMBACKUPPATH.$backup)) == realpath(NMBACKUPPATH)) // no path traversal
        if (file_exists(NMPOSTPATH . $current) && file_exists(NMBACKUPPATH . $backup))
          $status = unlink(NMPOSTPATH . $current) &&
                    nm_rename_file(NMBACKUPPATH.$backup, NMPOSTPATH.$backup) &&
                    nm_update_cache();
  } else {
    # restore the deleted post
    $backup .= '.xml';
    if (dirname(realpath(NMBACKUPPATH.$backup)) == realpath(NMBACKUPPATH)) // no path traversal
        if (file_exists(NMBACKUPPATH . $backup))
          $status = nm_rename_file(NMBACKUPPATH.$backup, NMPOSTPATH.$backup) &&
                    nm_update_cache();
  }
  if (@$status) {
    nm_generate_sitemap();
    nm_display_message(i18n_r('news_manager/SUCCESS_RESTORE'));
  } else {
    nm_display_message(i18n_r('news_manager/ERROR_RESTORE'), true);
  }
}


<?php
define('CACHE_SECONDS', 3600); // for how long images should be cached

$infile = preg_replace('/\.+\//', '', $_GET['p']);
$gallery = @$_GET['g'];

# check authorization
$load['plugin'] = true;
if (file_exists('../../../gsconfig.php')) {
  require_once('../../../gsconfig.php');
}
$GSADMIN = '../../../' . (defined('GSADMIN') ? GSADMIN : 'admin');
if (defined('I18N_GALLERY_PIC_FILTER') && I18N_GALLERY_PIC_FILTER) {
  try {
    global $file, $filters;
    include($GSADMIN.'/inc/common.php');
    $loggedin = cookie_check(); // logged in on backend?
    if (!$loggedin) {
      $file = GSDATAUPLOADPATH . $infile;
      if (!file_exists($file)) error404();
      if ($gallery) {
        $data = return_i18n_gallery($gallery);
        if ($data) foreach ($data['items'] as $item) {
          if ($item['filename'] == $infile) {
            $tags = preg_split('/\s*,\s*/',$item['tags']);
            break;
          }
        }
      }
      if (!isset($tags)) error404();
      @session_start();
      foreach ($filters as $filter)  {
        if ($gallery && $filter['filter'] == 'image-veto') {
          if (call_user_func_array($filter['function'], array($gallery, $infile, $tags))) error404();
        }
      }
      exec_action('pre-image');
    }
  } catch (Exception $e) {
    header("X-Message: ".preg_replace('/\r?\n/','',(string)$e));
  }
  @session_write_close();
  # end check authorization
}
$maxWidth = @$_GET['w'] ? intval($_GET['w']) : null;
$maxHeight = @$_GET['h'] ? intval($_GET['h']) : null;
$displacement = @$_GET['d'] ? intval($_GET['d']) : null;
$crop = @$_GET['c'] && $maxWidth && $maxHeight;
$datadir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR.'plugins')) . '/data/';
$imagedir = $datadir . 'uploads/';
if (!$maxWidth && !$maxHeight) {
  $info = @getimagesize($imagedir.$infile);
  if (!$info) die('File not found or not an image!');
  header('Content-Type: '.$info['mime']);
  header("Cache-Control: max-age=3600, private, must-revalidate");
  header("Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
  readfile($imagedir.$infile);
} else {
  $pos = strrpos($infile,'/');
  if ($pos === false) $pos = -1;
  $outfile = substr($infile, 0, $pos+1) . 'i18npic.' . ($crop ? 'C' : '') . 
             ($maxWidth ? $maxWidth : '0') . 'x' . ($maxHeight ? $maxHeight : '0') . 
             ($displacement ? 'd'.$displacement : '') . '.' . substr($infile, $pos+1);
  $outfile = substr($outfile, 0, strrpos($outfile,'.')) . '.jpg';
  $thumbdir = $datadir . 'thumbs/';
  if (!file_exists($thumbdir.$outfile) || @filemtime($thumbdir.$outfile) < @filemtime($imagedir.$infile)) {
    if (!file_exists($imagedir.$infile)) die('File not found!');
    $info = @getimagesize($imagedir.$infile);
    if (!$info) die('Not an image!');
    $width = $info[0];
    $height = $info[1];
    if (!$crop && $width <= $maxWidth && $height <= $maxHeight) {
      header('Content-Type: '.$info['mime']);
      readfile($imagedir.$infile);
      exit(0);
    }
    if (!function_exists('imagecreatetruecolor')) {
      die('GD not installed!');
    }
    switch ($info[2]) {
      case IMAGETYPE_JPEG:
      case IMAGETYPE_JPEG2000: $src = @imagecreatefromjpeg($imagedir.$infile); break;
      case IMAGETYPE_PNG: $src = @imagecreatefrompng($imagedir.$infile); break;
      case IMAGETYPE_GIF: $src = @imagecreatefromgif($imagedir.$infile); break;
    }
    if (!@$src) die('Can\' read image!');
    if ($crop) {
      $px = $py = 0;
      $sx = $width;
      $sy = $height;
      $d = $displacement !== null ? $displacement/100.0 : 0.5;
      if ($maxWidth*$height > $width*$maxHeight) {
        $sy = (int) ($width*$maxHeight/$maxWidth);
        $py = (int) ($d * ($height - $width*$maxHeight/$maxWidth)); 
      } else {
        $sx = (int) ($height*$maxWidth/$maxHeight);
        $px = (int) ($d * ($width - $height*$maxWidth/$maxHeight));
      }
      $dst = imagecreatetruecolor($maxWidth, $maxHeight); 
      imagecopyresampled($dst, $src, 0, 0, $px, $py, $maxWidth, $maxHeight, $sx, $sy);
    } else {
      if (!$maxHeight || ($maxWidth && $width/$height > $maxWidth/$maxHeight)) {
        $newWidth = (int) $maxWidth;
        $newHeight = (int) (1.0*$newWidth*$height/$width);
      } else {
        $newHeight = (int) $maxHeight;
        $newWidth = (int) (1.0*$newHeight*$width/$height);
      }
      $dst = imagecreatetruecolor($newWidth, $newHeight); 
      imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    }
    $outdir = substr($thumbdir.$outfile, 0, strrpos($thumbdir.$outfile, '/'));
    if (!file_exists($outdir)) @mkdir($outdir, 0777, true);
    imagejpeg($dst, $thumbdir.$outfile, 85);
  }
  header('Content-Type: image/jpeg');
  // Caching headers: private caches only in case of restrictions on the image
  header("Cache-Control: max-age=".CACHE_SECONDS.", private, must-revalidate");
  header("Expires: " . gmdate("D, d M Y H:i:s", time() + CACHE_SECONDS) . " GMT");
  readfile($thumbdir.$outfile);
} 

function error404() {
  header('HTTP/1.1 404 Not Found');
  header('Content-Type: text/plain');
  echo '404 File not found';
  exit(0);
}




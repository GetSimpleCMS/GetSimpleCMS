<?php
  /**
   * Basic File Browser for I18N Gallery
   *
   * Displays and selects file link to insert
   */
   
  function i18n_gallery_exif_text($text, $defEnc=null) {
    if (!$defEnc) $defEnc = 'ISO-8859-15'; 
    if (function_exists('mb_convert_encoding') && function_exists('mb_detect_encoding')) {
      return mb_convert_encoding($text, "UTF-8", mb_detect_encoding($text, 'UTF-8, '.$defEnc));
    } else {
      return $text;
    }
  }
   
  function i18n_gallery_image_info($file, $defEnc=null, $debug=false) {
    $info = array();
    if ($debug) $info['debug'] = '';
    // 1. get XMP meta data (it's in UTF-8 and we shouldn't have problems)
    try {
      $content = file_get_contents($file);
      $xmpdata_start = strpos($content, "<x:xmpmeta");
      if ($xmpdata_start !== false) {
        $xmpdata_end = strpos($content, "</x:xmpmeta>");
        $xmpdata = substr($content, $xmpdata_start, $xmpdata_end-$xmpdata_start+12);
        if ($debug) $info['debug'] .= $xmpdata . "\r\n";
        $xmp = @simplexml_load_string($xmpdata);
        if ($xmp != null) {
          #$xmp->registerXPathNamespace("x", "adobe:ns:meta/");
          $xmp->registerXPathNamespace("rdf", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
          $xmp->registerXPathNamespace("dc", "http://purl.org/dc/elements/1.1/");
          $elems = $xmp->xpath("//dc:title/rdf:Alt/rdf:li");
          if ($elems && count($elems) > 0) {
            $info['title'] = '';
            foreach ($elems as $elem) $info['title'] .= ($info['title'] ? "\r\n" : '').((string) $elem);
          }
          $elems = $xmp->xpath("//dc:description/rdf:Alt/rdf:li");
          if ($elems && count($elems) > 0) {
            $info['description'] = '';
            foreach ($elems as $elem) $info['description'] .= ($info['description'] ? "\r\n" : '').((string) $elem);
            if ($info['description'] == @$info['title']) unset($info['description']);
          }
          $elems = $xmp->xpath("//dc:subject/rdf:Bag/rdf:li");
          if ($elems && count($elems) > 0) {
            $info['tags'] = array();
            foreach ($elems as $elem) $info['tags'][] = (string) $elem;
          }
        }
      }
    } catch (Exception $e) {
      # ignore
    }   
    if (!$debug && count($info) == 4) return $info;
    # 2. get IPTC data, if not in XMP data (assume ISO-8859-1)
    try {
      getimagesize($file,$arrInfo);
      if (function_exists('iptcparse')) {
        $iptc = $arrInfo && isset($arrInfo['APP13']) ? @iptcparse($arrInfo['APP13']) : null;
        if ($debug) $info['debug'] .= print_r($iptc,true) . "\r\n";
        if (!isset($info['title']) && @$iptc['2#005']) { # document title
          $info['title'] = i18n_gallery_exif_text(implode("\r\n", $iptc['2#005']));
        }    
        if (!isset($info['title']) && @$iptc['2#105']) { # title
          $info['title'] = i18n_gallery_exif_text(implode("\r\n", $iptc['2#105']));
        }    
        if (!isset($info['description']) && @$iptc['2#120']) { # description
          $info['description'] = i18n_gallery_exif_text(implode("\r\n", $iptc['2#120']));
          if ($info['description'] == @$info['title']) unset($info['description']);
        }
        if (!isset($info['tags']) && @$iptc['2#025']) { # keywords
          $info['tags'] = array();
          foreach ($iptc['2#025'] as $t) $info['tags'][] = i18n_gallery_exif_text($t); 
        }
        if (!isset($info['author']) && @$iptc['2#080']) { # author
          $info['author'] = i18n_gallery_exif_text(implode("\r\n", $iptc['2#080']));
        }
      }
    } catch (Exception $e) {
      # ignore
    }
    if (!$debug && count($info) == 4) return $info;
    # 3. get EXIF data, if neither in XMP nor in IPTC data (assume ISO-8859-1)
    try {
      if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($file, null, true);
        if ($debug) $info['debug'] .= print_r($exif,true) . "\r\n";
        if (!isset($info['title'])) {
          if (@$exif['IFD0']['ImageDescription']) $info['title'] = i18n_gallery_exif_text($exif['IFD0']['ImageDescription']);
          else if (@$exif['IFD0']['Title']) $info['title'] = i18n_gallery_exif_text($exif['IFD0']['Title']);
        }
        if (!isset($info['description'])) {
          if (@$exif['IFD0']['Comments']) {
            $info['description'] = i18n_gallery_exif_text($exif['IFD0']['Comments']);
            if ($info['description'] == @$info['title']) unset($info['description']);        
          }
          if (@$exif['EXIF']['UserComment']) {
            $info['description'] = i18n_gallery_exif_text($exif['EXIF']['UserComment']);
            if ($info['description'] == @$info['title']) unset($info['description']);
          }
        }
        if (!isset($info['tags']) && @$exif['IFD0']['Keywords']) {
          $info['tags'] = preg_split('/;/', i18n_gallery_exif_text($exif['IFD0']['Keywords']));
        }
        if (!isset($info['author'])) {
          if (@$exif['IFD0']['Author']) $info['author'] = i18n_gallery_exif_text($exif['IFD0']['Author']);
          else if (@$exif['IFD0']['Artist']) $info['author'] = i18n_gallery_exif_text($exif['IFD0']['Artist']);
        }
      }
    } catch (Exception $e) {
      # ignore
    }
    return $info;
  }
   
  include('../../../gsconfig.php');
  $admin = defined('GSADMIN') ? GSADMIN : 'admin';
  include("../../../${admin}/inc/common.php");
  $loggedin = cookie_check();
  if (!$loggedin) die("Not logged in!");
  if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

  i18n_merge('i18n_gallery',substr($LANG,0,2));
  i18n_merge('i18n_gallery','en');
  
  if (isset($_GET['path'])) {
    $subPath = preg_replace('/\.+\//','',$_GET['path']);
    if ($subPath) $subPath .= '/';
    $path = "../../../data/uploads/".$subPath;
  } else {
    $subPath = "";
    $path = "../../../data/uploads/";
  }
  $path = tsl($path);

  // check if host uses Linux (used for displaying permissions
  $isUnixHost = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? false : true);
  $path_parts = pathinfo($_SERVER['PHP_SELF']);
  $dir = str_replace("/plugins/i18n_gallery/browser", "", $path_parts['dirname']);
  $fullPath = htmlentities("http://".$_SERVER['SERVER_NAME'].($dir == '/' ? "" : $dir)."/data/uploads/", ENT_QUOTES);
  $sitepath = htmlentities("http://".$_SERVER['SERVER_NAME'].($dir == '/' ? "" : $dir)."/", ENT_QUOTES);

  $func = preg_replace('/[^\w]/', '', @$_GET['func']);
  $w = (int) @$_GET['w'];
  $h = (int) @$_GET['h'];
  if (!$w && !$h) { $w = 160; $h = 120; }
  $autoclose = @$_GET['autoclose'];
  $debug = @$_GET['debug'];

  global $LANG;
  $LANG_header = preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG);
	$count="0";
	$dircount="0";
	$counter = "0";
	$totalsize = 0;
	$filesArray = array();
	$dirsArray = array();

  clearstatcache();
  $dir_handle = opendir($path) or die("Unable to open $path");
	while ($file = readdir($dir_handle)) {
		if ($file == "." || $file == ".." || $file == ".htaccess" ){
		  // not a upload file
		} elseif (is_dir($path . $file)) {
		  $dirsArray[$dircount]['name'] = $file;
		  $dircount++;
		} else {
			$ext = @strtolower(substr($file, strrpos($file, '.') + 1));
      if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
			  $ss = @stat($path . $file);
        list($width,$height) = getimagesize($path . $file);
        $info = i18n_gallery_image_info($path . $file, null, @$debug);
        $filesArray[] = array('name' => $file, 'date' => @date('M j, Y',$ss['ctime']), 'size' => fSize($ss['size']), 
                              'bytes' => $ss['size'], 'width' => $width, 'height' => $height,
                              'title' => @$info['title'], 'tags' => @$info['tags'],
                              'description' => @$info['description'], 
                              'debug' => @$info['debug']);
			  $totalsize = $totalsize + $ss['size'];
			  $count++;
      }
		}
	}
	$filesSorted = subval_sort($filesArray,'name');
	$dirsSorted = subval_sort($dirsArray,'name');

	$pathParts=explode("/",$subPath);
	$urlPath="";
?>
<!DOCTYPE html>
<html lang="<?php echo $LANG_header; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo i18n_r('FILE_BROWSER'); ?></title>
	<link rel="shortcut icon" href="../../../<?php echo $admin; ?>/favicon.png" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="../../../<?php echo $admin; ?>/template/style.php?v=<?php echo GSVERSION; ?>" media="screen" />
	<style>
		.wrapper, #maincontent, #imageTable { width: 100% }
	</style>
</head>
<body id="imagebrowser" >	
  <div class="wrapper">
  <div id="maincontent">
	  <div class="main" style="border:none;">
		  <h3><?php i18n('UPLOADED_FILES'); ?></h3>
      <div class="h5">/ <a href="?func=<?php echo $func; ?>&amp;w=<?php echo $w; ?>&amp;h=<?php echo $h; ?>&amp;autoclose=<?php echo $autoclose; ?>">uploads</a> / 
<?php 
  foreach ($pathParts as $pathPart){
		if ($pathPart!=''){
			$urlPath .= $pathPart;
?>
        <a href="?path=<?php echo $urlPath; ?>&amp;func=<?php echo $func; ?>&amp;w=<?php echo $w; ?>&amp;h=<?php echo $h; ?>&autoclose=1"><?php echo $pathPart; ?></a> / 
<?php
      $urlPath .= '/';
		}
	}
?>
      </div>
      <table class="highlight" id="imageTable">
        <tbody>
<?php
	if (count($dirsSorted) != 0) {       
		foreach ($dirsSorted as $upload) {
			$p = $subPath . $upload['name']; 
?>
          <tr class="All" > 
		        <td class="" colspan="5">
		          <img src="../../../<?php echo $admin; ?>/template/images/folder.png" width="11" /> 
              <a href="imagebrowser.php?path=<?php echo $p; ?>&amp;func=<?php echo $func; ?>&amp;w=<?php echo $w; ?>&amp;h=<?php echo $h; ?>&autoclose=1" title="<?php echo $upload['name']; ?>"><strong><?php echo $upload['name']; ?></strong></a>
		        </td>
          </tr>
<?php
		}
	}

  $metadata = array();
	if (count($filesSorted) != 0) { 			
		foreach ($filesSorted as $upload) {
      $onclick = 'submitLink('.count($metadata).')';
      $metadata[] = array('url' => $subPath.$upload['name'],
                          'size' => $upload['bytes'],
                          'width' => $upload['width'],
                          'height' => $upload['height'],
                          'title' => $upload['title'],
                          'tags' => $upload['tags'],
                          'description' => $upload['description']
                         );
			if ($isUnixHost && defined('GSDEBUG') && function_exists('posix_getpwuid')) {
				$filePerms = substr(sprintf('%o', fileperms($path.$upload['name'])), -4);
				$fileOwner = posix_getpwuid(fileowner($path.$upload['name']));
			}
?>
          <tr class="All images">
            <td>
              <a href="javascript:void(0)" title="<?php i18n('SELECT_FILE').': '.htmlspecialchars(@$upload['name']); ?>" onclick="<?php echo $onclick; ?>">
                <img src="pic.php?p=<?php echo $subPath.$upload['name']; ?>&amp;w=<?php echo $w; ?>&amp;h=<?php echo $h; ?>"/>
              </a>
            </td>
            <td>
              <a class="primarylink" href="javascript:void(0)" title="<?php i18n('SELECT_FILE').': '.htmlspecialchars(@$upload['name']); ?>" onclick="<?php echo $onclick; ?>">
                <?php echo htmlspecialchars($upload['name']); ?>
              </a>
              <p>
              <?php if (@$upload['title']) echo '<b>'.htmlspecialchars($upload['title']).'</b><br/>'; ?>
              <?php if (@$upload['tags']) echo '<i>'.htmlspecialchars(implode(', ',$upload['tags'])).'</i><br/>'; ?>
              <?php if (@$upload['description']) echo preg_replace('/\r?\n/', '<br/>', htmlspecialchars($upload['description'])); ?>
              </p>
            </td>
            <td style="white-space:nowrap;"><span><?php echo $upload['width']; ?> x <?php echo $upload['height']; ?></span></td>
			      <td style="width:80px;text-align:right;" ><span><?php echo $upload['size']; ?></span></td>
<?php	if (isset($filePerms) && isset($fileOwner['name'])) { ?>
					  <td style="width:70px;text-align:right;"><span><?php echo $fileOwner['name']; ?>/<?php echo $filePerms; ?></span></td>
<?php } ?>
            <td style="width:85px;text-align:right;" ><span><?php echo shtDate($upload['date']); ?></span></td>
			    </tr>
          <?php if ($debug) echo '<tr><td colspan="4"><pre>'.htmlspecialchars(@$upload['debug']).'</pre></td></tr>'; ?>
<?php
		}
	}
?>
        </tbody>
      </table>
	    <p><em><b><?php echo count($filesSorted); ?></b> <?php i18n('TOTAL_FILES'); ?> (<?php echo fSize($totalsize); ?>)</em></p>
      <p><a href="javascript:void(0)" onclick="submitAllLinks()"><?php i18n('i18n_gallery/ADD_ALL_IMAGES'); ?></a></p>
      <?php // foreach ($metadata as &$m) if (!@$m['title']) $m['title'] = basename($m['url']); ?>
      <script type='text/javascript'>
        // <![CDATA[
        var metadata = <?php echo json_encode($metadata); ?>;
        function submitLink(i) {
          var item = metadata[i];
          if(window.opener){
            window.opener.<?php echo $func; ?>(item['url'], item['size'], item['width'], item['height'], item['title'], item['tags'], item['description']);
            <?php if ($autoclose) { ?>window.close();<?php } ?>
          }
        }
        function submitAllLinks() {
          for (var i=0; i < metadata.length; i++) {
            submitLink(i);
          }
        }
        // ]]>
      </script>
    </div>
  </div>
  </div>	
</body>
</html>

<?php
/**
 * Basic Page Browser for I18N Custom Fields
 *
 * Displays and selects file link to insert
 */
  include('../../../admin/inc/common.php');
  $loggedin = cookie_check();
  if (!$loggedin) die;
  include('../../../admin/inc/theme_functions.php');
  i18n_merge('i18n_specialpages') || i18n_merge('i18n_specialpages', 'en');
  $func = preg_replace('/[^\w]/', '', @$_GET['func']);
  
  global $SITEURL;
  $sitepath = (string) $SITEURL;

  $isI18N = @$_GET['i18n'];
  $pages = array();
	$dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
	while ($filename = readdir($dir_handle)) {
    if (strrpos($filename,'.xml') === strlen($filename)-4 && !is_dir(GSDATAPAGESPATH . $filename)) {
			$data = getXML(GSDATAPAGESPATH . $filename);
      if (!$isI18N || strpos($filename,'_') === false) {
        $url = '' . $data->url;
        if (!isset($pages[$url])) {
          $pages[$url] = array('url' => $url, 'variants' => array());
        }
        $pages[$url]['exists'] = true;
        $pages[$url]['parent'] = (string) $data->parent;
			  $pages[$url]['title'] = (string) $data->title;
			  $pages[$url]['menuStatus'] = (string) $data->menuStatus;
			  $pages[$url]['menuOrder'] = (int) $data->menuOrder;
        $parent = (string) $data->parent;
        if ($parent && !isset($pages[$parent])) {
          $pages[$parent] = array('url' => $parent, 'exists' => false, 'title' => '', 'menuOrder' => 99, 'parent' => null);
        }
      }
    }
	}
  // sort pages
  $view = @$_GET['view'];
  if (!$view) $view = 'hierarchical';
  if (count($pages) > 0) foreach ($pages as &$page) {
    if ($view == 'hierarchical') {
      $level = -1;
      $sort = '';
      for ($p = $page; $p; $p = $p['parent'] ? $pages[$p['parent']] : null) {
        $sort = sprintf('%03d',$p['menuOrder']).$p['title'].' '.$sort;
        $level++;
      }
      $page['level'] = $level;
      $page['sort'] = $sort;
    } else {
      $page['sort'] = $page['title'];
    }
  }
  $isHierarchical = $view == 'hierarchical';
  $pages = subval_sort($pages,'sort');
  $link = "pagebrowser.php?func=".$func.'&amp;i18n='.$isI18N;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"  />
	<title><?php echo i18n_r('FILE_BROWSER'); ?></title>
	<link rel="shortcut icon" href="../../../admin/favicon.png" type="image/x-icon" />
	<script type="text/javascript" src="../../../admin/template/js/jquery.min.js?v=1.5.1"></script>
	<link rel="stylesheet" type="text/css" href="../../../admin/template/style.php?v=<?php echo GSVERSION; ?>" media="screen" />
	<style>
		.wrapper, #maincontent, #pages { width: 100% }
	</style>
	<script type='text/javascript'>
	function submitLink(url,parent) {
		if (window.opener){
			window.opener.<?php echo $func; ?>(<?php echo json_encode($sitepath); ?> + (parent ? parent + "/" : "") + url);
		}
		window.close();
	}
	</script>
</head>
<body id="filebrowser" >	
  <div class="wrapper">
    <div id="maincontent">
  	  <div class="main" style="border:none;">
			  <h3 class="floated" style="float:left"><?php i18n('ALL_PAGES'); ?></h3>
			  <div class="edit-nav" >
          <p>
            <?php echo i18n_r('i18n_specialpages/FILTER'); ?>: <input type="text" id="filter" value="" class="text" style="width:80px"/>
            <a href="<?php echo $link; ?>&view=hierarchical" <?php echo $view=='hierarchical' ? 'class="current"' : ''; ?> ><?php echo i18n_r('i18n_specialpages/VIEW_HIERARCHICAL'); ?></a>
            <a href="<?php echo $link; ?>&view=title" <?php echo $view=='title' ? 'class="current"' : ''; ?> ><?php echo i18n_r('i18n_specialpages/VIEW_TITLE'); ?></a>
          </p>
          <div class="clear" ></div>
        </div>
			  <table id="pages" class="highlight">
          <tbody>
          <?php
            foreach ($pages as &$page) {
              if ($page['exists']) {
                if ($page['title'] == '') $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; 
          ?>
            <tr>
              <td <?php echo $isHierarchical ? 'style="padding-left:'.(4+20*$page['level']).'px"' : ''; ?> >
                <a title="<?php i18n('SELECT_FILE'); ?>: <?php htmlspecialchars($page['title']); ?>" href="javascript:void(0)" onclick="submitLink('<?php echo $page['url']; ?>','<?php echo $page['parent']; ?>')"><?php echo cl($page['title']); ?></a>
              </td>
            </tr>
          <?php
              }
            }
          ?>
          </tbody>
			  </table>
      <script type="text/javascript">
        function filterPages() {
          var s = $('#filter').val().toLowerCase();
          if (s == '') {
            $('#pages tbody tr').css('display', 'table-row');
          } else {
            $('#pages tbody tr').each(function(i,tr) {
              var found = $(tr).find('td').text().toLowerCase().indexOf(s) >= 0;
              $(tr).css('display', found ? 'table-row' : 'none');
            });
          }
        }
        $(function() {
          $('#filter').keyup(filterPages);
          filterPages();
          $('#filter').focus();
        });
      </script>
      </div>
    </div>
  </div>	
</body>
</html>

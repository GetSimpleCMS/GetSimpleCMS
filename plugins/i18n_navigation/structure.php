<?php
  function i18n_navigation_structure_load(&$pages, &$languages) {
    $pages = array();
    $languages = array();
    $is_i18n = function_exists('return_i18n_default_language');
	  $dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
    $files_to_ignore = '/^$/';
    @include_once(GSPLUGINPATH.'i18n_base/basic.class.php');
    if (class_exists('I18nBasic') && defined('I18N_PROP_URLS_TO_IGNORE')) {
      $files_to_ignore = '/^('.I18nBasic::getProperty(I18N_PROP_URLS_TO_IGNORE, '').')\.xml$/';
    }
	  while ($filename = readdir($dir_handle)) {
      if (strrpos($filename,'.xml') === strlen($filename)-4 && !is_dir(GSDATAPAGESPATH . $filename) && !preg_match($files_to_ignore, $filename)) {
			  $data = getXML(GSDATAPAGESPATH . $filename);
        $url = (string) $data->url;
        if ($is_i18n && strpos($url,'_') !== false) {
          $lang = substr($url, strpos($url,'_')+1);
          $url = substr($url, 0, strpos($url,'_'));
          if (!in_array($lang, $languages)) $languages[] = $lang;
          if (!isset($pages[$url])) {
            $pages[$url] = array('url' => $url, 'variants' => array(), 'exists' => false, 'title' => '', 'menuOrder' => 99, 'parent' => null);
          }
          $pages[$url]['variants'][$lang] = array('url' => $url.'_'.$lang);
          $pages[$url]['variants'][$lang]['language'] = $lang;
          $pages[$url]['variants'][$lang]['title'] = (string) $data->title;
          $pages[$url]['variants'][$lang]['menu'] = (string) $data->menu;
          $pages[$url]['variants'][$lang]['accesskey'] = (string) $data->accesskey;
          $pages[$url]['variants'][$lang]['taborder'] = (int) $data->taborder;
          $pages[$url]['variants'][$lang]['private'] = (string) $data->private;
        } else {
          if (!isset($pages[$url])) {
            $pages[$url] = array('url' => $url, 'variants' => array());
          }
          $pages[$url]['exists'] = true;
          $pages[$url]['parent'] = (string) $data->parent;
			    $pages[$url]['title'] = (string) $data->title;
          $pages[$url]['menu'] = (string) $data->menu;
			    $pages[$url]['menuStatus'] = (string) $data->menuStatus;
			    $pages[$url]['menuOrder'] = (int) $data->menuOrder;
          $pages[$url]['date'] = $data->pubDate;
          $pages[$url]['accesskey'] = (string) $data->accesskey;
          $pages[$url]['taborder'] = (int) $data->taborder;
          $pages[$url]['private'] = (string) $data->private;
        }
      }
	  }
    closedir($dir_handle);
    sort($languages);
  }

  function i18n_navigation_structure_save(&$pages) {
    $stack = array();
    for ($i=0; isset($_POST['page_'.$i.'_url']); $i++) {
      $url = $_POST['page_'.$i.'_url'];
      if (isset($pages[$url])) {
        $level = (int) $_POST['page_'.$i.'_level'];
        if ($level > count($stack)) $level = count($stack);
        $parent = $level <= 0 ? '' : $stack[$level-1];
        $menuOrder = count($stack) <= $level ? 0 : $pages[$stack[$level]]['menuOrder'] + 1;
        $menu = htmlentities($_POST['page_'.$i.'_menu'], ENT_QUOTES, 'UTF-8');
        $menuStatus = $_POST['page_'.$i.'_menuStatus'];
        $private = $_POST['page_'.$i.'_private'];
        if ($menu == $pages[$url]['title']) $menu = '';
        if ($parent != $pages[$url]['parent'] || $menuOrder != $pages[$url]['menuOrder'] || $menu != $pages[$url]['menu'] ||
            $menuStatus != $pages[$url]['menuStatus'] || $private != $pages[$url]['private']) {
          // update pages array
          $pages[$url]['parent'] = $parent;
          $pages[$url]['menuOrder'] = $menuOrder;
          $pages[$url]['menu'] = $menu;
          $pages[$url]['menuStatus'] = $menuStatus;
          $pages[$url]['private'] = $private;
          // backup page file
          if (!copy(GSDATAPAGESPATH . $url . '.xml', GSBACKUPSPATH . 'i18n_navigation/' . $url . '.xml')) return false;
          // update page file
    			$data = getXML(GSDATAPAGESPATH . $url . '.xml');
	        $xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
	        $xml->addChild('pubDate', (string) $data->pubDate);
          if (@$data->creDate) $xml->addChild('creDate', (string) $data->creDate);
          foreach ($data->children() as $child) {
            $name = $child->getName();
            if ($name != 'parent' && $name != 'menuOrder' && $name != 'menu' && $name != 'menuStatus' && $name != 'private' && 
                $name != 'pubDate' && $name != 'creDate') {
              $xml->addChild($name)->addCData((string) $child);
            }
          }
	        $xml->addChild('parent')->addCData((string) $parent);
	        $xml->addChild('menuOrder')->addCData((string) $menuOrder);
	        $xml->addChild('menu')->addCData((string) $menu);
          $xml->addChild('menuStatus')->addCData((string) $menuStatus);
          $xml->addChild('private')->addCData((string) $private);
       		XMLsave($xml, GSDATAPAGESPATH . $url . '.xml');
        }
        // variants
        if (count($pages[$url]['variants']) > 0) foreach ($pages[$url]['variants'] as $lang => &$variant) {
          if (isset($_POST['page_'.$i.'_menu'.$lang])) {
            $menu = htmlentities($_POST['page_'.$i.'_menu'.$lang], ENT_QUOTES, 'UTF-8');
            if ($menu == $variant['title']) $menu = '';
            if ($menu != $variant['menu'] || $private != $variant['private']) {
              $variant['menu'] = $menu;
              // backup variant file
              if (!copy(GSDATAPAGESPATH . $variant['url'] . '.xml', GSBACKUPSPATH . 'i18n_navigation/' . $variant['url'] . '.xml')) return false;
              // update variant file
        			$data = getXML(GSDATAPAGESPATH . $variant['url'] . '.xml');
              $xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
              $xml->addChild('pubDate', (string) $data->pubDate);
              if (@$data->creDate) $xml->addChild('creDate', (string) $data->creDate);
              foreach ($data->children() as $child) {
                $name = $child->getName();
                if ($name != 'menu' && $name != 'menuStatus' && $name != 'private' && $name != 'pubDate' && $name != 'creDate') {
                  $xml->addChild($name)->addCData((string) $child);
                }
              }
              $xml->addChild('menu')->addCData((string) $menu);
              $xml->addChild('menuStatus')->addCData((string) $menuStatus);
              $xml->addChild('private')->addCData((string) $private);
           		XMLsave($xml, GSDATAPAGESPATH . $variant['url'] . '.xml');
            }
          }
        }
        while (count($stack) > $level) array_pop($stack);
        array_push($stack, $url);
      }
    }
    exec_action('menu-aftersave');
    return true;
  }

  function i18n_navigation_structure_undo() {
    $dir = GSBACKUPSPATH . 'i18n_navigation/';
	  $dir_handle = @opendir($dir);
    if (!$dir_handle) return false;
    while ($filename = readdir($dir_handle)) {
      if (!is_dir($dir . $filename)) {
        if (!copy($dir . $filename, GSDATAPAGESPATH . $filename)) {
          closedir($dir_handle);
          return false;
        }
      }
    }
    closedir($dir_handle);
    exec_action('menu-aftersave');
    return true;
  }

  global $LANG;
  $i18n_url = function_exists('find_i18n_url');
  $def_language = function_exists('return_i18n_default_language') ? return_i18n_default_language() : $LANG;
  if (!isset($_POST['save']) && isset($_GET['undo'])) {
    if (i18n_navigation_structure_undo()) {
      $msg = i18n_r('i18n_navigation/UNDO_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('i18n_navigation/UNDO_FAILURE');
    }
  }
  i18n_navigation_structure_load($pages, $languages);
  if (isset($_POST['save'])) {
    $dir = GSBACKUPSPATH . 'i18n_navigation/';
    // create directory if necessary
    if (!file_exists($dir)) {
      @mkdir(substr($dir,0,strlen($dir)-1), 0777);
      $fp = @fopen($dir . '.htaccess', 'w');
      if ($fp) {
        fputs($fp, 'Deny from all');
        fclose($fp);
      }
    }
    // delete old backup files
	  $dir_handle = @opendir($dir);
    if ($dir_handle) {
      while ($filename = readdir($dir_handle)) {
        if (!is_dir($dir . $filename)) unlink($dir . $filename);
      }
      closedir($dir_handle);
    }
    // save
    if (i18n_navigation_structure_save($pages)) {
      $msg = i18n_r('i18n_navigation/SAVE_SUCCESS').' <a href="load.php?id=i18n_navigation&undo">' . i18n_r('UNDO') . '</a>';
      $success = true;
      i18n_clear_cache();
    } else {
      $msg = i18n_r('i18n_navigation/SAVE_FAILURE');
      if (!i18n_navigation_structure_undo()) i18n_clear_cache();
      i18n_navigation_structure_load($pages, $languages);
    }
  }
  // sort pages
  if (count($pages) > 0) foreach ($pages as &$page) {
    if ($page['parent'] && !isset($pages[$page['parent']])) $page['parent'] = null;
    $level = -1;
    $sort = '';
    for ($p = $page; $p && $level<20; $p = $p['parent'] ? $pages[$p['parent']] : null) {
      $sort = sprintf('%03d',$p['menuOrder']).$p['title'].' '.$sort;
      $level++;
    }
    $page['level'] = $level;
    $page['sort'] = $sort;
  }
  $pages = subval_sort($pages,'sort');
  $i = 0;
?>
		<h3 class="floated" style="float:left"><?php echo i18n_r('i18n_navigation/NAVIGATION'); ?></h3>
    <p class="clear"><?php i18n('i18n_navigation/NAVIGATION_DESCR'); ?></p>
<?php if (count($languages) > 0) { ?>
    <p>
      <?php i18n('i18n_navigation/LANGUAGE_DESCR'); ?>
      <select name="navlang" class="text" id="navlang">
        <?php echo '<option value="" selected="selected">'.$def_language.'</option>'; ?>
        <?php foreach ($languages as $language) echo '<option>'.$language.'</option>'; ?>
      </select>.
    </p>
<?php } ?>
    <form method="post" id="navigationForm" action="load.php?id=i18n_navigation">
			<table id="editnav" class="edittable highlight">
        <tbody>
        <?php
          foreach ($pages as &$page) {
            if ($page['exists']) {
              $isPrivate = @$page['private'] == 'Y';
              $isMenu = !$isPrivate && @$page['menuStatus'] == 'Y';
              $menu = @$page['menu'] ? $page['menu'] : $page['title'];
              $notitle = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; 
              $colorStyle = $isPrivate ? 'color:lightgray;' : ($isMenu ? '' : 'color:gray');
        ?>
          <tr id="tr-<?php echo $page['url']; ?>">
            <td style="<?php echo 'padding-left:'.(4+20*$page['level']).'px;' . $colorStyle; ?>" title="<?php echo $isMenu ? stripslashes($page['title']) : ''; ?>" class="menu">
              <input type="hidden" name="page_<?php echo $i; ?>_menuStatus" value="<?php echo $isMenu ? 'Y' : ''; ?>"/>
              <input type="hidden" name="page_<?php echo $i; ?>_private" value="<?php echo $isPrivate ? 'Y' : ''; ?>"/>
              <input type="hidden" name="page_<?php echo $i; ?>_url" value="<?php echo $page['url']; ?>"/>
              <input type="hidden" name="page_<?php echo $i; ?>_level" value="<?php echo 0+$page['level']; ?>"/>
              <input type="hidden" name="page_<?php echo $i; ?>_title" value="<?php echo stripslashes($page['title']); ?>"/>
              <span class="title" style="color:inherit;font-size:inherit;line-height:inherit;<?php echo $isMenu ? 'display:none;' : ''; ?>"><?php echo stripslashes($page['title']); ?></span>
              <span class="modifyable menu" style="color:inherit;font-size:inherit;line-height:inherit;<?php echo $isMenu ? '' : 'display:none;'; ?>"><?php echo stripslashes($menu); ?></span>
              <sup style="display:none;"><?php echo $def_language; ?></sup>
              <input class="modifyable menu text" style="display:none;width:20em;" name="page_<?php echo $i; ?>_menu" value="<?php echo stripslashes($menu); ?>"/>
<?php if (count($page['variants']) > 0) foreach ($page['variants'] as $lang => $variant) { ?>
              <input type="hidden" name="page_<?php echo $i; ?>_title<?php echo $lang; ?>" value="<?php echo stripslashes($variant['title']); ?>"/>
              <span class="title<?php echo $lang; ?>" style="display:none;color:inherit;font-size:inherit;line-height:inherit;"><?php echo stripslashes($variant['title']); ?></span>
              <span class="modifyable menu<?php echo $lang; ?>" style="display:none;color:inherit;font-size:inherit;line-height:inherit;"><?php echo stripslashes(@$variant['menu'] ? $variant['menu'] : $variant['title']); ?></span>
              <input class="modifyable menu<?php echo $lang; ?> text" style="display:none;width:20em;" name="page_<?php echo $i; ?>_menu<?php echo $lang; ?>" value="<?php echo stripslashes(@$variant['menu'] ? $variant['menu'] : $variant['title']); ?>"/>
<?php } ?>
            </td>
            <td class="secondarylink"><a href="#" class="moveLeft" title="<?php i18n('i18n_navigation/OUTDENT'); ?>">&lt;</a></td>
            <td class="secondarylink"><a href="#" class="moveRight" title="<?php i18n('i18n_navigation/INDENT'); ?>">&gt;</a></td>
            <td class="secondarylink"><a href="#" class="toggleMenu" title="<?php i18n('i18n_navigation/TOGGLE_MENU'); ?>">M</a></td>
            <td class="secondarylink"><a href="#" class="togglePrivate" title="<?php i18n('i18n_navigation/TOGGLE_PRIVATE'); ?>">P</a></td>
	          <td class="secondarylink">
	            <a title="<?php echo i18n_r('VIEWPAGE_TITLE').': '. stripslashes($page['title']); ?>" target="_blank" href="<?php echo $i18n_url ? find_i18n_url($page['url'],$page['parent'],$def_language) : find_url($page['url'],$page['parent']); ?>">#</a>
	          </td>
           </tr>
        <?php
              $i++;
            }
          }
        ?>
        </tbody>
			</table>
      <input type="submit" name="save" value="<?php i18n('i18n_navigation/SAVE_NAVIGATION'); ?>" class="submit"/>
    </form>
    <script type="text/javascript" src="../plugins/i18n_navigation/js/jquery-ui.sort.min.js"></script>
    <script type="text/javascript">
      var lang = '';
      function getLevel($tr) {
        var level = parseInt($tr.find('[name$=level]').val());
        return level;
      }
      function setLevel($tr, level) {
        $tr.find('[name$=level]').val(level);
        $tr.find('td:first').css('padding-left', (4+20*level)+'px');
      }
      function moveLeft(e) {
        var $tr = $(e.target).closest('tr');
        var level = getLevel($tr);
        if ($tr.next().length > 0) {
          var nextlevel = getLevel($tr.next());
          if (level > 0 && nextlevel <= level) setLevel($tr, level-1); 
        } else {
          if (level > 0) setLevel($tr, level-1);
        }
        e.preventDefault();
      }
      function moveRight(e) {
        var $tr = $(e.target).closest('tr'); 
        var level = getLevel($tr);
        var prevlevel = getLevel($tr.prev());
        if (prevlevel >= level) setLevel($tr, level+1);
        e.preventDefault();
      }
      function drop(e,ui) {
        var $tr = $(ui.item).closest('tr'); 
        var level = getLevel($tr);
        var prevlevel = getLevel($tr.prev());
        var nextlevel = getLevel($tr.next());
        if (prevlevel == null) {
          setLevel($tr, 0);
        } else if (prevlevel+1 < level) {
          setLevel($tr, prevlevel+1);
        } else if (nextlevel && level < nextlevel-1) {
          setLevel($tr, nextlevel-1);
        }
        renumberRows();
      }
      function renumberRows() {
        var oldlevel = -1;
        $('#editnav tbody tr').each(function(i,tr) {
          $tr = $(tr);
          $tr.find('input, select, textarea').each(function(k,elem) {
            var name = $(elem).attr('name').replace(/_\d+_/, '_'+(i)+'_');
            $(elem).attr('name', name);
          });
          var level = getLevel($tr);
          if (level > oldlevel+1) setLevel($tr, oldlevel+1); else setLevel($tr, level);
          oldlevel = level;
        });
      }
      function startEdit(e) {
        $('#editnav tbody input.menu'+lang+':visible').each(function(i,input) {
          $(input).css('display','none');
          if (input.value == '') input.value = $(input).closest('td').attr('title');
          $(input).closest('td').find('span.menu'+lang).text($(input).val()).css('display','inline');
        });
        if ($(e.target).closest('td').find('input[name$=menuStatus]').val() != 'Y') return;
        if (!$(e.target).closest('span').hasClass('menu'+lang)) return;
        $(e.target).closest('span').css('display','none');
        $(e.target).closest('td').find('input.menu'+lang).css('display','inline').focus();
      }
      function finishEdit(e) {
        $(e.target).css('display','none');
        if (e.target.value == '') e.target.value = $(e.target).closest('td').find('input[name$=title'+lang+']').val();
        $(e.target).closest('td').find('span.menu'+lang).text($(e.target).val()).css('display','inline');
      }
      function changeLanguage(e) {
        $('#editnav td .title, #editnav td .menu, #editnav td sup').hide();
        if (lang != '') $('#editnav td .title'+lang+', #editnav td .menu'+lang).hide();
        lang = $('#navlang').val();
        $('#editnav tbody tr').each(function(i,tr) {
          var isMenu = $(tr).find('input[name$=menuStatus]').val() == 'Y';
          var l = $(tr).find('input[name$=title'+lang+']').size() > 0 ? lang : '';
          if (l != lang) $(tr).find('sup').show();
          if (!isMenu) {
            $(tr).find('span.title'+l).show();
            $(tr).find('td.menu').attr('title','');
          } else {
            $(tr).find('span.menu'+l).show();
            $(tr).find('td.menu').attr('title',$(tr).find('input[name$=title'+l+']').val());
          }
        });        
      }
      function showMenu($tr,show) {
        var l = $tr.find('input[name$=title'+lang+']').size() > 0 ? lang : '';
        if (show) {
          $tr.find('span.title'+l).hide();
          $tr.find('span.menu'+l).show();
          $tr.find('td.menu').css('color','').attr('title', $tr.find('input[name$=title'+l+']').val());
        } else {
          $tr.find('span.title'+l).show();
          $tr.find('span.menu'+l).hide();
          $tr.find('td.menu').attr('title', '');
        }
      }
      function toggleMenu(e) {
        var $tr = $(e.target).closest('tr');
        var $field = $tr.find('input[name$=menuStatus]');
        if ($field.val() == 'Y') {
          $field.val('');
          $tr.find('td.menu').css('color','gray');
          showMenu($tr,false);
        } else {
          $field.val('Y');
          $tr.find('input[name$=private]').val('');
          showMenu($tr,true);
        }
        e.preventDefault();
      }
      function togglePrivate(e) {
        var $tr = $(e.target).closest('tr');
        $field = $tr.find('input[name$=private]');
        if ($field.val() == 'Y') {
          $field.val('');
          $tr.find('td.menu').css('color','gray');
        } else {
          $field.val('Y');
          $tr.find('input[name$=menuStatus]').val('');
          $tr.find('td.menu').css('color','lightgray');
          showMenu($tr,false);
        }
        e.preventDefault();
      }
      $(function() {
        $('#navlang').val('');
        $('#navigationForm').get(0).reset();
        $('#editnav .moveLeft').click(moveLeft);
        $('#editnav .moveRight').click(moveRight);
        $('#editnav .toggleMenu').click(toggleMenu);
        $('#editnav .togglePrivate').click(togglePrivate);
        $('#editnav tbody').sortable({
          items:"tr", handle:'td',
          update:drop
        });
        renumberRows();
        $('#editnav span.modifyable').click(startEdit);
        $('#editnav input.modifyable').blur(finishEdit);
        $('#navlang').change(changeLanguage);
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
	      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>


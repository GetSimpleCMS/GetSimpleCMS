<?php
  global $USR;
  require_once(GSPLUGINPATH.'i18n_base/basic.class.php');
  $success = false;
  $is31 = function_exists('generate_sitemap');
  if (isset($_POST['save'])) {
    if (preg_match('/[a-z][a-z]/i', @$_POST['default-language'])) {
      I18nBasic::setProperty(I18N_PROP_DEFAULT_LANGUAGE, $_POST['default-language']);
      $msg = i18n_r('i18n_base/SAVE_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('i18n_base/SAVE_FAILURE');
    }
  } 
  $open = '';
  if (isset($_REQUEST['open'])) {
    $open = trim($_REQUEST['open']);
    file_put_contents(GSDATAOTHERPATH . 'i18n_pages_' . $USR . '_prefs', $open);
  } else {
    $lines = @file(GSDATAOTHERPATH . 'i18n_pages_' . $USR . '_prefs');
    if ($lines) $open = $lines[0];
  }
  $deflang = return_i18n_default_language();
  $pages = array();
  $languages = array();
  $tags = array();
	$dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
  $files_to_ignore = '/^('.I18nBasic::getProperty(I18N_PROP_URLS_TO_IGNORE, '').')\.xml$/';
	while ($filename = readdir($dir_handle)) {
    if (strrpos($filename,'.xml') === strlen($filename)-4 && !is_dir(GSDATAPAGESPATH . $filename) && !preg_match($files_to_ignore, $filename)) {
			$data = getXML(GSDATAPAGESPATH . $filename);
      if (strpos($filename,'_') !== false) {
        $pos = strpos($data->url,'_');
        $url = substr($data->url,0,$pos);
        $lang = substr($data->url,$pos+1);
        if (!in_array($lang,$languages)) $languages[] = $lang;
        if (!isset($pages[$url])) {
          $pages[$url] = array('url' => $url, 'variants' => array(), 'exists' => false, 'title' => '', 'menuOrder' => 99, 'parent' => null);
        }
        $pages[$url]['variants'][$lang] = array();
        $pages[$url]['variants'][$lang]['url'] = (string) $data->url;
        $pages[$url]['variants'][$lang]['parent'] = (string) $data->parent;
        $pages[$url]['variants'][$lang]['title'] = (string) $data->title;
        $pages[$url]['variants'][$lang]['date'] = $data->pubDate;
      } else {
        $url = '' . $data->url;
        if (!isset($pages[$url])) {
          $pages[$url] = array('url' => $url, 'variants' => array());
        }
        $pages[$url]['exists'] = true;
        $pages[$url]['parent'] = (string) $data->parent;
			  $pages[$url]['title'] = (string) $data->title;
        $pages[$url]['metak'] = (string) $data->meta;
        $pages[$url]['metad'] = (string) $data->metad;
			  $pages[$url]['menuStatus'] = (string) $data->menuStatus;
			  $pages[$url]['menuOrder'] = (int) $data->menuOrder;
        $pages[$url]['private'] = (string) $data->private;
        $pages[$url]['date'] = $data->pubDate;
        foreach (preg_split('/\s*,\s*/', trim(@$pages[$url]['metak'])) as $t) $tags[':'.$t] = true;
      }
      $parent = (string) $data->parent;
      if ($parent && !isset($pages[$parent])) {
        $pages[$parent] = array('url' => $parent, 'variants' => array(), 'exists' => false, 'title' => '', 'menuOrder' => 99, 'parent' => null);
      }
    }
	}
  $tags = array_keys($tags);
  sort($tags);
  sort($languages);
  // sort pages
  $view = @$_REQUEST['view'];
  $sortfield = @$_REQUEST['sort'];
  if (!$view) {
    $view = I18nBasic::getProperty(I18N_PROP_PAGES_VIEW, 'hierarchical');
    $sortfield = I18nBasic::getProperty(I18N_PROP_PAGES_SORT, 'sort');
  } else {
    I18nBasic::setProperties(array(I18N_PROP_PAGES_VIEW => $view, I18N_PROP_PAGES_SORT => $sortfield));
  }
  if (count($pages) > 0) {
    if ($view == 'hierarchical') {
      $openarr = explode(' ',trim($open));
      foreach ($openarr as $url) if (isset($pages[$url])) $pages[$url]['open'] = true;
    }
    foreach ($pages as &$page) {
      if ($view == 'hierarchical') {
        $level = -1;
        $sort = '';
        for ($p = $page; $p; $p = $p['parent'] ? $pages[$p['parent']] : null) {
        	$sort = sprintf('%03d',$p['menuOrder']).$p['title'].' '.$sort;
          if ($sortfield) {
          	$sort = @$p[$sortfield].' '.$sort;
        	}
        	if ($p['parent']) $pages[$p['parent']]['hasChildren'] = true;
          if ($level >= 0 && !@$p['open']) { $page['invisible'] = true; unset($page['open']); }
          $level++;
        }
        $page['level'] = $level;
        $page['sort'] = $sort;
      } else {
        $page['sort'] = $page['title'];
        if ($sortfield) {
        	$page['sort'] = @$page[$sortfield].' '.$page['sort'];
       	}
      }
    }
  }
  $isHierarchical = $view == 'hierarchical';
  $pages = subval_sort($pages,'sort');
  $counter = count($pages);
  // display overview
  $link = "load.php?id=i18n_base";
  $viewlink = $link . ($isHierarchical ? '&view=flat&sort=title' : '&view=hierarchical&sort='.$sortfield);
  $titlelink = $link . ($sortfield == 'title' ? '&view=hierarchical' : '&view=hierarchical&sort=title');
  $singleLanguage = defined('I18N_SINGLE_LANGUAGE') && I18N_SINGLE_LANGUAGE && count($languages) <= 0;
?>
			<h3 class="floated" style="float:left"><?php echo i18n_r('PAGE_MANAGEMENT'); ?></h3>
			<div class="edit-nav" >
        <p>
          <a href="<?php echo $viewlink; ?>" <?php echo $isHierarchical ? 'class="current"' : ''; ?> ><?php echo i18n_r('i18n_base/VIEW_HIERARCHICAL'); ?></a>
          <a href="<?php echo $titlelink; ?>" <?php echo !$isHierarchical || $sortfield == 'title' ? 'class="current"' : ''; ?> ><?php echo i18n_r('i18n_base/VIEW_TITLE'); ?></a>
          <?php if ($is31) { ?>
          <a href="#" id="show-characters" accesskey="<?php echo find_accesskey(i18n_r('TOGGLE_STATUS'));?>" ><?php i18n('TOGGLE_STATUS'); ?></a>          
          <?php } else { ?>
          <?php echo i18n_r('TOGGLE_STATUS'); ?> &nbsp;<input type="checkbox" id="show-characters" value="" />
          <?php } ?>
          &nbsp;&nbsp;
          <?php echo i18n_r('i18n_base/FILTER'); ?>: 
          <input type="text" id="filter" value="" class="_text" style="width:80px" title="<?php echo htmlspecialchars(i18n_r('i18n_base/FILTER_TITLE'));?>"/>
        </p>
        <div class="clear" ></div>
      </div>
      <?php if (!$singleLanguage) { ?>
        <p><?php echo i18n_r('i18n_base/NEW_LANGUAGE_DESCR'); ?></p>
        <form action="<?php echo $link; ?>" method="post" style="margin-bottom:10px">
          <span><?php i18n('i18n_base/DEFAULT_LANGUAGE_DESCR'); ?></span> 
          <input type="text" name="default-language" value="<?php echo $deflang; ?>" maxlength="2" style="width:2em;"/> &nbsp; 
          <input type="submit" name="save" value="<?php i18n('i18n_base/SAVE_DEFAULT_LANGUAGE'); ?>"/> 
        </form>
      <?php } ?>

      <a id="closeall" class="cancel" href="#"><?php i18n('i18n_base/CLOSE_ALL'); ?></a>
      <a id="openall" class="cancel" href="#"><?php i18n('i18n_base/OPEN_ALL'); ?></a>
      <a id="saveopen" class="cancel" href="?id=i18n_base&amp;open=<?php echo urlencode($open); ?>"><?php i18n('i18n_base/SAVE_OPEN'); ?></a>

			<table id="editpages" class="edittable highlight">
        <?php if (count($languages) > 0) { ?>
        <thead>
          <tr class="header"><th colspan="3" style='font-weight:800'><?php echo $deflang; ?></th><?php foreach ($languages as $lang) echo "<th colspan='3' style='font-weight:800'>$lang</th>"; ?></tr>
        </thead>
        <?php } ?>
        <tbody>
        <?php
          foreach ($pages as &$page) {
            $trclass = (!@$page['parent'] ? 'top ' : '') . (@$page['hasChildren'] ? 'parent ' : '') . (@$page['invisible'] ? 'invisible' : (@$page['open'] ? 'open' : ''));
        ?>
          <tr id="tr-<?php echo $page['url']; ?>" class="<?php echo $trclass; ?>" > 
        <?php
            if ($page['exists']) {
              if ($page['title'] == '') $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; 
              $status = '';
	            if ($page['url'] == 'index' ) $status .= ' <sup>['.i18n_r('HOMEPAGE_SUBTITLE').']</sup>'; 
	            if ($page['menuStatus'] != '' ) $status .= ' <sup>['.i18n_r('MENUITEM_SUBTITLE').']</sup>';
	            if ($page['private'] != '' ) $status .= ' <sup>['.i18n_r('PRIVATE_SUBTITLE').']</sup>'; 
        ?>
            <td <?php echo $isHierarchical ? 'style="padding-left:'.(4+20*$page['level']).'px"' : ''; ?> >
              <input type="hidden" name="url" value="<?php echo $page['url']; ?>"/>
              <input type="hidden" name="tags" value="<?php echo stripslashes($page['metak']); ?>"/>
              <input type="hidden" name="title" value="<?php echo stripslashes($page['title']); ?>"/>
              <?php if (@$page['hasChildren']) echo '<a href="#" style="text-decoration:none" class="dirtoggle">'.(@$page['open'] && !@$page['invisible'] ? '&#8863;' : '&#8862;').'</a> '; ?>
              <a title="<?php echo i18n_r('EDITPAGE_TITLE').': '.stripslashes($page['title']).' - '.shtDate($page['date']); ?>" href="edit.php?id=<?php echo $page['url']; ?>" class="title"><?php echo stripslashes($page['title']); ?></a>
              <span class="showstatus toggle"><?php echo $status; ?></span>
            </td>
	          <td class="secondarylink">
	            <a title="<?php echo i18n_r('VIEWPAGE_TITLE').': '. stripslashes($page['title']); ?>" target="_blank" href="<?php echo find_i18n_url($page['url'],$page['parent'],$deflang); ?>">#</a>
	          </td>
	          <td class="delete" >
              <a class="i18n-delconfirm" href="deletefile.php?id=<?php echo $page['url']; ?>&nonce=<?php echo get_nonce("delete", "deletefile.php"); ?>" title="<?php echo i18n_r('DELETEPAGE_TITLE').': '.stripslashes($page['title']); ?>"><?php echo $is31 ? '&times;' : 'X' ?></a>
            </td>
        <?php
            } else {
        ?>
            <td></td>
            <td></td>
            <td class="secondarylink">            
              <a href="edit.php?newid=<?php echo $page['url']; ?>" title="<?php echo i18n_r('CREATE_NEW_PAGE').': '.$page['url']; ?>">+</a>
            </td>
        <?php
            }
            if (!$singleLanguage) foreach ($languages as $lang) {
              $params = 'newid='.$page['url'].'_'.$lang;
              $title = i18n_r('CREATE_NEW_PAGE').': ';
              if ($page['exists']) {
                $params .= '&title='.urlencode($page['title'].' ('.$lang.')').
                           '&metak='.urlencode($page['metak']).
                           '&metad='.urlencode($page['metad']);
                $title .= cl($page['title']).' ('.$lang.')';
              } else {
                $title .= $page['url'].'_'.$lang;
              }
              if (isset($page['variants'][$lang])) {
                $variant =& $page['variants'][$lang];
                if ($variant['title'] == '') $variant['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $variant['url'] .'</em>'; 
        ?> 
            <td>
              <input type="hidden" name="title" value="<?php echo stripslashes($variant['title']); ?>"/>
              <a title="<?php echo i18n_r('EDITPAGE_TITLE').': '.stripslashes($variant['title']).' - '.shtDate($variant['date']); ?>" href="edit.php?id=<?php echo $variant['url']; ?>" class="title"><?php echo stripslashes($variant['title']); ?></a>
            </td>
	          <td class="secondarylink">
	            <a title="<?php echo i18n_r('VIEWPAGE_TITLE').': '.stripslashes($variant['title']); ?>" target="_blank" href="<?php echo find_i18n_url($page['url'],$page['parent'],$lang); ?>">#</a>
	          </td>
	          <td class="delete" >
              <a class="i18n-delconfirm" href="deletefile.php?id=<?php echo $variant['url']; ?>&nonce=<?php echo get_nonce("delete", "deletefile.php"); ?>" title="<?php echo i18n_r('DELETEPAGE_TITLE').': '.stripslashes($variant['title']); ?>"><?php echo $is31 ? '&times;' : 'X' ?></a>
              <a href="edit.php?<?php echo $params; ?>" title="<?php echo $title; ?>" style="display:none">+</a>
            </td>
        <?php
              } else {
        ?>
            <td></td>
            <td></td>
            <td class="secondarylink">            
              <a href="edit.php?<?php echo $params; ?>" title="<?php echo $title; ?>">+</a>
            </td>
        <?php
              }
            }
        ?>
           </tr>
        <?php
          }
        ?>
        </tbody>
			</table>
			<div id="page_counter" class="qc_pager"></div> 	
			<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php echo i18n_r('TOTAL_PAGES'); ?></em></p>
      <script type="text/javascript">
        // <![CDATA[
        var tags = <?php echo json_encode($tags); ?>;
        function filterPages() {
          var s = $.trim($('#filter').val()).toLowerCase();
          if (s == '' || s == ':') {
            $('#editpages tbody tr').removeClass('nomatch').removeClass('match');
          } else if (s.substring(0,1) == ':') {
            s = s.substring(1);
            $('#editpages tbody tr').each(function(i,tr) {
              var found = false;
              $(tr).find('input[name=tags]').each(function(k,input) {
                var tags = $(input).val().toLowerCase().split(/\s*,\s*/g);
                for (var i=0; i<tags.length; i++) if (tags[i].substring(0,s.length) == s) found = true;
              });
              if (found) $(tr).removeClass('nomatch').addClass('match'); else $(tr).addClass('nomatch').removeClass('match');
            });
          } else {
            $('#editpages tbody tr').each(function(i,tr) {
              var found = false;
              $(tr).find('input[name=title]').each(function(k,input) {
                if ($(input).val().toLowerCase().indexOf(s) >= 0) found = true;
              });
              if (found) $(tr).removeClass('nomatch').addClass('match'); else $(tr).addClass('nomatch').removeClass('match');
            });
          }
        }
        function toggleDir(e) {
          e.preventDefault();
          var $dirtr = $(e.target).closest('tr');
          var finished = false;
          var padding = parseInt($dirtr.next().find('td:first').css('padding-left'));
          if ($dirtr.next().hasClass('invisible')) {
            // currently closed
            $dirtr.addClass('open').find('a.dirtoggle').html('&#8863');
            $dirtr.nextAll().each(function(i,tr) {
              if (finished) return;
              var p = parseInt($(tr).find('td:first').css('padding-left'));
              if (p == padding) $(tr).removeClass('invisible'); else if (p == NaN || p < padding) finished = true;
            });
          } else {
            // currently open
            $dirtr.removeClass('open').find('a.dirtoggle').html('&#8862');
            $dirtr.nextAll().each(function(i,tr) {
              if (finished) return;
              var p = parseInt($(tr).find('td:first').css('padding-left'));
              if (p >= padding) {
                $(tr).addClass('invisible').removeClass('open').find('a.dirtoggle').html('&#8862');
              } else if (p == NaN || p < padding) finished = true;
            });
          }
          updateSaveOpenLink();
        }
        function updateSaveOpenLink() {
          var open = '';
          $('#editpages tr.open input[name=url]').each(function(i,input) { open += $(input).val() + ' '; });
          $('#saveopen').attr('href','?id=i18n_base&open='+escape(open));
        }
        function closeAll(e) {
          e.preventDefault();
          $('#editpages tr.open').removeClass('open').find('a.dirtoggle').each(function(i,a) { $(a).html('&#8862') });
          $('#editpages tr:not(.top):not(.header)').addClass('invisible');
          updateSaveOpenLink();
        }
        function openAll(e) {
          e.preventDefault();
          $('#editpages tr.parent').addClass('open').find('a.dirtoggle').each(function(i,a) { $(a).html('&#8863') });
          $('#editpages tr.invisible').removeClass('invisible');
          updateSaveOpenLink();
        }
        $(function() {
          $('#filter').keyup(filterPages).autocomplete(tags, {
            minChars: 1,
            max: 50,
            scroll: true,
            multiple: false
          });
          $('.dirtoggle').click(toggleDir);
          $('#closeall').click(closeAll);
          $('#openall').click(openAll);
	        $(".i18n-delconfirm").live("click", function(e) {
		        var message = $(this).attr("title");
		        var dlink = $(this).attr("href");
	          var answer = confirm(message);
            if (answer){
	            $.ajax({
                 type: "GET",
                 url: dlink,
                 success: function(response) {
                    if ($(e.target).closest('tr').find('a').length > 4) {
                      var $td=$(e.target).closest('td');
                      $(e.target).remove();
                      $td.prev().prev().empty();
                      $td.prev().empty();
                      $td.removeClass('delete').addClass('secondarylink');
                      $td.find('a').attr('style',null);
                    } else {
                      $(e.target).closest('tr').remove();
                      $("#page_counter").html("");
                      if($("#pg_counter").length) {
                    	  counter=$("#pg_counter").html();
                        $("#pg_counter").html(counter-1);
                      }
                      $('table.paginate tr').quickpaginate( { perpage: 15, showcounter: true, pager : $("#page_counter") } );
                    }                  
                    //added by dniesel
                    if($(response).find('div.error').html()) {
                      $('div.bodycontent').before('<div class="error">'+ $(response).find('div.error').html() + '</div>'); 
                    }
                    if($(response).find('div.updated').html()) {
                      $('div.bodycontent').before('<div class="updated">'+ $(response).find('div.updated').html() + '</div>'); 
                    }
                 }
              });
            }
            return false;	            
	        });
          filterPages();
          $('#filter').focus();
<?php if (isset($msg)) { ?>
          $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
<?php } ?>
        });
        // ]]>
      </script>


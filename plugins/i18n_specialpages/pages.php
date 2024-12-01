<?php
  $special = @$_GET['special'];
  $def = $special ? i18n_specialpages_settings($special) : null;
  $is31 = function_exists('generate_sitemap');
  if (!$special || !$def) {
    $settings = i18n_specialpages_settings();
    $settings = subval_sort($settings, 'title');
    if (!$settings) $settings = array();
    $link = "load.php?id=i18n_specialpages&amp;pages&amp;special=";
?>
    <h3><?php i18n('i18n_specialpages/PAGES_TITLE'); ?></h3>
    <p><?php i18n('i18n_specialpages/PAGES_DESCR'); ?></p>
    <table id="editspecial" class="edittable highlight">
      <thead>
        <tr>
          <th><?php i18n('i18n_specialpages/TITLE'); ?></th>
          <th><?php i18n('i18n_specialpages/NAME'); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($settings as $def) { ?>
        <tr>
          <td><a href="<?php echo $link.urlencode($def['name']); ?>"><?php echo htmlspecialchars($def['title']); ?></a></td>
          <td><?php echo htmlspecialchars($def['name']); ?></td>
          <td class="secondarylink">
            <a href="edit.php?special=<?php echo $def['name']; ?>" title="<?php echo i18n_r('CREATE_NEW_PAGE').': '.$def['title']; ?>">+</a>
          </td>
        </tr>
        <?php } ?>
      </body>
    </table> 
<?php
    return;
  }

  global $USR;
  $isi18n = function_exists('i18n_init');
  $deflang = $isi18n ? return_i18n_default_language() : null;
  $pages = array();
  $languages = array();
  $tags = array();
	$dir_handle = @opendir(GSDATAPAGESPATH) or die("Unable to open pages directory");
	while ($filename = readdir($dir_handle)) {
    if (substr($filename,-4) == '.xml' && !is_dir(GSDATAPAGESPATH . $filename)) {
			$data = getXML(GSDATAPAGESPATH . $filename);
      if ($special == (string) @$data->special) {
        if ($isi18n && strpos($filename,'_') !== false) {
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
          $pages[$url]['variants'][$lang]['date'] = (string) $data->pubDate;
          $pages[$url]['variants'][$lang]['creDate'] = (string) @$data->creDate;
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
          $pages[$url]['date'] = (string) $data->pubDate;
          $pages[$url]['creDate'] = (string) @$data->creDate;
          foreach (preg_split('/\s*,\s*/', trim(@$pages[$url]['metak'])) as $t) $tags[':'.$t] = true;
        }
      } else {
        if ($isi18n && strpos($filename,'_') !== false) {
          $pos = strpos($data->url,'_');
          $lang = substr($data->url,$pos+1);
          if (!in_array($lang,$languages)) $languages[] = $lang;
        }        
      }
    }
	}
  $tags = array_keys($tags);
  sort($tags);
  sort($languages);
  // sort pages
  $view = @$_GET['view'];
  if (!$view) $view = @$def['pages_view'];
  if (!$view) $view = 'title';
  if (count($pages) > 0) {
    foreach ($pages as &$page) {
      if ($view == 'creDate') {
        $page['sort'] = -@strtotime($page['creDate']);
      } else if ($view == 'pubDate') {
        $page['sort'] = -@strtotime($page['date']);
      } else {
        $page['sort'] = $page['title'];
      }
    }
  }
  $pages = subval_sort($pages,'sort');
  if (!$pages) $pages = array();
  $counter = count($pages);
  // display overview
  $link = "load.php?id=i18n_specialpages&amp;pages&amp;special=".$special;
?>
			<h3 class="floated" style="float:left"><?php i18n('i18n_specialpages/PAGES_FOR'); ?> <?php echo htmlspecialchars($def['title']); ?></h3>
			<div class="edit-nav" >
        <p>
          <a href="<?php echo $link; ?>&view=creDate" <?php echo $view=='creDate' ? 'class="current"' : ''; ?> ><?php i18n('i18n_specialpages/VIEW_CREDATE'); ?></a>
          <a href="<?php echo $link; ?>&view=pubDate" <?php echo $view=='pubDate' ? 'class="current"' : ''; ?> ><?php i18n('i18n_specialpages/VIEW_PUBDATE'); ?></a>
          <a href="<?php echo $link; ?>&view=title" <?php echo $view=='title' ? 'class="current"' : ''; ?> ><?php i18n('i18n_specialpages/VIEW_TITLE'); ?></a>
        </p>
        <p style="margin-top: 5px;">
          <?php if ($is31) { ?>
          <a href="#" id="show-characters" accesskey="<?php echo find_accesskey(i18n_r('TOGGLE_STATUS'));?>" ><?php i18n('TOGGLE_STATUS'); ?></a>          
          <?php } else { ?>
          <?php i18n('TOGGLE_STATUS'); ?> &nbsp;<input type="checkbox" id="show-characters" value="" />
          <?php } ?>
          &nbsp;&nbsp;
          <?php i18n('i18n_specialpages/FILTER'); ?>: 
          <input type="text" id="filter" value="" class="_text" style="width:80px" title="<?php i18n('i18n_specialpages/FILTER_TITLE'); ?>"/>
        </p>
        <div class="clear" ></div>
      </div>

			<table id="editpages" class="edittable highlight">
        <?php if (count($languages) > 0) { ?>
        <thead>
          <tr class="header"><th colspan="3" style='font-weight:800'><?php echo $deflang; ?></th><?php foreach ($languages as $lang) echo "<th colspan='3' style='font-weight:800'>$lang</th>"; ?></tr>
        </thead>
        <?php } ?>
        <tbody>
        <?php
          foreach ($pages as &$page) {
        ?>
          <tr id="tr-<?php echo $page['url']; ?>" > 
        <?php
            if ($page['exists']) {
              if ($page['title'] == '') $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; 
              $status = '';
	            if ($page['url'] == 'index' ) $status .= ' <sup>['.i18n_r('HOMEPAGE_SUBTITLE').']</sup>'; 
	            if ($page['menuStatus'] != '' ) $status .= ' <sup>['.i18n_r('MENUITEM_SUBTITLE').']</sup>';
	            if ($page['private'] != '' ) $status .= ' <sup>['.i18n_r('PRIVATE_SUBTITLE').']</sup>'; 
        ?>
            <td>
              <input type="hidden" name="url" value="<?php echo $page['url']; ?>"/>
              <input type="hidden" name="tags" value="<?php echo stripslashes($page['metak']); ?>"/>
              <input type="hidden" name="title" value="<?php echo stripslashes($page['title']); ?>"/>
              <a title="<?php i18n('EDITPAGE_TITLE').': '.stripslashes($page['title']).' - '.shtDate($page['date']); ?>" href="edit.php?id=<?php echo $page['url']; ?>" class="title"><?php echo stripslashes($page['title']); ?></a>
              <span class="showstatus toggle"><?php echo $status; ?></span>
            </td>
	          <td class="secondarylink">
	            <a title="<?php i18n('VIEWPAGE_TITLE').': '. stripslashes($page['title']); ?>" target="_blank" href="<?php echo $isi18n ? find_i18n_url($page['url'],$page['parent'],$deflang) : find_url($page['url'],$page['parent']); ?>">#</a>
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
            foreach ($languages as $lang) {
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
	            <a title="<?php echo i18n_r('VIEWPAGE_TITLE').': '.stripslashes($variant['title']); ?>" target="_blank" href="<?php echo $isi18n ? find_i18n_url($page['url'],$page['parent'],$lang) : find_url($page['url'],$page['parent']); ?>">#</a>
	          </td>
	          <td class="delete" >
              <a class="i18n-delconfirm" href="deletefile.php?id=<?php echo $variant['url']; ?>&nonce=<?php echo get_nonce("delete", "deletefile.php"); ?>" title="<?php i18n('DELETEPAGE_TITLE').': '.stripslashes($variant['title']); ?>"><?php echo $is31 ? '&times;' : 'X' ?></a>
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
			<p><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php i18n('TOTAL_PAGES'); ?></em></p>
      <p>
        <a href="edit.php?special=<?php echo $special; ?>"><?php i18n('CREATE_NEW_PAGE'); ?></a>
        &nbsp;<?php i18n('OR'); ?>&nbsp;
        <a class="cancel" href="load.php?id=i18n_specialpages&amp;pages"><?php i18n('CANCEL'); ?></a>
      </p>
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
        $(function() {
          $('#filter').keyup(filterPages).autocomplete(tags, {
            minChars: 1,
            max: 50,
            scroll: true,
            multiple: false
          });
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
        });
        // ]]>
      </script>


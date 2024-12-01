<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager main admin panel.
 */


/*******************************************************
 * @function nm_admin_panel
 * @action back-end main panel (post overview)
 */
function nm_admin_panel() {
  global $NMPAGEURL;
  $posts = nm_get_posts(true);
  if (nm_post_files_differ($posts)) {
    nm_update_cache();
    $posts = nm_get_posts(true);
    if (nm_post_files_differ($posts))
      nm_display_message('<b>Warning:</b> Post files/cache mismatch', true); // not translated
    else
      nm_display_message('Post cache file has been updated', false); // not translated
  }

  $totalposts = count($posts);
  if (defined('NMPAGINATION'))
    $postsperpage = (intval(NMPAGINATION) > 0) ? intval(NMPAGINATION) : $totalposts;
  else
    $postsperpage = 15;
  if (isset($_GET['showall']) || $postsperpage >= $totalposts) {
    $showall = true;
  } else {
    $showall = false;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $posts = array_slice($posts, ($page-1)*$postsperpage, $postsperpage);
    $last = ceil($totalposts/$postsperpage);
  }

  ?>
  <h3 class="floated"><?php i18n('news_manager/PLUGIN_NAME'); ?></h3>
  <div class="edit-nav clearfix">
    <a href="#" id="filter-button" ><?php i18n('FILTER'); ?></a>
    <a href="load.php?id=news_manager&amp;edit"><?php i18n('news_manager/NEW_POST'); ?></a>
    <?php if (nm_allow_settings()) { ?>
      <a href="load.php?id=news_manager&amp;settings"><?php i18n('news_manager/SETTINGS'); ?></a>
    <?php } ?>
  </div>
  <?php
  if (!empty($posts)) {
    ?>
    <div id="filter-search">
      <form>
        <input type="text" class="text" id="tokens" placeholder="<?php echo lowercase(strip_tags(i18n_r('FILTER'))); ?>..." />
        &nbsp;
        <a href="load.php?id=news_manager" class="cancel"><?php i18n('news_manager/CANCEL'); ?></a>
      </form>
    </div>
    <table id="posts" class="highlight">
    <tr>
      <th><?php i18n('news_manager/POST_TITLE'); ?></th>
      <th style="text-align: right;"><?php i18n('news_manager/DATE'); ?></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
    <?php
    foreach ($posts as $post) {
      $title = stripslashes($post->title);
      $date = shtDate($post->date);
      $url = nm_get_url('post') . $post->slug;
      $url = nm_patch_i18n_url($url);
      ?>
      <tr>
        <td class="posttitle">
          <a href="load.php?id=news_manager&amp;edit=<?php echo $post->slug; ?>" title="<?php i18n('news_manager/EDIT_POST'); ?>: <?php echo $title; ?>">
            <?php echo $title; ?>
          </a>
        </td>
        <td style="text-align: right;">
          <?php 
            if (strtotime($post->date) > time())
              echo '<span style="color:#aaa">',$date,'</span>';
            else
              echo '<span>',$date,'</span>';
          ?>
        </td>
        <td style="width: 20px;text-align: center;">
          <?php
            if ($post->private == 'Y')
              echo '<span style="color: #aaa;">P</span>';
          ?>
        </td>
        <td class="secondarylink">
          <?php if ($NMPAGEURL && $NMPAGEURL != '') { ?>
            <a href="<?php echo $url; ?>" target="_blank" title="<?php i18n('news_manager/VIEW_POST'); ?>: <?php echo $title; ?>">
              #
            </a>
          <?php } ?>
        </td>
        <td class="delete">
          <a href="load.php?id=news_manager&amp;delete=<?php echo $post->slug; ?>" class="nm_delconfirm" title="<?php i18n('news_manager/DELETE_POST'); ?>: <?php echo $title; ?>?">
            &times;
          </a>
        </td>
      </tr>
      <?php
    }
    ?>
    </table>
    <?php
      if (!$showall) {
    ?>
    <p class="nm_pagination">
    <?php
        if ($page > 1) echo '<span class="prev"><a href="load.php?id=news_manager&amp;page=',$page-1,'">',i18n('news_manager/PREV_TEXT'),'</a></span> ';
        for ($i = 1; $i <= $last; $i++) {
          if ($i == $page) 
            echo '<span class="current"><span>',$i,'</span></span> ';
          else
            echo '<span><a href="load.php?id=news_manager&amp;page=',$i,'">',$i,'</a></span> ';
        }
        if ($page < $last)
          echo ' <span class="next"><a href="load.php?id=news_manager&amp;page=',$page+1,'">',i18n('news_manager/NEXT_TEXT'),'</a></span>';
    ?>
    </p>
    <?php
      }
    ?>
    <p>
      <b><?php
        if ($postsperpage >= $totalposts)
          echo $totalposts;
        elseif ($showall)
          echo '<a href="load.php?id=news_manager">',$postsperpage,'</a> / ',$totalposts;
        else
          echo count($posts),' / <a href="load.php?id=news_manager&amp;showall">',$totalposts,'</a>';
      ?></b>
      <?php i18n('news_manager/POSTS'); ?> 
    </p>

    <script>
    $(document).ready(function() {
      // filter button opens up filter dialog
      $("#filter-button").on("click", function($e) {
        $e.preventDefault();
        $("#filter-search").slideToggle();
        $(this).toggleClass("current");
        $("#filter-search #tokens").focus();
      });
      // ignore enter key in filter form
      $("#filter-search #tokens").keydown(function($e) {
        if($e.keyCode == 13) {
          $e.preventDefault();
        }
      });
      // close filter dialog on cancel
      $("#filter-search .cancel").on("click", function($e) {
        $e.preventDefault();
        $("#posts tr").show();
        $('#filter-button').toggleClass("current");
        $("#filter-search #tokens").val("");
        $("#filter-search").slideUp();
      });
      // filter table, see:
      // http://kobikobi.wordpress.com/2008/09/15/using-jquery-to-filter-table-rows/
      $("#posts tr:has(td.posttitle)").each(function() {
        var t = $(this).find('td.posttitle').text().toLowerCase();
        $("<td class='indexColumn'></td>")
        .hide().text(t).appendTo(this);
      });
      $("#tokens").keyup(function() {
        var s = $(this).val().toLowerCase().split(" ");
      $("#posts tr:hidden").show();
      $.each(s, function(){
           $("#posts tr:visible .indexColumn:not(:contains('"
              + this + "'))").parent().hide();
        });
      });
      // confirm delete 
      $('.nm_delconfirm').on('click', function () {
        return confirm($(this).attr("title"));
      });
    });
    </script>

    <?php
  }
}


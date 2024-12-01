<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager edit settings template
 */

?>
<h3><?php i18n('news_manager/NM_SETTINGS'); ?></h3>
<p class="hint">
  <?php echo sprintf(i18n_r('news_manager/DOCUMENTATION'), 'http://newsmanager.c1b.org/documentation/'); ?>
</p>
<form class="largeform" id="settings" action="load.php?id=news_manager" method="post" accept-charset="utf-8">
  <div class="leftsec">
    <p>
      <label for="page-url"><?php i18n('news_manager/PAGE_URL'); ?>:</label>
      <select class="text" name="page-url" id="page-url">
      <?php
      $pages = glob(GSDATAPAGESPATH.'*.xml');
      foreach ($pages as &$page)
        $page = substr(basename($page), 0, -4);
      $pages = array_diff($pages, array('index'));
      array_unshift($pages, '', 'index');
      foreach ($pages as $slug) {
        $option = ($slug != '') ? $slug : '-';
        echo '<option value="',$slug,'"';
        if ($slug == $NMPAGEURL) echo ' selected="selected"';
        echo '>',$option,'</option>',"\n";
      }
      ?>
      </select>
      <label id="no-page" class="invalid" <?php if ($NMPAGEURL != '') echo ' style="display:none"'; ?>><?php i18n('news_manager/NO_PAGE_SELECTED'); ?></label>
    </p>
  </div>
  <div class="rightsec">
    <p>
      <label for="posts-per-page"><?php i18n('news_manager/POSTS_PER_PAGE'); ?>:</label>
      <input class="text required" type="text" name="posts-per-page" id="posts-per-page" value="<?php echo $NMPOSTSPERPAGE; ?>" />
    </p>
  </div>
  <div class="clear"></div>
  <div class="leftsec">
    <p>
      <label for="language"><?php i18n('news_manager/LANGUAGE'); ?></label>
      <select class="text" name="language" id="language">
      <?php
      $languages = nm_get_languages();
      foreach ($languages as $lang=>$file) {
        if ($lang == $NMLANG)
          echo "<option value=\"$lang\" selected=\"selected\">$lang</option>\n";
        else
          echo "<option value=\"$lang\">$lang</option>\n";
      }
      ?>
      </select>
    </p>
  </div>
  <div class="rightsec">
    <p>
      <label for="recent-posts"><?php i18n('news_manager/RECENT_POSTS'); ?>:</label>
      <input class="text required" type="text" name="recent-posts" id="recent-posts" value="<?php echo $NMRECENTPOSTS; ?>" />
    </p>
  </div>
  <div class="clear"></div>
  <div class="leftsec">
    <p>
      <label for="show-excerpt"><?php i18n('news_manager/SHOW_POSTS_AS'); ?>:</label>
      <input name="show-excerpt" type="radio" value="0" <?php if ($NMSHOWEXCERPT != 'Y') echo "checked=\"checked\""; ?> style="vertical-align: middle;" />
      &nbsp;<?php i18n('news_manager/FULL_TEXT'); ?>
      <span style="margin-left: 30px;">&nbsp;</span>
      <input name="show-excerpt" type="radio" value="1" <?php if ($NMSHOWEXCERPT == 'Y') echo "checked=\"checked\""; ?> style="vertical-align: middle;" />
      &nbsp;<?php i18n('news_manager/EXCERPT'); ?>
    </p>
  </div>
  <div class="rightsec">
    <p>
      <label for="archivesby"><?php i18n('news_manager/ENABLE_ARCHIVES'); ?>:</label>
      <select class="text" name="archivesby" id="archivesby">
        <option value="m"<?php if ($NMSETTING['archivesby']=='m') echo ' selected="selected"'; ?>><?php i18n('news_manager/BY_MONTH'); ?></option>
        <option value="y"<?php if ($NMSETTING['archivesby']=='y') echo ' selected="selected"'; ?>><?php i18n('news_manager/BY_YEAR'); ?></option>
      </select>
    </p>
  </div>
  <div class="clear"></div>
  <div class="leftsec">
    <p>
      <label for="excerpt-length"><?php i18n('news_manager/EXCERPT_LENGTH'); ?>:</label>
      <input class="text required" type="text" name="excerpt-length" id="excerpt-length" value="<?php echo $NMEXCERPTLENGTH; ?>" />
    </p>
  </div>
  <div class="rightsec">
    <p>
      <label for="readmore"><?php i18n('news_manager/READ_MORE_LINK'); ?>:</label>
      <select class="text" name="readmore" id="readmore">
        <option value="N"<?php if ($NMSETTING['readmore']=='N') echo ' selected="selected"'; ?>><?php i18n('NO'); ?></option>
        <option value="R"<?php if ($NMSETTING['readmore']=='R') echo ' selected="selected"'; ?>><?php i18n('YES'); ?></option>
        <option value="F"<?php if ($NMSETTING['readmore']=='F') echo ' selected="selected"'; ?>><?php i18n('news_manager/ALWAYS'); ?></option>
      </select>
    </p>
  </div>
  <div class="clear"></div>
  <div class="leftsec">
    <p>
      <label for="titlelink"><?php i18n('news_manager/TITLE_LINK'); ?>:</label>
      <select class="text" name="titlelink" id="titlelink">
        <option value="Y"<?php if ($NMSETTING['titlelink']=='Y') echo ' selected="selected"'; ?>><?php i18n('YES'); ?></option>
        <option value="P"<?php if ($NMSETTING['titlelink']=='P') echo ' selected="selected"'; ?>><?php i18n('news_manager/NOT_SINGLE'); ?></option>
        <option value="N"<?php if ($NMSETTING['titlelink']=='N') echo ' selected="selected"'; ?>><?php i18n('NO'); ?></option>
      </select>
    </p>
  </div>
  <div class="rightsec">
    <p>
      <label for="gobacklink"><?php i18n('news_manager/GO_BACK_LINK'); ?>:</label>
      <select class="text" name="gobacklink" id="gobacklink">
        <option value="B"<?php if ($NMSETTING['gobacklink']=='B') echo ' selected="selected"'; ?>><?php i18n('news_manager/BROWSER_BACK'); ?></option>
        <option value="M"<?php if ($NMSETTING['gobacklink']=='M') echo ' selected="selected"'; ?>><?php i18n('news_manager/MAIN_NEWS_PAGE'); ?></option>
        <option value="N"<?php if ($NMSETTING['gobacklink']=='N') echo ' selected="selected"'; ?>><?php i18n('NO'); ?></option>
      </select>
    </p>
  </div>
  <div class="clear"></div>
  <div class="leftsec">
    <p>
      <label for="images"><?php i18n('news_manager/ENABLE_IMAGES'); ?>:</label>
      <select class="text" name="images" id="images">
        <option value="N"<?php if ($NMSETTING['images']=='N') echo ' selected="selected"'; ?>><?php i18n('NO'); ?></option>
        <option value="Y"<?php if ($NMSETTING['images']=='Y') echo ' selected="selected"'; ?>><?php i18n('YES'); ?></option>
        <option value="P"<?php if ($NMSETTING['images']=='P') echo ' selected="selected"'; ?>><?php i18n('news_manager/NOT_SINGLE'); ?></option>
        <option value="M"<?php if ($NMSETTING['images']=='M') echo ' selected="selected"'; ?>><?php i18n('news_manager/MAIN_NEWS_PAGE'); ?></option>
      </select>
    </p>
  </div>
  <div class="rightsec" id="divimagelink">
    <p class="inline">
      <br />
      <input name="imagelink" id="imagelink" type="checkbox" <?php if ($NMSETTING['imagelink'] == '1') echo 'checked'; ?> />&nbsp;
      <label for="imagelink"><?php i18n('news_manager/IMAGE_LINKS'); ?></label>
    </p>
  </div>
  <div class="clear"></div>
  <div id="divimageoptions">
    <div class="leftsec">
      <p>
        <label for="imagewidth"><?php i18n('news_manager/IMAGE_WIDTH'); ?>:</label>
        <input class="text" type="text" name="imagewidth" id="imagewidth" value="<?php echo $NMSETTING['imagewidth']; ?>" placeholder="0 = <?php i18n('news_manager/FULL'); ?>" />
      </p>
    </div>
    <div class="rightsec">
      <p>
        <label for="imageheight"><?php i18n('news_manager/IMAGE_HEIGHT'); ?>:</label>
        <input class="text" type="text" name="imageheight" id="imageheight" value="<?php echo $NMSETTING['imageheight']; ?>" placeholder="0 = <?php i18n('news_manager/FULL'); ?>" />
      </p>
    </div>
    <div class="clear"></div>
    <div class="leftsec">
      <p class="inline">
        <input name="imagecrop" id="imagecrop" type="checkbox" <?php if ($NMSETTING['imagecrop'] == '1') echo 'checked'; ?> />&nbsp;
        <label for="imagecrop"><?php i18n('news_manager/IMAGE_CROP'); ?></label>
      </p>
    </div>
    <div class="rightsec">
      <p class="inline">
        <input name="imagealt" id="imagealt" type="checkbox" <?php if ($NMSETTING['imagealt'] == '1') echo 'checked'; ?> />&nbsp;
        <label for="imagealt"><?php i18n('news_manager/IMAGE_ALT'); ?></label>
      </p>
    </div>
    <div class="clear"></div>
  </div>
  <p class="inline">
    <input name="enablecustomsettings" id="enablecustomsettings" type="checkbox" <?php if ($NMSETTING['enablecustomsettings'] == '1') echo 'checked'; ?> />&nbsp;
    <label for="enablecustomsettings"><?php i18n('news_manager/CUSTOM_SETTINGS'); ?></label>
    <br />
    <textarea style="height:150px" name="customsettings" id="customsettings"><?php echo htmlspecialchars($NMSETTING['customsettings'],ENT_NOQUOTES); ?></textarea>
  </p>

  <?php if ( $PRETTYURLS == 1 && (!$PERMALINK || strpos($PERMALINK,'?') === false) )  { ?>
  <p class="inline">
    <input name="pretty-urls" id="pretty-urls" type="checkbox" <?php if ($NMPRETTYURLS == 'Y') echo 'checked'; ?> />&nbsp;
    <label for="pretty-urls"><?php i18n('news_manager/PRETTY_URLS'); ?></label> -
    <span class="hint"><?php i18n('news_manager/PRETTY_URLS_NOTE'); ?> <a href="load.php?id=news_manager&amp;htaccess"><?php i18n('MORE'); ?></a></span>
  </p>
  <?php } ?>
  <p>
    <span>
      <input class="submit" type="submit" name="settings" value="<?php i18n('news_manager/SAVE_SETTINGS'); ?>" />
    </span>
    &nbsp;&nbsp;<?php i18n('news_manager/OR'); ?>&nbsp;&nbsp;
    <a href="load.php?id=news_manager&amp;cancel" class="cancel"><?php i18n('news_manager/CANCEL'); ?></a>
  </p>
</form>

<script>
  if ($.validator) {
    jQuery.extend(jQuery.validator.messages, {
      required: "<?php i18n('news_manager/FIELD_IS_REQUIRED'); ?>",
      min: jQuery.validator.format("<?php echo str_replace('%d', '{0}', i18n_r('news_manager/ENTER_VALUE_MIN')); ?>")
    });
  }

  $(document).ready(function(){
    if ($.validator) {
      $("#settings").validate({
        errorClass: "invalid",
        rules: {
          "excerpt-length": { min: 0 },
          "posts-per-page": { min: 1 },
          "recent-posts": { min: 1 }
        }
      });
    }

    $('.submit').clone().appendTo('#sidebar');
    $('#sidebar .submit').css({'margin-left': '14px'}).click(function() { $('form#settings.largeform input.submit').trigger('click'); });

    if ($('#images option:selected').val() == "N"){
      $('#divimagelink').hide();
      $('#divimageoptions').hide();
    }

    if ($('#enablecustomsettings').is(':checked')) {
      $('#customsettings').show();
    } else {
      $('#customsettings').hide();
    }

<?php if (!defined('NMWARNUNSAVED') || NMWARNUNSAVED) { ?>
    $('form').areYouSure({'message':'<?php i18n('UNSAVED_INFORMATION'); ?>'});
<?php } ?>

  });

  $('#images').change(function(){
    if ($('#images option:selected').val() == "N"){
      $('#divimagelink').hide();
      $('#divimageoptions').hide();
    } else {
      $('#divimagelink').show();
      $('#divimageoptions').show();
    }
  });

  $('#enablecustomsettings').change(function(){
    if ($('#enablecustomsettings').is(':checked')) {
      $('#customsettings').show();
    } else {
      $('#customsettings').hide();
    }
  });

  $('#page-url').change(function(){
    if ($('#page-url option:selected').val() == "") {
      $('#no-page').show();
    } else {
      $('#no-page').hide();
    }
  });
</script>

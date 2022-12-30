<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager edit post template
 */

global $NMPAGEURL;

# image input field (since 3.0)
global $NMSETTING;
if (defined('NMIMAGEINPUT')) {
  $imageinputpos = intval(NMIMAGEINPUT);
  if ($imageinputpos < 0 || $imageinputpos > 4) $imageinputpos = 2;
} else {
  $imageinputpos = $NMSETTING['images'] != 'N' ? 2 : 0;
}
if ($imageinputpos > 0) {
  global $SITEURL;
  if (defined('NMIMAGEDIR')) {
    $imagepath = '&path='.trim(NMIMAGEDIR, '/');
  } else {
    $imagepath = '';
  }
  $imageinputcode = '  <p>
      <label for="post-image">'.i18n_r('news_manager/POST_IMAGE').':</label>
      <input class="text short" id="post-image" name="post-image" type="text" style="width:450px" value="'.$image.'" />
      <span class="edit-nav"><a href="#" id="browse-image">'.i18n_r('SELECT_FILE').'</a></span>
    </p>
    <div class="clear"></div>
    <script type="text/javascript">'."
      function fill_image(url) {
        $('#post-image').val(url);
      }
      $(function() {
        $('#browse-image').click(function(e) {
          e.preventDefault();
          window.open('../plugins/news_manager/browser/filebrowser.php?func=fill_image&type=images".$imagepath."', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
        });
      });
    </script>
";
} else {
  $imageinputcode = '<input name="post-image" type="hidden" value="'.$image.'" />
';
}

?>

<h3 class="floated">
  <?php
  if (empty($data))
    i18n('news_manager/NEW_POST');
  else
    i18n('news_manager/EDIT_POST');
  ?>
</h3>
<div class="edit-nav" >
  <?php
  if (!empty($NMPAGEURL) && $NMPAGEURL != '' && !$newpost) {
    $url = nm_get_url('post') . $slug;
    $url = nm_patch_i18n_url($url);
    ?>
    <a href="<?php echo $url; ?>" target="_blank">
      <?php i18n('news_manager/VIEW_POST'); ?>
    </a>
    <?php
  }
  ?>
  <a href="#" id="metadata_toggle">
    <?php i18n('news_manager/POST_OPTIONS'); ?>
  </a>
  <div class="clear"></div>
</div>
<form class="largeform" id="edit" action="load.php?id=news_manager" method="post" accept-charset="utf-8">
  <?php
  if (!$newpost)
    echo '<input name="current-slug" type="hidden" value="',$slug,'" />';
  if (!empty($author))
    echo '<input name="author" type="hidden" value="',$author,'" />';
  ?>
  <p>
    <input class="text title required" name="post-title" id="post-title" type="text" value="<?php echo $title; ?>" placeholder="<?php i18n('news_manager/POST_TITLE'); ?>" />
  </p>
  <noscript><style>#metadata_window {display:block !important} </style></noscript>
  <div style="display:none;" id="metadata_window">
  <?php if ($imageinputpos <= 1) echo $imageinputcode; ?>
    <div class="leftopt">
      <p>
        <label for="post-slug"><?php i18n('news_manager/POST_SLUG'); ?>:</label>
        <input class="text short" id="post-slug" name="post-slug" type="text" value="<?php echo $slug; ?>" />
      </p>
    </div>
    <div class="rightopt">
      <p>
        <label for="post-tags"><?php i18n('news_manager/POST_TAGS'); ?>:</label>
        <input class="text short" id="post-tags" name="post-tags" type="text" value="<?php echo $tags; ?>" />
      </p>
    </div>
    <div class="leftopt">
      <p>
        <label for="post-date"><?php i18n('news_manager/POST_DATE'); ?>:</label>
        <input class="text short" id="post-date" name="post-date" type="text" value="<?php echo $date; ?>" />
      </p>
    </div>
    <div class="rightopt">
      <p>
        <label for="post-time"><?php i18n('news_manager/POST_TIME'); ?>:</label>
        <input class="text short" id="post-time" name="post-time" type="text" value="<?php echo $time; ?>" />
      </p>
    </div>
    <div class="clear"></div>
    <style>#post-private { width:auto !important; } /* properly align checkbox - fix for GetSimple 3.1+ */</style>
    <div class="leftopt">
      <p class="inline" id="post-private-wrap">
        <input type="checkbox" id="post-private" name="post-private" <?php echo $private; ?> />&nbsp;
        <label for="post-private"><?php i18n('news_manager/POST_PRIVATE'); ?></label>
      </p>
    </div>
    <div class="rightopt">
      <p>
        <label for="post-metad"><?php i18n('META_DESC'); ?>: <span id="countdownwrap"><strong id="countdown"></strong> <?php i18n('REMAINING'); ?></span></label>
				<textarea class="text" id="post-metad" name="post-metad" ><?php echo $metad; ?></textarea>
			</p>
    </div>
    <div class="clear"></div>
    <?php if ($imageinputpos == 2) echo $imageinputcode; ?>
  </div>
  <?php if ($imageinputpos == 3) echo $imageinputcode; ?>
  <p>
    <textarea name="post-content"><?php echo $content; ?></textarea>
  </p>
  <?php if ($imageinputpos == 4) echo $imageinputcode; ?>
  <p>
    <input name="post" type="submit" class="submit" value="<?php i18n('news_manager/SAVE_POST'); ?>" />
    &nbsp;&nbsp;<?php i18n('news_manager/OR'); ?>&nbsp;&nbsp;
    <a href="load.php?id=news_manager&amp;cancel" class="cancel"><?php i18n('news_manager/CANCEL'); ?></a>
    <?php
    if (!$newpost) {
      ?>
      /
      <a href="load.php?id=news_manager&amp;delete=<?php echo $slug; ?>" class="cancel">
        <?php i18n('news_manager/DELETE'); ?>
      </a>
      <?php
    }
    ?>
  </p>
</form>

<script type="text/javascript">

<?php if (!defined('NMWARNUNSAVED') || NMWARNUNSAVED) { ?>
  $('form').areYouSure( {'silent':true} );
  var warnme = false;
  var notsubmit = true;
  window.onbeforeunload = function () {
    if (typeof(CKEDITOR) != 'undefined') {
      if (CKEDITOR.instances["post-content"].checkDirty()) {
        warnme = true;
      }
    }
    if (notsubmit) {
      if (warnme || $('#edit').hasClass('dirty')) {
        return "<?php i18n('UNSAVED_INFORMATION'); ?>";
      }
    }
  }
  $('#edit').submit(function(){
    notsubmit = false;
  });
<?php } ?>

<?php
# date/time picker, validation
$datetimepicker = !defined('NMDATETIMEPICKER') || NMDATETIMEPICKER;
if (!defined('NMDATETIMEVALIDATION') && $datetimepicker)
  $datetimevalidation = false; // by default, date/time validation disabled if datetimepicker enabled
else
  $datetimevalidation = !defined('NMDATETIMEVALIDATION') || NMDATETIMEVALIDATION;
?>
  if ($.validator) {
    jQuery.extend(jQuery.validator.messages, {
<?php if ($datetimevalidation) { ?>
      dateISO: "<?php i18n('news_manager/ENTER_VALID_DATE'); ?>",
<?php } ?>
      required: "<?php i18n('news_manager/FIELD_IS_REQUIRED'); ?>"
    });
  }
  
  $(document).ready(function(){
    
<?php if ($datetimevalidation) { ?>
    if ($.validator) {
      $.validator.addMethod("time", function(value, element) {
          return this.optional(element) || /^([01]?[0-9]|2[0-3]):[0-5][0-9]/.test(value);
      },
      "<?php i18n('news_manager/ENTER_VALID_TIME'); ?>")
    }
<?php } ?>
    if ($.validator) {
      $("#edit").validate({
<?php if ($datetimevalidation) { ?>
        rules: {
          "post-date": { dateISO: true },
          "post-time": { time: true }
        },
<?php } ?>
        errorClass: "invalid"
      })
    }

    $("#<?php echo (empty($data)) ? 'post-title' : 'metadata_toggle'; ?>").focus();

    $('.submit').clone().appendTo('#sidebar');
    $('#sidebar .submit').css({'margin-left': '14px'}).click(function() { $('form#edit.largeform input.submit').trigger('click'); });
    
    /* highlight private post label - fix for GetSimple 3.1+ */
    $("#post-private").change(function(){
      if ($("#post-private").is(":checked")) { 
        $("#post-private-wrap label").css("color", '#cc0000');
      } else {
        $("#post-private-wrap label").css("color", '#333333'); 
      }
    });
    if ($("#post-private").is(":checked")) { 
      $("#post-private-wrap label").css("color", '#cc0000');
    } else {
      $("#post-private-wrap label").css("color", '#333333'); 
    }

  });
<?php
if ($datetimepicker) {
  global $LANG;
?>
  jQuery.datetimepicker.setLocale('<?php echo substr($LANG, 0, 2); ?>');
  jQuery('#post-date').datetimepicker({
    format: 'Y-m-d',
    timepicker: false,
    dayOfWeekStart: <?php echo intval(i18n_r('news_manager/DAY_OF_WEEK_START')); ?> 
  });
  jQuery('#post-time').datetimepicker({
    format: 'H:i',
    datepicker: false
  });
<?php 
}
?>

</script>

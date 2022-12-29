<?php
i18n_gallery_register('prettyphoto', 'prettyPhoto', 
  '<strong>prettyPhoto</strong> is a jQuery lightbox clone. It features a slideshow mode and can display both titel and description of an image.<br/>'.
  'License: Creative Commons Attribution 2.5<br/>'.
  '<a target="_blank" href="http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/">http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/</a>',
  'i18n_gallery_prettyphoto_edit', 'i18n_gallery_prettyphoto_header', 'i18n_gallery_prettyphoto_content');

function i18n_gallery_prettyphoto_edit($gallery) {
?>
  <p>
    <label for="prettyphoto-thumbwidth"><?php i18n('i18n_gallery/MAX_THUMB_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="prettyphoto-thumbwidth" name="prettyphoto-thumbwidth" value="<?php echo @$gallery['thumbwidth']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="prettyphoto-thumbheight" name="prettyphoto-thumbheight" value="<?php echo @$gallery['thumbheight']; ?>" style="width:5em"/>
    &nbsp;
    <span id="prettyphoto-thumbcrop-span">
      <input type="checkbox" id="prettyphoto-thumbcrop" name="prettyphoto-thumbcrop" value="1" <?php echo @$gallery['thumbcrop'] ? 'checked="checked"' : ''; ?> style="vertical-align:middle; width:auto;"/> 
      <?php i18n('i18n_gallery/CROP'); ?>
    </span>
  </p>
  <p class="inline">
    <label for="prettyphoto-thumbtitles"><?php i18n('i18n_gallery/SHOW_THUMB_TITLES'); ?></label>
    <input type="checkbox" id="prettyphoto-thumbtitles" name="prettyphoto-thumbtitles" value="1" <?php echo @$gallery['thumbtitles'] ? 'checked="checked"' : ''; ?> />
  </p>
  <p class="inline">
    <label for="prettyphoto-pagify"><?php i18n('i18n_gallery/PAGIFY'); ?></label>
    <input type="checkbox" id="prettyphoto-pagify" name="prettyphoto-pagify" value="1" <?php echo @$gallery['pagify'] ? 'checked="checked"' : ''; ?> />
  </p>
  <p id="prettyphoto-pagify-span">
    <label for="prettyphoto-pagesize"><?php i18n('i18n_gallery/PAGESIZE'); ?></label>
    <input type="text" class="text" id="prettyphoto-pagesize" name="prettyphoto-pagesize" value="<?php echo @$gallery['pagesize']; ?>" style="width:5em"/>
  </p>
  <p>
    <label for="prettyphoto-width"><?php i18n('i18n_gallery/MAX_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="prettyphoto-width" name="prettyphoto-width" value="<?php echo @$gallery['width']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="prettyphoto-height" name="prettyphoto-height" value="<?php echo @$gallery['height']; ?>" style="width:5em"/>
    &nbsp;
    <span id="prettyphoto-crop-span">
      <input type="checkbox" id="prettyphoto-crop" name="prettyphoto-crop" value="1" <?php echo @$gallery['crop'] ? 'checked="checked"' : ''; ?> style="vertical-align:middle; width:auto;"/> 
      <?php i18n('i18n_gallery/CROP'); ?>
    </span>
  </p>
  <p class="inline">
    <label for="prettyphoto-autostart"><?php i18n('i18n_gallery/AUTOSTART'); ?></label>
    <input type="checkbox" id="prettyphoto-autostart" name="prettyphoto-autostart" value="1" <?php echo @$gallery['autostart'] ? 'checked="checked"' : ''; ?> />
  </p>
  <p>
    <label for="prettyphoto-interval"><?php i18n('i18n_gallery/INTERVAL'); ?></label>
    <input type="text" class="text" id="prettyphoto-interval" name="prettyphoto-interval" value="<?php echo @$gallery['interval']; ?>" style="width:5em"/>
  </p>
  <p>
    <label for="prettyphoto-theme"><?php i18n('i18n_gallery/THEME'); ?></label>
    <select class="text" id="prettyphoto-theme" name="prettyphoto-theme">
      <option value="pp_default" <?php if (@$gallery['theme'] == 'default') echo 'selected="selected"'; ?> >Default</option>
      <option value="light_rounded" <?php if (@$gallery['theme'] == 'light_rounded') echo 'selected="selected"'; ?> >Light Rounded</option>
      <option value="dark_rounded" <?php if (@$gallery['theme'] == 'dark_rounded') echo 'selected="selected"'; ?> >Dark Rounded</option>
      <option value="light_square" <?php if (@$gallery['theme'] == 'light_square') echo 'selected="selected"'; ?> >Light Square</option>
      <option value="dark_square" <?php if (@$gallery['theme'] == 'dark_square') echo 'selected="selected"'; ?> >Dark Square</option>
      <option value="facebook" <?php if (@$gallery['theme'] == 'facebook') echo 'selected="selected"'; ?> >Facebook</option>
    </select>
  </p>
  <script type="text/javascript">
    function changePrettyphotoThumbSize() {
      var show = $.trim($('#prettyphoto-thumbwidth').val()) != '' && $.trim($('#prettyphoto-thumbheight').val()) != '';
      if (show) $('#prettyphoto-thumbcrop-span').show(); else $('#prettyphoto-thumbcrop-span').hide().find('input').attr('checked',false);
    }
    function changePrettyphotoSize() {
        var show = $.trim($('#prettyphoto-width').val()) != '' && $.trim($('#prettyphoto-height').val()) != '';
        if (show) $('#prettyphoto-crop-span').show(); else $('#prettyphoto-crop-span').hide().find('input').attr('checked',false);
    }
    function changePrettyphotoPagify() {
        var show = $('#prettyphoto-pagify:checked').size() > 0;
        if (show) $('#prettyphoto-pagify-span').show(); else $('#prettyphoto-pagify-span').hide().find('input').val('');
    }
    $(function() {
      $('#prettyphoto-thumbwidth, #prettyphoto-thumbheight').change(changePrettyphotoThumbSize);
      $('#prettyphoto-width, #prettyphoto-height').change(changePrettyphotoSize);
      $('#prettyphoto-pagify').click(changePrettyphotoPagify);
      changePrettyphotoThumbSize();
      changePrettyphotoSize();
      changePrettyphotoPagify();
    });
  </script>
<?php
}

function i18n_gallery_prettyphoto_header($gallery) {
  if (i18n_gallery_check($gallery,'jquery') && i18n_gallery_needs_include('jquery.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery-1.11.2.min.js"></script>
<?php
  }
  if (i18n_gallery_check($gallery,'js') && i18n_gallery_check($gallery,'pagify',false) && i18n_gallery_needs_include('pagify.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery.pagify.js"></script>
<?php    
  }
  if (i18n_gallery_check($gallery,'js') && i18n_gallery_needs_include('prettyphoto.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery.prettyPhoto.js"></script>
<?php
  } 
  if (i18n_gallery_check($gallery,'css') && i18n_gallery_needs_include('prettyphoto.css')) { 
?>
    <link rel="stylesheet" href="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
    <style type="text/css">
      .pp_pic_holder a {
        text-decoration: none;
        border-bottom: none;
      }
      .gallery-prettyphoto * {
        padding: 0;
        margin: 0;
        border: 0 none;
        vertical-align: middle;
        text-align: center;
      }
      .gallery-prettyphoto .gallery-thumb {
        float: left;
        display: block;
        padding: 3px;
        border: solid 1px #C7C7C7;
        margin-right: 10px;
        margin-bottom: 10px;
      }
      .gallery-prettyphoto a {
        display: table-cell;
        text-decoration: none;
      }
      .gallery-prettyphoto .gallery-thumb .gallery-title {
        margin: 0;
        padding: 2px 5px;
      }
      .gallery-prettyphoto .gallery-image { 
        float: left;
        padding: 3px;
        border: solid 1px #C7C7C7;
        max-width: 100%;
      }
      .gallery-prettyphoto .gallery-image a {
        float: left;
        left: auto;
      }
      .gallery-prettyphoto .gallery-image a.pp_close {
        position: relative;
        float: right;
      }
      .gallery-prettyphoto div.pagify {
        clear: both;
        text-align: left;
      }
      .gallery-prettyphoto div.pagify a {
        display: inline;
        font-size: 18px;
        border: 0 none;
        text-decoration: none;
        color: #999999;
        padding: 0px 5px;
        margin: 0px 2px;
        border: 1px solid #999999;
        background-color: white;
      }
      .gallery-prettyphoto div.pagify a.current {
        color: #C5400E;
      }
    </style>
<?php
  }
  if (i18n_gallery_check($gallery,'css')) {
    $id = i18n_gallery_id($gallery);
    $tw = @$gallery['thumbwidth'];
    $th = @$gallery['thumbheight'];
?>
    <style type="text/css">
      .gallery-<?php echo $id; ?>.gallery-prettyphoto a {
        <?php if ($th) echo 'height: '.$th.'px;'; ?> 
      }
    </style>
<?php
  }
}

function i18n_gallery_prettyphoto_content($gallery, $pic) {
  $id = i18n_gallery_id($gallery);
  if (i18n_gallery_is_show_image($pic)) {
    $item = i18n_gallery_item($gallery, $pic);
?>
    <div class="gallery gallery-prettyphoto gallery-<?php echo $id; ?>">
      <div class="gallery-image pp_default">
        <a class="pp_arrow_previous" href="<?php i18n_gallery_prev_link($gallery,$pic); ?>" title="<?php i18n_gallery_PREV(); ?>"><?php i18n_gallery_PREV(); ?></a>
        <a class="pp_arrow_next" href="<?php i18n_gallery_next_link($gallery,$pic); ?>" title="<?php i18n_gallery_NEXT(); ?>"><?php i18n_gallery_NEXT(); ?></a>
        <a class="pp_close" href="<?php i18n_gallery_back_link(); ?>" title="<?php i18n_gallery_BACK(); ?>"><?php i18n_gallery_BACK(); ?></a>
        <h2><?php echo htmlspecialchars(@$item['_title']); ?></h2>
        <img src="<?php i18n_gallery_image_link($gallery,$pic); ?>" alt="<?php echo htmlspecialchars(@$item['_title']); ?>"/>
        <?php if (@$item['_description']) echo '<p>'.htmlspecialchars(@$item['_description']).'</p>'; ?>
      </div>
    </div>
<?php
  } else { 
    $thumb = i18n_gallery_thumb($gallery);
    $showtitles = i18n_gallery_check($gallery, 'thumbtitles', false);
    $pageSize = !isset($thumb) && i18n_gallery_check($gallery,'pagify',false) ? (int) $gallery['pagesize'] : 0;
?>
    <div class="gallery gallery-prettyphoto gallery-<?php echo $id; ?>">
<?php
    $i = 0;
    foreach ($gallery['items'] as $item) { 
?>
      <div class="gallery-thumb" <?php if (isset($thumb) && $thumb != $i) echo 'style="display:none"'; ?>>
        <a href="<?php i18n_gallery_pic_link($gallery,$i); ?>" rel="prettyPhoto[<?php echo $id; ?>]" title="<?php echo htmlspecialchars(@$item['_description']); ?>">
          <img src="<?php i18n_gallery_thumb_link($gallery,$item); ?>" alt="<?php echo htmlspecialchars(@$item['_title']); ?>"/>
        </a>
<?php if ($showtitles) { ?>
        <p class="gallery-title"><?php echo htmlspecialchars(@$item['_title']); ?></p>
<?php } ?>
      </div>
<?php 
      $i++;
    } 
?>
      <div style="clear:both"></div>
    </div>
    <script type="text/javascript">
      $(document).ready(function(){
        var $sel = $("a[rel='prettyPhoto[<?php echo $id; ?>]']");
        <?php i18n_gallery_replace_nojs_links($gallery, '$sel'); ?>
        $sel.prettyPhoto({
<?php if (i18n_gallery_check($gallery,'autostart',false) && $pic == null) echo "autoplay_slideshow: true, "; ?>
<?php if (@$gallery['theme']) echo "theme: ".json_encode($gallery['theme']).", "; ?>
<?php if (intval(@$gallery['interval'])) echo "slideshow: ".intval(@$gallery['interval']).", "; ?>
          social_tools: false
<?php foreach ($gallery as $key => $value) if (substr($key,0,2) == 'x-') { ?>
          ,<?php echo substr($key,2); ?>: <?php echo preg_match('/^(null|true|false|\d+)$/',$value) ? $value : json_encode($value); ?>
<?php } ?>
        });
<?php if (i18n_gallery_is_goto_image($pic)) { ?>
        if (window.location.href.indexOf('#') < 0) $("a[rel='prettyPhoto[<?php echo $id; ?>]']:eq(<?php echo (int) $pic; ?>)").trigger('click');
<?php } else if (i18n_gallery_check($gallery,'autostart',false)) { ?>
        if (window.location.href.indexOf('#') < 0) $("a[rel='prettyPhoto[<?php echo $id; ?>]']:eq(0)").trigger('click');
<?php } ?>
<?php if ($pageSize >= 1) { ?>
        $('.gallery-<?php echo $id; ?>').pagify({ pageSize:<?php echo $pageSize; ?>, items:'.gallery-thumb'});
<?php } ?>
      });
    </script>
<?php
  }
}

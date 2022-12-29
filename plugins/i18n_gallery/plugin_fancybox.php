<?php
i18n_gallery_register('fancybox', 'fancybox', 
  '<strong>FancyBox</strong> is a tool for displaying images and can display a title along with the image. It does not have a slideshow feature.<br/>'.
  'License: MIT and GPL<br/>'.
  '<a target="_blank" href="http://fancybox.net/">http://fancybox.net/</a>',
  'i18n_gallery_fancybox_edit', 'i18n_gallery_fancybox_header', 'i18n_gallery_fancybox_content');

function i18n_gallery_fancybox_edit($gallery) {
?>
  <p>
    <label for="fancybox-thumbwidth"><?php i18n('i18n_gallery/MAX_THUMB_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="fancybox-thumbwidth" name="fancybox-thumbwidth" value="<?php echo @$gallery['thumbwidth']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="fancybox-thumbheight" name="fancybox-thumbheight" value="<?php echo @$gallery['thumbheight']; ?>" style="width:5em"/>
    &nbsp;
    <span id="fancybox-thumbcrop-span">
      <input type="checkbox" id="fancybox-thumbcrop" name="fancybox-thumbcrop" value="1" <?php echo @$gallery['thumbcrop'] ? 'checked="checked"' : ''; ?> style="vertical-align:middle; width:auto;"/> 
      <?php i18n('i18n_gallery/CROP'); ?>
    </span>
  </p>
  <p class="inline">
    <label for="fancybox-thumbtitles"><?php i18n('i18n_gallery/SHOW_THUMB_TITLES'); ?></label>
    <input type="checkbox" id="fancybox-thumbtitles" name="fancybox-thumbtitles" value="1" <?php echo @$gallery['thumbtitles'] ? 'checked="checked"' : ''; ?> />
  </p>
  <p class="inline">
    <label for="fancybox-pagify"><?php i18n('i18n_gallery/PAGIFY'); ?></label>
    <input type="checkbox" id="fancybox-pagify" name="fancybox-pagify" value="1" <?php echo @$gallery['pagify'] ? 'checked="checked"' : ''; ?> />
  </p>
  <p id="fancybox-pagify-span">
    <label for="fancybox-pagesize"><?php i18n('i18n_gallery/PAGESIZE'); ?></label>
    <input type="text" class="text" id="fancybox-pagesize" name="fancybox-pagesize" value="<?php echo @$gallery['pagesize']; ?>" style="width:5em"/>
  </p>
  <p>
    <label for="fancybox-width"><?php i18n('i18n_gallery/MAX_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="fancybox-width" name="fancybox-width" value="<?php echo @$gallery['width']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="fancybox-height" name="fancybox-height" value="<?php echo @$gallery['height']; ?>" style="width:5em"/>
    &nbsp;
    <span id="fancybox-crop-span">
      <input type="checkbox" id="fancybox-crop" name="fancybox-crop" value="1" <?php echo @$gallery['crop'] ? 'checked="checked"' : ''; ?> style="vertical-align:middle; width:auto;"/> 
      <?php i18n('i18n_gallery/CROP'); ?>
    </span>
  </p>
  <script type="text/javascript">
    function changeFancyboxThumbSize() {
      var show = $.trim($('#fancybox-thumbwidth').val()) != '' && $.trim($('#fancybox-thumbheight').val()) != '';
      if (show) $('#fancybox-thumbcrop-span').show(); else $('#fancybox-thumbcrop-span').hide().find('input').attr('checked',false);
    }
    function changeFancyboxSize() {
        var show = $.trim($('#fancybox-width').val()) != '' && $.trim($('#fancybox-height').val()) != '';
        if (show) $('#fancybox-crop-span').show(); else $('#fancybox-crop-span').hide().find('input').attr('checked',false);
    }
    function changeFancyboxPagify() {
        var show = $('#fancybox-pagify:checked').size() > 0;
        if (show) $('#fancybox-pagify-span').show(); else $('#fancybox-pagify-span').hide().find('input').val('');
    }
    $(function() {
      $('#fancybox-thumbwidth, #fancybox-thumbheight').change(changeFancyboxThumbSize);
      $('#fancybox-width, #fancybox-height').change(changeFancyboxSize);
      $('#fancybox-pagify').click(changeFancyboxPagify);
      changeFancyboxThumbSize();
      changeFancyboxSize();
      changeFancyboxPagify();
    });
  </script>
<?php
}

function i18n_gallery_fancybox_header($gallery) {
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
  if (i18n_gallery_check($gallery,'js') && i18n_gallery_needs_include('fancybox.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery-migrate-1.2.1.min.js"></script>
	<script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery.mousewheel-3.0.4.pack.js"></script>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery.fancybox-1.3.4.pack.js"></script>
<?php
  } 
  if (i18n_gallery_check($gallery,'css') && i18n_gallery_needs_include('fancybox.css')) { 
?>
    <link rel="stylesheet" href="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" charset="utf-8" />
    <style type="text/css">
      .gallery-fancybox * {
        margin: 0;
        padding: 0;
        border: 0 none;
        vertical-align: middle;
        text-align: center;
      }
      .gallery-fancybox .gallery-thumb {
        float: left;
        padding: 3px;
        border: solid 1px #C7C7C7;
        margin-right: 10px;
        margin-bottom: 10px;
      }
      .gallery-fancybox a {
        display: table-cell;
        text-decoration: none;
      }
      .gallery-fancybox .gallery-thumb .gallery-title {
        margin: 0;
        padding: 2px 5px;
      }
      .gallery-fancybox .gallery-image {
        float: left;
        padding: 3px;
        border: solid 1px #C7C7C7;
        max-width: 100%;
        position:relative;
      }
      .gallery-fancybox .gallery-image #fancybox-left, .gallery-fancybox .gallery-image #fancybox-right {
        display: block;
      }
      .gallery-fancybox .gallery-image a#fancybox-close {
        display: block;
        position: relative;
        float: right;
      }
      .gallery-fancybox div.pagify {
        clear: both;
        text-align: left;
      }
      .gallery-fancybox div.pagify a {
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
      .gallery-fancybox div.pagify a.current {
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
      .gallery-<?php echo $id; ?>.gallery-fancybox a {
        <?php if ($th) echo 'height: '.$th.'px;'; ?> 
      }
    </style>
<?php
  }
}

function i18n_gallery_fancybox_content($gallery, $pic) {
  $id = i18n_gallery_id($gallery);
  if (i18n_gallery_is_show_image($pic)) {
    $item = i18n_gallery_item($gallery, $pic);
?>
    <div class="gallery gallery-fancybox gallery-<?php echo $id; ?>">
      <div class="gallery-image ">
        <a id="fancybox-close" href="<?php i18n_gallery_back_link(); ?>" title="<?php i18n_gallery_BACK(); ?>"></a>      
        <h2><?php echo htmlspecialchars(@$item['_title']); ?></h2>
        <img src="<?php i18n_gallery_image_link($gallery,$pic); ?>" alt="<?php echo htmlspecialchars(@$item['_title']); ?>"/>
        <?php if (@$item['_description']) echo '<p>'.htmlspecialchars(@$item['_description']).'</p>'; ?>
        <a id="fancybox-left" href="<?php i18n_gallery_prev_link($gallery,$pic); ?>" title="<?php i18n_gallery_PREV(); ?>"><span id="fancybox-left-ico" class="fancy-ico"></span></a>
        <a id="fancybox-right" href="<?php i18n_gallery_next_link($gallery,$pic); ?>" title="<?php i18n_gallery_NEXT(); ?>"><span id="fancybox-right-ico" class="fancy-ico"></span></a>
      </div>
    </div>
<?php
  } else { 
    $thumb = i18n_gallery_thumb($gallery);
    $showtitles = i18n_gallery_check($gallery, 'thumbtitles', false);
    $pageSize = !isset($thumb) && i18n_gallery_check($gallery,'pagify',false) ? (int) $gallery['pagesize'] : 0;
?>
    <div class="gallery gallery-fancybox gallery-<?php echo $id; ?>">
<?php 
    $i = 0;
    foreach ($gallery['items'] as $item) { 
      $text = @$item['_title'];
      if (!$text) $text = @$item['_description']; else if (@$item['_description']) $text .= ' - '.$item['_description'];
?>
      <div class="gallery-thumb" <?php if (isset($thumb) && $thumb != $i) echo 'style="display:none"'; ?>>
        <a href="<?php i18n_gallery_pic_link($gallery,$i); ?>" rel="fancybox-<?php echo $id; ?>" title="<?php echo htmlspecialchars($text); ?>">
          <img src="<?php i18n_gallery_thumb_link($gallery,$item); ?>" alt="<?php echo htmlspecialchars(@$item['_description']); ?>"/>
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
        var $sel = $("[rel=fancybox-<?php echo $id; ?>]")
        <?php i18n_gallery_replace_nojs_links($gallery, '$sel'); ?>
        $sel.fancybox({
<?php if ($pageSize >= 1) { ?>
          onStart: function(arr,index,opts) {
            var page = Math.floor(index/<?php echo $pageSize; ?>) + 1;
            $('.gallery-<?php echo $id; ?>').pagify('setPage', page);
          },
<?php } ?>          
          cyclic: true
<?php foreach ($gallery as $key => $value) if (substr($key,0,2) == 'x-') { ?>
          ,<?php echo substr($key,2); ?>: <?php echo preg_match('/^(null|true|false|\d+)$/',$value) ? $value : json_encode($value); ?>
<?php } ?>
        });
<?php if (i18n_gallery_is_goto_image($pic)) { ?>
        $("[rel=fancybox-<?php echo $id; ?>]:eq(<?php echo (int) $pic; ?>)").trigger('click');
<?php } ?>
<?php if ($pageSize >= 1) { ?>
        $('.gallery-<?php echo $id; ?>').pagify({ pageSize:<?php echo $pageSize; ?>, items:'.gallery-thumb' });
<?php } ?>
      });
    </script>
<?php
  }
}

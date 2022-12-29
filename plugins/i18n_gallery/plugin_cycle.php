<?php
i18n_gallery_register('cycle', 'cycle', 
  '<strong>cycle</strong> is a slideshow plugin that supports many different types of transition effects.<br/>'.
  'License: MIT and GPL<br/>'.
  '<a target="_blank" href="http://jquery.malsup.com/cycle/">http://jquery.malsup.com/cycle/</a>',
  'i18n_gallery_cycle_edit', 'i18n_gallery_cycle_header', 'i18n_gallery_cycle_content');

function i18n_gallery_cycle_edit($gallery) {
?>
  <p>
    <label for="cycle-width"><?php i18n('i18n_gallery/MAX_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="cycle-width" name="cycle-width" value="<?php echo @$gallery['width']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="cycle-height" name="cycle-height" value="<?php echo @$gallery['height']; ?>" style="width:5em"/>
    &nbsp;
    <span id="cycle-crop-span">
      <input type="checkbox" id="cycle-crop" name="cycle-crop" value="1" <?php echo @$gallery['crop'] ? 'checked="checked"' : ''; ?> style="vertical-align:middle"/> 
      <?php i18n('i18n_gallery/CROP'); ?>
    </span>
  </p>
  <script type="text/javascript">
    function changeCycleSize() {
        var show = $.trim($('#cycle-width').val()) != '' && $.trim($('#cycle-height').val()) != '';
        if (show) $('#cycle-crop-span').show(); else $('#cycle-crop-span').hide().find('input').attr('checked',false);
    }
    $(function() {
      $('#cycle-width, #cycle-height').change(changeCycleSize);
      changeCycleSize();
    });
  </script>
  <p>
    <label for="cycle-textpos"><?php i18n('i18n_gallery/TEXT_POSITION'); ?></label>
    <select class="text" name="cycle-textpos">
      <option value="left" <?php echo @$gallery['textpos'] == 'left' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/LEFT'); ?></option>
      <option value="right" <?php echo @$gallery['textpos'] == 'right' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/RIGHT'); ?></option>
      <option value="top" <?php echo @$gallery['textpos'] == 'top' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/TOP'); ?></option>
      <option value="bottom" <?php echo @$gallery['textpos'] == 'bottom' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/BOTTOM'); ?></option>
      <option value="overlay" <?php echo @$gallery['textpos'] == 'overlay' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/OVERLAY'); ?></option>
      <option value="" <?php echo @$gallery['textpos'] == '' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/NO_TEXT'); ?></option>
    </select>
  </p>
  <p>
    <label for="cycle-textwidth"><?php i18n('i18n_gallery/TEXT_WIDTH'); ?></label>
    <input type="text" class="text" id="cycle-textwidth" name="cycle-textwidth" value="<?php echo @$gallery['textwidth']; ?>" style="width:5em"/>
  </p>
  <p>
    <label for="cycle-interval"><?php i18n('i18n_gallery/INTERVAL'); ?></label>
    <input type="text" class="text" id="cycle-interval" name="cycle-interval" value="<?php echo @$gallery['interval']; ?>" style="width:5em"/>
  </p>
  <p>
    <label for="cycle-effect"><?php i18n('i18n_gallery/EFFECT'); ?></label>
    <select class="text" name="cycle-effect">
      <option value="scrollLeft" <?php echo @$gallery['effect'] == 'scrollLeft' ? 'selected="selected"' : ''; ?>>scrollLeft</option>
      <option value="scrollRight" <?php echo @$gallery['effect'] == 'scrollRight' ? 'selected="selected"' : ''; ?>>scrollRight</option>
      <option value="scrollUp" <?php echo @$gallery['effect'] == 'scrollUp' ? 'selected="selected"' : ''; ?>>scrollUp</option>
      <option value="scrollDown" <?php echo @$gallery['effect'] == 'scrollDown' ? 'selected="selected"' : ''; ?>>scrollDown</option>
      <option value="fade" <?php echo @$gallery['effect'] == 'fade' ? 'selected="selected"' : ''; ?>>fade</option>
      <option value="growX" <?php echo @$gallery['effect'] == 'growX' ? 'selected="selected"' : ''; ?>>growX</option>
      <option value="growY" <?php echo @$gallery['effect'] == 'growY' ? 'selected="selected"' : ''; ?>>growY</option>
      <option value="turnLeft" <?php echo @$gallery['effect'] == 'turnLeft' ? 'selected="selected"' : ''; ?>>turnLeft</option>
      <option value="turnRight" <?php echo @$gallery['effect'] == 'turnRight' ? 'selected="selected"' : ''; ?>>turnRight</option>
      <option value="turnUp" <?php echo @$gallery['effect'] == 'turnUp' ? 'selected="selected"' : ''; ?>>turnUp</option>
      <option value="turnDown" <?php echo @$gallery['effect'] == 'turnDown' ? 'selected="selected"' : ''; ?>>turnDown</option>
      <option value="uncover" <?php echo @$gallery['effect'] == 'uncover' ? 'selected="selected"' : ''; ?>>uncover</option>
      <option value="wipe" <?php echo @$gallery['effect'] == 'wipe' ? 'selected="selected"' : ''; ?>>wipe</option>
      <option value="none" <?php echo @$gallery['effect'] == 'none' ? 'selected="selected"' : ''; ?>>none</option>
    </select>
  <p>
  <p>
    <label for="cycle-navtype"><?php i18n('i18n_gallery/NAVIGATION_TYPE'); ?></label>
    <select class="text" name="cycle-navtype">
      <option value="dots" <?php echo @$gallery['navtype'] == 'dots' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/NAVIGATION_DOTS'); ?></option>
      <option value="numbers" <?php echo @$gallery['navtype'] == 'numbers' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/NAVIGATION_NUMBERS'); ?></option>
      <option value="images" <?php echo @$gallery['navtype'] == 'images' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/NAVIGATION_IMAGES'); ?></option>
      <option value="" <?php echo @$gallery['navtype'] == '' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/NAVIGATION_NONE'); ?></option>
    </select>
  </p>
<?php
}

function i18n_gallery_cycle_header($gallery) {
  global $SITEURL;
  if (i18n_gallery_check($gallery,'jquery') && i18n_gallery_needs_include('jquery.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery-1.11.2.min.js"></script>
<?php
  }
  if (i18n_gallery_check($gallery,'js') && i18n_gallery_needs_include('cycle.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery.cycle.all.min.js"></script>
<?php
  } 
  if (i18n_gallery_check($gallery,'css') && i18n_gallery_needs_include('cycle.css')) {
?> 
    <style type="text/css">
      .gallery-cycle {
        padding: 3px;
        border: solid 1px #C7C7C7;
        position: relative;
      }
      .gallery-cycle a {
        outline: none;
      }
      .gallery-cycle .gallery-container {
        margin: 0;
        padding: 0;
        border: 0 none;
        overflow: hidden;
        position: relative;
      }
      div.gallery-cycle .gallery-slide {
        margin: 0;
        padding: 0;
        border: 0 none;
        width: 100%;
        height: 100%;
      }
      .gallery-cycle .gallery-text {
        margin: 0;
        padding: 10px;
        border: 0 none;
        background-color: white;
        overflow: hidden;
      }
      .gallery-cycle .gallery-image {
        margin: 0;
        padding: 0;
        border: 0 none;
        display: table-cell;
        text-align: center;
        vertical-align: middle;
        background-color: white;
      }
      .gallery-cycle .gallery-image img {
        margin: 0;
        padding: 0;
        border: 0 none;
      }
      .gallery-cycle .gallery-control {
        position: absolute;
        margin: 0;
        padding: 5px 10px;
        border: 0 none;
        z-index: 10000;
      }
      .gallery-cycle .gallery-control-dots a {
        font-size: 40px;
        border: 0 none;
        text-decoration: none;
        color: #999999;
      }
      .gallery-cycle .gallery-control-numbers a {
        font-size: 18px;
        border: 0 none;
        text-decoration: none;cylce
        color: #999999;
        padding: 0px 5px;
        margin: 0px 2px;
        border: 1px solid #999999;
        background-color: white;
      }
      .gallery-cycle .gallery-control-images a {
        background: url(<?php echo $SITEURL; ?>plugins/i18n_gallery/images/cycle/pagination.png);
        width: 12px;
        height: 0px;
        overflow: hidden;
        padding-top: 12px;
        margin: 10px 2px;
        border: 0 none;
        display: block;
        float: left;
      }
      .gallery-cycle .gallery-control a:hover {
        color: #666666;
      }
      .gallery-cycle .gallery-control a.activeSlide {
        color: #C5400E;
        background-position: 0 -12px;
      }
      .gallery-cycle .prev {
        top: 0;
        left: 0;
        height: 100%;
        width: 35%;
        cursor: pointer;
        position: absolute;
        z-index: 9000;
      }
      .gallery-cycle .prev img {
        left: 10px;
        margin-top: -15px;
        position: absolute;
        top: 50%;
        display: none;
      }
      .gallery-cycle .next {
        top: 0;
        right: 0;
        height: 100%;
        width: 35%;
        cursor: pointer;
        position: absolute;
        z-index: 9000;
      }
      .gallery-cycle .next img {
        right: 10px;
        margin-top: -15px;
        position: absolute;
        top: 50%;
        display: none;
      }
      .gallery-cycle .prev:hover img, .gallery-cycle .next:hover img {
        display: block;
      }
    </style>
<?php
  }
  if (i18n_gallery_check($gallery,'css')) { 
    $id = i18n_gallery_id($gallery);
    $w = @$gallery['width'] ? $gallery['width'] : (@$gallery['height'] ? (int) $gallery['height']*$gallery['items'][0]['width']/$gallery['items'][0]['height'] : $gallery['items'][0]['width']);
    $h = @$gallery['height'] ? $gallery['height'] : (@$gallery['width'] ? (int) $gallery['width']*$gallery['items'][0]['height']/$gallery['items'][0]['width'] : $gallery['items'][0]['height']);
    $tw = @$gallery['textwidth'] ? intval($gallery['textwidth']) : $w / 3;
    $tp = @$gallery['textpos'];
?>
    <style type="text/css">
      #gallery-cycle-<?php echo $id; ?>.gallery-cycle {
        width: <?php echo $tp == 'left' || $tp == 'right' ? $w + $tw : $w; ?>px;
        height: <?php echo $tp == 'top' || $tp == 'bottom' ? $h + $tw : $h; ?>px;
      }
      #gallery-cycle-<?php echo $id; ?>.gallery-cycle .gallery-container {
        width: <?php echo $tp == 'left' || $tp == 'right' ? $w + $tw : $w; ?>px;
        height: <?php echo $tp == 'top' || $tp == 'bottom' ? $h + $tw : $h; ?>px;
        position: relative;
      }
      #gallery-cycle-<?php echo $id; ?>.gallery-cycle .gallery-text {
        <?php if ($tp == 'overlay') { ?>
          position:absolute;
          width: <?php echo $w; ?>px;
          bottom: 0;
          background: rgba(0,0,0,0.5);
          color: white;
        <?php } else { ?>
          width: <?php echo $tp == 'left' || $tp == 'right' ? $tw-20 : $w-20; ?>px;
          height: <?php echo $tp == 'top' || $tp == 'bottom' ? $tw-20 : $h-20; ?>px;
          <?php if ($tp == 'left') echo 'float: left;'; else if ($tp == 'right') echo 'float: right;'; ?>
        <?php } ?>
      }
      #gallery-cycle-<?php echo $id; ?>.gallery-cycle .gallery-image {
        width: <?php echo $w; ?>px;
        height: <?php echo $h; ?>px;
      }
      #gallery-cycle-<?php echo $id; ?>.gallery-cycle .gallery-control {
        <?php if ($tp == 'overlay') { ?>
          left: <?php echo ($w/2 - count($gallery['items'])*16/2 - 10 + 4); ?>px;
          text-align: center;
        <?php } else { ?>
          <?php echo $tp == 'left' ? 'left: 0;' : 'right: 0;'; ?>
          <?php echo $tp == 'top' ? 'top: 0;' : 'bottom:0;'; ?>
        <?php } ?>
      }
    </style>
<?php
  }
}

function i18n_gallery_cycle_content($gallery, $pic) {
  global $SITEURL;
  $id = i18n_gallery_id($gallery);
  $w = @$gallery['width'] ? $gallery['width'] : (@$gallery['height'] ? (int) $gallery['height']*$gallery['items'][0]['width']/$gallery['items'][0]['height'] : $gallery['items'][0]['width']);
  $h = @$gallery['height'] ? $gallery['height'] : (@$gallery['width'] ? (int) $gallery['width']*$gallery['items'][0]['height']/$gallery['items'][0]['width'] : $gallery['items'][0]['height']);
  // set gallery width/height for i18n_gallery_image_link:
  $gallery['width'] = $w;
  $gallery['height'] = $h;
  $tp = @$gallery['textpos'];
  if (!isset($pic) || $pic === null) $pic = 0; else if ($pic < 0) $pic = -$pic-1;
  $navtype = @$gallery['navtype'] ? $gallery['navtype'] : 'dots';
?>
  <div id="gallery-cycle-<?php echo $id; ?>" class="gallery gallery-cycle gallery-<?php echo $id; ?>">
    <div class="gallery-container">
<?php 
  $count = count($gallery['items']);
  for ($i=0; $i<$count; $i++) {
    $item = $gallery['items'][$i]; 
    $descr = @$item['_description'];
    if ($descr && !preg_match('/^(<p>|<p |<div>|<div ).*/', $descr)) $descr = '<p>'.$descr.'</p>';
?>
      <div class="gallery-slide" <?php if ($i != $pic) echo 'style="display:none"'; ?>>
        <?php if ($tp == 'bottom') { ?><div class="gallery-image"><img src="<?php i18n_gallery_image_link($gallery,$item); ?>" alt=""/></div><?php } ?>
        <?php if ($tp) { ?>
        <div class="gallery-text">
          <?php if (@$item['_title']) echo '<h2>'.htmlspecialchars($item['_title']).'</h2>'; ?>
          <?php echo $descr; ?>
        </div>
        <?php } ?>
        <?php if ($tp != 'bottom') { ?><div class="gallery-image"><img src="<?php i18n_gallery_image_link($gallery,$item); ?>" alt=""/></div><?php } ?>
      </div>
<?php
  } 
?>
      <a class="prev" href="<?php i18n_gallery_pic_link($gallery,$pic>0 ? $pic-1 : $count-1); ?>"><img src="<?php echo $SITEURL; ?>plugins/i18n_gallery/images/cycle/prev.png" alt=""/></a>
      <a class="next" href="<?php i18n_gallery_pic_link($gallery,$pic<$count-1 ? $pic+1 : 0); ?>"><img src="<?php echo $SITEURL; ?>plugins/i18n_gallery/images/cycle/next.png" alt=""/></a>
    </div>
<?php if (@$gallery['navtype']) { ?>
    <div class="gallery-control gallery-control-<?php echo $navtype; ?>">
      <?php for ($i=0; $i<count($gallery['items']); $i++) echo '<a href="'.i18n_gallery_pic_link($gallery,$i,false).'"'.($pic==$i ? ' class="activeSlide"' : '').'>'.($navtype == 'numbers' ? ($i+1) : '&#149;').'</a>'; ?>
    </div>
<?php } ?>
  </div>
  <script type="text/javascript">
    $(document).ready(function(){
<?php if ($tp == 'overlay') { ?>
      // set text positions
      $('.gallery-cycle.gallery-<?php echo $id; ?> .gallery-slide:hidden').each(function(i,elem) {
        $elem = $(elem);
        $text = $elem.find('.gallery-text');
        $elem.css('left','-10000px').show();
        $text.css('bottom',-$text.outerHeight()+'px');
        $(elem).hide().css('left','0');
      });
<?php } ?>
      $('.gallery-cycle.gallery-<?php echo $id; ?> .gallery-control').empty();
		  $('.gallery-cycle.gallery-<?php echo $id; ?> .gallery-container').cycle({ 
        slideExpr: '.gallery-slide',
		    fx: <?php echo @$gallery['effect'] ? json_encode($gallery['effect']) : 'scrollLeft'; ?>, 
		    speed: 1000,
		    timeout: <?php echo @$gallery['interval'] ? intval(@$gallery['interval']) : 5000; ?>,
		    pause: 1,
        prev: $('.gallery-cycle.gallery-<?php echo $id; ?> .gallery-container .prev').get(),
        next: $('.gallery-cycle.gallery-<?php echo $id; ?> .gallery-container .next').get(),
<?php if ($tp == 'overlay') { ?>
        before: function(currSlideElem,nextSlideElem,options,forwardFlag) {
          $text = $(currSlideElem).find('.gallery-text');
          $text.animate({	bottom:-$text.outerHeight() },100);
        },
        after: function(currSlideElem,nextSlideElem,options,forwardFlag) {
          $text = $(nextSlideElem).find('.gallery-text');
          $text.animate({	bottom:0 },200);
        },
<?php } ?>
<?php foreach ($gallery as $key => $value) if (substr($key,0,2) == 'x-') { ?>
        <?php echo substr($key,2); ?>: <?php echo preg_match('/^(null|true|false|\d+)$/',$value) ? $value : json_encode($value); ?>,
<?php } ?>
		    pager:  '.gallery-cycle.gallery-<?php echo $id; ?> .gallery-control',
		    pagerAnchorBuilder: function(idx, slide) { return '<a href="#">'+<?php echo $navtype == 'numbers' ? '(idx+1)' : "'&#149;'"; ?>+'</a>'; } 
		  });
    });
  </script>
<?php
}

<?php
i18n_gallery_register('s3slider', 's3Slider', 
  '<strong>s3Slider</strong> displays slideshows, consisting of images along with titles and descriptions directly on the page. All images must have the same size.<br/>'.
  'License: Creative Commons Attribution 2.5<br/>'.
  '<a target="_blank" href="http://www.serie3.info/s3slider/">http://www.serie3.info/s3slider/</a>',
  'i18n_gallery_s3slider_edit', 'i18n_gallery_s3slider_header', 'i18n_gallery_s3slider_content');

function i18n_gallery_s3slider_edit($gallery) {
?>
  <p>
    <label for="s3slider-width"><?php i18n('i18n_gallery/MAX_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="s3slider-width" name="s3slider-width" value="<?php echo @$gallery['width']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="s3slider-height" name="s3slider-height" value="<?php echo @$gallery['height']; ?>" style="width:5em"/>
    <input type="hidden" name="s3slider-crop" value="1"/>
  </p>
  <p>
    <label for="s3slider-textpos"><?php i18n('i18n_gallery/TEXT_POSITION'); ?></label>
    <select class="text" name="s3slider-textpos">
      <option value="top" <?php echo @$gallery['textpos'] == 'top' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/TOP'); ?></option>
      <option value="bottom" <?php echo @$gallery['textpos'] == 'bottom' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/BOTTOM'); ?></option>
      <option value="left" <?php echo @$gallery['textpos'] == 'left' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/LEFT'); ?></option>
      <option value="right" <?php echo @$gallery['textpos'] == 'right' ? 'selected="selected"' : ''; ?>><?php i18n('i18n_gallery/RIGHT'); ?></option>
    </select>
  </p>
  <p>
    <label for="s3slider-interval"><?php i18n('i18n_gallery/INTERVAL'); ?></label>
    <input type="text" class="text" id="s3slider-interval" name="s3slider-interval" value="<?php echo @$gallery['interval']; ?>" style="width:5em"/>
  </p>
<?php
}

function i18n_gallery_s3slider_header($gallery) {
  $id = 's3slider-'.i18n_gallery_id($gallery);
  $w = @$gallery['width'] ? $gallery['width'] : (@$gallery['height'] ? (int) $gallery['height']*$gallery['items'][0]['width']/$gallery['items'][0]['height'] : $gallery['items'][0]['width']);
  $h = @$gallery['height'] ? $gallery['height'] : (@$gallery['width'] ? (int) $gallery['width']*$gallery['items'][0]['height']/$gallery['items'][0]['width'] : $gallery['items'][0]['height']);
  if (i18n_gallery_check($gallery,'jquery') && i18n_gallery_needs_include('jquery.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery-1.11.2.min.js"></script>
<?php
  }
  if (i18n_gallery_check($gallery,'js') && i18n_gallery_needs_include('s3slider.js')) {
    // packed version of js does not work!
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/s3Slider.js"></script>
<?php
  } 
  if (i18n_gallery_check($gallery,'css')) { 
?>
    <style type="text/css">
      #<?php echo $id; ?> {
        width: <?php echo $w; ?>px; /* important to be same as image width */
        height: <?php echo $h; ?>px; /* important to be same as image height */
        position: relative; /* important */
        overflow: hidden; /* important */
        padding: 0;
        border: 0 none;
      }
      #<?php echo $id; ?> #<?php echo $id; ?>Content {
        width: <?php echo $w; ?>px;
        height: <?php echo $h; ?>px;
        position: absolute;
	      top: 0;
	      margin-left: 0;
        list-style: none;
        padding: 0;
        margin: 0;
      }
      #<?php echo $id; ?> .<?php echo $id; ?>Image {
        float: left;
        margin: 0 !important;
        padding: 0 !important;
        position: relative;
	      display: none;
        background-image: none !important;
      }
      #<?php echo $id; ?> .<?php echo $id; ?>Image span {
        position: absolute;
	      font: 10px/15px Arial, Helvetica, sans-serif;
        padding: 10px 13px;
        background-color: #000;
        filter: alpha(opacity=70);
        -moz-opacity: 0.7;
	      -khtml-opacity: 0.7;
        opacity: 0.7;
        color: #fff;
        display: none;
        word-wrap: break-word;
<?php if (@$gallery['textpos'] == 'top') { ?>
	      top: 0;
	      left: 0;
        width: <?php echo $w-2*13; ?>px;
<?php } else if (@$gallery['textpos'] == 'left') { ?>
	      top: 0;
        left: 0;
	      width: <?php echo (int) min(110, $w/4); ?>px;
	      height: <?php echo $h-2*10; ?>px;
<?php } else if (@$gallery['textpos'] == 'right') { ?>
	      right: 0;
	      bottom: 0;
	      width: <?php echo (int) min(110, $w/4); ?>px;
	      height: <?php echo $h-10; ?>px;
<?php } else { ?>
	      bottom: 0;
        left: 0;
        width: <?php echo $w-2*13; ?>px;
<?php } ?>
      }
      .<?php echo $id; ?>Image span strong {
          font-size: 14px;
      }
      .clear {
	      clear: both;
      }
    </style>
<?php
  }
}

function i18n_gallery_s3slider_content($gallery) {
  $id = 's3slider-'.i18n_gallery_id($gallery);
?>
  <div id="<?php echo $id; ?>">
     <ul id="<?php echo $id; ?>Content">
<?php 
  foreach ($gallery['items'] as $item) { 
?>
        <li class="<?php echo $id; ?>Image">
            <img src="<?php i18n_gallery_image_link($gallery,$item); ?>" />
            <span><strong><?php echo htmlspecialchars(@$item['_title']); ?></strong><br/><?php echo htmlspecialchars(@$item['_description']); ?></span>
        </li>
<?php 
  } 
?>
        <div class="clear <?php echo $id; ?>Image"></div>
     </ul>
  </div> 
  <script type="text/javascript">
    $(document).ready(function() {
       $('#<?php echo $id; ?>').s3Slider({
<?php echo (intval(@$gallery['interval'])) ? "timeOut: ".intval(@$gallery['interval']) : "timeout: 5000"; ?>
       });
    }); 
  </script>
<?php
}

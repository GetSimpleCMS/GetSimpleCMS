<?php
i18n_gallery_register('supersized', 'Supersized', 
  '<strong>Supersized</strong> resizes images to fill browser while maintaining image dimension ratio and cycles them via slideshow with transitions and preloading.<br/>'.
  'You need to use an empty template without styles, navigation, etc. for a page displaying this slide show.<br/>'.
  'License: MIT and GPL<br/>'.
  '<a target="_blank" href="http://www.buildinternet.com/project/supersized/">http://www.buildinternet.com/project/supersized/</a>',
  'i18n_gallery_supersized_edit', 'i18n_gallery_supersized_header', 'i18n_gallery_supersized_content');

function i18n_gallery_supersized_edit($gallery) {
?>
  <p>
    <label for="supersized-width"><?php i18n('i18n_gallery/MAX_DIMENSIONS'); ?></label>
    <input type="text" class="text" id="supersized-width" name="supersized-width" value="<?php echo @$gallery['width']; ?>" style="width:5em"/>
    x
    <input type="text" class="text" id="supersized-height" name="supersized-height" value="<?php echo @$gallery['height']; ?>" style="width:5em"/>
  </p>
  <p>
    <label for="supersized-interval"><?php i18n('i18n_gallery/INTERVAL'); ?></label>
    <input type="text" class="text" id="supersized-interval" name="supersized-interval" value="<?php echo @$gallery['interval']; ?>" style="width:5em"/>
  </p>
<?php
}

function i18n_gallery_supersized_header($gallery) {
  if (i18n_gallery_check($gallery,'css') && i18n_gallery_needs_include('supersized.css')) { 
?>
    <link rel="stylesheet" href="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/css/supersized.css" type="text/css" media="screen" charset="utf-8" />
    <link rel="stylesheet" href="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/css/supersized.shutter.css" type="text/css" media="screen" charset="utf-8" />
<?php
  }
  if (i18n_gallery_check($gallery,'jquery') && i18n_gallery_needs_include('jquery.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery-1.11.2.min.js"></script>
<?php
  }
  if (i18n_gallery_check($gallery,'js') && i18n_gallery_needs_include('supersized.js')) {
?>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/jquery.easing.min.js"></script>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/supersized.3.2.6.min.js"></script>
    <script type="text/javascript" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/js/supersized.shutter.min.js"></script>
<?php
  }
}

function i18n_gallery_supersized_content($gallery, $pic) {
  $interval = intval(@$gallery['interval']) ? intval($gallery['interval']) : 5000;
?>
	<!--Thumbnail Navigation-->
	<div id="prevthumb"></div>
	<div id="nextthumb"></div>
	
	<!--Arrow Navigation-->

	<a id="prevslide" class="load-item"></a>
	<a id="nextslide" class="load-item"></a>

	<div id="thumb-tray" class="load-item">
		<div id="thumb-back"></div>
		<div id="thumb-forward"></div>
	</div>
	
	<!--Time Bar-->
	<div id="progress-back" class="load-item">
		<div id="progress-bar"></div>

	</div>
	
	<!--Control Bar-->
	<div id="controls-wrapper" class="load-item">
		<div id="controls">
			
			<a id="play-button"><img id="pauseplay" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/images/supersized/pause.png"/></a>
		
			<!--Slide counter-->
			<div id="slidecounter">
				<span class="slidenumber"></span> / <span class="totalslides"></span>

			</div>
			
			<!--Slide captions displayed here-->
			<div id="slidecaption"></div>
			
			<!--Thumb Tray button-->
			<a id="tray-button"><img id="tray-arrow" src="<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/images/supersized/button-tray-up.png"/></a>
			
			<!--Navigation-->
			<ul id="slide-list"></ul>
			
		</div>
	</div>

	<script type="text/javascript">  
		jQuery(function($) {
      $.supersized.themeVars.image_path = '<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/images/supersized/';
			$.supersized({
				//Functionality
				slideshow        :    1, //Slideshow on/off
				autoplay         :    <?php echo (@$pic != null) ? 0 : 1; ?>, //Slideshow starts playing automatically
				start_slide      :    <?php echo (@$pic != null) ? intval($pic)+1 : 1; ?>, //Start slide (0 is random)
				random				 	 :    0, //Randomize slide order (Ignores start slide)
				slide_interval   : <?php echo intval($interval); ?>, //Length between transitions
				transition       :    1, //0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
				transition_speed :	500, //Speed of transition
				new_window       :    1, //Image links open in new window/tab
				pause_hover      :    0, //Pause slideshow on hover
				keyboard_nav     :    1, //Keyboard navigation on/off
				performance      :    1, //0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)
				image_protect    :    1, //Disables image dragging and right click with Javascript
				image_path       : '<?php echo i18n_gallery_site_link(); ?>plugins/i18n_gallery/images/supersized/', //Default image path

				//Size & Position
				min_width        :    0, //Min width allowed (in pixels)
				min_height       :    0, //Min height allowed (in pixels)
				vertical_center  :    1, //Vertically center background
				horizontal_center:    1, //Horizontally center background
        fit_always       :    1,
				fit_portrait     :    1, //Portrait images will not exceed browser height
				fit_landscape    :    1, //Landscape images will not exceed browser width
				
				//Components
				navigation              :   1, //Slideshow controls on/off
				thumbnail_navigation    :   1, //Thumbnail navigation
				slide_counter           :   1, //Display slide numbers
				slide_captions          :   1, //Slide caption (Pull from "title" in slides array)
		    slide_links				      :	'blank',	// Individual links for each slide (Options: false, 'number', 'name', 'blank')
				slides : [ //Slideshow Images
<?php 
$first = true;
foreach ($gallery['items'] as $item) { 
?>
          <?php if (!$first) echo ', '; ?>{ image: <?php echo json_encode(i18n_gallery_image_link($gallery,$item,false)); ?>, title: <?php echo json_encode(@$item["_title"]); ?>, thumb:<?php echo json_encode(i18n_gallery_thumb_link($gallery,$item,false)); ?>, url: <?php echo json_encode(i18n_gallery_site_link()); ?> }
<?php 
  $first = false;
} 
?>
				]
<?php foreach ($gallery as $key => $value) if (substr($key,0,2) == 'x-') { ?>
          ,<?php echo substr($key,2); ?>: <?php echo preg_match('/^(null|true|false|\d+)$/',$value) ? $value : json_encode($value); ?>
<?php } ?>
      }); 
    });
	</script>
<?php
}

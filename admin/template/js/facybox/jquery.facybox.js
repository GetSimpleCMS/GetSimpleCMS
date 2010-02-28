/*
 * facybox (for jQuery)
 * version: 1.2.1 (11/09/2009)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://bitbonsai.com/facybox/
 *
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2009 Mauricio Wolff [ chris@ozmm.org ]
 *
 * Usage:
 *
 *  jQuery(document).ready(function() {
 *    jQuery('a[rel*=facybox]').facybox()
 *  })
 *
 *  <a href="#terms" rel="facybox">Terms</a>
 *    Loads the #terms div in the box
 *
 *  <a href="terms.html" rel="facybox">Terms</a>
 *    Loads the terms.html page in the box
 *
 *  <a href="terms.png" rel="facybox">Terms</a>
 *    Loads the terms.png image in the box
 *
 *
 *  You can also use it programmatically:
 *
 *    jQuery.facybox('some html')
 *    jQuery.facybox('some html', 'my-groovy-style')
 *
 *  The above will open a facybox with "some html" as the content.
 *
 *    jQuery.facybox(function($) {
 *      $.get('blah.html', function(data) { $.facybox(data) })
 *    })
 *
 *  The above will show a loading screen before the passed function is called,
 *  allowing for a better ajaxy experience.
 *
 *  The facybox function can also display an ajax page, an image, or the contents of a div:
 *
 *    jQuery.facybox({ ajax: 'remote.html' })
 *    jQuery.facybox({ ajax: 'remote.html' }, 'my-groovy-style')
 *    jQuery.facybox({ image: 'stairs.jpg' })
 *    jQuery.facybox({ images: ['stairs.jpg','ballon.jpg'] })
 *    jQuery.facybox({ images: ['stairs.jpg','ballon.jpg'], initial:'ballon.jpg'})
 *    jQuery.facybox({ image: 'stairs.jpg' }, 'my-groovy-style')
 *    jQuery.facybox({ div: '#box' })
 *    jQuery.facybox({ div: '#box' }, 'my-groovy-style')
 *
 *  Want to close the facybox?  Trigger the 'close.facybox' document event:
 *
 *    jQuery(document).trigger('close.facybox')
 *
 *  facybox also has a bunch of other hooks:
 *
 *    loading.facybox
 *    beforeReveal.facybox
 *    reveal.facybox (aliased as 'afterReveal.facybox')
 *    init.facybox
 *
 *  Simply bind a function to any of these hooks:
 *
 *   $(document).bind('reveal.facybox', function() { ...stuff to do after the facybox and contents are revealed... })
 *
 */
;(function($) {
	
	$.fn.fixPNG = function() {
		return this.each(function () {
			var image = $(this).css('backgroundImage');

			if (image.match(/^url\(["']?(.*\.png)["']?\)$/i)) {
				image = RegExp.$1;
				$(this).css({
					'backgroundImage': 'none',
					'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true, sizingMethod=" + ($(this).css('backgroundRepeat') == 'no-repeat' ? 'crop' : 'scale') + ", src='" + image + "')"
				}).each(function () {
					var position = $(this).css('position');
					if (position != 'absolute' && position != 'relative')
						$(this).css('position', 'relative');
				});
			}
		});
	};
	
  //TODO refactor using data.content_klass
  $.facybox = function(data, klass) {
    $.facybox.loading();
    $.facybox.content_klass = klass;
    if (data.ajax) revealAjax(data.ajax);
    else if(data.image) revealImage(data.image);
    else if(data.images) revealGallery(data.images,data.initial);
    else if(data.div) revealHref(data.div);
    else if($.isFunction(data)) data.call($);
    else $.facybox.reveal(data);
  }

  /*
   * Public, $.facybox methods
   */

  $.extend($.facybox, {
    //possible option: noAutoload --- will build facybox only when it is needed
    settings: {
		opacity      : 0.3,
		overlay      : true,
		modal        : false,
		imageTypes   : [ 'png', 'jpg', 'jpeg', 'gif' ],
		imageMimeTypes  : [ 'image/jpeg', 'image/png', 'image/gif' ]
    },

    html : function(){
      return '\
		<div id="facybox" style="display:none;"> \
			<div class="popup"> \
				<table> \
					<tbody> \
						<tr> \
							<td class="nw"/><td class="n" /><td class="ne"/> \
						</tr> \
						<tr> \
							<td class="w" /> \
							<td class="body"> \
							<div class="footer"> </div> \
							<a href="#" class="close"></a>\
							<div class="content"> \
							</div> \
						</td> \
							<td class="e"/> \
						</tr> \
						<tr> \
							<td class="sw"/><td class="s"/><td class="se"/> \
						</tr> \
					</tbody> \
				</table> \
			</div> \
		</div> \
		<div class="loading"></div> \
	'
    },

    loading: function(){
      init();
      if($('.loading',$('#facybox'))[0]) return;//already in loading state...
      showOverlay();
      $.facybox.wait();
      if (!$.facybox.settings.modal) {
        $(document).bind('keydown.facybox', function(e) {
          if(e.keyCode == 27) $.facybox.close();//ESC
        });
      }
      $(document).trigger('loading.facybox');
    },

    wait: function(){
      var $f = $('#facybox');
      $('.content',$f).empty();//clear out old content
      $('.body',$f).children().hide().end().append('<div class="loading"></div>');
      $f.fadeIn('fast');
	  $.facybox.centralize();
      $(document).trigger('reveal.facybox').trigger('afterReveal.facybox');
    },

    centralize: function(){
		
		var $f = $('#facybox');
		var pos = $.facybox.getViewport();
		var wl = parseInt(pos[0]/2) - parseInt($f.find("table").width() / 2);
		var fh = parseInt($f.height());

		if(pos[1] > fh){
			var t = (pos[3] + (pos[1] - fh)/2);
			$f.css({ 'left': wl, 'top': t });
			// console.log('height smaller then window: '+fh, pos[1], pos[3])
		} else {
			var t = (pos[3] + (pos[1] /10));
			$f.css({ 'left': wl, 'top': t });
			// console.log('height bigger then window')
		}
    },

	getViewport: function() {
		//  [1009, 426, 0, 704]
		return [$(window).width(), $(window).height(), $(window).scrollLeft(), $(window).scrollTop()];
	},

    reveal: function(content){
	$(document).trigger('beforeReveal.facybox');
	var $f = $('#facybox');
	$('.content',$f)
		.attr('class',($.facybox.content_klass||'')+' content') //do not simply add the new class, since on the next call the old classes would remain
		.html(content);
	$('.loading',$f).remove();
	
	var $body 	= $('.body',$f);

	$body.children().fadeIn('fast');
	
	$.facybox.centralize();

	$(document).trigger('reveal.facybox').trigger('afterReveal.facybox');
    },

    close: function(){
      $(document).trigger('close.facybox');
      return false;
    }
  })

  /*
   * Bind to links, on click they open a facybox which
   * contains what their href points to
   */
  $.fn.facybox = function(settings) {
    var $this = $(this);
    if(!$this[0]) return $this;//called on empty elements, just stop and continue chain
    if(settings)$.extend($.facybox.settings, settings);
    if(!$.facybox.settings.noAutoload) init();

    $this.bind('click.facybox',function(){
      $.facybox.loading();
      // support for rel="facybox.inline_popup" syntax, to add a class
      // also supports deprecated "facybox[.inline_popup]" syntax
      var klass = this.rel.match(/facybox\[?\.(\w+)\]?/);
      $.facybox.content_klass = klass ? klass[1] : '';
      revealHref(this.href);
      return false;
    });
    return $this;//continue chain
  }

  /*
   * Private methods
   */
  // called one time to setup facybox on this page
  function init() {
    if($.facybox.settings.inited) return;
    else $.facybox.settings.inited = true;

    $(document).trigger('init.facybox');
    makeBackwardsCompatible();

    var imageTypes = $.facybox.settings.imageTypes.join('|');
    $.facybox.settings.imageTypesRegexp = new RegExp('\.(' + imageTypes + ')', 'i');

    $('body').append($.facybox.html());//insert facybox to dom


	// ie hacks
	var $f = $("#facybox");
	
	// it amazes me that this is still better than native png32 support in ie8...
	if($.browser.msie){
		$(".n, .s, .w, .e, .nw, .ne, .sw, .se", $f).fixPNG();
		// ie6
		if(parseInt($.browser.version) <= 6){
			var css = "<style type='text/css' media='screen'>* html #facybox_overlay { position: absolute; height: expression(document.body.scrollHeight > document.body.offsetHeight ? document.body.scrollHeight : document.body.offsetHeight + 'px');}</style>"
			$('head').append(css);
			$(".close", $f).fixPNG();
			$(".close",$f).css({
				'right': '15px'
			});
		}
		$(".w, .e",$f).css({
			width: '13px',
			'font-size': '0'
		}).text("&nbsp;");
	}

    //if we did not autoload, so the user has just clicked the facybox and pre-loading is useless
    if(! $.facybox.settings.noAutoload){
		preloadImages();
	}
        $('#facybox .close').click($.facybox.close);
  }

  //preloads all the static facybox images
  function preloadImages(){
    //TODO preload prev/next ?
    $('#facybox').find('.n, .close , .s, .w, .e, .nw, ne, sw, se').each(function() {
		var img = new Image();
		img.src = $(this).css('background-image').replace(/url\((.+)\)/, '$1');
    })
	// var img = new Image();
	// img.src = 'images/loading.gif';
	/*
		TODO: remove and load preloader from filament group
	*/
  }

  function makeBackwardsCompatible() {
    var $s = $.facybox.settings;
    $s.imageTypes = $s.image_types || $s.imageTypes;
    $s.facyboxHtml = $s.facybox_html || $s.facyboxHtml;
  }

  // Figures out what you want to display and displays it
  // formats are:
  //     div: #id
  //   image: blah.extension
  //    ajax: anything else
  function revealHref(href) {
    // div
    if(href.match(/#/)) {
      var url    = window.location.href.split('#')[0];
      var target = href.replace(url,'');
      if (target == '#') return
      $.facybox.reveal($(target).html(), $.facybox.content_klass);
    // image
    } else if(href.match($.facybox.settings.imageTypesRegexp)) {
      revealImage(href);
    // ajax
    } else { revealAjax(href)}
  }

  function revealGallery(hrefs, initial) {
    //initial position
    var position = $.inArray(initial||0,hrefs);
    if(position ==-1){
		position = 0;
	}

    //build navigation and ensure it will be removed
	var $footer  = $('#facybox div.footer');
	
    $footer.append($('<div class="navigation"><a class="prev"/><a class="next"/><div class="counter"></div></div>'));
    var $nav = $('#facybox .navigation');

    $(document).bind('afterClose.facybox',function(){$nav.remove()});

    function change_image(diff){
      position = (position + diff + hrefs.length) % hrefs.length;
      revealImage(hrefs[position]);
      $nav.find('.counter').html(position +1+" / "+hrefs.length);
    }
    change_image(0);

    //bind events
    $('.prev',$nav).click(function(){change_image(-1)});
    $('.next',$nav).click(function(){change_image(1)});
    $(document).bind('keydown.facybox', function(e) {
      if(e.keyCode == 39)change_image(1);  // right
      if(e.keyCode == 37)change_image(-1); // left
    });
  }

  function revealImage(href){
	
	var $f = $("#facybox");
	
    $('#facybox .content').empty();
    $.facybox.loading();//TODO loading must be shown until image is loaded -> stopLoading() on onload
    var image = new Image();
    image.onload = function() {
		$.facybox.reveal('<div class="image"><img src="' + image.src + '" /></div>', $.facybox.content_klass);
		
		var $footer  	= $("div.footer",$f);
		var $content 	= $("div.content",$f);
		var $navigation	= $("div.navigation",$f);
		var $next		= $("a.next",$f);
		var $prev		= $("a.prev",$f);
		var $counter	= $("div.counter",$f);
		
		var size = [$content.width(), $content.height()];
		
		$footer.width(size[0]).height(size[1]);
		$navigation.width(size[0]).height(size[1]);
		$next.width(parseInt(size[0]/2)).height(size[1]).css({ left: (size[0]/2) });
		$prev.width(size[0]/2).height(size[1]);
		$counter.width(parseInt($f.width() -26)).css({'opacity' : 0.5, '-moz-border-radius' : '8px', '-webkit-border-radius' : '8px'})
		
    }
    image.src = href;
  }

  function revealAjax(href) {
    // $.get(href, function(data) { $.facybox.reveal(data) });
	$.ajax({
	  type: "GET",
	  url: href,
	  complete: function(XMLHttpRequest, textStatus) {
	    content_type = XMLHttpRequest.getResponseHeader("Content-Type");
	    if ( jQuery.inArray(content_type, $.facybox.settings.imageMimeTypes) >= 0 ) {
	      revealImage(href)
	  $.facybox.centralize();
	    } else {
	      $.facybox.reveal( XMLHttpRequest.responseText );
	    }
	  }
	});
  }

  function skipOverlay() {
    return $.facybox.settings.overlay == false || $.facybox.settings.opacity === null
  }

  function showOverlay() {
    if(skipOverlay()) return;

    if($('#facybox_overlay').length == 0){
      $("body").append('<div id="facybox_overlay" class="facybox_hide"></div>');
    }

    $('#facybox_overlay').hide().addClass("facybox_overlayBG")
      .css('opacity', $.facybox.settings.opacity)
      .fadeIn(200);
    if(!$.facybox.settings.modal){
      $('#facybox_overlay').click(function(){ $(document).trigger('close.facybox')})
    }
  }

  function hideOverlay() {
    if(skipOverlay()) return;

    $('#facybox_overlay').fadeOut(200, function(){
      $("#facybox_overlay").removeClass("facybox_overlayBG").
        addClass("facybox_hide").
        remove();
    })
  }

  /*
   * Bindings
   */

  $(document).bind('close.facybox', function() {
    $(document).unbind('keydown.facybox');

	// ie hacks
	var $f = $("#facybox");
	if($.browser.msie){
		$('#facybox').hide();
		hideOverlay();
		$('#facybox .loading').remove();
	} else {
		$('#facybox').fadeOut('fast',function() {
	      $('#facybox .content').removeClass().addClass('content');//revert changing class
	      hideOverlay();
	      $('#facybox .loading').remove();
	    })
	}

    $(document).trigger('afterClose.facybox');
  });

})(jQuery);
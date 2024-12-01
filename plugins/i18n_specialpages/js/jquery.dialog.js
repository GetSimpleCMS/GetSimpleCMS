(function($) {
	
	var $openDialog = null;
	var $background = null;
	
	var defaultOptions = {
 		   autoOpen:true, 
		   opacity:'0.3'		   
	};	
	
	function initialize() {
        if (!$background) {
     	   $background = $('<div />').css({
     		   'display':'none', 'position':'fixed', '_position':'absolute', 
     		   'left':'0', 'top':'0', 'width':'100%', 'height':'100%',
     		   'background':'#000000', 'margin':'0', 'padding':'0', 'border':'0 none',
     		   'z-index':'9000'
     	   }).appendTo('body').click(function(e) {
	    	   if ($openDialog) methods.close.apply($openDialog)
	       });
        }
	}
	
	var methods = {
		init: function(options) {
			var opts = $.extend(defaultOptions, options);
	        this.each(function() {
	           var $this = $(this)
	           var data = $this.data('dialog');
	           if (!data) {
	        	   $this.data('dialog', opts);
	        	   data = $this.data('dialog');
	           }
	        });
	        if (opts.autoOpen) methods.open.apply(this);
	        return this;
		},
		open: function() {
			if ($openDialog) methods.close.apply($openDialog);
			return this.first().each(function() {
		       var $this = $(this)
		       var data = $this.data('dialog');
		       if (!data) data = defaultOptions;
		       $background.css('opacity',data.opacity).fadeIn(500);
		       $this.css('position','absolute').css('z-index','9900')
		       		.css('top', ($(window).height()/2-$this.height()/2)+'px')
		       		.css('left', ($(window).width()/2-$this.width()/2)+'px')
		       		.fadeIn(500);
		       $openDialog = $this;
		       $(window).bind('keypress.dialog', function(e) {
		    	   if (e.keyCode == 27) methods.close.apply($openDialog);
		       });
			});
		},
		close: function() {
			$(window).unbind('.dialog');
			this.hide();
			if ($openDialog && $openDialog.filter(':visible').size() <= 0) {
				$background.hide();
				$openDialog = null;
			}
			return this;
		}
	}
	
	$.fn.dialog = function(method) {
		initialize();
	    if (methods[method]) {
		    return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	    } else if (typeof method === 'object' || !method) {
		    return methods.init.apply(this, arguments);
		} else {
		    $.error( 'Method ' +  method + ' does not exist on jQuery.dialog' );
		}    
	};
	
})(jQuery);

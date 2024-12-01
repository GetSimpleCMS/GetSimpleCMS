(function($) {
	
	var $openedDialog = null;
	var $background = null;
	
	var defaultOptions = {
 		autoOpen:true, 
		opacity:'0.3',
		onClose : $.noop
	};	
	
	function initialize() {
        if (!$background) {
     	   $background = $('<div />').css({
     		   'display':'none', 'position':'fixed', '_position':'absolute', 
     		   'left':'0', 'top':'0', 'width':'100%', 'height':'100%',
     		   'background':'#000000', 'margin':'0', 'padding':'0', 'border':'0 none',
     		   'z-index':'9000'
     	   }).appendTo('body').click(function(e) {
	    	   if ($openedDialog) methods.close.apply($openedDialog)
	       });
        }
	}
	
	var methods = {
		init: function(options) {
			var opts = $.extend(defaultOptions, options);
	        this.each(function() {
	           var $this = $(this)
	           var data = $this.data('egDialog');
	           if (!data) {
	        	   $this.data('egDialog', opts);
	        	   data = $this.data('egDialog');
	           }
	        });
	        if (opts.autoOpen) methods.open.apply(this);
	        return this;
		},
		open: function() {
			if ($openedDialog) methods.close.apply($openedDialog);
			return this.first().each(function() {
		       var $this = $(this)
		       var data = $this.data('egDialog');
		       if (!data) data = defaultOptions;
		       $background.css( 'opacity', data.opacity ).fadeIn(500);
			   $this.css('position' , 'fixed') //firstly set position
		       .css({
					'z-index' : '9900',
                    top : '50%',
                    left : '50%',
                    marginTop : -($this.outerHeight() / 2) + 'px',
                    marginLeft : -($this.outerWidth() / 2) + 'px'
			   })
               .fadeIn();
		       		
		       $openedDialog = $this;
		       $(window).on('keydown.egDialog', function(e) {
		    	   if (e.keyCode == 27) methods.close.apply($openedDialog);
		       });		

			});
		},
		close: function() {
			$(window).off('.egDialog');
			this
				.hide()
				.css('position', '');
			if ($openedDialog) {
				$background.hide();
				$openedDialog = null;
			}
			this.data('egDialog').onClose();
			this.data('egDialog', null);
			return this;
		}
	}
	
	$.fn.egDialog = function(method) {
		initialize();
	    if (methods[method]) {
		    return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	    } else if (typeof method === 'object' || !method) {
		    return methods.init.apply(this, arguments);
		} else {
		    $.error( 'Method ' +  method + ' does not exist on jQuery.egDialog' );
		}    
	};
	
})(jQuery);

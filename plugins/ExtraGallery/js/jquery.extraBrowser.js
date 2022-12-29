/*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 3.1.12
 *
 * Requires: jQuery 1.2.2+
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});


(function($) {

    var ExtraBrowser = function(options) {
        this._init(options)
    }

    ExtraBrowser.prototype = {

        constructor : ExtraBrowser,

        _init : function(options) {
			var self = this;
		
			this.$browser = $('#extraBrowser');
			this.$selectionStatus = this.$browser.find('.selection');
			this.$okButton = this.$browser.find('button.ok');
			this.$path = this.$browser.find('.bar.top .path');
			this.$content = this.$browser.find('.content');
			this.$html = this.$content.find('.html');
			this.$busy = this.$browser.find('.busy-indicator');
			this.selectedNum = 0;
			this.isBusy = false;
			this.liW = 198;
			this.cMargin = parseInt(this.$content.css('left')) * 2; //space 
			
			
			this.$background = $('<div />').css({
     		   'display':'none', 
			   'position':'fixed', 
			   '_position':'absolute', 
     		   'left':'0', 
			   'top':'0', 
			   'width':'100%', 
			   'height':'100%',
     		   'background':'#000000', 
			   'margin':'0', 
			   'padding':'0', 
			   'border':'0 none',
			   'opacity':0.3,
     		   'z-index':'9000'
     	    }).appendTo('body').click(function() {
	    	   self._close();
	        });
			
		    $(window).on('keydown', function(e) {
			    if (e.keyCode == 27) //ESC button
					self._close();
		    });	

            
            //prevents scrolling body when scrolling inside
            this.$content.on('mousewheel', function(event){
                var sTop = self.$content.scrollTop();
                if (
                    ((sTop + self.$content.outerHeight() >= self.$content.prop('scrollHeight')) && event.deltaY < 0) ||
                    (sTop == 0 && event.deltaY > 0)
                ){
                    event.preventDefault(); //stop scrolling
                }
            });

			//handlers
			//on folder click
			this.$browser.on('click.browser', '.content ul li:not(".folder")', function(){
				self._onImageClick($(this));
			});	
			
			this.$browser.on('click.browser', '.content ul li.folder', function(){
				var $folder = $(this);
				self._navigateFolder($folder.data('path'));
			});	
			
			this.$browser.on('click.browser', '.top.bar .path a', function(event){
				event.preventDefault();
				self._navigateFolder($(this).attr('href'));
			});
			
			this.$browser.on('click.browser', 'button.ok', function(event){
                event.preventDefault(); //if its in form it will work like link
				if ( $(this).attr('disabled') )
					return;
                    
                $(this).attr('disabled', 'disabled');
					
				var $selected = self.$browser.find('li.selected'),
					lastIdx = $selected.length - 1;
				
				$selected.each(function(index){
					var $selected = $(this);
						
					self.options.addImage && self.options.addImage($selected.data('filepath'), $selected.data('width'), $selected.data('height') , lastIdx - index);
				});
				
				self._close();
			});	


			this.$browser.on('click.browser', 'button.cancel', function(event){
                event.preventDefault(); //if its in form it will work like link
				self._close();
			});
        },
		
		open : function(options){
			if (this.opened)
				return;
                
            var self = this;
				
			this.opened = true;
			this.options = this._getOptions(options);
            
 			this.maxWidth = $(window).width() - 100;
			this.maxHeight = $(window).height() - 100;
			this.htmlW = Math.ceil(this.liW * Math.floor( (this.maxWidth - this.cMargin) / this.liW ));
	
			this.$html.width(this.htmlW + 'px'); //set element width
			
			if (this.options.headerText)
				this.$browser.find('h3').text(this.options.headerText);
				
			if (!this.options.multipleSelection){
				this.$selectionStatus.hide();
			}
			else{
				this.$selectionStatus.find('button').click(function(){
					self.$browser.find('.content ul li:not(".folder,.selected")').trigger('click');
				});
			}
			
			this.$browser.css({
				width : this.htmlW + this.cMargin + this._getBrowserScrollbarWidth() + 6 + 'px',
				height : this.maxHeight + 'px'
			});
			
		    this.$background.fadeIn(500);
		    this.$browser.css({
                top : '50%',
                left : '50%',
                marginTop : -( this.$browser.outerHeight() / 2) + 'px',
                marginLeft : -( this.$browser.outerWidth() / 2) + 'px'
		    }).fadeIn();
			
			this._navigate(this.options.startingPath && !this.lastPath ? this.options.startingPath : this.options.remeberLastPath ? this.lastPath : this.options.startingPath);
            
		},
		
		_close : function(){
			if (!this.opened)
				return;
				
			this.opened = false;
            
            this.$browser.find('li.selected').removeClass('selected');
            
            this._updateSelectionStatus();

			this.$background.add(this.$browser).hide();
		},

        /*retrieves options*/
        _getOptions : function(options) {
            options = $.extend({}, $.extraBrowser.defaults, options);

            return options;
        },
		
		_getBrowserScrollbarWidth : function(){
			var outer, outerStyle, scrollbarWidth;
			outer = document.createElement('div');
			outerStyle = outer.style;
			outerStyle.position = 'absolute';
			outerStyle.width = '100px';
			outerStyle.height = '100px';
			outerStyle.overflow = 'scroll';
			outerStyle.top = '-9999px';
			document.body.appendChild(outer);
			scrollbarWidth = outer.offsetWidth - outer.clientWidth;
			document.body.removeChild(outer);
			return scrollbarWidth;
		},
		
		_navigate : function(path){
			var self = this;
			if (this.isBusy)
				return;
				
			this.isBusy = true;
			this.$busy.show();
			
			this.$html.hide();

			$.ajax({
				url: this.$browser.data('url'),
				data: {
					path : path,
					bust : new Date().getTime()
				},
				dataType: 'html'
			})
			.done(function(data){
				self.$html.html(data);
				self.$html.fadeIn(200);
				self._updateBreadcrumb(path);
			})
			.fail(function(){
				alert('ExtraBrowser: Ajax call failed!');
			})
			.always(function(){
				self.$busy.hide();
				self.isBusy = false;
				
				self.lastPath = path;
			});
		},
		
		_onImageClick : function($clickedElement){
			if (!this.options.multipleSelection){
				var $selected = this.$browser.find('.selected');
				if ($selected.is($clickedElement))
					$clickedElement.removeClass('selected');
				else{
					$selected.removeClass('selected');
					$clickedElement.addClass('selected');
				}
			}
			else{
				$clickedElement.toggleClass('selected');
			}	
		
			this._updateSelectionStatus();
		},

		_navigateFolder : function(path){
			var agree;
				
			if (this.selectedNum){
				agree = confirm(this.$browser.data('confirm'));
			}
			
			if (this.selectedNum && !agree){
				return;
			}

			this.$browser.find('li').removeClass('selected');
			this._updateSelectionStatus();
			this._navigate(path);
		},

		_updateBreadcrumb : function(path){
			var $c = this.$path.find('.h5 .container'),
				pathArray = path ? path.split('/') : [];
			
			$c.html('<a href="">uploads</a>');
			$.each(pathArray, function(index, value){
				$c.append(' / <a href="'+pathArray.slice(0,index+1).join('/')+'">'+ value + '</a>');
			});
		},

		_updateSelectionStatus : function(){
			this.selectedNum = this.$browser.find('li.selected').length;
			
			this.$selectionStatus.find('span').text(this.selectedNum);
			
			if(!this.selectedNum)
				this.$okButton.attr('disabled', 'disabled');
			else
				this.$okButton.removeAttr('disabled').focus();
		}
    }
	
	var extraBrowser;
    //only one parameter is supported
    $.extraBrowser = function(options) {
		if (!extraBrowser) //called first time
			extraBrowser = new ExtraBrowser(options);
		
		extraBrowser.open.call(extraBrowser, options);
    }

    $.extraBrowser.Constructor = ExtraBrowser;

    $.extraBrowser.defaults = {
        headerText : '', //if empty, default one will be displayed
        startingPath : '', //starting path relative to uploads dir,
		remeberLastPath : true, //remember last opened path
        multipleSelection : false, //allow selecting multiple images or one
		//function called multiple times or once when user clicks OK button, parameters : (filename, width, height, leftCalls), leftCalls is how many times func will be called
        addImage : $.noop 
    }

})($); 



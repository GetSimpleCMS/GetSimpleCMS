/**
 * Pagify jQuery plugin
 * 
 * Copyright (C) Martin Vlcek, 2011
 * 
 * License: GPL 3
 */

(function($) {
	
	var defaults = {
		items: null,
		pageSize: 10,
		location: "after",
		wrap: false,
		currentPage: 1,
		onPage: null
	};	
	
	var methods = {

		init: function(options) {
	        return this.each(function() {
			    var $this = $(this);
				var opts = $.extend(defaults, options);
			    $this.data('pagify', opts);
			    var $items = opts.items ? $this.find(opts.items) : $this.children();
			    var numPages = Math.ceil($items.size() / opts.pageSize);
			    if (numPages > 1) {
				    var $container = $this;
				    if (opts.wrap) {
				    	$container = $this.wrap('<div class="pagify-container"></div>').closest('.pagify-container');
				    }
				    var nav = '<div class="pagify">';
				    for (var i=1; i<=numPages; i++) nav += ' <a href="#" class="pagify-'+i+'">'+i+'</a> ';
				    nav += '</div>';
				    switch (opts.location) {
				   		case "before": $container.prepend(nav); break;
				   		case "both"  : $container.prepend(nav).append(nav); break;
				   		default      : $container.append(nav);
				    }
				    $container.find('.pagify a').click(function(e) {
				    	methods.setPage.apply($this, [ $(e.target).text() ]);
				    })
				    methods.setPage.apply($this, [ opts.currentPage ]);
			    }
	        });
		},
		
		setPage: function(page) {
			var num = parseInt(page);
			return this.each(function() {
				var $this = $(this);
				var opts = $this.data('pagify');
				var $container = opts.wrap ? $this.closest('.pagify-container') : $this;
				if ($container.find('.pagify a.current.pagify-'+page).size() == 0) {
				    var $items = opts.items ? $this.find(opts.items) : $this.children();
				    if (!opts.wrap) $items = $items.filter(':not(.pagify)');
				    var numPages = Math.ceil($items.size() / opts.pageSize);
				    if (page < 1) page = 1; else if (page > numPages) page = numPages;
				    $items.hide();
				    $items.slice((page-1)*opts.pageSize, page*opts.pageSize).show();
				    $container.find('.pagify a').removeClass('current').
				    		filter('.pagify-'+page).addClass('current');
				    if (opts.onPage) opts.onPage.apply(this, [ this, page ]);
				}
			})
		},
	}
	
	$.fn.pagify = function(method) {
	    if (methods[method]) {
		    return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	    } else if (typeof method === 'object' || !method) {
		    return methods.init.apply(this, arguments);
		} else {
		    $.error( 'Method ' +  method + ' does not exist on jQuery.pagify' );
		}    
	};
	
})(jQuery);

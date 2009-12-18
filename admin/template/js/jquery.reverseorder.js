/* reverseOrder : jQuery order reverser plugin
 * Written by Corey H Maass for Arc90
 * (c) Arc90, Inc.
 * 
 * Licensed under:Creative Commons Attribution-Share Alike 3.0 http://creativecommons.org/licenses/by-sa/3.0/us/
 */

(function($){$.fn.reverseOrder=function(){return this.each(function(){$(this).prependTo($(this).parent())})}})(jQuery);
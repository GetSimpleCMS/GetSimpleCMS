$(document).ready(function(){
	 if (location.href.indexOf('style.css') > -1) {
      $("body").addClass("css");
      }  
	 
	 if (location.href.indexOf('load.php') > -1) {
      $("p").addClass("text");
      } 
	  
	 $(".text").each(function(i) {
        $(this).addClass("" + (i+1));
     });
	 
	 $('#colorpickerField1').ColorPicker({
	 onSubmit: function(hsb, hex, rgb, el) {
		$(el).val(hex);
		$(el).ColorPickerHide();
	 },
	 onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	 }
     })
    .bind('keyup', function(){
	$(this).ColorPickerSetColor(this.value);
    });
	 
	$('#sidebar').after("<div class='user-tools'><a href='logout.php' class='power-off'><i class='fa fa-power-off'></i></a><a href='settings.php' class='settings'><i class='fa fa-cogs'></i></a><a href='support.php' class='help'><i class='fa fa-life-ring'></i></a><div style='clear: both'></div></div>");
 	
	$(document).ready(function(){
	$('a.scrollToTop').click(function(){
	$('html, body').animate({scrollTop:0}, 'slow');
	return false;
	});
	}) 
	
	$(window).scroll( function(){
	/* Check the location of each desired element */
	$('.scroll_top').each( function(i){
	var bottom_of_object = $(this).position().top + $(this).outerHeight();
	var bottom_of_window = $(window).scrollTop() + $(window).height();
	 /* If the object is completely visible in the window, fade it in */
	if( bottom_of_window > bottom_of_object ){
	$(this).animate({'opacity':'1'},100);
	}
	$(window).bind('scroll', function(){
	if($(this).scrollTop() < 50) {$(".scroll_top").hide();} 
	});
	$(window).bind('scroll', function(){
	if($(this).scrollTop() > 50) {$(".scroll_top").show().animate({'opacity':'1'},100);} 
	});
	
	}); 
	}); 
	
	$(window).bind('scroll', function(){
	if($(this).scrollTop() > 50) {$("#footer").hide();}  }); 
 
	$(document).ready(function(){   
	$(window).bind('scroll', function(){    
	$("footer").toggle($(this).scrollTop() > 0); }); });
	$(window).bind('scroll', function(){ 
	if($(this).scrollTop() > 200) {
	$("#footer").show(); } });
	
	setTimeout(function() { 
	 
    $('footer').fadeIn('slow');  
	document.getElementById('#footer').style.display = 'all';
	
	}, 3000);     
	
    });// JavaScript Document
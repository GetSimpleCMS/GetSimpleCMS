$(document).ready(function() {
  
	// pages tag input
  $('input:text[name=post-metak]').addTagInput();

	// mikeh blog plugin tag input
  $('input:text[name=post-tags]').addTagInput();
    
  // i8n gallery
  // This is for i18n gallery
	$('input:text[name$=_tags]').addTagInput();
	
	// generic support
  $('.tags_input').addTagInput();
	
});

// If tags not already defined define it here
// this lets us use i18n autocomplete tags for now
if(!tags) var tags = ['yellow','green','blue','red'];

// custom wrapper for tagsInput, it has a problem receiving objects directly from selectors
jQuery.fn.addTagInput = function(){
  
    $(this).tagsInput({
			readOnly: false, 
      width:'auto',
      height:'auto',
      minHeight:'auto',    
      // autocomplete_url:tags,      
      // autocomplete:{
      //   minChars: 3,
      //   max: 50,
      //   scroll: true,
      //   // multiple: true,
      //   // multipleSeparator: ', ',
      //   matchContains:true,
      //   selectFirst:false,
      //   source:tags
      // },
    });
    
}

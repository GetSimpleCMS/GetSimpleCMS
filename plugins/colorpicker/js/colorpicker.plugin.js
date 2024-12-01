/*
 * Color picker functions
 * v0.1
 * www.singulae.com
 */
 

var ColorPickerSelection = {start:0,end:0,scroll:0,text:[],color:[],rangeObj:{}};


/* Check if string is color */
 function ColorPickerIsThatAColor (c) {
		var col = new RGBColor(c);
		if (col.ok) {		
			return [col.toHex(),col.t,col.a];
		}else{		
			return false;
		}
}

/* Replace color string */
function ColorPickerReplaceIt(hsb, hex, rgb) {

		var txtarea = $('#codetext')[0];
		var newcolor = 	ColorPickerSelection.color[1] == 'rgb'  ? rgb :
						ColorPickerSelection.color[1] == 'hex'  ? hex :
						ColorPickerSelection.color[1] == 'rgba' ? rgb :
						false;				
		var a = ColorPickerSelection.color[1] == 'rgba' ? ColorPickerSelection.color[2] : false;		

		if(typeof(newcolor) == 'object')
		{	
			if(a){	newcolor =  'rgba(' + newcolor.r + ',' + newcolor.g + ',' + newcolor.b + ',' + a + ');';
			}else{	newcolor =  'rgb(' + newcolor.r + ',' + newcolor.g + ',' + newcolor.b + ');';};						
		}
		
		if ($.browser.msie) {	
			$('#codetext').RestoreSelection();			
			var text = ColorPickerSelection.textie;
		}else{
			var text = ColorPickerSelection.text;	
		}
		
		var firstchar = text.substring(0,1);
		var lastchar  = text.substring(text.length-1,text.length);	
		newcolor = firstchar == '#' ? '#'+newcolor : newcolor;
		newcolor = (lastchar  == ';' && newcolor.substring(0,1) != 'r') ? newcolor+';' : newcolor;
		newcolor = lastchar == ' ' ? newcolor+"" : newcolor;
		
		$('#codetext').ReplaceSelection(newcolor,true);

};


	/*
	 * _________________________________________________________________________________	
	 * Colorpicker
	 */

		$(document).ready(function()
		{	
		
			$('div.bodycontent').before('<div class="updated">Color picker ON</div>');
					$(".updated").fadeOut(500).fadeIn(500).delay(2000).fadeOut(500);
			});
		
			$('#codetext').ColorPicker({			
				eventName:'mouseup',
				onShow: function (colpkr) {	
					var select = $(this).SaveSelection();					
					if(select){
						ColorPickerSelection.color 	= ColorPickerIsThatAColor(select.text);
						ColorPickerSelection.start 	= select.start;
						ColorPickerSelection.end 	= select.end;
						
						if(ColorPickerSelection.color.length>0 ){
							$(this).ColorPickerSetColor(ColorPickerSelection.color[0]);						
							$(colpkr).fadeIn(500);
						}
					}
					return false;
				},				
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					$('#codetext').ResetSelection();
					return false;
				},				
				onChange: function (hsb, hex, rgb) {
					if (!$.browser.msie) {	
						ColorPickerReplaceIt(hsb, hex, rgb);
					}
				},				
				onSubmit: function(hsb, hex, rgb,colpkr) {				
					ColorPickerReplaceIt(hsb, hex, rgb);				
					$(colpkr).ColorPickerHide();					
				}
			});
			

/*
 * jQuery functions for caret control
 *
 */
 
(function() {

	var textSelect = {

		SaveSelection: function() {

			var e = this.jquery ? this[0] : this;

			return (
				 /* Internet Explorer */
				(document.selection && function() {

					//e.focus();
					var range = document.selection.createRange ();
					
					if (range == null) {
						return { start: 0, end: e.value.length, length: 0 }
					}

					var re = e.createTextRange();
					var rc = re.duplicate();
					if (range.getBookmark) { 					
						ColorPickerSelection.text = range.getBookmark ();
						re.moveToBookmark(range.getBookmark());
						rc.setEndPoint('EndToStart', re);
					}	
					ColorPickerSelection.textie = range.text;
					return { start: rc.text.length, end: rc.text.length + range.text.length, length: range.text.length, text: range.text };
					
				}) ||
				
				/* Firefox, Opera, Google Chrome and Safari */
				('selectionStart' in e && function() {
								
					var alltext = e.value;
					ColorPickerSelection.text 	= alltext.substring (e.selectionStart, e.selectionEnd);
					ColorPickerSelection.start 	= e.selectionStart;
					ColorPickerSelection.end 	= e.selectionEnd;
					var len 	= ColorPickerSelection.start - ColorPickerSelection.end;
					var ua = $.browser;  //FF
					if ( ua.mozilla){ColorPickerSelection.scroll = e.scrollTop;};					
					
					return { start: e.selectionStart, end: e.selectionEnd, length: len, text: ColorPickerSelection.text };
				}) ||

				/* browser not supported */
				function() {
					// alert('browser not supported');
					return { start: 0, end: e.value.length, length: 0 };
				}
			)();

		},
		
		RestoreSelection: function(){
		
			var e = this.jquery ? this[0] : this;
			var text = arguments[0] || '';
			
			return (
				// Internet Explorer
				(document.body.createTextRange && function() { 
					ColorPickerSelection.rangeObj = document.body.createTextRange ();
					ColorPickerSelection.rangeObj.moveToBookmark (ColorPickerSelection.text);
					ColorPickerSelection.rangeObj.select ();
					return this;
				}) ||
					
				/* Firefox, Opera, Google Chrome and Safari */					
				('selectionStart' in e &&  function(){					
					e.selectionStart = ColorPickerSelection.start;
					e.selectionEnd 	 = ColorPickerSelection.end;
					var ua = $.browser;  //FF
					if ( ua.mozilla){e.scrollTop = ColorPickerSelection.scroll;};
					return this;
				}) ||
					
				/* browser not supported */
				function() {
				  alert('browser not supported');						
					return this;
				}
			)();
		},
		ReplaceSelection: function() {

			var e = this.jquery ? this[0] : this;
			var text = arguments[0] || '';

			return (
				/* Firefox, Opera, Google Chrome and Safari */	
				('selectionStart' in e && function() { 
					e.value 	= e.value.substring(0, ColorPickerSelection.start) + text + e.value.substring(ColorPickerSelection.end, e.value.length);
					ColorPickerSelection.end = ColorPickerSelection.start+text.length;
					var ua = $.browser;  //FF
					if ( ua.mozilla){e.scrollTop = ColorPickerSelection.scroll;};
					
					return this;
				}) ||

				/* Internet Explorer */
				(document.selection && function() {
					//e.focus();
					document.selection.createRange().text = text;
					return this;
				}) ||

				/* browser not supported */
				function() {
					e.value += text;
					return this;
				}
			)();
		},
		ResetSelection: function(){
			var e = this.jquery ? this[0] : this;
			return (				
				function() {
					ColorPickerSelection.start		= 0;
					ColorPickerSelection.end 		= 0;					
					ColorPickerSelection.rangeObj	= 0;
					ColorPickerSelection.text 		= '';
					e.selectionStart = 0;
					e.selectionEnd 	 = 0;
					var ua = $.browser;  //FF
					if ( ua.mozilla){ ColorPickerSelection.scroll = 0;};
					return this;
				}
			)();
		}
}
	jQuery.each(textSelect, function(i) { jQuery.fn[i] = this; });

})();


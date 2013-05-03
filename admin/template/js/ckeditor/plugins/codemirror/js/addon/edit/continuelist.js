<<<<<<< HEAD
﻿(function(){CodeMirror.commands.newlineAndIndentContinueMarkdownList=function(c){var a=c.getCursor(),d=c.getTokenAt(a),b;if("string"==d.className){var a=c.getRange({line:a.line,ch:0},{line:a.line,ch:d.end}),e;if(0==d.string.search(/\*|\d+\./)&&(e=(b=/^[\W]*(\d+)\./g.exec(a))?parseInt(b[1])+1+".  ":"*   ",b=a.slice(0,d.start),!/^\s*$/.test(b))){b="";for(a=0;a<d.start;++a)b+=" "}}null!=b?c.replaceSelection("\n"+b+e,"end"):c.execCommand("newlineAndIndent")}})();
=======
﻿(function(){var d=/^(\s*)([*+-]|(\d+)\.)(\s*)/;CodeMirror.commands.newlineAndIndentContinueMarkdownList=function(b){var c=b.getCursor(),a;if(!b.getStateAfter(c.line).list||!(a=b.getLine(c.line).match(d)))b.execCommand("newlineAndIndent");else{var c=a[1],e=a[4];a=0<="*+-".indexOf(a[2])?a[2]:parseInt(a[3],10)+1+".";b.replaceSelection("\n"+c+a+e,"end")}}})();
>>>>>>> patch_cke

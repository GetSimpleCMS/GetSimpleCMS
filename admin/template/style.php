<?php 
/**
 * Admin Stylesheet
 * 
 * @package GetSimple
 * @subpackage init
 */
header("Content-type: text/css");
header("Expires: ".date("D, d M Y H:i:s", time() + 3600) ); # cache for an hour
header("Pragma: cache");
header("Cache-Control: maxage=3600");

function getXML($file) {
	$xml = file_get_contents($file);
	$data = simplexml_load_string($xml, 'SimpleXMLExtended', LIBXML_NOCDATA);
	return $data;
}

if (file_exists(GSTHEMESPATH.'admin.xml')) {
	#load admin theme xml file
	$theme = getXML(GSTHEMESPATH.'admin.xml');
	$primary_0 = trim($theme->primary->darkest);
	$primary_1 = trim($theme->primary->darker);
	$primary_2 = trim($theme->primary->dark);
	$primary_3 = trim($theme->primary->middle);
	$primary_4 = trim($theme->primary->light);
	$primary_5 = trim($theme->primary->lighter);
	$primary_6 = trim($theme->primary->lightest);
	$secondary_0 = trim($theme->secondary->darkest);
	$secondary_1 = trim($theme->secondary->lightest);
} else {
	# set default colors
	$primary_0 = '#0E1316'; # darkest
	$primary_1 = '#182227';
	$primary_2 = '#283840';
	$primary_3 = '#415A66';
	$primary_4 = '#618899';
	$primary_5 = '#E8EDF0';
	$primary_6 = '#AFC5CF'; # lightest
	
	$secondary_0 = '#9F2C04'; # darkest
	$secondary_1 = '#CF3805'; # lightest
}
?>

/** GLOBAL RESETS **/
:link,:visited {text-decoration:none}
h1,h2,h3,h4,h5,h6,pre,code {font-size:1em;font-weight:400;}
ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,body,html,p,blockquote,fieldset,input {margin:0; padding:0}
body {height:100%;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:12px;background:#f6f6f6;}
a img,:link img,:visited img {border:none}
.clear {clear:both;}
#help {display:none;}
.imgthumb {display:none;width:70px;}
.imgthumb img {border:1px solid #555;}
.hidden {display:none;}
html {overflow-y: scroll;}
.clearfix:before, .clearfix:after { content: "\0020"; display: block; height: 0; visibility: hidden; }
.clearfix:after { clear: both; }
.clearfix {zoom:1;}

/** HEADER / NAVIGATION **/
.header {
	color:#FFF;
	border-top:1px solid <?php echo $primary_1; ?>;
	background: <?php echo $primary_3; ?>; /* old browsers */
	background: -moz-linear-gradient(top, <?php echo $primary_4; ?> 0%, <?php echo $primary_2; ?> 100%); /* firefox */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $primary_4; ?>), color-stop(100%,<?php echo $primary_2; ?>)); /* webkit */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $primary_4; ?>', endColorstr='<?php echo $primary_2; ?>',GradientType=0 ); /* ie */
	margin:0 0 25px 0;
	}
	.header .wrapper {height:115px;position:relative;border:none;}
	.wrapper .nav {
	list-style:none;
	font-size:13px;
	position:absolute;
	bottom:0px;
	left:0;
	width:960px;
	}
	.wrapper .nav li a {
		padding:7px 13px;
		font-weight:100 !important;
		text-decoration:none !important;
		display:block;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		-moz-border-radius-topright:5px;
		-webkit-border-top-right-radius: 5px;
		-webkit-transition: all .3s ease-in-out;
		-moz-transition: all .3s ease-in-out;
		-o-transition: all .3s ease-in-out;
		transition: all .3s ease-in-out;
	}
	.wrapper .nav li a:link, .wrapper .nav li a:visited, .wrapper #pill li a:link, .wrapper #pill li a:visited {
		color:<?php echo $primary_6; ?>;
		background:<?php echo $primary_1; ?>;
		text-shadow: 1px 1px 0px <?php echo $primary_0; ?>;
	}
	.wrapper #pill li.debug a:link, .wrapper #pill li.debug a:visited, .wrapper #pill li.debug a:hover {
		color:#fff;
		background:#cc0000;
		padding:4px 10px;
		font-weight:700 !important;
		text-decoration:none !important;
		display:block;
		border-left:1px solid <?php echo $primary_3; ?>;
	}
	 
	#edit .wrapper .nav li a.pages,
	#pages .wrapper .nav li a.pages,
	#plugins .wrapper .nav li a.plugins,
	#settings .wrapper .nav li a.settings,
	#components .wrapper .nav li a.theme,
	#theme .wrapper .nav li a.theme,
	#theme-edit .wrapper .nav li a.theme,
	#navigation .wrapper .nav li a.theme,
	#upload .wrapper .nav li a.files,
	#image .wrapper .nav li a.files,
	#backups .wrapper .nav li a.backups,
	#support .wrapper .nav li a.support,
	#log .wrapper .nav li a.support,
	#health-check .wrapper .nav li a.support,
	#backup-edit .wrapper .nav li a.backups,
	#archive .wrapper .nav li a.backups, 
	#load .wrapper .pages li a.pages,
	#load .wrapper .plugins li a.plugins,
	#load .wrapper .settings li a.settings,
	#load .wrapper .theme li a.theme,
	#load .wrapper .files li a.files,
	#load .wrapper .backups li a.backups,
	#load	.wrapper .support li a.support {
		color:<?php echo $primary_1; ?>;
		background:#f6f6f6;
		background: -moz-linear-gradient(top, #FFF 3%, #F6F6F6 100%); /* firefox */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(3%,#FFF), color-stop(100%,#F6F6F6)); /* webkit */
		font-weight:bold !important;
		text-shadow: 1px 1px 0px #fff;
		box-shadow: rgba(0,0,0, 0.10) 2px -2px 2px;  
		-moz-box-shadow: rgba(0,0,0, 0.10) 2px -2px 2px;  
		-webkit-box-shadow: rgba(0,0,0, 0.10) 2px -2px 2px;
	}
	.wrapper .nav li a:active, .wrapper .nav li a:focus, .wrapper .nav li a:hover, .wrapper #pill li a:hover, .wrapper #pill li a:focus {
		color:#FFF;background:<?php echo $primary_0; ?>;
		text-shadow: 1px 1px 0px #000;
		}	
	.wrapper .nav li {float:left; margin:0 5px 0 0;position:relative;}
	.wrapper .nav li.rightnav { float:right; margin:0 0 0 0; font-size:11px; }
	.wrapper .nav li.rightnav a.first {
		padding:4px 10px;
		font-weight:100 !important;
		text-decoration:none !important;
		display:block;
		border-top-right-radius: 5px;
		border-bottom-right-radius: 5px;
		-moz-border-radius-topright: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-bottomright:5px;
		-webkit-border-bottom-right-radius: 5px;
		border-top-left-radius: 0;
		border-bottom-left-radius:0;
		-moz-border-radius-topleft: 0;
		-webkit-border-top-left-radius: 0;
		-moz-border-radius-bottomleft:0;
		-webkit-border-bottom-left-radius: 0;
		border-left:1px solid <?php echo $primary_3; ?>;
	}
		.wrapper .nav li.rightnav a.last {
		padding:4px 10px;
		font-weight:100 !important;
		text-decoration:none !important;
		display:block;
		border-top-left-radius: 5px;
		border-bottom-left-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-webkit-border-top-left-radius: 5px;
		-moz-border-radius-bottomleft:5px;
		-webkit-border-bottom-left-radius: 5px;
		border-top-right-radius: 0;
		border-bottom-right-radius: 0;
		-moz-border-radius-topright: 0;
		-webkit-border-top-right-radius: 0;
		-moz-border-radius-bottomright:0;
		-webkit-border-bottom-right-radius: 0;
	}
	
	/* warning alert on tab */
	.nav li .warning {
		position:absolute;
		top:-5px;left:-5px;
		font-size:10px;
		color:#000;
		text-shadow: 1px 1px 0px rgba(255,255,255,.5);
		font-weight:bold;
		text-align:center;
		border-radius:5px;
		display:block;
		width:11px;
		border:1px solid #D5AF00;
		background:#F2C800;
		background: -moz-linear-gradient(top, #F2C800 0%, #D5AF00 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#F2C800), color-stop(100%,#D5AF00));
	}
	
	.wrapper .nav li a em, .wrapper #pill li a em {font-style:normal;border-bottom:1px dotted #666;}
	.wrapper #pill {list-style:none;position:absolute;top:0px;right:0;font-size:11px;}
	.wrapper #pill li {float:right;}
	.wrapper #pill li.leftnav a {
		padding:4px 10px;
		font-weight:100 !important;
		text-decoration:none !important;
		display:block;
		border-bottom-right-radius: 5px;
		-moz-border-radius-bottomright:5px;
		-webkit-border-bottom-right-radius: 5px;
		border-bottom-left-radius:0;
		-moz-border-radius-bottomleft:0;
		-webkit-border-bottom-left-radius: 0;
		border-left:1px solid <?php echo $primary_3; ?>;
	}
		.wrapper #pill li.rightnav a {
		padding:4px 10px;
		font-weight:100 !important;
		text-decoration:none !important;
		display:block;
		border-bottom-left-radius: 5px;
		-moz-border-radius-bottomleft:5px;
		-webkit-border-bottom-left-radius: 5px;
		border-bottom-right-radius: 0;
		-moz-border-radius-bottomright:0;
		-webkit-border-bottom-right-radius: 0;
	}



.wrapper {
	margin-left:auto;
	margin-right:auto;
	width:960px;
	text-align:left;
	padding-top:1px;
	}
.wrapper p {
	line-height:18px;
	margin:0 0 20px 0;
	}
	.wrapper #maincontent ul, .wrapper #maincontent ol {
	line-height:18px;
	margin:0 0 20px 30px;
	}
.wrapper a:link, .wrapper a:visited {
	color:<?php echo $primary_3; ?>;
	text-decoration:underline;
	font-weight:bold;
	}
.wrapper a:hover {
	color:#333;
	text-decoration:underline;
	font-weight:bold;
	}
.inner {padding:20px;}
.header h1 {
	font-size:20px;
	font-family:Georgia, Times, Times New Roman, serif;
	position:absolute;
	text-shadow: 1px 1px 0px <?php echo $primary_2; ?>;
	top:30px;
	left:0;
	}
	.header h1 a:link, .header h1 a:visited, .header h1 a:hover {
		font-weight:normal;
		color:<?php echo $primary_5; ?>;
		text-decoration:none;
		font-size:24px;
		}
	.header h1 a:hover  {color:#FFF;}
	.header h1 span {
		color:<?php echo $primary_6; ?>;
		}
	.header h1 span.filename {
		font-style:italic;
	}
	.wrapper h2 {
	font-size:18px;
	font-family:Georgia, Times, Times New Roman, serif;
	color:#777;
	margin:0 0 20px 0;
	}
	.wrapper h2 span {
		color:#bbb;
		font-style:italic;
		}
	h3 {
		font-size:17px;
		font-family:Georgia, Times, Times New Roman, serif;
		font-weight:normal;
		color:<?php echo $secondary_1; ?>;
		margin:0 0 20px 0;
		}
	h3 em {font-style:normal;}
	h3.floated {
		font-size:16px;	
		font-weight:normal;
		font-family:Georgia, Times, Times New Roman, serif ;
		padding:2px 0 0 0;
		color:<?php echo $secondary_1; ?>;
		float:left;
		display:block;
		margin:0 0 5px 0;
	}
	h5,div.h5 {
		margin:10px 0 10px 0;font-size:14px;
		line-height:28px;
		display:block;
		padding:3px 10px;
		background:#EEEEEE;
		background: -moz-linear-gradient(top, #f6f6f6 3%, #EEEEEE 100%); /* firefox */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(3%,#f6f6f6), color-stop(100%,#EEEEEE)); /* webkit */
		border:1px solid #cccccc;
		text-shadow:1px 1px 0 #fff;
		-moz-border-radius: 3px;
		-khtml-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius:3px;
		color:#999;
	}
	h5 a {text-decoration:none !important;}
	h5 img, tr.folder img {vertical-align:middle; margin:0 5px 0 0;opacity:.5;}
	h5:hover img {opacity:1;}
	.bodycontent ul, .bodycontent ol {margin:0 0 20px 30px;}
	.bodycontent ul p, .bodycontent ol p {margin:0 0 10px 0;}


#maincontent {width:690px;float:left;text-align:left;}
#sidebar {width:225px;float:right;}
#sidebar .section {background:#fff;border:1px solid #ccc;padding:20px;margin:0 0 30px 0;line-height:18px;}
#sidebar .section p.small {font-size:11px;margin:15px 0 0 0;}
#sidebar .section input.text {width:175px;font-size:11px;padding:4px;border:1px solid #666;}
#sidebar .snav {list-style:none;margin:0 0 30px 0;}
#sidebar .snav ul {list-style:none;margin:0;}
#sidebar .snav li {margin:0 0 3px 0;}
#sidebar .snav li ul li {margin:0 0 3px 0;}
#sidebar #js_submit_line {margin:0 0 0 12px;}
#sidebar .snav li a {font-weight:800;display:block;padding:5px 15px 5px 15px;text-decoration:none;
	border-radius: 4px;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	-webkit-border-radius: 4px;
		}
#sidebar .snav li a:link, #sidebar .snav li a:visited {
	margin-left:13px;
	color:<?php echo $primary_6; ?>;
	background:<?php echo $primary_1; ?>;
	text-shadow: 1px 1px 0px <?php echo $primary_0; ?>;
	-webkit-transition: all .3s ease-in-out;
	-moz-transition: all .3s ease-in-out;
	-o-transition: all .3s ease-in-out;
	transition: all .3s ease-in-out;
	}
#sidebar .snav li a.current {
	margin-left:0px;
	cursor:default;
	color:#FFF;
	background:<?php echo $secondary_1; ?> url('images/active.png') center left no-repeat !important;
	text-shadow: 1px 1px 0px <?php echo $secondary_0; ?>;
	padding-left:28px;
	border-radius: 0;
	-moz-border-radius: 0;
	-khtml-border-radius: 0;
	-webkit-border-radius: 0;
	border-top-right-radius: 4px;
	border-bottom-right-radius: 4px;
	-moz-border-radius-bottomright: 4px;
	-webkit-border-bottom-right-radius: 4px;
	-moz-border-radius-topright: 4px;
	-webkit-border-top-right-radius: 4px;
	}
#sidebar .snav li a.current:hover {
	text-shadow: 1px 1px 0px <?php echo $secondary_0; ?>;
	margin-left:0px;
	cursor:default;
	color:#FFF;
	background:<?php echo $secondary_1; ?> url('images/active.png') center left no-repeat !important;
	padding-left:28px;
}
#sidebar .snav li a:hover {
	color:#FFF;background:<?php echo $primary_0; ?>;
	margin-left:13px;
	text-shadow: 1px 1px 0px #000;
}
#sidebar .snav li a em  {font-style:normal;border-bottom:1px dotted #666;}
#sidebar .snav li a.current em {font-style:normal;border-bottom:1px dotted #fff;}
#sidebar .snav small {color:#666;}
.edit-nav {margin:0 0 15px 0;}
.edit-nav a {
	font-size:10px;
	text-transform:uppercase;
	display:block;
	padding:3px 10px;
	float:right;
	margin:0 0 0 5px;
	border-radius: 15px;
	-moz-border-radius: 15px;
	-webkit-border-radius: 15px;
	background-repeat:no-repeat;
	background-position: 94% center;
}
.edit-nav select {margin-top:-3px;float:right;padding:1px;border:1px solid #999;font-size:11px;border-radius: 2px;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;}
.edit-nav p {float:right;font-size:11px;margin:0;}
.edit-nav label {font-weight:100;display:inline;font-size:11px;color:#666;margin:0;padding:0;}
.edit-nav a#metadata_toggle {background-image: url('images/plus.png'); padding-right:20px;}
.edit-nav a#metadata_toggle.current {background-image: url('images/minus.png'); padding-right:20px;}


.edit-nav {height:1%;}
.edit-nav a:link, .edit-nav a:visited {
	line-height:14px !important;
	background-color:<?php echo $primary_1; ?>;
	color:#ccc;
	font-weight:bold;
	text-decoration:none;
	text-shadow: 1px 1px 0px <?php echo $primary_0; ?>;
	-webkit-transition: all .15s ease-in-out;
	-moz-transition: all .15s ease-in-out;
	-o-transition: all .15s ease-in-out;
	transition: all .15s ease-in-out;
}

.edit-nav a:hover, #sidebar .edit-nav a:hover, .edit-nav a.current {
	background-color:<?php echo $secondary_1; ?>;
	color:#FFF;
	font-weight:bold;
	text-decoration:none;
	line-height:14px !important;
	text-shadow: 1px 1px 0px <?php echo $secondary_0; ?>;
}
.edit-nav a:link em, .edit-nav a:visited em {font-style:normal;border-bottom:1px dotted #999;}
.edit-nav a.current em, .edit-nav a:hover em {font-style:normal;border-bottom:1px dotted #FFF;}



/* basic default table style */
.wrapper table {border-collapse:collapse;margin:0 0 20px 0;width:645px;}
.wrapper table td {vertical-align:top;padding:4px;border-bottom:1px solid #eee;border-top:1px solid #eee;line-height:20px !important;}
.wrapper table th {background:#FFF !important;padding:2px 4px;font-size:11px;border-top:1px solid #FFF;color:#222;font-weight:bold;text-transform:uppercase;line-height:20px !important;text-align:left;}
.wrapper table tr.head {}
.wrapper table td span {font-size:12px;color:#777;}
.wrapper table.highlight tr:nth-child(odd) { background:#f7f7f7;}
.wrapper table tr#tr-index a { font-weight:bold !important;}
.wrapper table.highlight tr:hover {background:#FFFFD5 !important; }
.wrapper table tr.currentpage{ background:#FFFFD1;}
.wrapper table tr {border-bottom:1px solid #eee;border-top:1px solid #eee;
	-webkit-transition: background-color .3s ease-in-out;
		-moz-transition: background-color .3s ease-in-out;
		-o-transition: background-color .3s ease-in-out;
		transition: background-color .3s ease-in-out;
		}
table td a {font-weight:normal !important;}
.wrapper table.healthcheck tr td {font-size:12px;}
.popup table td {padding:4px;}
.popup table a:link, .popup table a:visited {
	color:<?php echo $primary_3; ?>;
	text-decoration:underline;
	}
.popup table a:hover {
	color:#333;
	text-decoration:underline;
}
sup {
		color:#666;
		font-weight:100 !important;
    vertical-align: baseline;
    font-size: 0.8em;
    position: relative;
    top: -0.4em;
}

/* default form css */
#maincontent .main {
	padding:20px;
	background:#fff;
	border:1px solid #c3c3c3;
	margin:0 0 30px 0;
	box-shadow: rgba(0,0,0, 0.06) 0px 0px 4px;  
	-moz-box-shadow: rgba(0,0,0, 0.06) 0px 0px 4px;  
	-webkit-box-shadow: rgba(0,0,0, 0.06) 0px 0px 4px;
}
#maincontent .main .section {padding-top:40px;}
#maincontent .main pre {
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace; 
	line-height:18px;
	font-size:12px;
	display:block;
}


form p {margin:0 0 10px 0;}
form input.text, form select.text {
	color:#333;
	border:1px solid #aaa;
	padding:5px;
	font-family:Arial, Helvetica Neue, Helvetica, sans-serif;
	font-size:12px;
	width:510px;
	border-radius: 2px;
	-moz-border-radius: 2px;
	-khtml-border-radius: 2px;
	-webkit-border-radius: 2px;
}
form select.text { width:521px;padding:4px 5px; }
input.text:focus, select.text:focus, textarea.text:focus {
	outline:none;
	border:1px solid #666 !important;
	box-shadow: rgba(0,0,0, 0.15) 0px 0px 6px;  
	-moz-box-shadow: rgba(0,0,0, 0.15) 0px 0px 6px;  
	-webkit-box-shadow: rgba(0,0,0, 0.15) 0px 0px 6px;
	background-image:none;
}

form textarea { width:635px; height:420px;line-height:18px;text-align:left;	color:#333;
	border:1px solid #aaa;
	padding:5px;
	font-family:Arial, Helvetica Neue, Helvetica, sans-serif;
	font-size:12px;}
form input[readonly], form select[readonly], form textarea[readonly] {
  background:#eeeeee;
  border:1px solid #999;
  color:#666;
  cursor:default;
}


textarea#codetext { 
	height:660px; 
	font-family: Consolas, Monaco, "Courier New", Courier, monospace;
	font-size: 12px;line-height:18px;
	overflow: scroll; 
	overflow-y: scroll; 
	overflow-x: scroll; 
}
#menu-items span {text-transform:lowercase}
label {
	padding:0;
	margin:0;
	color:#222;
	display:block;
	font-size:12px;
	font-weight:bold;
	font-family:arial, helvetica, sans-serif
}
.inline label {display:inline;}

/* meta dropdown style */
#metadata_window {margin:0 15px 30px 0;}
#metadata_window .rightopt {float:right;width:48%;}
#metadata_window .leftopt {float:left;width:48%;}
#metadata_window p {margin:0 0 15px 0;}
#metadata_window input, #metadata_window select, #metadata_window textarea {
	width:98%;
	font-size:11px;
	padding:3px;	
	margin:0 !important;
}
#metadata_window textarea { height:74px;}
#metadata_window select { width:100%;}
#metadata_window p.inline input {width:25px;padding:0;margin:0;}
#metadata_window #menu-items {height:75px;}
#metadata_window #menu-items span label {display:inline;font-size:11px;color:#777;font-weight:normal;margin:0;padding:0;}
#metadata_window #menu-items select {padding:2px 3px;}
form table.formtable select { width:275px;padding:3px 4px; } 
table.cleantable {border-collapse:collapse;margin:0 0 0 0;}
table.cleantable tr {border:none;}
table.cleantable tr td {border:none;}

/* form submit button style */
input.submit {
	padding:5px 12px;
	font-family:Arial, Helvetica Neue, Helvetica, sans-serif;
	font-weight:bold;
}
.leftsec {float:left;width:50%;padding-bottom:5px;}
.rightsec {float:left;width:50%;padding-bottom:5px;}
.rightsec input.text, .leftsec input.text {
	width:92%;
}	
.rightsec select.text,  .leftsec select.text {
	width:96%;
}
/* login css */
form.login input.text { width:250px; }

/* edit css */
form input.title {font-size:18px;border-color:#000; width:635px; }
form input.secondary {width:280px;}
/* components css */
form.manyinputs input.text { width:230px; }
form.manyinputs textarea { width:632px; height:200px; }
form.manyinputs p {margin:0 0 25px 0;}
.compdiv {padding:10px 0 35px 0;}
table.comptable {margin:0px !important;width:645px;background:#fff;border:none;padding:0;}
table.comptable tr td {font-size:12px;border: none;padding:0;}
table.comptable tr td code {font-size:11px;color:#666;padding:0 4px 0 0;display:block;font-family: Consolas, Monaco, "Courier New", Courier, monospace;}
table.comptable tr {border:none;border:none !important;}
table.comptable tr td input.newtitle {margin-bottom:2px !important;}
.wrapper a.component { float:left;font-weight:800; margin:0 5px 5px 0; padding:3px 10px; text-decoration:none; border-radius: 3px; -moz-border-radius: 3px; -khtml-border-radius: 3px; -webkit-border-radius: 3px; }
.wrapper a.component:link, .wrapper a.component:visited { color:#666; background:#fff;border:1px solid #999; text-decoration: none; }
.wrapper a.component:hover { color:<?php echo $primary_6; ?>; background:<?php echo $primary_1; ?>;border:1px solid <?php echo $primary_0; ?>;text-decoration: none; }
.compdivlist {padding:30px 0;text-align:center;margin:0 0 0 15px;overflow:auto;}


/* alert styles */
.updated, .error {
	margin:0 0 20px 0;
	background:#FCFBB8;
	background: -moz-linear-gradient(top, #F7F7C3 5%, #F9F8B3 100%); /* firefox */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(5%,#F7F7C3), color-stop(100%,#F9F8B3)); /* webkit */
	line-height:30px;
	padding:0 10px;
	border:1px solid #F9CF51;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-khtml-border-radius: 5px;
	-webkit-border-radius: 5px;
}
.error {color:#D94136;}
.deletedrow {background-color:#FFB19B}
.error code {
	color:#000;
	font-size:11px;
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace; 
	line-height:14px;
	background:#fff;
	opacity:.8;
	padding:1px;
}
.updated, #temp_good_flash {color:#308000;}
.hint {color:#777;}


/* button link style */
a.button {
	padding:5px 10px;
	margin:0 0 0 0;
	font-weight:100;
	text-decoration:none !important;
	text-transform:uppercase;
	font-size:11px;
	border-right:1px solid <?php echo $primary_3; ?>;
}
a.button:last-child {
	border-radius: 0 5px 5px 0;
	-moz-border-radius: 0 5px 5px 0;
	-khtml-border-radius: 0 5px 5px 0;
	-webkit-border-radius: 0 5px 5px 0;
	border-right:none;
}
a.button:first-child {
	border-radius: 5px 0 0 5px;
	-moz-border-radius: 5px 0 0 5px;
	-khtml-border-radius: 5px 0 0 5px;
	-webkit-border-radius: 5px 0 0 5px;
}
a.button:link, a.button:visited {
	color:<?php echo $primary_6; ?>;
	background:<?php echo $primary_1; ?>;
	text-shadow: 1px 1px 0px <?php echo $primary_0; ?>;
}
a.button:hover {
	color:#FFF;
	background:<?php echo $primary_0; ?>;
	text-shadow: 1px 1px 0px #000;
}


/* file listing table style */
#filetypetoggle { color:#999; font-size:12px;}
table td.delete {width:20px;text-align:center;font-size:12px;}
.view {width:48px;text-align:center;}
.editl {width:40px;text-align:center;}
.editlw {width:220px;text-align:left;}
.delete a:link, .delete a:visited {
	color:#999 !important; text-decoration:none !important; padding: 1px;
	display:block;line-height:16px;font-size:12px;font-weight:normal;
	-webkit-transition: all .15s ease-in-out;
	-moz-transition: all .15s ease-in-out;
	-o-transition: all .15s ease-in-out;
	transition: all .15s ease-in-out;
	}
.delete a:hover {background:#D94136 !important; color:#fff !important; text-decoration:none !important;padding: 1px;line-height:16px;display:block;font-size:12px;font-weight:normal; }

a.cancel:link, a.cancel:visited { 
	font-weight:100; color:#D94136 !important;text-decoration:underline;
	padding: 1px 3px;background:none !important;line-height:16px;
	-webkit-transition: all .15s ease-in-out;
	-moz-transition: all .15s ease-in-out;
	-o-transition: all .15s ease-in-out;
	transition: all .15s ease-in-out;
	}
a.cancel:hover { font-weight:100; background:#D94136 !important; color:#fff !important;text-decoration:none !important;padding: 1px 3px;line-height:16px;}
a.cancel em {font-style:normal}

.wrapper .secondarylink {width:20px;text-align:center;font-size:12px;line-height:14px;}
.wrapper .secondarylink a:link, .wrapper .secondarylink a:visited {
	color:#aaa;text-decoration:none;	font-weight:normal;
	padding: 1px;display:block;line-height:16px;
	-webkit-transition: all .15s ease-in-out;
	-moz-transition: all .15s ease-in-out;
	-o-transition: all .15s ease-in-out;
	transition: all .15s ease-in-out;
	}
.wrapper .secondarylink a:hover {background:<?php echo $primary_3; ?>;color:#FFF;text-decoration:none;font-weight:normal;padding: 1px;display:block;line-height:16px;}


/* backup info display */
table.simple td {border:1px solid #eee;border-collapse:collapse;color:#555;font-size:12px;padding:4px 10px 4px 4px;}
table.simple {width:100%;border:1px solid #aaa;}


/* footer */
#footer {
	border-top:1px solid #ccc;
	margin:40px 0 0 0;
	padding:10px 0;
	font-size:11px;
	color:#777;
}
#footer p {margin:0 0 8px 0;}
#footer a {font-weight:100;}	
#footer .gslogo a {
float:right;width:60px;text-align:right;
opacity:.10;	
}
#footer .footer-left {float:left;width:85% }
#footer .gslogo a:link,#footer .gslogo a:visited  {
	
	-webkit-transition: opacity .3s ease-in-out;
	-moz-transition: opacity .3s ease-in-out;
	-o-transition: opacity .3s ease-in-out;
	transition: opacity .3s ease-in-out;
}
#footer .gslogo a:hover  {opacity:1}
.toggle {display:none;}

.editable {
	padding: 3px 1px; 
	cursor:pointer;
	-webkit-transition: background-color .3s ease-in-out;
	-moz-transition: background-color .3s ease-in-out;
	-o-transition: background-color .3s ease-in-out;
	transition: background-color .3s ease-in-out;
	}
.editable:hover { background:#FCFBB8; }

.wrapper table td span.ERRmsg {color:#D94136;font-color:12px;}
.wrapper table td span.OKmsg {color:#308000;font-color:12px;}
.wrapper table td span.WARNmsg {color:#FFCC33;font-color:12px;}
.wrapper table.highlight tr.img-highlight {
	background:#FFFFD1 !important;
}
.wrapper table.highlight tr.img-highlight td a.primarylink {
	font-weight:bold !important;
}

#sidebar .uploadform {
	padding:5px 15px;
}

/* JQuery Uploadify Styles */
.uploadifyQueueItem {
	font-size: 10px;
	padding:8px 15px;
	width:190px;
}
.uploadifyError {
	color: #D94136 !important;
}
.uploadifyError .uploadifyProgressBar {
	background-color: #D94136 !important;
}
#sidebar .snav li .cancel {float:right;}
#sidebar .snav li .cancel a:link,
#sidebar .snav li .cancel a:visited,
#sidebar .snav li .cancel a:hover {
	padding:0 !important;
	margin:0 4px 0 0 !important;
	width:11px !important;
	opacity:.8;
	background: transaparent !important;
}
.uploadifyProgress {
	background-color: #FFF;
	margin-top: 5px;
	width: 97%;
}
.uploadifyProgressBar {
	background-color: <?php echo $primary_6; ?>;
	width: 1px;
	height: 4px;
}
#sidebar .snav li.upload {
	display:block;
	border-radius: 4px;
	-moz-border-radius: 4px;
	-khtml-border-radius: 4px;
	-webkit-border-radius: 4px;
	margin-left:13px;
	color:#FFF;
	background:<?php echo $primary_1; ?>;
	font-weight:100;
}
.uploadifyButton {
	width:100%;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	display: block;
	font-weight: 800;
	color: #AFC5CF;
	background: #182227;
	text-shadow: 1px 1px 0px #0E1316;
	-webkit-transition: all .3s ease-in-out;
	-moz-transition: all .3s ease-in-out;
	-o-transition: all .3s ease-in-out;
	transition: all .3s ease-in-out;
}
.uploadify:hover .uploadifyButton {
	background-color: #0e1316;
	color: #ffffff;
	text-shadow: 1px 1px 0px #000;
}
.uploadifyButtonText{
	padding: 5px 15px 5px 15px;
	display: block;
}
#uploadify object { 
	position:absolute;
	left:0; right:0;
	cursor: pointer;
}


/* Image Editor Styles */
textarea.copykit {
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace; 
	font-size:12px;
	outline:none
	color:#666;
	border:1px solid #aaa;
	line-height:17px;
	padding:4px;
	border-radius: 2px;
	-moz-border-radius: 2px;
	-khtml-border-radius: 2px;
	-webkit-border-radius: 2px;
	width:98%;
	height:70px;
	margin-bottom:10px;
}
#handw {z-index:1;padding:8px;background:#000;opacity:.80;color:#fff;font-size:11px;width:150px;text-align:center;margin:-50px 0 0 0;}
#handw span {font-size:15px;font-weight:bold;}
#jcropform .submit {margin:20px 0 0 0;}
#jcrop_open {}


/* jQuery Pagination Styles */
.qc_pager {padding:0 0 15px 0;}
.qc_pager a {padding:5px;margin:0 10px 0 0;}
.qc_pager a:link,	.qc_pager a:visited {}
.qc_pager a:hover, .qc_pager a:focus {}
.qc_pager .qp_counter {margin:0 10px 0 0;font-size:11px;}
.qc_pager a.qp_disabled:link,
.qc_pager a.qp_disabled:visited,
.qc_pager a.qp_disabled:hover,
.qc_pager a.qp_disabled:focus {color:#ccc;cursor:text !important;}

/* File Browser Styles */
#filebrowser {background:#fff;}

/* Logged out specific styles */
#index .header,
#resetpassword .header {display:none;}
#index #maincontent,
#resetpassword #maincontent {width:100%;}
#index #maincontent .main,
#resetpassword #maincontent .main {margin:50px auto 0 auto;width:270px;float:none;text-align:left;
	border-radius: 8px;
	-moz-border-radius: 8px;
	-khtml-border-radius: 8px;
	-webkit-border-radius: 8px;
	box-shadow: rgba(0,0,0, 0.2) 0px 0px 8px;  
	-moz-box-shadow: rgba(0,0,0, 0.2) 0px 0px 8px;  
	-webkit-box-shadow: rgba(0,0,0, 0.2) 0px 0px 8px;
}
#index p.cta,
#resetpassword p.cta {font-size:11px;margin:0 0 0 0;color:#999;}
#index p.cta a,
#resetpassword p.cta a {font-weight:100;}
#index .error, #index .updated,
#resetpassword .error, #resetpassword .updated {margin:30px auto -20px auto;width:400px;line-height:18px;padding:5px 10px;}
#index #footer,
#resetpassword #footer {width:270px;border-top:none;margin:0 auto 20px auto;text-align:center;opacity:.7}
#index .footer-left,
#resetpassword .footer-left  {float:none;width:100%;}
#index .gslogo,
#resetpassword .gslogo {display:none;}
.desc {font-size:12px;line-height:17px;border-bottom:1px dotted #ccc;padding:0 0 15px 0;margin:0 0 5px 0;}

#filter-search  {margin:0 0 10px 0;display:none;}
#filter-search input.text {width:250px;font-size:11px;padding:3px;}

#createfolder {font-weight:100;font-size:11px;text-decoration:underline !important;}
h5 .crumbs, div.h5 .crumbs {float:left;}
#new-folder {float:left;padding-left:5px;}
#new-folder form {display:none;}
#new-folder .cancel {font-size:11px;text-shadow:none !important;}
#new-folder input.submit {font-size:11px;padding:3px;}
#new-folder input.text {width:120px;font-size:11px;padding:3px;}

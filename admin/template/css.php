<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

/** CSS **/

/**
 * CSS php dynamic
 * DO NOT TIDY!
 *
 * @uses $primary_0 - $primary_6
 * @uses $secondary_0 - $secondary_1
 * @uses $label_0 - $label_6
 *
 */

/* <style> */

/** GLOBAL RESETS **/
:link,:visited {
	text-decoration: none}

h1,h2,h3,h4,h5,h6,pre,code {
	font-size: 1em;
	font-weight: 400;
	word-wrap: break-word;
}

ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,body,html,p,blockquote,fieldset,input {
	margin: 0;
	padding: 0;
}

body {
	height: 100%;
	font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
	font-size: 12px;
	background: #f6f6f6;
}

a {
	color: <?php echo $primary_3; ?>;
	text-decoration: none;
	font-weight: bold;
}

a:hover {
	text-decoration: underline;
	font-weight: bold;
}

a img {
	border: none;
}

/* @todo all these modifier classe need to go togather probably at the end */

.boxsizingBorder {
    -webkit-box-sizing: border-box;
       -moz-box-sizing: border-box;
            box-sizing: border-box;
}

.clear {
	clear: both;
}

.unformatted {
	white-space: pre;
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace;
	line-height: 15px;
	font-size: 11px;
	display: block;
	padding-bottom: 15px;
	color: #555;
	overflow: auto;
}

#help {
	display: none;
}

.imgthumb {
	display: none;
	width: 70px;
}

.imgthumb img {
	border: 1px solid #555;
}

.hidden {
	display: none !important;
}

html {
	overflow-y: scroll;
}

.clearfix:before, .clearfix:after {
	content: "\0020";
	display: block;
	height: 0;
	visibility: hidden;
}

.clearfix:after {
	clear: both;
}

.clearfix {
	zoom: 1;
}

/* @todo annotate these */
::selection {
	text-shadow: none !important;
	background: #a8d1ff;
	color: #111;
}

::-moz-selection {
	text-shadow: none !important;
	background: #a8d1ff;
	color: #111;
}

::-webkit-input-placeholder {
	color: #c3c3c3;
}

:-moz-placeholder {
	color: #c3c3c3;
}

/** HEADER / NAVIGATION **/
.header {
	color: #FFF;
	border-top: 1px solid <?php echo $primary_1; ?>;
	background: <?php echo $header_base; ?>;	/* old browsers */
	margin: 0 0 25px 0;
}

.header.gradient{
		background: -moz-linear-gradient(top, <?php echo $primary_4; ?> 0%, <?php echo $primary_2; ?> 100%);	 /* firefox */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,<?php echo $primary_4; ?>), color-stop(100%,<?php echo $primary_2; ?>));	/* webkit */
		filter: progid: DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $primary_4; ?>', endColorstr='<?php echo $primary_2; ?>',GradientType=0 );	/* ie */
}

.header .wrapper {
	height: 95px;
	position: relative;
	border: none;
}

.wrapper .nav {
	list-style: none;
	font-size: 13px;
	position: absolute;
	bottom: 0;
	left: 0;
	width: 960px;
}

.wrapper .nav li a {
	padding: 7px 13px;
	font-weight: 100 !important;
	text-decoration: none !important;
	display: block;
	border-radius: 5px 5px 0 0;
}

/* @todo noooooo */
.wrapper .nav li a:link,
.wrapper .nav li a:visited,
.wrapper #pill li a:link,
.wrapper #pill li a:visited {
	color: <?php echo $primary_6; ?>;
	background: <?php echo $primary_1; ?>;
	text-shadow: 1px 1px 0 rgba(0,0,0,.3);
}

/* @todo noooooo */
.wrapper #pill li.debug a:link,
.wrapper #pill li.debug a:visited,
.wrapper #pill li.debug a:hover {
	color: #fff;
	background: #cc0000;
	padding: 4px 10px;
	font-weight: 700 !important;
	text-decoration: none !important;
	display: block;
	border-left: 1px solid <?php echo $primary_3; ?>;
}

/* @todo: noooooo */
#edit .wrapper .nav li a.pages,
#pages .wrapper .nav li a.pages,
#menu-manager .wrapper .nav li a.pages,
#plugins .wrapper .nav li a.plugins,
#settings .wrapper .nav li a.settings,
#components .wrapper .nav li a.theme,
#theme .wrapper .nav li a.theme,
#sitemap .wrapper .nav li a.theme,
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
#load	.wrapper .support li a.support,
#load .wrapper .nav li a.current,
#loadtab .wrapper .nav li a.current {
	color: <?php echo $primary_1; ?>;
	background: #f6f6f6;
		background: -moz-linear-gradient(top, #FFF 3%, #F6F6F6 100%);	/* firefox */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(3%,#FFF), color-stop(100%,#F6F6F6));	/* webkit */
	font-weight: bold !important;
	text-shadow: 1px 1px 0 rgba(255,244,255,.2);
		-moz-box-shadow: rgba(0,0,0, 0.10) 2px -2px 2px;
		-webkit-box-shadow: rgba(0,0,0, 0.10) 2px -2px 2px;
	box-shadow: rgba(0,0,0, 0.10) 2px -2px 2px;
}

.wrapper .nav li a:active,
.wrapper .nav li a:focus,
.wrapper .nav li a:hover,
.wrapper #pill li a:hover,
.wrapper #pill li a:focus {
	color: #FFF;
	background: <?php echo $primary_0; ?>;
	text-shadow: 1px 1px 0 rgba(0,0,0,.4);
}

.wrapper .nav li {
	float: left;
	margin: 0 8px 0 0;
	position: relative;
}

.wrapper .nav li.rightnav {
	float: right;
	margin: 0 0 0 0;
	font-size: 11px;
 }

.wrapper .nav li.rightnav a.first {
	padding: 4px 10px;
	font-weight: 100 !important;
	text-decoration: none !important;
	display: block;
	border-radius: 0 3px 3px 0;
	border-left: 1px solid <?php echo $primary_3; ?>;
}

.wrapper .nav li.rightnav a.last {
	padding: 4px 10px;
	font-weight: 100;
	text-decoration: none !important;
	display: block;
	border-radius: 3px 0 0 3px;
	margin-left:4px;
}

.wrapper .nav li.rightnav a.label {
	padding: 3px 4px;
	background-color: none;
	display: inline-block;
	/*margin:0px 0px;*/
	border-radius:3px !important;
	color: #FFFFFF;
	border: 1px solid rgba(0,0,0,0.5);
	        background-clip: padding-box;
       -moz-background-clip: padding;
    -webkit-background-clip: padding;

            box-sizing: border-box;
       -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
}

.wrapper .nav li.rightnav a.label:hover {
	border: 1px solid rgba(0,0,0,0.8);
	box-shadow: rgba(0,0,0, 0.3) 0 0 4px;
	opacity:.9;
	transition: all;
}

.wrapper .nav li.rightnav a.label.label-ghost {
	background-color: rgba(0, 0, 0, 0.3) !important;
}

/*
 * DEPRECATED
 * rightnav status badges
 * .warning
 * .info
*/
.nav li .warning, .nav li .info {
	/*position: absolute;*/
	/*top: -5px;*/
	/*right: -5px;*/
	font-size: 15px;
	color: #000;
	text-shadow: 1px 1px 0 rgba(255,255,255,.5);
	font-weight: bold;
	text-align: center;
	border-radius: 2px;
	display: block;
	height: 16px;
	width: 16px;
	margin:2px 6px;
	border: 1px solid #FFCC33;
	background: #FFFF66;
	background: -moz-linear-gradient(top, #FFFF66 0%, #FFCC33 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#FFFFFF), color-stop(100%,#FFCC33));
	opacity:.9;
}

.nav li .info {
	font-family: serif;
	border: 1px solid #BDBDBD;
	background: #C7C7C7;
	background: -moz-linear-gradient(top, #BDF2FF 0%, #6FCCFF 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#F7F7F7), color-stop(100%,#999));
}

.wrapper .nav li a em, .wrapper #pill li a em {
	font-style: normal;
}

.wrapper .nav li a:hover em, .wrapper .nav li a:focus em {
	border-bottom: 1px dotted #666;
}

#nav_loaderimg {
	width: 30px;
	padding: 13px 7px;
	-webkit-transition: none;
}

.wrapper #pill {
	list-style: none;
	position: absolute;
	top: 0;
	right: 0;
	font-size: 11px;
}

.wrapper #pill li {
	float: right;
}

.wrapper #pill li.leftnav a {
	padding: 4px 10px;
	font-weight: 100 !important;
	text-decoration: none !important;
	display: block;
	border-radius: 0 0 3px 0;
	border-left: 1px solid <?php echo $primary_3; ?>;
}

.wrapper #pill li.rightnav a {
	padding: 4px 10px;
	font-weight: 100 !important;
	text-decoration: none !important;
	display: block;
	border-radius: 0 0 0 3px;
}

.wrapper {
	margin-left: auto;
	margin-right: auto;
	width: 960px;
	text-align: left;
	padding-top: 1px;
}

.wrapper p {
	line-height: 18px;
	margin: 0 0 20px 0;
}

.wrapper #maincontent ul,
.wrapper #maincontent ol {
/*	line-height: 18px;
	margin: 0 0 20px 30px;*/
}

.inner {
	padding: 20px;
}

.header h1 {
	font-size: 25px;
	font-family: Georgia, Times, Times New Roman, serif;
	position: absolute;
	text-shadow: 1px 1px 0 <?php echo $primary_2; ?>;
	top: 17px;
	left: 0;
}

.header h1 a:link,
.header h1 a:visited,
.header h1 a:hover {
	font-weight: normal;
	color: <?php echo $primary_5; ?>;
	text-decoration: none;
}

.header h1 a:hover  {
	color: #FFF;
}

.wrapper h2 {
	font-size: 18px;
	font-family: Georgia, Times, Times New Roman, serif;
	color: #777;
	margin: 0 0 20px 0;
}

.wrapper h2 span {
	color: #bbb;
	font-style: italic;
}

h3 {
	font-size: 19px;
	font-family: Georgia, Times, Times New Roman, serif;
	font-weight: normal;
	font-style: italic;
	color: <?php echo $secondary_1; ?>;
	margin: 0 0 20px 0;
	text-shadow: 1px 1px 0 #fff;
}

h3.floated {
	font-size: 18px;
	font-weight: normal;
	font-family: Georgia, Times, Times New Roman, serif ;
	padding: 2px 0 0 0;
	color: <?php echo $secondary_1; ?>;
	float: left;
	display: block;
	margin: 0 0 20px 0;
}

h3 span {
	color: #999;
	font-size: 14px;
}

h5,
div.h5 {
	margin: 10px 0 10px 0;
	font-size: 14px;
	line-height: 28px;
	display: block;
	padding: 3px 10px;
	background: #EEEEEE;
		background: -moz-linear-gradient(top, #f6f6f6 3%, #EEEEEE 100%);	/* firefox */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(3%,#f6f6f6), color-stop(100%,#EEEEEE));	/* webkit */
	border: 1px solid #cccccc;
	text-shadow: 1px 1px 0 rgba(255,255,255,0.5);
	color: #999;
}

h5 a {
	text-decoration: none !important;
}

h5 img,
tr.folder img {
	vertical-align: middle;
	margin: 0 5px 0 0;
	opacity: .5;
}

h5:hover img {
	opacity: 1;
}

.bodycontent ul,
.bodycontent ol {
	margin: 0 0 20px 30px;
}

.bodycontent ul p,
.bodycontent ol p {
	margin: 0 0 10px 0;
}

#maincontent {
	width: 710px;
	float: left;
	text-align: left;
}

#sidebar {
	width: 225px;
	float: right;
}

#sidebar.fixed{
	-transition: top .02s ease-out;
		-webkit-transition: top .2s ease-in-out;
		-moz-transition: top .2s ease-in-out;
		-o-transition: top .2s ease-in-out;
}

#sidebar .section {
	background: #fff;
	border: 1px solid #ccc;
	padding: 20px;
	margin: 0 0 30px 0;
	line-height: 18px;
}

#sidebar .section p.small {
	font-size: 11px;
	margin: 15px 0 0 0;
}

#sidebar .section input.text {
	width: 175px;
	font-size: 11px;
	padding: 4px;
	border: 1px solid #666;
}

#sidebar .snav {
	list-style: none;
	margin: 0 0 30px 0;
}

#sidebar .snav ul {
	list-style: none;
	margin: 0;
}

#sidebar .snav li {
	margin: 0 0 3px 0;
}

#sidebar .snav li ul li {
	margin: 0 0 3px 0;
}

#submit_line {
	margin: 15px 0 15px 0;
}

#sidebar #js_submit_line {
	margin: 0 0 0 12px;
}

#sidebar .snav li a {
	font-weight: bold;
	display: block;
	padding: 5px 15px 5px 15px;
	text-decoration: none;
	border-radius: 3px;
}

#sidebar .snav li a:link,
#sidebar .snav li a:visited {
	margin-left: 13px;
	color: <?php echo $primary_6; ?>;
	background: <?php echo $primary_1; ?>;
	text-shadow: 1px 1px 0 <?php echo $primary_0; ?>;
	transition: all .2s ease-in-out;
		-webkit-transition: all .2s ease-in-out;
		-moz-transition: all .2s ease-in-out;
		-o-transition: all .2s ease-in-out;
}

#sidebar .snav li a.current {
    position: relative;
	cursor: default;
	color: #FFF;
	text-shadow: 1px 1px 0 <?php echo $secondary_0; ?>;
	border-radius: 0 3px 3px 0;
	height:14px;
    background-color:<?php echo $secondary_1; ?>;
    margin-left:13px;
    padding-left:15px;
}

#sidebar .snav li a.current:hover {
    background-color: <?php echo $secondary_1; ?>;
}

/* sidebar current arrow */
#sidebar .snav li a.current:after {
    right: 100%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    /*pointer-events: none;*/
}
#sidebar .snav li a.current:after {
    border-color: rgba(207, 56, 5, 0);
    border-right-color: <?php echo $secondary_1; ?>;
    border-width: 12px;
    top: 50%;
    margin-top: -12px;
}
/**/

#sidebar .snav li a:hover {
	color: #FFF;
	background: <?php echo $primary_0; ?>;
	margin-left: 13px;
	text-shadow: 1px 1px 0 rgba(0,0,0,.25);
}

#sidebar .snav li a em  {
	font-style: normal;
}

#sidebar .snav li a:hover em,
#sidebar .snav li a:focus em {
	border-bottom: 1px dotted #666;
}

#sidebar .snav li a.current:hover em,
#sidebar .snav li a.current:focus em  {
	border-bottom: 1px dotted #fff;
}

#sidebar .snav small {
	color: #666;
}

/* sidebar plugins seperator */
#sidebar .snav li.last_sb + li.plugin_sb:before, #sidebar hr
{
	margin: 4px 3px 4px 16px;
	border:none;
	border-bottom: 1px solid <?php echo $primary_0; ?>;
	content: "";
	display: block;
	border-width: thin;
	opacity: 0.2;
}
/**/

/**
 * .main top action nav links
 */
.edit-nav {
	/*margin: 0 0 10px 0;*/
	margin: 0;
}

.edit-nav a {
	font-size: 10px;
	text-transform: uppercase;
	display: block;
	padding: 4px 10px;
	float: right;
	margin: 0 0 0 5px;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
	border-radius: 3px;
	background-repeat: no-repeat;
	background-position: 94% center;
}

.edit-nav select {
	margin-top: 0;
	float: right;
	padding: 2px;
	border: 1px solid #999;
	font-size: 11px;
		-moz-border-radius: 2px;
		-webkit-border-radius: 2px;
	border-radius: 2px;
}

.edit-nav p {
	float: right;
	font-size: 11px;
	margin: 0;
}

/*not used*/
.edit-nav label {
/*	font-weight: 100;
	display: inline;
	font-size: 11px;
	color: #666;
	padding: 0;*/

	font-size: 12px;
	color: #BBB;
	margin: 0 3px;
	line-height: 22px;
	float: right;
	font-weight: normal;
}

/*.edit-nav a#metadata_toggle {
	background-image: url('images/plus.png');
	padding-right: 20px;
}

.edit-nav a#metadata_toggle.current {
	background-image: url('images/minus.png');
	 padding-right: 20px;
}*/

.edit-nav {
	height: 1%;
}

.edit-nav a:link,
.edit-nav a:visited {
	line-height: 14px;
	background-color: <?php echo $primary_1; ?>;
	color: <?php echo $primary_6; ?>;
	font-weight: bold;
	text-decoration: none;
	text-shadow: 1px 1px 0 rgba(0,0,0,.2);
	transition: all .10s ease-in-out;
		-webkit-transition: all .10s ease-in-out;
		-moz-transition: all .10s ease-in-out;
		-o-transition: all .10s ease-in-out;
}

/* what is this for ? edit-nav in sidebar ? */
.edit-nav a:hover,
#sidebar .edit-nav a:hover,
.edit-nav a.current {
	background-color: <?php echo $secondary_1; ?>;
	color: #FFF;
	font-weight: bold;
	text-decoration: none;
	line-height: 14px;
	text-shadow: 1px 1px 0 rgba(0,0,0,.2);
}

/* shortcut letter highlights */
.edit-nav a:link em,
.edit-nav a:visited em {
	font-style: normal;
}

.edit-nav a.current em,
.edit-nav a:hover em,
.edit-nav a:focus em {
	font-style: normal;
	border-bottom: 1px dotted #FFF;
}

/* basic default table style */
.wrapper table {
	border-collapse: collapse;
	margin: 0 0 20px 0;
	width: 668px;
}

.wrapper table tbody tr {
	line-height:20px;
	font-size: 12px;
	color: #777;
	border-bottom: 1px solid #eee;
	border-top: 1px solid #eee;
	vertical-align: top;
	/*line-height: 20px !important;*/
	color: #777;
}

.wrapper table td {
	padding: 4px;
	vertical-align: top;
	/*line-height: 20px !important;*/
}

.wrapper table th {
	/*background: #FFF !important;*/
	padding: 2px 4px;
	font-size: 11px;
	/*border-top: 1px solid #FFF;*/
	color: #222;
	font-weight: bold;
	text-transform: uppercase;
	line-height: 20px;
	text-align: left;
}

.wrapper table td span {
	/*font-size: 12px;*/
	/*color: #777;*/
}

.wrapper table.highlight tbody{
	text-shadow: 1px 1px 0 #fff;
}

.wrapper table.highlight tbody tr:nth-child(odd), .wrapper table.striped tbody tr:nth-child(odd) {
	background: #f7f7f7;
}

.wrapper table.striped tbody tr.odd {
	background: #f7f7f7;
}
.wrapper table.striped tbody tr.even {
	background: #FFF;
}

.wrapper table tr#tr-index a {
	 font-weight: bold;
}

.wrapper table.highlight tbody tr:hover {
	background: #FFFFD5 !important;
	text-shadow: none;
}

.wrapper table.highlight tbody tr.nohighlight:hover {
	background: none !important;
}

.wrapper table tr.currentpage{
	 background: #FFFFD1;
}

.wrapper table tr {
	/*border-bottom: 1px solid #eee;*/
	/*border-top: 1px solid #eee;*/
/*	transition: background-color .3s ease-in-out;
		-webkit-transition: background-color .3s ease-in-out;
		-moz-transition: background-color .3s ease-in-out;
		-o-transition: background-color .3s ease-in-out;*/
}

.wrapper table tr.attention{
	background: #FFCCC8 !important;
	font-weight:bold;
	text-shadow: none;
}

.wrapper table td a:link, .wrapper table td a:visited {
	font-weight: normal;
}

.wrapper table.healthcheck tr {
	font-size: 12px;
	color: #000;
}

/*
 fix for firefox row heighs inheritance for positioning children, specifically absolute children
 */
tr, td.status {
	height: 100%;
}

/* popup does not appear to be in use */
.popup table td {
	padding: 4px;
}

.popup table a:link,
.popup table a:visited {
	color: <?php echo $primary_3; ?>;
	text-decoration: underline;
}

.popup table a:hover {
	color: #333;
	text-decoration: underline;
}

sup {
	color: #666;
	font-weight: 100 !important;
	vertical-align: baseline;
	font-size: 0.8em;
	position: relative;
	top: -0.4em;

	vertical-align: baseline;
	font-size: .8em;
	position: inherit;
	top: -0.4em;
	background: #E7E7E7;
	border-radius: 3px;
	text-shadow: none;
	padding: 3px 6px;
	font-weight: bold;
}


/* default form css */
#maincontent .main {
	padding: 20px;
	background: #fff;
	border: 1px solid #c8c8c8;
	margin: 0 0 30px 0;
		-moz-box-shadow: rgba(0,0,0, 0.06) 0 0 4px;
		-webkit-box-shadow: rgba(0,0,0, 0.06) 0 0 4px;
	box-shadow: rgba(0,0,0, 0.06) 0 0 4px;
}

#maincontent .main .section {
	padding-top: 40px;
}

#themecontent{
	/* replaces #maincontent on theme editor page, fix for the above style interferring with codemirror for now */
	padding: 20px;
	background: white;
	border: 1px solid #C8C8C8;
	margin: 0 0 30px 0;
		-moz-box-shadow: rgba(0,0,0, 0.06) 0 0 4px;
		-webkit-box-shadow: rgba(0,0,0, 0.06) 0 0 4px;
	box-shadow: rgba(0,0,0, 0.06) 0 0 4px;
	border-image: initial;
}

form p {
	margin: 0 0 10px 0;
}

form input.text,
form select.text {
	color: #333;
	border: 1px solid #aaa;
	padding: 3px;
	font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
	font-size: 11px;
	/*width: 510px;*/
	width:100%;
	border-radius: 2px;
    -moz-box-sizing: content-box;
    -webkit-box-sizing: content-box;
    box-sizing: content-box;
}

form input.text:focus,
form select.text:focus,
form textarea.text:focus,
#post-content:focus {
	outline: none;
	border: 1px solid #666 !important;
		-moz-box-shadow: rgba(0,0,0, 0.10) 0 0 6px;
		-webkit-box-shadow: rgba(0,0,0, 0.10) 0 0 6px;
	box-shadow: rgba(0,0,0, 0.10) 0 0 6px;
}

form textarea {
	outline: none;
	width: 100%;
	height: 420px;
	line-height: 15px;
	text-align: left;
	color: #333;
	border: 1px solid #aaa;
	padding: 3px;
	font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
	font-size: 11px;
	border-radius: 2px;
	tab-size:4;
	 -moz-tab-size:4;
	 -o-tab-size:4;
}

textarea.short {
	height:62px;
}

form input[readonly], form select[readonly], form textarea[readonly] {
	background: #eeeeee;
	border: 1px solid #999;
	color: #666;
	cursor: default;
}

form input.capslock {
	background-image: url('images/capslock.png');
	background-position: right center;
	background-repeat: no-repeat;
}

textarea#codetext {
	height: 660px;
	width: 99%;
	font-family: Consolas, Monaco, "Courier New", Courier, monospace;
	font-size: 13px;
	line-height: 13px;
	overflow: scroll;
	overflow-y: scroll;
	overflow-x: scroll;
}

#menu-items span {
	text-transform: lowercase;
}

label {
	padding: 0;
	margin: 0;
	color: #222;
	display: block;
	font-size: 12px;
	font-weight: bold;
	font-family: arial, helvetica, sans-serif
}

label span.right {
	float: right;
	color: #999;
	display: block;
	margin-right: 0;
	font-weight: 100 !important;
	font-size: 11px !important;
}

label span.right a:link,
label span.right a:visited {
	font-size: 11px;
	color: #999;
	text-decoration: none;
	font-weight: 100 !important;
}

label span.right a:focus,
label span.right a:hover {
	font-size: 11px;
	color: #333;
	text-decoration: underline;
	font-weight: 100 !important;
}

.inline label {
	display: inline-block;
	padding-top: 3px;
}

label.checkbox {
	width: 100px;
	margin-right: 3px;
	text-align: right;
	font-weight: normal;
}

/* meta dropdown style */
#metadata_window,fieldset {
	margin: 0 2px 0 0;
	background: #f9f9f9;
	border: 1px solid #e8e8e8;
	padding: 15px 10px 5px 10px;
	border-radius: 2px;
	text-shadow: 1px 1px 0 rgba(255,255,255,.3);
}

.rightopt {
	float: right;
	width: 48%;
}

.leftopt {
	float: left;
	width: 48%;
}

.wideopt {
	clear:both;
	width: 100%;
}

.leftopt p,.rightopt p,.wideopt p{
	margin:0 10px 15px 0;
}

.leftopt input,.rightopt input,.wideopt input{
	margin:0;
}

.leftopt select,.rightopt select,.wideopt select{
	margin:0;
}

input#post-menu-enable {
	width: 20px;
	padding: 0;
	margin: 0;
}

select.text.autowidth {
	width: 155px;
	float: right;
}

p.post-menu {
	margin-bottom: 5px;
}

a.viewlink img {
	vertical-align: baseline;
	margin-left: 15px;
	opacity: .5;
}

a.viewlink:hover img {
	opacity: 1;
}

#menu-items {
	height: 50px;
	background: #222;
	padding: 5px 18px 0 10px;
	position: relative;
	border-radius: 2px;
	margin-bottom:5px;
}

#menu-items #tick {
	margin-top: -10px;
	left: 5px;
	position: absolute;
}

#menu-items input,
#menu-items select {
	border: 1px solid #000;
	padding:3px;
}

#menu-items span label {
	text-shadow: none;
	display: inline-block;
	font-size: 11px;
	line-height: 16px;
	color: #e3e3e3;
	font-weight: normal;
	margin: 0;
	padding: 0;
}

#menu-items select {
	padding: 2px 3px;
	/*margin-left:13px;*/
}

.countdownwrap {
	display: block;
	color: #999;
	font-size: 11px;
	font-weight: normal;
	float: right;
}

.maxchars {
	color:#C00;
}

form table.formtable select {
	width: 275px;
	padding: 3px 4px;
}

table.cleantable {
	border-collapse: collapse;
	margin: 0 0 0 0;
}

table.cleantable tr {
	border: none;
}

table.cleantable tr td {
	border: none;
}

#autosavenotify {
	padding: 15px 0 0 13px;
	color: #666;
	text-shadow: 1px 1px 0 #fff;
	font-size: 11px;
}

#pagechangednotify {
	margin: 0;
	padding: 15px 0 0 13px;
	color: #CC0000;
	text-shadow: 1px 1px 0 #fff;
	font-size: 11px;
}

p.backuplink {
	text-shadow: 1px 1px 0 rgba(255,255,255,.3);
	color: #888;
	font-size: 11px;
	margin: 20px -20px -20px -20px;
	padding: 10px 8px 10px 40px;
	background: #f9f9f9 url('images/clock.png') 20px center no-repeat;
	border-top: 1px solid #eee;
}

p.backuplink a {
	font-weight: 100 !important;
}

.editing {
	font-size: 10px;
	padding: 3px;
	display: block;
	/*margin-top: -13px;*/
	margin-bottom: -10px;
	color: #888;
	font-style: italic;
}

/* form submit button style */
/*
input.submit {
	padding: 5px 12px;
	font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
	font-weight: bold;
	cursor: pointer;
}*/

input.submit, .button {
	font: bold 13px Helvetica, Arial, sans-serif;
	text-decoration: none !important;
	padding: 7px 15px;
	text-shadow: 0 1px 0 rgba(255,255,255,.5);
	transition: all .218s;
		-webkit-transition: all .218s;
		-moz-transition: all .218s;
		-o-transition: all .218s;
	color: #333333;
	background: #dddddd;
		background: -webkit-gradient(linear,0% 40%,0% 70%,from(#eeeeee),to(#e1e1e1));
		background: -moz-linear-gradient(linear,0% 40%,0% 70%,from(#eeeeee),to(#e1e1e1));
	border: solid 1px #acacac;
		-webkit-border-radius: 2px;
		-moz-border-radius: 2px;
	border-radius: 2px;
	cursor: pointer;
		-moz-box-shadow: rgba(0,0,0, 0.06) 0 0 3px;
		-webkit-box-shadow: rgba(0,0,0, 0.06) 0 0 3px;
	box-shadow: rgba(0,0,0, 0.06) 0 0 3px;
}

input.submit:focus, input.submit:hover, .button:hover {
	color: #111111;
	background: #eeeeee;
		background: -webkit-gradient(linear,0% 40%,0% 70%,from(#eeeeee),to(#dddddd));
		background: -moz-linear-gradient(linear,0% 40%,0% 70%,from(#eeeeee),to(#dddddd));
	border: solid 1px #aaaaaa;
		-moz-box-shadow: rgba(0,0,0, 0.15) 0 0 4px;
		-webkit-box-shadow: rgba(0,0,0, 0.15) 0 0 4px;
	box-shadow: rgba(0,0,0, 0.15) 0 0 4px;
}

.button {
	padding: 3px 7px;
}

.leftsec,.rightsec {
	float: left;
	width: 50%;
	padding-bottom: 5px;
}

.widesec {
	clear: both;
	width: 100%;
	padding-bottom: 5px;
}

.widesec input.text, .rightsec input.text, .leftsec input.text {
	width: 100%;
	font-size:12px !important;
}

.widesec input.text, .rightsec select.text, .leftsec select.text {
	width: 100%;
	font-size:12px !important;
}

.leftsec p,.rightsec p,.widesec p {
	margin: 0 20px 20px 0;
}

/* edit css */
form input.title {
	font-size: 19px;
	border-color: #999;
	width: 655px;
	padding: 3px 4px;
}

form input.secondary {
	width: 280px;
}

/* components css */
form.manyinputs input.text {
	width: 230px;
}

form.manyinputs textarea {
	width: 632px;
	height: 200px;
	font-size:12px !important;
	font-family: Consolas, Monaco, Menlo, 'Ubuntu Mono', 'Droid Sans Mono', monospace !important;
}

form.manyinputs p {
	margin: 0 0 25px 0;
}

.compdiv {
	padding: 10px 0 35px 0;
}

table.comptable {
	margin: 0 !important;
	/*width: 645px;*/
	/*background: #fff;*/
	border: none;
	padding: 0;
}

table.comptable tr td {
	font-size: 12px;
	border: none;
	padding: 0;
}

table.comptable tr td code {
	font-size: 11px;
	color: #666;
	padding: 0 4px 0 0;
	display: block;
	font-family: Consolas, Monaco, "Courier New", Courier, monospace;
}

table.comptable tr {
	border: none !important;
}

table.comptable tr td input.newtitle {
	margin-bottom: 2px !important;
	font-size: 12px;
	font-weight: bold;
}

table.comptable .comptitle {
	color: #000;
}

table.comptable label {
	display: inline-block;
	margin-right: 3px;
	color: #777;
}

/* @todo more wrappers */
.wrapper a.component {
	float: left;
	font-weight: bold;
	margin: 0 5px 5px 0;
	padding: 3px 10px;
	text-decoration: none;
		-moz-border-radius: 3px;
		-khtml-border-radius: 3px;
		-webkit-border-radius: 3px;
	border-radius: 3px;
}

.wrapper a.component:link, .wrapper a.component:visited {
	color: #666;
	background: #fff;
	border: 1px solid #999;
	text-decoration: none;
}

.wrapper a.component:hover {
	color: <?php echo $primary_6; ?>;
	background: <?php echo $primary_1; ?>;
	border: 1px solid <?php echo $primary_0; ?>;
	text-decoration: none;
}

.compdivlist {
	padding: 30px 0;
	text-align: center;
	margin: 0 0 0 15px;
	overflow: auto;
}

/* Notification styles
 *
 * alerts are now notifications
 * use notify and notify_type
 * .error and .upddated are DEPRECATED but still supported for legacy alerts
 * use "notify notify_error" etc.
 *
 */

.fullscreen .notify{
	position: fixed;
	width: 600px;
	margin: 5px;
	right: 60px;
	opacity: 1;
	top: 0;
	background: rgba(119, 119, 119, 0.2);
	z-index: 9999;
	font-weight: bold;
	border: none;
	padding: 3px 9px;
}

.updated, .error, .notify {
	/*border: 1px solid #E6DB55;*/
	border-radius: 2px;
	/*background: #FFFBCC;*/
	background: <?php echo $notify_6; ?>;
	line-height: 22px;
	padding: 5px 10px;
	margin-bottom: 20px;
}

.updated p, .error p, .notify p {
	margin: 0;
	line-height: 22px;
}

.error, .notify_error {
	color: <?php echo $label_4; ?>;
	background: <?php echo $notify_4; ?>;
}

.notify_ok, .notify_success {
	color: <?php echo $label_2; ?>;
	background: <?php echo $notify_2; ?>;
}

.notify_info {
	color: <?php echo $label_1; ?>;
	background: <?php echo $notify_1; ?>;
}

.notify_warning {
	color: <?php echo $label_3; ?>;
	background: <?php echo $notify_3; ?>;	
}

.deletedrow {
	background-color: #FFB19B;
}

/* what is this for , code inside a notify ? */
.error code,
.notify code {
	color: #990000;
	font-size: 11px;
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace;

	line-height: 14px;
	background: #fff;
	opacity: .8;
	padding: 1px;
}

.hint {
	color: #777;
	-ms-word-break: break-all;
	    word-break: break-all;
	-webkit-hyphens: auto;
	   -moz-hyphens: auto;
	        hyphens: auto;
}

/* file listing table style */
#filetypetoggle {
	color: #999;
	font-size: 12px;
}

.view {
	width: 48px;
	text-align: center;
}

.editl {
	width: 40px;
	text-align: center;
}

.editlw {
	width: 220px;
	text-align: left;
}

.delete a {
	font-size: 18px;
}

.delete a, .secondarylink a, a.cancel, a.updatelink{
	color: #999;
	text-decoration: none;
	padding: 1px;
	display: block;
	line-height: 16px;
	font-weight: normal;
	font-style: normal;
}

a.updatelink {
	display: inline-block;
	color: #CF3805;
	padding: 1px 3px;
	margin-left:-3px;
}

a.updatelink:hover {
	color: #FFF;
	background-color: <?php echo $primary_3; ?>;
}

.delete a:hover {
	background: #D94136;
	color: #fff;
}

.secondarylink, .delete {
	width: 20px;
	text-align: center;
	line-height: 14px;
}

.secondarylink a:hover {
	background: <?php echo $primary_3; ?>;
	color: #FFF;
}

a.cancel {
	display: inline-block;
	color: #D94136;
	text-decoration: underline;
	padding: 1px 3px;
	background: none;
	line-height: 16px;
}

a.cancel:hover {
	background: #D94136;
	color: #fff;
	text-decoration: none;
}

a.cancel em {
	font-style: normal;
}

.delete a, .secondarylink a, a.cancel, a.updatelink{
    -webkit-transition:  150ms ease-in-out;
	   -moz-transition:  150ms ease-in-out;
	     -o-transition:  150ms ease-in-out;
	        transition:  150ms ease-in-out;
	-webkit-transition-property: background-color, color, text-shadow;
	   -moz-transition-property: background-color, color, text-shadow;
	     -o-transition-property: background-color, color, text-shadow;
	        transition-property: background-color, color, text-shadow;

	border-radius:3px;
}


/* backup info display */
table.simple td {
	border: 1px solid #eee;
	border-collapse: collapse;
	color: #555;
	font-size: 12px;
	padding: 4px 10px 4px 4px;
}

table.simple {
	width: 100%;
	border: 1px solid #aaa;
}

table.simple td.title {
	width: 125px;
	color: #222;
	font-weight: bold;
}


/* footer */
#footer {
	border-top: 1px solid #e3e3e3;
	text-shadow: 1px 1px 0 rgba(255,255,255,.4);
	margin: 40px 0 0 0;
	padding: 10px 0;
	font-size: 11px;
	color: #888;
}

#footer p {
	margin: 0 0 8px 0;
}

#footer a:link,
#footer a:visited {
	font-weight: 100;
	color: #888;
}

#footer a:focus,
#footer a:hover {
	font-weight: 100;
	color: #333;
}
#footer .gslogo a {
	float: right;
	width: 60px;
	text-align: right;
	opacity: .10;
}

#footer .footer-left {
	float: left;
	width: 85%;
}

#footer .gslogo a:link,
#footer .gslogo a:visited  {
	transition: opacity .2s ease-in-out;
		-webkit-transition: opacity .2s ease-in-out;
		-moz-transition: opacity .2s ease-in-out;
		-o-transition: opacity .2s ease-in-out;
}

#footer .gslogo a:hover  {
	opacity: 1;
}

.toggle {
	display: none;
}

.editable {
	padding: 3px 1px;
	cursor: pointer;
	transition: background-color .3s ease-in-out;
		-webkit-transition: background-color .3s ease-in-out;
		-moz-transition: background-color .3s ease-in-out;
		-o-transition: background-color .3s ease-in-out;
}

.editable:hover {
	 background: #FCFBB8;
}

/* @todo all these wrappers, why every page has a wrapper */
.wrapper table td span.ERRmsg {
	color: <?php echo $label_4;?> ; /*#D94136;*/
	font-size: 12px;
}

.wrapper table td span.OKmsg {
	color: <?php echo $label_2;?> ; /*#308000;*/
	font-size: 12px;
}

.wrapper table td span.WARNmsg {
	color: <?php echo $label_3;?> ; /*#FFA500;*/
	font-size: 12px;
}

.wrapper table td span.INFOmsg {
	color: <?php echo $label_1;?> ; /*#2B5CB3;*/
	font-size: 12px;
}

.wrapper table.highlight tr.img-highlight {
	background: #FFFFD1 !important;
}

.wrapper table.highlight tr.img-highlight td a.primarylink {
	font-weight: bold !important;
}


#sidebar .uploadform {
	padding: 5px 15px;
}

/* upload link larger button for touch devices */
#fileuploadlink span.touch {
	display: none;
}
#fileuploadlink.touch span {
	display:none;
}
#fileuploadlink.touch span.touch {
	display: block;
	text-align: center;
	font-size: 20px;
}

/* Upload Queue */

.uploaddropzone {
	display: none;
	border: 3px dashed <?php echo $primary_1 ?>;
	border-radius: 3px;
	margin: 8px 0 5px 15px !important;
	text-align: center;
	height: 40px;
	line-height: 40px;
	font-weight: bold;
	font-size: 16px;
	color: <?php echo $primary_1 ?>;
	font-family: sans-serif;
	opacity: .2;

}

#queue-item-template{
	display: none;
}

.queue-item-wrap{
	margin:0;
	padding:0;
	position:relative;
}

.queue-item-wrap .queue-item {
	font-size: 10px;
	padding: 8px 15px;
	width: 190px;
}

/* special overrides for non templated remove links */
.queue-item-wrap a.dz-remove{
	margin:0;
	padding:0 !important;
	position: absolute;
	right: 13px;
	top: 6px;
	background: none !important;
	opacity: .8;
	font-size:14px;
}

.queue-item-wrap .queue-item .progress {
	background-color: <?php echo $primary_4; ?>;;
	margin-top: 5px;
	width: 97%;
}

.queue-item-wrap .queue-item .progress-bar {
	background-color: <?php echo $primary_6; ?>;
	width: 1px;
	height: 4px;
	transition: width 200ms;
}

.queue-item-wrap .dz-filename {
	width:165px;
	word-break: break-all;
	display: inline-block;
}

.queue-item-wrap .dz-name{
	word-break: break-all;
}

.queue-item-wrap .dz-filename .size{
	word-break: normal;
	white-space: nowrap;
}

/* statuses */

.queue-item-wrap .dz-error-mark,
.queue-item-wrap .dz-success-mark,
.queue-item-wrap dz-process-mark
{
	display:none;
	font-weight:bold;
}

/* processing */
.queue-item-wrap.dz-processing .dz-process-mark{
	display:inline;
	color: <?php echo $primary_6; ?>;
	/*opacity:.8;*/
}

.queue-item-wrap.dz-success .dz-process-mark,
.queue-item-wrap.dz-error .dz-process-mark
{
	display:none;
}

/* error */
.queue-item-wrap.dz-error .dz-error-mark{
	display: inline;
	color: <?php echo $label_4; ?>;
}

/* error progressbar */
.queue-item-wrap.dz-error .progress,
.queue-item-wrap.dz-error .progress-bar
{
	background-color: <?php echo $label_4; ?>;
}

/* error message */
.queue-item-wrap .dz-error-message{
	color: <?php echo $label_4; ?>;
	font-weight:bold;
}

/* success */
.queue-item-wrap.dz-success .dz-success-mark{
	display:inline;
	color: <?php echo $label_2; ?>;
}

#sidebar .snav li.upload {
	display: none;
		-moz-border-radius: 4px;
		-khtml-border-radius: 4px;
		-webkit-border-radius: 4px;
	border-radius: 4px;
	margin-left: 13px;
	color: #FFF;
	background: <?php echo $primary_1; ?>;
	font-weight: 100;
	text-shadow: 1px 1px 1px <?php echo $primary_1; ?>;
}

/* Image Editor Styles */
textarea.copykit {
	font-family: Consolas, "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", Monaco, "Courier New", Courier, monospace;
	font-size: 12px;
	outline: none;
	color: #666;
	border: 1px solid #aaa;
	line-height: 17px;
	padding: 4px;
		-moz-border-radius: 2px;
		-khtml-border-radius: 2px;
		-webkit-border-radius: 2px;
	border-radius: 2px;
	width: 98%;
	height: 70px;
	margin-bottom: 10px;
}

#handw {
	z-index: 1;
	padding: 8px;
	background: #000;
	opacity: .80;
	color: #fff;
	font-size: 11px;
	width: 150px;
	text-align: center;
	margin: 5px 0 0 0;
}

#handw span {
	font-size: 15px;
	font-weight: bold;
}

#jcropform .submit {
	margin: 20px 0 0 0;
}


/* Labels */
.label {
	padding: 1px 6px;
	border-radius: 3px;
	text-align: center;
	color: #F2F2F2;
	margin:0px 3px;
 	background-color: <?php echo $label_0; ?>;
 	/*display: inline-block;*/
}

.wrapper .label a,.label a{
	color: white;
	text-decoration: underline;
	font-weight: bold !important;
}

.label a:hover{
	opacity:.6;
}

.label-info {
	background-color:  <?php echo $label_1; ?> !important;
}

.label-ok {
	background-color: <?php echo $label_2; ?> !important;
}

.label-warn {
	background-color:  <?php echo $label_3; ?> !important;
}

.label-error {
	background-color:  <?php echo $label_4; ?> !important;
}

.label-medium {
	color: <?php echo $primary_0; ?> !important;
	background-color:  <?php echo $label_6; ?> !important;
}

.label-light{
	color: <?php echo $primary_6; ?> !important;
	background:  <?php echo $label_5; ?> !important;
	border: 1px solid <?php echo $primary_6; ?> !important;
}

.label-ghost {
	background-color: rgba(221, 221, 221, 0.5) !important;
}

.label-gold {
	color: black !important;
	text-shadow: none !important;
	background-color: #FFE000 !important;
}

.label hr{
	border: none;
	border-top: 1px solid rgba(255, 255, 255, 0.2);
	margin: 6px -8px;
}

.edittable .label-ghost{
	font-size:11px;
	padding:3px 6px;
	color: #999999;
	text-shadow: none;
}

div.showstatus{
	float:right;
}


/* @todo #ID selector hell, can be fixed up once some heirarchy is established */
/* healthcheck only */
#health-check #hc_version.label {
	text-align: left;
	padding: 9px 15px;
	font-weight: normal;
	line-height: normal;
	text-shadow: 1px 1px 0 rgba(60, 60, 60, 0.2) !important;
}

#hc_alert {
	float: right;
	margin-top: -15px;
	font-weight: bold;
	color: #5C5C5C;
}

#hc_alert .label {
	color: #FCFCFC !important;
	width: 90px;
	text-align: center;
	padding: 1px 10px;
	font-weight: normal;
	font-weight: bold;
}

#health-check .wrapper table td span.label {
	font-weight: bold;
	color: white;
	text-shadow: none !important;
	width:50px;
	display:block;
	line-height: 17px;
	float:right;
	margin:0;
}

td.hc_item {
	width: 330px;
}

/* File Browser Styles */
#filebrowser {
	background: #fff;
}


/* plugin styles */
table tr.enabled {
	background: #fff;
}

#maincontent table tr.enabled td span {
	color: #333;
}

table tr.disabled {
	background: #f6f6f6;
}

table tr.disabled td b {
	color: #666;
}


/* Logged out specific styles */
body#index {
	background: #f9f9f9;
}

#index .header,
#resetpassword .header,
#setup .header,
#install .header,
#setup .gslogo,
#install .gslogo,
#index .gslogo,
#resetpassword .gslogo {
	display: none;
}

#index #maincontent,
#resetpassword #maincontent,
#setup #maincontent,
#install #maincontent {
	width: 100%;
}

#index #maincontent .main,
#resetpassword #maincontent .main,
#setup #maincontent .main,
#install #maincontent .main {
	margin: 50px auto 0 auto;
	float: none;
	text-align: left;
		-moz-border-radius: 5px;
		-khtml-border-radius: 5px;
		-webkit-border-radius: 5px;
	border-radius: 5px;
		-moz-box-shadow: rgba(0,0,0, 0.05) 0 0 10px;
		-webkit-box-shadow: rgba(0,0,0, 0.05) 0 0 10px;
	box-shadow: rgba(0,0,0, 0.05) 0 0 10px;
	background: #FFF;
	width: 671px;
}

#setup #maincontent .main {
	width: 270px;
}

#setup input.text {
	width: 250px;
}

#index #maincontent .main,
#resetpassword #maincontent .main {
	width: 270px;
	border-bottom: 1px solid #999;
	border-right: 1px solid #999;
	text-shadow: 1px 1px 0 #fff;
	background: #FFF;
		background: -moz-linear-gradient(top, #f9f9f9 5%, #eeeeee 100%);	/* firefox */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(5%,#f9f9f9), color-stop(100%,#eeeeee));	/* webkit */
}

#resetpassword form input.text,
#index form input.text {
	width: 255px;
	font-size: 18px;
	padding: 5px;
	margin-top: 2px;
}

#index p.cta,
#resetpassword p.cta {
	font-size: 11px;
	margin: 0 0 0 0;
	color: #999;
	text-align: center;
}

#index form p,
#resetpassword form p {
	margin-bottom: 15px;
}

#index p.cta a,
#resetpassword p.cta a {
	font-weight: 100;
}

#setup .error,
#setup .updated,
#install .error,
#install .updated,
#index .error,
#index .updated,
#resetpassword .error,
#resetpassword .updated {
	margin: 25px auto -23px auto;
	width: 290px;
	line-height: 18px;
	padding: 5px 10px;
}

#setup #footer,#install #footer,#index #footer, #resetpassword #footer {
	width: 270px;
	border-top: none;
	margin: 0 auto 20px auto;
	text-align: center;
}

#index .footer-left,#resetpassword .footer-left  {
	float: none;
	width: 100%;
}

.desc {
	font-size: 12px;
	line-height: 17px;
	border-bottom: 1px dotted #ccc;
	padding: 0 0 15px 0;
	margin: 0 0 5px 0;
}

#filter-search  {
	margin: 0 0 10px 0;
	display: none;
}

#filter-search input.text {
	width: 250px;
	font-size: 11px;
	padding: 3px;
}

#createfolder {
	font-weight: 100;
	font-size: 11px;
	text-decoration: underline !important;
}

h5 .crumbs, div.h5 .crumbs {
	float: left;
}

#new-folder {
	float: left;
	padding-left: 5px;
}

#new-folder form {
	display: none;
}

#new-folder .cancel {
	font-size: 11px;
	text-shadow: none !important;
}

#new-folder input.submit {
	font-size: 11px;
	padding: 3px;
}

#new-folder input.text {
	width: 120px;
	font-size: 11px;
	padding: 3px;
}

#edit #dropdown {
	display: inline-block;
	padding: 3px 15px;
	position: relative;
}

#edit #dropdown h6 {
	font-weight: bold;
	font-size: 11px;
	color: #777;
	margin-bottom: 5px;
	padding-right: 15px;
	display: inline-block;
	background: transparent url('images/utick.png') right center no-repeat;
}

#edit #dropdown:hover h6 {
	visibility: hidden;
}

#edit #dropdown ul {
	display: none;
	text-shadow: 1px 1px 0 #fff;
	list-style: none;
	margin: 0;
	font-size: 11px;
	opacity: 0;
	background: #f6f6f6;
	border: 1px solid #ccc;
	position: absolute;
	top:  5px;
	left: 5px;
	min-width: 100px;
		-moz-border-radius: 2px;
		-khtml-border-radius: 2px;
		-webkit-border-radius: 2px;
	border-radius: 2px;
		-webkit-transition: opacity .3s ease-in-out;
		-moz-transition: opacity .3s ease-in-out;
		-o-transition: opacity .3s ease-in-out;
	transition: opacity .3s ease-in-out;
		-moz-box-shadow: rgba(0,0,0, 0.2) 1px 1px 3px 0;
		-webkit-box-shadow: rgba(0,0,0, 0.2) 1px 1px 3px 0;
	box-shadow: rgba(0,0,0, 0.2) 1px 1px 3px 0;
	overflow: hidden;
}

#edit #dropdown li {
	line-height: 22px;
	border-bottom: #eee 1px solid;
	padding: 0 8px;
}

#edit #dropdown li:last-child {
	border: none;
}

#edit #dropdown li a:link,
#edit #dropdown li a:visited,
#edit #dropdown li a:hover {
	font-weight: 100;
	color: #666;
	text-decoration: none;
	display: block;
}

#edit #dropdown li:hover {
	background: #fff;
}

#edit #dropdown li:hover a {
	color: #cc0000;
}

#edit #dropdown li.alertme:hover {
	background: #cc0000;
}

#edit #dropdown li.alertme:hover a {
	color: #FFF;
	text-shadow: none;
}

#edit #dropdown:hover ul {
	display: block;
	opacity: 1;
}

.wrapper #maincontent ul#menu-order {
	list-style: none;
	margin: 0 0 25px 0;
}

#menu-order li {
	text-shadow: 1px 1px 0 rgba(255,255,255,.3);
	cursor: move;
	display: block;
	margin: 2px 0;
	border: 1px solid #eee;
	background: #fbfbfb;
	padding: 5px 10px;
}

#menu-order li:hover {
	border: 1px solid #ccc;
	background: #f6f6f6;
}

#menu-order li em {
	float: right;
	color: #666;
}

#menu-order li.placeholder-menu {
	height: 18px;
	background: #FFB164;
	border: 1px solid #FF9933;
}

#theme_select {
	font-size:16px;
	font-weight:bold;
}

#theme-edit #maincontent {
	width: 100%;
}

#theme-edit #sidebar {
	display: none;
}

#gsdebug{
	border: 1px solid #FF9933;
	padding: 5px;
	background: white;
	height: 500px;
	overflow: scroll;
	margin-bottom: 20px;
}

/* ajaxwait deprecated , use js spin() */
.ajaxwait {
	background-image: url('images/ajax.gif');
	background-position: center;
	background-repeat: no-repeat;
}

.ajaxwait_dark {
	background-image: url('images/ajax_dark.gif');
}

.ajaxwait_tint_dark {
	background-color: #F6F6F6;
	/*position: relative;	*/
}

/* @todo with other link styles? */
a.disabled:link,
a.disabled:visited {
	pointer-events: none;
	cursor: default;
	text-decoration: none !important;
	color: #CCC !important;
}

/*
 * Theme Editor custom layout
 */

/* @todo ditch the IDs */
#theme-edit .updated, #theme-edit .error, #theme-edit .notify {
	/*margin: 0 15px 15px 15px;*/
}

#theme-edit #header .wrapper{
	width:950px;
}

/* core theme override for theme editor */
#theme-edit .wrapper{
	/*width: 100%;*/
	/*max-width:1680px;*/
}

#theme-edit #footer{
	/*margin-left: 15px;*/
	/*margin-right: 15px;*/
}

#theme-edit .bodycontent {
	/*margin:0 15px;*/
}

#theme_edit_wrap {
	width:100%;
	margin:0;
	padding:0;
}

#theme_edit_wrap .well{
	background-color:#F9F9F9;
	border:1px solid #E8E8E8;
	border-radius: 2px;
	padding:6px;
	line-height:27px;
	height:24px;
}

#theme_edit_nav {
	width: 20%;
	float: left;
}

#theme_edit_select{
	margin-right:10px;
}

#theme_edit_select select{
	width:100%;
	padding: 2px;
	border-radius: 2px;
	border: 1px solid rgb(189, 189, 189);
}

#theme_edit_select .well{
	background-color:#DDD;
}

#theme_edit_code {
	float:left;
	width:80%;
}

#theme_edit_code .well{
	padding-left: 12px;
	margin-bottom:14px;
}

#theme_edit_code .CodeMirror{
	/*border:1px solid #E8E8E8;*/
	border-radius:2px;
	min-height:550px;
}

#cm_themeselect {
	float: right;
}

#theme_filemanager {
	margin: 14px 10px 20px 0;
	background: #F9F9F9;
	border: 1px solid #E8E8E8;
	border-radius:2px;
	padding:15px 10px 15px 10px;
	/*height: 60%;*/
	padding: 8px;
	overflow: auto;

}

#theme_filemanager ul {
		display: block;
		margin: 0 !important;
		padding: 0;
		line-height: 14px !important;
}
#theme_filemanager li {
		display: block;
		margin: 0;
		padding: 2px 0;
		list-style: none;
		position:relative;
}
#theme_filemanager ul ul li {
		margin-left: 20px;
		white-space: nowrap;
}
#theme_filemanager a {
		display: block;
		min-width: 100%;
		cursor: pointer;
		padding: 5px 0;
		border-radius: 5px;
		text-decoration: none;
		font-weight: normal;
		font-family: sans-serif;
		color: #333;
}

#theme_filemanager ul ul{
	display:none;
}

#theme_filemanager a:hover, #file-manager a.context-menu-active {
		background-color: #E9E9E9;
		text-decoration:none;
		font-weight:normal;
}

#theme_filemanager a.open {
		background-color: #DADADA;
		font-weight:bold;
}

#theme_filemanager .well{
	border-radius:4px;
	background-color:#CCC;
	padding:4px 8px;
}

#theme_filemanager #theme-folder{
	width:100% !important;
}

/* ICONS */

#theme_filemanager a {
		background-repeat: no-repeat;
		background-position: 6px 6px;
		text-indent: 28px;
		padding-top: 6px;
}

#theme_filemanager .directory {
		background-image: url(images/filemanager/folder.png);
}

#theme_filemanager .dir-open {
		background-image: url(images/filemanager/folder-open.png);
}

/* File Extension icons*/
.file {    background-image: url(images/filemanager/text-x-generic.png); }
.ext- {background-image: url(images/filemanager/text-x-preview.png);}
.ext-js {background-image: url(images/filemanager/application-javascript.png);}
.ext-html {background-image: url(images/filemanager/text-html.png);}
.ext-htm {background-image: url(images/filemanager/text-html.png);}
.ext-php {background-image: url(images/filemanager/application-x-php.png);}
.ext-css {background-image: url(images/filemanager/text-css.png);}
.ext-theme {background-image: url(images/filemanager/preferences-desktop-theme.png);}
.ext-wait,.ext-blank {background-image: none;}

/* Grayscale */
.desaturate, .dir-empty{
		filter: gray; /* IE */
		-moz-filter: grayscale(100%);
		-ms-filter: grayscale(100%);
		-o-filter: grayscale(100%); /* Not yet supported in Gecko, Opera or IE */
		/*filter: url(resources.svg#desaturate); /* Gecko */
		-webkit-filter: grayscale(1); /* Old WebKit */
		-webkit-filter: grayscale(100%); /* New WebKit */
		filter: grayscale(100%); /* Current draft standard */
}

/* force text to be non selectable , for labels psuedo buttons */
*.unselectable {
	cursor:default;
   -moz-user-select: none;
   -khtml-user-select: none;
   -webkit-user-select: none;
   /*
     Introduced in IE 10.
     See http://ie.microsoft.com/testdrive/HTML5/msUserSelect/
   */
   -ms-user-select: none;
   user-select: none;
}

.notransition {
  -webkit-transition: none !important;
  -moz-transition: none !important;
  -o-transition: none !important;
  -ms-transition: none !important;
  transition: none !important;
}

/* codemirror */
/* CodeMirror WEBKIT SCROLLBARS */

/* scroll on focus only */
.CodeMirror:not(.CodeMirror-focused) .CodeMirror-hscrollbar,
.CodeMirror:not(.CodeMirror-focused) .CodeMirror-vscrollbar
{
	/*visibility: hidden !important;*/
	opacity:0.2;
}

.CodeMirror .CodeMirror-hscrollbar,
.CodeMirror .CodeMirror-vscrollbar{
	/*visibility: visible;*/
	opacity:1;
}

.CodeMirror .CodeMirror-hscrollbar{
	margin: 0 15px 2px 2px;
}

.CodeMirror .CodeMirror-vscrollbar{
	margin: 2px 2px 15px 0;
}

.CodeMirror ::-webkit-scrollbar {
		width: 10px;
		height: 10px;
}
.CodeMirror ::-webkit-scrollbar-track-piece {
		background-color: rgba(100,100,100,.2);
		-webkit-border-radius: 0;
		border-radius: 3px;
}

.CodeMirror ::-webkit-scrollbar-track-piece:vertical {
	/*margin-bottom:10px;*/
}

.CodeMirror ::-webkit-scrollbar-track-piece:horizontal {
	/*margin-right:10px;*/
}

.CodeMirror ::-webkit-scrollbar-thumb:vertical {
		height: 25px;
		background-color: rgba(100,100,100,.5);
		border:1px solid #666;
		-webkit-border-radius: 3px;
		border-radius: 3px;
}
.CodeMirror ::-webkit-scrollbar-thumb:vertical:hover {
		background-color: #666;
}
.CodeMirror ::-webkit-scrollbar-thumb:horizontal {
		width: 25px;
		background-color: rgba(100,100,100,.5);
		border:1px solid #666;
		-webkit-border-radius: 3px;
		border-radius: 3px;
}
.CodeMirror ::-webkit-scrollbar-thumb:horizontal:hover {
		background-color: #666;
}
.CodeMirror ::-webkit-scrollbar-corner {
		background-color: transparent;
}

/* tab guides */
.CodeMirror .cm-tab:not(:first-child){
	border-left: 1px dotted rgba(100,100,100,0.3);
}

/* match highlight rounded outline */
.CodeMirror-focused .cm-matchhighlight{
	border: 1px solid #777777;
	border-radius: 3px;
	margin:-1px; /* border offset */
}

/* hides codemirror active line when not focuses */
.CodeMirror-activeline-background{
	display:none;
}
.CodeMirror-focused .CodeMirror-activeline-background{
	display:block;
}

.CodeMirror-foldmarker {
	color: white;
	text-shadow: #000 1px 1px 2px, #000 -1px -1px 2px, #000 1px -1px 2px, #000 -1px 1px 2px;
	font-family: arial;
	line-height: .3;
	cursor: pointer;
}

.codewrap {
	/*font-size: 13px;*/
	/*line-height: 13px;*/
}

.codewrap textarea, #tabs .codewrap textarea{
	font-family: "Consolas", "Andale Mono WT", "Andale Mono", "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", "Bitstream Vera Sans Mono", "Liberation Mono", "Nimbus Mono L", "Monaco", "Courier New", "Courier", "monospace";
	font-size: 12px;
}

/* codemirror overrides */

.codewrap .cm-s-midnight.CodeMirror {border:none;} /* override midnight borers */

.codewrap .CodeMirror {
	font-family: Consolas, Monaco, Menlo, 'Ubuntu Mono', 'Droid Sans Mono', monospace;
	height: auto; /* autosizing max height @todo: breaks gutter height and overrides above */
	max-height: 550px;
	line-height:1.2em;
	background-color:#FEFEFE;
	/*padding-bottom:20px;*/
	border: 3px solid rgba(128,128, 128, .15);
    background-clip: padding-box !important; /* for IE9+, Firefox 4+, Opera, Chrome */
    -webkit-background-clip: padding-box !important; /* for Safari */
    /*z-index: 1001;*/
    padding-bottom: 10px; /* padding for hscroll not obfuscate last line */
}


/* codemirror focused special border highlight style */
.codewrap .CodeMirror.CodeMirror-focused{
/*    outline: none;
    border-color: #9ecaed;
    box-shadow: 0 0 10px #9ecaed;*/
}

.codewrap .CodeMirror-scroll {
	max-height: inherit; /* autosizing max height */
	overflow-y: auto;
	overflow-x: hidden;
}

.codewrap .CodeMirror.CodeMirror-fullscreen{
    border: none;
}

.CodeMirror-fullscreen {
	border: none;
	display: block;
	position: fixed !important;
	top: 0; left: 0;
	width: 100% !important;
	z-index: 1001 !important;
	background-color:#FFF;
  	max-height: none !important;
}

.CodeMirror-hints{
	z-index: 1002 !important;
}

.CodeMirror-fullscreen .CodeMirror-scroll, .CodeMirror.ui-resizable-resizing, .CodeMirror.ui-resizable-resizing .CodeMirror-scroll{
  	max-height: none !important;
}


/* custom se resize grip handle */
.CodeMirror .handle {
	font-family: monospace;
	font-size: 17px;
	/*margin-bottom: 1px;*/
	/*margin-right: -1px;*/
	margin:0;
	padding-bottom:1px;
	padding-right:0;
	text-decoration:none;
	text-shadow:none;
	box-shadow : none;
	font-weight: normal;
	line-height:1;
}

/* fullscreen button */
.CodeMirror .overlay_but_fullscrn {
	position:absolute;
	top: 5px;
	right: 5px;
	/*width: 16px;*/
	/*height: 16px;*/
}

.CodeMirror .overlay_but_fullscrn a {
	position: relative;
	padding: 0 2px;
	background-color: #777777;
	border-radius: 4px;
	color: whitesmoke;
	font-family: FontAwesome;
	font-size: 17px;
	text-align: center;
	text-decoration:none;
	box-shadow : none;
	z-index: 10;
	font-weight: normal;
	opacity:.2;
		-webkit-transition: opacity 300ms;
		-moz-transition: opacity 300ms;
		-o-transition: opacity 300ms;
		-ms-transition: opacity 300ms;
	transition: opacity 300ms;
}

.fullscreen .CodeMirror .overlay_but_fullscrn a{
	font-size:27px;
}

.CodeMirror .overlay_but_fullscrn a.scrolled {
	right: 14px;
}

.CodeMirror .overlay_but_fullscrn a:hover {
	opacity:.6;
}

/* jquiry-ui theme overrides
	tab border bug https://github.com/jquery/download.jqueryui.com/issues/87
*/

.CodeMirror .ui-icon-gripsmall-diagonal-se {
	opacity:.3;
}

.CodeMirror .ui-icon-gripsmall-diagonal-se:hover {
	opacity:.6;
}

#maincontent .ui-tabs .ui-tabs-panel {
	border-width: 0 !important;
	padding: 0;
	margin:10px 0;
}

#maincontent .ui-widget-content {
	border-width:0 !important;
}

#maincontent a.ui-tabs-anchor, #maincontent a.ui-tabs-anchor:hover{
	outline:0;
	font-weight:normal;
}

#maincontent .ui-tabs .ui-tabs-nav {
	padding:0 !important;
}

#maincontent .ui-tabs .ui-tabs-nav.ui-widget-header {
	border-top:none !important;
	border-left:none !important;
	border-right:none !important;
	background:none !important;
	border-bottom: 1px solid #aaa;
	border-radius:0 !important;
}

/* jui tabs */
#tabs ul.tab-list{
	display:none;
}

#tabs.ui-tabs ul.tab-list{
	display:block;
}

/* jqueryui tabs specivity overrides */
#tabs textarea {
	font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
	font-size: 11px;
}

/* prevent ui tabs fouc */
.tab {
	display:none;
}

.tab fieldset legend{
	display: none;
}

.outline {
	border: 1px dotted gray;
}

/* Allow Font Awesome Icons in lieu of jQuery UI and only apply when using a FA icon */
.ui-icon[class*=" icon-"] {
    /* Remove the jQuery UI Icon */
    background: none repeat scroll 0 0 transparent;
    /* Remove the jQuery UI Text Indent */
    text-indent: 0;
    /* Bump it up - jQuery UI is -8px */
    margin-top: -0.5em;
}

/* Allow use of icon-large to be properly aligned */
.ui-icon.icon-large {
    margin-top: -0.75em;
}

.ui-button-icon-only .ui-icon[class*=" icon-"] {
    /* Bump it - jQuery UI is -8px */
    margin-left: -7px;
}

/* gstree styles */
.tree-roottoggle .label{
 	cursor: pointer;
 	font-size: 11px;
 	margin-left:0;
 	color: #777;
}

.tree-indent {
	padding: 0 10px 0 5px;
	display: inline;
	position: relative;
}

.tree-expander {
	cursor: pointer;
	color: #868686;
	font-size: 0.8em;
	margin-right: 7px;
	transition: all 50ms ease-out;
		-webkit-transition: all 50ms ease-out;
		-moz-transition: all 50ms ease-out;
		-o-transition: all 50ms ease-out;
}

.tree-expander-expanded{
	padding: 0 12px 0 3px;
	display: inline;
	position: relative;
}

.tree-expander-collapsed{
	padding: 0 12px 0 3px;
	display: inline;
	position: relative;
}

.tree-parentcollapsed {
	display:none;
}

.tree-error {
	color: #990000;
	border: 1px solid #cc0000;
}

/* icon offset override for 10px for rotation centering */
.tree .fa-play{
    -webkit-transform-origin-x: 38%;
    -webkit-transform-origin-y: 45%;
}


/* END */

/* Admin theme colors */

/* background-color */
  .primary_0-back, .darkest-back               { background-color: <?php echo $primary_0;   ?> !important;}
  .primary_1-back, .darker-back                { background-color: <?php echo $primary_1;   ?> !important;}
  .primary_2-back, .dark-back                  { background-color: <?php echo $primary_2;   ?> !important;}
  .primary_3-back, .middle-back                { background-color: <?php echo $primary_3;   ?> !important;}
  .primary_4-back, .light-back                 { background-color: <?php echo $primary_4;   ?> !important;}
  .primary_5-back, .lighter-back               { background-color: <?php echo $primary_5;   ?> !important;}
  .primary_6-back, .lightest-back              { background-color: <?php echo $primary_6;   ?> !important;}
.secondary_0-back, .secondary-darkest-back     { background-color: <?php echo $secondary_0; ?> !important;}
.secondary_1-back, .secondary-lightest-back    { background-color: <?php echo $secondary_1; ?> !important;}

/* color */
  .primary_0-color, .darkest-color             { color: <?php echo $primary_0;   ?> !important;}
  .primary_1-color, .darker-color              { color: <?php echo $primary_1;   ?> !important;}
  .primary_2-color, .dark-color                { color: <?php echo $primary_2;   ?> !important;}
  .primary_3-color, .middle-color              { color: <?php echo $primary_3;   ?> !important;}
  .primary_4-color, .light-color               { color: <?php echo $primary_4;   ?> !important;}
  .primary_5-color, .lighter-color             { color: <?php echo $primary_5;   ?> !important;}
  .primary_6-color, .lightest-color            { color: <?php echo $primary_6;   ?> !important;}
.secondary_0-color, .secondary-darkest-color   { color: <?php echo $secondary_0; ?> !important;}
.secondary_1-color, .secondary-lightest-color  { color: <?php echo $secondary_1; ?> !important;}

/* border color */
  .primary_0-border-color, .darkest-border             { border-color: <?php echo $primary_0;   ?> !important;}
  .primary_1-border-color, .darker-border              { border-color: <?php echo $primary_1;   ?> !important;}
  .primary_2-border-color, .dark-border                { border-color: <?php echo $primary_2;   ?> !important;}
  .primary_3-border-color, .middle-border              { border-color: <?php echo $primary_3;   ?> !important;}
  .primary_4-border-color, .light-border               { border-color: <?php echo $primary_4;   ?> !important;}
  .primary_5-border-color, .lighter-border             { border-color: <?php echo $primary_5;   ?> !important;}
  .primary_6-border-color, .lightest-border            { border-color: <?php echo $primary_6;   ?> !important;}
.secondary_0-border-color, .secondary-darkest-border   { border-color: <?php echo $secondary_0; ?> !important;}
.secondary_1-border-color, .secondary-lightest-border  { border-color: <?php echo $secondary_1; ?> !important;}

  .label-info-border                                         { border-color: <?php echo $label_0; ?> !important;}
  .label-ok-border                                           { border-color: <?php echo $label_1; ?> !important;}
  .label-warn-border                                         { border-color: <?php echo $label_2; ?> !important;}
  .label-error-border                                        { border-color: <?php echo $label_3; ?> !important;}
  .label-light-border                                        { border-color: <?php echo $label_4; ?> !important;}
  .label-medium-border                                       { border-color: <?php echo $label_5; ?> !important;}

/* debug color */
  .primary_0-border-debug:after, .darkest-debug:after             { content: "<?php echo $primary_0;   ?>" !important;}
  .primary_1-border-debug:after, .darker-debug:after              { content: "<?php echo $primary_1;   ?>" !important;}
  .primary_2-border-debug:after, .dark-debug:after                { content: "<?php echo $primary_2;   ?>" !important;}
  .primary_3-border-debug:after, .middle-debug:after              { content: "<?php echo $primary_3;   ?>" !important;}
  .primary_4-border-debug:after, .light-debug:after               { content: "<?php echo $primary_4;   ?>" !important;}
  .primary_5-border-debug:after, .lighter-debug:after             { content: "<?php echo $primary_5;   ?>" !important;}
  .primary_6-border-debug:after, .lightest-debug:after            { content: "<?php echo $primary_6;   ?>" !important;}
.secondary_0-border-debug:after, .secondary-darkest-debug:after   { content: "<?php echo $secondary_0; ?>" !important;}
.secondary_1-border-debug:after, .secondary-lightest-debug:after  { content: "<?php echo $secondary_1; ?>" !important;}

/* label colors */
   .label-default-debug:after                                     { content: "<?php echo $label_0;   ?>" !important;}
   .label-info-debug:after                                        { content: "<?php echo $label_1;   ?>" !important;}
   .label-ok-debug:after                                          { content: "<?php echo $label_2;   ?>" !important;}
   .label-warn-debug:after                                        { content: "<?php echo $label_3;   ?>" !important;}
   .label-error-debug:after                                       { content: "<?php echo $label_4;   ?>" !important;}
   .label-light-debug:after                                       { content: "<?php echo $label_5;   ?>" !important;}
   .label-medium-debug:after                                      { content: "<?php echo $label_6;   ?>" !important;}

/* </style> */

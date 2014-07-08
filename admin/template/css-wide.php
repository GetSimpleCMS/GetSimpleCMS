<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } ?>

/** CSS **/

/*
 * CSS wide
 */

/* <style> */


body{
	margin:0 15px;
}

body .header{
	margin-left: -15px;
	padding: 0 15px;
	margin-right: -15px;
}

.wrapper{
	/* main wrapper */
	width:100%;
	max-width:<?php echo $width; ?>;
}

<?php echo $widepagecss; ?>

.wrapper .nav{
	/* top header nav wrapper */
	width:100%;
}

.wrapper h1 {
	/*margin-left: 15px;*/
}

.wrapper .nav li:first-child {
	/* breathing space for tabs */
	/*margin-left: 15px;*/
}

.wrapper table {
	/* tables in wrapper, eg page management lists */
	width:100%;
}

.wrapper #pill {
	/*right: 15px;*/
}

.wrapper .nav li.rightnav {
	/*margin-right: 15px;*/
	/*margin-left: -15px;*/
}

.updated, .error, .notify {
	/*margin: 0 15px 20px 15px;*/
}

.bodycontent {
	/*margin:0px 15px;*/
}

#maincontent{
	/* wrapper for admin inputs */
	width:100%;
	float:left;
}

#maincontent .main {
	margin-right: 250px;
	min-width: 260px;
}

#sidebar {
	margin-left: -225px;
}

#footer {
	/*margin-left:15px;*/
	/*margin-right:15px;*/
}

#theme-edit #header .wrapper  {
	width:100%;
}

#theme-edit #maincontent .main {
	margin-right: 0;
}

textarea, form input.title{
	/* resize backend textareas */
	/* outline backend administrative input fields for emphasis */
	width:100% ;
}

form.manyinputs textarea{
	/* resize backend textareas for components */
	width:100% ;
}

.main form{
	/*max-width: 600px;*/
}


.wideopt {
	max-width:600px;	
}

.leftopt,.rightopt {
	max-width:300px;
}

.rightopt {
	float:left;
}

/* </style> */

<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); } 

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
$header_base = "#415A66";
// $header_base = "#604466";
$primary_0 ="rgba(0,0,0,0.5)";
$primary_1 = "rgba(16,16,16,0.38)";
$primary_6 = "rgba(255,255,255,.9)";
$primary_5 = "rgba(255,255,255,0.8)";
?>
/** css.php **/
/* <style> */

#header {
	border-top: 1px solid <?php echo $primary_1; ?>;
	background: <?php echo $header_base; ?>;	/* old browsers */
}

#header h1#sitename a:link,
#header h1#sitename a:visited,
#header h1#sitename a:hover {
	color: <?php echo $primary_5; ?>;
}

#header .wrapper .nav li a {
	color: <?php echo $primary_6; ?>;
	background-color: <?php echo $primary_1; ?>;
}

#load .wrapper .nav li a.current,
#loadtab .wrapper .nav li a.current,
#header .wrapper .nav li a.current {
	color: <?php echo $primary_1; ?>;
}

#header .wrapper .nav li a:active,
#header .wrapper .nav li a:focus,
#header .wrapper .nav li a:hover{
	background-color: <?php echo $primary_0; ?>;
}

#header .edit-nav a:link,
#heaeder .edit-nav a:visited {
	line-height: 14px;
	background-color: <?php echo $primary_1; ?>;
	color: <?php echo $primary_6; ?>;
}

<?php
/*
  based on polyfragmented's hack 
  (http://get-simple.info/forum/topic/1549/hack-v30-autoopen-page-options-slug-tags-navigation-etc/)
*/

$thisfile = basename(__FILE__, ".php");

register_plugin(
	$thisfile, 
	'Auto-open Page Options', 	
	'1.0', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Automatically opens the page options when editing pages',
	'',
	''  
);

add_action('edit-extras','pageoptions_auto_open');

function pageoptions_auto_open(){
?>
  <script type="text/javascript">
    $(function() {
      $("#metadata_window").slideToggle('fast');
      $("#metadata_toggle").toggleClass('current');    
    });
  </script>
<?php
}



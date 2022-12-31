<?php
/*
  based on polyfragmented's hack 
  (http://get-simple.info/forum/topic/1549/hack-v30-autoopen-page-options-slug-tags-navigation-etc/)
*/

$thisfile = basename(__FILE__, ".php");

register_plugin(
	$thisfile, 
	'Auto-open Page Options', 	
	'2.0', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Automatically opens the page options when editing pages',
	'',
	''  
);

add_action('edit-extras','pageoptions_init');
add_action('changedata-save', 'pageoptions_save');

function pageoptions_init() {
  global $id;
  $closed = array();
  if (isset($_COOKIE['no-autoopen'])) $closed = explode(',', $_COOKIE['no-autoopen']);
  $open = !in_array($id, $closed);
?>
  <script type="text/javascript">
    $(function() {
      <?php if ($open) { ?>
      $("#metadata_window").slideToggle('fast');
      $("#metadata_toggle").toggleClass('current'); 
      <?php } ?>
      $("#metadata_toggle").click(function(e) {
        var val = $("#autoopen-status").val();
        $("#autoopen-status").val(val == 1 ? 0 : 1);
      })   
    });
  </script>
  <input type="hidden" id="autoopen-status" name="autoopen-status" value="<?php echo $open ? 1 : 0; ?>"/>
<?php
}

function pageoptions_save() {
  global $url;
  $oldId = $_POST['existing-url'];
  $newId = $url;
  $closed = array();
  if (isset($_COOKIE['no-autoopen'])) $closed = explode(',', $_COOKIE['no-autoopen']);
  foreach ($closed as $i => $id) if ($id == $oldId || $id == $newId) unset($closed[$i]);
  if (@$_POST['autoopen-status'] == 0) $closed[] = $newId;
  setcookie('no-autoopen', implode(',', $closed), time()+365*24*3600);
}



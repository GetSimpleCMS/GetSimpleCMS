<?php  // GS defaults
  $sec_light = '#CF3805';
  $sec_dark = '#9F2C04';
    
  // admin.xml styles
  if (file_exists(GSTHEMESPATH . 'admin.xml')) {
    $css = getXML(GSTHEMESPATH . 'admin.xml');
    $sec_light = trim((string)$css->secondary->lightest);
    $sec_dark = trim((string)$css->secondary->darkest);
  }
  
  // if Flat Blue Admin Theme is installed override both defaults & admin.xml styles
  if (!function_exists('flatBlue')) {
    function flatBlue() {
      global $live_plugins;
      if (array_key_exists('flat-blue.php', $live_plugins) && $live_plugins['flat-blue.php'] === 'true')
        return true;
    }
  }
  if (flatBlue())  
    $sec_light = '#1E282C';
    
  // based on the $sec_light color, calculate the color to use for selected settings in the Edit mode grid
  if (!function_exists('convert')) {
    function convert($pair) {
      $hex_map = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
      $first = array_search(substr($pair, 0, 1), $hex_map) + 8 >= count($hex_map) - 1 ? $hex_map[count($hex_map)-1] : $hex_map[substr($pair, 0, 1) + 8];
      $second = substr($pair, 1, 1);
      return $first . $second;
    }
  }
  $grid_color = '#' . convert(substr($sec_light, 1, 2)) . convert(substr($sec_light, 3, 2)) . convert(substr($sec_light, 5, 2));
?>

<style>
  .edit .setting + .setting.active { border-bottom: 1px solid <?php echo $sec_light; ?>; }
  .edit .setting:not(.active) + .setting.active { border-top: 1px solid <?php echo $sec_light; ?>; }
  .edit .setting.active { background: <?php echo $grid_color; ?>; border-color: <?php echo $sec_light; ?>;}
  .edit .setting.active .fa-location-arrow, #expand-all .fa-minus-square, 
  .cs-toolbar button:not([disabled]):hover i, .edit .setting .fa-minus-square, .cs-toolbar button.active { color: <?php echo $sec_light; ?>; }
  #sidebar .snav li.current a { text-shadow: 1px 1px 0px <?php echo $sec_dark; ?>; background-color: <?php echo $sec_light; ?>; }
<?php if (!flatBlue() && $css) { ?> #sidebar .snav li { color: <?php echo trim((string)$css->primary->lightest); ?>; }<?php } ?>
  ::selection { background-color: <?php echo $sec_light; ?>; color: white; }
<?php if (flatBlue()) { ?>
  #sidebar .snav h3 { font-size: 20px; margin-bottom: 16px; }
  #sidebar .snav .cs-toolbar { right: 10px; }
  #sidebar .snav li a { overflow:visible; }
  #sidebar .snav li.current a { border-left-color: #3c8dbc; }
  #sidebar .snav li input { font-weight: normal; top: 15px; }
  #notification-manager { margin-left: -450px; max-height: 90px; left: 50%; max-width: 100%; text-align: left; background: white !important; }
  .cs-main .setting:hover .fa.fa-code { font-size: 14px; }
  .edit-nav a:link, .edit-nav a:visited { font-size: 12px !important;}
<?php } ?>
</style>

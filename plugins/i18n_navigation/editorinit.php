<?php
  if (function_exists('find_i18n_url')) {
    $slug = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['newid']) ? $_GET['newid'] : '');
    $pos = strpos($slug, '_');
    $lang = $pos !== false ? substr($slug, $pos+1) : null;
    $structure = I18nNavigationFrontend::getPageStructure(null, false, null, $lang);
    $pages = array();
    $nbsp = html_entity_decode('&nbsp;', ENT_QUOTES, 'UTF-8');
    $lfloor = html_entity_decode('&lfloor;', ENT_QUOTES, 'UTF-8');
    foreach ($structure as $page) {
      $text = ($page['level'] > 0 ? str_repeat($nbsp,5*$page['level']-2).$lfloor.$nbsp : '').cl($page['title']);
      $link = find_i18n_url($page['url'], $page['parent'], '('.($lang ? $lang : return_i18n_default_language()).')');
      $pages[] = array($text, $link);
    }
?>
<script type="text/javascript">
  //<![CDATA[
  // modify existing Link dialog
  CKEDITOR.on( 'dialogDefinition', function( ev ) {
    if ((ev.editor != editor) || (ev.data.name != 'link')) return;

    var definition = ev.data.definition;
    var infoTab = definition.getContents('info');
    
    for (var i=0; i<infoTab.elements.length; i++) {
      var element = infoTab.elements[i];
      if ('id' in element && element.id == 'localPageOptions') {
        element.children[0].items = <?php echo json_encode($pages); ?>;
      }
    }
  });
  //]]>
</script>
<?php
  }
?> 
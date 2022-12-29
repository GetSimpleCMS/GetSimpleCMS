<?php
global $xml, $url;
if (strpos($url,'_') === false) {
  $parent = $_POST['post-parent'];
  $after = $_POST['post-menu-order'];
  $pages = return_i18n_pages();
  if (isset($pages[$parent]['children'])) {
    $siblings = $pages[$parent]['children'];
    $i = 0;
    if (!$after) $i++; // menuOrder is automatically set to 0
    foreach ($siblings as $sibling) {
      if ($sibling != $url) {
        $file = GSDATAPAGESPATH . $sibling . '.xml';
        if (file_exists($file)) {
          $data = simplexml_load_file($file, 'SimpleXMLExtended');
          if ($i != (int) $data->menuOrder) {
            unset($data->menuOrder);
            $data->addChild('menuOrder')->addCData($i);
            XMLsave($data,$file);
          }
          $i++;
        }
        if ($sibling == $after) {
          unset($xml->menuOrder);
          $xml->addChild('menuOrder')->addCData($i);
          $i++;
        }
      } 
    }
  }
} else {
  // reset parent, private, menu (menuOrder is already 0)
  unset($xml->parent);
  $xml->addChild('parent')->addCData('');
  unset($xml->private);
  $xml->addChild('private')->addCData('');
  if (!isset($_POST['post-menu-enable'])) {
    unset($xml->menu);
    $xml->addChild('menu')->addCData('');
  }
}

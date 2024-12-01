<?php
define ('ITEMSFILE', GSDATAOTHERPATH.'item_manager.xml');
define('IM_CUSTOMFIELDS_FILE', 'plugincustomfields.xml');
define('IMITEMPATH', GSDATAPATH  . 'items/');


function im_sitemap_include() {
  global $page, $xml, $SITEURL;;
  if (strval($page['url']) == ITEMPAGE) {

      $dir_handle = @opendir(ITEMSDATA);
      while ($filename = readdir($dir_handle)) {
      if (strrpos($filename,'.xml') === strlen($filename)-4) {
      $data = getXML(ITEMSDATA . $filename);
      if ($data->visible == true) {
      $url = $SITEURL.ITEMPAGE."/?item=".$data->slug;
      $file = IMITEMPATH . "$data->slug.xml";
      $date = makeIso8601TimeStamp(date("Y-m-d H:i:s", filemtime($file)));
      $item = $xml->addChild('url');
      $item->addChild('loc', $url);
      $item->addChild('lastmod', $date);
      $item->addChild('changefreq', 'monthly');
      $item->addChild('priority', '0.5');
      }
    } 
    }
  }
}
function im_get_posts($all=false) {
  $now = time();
  $posts = array();
  $data = @getXML(NMPOSTCACHE);
  foreach ($data->item as $item) {
    if ($all || $item->private != 'Y' && strtotime($item->date) < $now)
      $posts[] = $item;
  }
  return $posts;
}

function im_customfield_def(){
	global $im_customfield_def;
	if ($im_customfield_def == null) {
		$files = GSDATAOTHERPATH . IM_CUSTOMFIELDS_FILE;
		if (file_exists($files)) {
			$data = getXML($files);
			$items = $data->item;
			if (count($items) > 0) {
				foreach ($items as $item) {
					$cf = array();
					$cf['key'] = (string) $item->desc;
					$cf['label'] = (string) $item->label;
					$cf['type'] = (string) $item->type;
					$cf['value'] = (string) $item->value;
					if ($item->type == "dropdown") {
						$cf['options'] = array();
						foreach ($item->option as $option) {
							$cf['options'][] = (string) $option;
						}
					}
					$im_customfield_def[] = $cf;
				}
			}
		}
	}
	return $im_customfield_def;
}


// these functions should be in the GetSimple Core:

if (!function_exists('i18n_merge')) {

  function i18n_merge($plugin, $language=null) {

    global $i18n, $LANG;

    return i18n_merge_impl($plugin, $language ? $language : $LANG, $i18n);

  }



  function i18n_merge_impl($plugin, $lang, &$globali18n) { 

    $i18n = array();

    if (!file_exists(GSPLUGINPATH.'items'.'/lang/'.$lang.'.php')) return false;

    @include(GSPLUGINPATH.'items'.'/lang/'.$lang.'.php'); 

    if (count($i18n) > 0) foreach ($i18n as $code => $text) {

      if (!array_key_exists($plugin.'/'.$code, $globali18n)) $globali18n[$plugin.'/'.$code] = $text;

    }

    return true;

  }

}



// GetSimple 3.0 function - compatibility

if (!function_exists('i18n')) {

  function i18n($name, $echo=true) {

	  global $i18n, $LANG;

	  if (array_key_exists($name, $i18n)) {

		  $myVar = $i18n[$name];

	  } else {

		  $myVar = '{'.$name.'}';

	  }

    if ($echo) echo $myVar; else return $myVar;

  } 

}

?>
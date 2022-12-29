<?php
require_once(GSPLUGINPATH.'i18n_base/frontend.class.php');

class I18nSitemap {
    
  public static function generateSitemapWithoutPing() {
    global $SITEURL;

    $filenames = getFiles(GSDATAPAGESPATH);
    if (count($filenames)) { 
      foreach ($filenames as $file) {
        if ( isFile($file, GSDATAPAGESPATH, 'xml')) {
          $data = getXML(GSDATAPAGESPATH . $file);
          if ($data->url != '404' && $data->private != 'Y') {
            $pagesArray[] = array(
              'url' => (string) $data->url, 
              'parent' => (string) $data->parent,
              'date' => (string) $data->pubDate,
              'menuStatus' => (string) $data->menuStatus
            );
          }
        }
      }
    }
    $pagesSorted = subval_sort($pagesArray,'menuStatus');
    
    $languages = return_i18n_available_languages();
    $deflang = return_i18n_default_language();
    
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset></urlset>');
    $xml->addAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd', 'http://www.w3.org/2001/XMLSchema-instance');
    $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    if (count($pagesSorted)) { 
      foreach ($pagesSorted as $page) { 
        // set <loc>
        if (count($languages) > 1) {
          $pos = strrpos($page['url'],'_');
          if ($pos !== false) {
            $pageLoc = find_i18n_url(substr($page['url'],0,$pos), $page['parent'], substr($page['url'],$pos+1));
          } else {
            $pageLoc = find_i18n_url($page['url'], $page['parent'], $deflang);
          }
        } else {
          $pageLoc = find_i18n_url($page['url'], $page['parent']);
        }      
        // set <lastmod>
        $pageLastMod = makeIso8601TimeStamp(date("Y-m-d H:i:s", strtotime($page['date'])));
        // set <changefreq>
        $pageChangeFreq = 'weekly';
        // set <priority>
        $pagePriority = $page['menuStatus'] == 'Y' ? '1.0' : '0.5';
        //add to sitemap
        $url_item = $xml->addChild('url');
        $url_item->addChild('loc', htmlspecialchars($pageLoc));
        $url_item->addChild('lastmod', $pageLastMod);
        $url_item->addChild('changefreq', $pageChangeFreq);
        $url_item->addChild('priority', $pagePriority);
      }
    }
    //create xml file
    $file = GSROOTPATH .'sitemap.xml';
    XMLsave($xml, $file);
  }
  
  public static function generateSitemap() {
    global $SITEURL;

    self::generateSitemapWithoutPing();
    if (!defined('GSDONOTPING') || !GSDONOTPING) {
      if (file_exists(GSROOTPATH .'sitemap.xml')){
        if( 200 === ($status=pingGoogleSitemaps($SITEURL.'sitemap.xml'))) {
          #sitemap successfully created & pinged
          return true;
        } else {
          error_log(i18n_r('SITEMAP_ERRORPING'));
          return i18n_r('SITEMAP_ERRORPING');
        }
      } else {
        error_log(i18n_r('SITEMAP_ERROR'));
        return i18n_r('SITEMAP_ERROR');
      }
    } else {
      #sitemap successfully created - did not ping
      return true;
    }
  }
  
  public static function isMultipleLanguages() {
    global $PERMALINK;
    if (@strpos(@$PERMALINK,'%language%') !== false || @strpos(@$PERMALINK,'%nondefaultlanguage%') !== false) {
      return true;
    } else {
      $languages = return_i18n_available_languages();
      return count($languages) > 1;
    }
  }
  
  public static function isAutoSitemap() {
    return !defined('GSNOSITEMAP') || !GSNOSITEMAP;
  }

  // execute action for all registered functions after the given one
  public static function executeOtherFunctions($action, $function) {
    global $plugins;
    $done = true;
    foreach ($plugins as $hook) {
      if ($hook['hook'] == $action) {
        if (!$done) {
          call_user_func_array($hook['function'], $hook['args']);
        } else if ($hook['function'] == $function) {
          $done = false;
        }
      }
    }
  }
  
  public static function patchSaveFile() {
    global $url;
    if (!self::isAutoSitemap()) return;
    self::generateSitemap();
    // redirect user back to edit page 
    if (isset($_POST['autosave']) && $_POST['autosave'] == 'true') {
      echo 'OK';
    } else {
      
      if (@$_POST['redirectto'] != '') {
        $redirect_url = $_POST['redirectto'];
      } else {
        $redirect_url = 'edit.php';
      }
      
      if (!@$_POST['existing-url'] || $url == $_POST['existing-url']) {
        redirect($redirect_url."?id=". $url ."&upd=edit-success&type=edit");
      } else {
        redirect($redirect_url."?id=". $url ."&old=".$_POST['existing-url']."&upd=edit-success&type=edit");
      }
    }
    die;
  }
  
  public static function patchDeleteFile() {
    global $id;
    if (!self::isAutoSitemap()) return;
    // (re)generate sitemap if there are multiple languages, as it was just generated incorrectly
    if (self::isMultipleLanguages()) self::generateSitemap();
    redirect("pages.php?upd=edit-success&id=". $id ."&type=delete");
    die;
  }
  
  public static function patchSettings() {
    if (!self::isAutoSitemap()) return;
    // (re)generate sitemap if there are multiple languages, as it was just generated incorrectly
    if (self::isMultipleLanguages()) self::generateSitemap();
  }
  
}

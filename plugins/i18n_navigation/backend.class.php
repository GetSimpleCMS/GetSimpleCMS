<?php

class I18nNavigationBackend {

  public static function outputHeader() {
    if (basename($_SERVER['PHP_SELF']) == 'edit.php') {
      global $SITEURL;
      echo '<link rel="stylesheet" href="'.$SITEURL.'plugins/i18n_navigation/css/jquery.autocomplete.css" type="text/css" charset="utf-8" />'."\n";
      echo '<script type="text/javascript" src="'.$SITEURL.'plugins/i18n_navigation/js/jquery.autocomplete.min.js"></script>'."\n";
    }
    echo '<script type="text/javascript">$(function() { $("#sb_menumanager").hide(); });</script>';
  }
  
  public static function clearCache() {
    $cachefile = GSDATAOTHERPATH . I18N_CACHE_FILE;
    if (file_exists($cachefile)) unlink($cachefile);
  }

}

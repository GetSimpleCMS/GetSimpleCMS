<?php
require_once(GSPLUGINPATH.'i18n_base/sitemap.class.php');

$result = I18nSitemap::generateSitemap();
if ($result === true) {
  if (!defined('GSDONOTPING') || !GSDONOTPING) {
    $result = i18n_r('SITEMAP_CREATED');
  } else {
    $result = i18n_r('SITEMAP_ERRORPING');
  }
  $url = 'theme.php?success=' . urlencode($result);
} else {
  $url = 'theme.php?err=' . urlencode($result);
}
echo $result;
echo '<script type="text/javascript">window.location = '.json_encode($url).';</script>';


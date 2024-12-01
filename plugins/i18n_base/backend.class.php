<?php

class I18nBackend {

  public static function processPreHeader() {
    if (basename($_SERVER['PHP_SELF']) == 'pages.php') {
      header('Location: load.php?id=i18n_base');
      exit(0);
    }
  }

  public static function processHeader() {
    global $SITEURL;
    if (basename($_SERVER['PHP_SELF']) == 'pages.php') {
?>
      </head>
      <body>
        <a href="load.php?id=i18n_base">Continue to I18N ...</a>
        <script type="text/javascript">window.location = "load.php?id=i18n_base";</script>
      </body>
      </html>
<?php
      exit(0);
    } else {
?>
      <script type="text/javascript">
        $(function() { 
          $('#sidebar a[href=pages\\.php]').closest('li').hide(); 
          $('a[href=pages\\.php]').attr('href','load.php?id=i18n_base');
          <?php if (!function_exists('generate_sitemap')) { ?>
          $('a[href^=sitemap\\.php]').attr('href','load.php?id=i18n_base&sitemap');
          <?php } else {  # directly to XML as sitemap.php regenerates site map ?>
          $('a[href^=sitemap\\.php]').attr('href',<?php echo json_encode($SITEURL.'sitemap.xml'); ?>);
          <?php } ?>
        });
      </script>
<?php
      if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_base') {
        global $SITEURL;
?>  
      <link rel="stylesheet" href="<?php echo $SITEURL ?>plugins/i18n_base/css/jquery.autocomplete.css" type="text/css" charset="utf-8" />
      <style type="text/css">
        #editpages tr.invisible, #editpages tr.nomatch { display: none; }
        #editpages tr.invisible.match { display: table-row; }
        #editpages tr.invisible.match a.title { color: gray; }
      </style>
      <script type="text/javascript" src="<?php echo $SITEURL ?>plugins/i18n_base/js/jquery.autocomplete.min.js"></script>
<?php
      }           
      echo '<script type="text/javascript">$(function() { $("#sb_pages").hide(); });</script>';
    }
  }
  
}

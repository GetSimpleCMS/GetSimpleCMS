<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * CKEditor template
 */

global $TEMPLATE, $SITEURL;

$EDHEIGHT = defined('GSEDITORHEIGHT') ? GSEDITORHEIGHT.'px' : '300px';
$EDTOOL = defined('GSEDITORTOOL') ? GSEDITORTOOL : 'basic';
$EDLANG = defined('GSEDITORLANG') ? GSEDITORLANG : i18n_r('CKEDITOR_LANG');
$EDOPTIONS = defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS) != '' ? ', '.GSEDITOROPTIONS : '';

if ($EDTOOL == 'advanced') {
  $TOOLBAR = "[
    ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
    '/',
    ['Styles','Format','Font','FontSize']
  ]";
} elseif ($EDTOOL == 'basic') {
  $TOOLBAR = "[['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']]";
} elseif (strpos($EDTOOL, '[') === false) {
  $TOOLBAR = "'$EDTOOL'";
} else {
  $TOOLBAR = "[$EDTOOL]";
}

$cketimestamp = defined('GSCKETSTAMP') ? GSCKETSTAMP : false;

?>
    <script type="text/javascript" src="template/js/ckeditor/ckeditor.js<?php if ($cketimestamp) echo '?t=',$cketimestamp; ?>"></script>
    <script type="text/javascript">
      <?php if ($cketimestamp) echo "CKEDITOR.timestamp = '$cketimestamp';\n"; ?>
      var editor = CKEDITOR.replace('post-content', {
        skin : 'getsimple',
        forcePasteAsPlainText : true,
        language : '<?php echo $EDLANG; ?>',
        defaultLanguage : 'en',
        <?php
        if (file_exists(GSTHEMESPATH . $TEMPLATE . '/editor.css')) {
          $path = suggest_site_path();
          ?>
          contentsCss: '<?php echo $path; ?>theme/<?php echo $TEMPLATE; ?>/editor.css',
          <?php
        }
        ?>
        entities : false,
        uiColor : '#FFFFFF',
        height: '<?php echo $EDHEIGHT; ?>',
        baseHref : '<?php echo $SITEURL; ?>',
        tabSpaces:10,
        filebrowserBrowseUrl : 'filebrowser.php?type=all',
        filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
        filebrowserWindowWidth : '730',
        filebrowserWindowHeight : '500',
        toolbar : <?php echo $TOOLBAR; ?>
        <?php echo $EDOPTIONS; ?>
      });
      linkdefault = 'url';
    </script>

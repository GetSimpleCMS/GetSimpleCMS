<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}
/**
 * CKEditor template
 */

global $SITEURL, $EDHEIGHT, $TEMPLATE, $EDTOOL, $EDLANG, $EDOPTIONS, $TOOLBAR, $EDLANG, $editor_id;
    ?>
    <script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
      var editor = CKEDITOR.replace('post-content<?php echo $editor_id; ?>', {
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
        entities : true,
        uiColor : '#FFFFFF',
        height: '<?php echo $EDHEIGHT; ?>',
        baseHref : '<?php echo $SITEURL; ?>',
        toolbar :
        [
        <?php echo $TOOLBAR; ?>
        ]
        <?php echo $EDOPTIONS; ?>,
        tabSpaces:10,
        filebrowserBrowseUrl : 'filebrowser.php?type=all',
        filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
        filebrowserWindowWidth : '730',
        filebrowserWindowHeight : '500'
      });
    </script>
    <?php
?>

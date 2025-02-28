<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }

/**
 * Footer Admin Template
 *
 * @package GetSimple
 */

?>
        <div id="footer">
        <div class="footer-left" >
        <?php

        include(GSADMININCPATH ."configuration.php");
        if (cookie_check()) {
            echo '<p><a href="pages.php">'.i18n_r('PAGE_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="upload.php">'.i18n_r('FILE_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="theme.php">'.i18n_r('THEME_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="backups.php">'.i18n_r('BAK_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="plugins.php">'.i18n_r('PLUGINS_MANAGEMENT').'</a> &nbsp;&bull;&nbsp; <a href="settings.php">'.i18n_r('GENERAL_SETTINGS').'</a> &nbsp;&bull;&nbsp; <a href="support.php">'.i18n_r('SUPPORT').'</a> &nbsp;&bull;&nbsp; <a href="share.php?term='.i18n_r('SHARE').'" rel="fancybox_s" >'.i18n_r('SHARE').'</a></p>';
        
            // draw sidebar items if no sidebar
            $menuitems = getDef('GSNOSIDEBAR',false,true);
            $current   = get_filename_id();
            if(in_array($current,$menuitems)){
                GLOBAL $sidemenudefinition,$tabdefinition,$sidemenutitles; // global?
                if(isset($sidemenudefinition[$current])){
                    $tab = $sidemenudefinition[$current];
                    if(empty($tab)) $tab = $current;
                    if(isset($tabdefinition[$tab])){
                        echo "<p>";
                        foreach($tabdefinition[$tab] as $item){
                            echo '<a href="'.$item.'.php">';
                            echo strip_tags(i18n_r($sidemenutitles[$item]));
                            echo "</a> &nbsp;•&nbsp; ";
                        }
                        echo "</p>";
                    }
                }
            }    
        }

        if(!isAuthPage()){ ?>
            <p>&copy; 2009-<?php echo date('Y'); ?> <a href="http://get-simple.info/" target="_blank" >GetSimple CMS</a>
                <?php echo '&ndash;'. i18n_r('VERSION') .' '. $site_version_no;  ?>
            </p> 
        </div> <!-- end .footer-left -->
        <div class="gslogo" >
            <a href="<?php echo $site_link_back_url; ?>" target="_blank" ><img src="template/images/getsimple_logo.gif" alt="GetSimple Content Management System" /></a>
        </div>
        <div class="clear"></div>
        <?php
            get_scripts_backend(true);
            exec_action('footer-pre'); // INTERNAL USE ONLY!
            echo "<!-- end #footer-pre -->";
            exec_action('footer'); 
        }
        ?>

        </div><!-- end #footer -->
        <?php
        if(!isAuthPage()) {
            if (isDebug()){
                outputDebugLog();
            }
        }
        ?>
    </div><!-- end .wrapper -->

<?php exec_action('footer-body-end'); // @hook footer-body-end before html body closing ?> 
</body>
</html>
<?php exec_action('footer-end'); // @hook footer-end the end before php flushes its output  ?>
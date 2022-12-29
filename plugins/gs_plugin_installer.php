<?php

// No direct access
defined('IN_GS') or die('Cannot load plugin directly.');


/**
 * Only used for development
 * Uncomment to enable full error reporting
 **********************************************************************/
// error_reporting(E_ALL);
// ini_set("display_errors", "on");


/**
 *  Gets the plugin "id"
 **********************************************************************/
$thisfile = basename(__FILE__, ".php");


/**
 *  Include the plugin installer class
 **********************************************************************/
require_once($thisfile . "/PluginInstaller.class.php");


/**
 *  Register the plugin
 **********************************************************************/
register_plugin(
    $thisfile,
    'GS Plugin Installer',
    '1.4.8',
    'Helge Sverre',
    'https://helgesverre.com/',
    'Let\'s you browse, install and uninstall plugins from your administration area.',
    'plugins',
    'gs_plugin_installer_init'
);


/**
 *  Add link to plugin in sidebar
 **********************************************************************/
add_action('plugins-sidebar', 'createSideMenu', array($thisfile, "Plugin Installer"));

// Only queue scripts when we are actually executing this plugin
if (isset($_GET['id']) && $_GET['id'] === $thisfile) {
    /**
     *  Register scripts
     **********************************************************************/
    register_script('datatables_js', '//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js', '1.0');
    register_script('gs_plugin_installer_js', $SITEURL . 'plugins/gs_plugin_installer/js/script.js', '0.1');


    /**
     *  Register the styles
     **********************************************************************/
    register_style('datatables_css', '//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css', '1.0', 'screen');
    register_style('gs_plugin_installer_css', $SITEURL . 'plugins/gs_plugin_installer/css/style.css', '0.1', 'screen');


    /**
     *  Queue the scripts
     **********************************************************************/
    queue_script('datatables_js', GSBACK);
    queue_script('gs_plugin_installer_js', GSBACK);


    /**
     *  Queue the styles
     **********************************************************************/
    queue_style('gs_plugin_installer_css', GSBACK);
    queue_style('datatables_css', GSBACK);
}



/**
 * Function responsible for initializing the plugin
 */
function gs_plugin_installer_init() {

    /**
     *  Import localization files, default to english
     **********************************************************************/
    i18n_merge('gs_plugin_installer') || i18n_merge('gs_plugin_installer', "en_US");

    /**
     * Initialize our PluginInstaller object
     **********************************************************************/
    $Installer = new PluginInstaller( dirname(__FILE__) . "/gs_plugin_installer/plugin_cache.json");


    /**
     * Run our plugin
     **********************************************************************/
    gs_plugin_installer_main($Installer);
}


/**
 * Main plugin function, runs all the logic, you could call it the "controller"
 * @param PluginInstaller $pluginInstaller an instance of the PluginInstaller class.
 */
function gs_plugin_installer_main($pluginInstaller)
{

    // TODO(03.07.2015) ~ Helge: Move a lot of this logic into a "controller" class, leaving this method as only a routing function

    if (isset($_GET["update"])) {
        $pluginInstaller->deleteCache();
        ?>
        <script>
        $(function () {
            $('div.bodycontent').before('<div class="updated" style="display:block;"><?php i18n("gs_plugin_installer/CACHE_REFRESHED"); ?></div>');
                $('.updated, .error').fadeOut(300).fadeIn(500);
            });
        </script>
        <?php
    }


    if (isset($_GET["install"])) {
        $plugin_ids = $_GET["plugins"];
        $installed = 0;

        if (is_array($plugin_ids)) {
            foreach($plugin_ids as $plugin_id) {
                if($pluginInstaller->installPlugin($plugin_id)) {
                    $installed++;
                }
            }
        } else {
            if($pluginInstaller->installPlugin($plugin_ids)) {
                $installed++;
            }
        }

        $install_msg = ($installed ? $installed . i18n_r("gs_plugin_installer/INSTALLED_SUCCESS") : i18n_r("gs_plugin_installer/INSTALLED_FAIL"));

        ?>
        <script>
            $(function () {
                $('div.bodycontent').before('<div class="<?php echo ($installed ? 'updated' : 'error'); ?>" style="display:block;">' + "<?php echo $install_msg ?>" + '</div>');
                $('.updated, .error').fadeOut(300).fadeIn(500);
            });
        </script>

    <?php
    }

    if (isset($_GET["uninstall"])) {

        $plugin_ids = $_GET["plugins"];
        $uninstalled = 0;

        if (is_array($plugin_ids)) {
            foreach($plugin_ids as $plugin_id) {
                if($pluginInstaller->uninstallPlugin($plugin_id)) {
                    $uninstalled++;
                }
            }
        } else {
            if($pluginInstaller->uninstallPlugin($plugin_ids)) {
                $uninstalled++;
            }
        }

        $uninstall_msg = ($uninstalled ? $uninstalled . i18n_r("gs_plugin_installer/UNINSTALLED_SUCCESS") : i18n_r("gs_plugin_installer/UNINSTALLED_FAIL"));

        ?>
        <script>
            $(function () {
                $('div.bodycontent').before('<div class="<?php echo ($uninstalled ? 'updated' : 'error'); ?>" style="display:block;">' + "<?php echo $uninstall_msg ?>" + '</div>');
                $('.updated, .error').fadeOut(300).fadeIn(500);
            });
        </script>
    <?php

    }

    $plugins = $pluginInstaller->getPlugins();

    ?>

    <script>
        (function(i18n) {
            i18n['CONFIRM_UNINSTALL'] = '<?php i18n("gs_plugin_installer/CONFIRM_UNINSTALL"); ?>';
            i18n['CONFIRM_UNINSTALL_ALL'] = '<?php i18n("gs_plugin_installer/CONFIRM_UNINSTALL_ALL"); ?>';
        }(GS.i18n));
    </script>
    <form id="gs_plugin_form" action="load.php" method="GET">
        <input type="hidden" name="id" value="gs_plugin_installer">
        <h3 class="floated"><?php i18n("gs_plugin_installer/PLUGIN_NAME"); ?></h3>
        <div class="edit-nav clearfix">
            <a href="load.php?id=gs_plugin_installer&update"><?php i18n("gs_plugin_installer/REFRESH"); ?></a>
            <button id="install" type="submit" name="install" value="1"><?php i18n("gs_plugin_installer/INSTALL"); ?></button>
            <button id="uninstall" type="submit" name="uninstall" value="1"><?php i18n("gs_plugin_installer/UNINSTALL"); ?></button>
        </div>

        <table id="plugin_table" class="highlight">
            <thead>
            <tr>
                <th><?php i18n("gs_plugin_installer/LIST_PLUGIN"); ?></th>
                <th><?php i18n("gs_plugin_installer/LIST_DESCRIPTION"); ?></th>
                <th><?php i18n("gs_plugin_installer/LIST_INSTALL"); ?></th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($plugins as $index => $plugin): ?>
                <tr id="tr-<?php echo $index ?>">
                    <td>
                        <a href="<?php echo $plugin->path?>" target="_blank">
                            <b><?php echo $plugin->name ?></b>
                        </a>
                    </td>
                    <td>
                        <div class="description">
                            <?php echo trim(strip_tags(nl2br($plugin->description), "<br><br/>")) ?>
                        </div>
                        <b><?php i18n("gs_plugin_installer/VERSION"); ?> <?php echo $plugin->version ?></b>
                        — <?php i18n("gs_plugin_installer/AUTHOR"); ?>:
                        <a href="<?php echo $plugin->author_url ?>" target="_blank"><?php echo $plugin->owner ?></a>
                    </td>
                    <td>
                        <?php if ($pluginInstaller->isPluginInstalled($plugin)): ?>
                            <a class="cancel" href="load.php?id=gs_plugin_installer&uninstall=1&plugins=<?php echo $plugin->id ?>">Uninstall</a>
                        <?php else: ?>
                            <a href="load.php?id=gs_plugin_installer&install=1&plugins=<?php echo $plugin->id ?>"><?php i18n("gs_plugin_installer/INSTALL"); ?></a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input name="plugins[]" type="checkbox" value="<?php echo $plugin->id ?>">
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>
<?php
}
?>

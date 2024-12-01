<?php
$thisfile=basename(__FILE__, ".php");
if(!function_exists('i18n_merge')) { // Backport from GS 3.1 for GS 2.0 support
    function i18n_merge($plugin, $language=null) {
        global $i18n, $LANG;
        return i18n_merge_impl($plugin, $language ? $language : $LANG, $i18n);
    }
    function i18n_merge_impl($plugin, $lang, &$globali18n) {
        $i18n = array();
        if (!file_exists(GSPLUGINPATH.$plugin.'/lang/'.$lang.'.php')) {
            return false;
        }
        @include(GSPLUGINPATH.$plugin.'/lang/'.$lang.'.php');
        if (count($i18n) > 0) foreach ($i18n as $code => $text) {
            if (!array_key_exists($plugin.'/'.$code, $globali18n)) {
                $globali18n[$plugin.'/'.$code] = $text;
            }
        }
        return true;
    }
    function i18n($name, $echo=true) {
        global $i18n;
        global $LANG;

        if (array_key_exists($name, $i18n)) {
            $myVar = $i18n[$name];
        } else {
            $myVar = '{'.$name.'}';
        }

        if (!$echo) {
            return $myVar;
        } else {
            echo $myVar;
        }
    }
    function i18n_r($name) {
        return i18n($name, false);
    }
}
i18n_merge($thisfile) || i18n_merge($thisfile, 'en_US');
require_once $thisfile . "/inc/include.php";
$updater_config = updater_config();

register_plugin(
    UPDATER_SHORTNAME,
    UPDATER_NAME,
    UPDATER_VERSION,
    UPDATER_AUTHOR,
    UPDATER_URL,
    UPDATER_DESCRIPTION,
    UPDATER_TABNAME,
    UPDATER_ACTION_MAIN
);

add_action(UPDATER_TABNAME.'-sidebar', 'createSideMenu', array(UPDATER_SHORTNAME, UPDATER_NAME, UPDATER_ACTION_MAIN));

if(GSVERSION >= "3.1") {
    register_script('updater_main', UPDATER_JSURL . 'main.js', UPDATER_VERSION, False);
    register_style('updater_main', UPDATER_CSSURL . 'main.css', UPDATER_VERSION, 'screen');

    queue_script('updater_main', GSBACK);
    queue_style('updater_main', GSBACK);
} else {
    add_action('header', 'updater_action_inject_scripts');
}

function updater_action_admin() {
    $updater_config = updater_config();
    updater_render_header();
    $selected_action = updater_current_action();
    updater_render_page($selected_action);
}

function updater_action_inject_scripts() { ?>
    <link rel="stylesheet" type="text/css" href="<?php echo UPDATER_CSSURL . 'main.css';?>" media="screen" />
    <script type="text/javascript" src="<?php echo UPDATER_JSURL . 'main.js' ?>"></script>
<?php }

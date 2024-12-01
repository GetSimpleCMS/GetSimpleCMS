<?php
function updater_render_header() {
    $updater_config = updater_config();
    $action = updater_current_action();
    if (isset($updater_config['submenu_actions'][$action])) {
        $submenu = $updater_config['submenu_actions'][$action];
    } else {
        $submenu = array();
        foreach ($updater_config['submenu_actions'] as $key => $submenu_items) {
            if (array_key_exists($action, $submenu_items)) {
                $submenu = $submenu_items;
                break;
            }
        }
    }
?>
    <h3 class="floated"><?php echo UPDATER_NAME; ?></h3>
    <div class="edit-nav clearfix">
<?php foreach($submenu as $action => $text) { ?>
    <a <?php if (updater_is_current_action($action)) { echo 'class="current"'; } ?> href="<?php echo updater_link($action); ?>"><?php echo $text; ?></a>
<?php } ?>
    </div>
<?php
}

function updater_render_page($page) {
    $filename = UPDATER_PAGESPATH . $page . ".php";
    if (file_exists($filename)) {
        require $filename;
    } else {
        updater_render_not_implemented($page);
    }
}

function updater_render_not_implemented($page) {
    echo '<h4>'.sprintf(i18n_r(UPDATER_SHORTNAME.'/NOT_IMPLEMENTED'), $page).'</h4>';
}

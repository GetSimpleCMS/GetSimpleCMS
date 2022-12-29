<?php
$status = updater_get_status();
?>
<h3 class="floated"><?php i18n(UPDATER_SHORTNAME.'/TITLE_GETSIMPLE_STATUS'); ?></h3>
<table class="highlight healthcheck">
    <thead>
        <tr>
            <th><?php i18n('INSTALLED'); ?></th>
            <th><?php i18n(UPDATER_SHORTNAME.'/LATEST'); ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
<?php
if ($status['getsimple']['ok'] === True) {
    $message_class = "OKmsg";
    $message = i18n_r('LATEST_VERSION');
    $update_text = i18n_r(UPDATER_SHORTNAME.'/REINSTALL_GETSIMPLE');
} elseif ($status['getsimple']['ok'] === False) {
    $message_class = "ERRmsg";
    $message = i18n_r('UPG_NEEDED') . " " . $status['getsimple']['latest'];
    $update_text = sprintf(i18n_r(UPDATER_SHORTNAME.'/UPDATE_GETSIMPLE'), $status['getsimple']['latest']);
} else {
    $message_class = "WARNmsg";
    $message = i18n_r('CANNOT_CHECK') . " " . $status['getsimple']['installed'];
    $update_text = Null;
}
?>
            <td><?php echo $status['getsimple']['installed']; ?></td>
            <td><?php echo $status['getsimple']['latest']; ?></td>
            <td><span class="<?php echo $message_class; ?>"><?php echo $message; ?></span></td>
            <td>
<?php
    if ($status['getsimple']['ok'] === False) {
        echo '<a class="update" href="'.updater_link('update_getsimple').'" title="' . $update_text . '"><span>' . $update_text . '</span></a>';
    }
?>
            </td>
        </tr>
    </tbody>
</table>
<h3 class="floated"><?php i18n(UPDATER_SHORTNAME.'/TITLE_PLUGIN_STATUS'); ?></h3>
<div class="edit-nav clearfix">
    <a href="<?php echo updater_link('update_all_plugins'); ?>"><?php i18n(UPDATER_SHORTNAME.'/MENU_UPDATE_ALL_PLUGINS'); ?></a>
</div>
<table class="highlight healthcheck">
    <thead>
        <tr>
            <th><?php i18n('PLUGIN_NAME'); ?></th>
            <th><?php i18n('INSTALLED'); ?></th>
            <th><?php i18n(UPDATER_SHORTNAME.'/LATEST'); ?></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?php foreach($status['plugins'] as $key => $plugin) {
    if ($plugin['ok'] === True) {
        $message_class = "OKmsg";
        $message = i18n_r('LATEST_VERSION');
        $update_text = sprintf(i18n_r(UPDATER_SHORTNAME.'/REINSTALL_PLUGIN'), $plugin['name']);
        $link_class = "reinstall";
    } elseif ($plugin['ok'] === False) {
        $message_class = "ERRmsg";
        $message = i18n_r('UPG_NEEDED') . " " . $plugin['latest'];
        $update_text = sprintf(i18n_r(UPDATER_SHORTNAME.'/UPDATE_PLUGIN'), $plugin['name']);
        $link_class = "update";
    } else {
        $message_class = "WARNmsg";
        $message = i18n_r('CANNOT_CHECK') . " " . $plugin['installed'];
        $update_text = Null;
        $link_class = Null;
    }

    $uninstall_text = sprintf(i18n_r(UPDATER_SHORTNAME.'/UNINSTALL_PLUGIN'), $plugin['name']);
?>
        <tr>
            <td><?php echo $plugin['name']; ?></td>
            <td><?php echo $plugin['installed']; ?></td>
            <td><?php echo $plugin['latest']; ?></td>
            <td><span class="<?php echo $message_class; ?>"><?php echo $message; ?></span></td>
            <td>
<?php if ($plugin['ok'] !== Null) { ?><a class="<?php echo $link_class;?>" href="<?php echo updater_link('update_plugin', $key); ?>" title="<?php echo $update_text; ?>"><span><?php echo $update_text; ?></span></a><?php } ?>
            </td>
            <td><a class="uninstall" href="<?php echo updater_link('uninstall_plugin', $key); ?>" title="<?php echo $uninstall_text; ?>"><span><?php echo $uninstall_text; ?></span></a></td>
            <td>
<?php if ($plugin['backup']) {
    $revert_text = sprintf(i18n_r(UPDATER_SHORTNAME.'/REVERT_PLUGIN'), $plugin['name'], $plugin['backup']);
    ?><a class="revert" href="<?php echo updater_link('revert_plugin', $key); ?>" title="<?php echo $revert_text; ?>"><span><?php echo $revert_text; ?></span></a><?php } ?>
            </td>
        </tr>
<?php } ?>
    </tbody>
</table>

<?php
function updater_get_status() {
    $status = array();
    $status['getsimple'] = updater_get_getsimple_status();
    $status['plugins'] = updater_get_all_plugin_status();
    return $status;
}

function updater_get_getsimple_status() {
    $data = get_api_details();
    if($data) {
        $apikey = json_decode($data);
        $latest_ver = $apikey->latest;
        $ok = ($apikey->status >= 1);
    } else {
        $latest_ver = Null;
        $ok = Null;
    }
    $status = array(
        "installed" => GSVERSION,
        "latest" => $latest_ver,
        "ok" => $ok,
    );

    return $status;
}

function updater_get_all_plugin_status() {
    $status = array();
    foreach (updater_get_all_plugins() as $plugin) {
        $status[$plugin] = updater_get_plugin_status($plugin);
    }
    return $status;
}

function updater_get_plugin_status($plugin_file) {
    global $plugin_info;
    $fileext = pathinfo($plugin_file, PATHINFO_EXTENSION);
    $basename = basename($plugin_file, ".$fileext");

    $status = array(
        'name' => $plugin_file,
        'installed' => Null,
    );

    if (array_key_exists($basename, $plugin_info)) {
        $status['name'] = $plugin_info[$basename]['name'];
        $status['installed'] = $plugin_info[$basename]['version'];
    }

    if (file_exists(UPDATER_PLUGIN_BACKUP_PATH . $basename)) {
        $status['backup'] = file_get_contents(UPDATER_PLUGIN_BACKUP_PATH . $basename . "/__version__.txt");
    } else {
        $status['backup'] = False;
    }

    $api_data = json_decode(get_api_details('plugin', $plugin_file));
    if ($api_data && $api_data->status == "successful") {
        $status['name'] = $api_data->name;
        $status['latest'] = $api_data->version;
        $status['file'] = $api_data->file;
        $status['id'] = $api_data->id;
        $status['ok'] = ($status['installed'] >= $status['latest']);
    } else {
        $status['latest'] = Null;
        $status['file'] = Null;
        $status['id'] = Null;
        $status['ok'] = Null;
    }

    return $status;
}

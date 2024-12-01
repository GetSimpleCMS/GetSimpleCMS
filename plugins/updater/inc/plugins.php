<?php
function updater_update_all_plugins() {
    $retval = true;
    foreach(updater_get_all_plugins() as $plugin) {
        $status = updater_get_plugin_status($plugin);
        if ($status['ok'] === False) {
            $retval &= updater_update_plugin($plugin);
        }
    }
    return $retval;
}

function updater_update_plugin($plugin_filename, $force=false) {
    $plugin = updater_get_plugin_status($plugin_filename);
    $fileext = pathinfo($plugin_filename, PATHINFO_EXTENSION);
    $basename = basename($plugin_filename, ".$fileext");
    if (!$force && $plugin['ok']) {
        return true;
    }

    if ($plugin['ok'] === Null) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_UPDATE_PLUGIN'), $plugin['name']);
        return false;
    }

    if (!updater_backup_plugin($plugin_filename)) {
        return false;
    }

    $updated_plugin_file = updater_download_plugin($plugin);
    if ($updated_plugin_file === false) {
        return false;
    }
    
    $ignore = updater_get_plugin_ignored_files($plugin_filename); 
    if (!updater_install_plugin_zip($updated_plugin_file, $basename, $ignore)) {
        @unlink($updated_plugin_file);
        return false;
    }

    @unlink($updated_plugin_file);
    return true;
}

function updater_install_new_plugin($plugin_filename) {
    $plugin = updater_get_plugin_status($plugin_filename);
    $fileext = pathinfo($plugin_filename, PATHINFO_EXTENSION);
    $basename = basename($plugin_filename, ".$fileext");
    $plugin_file = updater_download_plugin($plugin);
    
    $plugin_file = updater_download_plugin($plugin);
    if ($plugin_file === false) {
        return false;
    }

    $ignore = updater_get_plugin_ignored_files($plugin_filename); 
    if (!updater_install_plugin_zip($plugin_file, $basename, $ignore)) {
        @unlink($plugin_file);
        return false;
    }

    @unlink($plugin_file);
    return true;
}

function updater_download_plugin($plugin_data) {
    $success = false;
    if (!$plugin_data or !isset($plugin_data['file'])) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_DOWNLOAD_PLUGIN'), $plugin_data['name']);
        return false;
    }
    $url = $plugin_data['file'];
    updater_ensure_directory_exists(UPDATER_TEMPPATH);
    $destination = UPDATER_TEMPPATH . basename($url);
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_FILE => fopen($destination, "w"),
    );
    curl_setopt_array($ch, $curl_options);
    if (!curl_exec($ch)) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_DOWNLOAD_PLUGIN_CURL'), array($plugin_data['name'], curl_errno($ch), curl_error($ch)));
        return false;
    }
    return $destination;
}

function updater_install_plugin_zip($zipfile, $plugin_name, $ignore=Null) {
    $config = updater_config();
    $success = true;
    if (!file_exists($zipfile)) {
        return false;
    }
    $ziparchive = new ZipArchive();
    $tempfolder = UPDATER_TEMPPATH . $plugin_name . "/";
    updater_ensure_directory_exists($tempfolder);

    if ( $ziparchive->open($zipfile) !== true) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_OPEN_ZIPFILE'), $zipfile);
        return false;
    }

    if ($ziparchive->extractTo($tempfolder) !== true) { 
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_EXTRACT_ZIPFILE'), $zipfile);
        return false;
    }

    if (!updater_sanity_check_plugin($tempfolder, $plugin_name, $ignore)) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_PLUGIN_MALFORMED'), $plugin_name);
        rrmdir($tempfolder);
        return false;
    }

    $success &= copy($tempfolder . $plugin_name . ".php", GSPLUGINPATH . $plugin_name . ".php");
    if (file_exists($tempfolder . $plugin_name) and is_dir($tempfolder . $plugin_name)) {
        $success &= rcopy($tempfolder . $plugin_name, GSPLUGINPATH . $plugin_name, 0775, $ignore);
    }

    if (!$success) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_INSTALLING_PLUGIN'), $plugin_name);
    }
    rrmdir($tempfolder);

    return $success;
}

function updater_sanity_check_plugin($folder, $plugin_name, $ignore) {
    $config = updater_config();
    $sane = file_exists($folder . $plugin_name . ".php");
    foreach(scandir($folder) as $filename) {
        if ($filename == "." || $filename == "..") {
            continue;
        }
        if (in_array(basename($filename), $ignore)) {
            continue;
        }
        $full_filename = $folder . $filename;
        if (is_dir($full_filename)) {
            if ($filename !== $plugin_name) {
                updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_PLUGIN_MALFORMED_DIRECTORY'), $filename);
                $sane = false;
            }
        } else {
            if (strtolower($filename) !== strtolower($plugin_name . ".php")) {
                updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_PLUGIN_MALFORMED_FILE'), $filename);
                $sane = false;
            }
        }
    }
    return $sane;
}

function updater_backup_plugin($plugin_filename) {
    $success = true;
    $plugin = updater_get_plugin_info($plugin_filename);
    $fileext = pathinfo($plugin_filename, PATHINFO_EXTENSION);
    $basename = basename($plugin_filename, ".$fileext");
    $backup_dest = UPDATER_PLUGIN_BACKUP_PATH . $basename . "/";

    if (file_exists($backup_dest)) {
        rrmdir($backup_dest);
    }
    updater_ensure_directory_exists($backup_dest);
    file_put_contents($backup_dest . "__version__.txt", $plugin['version']);
    $success &= copy(GSPLUGINPATH . $plugin_filename, $backup_dest . $plugin_filename);
    if (file_exists(GSPLUGINPATH . $basename)) {
        $success &= rcopy(GSPLUGINPATH . $basename, $backup_dest . $basename);
    }

    if (!$success) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_BACKUP_PLUGIN'), $plugin['name']);
    }
    return $success;
}

function updater_revert_plugin($plugin_filename) {
    $success = true;
    $plugin = updater_get_plugin_info($plugin_filename);
    $fileext = pathinfo($plugin_filename, PATHINFO_EXTENSION);
    $basename = basename($plugin_filename, ".$fileext");
    $backup_dest = UPDATER_PLUGIN_BACKUP_PATH . $basename . "/";
    if (!file_exists($backup_dest) or !file_exists($backup_dest . $plugin_filename)) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_REVERT_PLUGIN'), $plugin['name']);
        return false;
    }

    @unlink($backup_dest . "__version__.txt");
    $success &= copy($backup_dest . $plugin_filename, GSPLUGINPATH . $plugin_filename);
    if (file_exists($backup_dest . $basename)) {
        $success &= rcopy($backup_dest . $basename, GSPLUGINPATH . $basename);
    }

    if (!$success) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_REVERT_PLUGIN'), $plugin['name']);
    } else {
        rrmdir($backup_dest);
    }
    return $success;
}

function updater_uninstall_plugin($plugin_filename) {
    $success = true;
    $plugin = updater_get_plugin_info($plugin_filename);
    $fileext = pathinfo($plugin_filename, PATHINFO_EXTENSION);
    $basename = basename($plugin_filename, ".$fileext");
    $success &= @unlink(GSPLUGINPATH . $plugin_filename);
    if (file_exists(GSPLUGINPATH . $basename)) {
        $success &= rrmdir(GSPLUGINPATH . $basename);
    }
    if (!$success) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_UNINSTALL_PLUGIN'), $plugin['name']);
    }
    return $success;
}

function updater_get_all_plugins() {
    $plugin_files = array();
    foreach (getFiles(GSPLUGINPATH) as $plugin_file) {
        $fileext = pathinfo($plugin_file, PATHINFO_EXTENSION);
        if (strtolower($fileext) == "php") {
            $plugin_files[] = $plugin_file;
        }
    }
    sort($plugin_files);
    return $plugin_files;
}

function updater_get_plugin_info($plugin_filename) {
    global $plugin_info;
    $fileext = pathinfo($plugin_filename, PATHINFO_EXTENSION);
    $basename = basename($plugin_filename, ".$fileext");

    return $plugin_info[$basename];
}

function updater_get_plugin_ignored_files($plugin_filename) {
    $config = updater_config();
    $ignore = $config['plugin_ignorable']['*'];
    if (array_key_exists($plugin_filename, $config['plugin_ignorable'])) {
        $ignore = array_merge($ignore, $config['plugin_ignorable'][$plugin_filename]);
    }
    return $ignore;
}

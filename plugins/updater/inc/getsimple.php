<?php
function updater_update_getsimple($force=false) {
    $getsimple = updater_get_getsimple_status();
    if (!$force && $getsimple['ok']) {
        return true;
    }

    if ($getsimple['ok'] === Null) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_UPDATING_GETSIMPLE'));
        return false;
    }

    if (!updater_backup_getsimple()) {
        return false;
    }

    $updated_getsimple_file = updater_download_getsimple();
    if ($updated_getsimple_file === false) {
        return false;
    }

    if (!updater_install_getsimple_zip($updated_getsimple_file)) {
        @unlink($updated_getsimple_file);
        return false;
    }

    @unlink($updated_getsimple_file);
    return true;
}

function updater_download_getsimple() {
    $success = false;
    $url = UPDATER_GETSIMPLE_LATEST_URL;
    updater_ensure_directory_exists(UPDATER_TEMPPATH);
    $destination = UPDATER_TEMPPATH . "getsimple.zip";
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_FILE => fopen($destination, "w"),
    );
    curl_setopt_array($ch, $curl_options);
    if (!curl_exec($ch)) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_DOWNLOAD_GETSIMPLE'), array(curl_errno($ch), curl_error($ch)));
        return false;
    }
    return $destination;
}

function updater_install_getsimple_zip($zipfile) {
    $success = true;
    $config = updater_config();
    if (!file_exists($zipfile)) {
        return false;
    }
    $ziparchive = new ZipArchive();
    $tempfolder = UPDATER_TEMPPATH . "getsimple/";
    updater_ensure_directory_exists($tempfolder);

    if ( $ziparchive->open($zipfile) !== true) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_OPEN_ZIPFILE'), $zipfile);
        return false;
    }

    if ($ziparchive->extractTo($tempfolder) !== true) { 
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_EXTRACT_ZIPFILE'), array($zipfile, $tempfolder));
        return false;
    }

    if (false === $gs_folder = updater_sanity_check_getsimple($tempfolder)) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_GETSIMPLE_MALFORMED'));
        rrmdir($tempfolder);
        return false;
    }

    $ignore = $config['getsimple_ignore'];
    $success &= rcopy($gs_folder, GSROOTPATH, 0775, $ignore);

    if (!$success) {
        updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_INSTALLING_GETSIMPLE'), $getsimple_name);
    }
    rrmdir($tempfolder);

    return $success;
}

function updater_sanity_check_getsimple($folder) {
    $config = updater_config();
    $known_getsimple_contents = $config['getsimple_known'];

    $contents = scandir($folder);
    $sane = count($contents) == 3;
    if ($sane) {
        foreach ($contents as $name) {
            if ($name == "." || $name == "..")
                continue;
            $getsimple_dir = $folder.$name."/";
        }

        foreach(scandir($getsimple_dir) as $filename) {
            if ($filename == "." || $filename == "..") {
                continue;
            }
            $full_filename = $getsimple_dir . $filename;

            if (!(
                array_key_exists($filename, $known_getsimple_contents) &&
                is_dir($full_filename) == $known_getsimple_contents[$filename]['is_dir']
            )) {
                $sane = false;
                updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_GETSIMPLE_UNKNOWN_FILE'), $filename);
            } elseif(array_key_exists($filename, $known_getsimple_contents)) {
                unset($known_getsimple_contents[$filename]);
            }
        }

        if (count($known_getsimple_contents) !== 0) {
            $sane = false;
            updater_set_error(i18n_r(UPDATER_SHORTNAME.'/ERROR_GETSIMPLE_MISSING_FILES'), implode(", ", array_keys($known_getsimple_contents)));
        }
    }
    if ($sane) {
        return $getsimple_dir;
    } else {
        return $sane;
    }
}

function updater_backup_getsimple() {
    // This would be nice
    return true;
}

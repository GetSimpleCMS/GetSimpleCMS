<?php
function updater_link($action=Null, $target=Null) {
    $link = "?id=" . UPDATER_SHORTNAME;
    if ($action !== Null) {
        $link .= "&$action";
    }
    if ($target !== Null) {
        $link .= "&target=$target";
    }
    return $link;
}

function updater_is_current_action($action) {
    return $action == updater_current_action();
}

function updater_current_action() {
    $updater_config = updater_config();
    $selected_action = Null;
    foreach ($updater_config['actions'] as $action) {
        if (isset($_GET[$action])) {
            $selected_action = $action;
            break;
        }
    }
    $selected_action = $selected_action ? $selected_action : $updater_config['default_action'];
    return $selected_action;
}

function updater_tempfile($filename) {
    $path = UPDATER_TEMPPATH;
    updater_ensure_directory_exists($path);
    $filename = $path . $filename;
    touch($filename);
    return $filename;
}

function updater_set_error($message, $args=Null) {
    global $updater_errors;
    if (!isset($updater_errors)) {
        $updater_errors = array();
    }

    if (is_array($args)) {
        $message = vsprintf($message, $args);
    } elseif ($args != Null) {
        $message = sprintf($message, $args);
    }

    $updater_errors[] = $message;
}

function updater_get_errors($implode=true) {
    global $updater_errors;
    if (!isset($updater_errors)) {
        $updater_errors = array();
    }

    if($implode) {
        return implode("\n", $updater_errors);
    } else {
        return $updater_errors;
    }
}

function updater_has_error() {
    global $updater_errors;
    return (isset($updater_errors) && count($updater_errors) > 0);
}

function updater_ensure_directory_exists($path) {
    if (!file_exists($path)) {
        return mkdir($path, 0777, true);
    }
    return True;
}

function updater_path_trailing_slash($path) {
    if ($path == "" || $path[strlen($path)-1] != '/') {
        return $path . "/";
    }
    return $path;
}

function updater_remake_time($dt) {
    return mktime($dt['hours'], $dt['minutes'], $dt['seconds'], $dt['mon'], $dt['mday'], $dt['year']);
}

function updater_executable_exists($executable) {
    $output = array();
    $retval = 0;
    exec("which $executable", $outout, $retval);
    return ($retval === 0);
}

function updater_recursive_dirscan($directory, $excludes=Null) {
    $filenames = array();
    $directories = array($directory);
    while(count($directories) > 0) {
        $current_dir = array_pop($directories);
        foreach (scandir($current_dir) as $filename) {
            if ($filename == "." || $filename == "..") {
                continue;
            }
            $filename = $current_dir . $filename;
            if (is_dir($filename)) {
                $filename = updater_path_trailing_slash($filename);
                if (is_array($excludes)) {
                    if (in_array($filename, $excludes)) {
                        continue;
                    }
                }
                $directories[] = $filename;
            } else {
                $filenames[] = $filename;
            }
        }
    }

    return $filenames;
}

if (!function_exists("rrmdir")) {
    function rrmdir($path) {
        if (is_file($path)) {
            return @unlink($path);
        } else {
            foreach(scandir($path) as $file) {
                if ($file == "." or $file == "..")
                    continue;
                rrmdir($path . "/" . $file);
            }
            @rmdir($path);
        }
    }
}

if (!function_exists("rcopy")) {
    function rcopy($source, $dest, $mode=0775, $ignore=Null){
        $success = true;
        if ($ignore != Null && in_array($source, $ignore))
            // continue; // removed continue; fatal error here
            return; 
        if (is_file($source)) {
            $c = copy($source, $dest);
            chmod($dest, $mode);
            return $c;
        }
         
        if (!is_dir($dest)) {
            mkdir($dest, $mode);
        }
         
        foreach(scandir($source) as $entry) {
            if ($entry == "." || $entry == "..") {
                continue;
            }
            $success &= rcopy("$source/$entry", "$dest/$entry");
        }
        return $success;
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        global $i18n;

        if (!headers_sent($filename, $linenum)) {
            header('Location: '.$url);
        } else {
            echo "<html><head><title>".i18n_r('REDIRECT')."</title></head><body>";
            if ( !defined('GSDEBUG') || (GSDEBUG != TRUE) ) {
                echo '<script type="text/javascript">';
                echo 'window.location.href="'.$url.'";';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
                echo '</noscript>';
            }
            echo i18n_r('ERROR').": Headers already sent in ".$filename." on line ".$linenum."\n";
            printf(i18n_r('REDIRECT_MSG'), $url);
            echo "</body></html>";
        }
        exit;
    }
}

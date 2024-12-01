<?php
$result = updater_update_all_plugins();
$redirect = "status";
if (!$result) {
    $message_class = "error";
    $message = updater_get_errors();
} else {
    $message_class = "success";
    $message = i18n_r(UPDATER_SHORTNAME.'/SUCCESS_UPDATE_ALL_PLUGINS');
}
redirect(updater_link($redirect) . "&$message_class=" . urlencode($message));
exit;

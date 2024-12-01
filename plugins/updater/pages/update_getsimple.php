<?php
$result = updater_update_getsimple();
$redirect = "status";
if (!$result) {
    $message_class = "error";
    $message = updater_get_errors();
} else {
    $message_class = "success";
    $message = i18n_r(UPDATER_SHORTNAME.'/SUCCESS_UPDATE_GETSIMPLE');
}
redirect(updater_link($redirect) . "&$message_class=" . urlencode($message));
exit;

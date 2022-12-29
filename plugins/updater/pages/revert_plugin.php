<?php
$target = isset($_POST['target']) ? $_POST['target'] : (isset($_GET['target']) ? $_GET['target'] : Null);
$result = updater_revert_plugin($target);
$redirect = "status";
if (!$result) {
    $message_class = "error";
    $message = updater_get_errors();
} else {
    $message_class = "success";
    $message = sprintf(i18n_r(UPDATER_SHORTNAME.'/SUCCESS_REVERT_PLUGIN'), $target);
}
redirect(updater_link($redirect) . "&$message_class=" . urlencode($message));
exit;

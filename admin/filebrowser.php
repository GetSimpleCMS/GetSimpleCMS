<?php

// deprecated legacy support redirect to upload.php?browse
header("Location: upload.php?browse&". $_SERVER['QUERY_STRING']);
exit;

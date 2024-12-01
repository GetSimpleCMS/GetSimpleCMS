<?php
if (!isset($_GET['from']) || !$_GET['from']) error404();

define('HITCOUNT_DIR', 'hitcount/'); 
define('HITCOUNT_BLACKLIST', 'hitcount_blacklist.txt');
define('HITCOUNT_BLACKLIST_COOKIE', 'hitcount_blacklisted');
define('HITCOUNT_VISIT_COOKIE', 'hitcount_visit');
define('HITCOUNT_VISIT_DURATION', 30*60);

define('GSDATAOTHERPATH', '../../../data/other/');

$visit = @$_COOKIE[HITCOUNT_VISIT_COOKIE];
if ($visit || !is_blacklisted()) {  
  $hcfile = GSDATAOTHERPATH . HITCOUNT_DIR . 'hits_'.preg_replace('/[^A-Za-z0-9-]+/','_',$_GET['from']).'.txt';
  $lp = fopen(GSDATAOTHERPATH . HITCOUNT_DIR . 'lock.txt', 'w');
  if (flock($lp, LOCK_EX)) {
    # hits
    if (file_exists($hcfile)) {
      $hits = file($hcfile);
      $hits = $hits[0] + 1;
    } else {
      $hits = 1;
    }
    $fp = fopen($hcfile, 'w');
    fputs($fp, $hits);
    fclose($fp);
    flock($lp, LOCK_UN);
  }
  fclose($lp);
}

header('Expires: -1');
header('Pragma: no-cache');
header('Cache-Control: no-cache, must-revalidate');
if (isset($_GET['js'])) {
  header('Content-Type: text/javascript');
} else if (isset($_GET['css'])) {
  header('Content-Type: text/css');
} else {
  header('Content-Type: image/gif');
  readfile('spacer.gif');
}

function error404() {
  header('HTTP/1.1 404 Not Found');
  header('Content-Type: text/plain');
  echo '404 File not found';
  exit(0);
}

function is_blacklisted() {
  if (@$_COOKIE[HITCOUNT_BLACKLIST_COOKIE]) return true;
  $blfile = GSDATAOTHERPATH . HITCOUNT_BLACKLIST;
  if (!file_exists($blfile)) return false;
  if (isset($_SERVER["REMOTE_ADDR"]))    {
    $ip = $_SERVER["REMOTE_ADDR"];
  } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))    {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
  } else if (isset($_SERVER["HTTP_CLIENT_IP"]))    {
    $ip = $_SERVER["HTTP_CLIENT_IP"];
  }
  if (!$ip) return false;
  $ips = preg_split('/\s+/',file_get_contents($blfile)); // file() has problems with \r\n on Linux
  if (in_array(trim($ip),$ips)) return true;
  return false;
}

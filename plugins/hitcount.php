<?php
/*
Plugin Name: HitCount
Description: Counts page hits and visitors
Version: 2.1.3
Author: Martin Vlcek (c) 2011
Author URI: http://mvlcek.bplaced.net

Public functions:
	return_hitcount_hits($slug)
		returns the hits for the page identified by $slug, normally return_page_slug()
	return_hitcount_visits()
		returns the number of visits to the site

Counting hits from other web sites:
  include a link to http://your-path-to-getsimple/plugins/hitcount/ping/ping.php?from=subject
  where subject is the name, under which you will see the hits one the Hits & Visits page
  it returns a 1px transparent gif, with an additional parameter js or css empty files of that type.

Blacklist:
  create a file /data/other/hitcount_blacklist.txt with IP addresses separated by blanks or newlines
  hits from these IP addresses will not be counted
*/

# get correct id for plugin
$thisfile = basename(__FILE__, ".php");

define('HITCOUNT_DIR', 'hitcount/'); 
define('HITCOUNT_INDEX_DIR', 'hitcount_index/'); 
define('HITCOUNT_BLACKLIST', 'hitcount_blacklist.txt');

define('HITCOUNT_BLACKLIST_COOKIE', 'hitcount_blacklisted');
define('HITCOUNT_BLACKLIST_DURATION', 365*24*3600);
define('HITCOUNT_VISIT_COOKIE', 'hitcount_visit');
define('HITCOUNT_VISIT_DURATION', 30*60);

# register plugin
register_plugin(
	$thisfile, 
	'HitCount', 	
	'2.1.3', 		
	'Martin Vlcek',
	'http://mvlcek.bplaced.net', 
	'Counts page hits and visitors',
	(defined('HITCOUNT_TAB') ? (string) HITCOUNT_TAB : 'support'),
	'hitcount_overview'  
);

if (basename($_SERVER['PHP_SELF']) == 'index.php') {
  # FRONTEND
  add_action('index-pretemplate', 'hitcount_init_page');
} else {
  # BACKEND
  i18n_merge('hitcount', substr($LANG,0,2));
  i18n_merge('hitcount', 'en');
  add_action((defined('HITCOUNT_TAB') ? (string) HITCOUNT_TAB : 'support').'-sidebar', 
      'createSideMenu', array($thisfile, i18n_r('hitcount/SIDEMENU')));
  add_action('header', 'hitcount_header');
  if (hitcount_gsversion() == '3.0') {
    // workaround for GetSimple 3.0:
    if (isset($_COOKIE['GS_ADMIN_USERNAME'])) setcookie('GS_ADMIN_USERNAME', $_COOKIE['GS_ADMIN_USERNAME'], 0, '/');
  }
}
add_action('pre-download', 'hitcount_init_download');  # requires Download Interceptor plugin

# set/unset cookie to blacklist computer/browser
if (basename($_SERVER['PHP_SELF']) == 'load.php' && $_GET['id'] == 'hitcount') {
  global $SITEURL;
  if (isset($_GET['setcookie'])) {
    setcookie(HITCOUNT_BLACKLIST_COOKIE, '1', time()+HITCOUNT_BLACKLIST_DURATION, parse_url($SITEURL, PHP_URL_PATH));
  } else if (isset($_GET['delcookie'])) {
    setcookie(HITCOUNT_BLACKLIST_COOKIE, '', time()-3600, parse_url($SITEURL, PHP_URL_PATH));
  }
}

function hitcount_gsversion() {
  @include(GSADMININCPATH.'configuration.php');
  return GSVERSION;
}


# ===== FRONTEND HOOKS =====

function hitcount_init_page() {
  global $url;
  hitcount_init($url);
}

function hitcount_init_download() {
  global $file;
  hitcount_init(substr($file,strlen(GSDATAUPLOADPATH)));
}

# ===== FRONTEND FUNCTIONS =====

function return_hitcount_hits($slugOrFile) {
  $hits = @file(GSDATAOTHERPATH . HITCOUNT_DIR . 'hits_' . preg_replace('/[^A-Za-z0-9\.-]+/','_',$slugOrFile) . '.txt');
	return $hits ? $hits[0] : 0;
}

function return_hitcount_visits() {
	$visits = @file(GSDATAOTHERPATH . HITCOUNT_DIR . 'visits.txt');
	return $visits ? $visits[0] : 0;
}


# ===== BACKEND PAGES =====

function hitcount_header() {
  if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'hitcount') {
    include(GSPLUGINPATH.'hitcount/header.php');
  }
}

function hitcount_overview() {
  $hcdir = GSDATAOTHERPATH . HITCOUNT_DIR;
  if (!file_exists($hcdir)) {
    mkdir(substr($hcdir,0,strlen($hcdir)-1), 0777);
    $fp = fopen($hcdir . '.htaccess', 'w');
    fputs($fp, 'Deny from all');
    fclose($fp);
  }
  include(GSPLUGINPATH.'hitcount/backend.php');
}


# ===== OTHER FUNCTIONS =====

function hitcount_init($slugOrFile) {
  $hcdir = GSDATAOTHERPATH . HITCOUNT_DIR;
  if (!file_exists($hcdir)) {
    mkdir(substr($hcdir,0,strlen($hcdir)-1), 0777);
    $fp = fopen($hcdir . '.htaccess', 'w');
    fputs($fp, 'Deny from all');
    fclose($fp);
  }
  $visit = @$_COOKIE[HITCOUNT_VISIT_COOKIE];
  if (!$visit && hitcount_is_blacklisted()) return;
  $lp = fopen(GSDATAOTHERPATH . HITCOUNT_DIR . 'lock.txt', 'w');
  if (flock($lp, LOCK_EX)) {
    global $SITEURL;
    if (file_exists(GSDATAOTHERPATH . HITCOUNT_DIR . 'log.txt')) {
      require_once(GSPLUGINPATH.'hitcount/splitter.class.php');
      HitcountSplitter::split();  
    }
    # visitors
    if (!$visit) {
      $visit = hitcount_count($hcdir . 'visits.txt');
      $country = hitcount_get_country();
      if ($country) $visit = $visit.'/'.$country;
    }
    setcookie(HITCOUNT_VISIT_COOKIE, $visit, time()+HITCOUNT_VISIT_DURATION, parse_url($SITEURL, PHP_URL_PATH));
    # hits
    hitcount_count($hcdir . 'hits_' . preg_replace('/[^A-Za-z0-9\.-]+/','_',$slugOrFile) . '.txt');
    # log
    $time = time();
    $dateprops = getdate($time);
    $fp = fopen(GSDATAOTHERPATH . HITCOUNT_DIR . 'log_' . sprintf("%4d%02d",$dateprops['year'],$dateprops['mon']) . '.txt', 'a');
    $referer = @$_SERVER["HTTP_REFERER"];
    $useragent = @$_SERVER["HTTP_USER_AGENT"];
    $languages = @$_SERVER["HTTP_ACCEPT_LANGUAGE"];
    fputs($fp, $time . " " . $visit . " " . preg_replace('/\s+/','_',$slugOrFile) . " " . $referer . " " . $useragent . ($languages ? ' ('.$languages.')' : '') . "\n");
    fclose($fp);
    flock($lp, LOCK_UN);
  }
  fclose($lp);
}

function hitcount_count($hcfile) {
  if (file_exists($hcfile)) {
    $hits = file($hcfile);
    $hits = $hits[0] + 1;
  } else {
    $hits = 1;
  }
  $fp = fopen($hcfile, 'w');
  fputs($fp, $hits);
  fclose($fp);
  return $hits;
}

function hitcount_reset() {
  $dir_handle = @opendir(GSDATAOTHERPATH . HITCOUNT_DIR) or die("Unable to open hitcount directory");
  while ($filename = readdir($dir_handle)) {
    if (!is_dir(GSDATAOTHERPATH . HITCOUNT_DIR . $filename) && $filename != '.htaccess') {
      unlink(GSDATAOTHERPATH . HITCOUNT_DIR . $filename);
    }
  }
}

function hitcount_is_blacklisted() {
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

function hitcount_get_country() {
  if (file_exists(GSDATAOTHERPATH.'ip2country.txt')) {
    $ip = sprintf('%010u', ip2long($_SERVER['REMOTE_ADDR']));
    $fp = fopen(GSDATAOTHERPATH.'ip2country.txt', "r");
    $min = 0;
    $max = (int) (filesize(GSDATAOTHERPATH.'ip2country.txt') / 26) - 1;
    while ($max > $min) {
      $cur = (int) (($min + $max) / 2);
      fseek($fp, $cur*26);
      $entry = fgets($fp, 24+1);
      if ($ip < substr($entry,0,10)) {
        $max = $cur-1;
      } else if ($ip > substr($entry,11,10)) {
        $min = $cur+1;
      } else {
        return substr($entry,22,2);
      }
    }
  }
  return null;
}



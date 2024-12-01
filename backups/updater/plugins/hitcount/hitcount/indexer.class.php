<?php

global $hitcount_ua_browsers, $hitcount_ua_systems, $hitcount_durations, $hitcount_robots_pattern;
$hitcount_ua_browsers = array("firefox", "msie", "opera mini", "opera", "chrome", "safari",
                    "seamonkey", "konqueror", "netscape", "netfront",
                    "gecko", "navigator", "mosaic", "lynx", "amaya",
                    "omniweb", "avant", "camino", "flock", "aol", "mozilla");
$hitcount_ua_systems = array('windows nt 5.0' => array('Windows','2000'), 'windows nt 5.1' => array('Windows','XP'),
                    'windows nt 5.2' => array('Windows','XP'), 'windows nt 6.0' => array('Windows','Vista'),
                    'windows nt 6.1' => array('Windows','7'), 'windows nt' => array('Windows','NT'), 
                    'windows|win32' => array('Windows',''), 
                    'android ([\d\.]+)' => array('Android',''),
                    '(?:ipod|iphone|ipad).*([\d\._]+)?\s+like mac os x' => array('iOS',''),
                    'mac os x (\d+[\._]\d+)' => array('Mac OS X',''),
                    'ubuntu/([\d\.]+)' => array('Ubuntu',''), 'linux' => array('Linux',''), 
                    'j2me' => array('J2ME',''), 'midp' => array('J2ME',''));
$hitcount_durations = array(
  array('max' =>   30, 'text' => '_DUR_00030'),
  array('max' =>   60, 'text' => '_DUR_00060'),
  array('max' =>  120, 'text' => '_DUR_00120'),
  array('max' =>  180, 'text' => '_DUR_00180'),
  array('max' =>  240, 'text' => '_DUR_00240'),
  array('max' =>  300, 'text' => '_DUR_00300'),
  array('max' =>  600, 'text' => '_DUR_00600'),
  array('max' =>  900, 'text' => '_DUR_00900'),
  array('max' => 1800, 'text' => '_DUR_01800'),
  array('max' => 2700, 'text' => '_DUR_02700'),
  array('max' => 3600, 'text' => '_DUR_03600'),
  array('max' =>    0, 'text' => '_DUR_99999')
);
$hitcount_robots_pattern = '/bot|spider|crawler|curl|slurp|aboundex|^$/i';


class HitcountIndexer {
  
  private static function init() {
    $hcdir = GSDATAOTHERPATH . HITCOUNT_INDEX_DIR;
    if (!file_exists($hcdir)) {
      mkdir(substr($hcdir,0,strlen($hcdir)-1), 0777);
      $fp = fopen($hcdir . '.htaccess', 'w');
      fputs($fp, 'Deny from all');
      fclose($fp);
    }
  }
  
  public static function indexMonth($year,$month) {
    global $hitcount_ua_browsers, $hitcount_ua_systems, $hitcount_durations, $hitcount_robots_pattern;
    self::init();
    $visnum = null;
    $day = null;
    $until = null;
    $isRobot = false;
    $visitday = $visitbegin = $visitend = 0;
    $numhits = 0;
    $visits['total'] = array();
    $hits['total'] = array();
    # names and values
    $namesAndValues = array();
    $lines = @file(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_names.txt');
    if ($lines) foreach ($lines as $line) {
      $namesAndValues[trim($line)] = 1;
    }
    unset($namesAndValues['']);
    # date range
    $firstdate = $lastdate = null;
    $lines = @file(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_dates.txt');
    if ($lines) {
      $firstdate = trim(@$lines[0]);
      $lastdate = trim(@$lines[1]);
    }
    # days in month
    $dateprops = getdate(mktime(0,0,0,$month+1,1,$year)-3600*6);
    $daysinmonth = $dateprops['mday'];
    # read log of month  
    $f = fopen(GSDATAOTHERPATH . HITCOUNT_DIR . 'log_' . sprintf("%4d%02d",$year,$month) . '.txt', 'r');
    while (($line = fgets($f)) !== false) {
      $parts = preg_split('/ /', $line, 5);
      $time = (int) $parts[0];
      $pos = strpos($parts[1],'/');
      if ($pos === false) {
        $visit = (int) $parts[1];
        $country = null;
      } else {
        $visit = (int) substr($parts[1],0,$pos);
        $country = substr($parts[1],$pos+1);
      }
      $slug = $parts[2];
      $referer = $parts[3];
      $ua = trim($parts[4]);
      if (!$day || $until <= $time) {
        $dateprops = getdate($time);
        if ($dateprops['mon'] == $month) $day = $dateprops['mday']; else if (!$day) $day = 1; // daylight savings work around
        $date = sprintf('%04d%02d%02d',$year,$month,$day);
        if (!$firstdate || $date < $firstdate) $firstdate = $date;
        if (!$lastdate || $date > $lastdate) $lastdate = $date;
        $until = mktime(0,0,0,$month,$day+1,$year);
      }
      if ($visit != $visnum) {
        if ($visnum && !$isRobot) {
          # duration
          $duration = $visitend - $visitbegin;
          foreach ($hitcount_durations as $d) {
            if ($d['max'] <= 0 || $d['max'] > $duration) {
              $value = $d['text'];
              $visits['duration'][$value][$visitday] = @$visits['duration'][$value][$visitday] + 1;
              $hits['duration'][$value][$visitday] = @$hits['duration'][$value][$visitday] + $numhits;
              $namesAndValues["duration $value"] = 1;
              break;
            }
          }
        }
        $visitday = $day;
        $numhits = 0;
        $val = array();
        $slugs = array();
        $isRobot = preg_match($hitcount_robots_pattern, $ua);
        if (!$isRobot) {
          # country
          if ($country) {
            $val['country'] = $country;
            $namesAndValues['country '.$val['country']] = 1;
          }
          # language
          if (preg_match("#[;(]\s*([a-z][a-z](?:-[a-z][a-z])?)[,;)]#", strtolower($ua), $match)) {
            $val['lang'] = strtolower(substr($match[1],0,2));
            $val['lang_d'] = $val['lang'] . strtoupper(substr($match[1],2));
            $namesAndValues['lang '.$val['lang']] = 1;
            $namesAndValues['lang_d '.$val['lang_d']] = 1;
          }
          # operating system
          foreach ($hitcount_ua_systems as $system => $nameAndVersion) {
            if (preg_match("#$system#", strtolower($ua), $match)) {
              $name = $nameAndVersion[0];
              $version = trim(isset($match[1]) ? ' '.preg_replace('/_/','.',$match[1]) : $nameAndVersion[1]);
              $val['os'] = $name;
              $val['os_d'] = $name.($version ? ' '.$version : '');
              $namesAndValues['os '.$val['os']] = 1;
              $namesAndValues['os_d '.$val['os_d']] = 1;
              break;
            }
          }
          # browser
          foreach ($hitcount_ua_browsers as $browser) {
            if (preg_match("#($browser)[/ ]?([^/\s]*)#i", $ua, $match)) {
              $name = $match[1];
              $version = trim($match[2]); // maximum major and minor version
              if (preg_match('/^\d+(\.\d+)?/',$version,$vmatch)) $version = $vmatch[0]; else $version = '';
              $val['browser'] = $name;
              $val['browser_d'] = $name.(isset($version) ? ' '.$version : '');
              $namesAndValues['browser '.$val['browser']] = 1;
              $namesAndValues['browser_d '.$val['browser_d']] = 1;
              break;
            }
          }
          # referer
          if (preg_match('#^https?://([^/]+)(:\d+)?/#', $referer, $match)) {
            if (!str_contains($match[1],'.') || preg_match('#^(127\.|10\.|192\.168\.)#',$match[1]) ||
                preg_match('#^172\.(16|17|18|19|2\d|30|31)\.#',$match[1])) {
              $val['referer'] = '(private)';    
              $namesAndValues['referer (private)'] = 1;
            } else {
              $val['referer'] = $match[1];
              $namesAndValues['referer '.$val['referer']] = 1;
            }
          }
          # duration
          $visitbegin = $visitend = $time;
          # total
          $val['total'] = '_HUMAN';
        } else {
          $val['bot'] = str_replace('"',"'",$ua);
          $namesAndValues['bot '.$val['bot']] = 1;
          $val['total'] = '_ROBOT';
        }
        # increment visits
        $visits['total']['_TOTAL'][$visitday] = @$visits['total']['_TOTAL'][$visitday] + 1;
        foreach ($val as $name => $value) {
          $visits[$name][$value][$visitday] = @$visits[$name][$value][$visitday] + 1;
        }
      }
      # increment hits
      $hits['total']['_TOTAL'][$day] = @$hits['total']['_TOTAL'][$day] + 1;
      foreach ($val as $name => $value) {
        $hits[$name][$value][$day] = @$hits[$name][$value][$day] + 1;
      }
      # slug
      if (!$isRobot) {
        $hits['slug'][$slug][$day] = @$hits['slug'][$slug][$day] + 1;
        if (!isset($slugs[$slug])) {
          $visits['slug'][$slug][$day] = @$visits['slug'][$slug][$day] + 1;
          $namesAndValues["slug $slug"] = 1;
          $slugs[$slug] = 1;
        }
      }
      $visitend = $time;
      $visnum = $visit;
      $numhits++;
    }
    fclose($f);
    # duration of last visit
    if (!$isRobot) {
      $duration = $visitend - $visitbegin;
      foreach ($hitcount_durations as $d) {
        if ($d['max'] <= 0 || $d['max'] > $duration) {
          $value = $d['text'];
          $visits['duration'][$value][$visitday] = @$visits['duration'][$value][$visitday] + 1;
          $hits['duration'][$value][$visitday] = @$hits['duration'][$value][$visitday] + $numhits;
          $namesAndValues["duration $value"] = 1;
          break;
        }
      }
    }
    # save everything
    foreach ($visits as $name => &$values) {
      ksort($values);
      $f = fopen(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . sprintf("index_%4d%02d_%s",$year,$month,$name) . '.txt', 'w');
      foreach ($values as $value => $days) {
        $line = '';
        $sumhits = $sumvisits = 0;
        for ($day = 1; $day <= $daysinmonth; $day++) {
          $numvisits = isset($days[$day]) ? $days[$day] : 0;
          $numhits = isset($hits[$name][$value][$day]) ? $hits[$name][$value][$day] : 0;
          $line .= ' '.$numhits.'/'.$numvisits; 
          $sumvisits += $numvisits;
          $sumhits += $numhits;
        }
        $line = '"'.$value.'"' . ' ' . $sumhits . '/' . $sumvisits . $line . "\n";
        fputs($f,$line);
      }
      fclose($f);
    }
    # save names and values
    $f = fopen(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_names.txt','w');
    ksort($namesAndValues);
    foreach ($namesAndValues as $nav => $val) fputs($f,"$nav\n");
    fclose($f);
    # save dates
    $f = fopen(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_dates.txt','w');
    fputs($f,"$firstdate\n$lastdate\n");
    fclose($f);
  }
  
  public static function index() {
    $numIndexed = 0;
    $dir = @opendir(GSDATAOTHERPATH . HITCOUNT_DIR) or die("Unable to open hitcount directory");
    $msgs = array();
    while ($filename = readdir($dir)) {
      if (preg_match('/^log_(\d\d\d\d)(\d\d)\.txt$/',$filename,$match)) {
        if (!file_exists(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_'.$match[1].$match[2].'_total.txt') ||
            filemtime(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_'.$match[1].$match[2].'_total.txt') + 1800 <
            filemtime(GSDATAOTHERPATH . HITCOUNT_DIR . $filename)) {
          if ($numIndexed >= 1) {
            @closedir($dir);
            foreach ($msgs as $msg) echo "<p>".htmlspecialchars($msg).'</p>';
            echo '<script type="text/javascript">window.location = "load.php?id=hitcount";</script>';
            die;
          }
          self::indexMonth((int)$match[1],(int)$match[2]);
          $msgs[] = "Month $match[1]/$match[2] indexed...";
          $numIndexed++;
        }
      }
    }
    @closedir($dir);
  }
  
}

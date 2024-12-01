<?php

class HitcountSplitter {
  
  public static function split() {
    $fs = fopen(GSDATAOTHERPATH . HITCOUNT_DIR . 'log.txt', 'r');
    $until = null;
    $fd = null;
    while (($line = fgets($fs)) !== false) {
      $parts = preg_split('/ /', $line, 5);
      $time = (int) $parts[0];
      if ($until && $until <= $time) {
        if ($fd) fclose($fd);
        $until = null;
      }
      if (!$until) {
        $dateprops = getdate($time);
        $fd = fopen(GSDATAOTHERPATH . HITCOUNT_DIR . 'log_' . sprintf("%4d%02d",$dateprops['year'],$dateprops['mon']) . '.txt', 'w');
        $until = mktime(0,0,0,$dateprops['mon']+1,1,$dateprops['year']);
      }
      fputs($fd, $line);
    }
    fclose($fs);
    if ($fd) fclose($fd);
    rename(GSDATAOTHERPATH . HITCOUNT_DIR . 'log.txt', GSDATAOTHERPATH . HITCOUNT_DIR . 'log.txt.bak');
  }
  
}
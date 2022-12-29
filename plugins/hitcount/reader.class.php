<?php

class HitcountReader {
  
  private $maxItems;
  private $unit;
  private $minDate;
  private $maxDate;
  private $fromDate;
  private $toDate;
  private $dates;
  private $hits;
  private $visits;
  
  // $from and $to in format 'yyyyMMdd'
  public function __construct($from=null, $to=null, $maxItems=60) {
    $this->maxItems = $maxItems;
    $lines = @file(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . 'index_dates.txt');
    $this->fromDate = $this->minDate = mktime(0,0,0,substr($lines[0],4,2),substr($lines[0],6,2),substr(@$lines[0],0,4));
    $this->toDate = $this->maxDate = mktime(0,0,0,substr($lines[1],4,2),substr($lines[1],6,2),substr(@$lines[1],0,4));
    if (@$from) {
      $this->fromDate = mktime(0,0,0,substr($from,4,2),substr($from,6,2),substr($from,0,4));
      if ($this->fromDate < $this->minDate) $this->fromDate = $this->minDate;
    }
    if (@$to) {
      $this->toDate = mktime(0,0,0,substr($to,4,2),substr($to,6,2),substr($to,0,4));
      if ($this->toDate > $this->maxDate) $this->toDate = $this->maxDate;
    }
    if ($this->fromDate > $this->toDate) {
      $this->fromDate = $this->minDate;
      $this->toDate = $this->maxDate;
    }
    $this->hits = array();
    $this->visits = array();
    $days = round(($this->toDate - $this->fromDate)/(3600*24))+1;
    if ($days <= $this->maxItems) {
      $this->initDays();
    } else if ($days/7 <= $this->maxItems) {
      $this->initWeeks();
    } else if ($days*12/365 <= $this->maxItems) {
      $this->initMonths();
    } else if ($days*4/365 <= $this->maxItems) {
      $this->initQuarters();
    } else {
      $this->initYears();
    }
  }
  
  // $names = arrays of values indexed by name, no values = all values
  public function read($names) {
    if ($this->unit == 'd' || $this->unit == 'w') {
      $this->readDays($names);
    } else {
      $this->readMonths($names);
    }
    $this->calculateAllSummaries();
  }
  
  public function sort() {
    foreach ($this->hits as $name => &$values) {
      if ($name == 'duration') {
        ksort($values);
      } else {
        uasort($values, array($this,'compareTotals'));
      }
    }
  }
  
  public function getDates() { return $this->dates; }
  public function getHits() { return $this->hits; }
  public function getVisits() { return $this->visits; }
  public function getMinDate() { return $this->minDate; }
  public function getMaxDate() { return $this->maxDate; }
  public function getFromDate() { return $this->fromDate; }
  public function getToDate() { return $this->toDate; }
  public function getUnit() { return $this->unit; }
  
  private function initDays() {
    $this->unit = 'd';
    $date = $this->fromDate;
    do {
      $this->dates[] = $date;
      $date = strtotime('+1 day',$date);
    } while ($date <= $this->toDate);
  }

  private function initWeeks() {
    $this->unit = 'w';
    $dateprops = getdate($this->fromDate);
    if ($dateprops['wday'] != 1) $this->fromDate = strtotime('-'.(($dateprops['wday']+6)%7).' days', $this->fromDate);
    $dateprops = getdate($this->toDate);
    if ($dateprops['wday'] != 0) $this->toDate = strtotime('+'.(7-$dateprops['wday']).' days', $this->toDate);
    $date = $this->fromDate;
    do {
      $this->dates[] = $date;
      $date = strtotime('+1 week',$date);
    } while ($date <= $this->toDate);
  }
  
  private function initMonths() {
    $dateprops = getdate($this->fromDate);
    $this->fromDate = mktime(0,0,0,$dateprops['mon'],1,$dateprops['year']);
    $dateprops = getdate($this->toDate);
    $this->toDate = mktime(0,0,0,$dateprops['mon']+1,0,$dateprops['year']);
    $this->unit = 'm';
    $date = $this->fromDate;
    do {
      $this->dates[] = $date;
      $date = strtotime('+1 month',$date);
    } while ($date <= $this->toDate);
  }
  
  private function initQuarters() {
    $dateprops = getdate($this->fromDate);
    $this->fromDate = mktime(0,0,0,floor(($dateprops['mon']-1)/3)*3,1,$dateprops['year']);
    $dateprops = getdate($this->toDate);
    $this->toDate = mktime(0,0,0,floor(($dateprops['mon']-1)/3)*3+2,0,$dateprops['year']);
    $this->unit = 'q';
    $date = $this->fromDate;
    do {
      $this->dates[] = $date;
      $date = strtotime('+3 months',$date);
    } while ($date <= $this->toDate);
  }
  
  private function initYears() {
    $dateprops = getdate($this->fromDate);
    $this->fromDate = mktime(0,0,0,1,1,$dateprops['year']);
    $dateprops = getdate($this->toDate);
    $this->toDate = mktime(0,0,0,12,31,$dateprops['year']);
    $this->unit = 'y';
    $date = $this->fromDate;
    do {
      $this->dates[] = $date;
      $date = strtotime('+1 year',$date);
    } while ($date <= $this->toDate);
  }

  private function getFileDatesToRead() {
    $filedates = array();
    $dateProps = getDate($this->fromDate);
    $date = mktime(0,0,0,$dateProps['mon'],1,$dateProps['year']);
    while ($date <= $this->toDate) {
      $dateProps = getDate($date);
      $filedates[] = array('month' => $dateProps['mon'], 'year' => $dateProps['year']);
      $date = strtotime('+1 month', $date);
    }
    return $filedates;
  }
  
  private function readDays($names) {
    $filedates = $this->getFileDatesToRead();
    $index = 0;
    foreach ($filedates as $filedate) {
      $data = $this->readDataFor($filedate, $names);
      //$lastday = mktime(0,0,0,$filedate['month'],0,$filedate['year']);
      $lastday = mktime(0,0,0,$filedate['month']+1,1,$filedate['year'])-3600*6; // same as in indexer
      $dateProps = getdate($lastday);
      for ($day=1; $day<=$dateProps['mday']; $day++) {
        $date = mktime(0,0,0,$filedate['month'],$day,$filedate['year']);
        if ($date >= $this->fromDate && $date <= $this->toDate) {
          while ($index < count($this->dates)-1 && $this->dates[$index+1] <= $date) $index++;
          foreach ($names as $name => $values) {
            if (isset($data[$name])) {
              foreach ($data[$name] as $value => &$hv) {
                if (isset($hv[2*$day])) $this->hits[$name][$value][$index] = @$this->hits[$name][$value][$index] + $hv[2*$day];
                if (isset($hv[2*$day+1])) $this->visits[$name][$value][$index] = @$this->visits[$name][$value][$index] + $hv[2*$day+1];
              }
            }
          }
        }
      }
    }
  }
  
  private function readMonths($names) {
    $filedates = $this->getFileDatesToRead();
    $index = 0;
    foreach ($filedates as $filedate) {
      $data = $this->readDataFor($filedate, $names);
      $date = mktime(0,0,0,$filedate['month'],1,$filedate['year']);
      if ($date >= $this->fromDate && $date <= $this->toDate) {
        while ($index < count($this->dates)-1 && $this->dates[$index+1] <= $date) $index++;
        foreach ($names as $name => $values) {
          if (isset($data[$name])) {
            foreach ($data[$name] as $value => &$hv) {
              if (isset($hv[0])) $this->hits[$name][$value][$index] = @$this->hits[$name][$value][$index] + $hv[0];
              if (isset($hv[1])) $this->visits[$name][$value][$index] = @$this->visits[$name][$value][$index] + $hv[1];
            }
          }
        }
      }
    }
  }
  
  private function calculateAllSummaries() {
    foreach ($this->hits as $name => &$values) {
      foreach ($values as $value => &$numbers) {
        $this->calculateSummary($numbers);
      }
    }
    foreach ($this->visits as $name => &$values) {
      foreach ($values as $value => &$numbers) {
        $this->calculateSummary($numbers);
      }
    }
  }

  private function calculateSummary(&$numbers) {
    for ($i=0; $i<count($this->dates); $i++) {
      if (!isset($numbers[$i])) $numbers[$i] = 0;
      if ($i == 0) {
        $numbers['min'] = $numbers['max'] = $numbers['total'] = $numbers[$i];
      } else {
        $numbers['total'] += $numbers[$i];
        if ($numbers[$i] > $numbers['max']) $numbers['max'] = $numbers[$i]; else
        if ($numbers[$i] < $numbers['min']) $numbers['min'] = $numbers[$i];
      }
    }
  }
  
  private function readDataFor($filedate, $names) {
    $data = array();
    foreach ($names as $name => $values) {
      $lines = @file(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . sprintf('index_%04d%02d_%s.txt',$filedate['year'],$filedate['month'],$name));
      if (!$lines || count($lines) < 1) return $data;
      foreach ($lines as $line) {
        if (substr($line,0,1) == '"') {
          $quotePos = strpos($line,'"',1);
          $value = substr($line,1,$quotePos-1);
          if (!$values || count($values) == 0 || in_array($value,$values)) {
            $data[$name][$value] = preg_split('# |/#',trim(substr($line,$quotePos+1)));
          }
        }
      }
    }
    return $data;
  }

  private function compareTotals($a, $b) {
    return $b['total'] - $a['total'];
  }
  
}
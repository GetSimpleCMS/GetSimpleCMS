<?php

class HitcountExporter {

  public static function csvString($s) {
    return '"'.str_replace('"',"'",$s).'"';
  }

  public static function exportCSV(&$dates, &$hits, &$visits, $sep=',') {
    header("Content-Type: text/csv");
    echo $sep.'Category'.$sep.'Value';
    for ($i=0; $i<count($dates); $i++) echo $sep.strftime('%Y-%m-%d',$dates[$i]);
    echo "\r\n";
    foreach ($hits as $name => &$values) {
      foreach ($values as $value => &$numbers) {
        echo 'Hits'.$sep.self::csvString(i18n_r('hitcount/'.strtoupper($name)));
        echo $sep.self::csvString(substr($value,0,1)=='_' ? i18n_r('hitcount/V'.strtoupper($value)) : $value);
        for ($i=0; $i<count($dates); $i++) echo $sep.$numbers[$i];
        echo "\r\n";
      }
    }
    foreach ($visits as $name => &$values) {
      foreach ($values as $value => &$numbers) {
        echo 'Visits'.$sep.self::csvString(i18n_r('hitcount/'.strtoupper($name)));
        echo $sep.self::csvString(substr($value,0,1)=='_' ? i18n_r('hitcount/V'.strtoupper($value)) : $value);
        for ($i=0; $i<count($dates); $i++) echo $sep.$numbers[$i];
        echo "\r\n";
      }
    }
    exit;
  }
  
}


<?php

class HitcountCountries {
  
  public static function retrieve() {
    if (file_exists(GSDATAOTHERPATH.'ip2country.csv')) {
      self::createIndex();
    } else if (file_exists(GSDATAOTHERPATH.'ip2country.zip')) {
      self::extractZip();
    } else {
      self::downloadZip();
    }
  }
  
  private static function downloadZip() {
    // download ip-to-country list
    self::outputProgressParagraph();
    $target = fopen(GSDATAOTHERPATH.'ip2country.zip','w');
    $source = fopen('http://ip-to-country.webhosting.info/downloads/ip-to-country.csv.zip','r');
    $bytes = 0;
    while (($s = fread($source, 1024*50))) {
      fwrite($target, $s);
      $bytes += strlen($s);
      self::outputProgress('Downloading... '.((int) ($bytes/1024)).' kB');
    }
    fclose($source);
    fclose($target);
    self::redirect('load.php?id=hitcount&download');
  }
  
  private static function extractZip() {
    // unzip ip-to-country list
    self::outputProgressParagraph();
    $f = fopen(GSDATAOTHERPATH.'ip2country.csv','w');
    $zip = zip_open(GSDATAOTHERPATH.'ip2country.zip');
    $entry = zip_read($zip);
    zip_entry_open($zip, $entry);
    $bytes = 0;
    while (($s = zip_entry_read($entry,16384))) {
      fwrite($f, $s);
      $bytes += strlen($s);
      self::outputProgress('Extracting... '.((int) ($bytes/1024)).' kB');
    }
    zip_entry_close($entry);
    zip_close($zip);
    fclose($f);
    self::redirect('load.php?id=hitcount&download');
  }
  
  private static function createIndex() {
    // create index file and read countries
    self::outputProgressParagraph();
    $countries = array();
    $t = fopen(GSDATAOTHERPATH.'ip2country.txt','w');
    $f = fopen(GSDATAOTHERPATH.'ip2country.csv','r');
    $num = 0;
    while (($line = fgetcsv($f)) !== false) {
      fprintf($t, "%010u %010u %2s\r\n", $line[0], $line[1], $line[2]);
      $countries[$line[2]] = $line[4];
      $num++;
      if ($num % 1000 == 0) self::outputProgress('Indexing... '.$num.' lines');
    }
    fclose($f);
    fclose($t);
    // create country file
    ksort($countries);
    $c = fopen(GSDATAOTHERPATH.'countries.txt','w');
    foreach ($countries as $code => $name) {
      fwrite($c, "$code $name\r\n");
    }
    fclose($c);
    unlink(GSDATAOTHERPATH.'ip2country.zip');
    unlink(GSDATAOTHERPATH.'ip2country.csv');
    self::redirect('load.php?id=hitcount');
  }
  
  private static function outputProgressParagraph() {
    echo '<p id="progress"></p>';
  }
  
  private static function outputProgress($s) {
    echo '<script type="text/javascript">$("#progress").text('.json_encode($s).');</script>'."\r\n";
    flush();
  }
  
  private static function redirect($link) {
    echo '<script type="text/javascript">window.location = '.json_encode($link).';</script>';
    die;
  }
  
}
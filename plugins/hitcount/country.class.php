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
    $source = fopen(HITCOUNT_URL,'r');
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
  	if (file_exists(GSDATAOTHERPATH.'ip2country.txt')) {
  		unlink(GSDATAOTHERPATH.'ip2country.txt');
  	}
  	$f = fopen(GSDATAOTHERPATH.'ip2country.csv','w');
    $zip = zip_open(GSDATAOTHERPATH.'ip2country.zip');
    while ($entry = zip_read($zip)) {
    	if (preg_match('/^.*\.csv$/i',zip_entry_name($entry)) === 1) {
	      zip_entry_open($zip, $entry);
	      $bytes = 0;
	      while (($s = zip_entry_read($entry,16384))) {
	        fwrite($f, $s);
	        $bytes += strlen($s);
	        self::outputProgress('Extracting... '.((int) ($bytes/1024)).' kB');
	      }
	      zip_entry_close($entry);
    	}
    }
    zip_close($zip);
    fclose($f);
    self::redirect('load.php?id=hitcount&download');
  }
  
  private static function createIndex() {
    // create index file and read countries
    self::outputProgressParagraph();
    $t = fopen(GSDATAOTHERPATH.'ip2country.txt','a');
    $f = fopen(GSDATAOTHERPATH.'ip2country.csv','r');
    $iIpFrom = defined('HITCOUNT_IPFROM_COLUMN') ? HITCOUNT_IPFROM_COLUMN : 0;
    $iIpTo = defined('HITCOUNT_IPTO_COLUMN') ? HITCOUNT_IPTO_COLUMN : 1;
    $iCountryCode = defined('HITCOUNT_COUNTRYCODE_COLUMN') ? HITCOUNT_COUNTRYCODE_COLUMN : 2;
    $linelen = 70;
    $min = (int) (filesize(GSDATAOTHERPATH.'ip2country.txt') / $linelen);
    $max = $min + 50000;
    $num = 0;
    $finished = true;
    while (($line = fgetcsv($f)) !== false) {
    	if ($num >= $max) {
    		$finished = false;
    		break;
    	}
    	if ($num >= $min) {
    		if ($num % 1000 == 0) self::outputProgress('Indexing... '.$num.' lines');
    		$ipFrom = self::asIp6Hex($line[$iIpFrom]);
	    	$ipTo = self::asIp6Hex($line[$iIpTo]);
	    	$countryCode = $line[$iCountryCode];
	      fprintf($t, "%032s %032s %-2s\r\n", $ipFrom, $ipTo, $countryCode);
    	}
      $num++;
    }
    fclose($f);
    fclose($t);
    if ($finished) {
    	self::createCountries();
    	unlink(GSDATAOTHERPATH.'ip2country.zip');
    	unlink(GSDATAOTHERPATH.'ip2country.csv');
    	self::redirect('load.php?id=hitcount');
    } else {
    	self::redirect('load.php?id=hitcount&download');
    }
  }
  
  private static function createCountries() {
  	$countries = array();
  	$f = fopen(GSDATAOTHERPATH.'ip2country.csv','r');
  	$iCountryCode = defined('HITCOUNT_COUNTRYCODE_COLUMN') ? HITCOUNT_COUNTRYCODE_COLUMN : 2;
  	$iCountryName = defined('HITCOUNT_COUNTRYNAME_COLUMN') ? HITCOUNT_COUNTRYNAME_COLUMN : 3;
  	while (($line = fgetcsv($f)) !== false) {
  		$countryCode = $line[$iCountryCode];
  		$countryName = $line[$iCountryName];
  		$countries[$countryCode] = $countryName;
  	}
  	ksort($countries);
  	$c = fopen(GSDATAOTHERPATH.'countries.txt','w');
  	foreach ($countries as $code => $name) {
  		fwrite($c, "$code $name\r\n");
  	}
  	fclose($c);
  }
  
  private static function asIp6Hex($ipNumeric) {
  	if (strlen($ipNumeric) <= 10) {
  		// it is ip4 - convert to ip6
  		return sprintf('ffff%08x', intval($ipNumeric));
  	}
  	return self::convert_base_10_to_16($ipNumeric);
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
  
  private static function convert_base_10_to_16($source_str) {
  	$source = array();
  	// convert to digit array with least significant digit first
  	$source_len = strlen($source_str);
  	for ($i = 0; $i < $source_len; $i++) {
  		$c = ord($source_str[$i]);
  		if ($c >= 48 && $c <= 57) array_unshift($source, $c - 48);
  	}
  	while (count($source) > 0 && $source[count($source) - 1] === 0) array_pop($source);
 		$target = array();
 		while (count($source) > 0) {
 			// divide by $to
 			$remainder = 0;
 			for ($i = count($source) - 1; $i >= 0; $i--) {
 				$d = ($remainder << 3) + ($remainder << 1) + $source[$i];
 				$source[$i] = $d >> 4;
 				$remainder = $d & 0x0f;
 			}
 			while (count($source) > 0 && $source[count($source) - 1] === 0) array_pop($source);
 			// .. and push remainder
 			array_push($target, $remainder);
 		}
  	$target_str = '';
  	for ($i = count($target) - 1; $i >= 0; $i--) {
  		$d = $target[$i];
  		$target_str .= $d < 10 ? chr(48 + $d) : chr(87 + $d);
  	}
  	return $target_str;
  }
  
  
}
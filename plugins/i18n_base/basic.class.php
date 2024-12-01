<?php

class I18nBasic {
  
  static $settings = null;
  
  public static function getProperty($name, $default = null) {
    if (self::$settings == null) self::loadSettings();
    return isset(self::$settings[$name]) ? self::$settings[$name] : $default;
  }
  
  public static function setProperty($name, $value) {
    self::setProperties(array($name => $value));
  }
  
  public static function setProperties($properties) {
    if (self::$settings == null) self::loadSettings();
    foreach ($properties as $key => $value) self::$settings[$key] = (string) $value;
    self::saveSettings();
  }
  
  public static function addUrlsToIgnore($pattern) {
    $patterns = preg_split('/\|/', self::getProperty(I18N_PROP_URLS_TO_IGNORE, ''));
    if (!in_array($pattern, $patterns)) $patterns[] = $pattern;
    self::setProperty(I18N_PROP_URLS_TO_IGNORE, implode('|', $patterns));
  }
  
  public static function removeUrlsToIgnore($pattern) {
    $patterns = preg_split('/\|/', self::getProperty(I18N_PROP_URLS_TO_IGNORE, ''));
    $patterns = array_diff($patterns, array($pattern));
    self::setProperty(I18N_PROP_URLS_TO_IGNORE, implode('|', $patterns));
  }

  private static function loadSettings() {
    self::$settings = array();
    if (file_exists(GSDATAOTHERPATH . I18N_SETTINGS_FILE)) {
      $data = getXML(GSDATAOTHERPATH . I18N_SETTINGS_FILE);
      foreach ($data->children() as $child) self::$settings[$child->getName()] = (string) $child;
    }
  }
  
  private static function saveSettings() {
    $data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><settings></settings>');
    foreach (self::$settings as $key => $value) {
      if ($value !== null) {
        $data->addChild($key)->addCData((string) $value);
      }
    }
    XMLsave($data, GSDATAOTHERPATH . I18N_SETTINGS_FILE);
  }
  
}
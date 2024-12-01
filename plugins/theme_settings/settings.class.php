<?php

class ThemeSettings {

  private static $settings = array();

  public static function getCurrentTheme() {
    global $TEMPLATE;
    return (string) $TEMPLATE;
  }
  
  public static function isThemeConfigurable($theme=null) {
    if ($theme == null) $theme = self::getCurrentTheme();
    return $theme != null && file_exists(GSTHEMESPATH.$theme.'/settings.php');
  }

  public static function getSettings($theme=null, $defaults='default') {
    if ($theme == null) $theme = self::getCurrentTheme();
    if ($theme != null) {
      if (!array_key_exists($theme, self::$settings)) {
        self::$settings[$theme] = is_array($defaults) ? $defaults : ($defaults ? self::getSchema($theme, (string) $defaults) : array());
        $data = getXML(self::getSettingsFile($theme));
        if ($data != null) {
          foreach ($data as $child) {
            $key = $child->getName();
            self::$settings[$theme][$key] = (string) $child;
          }
        }
      }
      return self::$settings[$theme];
    }
    return array();
  }
  
  public static function getSchema($theme=null, $schemaName='default') {
    if ($theme == null) $theme = self::getCurrentTheme();
    $file = GSTHEMESPATH.$theme.'/'.$schemaName.'.properties';
    $settings = array();
    if (file_exists($file)) {
      $lines = file($file);
      foreach ($lines as $line) {
        $pos = strpos($line,'=');
        if ($pos > 0) {
          $settings[trim(substr($line,0,$pos))] = trim(substr($line,$pos+1));
        }
      }
    }
    return $settings;
  }
  
  public static function getSettingsFile($theme) {
    return GSDATAOTHERPATH.'theme_settings_'.$theme.'.xml';
  }

}
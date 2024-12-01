<?php
require_once(GSPLUGINPATH.'theme_settings/settings.class.php');

class ThemeSettingsBackend {

  public static function getConfigurableThemes() {
    $themes = array();
    $dir = opendir(GSTHEMESPATH) or die("Unable to open ".GSTHEMESPATH);
    while ($file = readdir($dir)) {
      $path = GSTHEMESPATH . $file;
      if ($file != "." && $file != ".." && is_dir($path) && file_exists($path.'/template.php') && file_exists($path."/settings.php")) {
        $themes[] = $file;
      }
    }
    sort($themes);
    return $themes;
  }
  
  public static function getSchemes($theme) {
    $names = array();
    $dir = opendir(GSTHEMESPATH.$theme);
    while ($file = readdir($dir)) {
      if (substr($file,-11) == '.properties') {
        $names[] = substr($file,0,-11);
      }
    }
    sort($names);
    return $names;
  }
  
  public static function saveSettings($theme, $settings=null) {
    if (!$settings) {
      $settings = array();
      if (@$_POST['schema']) {
        $settings = ThemeSettings::getSchema($theme, $_POST['schema']);
      }
      foreach ($_POST as $key => $value) {
		if (!in_array($key, array('save', 'apply'))) {
          $settings[$key] = $value;
        }
      }
    }
    $data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><settings></settings>');
    foreach ($settings as $key => $value) {
      $data->addChild($key, htmlspecialchars($value));
    }
    XMLsave($data, ThemeSettings::getSettingsFile($theme));
    return true;    
  }
  
  public static function resetSettings($theme) {
    $file = ThemeSettings::getSettingsFile($theme);
    if (file_exists($file)) {
      return unlink($file);
    }
    return true;
  }
  
  public static function showSettings($theme) {
    $settings = ThemeSettings::getSettings($theme);
?>
    <h3><?php i18n('theme_settings/SETTINGS_TITLE'); ?></h3>
    <form method="post" id="themeSettingsForm" action="load.php?id=theme_settings">
      <?php self::includeSettings($theme, $settings); ?>
      <div class="clear"></div>
      <p id="submitline">
        <input type="submit" name="save" value="<?php i18n('theme_settings/SAVE'); ?>" class="submit"/>
        &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
        <a class="cancel" href="load.php?id=theme_settings&amp;reset"><?php i18n('theme_settings/RESET'); ?></a>
        &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
        <a class="cancel" href="theme.php"><?php i18n('CANCEL'); ?></a>
      </p>
    </form>
<?php    
  }
  
  public static function outputSchemaSelect($theme, $default=null) {
    $settings = ThemeSettings::getSettings($theme);
    $schemes = self::getSchemes($theme);
    $schema = @$settings['schema'];
    echo '<select name="schema" id="schema" class="text">';
    if ($default !== null && $default == '') {
      echo '<option'.($schema == '' ? ' selected="selected"' : '').'></option>';
    }
    foreach ($schemes as $text) {
      echo '<option'.($schema == $text ? ' selected="selected"' : '').' value="'.htmlspecialchars($text).'">'.htmlspecialchars($text).'</option>';
    }
    echo '</select>';
  }
  
  public static function includeSettings($theme, $settings) {
    include_once(GSTHEMESPATH.$theme.'/settings.php');
  }
  
}
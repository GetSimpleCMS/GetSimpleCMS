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
    // integration with I18N plugin:
    if (function_exists('return_i18n_available_languages')) {
    	$languages = return_i18n_available_languages();
    	if (count($languages) > 1) {
	    	$deflanguage = return_i18n_default_language();
    	} else {
    		$languages = null;
    	}
    } else {
    	$languages = null;
    }
?>
    <h3 class="floated" style="float:left"><?php i18n('theme_settings/SETTINGS_TITLE'); ?></h3>
    <?php if ($languages) { ?>
  	<div id="themeSettingsLanguages" class="edit-nav" style="display:none">
   		<p>
    		<?php foreach (array_reverse($languages) as $lang) {?>
   			<a href="<?php echo '#'.$lang; ?>" class="<?php echo $lang . ($lang == $deflanguage ? ' current' : ''); ?>" onclick="setLanguage('<?php echo $lang; ?>')">
   				<?php echo $lang; ?>
   			</a>
 	  		<?php } ?>
 	  	</p>
   	</div>
   	<?php } ?>
    <form method="post" id="themeSettingsForm" action="load.php?id=theme_settings" style="clear:both">
      <?php self::includeSettings($theme, $settings); ?>
      <div class="clear"></div>
      <p id="submitline">
        <input type="submit" name="save" value="<?php i18n('theme_settings/SAVE'); ?>" class="submit"/>
        &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
        <a class="cancel" href="load.php?id=theme_settings&amp;reset"><?php i18n('theme_settings/RESET'); ?></a>
        &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
        <a class="cancel" href="theme.php"><?php i18n('CANCEL'); ?></a>
      </p>
      <?php if ($languages) { ?>
      <script type="text/javascript">
        var languages = <?php echo json_encode($languages); ?>;
        var deflanguage = <?php echo json_encode($deflanguage); ?>;
        var settings = <?php echo json_encode($settings); ?>;
      	function setLanguage(lang) {
          $('#themeSettingsLanguages a').removeClass('current');
          $('#themeSettingsLanguages a.'+lang).addClass('current');
          $('#themeSettingsForm .i18n').hide();
          $('#themeSettingsForm .i18n.'+lang).show();
      	}
      	$(function() {
          var found = false; 
	      	$('#themeSettingsForm .i18n').each(function(i, elem) {
		      	found = true;
	          $elem = $(elem);
						for (var i=0; i<languages.length; i++) {
							var lang = languages[i];
							if (lang !== deflanguage) {
								var name = $elem.attr('name');
								var value = settings[name+'_'+lang];
								$elem.clone().addClass(lang)
								  .attr('id',$elem.attr('id')+'_'+lang)
								  .attr('name',name+'_'+lang)
								  .css('display','none')
								  .val(value ? value : '')
								  .insertAfter($elem);
							}
						}
						$elem.addClass(deflanguage);
	      	});
	      	if (found) {
		      	$('#themeSettingsLanguages').css('display','block');
	      	}
      	});
      </script>
      <?php } ?>
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
<?php 
if (!class_exists('customSettings')) {

  class customSettings {
    
    private static $devMode = false;
    private static $defaultJSON = '{"site": []}';
    public static $version = '0.6.2';    
    
    //////////////////////////////////////////////////////////////////////////
    //                                                                      //
    //                    Settings retrieval from files                     //
    //                                                                      //
    //////////////////////////////////////////////////////////////////////////
    
    /** Retrieves settings created in the site
     *  Launched before other retrieves, so creates a subfolder if none is present
     */
    public static function retrieveSiteSettings() 
    {
      $path = GSDATAOTHERPATH . 'custom_settings/data.json';
      $result = array();
      if (!file_exists($path))
        self::createDataSubfolder();
      $content = json_decode(file_get_contents($path), TRUE);
      foreach ($content['site'] as $tab) {
        $result[$tab['tab']['lookup']] = $tab;
      }
      return $result;
    }
    
    /** Utility for mapping setting arrays to indexes, used in self::versionUpdate
     *  @param {array} $settings - An array of settings
     */
    public static function mapToKeys($settings)
    {
      $result = array();
      foreach ($settings as $value) 
        $result[$value['lookup']] = $value;
      return $result;
    }
    public static function adminPath() { return GSADMININCPATH; }
    /** Updates settings.json files from both plugins and themes
     *  ORI stands for origin, DAT for data
     *  @param {array} &$datFile - data file in /data/other/custom_settings
     *  @param {array} &$oriFile - data file that accompanies plugins/themes
     */
    public static function versionUpdate(Array &$datFile, Array &$oriFile)
    {
      $vDat = &$datFile['tab']['version'];
      $vOri = &$oriFile['tab']['version'];
      // convert both versions to float numbers for easy comparison
      $v1 = (float)(substr($vDat, 0, 2) . str_replace('.', '', substr($vDat, 2)));
      $v2 = (float)(substr($vOri, 0, 2) . str_replace('.', '', substr($vOri, 2)));
      // only update settings if no version in the data folder file is present or 
      // if the version is older than the one included with the theme or plugin
      if (isset($vOri) && (!isset($vDat) || $v2 > $v1)) {
        $vDat = $vOri;
        if (function_exists('delete_cache')) 
          delete_cache();
        // map both plugin/ theme and data file to lookup-based key arrays
        $oriS = self::mapToKeys($oriFile['settings']);
        $datS = self::mapToKeys($datFile['settings']);
        $merged = array();
        foreach ($oriS as $ori) {
          // if the setting already existed and is not a section title (those are always overwritten)
          if (array_key_exists($ori['lookup'], $datS) && $datS[$ori['lookup']]['type'] !== 'section-title') {
            // if the type of setting has changed, overwrite the old setting completely
            if ($ori['type'] !== $datS[$ori['lookup']]['type'])
              array_push($merged, $ori);
            // else if the type is identical, overwrite all properties except the value
            // needs to be more specific (eg for option settings)
            else {
              $oldVal = @$datS[$ori['lookup']]['value'];
              $mixS = $ori;
              $mixS['value'] = $oldVal;
              array_push($merged, $mixS);
            }
          // if the setting didn't exist, just create a new one
          } else {
            array_push($merged, $ori);
          }
        }      
      $datFile['settings'] = $merged;
      }
    }
    
    /** Retrieves settings from all plugins using this plugin
     *  if they are activated.
     */
    public static function retrievePluginSettings()
    {
      global $live_plugins, $LANG;
      $settings = array();
      foreach ($live_plugins as $plugin => $activated) {
        $pluginName = str_replace('.php', '', $plugin);
        $pluginData = GSDATAOTHERPATH . 'custom_settings/plugin_data_' . $pluginName . '.json';
        $pluginDefs = GSPLUGINPATH . $pluginName . '/settings.json';
        if ($activated != 'false' && file_exists($pluginDefs)) {
          $defContents = file_get_contents($pluginDefs);
          if (!file_exists($pluginData)) {
            file_put_contents($pluginData, $defContents);  
            $contents = json_decode($defContents, TRUE);
          } else {
            $contents = json_decode(file_get_contents($pluginData), TRUE);
          }
          $defContents = json_decode($defContents, TRUE);
          self::versionUpdate($contents, $defContents);
          if (file_exists(GSPLUGINPATH . $pluginName . '/lang/' . $LANG . '.json')) {
            // map setting indexes to a lookup dictionary for easy value assignment
            // also used to check whether an entry still exists in the data file
            $dictionary = array();
            for ($i = 0; $i < count($contents['settings']); $i++)
              $dictionary[$contents['settings'][$i]['lookup']] = $i;
              
            $pluginLang = json_decode(file_get_contents(GSPLUGINPATH . $pluginName . '/lang/' . $LANG . '.json'), TRUE);
            foreach ($pluginLang['strings'] as $string => $translation) {
              $prop = substr($string, strrpos($string, '_') + 1);
              $setting = substr($string, 0, strlen($string) - (strlen($prop) + 1));
              if (isset($dictionary[$setting])) 
                $contents['settings'][$dictionary[$setting]][$prop] = $translation;
            }
          }
          $settings[$pluginName] = $contents;
          $settings[$pluginName]['tab']['type'] = 'plugin';
        }          
      }
      return $settings;
    }
    
    /** Retrieves theme settings from data/other/custom_settings/theme_data_<name>.json if active theme has any.
     *  If first initiation, copies the data from themedir/settings.json to data folder.
     */
    public static function retrieveThemeSettings() 
    {
      global $TEMPLATE, $LANG;
      $themeDir = GSTHEMESPATH . $TEMPLATE . '/';
      $themeDataPath = GSDATAOTHERPATH . 'custom_settings/theme_data_' . strtolower($TEMPLATE) . '.json';
      $themeDefs = $themeDir . 'settings.json';
      if (file_exists($themeDataPath)) {
        if (file_exists($themeDefs)) 
          $defContents = file_get_contents($themeDefs);
        if (!file_exists($themeDataPath)) {
          file_put_contents($themeDataPath, $defContents);
          $contents = json_decode($defContents, TRUE);
        } else
          $contents = json_decode(file_get_contents($themeDataPath), TRUE);
        $defContents = json_decode($defContents, TRUE);
        self::versionUpdate($contents, $defContents);
        // output expected by JS files
        $file = array('theme_settings'=>array(
          'settings' => $contents['settings'],
          'tab' => array('lookup'=> 'theme_settings', 'type' => 'theme')));
        
        if (isset($contents['tab'])) {
          $tabOptions = array('version','enableReset','enableAccessAll','enableCodeDisplay');
          foreach ($tabOptions as $opt) {
            if (isset($contents['tab'][$opt]))
              $file['theme_settings']['tab'][$opt] = $contents['tab'][$opt];
          }
        }
        // map setting indexes to a lookup dictionary for easy value assignment
        // also used to check whether an entry still exists in the data file
        $dictionary = array();
        for ($i = 0; $i < count($file['theme_settings']['settings']); $i++)
          $dictionary[@$file['theme_settings']['settings'][$i]['lookup']] = $i;
          
        // handle lang
        if (file_exists($themeDir . 'lang/' . $LANG . '.json')) {
          $jsonLangFile = json_decode(file_get_contents($themeDir . 'lang/' . $LANG . '.json'), TRUE);
          foreach ($jsonLangFile['strings'] as $string => $translation) {
            $prop = substr($string, strrpos($string, '_') + 1);
            $setting = substr($string, 0, strlen($string) - (strlen($prop) + 1));
            if (isset($dictionary[$setting])) 
              $file['theme_settings']['settings'][$dictionary[$setting]][$prop] = $translation;
          }
        }
      } else {
        if (file_exists(GSTHEMESPATH . $TEMPLATE . '/settings.json')) {
          $defContents = file_get_contents(GSTHEMESPATH . $TEMPLATE . '/settings.json'); // default theme settings
          $defContentsPHP = json_decode($defContents, TRUE);
          file_put_contents($themeDataPath, $defContents);
          $file = array('theme_settings'=>array(
            'settings' => $defContentsPHP['settings'],
            'tab' => array('lookup'=> 'theme_settings', 'type' => 'theme')));
        } else 
          $file = array();
      }
      return $file;
    }
    
    /** Retrieves all settings from files
     *  Combination of previous retrieval functions
     */
    public static function retrieveAllSettings() 
    {
      self::gscsBackup();
      $customSettings = self::retrieveSiteSettings();
      $pluginSettings = self::retrievePluginSettings();
      $themeSettings = self::retrieveThemeSettings();
      $result = array('data'=> array_merge($customSettings, $themeSettings, $pluginSettings));
      return $result;
    }
    
    /** Maps settings to an array dictionary containing indexes
     *  Used in all display/ return functions and globalized as $custom_settings_dictionary
     */
    public static function mapAllSettings($beh =NULL) 
    {
      global $custom_settings;
      $result = array();
      // A little bit confusing, so: first iterate over $custom_settings and assign tab lookup
      // to the result, nothing special here.
      if ($beh === 'blek') {
      echo json_encode($custom_settings);
      return;
      }
      foreach ($custom_settings['data'] as $tab) {
        $result[$tab['tab']['lookup']] = array();
        // For each setting in the $custom_settings tab, assign the setting's lookup as key
        // and the setting's index as value
        for ($i = 0; $i < count($tab['settings']); $i++) {
          if (isset($tab['settings'][$i]['lookup']))
          $result[$tab['tab']['lookup']][$tab['settings'][$i]['lookup']] = $i;
        }
      }
      return $result;
    }
    
    public static function saveAllSettings($data) 
    {
      global $TEMPLATE, $custom_settings;
      require_once(GSPLUGINPATH . 'custom_settings/filehandler.class.php');
      // map paths; plugin path tildes are replaced with the plugin's lookup
      $paths = array(
        'theme' => GSDATAOTHERPATH . 'custom_settings/theme_data_' . strtolower($TEMPLATE) . '.json',
        'plugin' => GSDATAOTHERPATH . 'custom_settings/plugin_data_~~~.json',
        'site' => GSDATAOTHERPATH . 'custom_settings/data.json');
      // settings saved to data.json (site) require an extra 'site' top property
      $siteData = array('site'=>array());
      // execute plugin hook
      exec_action('custom-settings-save');
      // iterate over collection of settings as outputted by JS.
      // Each needs to save to its own file, except site tabs, therefore if a tab is of the type 'site',
      // push it to the $siteData array and save all at the end
      foreach($data['data'] as $tab) {
        $type = $tab['tab']['type'];
        $path = $paths[$type];
        unset($tab['tab']['type']);
        if ($type === 'site')  array_push($siteData['site'], $tab);
        if ($type === 'plugin')  $path = str_replace('~~~', $tab['tab']['lookup'], $path);
        if ($type !== 'site')  file_put_contents($path, fileUtils::indentJSON($tab));
      }
      file_put_contents($paths['site'], fileUtils::indentJSON($siteData));
    }
    
    private static function gscsBackup() {
      $bakpath = GSBACKUPSPATH . 'other/custom_settings/';
      if (!file_exists($bakpath))
        mkdir($bakpath); 
      $csfiles = array_diff(scandir(GSDATAOTHERPATH . 'custom_settings'), array('.', '..', '.htaccess'));
      foreach($csfiles as $csfile)
        createBak($csfile, GSDATAOTHERPATH . 'custom_settings/', $bakpath);
    }
    
    //////////////////////////////////////////////////////////////////////////
    //                                                                      //
    //                          Settings PHP API                            //
    //                                                                      //
    //////////////////////////////////////////////////////////////////////////
    
    /** Outputs a setting's value
     *  @param {string} $tab
     *  @param {string} $setting
     */
    public static function getSetting($tab, $setting, $echo=TRUE) 
    {
      global $custom_settings, $custom_settings_dictionary, $language;
      $tab = $tab === 'theme' ? 'theme_settings' : $tab;
      
      if (isset($custom_settings['data'][$tab]) && isset($custom_settings['data'][$tab]['settings'][@$custom_settings_dictionary[$tab][$setting]])) {
        $settingInArray = $custom_settings['data'][$tab]['settings'][@$custom_settings_dictionary[$tab][$setting]];
        $value = array_key_exists('value', $settingInArray) ? $settingInArray['value'] : ''; 
        $returnValue = '';
        if ($value === TRUE)  
          $returnValue = 'on';
        elseif ($value === FALSE)  
          $returnValue = 'off';
        elseif (isset($settingInArray['options']) && is_array($settingInArray['options']) && count($settingInArray['options'])) 
          $returnValue = $settingInArray['options'][$value];
        elseif ($settingInArray['type'] === 'image' && $value)
          $returnValue = '<img src="' . str_replace(' ', '%20', $value) . '" alt="' . $settingInArray['label'] . '">';
        elseif ($settingInArray['type'] === 'date')
          $returnValue = $value;
        else 
          $returnValue = $value;
        
        if ($echo === TRUE) 
          echo $returnValue;
        else
          return $returnValue;
      }
    }
    
    /** Return a setting completely, or a setting's property
     *  @param {string} $tab
     *  @param {string} $setting
     *  @param {string|boolean} [$prop=NULL] - if FALSE, returns the entire setting, else must be one of the setting's properties
     */
    public static function returnSetting($tab, $setting, $prop=NULL) 
    {
      global $custom_settings, $custom_settings_dictionary;
      $tab = $tab === 'theme' ? 'theme_settings' : $tab;
      if (isset($custom_settings_dictionary[$tab][$setting]))
        $settingInArray = $custom_settings['data'][$tab]['settings'][$custom_settings_dictionary[$tab][$setting]];
          
      if (isset($custom_settings['data'][$tab]) && isset($settingInArray)) {
        if (isset($prop)) {
          if ($prop === FALSE) 
            return $settingInArray;
          if (isset($settingInArray[$prop])) 
            return $settingInArray[$prop];
          return;
        }
        return $settingInArray['value'];
      }
      return;
    }    
    
    /** Returns a group of settings starting with the lookup $group, eg. 'social_'
     *  @param {string} $tab
     *  @param {string} $setting
     *  @param {string|boolean} [$prop=NULL] - if FALSE, returns the entire setting, else must be one of the setting's properties
     */
    public static function returnSettingGroup($tab, $group, $prop=NULL)
    {
      global $custom_settings, $custom_settings_dictionary;
      $tab = $tab === 'theme' ? 'theme_settings' : $tab;
      $tabToSearch = @$custom_settings['data'][$tab]['settings'];
      $result = array();
      if ($prop === NULL)
        $prop = 'value';
      foreach ($tabToSearch as $setting) {
        if (strpos(@$setting['lookup'], $group) !== FALSE) {
          if ($prop === FALSE) {
            $result[str_replace($group . '_', '', @$setting['lookup'])] = $setting;
          } else if (isset($setting[$prop]))
            $result[str_replace($group . '_', '', @$setting['lookup'])] = $setting[$prop];
        }
      }
      return $result;
    }
    
    /** Output an i18n-enabled (through I18N-plugin) setting completely, or a setting's property
     *  @param {string} $tab
     *  @param {string} $setting
     *  @param {string|boolean} [$prop=NULL] - if FALSE, returns the entire setting, else must be one of the setting's properties
     */
    public static function getI18nSetting($tab, $setting, $echo=TRUE) 
    {
      global $language;
      if (defined('I18N_DEFAULT_LANGUAGE') && isset($language)) {
        $langs = return_i18n_available_languages();
        $settingVals = self::returnSetting($tab, $setting, 'i18n');
        if ($echo === TRUE) 
          echo $settingVals ? $settingVals[array_search($language, $langs)] : '';
        else
          return $settingVals ? $settingVals[array_search($language, $langs)] : $settingVals;
      }
      return;
    }
    
    /** Remove a setting
     *  @param {string} $tab
     *  @param {string} $setting
     */
    public static function removeSetting($tab, $setting) 
    {
      global $custom_settings, $custom_settings_dictionary;
      if (isset($custom_settings_dictionary[$tab][$setting]))
        $settingInArray = $custom_settings['data'][$tab]['settings'][$custom_settings_dictionary[$tab][$setting]];
        
      if (isset($custom_settings['data'][$tab]) && isset($settingInArray)) {
        unset($settingInArray);
        // make sure to update the settings dictionary if a setting has been removed
        $custom_settings_dictionary = self::mapAllSettings();
      }
    }
    
    // not production ready
    /** Set a setting's value, or multiple properties to a new value
     *  @param {string} $tab
     *  @param {string} $setting
     *  @param {string|array} $newValue - if string, sets the setting's value, if array, sets the properties in the array on the setting
     */
    public static function setSetting($tab, $setting, $newValue) {
      global $custom_settings, $custom_settings_dictionary;
      if (isset($custom_settings['data'][$tab])) {
        if (isset($custom_settings['data'][$tab]['settings'][$custom_settings_dictionary[$tab][$setting]])) {
          if (strlen($newValue)) {
            $custom_settings['data'][$tab]['settings'][$custom_settings_dictionary[$tab][$setting]]['value'] = $newValue;
          }  elseif (is_array($newValue)) {
            foreach ($newValue as $prop=>$val)
              $custom_settings['data'][$tab]['settings'][$custom_settings_dictionary[$tab][$setting]][$prop] = $val;
          }
        } else {
          array_push($custom_settings['data'][$tab]['settings'], $newValue);
          // make sure to update the settings dictionary if a setting has been added
          $custom_settings_dictionary = self::mapAllSettings();
        }
      }
    }
    
    //////////////////////////////////////////////////////////////////////////
    //                                                                      //
    //                          GetSimple Hooks                             //
    //                                                                      //
    //////////////////////////////////////////////////////////////////////////
    
    /** Sets per-user editing permission
     *  Hook: settings-user
     */
    public static function setUserPermission() 
    {
      global $datau, $xml;
      // $datau holds the current user data as retrieved from the file
      // $xml holds the future user data that is going to be saved to the file
      if ($datau->KO_EDIT)
        $xml->addChild('KO_EDIT',$datau->KO_EDIT);
    }
    
    /** Gets per-user editing permission
     *  Used in init function
     */
    public static function getUserPermission() 
    {
      global $USR;
      $userdata = getXML(GSUSERSPATH . $USR . '.xml');
      if (!isset($userdata->KO_EDIT)) 
        return 'true';
      return (string)$userdata->KO_EDIT;
    }
    
    /** Sets per-user editing permission with Multi-User plugin (v1.8.2 onwards)
     *  Hook: common (requires another hook because MU overwrites the settings-user hook)
     */
    public static function mu_setUserPermission() {
      global $live_plugins;
      if (isset($live_plugins['user-managment.php']) && $live_plugins['user-managment.php'] !== 'false') {
        // set Multi User setting
        $pluginLang = self::getLangFile();
        add_mu_permission('KO_EDIT', $pluginLang['title']);    
      }
    }
    
    /** Gets per-user editing permission with Multi-User plugin (v1.8.2 onwards)
     *  Hook: common (requires another hook because MU overwrites the settings-user hook)
     */
    public static function mu_getUserPermission() {
      global $USR;
      return check_user_permission($USR, 'KO_EDIT') === false ? 'false' : 'true';
    }
    
    /** Used in contentFilter preg_replace_callback function
     *  No need to require filehandler.class.php, called in parent execution context
     *  @param {string} $matches - Match as returned by preg_replace_callback
     */
    private static function contentReplaceCallback($matches) {
      $jsonPath = fileUtils::splitPathArray($matches[1]);
      $jsonPath[0] = $jsonPath[0] === 'theme' ? 'theme_settings' : $jsonPath[0];
      $result = self::getSetting($jsonPath[0], $jsonPath[1], false);
      return $result;
    }
    
    /** Filters (% setting: tab/setting %) text from Pages content 
     *  Hook: content
     */
    public static function contentFilter($content) {
      require_once(GSPLUGINPATH . 'custom_settings/filehandler.class.php');
      $regex = '#\(%\s*setting[:=]\s*(.*?[\/]*.*?)\s*%\)#';
      $new_content = preg_replace_callback($regex, array('self', 'contentReplaceCallback'), $content);
      return $new_content;
    }
    /** Loads plugin content in plugin custom tab
     *  Hook: plugin init function in register_plugin
     */
    public static function init() 
    {
      global $custom_settings, $i18n_initialized, $LANG, $TEMPLATE, $SITEURL, $pagesArray, $id;
      exec_action('custom-settings-load');
      $tmpl = GSPLUGINPATH . 'custom_settings/tmpl/'; 
      include($tmpl . 'view.php');
      include($tmpl . 'setting-templates.html');
    }
    
    //////////////////////////////////////////////////////////////////////////
    //                                                                      //
    //                               Other                                  //
    //                                                                      //
    //////////////////////////////////////////////////////////////////////////
    
    /** Check for latest plugin version and prompt for download
     *  Returns plugin info from GS Extend API
     */
    public static function loadPluginInfo() 
    {
      $pluginInfoJSON = file_get_contents('http://get-simple.info/api/extend/?id=913');
      $pluginInfo = json_decode($pluginInfoJSON, TRUE);
      if ($pluginInfo['status'] === 'successful')
        return $pluginInfoJSON;
    }
    
    
    public static function getConfig() 
    {
      global $LANG, $TEMPLATE, $SITEURL;
      require_once(GSPLUGINPATH . 'custom_settings/filehandler.class.php');
      require_once(GSPLUGINPATH . 'custom_settings/gs.utils.php');
      $conf = array(
        'lang'        => (string)$LANG,
        'langFile'    => GSPLUGINPATH . 'custom_settings/lang/' . $LANG . '.json',
        'i18nLangs'   => function_exists('return_i18n_available_languages') ? return_i18n_available_languages() : false,
        'handler'     => $SITEURL . 'plugins/custom_settings/customsettings.handler.php',
        'dataFile'    => $SITEURL . 'custom_settings/data.json',
        'pluginVer'   => self::$version,
        'editPerm'    => GSutils::pluginIsActive('user-managment') ? self::mu_getUserPermission() : self::getUserPermission(),
        'siteTmpl'    => strtolower($TEMPLATE),
        'requestToken'=> fileUtils::requestToken('kosstt'),
        'adminDir'    => GSADMINPATH,
        'baseUrl'     => strtolower((string)$SITEURL),
        'id'          => 'custom_settings',
        'template'    => strtolower((string)$TEMPLATE)
      );
      $output = htmlspecialchars(json_encode($conf), ENT_COMPAT, 'UTF-8');
      return $output;
    }
    
    
    /** Loads JS & CSS resources for the plugin
     *  Used at start of main plugin file
     */
    public static function LoadJsLibs() 
    {
      global $SITEURL;
      require_once(GSPLUGINPATH . 'custom_settings/gs.utils.php');
      
      $main = self::$devMode ? 'main' : 'gscs';
      
      // set standard KO libraries (JS scripts and CSS styles, as well as fonts)
      // with possibility to add plugin-specific ones in the plugin folder
      
      GSutils::registerLib('custom_settings', array(
        'koStyle'         => array($SITEURL . 'plugins/custom_settings/css/gscs.css',           '1.0',  'screen'),
        'fontawesome'     => array($SITEURL . 'plugins/custom_settings/css/font-awesome.min.css',   '4.3',  'screen'),
        'requirejs'       => array($SITEURL . 'plugins/custom_settings/js/require.js',  '1.0',  FALSE),
        'koMain'          => array($SITEURL . 'plugins/custom_settings/js/' . $main . '.js',     '1.0',  TRUE)
      ));
    }    
    
    /**
     *  Creates a data folder in /data/other with necessary .htaccess and main data file
     */
    public static function createDataSubfolder() 
    {
      $path = GSDATAOTHERPATH . 'custom_settings/';
      $file = 'data.json';
      if (!file_exists($path)) mkdir($path);
      if (!file_exists($path . '.htaccess'))
        file_put_contents($path . '.htaccess','Deny from all');
      if (!file_exists($path . $file)) {
        $contents = self::$defaultJSON;
        file_put_contents($path . $file, $contents);
      }
    }
    /** Simply outputs all lang strings to the page; 
     *  called via AJAX through customsettings.handler.php
     */
    public static function getLangFile() 
    {
      global $LANG;
      $path = GSPLUGINPATH . 'custom_settings/lang/' . $LANG . '.json';
      if (!file_exists(GSPLUGINPATH . 'custom_settings/lang/' . $LANG . '.json'))
        $path = GSPLUGINPATH . 'custom_settings/lang/en_US.json';
      $f = json_decode(file_get_contents($path), TRUE);
      return $f['strings'];
    }
    
    public static function i18nMerge() 
    {
      global $LANG, $i18n;
      $path = GSPLUGINPATH . 'custom_settings/lang/' . $LANG . '.json';
      if (!file_exists($path))
        $path = GSPLUGINPATH . 'custom_settings/lang/en_US.json';
      $file = json_decode(file_get_contents($path), TRUE);
      $file['strings']['SETTINGS_UPDATED'] = $i18n['SETTINGS_UPDATED'];
      $file['strings']['OK'] = $i18n['OK'];
      $file['strings']['CANCEL'] = $i18n['CANCEL'];
      $file['strings']['BTN_SAVESETTINGS'] = $i18n['BTN_SAVESETTINGS'];
      $file['strings']['traductors'] = $file['meta']['traductors'];
      return json_encode($file['strings']);
    }
    
    public static function upgrade() 
    {
      require_once('filehandler.class.php');
      $path  = GSDATAOTHERPATH . 'custom_settings/';
      $files = array_diff(scandir($path), array('.','..', '.htaccess'));
      $flag = false;
      foreach ($files as $file) {
        $data = file_get_contents($path . $file);
        $data = json_decode($data, TRUE);
        $tabs = array(&$data);
          if (array_key_exists('site', $data) )
            $tabs = &$data['site'];
          foreach ($tabs as &$tab) {
          foreach ($tab['settings'] as &$setting) {
            if (strpos($setting['type'], 'fancy-') > -1) {
              $setting['type'] = str_replace('fancy', 'icon', $setting['type']);
              $flag = true;
            }
            if (array_key_exists('values', $setting)) {
              $setting['i18n'] = $setting['values'];
              unset($setting['values']);
              $flag = true;
            }
            if (array_key_exists('langs', $setting)) {
              $setting['i18n'] = $setting['langs'];
              unset($setting['langs']);
              $flag = true;
            }
            if (array_key_exists('i18n', $setting) && array_key_exists('value', $setting) && $setting['value'] !== $setting['i18n'][0]) {
              $setting['value'] = $setting['i18n'][0];
              $flag = true;
            }
          }
          }
        if ($flag === true) 
          file_put_contents($path . $file, fileUtils::indentJSON($data));
      }
    }
  }
}
?>

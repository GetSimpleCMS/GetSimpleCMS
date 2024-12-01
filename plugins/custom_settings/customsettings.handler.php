<?php if (isset($_REQUEST) && isset($_REQUEST['id']) && isset($_REQUEST['requestToken']) && isset($_REQUEST['adminDir'])) {

    require_once('../../' . $_REQUEST['adminDir'] .'/inc/common.php');
    require_once('filehandler.class.php');
    require_once('customsettings.class.php');
    require_once('../../' . $_REQUEST['adminDir'] .'/inc/plugin_functions.php');
    
    global $USR, $i18n, $custom_settings, $custom_settings_dictionary;
    
    $token = $_REQUEST['requestToken'];
    $id = $_REQUEST['id'];
    $getToken = fileUtils::requestToken('kosstt');
    if ($token === $getToken) {
      if (isset($_REQUEST['action'])) {
        $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : NULL;
        switch($_REQUEST['action']) {
          case 'loadPluginInfo':
            echo customSettings::loadPluginInfo();
            break;
          case 'loadImageBrowser':
            echo json_encode(GSutils::getImageUploads(GSDATAUPLOADPATH));
            break;
          case 'getI18NFile':
            echo customSettings::i18nMerge();
            break;
          case 'getDataFile':
            echo json_encode($custom_settings);
            break;
          case 'saveData':
            $custom_settings = array('data' => json_decode($data, TRUE));
            $custom_settings_dictionary = customSettings::mapAllSettings();
            customSettings::saveAllSettings($custom_settings);
            break;
          default: 
            echo 'The data could not be loaded from the server';
        }
      }
    }
  } else 
    
    die(json_encode($_REQUEST));

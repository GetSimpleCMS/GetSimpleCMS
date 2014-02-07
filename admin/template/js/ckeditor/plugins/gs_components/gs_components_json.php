<?php

// TESTING DISABLED
header('Status: 404 Not Found');
die();

# Setup inclusions
$load['plugin'] = true;

include('../../../../../inc/common.php');
include('../../../../../inc/theme_functions.php');


// function get_components() {
    global $components;
    if (!$components) {
         if (file_exists(GSDATAOTHERPATH.'components.xml')) {
            $data = getXML(GSDATAOTHERPATH.'components.xml');
            $components = $data->item;
        } else {
            $components = array();
        }
    }
    if (count($components) > 0) {
        foreach ($components as $component) {
        	$components_data[] = array('(% '.(string)$component->slug.' %)',(string)$component->slug,(string)$component->slug);
        }
    }
// }

// convert to JSON and return 
$jsonData = str_replace('\\"',"", json_encode($components_data));
echo $jsonData;
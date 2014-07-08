<?php
/*
Plugin Name: GSJQueryMigrate
Description: implements jquery Migrate for backwards compatability of jquery code older than 1.9
Version: 1.0
Author: getSimpleCMS
Author URI: http://get-simple.info
*/

$thisfile_GSJQM = basename(__FILE__, ".php");

function jQuery_migrate_init(){
    GLOBAL $thisfile_GSJQM, $SITEURL;

    i18n_merge($thisfile_GSJQM ) || i18n_merge($thisfile_GSJQM , 'en_US');

    # register plugin
    register_plugin(
        $thisfile_GSJQM ,                              # ID of plugin, should be filename minus php
        i18n_r($thisfile_GSJQM .'/GSJQMigrate_TITLE'), # Title of plugin
        '1.0',                                         # Version of plugin
        'GetSimpleCMS',                                # Author of plugin
        'http://get-simple.info',                      # Author URL
        i18n_r($thisfile_GSJQM .'/GSJQMigrate_DESC'),  # Plugin Description
        '',                                            # Page type of plugin
        ''                                             # Function that displays content
    );

    $asset = isDebug() ? 'jquery-migrate-1.2.1.js' : 'jquery-migrate-1.2.1.min.js'; // when debug is on, migrate will output to console with deprecated notices.
    $url = $SITEURL.'plugins/'.$thisfile_GSJQM.'/assets/js/'.$asset;

    register_script('jquerymigrate', $url, '', FALSE);
    queue_script('jquerymigrate',GSBACK);

}

jQuery_migrate_init();
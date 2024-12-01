<?php
/* Backport of get_api_details from GS 3.1 */
if (GSVERSION < "3.1") {
    define('GSCACHEPATH', GSROOTPATH. 'data/cache/');
    updater_ensure_directory_exists(GSCACHEPATH);
    function get_api_details($type='core', $args=null) {
        include(GSADMININCPATH.'configuration.php');

        # core api details
        if ($type=='core') {
            $fetch_this_api = $api_url .'?v='.GSVERSION;
        }
        
        # plugin api details. requires a passed plugin id
        if ($type=='plugin' && $args) {
            $apiurl = 'http://get-simple.info/api/extend/?file=';
            $fetch_this_api = $apiurl.$args;
        }
        
        # custom api details. requires a passed url
        if ($type=='custom' && $args) {
            $fetch_this_api = $args;
        }
            
        # check to see if cache is available for this
        $cachefile = md5($fetch_this_api).'.txt';

        if (file_exists(GSCACHEPATH.$cachefile) && time() - 40000 < filemtime(GSCACHEPATH.$cachefile)) {
            # grab the api request from the cache
            
            $data = file_get_contents(GSCACHEPATH.$cachefile);
        } else {	
            # make the api call
            if (function_exists('curl_exec')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, $fetch_this_api);
                $data = curl_exec($ch);
                curl_close($ch);
            } else {
                $data = file_get_contents($fetch_this_api);
            }
            file_put_contents(GSCACHEPATH.$cachefile, $data);
        chmod(GSCACHEPATH.$cachefile, 0644);
        }
        return $data;
    }
}

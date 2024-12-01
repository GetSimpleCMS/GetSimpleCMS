<?php

// No direct access
defined('IN_GS') or die('Cannot load plugin directly.');

// TODO(03.07.2015) ~ Helge: Decouple code
// TODO(03.07.2015) ~ Helge: move "singular"-plugin related code into own class.

class PluginInstaller
{

    protected $plugins;
    protected $cache_path;


    public function __construct($cache_path)
    {
        $this->cache_path = $cache_path;
    }


    /**
     * NOTE: This function takes a little time to execute since there is no paging
     * support in the api I have to grab EVERYTHING for on every request :(
     * Goes out to the Extend API and fetches all currently available plugins
     * @return array list of plugin objects
     */
    public function getPlugins()
    {

        // Check if we have a cached version of the plugins json file
        if (file_exists($this->cache_path)) {

            // Get the last time that the cache was modified
            $cache_age = (time() - filemtime($this->cache_path));

            // If the cache is older than 24 hours, we fetch new data from the API
            if (($cache_age) > (24 * 3600)) {

                // Fetch the plugins from the api
                $this->plugins = $this->getPluginsFromApi();

                // Let's cache the plugin list, so we don't have to query the Extend API every time.
                $this->saveCache($this->plugins);

            } else {
                // If the cache is fresh enough, we just load the data from it instead.
                $cachedata = file_get_contents($this->cache_path);
                $this->plugins = json_decode($cachedata);
            }
        } else {
            // We have no cache file, fetch from API
            $this->plugins = $this->getPluginsFromApi();

            // Then let's save it to the cache
            $this->saveCache($this->plugins);
        }

        // Return all plugins
        return $this->plugins;
    }


    /**
     * Saves data to the cache file as JSON
     * @param mixed $data array to save as json
     * @param string $file the filepath of the cache file.
     */
    function saveCache($data, $file = false)
    {
        if (!$file) $file = $this->cache_path;

        $data = json_encode($data);

        $cache_directory = dirname($file);

        // If the folder does not exist, create it
        if (!file_exists($cache_directory)) {
            mkdir($cache_directory);
            chmod($cache_directory, 0755);
        }

        file_put_contents($file, $data);
    }


    /**
     * deletes a file
     * @param string $file pass it the cache file to delete
     */
    function deleteCache($file = false)
    {
        if (!$file) $file = $this->cache_path;
        if (file_exists($file))
            unlink($file);
    }


    /**
     * Fetches all plugins from the Extend API.
     * @return array array of plugins from the Extend API
     */
    function getPluginsFromApi()
    {

        // Fetch all items from the api
        $items = $this->queryApi("http://get-simple.info/api/extend/all.php");

        // Sort through all the items, we only want the Plugins, they have a category of "Plugin"
        foreach ($items as $item) {
            if (isset($item->category) && $item->category == "Plugin") {

                // If the plugin does not have a main file, it is not installable with this plugin, so ignore it.
                if ($item->filename_id !== "") {

                    // Put the plugin in the plugins array
                    $this->plugins[] = $item;
                }
            }
        }

        return $this->plugins;
    }


    /**
     * Queries the URL and returns the data as the appropriate type after being json decoded.
     * @param string $url the url to go and fetch json data from
     * @return mixed returns array or object depending on context
     */
    function queryApi($url)
    {

        // Check if we have access to curl
        if (function_exists("curl_version")) {

            // initialize curl
            $ch = curl_init();

            // Return the response, don't verify SSL, and pass the $url as the url.
            curl_setopt_array($ch, array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url
            ));

            // Send the request
            $json = curl_exec($ch);

            // Close connection
            curl_close($ch);

        } else {
            // Fallback to file_get_contents
            $json = file_get_contents($url);
        }

        $data = json_decode($json);

        return $data;
    }


    /**
     * Deletes a folder and everything inside of it by using recursion.
     * @param string $dir the folder to delete the contents of
     * @return bool true on success, false on failure
     */
    function deleteDirectory($dir)
    {
        // Exclude the current and parent folder, we don't want to delete those.
        $files = array_diff(scandir($dir), array('.', '..'));

        // foreach item in the folder
        foreach ($files as $file) {
            if ((is_dir($dir . '/' . $file))) {
                // If the item is a folder, we recurse this function
                $this->deleteDirectory($dir . '/' . $file);
            } else {
                // if the item is a file, delete it.
                unlink($dir . '/' . $file);
            }
        }

        // No more subfolders in $dir.
        return rmdir($dir);
    }


    /**
     * Checks if a plugin is installed by checking for the main plugin file.
     * @param object $plugin the plugin object (json_decoded object from the extend JSON API for a single plugin
     * @return bool true if it is installed, false if it is not
     */
    function isPluginInstalled($plugin)
    {
        $plugin_file = GSPLUGINPATH . "/" . $plugin->filename_id;

        // If the plugin file exists
        if (file_exists($plugin_file)) {
            return true;
        }

        // If the plugin folder exists
        if (file_exists(basename($plugin_file, ".php"))) {
            return true;
        }

        // Plugin is (most likely) not installed
        return false;
    }


    /**
     * Removes the files and folders associated with a plugin, it does this by querying
     * the Extend api and getting the main filename of the plugin and guessing the folder
     * for the plugin if it exists, NOTE that this function assumes the plugin developer
     * followed the naming standards of plugin folders (having the same name as the main plugin file
     * @param int $id the id of the plugin to uninstall
     * @return bool true on success, false on failure
     */
    function uninstallPlugin($id)
    {
        if (is_numeric($id)) {

            // We need to get the main plugin file name.
            $plugin = $this->queryApi("http://get-simple.info/api/extend/?id=" . $id);

            // This is assuming that the plugin keeps the GetSimple naming convention
            $plugin_folder = GSPLUGINPATH . "/" . trim($plugin->filename_id, ".php");
            $plugin_file = GSPLUGINPATH . "/" . $plugin->filename_id;


            // check if the plugin file exists
            if (file_exists($plugin_file)) {
                if (!unlink($plugin_file))
                    return false;
            }

            // check if the plugin folder exists, this might not always be the case.
            if (file_exists($plugin_folder)) {
                if (!$this->deleteDirectory($plugin_folder))
                    return false;
            }

            // We successfully uninstalled this plugin
            return true;
        }

        return false;
    }


    /**
     * Installs the plugin by downloading the zip archive with the plugin files to
     * the plugins/ folder giving it a unique randomized name, then unzipping it,
     * after it's unzipped it will remove the zip file.
     * @param int $id the id of the plugin
     * @return bool true on success, false on failure
     */
    function installPlugin($id)
    {
        if (is_numeric($id)) {

            $data = $this->queryApi("http://get-simple.info/api/extend/?id=" . $id);

            // Define the tmp filepath for the zip file
            $filepath = GSPLUGINPATH . "/" . uniqid() . ".zip";

            // Create a file stream to the plugin zip file on Extend
            $pluginFile = fopen($data->file, 'r');

            // Put the zip file in the filepath
            file_put_contents($filepath, $pluginFile);

            // If it exists
            if (file_exists($filepath)) {

                // Open the zip file object
                $zip = new ZipArchive;

                // If we can open the file
                if ($zip->open($filepath)) {

                    // extract/install the plugin into the GetSimple Plugin folder
                    $zip->extractTo(GSPLUGINPATH);

                    // Close the resource handle
                    $zip->close();

                    // Delete the temp file
                    unlink($filepath);

                    return true; // Installation successful
                } else {
                    return false; // Installation failed
                }
            }
        }

        // Invalid plugin id or couldn't save plugin to disk
        return false;
    }

    /**
     * @return mixed path to cache
     */
    public function getCachePath()
    {
        return $this->cache_path;
    }

    /**
     * Sets the path to the cache file
     * @param mixed $cache_path path to cache file (absolute path)
     */
    public function setCachePath($cache_path)
    {
        $this->cache_path = $cache_path;
    }

}
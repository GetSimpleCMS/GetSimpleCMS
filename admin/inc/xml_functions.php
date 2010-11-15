<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * XML Functions 
 *
 * These functions are used for GetSimple installations that use XML as it's storage engine
 *
 * @package GetSimple
 * @subpackage Storage
 */

/**
 * Save Page (XML)
 *
 * @since 3.0
 *
 * @param string $id ID of the page being saved
 * @param array $data Page data in multidimential array
 * @return bool
 */
function storage_save_page($id, $data) {

}

/**
 * Save User Data (XML)
 *
 * @since 3.0
 *
 * @param string $id ID of the user
 * @param array $data User data in multidimential array
 * @return bool
 */
function storage_save_userdata($id, $data) {

}

/**
 * Save Components (XML)
 *
 * @since 3.0
 *
 * @todo Not sure how to do this. All components at once like with XML, or one at a time in their own table
 */
function storage_save_components() {

}

/**
 * Save Website Settings (XML)
 *
 * @since 3.0
 *
 * @param array $data
 * @return bool
 */
function storage_save_settings($data) {
	
}

/**
 * Save General Option (XML)
 *
 * @since 3.0
 *
 * @param string $key
 * @param string $meta
 * @return bool
 */
function storage_save_option($key, $meta) {

}

/**
 * Get Page Data (XML)
 *
 * @since 3.0
 *
 * @param string $id
 * @return array
 */
function storage_get_page($id) {

}

/**
 * Get User Data (XML)
 *
 * @since 3.0
 *
 * @param string $id
 * @return array
 */
function storage_get_userdata($id) {
	
}

/**
 * Get Components (XML)
 *
 * @since 3.0
 *
 * @param string $id Optional
 * @return array
 */
function storage_get_components($id=null) {
	
}

/**
 * Get Settings (XML)
 *
 * @since 3.0
 *
 * @return array
 */
function storage_get_settings() {
	
}

/**
 * Get Option (XML)
 *
 * @since 3.0
 *
 * @param string $key
 * @return string
 */
function storage_get_option($key) {
	
}


?>
<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * MySQL Functions 
 *
 * These functions are used for GetSimple installations that use MySQL as it's storage engine
 *
 * @package GetSimple
 * @subpackage Storage
 */

/**
 * Database Connection (MySQL)
 *
 * @since 3.0
 * @uses DB_HOST
 * @uses DB_USER
 * @uses DB_PASS
 * @uses DB_DATABASE
 *
 * @return bool
 */
function storage_connect() {
	$status_connect = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
  $status_select = mysql_select_db(DB_DATABASE);
  if (mysql_error()) {
  	error_log(mysql_error());
  } 
  return $status_select && $status_connect;
}

/**
 * Escape Data (MySQL)
 *
 * @since 3.0
 * @uses DB_HOST
 * @uses DB_USER
 * @uses DB_PASS
 * @uses DB_DATABASE
 *
 * @param string $data
 * @param string $db Optional, default is TRUE. TRUE uses 'mysql_real_escape_string'
 * @param string $html Optional, default is FALSE. FALSE does not strip HTML from $data
 * @return string
 */
function storage_escape($data, $db=true, $html=false) {
	$data = trim($data);
  if (get_magic_quotes_gpc()) { $data = stripslashes($data); } // if get magic quotes is on, stripslashes
  if ($html) { $data = strip_tags($data); } // no html wanted
	
	if (!$db) { // not used in query (just email or display)
		return $data;
	} elseif ($db) { // used in mysql query
		if (is_numeric($data)) {
			return $data;
		} else {
			$data = mysql_real_escape_string($data);
			return $data;
		}
 }
}

/**
 * Save Page (MySQL)
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
 * Save User Data (MySQL)
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
 * Save Components (MySQL)
 *
 * @since 3.0
 *
 * @todo Not sure how to do this. All components at once like with XML, or one at a time in their own table
 */
function storage_save_components() {

}

/**
 * Save Website Settings (MySQL)
 *
 * @since 3.0
 *
 * @param array $data
 * @return bool
 */
function storage_save_settings($data) {
	
}

/**
 * Save General Option (MySQL)
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
 * Get Page Data (MySQL)
 *
 * @since 3.0
 *
 * @param string $id
 * @return array
 */
function storage_get_page($id) {

}

/**
 * Get User Data (MySQL)
 *
 * @since 3.0
 *
 * @param string $id
 * @return array
 */
function storage_get_userdata($id) {
	
}

/**
 * Get Components (MySQL)
 *
 * @since 3.0
 *
 * @param string $id Optional
 * @return array
 */
function storage_get_components($id=null) {
	
}

/**
 * Get Settings (MySQL)
 *
 * @since 3.0
 *
 * @return array
 */
function storage_get_settings() {
	
}

/**
 * Get Option (MySQL)
 *
 * @since 3.0
 *
 * @param string $key
 * @return string
 */
function storage_get_option($key) {
	
}


?>
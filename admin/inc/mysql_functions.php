<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * MySQL Functions 
 *
 * These functions are used for GetSimple installations that use MySQL
 *
 * @package GetSimple
 * @subpackage MySQL-Functions
 */

/**
 * Connect to MySQL Database
 *
 * @since 3.0
 * @uses DB_HOST
 * @uses DB_USER
 * @uses DB_PASS
 * @uses DB_DATABASE
 *
 * @return bool
 */
function connect_database() {
	$status_connect = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
  $status_select = mysql_select_db(DB_DATABASE);
  if (mysql_error()) {
  	error_log(mysql_error());
  } 
  return $status_select && $status_connect;
}

/**
 * Escape Data for Use in MySQL Queries
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
function escape_data($data, $db=true, $html=false) {
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





?>
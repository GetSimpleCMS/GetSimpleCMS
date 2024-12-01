<?php
namespace Imanager;

class Database
{
	public function genDatabase($path = '')
	{
		$path = empty($path) ? IM_DATABASE_DIR.IM_DATABASE : $path;
		$db = null;
		if(!file_exists(dirname($path))) {
			MsgReporter::setClause('database_file_not_found', array(), true);
			return false;
		}
		try {
			$db = new \PDO('sqlite:'.$path);
			// Set errormode to exceptions
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return $db;
		} catch(PDOException $e) {
			// Print PDOException message
			echo $e->getMessage();
			return false;
		}
		return false;
	}
}
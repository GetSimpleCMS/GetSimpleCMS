<?php
class Initializer
{
	protected static $db;

	public function __construct()
	{
		$this->setDatabase();
	}

	public function setDatabase($path = '')
	{
		$db = new Database();
		self::$db = $db->genDatabase($path);
	}
}


class Database
{
	protected $chmodFile = 0666;
	protected $chmodDir = 0755;

	public function genDatabase($path = '')
	{
		$path = empty($path) ? IM_CACHE_DIR.'/repo' : $path;
		$db = null;
		if(!file_exists(dirname($path)))
		{
			if(!is_dir($path))
			{
				if(!mkdir(dirname($path), $this->chmodDir, true)) echo 'Unable to create path: '.dirname($path);
				if(!$handle = fopen(dirname($path).'/.htaccess', 'w')) return false;
				fwrite($handle,
'# apache < 2.3
<IfModule !mod_authz_core.c>
	Deny from all
</IfModule>

# apache > 2.3 with mod_access_compat
<IfModule mod_access_compat.c>
	Deny from all
</IfModule>

# apache > 2.3 without mod_access_compat
<IfModule mod_authz_core.c>

	<IfModule !mod_access_compat.c>
		Require all denied
	</IfModule>

</IfModule>'	);
				fclose($handle);
			}
		}
		try {
			$db = new PDO('sqlite:'.$path);
			// Set errormode to exceptions
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $db;
		} catch(PDOException $e) {
			// Print PDOException message
			echo $e->getMessage();
			return false;
		}
		return false;
	}
}
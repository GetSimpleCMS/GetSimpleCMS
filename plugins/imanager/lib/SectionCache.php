<?php
/**
 * A simple way to cache segments of markup in your themes
 */
class SectionCache {

	/**
	 * Instance of CacheFile
	 *
	 */
	protected $cache = null;


	/**
	 * Boolean indicating whether we've already cleared the cache.
	 *
	 */
	protected $cleared = false;

	/**
	 * Path to cache files, as set by the init() method.
	 *
	 */
	protected $path = '';

	/**
	 * Non zero when caches shouldn't expire on item save
	 *
	 */
	protected $noExpire = 0;

	/**
	 * Initialize the module
	 *
	 */
	public function __construct($path='') {
		$this->path = empty($path) ? IM_SECTIONS_CACHE_DIR : $path;
	}

	/**
	 * Get cached data identified by 'uniqueName' or false if cache not available
	 *
	 * @param string $uniqueName A unique string or number to identify this cache, i.e. 'citiesList'
	 * @param int $seconds The number of seconds the cache should live.
	 * @return Returns the cache data, or FALSE if it has expired and needs to be re-created.
	 *
	 */
	public function get($uniqueName, $seconds = 3600) {
		$cache = new ImCacheFile($this->path, $uniqueName, $seconds);
		if(!$cache) echo ("Unable to create cache '{$this->path}/$uniqueName'");
		$this->cache = $cache;
		return $this->cache->get();
	}

	/**
	 * Save the data to the cache
	 *
	 * Must be preceded by a call to get() so that you have set the cache unique name
	 *
	 * @param string $data Data to cache
	 * @return int Number of bytes written to cache, or FALSE on failure.
	 *
	 */
	public function save($data)
	{
		if(!$this->cache) echo 'You must attempt to retrieve a cache first, before you can save it.';
		$result = $this->cache->save($data);
		$this->cache = null;
		return $result;
	}

	/**
	 * Expire the cache, can be automatically hooked to every $item->save() call
	 *
	 */
	public function expire() {
		/*
		 * If already cleared during this session
		 */
		if($this->cleared) return;

		if($this->cache) $cache = $this->cache;
		else $cache = new ImCacheFile($this->path, '', 0);
		$cache->expireAll();
		$this->cleared = true;
	}

	/**
	 * Clears all MarkupCache files
	 *
	 * @return number of files/dirs deleted
	 *
	 */
	protected static function _removeAll() {
		$path = self::path();
		try {
			$num = CacheFile::removeAll($path, true);
		} catch(Exception $e) {
			$num = 0;
		}
		return $num;
	}

	/**
	 * Non static implmeentation of removeAll() for convenience
	 *
	 */
	public function removeAll() {
		return self::_removeAll();
	}

	/**
	 * For ConfigurableModule interface, even though we aren't currently using it
	 *
	 */
	public function __set($key, $value) {
		if($key == 'noExpire') $this->noExpire = (int) $value;
	}
}

class ImCacheFile
{
	const cacheFileExtension = '.cache';
	const globalExpireFilename = 'last';

	/**
	 * Max cache files that will be allowed in a directory before it starts removing them.
	 *
	 */
	const maxCacheFiles = 999;

	protected $path;
	protected $primaryID = '';
	protected $secondaryID = '';
	protected $cacheTimeSeconds = 0;
	protected $globalExpireFile = '';
	protected $globalExpireTime = 0;
	protected $chmodFile = 0666;
	protected $chmodDir = 0755;

	public function __construct($path, $id, $cacheTimeSeconds)
	{
		$path = rtrim($path, '/') . '/';
		$this->globalExpireFile = $path . self::globalExpireFilename;
		$this->path = $path;

		if(!is_dir($this->path))
		{
			if(!mkdir($this->path, $this->chmodDir, true)) echo 'Unable to create path: '.$this->path;
		}

		if(is_file($this->globalExpireFile)) {
			$this->globalExpireTime = @filemtime($this->globalExpireFile);
		}

		$this->primaryID = $id;
		$this->cacheTimeSeconds = (int) $cacheTimeSeconds;
	}

	/**
	 * Build a filename for use by the cache
	 *
	 * Filename takes this form: /path/primaryID.cache
	 *
	 * @return string
	 *
	 */
	protected function buildFilename()
	{
		$filename = $this->path.$this->primaryID.self::cacheFileExtension;
		return $filename;
	}


	/**
	 * Is the given cache filename expired?
	 *
	 * @param string $filename
	 * @return bool
	 *
	 */
	protected function isCacheFileExpired($filename)
	{
		if(!$mtime = @filemtime($filename)) return false;
		if(($mtime + $this->cacheTimeSeconds < time()) || ($this->globalExpireTime && $mtime < $this->globalExpireTime))
		{
			return true;
		}
		return false;
	}

	/**
	 * Is the given filename a cache file?
	 *
	 * @param string $filename
	 * @return bool
	 *
	 */
	static protected function isCacheFile($filename)
	{
		$ext = self::cacheFileExtension;
		if(is_file($filename) && substr($filename, -1 * strlen($ext)) == $ext) return true;
		return false;
	}

	/**
	 * Removes just the given file, as opposed to remove() which removes the entire cache
	 *
	 */
	protected function removeFilename($filename){@unlink($filename);}


	/**
	 * Get the contents of the cache based on the primary ID
	 *
	 * @return string
	 *
	 */
	public function get()
	{
		$filename = $this->buildFilename();
		if(self::isCacheFile($filename) && $this->isCacheFileExpired($filename))
		{
			$this->removeFilename($filename);
			return false;
		}
		// note file_get_contents returns boolean false if file can't be opened (i.e. if it's locked from a write)
		return @file_get_contents($filename);
	}


	/**
	 * Saves $data to the cache
	 *
	 * @param string $data
	 * @return bool
	 *
	 */
	public function save($data)
	{
		$filename = $this->buildFilename();
		if(!is_file($filename)) {
			$dirname = dirname($filename);
			if(is_dir($dirname)) {
				$files = glob("$dirname/*.*");
				$numFiles = count($files);
				if($numFiles >= self::maxCacheFiles)
				{
					// if the cache file doesn't already exist, and there are too many files here
					// then abort the cache save for security (we don't want to fill up the disk)
					// also go through and remove any expired files while we are here, to avoid
					// this limit interrupting more cache saves.
					foreach($files as $file) {
						if(self::isCacheFile($file) && $this->isCacheFileExpired($file))
							$this->removeFilename($file);
					}
					return false;
				}
			} else {
				mkdir("$dirname/", $this->chmodDir, true);
			}
		}

		$result = file_put_contents($filename, $data);
		@chmod($filename, $this->chmodFile);
		return $result;
	}

	/**
	 * Causes all cache files in this type to be immediately expired
	 *
	 * Note it does not remove any files, only places a globalExpireFile with an mtime newer than the cache files
	 *
	 */
	public function expireAll() {
		$note = "The modification time of this file represents the time of the last usable cache file. " .
			"Cache files older than this file are considered expired. " . date('m.d.Y H:i:s');
		@file_put_contents($this->globalExpireFile, $note, LOCK_EX);
	}
}
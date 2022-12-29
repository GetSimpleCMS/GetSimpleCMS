<?php

class ItemManager extends Model
{
	/**
	 * Just for counting ItemManager instances
	 */
	public static $counter = 0;


	public function __construct()
	{
		spl_autoload_register(array($this, 'loader'));
		require_once(IM_SOURCE_DIR.'_Util.php');
		require_once(IM_SOURCE_DIR . 'processors/FieldInterface.php');
		require_once(IM_SOURCE_DIR . 'processors/InputInterface.php');
		self::$counter++;
		parent::__construct();
	}

	/**
	 * Autoload method
	 * @param $classPattern
	 */
	private function loader($classPattern)
	{
		$classPath = IM_SOURCE_DIR . $classPattern . '.php';
		$fieldsPath = IM_SOURCE_DIR . 'processors/fields/' . $classPattern. '.php';
		$inputsPath = IM_SOURCE_DIR . 'processors/inputs/' . $classPattern . '.php';
		if(file_exists($classPath)) include($classPath);
		elseif(file_exists($fieldsPath)) include($fieldsPath);
		elseif(file_exists($inputsPath)) include($inputsPath);
	}


	public function getTemplateEngine($path='')
	{
		if(self::$templateEngine === null)
			self::$templateEngine = new TemplateEngine($path);
		return self::$templateEngine;
	}


	public function getSectionCache($path='')
	{
		if(self::$sectionCache === null)
			self::$sectionCache = new SectionCache($path);
		return self::$sectionCache;
	}


	public function getCategoryMapper()
	{
		if(self::$categoryMapper === null)
			self::$categoryMapper = new CategoryMapper();
		return self::$categoryMapper;
	}


	public function getItemMapper()
	{
		if(self::$itemMapper === null)
			self::$itemMapper = new ItemMapper();
		return self::$itemMapper;
	}


	public function getCategory($stat, array $categories=array())
	{
		return $this->getCategoryMapper()->getCategory($stat, $categories);
	}


	public function getItem($cat, $stat, array $items=array())
	{
		self::$itemMapper = $this->getItemMapper();
		if(is_numeric($cat) && (is_numeric($stat) || preg_match('/^id=\d*$/', $stat, $tar)))
		{
			self::$itemMapper->limitedInit((int) $cat, !empty($tar[2]) ? (int) $tar[2] : (int) $stat);
			return self::$itemMapper->items[!empty($tar[2]) ? (int) $tar[2] : (int) $stat];
		} elseif(is_numeric($cat)) {
			self::$itemMapper->init((int) $cat);
			return self::$itemMapper->getItem($stat);
		} elseif(!is_numeric($cat)) {
			self::$categoryMapper = $this->getCategoryMapper();
			$tarCat = self::$categoryMapper->getCategory($cat);
			self::$itemMapper->init((int) $tarCat->id);
			return self::$itemMapper->getItem($stat);
		}
	}

	public function getItems($cat, $stat='', $offset=0, $length=0, array $items=array())
	{
		self::$itemMapper = $this->getItemMapper();
		if(is_numeric($cat)) {
			self::$itemMapper->init((int) $cat);
			if(empty($stat)) return self::$itemMapper->items;
			return self::$itemMapper->getItems($stat, $offset, $length, $items);
		} else {
			self::$categoryMapper = $this->getCategoryMapper();
			$tarCat = self::$categoryMapper->getCategory($cat);
			self::$itemMapper->init((int) $tarCat->id);
			//if(empty($stat)) return self::$itemMapper->items;
			return self::$itemMapper->getItems($stat, $offset, $length, $items);
		}
	}


	public function filter(array $itemArray, $filterby, $option = 'asc',  $offset=0, $length=0)
	{
		self::$itemMapper = $this->getItemMapper();
		return self::$itemMapper->filterItems($filterby, $option, $offset=0, $length=0, $itemArray);
	}

	/**
	 * Some deprecated accessor calls, just for backward compatibility reasons
	 */
	public function newTemplate($name=''){return new Template($name='');}
	public function getCategoryClass(){return $this->getCategoryMapper();}
	public function newCategory(){return new Category();}
	public function getFieldsClass(){return new FieldMapper();}
	public function newField($catid){return new Field($catid);}
	public function getItemClass(){return $this->getItemMapper();}
	public function newItem($catid){return new Item($catid);}
}
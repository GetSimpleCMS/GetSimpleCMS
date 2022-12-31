<?php
class ItemMapper extends Allocator
{
	/**
	 * @var array of the objects of type Item
	 */
	public $items;
	/**
	 * @var string filter by node
	 */
	protected $filterby;
	/**
	 * @var boolean indicates to searchig field values
	 */
	private $fieldflag = false;

	public $total = 0;


	public function __construct(){$this->items = array();}

	/**
	 * Just another forced init method
	 *
	 * @param $catid        - Category ID to be searched through
	 * @param array $fields - Define custom fields that item objects should load (default: all)
	 * @param int $start    - Define start index for the loop
	 * @param bool $bulk    - Define max number of items in selected array
	 * @param string $pat   - Define a pattern (optional)
	 */
	public function quickInit($catid, $fields=array(), $start=0, $bulk=false, $pat='*')
	{
		// nitialize the fields class
		$fc = new FieldMapper();
		$fc->init($catid);
		$this->items = array();
		$i=0;
		$fl = glob(IM_ITEM_DIR.$pat.'.'.$catid.IM_ITEM_FILE_SUFFIX, GLOB_NOSORT);
		$this->total = count($fl);
		foreach($fl as $file)
		{
			if($i < $start) {$i++; continue;}

			$base = basename($file, IM_ITEM_FILE_SUFFIX);
			$strp = strpos($base, '.');
			$id = substr($base, 0, $strp);
			$category = substr($base, $strp+1);

			$xml = getXML($file);

			$item = new Item($category);

			$item->categoryid = (int) $category;
			$item->id = (int) $id;
			$item->file = $file;
			$item->filename = $base.IM_ITEM_FILE_SUFFIX;

			$item->name = (string) $xml->name;
			$item->label = (string) $xml->label;
			$item->position = (int) $xml->position;
			$item->active = (int) $xml->active;

			$item->created = (int) $xml->created;
			$item->updated = (int) $xml->updated;

			$this->items[$item->id] = $item;

			foreach($fc->fields as $name => $obj)
			{
				if(in_array($name, $fields))
				{
					$new_field = new Field($category);
					// clone object otherwise we'll lose the value data
					$new_field = clone $obj;

					foreach($xml->field as $fieldkey => $field)
					{
						if($new_field->id == $field->id)
						{
							$inputClassName = 'Input'.ucfirst($new_field->type);
							$InputType = new $inputClassName($fc->fields[$name]);
							$output = $InputType->prepareOutput();

							foreach($output as $outputkey => $outputvalue)
							{
								if(is_array($outputvalue))
								{
									$new_field->$outputkey = array();
									$counter = 0;
									foreach($field->$outputkey as $arrkey => $arrval)
									{
										$url = (($outputkey == 'imageurl' || $outputkey == 'imagefullurl') ? IM_SITE_URL : '');
										$new_field->{$outputkey}[] = $url.(string)$field->{$outputkey}[$counter];
										$counter++;
									}
								} else
								{
									$new_field->$outputkey = '';
									$new_field->$outputkey = (string) $field->$outputkey;
								}
							}
							if(empty($new_field->value) && !empty($new_field->default))
							{
								$new_field->value = (string)$new_field->default;
							}
						}
					}

					$item->fields->$name = $new_field;
				}
			}
			$this->items[$item->id] = $item;
			if($bulk && (++$i) == $bulk) return;
		}
	}

	/**
	 * A limited init method, very useful when you wish to select only one or a few items
	 *
	 * @param integer $catid  - Category ID to be searched through
	 * @param integer $from   - Define start id index for the loop
	 * @param integer $limit    - Define max id of items in selected array
	 */
	public function limitedInit($catid, $index, $limit=0)
	{
		// nitialize the fields class
		$fc = new FieldMapper();
		$fc->init($catid);
		$this->items = array();

		if($limit == 0) $limit = ($index+1);
		else $limit++;
		for($i = $index; $i < $limit; $i++)
		{
			$res = glob(IM_ITEM_DIR.$i.'.'.$catid.IM_ITEM_FILE_SUFFIX, GLOB_NOSORT);
			if(empty($res)) continue;
			$file = $res[0];

			$base = basename($file, IM_ITEM_FILE_SUFFIX);
			$strp = strpos($base, '.');
			$id = substr($base, 0, $strp);
			$category = substr($base, $strp+1);

			$xml = getXML($file);

			$item = new Item($category);

			$item->categoryid = (int)$category;
			$item->id = (int) $id;
			$item->file =  $file;
			$item->filename = $base.IM_ITEM_FILE_SUFFIX;

			$item->name = (string) $xml->name;
			$item->label = (string) $xml->label;
			$item->position = (int) $xml->position;
			$item->active = (int) $xml->active;

			$item->created = (int) $xml->created;
			$item->updated = (int) $xml->updated;

			$this->items[$item->id] = $item;

			foreach($fc->fields as $name => $obj)
			{
				$new_field = new Field($category);
				// clone object because otherwise we'll lose the value data
				$new_field = clone $obj;

				foreach($xml->field as $fieldkey => $field)
				{
					if( $new_field->id == $field->id)
					{
						$inputClassName = 'Input'.ucfirst($new_field->type);
						$InputType = new $inputClassName($fc->fields[$name]);
						$output = $InputType->prepareOutput();

						foreach($output as $outputkey => $outputvalue)
						{
							if(is_array($outputvalue))
							{
								$new_field->$outputkey = array();
								$counter = 0;
								foreach($field->$outputkey as $arrkey => $arrval)
								{
									$url = (($outputkey == 'imageurl' || $outputkey == 'imagefullurl') ? IM_SITE_URL : '');
									$new_field->{$outputkey}[] = $url.(string)$field->{$outputkey}[$counter];
									$counter++;
								}
							} else
							{
								$new_field->$outputkey = '';
								$new_field->$outputkey = (string) $field->$outputkey;
							}
						}
						if(empty($new_field->value) && !empty($new_field->default))
						{
							$new_field->value = (string) $new_field->default;
						}
					}
				}
				$item->fields->$name = $new_field;
			}
			$this->items[$item->id] = $item;
		}

		$this->total = count($this->items);
	}

	/**
	 * Initializes all the items of a category and made them available in ImItem::$items
	 */
	public function init($catid)
	{
		// nitialize the fields class
		$fc = new FieldMapper();
		$fc->init($catid);
		$this->items = array();
		foreach(glob(IM_ITEM_DIR.'*.'.$catid.IM_ITEM_FILE_SUFFIX) as $file)
		{

			$base = basename($file, IM_ITEM_FILE_SUFFIX);
			$strp = strpos($base, '.');
			$id = substr($base, 0, $strp);
			$category = substr($base, $strp+1);

			$xml = getXML($file);

			$item = new Item($category);

			$item->categoryid = (int) $category;
			$item->id = (int) $id;
			$item->file =  $file;
			$item->filename = $base.IM_ITEM_FILE_SUFFIX;

			$item->name = (string) $xml->name;
			$item->label = (string) $xml->label;
			$item->position = (int) $xml->position;
			$item->active = (int) $xml->active;

			$item->created = (int) $xml->created;
			$item->updated = (int) $xml->updated;

			foreach($fc->fields as $name => $obj)
			{
				$new_field = new Field($category);
				// clone object because otherwise we'll lose the value data
				$new_field = clone $obj;

				foreach($xml->field as $fieldkey => $field)
				{
					if( $new_field->id == $field->id)
					{
						$inputClassName = 'Input'.ucfirst($new_field->type);
						$InputType = new $inputClassName($fc->fields[$name]);
						$output = $InputType->prepareOutput();

						foreach($output as $outputkey => $outputvalue)
						{
							if(is_array($outputvalue))
							{
								$new_field->$outputkey = array();
								$counter = 0;
								foreach($field->$outputkey as $arrkey => $arrval)
								{
									$url = (($outputkey == 'imageurl' || $outputkey == 'imagefullurl') ? IM_SITE_URL : '');
									$new_field->{$outputkey}[] = $url.(string)$field->{$outputkey}[$counter];
									$counter++;
								}
							} else
							{
								$new_field->$outputkey = '';
								$new_field->$outputkey = (string) $field->$outputkey;
							}
						}
						if(empty($new_field->value) && !empty($new_field->default))
						{
							$new_field->value = (string) $new_field->default;
						}
					}
				}

				$item->fields->$name = $new_field;
			}

			$this->items[$item->id] = $item;
		}
		$this->total = count($this->items);
	}


	/**
	 * Initializes all items and made them available in ImItem::$items array
	 * NOTE: Could be extrem slow and memory intensive with high data volumes
	 *
	 * @return bool|mixed
	 */
	public function initAll()
	{
		// initialize categories
		$c = new ImCategory();
		$c->init();
		$this->items = array();
		foreach($c->categories as $catid => $catvalue)
		{
			// nitialize the fields class
			$fc = new FieldMapper();
			$fc->init($catid);
			foreach(glob(IM_ITEM_DIR.'*.'.$catid.IM_ITEM_FILE_SUFFIX) as $file)
			{
				$base = basename($file, IM_ITEM_FILE_SUFFIX);
				$strp = strpos($base, '.');
				$id = substr($base, 0, $strp);
				$category = substr($base, $strp+1);

				$xml = getXML($file);

				$item = new Item($category);

				$item->set('categoryid', $category);
				$item->set('id', $id);
				$item->set('file', $file);
				$item->set('filename',$base.IM_ITEM_FILE_SUFFIX);

				$item->name = (string) $xml->name;
				$item->label = (string) $xml->label;
				$item->position = (int) $xml->position;
				$item->active = (int) $xml->active;

				$item->created = (int) $xml->created;
				$item->updated = (int) $xml->updated;

				foreach($fc->fields as $name => $obj)
				{
					$new_field = new Field($category);
					// clone object because otherwise we'll lose the value data
					$new_field = clone $obj;

					foreach($xml->field as $fieldkey => $field)
					{
						if( $new_field->id == $field->id)
						{
							$inputClassName = 'Input'.ucfirst($new_field->type);
							$InputType = new $inputClassName($fc->fields[$name]);
							$output = $InputType->prepareOutput();

							foreach($output as $outputkey => $outputvalue)
							{
								if(is_array($outputvalue))
								{
									$new_field->$outputkey = array();
									$counter = 0;
									foreach($field->$outputkey as $arrkey => $arrval)
									{
										$new_field->{$outputkey}[] = (string) $field->{$outputkey}[$counter];
										$counter++;
									}
								} else
								{
									$new_field->$outputkey = '';
									$new_field->$outputkey = (string) $field->$outputkey;
								}
							}

							if(empty($new_field->value) && !empty($new_field->default))
							{
								$new_field->value = (string) $new_field->default;
							}
						}
					}

					$item->fields->$name = $new_field;
				}

				$this->items[$catid][$item->id] = $item;
			}
		}
		$this->total = count($this->items);
	}


	/**
	 * Returns a total number of given items
	 *
	 * @param array $items
	 *
	 * @return int
	 */
	public function countItems(array $items=array())
	{return !empty($items) ? count($items) : count($this->items);}


	/**
	 * Count all items in a category, it is best to use this method, not init() then countItems()
	 */
	public function quickCount($catid)
	{
		return count(glob(IM_ITEM_DIR.'*.'.$catid.IM_ITEM_FILE_SUFFIX, GLOB_NOSORT));
	}


	/**
	 * Get single item
	 *
	 * @param $stat - Selector
	 * @param array $items
	 *
	 * @return bool|mixed
	 */
	public function getItem($stat, array $items=array())
	{
		$locitems = !empty($items) ? $items : $this->items;

		// nothing to select
		if(empty($items)) { if(!$this->countItems() || $this->countItems() <= 0) return false;}

		// just id was entered
		if(is_numeric($stat)) return !empty($locitems[$stat]) ? $locitems[$stat] : false;

		// all parameter have to match the data
		$treads = array();
		if(false !== strpos($stat, '&&'))
		{
			$treads = explode('&&', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part)
			{
				$sepitems[] = $this->separateItems($locitems, $part);
			}

			if(!empty($sepitems[0]) && !empty($sepitems[1]))
			{
				$arr = array_map('unserialize', array_intersect(array_map('serialize', $sepitems[0]), array_map('serialize', $sepitems[1])));

				return !empty($arr) ? reset($arr) : false;
			}
			// only one parameter have to match the data
		} elseif(false !== strpos($stat, '||'))
		{
			$treads = explode('||', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part)
			{
				if($res = $this->separateItem($locitems, $part))
					return $res;
			}
			// $stat contains just one command
		} else
		{
			return $this->separateItem($locitems, $stat);
		}
		return false;
	}


	/**
	 * Find matching item - Finds an item belonging to one category (returns exactly one result)
	 *
	 * @param $stat – A search selector: (name=Item Name) for example
	 * @param array $limit_ids – An optional parameter array, with category id's, to restrict the search process
	 *                           to specific categories (NOTE: The specifying category id's could speed up the
	 *                           searsh process!)
	 *
	 * @param array $limit_ids
	 *
	 * @return bool|mixed
	 */
	public function findItem($stat, array $limit_ids = array())
	{
		$mapper = imanager()->getCategoryMapper();
		if(!empty($limit_ids))
		{
			foreach($limit_ids as $catid) {
				$this->init($mapper->categories[(int)$catid]->id);
				$item = $this->getItem($stat);
				if(!empty($item)) return $item;
			}
			return false;
		}
		foreach($mapper->categories as $category)
		{
			$this->init($category->id);
			$item = $this->getItem($stat);
			if(!empty($item)) return $item;
		}
		return false;
	}


	/**
	 * Find matching items - Finds all items belonging to one category (returns matching items of a category)
	 *
	 * @param $stat – A search selector: (name=Item Name) for example
	 * @param array $limit_ids – An optional parameter array, with category id's, to restrict the search process
	 *                           to specific categories (NOTE: The specifying category id's could speed up the
	 *                           searsh process!)
	 *
	 * @return array|bool
	 */
	public function findItems($stat, array $limit_ids = array())
	{
		$mapper = imanager()->getCategoryMapper();
		if(!empty($limit_ids))
		{
			foreach($limit_ids as $catid) {
				$this->init($mapper->categories[(int)$catid]->id);
				$items = $this->getItems($stat);
				if(!empty($items)) return $items;
			}
			return false;
		}
		foreach($mapper->categories as $category)
		{
			$this->init($category->id);
			$items = $this->getItems($stat);
			if(!empty($items)) return $items;
		}
		return false;
	}


	/**
	 * Find all matching items - Finds all items of all categories (returns matching items of all categories)
	 *
	 * @param $stat – A search selector: (name=Item Name) for example
	 * @param array $limit_ids – An optional parameter array, with category id's, to restrict the search process
	 *                           to specific categories (NOTE: The specifying category id's could speed up the
	 *                           searsh process!)
	 *
	 * @return array|bool
	 */
	public function findAll($stat, array $limit_ids = array())
	{
		$allItems = array();
		$count = 0;
		$mapper = imanager()->getCategoryMapper();
		if(!empty($limit_ids))
		{
			foreach($limit_ids as $catid) {
				$this->init($mapper->categories[(int)$catid]->id);
				$items = $this->getItems($stat);
				$count += $this->total;
				if(!empty($items)) $allItems[] = $items;
			}
			$this->total = $count;
			return (!empty($allItems) ? $allItems : false);
		}
		foreach($mapper->categories as $category)
		{
			$this->init($category->id);
			$items = $this->getItems($stat);
			$count += $this->total;
			if(!empty($items)) $allItems[] = $items;
		}
		$this->total = $count;
		return (!empty($allItems) ? $allItems : false);
	}



	public function getItems($stat, $offset=0, $length=0, array $items=array())
	{
		// reset offset
		$offset = ($offset > 0) ? $offset-1 : $offset;

		if($offset > 0 && $length > 0 && $offset >= $length)
			return false;

		$locitems = !empty($items) ? $items : $this->items;

		// nothing to select
		if(empty($items)) if(!$this->countItems() || $this->countItems() <= 0) return false;

		// just id was entered
		if(is_numeric($stat)) return !empty($locitems[(int) $stat]) ? $locitems[(int) $stat] : false;


		// all parameter have to match the data
		$treads = array();


		// ***** HIER FÄNGT DER TESTBEREICH AN *****

//		if(false !== strpos($stat, '||') || false !== strpos($stat, '&&'))
//		{
//			$parts = preg_split('/\s*(&&|\|\|)\s*/', $stat);
//			$strop = preg_replace('/[^\|\|&&]/', '', $stat);
//			$strops = str_split($strop, 2);
//
//		} else { $parts[] = $stat; }
//
//		$i = 0;
//		//$arr = array();
//		$sepitems = array();
//		foreach($parts as $part) {
//			$buff = $this->separateItems($locitems, $part);
//			if(!empty($buff)) {
//				//array_map('serialize', $buff);
//				$sepitems[] = array_map('serialize', $buff);
//			}
//		}
//		call_user_func_array('array_intersect', $sepitems);
//		$ret = array_map('unserialize', end($sepitems));
//		Util::preformat($ret);

		// ***** HIER ENDET DER TESTBEREICH *****


		if(false !== strpos($stat, '&&'))
		{
			$treads = explode('&&', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part)
			{
				$sepitems[] = $this->separateItems($locitems, $part);
			}
			if(!empty($sepitems[0]) && !empty($sepitems[1]))
			{
				$arr = array_map('unserialize', array_intersect(array_map('serialize', $sepitems[0]), array_map('serialize', $sepitems[1])));

				// limited output
				if(!empty($arr) && ((int) $offset > 0 || (int) $length > 0))
				{
					if((int) $length == 0) $len = null;
					$arr = array_slice($arr, (int) $offset, (int) $length, true);
				}
				return $arr;
			}
			// only one parameter have to match the data
		} elseif(false !== strpos($stat, '||'))
		{
			$treads = explode('||', $stat, 2);
			$parts[] = trim($treads[0]);
			$parts[] = trim($treads[1]);

			$sepitems = array();
			foreach($parts as $part)
			{
				$sepitems[] = $this->separateItems($locitems, $part);
			}
			if(!empty($sepitems[0]) || !empty($sepitems[1]))
			{
				if(is_array($sepitems[0]) && is_array($sepitems[1]))
				{
					// limited output
					if(!empty($sepitems[0]) && ((int) $offset > 0 || (int) $length > 0))
					{
						if((int) $length == 0) $len = null;
						$sepitems[0] = array_slice($sepitems[0], (int) $offset, (int) $length, true);
						$sepitems[1] = array_slice($sepitems[1], (int) $offset, (int) $length, true);
						$return = array_merge($sepitems[0], $sepitems[1]);
						return array_slice($return, (int) $offset, (int) $length, true);
					}
					return array_merge($sepitems[0], $sepitems[1]);

				} elseif(is_array($sepitems[0]) && !is_array($sepitems[1]))
				{
					// limited output
					if(!empty($sepitems[0]) && ((int) $offset > 0 || (int) $length > 0))
					{
						if((int) $length == 0) $len = null;
						$sepitems[0] = array_slice($sepitems[0], (int) $offset, (int) $length, true);
					}
					return $sepitems[0];
				} else
				{
					// limited output
					if(!empty($sepitems[1]) && ((int) $offset > 0 || (int) $length > 0))
					{
						if((int) $length == 0) $len = null;
						$sepitems[1] = array_slice($sepitems[1], (int) $offset, (int) $length, true);
					}
					return $sepitems[1];
				}
			}

		// run this if $stat contains just one command
		} else
		{
			$arr = $this->separateItems($locitems, $stat);

			// limited output
			if(!empty($arr) && ((int) $offset > 0 || (int) $length > 0))
			{
				if((int) $length == 0) $len = null;
				$arr = array_slice($arr, (int) $offset, (int) $length, true);
			}

			return $arr;
		}
		return false;
	}


	/**
	 * Returns the array of objects of the type Item, sorted by any node your choice
	 * NOTE: If no $items argument is passed to the function, the fields
	 * must already be in the buffer: ImItem::$items. Call the ImItem::init($category_id)
	 * method before to assign the fields to the buffer.
	 *
	 * You can sort items by using any node
	 * Sample sortng by "position":
	 * ImItem::filterItems('position', 'DESC', $your_items_array)
	 *
	 * @param string $filterby
	 * @param string $key
	 * @param array $items
	 * @return boolean|array of objects of type Item
	 */
	public function filterItems($filterby='position', $option='asc',  $offset=0, $length=0, array $items=array())
	{
		// reset offset
		$offset = ($offset > 0) ? $offset-1 : $offset;

		$locitems = !empty($items) ? $items : $this->items;
		if(empty($locitems))
		{
			if(!$this->countItems() || $this->countItems() <= 0)
				return false;
		}

		$itemcontainer = array();

		if($filterby == 'id' || $filterby == 'position' || $filterby == 'name' || $filterby == 'label' || $filterby == 'active'
			|| $filterby == 'created' || $filterby == 'updated')
		{
			if(empty($locitems)) return false;

			foreach($locitems as $item_id => $i)
			{
				if(!isset($i->$filterby)) continue;
				$itemcontainer[$item_id] = $locitems[$item_id];
			}
		} else
		{
			// filtering for complex value types
			foreach($locitems as $itemkey => $item)
			{
				foreach($item->fields as $fieldkey => $fieldval)
				{
					if($fieldkey != $filterby) continue;
					$itemcontainer[$itemkey] = $locitems[$itemkey];
					$this->fieldflag = true;
					break;
				}
			}
		}

		if(!empty($itemcontainer))
		{
			$this->filterby = $filterby;
			usort($itemcontainer, array($this, 'sortObjects'));
			// sort DESCENDING
			if(strtolower($option) != 'asc') $itemcontainer = $this->reverseItems($itemcontainer);
			$itemcontainer = $this->reviseItemIds($itemcontainer);

			// limited output
			if(!empty($itemcontainer) && ((int) $offset > 0 || (int) $length > 0))
			{
				if((int) $length == 0) $len = null;
				$itemcontainer = array_slice($itemcontainer, (int) $offset, (int) $length, true);
			}

			if(!empty($items))
				return $itemcontainer;
			$this->items = $itemcontainer;
			return $this->items;
		}

		return false;
	}



	/**
	 * Deletes an item
	 *
	 * @param Item $item
	 * @param reinitialize flag $re
	 * @return bool
	 */
	public function destroyItem(Item $item, $re = false)
	{
		if(file_exists(IM_ITEM_DIR.$item->id.'.'.$item->categoryid.IM_ITEM_FILE_SUFFIX))
		{
			unlink(IM_ITEM_DIR.$item->id.'.'.$item->categoryid.IM_ITEM_FILE_SUFFIX);
			// reinitialize items
			if($re) $this->init($item->categoryid);
			return true;
		}
		return false;
	}



	protected function separateItem(array $items, $stat)
	{
		if (false !== strpos($stat, '='))
		{
			$data = explode('=', $stat, 2);
			$key = strtolower(trim($data[0]));
			$val = trim($data[1]);

			$num = substr_count($val, '%');

			$pat = false;
			if($num == 1) {
				$pos = strpos($val, '%');
				if($pos == 0) {
					$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'$/';
				} elseif($pos == strlen($val)) {
					$pat = '/^'.strtolower(trim(str_replace('%', '', $val))).'/';
				}
			} elseif($num == 2) {
				$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'/';
			}

			if(false !== strpos($key, ' ')) return false;

			// Searching for the name and other simple attributs
			if($key == 'id' || $key == 'name' || $key == 'label' || $key == 'position' || $key == 'active'
				|| $key == 'created' || $key == 'updated')
			{
				foreach($items as $itemkey => $item)
				{
					if(!$pat && strtolower($item->{$key}) == strtolower($val)) return $item;
					elseif($pat && preg_match($pat, strtolower($item->{$key}))) return $item;
				}
				return false;
			}
			// searching for field in complex value types
			foreach($items as $itemkey => $item)
			{
				foreach($item->fields as $fieldkey => $fieldval)
				{
					if(!empty($fieldval->value) && $fieldkey == $key && $fieldval->value == $val) return $item;
					elseif(!empty($fieldval->value) && $pat && preg_match($pat, strtolower($fieldval->value))) return $item;
				}
			}
		}
		return false;
	}


	protected function separateItems(array $items, $stat)
	{
		$res = array();
		$pattern = array(0 => '>=', 1 => '<=', 2 => '!=', 3 => '>', 4 => '<', 5 => '=');

		foreach($pattern as $pkey => $pval)
		{
			if(false !== strpos($stat, $pval))
			{
				$data = explode($pval, $stat, 2);
				$key = strtolower(trim($data[0]));
				$val = trim($data[1]);
				if(false !== strpos($key, ' '))
					return false;

				$num = substr_count($val, '%');
				$pat = false;
				if($num == 1) {
					$pos = strpos($val, '%');
					if($pos == 0) {
						$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'$/';
					} elseif($pos == (strlen($val)-1)) {
						$pat = '/^'.strtolower(trim(str_replace('%', '', $val))).'/';
					}
				} elseif($num == 2) {
					$pat = '/'.strtolower(trim(str_replace('%', '', $val))).'/';

				}

				// Searching for value in item attributes
				if($key == 'name' || $key == 'label' || $key == 'position' || $key == 'active'
					|| $key == 'created' || $key == 'updated')
				{
					foreach($items as $itemkey => $item)
					{
						if(!isset($item->{$key})) continue;

						if($pkey == 0)
						{
							if($item->{$key} < $val) continue;
						} elseif($pkey == 1)
						{
							if($item->{$key} > $val) continue;
						} elseif($pkey == 2)
						{
							if($item->{$key} == $val) continue;
						} elseif($pkey == 3)
						{
							if($item->{$key} <= $val) continue;
						} elseif($pkey == 4)
						{
							if($item->{$key} >= $val) continue;
						} elseif($pkey == 5)
						{
							if($item->{$key} != $val && !$pat) {

								continue;
							}
							elseif($pat && !preg_match($pat, strtolower($item->{$key}))){
								continue;
							}
						}

						$res[$item->id] = $item;
					}

				// Searching for the value in complex field types
				} else
				{
					foreach($items as $itemkey => $item)
					{
						foreach($item->fields as $fieldkey => $fieldval)
						{
							if(!isset($item->fields->{$key}->value)) continue;

							if($pkey == 0)
							{
								if($item->fields->{$key}->value < $val) continue;
							} elseif($pkey == 1)
							{
								if($item->fields->{$key}->value > $val) continue;
							} elseif($pkey == 2)
							{
								if($item->fields->{$key}->value == $val) continue;
							} elseif($pkey == 3)
							{
								if($item->fields->{$key}->value <= $val) continue;
							} elseif($pkey == 4)
							{
								if($item->fields->{$key}->value >= $val) continue;
							}elseif($pkey == 5)
							{
								if(!$pat && $item->fields->{$key}->value != $val) continue;
								elseif($pat && !preg_match($pat, strtolower($item->fields->{$key}->value))) continue;
							}

							$res[$item->id] = $item;

						}
					}
				}
				if(!empty($res)) return $res;

				return false;
			}
		}

		return false;
	}


	/**
	 * Sorts the objects
	 *
	 * @param $a $b objects to be sorted
	 * @return boolean
	 */
	protected function sortObjects($a, $b)
	{
		if(!$this->fieldflag)
		{
			$a = $a->{$this->filterby};
			$b = $b->{$this->filterby};
			if(is_numeric($a))
			{
				if($a == $b) {return 0;}
				else
				{
					if($b > $a) {return -1;}
					else {return 1;}
				}
			} else {return strcasecmp($a, $b);}

		} else
		{
			$a = $a->fields->{$this->filterby}->value;
			$b = $b->fields->{$this->filterby}->value;
			if(is_numeric($a))
			{
				if($a == $b) {return 0;}
				else
				{
					if($b > $a) {return -1;}
					else {return 1;}
				}
			} else {return strcasecmp($a, $b);}
		}
	}


	/**
	 * Reverse the array of items
	 *
	 * @param array $itemcontainer An array of objects
	 * @return boolean|array
	 */
	public function reverseItems($itemcontainer)
	{
		if(!is_array($itemcontainer)) return false;
		return array_reverse($itemcontainer);
	}


	/**
	 * Revise keys of the array of items and changes these into real item id's
	 *
	 * @param array $itemcontainer An array of objects
	 * @return boolean|array
	 */
	public function reviseItemIds($itemcontainer)
	{
		if(!is_array($itemcontainer)) return false;
		$result = array();
		foreach($itemcontainer as $val)
			$result[$val->id] = $val;
		return $result;
	}

	/**
	 * Used to check if max number of item files for a category is exceeded
	 * We don't want to fill up the disk
	 */
	public function maxItemsExceeded($catid, $max_files = 800)
	{
		return ((count(glob(IM_ITEM_DIR.'*.'.$catid.IM_ITEM_FILE_SUFFIX, GLOB_NOSORT))) > $max_files ? false : true);
	}

	public function pagination(array $params = array(), $argtpls = array())
	{

		$tpl = imanager()->getTemplateEngine();
		if(is_null($tpl->templates)) $tpl->init();
		$config = imanager('config');
		$pagination = $tpl->getTemplates('pagination');
		$tpls['wrapper'] = !empty($argtpls['wrapper']) ? $argtpls['wrapper'] : $tpl->getTemplate('wrapper', $pagination);
		$tpls['prev'] = !empty($argtpls['prev']) ? $argtpls['prev'] : $tpl->getTemplate('prev', $pagination);
		$tpls['prev_inactive'] = !empty($argtpls['prev_inactive']) ? $argtpls['prev_inactive'] : $tpl->getTemplate('prev_inactive', $pagination);
		$tpls['central'] = !empty($argtpls['central']) ? $argtpls['central'] : $tpl->getTemplate('central', $pagination);
		$tpls['central_inactive'] = !empty($argtpls['central_inactive']) ? $argtpls['central_inactive'] : $tpl->getTemplate('central_inactive', $pagination);
		$tpls['next'] = !empty($argtpls['next']) ? $argtpls['next'] : $tpl->getTemplate('next', $pagination);
		$tpls['next_inactive'] = !empty($argtpls['next_inactive']) ? $argtpls['next_inactive'] : $tpl->getTemplate('next_inactive', $pagination);
		$tpls['ellipsis'] = !empty($argtpls['ellipsis']) ? $argtpls['ellipsis'] : $tpl->getTemplate('ellipsis', $pagination);
		$tpls['secondlast'] = !empty($argtpls['secondlast']) ? $argtpls['secondlast'] : $tpl->getTemplate('secondlast', $pagination);
		$tpls['second'] = !empty($argtpls['second']) ? $argtpls['second'] : $tpl->getTemplate('second', $pagination);
		$tpls['last'] = !empty($argtpls['last']) ? $argtpls['last'] : $tpl->getTemplate('last', $pagination);
		$tpls['first'] = !empty($argtpls['first']) ? $argtpls['first'] : $tpl->getTemplate('first', $pagination);

		$page = (!empty($params['page']) ? $params['page'] : (isset($_GET['page']) ? (int) $_GET['page'] : 1));
		$params['items'] = !empty($params['count']) ? $params['count'] : $this->total;

		$pageurl = !empty($params['pageurl']) ? $params['pageurl'] : '?page=';
		$start = !empty($params['start']) ? $params['start'] : 1; // todo: remove it

		$maxitemperpage = ((int) $config->backend->maxitemperpage > 0) ? $config->backend->maxitemperpage : 20;
		$limit = !empty($params['limit']) ? $params['limit'] : $config->backend->maxitemperpage;
		$adjacents = !empty($params['adjacents']) ? $params['adjacents'] : 3;
		$lastpage = !empty($params['lastpage']) ? $params['lastpage'] : ceil($params['items'] / $limit);

		$next = ($page+1);
		$prev = ($page-1);

		//$tpl->init();
		// only one page to show
		if($lastpage <= 1)
			return $tpl->render($tpls['wrapper'], array('value' => ''), true);

		$output = '';
		// $pageurl . '1'
		if($page > 1)
			$output .= $tpl->render($tpls['prev'], array('href' => $pageurl . $prev), true);
		else
			$output .= $tpl->render($tpls['prev_inactive'], array(), true);

		// not enough pages to bother breaking it up
		if($lastpage < 7 + ($adjacents * 2))
		{
			for($counter = 1; $counter <= $lastpage; $counter++)
			{
				if($counter == $page)
				{
					$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
				} else
				{
					// $pageurl . '1'
					$output .= $tpl->render($tpls['central'], array(
							'href' => ($counter > 1) ? $pageurl . $counter : $pageurl . '1', 'counter' => $counter), true
					);
				}
			}
		// enough pages to hide some
		} elseif($lastpage > 5 + ($adjacents * 2))
		{
			// vclose to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if($counter == $page)
					{
						$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
					} else
					{
						$output .= $tpl->render($tpls['central'], array('href' => $pageurl . $counter,
							'counter' => $counter), true);
					}
				}
				// ...
				$output .= $tpl->render($tpls['ellipsis']);
				// sec last
				$output .= $tpl->render($tpls['secondlast'], array('href' => $pageurl . ($lastpage - 1),
					'counter' => ($lastpage - 1)), true);
				// last
				$output .= $tpl->render($tpls['last'], array('href' => $pageurl . $lastpage,
					'counter' => $lastpage), true);
			}
			// middle pos; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				// first
				$output .= $tpl->render($tpls['first'], array('href' => $pageurl . '1'), true);
				// second
				$output .= $tpl->render($tpls['second'], array('href' => $pageurl . '2', 'counter' => '2'), true);
				// ...
				$output .= $tpl->render($tpls['ellipsis']);

				for($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if($counter == $page)
					{
						$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
					} else
					{
						$output .= $tpl->render($tpls['central'], array('href' => $pageurl . $counter,
							'counter' => $counter), true);
					}
				}
				// ...
				$output .= $tpl->render($tpls['ellipsis']);
				// sec last
				$output .= $tpl->render($tpls['secondlast'], array('href' => $pageurl . ($lastpage - 1),
					'counter' => ($lastpage - 1)), true);
				// last
				$output .= $tpl->render($tpls['last'], array('href' => $pageurl . $lastpage,
					'counter' => $lastpage), true);
			}
			//close to end; only hide early pages
			else
			{
				// first ($pageurl . '1')
				$output .= $tpl->render($tpls['first'], array('href' => $pageurl . '1'), true);
				// second
				$output .= $tpl->render($tpls['second'], array('href' => $pageurl . '2', 'counter' => '2'), true);
				// ...
				$output .= $tpl->render($tpls['ellipsis']);

				for($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if($counter == $page)
					{
						$output .= $tpl->render($tpls['central_inactive'], array('counter' => $counter), true);
					} else
					{
						$output .= $tpl->render($tpls['central'], array('href' => $pageurl . $counter,
							'counter' => $counter), true);
					}
				}
			}
		}
		//next link
		if($page < $counter - 1)
			$output .= $tpl->render($tpls['next'], array('href' => $pageurl . $next), true);
		else
			$output .= $tpl->render($tpls['next_inactive'], array(), true);

		return $tpl->render($tpls['wrapper'], array('value' => $output), true);
	}
}
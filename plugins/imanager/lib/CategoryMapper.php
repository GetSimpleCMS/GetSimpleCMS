<?php

class CategoryMapper
{
	/**
	 * @var array of the object of type Category
	 */
	//public  $categories;
	/**
	 * @var string filter by node
	 */
	private $filterby;


	public $total = 0;


	public function __construct()
	{
		//$this->categories = array();
	}


	public function &__get($param)
	{
		if($param == 'categories') {
			$this->init();
			return $this->categories;
		}
	}

	/**
	 * Initializes all the categories and made them available in ImCategory::$categories buffer
	 */
	public function init()
	{
		$this->categories = array();
		foreach (glob(IM_CATEGORY_DIR . '*' . IM_CATEGORY_FILE_SUFFIX) as $file)
		{
			$cat = new Category();

			$base = basename($file);
			$strp = strpos($base, '.');
			$id = substr($base, 0, $strp);
			$xml = getXML($file);

			if(!$cat->setProtectedParams((int) $id))
				continue;

			$cat->name = (string) $xml->name;
			$cat->slug = (string) $xml->slug;
			$cat->position = (int) $xml->position;
			$cat->created = (int) $xml->created;
			$cat->updated = (int) $xml->updated;

			$this->categories[$cat->get('id')] = $cat;
		}
		$this->total = count($this->categories);
	}


	/**
	 * Returns the number of categories
	 *
	 * @param array $categories
	 * @return int
	 */
	public function countCategories(array $categories=array())
	{return count($cat = !empty($categories) ? $categories : $this->categories);}


	/**
	 * Returns the object of type Category
	 * NOTE: However if no $categories argument is passed to the function, the categories
	 * must already be in the buffer: ImCategory::$categories. Call the ImCategory::init()
	 * method before to assign the categories to the buffer.
	 *
	 * You can search for category by ID: ImCategory::getCategory(2) or similar to ImCategory::getCategory('id=2')
	 * or by category name ImCategory::getCategory('name=My category name')
	 *
	 * @param string/integer $stat
	 * @param array $categories
	 * @return boolean|object of the type Category
	 */
	public function getCategory($stat, array $categories=array())
	{

		$loccat = !empty($categories) ? $categories : $this->categories;
		// nothing to select
		if(empty($categories))
		{
			if(!$this->countCategories() || $this->countCategories() <= 0)
				return false;
		}

		// stat is an id
		if(is_numeric($stat))
		{
			// id not found
			if(!isset($loccat[(int) $stat]) || !$loccat[(int) $stat]->get('id'))
				return false;

			return !empty($loccat[(int) $stat]) ? $loccat[(int) $stat] : false;

			// stat is a string
		} elseif (false !== strpos($stat, '='))
		{
			$data = explode('=', $stat, 2);
			$key = strtolower(trim($data[0]));
			$val = trim($data[1]);
			if(false !== strpos($key, ' '))
				return false;

			// id
			if($key == 'id')
			{
				return !empty($loccat[(int) $val]) ? $loccat[(int) $val] : false;
			}
			foreach($loccat as $catid => $c)
			{
				if(!isset($c->$key) || strtolower($c->$key) != strtolower($val)) continue;

				return !empty($loccat[$catid]) ? $loccat[$catid] : false;
			}
		}
		return false;
	}


	/**
	 * Returns the array of objects of the type Category, by a comparison of values
	 * NOTE: However if no $categories argument is passed to the function, the categories
	 * must already be in the buffer: ImCategory::$categories. Call the ImCategory::init()
	 * method before to assign the categories to the buffer.
	 *
	 * You can sort categories by using any node
	 * Sample sortng by "position":
	 * ImCategory::filterCategories('position', 'DESC', $your_categories_array)
	 *
	 * @param string $filterby
	 * @param string $key
	 * @param array $categories
	 * @return boolean|array
	 */
	public function getCategories($stat, $offset=0, $length=0, array $categories=array())
	{
		// reset offset
		$offset = ($offset > 0) ? $offset-1 : $offset;

		$loccat = !empty($categories) ? $categories : $this->categories;
		// nothing to select
		if(empty($categories))
		{
			if(!$this->countCategories() || $this->countCategories() <= 0)
				return false;
		}

		$catcontainer = array();

		$pattern = array(0 => '>=', 1 => '<=', 2 => '!=', 3 => '>', 4 => '<', 5 => '=');

		foreach($pattern as $pkey => $pval)
		{
			if(false !== strpos($stat, $pval))
			{

				$data = explode($pval, $stat, 2);
				$key = strtolower(trim($data[0]));
				if($pkey != 5 && $pkey != 2)
					$val = (int) trim($data[1]);
				else
					$val = trim($data[1]);

				if(false !== strpos($key, ' '))
					return false;

				foreach($loccat as $cat_id => $c)
				{
					if($pkey == 0)
					{
						if(!isset($c->$key) || $c->$key < $val) continue;
					} elseif($pkey == 1)
					{
						if(!isset($c->$key) || $c->$key > $val) continue;
					} elseif($pkey == 2)
					{
						if(!isset($c->$key) || $c->$key == $val) continue;
					} elseif($pkey == 3)
					{
						if(!isset($c->$key) || $c->$key <= $val) continue;
					} elseif($pkey == 4)
					{
						if(!isset($c->$key) || $c->$key >= $val) continue;
					} elseif($pkey == 5)
					{
						if(!isset($c->$key) || $c->$key != $val) continue;
					}

					$catcontainer[$cat_id] = $loccat[$cat_id];
				}
				if(!empty($catcontainer))
				{
					// limited output
					if((int) $offset > 0 || (int) $length > 0)
					{
						if((int) $length == 0) $len = null;
						$catcontainer = array_slice($catcontainer, (int) $offset, (int) $length, true);
					}
					return $catcontainer;
				}
				return false;
			}
		}
		return false;
	}


	/**
	 * Returns the array of objects of the type Category, sorted by any node
	 * NOTE: However if no $categories argument is passed to the function, the categories
	 * must already be in the buffer: ImCategory::$categories. Call the ImCategory::init()
	 * method before to assign the categories to the buffer.
	 *
	 * You can sort categories by using any node
	 * Sample sortng by "position":
	 * ImCategory::filterCategories('position', 'DESC', $your_categories_array)
	 *
	 * @param string $filterby
	 * @param string $key
	 * @param array $categories
	 * @return boolean|array of objects of the type Category
	 */
	public function filterCategories($filterby, $key, $offset=0, $length=0, array $categories=array())
	{
		// reset offset
		$offset = ($offset > 0) ? $offset-1 : $offset;

		$loccat = !empty($categories) ? $categories : $this->categories;
		if(empty($categories))
		{
			if(!$this->countCategories() || $this->countCategories() <= 0)
				return false;
		}

		$catcontainer = array();

		foreach($loccat as $cat_id => $c)
		{
			if(!isset($c->$filterby)) continue;

			$catcontainer[$cat_id] = $loccat[$cat_id];
		}

		if(!empty($catcontainer))
		{
			$this->filterby = $filterby;
			usort($catcontainer, array($this, 'sortObjects'));
			// sorte DESCENDING
			if(strtolower($key) != 'asc') $catcontainer = $this->reverseCategories($catcontainer);
			$catcontainer = $this->reviseCatIds($catcontainer);

			// limited output
			if((int) $offset > 0 || (int) $length > 0)
			{
				if((int) $length == 0) $len = null;
				$catcontainer = array_slice($catcontainer, (int) $offset, (int) $length, true);
			}
			return $catcontainer;
		}

		return false;
	}


	/**
	 * Deletes the category
	 *
	 * @param Category $cat
	 * @return bool
	 */
	public function destroyCategory(Category $cat)
	{
		if(file_exists(IM_CATEGORY_DIR . $cat->get('id') . IM_CATEGORY_FILE_SUFFIX))
			return unlink(IM_CATEGORY_DIR . $cat->get('id') . IM_CATEGORY_FILE_SUFFIX);
		return false;
	}


	/**
	 * Reverse the array of categoriies
	 *
	 * @param array $catcontainer An array of objects
	 * @return boolean|array
	 */
	public function reverseCategories($catcontainer)
	{
		if(!is_array($catcontainer)) return false;
		return array_reverse($catcontainer);
	}


	/**
	 * Revise keys of the array of categories and changes these into real category Ids
	 *
	 * @param array $catcontainer An array of objects
	 * @return boolean|array
	 */
	public function reviseCatIds($catcontainer)
	{
		if(!is_array($catcontainer)) return false;
		$result = array();
		foreach($catcontainer as $val)
			$result[$val->get('id')] = $val;
		return $result;
	}


	/**
	 * Sorts the objects
	 *
	 * @param $a $b objects to be sorted
	 * @return boolean
	 */
	private function sortObjects($a, $b)
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
	}


	public function pagination(array $params, $argtpls = array())
	{

		$tpl = imanager()->getTemplateEngine();
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
		$pageurl = !empty($params['pageurl']) ? $params['pageurl'] : 'page=';
		$start = !empty($params['start']) ? $params['start'] : 1;

		$maxitemperpage = ((int) $config->backend->maxcatperpage > 0) ?
			$config->backend->maxcatperpage : 20;
		$limit = !empty($params['limit']) ? $params['limit'] : $config->backend->maxcatperpage;
		$adjacents = !empty($params['adjacents']) ? $params['adjacents'] : 3;
		$lastpage = !empty($params['lastpage']) ? $params['lastpage'] : ceil($params['items'] / $maxitemperpage);

		$next = ($page+1);
		$prev = ($page-1);

		//$tpl->init();
		// only one page to show
		if($lastpage <= 1)
			return $tpl->render($tpls['wrapper'], array('value' => ''), true);

		$output = '';

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
					$output .= $tpl->render($tpls['central'], array(
							'href' => $pageurl . $counter, 'counter' => $counter), true
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
				// first
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
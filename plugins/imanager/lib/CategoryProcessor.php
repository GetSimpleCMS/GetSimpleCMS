<?php
/**
* ImCategory class 
*
* Categories administration
*
*/
class CategoryProcessor
{
	public $is_cat_valid;
	public $is_cat_exist;
	private $category;

	public function __construct(CategoryMapper &$category)
	{
		// initialise categories
		$this->category = $category;

		if(self::isCategoryValid())
		{
			$this->is_cat_exist = true;
		} else
		{
			if($this->category->countCategories() && $this->category->countCategories() > 0)
			{
				$this->is_cat_exist = true;
				$cur = current($this->category->categories);
				$this->setCategory($cur->get('id'));
			} else
			{
				$this->is_cat_exist = false;
			}
		}
	}

	public function setCategory($newcat)
	{

		if(!isset($this->category->categories[(int) $newcat]) ||
			($this->category->categories[(int) $newcat]->get('id') == ''))
			return false;
		$this->is_cat_exist = true;
		$_SESSION['cat'] = (int) $newcat;
		return true;
	}

	public function currentCategory()
	{
		return (int) $_SESSION['cat'];
	}

	public function isCategoryValid($cq='')
	{
		if(isset($cq) && !empty($cq))
			if(!empty($this->category->categories[(int)$cq])) return true;
			else return false;

		if(!isset($_SESSION['cat']) || empty($_SESSION['cat']))
			return false;

		if(isset($this->category->categories[$_SESSION['cat']]) &&
			$this->category->categories[$_SESSION['cat']]->get('id') != '')
			return true;

		return false;
	}
}
?>

<?php

class Category
{
	//public $xmlobject;

	public function __construct()
	{
		$this->id = null;
		$this->file = '';
		$this->filename = '';

		$this->position = null;
		$this->name = '';
		$this->slug = '';
		$this->created = time();
		$this->updated = '';
	}

	public function get($name){return isset($this->$name) ? $this->$name : false;}

	public function set($key, $val)
	{

		$key = strtolower($key);
		$val = imanager('sanitizer')->text($val);
		// id is readonly
		if(!in_array($key, array('name', 'slug', 'position', 'created', 'updated')))
			return false;
		if($key == 'slug') $val = imanager('sanitizer')->pageName($val);
		if($key == 'position' || $key == 'created' || $key == 'updated') $val = (int) $val;
		$this->$key = $val;
	}

	public function setProtectedParams($id)
	{
		if(!is_numeric($id)) return false;
		$this->id = $id;
		$this->file = IM_CATEGORY_DIR.$id.IM_CATEGORY_FILE_SUFFIX;
		$this->filename = $id.IM_CATEGORY_FILE_SUFFIX;
		return true;
	}


	public function save()
	{
		// edit category
		if(!is_null($this->id) && $this->id > 0)
		{
			$xml = simplexml_load_file($this->file);
			$this->updated = time();

			$xml->id = (int) $this->id;
			$xml->name = (string) $this->name;
			$xml->slug = (string) $this->slug;
			$xml->position = !is_null($this->position) ? (int) $this->position : (int) $this->id;
			$xml->created = (int) $this->created;
			$xml->updated = !empty($this->updated) ? (int) $this->updated : time();

			return $xml->asXml($this->file);
		}

		// new category
		else
		{
			$c = imanager()->getCategoryMapper();
			$c->init();

			$this->id = 1;
			if(!empty($c->categories))
				$this->id = max(array_keys($c->categories))+1;

			$this->file = IM_CATEGORY_DIR.$this->id.IM_CATEGORY_FILE_SUFFIX;

			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><category></category>');

			$xml->name = (string) $this->name;
			$xml->slug = (string) $this->slug;
			$xml->position = !is_null($this->position) ? (int) $this->position : (int) $this->id;
			$xml->created = !empty($this->created) ? (int) $this->created : time();
			$xml->updated = (int) $this->updated;

			return $xml->asXml($this->file);
		}
	}
}
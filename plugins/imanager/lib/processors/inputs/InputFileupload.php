<?php

class InputFileupload implements InputInterface
{
	//protected $values;
	//protected $field;

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->values = new stdClass();
		$this->values->value = null;

		$this->values->file_name = array();
		$this->values->path = array();
		$this->values->fullpath = array();
		$this->values->url = array();
		$this->values->fullurl = array();
		$this->values->title = array();
		$this->positions = array();
		$this->titles = array();
	}


	public function prepareInput($value, $sanitize=false)
	{
		if(!file_exists($value))
			return $this->values;

		$temp_arr = array();


		if(empty($this->positions) && file_exists($value.'config.xml'))
		{
			$xml = simplexml_load_file($value.'config.xml');
			for($i = 0; $i < count($xml->image); $i++)
			{
				$this->positions[(int) $xml->image[$i]->position] = (string) $xml->image[$i]->name;
				$this->titles[(int) $xml->image[$i]->position] = (string) $xml->image[$i]->title;
			}
		}
		$i = 0;
		foreach(glob($value.'*') as $file)
		{
			if(is_dir($file) || 'xml' == pathinfo($file, PATHINFO_EXTENSION)) continue;

			$base = basename($file);
			$basedir = basename($value);

			$poskey = $i;
			$title = '';
			if(!empty($this->positions))
			{
				$poskey = array_search($base, $this->positions);
				$title = $this->titles[$poskey];
			}

			$temp_arr[$i] = new stdClass();
			$temp_arr[$i]->file_name = $base;
			$temp_arr[$i]->position = (int) $poskey;
			$temp_arr[$i]->path = $value;
			$temp_arr[$i]->fullpath = $value.$base;
			$temp_arr[$i]->url = 'data/uploads/imanager/'.$basedir.'/';
			$temp_arr[$i]->fullurl = 'data/uploads/imanager/'.$basedir.'/'.$base;
			$temp_arr[$i]->title = $title;

			$i++;
		}

		usort($temp_arr, array($this, 'sortObjects'));

		$this->values->value = $value;

		foreach($temp_arr as $key => $val)
		{
			$this->values->file_name[] = $temp_arr[$key]->file_name;
			$this->values->path[] = $temp_arr[$key]->path;
			$this->values->fullpath[] = $temp_arr[$key]->fullpath;
			$this->values->url[] = $temp_arr[$key]->url;
			$this->values->fullurl[] = $temp_arr[$key]->fullurl;
			$this->values->title[] = $temp_arr[$key]->title;
		}
		// delete empty config file
		if($i <= 0 && file_exists($value.'config.xml')) {unlink($value.'config.xml');}

		return $this->values;
	}


	public function prepareOutput(){return $this->values;}


	protected function sanitize($value){return imanager('sanitizer')->text($value);}


	private function sortObjects($a, $b)
	{
		$a = $a->position;
		$b = $b->position;

		if($a == $b) {return 0;}
		else
		{
			if($b > $a) {return -1;}
			else {return 1;}
		}
	}

	protected function getFullUrl()
	{
		return IM_SITE_URL;
	}
}
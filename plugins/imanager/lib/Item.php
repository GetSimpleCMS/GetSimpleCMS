<?php

class Item
{
	public function __construct($catid)
	{
		$this->categoryid = intval($catid);

		$this->id = null;
		$this->file = '';
		$this->filename = '';

		$this->name = '';
		$this->label = '';
		$this->position = null;
		$this->active = 0;

		$this->created = time();
		$this->updated = null;

		$this->fields = new \stdClass();
		// field arts object array
		$fc = new FieldMapper();
		$fc->init($catid);
		foreach($fc->fields as $name => $value)
			$this->fields->$name = $value;

	}



	public function getNextId()
	{
		// no category is selected, return false
		if(!$this->categoryid) return false;

		$ids = array();
		// check item file exists return back
		if(glob(IM_ITEM_DIR.'*.'.$this->categoryid.IM_ITEM_FILE_SUFFIX))
		{
			foreach (glob(IM_ITEM_DIR.'*.'.$this->categoryid.IM_ITEM_FILE_SUFFIX) as $file)
			{
				$base = basename($file, IM_ITEM_FILE_SUFFIX);
				$strp = strpos($base, '.');
				$ids[] = substr($base, 0, $strp);
			}
			return !empty($ids) ? max($ids)+1 : false;
		}
		// ok this may the first item for this category
		if(!file_exists(IM_ITEM_DIR.'1.'.$this->categoryid.IM_ITEM_FILE_SUFFIX))
			return 1;
	}


	public function set($key, $val){ $this->$key = $val; }


	public function setFieldValue($fieldname, $value, $sanitize=true)
	{
		if(empty($this->fields->$fieldname))
		{
			MsgReporter::setCode(6);
			return false;
		}
		$field = $this->fields->$fieldname;

		$inputClassName = 'Input'.ucfirst($field->type);
		$Input = new $inputClassName($field);
		if(!is_array($value))
		{
			if(!$sanitize)
			{
				$fieldvalue =  $Input->prepareInput($value);
				if(empty($fieldvalue) || is_int($fieldvalue))
				{
					MsgReporter::setCode($fieldvalue);
					return false;
				}
				$this->fields->{$fieldname}->value = $fieldvalue->value;
			} else {
				$fieldvalue = $Input->prepareInput($value, true);
				if(empty($fieldvalue) || is_int($fieldvalue))
				{
					MsgReporter::setCode($fieldvalue);
					return false;
				}
				$this->fields->{$fieldname}->value = $fieldvalue->value;
			}
			return true;

		} else
		{
			foreach($value as $key => $val)
			{
				if($key != 'value')
					$Input->$key = $val;
				elseif($key == 'value')
					$inputval = $val;
			}
			if(isset($inputval))
			{
				if(!$sanitize)
					$resultinput = $Input->prepareInput($inputval);
				else
					$resultinput = $Input->prepareInput($inputval, true);

				if(!empty($resultinput) && !is_int($resultinput))
				{
					foreach($resultinput as $inputputkey => $inputvalue)
						$this->fields->{$fieldname}->$inputputkey = $inputvalue;
					return true;
				}
				MsgReporter::setCode($resultinput);
				return false;
			}
			MsgReporter::setCode(6);
			return false;
		}
	}


	public function get($key)
	{
		if(isset($this->$key)) return $this->$key;

		return false;
	}


	public function save()
	{
		// new file
		if(is_null($this->id) && !file_exists(IM_ITEM_DIR.$this->id.'.'.$this->categoryid.IM_ITEM_FILE_SUFFIX))
		{
			$this->id = $this->getNextId();
			$this->file = IM_ITEM_DIR.$this->id.'.'.$this->categoryid.IM_ITEM_FILE_SUFFIX;
			$this->filename = $this->id.'.'.$this->categoryid.IM_ITEM_FILE_SUFFIX;

			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><item></item>');

			$xml->categoryid = $this->categoryid;
			$xml->id = $this->id;
			$xml->name = $this->name;
			$xml->label = $this->label;
			$xml->position = !is_null($this->position) ? $this->position : $this->id;
			$xml->active = $this->active;

			$xml->created = $this->created;
			$xml->updated = $this->updated;

			$data = $this->getFieldsDataToSave();

			if(!empty($data['ids']))
			{
				foreach($data['ids'] as $key => $val)
				{
					$xml->field[$key]->id = $val;

					if(!empty($this->fields->{$data['names'][$key]}->value))
					{
						$inputClassName = 'Input'.ucfirst($data['types'][$key]);
						$InputType = new $inputClassName($this->fields->{$data['names'][$key]});

						//$input = $InputType->prepareInput($this->fields->$data['names'][$key]->value);
						$output = $InputType->prepareOutput();
						$input = new stdClass();

						foreach ($output as $inputkey => $inputval)
							$input->$inputkey = $this->fields->{$data['names'][$key]}->$inputkey;

						foreach($input as $inputkey => $inputvalue)
						{
							if(!is_array($inputvalue))
							{
								$xml->field[$key]->$inputkey = $inputvalue;
							} else
							{
								foreach($inputvalue as $inputvalue_key => $inputvalue_value)
								{
									$xml->field[$key]->{$inputkey}[] = $inputvalue_value;
								}
							}
						}
					}
				}
			} else
				$xml->field = '';

			return $xml->asXml($this->file);

			// overwrite file
		} elseif(!is_null($this->id))
		{
			$xml = simplexml_load_file($this->file);

			$xml->categoryid = $this->categoryid;
			$xml->id = $this->id;
			$xml->name = $this->name;
			$xml->label = $this->label;
			$xml->position = !is_null($this->position) ? $this->position : $this->id;
			$xml->active = $this->active;

			$xml->created = $this->created;
			// simple check if item has been updated by another process
			if((int) $this->updated != (int) $xml->updated)
			{
				MsgReporter::setClause('err_updated_by_process', array(), true);
				MsgReporter::setCode(MsgReporter::ERR_UPDATED_BY_PROCESS);
				return false;
			}
			$xml->updated = time();

			$data = $this->getFieldsDataToSave();

			if(!empty($data['ids']))
			{
				$xmlbackup = clone $xml->field;
				unset($xml->field);
				foreach($data['ids'] as $key => $val)
				{
					$xml->field[$key]->id = $val;

					// first, check whether field exists (quickInit)
					//var_dump($this->fields->{$data['names'][$key]}->value);
					if(!isset($this->fields->{$data['names'][$key]}->value) && !empty($xmlbackup[$key]->value))
					{
						foreach($xmlbackup[$key] as $xmbackupkey => $xmlbackupvalue)
						{
							if(!is_array($xmlbackupvalue))
							{
								$xml->field[$key]->$xmbackupkey = $xmlbackupvalue;
							} else
							{
								foreach($xmlbackupvalue as $xmlbackupvalue_key => $xmlbackupvalue_value)
								{
									$xml->field[$key]->{$xmbackupkey}[] = $xmlbackupvalue_value;
								}
							}
						}
					}

					if(!empty($this->fields->{$data['names'][$key]}->value))
					{
						$inputClassName = 'Input'.ucfirst($data['types'][$key]);
						$InputType = new $inputClassName($this->fields->{$data['names'][$key]});

						$output = $InputType->prepareOutput();
						$input = new stdClass();

						foreach ($output as $inputkey => $inputval)
						{
							$input->$inputkey = $this->fields->{$data['names'][$key]}->$inputkey;
						}

						foreach($input as $inputkey => $inputvalue)
						{
							if(!is_array($inputvalue))
							{
								$xml->field[$key]->$inputkey = $inputvalue;
							} else
							{
								foreach($inputvalue as $inputvalue_key => $inputvalue_value)
								{
									$xml->field[$key]->{$inputkey}[] = $inputvalue_value;
								}
							}
						}
					}
				}
			} else
				$xml->fields = '';

			return $xml->asXml($this->file);

		}

		return false;
	}

	public function join($catids)
	{

		$imapper = imanager()->getItemMapper();
		if(!is_array($catids))
		{
			$imapper->limitedInit($catids, $this->id);
			$this->linked_categoryids[] = (int) $catids;
			$this->linked_fields[(int) $catids] = (!empty($imapper->items[$this->id]->fields) ?
				$imapper->items[$this->id]->fields : null);
		} else
		{
			foreach($catids as $catid)
			{
				$imapper->limitedInit($catid, $this->id);
				$this->linked_categoryids[] = (int) $catid;
				$this->linked_fields[(int) $catid] = $imapper->items[$this->id]->fields;
			}
		}
	}

	// todo: Wird die hier noch verwendet?
	public function getFieldValue($fieldid)
	{
		foreach($this->fields as $key => $val)
			var_dump($val);
	}

	protected function getFieldsDataToSave()
	{
		return FieldMapper::getFieldsSaveInfo($this->categoryid);
	}
}
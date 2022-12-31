<?php

class Processor
{
	protected $imanager;
	public $curcat;
	public $curitem;
	public $imapp;


	public function __construct()
	{
		$this->imanager = imanager();
		$this->imapp = $this->imanager->getItemMapper();
	}


	/**
	 * Just try to init the IM-Extra-Fields item for specific GS page
	 */
	public function init()
	{
		global $url, $id;
		if(empty($url)) $url = $id;
		// This is the selected category-ID
		$categoryid = !empty($_POST['epcatid']) ? (int)$_POST['epcatid'] : (!empty($_GET['epcatid']) ? (int)$_GET['epcatid'] : null);
		// No selected category was found, try to localize the category by item name, same like the current page slug
		if(is_null($categoryid))
		{
			$itemid = $this->computeUnsignedCRC32($url);
			foreach($this->imanager->getCategoryMapper()->categories as $category)
			{
				if(NUMUNIFY) {
					$this->imapp->limitedInit($category->id, $itemid);
					$this->curitem = $this->imapp->getItem('name='.$itemid);
				} else {
					$this->imapp->init($category->id);
					$this->curitem = $this->imapp->getItem('name='.$url);
				}

				if(!empty($this->curitem))
				{
					$this->curcat = $this->imanager->getCategory($this->curitem->categoryid);
					return;
				}
			}

		// Do not use IM-Extra-Fields for this page
		} elseif($categoryid < 0)
		{
			$this->curcat = '';
			$this->curitem = '';

		} else
		{
			$this->curcat = $this->imanager->getCategory($categoryid);

			if(NUMUNIFY) {
				$this->imapp->limitedInit($this->curcat->id, $this->computeUnsignedCRC32($url));
				$this->curitem = $this->imapp->getItem('name='.$this->computeUnsignedCRC32($url));
			} else {
				$this->imapp->init($this->curcat->id);
				$this->curitem = $this->imapp->getItem('name='.$url);
			}

			if(empty($this->curitem->id)) $this->curitem = new Item($categoryid);
		}
	}


	/**
	 * This method will called after page data saved, so we don't
	 * need to check the values like page title, slug etc...
	 *
	 * @return bool
	 */
	public function saveItem()
	{
		global $url;
		$error = false;

		// No Category is selected just return without saving
		if(empty($_POST['imcat'])) return;

		// Check if category is valid and delete the assigned item if necessary
		$mapper = $this->imanager->getCategoryMapper();
		$this->imanager->ProcessCategory();
		if(!$this->imanager->cp->isCategoryValid((int)$_POST['imcat']))
		{
			// User selected an empty option in category selector, so delete the assigned item
			if(isset($_POST['categoryid']) && $_POST['categoryid'] == -1) {
				$this->searchAndDelete($mapper->categories, $url);
			}
			return;
		}

		$categoryid = (int)$_POST['imcat'];

		if(!empty($_POST['itemid'])) {
			$this->imapp->limitedInit($categoryid, (int)$_POST['itemid']);
		}

		$curitem = !empty($this->imapp->items[(int)@$_POST['itemid']]) ?
			$this->imapp->items[(int)$_POST['itemid']] : new Item($categoryid);
		// Clean up cached images
		$this->imanager->cleanUpCachedFiles($curitem);

		// Check if slug field available
		foreach($curitem->fields as $fieldname => $fieldvalue)
		{
			$fieldinput = !empty($_POST[$fieldname]) ? str_replace('"', "'", $_POST[$fieldname]) : '';
			$inputClassName = 'Input'.ucfirst($fieldvalue->type);
			$InputType = new $inputClassName($curitem->fields->$fieldname);

			// imageupload
			if($fieldvalue->type == 'imageupload' || $fieldvalue->type == 'fileupload')
			{

				// new item
				if(empty($_POST['itemid']) && !empty($_POST['timestamp']))
				{
					// pass temporary image directory
					$tmp_image_dir = IM_IMAGE_UPLOAD_DIR.'tmp_'.(int)$_POST['timestamp'].'_'.$categoryid.'/';
					$fieldinput = $tmp_image_dir;
				} else
				{
					// pass image directory
					$fieldinput = IM_IMAGE_UPLOAD_DIR.$curitem->id.'.'.$categoryid.'/';
				}

				// position is send
				if(isset($_POST['position']) && is_array($_POST['position']))
				{
					$InputType->positions = $_POST['position'];
					$InputType->titles = isset($_POST['title']) ? $_POST['title'] : '';

					if(!file_exists($fieldinput.'config.xml'))
					{
						$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><params></params>');
						$i = 0;
						foreach($InputType->positions as $filepos => $filename)
						{
							$xml->image[$i]->name = $filename;
							$xml->image[$i]->position = $filepos;
							$xml->image[$i]->title = !empty($InputType->titles[$filepos])
								? $InputType->titles[$filepos] : '';
							$i++;
						}

					} else
					{
						$xml = simplexml_load_file($fieldinput.'config.xml');
						unset($xml->image);
						$i = 0;
						foreach($InputType->positions as $filepos => $filename)
						{
							$xml->image[$i]->name = $filename;
							$xml->image[$i]->position = $filepos;
							$xml->image[$i]->title = !empty($InputType->titles[$filepos])
								? $InputType->titles[$filepos] : '';
							$i++;
						}
					}

					if(is_dir($fieldinput))
						$xml->asXml($fieldinput.'config.xml');
				}
			} elseif($fieldvalue->type == 'password')
			{
				$InputType->confirm = !empty($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
				// refill password field values if empty
				$InputType->password = !empty($curitem->fields->$fieldname->value)
					? $curitem->fields->$fieldname->value : '';
				$InputType->salt = !empty($curitem->fields->$fieldname->salt)
					? $curitem->fields->$fieldname->salt : '';
				$fieldinput = !empty($_POST['password']) ? $_POST['password'] : '';
			}

			$resultinput = $InputType->prepareInput($fieldinput);

			if(!isset($resultinput) || empty($resultinput) || is_int($resultinput))
			{
				// parse error codes
				switch ($resultinput)
				{
					case 1:
						$error = 'The field '.$fieldvalue->label.' must be filled out';
						redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode($error));
					case 2:
						$error = 'Error field value length: The value of the '.$fieldvalue->label.' should be minimum of '.$fieldvalue->minimum.' characters';
						redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode($error));
					case 3:
						$error = 'Error field value length: The value of the '.$fieldvalue->label.' is limited to a maximum of '.$fieldvalue->maximum.' characters';
						redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode($error));
					case 5:
						$error = 'Error value incomplete: '.$fieldvalue->label.' all fields have to be filled out';
						redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode($error));
					case 7:
						$error = 'Error: Values of the '.$fieldvalue->label.' field do not match.';
						redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode($error));
					case 8:
						$error = 'Error: Invalid value format for the fieldtype '.$fieldvalue->label;
						redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode($error));
				}
				redirect("edit.php?id=$url&epcatid=$categoryid&upd=edit-error&type=".urlencode('An error occurred when saving the item'));
				return;
			}

			foreach($resultinput as $inputputkey => $inputvalue)
				$curitem->fields->$fieldname->$inputputkey = $inputvalue;
		}

		if(NUMUNIFY) {
			$id = $this->computeUnsignedCRC32($url);
			$curitem->name = $id;
			$curitem->id = $id;
		} else {
			$curitem->name = $url;
		}
		$curitem->active = 1;

		// Delete all items with the same name. There i see no other option to prevent orphaned files
		$this->searchAndDelete($mapper->categories, $url, $curitem->id);

		if(NUMUNIFY) {
			if(!$curitem->forcedSave()) {
				redirect("edit.php?id=$url&upd=edit-error&type=".urlencode(MsgReporter::getClause('err_save_item')));
				return false;
			}
		} else {
			if(!$curitem->save()) {
				redirect("edit.php?id=$url&upd=edit-error&type=".urlencode(MsgReporter::getClause('err_save_item')));
				return false;
			}
		}
		// Save SimpleItem
		$this->saveSimpleItem($curitem);

		$this->imanager->getSectionCache()->expire();

		/* Check if it's a new item, so temporary image directory should be renamed */
		if(!empty($tmp_image_dir) && file_exists($tmp_image_dir))
		{
			$this->imanager->renameTmpDir($curitem);
			// clean up directories from orphaned data
			$this->imanager->cleanUpTempContainers('imageupload');
			$this->imanager->cleanUpTempContainers('fileupload');
		}
		// delete search index (i18n search). Currently we do not use this stuff
		//$this->imanager->deleteSearchIndex();
		return true;
	}

	/**
	 * The method saves SimpleItem object if useAllocater is activated
	 *
	 * @return bool
	 */
	protected function saveSimpleItem($curitem)
	{
		if($this->imanager->config->useAllocater !== true) {
			return false;
		}
		if($this->imapp->alloc($curitem->categoryid) !== true) {
			$this->imapp->init($curitem->categoryid);
			if(!empty($this->imapp->items)) {
				$this->imapp->simplifyBunch($this->imapp->items);
				$this->imapp->save();
			}
		}
		$this->imapp->simplify($curitem);
		return ($this->imapp->save() !== false) ? true : false;
	}


	/**
	 * Scan all categories for a specific item name and delete it physically
	 *
	 * @return bool
	 */
	protected function searchAndDelete($categories, $name, $excludeid = null)
	{
		// Delete all items with the same name. There i see no other option to prevent orphaned files
		$crc32name = $this->computeUnsignedCRC32($name);
		foreach($categories as $category)
		{
			if(NUMUNIFY) {
				$this->imapp->limitedInit($category->id, $crc32name);
				$orphaneditem = $this->imapp->getItem('name='.$crc32name);
			} else {
				$this->imapp->init($category->id);
				$orphaneditem = $this->imapp->getItem('name='.$name);
			}
			if(!empty($orphaneditem) && $orphaneditem->id != $excludeid)
			{
				if(!empty($orphaneditem->categoryid))
					return $this->imanager->deleteItem($orphaneditem->id, $orphaneditem->categoryid);
			}
		}
	}


	/**
	 * Delete a single item
	 *
	 * @return bool
	 */
	public function deleteItem()
	{
		if(!empty($this->curcat->id) && !empty($this->curitem->id))
			return $this->imanager->deleteItem($this->curitem->id, $this->curcat->id);
	}


	/**
	 * Function to compute the unsigned crc32 value.
	 * PHP crc32 function returns int which is signed, so in order to get the correct crc32 value
	 * we need to convert it to unsigned value.
	 *
	 * NOTE: it produces different results on 64-bit compared to 32-bit PHP system
	 *
	 * @param $str - String to compute the unsigned crc32 value.
	 * @return $var - Unsinged inter value.
	 */
	private function computeUnsignedCRC32($str)
	{
		sscanf(crc32($str), "%u", $var);
		return $var;
	}
}
<?php

class Manager
{
	protected static $categoryMapper = null;
	protected static $itemMapper = null;
	protected static $templateEngine = null;
	protected static $sectionCache = null;
	protected $actionsProcessor = null;

	public $sanitizer = null;
	// Configuration Class
	public $config;

	// is ItemManager installed
	public static $installed;

	public $admin = null;

	public function __construct()
	{
		// set ItemManager-installed to false
		self::$installed = false;
		// initialize settup class
		$this->config = new Setup();
		// Deprecated check if the user inside admin panel (not really for security reasons, just to avoid the backend loading)
		$this->is_admin_panel = false;
		// start SETUP Procedure
		if(!file_exists(ITEMDATA))
		{
			if($this->config->setup())
				if(!file_exists(IM_CONFIG_FILE))
				{
					if($this->config->setupConfig())
					{
						$this->preferencesRefresh();
						self::$installed = true;
					}
				}
		} else
		{
			self::$installed = true;
		}

		// Admin only aktions
		if(defined('IS_ADMIN_PANEL') && !$this->config->hiddeAdmin) {
			$this->is_admin_panel = !empty(self::$installed) ? true : false;
			// Initialize Admin
			$this->setAdmin(new Admin());
		}

		// Set actions?
		if(!empty($this->config->injectActions))
		{
			global $plugins;
			$actions = array('ImActivated');
			foreach($plugins as $key => $plugin)
			{
				if(in_array($plugin['hook'], $actions))
				{
					$plugins[$key]['args']['imanager'] = & $this;
				}
			}
			$this->setActions();
		}

		$this->sanitizer = new Sanitizer();
	}


	public function setAdmin($admin)
	{
		$this->admin = $admin;
	}


	// Set Actions
	public function setActions()
	{
		global $plugins;
		$actions = array('ImActivated');
		if(function_exists('exec_action')) exec_action('ImActivated');
	}


	public function __call($method, $args)
	{
		//if($this->actionsProcessor === null)
			//$this->actionsProcessor = new ActionsProcessor();
		//return $this->actionsProcessor->{$method}($args);
		//return $this->{$method}($args);
	}


	public function ProcessCategory() {
		self::$categoryMapper = (!self::$categoryMapper) ? $this->getCategoryMapper() : self::$categoryMapper;
		$this->cp = new CategoryProcessor(self::$categoryMapper);
	}

	/**
	 * Deletes the category and ther fields and items
	 *
	 * @param integer $cat, category id
	 * @param bool $refresh
	 * @return bool
	 */
	public function deleteCategory($cat, $refresh=false)
	{
		if(empty($cat)) return false;

		if(!is_numeric($cat)) return false;

		$cat = $this->getCategoryMapper()->getCategory($cat);

		if(!$cat)
		{
			MsgReporter::setClause('err_deleting_category', array(
					'errormsg' => MsgReporter::getClause('err_category_not_exists', array()))
			);
			return false;
		}


		$fc = new FieldMapper();

		// try to create fields backup of the category to be deleted
		if(intval($this->config->backend->fieldbackup) == 1  && !empty($fc))
		{
			$fc->init($cat->get('id'));
			if(!$fc->fieldsExists($cat->id))
				if(!$fc->createFields($cat->id))
				{
					MsgReporter::setClause('save_failure', array(), true);
					return false;
				}

			if(!$this->config->createBackup(IM_FIELDS_DIR, $cat->id, IM_FIELDS_FILE_SUFFIX))
			{
				MsgReporter::setClause('err_backup', array('backup' => $this->config->backend->fieldbackupdir), true);
				return false;
			}
		}

		// create category backup
		if(intval($this->config->backend->catbackup) == 1)
		{
			if(!$this->config->createBackup(IM_CATEGORY_DIR, $cat->id, IM_CATEGORY_FILE_SUFFIX))
			{
				MsgReporter::setClause('err_backup', array('backup' => $this->config->backend->catbackupdir), true);
				return false;
			}
		}

		$ic = $this->getItemMapper();
		$ic->init($cat->id);

		// backup items before delete category
		if(intval($this->config->backend->itembackup) == 1 && !empty($ic->items))
		{
			foreach($ic->items as $item_id => $item)
			{
				if(!$this->config->createBackup(IM_ITEM_DIR, $item_id.'.'.$item->categoryid,
					IM_ITEM_FILE_SUFFIX))
				{
					MsgReporter::setClause('err_backup', array(
							'backup' => $this->config->backend->itembackupdir), true
					);
					return false;
				}

				// get image directory to delete
				$imagedir = IM_IMAGE_UPLOAD_DIR.$item_id.'.'.$item->categoryid;

				if(!$ic->destroyItem($item))
				{
					MsgReporter::setClause('err_item_delete', array(), true);
					return false;
				}
				/* Item has been successfully deleted, now we have to clean up the image uploads */
				$this->delTree($imagedir);
			}
		}
		// destroy category file
		if(!self::$categoryMapper->destroyCategory($cat))
		{
			MsgReporter::setClause('err_deleting_category', array(
					'errormsg' => MsgReporter::getClause('err_category_file_writable', array()))
			);
			return false;
		}
		// disalloc SimpleItems
		if($this->config->useAllocater == true) { $ic->disalloc($cat->id); }

		// destroy fields file
		if(isset($fc) && !$fc->destroyFieldsFile($cat))
		{
			MsgReporter::setClause('err_delete_fields_file', array(), true);
			return false;
		}

		// reinitialize the categories
		if($refresh) self::$categoryMapper->init();

		// reselect current category if its deleted

		MsgReporter::setClause('category_deleted', array('category' => $cat->name));
		return true;
	}



	public function createCategoryByName($cat, $refresh=false)
	{
		if(empty($cat)) return false;

		if(!is_string($cat)) return false;

		if(false !== strpos($cat, '='))
		{
			$data = explode('=', $cat, 2);
			$key = strtolower(trim($data[0]));
			$val = trim($data[1]);
			if(false !== strpos($key, ' ')) return false;
			if($key != 'name') return false;
			$cat = $val;
		}

		if(strlen($cat) > $this->config->common->maxcatname)
		{
			MsgReporter::setClause('err_category_name_length',
				array('count' => $this->config->common->maxcatname),true);
			return false;
		}
		// CHECK here category name
		$new_cat = new Category();
		$new_cat->name = $this->sanitizer->text(str_replace('"', '\'', $cat));

		$new_cat->slug = $this->sanitizer->pageName($cat);

		// do not save category if name already exists
		if(!$this->getCategoryMapper()->getCategory('name='.$this->sanitizer->text($new_cat->name)))
			$new_cat->save();
		else
		{
			MsgReporter::setClause('err_category_name_exists', array(), true);
			return false;
		}

		// reinitialize categories
		if($refresh) self::$categoryMapper->init();

		MsgReporter::setClause('successfull_category_created', array(
				'category' => $this->sanitizer->text($new_cat->name))
		);
		return true;
	}


	public function updateCategory($input, $refresh=false)
	{
		if(empty($input)) return false;
		if(empty($input['id']) || !is_numeric($input['id'])) return false;

		$cat = $this->getCategoryMapper()->getCategory($input['id']);

		if(!$cat)
		{
			MsgReporter::setClause('err_updating_category', array(
					'errormsg' => MsgReporter::getClause('err_category_not_exists', array()), true)
			);
			return false;
		}

		$flag = true;

		if(strlen($input['name']) > $this->config->common->maxcatname)
		{
			MsgReporter::setClause('err_category_name_length', array('count' => $this->config->common->maxcatname), true);
			return false;
		}
		if(str_replace('"', '\'', $input['name']) != $cat->name)
		{
			if(!self::$categoryMapper->getCategory('name='.$this->sanitizer->text(str_replace('"', '\'', $input['name']))))
			{
				$cat->name = $this->sanitizer->text(str_replace('"', '\'', $input['name']));
				$flag = true;
			} else
			{
				MsgReporter::setClause('err_category_name_exists', array(), true);
				return false;
			}
		}
		if(!is_numeric($input['position']))
		{
			MsgReporter::setClause('err_category_position', array(), true);
			return false;
		}


		// set slug
		$cat->slug =  !empty($input['slug']) ? $this->sanitizer->pageName($input['slug']) : $this->sanitizer->pageName($input['name']);

		if(!empty($input['position']))
		{
			if((int)$input['position'] != (int)$cat->position)
			{
				$cat->position = (int)$input['position'];
				$flag = true;
			}
		}

		if($flag)
		{
			$cat->save();
			// reinitialize categories
			if($refresh) self::$categoryMapper->init();

			MsgReporter::setClause('successfull_category_updated', array(
					'category' => $this->sanitizer->text($cat->name))
			);
			return true;
		}
	}


	public function createFields(array $input)
	{
		if(empty($input)) return false;

		$this->ProcessCategory();
		// Check category first
		if(!$this->cp->isCategoryValid($input['cat']))
		{
			MsgReporter::setClause('invalid_category', array(), true);
			return false;
		}

		$ids = array();
		$names = array();
		$labels = array();
		$types = array();
		$options = array();
		$defaults = array();
		// walk through input
		for($i=0; isset($input['cf_'.$i.'_key']); $i++)
		{
			// check the max name length
			if(!empty($input['cf_'.$i.'_key']) && $this->config->common->maxfieldname > $input['cf_'.$i.'_key'])
			{
				MsgReporter::setClause('err_save_fields_maxlength', array(
						'count' => intval($this->config->common->maxfieldname), true
					)
				);
				continue;
			}

			if(!empty($input['cf_'.$i.'_key']))
			{
				if(!empty($input['cf_'.$i.'_type']) && ($input['cf_'.$i.'_type'] == 'imageupload' || $input['cf_'.$i.'_type'] == 'fileupload'))
				{
					if(in_array($input['cf_'.$i.'_type'], $types))
					{
						MsgReporter::setClause('err_upload_fields_usage', array(), true);
						continue;
					}
				}

				// Check/Rename reserved field names
				$buffname = strtolower($input['cf_'.$i.'_key']);
				if(in_array($buffname, array('id', 'categoryid', 'file', 'filename', 'name', 'label', 'position', 'active', 'created', 'updated'))) {
					$newname = $buffname.($i + 1);
					MsgReporter::setClause('err_reserved_field_name', array('fieldname' => $buffname, 'newname' => $newname), true);
					$buffname = $newname;
				}


				$ids[] = !empty($input['cf_'.$i.'_id']) ? intval($input['cf_'.$i.'_id']) : null;
				$names[] = $buffname;
				$labels[] = !empty($input['cf_'.$i.'_label']) ? $input['cf_'.$i.'_label'] : '';
				$types[] = !empty($input['cf_'.$i.'_type']) ? $input['cf_'.$i.'_type'] : '';
				$options[] = !empty($input['cf_'.$i.'_options']) ? $input['cf_'.$i.'_options'] : '';
				$defaults[] = !empty($input['cf_'.$i.'_value']) ? $input['cf_'.$i.'_value'] : '';
			}
		}

		// Show message when duplicate values exist, but save correctly entered names
		if(count($names) != count(array_unique($names)))
		{
			//$names = array_unique($names);
			// remove duplicate keys in other arrays
			$dupl = $this->getDuplicate($names);
			if(!empty($dupl))
			{
				foreach($dupl as $val)
				{
					unset($ids[$val]);
					unset($names[$val]);
					unset($labels[$val]);
					unset($types[$val]);
					unset($options[$val]);
					unset($defaults[$val]);
				}
			}
			MsgReporter::setClause('err_save_fields_unique', array(), true);
		}

		$fc = new FieldMapper();
		$fc->init($input['cat']);

		// backup fields?
		if((int)$this->config->backend->fieldbackup == 1)
		{
			if(!$fc->fieldsExists($input['cat']))
				if(!$fc->createFields($input['cat']))
				{
					MsgReporter::setClause('save_failure', array(), true);
					return false;
				}
			if(!$this->config->createBackup(IM_FIELDS_DIR, $input['cat'], IM_FIELDS_FILE_SUFFIX))
			{
				MsgReporter::setClause('err_backup', array('backup' => $this->config->backend->fieldbackupdir), true);
				return false;
			}
		}

		// Update the field data or create new
		foreach($ids as $key => $id)
		{
			// check if fields already exists
			$field = $fc->getField($id);

			if($field)
			{
				$field->name = str_replace('-', '_', $this->sanitizer->pageName($names[$key]));
				$field->label = str_replace('"', '\'', $labels[$key]);
				$field->type = $this->sanitizer->pageName($types[$key]);
				$field->position = $key+1;
				// Handle chunk field default value
				if($field->type == 'chunk') {
					// Do not allow to save default for the chunk field
					//$field->default = !empty($defaults[$key]) ? $defaults[$key] : '';
				} else {
					$field->default = str_replace('"', '\'', $defaults[$key]);
				}
				$field->options = array();
				if(!empty($options[$key]))
				{
					$split = preg_split("/\r?\n/", rtrim(stripslashes(str_replace('"', '\'', $options[$key]))));
					foreach($split as $option)
						$field->options[] = $option;
				}
			// field does not exist, create a new field
			} else
			{
				$field = new Field($input['cat']);
				$field->name = $this->sanitizer->pageName($names[$key]);
				$field->label = str_replace('"', '\'', $labels[$key]);
				$field->type = $this->sanitizer->pageName($types[$key]);
				$field->position = $key+1;
				// Handle chunk field default value
				if($field->type == 'chunk') {
					// Do not allow to save default for the chunk field
					//$field->default = !empty($defaults[$key]) ? $defaults[$key] : '';
				} else {
					$field->default = str_replace('"', '\'', $defaults[$key]);
				}
				$field->options = array();
				if(!empty($options[$key]))
				{
					$split = preg_split("/\r?\n/", rtrim(stripslashes(str_replace('"', '\'', $options[$key]))));
					foreach($split as $option)
						$field->options[] = $option;
				}
			}
			$field->save();
		}

		// useAllocater is activated
		if($this->config->useAllocater == true) {
			$mapper = $this->getItemMapper();
			$mapper->disalloc($input['cat']);

			if($mapper->alloc($input['cat']) !== true)
			{
				$mapper->init($input['cat']);
				if(!empty($mapper->items))
				{
					$mapper->simplifyBunch($mapper->items);
					$mapper->save();
				}
			}
		}

		// remove deleted fieds
		$data = FieldMapper::getFieldsSaveInfo($input['cat']);
		$result = array_diff($data['ids'], $ids);
		foreach($result as $fieldkey)
		{
			$deletion = $fc->getField($fieldkey);
			if(is_object($deletion) && !$deletion->delete())
			{
				MsgReporter::setClause('err_delete_field', array('fieldname' => $deletion->name), true);
				return false;
			}
		}

		MsgReporter::setClause('save_success');
		return true;
	}

	public function saveFieldDetails($input)
	{
		$cf = new FieldMapper();
		$this->ProcessCategory();
		$cf->init($this->cp->currentCategory());
		// get current field by id
		$currfield = $cf->getField((int)$input['field']);

		if(!$currfield)
		{
			// todo: must to be corrected (real error?)
			MsgReporter::setClause('err_field_id', array(), true);
			return false;
		}

		// Handle chunk field
		if($currfield->type == 'chunk') {
			$currfield->default = !empty($input['default']) ? $input['default'] : '';
		} else {
			$currfield->default = !empty($input['default']) ? str_replace('"', "'", $input['default']) : '';
		}

		// <div><p>Das ist ein test 3</p><textarea name="bla">Mla</textarea></div>

		$currfield->info = !empty($input['info']) ? str_replace('"', "'", $input['info']) : '';
		$currfield->required = (isset($input['required']) && $input['required'] > 0) ? 1 : null;
		$currfield->minimum = (isset($input['min_field_input']) && intval($input['min_field_input']) > 0)
			? intval($input['min_field_input']) : null;
		$currfield->maximum = (isset($input['max_field_input']) && intval($input['max_field_input']) > 0)
			? intval($input['max_field_input']) : null;
		$currfield->areaclass = !empty($input['areaclass']) ? str_replace('"', "'", $input['areaclass']) : '';
		$currfield->labelclass = !empty($input['labelclass']) ? str_replace('"', "'", $input['labelclass']) : '';
		$currfield->fieldclass = !empty($input['fieldclass']) ? str_replace('"', "'", $input['fieldclass']) : '';

		// process custom Fieldtype settings
		foreach($input as $key => $value)
		{
			if(strpos($key, 'custom-') !== false)
			{
				$fieldkey = str_replace('custom-', '', $key);
				$currfield->configs->$fieldkey = $value;
			}
		}

		$currfield->save();

		MsgReporter::setClause('save_success');
		return true;

	}

	public static function deleteSearchIndex()
	{
		if(function_exists('delete_i18n_search_index'))
			delete_i18n_search_index();
	}


	protected function getDuplicate($arr, $clean=false)
	{
		if($clean) {
			return array_unique($arr);
		}
		$new_arr = array();
		$dups = array();
		foreach ($arr as $key => $val) {
			if (!isset($new_arr[$val])) {
				$new_arr[$val] = $key;
			} else {
				$dups[] = $key;
			}
		}
		return $dups;
	}




	public function buildPagination(array $tpls, array $params)
	{
		return self::$itemMapper->pagination($params, $tpls);
	}


	public function saveItem(&$input)
	{
		/* check there user errors: If user tried to compromise script, just
		reset field values and display an error message */

		// timestamp or item id required
		if(empty($input['timestamp']) && empty($input['iid']))
		{
			MsgReporter::setClause('err_save_item_timestamp_id', array(), true);
			return false;
		}

		// check the timestamp first
		if(!empty($input['timestamp']))
		{
			if(!Util::isTimestamp($input['timestamp']))
			{
				MsgReporter::setClause('err_timestamp', array(), true);
				return false;
			}
		}

		$id = !empty($input['iid']) ? (int) $input['iid'] : null;

		$categoryid = !empty($input['categoryid']) ? (int) $input['categoryid'] : null;

		$this->ProcessCategory();
		// is category valid?
		if(!$this->cp->isCategoryValid($categoryid))
		{
			MsgReporter::setClause('invalid_category', array(), true);
			return false;
		}

		// Initialize items of the passed category
		$ic = $this->getItemMapper();
		$ic->limitedInit($categoryid, $id);

		$curitem = !empty($ic->items[$id]) ? $ic->items[$id] : null;

		// new item
		if(!$curitem) $curitem = new Item($categoryid);

		// Clean up cached images
		$this->cleanUpCachedFiles($curitem);

		// check required item name
		if(empty($input['name']))
		{
			MsgReporter::setClause('err_by_empty_field', array(
					'field' => MsgReporter::getClause('title', array())), true
			);
			$this->delTree(IM_IMAGE_UPLOAD_DIR.'tmp_'.$input['timestamp'].'_'.$categoryid.'/');
			return false;
		}

		// should the item name to be unique
		if($this->config->backend->unique_itemname == 1)
		{
			// check if item name already exist and is not the same item
			$item_by_name = $ic->getItem('name='.str_replace('"', '\'', $input['name']));
			if(!empty($item_by_name) && ($id != $item_by_name->id))
			{
				MsgReporter::setClause('err_item_exists', array('name' => $this->sanitizer->text(
					str_replace('"', '\'', $input['name']))), true);
				return false;
			}
		}

		// check item name length
		if(strlen($input['name']) > $this->config->common->maxitemname)
		{
			MsgReporter::setClause('err_item_name_length', array('count' =>
				intval($this->config->common->maxitemname)));
			$this->delTree(IM_IMAGE_UPLOAD_DIR.'tmp_'.$input['timestamp'].'_'.$categoryid.'/');
			return false;
		}


		/* Ok, the standard procedure is completed, now we want to make the next step
		and loop through the fields of the item and save values */

		$curitem->name = str_replace('"', '\'', $input['name']);
		$curitem->active = isset($input['active']) ? 1 : 0;
		$curitem->position = !empty($input['itempos']) ? (int) $input['itempos'] : null;

		$tmp_image_dir = '';

		foreach($curitem->fields as $fieldname => $fieldvalue)
		{

			$fieldinput = !empty($input[$fieldname]) ? str_replace('"', "&#34;", $input[$fieldname]) : '';

			$inputClassName = 'Input'.ucfirst($fieldvalue->type);
			$InputType = new $inputClassName($curitem->fields->$fieldname);

			// handle our special fields

			// imageupload
			if($fieldvalue->type == 'imageupload' || $fieldvalue->type == 'fileupload')
			{

				// new item
				if(empty($input['iid']) && !empty($input['timestamp']))
				{
					// pass temporary image directory
					$tmp_image_dir = IM_IMAGE_UPLOAD_DIR.'tmp_'.$input['timestamp'].'_'.$categoryid.'/';
					$fieldinput = $tmp_image_dir;
				} else
				{
					// pass image directory
					$fieldinput = IM_IMAGE_UPLOAD_DIR.intval($input['iid']).'.'.$categoryid.'/';
				}

				// position is send
				if(isset($input['position']) && is_array($input['position']))
				{
					$InputType->positions = $input['position'];
					$InputType->titles = isset($input['title']) ? $input['title'] : '';

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
				$InputType->confirm = !empty($input['password_confirm']) ? $input['password_confirm'] : '';
				// refill password field values if empty
				$InputType->password = !empty($curitem->fields->$fieldname->value)
					? $curitem->fields->$fieldname->value : '';
				$InputType->salt = !empty($curitem->fields->$fieldname->salt)
					? $curitem->fields->$fieldname->salt : '';
				$fieldinput = !empty($input[$fieldname]) ? $input[$fieldname] : '';
			}

			$resultinput = $InputType->prepareInput($fieldinput, true);

			if(!isset($resultinput) || empty($resultinput) || is_int($resultinput))
			{
				// parse error
				switch ($resultinput)
				{
					case 1:
						MsgReporter::setClause('err_required_field', array('fieldname' => $fieldvalue->label), true);
						return false;
					case 2:
						MsgReporter::setClause('err_input_min_length', array('fieldname' => $fieldvalue->label,
								'count' => $fieldvalue->minimum), true
						);
						return false;
					case 3:
						MsgReporter::setClause('err_input_max_length', array('fieldname' => $fieldvalue->label,
								'count' => $fieldvalue->maximum), true
						);
						return false;
					case 5:
						MsgReporter::setClause('err_input_incomplete',
							array('fieldname' => $fieldvalue->label), true);
						return false;
					case 7:
						MsgReporter::setClause('err_input_comparison',
							array('fieldname' => $fieldvalue->label), true);
						return false;
					case 8:
						MsgReporter::setClause('err_input_format',
							array('fieldname' => $fieldvalue->label), true);
						return false;
				}

				// todo: error log

			}

			foreach($resultinput as $inputputkey => $inputvalue)
				$curitem->fields->$fieldname->$inputputkey = $inputvalue;
		}


		if(!$curitem->save())
		{
			MsgReporter::setClause('err_save_item', array(), true);
			return false;
		}

		$this->getSectionCache()->expire();
		$this->admin->input['iid'] = $curitem->id;

		/* Congratulation, we have just came through some checkpoints well.
		   Item has been successfully saved, now we still have to take several
		   steps to clean up the system from dated stuff. */

		/* Check if it's a new item as we have not had the standard item-ID
		   and temporary image directory should be renamed */
		if(!empty($tmp_image_dir) && file_exists($tmp_image_dir))
		{
			if(!$this->renameTmpDir($curitem))
			{
				MsgReporter::setClause('err_rename_directory', array('name' => $tmp_image_dir), true);
				return false;
			}
			// clean up the older data
			$this->cleanUpTempContainers('imageupload');
			$this->cleanUpTempContainers('fileupload');
		}

		// useAllocater is activated
		if($this->config->useAllocater == true)
		{
			if($ic->alloc($curitem->categoryid) !== true)
			{
				$ic->init($categoryid);
				if(!empty($ic->items))
				{
					$ic->simplifyBunch($ic->items);
					$ic->save();
				}
			}
			$ic->simplify($curitem);
			$ic->save();
		}

		// delete search index (i18n search)
		$this->deleteSearchIndex();

		MsgReporter::setClause('item_successfully_saved', array('name' => $this->sanitizer->text(
			str_replace('"', '\'', $input['name']))));

		//exec_action('ImAfterItemSave');
		return true;
	}



	public function deleteItem($id, $catid)
	{
		// timestamp or item id required
		if(!is_numeric($id))
		{
			MsgReporter::setClause('err_unknow_itemid', array(), true);
			return false;
		}

		// is current category valid
		$this->ProcessCategory();
		if(!$this->cp->isCategoryValid($catid))
		{
			MsgReporter::setClause('invalid_category', array(), true);
			return false;
		}

		// Initialize items of the current category
		$ic = $this->getItemMapper();
		$ic->limitedInit($catid, $id);

		$item = !empty($ic->items[$id]) ? $ic->items[$id] : null;

		// item does not exist
		if(!$item)
		{
			MsgReporter::setClause('err_item_not_exist', array(), true);
			return false;
		}

		// backup item before delete
		if(intval($this->config->backend->itembackup) == 1)
		{
			if(!$this->config->createBackup(IM_ITEM_DIR, $item->get('id').'.'.$item->get('categoryid'),
				IM_ITEM_FILE_SUFFIX))
			{
				MsgReporter::setClause('err_backup', array('backup' => $this->config->backend->itembackupdir), true);
				return false;
			}
		}

		// get image directory to delete
		$imagedir = IM_IMAGE_UPLOAD_DIR.$item->id.'.'.$item->categoryid;
		// get image name to display
		$itemname = $item->name;

		if(!$ic->destroyItem($item))
		{
			MsgReporter::setClause('err_item_delete', array(), true);
			return false;
		}

		// useAllocater is activated
		if($this->config->useAllocater == true) {
			$ic->deleteSimpleItem($item);
		}

		/* Item has been successfully deleted, now we have to clean up the image uploads */
		$this->delTree($imagedir);
		// delete search index (i18n search)
		$this->deleteSearchIndex();

		// The deletion was successful, show a message
		MsgReporter::setClause('item_deleted', array('item' => $itemname));
		return true;

	}


	public function getSiteUrl()
	{
		$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
			!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
		return
			($https ? 'https://' : 'http://').
			(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
			(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
				($https && $_SERVER['SERVER_PORT'] === 443 ||
				$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT'])));
	}


	public function renameTmpDir($item)
	{
		$err = false;
		foreach($item->fields as $fieldname => $fieldvalue)
		{
			if($fieldvalue->type != 'imageupload' && $fieldvalue->type != 'fileupload')
				continue;

			$inputClassName = 'Input'.ucfirst($fieldvalue->type);
			$InputType = new $inputClassName($item->fields->$fieldname);


			// try to rename file directory
			$newpath = IM_IMAGE_UPLOAD_DIR.$item->id.'.'.$item->categoryid.'/';
			if(!rename($fieldvalue->value, $newpath))
				return false;

			$resultinput = $InputType->prepareInput($newpath);

			if(!isset($resultinput) || empty($resultinput))
				return false;

			foreach($resultinput as $inputputkey => $inputvalue)
				$item->fields->$fieldname->$inputputkey = $inputvalue;
		}

		if($item->save() && !$err) return true;

		return false;
	}


	/**
	 * Delete chached image files that starting with *_filename.* for example
	 *
	 * @param Item $item
	 */
	public function cleanUpCachedFiles(Item $item)
	{
		$fieldinput = IM_IMAGE_UPLOAD_DIR.(int)$item->id.'.'.$item->categoryid.'/';
		if(!file_exists($fieldinput.'config.xml')) {return;}
		$xml = simplexml_load_file($fieldinput.'config.xml');

		foreach(glob($fieldinput.'thumbnail/*_*.*') as $image) {
			$parts = explode('_', basename($image), 2);
			if(empty($parts[1])) continue;
			$chached = false;
			foreach($xml->image as $xmlimage)
			{
				if((string)$xmlimage->name == $parts[1]) {
					$chached = true;
					break;
				}
			}
			if($chached === true) { @unlink($image);}
		}
	}


	public function cleanUpTempContainers($datatyp)
	{
		if($datatyp == 'imageupload' || $datatyp == 'fileupload')
		{
			if(!file_exists(IM_IMAGE_UPLOAD_DIR))
				return false;

			foreach(glob(IM_IMAGE_UPLOAD_DIR.'tmp_*_*') as $file)
			{
				$base = basename($file);
				$strp = explode('_', $base);

				// wrong file name, continue
				if(count($strp) < 3)
					continue;

				if(!$this->cp->isCategoryValid($strp[2]))
					$this->delTree($file);

				$min_days = intval($this->config->backend->min_tmpimage_days);
				$storagetime =  time() - (60 * 60 * 24 * $min_days);

				if($strp[1] < $storagetime && $storagetime > 0)
					$this->delTree($file);
			}
			return true;
		}
	}


	protected function delTree($dir)
	{
		if(!file_exists($dir))
			return false;
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file)
		{
			(is_dir("$dir/$file") && !is_link($dir)) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}
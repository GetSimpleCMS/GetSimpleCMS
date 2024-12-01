<?php
/**
 * ItemManager's Admin Module
 *
 */

class Admin
{
	protected $manager;
	protected $input;
	protected $tpl;
	public $break;

	public function __construct()
	{
		//$this->manager = imanager();
		/*if(!$manager->is_admin_panel) return false;
		$this->manager = $manager;*/
	}

	public function init()
	{
		$this->manager = imanager();
		$this->manager->getCategoryMapper();
		$this->manager->ProcessCategory();
	}

	/*  */
	public function display()
	{
		$this->input = array_merge($_GET, $_POST);
		// there is the basic structure of the backend view
		$o = array('head' => '', 'msg' => '', 'content' => '' );
		// display category selector flag

		// Check if the category selected and make one to current
		if(isset($this->input['reloader']) && isset($this->input['post-category']))
			$this->manager->cp->setCategory($this->input['post-category']);
		elseif(!empty($this->input['catsender']) && !empty($this->input['cat']))
			$this->manager->cp->setCategory($this->input['cat']);

		// Initialize templates now, we'll need them to build our backend
		$this->tpl = $this->manager->getTemplateEngine();
		$this->tpl->init();


		// build tab panel template
		$o['head'] = $this->buildTabpanel();

		// errors already send?
		$msg = MsgReporter::msgs();

		// build edit item menu
		if(isset($this->input['edit']))
		{

			// save item
			if(isset($this->input['submit']) && !empty($this->input['categoryid']))
			{
				if(!empty($this->input['timestamp']) || !empty($this->input['iid']))
				{
					$this->callModelMethod('saveItem', $this->input);
				}
			}
			// show item editor
			if($this->manager->cp->is_cat_exist)
				$o['content'] = $this->buildItemEditor();
		}

		// save category & settings
		elseif (isset($this->input['category_edit']))
		{
			// create new category. true for refresh
			$this->callModelMethod('createCategoryByName', array($this->input['new_category'], true));
			$o['content'] = $this->buildCategoryEditor();
		}
		// show details of category
		elseif (isset($this->input['categorydetails']))
		{
			$o['content'] = $this->buildCategoryDetailsEditor();
		}
		// update_category
		elseif(isset($this->input['categoryupdate']))
		{
			//if($this->manager->updateCategory($this->input, true))
			if($this->callModelMethod('updateCategory', array($this->input, true)))
				$o['content'] = $this->buildCategoryEditor();
			else
				$o['content'] = $this->buildCategoryDetailsEditor();
		}
		// delete category
		elseif (isset($this->input['deletecategory']))
		{
			//$this->manager->deleteCategory($this->input['deletecategory'], true);
			$this->callModelMethod('deleteCategory', array($this->input['deletecategory'], true));
			$o['content'] = $this->buildCategoryEditor();
		}
		// category menu
		elseif (isset($this->input['category']))
		{
			// ajax
			if(isset($this->input['getcatlist']))
			{
				return $this->buildCategoryList();
			}
			$o['content'] = $this->buildCategoryEditor();
		}
		elseif (isset($this->input['settings_edit']))
		{
			// todo: do it dynamically
			$this->manager->config->setupConfig($this->input);
			$o['content'] = $this->buildSettingsSection();
		}
		elseif (isset($this->input['settings']))
		{
			$o['content'] = $this->buildSettingsSection();
		}

		// configure custom fields
		elseif (isset($this->input['fields']))
		{
			if(isset($this->input['save']))
			{
				$this->callModelMethod('createFields', $this->input);
			}

			if(isset($this->input['submit']))
			{
				$this->callModelMethod('saveFieldDetails', $this->input);
			}


			if(isset($this->input['field']) &&
				is_numeric($this->input['field']))
			{
				$o['content'] = $this->buildFieldDetails();
			} else
			{
				$o['content'] = $this->buildFieldEditor();
			}
		}

		// show item list menu
		elseif(Manager::$installed && !$msg)
		{
			// ajax
			if(isset($this->input['getitemlist']))
			{
				return $this->buildItemRows();
			}
			// delete item
			elseif (isset($this->input['delete']))
			{
				$this->callModelMethod('deleteItem', array($this->input['delete'], $this->manager->cp->currentCategory()));
			}

			$o['content'] = $this->buildItemList();
		}


		$o['msg'] = MsgReporter::buildMsg();

		return $this->buildBackend($o);
	}

	public function &__get($name){return $this->{$name};}


	/* call_user_func_array is really slow and if you are calling a method with a
	known number of parameters it is much faster to call it this way */
	protected function callModelMethod($method, $args)
	{
		//$multiargs = array('deleteItem');
		if($method == 'deleteItem' || $method == 'deleteCategory' || $method == 'updateCategory' ||
			$method == 'createCategoryByName')
		{
			return $this->manager->{$method}($args[0], $args[1]);
		}
		return $this->manager->{$method}($args);
	}

	/**
	 * Displays admin tab panel
	 *
	 * @return Template object
	 */
	protected function buildTabpanel()
	{
		// get temple with the name tabpanel
		$tabpanel = $this->tpl->getTemplate('tabpanel');

		// build template variables
		$tvs = array();

		// initialize the current css class to the tab-panel buttons
		$labels = array('settings', 'fields', 'category', 'edit', 'view');
		$activekey = '';
		if(!empty($this->input))
		{
			foreach($this->input as $key => $val)
			{
				if(in_array($key, $labels))
				{
					$tvs[$key] = 'class="current"';
					$activekey = $key;
				}
			}
		}
		if(empty($activekey)) $tvs['view'] = 'class="current"';

		// initialize ItemManager title
		$tvs['itemmanager-title'] = IMTITLE;

		// ok, all tvs were initialized – render template and return it to controller
		return $this->tpl->render($tabpanel, $tvs, true, array(), true);
	}


	protected function buildCategorySelector()
	{
		// get temples of type "catselector" then get the form
		$form = $this->tpl->getTemplate('form', $this->tpl->getTemplates('catselector'));
		// get the copy of the option template
		$option = $this->tpl->getTemplate('option');

		$category = $this->manager->getCategoryMapper();

		$category->categories = $category->filterCategories('position', 'ASC');

		$tvs = '';
		if(!$category->countCategories() || empty($form) || empty($option))
			return false;

		// render option template
		foreach($category->categories as $key => $cat)
		{
			$tag = ($this->manager->cp->currentCategory() == $cat->get('id')) ? 'selected' : '';
			$tploption = $this->tpl->render($option, array('selected' => $tag, 'value' => $cat->get('id'), 'name' => $cat->name));
			$tvs .= $tploption;
		}
		// render category selector form
		return $this->tpl->render($form,  array('options' => $tvs,
			'uri-add' => isset($this->input['fields']) ? '&fields' : ''), true, array(), true);
	}


	protected function buildMsg()
	{
		$o = '';
		$msg = MsgReporter::msgs();
		if(!empty($msg))
			foreach($msg as $val) $o .= $val;
		return $o;
	}


	protected function buildBackend($values)
	{
		// get "backend" temple
		$backend = $this->tpl->getTemplate('backend');
		return $this->tpl->render($backend, $values);
	}


	protected function buildCategoryEditor()
	{
		// get some templates of categorylist bundle
		$categorylist = $this->tpl->getTemplates('categorylist');
		$form = $this->tpl->getTemplate('form', $categorylist);
		$row = $this->tpl->getTemplate('row', $categorylist);
		$filter = $this->tpl->getTemplate('filter', $categorylist);

		$categoryMapper = $this->manager->getCategoryMapper();

		$catcount = $categoryMapper->countCategories($categoryMapper->categories);

		// get settings
		$configs = $this->manager->config;

		if(!$categoryMapper->countCategories())
			return $this->tpl->render($form,  array(
					'value' => ''), true, array(), true
			);

		$order = ($configs->backend->catorder == 'asc') ? 'ASC' : 'DESC';
		$attribut = (isset($configs->backend->catorderby) && (string) $configs->backend->catorderby != 'undefined') ?
			$this->manager->sanitizer->text($configs->backend->catorderby) : 'position';

		$page = !empty($this->input['page']) ? $this->input['page'] : 1;
		$perpage = !empty($this->input['getcatlist']) ? intval($this->input['getcatlist']) :
			(int) $configs->backend->maxcatperpage;

		// start item
		$start = (($page -1) * $perpage +1);

		// filter categories
		$categoryMapper->categories = $categoryMapper->filterCategories($attribut, $order, $start, $perpage);

		//category rows
		$tplrow = '';
		$ic = $this->manager->getItemMapper();
		foreach($categoryMapper->categories as $cat)
		{
			$count = $ic->quickCount($cat->id);

			$tplrow .= $this->tpl->render($row, array(
					'cat-position' => $cat->position,
					'categoryname' => $cat->name,
					'category' => $cat->id, 'count' => $count), true, array()
			);
		}

		// display category filter?
		if((int) $configs->backend->catfilter == 1)
		{
			$filter = $this->tpl->render($filter, array(
					'catnumber' => $configs->backend->maxcatperpage,
					'asc' => ($configs->backend->catorder == 'asc') ? 'selected' : '',
					'desc' => ($configs->backend->catorder == 'desc') ? 'selected' : '',
					'position' => ($attribut == 'position') ? 'selected' : '',
					'name' => ($attribut == 'name') ? 'selected' : '',
					'created' => ($attribut == 'created') ? 'selected' : '',
					'updated' => ($attribut == 'updated') ? 'selected' : '',
					'nswitch' => $this->buildNumberSwitch('category')
				), true, array()
			);
		}

		$params['pageurl'] = 'load.php?id=imanager&category&page=';

		$pagination = $categoryMapper->pagination($params);

		return $this->tpl->render($form,  array(
				'filter' => ((int) $configs->backend->catfilter == 1) ? $filter : '',
				'page' => $page,
				'value' => $tplrow,
				'pagination' => $pagination), true, array(), true
		);
	}


	// Ajax stuff
	protected function buildCategoryList()
	{
		$categorylist = $this->tpl->getTemplates('categorylist');
		$row = $this->tpl->getTemplate('row', $categorylist);

		$category = $this->manager->getCategoryMapper();
		if(!$category->countCategories())
			return false;

		// get config
		$config = $this->manager->config;

		$option = !empty($this->input['option']) ? $this->manager->sanitizer->text($this->input['option']) : 'ASC';

		$filterby = (isset($this->input['filterby']) && (string) $this->input['filterby'] != 'undefined') ?
			$this->manager->sanitizer->text(strtolower($this->input['filterby'])) : 'position';

		$perpage = !empty($this->input['getcatlist']) ? intval($this->input['getcatlist']) : $config->backend->maxcatperpage;
		$page = !empty($this->input['page']) ? $this->input['page'] : 1;

		// change config properties
		$config->backend->maxcatperpage = $perpage;
		$config->backend->catorderby = $filterby;
		$config->backend->catorder = $option;
		$config->save();

		// start item
		$start = (($page -1) * $perpage +1);

		// filter category
		$category->categories = $category->filterCategories($filterby, $option, $start, $perpage);


		if(empty($category->categories))
			return false;


		if(!empty($this->input['positions']))
		{
			foreach($this->input['positions'] as $element)
			{
				if(!isset($category->categories[$element['id']]->position) || empty($element['position']))
					continue;
				if((int)$category->categories[$element['id']]->position != $element['position'])
				{
					$category->categories[$element['id']]->position = $element['position'];
					$category->categories[$element['id']]->save();
				}
			}
			// refilter output
			$category->categories = $category->filterCategories($filterby, $option, $start, $perpage, $category->categories);
		}

		//category rows
		$tplrow = '';
		$ic = $this->manager->getItemMapper();
		foreach($category->categories as $cat)
		{
			$count = $ic->quickCount($cat->id);
			$tplrow .= $this->tpl->render($row, array(
				'cat-position' => $cat->position,
				'categoryname' => $cat->name,
				'category' => $cat->id, 'count' => $count), true, array());
		}

		return $tplrow;

	}


	protected function buildSettingsSection()
	{
		$settings = $this->tpl->getTemplates('settings');
		$form = $this->tpl->getTemplate('form', $settings);

		// get settings
		$configs = $this->manager->config;

		// category
		$attribut = isset($configs->backend->catorderby) ?
			$this->manager->sanitizer->text($configs->backend->catorderby) : 'position';

		// item
		$i_attribut = isset($configs->backend->itemorderby) ?
			$this->manager->sanitizer->text($configs->backend->itemorderby) : 'i_position';

		return $this->tpl->render($form,  array('maxcatname' => $configs->common->maxcatname,
				'maxfieldname' => $configs->common->maxfieldname,
				'maxitemname' => $configs->common->maxitemname,
				'catbackup' => ($configs->backend->catbackup == 1) ? ' checked ' : '',
				'fieldbackup' => ($configs->backend->fieldbackup == 1) ? ' checked ' : '',
				'catbackupdir' => $configs->backend->catbackupdir,
				'min_catbackup_days' => (intval($configs->backend->min_catbackup_days) > 0)
						? intval($configs->backend->min_catbackup_days) : 0,
				'fieldbackupdir' => $configs->backend->fieldbackupdir,
				'min_fieldbackup_days' => (intval($configs->backend->min_fieldbackup_days) > 0)
						? intval($configs->backend->min_fieldbackup_days) : 0,
				'i18nsearch' => ($configs->common->i18nsearch == 1) ? ' checked ' : '',
				'i18nsearch_field' => $configs->common->i18nsearchfield,
				'exclude_categories' => $configs->common->i18nsearchexcludes,
				'i18nsearch_url' => $configs->common->i18nsearch_url,
				'i18nsearch_segment' => $configs->common->i18nsearch_segment,
				'i18nsearch_content' => $configs->common->i18nsearch_content,


				'catfilter' => ($configs->backend->catfilter == 1) ? ' checked ' : '',
				'ten' => ($configs->backend->maxcatperpage == 10) ? 'selected' : '',
				'twenty' => ($configs->backend->maxcatperpage == 20) ? 'selected' : '',
				'thirty' => ($configs->backend->maxcatperpage == 30) ? 'selected' : '',
				'forty' => ($configs->backend->maxcatperpage == 40) ? 'selected' : '',
				'fifty' => ($configs->backend->maxcatperpage == 50) ? 'selected' : '',
				'catorder' => $configs->backend->catorder,
				'asc' => ($configs->backend->catorder == 'acs') ? 'selected' : '',
				'desc' => ($configs->backend->catorder == 'desc') ? 'selected' : '',
				'position' => ($attribut == 'position') ? 'selected' : '',
				'name' => ($attribut == 'name') ? 'selected' : '',
				'created' => ($attribut == 'created') ? 'selected' : '',
				'updated' => ($attribut == 'updated') ? 'selected' : '',

				'timeformat' => $configs->backend->timeformat,
				'itemfilter' => ($configs->backend->itemfilter == 1) ? ' checked ' : '',
				'i_ten' => ($configs->backend->maxitemperpage == 10) ? 'selected' : '',
				'i_twenty' => ($configs->backend->maxitemperpage == 20) ? 'selected' : '',
				'i_thirty' => ($configs->backend->maxitemperpage == 30) ? 'selected' : '',
				'i_forty' => ($configs->backend->maxitemperpage == 40) ? 'selected' : '',
				'i_fifty' => ($configs->backend->maxitemperpage == 50) ? 'selected' : '',
				'itemorder' => $configs->backend->itemorder,
				'i_asc' => ($configs->backend->itemorder == 'acs') ? 'selected' : '',
				'i_desc' => ($configs->backend->itemorder == 'desc') ? 'selected' : '',
				'i_position' => ($i_attribut == 'position') ? 'selected' : '',
				'i_name' => ($i_attribut == 'name') ? 'selected' : '',
				'i_created' => ($i_attribut == 'created') ? 'selected' : '',
				'i_updated' => ($i_attribut == 'updated') ? 'selected' : '',
				'i_label' => ($i_attribut == 'label') ? 'selected' : '',
				'i_active' => ($i_attribut == 'active') ? 'selected' : '',

				'itemactive' => ($configs->backend->itemactive == 1) ? ' checked ' : '',
				'uniqueitemname' => ($configs->backend->unique_itemname == 1) ? ' checked ' : '',
				'min_tmpimage_days' => (intval($configs->backend->min_tmpimage_days) > 0) ? intval($configs->backend->min_tmpimage_days) : 0,
				'itembackup' =>  ($configs->backend->itembackup == 1) ? ' checked ' : '',
				'min_itembackup_days' => (intval($configs->backend->min_itembackup_days) > 0) ? intval($configs->backend->min_itembackup_days) : 0,
				'itembackupdir' => $configs->backend->itembackupdir,


			), true, array(), true

		);
	}


	protected function buildCategoryDetailsEditor()
	{
		$settings = $this->tpl->getTemplates('detailscategory');
		$form = $this->tpl->getTemplate('form', $settings);

		// is category id available
		$id = isset($this->input['categorydetails']) ? $this->input['categorydetails'] : false;
		if(!$id) $id = isset($this->input['id']) ? $this->input['id'] : false;
		if(!$id) return false;

		$cat = $this->manager->getCategoryMapper()->getCategory($id);

		if(!$cat)
		{
			MsgReporter::setClause('err_category_id_unknown', array());
			return false;
		}

		// prepare some values
		$id = $cat->get('id');
		$name = !empty($this->input['name']) ? $this->input['name'] : $cat->get('name');
		$slug = !empty($this->input['slug']) ? $this->input['slug'] : $cat->get('slug');
		$position = !empty($this->input['position']) ? $this->input['position'] : $cat->get('position');

		$position = !empty($position) ? $position : $cat->get('id');

		// get settings
		$configs = $this->manager->config;

		return $this->tpl->render($form,  array('catid' => $id,
				'catname' => $name,
				'catslug' => $slug,
				'catposition' => $position,
				'created' => date((string) $configs->backend->timeformat, (int) $cat->get('created')),
				'updated' => ((int) $cat->get('updated') > 0) ?
						date((string) $configs->backend->timeformat, (int) $cat->get('updated')) : '',
				'categoryid' => $id), true, array(), true

		);
	}



	protected function buildFieldEditor()
	{
		// load fied editor templates
		$fields = $this->tpl->getTemplates('fields');
		$form = $this->tpl->getTemplate('form', $fields);
		$row = $this->tpl->getTemplate('row', $fields);
		$filepicker = $this->tpl->getTemplate('filepicker', $fields);
		$js = $this->tpl->getTemplate('js', $fields);
		$link = $this->tpl->getTemplate('link', $fields);
		$details = $this->tpl->getTemplate('details', $fields);

		// check is the current category valid
		if(!$this->manager->cp->isCategoryValid()) return;

		// field file already exist
		$cf = new FieldMapper();

		if(!$cf->fieldsExists($this->manager->cp->currentCategory()))
			if(!$cf->createFields($this->manager->cp->currentCategory()))
				return false;

		/* initialize all the fields of the current category */
		$cf->init($this->manager->cp->currentCategory());

		// order fields by position
		$cf->fields = $cf->filterFields('position', 'ASC');

		// define template buffers
		$tplrow = '';
		$rowbuffer = '';
		$tploptions = null;
		$catselector = null;

		// render category selector template
		$catselector = $this->buildCategorySelector();

		// Ok, there are no fields available for this category, so just do show the hidden stuff
		if(!$cf->fields)
		{
			// render file picker field
			$filepicker = $this->tpl->render($filepicker, array());
			// render row template
			$tplrow .=  $this->tpl->render($row, array('tr-class' => 'hidden',
					'i' => 0,
					'id' => '',
					'key' => '',
					'label' => '',
					'filepicker' => (file_exists(GSPLUGINPATH.'i18n_customfields/browser/filebrowser.php') ?
							$filepicker : ''),
					'area-display' => 'display: none',
					'text-options' => ''), true
			);
			// replace the form placeholders and return
			return $this->tpl->render($form,  array('catselector' => $catselector, 'categorie_items' => $tplrow,
					'cat' => $this->manager->cp->currentCategory()), true, array(), true
			);
		}

		// Hmmm Ok, some fields seems to be there, let's try to display them
		$i = 0;
		foreach($cf->fields as $f) {
			$options = '';
			$isdropdown = false;
			if(isset($f->type) && $f->type == 'dropdown') { $isdropdown = true; }
			if($isdropdown && count($f->options) > 0) {
				foreach ($f->options as $option) { $options .= $option . "\r\n"; }
			}
			// render details link
			$tpldetails = $this->tpl->render($details, array('field-id' => $f->get('id')), true, array());

			// render file picker field
			$filepicker = $this->tpl->render($filepicker, array());

			//$rowbuffer = $this->tpl->render($row, array());
			$tplrow .= $this->tpl->render($row, array(
					'tr-class' => 'sortable',
					'i' => $i,
					'field-details' => $tpldetails,
					'id' => $f->get('id'),
					'key' => isset($f->name) ? $f->name : '',
					'label' => isset($f->label) ? $f->label : '',
					'filepicker' => (file_exists(GSPLUGINPATH.'i18n_customfields/browser/filebrowser.php') ?
							$filepicker : ''),
					'area-display' => !$isdropdown ? 'display:none' : '',
					'text-options' => isset($f->default) ? htmlentities($f->default) : '',
					'selected-'.$f->type => ' selected="selected" ',
					'area-options' => $options), true
			);
		}
		// render hiden stuff
		$tplrow .=  $this->tpl->render($row, array('tr-class' => 'hidden',
				'i' => 0,
				'id' => '',
				'key' => '',
				'label' => '',
				'area-display' => 'display: none',
				'text-options' => ''), true
		);

		// replace the form placeholders
		return $this->tpl->render($form,  array(
				'catselector' => $catselector,
				'categorie_items' => $tplrow,
				'cat' => $this->manager->cp->currentCategory()), true, array(), true
		);
	}


	protected function buildFieldDetails()
	{
		// get fielddetail template templates
		$fielddetails = $this->tpl->getTemplates('fielddetails');
		$form = $this->tpl->getTemplate('form', $fielddetails);

		$cf = new FieldMapper();
		$cf->init($this->manager->cp->currentCategory());
		// get current field by id
		$currfield = $cf->getField(intval($this->input['field']));


		if(!$currfield)
		{
			// ERROR FIELD NOT FOUND
			return false;
		}

		$fieldClassName = 'Field'.ucfirst($currfield->type);
		$FieldType = new $fieldClassName($this->tpl);

		if(!empty($currfield->configs))
		{
			foreach($currfield->configs as $key => $val)
			{
				$FieldType->configs->$key = $val;
			}
		}


		$fieldproperties = $FieldType->getConfigFieldtype();


		$default = '';
		$default_tpl = $this->tpl->getTemplate('default', $fielddetails);
		$default = $this->tpl->render($default_tpl,  array(
			'fielddefault' => !empty($currfield->default) ? htmlentities($currfield->default) : '',
			)
		);

		// replace the form placeholders and return
		return $this->tpl->render($form,  array(
				'field-id' => $currfield->get('id'),
				'field_name' => !empty($currfield->name) ? $currfield->name : '',
				'field_label' => !empty($currfield->label) ? $currfield->label : '',
				'field_type' => !empty($currfield->type) ? $currfield->type : '',
				'default' => $default,

				'fieldinfo' => !empty($currfield->info) ? $currfield->info : '',
				'fieldrequired' => ($currfield->required == 1) ? 'checked' : '',
				'min_field_input' => !empty($currfield->minimum) ? intval($currfield->minimum) : '',
				'max_field_input' => !empty($currfield->maximum) ? intval($currfield->maximum) : '',

				'area_class' => !empty($currfield->areaclass) ? $currfield->areaclass : '',
				'label_class' => !empty($currfield->labelclass) ? $currfield->labelclass : '',
				'field_class' => !empty($currfield->fieldclass) ? $currfield->fieldclass : '',

				'fieldproperties' => !empty($fieldproperties) ? $fieldproperties : '',
				/*'area_css' => !empty($currfield->areacss) ? $currfield->areacss : '',
				'label_css' => !empty($currfield->labelcss) ? $currfield->labelcss : '',
				'field_css' => !empty($currfield->fieldcss) ? $currfield->fieldcss : '',*/
			), true, array()
		);
	}


	protected function buildNumberSwitch($obj)
	{
		// get numberswitch templates
		$nrswitch = $this->tpl->getTemplates('numberswitch');
		$container = $this->tpl->getTemplate('container', $nrswitch);
		$inactive = $this->tpl->getTemplate('inactive', $nrswitch);
		$active = $this->tpl->getTemplate('active', $nrswitch);

		// get settings
		$configs = $this->manager->config;

		// there are the default navigation points
		$defaults = array(10, 20, 30, 40, 50);


		if($obj == 'category')
		{
			// and max per page parameter
			$maxperpage = empty($configs->backend->maxcatperpage) ? 10 : intval($configs->backend->maxcatperpage);

			// noting entered by user, build standard template
			if(in_array($maxperpage, $defaults))
			{
				$tplbuffer[0] = ($maxperpage == $defaults[0]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $maxperpage), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[0]), true, array(), true);

				$tplbuffer[1] = ($maxperpage == $defaults[1]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[1]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[1]), true, array(), true);

				$tplbuffer[2] = ($maxperpage == $defaults[2]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[2]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[2]), true, array(), true);

				$tplbuffer[3] = ($maxperpage == $defaults[3]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[3]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[3]), true, array(), true);

				$tplbuffer[4] = ($maxperpage == $defaults[4]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[4]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[4]), true, array(), true);


				// replaze tvs with real values (<li> tags)
				return $this->tpl->render($container, array(
						'1' => $tplbuffer[0],
						'2' => $tplbuffer[1],
						'3' => $tplbuffer[2],
						'4' => $tplbuffer[3],
						'5' => $tplbuffer[4]
					), true, array(), true
				);
			}
		} elseif($obj == 'item')
		{
			// and max per page parameter
			$maxperpage = empty($configs->backend->maxitemperpage) ? 20 : intval($configs->backend->maxitemperpage);

			// noting entered by user, build standard template
			if(in_array($maxperpage, $defaults))
			{
				$tplbuffer[0] = ($maxperpage == $defaults[0]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $maxperpage), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[0]), true, array(), true);

				$tplbuffer[1] = ($maxperpage == $defaults[1]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[1]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[1]), true, array(), true);

				$tplbuffer[2] = ($maxperpage == $defaults[2]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[2]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[2]), true, array(), true);

				$tplbuffer[3] = ($maxperpage == $defaults[3]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[3]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[3]), true, array(), true);

				$tplbuffer[4] = ($maxperpage == $defaults[4]) ?
					$this->tpl->render($active, array('href' => '#', 'number' => $defaults[4]), true, array(), true) :
					$this->tpl->render($inactive, array('href' => '#', 'number' => $defaults[4]), true, array(), true);


				// replaze tvs with real valies (<li> tags)
				return $this->tpl->render($container, array(
						'1' => $tplbuffer[0],
						'2' => $tplbuffer[1],
						'3' => $tplbuffer[2],
						'4' => $tplbuffer[3],
						'5' => $tplbuffer[4]
					), true, array(), true
				);
			}
		}
	}




	protected function buildItemList()
	{
		// get numberswitch templates
		$itemlist = $this->tpl->getTemplates('itemlist');
		$form = $this->tpl->getTemplate('form', $itemlist);
		$row = $this->tpl->getTemplate('row', $itemlist);
		$filter = $this->tpl->getTemplate('filter', $itemlist);
		$active = $this->tpl->getTemplate('active', $itemlist);
		$inactive = $this->tpl->getTemplate('inactive', $itemlist);
		$noitems = $this->tpl->getTemplate('noitemsrow', $itemlist);
		$filteroptiontpl = $this->tpl->getTemplate('filteroption', $itemlist);

		// get settings
		$configs = $this->manager->config;

		// get categories
		$cat = $this->manager->getCategoryMapper()->categories;
		// check if object exist
		if(!$cat) return false;
		// get curren category object
		$curcatid = $this->manager->cp->currentCategory();
		$curcat = $cat[$curcatid];


		$filterby = !empty($configs->backend->itemorderby) ? $configs->backend->itemorderby : 'position';
		$filteroption = !empty($configs->backend->itemorder) ? $configs->backend->itemorder : 'desc';
		$page = !empty($this->input['page']) ? $this->input['page'] : 1;
		$maxitemperpage = !empty($configs->backend->maxitemperpage) ? $configs->backend->maxitemperpage : 20;

		// start item
		$start = (($page -1) * $maxitemperpage +1);

		// Initialize items of current category
		$ic = $this->manager->getItemMapper();
		$ic->init($curcatid);
		//$ic->quickInit($curcatid, array('id', 'name', 'position', 'created', 'updated'), ($start-1), (($start-1) * $maxitemperpage));

		$count = $ic->quickCount($curcatid);
		//$count = $ic->total;

		// order items
		$ic->filterItems($filterby, $filteroption, $start, $maxitemperpage);
		//$ic->filterItems($filterby, $filteroption);

		// define template buffers
		$catselector = '';
		$lines = '';

		// render category selector template
		$catselector = $this->buildCategorySelector();

		// activate / deactivate items
		if(!empty($this->input['activate']))
		{
			$id = intval($this->input['activate']);
			$item = $ic->getItem($id);
			if($item)
			{
				$item->active = ($item->active == 1) ? 0 : 1;

				// useAllocater is activated
				if($item->save() && $configs->useAllocater == true)
				{
					if($ic->alloc($item->categoryid) !== true)
					{
						$ic->init($item->categoryid);
						if(!empty($ic->items))
						{
							$ic->simplifyBunch($ic->items);
							$ic->save();
						}
					}
					$ic->simplify($item);
					$ic->save();
				}
			}
		}

		// display item filter?
		if((int) $configs->backend->itemfilter == 1)
		{

			// build field options for items of current category
			$fieldoptions = $this->tpl->render($filteroptiontpl, array(
					'fieldname' => 'name',
					'selected' => (!empty($configs->backend->filterbyfield) &&
							(string) $configs->backend->filterbyfield == 'name') ? 'selected' : '',
					'fieldlabel' => 'Item name')
			);

			// initialize fields
			$fc = new FieldMapper();
			if($fc->fieldsExists($curcat->id))
			{
				$fc->init($curcat->id);
				foreach($fc->fields as $fname => $fval)
				{
					$fieldoptions .= $this->tpl->render($filteroptiontpl, array(
							'fieldname' => $fname,
							'selected' => (!empty($configs->backend->filterbyfield) &&
									(string) $configs->backend->filterbyfield == (string) $fname) ? 'selected' : '',
							'fieldlabel' => !empty($fval->label) ? $fval->label : $fname)
					);
				}
			}

			$filter = $this->tpl->render($filter, array(
					'itemnumber' => $maxitemperpage,
					'asc' => ($filteroption == 'asc') ? 'selected' : '',
					'desc' => ($filteroption == 'desc') ? 'selected' : '',
					'position' => ($filterby == 'position') ? 'selected' : '',
					'name' => ($filterby == 'name') ? 'selected' : '',
					'label' => ($filterby == 'label') ? 'selected' : '',
					'active' => ($filterby == 'active') ? 'selected' : '',
					'created' => ($filterby == 'created') ? 'selected' : '',
					'updated' => ($filterby == 'updated') ? 'selected' : '',
					'nswitch' => $this->buildNumberSwitch('item'),
					'fieldoptions' => $fieldoptions,//$fieldoptions,
					'eq' => (!empty($configs->backend->filter) &&
							(string) $configs->backend->filter == 'eq') ? 'selected' : '',
					'geq' => (!empty($configs->backend->filter) &&
							(string) $configs->backend->filter == 'geq') ? 'selected' : '',
					'leq' => (!empty($configs->backend->filter) &&
							(string) $configs->backend->filter == 'leq') ? 'selected' : '',
					'g' => (!empty($configs->backend->filter) &&
							(string) $configs->backend->filter == 'g') ? 'selected' : '',
					'l' => (!empty($configs->backend->filter) &&
							(string) $configs->backend->filter == 'l') ? 'selected' : '',
					'filtervalue' => !empty($configs->backend->filtervalue) ? $configs->backend->filtervalue : ''
				), true, array()
			);
		} else
			$filter = false;

		if(empty($ic->items))
		{
			$lines .= $this->tpl->render($noitems, array(), true);
		} else
		{
			// build item rows
			foreach($ic->items as $itemkey => $itemvalue)
			{
				$lines .= $this->tpl->render($row, array(
					'page' => $page,
					'item-id' => $itemkey,
					'item-position' => !empty($itemvalue->position) ? $itemvalue->position : $itemkey,
					'item-name' => !empty($itemvalue->name) ? $itemvalue->name : '',
					'item-created' =>  !empty($itemvalue->created) ? date((string) $configs->backend->timeformat, (int) $itemvalue->created):'',
					'item-updated' =>  !empty($itemvalue->updated) ? date((string) $configs->backend->timeformat, (int) $itemvalue->updated):'',
					'item-checkuncheck' => ($itemvalue->active == 1) ? $active->content : $inactive->content
				), true);
			}
		}

		// build pagination
		$params['pageurl'] = 'load.php?id=imanager&page=';
		$pagination = $ic->pagination($params);

		return $this->tpl->render($form, array(
				'catselector' => $catselector,
				'page' => $page,
				'itemfilter' => ($filter) ? $filter : '',
				'content' => $lines,
				'count' => $count,
				'category' => $curcat->name,
				'pagination' => $pagination), true, array(), true
		);
	}


	// Ajax stuff
	protected function buildItemRows()
	{
		$itemlist = $this->tpl->getTemplates('itemlist');
		$row = $this->tpl->getTemplate('row', $itemlist);
		$active = $this->tpl->getTemplate('active', $itemlist);
		$inactive = $this->tpl->getTemplate('inactive', $itemlist);

		// get settings
		$configs = $this->manager->config;

		$curcategoryid = $this->manager->cp->currentCategory();

		// Initialize items of current category
		$ic = $this->manager->getItemMapper();//new ImItem();
		$ic->init($curcategoryid);

		$count = $ic->quickCount($curcategoryid);
		//$count = $ic->countItems();

		$sanitizer = $this->manager->sanitizer;

		$filteroption = !empty($this->input['option']) ? $sanitizer->name($this->input['option']) : 'desc';
		$filterby = !empty($this->input['filterby']) ? $sanitizer->text(strtolower($this->input['filterby'])) : 'position';
		$filterbyfield = !empty($this->input['filterbyfield']) ? $sanitizer->text(strtolower($this->input['filterbyfield'])) : '';
		$maxitemperpage = !empty($this->input['getitemlist']) ? (int) $this->input['getitemlist'] : $config->backend->maxitemperpage;
		$filter = !empty($this->input['filter']) ? $sanitizer->text(strtolower($this->input['filter'])) : '';
		$filtervalue = !empty($this->input['filtervalue']) ? $sanitizer->text($this->input['filtervalue']) : '';
		$page = !empty($this->input['page']) ? $this->input['page'] : 1;

		// change config properties
		$configs->backend->maxitemperpage = $maxitemperpage;
		$configs->backend->itemorderby = $filterby;
		$configs->backend->itemorder = $filteroption;
		$configs->backend->filterbyfield = $filterbyfield;
		$configs->backend->filter = $filter;
		$configs->backend->filtervalue = $filtervalue;
		$configs->save();

		// template
		$lines = '';

		// start item
		$start = (($page -1) * $maxitemperpage +1);

		$fc = new FieldMapper();
		if($fc->fieldsExists($curcategoryid))
		{
			$fc->init($curcategoryid);

			if(!empty($configs->backend->filterbyfield) && ($fc->fieldNameExists((string) $configs->backend->filterbyfield)
					|| $configs->backend->filterbyfield == 'name') &&
				!empty($configs->backend->filter) && !empty($configs->backend->filtervalue))
			{
				if($configs->backend->filter == 'eq') $filter = '=';
				elseif($configs->backend->filter == 'geq') $filter = '>=';
				elseif($configs->backend->filter == 'leq') $filter = '<=';
				elseif($configs->backend->filter == 'l') $filter = '<';
				elseif($configs->backend->filter == 'g') $filter = '>';

				$query = $configs->backend->filterbyfield.$filter.$configs->backend->filtervalue;

				$ic->items = $ic->getItems((string)$query, $start, (int) $maxitemperpage);

			}
		}

		// change position of the items
		$ic->filterItems($filterby, $filteroption, $start, $maxitemperpage);

		if(!empty($this->input['positions']) && !empty($ic->items))
		{
			foreach($this->input['positions'] as $element)
			{
				if(!isset($ic->items[$element['id']]->position) || !isset($element['position'])) continue;
				if($ic->items[$element['id']]->position != $element['position'])
				{
					$ic->items[$element['id']]->position = $element['position'];
					if($ic->items[$element['id']]->save() && $configs->useAllocater == true) {
						if($ic->alloc($ic->items[$element['id']]->categoryid) !== true)
						{
							$ic->init($ic->items[$element['id']]->categoryid);
							if(!empty($ic->items))
							{
								$ic->simplifyBunch($ic->items);
								$ic->save();
							}
						}
						$ic->simplify($ic->items[$element['id']]);
						$ic->save();
					}
				}
			}
			// refilter output
			$ic->filterItems($filterby, $filteroption);
		}


		// nothing was found
		if(empty($ic->items))
			return '&nbsp;';

		// build item rows
		foreach($ic->items as $itemkey => $itemvalue)
		{
			$lines .= $this->tpl->render($row, array(
				'page' => $page,
				'item-id' => $itemkey,
				'item-position' => !empty($itemvalue->position) ? $itemvalue->position : $itemkey,
				'item-name' => !empty($itemvalue->name) ? $itemvalue->name : '',
				'item-created' =>  !empty($itemvalue->created)?date((string) $configs->backend->timeformat, (int) $itemvalue->created):'',
				'item-updated' =>  !empty($itemvalue->updated)?date((string) $configs->backend->timeformat, (int) $itemvalue->updated):'',
				'item-checkuncheck' => ($itemvalue->active == 1) ? $active->content : $inactive->content
			), true);
		}

		return $lines;
	}


	protected function buildItemEditor()
	{
		$itemeditor = $this->tpl->getTemplates('itemeditor');
		$form = $this->tpl->getTemplate('form', $itemeditor);
		$fieldarea = $this->tpl->getTemplate('fieldarea', $itemeditor);
		$infotext = $this->tpl->getTemplate('infotext', $itemeditor);
		$required = $this->tpl->getTemplate('required', $itemeditor);

		$id = !empty($this->input['edit']) ? (int)$this->input['edit'] : null;
		if(is_null($id))
			$id = !empty($this->input['iid']) ? (int) $this->input['iid'] : null;
		if(MsgReporter::isError())
			$id = !empty($this->input['iid']) ? (int) $this->input['iid'] : null;

		$categoryid = $this->manager->cp->currentCategory();

		// try to get the current page for our back link
		$backpage = !empty($this->input['page']) ? intval($this->input['page']) : 1;
		$stamp = !empty($this->input['timestamp']) ? $this->input['timestamp'] : time();


		// Initialize items of current category
		$ic = $this->manager->getItemMapper();
		$ic->limitedInit($categoryid, $id);

		$curitem = !empty($ic->items[$id]) ? $ic->items[$id] : null;

		// new item
		if(!$curitem)
		{
			$curitem = new Item($categoryid);
			$active = $this->manager->config->backend->itemactive;
		} else
		{
			$active = $curitem->active;
		}

		// Initialize fields
		$fc = new FieldMapper();
		$fc->init($categoryid);

		$fields = $fc->filterFields('position', 'asc');

		$tplfields = '';
		if($fields)
		{
			foreach($fields as $fieldname => $field)
			{
				// Input
				$inputClassName = 'Input'.ucfirst($field->type);
				$InputType = new $inputClassName($fields[$fieldname]);
				$output = $InputType->prepareOutput();

				// Field
				$fieldClassName = 'Field'.ucfirst($field->type);
				$fieldType = new $fieldClassName($this->tpl);
				$fieldType->name = $fieldname;
				$fieldType->id = $fieldType->name;

				if(!empty($field->configs))
				{
					foreach($field->configs as $key => $val)
					{
						$fieldType->configs->$key = $val;
					}
				}

				// hidden
				if($field->type == 'hidden')
					continue;

				// dropdown
				if($field->type == 'dropdown')
					$fieldType->options = $field->options;

				// image upload
				if($field->type == 'imageupload')
				{
					$fieldType->categoryid = $categoryid;
					$fieldType->itemid = $id;
					$fieldType->realid = $field->id;
					$fieldType->timestamp = $stamp;
				}
				// file upload
				if($field->type == 'fileupload')
				{
					$fieldType->categoryid = $categoryid;
					$fieldType->itemid = $id;
					$fieldType->realid = $field->id;
					$fieldType->timestamp = $stamp;
				}

				foreach($output as $outputkey => $outputvalue)
				{
					$fieldType->class = empty($field->fieldclass) ? $field->type.'-field' : $field->fieldclass;

					if(!empty($field->fieldcss))
						$fieldType->style = $field->fieldcss;

					if(is_array($outputvalue))
					{
						$fieldType->$outputkey = array();
						$counter = 0;
						if(!isset($curitem->fields->$fieldname->$outputkey))
							continue;

						foreach($curitem->fields->$fieldname->$outputkey as $arrkey => $arrval)
						{
							$fieldType->{$outputkey}[] = (string) $curitem->fields->$fieldname->{$outputkey}[$counter];
							$counter++;
						}
					} else
						$fieldType->$outputkey = !empty($curitem->fields->$fieldname->$outputkey) ? (string) $curitem->fields->$fieldname->$outputkey : '';

					if(MsgReporter::isError())
						$fieldType->$outputkey = !empty($this->input[$fieldType->name]) ? $this->input[$fieldType->name] : '';
				}

				// set default field values
				if(empty($fieldType->value) && !empty($field->default))
					$fieldType->value = (string) $field->default;


				$tplinfotext = '';
				if(!empty($field->info))
					$tplinfotext = $this->tpl->render($infotext, array('infotext' => $field->info));

				$tplrequired = '';
				if(!empty($field->required) && $field->required == 1)
					$tplrequired = $this->tpl->render($required, array());


				if($field->type != 'chunk')
				{
					$tplfields .= $this->tpl->render($fieldarea, array(
							'fieldid' =>  $field->name,
							'label' => $field->label,
							'infotext' => $tplinfotext,
							'area-class' => !empty($field->areaclass) ? $field->areaclass : 'fieldarea',
							'area-style' => !empty($field->areacss) ? ' style="'.$field->areacss.'"' : '',
							'label-class' => !empty($field->labelclass) ? $field->labelclass : '',
							'label-style' => !empty($field->labelcss) ? ' style="'.$field->labelcss.'"' : '',
							'required' => $tplrequired,
							'field' => $fieldType->render())
					);
				} else
				{
					$tplfields .= $fieldType->render();
				}
			}
		}

		return $this->tpl->render($form, array(
				'action_edit' => !is_null($id) ? $id : '',
				'action_page' => $backpage,
				'item-id' => $id,
				'position' => !empty($curitem->position) ? $curitem->position : '',
				'checked' => ($active > 0) ? ' checked ' : '',
				'back-page' => $backpage,
				'timestamp' => $stamp,
				'infotext' => '',
				'currentcategory' => $this->manager->cp->currentCategory(),
				'itemname' => !empty($curitem->name) ? $curitem->name : '',
				'custom-fields' => $tplfields,
				'created' => ((int) $curitem->created > 0) ?
						date((string) $this->manager->config->backend->timeformat, (int) $curitem->created) : '',
				'updated' => ($curitem->updated > 0) ?
						date((string) $this->manager->config->backend->timeformat, (int) $curitem->updated) : ''
			), true, array()
		);
	}

}
?>
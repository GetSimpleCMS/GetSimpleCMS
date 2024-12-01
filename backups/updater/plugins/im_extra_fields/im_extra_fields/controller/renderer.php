<?php

class Renderer
{
	/**
	 * @var $processor - The processor instance
	 */
	protected $processor;


	/**
	 * Initialize some instances that we work with
	 *
	 * @param $processor - Instance of Processor class
	 */
	public function init(Processor $processor)
	{
		include(dirname(__DIR__).'/tpl/templates.php');
		$this->imanager = imanager();
		$this->processor = $processor;
		$this->processor->init();
	}


	/**
	 * Renders the category selector
	 *    <select name="...">
	 *        ...
	 *    </select>
	 *
	 *  @return string
	 */
	public function renderHeaderSelector()
	{
		return $this->imanager->getTemplateEngine()->render($this->editorFilter, array(
				'options' => $this->renderCategoryOptions()
			)
		);
	}


	/**
	 * Renders page edit body the image fields wrapper
	 * <div id="itemContent" class="manager-wrapper">...</div>
	 *
	 * @return mixed
	 */
	public function renderBody()
	{
		$output = json_decode($this->renderItemFields());
		return $this->imanager->getTemplateEngine()->render($this->editorBody, array(
				'inputs' => (!empty($output->status) ? $output->output : '')
			)
		);
	}


	/**
	 * Renders the category selector options
	 *     <option value="...">...</option>
	 *     ...
	 * @return string
	 */
	protected function renderCategoryOptions()
	{
		$categories = $this->imanager->getCategoryMapper()->categories;
		$tplEngine = $this->imanager->getTemplateEngine();
		$output = '';

		if(!empty($categories))
		{
			$output .= $tplEngine->render($this->selectOption, array(
					'value' => -1,
					'selected' => '',
					'label' => ''
				)
			);
			foreach($categories as $category)
			{
				$output .= $tplEngine->render($this->selectOption, array(
					'value' => (int)$category->id,
					'selected' => ((isset($this->processor->curcat->id) && $this->processor->curcat->id == $category->id) ? ' selected="selected"' : ''),
					'label' => $this->imanager->sanitizer->text($category->name)
					)
				);
			}
		}
		return $output;
	}


	/**
	 * This method renders item fields within page edit menu
	 *
	 * @return string
	 */
	public function renderItemFields()
	{
		$this->tpl = $this->imanager->getTemplateEngine();

		if(isset($_POST['epcatid']) && $_POST['epcatid'] < 0) {
			return json_encode(array('output' => $this->tpl->render($this->deleteInput), 'status' => false));
		} elseif(empty($this->processor->curcat->id)) {
			return json_encode(array('output' => '', 'status' => false));
		}

		$this->tpl->init();

		// Get the item fields templates
		$itemeditor = $this->tpl->getTemplates('itemeditor');
		$fieldarea = $this->tpl->getTemplate('fieldarea', $itemeditor);
		$infotext = $this->tpl->getTemplate('infotext', $itemeditor);
		$required = $this->tpl->getTemplate('required', $itemeditor);

		$timestamp = !empty($_GET['timestamp']) ? (int)$_GET['timestamp'] : time();

		// Initialize fields
		$fc = new FieldMapper();
		$fc->init($this->processor->curcat->id);
		$fields = $fc->filterFields('position', 'asc');

		$tplfields = 'The are no fields for this category';
		if($fields)
		{
			$tplfields = '';
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

				// Hidden
				if($field->type == 'hidden')
					continue;
				// Dropdown
				if($field->type == 'dropdown')
					$fieldType->options = $field->options;
				// Image upload
				if($field->type == 'imageupload')
				{
					$fieldType->categoryid = $this->processor->curcat->id;
					$fieldType->itemid = $this->processor->curitem->id;
					$fieldType->realid = $field->id;
					$fieldType->timestamp = $timestamp;
				}
				// file upload
				if($field->type == 'fileupload')
				{
					$fieldType->categoryid = $this->processor->curcat->id;
					$fieldType->itemid = $this->processor->curitem->id;
					$fieldType->realid = $field->id;
					$fieldType->timestamp = $timestamp;
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
						if(!isset($this->processor->curitem->fields->$fieldname->$outputkey))
							continue;

						foreach($this->processor->curitem->fields->$fieldname->$outputkey as $arrkey => $arrval)
						{
							$fieldType->{$outputkey}[] = (string) $this->processor->curitem->fields->$fieldname->{$outputkey}[$counter];
							$counter++;
						}
					} else
						$fieldType->$outputkey = !empty($this->processor->curitem->fields->$fieldname->$outputkey) ? (string)$this->processor->curitem->fields->$fieldname->$outputkey : '';

					if(MsgReporter::isError())
						$fieldType->$outputkey = !empty($_POST[$fieldType->name]) ? $_POST[$fieldType->name] : '';
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

		$imlink = '';
		if(!empty($this->processor->curitem->id) && !empty($this->processor->curcat->id))
		{
			$imlink = $this->tpl->render($this->itemManagerLink, array('link' =>
				'load.php?id=imanager&edit='.$this->processor->curitem->id.'&catsender=1&cat='.$this->processor->curcat->id));
		}

		$output = $this->tpl->render($this->inputsArounder, array(
				'fields' => $tplfields,
				'imlink' => $imlink,
				'timestamp' => $timestamp,
				'itemid'  => $this->processor->curitem->id,
				'categoryid'  => $this->processor->curcat->id
			), true, array()
		);
		return json_encode(array('output' => $output, 'status' => 1));
	}

}
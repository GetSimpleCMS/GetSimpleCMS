<?php

class FieldFileupload extends FieldText implements FieldInterface
{

	public $realid = null;

	public $categoryid = null;

	public $itemid = null;

	public $timestamp = null;

	/**
	 * FieldFileupload constructor.
	 *
	 * @param TemplateEngine $tpl
	 */
	public function __construct(TemplateEngine $tpl) {
		parent::__construct($tpl);
		$this->tpl = & $tpl;
	}

	/**
	 * Renders the field markup
	 *
	 * @param bool $sanitize
	 *
	 * @return bool|Template
	 */
	public function render($sanitize = false)
	{
		if(is_null($this->name)) { return false; }

		$itemeditor = $this->tpl->getTemplates('field');
		$field = $this->tpl->getTemplate('fileupload', $itemeditor);

		$output = $this->tpl->render($field, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'value' => $this->value,
				'scriptdir' => IM_SITE_URL,
				'item-id' => $this->itemid,
				'currentcategory' => $this->categoryid,
				'field' => $this->realid,
				'timestamp' => $this->timestamp,
			), true, array()
		);

		return $output;
	}

	/**
	 * Render config menu
	 */
	public function getConfigFieldtype()
	{
		$tpltext = $this->tpl->getTemplate('text', $this->tpl->getTemplates('field'));
		$tplinfotext = $this->tpl->getTemplate('infotext', $this->tpl->getTemplates('itemeditor'));
		$tplarea = $this->tpl->getTemplate('fieldarea', $this->tpl->getTemplates('itemeditor'));

		// Accepted file types
		$accept_types = isset($this->configs->accept_types) ? $this->configs->accept_types : '';

		// Render textfied <input name="[[name]]" type="text" class="[[class]]" id="[[id]]" value="[[value]]">
		$textfied = $this->tpl->render($tpltext, array(
				// NOTE: The PREFIX must always be used as a part of the field name
				'name' => self::PREFIX . 'accept_types',
				'class' => '',
				'id' => '',
				'value' => $accept_types
			)
		);

		// render infotext template <p class="field-info">[[infotext]]</p>
		$infotext = $this->tpl->render($tplinfotext, array(
				'infotext' => '<i class="fa fa-info-circle"></i>
					Accepted file types separated by pipe, example: gif|jpe?g|png|pdf')
		);

		// Return merged template parts
		return $this->tpl->render($tplarea, array(
				'fieldid' =>  '',
				'label' => 'Enter accepted file types here',
				'infotext' => $infotext,
				'area-class' => 'fieldarea',
				'label-class' => '',
				'required' => '',
				'field' => $textfied), true
		);
	}
}
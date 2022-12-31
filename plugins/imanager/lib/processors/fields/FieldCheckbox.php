<?php

class FieldCheckbox extends FieldText implements FieldInterface
{
	/**
	 * FieldCheckbox constructor.
	 *
	 * @param TemplateEngine $tpl
	 */
	public function __construct(TemplateEngine $tpl) {
		parent::__construct($tpl);
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
		$textfield = $this->tpl->getTemplate('checkbox', $itemeditor);
		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'value' => 1,
				'checked' => (!empty($this->value) && $this->value > 0) ? 'checked' : ''
			),
			true, array()
		);
		return $output;
	}

	/**
	 * Make the field configurable
	 */
	public function getConfigFieldtype(){}
}
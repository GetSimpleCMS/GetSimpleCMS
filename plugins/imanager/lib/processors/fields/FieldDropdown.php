<?php

class FieldDropdown extends FieldText implements FieldInterface
{
	/**
	 * @var array - Dropdown options
	 */
	public $options = array();

	/**
	 * FieldDropdown constructor.
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
		$select = $this->tpl->getTemplate('select', $itemeditor);
		$tploption = $this->tpl->getTemplate('option', $itemeditor);

		$tplbuffer = '';
		if(is_array($this->options)) {
			foreach($this->options as $option) {
				$tplbuffer .= $this->tpl->render($tploption, array(
					'option' => ($sanitize) ? $this->sanitize($option) : $option,
					'selected' => (!empty($this->value) && ($option == $this->value)) ? 'selected' : ''
					), true
				);
			}
		}

		return $this->tpl->render($select, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'options' => $tplbuffer
			),
			true, array()
		);
	}

	/**
	 * Configurable settings
	 */
	public function getConfigFieldtype(){}
}
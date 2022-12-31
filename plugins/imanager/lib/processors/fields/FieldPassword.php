<?php

class FieldPassword extends FieldText implements FieldInterface
{
	/**
	 * FieldPassword constructor.
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
		$field = $this->tpl->getTemplate('password', $itemeditor);

		$names = array($this->name, 'password_confirm');
		$labels = array('[[lang/password_field]]', '[[lang/password_confirm_field]]');
		$label_classes = array('label-left', 'label-right');

		$fields = '';
		for($i = 0; $i < 2; $i++) {
			$fields .= $this->tpl->render($field, array(
				'label' => !empty($labels[$i]) ? $labels[$i] : '',
				'labelclass' => !empty($label_classes[$i]) ? $label_classes[$i] : '',
				'name' => $names[$i],
				'class' => $this->class,
				'value' => ''), true, array()
			);
		}
		return $this->tpl->render($fields, array($fields), true, array(), true);
	}

	/**
	 * Make the field configurable
	 */
	public function getConfigFieldtype(){}
}
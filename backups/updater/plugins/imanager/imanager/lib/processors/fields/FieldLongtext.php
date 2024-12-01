<?php

class FieldLongtext extends FieldText implements FieldInterface
{
	/**
	 * @var int
	 * MEDIUMTEXT 16,777,215 bytes ~16MB
	 */
	protected $maxLen = 16777215;

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
		$textfield = $this->tpl->getTemplate('longtext', $itemeditor);
		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'value' => ($sanitize) ? $this->sanitize($this->value) : $this->value), true, array()
		);
		return $output;
	}

	/**
	 * This method used for sanitizing output
	 *
	 */
	protected function sanitize($value) {
		return imanager('sanitizer')->textarea($value, array('maxLength' => $this->maxLen));
	}

	/**
	 * Make the field configurable
	 */
	public function getConfigFieldtype(){}
}
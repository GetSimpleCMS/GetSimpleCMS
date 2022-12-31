<?php

class FieldChunk extends FieldLongtext implements FieldInterface
{
	/**
	 * FieldChunk constructor.
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

		$output = $this->tpl->render($this->value, array(
			'value' => $this->sanitize($this->value)), true, array()
		);
		return $output;
	}

	/**
	 * This method used for sanitizing output
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function sanitize($value) { return base64_encode(mb_substr($value, 0, $this->maxLen)); }

	/**
	 * Make the field configurable
	 */
	public function getConfigFieldtype(){}
}
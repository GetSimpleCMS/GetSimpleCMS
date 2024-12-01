<?php
class FieldSlug extends FieldText implements FieldInterface
{
	/**
	 * @var int - Max length of the output
	 */
	protected $maxLen = 255;

	/**
	 * FieldSlug constructor.
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
	public function render($sanitize = false) {
		return parent::render(true);
	}

	/**
	 * This is the method used for sanitizing.
	 * ItemManager' Sanitizer method "pageName" will be used for this.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function sanitize($value){ return imanager('sanitizer')->pageName($value); }

	/**
	 * Configurable settings
	 */
	public function getConfigFieldtype(){}
}
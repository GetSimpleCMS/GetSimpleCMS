<?php

class FieldText implements FieldInterface
{
	/**
	 * @var array
	 */
	public $properties;

	/**
	 * @var TemplateEngine
	 */
	protected $tpl;

	/**
	 * @var null
	 */
	public $name = null;

	/**
	 * @var null
	 */
	public $class = null;

	/**
	 * @var null
	 */
	public $id = null;

	/**
	 * @var null
	 */
	public $value = null;

	/**
	 * @var null
	 */
	public $style = null;

	/**
	 * @var int
	 * TEXT 65,535 bytes ~64kb
	 */
	protected $maxLen = 65535;

	/**
	 * FieldText constructor.
	 *
	 * @param TemplateEngine $tpl
	 */
	public function __construct(TemplateEngine $tpl)
	{
		$this->tpl = $tpl;
		$this->configs = new \stdClass();
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
		$textfield = $this->tpl->getTemplate('text', $itemeditor);
		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'style' => !empty($this->style) ? ' style="'.$this->style.'" ' : '',
				'id' => $this->id,
				'value' => ($sanitize) ? $this->sanitize($this->value) : $this->value), true, array()
		);
		return $output;
	}

	/**
	 * This method renders the field value when you insert it into:
	 * <input value="<value>" ...
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function sanitize($value) {
		return imanager('sanitizer')->text($value, array('maxLength' => $this->maxLen));
	}

	/**
	 * Configurable settings
	 */
	public function getConfigFieldtype(){}
}
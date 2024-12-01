<?php
class FieldHidden implements FieldInterface
{
	public $properties;
	protected $tpl;

	public function __construct(TemplateEngine $tpl)
	{
		$this->tpl = $tpl;
		$this->name = null;
		$this->class = null;
		$this->id = null;
		$this->value = null;
		$this->style = null;
		$this->configs = new stdClass();
	}


	public function render($sanitize = false)
	{
		return '';
	}
	public function getConfigFieldtype(){}
}
<?php
class FieldCheckbox implements FieldInterface
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
		$this->configs = new stdClass();
	}


	public function render($sanitize=false)
	{
		if(is_null($this->name))
			return false;

		$itemeditor = $this->tpl->getTemplates('field');
		$textfield = $this->tpl->getTemplate('checkbox', $itemeditor);
		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'value' => 1,
				'checked' => (!empty($this->value) && $this->value > 0) ? 'checked' : ''), true, array()
		);
		return $output;
	}

	public function getConfigFieldtype(){}
}
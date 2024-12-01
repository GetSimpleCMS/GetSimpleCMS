<?php
class FieldDropdown implements FieldInterface
{
	public $properties;
	protected $tpl;

	public function __construct(TemplateEngine $tpl)
	{
		$this->tpl = $tpl;
		$this->name = null;
		$this->class = null;
		$this->id = null;
		$this->options = array();
		$this->value = null;
		$this->configs = new stdClass();
	}


	public function render($sanitize=false)
	{
		if(is_null($this->name))
			return false;

		$itemeditor = $this->tpl->getTemplates('field');
		$select = $this->tpl->getTemplate('select', $itemeditor);
		$tploption = $this->tpl->getTemplate('option', $itemeditor);

		$tplbuffer = '';
		if(is_array($this->options))
		{
			foreach($this->options as $option)
			{
				$tplbuffer .= $this->tpl->render($tploption, array(
					'option' => !empty($sanitize) ? $this->sanitize($option) : $option,
					'selected' => (!empty($this->value) && ($option == $this->value)) ? 'selected' : ''
					), true
				);
			}
		}

		return $this->tpl->render($select, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'options' => $tplbuffer), true, array()
		);
	}
	protected function sanitize($value){return imanager('sanitizer')->text($value);}

	public function getConfigFieldtype(){}
}
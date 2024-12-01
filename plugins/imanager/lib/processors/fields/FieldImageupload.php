<?php
class FieldImageupload implements FieldInterface
{
	public $properties;
	protected $tpl;

	public function __construct(TemplateEngine $tpl)
	{
		$this->tpl = $tpl;
		$this->name = null;
		$this->class = null;
		$this->id = null;
		$this->realid = null;
		$this->value = null;
		$this->categoryid = null;
		$this->itemid = null;
		$this->timestamp = null;
		$this->configs = new stdClass();
	}


	public function render($sanitize=false)
	{
		if(is_null($this->name))
			return false;

		$itemeditor = $this->tpl->getTemplates('field');
		$field = $this->tpl->getTemplate('imageupload', $itemeditor);

		$output = $this->tpl->render($field, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'value' => $this->value,
				'scriptdir' => IM_SITE_URL,
				'item-id' => $this->itemid,
				'currentcategory' => $this->categoryid,
				'field' => $this->realid,
				'timestamp' => $this->timestamp,
			), true, array()
		);
		return $output;
	}

	public function getConfigFieldtype(){}
}
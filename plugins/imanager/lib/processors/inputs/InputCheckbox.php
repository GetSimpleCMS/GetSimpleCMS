<?php
class InputCheckbox implements InputInterface
{
	protected $values;
	protected $field;

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->values = new stdClass();
		$this->values->value = null;
	}

	public function prepareInput($value, $sanitize=false)
	{
		$this->values->value = ($value > 0) ? 1 : 0;
		return $this->values;
	}

	public function prepareOutput(){return $this->values;}

	protected function sanitize($value){return imanager('sanitizer')->text($value);}
}
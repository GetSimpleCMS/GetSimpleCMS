<?php
class InputDropdown implements InputInterface
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
		$this->values->value = empty($sanitize) ? $value : $this->sanitize($value);
		// check input required
		if(!empty($this->field->required) && $this->field->required == 1)
		{

			if(empty($this->values->value))
				return self::ERR_REQUIRED;
		}

		return $this->values;
	}

	public function prepareOutput(){return $this->values;}

	protected function sanitize($value){return imanager('sanitizer')->text($value);}
}
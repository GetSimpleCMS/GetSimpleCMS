<?php
class InputHidden implements InputInterface
{
	protected $values;
	protected $field;

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->values = new stdClass();
		$this->values->value = null;
	}

	/* Kontrolliert den Input beim speichern des Wertes  */
	public function prepareInput($value, $sanitize=false)
	{
		$this->values->value = empty($sanitize) ? $value : $this->sanitize($value);
		return $this->values;
	}

	public function prepareOutput(){return $this->values;}

	protected function sanitize($value){return imanager('sanitizer')->text($value);}
}
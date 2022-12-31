<?php

class InputCheckbox extends InputText implements InputInterface
{
	/**
	 * InputCheckbox constructor.
	 *
	 * @param Field $field
	 */
	public function __construct(Field $field) {
		parent::__construct($field);
	}

	/**
	 * This method checks the field inputs and sets the field contents.
	 * If an error occurs, the method returns an error code.
	 *
	 * @param $value
	 * @param bool $sanitize
	 *
	 * @return int|stdClass
	 */
	public function prepareInput($value, $sanitize = false)
	{
		// Set empty value, the input isn't required
		if(empty($value) && !$this->required) {
			$this->values->value = 0;
			return $this->values;
		}

		// Check input required
		if(($this->required) && empty($value)) {
			return self::ERR_REQUIRED;
		}

		$this->values->value = ($value > 0) ? 1 : 0;
		return $this->values;
	}

	/**
	 * The method that is called when initiating item content
	 * and is relevant for setting the field content.
	 * However, since we do not require any special formatting
	 * of the output, we can accept the value 1 to 1 here.
	 *
	 * @return stdClass
	 */
	public function prepareOutput(){ return $this->values; }
}
<?php

class InputText implements InputInterface
{
	/**
	 * @var stdClass - The vield value object
	 */
	protected $values;

	/**
	 * @var Field object
	 */
	protected $field;

	/**
	 * @var int
	 * TEXT 65,535 bytes ~64kb
	 */
	protected $maxLen = 65535;

	/**
	 * @var int - default value, if it wasn't defined in field settings menu
	 */
	protected $minLen = 0;

	/**
	 * @var bool - default value if it wasn't defined in field settings menu
	 */
	protected $required = false;

	/**
	 * InputText constructor.
	 *
	 * @param Field $field
	 */
	public function __construct(Field $field)
	{
		/**
		 * Set the field object
		 */
		$this->field = $field;

		/**
		 * Init field value and set it to null
		 */
		$this->values = new \stdClass();
		$this->values->value = null;

		/**
		 * Set local config values if these was set in the field settings (IM-Menu)
		 */
		if($this->field->required) {
			$this->required = true;
		}

		if($this->field->minimum) {
			$this->minLen = $this->field->minimum;
		}

		if($this->field->maximum) {
			$this->maxLen = $this->field->maximum;
		}
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
			$this->values->value = '';
			return $this->values;
		}

		// Check input required
		if(($this->required) && empty($value)) {
			return self::ERR_REQUIRED;
		}

		// Sanitize input
		if($sanitize) {
			$this->values->value = $this->sanitize($value);
		} else {
			$this->values->value = $value;
		}

		// Sanitizer has wiped the value?
		if(!$this->values->value) { return self::ERR_FORMAT; }

		// Check min value length
		if($this->minLen > 0) {
			if(mb_strlen($this->values->value) < (int) $this->minLen) { return self::ERR_MIN_VALUE; }
		}

		// Check max value length
		if($this->maxLen > 0) {
			if(mb_strlen($this->values->value) > (int) $this->maxLen) { return self::ERR_MAX_VALUE; }
		}

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
	public function prepareOutput() { return $this->values; }

	/**
	 * This is the method used for sanitizing.
	 * ItemManager' Sanitizer method "text" will be used for this.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function sanitize($value)
	{
		return imanager('sanitizer')->text($value,
			array('maxLength' => $this->maxLen)
		);
	}
}
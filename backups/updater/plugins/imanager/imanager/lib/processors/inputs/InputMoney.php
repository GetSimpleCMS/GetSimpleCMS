<?php

class InputMoney extends InputText implements InputInterface
{
	/**
	 * @var int
	 */
	protected $maxLen = 255;

	/**
	 * InputMoney constructor.
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
			$this->values->value = '';
			return $this->values;
		}

		// Check input required
		if(($this->required) && empty($value)) {
			return self::ERR_REQUIRED;
		}

		// Only numbers and thousand separators are permitted
		if(!preg_match('/^[0-9\., ]+$/', $value)) {
			return self::ERR_FORMAT;
		}

		// Change value into float format
		$this->values->value = self::toDecimal($value);

		// String format has wiped the value?
		if($this->values->value !== (float) '0.00' && !$this->values->value) { return self::ERR_FORMAT; }

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
	 * The method used for sanitizing.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function toDecimal($money)
	{
		$cleanString = preg_replace('/([^0-9\.,])/i', '', $money);
		$onlyNumbersString = preg_replace('/([^0-9])/i', '', $money);
		$separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;
		$stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
		$removedThousendSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '',  $stringWithCommaOrDot);
		return (float) str_replace(',', '.', $removedThousendSeparator);
	}
}

<?php
class InputMoney implements InputInterface
{
	protected $values;
	protected $field;
	protected $error = array();

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->values = new stdClass();
		$this->values->value = null;
	}

	/* */
	public function prepareInput($value, $sanitize=false)
	{
		// check value, only numbers and thousand separators are permitted
		if(!preg_match('/^[0-9\., ]+$/', $value))
		{
			return self::ERR_FORMAT;
		}

		// let's change value into float format
		$this->values->value = $this->toFloat($value);

		// check input required
		if(!empty($this->field->required) && $this->field->required == 1)
		{
			if(empty($this->values->value))
				return self::ERR_REQUIRED;
		}
		// check min value
		if(!empty($this->field->minimum) && $this->field->minimum > 0)
		{
			if(strlen($this->values->value) < intval($this->field->minimum))
				return self::ERR_MIN_VALUE;
		}
		// check input max value
		if(!empty($this->field->maximum) && $this->field->maximum > 0)
		{
			if(strlen($this->values->value) > intval($this->field->maximum))
				return self::ERR_MAX_VALUE;
		}
		return $this->values;
	}


	public function prepareOutput(){return $this->values;}


	protected function toFloat($str)
	{
		if(strstr($str, ","))
		{
			// replace dots and spaces (thousand seps) with blancs
			$str = str_replace('.', '', $str);
			$str = str_replace(' ', '', $str);
			// replace ',' with '.'
			$str = str_replace(',', '.', $str);
		}

		// search for number that may contain '.'
		if(preg_match('#([0-9\.]+)#', $str, $match))
		{
			return floatval($match[0]);
		} else
		{
			return floatval($str);
		}
	}
}
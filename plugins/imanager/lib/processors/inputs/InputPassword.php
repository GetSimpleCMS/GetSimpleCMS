<?php
class InputPassword implements InputInterface
{
	protected $values;
	protected $field;

	public function __construct(Field $field)
	{
		$this->field = $field;
		$this->values = new stdClass();
		$this->values->value = null;
		$this->confirm = null;
		$this->password = '';
		$this->salt = '';
		$this->values->salt = null;
	}

	public function prepareInput($value, $sanitize=false)
	{
		$value = trim($value);
		$this->confirm = trim($this->confirm);
		// check input required
		if(!empty($this->field->required) && $this->field->required == 1)
		{
			if(empty($value) || empty($this->confirm))
				return self::ERR_REQUIRED;
		}

		if((!empty($value) && empty($this->confirm)) ||
			(!empty($this->confirm) && empty($value)))
		{
			return self::ERR_INCOMPLETED;

		} elseif (empty($value) && empty($this->confirm))
		{
			$this->values->salt = $this->salt;
			$this->values->value = $this->password;
			return $this->values;
		}

		// check differences
		if($value != $this->confirm)
			return self::ERR_COMPARISON;

		// check min value
		if(!empty($this->field->minimum) && $this->field->minimum > 0)
		{
			if(strlen($value) < intval($this->field->minimum))
				return self::ERR_MIN_VALUE;
		}
		// check input max value
		if(!empty($this->field->maximum) && $this->field->maximum > 0)
		{
			if(strlen($value) > intval($this->field->maximum))
				return self::ERR_MAX_VALUE;
		}

		$this->values->salt = $this->randomString();
		$this->values->value = sha1($value . $this->values->salt);
		$this->field->setProtected('confirmed', true);
		return $this->values;
	}

	public function prepareOutput(){return $this->values;}


	public function checkInput($pass, $confirm){return self::SUCCESS;}


	public function randomString($length = 10)
	{
		$characters = '0123456*789abcdefg$hijk#lmnopqrstuvwxyzABC+EFGHIJKLMNOPQRSTUVW@XYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i = 0; $i < $length; $i++)
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		return $randomString;
	}
}
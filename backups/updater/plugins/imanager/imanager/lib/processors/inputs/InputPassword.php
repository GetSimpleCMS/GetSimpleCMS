<?php

class InputPassword implements InputInterface
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
	 */
	protected $maxLen = 255;

	/**
	 * @var int - default value, if it wasn't defined in field settings menu
	 */
	protected $minLen = 0;

	/**
	 * @var bool - default value if it wasn't defined in field settings menu
	 */
	protected $required = false;

	/**
	 * @var null
	 */
    public $confirm = null;

	/**
	 * @var null
	 */
    public $salt = null;

	/**
	 * @var null
	 */
    public $password = null;

	/**
	 * InputPassword constructor.
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
		$this->values->salt = null;

		/**
		 * Set password & salt to empty string
		 */
		$this->password = '';
		$this->salt = '';

		/**
		 * Set local config values if these are set in the field settings (IM-Menu)
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
		$value = trim($value);
		$this->confirm = trim($this->confirm);

		// Set empty or default value, the input isn't required
		if(empty($value) && empty($this->confirm) && !$this->required) {
			$this->values->salt = $this->salt;
			$this->values->value = $this->password;
			return $this->values;
		}

		// Check input required
		if($this->required && (empty($value) && empty($this->confirm))) {
			return self::ERR_REQUIRED;
		}

		// Input incompleted
		if(empty($value) || empty($this->confirm)) {
			return self::ERR_INCOMPLETED;
		}

		// check differences
		if(strcmp($value, $this->confirm) !== 0) {
			return self::ERR_COMPARISON;
		}

		// Check min value length
		if($this->minLen > 0) {
			if(mb_strlen($value) < (int) $this->minLen) { return self::ERR_MIN_VALUE; }
		}

		// Check max value length
		if($this->maxLen > 0) {
			if(mb_strlen($value) > (int) $this->maxLen) { return self::ERR_MAX_VALUE; }
		}

		// Build salt string
		// Note, since 2.4.4 salt is no longer used, but is still retained for compatibility reasons.
		$this->values->salt = $this->randomString();
		// Create hashed pass
		//$this->values->value = sha1($value . $this->values->salt);
		$this->values->value = password_hash($value, PASSWORD_DEFAULT);
		// Set confirmed flag
		$this->field->setProtected('confirmed', true);

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

	// O_o what is this used for?
	public function checkInput($pass, $confirm){ return self::SUCCESS; }

	/**
	 * Random string generator
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	public function randomString($length = 10)
	{
		$characters = '0123456*789abcdefg$hijk#lmnopqrstuvwxyzABC+EFGHIJKLMNOPQRSTUVW@XYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
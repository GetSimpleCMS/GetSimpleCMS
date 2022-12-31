<?php

class InputLongtext extends InputText implements InputInterface
{
	/**
	 * @var int
	 * MEDIUMTEXT 16,777,215 bytes ~16MB
	 */
	protected $maxLen = 16777215;

	/**
	 * InputLongtext constructor.
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
	public function prepareInput($value, $sanitize = false) {
		return parent::prepareInput($value, $sanitize);
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
	 * ItemManager' Sanitizer method "textarea" will be used for this.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	protected function sanitize($value)
	{
		return imanager('sanitizer')->textarea(
			$value, array('maxLength' => $this->maxLen)
		);
	}
}
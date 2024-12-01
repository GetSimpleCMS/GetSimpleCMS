<?php

class InputEditor extends InputLongtext implements InputInterface
{
	/**
	 * InputEditor constructor.
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
	 * mb_substr() will be used.
	 *
	 * @param $value
	 *
	 * @return string
	 */
	protected function sanitize($value) {
		return mb_substr($value, 0, $this->maxLen);
	}
}
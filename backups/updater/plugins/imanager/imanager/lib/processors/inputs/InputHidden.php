<?php
/**
 * Currently fully inherited from InputText
 */
class InputHidden extends InputText implements InputInterface {

	/**
	 * InputHidden constructor.
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

	public function prepareOutput(){ return $this->values; }
}
<?php

interface InputInterface
{
	const ERR_REQUIRED   = 1;
	const ERR_MIN_VALUE  = 2;
	const ERR_MAX_VALUE  = 3;
	const ERR_SANITIZE   = 4;
	const ERR_INCOMPLETED= 5;
	const ERR_UNDEFINED  = 6;
	const ERR_COMPARISON = 7;
	const ERR_FORMAT     = 8;
	const SUCCESS = 10;

	public function __construct(Field $field);

	//public function checkInput($value);

	public function prepareInput($value, $sanitize);

	public function prepareOutput();
}
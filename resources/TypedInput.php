<?php
namespace Resources;

class TypedInput
{
	public $type;
	public $isRequired;

	public function __construct($type, $isRequired = false)
	{
		$this->type = $type;
		$this->isRequired = $isRequired;
	}

	public function validate(&$value)
	{
		switch($this->type)
		{
			case 'integer': return $this->validateInteger($value);
			case 'string':  return $this->validateString($value);
			case 'float':   return $this->validateFloat($value);
			case 'boolean': return $this->validateBoolean($value);
		}
	}

	private function validateInteger($value)
	{
		//if it is null or a valid interger then return true
		return $value === null || strval($value) == strval(intval($value));
	}

	private function validateString($value)
	{
		return $value === null || is_string($value);
	}

	private function validateFloat($value)
	{
		return $value === null || strval($value) == strval(floatval($value));
	}

	//pass by reference should be refactored later
	private function validateBoolean(&$value)
	{
		if($value === '')
		{
			$value = '1';
			return true;
		}

		return $value === null || $value === '0' || $value === '1';
	}
}

?>

<?php
namespace Resources;

function validateInteger($value)
{
	//if not null and not a valid integer then error
	if($value !== null && strval($value) != strval(intval($value)))
	{
		http_response_code(400);
		exit();
	}
}

function validateString($value)
{
	if($value !== null && strval($value) != strval($value))
	{
		http_response_code(400);
		exit();
	}
}

function validateFloat($value)
{
	if($value !== null && strval($value) != strval(floatval($value)))
	{
		http_response_code(400);
		exit();
	}
}

//pass by reference should be refactored later
function validateBoolean(&$value)
{
	if($value !== null && $value !== '0' && $value !== '1')
	{
		if($value === '')
		{
			$value = 1;
		}
		else
		{
			http_response_code(400);
			exit();
		}
	}
}

use \PDO;

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
			case 'integer': return validateInteger($value);
			case 'string': return validateString($value);
			case 'float': return validateFloat($value);
			case 'boolean': return validateBoolean($value);
		}
	}

	public function pdoDataType()
	{
		switch($this->type)
		{
			case 'integer': return PDO::PARAM_INT;
			case 'string': return PDO::PARAM_STR;
			case 'boolean': return PDO::PARAM_BOOL;
			default: return PDO::PARAM_STR;
		}
	}
}

?>

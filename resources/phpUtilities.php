<?php

function generateRandomSalt( $numChar )
{
	//one base64 char repersents 6bits, there are 8bits in a byte
	$numBytes = $numChar * 6 / 8;
	return base64_encode(openssl_random_pseudo_bytes($numBytes));
}

function loadConfig()
{
	return parse_ini_file('../config/rest.ini');
}

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

?>

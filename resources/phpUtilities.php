<?php

require_once 'vendor/autoload.php';

//use Resources\TypedInput;

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

function connectToDatabase()
{
	$config = loadConfig();

	return new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}

function enforceKeys( array $input, array $inputKeys )
{
	$requiredCount = 0;
	foreach($inputKeys as $object)
		if($object->isRequired)
			$requiredCount++;

	$count = 0;
	foreach($input as $key => $value)
	{
		$typedInput = $inputKeys[$key];

		if($typedInput === null)
		{
			http_response_code(400);
			exit();
		}

		$typedInput->validate($value);

		if($typedInput->isRequiredKey)
			$count++;
	}

	if($requiredCount !== $count)
	{
		http_response_code(400);
		exit();
	}
}

function passInputKeys(array $input, array $inputKeys, $pdoStatement)
{
	foreach($inputKeys as $key => $typedInput)
		$pdoStatement->bindValue(":$key", $input[$key], $typedInput->pdoDataType());
}

?>

<?php

require_once 'vendor/autoload.php';

function generateRandomSalt( $numChar )
{
	//one base64 char repersents 6bits, there are 8bits in a byte
	$numBytes = $numChar * 6 / 8;
	return base64_encode(openssl_random_pseudo_bytes($numBytes));
}

function loadConfig()
{
	static $config = null;
	if($config === null)
		$config = parse_ini_file('../config/rest.ini');

	return $config;
}

function connectToDatabase()
{
	$config = loadConfig();

	return new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}

?>

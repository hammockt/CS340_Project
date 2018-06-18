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

?>

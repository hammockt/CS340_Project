<?php

function getJsonFromHttpBody()
{
	$data = json_decode(file_get_contents("php://input"), true);
	if($data === NULL)
	{
		http_response_code(400);
		exit();
	}

	return $data;
}

//checks if they exist, or have a non-empty non-zero value
function enforceNonEmptyKeys( array $array, array $keys )
{
	$count = 0;
	foreach( $keys as $key )
	{
		if(!empty($array[$key]))
		{
			$count++;
		}
	}

	if(count($keys) !== $count)
	{
		http_response_code(400);
		exit();
	}
}

function generateRandomSalt( $numChar )
{
	//one base64 char repersents 6bits, there are 8bits in a byte
	$numBytes = $numChar * 6 / 8;
	return base64_encode(openssl_random_pseudo_bytes($numBytes));
}

?>

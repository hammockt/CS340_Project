<?php

function enforceHttpMethods( array $methods )
{
	$requestMethod = $_SERVER["REQUEST_METHOD"];

	foreach( $methods as $method )
	{
		if($requestMethod === $method)
		{
			return;
		}
	}

	http_response_code(405);
	exit();
}

function enforceKeys( array $array, array $requiredKeys, array $optionalKeys )
{
	$count = 0;
	foreach($array as $key => $value)
	{
		$isRequiredKey = in_array($key, $requiredKeys);
		$isOptionalKey = in_array($key, $optionalKeys);

		if(!$isRequiredKey && !$isOptionalKey)
		{
			http_response_code(400);
			exit();
		}

		if($isRequiredKey && isset($value))
		{
			$count++;
		}
	}

	if(count($requiredKeys) !== $count)
	{
		http_response_code(400);
		exit();
	}
}

//checks if they exist, or have a non-empty, non-zero, or empty string value
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

?>

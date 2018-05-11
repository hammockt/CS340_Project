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

?>

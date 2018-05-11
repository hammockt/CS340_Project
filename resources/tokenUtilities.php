<?php

require_once 'vendor/autoload.php';

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

function verifyRefreshToken( $config, $token )
{
	verifyToken($config, $token, 'refresh');
}

function verifyAuthToken( $config, $token )
{
	verifyToken($config, $token, 'auth');
}

function verifyToken( $config, $token, $tokenType )
{
	if(!$token->verify(new Sha256(), $config["{$tokenType}_key"]) || !$token->hasClaim('uid'))
	{
		//bad tokens are forbidden
		http_response_code(403);
		exit();
	}

	//validate the token contraints. i.e token has not expired and the servers are correct
	$data = new ValidationData(); //it will use the current time to validate (iat, nbf and exp)
	$data->setIssuer($config["{$tokenType}_iss"]);
	$data->setAudience($config["{$tokenType}_aud"]);
	$data->setSubject($tokenType);
	if(!$token->validate($data))
	{
		//expired or non-correct credentials are unauthorized
		http_response_code(401);
		exit();
	}
}

?>

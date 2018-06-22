<?php

require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

function verifyRefreshToken( $config, $tokenString )
{
	return verifyToken($config, $tokenString, 'refresh');
}

function verifyAuthToken( $config, $tokenString )
{
	return verifyToken($config, $tokenString, 'auth');
}

function verifyToken( $config, $tokenString, $tokenType )
{
	try
	{
		$token = JWT::decode($tokenString, $config["{$tokenType}_key"], ['HS256']);
	}
	catch(Exception $e)
	{
		//expired tokens are unauthorized
		http_response_code(401);
		exit();
	}

	if(!isset($token->jti) || !isset($token->uid) || $token->iss !== $config["{$tokenType}_iss"] || $token->aud !== $config["{$tokenType}_aud"] || $token->sub !== $tokenType)
	{
		//bad tokens are unauthorized
		http_response_code(401);
		exit();
	}

	return $token;
}

?>

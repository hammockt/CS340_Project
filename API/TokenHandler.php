<?php
namespace API;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

class TokenHandler
{
	public static function encodeRefreshToken($username)
	{
		$config = loadConfig();
		$tokenData = array(
			'iss' => $config['refresh_iss'],
			'aud' => $config['refresh_aud'],
			'jti' => generateRandomSalt(16),
			'iat' => time(),
			'exp' => time() + $config['refresh_exp'],
			'sub' => 'refresh',
			'uid' => $username
		);

		return JWT::encode($tokenData, $config['refresh_key'], 'HS256');
	}

	public static function encodeAuthToken($username)
	{
		$config = loadConfig();
		$tokenData = array(
			'iss' => $config['auth_iss'],
			'aud' => $config['auth_aud'],
			'jti' => generateRandomSalt(16),
			'iat' => time(),
			'exp' => time() + $config['auth_exp'],
			'sub' => 'auth',
			'uid' => $username
		);

		return JWT::encode($tokenData, $config['auth_key'], 'HS256');
	}

	public static function verifyToken($tokenString, $tokenType)
	{
		$config = loadConfig();
		$token = null;

		try
		{
			$token = JWT::decode($tokenString, $config["{$tokenType}_key"], ['HS256']);
		}
		catch(\Exception $e)
		{
			return null;
		}

		if(!isset($token->jti) || !isset($token->uid) || $token->iss !== $config["{$tokenType}_iss"] || $token->aud !== $config["{$tokenType}_aud"] || $token->sub !== $tokenType)
		{
			return null;
		}

		return $token;
	}
}

?>
